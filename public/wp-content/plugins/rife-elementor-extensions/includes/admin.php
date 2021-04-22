<?php
namespace Apollo13_REE;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Plugins admin.
 *
 * Admin handler class is responsible for initializing Plugin in
 * WordPress admin.
 *
 * @since 1.0.0
 */
class Admin {


	/**
	 * Plugin page.
	 *
	 * Holds slug for plugin page.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var string
	 */
	private $plugin_page = 'rife-templates';

	/**
	 * Enqueue admin scripts.
	 *
	 * Registers all the admin scripts and enqueues them.
	 *
	 * Fired by `admin_enqueue_scripts` action.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function enqueue_scripts() {
		wp_register_script(
			'a13ree-admin',
			A13REE_ASSETS_URL . 'js/admin.js',
			[
				'jquery',
			],
			A13REE_VERSION,
			true
		);
	}

	/**
	 * Enqueue admin styles.
	 *
	 * Registers all the admin styles and enqueues them.
	 *
	 * Fired by `admin_enqueue_scripts` action.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function enqueue_styles() {
		wp_register_style(
			'a13ree-admin',
			A13REE_ASSETS_URL . 'css/admin.css',
			[],
			A13REE_VERSION
		);
	}

	/**
	 * Plugin action links.
	 *
	 * Adds action links to the plugin list table
	 *
	 * Fired by `plugin_action_links` filter.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $links An array of plugin action links.
	 *
	 * @return array An array of plugin action links.
	 */
	public function plugin_action_links( $links ) {
		$import_link = sprintf( '<a href="%1$s">%2$s</a>', admin_url( 'admin.php?page=' .$this->plugin_page ), __( 'Import templates', 'rife-elementor-extensions' ) );

		array_unshift( $links, $import_link );

		return $links;
	}

	/**
	 * Admin footer text.
	 *
	 * Modifies the "Thank you" text displayed in the admin footer.
	 *
	 * Fired by `admin_footer_text` filter.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $footer_text The content that will be printed.
	 *
	 * @return string The content that will be printed.
	 */
	public function admin_footer_text( $footer_text ) {
		$current_screen = get_current_screen();
		$is_plugin_screen = ( $current_screen && false !== strpos( $current_screen->id, $this->plugin_page ) );

		if ( $is_plugin_screen ) {
			$footer_text = esc_html__( 'Thanks for using Rife Elementor Extensions &amp; Templates plugin!', 'rife-elementor-extensions' ).
			               ' <a href="https://apollo13themes.com/">'.esc_html__( 'Apollo13Themes Team  &#10084;', 'rife-elementor-extensions' ).'</a>';
		}

		return $footer_text;
	}

	/**
	 * Elementor dashboard widget links
	 *
	 * Adds links in Elementor dashboard widget.
	 *
	 * Fired by `elementor/admin/dashboard_overview_widget/footer_actions` filter.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $additions_actions Elementor dashboard widget footer actions.
	 *
	 * @return array Elementor dashboard widget footer actions.
	 */
	public function dashboard_widget_links( $additions_actions ) {
		$additions_actions['rife-elementor-extensions'] =
			 [
				'title' => __( 'Import templates', 'rife-elementor-extensions' ),
				'link' => admin_url( 'admin.php?page=' .$this->plugin_page ),
			];

		return $additions_actions;
	}

	/**
	 * Admin constructor.
	 *
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ] );

		add_filter( 'plugin_action_links_' . A13REE_PLUGIN_BASE, [ $this, 'plugin_action_links' ] );
		add_filter( 'admin_footer_text', [ $this, 'admin_footer_text' ], 30 );

		add_filter( 'elementor/admin/dashboard_overview_widget/footer_actions', [ $this, 'dashboard_widget_links' ] );
	}
}
