<?php

/*
 * Prepare file system for import
 * @since  1.4.0
 */
function a13fe_demo_data_prepare_filesystem(){
	/* @var $wp_filesystem WP_Filesystem_Base */
	global $wp_filesystem, $apollo13framework_site_config;

	//test if file system works fine
	$url = wp_nonce_url(admin_url(),'');
	ob_start();
	$creds = request_filesystem_credentials( $url, '', false, A13FRAMEWORK_IMPORTER_TMP_DIR, null, true );
	ob_clean();

	if ( $creds  === false || ! WP_Filesystem($creds, A13FRAMEWORK_IMPORTER_TMP_DIR, true) ) {
		return false;
	}

	$demo_id = isset( $_POST['demo_id'] ) ? sanitize_text_field( wp_unslash( $_POST['demo_id'] ) ) : '';

	//prepare directory for demo data
	if ( !is_writable( A13FRAMEWORK_IMPORTER_TMP_DIR.'/'.$demo_id ) ) {
		wp_mkdir_p(A13FRAMEWORK_IMPORTER_TMP_DIR.'/'.$demo_id);
	}

	//check for demo config file
	$demo_config_file = A13FRAMEWORK_IMPORTER_TMP_DIR . '/' . $demo_id . '/site_config';

	//in case of FTP access we need to make sure we have proper path
	$demo_config_file = str_replace(ABSPATH, $wp_filesystem->abspath(), $demo_config_file);

	if ( $wp_filesystem->exists( $demo_config_file ) ) {
		$apollo13framework_site_config = json_decode( $content = $wp_filesystem->get_contents($demo_config_file), true );
	}

	return true;
}


/*
 * Gets demo data configuration
 * @since  1.4.0
 */
function a13fe_demo_data_start( $demo_id ) {
	$import_server = A13FRAMEWORK_IMPORT_SERVER;
	if( get_option('a13fe_import_by_http') === 'on'){
		//switch to http
		$import_server = str_replace( 'https:', 'http:', $import_server );
	}

	$response = wp_remote_post( $import_server . '/api.php',
		array(
			'method'  => 'POST',
			'timeout' => 15,
			'body'    => array(
				'demo_id' => $demo_id,
				'theme'   => A13FRAMEWORK_TPL_SLUG,
				'address' => get_site_url(),
				'key'     => 'Apollo13'
			)
		)
	);
	if( !is_wp_error( $response ) && wp_remote_retrieve_response_code( $response ) == 200 ){
		$demo = json_decode( wp_remote_retrieve_body( $response ), true );
	}
	else{
		//TODO report that server is down or DNS issue
		$demo = false;
	}

	//write demo info array
	update_option( A13FRAMEWORK_TPL_SLUG .'_processed_demo_import',serialize($demo));
	return true;
}


