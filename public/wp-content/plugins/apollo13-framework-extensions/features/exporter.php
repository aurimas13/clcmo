<?php

/**
 * Displays page content of Exporter tab in Theme pages
 */
function a13fe_get_demo_exporter_content(){
	global $apollo13framework_a13;

	do_action('apollo13framework_before_export_theme_options_section');
	?>
	<h2><?php echo esc_html__( 'Export &amp; Import theme options', 'apollo13-framework-extensions' ); ?></h2>
	<p style="text-align: center;"><a href="<?php echo esc_url( $apollo13framework_a13->get_docs_link('export') ); ?>"><?php echo esc_html__( 'Check the documentation for instructions about using the Export Area.', 'apollo13-framework-extensions' ); ?></a></p>
	<label for="export_theme_options_field"><?php echo esc_html__( 'Export theme settings', 'apollo13-framework-extensions' ); ?></label>
	<textarea rows="10" cols="20" class="large-text" id="export_theme_options_field" readonly><?php echo esc_textarea( a13fe_export_theme_setting() );?></textarea>
	<button class="button button-secondary copy-content" type="submit"><?php echo esc_html__( 'Copy to clipboard', 'apollo13-framework-extensions' ); ?></button>

	<hr />

	<label for="import_theme_options_field"><?php echo esc_html__( 'Import theme settings', 'apollo13-framework-extensions' ); ?></label>
	<textarea rows="10" cols="20" class="large-text" id="import_theme_options_field"></textarea>
	<button class="button button-primary import-theme-settings" data-import-field="import_theme_options_field" type="submit"><?php echo esc_html__( 'Import theme settings', 'apollo13-framework-extensions' ); ?></button>
	<div class="attention"><?php echo esc_html__( 'Attention! This will overwrite your current theme settings.', 'apollo13-framework-extensions' ); ?></div>

	<?php
	//export demo data field
	if ( apollo13framework_is_home_server() ){
		?>
		<hr />
		<label for="export_options_field">site_config</label>
		<textarea rows="10" cols="20" class="large-text" id="export_options_field" readonly><?php echo esc_textarea( a13fe_collect_site_data() );?></textarea>
		<button class="button button-secondary copy-content" type="submit"><?php echo esc_html__( 'Copy to clipboard', 'apollo13-framework-extensions' ); ?></button>
	<?php }
}
add_action('apollo13framework_apollo13_exporter_page_content', 'a13fe_get_demo_exporter_content');


/**
 * Returns theme settings in form of JSON string
 * @since  1.4.0
 *
 * @return string theme settings
 */
function a13fe_export_theme_setting() {
	return wp_json_encode( get_option(A13FRAMEWORK_OPTIONS_NAME) );
}


/**
 * Collect data for site export
 * @since  1.4.0
 *
 * @return array site settings
 */
