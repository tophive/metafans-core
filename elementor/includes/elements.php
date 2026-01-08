<?php
/**
 * tophive-core Elementor Integration
 *
 * @package TophiveElementor
 */
namespace TophiveElementor;
/**
 * tophive-core Elementor Integration Class
 * @since 1.0.0
 */
class Tophive_Elementor {

  // Constructor for initialization
  public function __construct() {

    // Register widget scripts and styles
    add_action('elementor/frontend/after_register_scripts', [$this, 'register_widget_scripts']); // Register widget scripts
    add_action('elementor/frontend/after_register_styles', [$this, 'register_widget_styles']);  // Register widget styles

    // Register widgets and categories
    add_action('elementor/widgets/register', [$this, 'register_widgets']); // Register custom widgets
    add_action('elementor/elements/categories_registered', [$this, 'th_register_elementor_categories'], 5);
    add_action('wp_enqueue_scripts', [$this, 'themeslug_enqueue_script']); // For general frontend scripts

    // AJAX Handlers for Cart Operations
    add_action("wp_ajax_nopriv_update_cart_content", [$this, 'update_cart_content']);
    add_action("wp_ajax_update_cart_content",        [$this, 'update_cart_content']);
    add_action('wp_ajax_update_cart_item_quantity', [$this, 'update_cart_item_quantity']);
    add_action('wp_ajax_nopriv_update_cart_item_quantity', [$this, 'update_cart_item_quantity']);
    add_action('wp_ajax_remove_cart_item', [$this, 'remove_cart_item']);
    add_action('wp_ajax_nopriv_remove_cart_item', [$this, 'remove_cart_item']);

    // User Registration Handler 
    add_action('wp_ajax_tophive_register_user', [$this, 'tophive_register_user']);
    add_action('wp_ajax_nopriv_tophive_register_user', [$this, 'tophive_register_user']);

    // Mega Menu Editor Handler
    add_action('elementor/editor/before_enqueue_scripts', function () {
      wp_enqueue_script('mega_menu_editor_handle', TH_ELEMENTOR_URL . "assets/js/mega-menu-editor.js", ['nested-elements'], TH_ELEMENTOR_PLUGIN_VERSION, false);
    });

    // Register AJAX actions
    add_action('wp_ajax_custom_search', [$this, 'custom_ajax_search']);
    add_action('wp_ajax_nopriv_custom_search', [$this, 'custom_ajax_search']);

  }
  
