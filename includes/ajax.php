<?php

class TH_Core_Ajax_Controller
{
  private $API_ROOT = "http://127.0.0.1:8000/api";
  // private $API_ROOT = "http://app.tophivetheme.com/api";
  private $API_ENDPOINT = "";
  private $API_ENDPOINT_LICENSE_ACTIVATE = "";
  private $API_ENDPOINT_LICENSE_CHECK = "";
  private $API_ENDPOINT_UPDATE_CHECK = "";
  private $IMPORTER = null;
  protected $theme_update_checked = false;
  protected $theme_update_result  = [];

  function __construct()
  {
    add_action("wp_ajax_tophive/api/templates/{resource_type}", [$this, "get_templates"]);
    add_action("wp_ajax_tophive/api/categories", [$this, "get_categories"]);
    add_action("wp_ajax_tophive/api/changelog", [$this, "get_changelog"]);
    add_action("wp_ajax_tophive_import_resource", [$this, "import_resource"]);
    add_action("wp_ajax_tophive_import_plugin", [$this, "import_plugin"]);
    add_action("wp_ajax_tophive_activate_license", [$this, "activate_license"]);
    add_action("wp_ajax_tophive_check_licence", [$this, "check_license"]);
    add_action("wp_ajax_tophive_get_theme_secret", [$this, "get_theme_secret"]);
    add_filter("site_transient_update_themes", [$this, "check_theme_update_handle"]);

    $this->IMPORTER = new TH_Importer();
    $this->API_ENDPOINT = "{$this->API_ROOT}/templates/{resource_type}";
    $this->API_ENDPOINT_LICENSE_ACTIVATE = "{$this->API_ROOT}/license/activate";
    $this->API_ENDPOINT_LICENSE_CHECK = "{$this->API_ROOT}/license/check";
    $this->API_ENDPOINT_UPDATE_CHECK  = "{$this->API_ROOT}/product";
  }

  private function restrict_for_admin()
  {
    if (! current_user_can('administrator')) {
      wp_send_json("Not an administrator", 401);
    }
  }

  private function get_product_id_key()
  {
    return get_option("stylesheet") . "_tophive_product_id";
  }

  private function get_licence_key()
  {
    return get_option("stylesheet") . "_tophive_license";
  }

  private function get_product_id()
  {
    return get_option($this->get_product_id_key(), "");
  }

  private function get_licence()
  {
    return get_option($this->get_licence_key(), "");
  }

  public function check_theme_update_handle($transient)
  {
    // Only run in admin
    if (! is_admin()) return $transient;

    // Make sure get_current_screen() exists
    if (function_exists('get_current_screen')) {
      $screen = get_current_screen();
      if (! $screen || $screen->id !== 'themes') {
        return $transient; // exit if not the Themes page
      }
    }


    //cached for later on same execution context
    if (! $this->theme_update_checked) {
      $this->theme_update_result = $this->check_theme_update();
      $this->theme_update_checked = true;
    }

    $res = $this->theme_update_result;

    if ($res[1] !== 200 || empty($res[0]["url"]) || !$res[0]["has_update"]) {
      return $transient;
    }

    $theme   = wp_get_theme();
    $slug    = $theme->get_stylesheet();

    if (empty($transient->response)) {
      $transient->response = [];
    }

    $transient->response[$slug] = [
      'theme'       => $slug,
      'package'     => $res[0]["url"],
      'new_version' => $res[0]["new_version"],
      'url'         => $theme->get('ThemeURI'),
    ];


    return $transient;
  }

  public function check_theme_update()
  {
    $theme_slug = wp_get_theme()->get_stylesheet();
    $current_version = wp_get_theme($theme_slug)->get('Version');

    $license_value = $this->get_licence();
    $product_id =  $this->get_product_id();
    $domain = get_site_url();
    $url = "{$this->API_ENDPOINT_UPDATE_CHECK}/{$product_id}?domain={$domain}";

    $remote_response  = wp_remote_request($url, [
      "method" => "GET",
      "headers" => [
        'Authorization' => $license_value,
        'content-type' => 'application/json',
      ],
    ]);

    if (is_wp_error($remote_response) || wp_remote_retrieve_response_code($remote_response) !== 200) {
      return [$remote_response, 400];
    }

    $remote_data = json_decode(wp_remote_retrieve_body($remote_response), true)["product"];
    $res = [
      'theme'       => $theme_slug,
      'new_version' => $remote_data["version"],
      'url'         => $remote_data["download_url"], // ZIP file URL
    ];
    if ($remote_data && version_compare($current_version, $remote_data["version"], '<')) {
      $res["has_update"] = true;
    } else {
      $res["has_update"] = false;
    }

    return [$res, 200];
  }