function a13fe_demo_data_download_files( $sublevel, &$sublevel_name, $demo_id ) {
	global $apollo13framework_a13;

	$server_path = $path = $file_to_download = $file_to_download_name = $revo_to_download = '';
	$this_page_url = get_site_url();

	//get demo definitions
	$demo = unserialize(get_option(A13FRAMEWORK_TPL_SLUG .'_processed_demo_import'));
	$needed_revos = $demo['demo']['revo'];
	$requested_demo_name = $demo['demo']['name'];

	//check if revo files are already downloaded
	foreach ( $needed_revos as $revo_index => $needed_revo ) {
		$file_to_check = A13FRAMEWORK_IMPORTER_TMP_DIR . '/' . $demo_id . '/' . $needed_revo . '.zip';
		if ( ! file_exists( $file_to_check ) ) {
			$revo_to_download      = $file_to_check;
			$file_to_download_name = $needed_revo . '.zip';
			$server_path           = 'revo';
			$sublevel              = $revo_index;
			$sublevel_name         = $needed_revo;
			break;
		}
	}

	//no files to download - check import files
	if ( $revo_to_download == '' ) {
		$needed_files = array(
			'data.xml',
			'extra_plugins.php',
			'site_config',
			'theme.json',
		);

		//check if files are already downloaded
		foreach ( $needed_files as $file_index => $needed_file ) {
			$file_to_check = A13FRAMEWORK_IMPORTER_TMP_DIR.'/'.$demo_id . '/' . $needed_file;
			if ( ! file_exists( $file_to_check ) ) {
				$file_to_download      = $file_to_check;
				$file_to_download_name = $needed_file;
				$server_path           = $demo_id;
				$sublevel              = $file_index;
				$sublevel_name         = $needed_file;
				break;
			}
		}
	}

	if ( $file_to_download == '' && $revo_to_download == '' ) {
		//end of this import stage
		$sublevel = true;
	} else {

		if ( $file_to_download != '' ) {
			$path = 'demo_data/' . $demo_id;
		} elseif ( $revo_to_download != '' ) {
			$file_to_download = $revo_to_download;
			$path             = 'demo_data/revo';
		}

		//check if destination folder is writable
		if ( is_writable( A13FRAMEWORK_IMPORTER_TMP_DIR .'/'.$demo_id ) ) {
			$purchase_code = $apollo13framework_a13->get_license_code();
			$import_server = A13FRAMEWORK_IMPORT_SERVER;
			if( get_option('a13fe_import_by_http') === 'on'){
				//switch to http
				$import_server = str_replace( 'https:', 'http:', $import_server );
			}

			$response      = wp_remote_get(
				$import_server . '/download.php',
				array(
					'timeout'    => 300,
					'stream'     => true,
					'headers'    => array( 'Connection: close\r\n' ),
					'user-agent' => 'Apollo13/'.str_replace(array("/","\\"),'_',$this_page_url).'/' . $requested_demo_name . '/' . $purchase_code . '/'.A13FRAMEWORK_TPL_SLUG.'/demo_data/' . $server_path . '/' . $file_to_download_name,
					'filename'   => $file_to_download,
				)
			);
			if ( $response['response']['code'] == 620 ) {
				//unlink empty file
				unlink( $file_to_download );
				$response['response']['message'] = 'Purchase Code Verification Fail';
				return array( 'sublevel' => false, 'response' => $response['response'] );
			}
		}
		else{
			return array(
				'sublevel' => false,
				'response' =>
					array(
						'code'=> 1013,
						'message' => A13FRAMEWORK_IMPORTER_TMP_DIR . '/' .$demo_id . '/' . $path . ' - ' . esc_html__( 'Can not access the file system.', 'apollo13-framework-extensions' )
					)
			);
		}


	}

	return $sublevel;

}


function a13fe_demo_data_clear_content() {
	global $wpdb;

	//Force Elementor to delete posts without wp_die()
	$_GET['force_delete_kit'] = true;

	//Wish list plugin fix
	//Need to deactivate it as after clearing posts it will display error notice that will break importer
	if ( class_exists( 'YITH_WCWL_Install' ) ) {
		$all_plugins = get_plugins();
		$path        = a13fe_get_plugin_basename_from_slug( 'yith-woocommerce-wishlist', $all_plugins );
		deactivate_plugins( $path );
	}


	//removes all posts, pages, works etc.
	/** @noinspection SqlDialectInspection */
	/** @noinspection SqlNoDataSourceInspection */
	$sql = "SELECT MIN(ID) as min, MAX(ID) as max FROM {$wpdb->posts}"; //note there is no place for SQL injection in this query
	$min = $max = 0;
	extract( $wpdb->get_row( $sql, ARRAY_A ) );
	// Now you have $min and $max

	for ( $i = $min; $i <= $max; $i ++ ) {
		wp_delete_post( $i, true );
	}


	//remove all menus
	$menus = wp_get_nav_menus();
	foreach ( $menus as $menu ) {
		wp_delete_nav_menu( $menu->term_id );
	}


	//removes all Revolution sliders
	//version 6.0 and above
	if ( class_exists( 'RevSliderSlider' ) ) {
		$RevoSlider     = new RevSliderSlider();
		$all_sliders = $RevoSlider->get_sliders();
		/** @var RevSliderSlider $_slider */
		foreach ( $all_sliders as $_slider ) {
			$_slider->delete_slider();
		}
	}
	//versions less then 6.0
	elseif ( class_exists( 'RevSlider' ) ) {
		$RevoSlider     = new RevSlider();
		$all_sliders = $RevoSlider->getArrSliders();
		/** @var RevSlider $_slider */
		foreach ( $all_sliders as $_slider ) {
			$data = array( 'sliderid' => $_slider->getID() );
			$_slider->deleteSliderFromData( $data );
		}
	}

	//this step is done
	return true;
}