  // Register external libraries as dependencies for widgets
  function register_widget_scripts() {

    // Register the main widget bundle script with dependencies and version control
    wp_register_script('tophive-elementor-bundle', TH_ELEMENTOR_URL . 'assets/dist/elementor.bundle.js', ['jquery', 'elementor-frontend', 'gsap', 'three-js'], TH_ELEMENTOR_PLUGIN_VERSION, true);
    
    // Register GSAP scripts with dependencies and version control
    wp_register_script('gsap', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js', [], '3.12.5', true);
    wp_register_script('gsap-draggable', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/Draggable.min.js', ['gsap'], '3.12.2', true);
    wp_register_script('gsap-inertia', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/InertiaPlugin.min.js', ['gsap'], '3.12.2', true);
    wp_register_script('hammerjs', 'https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js', [], '2.0.8', true);
    // Register Three.js script with dependencies and version control
    wp_register_script('three-js', 'https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js', [], 'r128', true);
    wp_register_script( 'fancybox-js', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js', [], null, true );

  }

  // Register all necessary styles for widgets
  function register_widget_styles() {
      wp_register_style('tophive-elements-css', TH_ELEMENTOR_URL . 'assets/css/elements.css', [], '1.0');
      wp_register_style( 'fancybox-css', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css', [], null );
  }

  // AJAX Search Handler
  function custom_ajax_search() {

    if (!isset($_POST['query'])) {
      wp_send_json_error(array("message" => "No search query provided."));
      wp_die();
    }

    $search_query = sanitize_text_field($_POST['query']);
    $post_types = isset($_POST['post_types']) ? explode(',', sanitize_text_field($_POST['post_types'])) : ['post'];
    $results_per_page = isset($_POST['results_per_page']) ? intval($_POST['results_per_page']) : 6;

    $args = array(
      's'              => $search_query,
      'post_type'      => $post_types,
      'posts_per_page' => $results_per_page,
    );

    $search_results = new \WP_Query($args);
    $results = array();

    if ($search_results->have_posts()) {
      while ($search_results->have_posts()) {
        $search_results->the_post();
        $results[] = array(
          'title' => wp_trim_words(get_the_title(), 8, '...'),
          'url'   => get_permalink(),
          'image' => get_the_post_thumbnail_url(get_the_ID(), 'medium') ?: 'https://via.placeholder.com/150',
        );
      }
      wp_reset_postdata();
    }

    if (!empty($results)) {
      wp_send_json_success($results);
    } else {
      wp_send_json_success(array());
    }

    wp_die();
  }

  // User Registration Handler
  function tophive_register_user() {
    // Validate input
    if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])) {
      wp_send_json_error(['message' => 'All fields are required.']);
    }

    $username = sanitize_user($_POST['username']);
    $email = sanitize_email($_POST['email']);
    $password = sanitize_text_field($_POST['password']);

    if (username_exists($username) || email_exists($email)) {
      wp_send_json_error(['message' => 'Username or email already exists.']);
    }

    // Create new user
    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
      wp_send_json_error(['message' => $user_id->get_error_message()]);
    }

    wp_send_json_success(['message' => 'Registration successful!']);
  }

  // Helper function to get cart content data for AJAX responses
  private function _get_cart_content_data() {
    if (!function_exists('WC') || !WC()->cart) {
        return ['success' => false, 'message' => 'WooCommerce is not active or cart is null'];
    }

    ob_start();
    echo '<ul class="cart-list">';
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
      $product = $cart_item['data'];
      $quantity = $cart_item['quantity'];
      $product_id = $product->get_id();
      $product_price = wc_price($cart_item['line_total']);

      echo '<li class="cart-item" data-cart-item-key="' . esc_attr($cart_item_key) . '">
					<img src="' . get_the_post_thumbnail_url($product_id, 'thumbnail') . '" />
					<span class="cart-item-name">' . esc_html($product->get_name()) . '</span>
					<span class="cart-item-price">' . $product_price . '</span>
	
					<div class="cart-quantity">
						<button class="cart-minus" data-cart-item-key="' . esc_attr($cart_item_key) . '">-</button>
						<input type="number" data-cart-item-key="' . esc_attr($cart_item_key) . '" class="cart-qty" value="' . esc_html($quantity) . '" min="1" />
						<button class="cart-plus" data-cart-item-key="' . esc_attr($cart_item_key) . '">+</button>
					</div>
	
					<button class="cart-remove" data-cart-item-key="' . esc_attr($cart_item_key) . '">×</button>
				</li>';
    }
    echo '</ul>';

    $output = ob_get_clean();
    $cart_subtotal = WC()->cart->get_cart_subtotal();
    $cart_subtotal_raw = WC()->cart->get_subtotal();

    return [
      'html' => $output,
      'subtotal' => $cart_subtotal,
      'subtotal_raw' => $cart_subtotal_raw,
    ];
  }

  // AJAX Handlers for Cart Operations 
  function update_cart_item_quantity() {
    check_ajax_referer('update_cart_nonce', 'nonce');

    if (!isset($_POST['cart_item_key']) || !isset($_POST['quantity'])) {
      wp_send_json_error(['message' => 'Invalid request']);
    }

    $cart_item_key = sanitize_text_field($_POST['cart_item_key']);
    $quantity = intval($_POST['quantity']);

    if ($quantity > 0) {
        WC()->cart->set_quantity($cart_item_key, $quantity);
    }

    WC()->cart->calculate_totals();
    wp_send_json_success($this->_get_cart_content_data());
  }

