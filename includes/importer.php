<?php

class TH_Importer
{
  /*
  * @param $plugin_data: ["download_link" => "link", "path" => "plugin core file path" ];
  */
  public function download_and_install_plugin(array $plugin_data)
  {

    //check plugin have download zip file link
    if (
      !isset($plugin_data["download_link"]) ||
      empty($plugin_data["download_link"]) ||
      !isset($plugin_data["path"]) ||
      empty($plugin_data["path"])
    ) {
      error_log("{$plugin_data['name']} dont have a download_link or path");
      return new WP_Error(404, "{$plugin_data['name']} dont have a download_link or path");
    }

    include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
    include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    include_once ABSPATH . 'wp-admin/includes/plugin.php';

    //download and install 
    $plugin_installer = new Plugin_Upgrader();
    $is_installed = $plugin_installer->install($plugin_data["download_link"]);
    if (is_wp_error($is_installed)) {
      error_log("{$plugin_data['name']} install fail");
      return $is_installed;
    }

    //plugin installed sucessfully now activate it
    $is_activated = activate_plugin($plugin_data["path"]);
    if (is_wp_error($is_activated)) {
      error_log("{$plugin_data['name']} activation fail");
      return $is_activated;
    }

    return true;
  }

  private function download_image_from_url(string $url)

  {
    if (filter_var($url, FILTER_VALIDATE_URL) ==  false) {
      error_log("validation fail of url {$url}");
      return false;
    };

    $file_content = @file_get_contents($url);
    if ($file_content == false) {
      error_log("download fail of url {$url}");
      return false;
    }

    return $file_content;
  }

  private function upload_raw_image_data($image_data, $url)
  {
    // Get the filename from the URL
    $filename = basename(parse_url($url, PHP_URL_PATH));

    // Upload to WordPress
    $upload = wp_upload_bits($filename, null, $image_data);

    if ($upload['error']) {
      error_log("upload_raw_image_data fail url:--->{$url}");
      return false;
    }

    // Get the file type
    $wp_filetype = wp_check_filetype($upload['file'], null);

    // Prepare attachment data
    $attachment = [
      'post_mime_type' => $wp_filetype['type'],
      'post_title'     => sanitize_file_name($filename),
      'post_content'   => '',
      'post_status'    => 'inherit'
    ];

    // Insert the attachment into the Media Library
    $attach_id = wp_insert_attachment($attachment, $upload['file'], 0);
    if (is_wp_error($attach_id)) {
      error_log("wp_insert_attachment fail url:--->{$url}");
      return false;
    }

    // Include WordPress image functions
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    // GENERATE ATTACHMENT METADATA AND UPDATE USE THIS LINE IF YOU WANT TO MAKE MULTIPLE RESULATION OF SAME IMAGE
    // $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
    // wp_update_attachment_metadata($attach_id, $attach_data);

    return [
      'attachment_id' => $attach_id,
      'file_url' => $upload['url']
    ];
  }

  function sideload_media_from_url($image_url)
  {
    // it allows us to use download_url() and wp_handle_sideload() functions
    require_once(ABSPATH . 'wp-admin/includes/file.php');

    // download to temp dir
    $temp_file = download_url($image_url);

    if (is_wp_error($temp_file)) {
      return false;
    }

    // move the temp file into the uploads directory
    $file = array(
      'name'     => basename($image_url),
      'type'     => mime_content_type($temp_file),
      'tmp_name' => $temp_file,
      'size'     => filesize($temp_file),
    );
    $sideload = wp_handle_sideload(
      $file,
      array(
        'test_form'   => false // no needs to check 'action' parameter
      )
    );

    if (! empty($sideload['error'])) {
      // you may return error message if you want
      return false;
    }

    // it is time to add our uploaded image into WordPress media library
    $attachment_id = wp_insert_attachment(
      array(
        'guid'           => $sideload['url'],
        'post_mime_type' => $sideload['type'],
        'post_title'     => basename($sideload['file']),
        'post_content'   => '',
        'post_status'    => 'inherit',
      ),
      $sideload['file']
    );

    if (is_wp_error($attachment_id) || ! $attachment_id) {
      return false;
    }

    // update medatata, regenerate image sizes
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    wp_update_attachment_metadata(
      $attachment_id,
      wp_generate_attachment_metadata($attachment_id, $sideload['file'])
    );

    return [
      'attachment_id' => $attachment_id,
      'file_url' => wp_get_attachment_url($attachment_id)
    ];
  }