function a13fe_demo_data_install_plugins( $sublevel, &$sublevel_name, $demo_id, $chosen_options ) {

	$demo = unserialize(get_option(A13FRAMEWORK_TPL_SLUG .'_processed_demo_import'));
	$plugins = $demo['plugins'];

	$extra_plugins_file = A13FRAMEWORK_IMPORTER_TMP_DIR . '/' . $demo_id . '/extra_plugins.php';
	if ( is_readable( $extra_plugins_file ) && file_exists( $extra_plugins_file ) ) {
		/** @noinspection PhpIncludeInspection */
		$extra_plugins = require_once( $extra_plugins_file );
		$plugins       = array_merge( $plugins, $extra_plugins );
	}

	$skip_plugins_list = array(
		'yith-woocommerce-wishlist' //skip by default, as it has to installed later
	);
	//remove shop plugins if not wanted
	if(!isset($chosen_options['import_shop'])){
		$skip_plugins_list[] = 'woocommerce';
		$skip_plugins_list[] = 'woocommerce-quantity-increment';
	}

	//save last plugin
	end( $plugins );
	$last_plugin = key( $plugins );
	reset( $plugins );

	if ( strlen( $sublevel ) === 0 ) {//we will install first plugin on list but in second call of this function
		$sublevel      = key( $plugins );
		$sublevel_name = $plugins[ $sublevel ]['name'];
	}
	else {
		$sublevel = (int) $sublevel;//convert from string type

		// move to currently installing plugin
		while ( key( $plugins ) !== $sublevel ) {
			next( $plugins );
		}
		a13fe_do_plugin_install( $plugins[ $sublevel ] );


		//if this was last plugin on list then we end this process
		if ( $last_plugin === $sublevel ) {
			$sublevel = true;
		}
		else{
			//finish loop in search for next plugin to install
			while(next( $plugins )){
				if(!in_array($plugins[ key( $plugins ) ]['slug'], $skip_plugins_list )){
					//we found next plugin to install
					$sublevel      = key( $plugins ); //we will install this one in next call
					$sublevel_name = $plugins[ $sublevel ]['name'];

					break;
				}
				//if this was last plugin on list then we end this process
				elseif($last_plugin === key( $plugins )){
					$sublevel = true;
					break;
				}
			}
		}
	}


	return $sublevel;
}


//install plugins after everything else is done
function a13fe_demo_data_install_plugins_2() {
	$demo = unserialize(get_option(A13FRAMEWORK_TPL_SLUG .'_processed_demo_import'));
	$plugins = $demo['plugins'];

	// scan for our special plugin
	while ( current( $plugins ) ) {
		$this_plugin = current( $plugins );
		if ( $this_plugin['slug'] === 'yith-woocommerce-wishlist' ) {
			break;
		}
		//go to next for checking
		next( $plugins );
	}


	//make sure it was found on this list
	$this_plugin = current( $plugins );
	if ( $this_plugin['slug'] === 'yith-woocommerce-wishlist' ) {
		a13fe_do_plugin_install( current( $plugins ) );
	}

	return true;
}


