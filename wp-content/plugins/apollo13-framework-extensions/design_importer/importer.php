<?php


add_action( 'wp_ajax_apollo13framework_import_demo_data', 'a13fe_import_demo_data' );
/**
 * Function leading demo data import process
 * @since  1.4.0
 */
function a13fe_import_demo_data() {
	/* in case there is old version of theme which had this feature inside, we don't add it */
	if ( function_exists( 'apollo13framework_import_demo_data' ) ){
		return;
	}

	global $apollo13framework_a13;
	//check if we got license key
	if( ! $apollo13framework_a13->check_is_import_allowed() ){
		$msg    = esc_html__( 'The import has been stopped - there is no valid purchase/license Code', 'apollo13-framework-extensions' );
		$result = array(
			'log'           => $msg,
			'sublevel_name' => '',
			'level_name'    => $msg,
			'is_it_end'     => true
		);

		//send AJAX response
		echo json_encode( sizeof( $result ) ? $result : false );
		die(); //this is required to return a proper result
	}
	/** @noinspection PhpIncludeInspection */
	$file_system_check = require_once A13FE_BASE_DIR . 'design_importer/actions.php';

	//error on file system access
	if( ! $file_system_check ){
		$result = array(
			'level'         => '',
			'level_name'    => esc_html__( 'Import failed', 'apollo13-framework-extensions' ),
			'sublevel'      => '',
			'sublevel_name' => '',
			'log'           => esc_html__( 'Can not access the file system.', 'apollo13-framework-extensions' ),
			'is_it_end'     => true,
			'alert'         => true
		);
	}
	else{
		$level         = isset( $_POST['level'] ) ? sanitize_text_field( wp_unslash( $_POST['level'] ) ) : '';
		$sublevel      = isset( $_POST['sublevel'] ) ? sanitize_text_field( wp_unslash( $_POST['sublevel'] ) ) : '';
		$sublevel_name = '';
		$log           = '';
		$array_index   = 0;
		$alert         = false;

		$chosen_options = isset( $_POST['import_options'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['import_options'] ) ) : array();

		$levels = array(
			'_'                     => '', //empty to avoid bonus logic
			'start'                 => esc_html__( 'Starting the import', 'apollo13-framework-extensions' ),
			'download_files'        => esc_html__( 'Downloading files', 'apollo13-framework-extensions' ),
			'clear_content'         => esc_html__( 'Deleting the current content', 'apollo13-framework-extensions' ),
			'install_plugins'       => esc_html__( 'Installing plugins', 'apollo13-framework-extensions' ),
			'install_content'       => esc_html__( 'Importing the content', 'apollo13-framework-extensions' ),
			'install_revo_sliders'  => esc_html__( 'Importing the sliders', 'apollo13-framework-extensions' ),
			'setup_plugins_configs' => esc_html__( 'Setting up plugins settings', 'apollo13-framework-extensions' ),
			'setup_wc'              => esc_html__( 'Setting up WooCommerce settings', 'apollo13-framework-extensions' ),
			'setup_fp'              => esc_html__( 'Setting up the front page', 'apollo13-framework-extensions' ),
			'setup_menus'           => esc_html__( 'Setting up the menus', 'apollo13-framework-extensions' ),
			'setup_widgets'         => esc_html__( 'Setting up the widgets', 'apollo13-framework-extensions' ),
			'setup_permalinks'      => esc_html__( 'Setting up the permalinks', 'apollo13-framework-extensions' ),
			'import_predefined_set' => esc_html__( 'Importing the theme settings', 'apollo13-framework-extensions' ),
			'generate_custom_style' => esc_html__( 'Importing the theme settings', 'apollo13-framework-extensions' ),
			'install_plugins_2'     => esc_html__( 'Installing plugins', 'apollo13-framework-extensions' ),
			'clean'                 => esc_html__( 'Cleaning', 'apollo13-framework-extensions' ),
			'end'                   => esc_html__( 'Everything done.', 'apollo13-framework-extensions' ) . ' ' . '<a href="'. esc_url( home_url( '/' ) ).'">' . esc_html__( 'View your website!', 'apollo13-framework-extensions' ) .'</a>',
		);

		//check what options are selected
		if( ! isset( $chosen_options['download_files'] ) ){
			unset( $levels['download_files'] );
		}
		if( ! isset( $chosen_options['clear_content'] ) ){
			unset( $levels['clear_content'] );
		}
		if( ! isset( $chosen_options['install_plugins'] ) ){
			unset( $levels['install_plugins'] );
			unset( $levels['setup_plugins_configs'] );
			unset( $levels['setup_wc'] );
			unset( $levels['install_plugins_2'] );
		}
		if( ! isset( $chosen_options['import_shop'] ) ){
			unset( $levels['setup_wc'] );
			unset( $levels['install_plugins_2'] );
		}
		if( ! isset( $chosen_options['install_content'] ) ){
			unset( $levels['install_content'] );
		}
		if( ! isset( $chosen_options['install_revo_sliders'] ) ){
			unset( $levels['install_revo_sliders'] );
		}
		if( ! isset( $chosen_options['install_site_settings'] ) ){
			unset( $levels['setup_fp'] );
			unset( $levels['setup_menus'] );
			unset( $levels['setup_widgets'] );
			unset( $levels['setup_permalinks'] );
		}
		if( ! isset( $chosen_options['install_theme_settings'] ) ){
			unset( $levels['import_predefined_set'] );
			unset( $levels['generate_custom_style'] );
		}
		if( ! isset( $chosen_options['clean'] ) ){
			unset( $levels['clean'] );
		}

		//get current level key
		if( strlen( $level ) === 0 ){
			//get first level to process
			$level = key( $levels );
		}
		else{
			//move array pointer to current importing level
			while( key( $levels ) !== $level ) {
				//and ask for next one
				next( $levels );
				$array_index ++;
			}
			//save new current level
			$level = key( $levels );
		}

		//Execute current level function
		$function = 'a13fe_demo_data_' . $level;
		if( function_exists( $function ) ){
			//no notices or other "echos", we put it in $log
			ob_start();

			$functions_with_1_param = array(
				'a13fe_demo_data_import_predefined_set',
				'a13fe_demo_data_start',
				'a13fe_demo_data_clean',
				'a13fe_demo_data_install_revo_sliders'
			);

			$demo_id = isset( $_POST['demo_id'] ) ? sanitize_text_field( wp_unslash( $_POST['demo_id'] ) ) : '';
			//how many params should function receive
			if( in_array( $function, $functions_with_1_param ) ){
				$sublevel = $function( $demo_id );
			}
			else{
				$sublevel = $function( $sublevel, $sublevel_name, $demo_id, $chosen_options );
			}

			//collect all produced output to log
			$log = ob_get_contents();
			ob_end_clean();

			//should we move to next level
			if( $sublevel === true ){
				$sublevel = ''; //reset
				next( $levels );
				$level = key( $levels );
			}
		}
		//no function - move to next level. Some steps are just information without action
		else{
			next( $levels );
			$array_index ++;
			$level = key( $levels );
		}

		//check if this is last element
		$is_it_end = false;
		end( $levels );
		if( key( $levels ) === $level ){
			$is_it_end = true;
		}

		//prepare progress info
		$progress = round( 100 * ( 1 + $array_index ) / count( $levels ) );

		//special case - demo import files download failure
		$failure_codes = array(
			620,    // invalid purchase code
			621,    // trying to get paid demo
			//		1012,   // no available servers
			1013    // server directory no writable
		);
		if( is_array( $sublevel ) && $sublevel['sublevel'] === false && in_array( $sublevel['response']['code'], $failure_codes ) ){
			$log       = $sublevel['response']['message'];
			$sublevel  = false;
			$is_it_end = true;
			$alert     = true;
		}

		$result = array(
			'level'         => $level,
			'level_name'    => $levels[ $level ],
			'sublevel'      => $sublevel,
			'sublevel_name' => $sublevel_name,
			'log'           => $log,
			'progress'      => $progress,
			'is_it_end'     => $is_it_end,
			'alert'         => $alert
		);
	}

	//send AJAX response
	echo json_encode( sizeof( $result ) ? $result : false );

	die(); //this is required to return a proper result
}


