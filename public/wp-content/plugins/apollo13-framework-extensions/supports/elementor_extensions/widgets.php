<?php
namespace Apollo13_FE;

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
	 * @since 1.5.0
	 * @access public
	 */
	public function register_widgets() {
		//theme widgets will create fatal errors without the theme
		if( !class_exists('Apollo13Framework') ){
			return;
		}

		/** @noinspection PhpIncludeInspection */
		require_once A13FE_BASE_DIR.'supports/elementor_extensions/widgets/socials.php';
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Socials );
		/** @noinspection PhpIncludeInspection */
		require_once A13FE_BASE_DIR.'supports/elementor_extensions/widgets/slider.php';
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Slider );
		/** @noinspection PhpIncludeInspection */
		require_once A13FE_BASE_DIR.'supports/elementor_extensions/widgets/scroller.php';
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Scroller );
		/** @noinspection PhpIncludeInspection */
		require_once A13FE_BASE_DIR.'supports/elementor_extensions/widgets/gallery.php';
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Gallery() );
		/** @noinspection PhpIncludeInspection */
		require_once A13FE_BASE_DIR.'supports/elementor_extensions/widgets/post-list.php';
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Post_List() );
	}


	/**
	 * Registers widgets scripts
	 *
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function widget_scripts() {
		a13fe_theme_scripts();
		if( function_exists( 'apollo13framework_theme_scripts' ) ){
			apollo13framework_theme_scripts();
		}
	}


	/**
	 * Enqueue widgets scripts in preview mode, as later calls in widgets render will not work,
	 * as it happens in admin env
	 *
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function widget_scripts_preview() {
		wp_enqueue_script('apollo13framework-slider');
		wp_enqueue_script('flickity');
		wp_enqueue_script('apollo13framework-isotope');
	}

	/**
	 * Registers widgets styles
	 *
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function widget_styles() {
//		wp_register_style( 'a13ree-frontend', A13REE_ASSETS_URL .'css/frontend.css' );
	}


	/**
	 * Registers widgets custom category
	 *
	 *
	 * @since  1.5.0
	 * @access public
	 *
	 * @param \Elementor\Elements_Manager $elements_manager
	 */
	public function add_elementor_widget_categories( $elements_manager ) {

		$elements_manager->add_category(
			'apollo13-framework',
			[
				'title' => __( 'Apollo13 Framework Extensions', 'apollo13-framework-extensions' ),
				'icon' => 'fa fa-plug',
			]
		);
	}



	/**
	 * Widget constructor.
	 *
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );
		// Register Widget Styles
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'widget_styles' ] );
		// Register Widget Scripts
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'widget_scripts' ] );
		// Enqueue ALL Widgets Scripts for preview
		add_action( 'elementor/preview/enqueue_scripts', [ $this, 'widget_scripts_preview' ], 30 );
		// Register custom categories for widgets
		add_action( 'elementor/elements/categories_registered', [ $this, 'add_elementor_widget_categories' ] );
	}
}
