<?php
namespace Apollo13_REE;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Custom widgets for Elementor
 *
 * This class handles custom widgets for Elementor
 *
 * @since 1.0.0
 */
class Widgets {
	/**
	 * Registers widgets in Elementor
	 *
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_widgets() {
		/** @noinspection PhpIncludeInspection */
		require_once A13REE_PATH . '/includes/elementor/widgets/writing-effect-headline.php';
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widget_Writing_Effect_Headline );
	}


	/**
	 * Registers widgets scripts
	 *
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function widget_scripts() {
		//typed.js - writing script
		wp_register_script(
			'jquery-typed',
			A13REE_ASSETS_URL .'js/typed.min.js' ,
			[
				'jquery',
			],
			'1.1.4',
			true
		);

		//fronted.js - plugin front-end actions
		wp_register_script(
			'a13ree-frontend',
			A13REE_ASSETS_URL .'js/frontend.js' ,
			[
				'elementor-waypoints',
				'jquery',
			],
			A13REE_VERSION,
			true
		);
	}


	/**
	 * Enqueue widgets scripts in preview mode, as later calls in widgets render will not work,
	 * as it happens in admin env
	 *
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function widget_scripts_preview() {
		wp_enqueue_script( 'jquery-typed' );
		wp_enqueue_script( 'a13ree-frontend' );
	}

	/**
	 * Registers widgets styles
	 *
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function widget_styles() {
		wp_register_style( 'a13ree-frontend', A13REE_ASSETS_URL .'css/frontend.css' );
	}


	/**
	 * Widget constructor.
	 *
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );
		// Register Widget Styles
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'widget_styles' ] );
		// Register Widget Scripts
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'widget_scripts' ] );
		// Enqueue ALL Widgets Scripts for preview
		add_action( 'elementor/preview/enqueue_scripts', [ $this, 'widget_scripts_preview' ] );
	}
}