/**
 * Retrieves list of Designs to import
 *
 * @since  1.4.0
 * @return bool|array list of designs or false on error
 */
function a13fe_get_demo_list() {

	$demos_definition = array();

	//try import by https
	$response = wp_remote_get( A13FRAMEWORK_IMPORT_SERVER . '/definitions/' . A13FRAMEWORK_TPL_SLUG . '_demos_definition.php', array('timeout' => 20) );
	if( !is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) == 200 ){
		$demos_definition = json_decode( wp_remote_retrieve_body( $response ), true );
	}
	else{
		// try http import
		$response = wp_remote_get( str_replace( 'https:', 'http:', A13FRAMEWORK_IMPORT_SERVER ) . '/definitions/' . A13FRAMEWORK_TPL_SLUG . '_demos_definition.php', array('timeout' => 20) );
		if( !is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) == 200 ){
			$demos_definition = json_decode( wp_remote_retrieve_body( $response ), true );

			//inform the whole import process that this server has issues with SSL
			update_option( 'a13fe_import_by_http', 'on' );
		}
	}

	if(!isset($demos_definition['demos'])){
		return false;
	}

	return $demos_definition;
}


/**
 * Displays whole Designs import interface
 *
 * @since  1.4.0
 */
function a13fe_get_demo_importer_content() {
	global $apollo13framework_a13;
	$demos_definition = a13fe_get_demo_list();
	$demos            = $demos_definition === false ? array() : $demos_definition['demos'];
	$demo_count       = $demos_definition === false ? 0 : count( $demos );
	$all_categories   = array();
	$available_demos  = array();

	$available_demos_number = 0;
	if($demos_definition !== false){
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		foreach ( $demos as $demo ) {
			//check if demo is available for this configuration
			if( isset( $demo['must_have_plugins'] ) && is_array( $demo['must_have_plugins'] ) ){
				$not_available = false;
				foreach( $demo['must_have_plugins'] as $plugin ) {
					if( ! is_plugin_active( $plugin ) ){
						$not_available = true;
					}
				}
				if( $not_available ){
					//skip this demo
					continue;
				}
			}

			//count this demo
			$available_demos_number++;
			$available_demos[] = $demo;

			//collect categories
			$all_categories = array_merge( $all_categories, $demo['categories'] );
		}
	}
	?>




		<div class="demo_import_wrapper">
			<?php
			if($demos_definition === false){
				$demos_list_url = A13FRAMEWORK_IMPORT_SERVER . '/definitions/' . A13FRAMEWORK_TPL_SLUG . '_demos_definition.php';
				echo '<div class="info_box">'.
				     '<p>'.esc_html__( 'There was a problem with getting a list of available Designs for import. This can happen for several reasons:', 'apollo13-framework-extensions' ).'</p>'.
				     '<ol><li>'.
				     sprintf(
				     /* translators: %1$s: URL */
					     esc_html__( 'Our server could be down. To verify this, check if you can open this URL %1$s. If it opens, then our server works well.', 'apollo13-framework-extensions'),
					     '<a target="_blank" href="'.esc_url( $demos_list_url ).'">'.esc_html( $demos_list_url ).'</a>'
				     ).
				     '</li><li>'.
				     esc_html__( 'Your server does not recognize our SSL certificate. Typically, upgrading the server PHP version to version 7.X solves the problem.', 'apollo13-framework-extensions'),
				     '</li><li>'.
				     sprintf(
				     /* translators: %1$s: URL */
				         esc_html__( 'Your server is blocking our website by a firewall or something similar. You could ask your server admin to add our demo data server to the "white list": %1$s', 'apollo13-framework-extensions'),
					     '<code>'.A13FRAMEWORK_IMPORT_SERVER.'</code>'
				     ).
				     '</li><li>'.
				     '<a target="_blank" href="https://apollo13themes.com/contact/">'.esc_html__( 'If this does not solve the problem, please contact us.', 'apollo13-framework-extensions').'</a>'.
				     '</li></ol>'.
					'</div>';
			}

			//we have some demos
			if($demo_count){
				?>

			<div id="a13-import-step-1" data-step="1" class="import-step step-1">
				<h2><?php echo esc_html__( 'Designs to choose from:', 'apollo13-framework-extensions' ); ?> <strong><?php echo (int)$available_demos_number; ?></strong></h2>
				<p class="center"><?php echo esc_html__( 'Please select the Design to import to go to the next step.', 'apollo13-framework-extensions' ); ?></p>

				<?php if($demo_count > 1){ ?>
					<span class="demo_search_wrap">
					<label><i class="fa fa-search"></i><?php echo esc_html__( 'Search Designs', 'apollo13-framework-extensions' ); ?>
						<input class="demo_search" type="search" value="" name="demo_search" placeholder="<?php esc_attr_e( 'At least 3 chars: name, category', 'apollo13-framework-extensions' ); ?>" />
					</label>
					</span>

					<div class="filter_wrapper">
						<?php
						//categories
						if(count($all_categories) > 1){
							$all_categories_unique = array_unique( $all_categories );
							sort( $all_categories_unique );

							echo '<ul class="demo_filter_categories">';
							echo '<li data-filter="*" class="active"> ' . esc_html__( 'All', 'apollo13-framework-extensions' ) . ' </li>';
							foreach ( $all_categories_unique as $category ) {
								echo '<li data-filter="' . esc_attr( str_replace( ' ', '_', strtolower( $category ) ) ) . '"> ' . esc_html( $category ). ' </li>';
							}
							echo '</ul>';
						}
						?>
					</div>
				<?php }
				do_action('apollo13framework_before_designs_list');
				?>

				<div id="a13_demo_grid" class="demo_grid">
				<?php
				$import_server = A13FRAMEWORK_IMPORT_SERVER;
				if( get_option('a13fe_import_by_http') === 'on'){
					//switch to http
					$import_server = str_replace( 'https:', 'http:', $import_server );
				}

				foreach ( $available_demos as $demo ) {
					//check for setting telling proper path to thumbnails
					if(isset( $demos_definition['settings'] ) && isset($demos_definition['settings']['files_path'])  ){
						$files_directory = $import_server . '/files/'.$demos_definition['settings']['files_path'].'/demo_data/' . $demo['id'] . '/';
					}
					else{
						$files_directory = $import_server . '/files/' . A13FRAMEWORK_TPL_SLUG . '/demo_data/' . $demo['id'] . '/';
					}

					apollo13framework_importer_grid_item( $files_directory, $demo );
				}
				?>
				</div>

			<?php
				do_action('apollo13framework_after_designs_list');
			?>
			</div>

			<div id="a13-import-step-2" data-step="2" class="import-step step-2 hidden">
				<h2><?php echo esc_html__( 'About the Design importer', 'apollo13-framework-extensions' ); ?></h2>
				<?php
				echo '<p>'.
				     esc_html__( 'This importer can be used to import the entire demo appearance &amp; content to your website. Use the below configuration and Designs to achieve the desired results.', 'apollo13-framework-extensions').
				     ' <a href="'.esc_url( $apollo13framework_a13->get_docs_link('importer-configuration') ).'">'.esc_html__( 'Read more about using the Design importer.', 'apollo13-framework-extensions').'</a>'.
				     '</p>';
				echo '<p>'.
				     esc_html__( 'When you use the Design Importer feature, some data will be stored on our server. These are: event date, website URL, IP address, selected Design name. All this data is used for statistic and for protection against the abuse of our services. This data is not shared with any third parties.', 'apollo13-framework-extensions').
				     '</p>';
				?>

				<h2><?php echo esc_html__( 'Configuration &amp; requirements', 'apollo13-framework-extensions' ); ?></h2>

				<div class="config-tables clearfix">
					<?php
					a13fe_theme_import_configuration();
					apollo13framework_theme_requirements_table();
					?>
				</div>

				<div class="import-navigation">
					<button class="button previous-step"><?php echo esc_html__( 'Previous step', 'apollo13-framework-extensions' ); ?></button>
					<button class="button button-primary button-hero next-step"><?php echo esc_html__( 'Next step', 'apollo13-framework-extensions' ); ?></button>
				</div>
			</div>

			<div id="a13-import-step-3" data-step="3" class="import-step step-3 hidden">
				<h2><?php echo esc_html__( 'You are about to import:', 'apollo13-framework-extensions' ); ?></h2>
				<div class="import-summary">
					<h3 class="design-name"></h3>
					<img src="" alt="<?php echo esc_attr( __( 'Design preview', 'apollo13-framework-extensions' ) ); ?>" />
				</div>

				<div class="status_info">
					<strong id="demo_data_import_status">&nbsp;</strong>
				</div>

				<div class="import_progress_bar">
					<div class="import_progress"></div>
				</div>

				<div id="demo_data_import_log">
					<div></div>
				</div>

				<div class="import-navigation">
					<button class="button previous-step"><?php echo esc_html__( 'Previous step', 'apollo13-framework-extensions' ); ?></button>
					<button class="button button-primary button-hero" id="start-demo-import" data-confirm="<?php echo esc_attr( __( 'Do you want to import the selected Design?', 'apollo13-framework-extensions' ) ); ?>" data-confirm-remove-content="<?php echo esc_attr( __( 'All your current content will be removed before import!', 'apollo13-framework-extensions' ) ); ?>"><?php echo esc_html__( 'Start importing the Design', 'apollo13-framework-extensions' ); ?></button>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="button button-primary button-hero" id="import-visit-site"><?php echo esc_html__( 'View your website!', 'apollo13-framework-extensions' ); ?></a>
				</div>
			</div>
				<?php
			}
			?>
		</div>
	<?php
}
add_action('apollo13framework_apollo13_importer_page_content', 'a13fe_get_demo_importer_content');



