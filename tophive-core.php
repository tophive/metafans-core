<?php
/**
 * Tophive Core Plugin
 *
 * @package TophiveCore
 * @since 1.0.0
 *
 * Plugin Name: Tophive Core
 * Description: Tophive Core Plugin for Tophive Theme and Elementor Page Builder. Includes Elementor Premium addons for Elementor.
 * Plugin URI:  https://tophivetheme.com/
 * Version:     3.0.0
 * Author:      Tophive Team
 * Author URI:  https://codecanyon.net/user/tophive
 * Text Domain: tophive-core
 *
 */
if (! defined('ABSPATH')) { exit; }

/**
 * Main Tophive Core Class
 *
 * @since 1.0.0
 */
final class Tophive_Core {

  /**
   * Addon Version
   *
   * @since 1.0.0
   * @var string The addon version.
   */
  const VERSION = '1.0.0';

  /**
   * Minimum Elementor Version
   *
   * @since 1.0.0
   * @var string Minimum Elementor version required to run the addon.
   */
  const MINIMUM_ELEMENTOR_VERSION = '3.21.0';

  /**
   * Minimum PHP Version
   *
   * @since 1.0.0
   * @var string Minimum PHP version required to run the addon.
   */
  const MINIMUM_PHP_VERSION = '7.4';

  /**
   * Instance
   *
   * @since 1.0.0
   * @access private
   * @static
   * @var \Tophive_Core\Plugin The single instance of the class.
   */
  private static $_instance = null;

  /**
   * Instance
   *
   * Ensures only one instance of the class is loaded or can be loaded.
   *
   * @since 1.0.0
   * @access public
   * @static
   * @return \Tophive_Core\Plugin An instance of the class.
   */
  public static function instance() {
  
    if (is_null(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  /**
   * Constructor
   *
   * Loads definitions
   * Perform some compatibility checks to make sure basic requirements are meet.
   * If all compatibility checks pass, initialize the functionality.
   *
   * @since 1.0.0
   * @access public
   */
  public function __construct() {

    // Define constants used by the plugin
    $this->definitions();
    if ($this->is_compatible()) {
      // Initialize the plugin resources
      add_action('elementor/init', [$this, 'init_plugin_resources']);
      include_once "elementor/tophive-elementor-base.php";

    }

    // Load Elementor editor scripts and styles
    add_action('elementor/editor/after_enqueue_scripts', [$this, 'elementor_load_scripts']);
    add_action('elementor/preview/enqueue_styles', [$this, 'elementor_preview_styles']);

    // Load importer/exporter and ajax files for theme builder page in admin panel only
    include_once TH_CORE_PATH . 'includes/importer.php';
    include_once TH_CORE_PATH . 'includes/ajax.php';

    // Load generated Custom Post Types
    if (file_exists(TH_CORE_PATH . 'includes/cpt-loader.php')) { include_once TH_CORE_PATH . 'includes/cpt-loader.php'; }

    // Add theme menu page in admin panel
    add_action('admin_menu', function () {
      add_menu_page(__('Theme', TH_CORE_SLUG), 'Theme', 'manage_options', 'theme-page', [$this, 'theme_page_markup'], 'dashicons-chart-pie', 1);
    });
    // Enqueue admin scripts for theme page only
    add_action("admin_enqueue_scripts", function ($hook) {
      if ($hook === "toplevel_page_theme-page") {
        wp_enqueue_script('th-app-script', TH_CORE_URL . 'assets/build/app.js', ['jquery'], TH_CORE_PLUGIN_VERSION, true);
        wp_localize_script("th-app-script", "th_local", ["ajax_url" => admin_url('admin-ajax.php'), "customizer_url" => admin_url("customize.php")]);
        wp_enqueue_style('th-app-style', TH_CORE_URL . 'assets/build/app.css', [], TH_CORE_PLUGIN_VERSION);
      }
    });
  }
  /**
   * Theme Page Markup
   *
   * @since 1.0.0
   * @access public
   */
  public function theme_page_markup() {
    echo "<div id='th-root'></div>";
  }

  /**
   * 
   * Defines plugin constants.
   *
   * @since 1.0.0
   * @access public
   */
  public function definitions() {
    // Plugin version constant. Used for cache busting, etc.
    define('TH_CORE_SLUG', 'tophive-core');
    define('TH_CORE_PLUGIN_VERSION', self::VERSION);
    define('TH_CORE_DISPLAY_NAME', 'TophiveCore');
    define('TH_CORE_DISPLAY_NAME_SC', 'TH');
    define('TH_CORE_PATH', plugin_dir_path(__FILE__));
    define('TH_CORE_URL', plugin_dir_url(__FILE__));

  }

  /**
   * Compatibility Checks
   *
   * Checks whether the site meets the addon requirement.
   *
   * @since 1.0.0
   * @access public
   */
  public function is_compatible() {

    // Check if Elementor is installed and activated
    if (! did_action('elementor/loaded')) {
      add_action('admin_notices', [$this, 'admin_notice_missing_main_plugin']);
      return false;
    }

    // Check for required Elementor version
    if (! version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
      add_action('admin_notices', [$this, 'admin_notice_minimum_elementor_version']);
      return false;
    }

    // Check for required PHP version
    if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
      add_action('admin_notices', [$this, 'admin_notice_minimum_php_version']);
      return false;
    }

    return true;
  }

  /**
   * Admin notice
   *
   * Warning when the site doesn't have Elementor installed or activated.
   *
   * @since 1.0.0
   * @access public
   */
  public function admin_notice_missing_main_plugin() {

    if (isset($_GET['activate'])) unset($_GET['activate']);
    // Message to be displayed to the user
    $message = sprintf(
      /* translators: 1: Plugin name 2: Elementor */
      esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'elementor-test-addon'),
      '<strong>' . esc_html__('Elementor Test Addon', 'elementor-test-addon') . '</strong>',
      '<strong>' . esc_html__('Elementor', 'elementor-test-addon') . '</strong>'
    );

    printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
  }

  /**
   * Admin notice
   *
   * Warning when the site doesn't have a minimum required Elementor version.
   *
   * @since 1.0.0
   * @access public
   */
  public function admin_notice_minimum_elementor_version() {

    if (isset($_GET['activate'])) unset($_GET['activate']);
    // Message to be displayed to the user
    $message = sprintf(
      /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
      esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'elementor-test-addon'),
      '<strong>' . esc_html__('Elementor Test Addon', 'elementor-test-addon') . '</strong>',
      '<strong>' . esc_html__('Elementor', 'elementor-test-addon') . '</strong>',
      self::MINIMUM_ELEMENTOR_VERSION
    );

    printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
  }