function a13fe_get_plugin_basename_from_slug( $slug, &$plugins ) {

	$keys = array_keys( $plugins );

	foreach ( $keys as $key ) {
		if ( preg_match( '|^' . $slug . '|', $key ) ) {
			return $key;
		}
	}

	return $slug;
}


/**
 * @param array $plugin
 *
 * @return bool
 */
function a13fe_do_plugin_install( $plugin = array() ) {
	//language upgrader breaks importer as it clears buffer in bad place. User can later update translations
	remove_action( 'upgrader_process_complete', array( 'Language_Pack_Upgrader', 'async_upgrade' ), 20 );

	// For plugins from WP repository
	if ( ! isset( $plugin['source'] ) ) {
		$plugin['source'] = 'repo';
	}

	// Retrieve a list of all the plugins
	$installed_plugins = get_plugins();

	// Add file_path key for plugin
	$plugin['file_path'] = a13fe_get_plugin_basename_from_slug( $plugin['slug'], $installed_plugins );

	// Install/update/activate states
	$install_type    = false;
	$activate_plugin = false;

	// What to do with this plugin
	// Do nothing or update
	if ( isset( $installed_plugins[ $plugin['file_path'] ] ) ) {
		// A minimum version has been specified
		if ( isset( $plugin['version'] ) && isset( $installed_plugins[ $plugin['file_path'] ]['Version'] ) ) {
			if ( version_compare( $installed_plugins[ $plugin['file_path'] ]['Version'], $plugin['version'], '<' ) ) {
				$install_type    = 'update';
				$activate_plugin = true;
			}
		}
	} // Install
	else {
		$install_type    = 'install';
		$activate_plugin = true;
	}

	// Activate
	if ( is_plugin_inactive( $plugin['file_path'] ) ) {
		$activate_plugin = true;
	}

	/** Pass all necessary information via URL if WP_Filesystem is needed */
	$url    = esc_url( wp_nonce_url(
		add_query_arg(
			array(
				'page'               => '',
				'plugin'             => $plugin['slug'],
				'plugin_name'        => $plugin['name'],
				'plugin_source'      => $plugin['source'],
				'a13-plugin-install' => 'install-plugin',
			),
			admin_url()
		),
		'a13-plugin-install'
	) );
	$method = ''; // Leave blank so WP_Filesystem can populate it as necessary
	$fields = array( sanitize_key( 'a13-plugin-install' ) ); // Extra fields to pass to WP_Filesystem

	if ( false === ( $creds = request_filesystem_credentials( $url, $method, false, false, $fields ) ) ) {
		return true;
	}

	if ( ! WP_Filesystem( $creds ) ) {
		request_filesystem_credentials( $url, $method, true, false, $fields ); // Setup WP_Filesystem
		return true;
	}

	require_once ABSPATH . 'wp-admin/includes/plugin-install.php'; // Need for plugins_api
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php'; // Need for upgrade classes
	require_once A13FE_BASE_DIR.'design_importer/class-a13-plugin-installer-skin.php'; // Overwrite of Plugin_Installer_Skin that doesn't clear buffer


	/** Set plugin source to WordPress API link if available */
	if ( isset( $plugin['source'] ) && 'repo' == $plugin['source'] ) {
		$api = plugins_api( 'plugin_information', array(
			'slug'   => $plugin['slug'],
			'fields' => array( 'sections' => false )
		) );

		if ( is_wp_error( $api ) ) {
			wp_die( wp_kses_post( $api ) );
		}

		if ( isset( $api->download_link ) ) {
			$plugin['source'] = $api->download_link;
		}
	}

	/** Set type, based on whether the source starts with http:// or https:// */
	$type = preg_match( '|^http(s)?://|', $plugin['source'] ) ? 'web' : 'upload';

	/** Prep variables for Plugin_Installer_Skin class */
	/* translators: %s: plugin name */
	$title = sprintf( esc_html__( 'Installing %s', 'apollo13-framework-extensions' ), $plugin['name'] );
	$url   = esc_url( add_query_arg( array(
		'action' => 'install-plugin',
		'plugin' => $plugin['slug']
	), 'update.php' ) );

	$nonce = 'install-plugin_' . $plugin['slug'];

	/** Prefix a default path to pre-packaged plugins */
	$source = $plugin['source'];


	/** Create a new instance of Plugin_Upgrader */
	$upgrader = new Plugin_Upgrader( $skin = new A13FRAMEWORK_Plugin_Installer_Skin( compact( 'type', 'title', 'url', 'nonce', 'plugin', 'api' ) ) );

	/** Perform the action and install the plugin from the $source urldecode() */
	if ( $install_type === 'update' ) {
		delete_site_transient( 'update_plugins' );
		$data                                       = get_site_transient( 'update_plugins' );
		$data->response[ $plugin['slug'] ]->version = $plugin['version'];

		set_site_transient( 'update_plugins', $data );
		$upgrader->upgrade( $plugin['slug'] );
		/* translators: %s: plugin name */
		echo sprintf( esc_html__( 'Plugin updated: %s', 'apollo13-framework-extensions' ), esc_html( $plugin['name'] ) ) . "\r\n";
	} elseif ( $install_type === 'install' ) {
		$upgrader->install( $source );
		/* translators: %s: plugin name */
		echo sprintf( esc_html__( 'Plugin installed: %s', 'apollo13-framework-extensions' ), esc_html( $plugin['name'] ) ) . "\r\n";
	}

	/** Flush plugins cache so we can make sure that the installed plugins list is always up to date */
	wp_cache_flush();

	// Try to activate plugin
	if ( $activate_plugin ) {
		$plugin_activate = $upgrader->plugin_info(); // Grab the plugin info from the Plugin_Upgrader method
		// Activation of only inactive plugins
		if ( $plugin_activate === false ) {
			$activate = activate_plugin( $plugin['file_path'] );
		} // Activating while installing/updating
		else {
			$activate = activate_plugin( $plugin_activate );
		}

		if ( is_wp_error( $activate ) ) {
			print wp_kses_post( $activate->get_error_message() );
		} else {
			/* translators: %s: plugin name */
			echo sprintf( esc_html__( 'Plugin activated: %s', 'apollo13-framework-extensions' ), esc_html(  $plugin['name'] ) ) . "\r\n";
		}
	}

	//delete options for welcome pages that are braking import
	delete_transient( '_vc_page_welcome_redirect' ); //Visual Composer
	delete_transient( '_wc_activation_redirect' ); //Woocommerce
	delete_transient( 'wpforms_activation_redirect' ); //WPForms

	return true;
}


