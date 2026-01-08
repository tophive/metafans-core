<?php
/**
 * Plugin Name:       {{pluginName}}
 * Plugin URI:        https://example.com/
 * Description:       {{pluginDescription}}
 * Version:           1.0.0
 * Author:            {{pluginAuthor}}
 * Author URI:        https://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       {{pluginSlug}}
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( '{{pluginConstPrefix}}_VERSION', '1.0.0' );
define( '{{pluginConstPrefix}}_PATH', plugin_dir_path( __FILE__ ) );
define( '{{pluginConstPrefix}}_URL', plugin_dir_url( __FILE__ ) );