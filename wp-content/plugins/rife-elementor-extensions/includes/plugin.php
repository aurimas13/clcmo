<?php
namespace Apollo13_REE;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 *
 * The main plugin handler class is responsible for initializing Plugin. The
 * class registers and all the components required to run the plugin.
 *
 * @since 1.0.0
 */
final class Plugin {

	/**
	 * Minimum Elementor version.
	 *
	 * Minimum Elementor version needed for Elementor extensions to work.
	 *
	 * @since 1.0.0
	 */
	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

	/**
	 * Instance.
	 *
	 * Holds the plugin instance.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @var Plugin
	 */
	public static $instance = null;


	/**
	 * Admin.
	 *
	 * Holds the plugin admin.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var Admin
	 */
	public $admin;


	/**
	 * Importer.
	 *
	 * Handles import page & import process
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var Importer
	 */
	public $importer;


	/**
	 * Importer.
	 *
	 * Handles Elementor widgets
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @var Widgets
	 */
	public $widgets;


	/**
	 * Clone.
	 *
	 * Disable class cloning and throw an error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object. Therefore, we don't want the object to be cloned.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong.', 'rife-elementor-extensions'  ), '1.0.0' );
	}

	/**
	 * Wakeup.
	 *
	 * Disable unserializing of the class.
	 *
	 * @access public
	 * @since 1.0.0
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Something went wrong.', 'rife-elementor-extensions'  ), '1.0.0' );
	}

	/**
	 * Instance.
	 *
	 * Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();

			/**
			 * Plugin loaded.
			 *
			 * Fires when Plugin was fully loaded and instantiated.
			 *
			 * @since 1.0.0
			 */
			do_action( 'a13ree/loaded' );
		}

		return self::$instance;
	}

	/**
	 * Init.
	 *
	 * Initialize Plugin.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init() {
		if ( is_admin() ){
			$this->admin = new Admin();
			$this->importer = new Importer();
		}

		// Check for required Elementor version
		if ( defined('ELEMENTOR_VERSION') && ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return;
		}
		else{
			$this->widgets = new Widgets();
		}


		/**
		 * Plugin init.
		 *
		 * Fires on Plugin init, after Plugin has finished loading but
		 * before any headers are sent.
		 *
		 * @since 1.0.0
		 */
		do_action( 'a13ree/init' );
	}


	public function admin_notice_minimum_elementor_version() {
		echo '<div class="notice notice-warning is-dismissible">'.
		     wpautop(
			     sprintf(
			     /* translators: %s: Required Elementor version */
				     esc_html__( 'Rife Elementor Extensions plugin requires Elementor version %s or greater.', 'rife-elementor-extensions' ),
				     self::MINIMUM_ELEMENTOR_VERSION
			     )
		     ).'</div>';
	}


	/**
	 * Register autoloader.
	 *
	 * Autoloader loads all the classes needed to run the plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function register_autoloader() {
		/** @noinspection PhpIncludeInspection */
		require A13REE_PATH . '/includes/autoloader.php';

		Autoloader::run();
	}

	/**
	 * Plugin constructor.
	 *
	 * Initializing Plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function __construct() {
		$this->register_autoloader();

		add_action( 'init', [ $this, 'init' ], 0 );
	}
}

Plugin::instance();