function a13fe_demo_data_install_revo_sliders($demo_id) {
	//imports all Revolution sliders
	if ( class_exists( 'RevSlider' ) ) {
		$slider = new RevSlider();
		$demo = unserialize(get_option(A13FRAMEWORK_TPL_SLUG .'_processed_demo_import'));
		$revos = $demo['demo']['revo'];

		$dir = A13FRAMEWORK_IMPORTER_TMP_DIR . '/' . $demo_id . '/';

		if ( is_dir( $dir ) ) {
			foreach ( $revos as $revo ) {
				$_FILES["import_file"]["tmp_name"] = $dir . $revo . '.zip';
				$slider->importSliderFromPost();
			}
		}
	}

	//this step is done
	return true;
}


function a13fe_demo_data_install_content( $sublevel, &$sublevel_name, $demo_id, $chosen_options ) {
	//imports all posts, pages, works etc.
	require_once A13FE_BASE_DIR.'/design_importer/a13-wordpress-importer/class-apollo13-framework-import.php';

	if ( class_exists( 'Apollo13Framework_Import' ) ) {

		$file = A13FRAMEWORK_IMPORTER_TMP_DIR . '/' . $demo_id . '/data.xml';
		set_time_limit( 0 );
		//we get previous state
		if ( strlen( $sublevel ) ) {
			$importer = unserialize( get_transient( 'a13_import_content_process' ) );
		}
		//we start importer for first time
		else {
			$importer = new Apollo13Framework_Import();
		}
		$is_done = $importer->import( $file, isset($chosen_options['install-attachments']) );
		if ( ! $is_done ) {
			//number of imported already items
			$all_items = sizeof($importer->posts);
			$last_item = 0;
			foreach($importer->posts as $key => $post){
				if($post['post_id'] == $importer->last_post_did){
					$last_item = $key;
					break;
				}
			}

			//empty posts array, cause we will read from file again and it adds lot of weight
			$importer->posts = array();

			//save last state
			set_transient( 'a13_import_content_process', serialize( $importer ), HOUR_IN_SECONDS );

			$sublevel        = esc_html__( 'Importing the content', 'apollo13-framework-extensions' );
			/* translators: %1$d - number, %2$d - number */
			$sublevel_name   = sprintf( esc_html__( 'Imported %1$d of %2$d items', 'apollo13-framework-extensions' ), $last_item, $all_items ) . "\r\n";

			return $sublevel;
		}
	}

	//this step is done
	return true;
}