  function restore_post_meta($post_id, $meta)
  {
    if (!is_array($meta)) {
      return false;
    }

    global $wpdb;

    foreach ($meta as $key => $values) {
      //1st delete all previous meta of that same key 
      delete_post_meta($post_id, $key);
      foreach ($values as $val) {
        //special case if its elementor page then import the page with elementor
        if ($key === "_elementor_data") {
          $res  = $this->elementor_import_post($val, $post_id);
          if ($res["success"] == false) error_log($res["message"]);
        } else {
          //add new meta. all values are string either normal string or serialized or json
          //use direct db so same value get inserted. Otherwise update_post_meta make some issue
          $wpdb->insert($wpdb->postmeta, ['post_id' => $post_id, 'meta_key' => $key, 'meta_value' => $val]);
        }
      }
    }

    return true;
  }

  function recursively_replace_urls($data, $url_map)
  {
    foreach ($data as $key => $value) {
      if (is_array($value)) {
        // Check for image object with 'url'
        if (isset($value['url']) && is_string($value['url'])) {
          foreach ($url_map as $old_url => $new_data) {
            if (strpos($value['url'], $old_url) !== false) {
              $data[$key]['url'] = $new_data['url'];
              $data[$key]['id'] = $new_data['attachment_id'];
              $data[$key]['source'] = 'library';
              break; // Skip deeper recursion for matched image
            }
          }
        } else {
          // Continue recursion
          $data[$key] = $this->recursively_replace_urls($value, $url_map);
        }
      } elseif (is_string($value)) {
        foreach ($url_map as $old_url => $new_data) {
          if (strpos($value, $old_url) !== false) {
            $data[$key] = str_replace($old_url, $new_data['url'], $value);
          }
        }
      }
    }
    return $data;
  }

  public function import_post(array $post, string $post_type)
  {
    if (empty($post["title"]) || empty($post["slug"]) || empty($post_type) || empty($post["status"])) {
      error_log("validation fail import_post:id-->{$post["id"]}");
      return false;
    }

    //if its elementor global theme settings type post then run seperate fn and return;
    if (array_key_exists("elementor_site_settings", $post) && $post["elementor_site_settings"] === true) {
      return $this->import_elementor_global_settings($post);
    }

    //images inside post content
    if (!empty($post["attachments"]) && is_array($post["attachments"])) {
      $content = $post["content"];

      // Create a lookup of old_url => new_url
      $url_map = [];

      foreach ($post["attachments"] as $attachment) {
        if (empty($attachment["url"])) continue;

        $old_url = $attachment["url"];

        // Upload to media library
        $new_attachment = $this->sideload_media_from_url($old_url);
        if ($new_attachment === false) continue;

        $new_url = $new_attachment["file_url"];
        $url_map[$old_url] = [
          'url' => $new_url,
          'attachment_id' => $new_attachment['attachment_id'],
        ];


        // Replace in content
        $content = str_replace($old_url, $new_url, $content);
      }

      $post["content"] = $content;
    }


    //images in feature image or thumbnails
    if (isset($post["thumbnail"]) && !empty($post["thumbnail"]["url"])) {
      //upload to media directory
      $attachment = $this->sideload_media_from_url($post["thumbnail"]["url"]);
      if ($attachment != false) {
        //update meta `_thumbnail_id` so this post thumbnail point this new image 
        //it will get updated in db in restore_post_meta fn
        $post["meta"]["_thumbnail_id"] = ["{$attachment['attachment_id']}"];
      };
    }

    $args = [
      "post_title"    => $post["title"],
      "post_content"  => $post["content"],
      "post_status"   => $post["status"],
      "post_type"     => $post_type,
    ];

    //$result is the post id
    $result = wp_insert_post($args);


    if (is_wp_error($result)) {
      error_log("post insert fail id:-->{$post['id']}");
      return $result;
    }

    $this->restore_post_meta($result, $post["meta"]);

    //if front page  or blog page update option
    foreach (["page_on_front", "page_for_posts"] as $key) {
      if (isset($post[$key]) && !empty($post[$key])) {
        update_option($key, $result);
      }
    }

    return $result;
  }

