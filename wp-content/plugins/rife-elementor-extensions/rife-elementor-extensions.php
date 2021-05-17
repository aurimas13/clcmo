<?php
/**
 * Plugin Name:			Rife Elementor Extensions &amp; Templates
 * Plugin URI:			https://apollo13themes.com/rife-elementor-extensions
 * Description:			Brings new widgets to be used in Elementor and allows you to import beautiful full page templates for Elementor page builder designed by Apollo13Themes
 * Author:				Apollo13Themes
 * Author URI:			https://apollo13themes.com/
 * License:             GPLv2 or later
 * License URI:         https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least:	4.7
 * Tested up to:		5.7
 * Version:				1.1.8
 * Text Domain:         rife-elementor-extensions
 * Elementor tested up to: 3.2.3
 *
 */

//no double instances
if(defined('A13REE_PATH')){
	return;
}


//plugin constants
define( 'A13REE_PATH', plugin_dir_path( __FILE__ ) );
define( 'A13REE_PLUGIN_BASE', plugin_basename( __FILE__ ) );
define( 'A13REE_PLUGIN_NAME', 'Rife Elementor Extensions &amp; Templates' );

define( 'A13REE_PLUGIN_URL', plugins_url( '/', __FILE__ ) );

define( 'A13REE_ASSETS_URL', A13REE_PLUGIN_URL . 'assets/' );


$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'), false);
$plugin_version = $plugin_data['Version'];
define ( 'A13REE_VERSION', $plugin_version );


//load text domain to translate notices
add_action( 'plugins_loaded', 'a13ree_load_plugin_textdomain' );

//check minimal requirements
if ( ! version_compare( PHP_VERSION, '5.4', '>=' ) ) {
	add_action( 'admin_notices', 'a13ree_fail_php_version' );
} elseif ( ! version_compare( get_bloginfo( 'version' ), '4.7', '>=' ) ) {
	add_action( 'admin_notices', 'a13ree_fail_wp_version' );
} else {
	/** @noinspection PhpIncludeInspection */
	require( A13REE_PATH . 'includes/plugin.php' );
}


/**
 * Load plugin text domain.
 *
 * @since 1.0.0
 *
 * @return void
 */
function a13ree_load_plugin_textdomain() {
	load_plugin_textdomain( 'rife-elementor-extensions' );
}

/**
 * Admin notice for minimum PHP version.
 *
 * Warning when the site doesn't have the minimum required PHP version.
 *
 * @since 1.0.0
 *
 * @return void
 */
function a13ree_fail_php_version() {
	echo '<div class="error">'.
	     wpautop(
			sprintf(
				/* translators: %s: PHP version */
				esc_html__( 'Rife Elementor Extensions plugin requires PHP version %s+, plugin is currently NOT RUNNING.', 'rife-elementor-extensions' ),
				'5.4'
			)
	     ).'</div>';
}

/**
 * Admin notice for minimum WordPress version.
 *
 * Warning when the site doesn't have the minimum required WordPress version.
 *
 * @since 1.0.0
 *
 * @return void
 */
function a13ree_fail_wp_version() {
	echo '<div class="error">'.
	     wpautop(
			sprintf(
			/* translators: %s: WordPress version */
				esc_html__( 'Rife Elementor Extensions plugin requires WordPress version %s+. Because you are using an earlier version, the plugin is currently NOT RUNNING.', 'rife-elementor-extensions' ),
				'4.7'
			)
	     ).'</div>';
}


/**
 * Check if Elementor is active.
 *
 * @since 1.0.0
 *
 * @return bool
 */
function a13ree_check_for_elementor() {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	return is_plugin_active( 'elementor/elementor.php' );
}