  // AJAX Handler to Remove Cart Item
  function remove_cart_item() {
    check_ajax_referer('update_cart_nonce', 'nonce');

    if (!isset($_POST['cart_item_key'])) {
      wp_send_json_error(['message' => 'Invalid request']);
    }

    $cart_item_key = sanitize_text_field($_POST['cart_item_key']);
    WC()->cart->remove_cart_item($cart_item_key);
    WC()->cart->calculate_totals();

    wp_send_json_success($this->_get_cart_content_data());
  }

  // AJAX Handler to Update Cart Content
  function update_cart_content() {
    check_ajax_referer('update_cart_nonce', 'nonce');
    $data = $this->_get_cart_content_data();
    if (isset($data['success']) && $data['success'] === false) {
        wp_send_json_error(['message' => $data['message']]);
    } else {
        wp_send_json_success($data);
    }
  }

  // Enqueue frontend and editor scripts and styles and localize AJAX variables
  function themeslug_enqueue_script() {
    // Frontend and editor
    wp_enqueue_style('core-styles', TH_ELEMENTOR_URL . '/assets/build/styles.css', [], TH_ELEMENTOR_PLUGIN_VERSION, 'all');

    // Localize the script with the correct handle
    wp_localize_script('tophive-elementor-bundle', 'tophive_ajax', [
        'ajaxurl' => admin_url('admin-ajax.php'), // Global ajaxurl for all scripts
        'nonce' => wp_create_nonce('update_cart_nonce')
    ]);
  }

  /**
   * Register Categories
   *
   * Fired by `elementor/elements/categories_registered` action hook.
   *
   * @param $elements_manager Elementor widgets manager.
   */
  public function th_register_elementor_categories($elements_manager) {
    // Define new categories to be added 
    $new_categories = [
      'th-general' => [
        'title' => TH_ELEMENTOR_DISPLAY_NAME . esc_html__(' - General', TH_ELEMENTOR_SLUG),
        'icon'  => 'eicon-font',
      ],
      'th-header'  => [
        'title' => TH_ELEMENTOR_DISPLAY_NAME . esc_html__(' - Header', TH_ELEMENTOR_SLUG),
        'icon'  => 'eicon-font',
      ],
    ];
    // Reorder categories to place new ones after 'basic' category
    if (! version_compare(PHP_VERSION, '7.0.0', '<')) {
      $existing_categories = $elements_manager->get_categories();

      $reordered = [];

      foreach ($existing_categories as $key => $category) {
        $reordered[$key] = $category;

        if ('basic' === $key) {
          // Insert new categories right after Basic
          foreach ($new_categories as $new_key => $new_cat) {
            $reordered[$new_key] = $new_cat;
          }
        }
      }
      // Use Closure to set the protected property 'categories' of Elements_Manager class
      $set_categories = function ($categories) {
        $this->categories = $categories;
      };
      $set_categories->call($elements_manager, $reordered);
    } else {
      foreach ($new_categories as $key => $category) {
        $elements_manager->add_category($key, $category);
      }
    }
  }

  /**
   * Register Categories.
   *
   * Fired by `elementor/elements/categories_registered` action hook.
   *
   * @param $elements_manager Elementor widgets manager
   */