function a13fe_collect_site_data() {
	$export = array();

	//export widgets
	global $wp_registered_widgets;
	$widgets_types = array();


	//we collect all registered widgets and check if we can get their id_base
	foreach ( $wp_registered_widgets as $widget ) {
		$temp_callback = $widget['callback'];
		if ( is_array( $temp_callback ) ) {
			$widgets_types[] = 'widget_' . $temp_callback[0]->id_base;
		}
	}

	//remove duplicates
	$widgets_types = array_unique( $widgets_types );

	//collect export info only
	$export_widgets = array();
	foreach ( $widgets_types as $type ) {
		$temp_type = get_option( $type );
		if ( $temp_type !== false ) {
			$export_widgets[ $type ] = $temp_type;
		}
	}

	//our export value
	$export['widgets'] = $export_widgets;


	//export sidebars
	$export['sidebars'] = get_option( 'sidebars_widgets' );


	//export frontpage
	$fp_options = array(
		'show_on_front'  => get_option( 'show_on_front' ),
		'page_on_front'  => get_option( 'page_on_front' ),
		'page_for_posts' => get_option( 'page_for_posts' )
	);

	//our export value
	$export['frontpage'] = $fp_options;


	//export menus
	$menu_locations = get_nav_menu_locations();
	foreach ( $menu_locations as $key => $id ) {
		if ( $id === 0 ) {
			continue;
		}
		$obj = get_term( $id, 'nav_menu' );
		//instead of id save slug of menu
		$menu_locations[ $key ] = $obj->slug;
	}

	$export['menus'] = $menu_locations;


	//export plugins settings
	//AddToAny
	$plugins_settings = array();
	if ( function_exists( 'A2A_SHARE_SAVE_init' ) ) {
		$plugins_settings['addtoany_options'] = get_option( 'addtoany_options' );
	}

	//Elementor
	if( defined( 'ELEMENTOR_VERSION' ) ){
		$options_to_export = array(
			'elementor_cpt_support',
			'elementor_scheme_color',
			'elementor_scheme_typography',
		);

		foreach( $options_to_export as $name ) {
			$temp = get_option( $name );
			//export only set options
			if( $temp !== false ){
				$plugins_settings[ $name ] = $temp;
			}
		}
	}


	//WPForms
	if( class_exists( 'WPForms') ){
		$plugins_settings['wpforms_settings'] = get_option( 'wpforms_settings' );
	}

	$export['plugins_configs'] = $plugins_settings;


	//Woocommerce
	if ( apollo13framework_is_woocommerce_activated() ) {

		$options_to_export = array(
			//pages
			'woocommerce_shop_page_id',
			'woocommerce_cart_page_id',
			'woocommerce_checkout_page_id',
			'woocommerce_myaccount_page_id',
			//image sizes
			'woocommerce_single_image_width',
			'woocommerce_thumbnail_image_width',
			'woocommerce_thumbnail_cropping_custom_width',
			'woocommerce_thumbnail_cropping_custom_height',
			'woocommerce_thumbnail_cropping'
		);

		$wc_options = array();

		foreach( $options_to_export as $name ) {
			$temp = get_option( $name );
			//export only set options
			if( $temp !== false ){
				$wc_options[ $name ] = $temp;
			}
		}

		//wishlist settings
		if ( class_exists( 'YITH_WCWL' ) ) {
			$wc_options['yith_wcwl_wishlist_page_id'] = get_option( 'yith_wcwl_wishlist_page_id' );
		}

		//our export value
		$export['woocommerce'] = $wc_options;
	}
	return wp_json_encode( $export );
}


add_action( 'wp_ajax_apollo13framework_import_theme_settings', 'a13fe_import_theme_settings' );
/**
 * Imports theme settings from "Export" screen on AJAX call
 * @since  1.4.0
 */
function a13fe_import_theme_settings() {
	/* in case there is old version of theme which had this feature inside, we don't add it */
	if ( function_exists( 'apollo13framework_import_theme_settings' ) ){
		return;
	}

	$out['response'] = 'success';
	$out['message']  = '';

	$settings = isset( $_POST['settings'] ) ? sanitize_text_field( wp_unslash( $_POST['settings'] ) ) : '';
	if( strlen( $settings ) ){
		//make sure we will have UTF8 JSON
		if( function_exists( 'utf8_encode' ) ){
			$settings = utf8_encode( $settings );
		}

		//decode
		$settings = json_decode( $settings, true );

		if( ! is_null( $settings ) ){
			global $apollo13framework_a13;

			//do the import
			$apollo13framework_a13->set_options( $settings );
			//generate user.css file
			do_action( 'apollo13framework_generate_user_css' );

			$out['message'] = esc_html__( 'The import was successful!', 'apollo13-framework-extensions' );
		}
		else{
			$out['response'] = 'error';
			$out['message']  = esc_html__( 'Looks like incorrectly formatted JSON string, cannot proceed with the import.', 'apollo13-framework-extensions' );
		}
	}
	else{
		$out['response'] = 'error';
		$out['message']  = esc_html__( 'Nothing to import.', 'apollo13-framework-extensions' );
	}

	echo wp_json_encode( $out, JSON_FORCE_OBJECT );
	exit;
}