function a13fe_demo_data_setup_widgets() {
	global $apollo13framework_site_config;
	if ( is_array($apollo13framework_site_config) ) {
		//first put widgets configuration
		$widget_config =  $apollo13framework_site_config['widgets'];
		foreach ( $widget_config as $name => $value ) {
			update_option( $name, $value );
		}

		//next lets tell which widget in which sidebar is:-)
		$sidebars_config = $apollo13framework_site_config['sidebars'];
		update_option( 'sidebars_widgets', $sidebars_config );
	}

	//this step is done
	return true;
}


function a13fe_demo_data_setup_fp() {
	global $apollo13framework_site_config;
	if ( is_array($apollo13framework_site_config) ) {
		$config = $apollo13framework_site_config['frontpage'];
		foreach ( $config as $name => $value ) {

			update_option( $name, $value );
		}
	}

	//this step is done
	return true;
}


function a13fe_demo_data_setup_plugins_configs() {
	global $apollo13framework_site_config;
	if ( is_array($apollo13framework_site_config) ) {
		$config = $apollo13framework_site_config['plugins_configs'];
		foreach ( $config as $name => $value ) {
			update_option( $name, $value );
		}

		//refresh global css settings for Elementor
		if(defined('ELEMENTOR_VERSION')){
			delete_option('_elementor_global_css');

			//move some global setting from Elementor 2.x to 3.x
			if(method_exists('\Elementor\Core\Upgrade\Upgrades', '_v_3_0_0_move_default_typography_to_kit') ){
				$updater = \Elementor\Plugin::instance()->upgrade->get_task_runner();

				$updater->set_current_item( [
					'iterate_num' => 1,
				] );
				\Elementor\Core\Upgrade\Upgrades::_v_3_0_0_move_default_typography_to_kit($updater);
				\Elementor\Core\Upgrade\Upgrades::_v_3_0_0_move_default_colors_to_kit($updater);
			}
		}
	}

	//this step is done
	return true;
}


function a13fe_demo_data_setup_wc() {
	global $apollo13framework_site_config;
	if ( is_array($apollo13framework_site_config) ) {
		$config = $apollo13framework_site_config['woocommerce'];
		foreach ( $config as $name => $value ) {
			update_option( $name, $value );
		}
	}

	//this step is done
	return true;
}


function a13fe_demo_data_setup_menus() {
	global $apollo13framework_site_config;
	if ( is_array($apollo13framework_site_config) ) {
		$menusy    = $apollo13framework_site_config['menus'];
		$nav_menus = wp_get_nav_menus( array( 'orderby' => 'name' ) );

		foreach ( $menusy as $location => $term ) {
			//search for such menu in available menus
			$our_menu = false;
			foreach ( $nav_menus as $menu ) {
				if ( $menu->slug === $term ) {//found it!
					$our_menu = $menu;
					break;
				}
			}

			if ( $our_menu !== false ) {
				//set this menu to proper location
				$locations = get_theme_mod( 'nav_menu_locations' );
				/** @noinspection PhpUndefinedFieldInspection */
				$locations[ $location ] = $our_menu->term_id;
				set_theme_mod( 'nav_menu_locations', $locations );
			}

		}
	}


	//this step is done
	return true;
}


