<?php
/**
 * Plugin Name: Eifelhoster Buttons Pro
 * Plugin URI:  https://eifelhoster.de
 * Description: Fügt grafisch gestaltete Buttons in den WordPress Classic Editor ein.
 * Version:     3.0.0
 * Author:      Michael Krämer
 * Author URI:  https://eifelhoster.de
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: eifelhoster-buttons-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EBP_VERSION',    '3.0.0' );
define( 'EBP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'EBP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'EBP_OPTION_KEY', 'ebp_defaults' );

require_once EBP_PLUGIN_DIR . 'includes/class-ebp-helpers.php';
require_once EBP_PLUGIN_DIR . 'includes/class-ebp-shortcode.php';
require_once EBP_PLUGIN_DIR . 'includes/class-ebp-editor.php';
require_once EBP_PLUGIN_DIR . 'includes/class-ebp-admin.php';

add_action( 'plugins_loaded', 'ebp_init' );

function ebp_init() {
	new EBP_Shortcode();
	if ( is_admin() ) {
		new EBP_Admin();
		new EBP_Editor();
	}
}