  /**
   * Register Widgets.
   *
   * Load widgets files and register new Elementor widgets.
   *
   * Fired by `elementor/widgets/register` action hook.
   * 
   * @param \Elementor\Widgets_Manager $widgets_manager elementor widgets manager
   */
  public function register_widgets($widgets_manager) {
    // Include widget files and register widgets
    require __DIR__ . '/helper-styles.php';
    require __DIR__ . '/helper-controls.php';
    require __DIR__ . '/widgets/button.php';
    require __DIR__ . '/widgets/header-image.php';
    require __DIR__ . '/widgets/header-icon.php';
    require __DIR__ . '/widgets/header-menu.php';
    require __DIR__ . '/widgets/header-search.php';
    require __DIR__ . '/widgets/icon-box.php';
    require __DIR__ . '/widgets/video.php';
    require __DIR__ . '/widgets/image.php';
    require __DIR__ . '/widgets/posts.php';
    require __DIR__ . '/widgets/advanced-heading.php';

    require __DIR__ . '/widgets/tophive-text-effect.php';
    require __DIR__ . '/widgets/gsap-hover-image.php';
    require __DIR__ . '/widgets/gsap-card-gallery.php';
    require __DIR__ . '/widgets/gsap-fancy-text.php';
    require __DIR__ . '/widgets/gsap-text-list-image.php';
    require __DIR__ . '/widgets/gsap-text-parallax.php';
    require __DIR__ . '/widgets/video-player.php';
    require __DIR__ . '/widgets/webgl-slider.php';
    require __DIR__ . '/widgets/osmo-slider.php';
    require __DIR__ . '/widgets/osmo-gallery.php';
    require __DIR__ . '/widgets/osmo-menu.php';
    require __DIR__ . '/widgets/gsap-slider-big.php';
    require __DIR__ . '/widgets/text-highlight.php';
    require __DIR__ . '/widgets/button-group.php';
    require __DIR__ . '/widgets/animated-headline.php';
    require __DIR__ . '/widgets/marquee-image.php';
    require __DIR__ . '/widgets/fancy-slider.php';
    require __DIR__ . '/widgets/circle-rotating-text.php';
    require __DIR__ . '/widgets/stress-ball.php';
    require __DIR__ . '/widgets/hero-tubes-cursor.php';
    require __DIR__ . '/widgets/project-gallery.php';
    require __DIR__ . '/widgets/scroll-section.php';
    require __DIR__ . '/widgets/gsap-menu-nav.php';
    
    require_once __DIR__ . '/widgets/our-team.php';
    $widgets_manager->register(new \My_Core_Plugin\Widgets\Our_Team());
    require_once __DIR__ . '/widgets/gsap-project-card.php';
    $widgets_manager->register(new \My_Core_Plugin\Widgets\Gsap_Project_Card());
    
    // These widgets are now using modern JS handlers, no need to register a PHP class if it doesn't exist
    require_once __DIR__ . '/widgets/webgl-slider.php';
    $widgets_manager->register(new \My_Core_Plugin\Widgets\WebGL_Slider()); // Assuming this has a PHP class
    require_once __DIR__ . '/widgets/lottie-animations.php'; // Assuming this has a PHP class
    $widgets_manager->register(new \My_Core_Plugin\Widgets\Lottie_Animations()); // Assuming this has a PHP class
    require_once __DIR__ . '/widgets/liquid-image.php';
    $widgets_manager->register(new \My_Core_Plugin\Widgets\Liquid_Image_Widget());
    require_once __DIR__ . '/widgets/liquid-morphology-slideshow.php';
    $widgets_manager->register(new \My_Core_Plugin\Widgets\Liquid_Morphology_Slideshow());
    require_once __DIR__ . '/widgets/advanced-image.php'; // Assuming this has a PHP class
    $widgets_manager->register(new \My_Core_Plugin\Widgets\Advanced_Image_Widget()); // Assuming this has a PHP class
    require_once __DIR__ . '/widgets/testimonial-carousel.php'; // Assuming this has a PHP class
    $widgets_manager->register(new \My_Core_Plugin\Widgets\Testimonial_Carousel()); // Assuming this has a PHP class

    require __DIR__ . '/widgets/header-woo-cart.php';
    require __DIR__ . '/widgets/header-account.php';
    require __DIR__ . '/widgets/header-dropdown.php';
    require __DIR__ . '/widgets/mega-menu.php';
    require __DIR__ . '/widgets/offcanvas.php';
    require __DIR__ . '/widgets/advance_url.php';
    require __DIR__ . '/widgets/horizontal_slider.php';
  }
}
// Initialize the Tophive Elementor integration
new Tophive_Elementor();