  public function check_license()
  {
    $this->restrict_for_admin();

    if (empty($this->get_licence()) || empty($this->get_product_id())) {
      wp_send_json("license required", 400);
    }

    $res = wp_remote_request($this->API_ENDPOINT_LICENSE_CHECK, [
      "method" => "POST",
      "headers" => ['content-type' => 'application/json'],
      "body" => json_encode(['license_key' => $this->get_licence(), 'domain' => get_site_url()])
    ]);

    if (is_wp_error($res)) {
      wp_send_json($res, 400);
    }

    if (wp_remote_retrieve_response_code($res) != 200) {
      wp_send_json($res, wp_remote_retrieve_response_code($res));
    } else {
      $body = json_decode(wp_remote_retrieve_body($res), true);
      wp_send_json(
        [
          "status_code" => wp_remote_retrieve_response_code($res),
          "data" => $body
        ],
        200
      );
    }
  }

  public function activate_license()
  {
    $this->restrict_for_admin();

    if (!isset($_POST["options"]) || empty($_POST["options"])) {
      wp_send_json("options is missing", 400);
    }
    $options = map_deep($_POST["options"], "sanitize_text_field");

    if (!isset($options["license"]) || empty($options["license"])) {
      wp_send_json("license required", 400);
    }
    if (!isset($options["secret"]) || empty($options["secret"])) {
      wp_send_json("secret required", 400);
    }

    $res = wp_remote_request($this->API_ENDPOINT_LICENSE_ACTIVATE, [
      "method" => "POST",
      "headers" => ['content-type' => 'application/json'],
      "body" => json_encode([
        'license_key' => $options["license"],
        'domain' => get_site_url(),
        'product_secret' => $options["secret"],
      ])
    ]);

    if (is_wp_error($res)) {
      wp_send_json($res, 400);
    }

    if (wp_remote_retrieve_response_code($res) != 200) {
      wp_send_json($res, wp_remote_retrieve_response_code($res));
    } else {
      $body = json_decode(wp_remote_retrieve_body($res), true);
      //save license_key 
      update_option($this->get_licence_key(), $options["license"]);
      update_option($this->get_product_id_key(), $body["data"]["product_id"]);

      wp_send_json(
        [
          "status_code" => wp_remote_retrieve_response_code($res),
          "data" => $body
        ],
        200
      );
    }
  }

  public function import_plugin()
  {
    $this->restrict_for_admin();

    if (!isset($_POST["options"]) || empty($_POST["options"])) {
      wp_send_json("options is missing", 400);
    }
    $options = map_deep($_POST["options"], "sanitize_text_field");
    if (!isset($options["download_link"]) || empty($options["download_link"]) || !isset($options["path"]) || empty($options["path"])) {
      wp_send_json("invalid options", 400);
    }
    $res = $this->IMPORTER->download_and_install_plugin(["download_link" => $options["download_link"], "path" => $options["path"]]);
    if (is_wp_error($res)) {
      wp_send_json("plugin install fail", 400);
    }

    wp_send_json("plugin installed", 200);
  }

  private function _get_templates($options)
  {
    if (empty($options)) {
      return new WP_Error(400, "params missing");
    }

    if (!isset($options["resource_type"]) || empty($options["resource_type"])) {
      return new WP_Error(400, "resource_type cant be blank");
    }

    $resource_type = $options["resource_type"];
    $page = 1;

    if (isset($options["page"]) && !empty($options["page"])) {
      $page = $options["page"];
    }
    $url = str_replace("{resource_type}", $resource_type, $this->API_ENDPOINT) . "?page={$page}";

    if (isset($options["id"]) && !empty($options["id"])) {
      $url .= "&id={$options['id']}";
    }

    //ADD LICENSE ADD PRODUCT_ID
    $license_value = $this->get_licence();
    $product_id = $this->get_product_id();
    $url .= "&product_id={$product_id}";
    $domain = get_site_url();
    $url .= "&domain={$domain}";

    //add filter params
    if (isset($options["category"]) && !empty($options["category"])) {
      $cat = strtolower($options["category"]);
      $url .= "&category={$cat}";
    }

    if (isset($options["plan"]) && !empty($options["plan"])) {
      $plan = strtolower($options["plan"]);
      $url .= "&plan={$plan}";
    }

    if (isset($options["builder"]) && !empty($options["builder"])) {
      $builder = strtolower($options["builder"]);
      $url .= "&builder={$builder}";
    }

    if (isset($options["search"]) && !empty($options["search"])) {
      $search = strtolower($options["search"]);
      $url .= "&search={$search}";
    }



    $res = wp_remote_request($url, [
      "method" => "GET",
      "headers" => [
        'Authorization' => $license_value,
        'content-type' => 'application/json',
      ],
    ]);

    if (is_wp_error($res)) {
      return new WP_Error(400, "request fail", $res);
    }

    return [
      "status_code" => wp_remote_retrieve_response_code($res),
      "data" => json_decode(wp_remote_retrieve_body($res), true),
    ];
  }