/**
 * Displays Designs import configuration
 *
 * @since  1.4.0
 */
function a13fe_theme_import_configuration(){
	global $apollo13framework_a13;
	?>
	<div class="import-config">
		<table class="status_table widefat" cellspacing="0">
			<thead>
			<tr>
				<th colspan="3"><?php esc_html_e( 'Import configuration', 'apollo13-framework-extensions' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<td><label for="import-clear-content"><strong style="color: #ca2121;"><?php esc_html_e( 'Remove current content', 'apollo13-framework-extensions' ); ?>:</strong></label></td>
				<td class="help"><?php apollo13framework_input_help_tip( __( '<p>In order to achieve the import results as close as possible to the original demo version, the importer will have to remove all current content.</p><p>If you are using a fresh WordPress installation and want to get the best import results you should check this option.</p><p>However, if you are just updating your existing website, stay away from this option.</p>', 'apollo13-framework-extensions' ) ); ?></td>
				<td><input type="checkbox" name="clear_content" id="import-clear-content" /><label for="import-clear-content"><?php esc_html_e( 'Caution!', 'apollo13-framework-extensions' ); ?></label></td>
			</tr>

			<tr>
				<td><label for="import-install-plugins"><?php esc_html_e( 'Install plugins', 'apollo13-framework-extensions' ); ?>:</label></td>
				<td class="help"><?php apollo13framework_input_help_tip( __( 'It will install plugins that are necessary to reproduce this demo.', 'apollo13-framework-extensions' ) ); ?></td>
				<td><input type="checkbox" name="install_plugins" id="import-install-plugins" value="off" checked /></td>
			</tr>
			<tr>
				<td><label for="import-install-shop"><?php esc_html_e( 'Import shop', 'apollo13-framework-extensions' ); ?></label></td>
				<td class="help"><?php apollo13framework_input_help_tip( __( 'Works only when the shop was created using WooCommerce for the selected demo.', 'apollo13-framework-extensions' ) ); ?></td>
				<td><input type="checkbox" name="import_shop" id="import-install-shop" /></td>
			</tr>

			<tr>
				<td><label for="import-install-content"><?php esc_html_e( 'Import demo content', 'apollo13-framework-extensions' ); ?></label></td>
				<td class="help"><?php apollo13framework_input_help_tip( __( 'It installs all the content created for the selected demo.', 'apollo13-framework-extensions' ) ); ?></td>
				<td><input type="checkbox" name="install_content" id="import-install-content" checked />
				<?php if ( !apollo13framework_is_home_server() ){ ?>
					<input class="hidden" type="checkbox" name="install-attachments" id="import-install-attachments" checked />
				<?php } ?>
				</td>
			</tr>
			<?php if ( apollo13framework_is_home_server() ){ ?>
			<tr>
				<td><label for="import-install-attachments"><?php esc_html_e( 'Import media attachments', 'apollo13-framework-extensions' ); ?></label></td>
				<td class="help">&nbsp;</td>
				<td><input type="checkbox" name="install-attachments" id="import-install-attachments" checked /></td>
			</tr>
			<?php } ?>

			<?php if ( $apollo13framework_a13->check_for_valid_license() ){ ?>
			<tr>
				<td><label for="import-install-sliders"><?php esc_html_e( 'Import sliders', 'apollo13-framework-extensions' ); ?></label></td>
				<td class="help"><?php apollo13framework_input_help_tip( __( 'Imports sliders created with the "Slider Revolution" plugin. Works only if it was used in the demo.', 'apollo13-framework-extensions' ) ); ?></td>
				<td><input type="checkbox" name="install_revo_sliders" id="import-install-sliders" checked /></td>
			</tr>
			<?php } ?>

			<tr>
				<td><label for="import-site-settings"><?php esc_html_e( 'Import site settings', 'apollo13-framework-extensions' ); ?></label></td>
				<td class="help"><?php apollo13framework_input_help_tip( __( 'Site settings are various setting mostly not connected to the theme. These are: permalinks and front page. Partly theme dependent settings are menus and sidebars with widgets.', 'apollo13-framework-extensions' ) ); ?></td>
				<td><input type="checkbox" name="install_site_settings" id="import-site-settings" checked /></td>
			</tr>

			<tr>
				<td><label for="import-theme-settings"><?php esc_html_e( 'Import theme settings', 'apollo13-framework-extensions' ); ?></label></td>
				<td class="help"><?php apollo13framework_input_help_tip( __( '<p>The theme settings are all the settings that you can later change on in the Customizer.</p><p>If you wish to change only the look of your existing site to one from our demos, then mark only this option.</p>', 'apollo13-framework-extensions' ) ); ?></td>
				<td><input type="checkbox" name="install_theme_settings" id="import-theme-settings" checked /></td>
			</tr>

			<tr class="readonly">
				<td><label for="import-cleanup"><?php esc_html_e( 'Clean up', 'apollo13-framework-extensions' ); ?></label></td>
				<td class="help">&nbsp;</td>
				<td><input type="checkbox" name="clean" id="import-cleanup" checked /></td>
			</tr>

			<tr class="readonly">
				<td><label for="import-download"><?php esc_html_e( 'Download', 'apollo13-framework-extensions' ); ?>:</label></td>
				<td class="help">&nbsp;</td>
				<td><input type="checkbox" name="download_files" id="import-download" checked /></td>
			</tr>
			</tbody>
		</table>
	</div>

	<?php
}


//prepare directory for demo data
if ( !is_writable( A13FRAMEWORK_IMPORTER_TMP_DIR ) ) {
	wp_mkdir_p(A13FRAMEWORK_IMPORTER_TMP_DIR);
}