<?php
namespace Apollo13_REE;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Importer
 *
 * Importer handles import of templates
 *
 * @since 1.0.0
 */
class Importer {


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
	private $importer_page = 'rife-templates';


	/**
	 * Tabs.
	 *
	 * Holds the settings page tabs, sections and fields.
	 *
	 * @access private
	 *
	 * @var array
	 */
	private $tabs;


	/**
	 * Get settings page title.
	 *
	 * Retrieve the title for the settings page.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return string Settings page title.
	 */
	protected function get_page_title() {
		/* translators: %s: Plugin name */
		return sprintf( esc_html__( 'Welcome to %s', 'rife-elementor-extensions' ), esc_html( A13REE_PLUGIN_NAME ) );
	}


	/**
	 * Get tabs.
	 *
	 * Retrieve the settings page tabs, sections and fields.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return array Settings page tabs, sections and fields.
	 */
	public final function get_tabs() {
		return $this->tabs;
	}


	/**
	 * Plugin menu link.
	 *
	 * Adds link to admin menu under Elementor menu
	 *
	 * Fired by `admin_menu` action.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_pages() {
		/* translators: %s: Theme name */
		$temp = A13REE_PLUGIN_NAME;
		add_submenu_page(
			'elementor',
			$temp,
			$temp,
			'manage_options',
			$this->importer_page,
			[ $this, 'display_import_page' ]
		);
	}

	/**
	 * Display import page.
	 *
	 * Output the content for the import page.
	 *
	 * @since 1.0
	 * @access public
	 */
	public function display_import_page() {
		if( !a13ree_check_for_elementor() ){
			$this->fail_elementor();
			return;
		}

		wp_enqueue_script( 'a13ree-admin' );
		wp_enqueue_style( 'a13ree-admin' );
		$tabs = $this->get_tabs();
		?>
		<div class="wrap apollo13-plugin-page about-wrap">
			<h1><?php echo $this->get_page_title(); ?></h1>

			<div class="about-text">
				<?php echo esc_html__( 'Here you can import templates and get more information about plugin. Thanks for being with us!', 'rife-elementor-extensions' ); ?><br />
				<p class="socials"><a class="on-twitter" href="https://twitter.com/apollo13themes" target="_blank">Apollo13Themes on Twitter</a></p>
			</div>

			<nav class="nav-tab-wrapper">
				<?php
				foreach ( $tabs as $tab_id => $tab ) {

					$active_class = '';

					if ( 'import' === $tab_id ) {
						$active_class = ' nav-tab-active';
					}

					echo '<a id="a13-settings-tab-'.esc_attr( $tab_id ).'" class="nav-tab'.esc_attr( $active_class ).'" href="#tab-'.esc_attr( $tab_id ).'">'.esc_html( $tab ).'</a>';
				}
				?>
			</nav>

			<?php

			foreach ( $tabs as $tab_id => $tab ) {

				$active_class = '';

				if ( 'import' === $tab_id ) {
					$active_class = ' active-section';
				}

				echo '<div id="tab-'.esc_attr( $tab_id ).'" class="a13-settings-section'.esc_attr($active_class).'">';

				$section_method = 'tab_section_'.$tab_id;

				if( method_exists( $this, $section_method ) ){
					$this->$section_method();
				}

				echo '</div>';
			}

				?>

		</div><!-- /.wrap -->
		<?php
	}


	/**
	 * Importer main tab.
	 *
	 * Output Importer main tab.
	 *
	 * Called in $this->display_import_page()
	 *
	 * @since 1.0
	 * @access public
	 */
	public function tab_section_import() {
		?>
		<h2><?php esc_html_e( 'Import templates', 'rife-elementor-extensions'); ?></h2>
		<p><a class="image" href="#start-import"><img src="<?php echo A13REE_ASSETS_URL .'images/import/screenshot.jpg' ?>" alt="<?php esc_html_e( 'Image of templates to import.', 'rife-elementor-extensions'); ?>" /></a></p>
		<p><?php echo nl2br( esc_html__( 'Please use below button to import templates to your WordPress installation.
		 More information, on where to find templates after import, can be found in "Plugin Instructions" tab.', 'rife-elementor-extensions') ); ?></p>
		<div class="import-section">
			<button class="button button-primary button-hero" id="start-import"><?php esc_html_e( 'Start Free Pack Import', 'rife-elementor-extensions'); ?></button>
			<div class="status">
				<strong id="demo_data_import_status"><?php esc_html_e( 'The Importer is ready to start.', 'rife-elementor-extensions' ); ?></strong>
				<div class="import_progress_bar"><div class="import_progress"></div></div>
			</div>
		</div>
		<hr />
		<p><?php echo esc_html__( 'Soon we will be adding more templates &amp; widgets. So please stay tuned.', 'rife-elementor-extensions' ); ?>
		<p><a href="https://apollo13themes.com/rife-elementor-extensions/"><?php echo esc_html__( 'Visit plugins page', 'rife-elementor-extensions' ); ?></a></p>
		<?php
	}


	/**
	 * Importer instructions tab.
	 *
	 * Output Importer instructions tab.
	 *
	 * Called in $this->display_import_page()
	 *
	 * @since 1.0
	 * @access public
	 */
	public function tab_section_instructions() {
		?>
		<h2><?php esc_html_e( 'Plugin instructions', 'rife-elementor-extensions'); ?></h2>
		<h3><?php esc_html_e( 'Elementor Templates', 'rife-elementor-extensions'); ?></h3>
		<p><?php echo nl2br( esc_html__( 'After you are done with importing on "Templates import" tab, you will be able to add/use these templates in Elementor.', 'rife-elementor-extensions') ); ?></p>
		<p><?php echo nl2br( esc_html__( 'To use templates in Elementor choose "Add Template" and in "My templates" you will find "Rife Templates".
		Please see below screenshots:', 'rife-elementor-extensions') ); ?></p>
		<p><img src="<?php echo A13REE_ASSETS_URL .'images/instructions/elementor-add-templates.png' ?>" alt="<?php esc_html_e( 'Add templates button in Elementor', 'rife-elementor-extensions'); ?>" /></p>
		<p><img src="<?php echo A13REE_ASSETS_URL .'images/instructions/elementor-insert-templates.png' ?>" alt="<?php esc_html_e( 'Import templates in Elementor', 'rife-elementor-extensions'); ?>" /></p>

		<h3><?php esc_html_e( 'Elementor Widgets', 'rife-elementor-extensions'); ?></h3>
		<p><?php echo nl2br( esc_html__( 'With Rife Elementor Extensions, you also get access to cool widgets that you can use in Elementor. See details below.', 'rife-elementor-extensions') ); ?></p>
		<h4><?php esc_html_e( 'Writing Effect', 'rife-elementor-extensions'); ?></h4>
		<p><?php echo nl2br( esc_html__( 'Writing effect offers you to create titles that are half static, and half written as user scrolls to it. You can achieve with it very interesting effects.', 'rife-elementor-extensions') ); ?></p>
		<p><?php echo nl2br( esc_html__( 'It can be found under name "Writing Effect Headline" in "Basic" Widgets.
		Please see below animation:', 'rife-elementor-extensions') ); ?></p>
		<p><img src="<?php echo A13REE_ASSETS_URL .'images/instructions/writing-effect.gif' ?>" alt="<?php esc_html_e( 'Writing effect in action.', 'rife-elementor-extensions'); ?>" /></p>
		<hr />
		<p><?php echo esc_html__( 'Soon we will be adding more widgets. So please stay tuned.', 'rife-elementor-extensions' ); ?>
		<p><a href="https://apollo13themes.com/rife-elementor-extensions/"><?php echo esc_html__( 'Visit plugins page', 'rife-elementor-extensions' ); ?></a></p>
		<?php
	}


	/**
	 * Importer "more from Apollo13" tab.
	 *
	 * Output info about different stuff from Apollo13Themes
	 *
	 * Called in $this->display_import_page()
	 *
	 * @since 1.0
	 * @access public
	 */
	public function tab_section_apollo13themes() {
		?>
		<h2><?php esc_html_e( 'More from Apollo13Themes', 'rife-elementor-extensions'); ?></h2>
		<h3><?php esc_html_e( 'Rife theme', 'rife-elementor-extensions'); ?></h3>
		<h4><?php esc_html_e( 'More Elementor templates', 'rife-elementor-extensions'); ?></h4>
		<p><img src="<?php echo A13REE_ASSETS_URL .'images/apollo13themes/designs.jpg' ?>" alt="<?php esc_html_e( 'Rife Theme Designs.', 'rife-elementor-extensions'); ?>" /></p>
		<p><?php echo nl2br( esc_html__( 'Would you like to get even more great &amp; beautiful  templates for Elementor?
		In our Rife theme, we have designed whole websites, with use of theme features and Elementor power.
		We call them Designs.', 'rife-elementor-extensions') ); ?></p>
		<p><a href="https://apollo13themes.com/rife/designs/"><?php echo esc_html__( 'Check all Rife Theme Designs.', 'rife-elementor-extensions'); ?></a></p>

		<h4><?php esc_html_e( 'Features beyond page builders', 'rife-elementor-extensions'); ?></h4>
		<p><img src="<?php echo A13REE_ASSETS_URL .'images/apollo13themes/logo-shield-badge.gif' ?>" alt="<?php esc_html_e( 'Shield logo in Rife Theme.', 'rife-elementor-extensions'); ?>" /></p>
		<p><?php echo nl2br( esc_html__( 'With the Rife theme, you get access to powerful features, that can push your projects beyond the limits of Page builders.
		Just check our Rife features!', 'rife-elementor-extensions') ); ?></p>
		<p><a href="https://apollo13themes.com/rife/features/"><?php echo esc_html__( 'Check all Rife Theme Features.', 'rife-elementor-extensions'); ?></a></p>
		<p><img src="<?php echo A13REE_ASSETS_URL .'images/apollo13themes/hidden-footer.gif' ?>" alt="<?php esc_html_e( 'Footer unravel effect in Rife Theme.', 'rife-elementor-extensions'); ?>" /></p>
		<p><a class="button button-primary button-hero" href="https://apollo13themes.com/rife/"><?php esc_html_e( 'Check out Rife Theme', 'rife-elementor-extensions'); ?></a></p>
		<?php
	}


	/**
	 * Admin notice for non active Elementor.
	 *
	 * Warning when the site doesn't have active Elementor plugin
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function fail_elementor() {
		echo '<div class="error">'.
		     wpautop(
			     esc_html__( 'Rife Elementor Extensions plugin requires Elementor plugin to be active. Without it import of templates will not work.', 'rife-elementor-extensions' )
		     ).'</div>';
	}


	/**
	 * Import process
	 *
	 * Controls import process via AJAX calls.
	 *
	 * Fired by `wp_ajax_a13ree_import_templates` action.
	 *
	 * @since 1.0
	 * @access public
	 */
	public function import_templates() {
		$level         = isset( $_POST['level'] )? sanitize_text_field( wp_unslash( $_POST['level'] ) ) : '';
		$sublevel      = isset( $_POST['sublevel'] )? sanitize_text_field( wp_unslash( $_POST['sublevel'] ) ) : '';
		$pack          = isset( $_POST['pack'] )? sanitize_text_field( wp_unslash( $_POST['pack'] ) ) : 'free';
		$sublevel_name = '';
		$log           = '';
		$array_index   = 0;

		$levels = array(
			'_'                     => '', //empty to avoid bonus logic
			'start'                 => esc_html__( 'Starting import', 'rife-elementor-extensions' ),
			'download_files'        => esc_html__( 'Downloading files', 'rife-elementor-extensions' ),
			'install_content'       => esc_html__( 'Importing templates', 'rife-elementor-extensions' ),
			'clean'                 => esc_html__( 'Cleaning...', 'rife-elementor-extensions' ),
			'end'                   => esc_html__( 'Everything done!', 'rife-elementor-extensions' ),
		);

		if($pack === 'free'){
			unset( $levels['start'] );
			unset( $levels['download_files'] );
			unset( $levels['clean'] );
		}

		//get current level key
		if ( strlen( $level ) === 0 ) {
			//get first level to process
			$level = key( $levels );
		}
		else {
			//move array pointer to current importing level
			while ( key( $levels ) !== $level ) {
				//and ask for next one
				next( $levels );
				$array_index++;
			}
			//save new current level
			$level = key( $levels );
		}

		//Execute current level function
		$method = 'import_' . $level;
		if ( method_exists( $this, $method ) ) {
			//no notices or other "echos", we put it in $log
			ob_start();

			$sublevel = $this->$method( $sublevel, $sublevel_name );

			//collect all produced output to log
			$log = ob_get_contents();
			ob_end_clean();

			//should we move to next level
			if ( $sublevel === true ) {
				$sublevel = ''; //reset
				next( $levels );
				$level = key( $levels );
			}
		}
		//no function - move to next level. Some steps are just information without action
		else {
			next( $levels );
			$array_index ++;
			$level = key( $levels );
		}

		//check if this is last element
		$is_it_end = false;
		end( $levels );
		if ( key( $levels ) === $level ) {
			$is_it_end = true;
		}

		//prepare progress info
		$progress = round( 100 * ( 1 + $array_index ) / count( $levels ) );

		$result = [
			'level'         => $level,
			'level_name'    => $levels[ $level ],
			'sublevel'      => $sublevel,
			'sublevel_name' => $sublevel_name,
			'log'           => $log,
			'progress'      => $progress,
			'is_it_end'     => $is_it_end
		];

		//send AJAX response
		echo json_encode( sizeof( $result ) ? $result : false );

		die(); //this is required to return a proper result
	}


	/**
	 * Import process - import content
	 *
	 * Part of import process responsible for importing posts
	 *
	 * Fired by $this->import_templates()
	 *
	 * @since 1.0
	 * @access public
	 */
	private function import_install_content($sublevel, &$sublevel_name){
		//imports templates
		$dir = A13REE_PATH . '/data/';
		$templates = array();

		//get all templates to import
		if ( is_dir( $dir ) ) {
			//The GLOB_BRACE flag is not available on some non GNU systems, like Solaris. So we use merge:-)
			foreach ( (array) glob( $dir . '/*.json' ) as $file ) {
				$templates[] = basename( $file );
			}
		}

		if ( strlen( $sublevel ) === 0 ) {//we will import first template on list but in second call of this function
			$sublevel      = key( $templates );
			$sublevel_name = $templates[ $sublevel ];
		}
		else {
			//save last template
			end( $templates );
			$last_template = key( $templates );
			reset( $templates );

			$sublevel = (int) $sublevel;//convert from string type

			// template to import now
			$file = $dir . $templates[ $sublevel ];

			/** @var \Elementor\TemplateLibrary\Source_Local $source */
			$source = \Elementor\Plugin::instance()->templates_manager->get_source( 'local' );

			$source->import_template( $templates[ $sublevel ], $file );

			//if this was last plugin on list then we end this process
			if ( $last_template === $sublevel ) {
				$sublevel = true;
			}
			else{
				//move to next template to import
				$sublevel++;
				$sublevel_name = $templates[ $sublevel ];
			}
		}

		return $sublevel;
	}


	/**
	 * Importer constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'admin_pages' ], 600 ); //as last item
		add_action( 'wp_ajax_a13ree_import_templates', [ $this, 'import_templates' ] );

		$this->tabs = [
			'import'         => esc_html__( 'Templates import', 'rife-elementor-extensions' ),
			'instructions'   => esc_html__( 'Plugin Instructions', 'rife-elementor-extensions' ),
			'apollo13themes' => esc_html__( 'More from Apollo13Themes', 'rife-elementor-extensions' )
		];
	}
}