  public function import_menus(array $menus_data)
  {
    foreach ($menus_data as $_menu) {
      //create menu term if not exist
      //update menu location so this menu get render on that location
      //insert menu item in post table with menu order and parent menu preservation
      $menu_name = $_menu["menu"]["name"];
      $menu_exist = wp_get_nav_menu_object($menu_name);

      if ($menu_exist == false) {
        $menu_id = wp_create_nav_menu($menu_name);
        if (is_wp_error($menu_id)) {
          error_log("Nav menu creation fail: {$menu_name}");
          continue;
        }

        $menu_obj = wp_get_nav_menu_object($menu_id);
        if ($menu_obj == false) {
          error_log("wp_get_nav_menu_object fail: {$menu_id}");
          continue;
        }

        $menu_locations = get_theme_mod("nav_menu_locations");
        if (is_array($_menu["menu"]["locations"])) {
          foreach ($_menu["menu"]["locations"] as $_location) {
            $menu_locations[$_location] = $menu_id;
          }
        }
        set_theme_mod("nav_menu_locations", $menu_locations);

        $oldid_map_newid = [];

        foreach ($_menu["items"] as $m) {
          if ($m["parent"] != "0") continue;
          $full_url = $m['url'];
          if (substr($full_url, 0, 1) === '/') {
            $full_url = rtrim(get_site_url(), '/') . $full_url;
          }
          $id = wp_update_nav_menu_item($menu_id, 0, [
            'menu-item-title' => $m['title'],
            'menu-item-url' => $full_url,
            'menu-item-status' => 'publish',
            'menu-item-position' => $m['order']
          ]);
          if (is_wp_error($id)) {
            error_log("wp_update_nav_menu_item fail: {$menu_id} title: {$m['title']}");
            continue;
          }
          $oldid_map_newid[$m["id"]] = $id;
        }

        foreach ($_menu["items"] as $m) {
          if ($m["parent"] == "0") continue;
          $full_url = $m['url'];
          if (substr($full_url, 0, 1) === '/') {
            $full_url = rtrim(get_site_url(), '/') . $full_url;
          }
          $id = wp_update_nav_menu_item($menu_id, 0, [
            'menu-item-title' => $m['title'],
            'menu-item-url' => $full_url,
            'menu-item-status' => 'publish',
            'menu-item-parent-id' => $oldid_map_newid[$m["parent"]],
            'menu-item-position' => $m['order']
          ]);
          if (is_wp_error($id)) {
            error_log("wp_update_nav_menu_item fail: {$menu_id} title: {$m['title']}");
            continue;
          }
          $oldid_map_newid[$m["id"]] = $id;
        }
      } else {
        //if same name menu exist 
        //then set old menu to this menu location
        $menu_locations = get_theme_mod("nav_menu_locations");
        if (is_array($_menu["menu"]["locations"])) {
          foreach ($_menu["menu"]["locations"] as $_location) {
            $menu_locations[$_location] = $menu_exist->term_id;
          }
        }
        set_theme_mod("nav_menu_locations", $menu_locations);
      }
    }

    return true;
  }

  public function import_customizer(array $customizer_value)
  {
    //remove nav_menu_locations from customizer_value
    //import_menus handle this nav_menu_locations update
    //so don't overwrite it with customizer value
    unset($customizer_value["nav_menu_locations"]);
    $customizer_key = "theme_mods_" . get_option("stylesheet");
    $current_value = get_option($customizer_key);
    $is_updated = null;

    if ($current_value === $customizer_value || maybe_serialize($current_value) === maybe_serialize($customizer_value)) {
      $is_updated = true;
    } else {
      $is_updated = update_option($customizer_key, $customizer_value);
    }


    //custom-logo
    if (isset($customizer_value["custom_logo"]) && !empty($customizer_value["custom_logo"])) {
      //download image
      $image_data = $this->download_image_from_url($customizer_value["custom_logo"]);
      if ($image_data != false) {
        //upload to media directory
        $attachment = $this->upload_raw_image_data($image_data, $customizer_value["custom_logo"]);
        if ($attachment != false) {
          //use set_theme_mod because somehow using update_option this 
          //does not work, even thogh the new attachment_id is stored in db
          //but it displayes previous attachment_id 
          //maybe some internal wp cache, so the fix is use set_theme_mod fn
          set_theme_mod("custom_logo", $attachment["attachment_id"]);
        };
      }
    }

    //also add home_page,posts_page,permalink_structure
    foreach (["show_on_front", "permalink_structure"] as $key) {
      if (!empty($customizer_value[$key])) {
        update_option($key, $customizer_value[$key]);
      }
    }


    if ($is_updated == false) {
      error_log("import_customizer: import fail!");
    }

    return $is_updated;
  }

  public function import_elementor_global_settings(array $post)
  {
    //get `elementor_active_kit` post id if have
    $elementor_hidden_post_id = get_option("elementor_active_kit");

    //if dont then create a elementor hidden post
    if (!$elementor_hidden_post_id) {
      $elementor_hidden_post_id = wp_insert_post(["post_title" => "Default Kit", "post_type" => "elementor_library"]);
    }

    if (!$elementor_hidden_post_id) {
      error_log("Elementor Global site settings import fails");
      return false;
    }

    //update `elementor_active_kit` option with post id
    update_option("elementor_active_kit", $elementor_hidden_post_id);

    //add meta for that hidden post that will have global site settings
    global $wpdb;
    if ($post["meta"] && is_array($post["meta"])) {
      foreach ($post["meta"] as $key => $values) {
        //delete previous key
        delete_post_meta($elementor_hidden_post_id, $key);
        foreach ($values as $value) {
          $wpdb->insert($wpdb->postmeta, ['post_id' => $elementor_hidden_post_id, 'meta_key' => $key, 'meta_value' => $value]);
        }
      }
    }

    return $elementor_hidden_post_id;
  }