  /**
   * Load Styles
   *
   * @since 1.0.0
   * @access public
   */
  public function elementor_preview_styles() {
    wp_enqueue_style('th-elementor-lib', TH_CORE_URL . 'assets/build/elementor.css', array(), TH_CORE_PLUGIN_VERSION, 'all');
  }
  
  /**
   * Load Scripts
   *
    * @since 1.0.0
   * @access public
   */
  public function elementor_load_scripts() {

    if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
      // Enqueue jQuery if it's not loaded
      wp_enqueue_style('th-elementor-lib', TH_CORE_URL . 'assets/build/elementor.css', array(), TH_CORE_PLUGIN_VERSION, 'all');
      wp_enqueue_script('jquery');
      // Enqueue the custom script
      wp_enqueue_script('th-elementor-lib', TH_CORE_URL . 'assets/build/elementor.js', ['jquery'], TH_CORE_PLUGIN_VERSION, true);
      wp_localize_script("th-elementor-lib", "th_local", ["ajax_url" => admin_url('admin-ajax.php')]);
    }
  }

  /**
   * Admin notice
   *
   * Warning when the site doesn't have a minimum required PHP version.
   *
   * @since 1.0.0
   * @access public
   */
  public function admin_notice_minimum_php_version() {

    if (isset($_GET['activate'])) unset($_GET['activate']);
    // Message to be displayed to the user
    $message = sprintf(
      /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
      esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'elementor-test-addon'),
      '<strong>' . esc_html__('Elementor Test Addon', 'elementor-test-addon') . '</strong>',
      '<strong>' . esc_html__('PHP', 'elementor-test-addon') . '</strong>',
      self::MINIMUM_PHP_VERSION
    );

    printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
  }

  /**
   * Initialize
   *
   * Load the addons functionality only after Elementor is initialized.
   *
   * Fired by `elementor/init` action hook.
   *
   * @since 1.0.0
   * @access public
   */
  public function init_plugin_resources() {
    include_once TH_CORE_PATH . 'includes/builder/menu.php';
    if (defined('ELEMENTOR_VERSION') && is_callable('Elementor\Plugin::instance')) {
      include_once TH_CORE_PATH . 'includes/builder/menu.php';
      include_once TH_CORE_PATH . 'includes/builder/builder.php';
      include_once TH_CORE_PATH . 'includes/builder/conditions-manager.php';
      include_once TH_CORE_PATH . 'includes/builder/header.php';
      include_once TH_CORE_PATH . 'includes/builder/footer.php';
    }
  }

}
// Run Tophive Core Plugin Instance
Tophive_Core::instance();