function a13fe_demo_data_setup_permalinks() {
	global $wp_rewrite;
	$wp_rewrite->set_permalink_structure( '/%postname%/' );
	update_option('a13_force_to_flush','on');

	//this step is done
	return true;
}


function a13fe_demo_data_import_predefined_set( $demo_id ) {
	/* @var $wp_filesystem WP_Filesystem_Base */
	global $wp_filesystem, $apollo13framework_a13;
	$config_file = A13FRAMEWORK_IMPORTER_TMP_DIR . '/' . $demo_id . '/theme.json';

	//in case of FTP access we need to make sure we have proper path
	$config_file = str_replace(ABSPATH, $wp_filesystem->abspath(), $config_file);

	if($wp_filesystem->exists($config_file)) {
		$file_contents = $wp_filesystem->get_contents($config_file);

		//rewrite paths to images in old not fully valid format
		$current_content_path_non_json = content_url();
		$possible_demo_paths_non_json = array(
			'https://rifetheme.com/wp-content',
			'http://rifetheme.com/wp-content',
		);
		foreach($possible_demo_paths_non_json as $old_content_path){
			$file_contents = str_replace($old_content_path, $current_content_path_non_json, $file_contents);
		}

		//rewrite paths to images in new JSON valid format
		$current_content_path = trim( json_encode( content_url() ), '"' );
		$possible_demo_paths = array(
			'https:\/\/rifetheme.com\/wp-content',
			'http:\/\/rifetheme.com\/wp-content',
		);
		foreach($possible_demo_paths as $old_content_path){
			$file_contents = str_replace($old_content_path, $current_content_path, $file_contents);
		}

		$options_to_import = json_decode( $file_contents, true );
		$apollo13framework_a13->set_options( $options_to_import );
	}

	//this step is done
	return true;
}

function a13fe_demo_data_generate_custom_style() {
	//generate user.css file
	do_action( 'apollo13framework_generate_user_css' );

	//this step is done
	return true;
}


function a13fe_demo_data_clean($demo_id){
	$demo = unserialize(get_option(A13FRAMEWORK_TPL_SLUG .'_processed_demo_import'));

	//collect files to remove
	$files_to_remove = array(
		'data.xml',
		'extra_plugins.php',
		'site_config',
		'theme.json',
	);

	//Sliders to remove
	$needed_revos = $demo['demo']['revo'];
	foreach ( $needed_revos as $revo_index => $needed_revo ) {
		$files_to_remove[] = $needed_revo . '.zip';
	}

	//remove import options
	delete_option( A13FRAMEWORK_TPL_SLUG .'_processed_demo_import' );
	delete_option( 'a13fe_import_by_http' );

	//remove files if they still exist
	foreach ( $files_to_remove as $file ) {
		$file_to_delete = A13FRAMEWORK_IMPORTER_TMP_DIR.'/'.$demo_id . '/' . $file;

		if ( file_exists( $file_to_delete ) ) {
			wp_delete_file( $file_to_delete );
		}
	}

	/* @var $wp_filesystem WP_Filesystem_Base */
	global $wp_filesystem;

	if( $wp_filesystem->exists(A13FRAMEWORK_IMPORTER_TMP_DIR.'/'.$demo_id) ){
		$wp_filesystem->rmdir(A13FRAMEWORK_IMPORTER_TMP_DIR.'/'.$demo_id);
	}


	return true;
}

/* define settings var to be used by other functions */
global $apollo13framework_site_config;
$apollo13framework_site_config = false;

//check if we can proceed
return a13fe_demo_data_prepare_filesystem();