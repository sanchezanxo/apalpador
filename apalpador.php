<?php
/**
 * Plugin Name:       Apalpador
 * Plugin URI:        https://github.com/sanchezanxo/apalpador
 * Description:       Adds the traditional Galician Christmas character "Apalpador" to your WordPress site with festive visual effects.
 * Version:           2.0.0
 * Requires at least: 5.0
 * Requires PHP:      7.4
 * Author:            Anxo Sánchez
 * Author URI:        https://www.anxosanchez.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       apalpador
 * Domain Path:       /languages
 *
 * @package Apalpador
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin constants.
define( 'APALPADOR_VERSION', '2.0.0' );
define( 'APALPADOR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'APALPADOR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'APALPADOR_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Include required files.
require_once APALPADOR_PLUGIN_DIR . 'includes/class-apalpador-settings.php';
require_once APALPADOR_PLUGIN_DIR . 'includes/class-apalpador-admin.php';
require_once APALPADOR_PLUGIN_DIR . 'includes/class-apalpador-frontend.php';


/**
 * Plugin activation hook.
 *
 * Sets default options on first activation.
 *
 * @return void
 */
function apalpador_activate() {
	if ( false === get_option( 'apalpador_options' ) ) {
		add_option( 'apalpador_options', Apalpador_Settings::get_defaults() );
	}
}
register_activation_hook( __FILE__, 'apalpador_activate' );

/**
 * Plugin deactivation hook.
 *
 * @return void
 */
function apalpador_deactivate() {
	// Options preserved on deactivation, removed on uninstall.
}
register_deactivation_hook( __FILE__, 'apalpador_deactivate' );

/**
 * Initialize the plugin.
 *
 * @return void
 */
function apalpador_init() {
	if ( is_admin() ) {
		new Apalpador_Admin();
	} else {
		new Apalpador_Frontend();
	}
}
add_action( 'plugins_loaded', 'apalpador_init', 20 );
