<?php

class Tophive_Core_Builder
{
  public function __construct()
  {
    add_action('init',                     [$this, 'tophive_register_cpts']);
    add_action('init',                     [$this, 'tophive_elementor_builder_defaults']);

    add_action('add_meta_boxes',           [$this, 'display_condition_metabox'], 0);
    add_action('admin_enqueue_scripts',    [$this, 'tophive_core_load_internal_scripts']);
    add_action('save_post',                [$this, 'save_builder_conditions_metabox']);

    // add_filter( 'views_edit-tophive-builder', [ $this, 'admin_print_tabs' ] );

    add_filter('tophive_page_title',        [$this, 'tophive_filter_title']);
  }
  function tophive_filter_title($output)
  {
    if ($this->is_elementor_editor() && $this->is_tophive_cpt()) {
      return '';
    }
    return $output;
  }

  /**
   * Detect if the user is editing in Elementor.
   */
  function is_elementor_editor()
  {
    if (defined('ELEMENTOR_VERSION') && \Elementor\Plugin::$instance->editor->is_edit_mode()) {
      return true;
    }
    if (isset($_GET['elementor-preview']) || (isset($_GET['action']) && $_GET['action'] === 'elementor')) {
      return true;
    }
    return false;
  }

  /**
   * Check if the current post is of a specific CPT (tophive-header, tophive-footer, tophive-forms).
   */
  function is_tophive_cpt()
  {
    global $post;

    // Try alternative methods if global $post is not available
    if (!$post && isset($_GET['post'])) {
      $post_id = intval($_GET['post']);
      $post = get_post($post_id);
    }

    if (!$post) {
      return false;
    }

    // List of allowed CPTs
    $allowed_cpts = ['tophive-header', 'tophive-footer', 'tophive-forms'];

    return in_array($post->post_type, $allowed_cpts, true);
  }


  public function admin_print_tabs($views)
  {
    $current_type = '';
    $active_class = ' nav-tab-active';

    if (! empty($_REQUEST['tabs'])) {
      $current_type = $_REQUEST['tabs'];
      $active_class = '';
    }

    $url_args = [
      'post_type' => 'tophive-builder',
    ];

    $baseurl = add_query_arg($url_args, admin_url('edit.php'));

    $post_types = $this->get_tabs();
?>

    <div id="tophive-builder-wrapp"></div>
    <div id="tophive-theme-builder-tabs" class="nav-tab-wrapper">
      <?php
      foreach ($post_types as $type => $type_label) {
        $active_class = '';

        if ($current_type === $type) {
          $active_class = ' nav-tab-active';
        }

        $type_url = add_query_arg('tabs', $type, $baseurl);

        echo "<a class='nav-tab{$active_class}' href='{$type_url}'>{$type_label}</a>";
      }
      ?>
    </div>
  <?php
    return $views;
  }
  public function get_tabs()
  {
    return array(
      '_tophive_header' => esc_html__('Header', TH_CORE_SLUG),
      '_tophive_footer' => esc_html__('Footer', TH_CORE_SLUG),
      '_tophive_forms' => esc_html__('Forms', TH_CORE_SLUG),
      '_tophive_popup' => esc_html__('Popup', TH_CORE_SLUG),
    );
  }