  public function elementor_import_post($json_data, $post_id)
  {
    $target_page_id = (int)$post_id;

    if (empty($json_data)) {
      return array('success' => false, 'message' => 'No JSON data provided.', 'code' => 400);
    }

    if (! $target_page_id || ! get_post($target_page_id)) {
      return array('success' => false, 'message' => 'Invalid or missing target_page_id.', 'code' => 400);
    }

    // Convert the JSON data to a string
    $template_content = json_encode([
      "content" => is_string($json_data) ? json_decode($json_data, true) : $json_data,
      "title" =>  get_the_title($post_id),
      //dont use dynamic post type its always page.
      //then elementor will treat it well or weird issues occurs
      "type" =>  "page"
    ]);

    // Create a temporary file to simulate a file upload for Elementor's import process
    $upload_dir = wp_upload_dir();
    $temp_dir = trailingslashit($upload_dir['path']) . 'elementor_temp_import/';
    wp_mkdir_p($temp_dir); // Ensure the directory exists

    $temp_file_name = uniqid('elementor_template_') . '.json';
    $temp_file_path = $temp_dir . $temp_file_name;

    // Write the JSON content to the temporary file
    file_put_contents($temp_file_path, $template_content);

    // Include Elementor's necessary files and classes
    if (! did_action('elementor/loaded')) {
      // Clean up temp file before returning
      if (file_exists($temp_file_path)) {
        unlink($temp_file_path);
      }
      if (is_dir($temp_dir) && count(scandir($temp_dir)) === 2) { // Check if directory is empty except for . and ..
        rmdir($temp_dir);
      }
      return array('success' => false, 'message' => 'Elementor is not loaded.', 'code' => 400);
    }

    // To store the ID of the template imported into the library
    $imported_template_id = 0;
    try {
      // Get the Elementor Template Library Manager instance
      $manager = \Elementor\Plugin::$instance->templates_manager;

      // Get the local source instance
      $source = $manager->get_source('local');

      // This will import the template into the Elementor library
      $import_result = $source->import_template($temp_file_name, $temp_file_path);

      // Clean up the temporary file and directory
      unlink($temp_file_path);

      // Check if directory is empty except for . and ..
      if (is_dir($temp_dir) && count(scandir($temp_dir)) === 2) {
        rmdir($temp_dir);
      }


      if (is_wp_error($import_result)) {
        return array('success' => false, 'message' => 'Template import failed: ' . $import_result->get_error_message(), 'code' => 500);
      }

      // The import_template function returns an array of imported items, get the first one's ID.
      if (! empty($import_result) && is_array($import_result)) {
        $imported_template_id = $import_result[0]['template_id'] ?? 0;
      }

      if (! $imported_template_id) {
        return array('success' => false, 'message' => 'Could not retrieve imported template ID.', 'code' => 500);
      }

      // Get the Elementor data from the newly imported template
      $template_elementor_data = get_post_meta($imported_template_id, '_elementor_data', true);

      if (empty($template_elementor_data)) {
        // Delete the temporary template from the library since its data is empty
        wp_delete_post($imported_template_id, true);
        return array('success' => false, 'message' => 'Imported template has no Elementor data.', 'code' => 500);
      }

      //use direct db so same value get inserted. Otherwise update_post_meta make some issue
      global $wpdb;
      $wpdb->insert($wpdb->postmeta, ['post_id' => $target_page_id, 'meta_key' => '_elementor_data', 'meta_value' => $template_elementor_data]);
      //remove the imported template from the library if you only want to insert it into the page and not keep it as a reusable template.
      wp_delete_post($imported_template_id, true);


      return array('success' => true, 'message' => 'Template imported and inserted into page successfully.', 'target_page_id' => $target_page_id, 'code' => 200);
    } catch (\Exception $e) {
      // Clean up the temporary file if an exception occurs
      if (file_exists($temp_file_path)) {
        unlink($temp_file_path);
      }

      // Check if directory is empty except for . and ..
      if (is_dir($temp_dir) && count(scandir($temp_dir)) === 2) {
        rmdir($temp_dir);
      }
      // If a template was imported into the library but then an error occurred, delete it.
      if ($imported_template_id) {
        wp_delete_post($imported_template_id, true);
      }

      return array('success' => false, 'message' => 'Error processing template: ' . $e->getMessage(), 'code' => 500);
    }
  }
}