  public function get_templates()
  {
    $this->restrict_for_admin();

    if (!isset($_POST["options"]) || empty($_POST["options"])) {
      wp_send_json("params missing", 400);
    }

    $options = $_POST["options"];
    $res = $this->_get_templates($options);

    if (is_wp_error($res)) {
      wp_send_json($res->get_error_message(), $res->get_error_code());
    }

    if ($res["status_code"] != 200) {
      wp_send_json($res, $res["status_code"]);
    }

    wp_send_json($res, 200);
  }

  public function get_changelog()
  {
    $this->restrict_for_admin();

    $product_id = $this->get_product_id();
    $res = wp_remote_request(
      $this->API_ROOT . "/changelogs/{$product_id}",
      ["method" => "GET", "headers" => ['content-type' => 'application/json']]
    );

    if (is_wp_error($res)) {
      wp_send_json($res->get_error_message(), $res->get_error_code());
    }

    $data = [
      "status_code" => wp_remote_retrieve_response_code($res),
      "data" => json_decode(wp_remote_retrieve_body($res), true),
    ];

    wp_send_json($data, $data["status_code"]);
  }

  public function get_categories()
  {
    $this->restrict_for_admin();

    $license_value = $this->get_licence();
    $domain = get_site_url();
    $res = wp_remote_request($this->API_ROOT . "/categories?domain={$domain}", [
      "method" => "GET",
      "headers" => [
        'Authorization' => $license_value,
        'content-type' => 'application/json',
      ],
    ]);

    if (is_wp_error($res)) {
      wp_send_json($res->get_error_message(), $res->get_error_code());
    }

    $data = [
      "status_code" => wp_remote_retrieve_response_code($res),
      "data" => json_decode(wp_remote_retrieve_body($res), true),
    ];

    wp_send_json($data, $data["status_code"]);
  }

  public function import_resource()
  {
    $this->restrict_for_admin();

    if (!isset($_POST["params"]) || empty($_POST["params"])) {
      wp_send_json("params missing", 400);
    }
    $params = $_POST["params"];

    if (
      empty($params["resource_type"]) || empty($params["id"])
    ) {
      wp_send_json("resource_type or id missing", 400);
    }

    $id = $params["id"];
    $resource_type = $params["resource_type"];
    $res = $this->_get_templates(["resource_type" => $resource_type, "id" => $id]);

    if ($res["status_code"] !== 200) {
      return wp_send_json(["Fetch resource id:{$id} fail", $res], 400);
    }

    if (empty($res["data"]["templates"])) {
      return wp_send_json(["No resource found with id:{$id}", $res], 400);
    }

    $data = json_decode($res["data"]["templates"][0]["json_code"], true);


    //import resource based on their type
    //post,page,customizer,menu,fullsite,custom_post
    if ($resource_type == "page" || $resource_type == "post" || $resource_type == "custom_post") {
      $res = $this->IMPORTER->import_post($data, $data["post_type"] ?? $resource_type);
      if ($res == false || is_wp_error($res)) {
        wp_send_json(["import fail check error log", $res], 400);
      }
      wp_send_json(["status" => "success", "data" => $res], 200);
    } elseif ($resource_type == "customizer") {
      $res = $this->IMPORTER->import_customizer($data);
      if ($res == false) {
        wp_send_json(["import customizer fail check error log", $res], 400);
      }
      wp_send_json(["status" => "success", "data" => $res], 200);
    } elseif ($resource_type == "menus") {
      $res = $this->IMPORTER->import_menus($data);
      if ($res == false) {
        wp_send_json(["import  menus fail check error log", $res], 400);
      }
      wp_send_json(["status" => "success", "data" => $res], 200);
    } else {
      wp_send_json("unknown resource_type ", 400);
    }
  }

  public function get_theme_secret()
  {
    $this->restrict_for_admin();

    $theme = wp_get_theme();
    $theme_name = $theme->get('Name') ?: $theme->get('TextDomain') ?: 'tophive';
    $author_name = $theme->get('Author') ?: 'tophive';

    // Generate secret key: theme_name-author_name (lowercase, no spaces)
    $secret = strtolower(str_replace(' ', '', $theme_name)) . '-' . strtolower(str_replace(' ', '', $author_name));

    wp_send_json([
      'secret' => $secret,
      'theme_name' => $theme_name,
      'author_name' => $author_name
    ], 200);
  }
}

new TH_Core_Ajax_Controller();