  public function condition_rules()
  {
    $rules = [
      [
        'id' => 'all',
        'name' => 'Entire Website',
      ],
      [
        'id' => 'singulars',
        'name' => 'Singulars',
      ],
      [
        'id' => 'archives',
        'name' => 'Archives',
      ],
      [
        'id' => '404',
        'name' => '404 page',
      ],
      [
        'id' => 'search',
        'name' => 'Search page',
      ],
      [
        'id' => 'blog',
        'name' => 'Blog',
      ],
      [
        'id' => 'front_page',
        'name' => 'Front page',
      ],
      [
        'id' => 'single_page',
        'name' => 'Single page',
      ],
      [
        'id' => 'post_tags',
        'name' => 'Post tags ',
      ],
      [
        'id' => 'all_post_archives',
        'name' => 'All post archives',
      ],
      [
        'id' => 'post_categories',
        'name' => 'Post categories',
      ],
      [
        'id' => 'page_id',
        'name' => 'Page ID'
      ],
      [
        'id' => 'post_id',
        'name' => 'Post ID'
      ],
    ];
    if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
      $woo = [
        [
          'id' => 'woo_shop',
          'name' => 'WooCommerce Shop Page'
        ],
        [
          'id' => 'woo_product_single',
          'name' => 'WooCommerce Single Product'
        ],
        [
          'id' => 'all_product_archives',
          'name' => 'WC All Products'
        ],
        [
          'id' => 'all_product_categories',
          'name' => 'WC Product Categories'
        ],
        [
          'id' => 'all_product_tags',
          'name' => 'WC Product tags'
        ],
      ];
      $rules = array_merge($rules, $woo);
    }
    return $rules;
  }

  public function save_builder_conditions_metabox($post_id)
  {

    // Check if our nonce is set.
    if (! isset($_POST['display_conditions_nonce'])) {
      return;
    }

    // Verify that the nonce is valid.
    if (! wp_verify_nonce($_POST['display_conditions_nonce'], 'display_conditions_nonce')) {
      return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return;
    }

    // Check the user's permissions.
    if (isset($_POST['post_type']) && 'tophive-header' == $_POST['post_type']) {

      if (! current_user_can('edit_page', $post_id)) {
        return;
      }
    } else {

      if (! current_user_can('edit_post', $post_id)) {
        return;
      }
    }

    /* OK, it's safe for us to save the data now. */

    // Make sure that it is set.
    if (! isset($_POST['display_conditions'])) {
      return;
    }

    // Sanitize user input.
    $conditions = sanitize_text_field($_POST['display_conditions']);

    // Update the meta field in the database.
    update_post_meta($post_id, '_display_conditions', $conditions);
  }

  public function get_all_pages()
  {
    $pages = get_posts([
      'post_type' => 'page',
      'fields' => 'ids'
    ]);


    $all_pages = [];
    foreach ($pages as $page_id) {
      $all_pages[] = [
        'id' => $page_id,
        'title' => get_the_title($page_id)
      ];
    }
    return $all_pages;
  }

  public function get_all_posts()
  {
    $posts = get_posts([
      'post_type' => 'post',
      'fields' => 'ids'
    ]);
    $all_posts = [];

    foreach ($posts as $post_id) {
      $all_posts[] = [
        'id' => $post_id,
        'title' => get_the_title($post_id)
      ];
    }
    return $all_posts;
  }

  public function tophive_core_load_internal_scripts()
  {
    global $post;
    wp_enqueue_style('th-core-cpts', TH_CORE_URL . 'assets/build/cpts.css', array(), TH_CORE_PLUGIN_VERSION, 'all');
    wp_enqueue_script('th-core-cpts', TH_CORE_URL . 'assets/js/admin-scripts.js', ['jquery'], TH_CORE_PLUGIN_VERSION, true);

    if (!empty($post)) {

      wp_localize_script('th-core-cpts', 'th_elem_locals', [
        'header_conditions' => get_post_meta($post->ID, '_display_conditions', true),
        'all_pages' => json_encode($this->get_all_pages()),
        'all_posts' => json_encode($this->get_all_posts()),
        'condition_rules' => json_encode($this->condition_rules())
      ]);
    }
  }

  public function display_condition_metabox()
  {
    add_meta_box(
      'th-core-display-conditions',
      __('Display condition', 'sitepoint'),
      [$this, 'display_condition_metabox_cb'],
      'tophive-header'
    );
    add_meta_box(
      'th-core-display-conditions',
      __('Display condition', 'sitepoint'),
      [$this, 'display_condition_metabox_cb'],
      'tophive-footer'
    );
  }
  function display_condition_metabox_cb($post)
  {
    // Add a nonce field so we can check for it later.
    wp_nonce_field('display_conditions_nonce', 'display_conditions_nonce');
    $value = get_post_meta($post->ID, '_display_conditions', true);

  ?>
    <div class="display-conditions-container">
      <h3>Display conditions</h3>
      <p>This condition is used to set where this header will be viewed or not.</p>
      <div class="main-controls">
        <div class="controls-inner">
          <?php
          $condition_types = ['in', 'out'];
          $conditions = json_decode($value);
          if (is_array($conditions)) {
            foreach ($conditions as $condition) {
              $id = explode('head_', $condition->id);
          ?>
              <div class="single-control" id="th_elem_head_<?php echo $id[1]; ?>">
                <div class="concluder">
                  <select class="condition_types" data-conditionid="<?php echo $id[1]; ?>">
                    <option value="in" <?php echo $condition->conditions->type === 'in' ? 'selected' : ''; ?>>Include</option>
                    <option value="out" <?php echo $condition->conditions->type === 'out' ? 'selected' : ''; ?>>Exclude</option>
                  </select>
                </div>
                <div class="dropdown_rules">
                  <select class="condition_rules" data-conditionid="<?php echo $id[1]; ?>">
                    <?php
                    foreach ($this->condition_rules() as $value) {

                    ?>
                      <option value="<?php echo $value['id']; ?>" <?php echo $condition->conditions->rule === $value['id'] ? 'selected' : ''; ?>><?php echo $value['name']; ?></option>';
                    <?php
                    }
                    ?>
                  </select>
                  <select name="all_pages" data-conditionid="<?php echo $id[1]; ?>" class="all_page_list <?php echo $condition->conditions->rule === 'page_id' ? '' : 'hidden' ?> list-<?php echo $id[1]; ?>">
                    <option value="">Select page</option>
                    <?php
                    foreach ($this->get_all_pages() as $page) {
                    ?>
                      <option value="<?php echo $page['id']; ?>" <?php echo $condition->conditions->payload == $page['id'] ? 'selected' : ''; ?>><?php echo $page['title']; ?></option>
                    <?php
                    }
                    ?>
                  </select>
                  <select name="all_posts" data-conditionid="<?php echo $id[1]; ?>" class="all_post_list  <?php echo $condition->conditions->rule === 'post_id' ? '' : 'hidden' ?>  list-<?php echo $id[1]; ?>">
                    <option value="">Select post</option>
                    <?php
                    foreach ($this->get_all_posts() as $val) {
                    ?>
                      <option value="<?php echo $val['id']; ?> <?php echo $condition->conditions->payload == $val['id'] ? 'selected' : ''; ?>"><?php echo $val['title']; ?></option>
                    <?php
                    }
                    ?>
                  </select>
                </div>
                <svg class="btn-th-condition-remove" data-id="th_elem_head_<?php echo $id[1]; ?>" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                  <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                </svg>
              </div>
          <?php
            }
          }

          ?>
        </div>

        <?php
        $post_conditions = get_post_meta($post->ID, '_display_conditions', true);
        ?>
        <input type="hidden" id="display_conditions" name="display_conditions" value='<?php echo $post_conditions; ?>' />
        <button class="button button-primary btn-th-display-condition">Add display condition</button>
      </div>
    </div>
<?php

  }

  public function get_component_id($type)
  {
    $posts = get_posts([
      'post_type' => $type,
      'fields' => 'ids'
    ]);

    if (count($posts) < 1) {
      return false;
    }

    $all_matches = [];
    $conditions_manager = new Tophive_Conditions_Manager();

    if (!is_array($posts)) {
      $posts = [];
    }
    foreach ($posts as $post_id) {
      $get_conditions = get_post_meta($post_id, '_display_conditions', true);
      $post_conditions = json_decode($get_conditions, true);
      if (!is_array($post_conditions)) {
        $post_conditions = []; // Ensure it's an array
      }
      foreach ($post_conditions as $key => $condition) {
        $condition['post_id'] = $post_id;
        $post_conditions[$key] = $condition;
      }
      $matches = $conditions_manager->condition_matches($post_conditions);
      if (is_array($matches)) {
        $all_matches = array_merge($all_matches, $matches);
      }
    }

    $global = array_filter($all_matches, function ($el) {
      return $el['conditions']['rule'] == 'all';
    });
    if (count($global) < count($all_matches)) {
      $all_matches = array_values(array_filter($all_matches, function ($el) {
        return $el['conditions']['rule'] != 'all';
      }));
    }
    if (empty($all_matches)) {
      return false;
    }
    return $all_matches[0]['post_id'];
  }

  public function render_elementor_component($post_id)
  {
    $frontend = new \Elementor\Frontend();
    echo $frontend->get_builder_content_for_display($post_id, $with_css = true);
  }

  public function tophive_elementor_builder_defaults()
  {

    $supported_cpts = get_option('elementor_cpt_support', []);
    $supported_cpts = array_merge($supported_cpts, ['tophive-header', 'tophive-footer', 'tophive-popup', 'tophive-forms']);
    update_option('elementor_cpt_support', $supported_cpts);
  }

  public function tophive_register_cpts()
  {
    $args_header = array(
      'label'               => esc_html__('Header', 'tophive'),
      'labels'              => [
        'name'                => esc_html_x('Headers', 'Post Type General Name', 'tophive'),
        'singular_name'       => esc_html_x('Header', 'Post Type Singular Name', 'tophive'),
      ],
      'supports'            => array('title', 'editor'),
      'hierarchical'        => false,
      'public'              => true,
      'show_ui'             => true,
      'show_in_menu'        => 'tophive-builder',
      'menu_position'       => 25,
      'menu_icon'           => 'dashicons-align-center',
      'show_in_admin_bar'   => true,
      'show_in_nav_menus'   => false,
      'can_export'          => true,
      'has_archive'         => false,
      'exclude_from_search' => true,
      'publicly_queryable'  => true,
      'rewrite'             => false,
      'capability_type'     => 'page',
    );
    $args_footer = array(
      'label'               => esc_html__('Footer', 'tophive'),
      'labels'              => [
        'name'              => esc_html_x('Footers', 'Post Type General Name', 'tophive'),
        'singular_name'     => esc_html_x('Footer', 'Post Type Singular Name', 'tophive'),
      ],
      'supports'            => array('title', 'editor'),
      'hierarchical'        => false,
      'public'              => true,
      'show_ui'             => true,
      'show_in_menu'        => 'tophive-builder',
      'menu_position'       => 26,
      'menu_icon'           => 'dashicons-align-center',
      'show_in_admin_bar'   => true,
      'show_in_nav_menus'   => false,
      'can_export'          => true,
      'has_archive'         => false,
      'exclude_from_search' => true,
      'publicly_queryable'  => true,
      'rewrite'             => false,
      'capability_type'     => 'page',
    );
    $args_builder = array(
      'label'               => esc_html__('Tophive Builder', 'tophive'),
      'labels'              => [
        'name'              => esc_html_x('Tophive Builder', 'Post Type General Name', 'tophive'),
        'singular_name'     => esc_html_x('Tophive Builder', 'Post Type Singular Name', 'tophive'),
      ],
      'supports'            => array('title', 'editor'),
      'hierarchical'        => false,
      'public'              => true,
      'show_ui'             => true,
      'show_in_menu'        => false,
      'menu_icon'           => 'dashicons-align-center',
      'show_in_admin_bar'   => false,
      'show_in_nav_menus'   => true,
      'can_export'          => true,
      'has_archive'         => false,
      'exclude_from_search' => true,
      'publicly_queryable'  => true,
      'rewrite'             => false,
      'capability_type'     => 'post',
    );

    register_post_type('tophive-header', $args_header);
    register_post_type('tophive-footer', $args_footer);
    register_post_type('tophive-builder', $args_builder);
  }
}

new Tophive_Core_Builder();
