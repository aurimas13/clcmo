<?php
require_once A13FE_BASE_DIR.'supports/wpbakery_pb_extensions/map_config.php';
require_once A13FE_BASE_DIR.'supports/wpbakery_pb_extensions/actions.php';
require_once A13FE_BASE_DIR.'supports/wpbakery_pb_extensions/filters.php';
require_once A13FE_BASE_DIR.'supports/wpbakery_pb_extensions/shortcodes.php';
if( is_admin() ){
	require_once A13FE_BASE_DIR . 'supports/wpbakery_pb_extensions/theme_post_grids.php';
	require_once A13FE_BASE_DIR . 'supports/wpbakery_pb_extensions/nava_support.php';
}




function a13fe_vc_custom_post_type(){
	//nava post type for anchor navigation
	$nava_type = defined( 'A13FRAMEWORK_CUSTOM_POST_TYPE_NAV_A' ) ? A13FRAMEWORK_CUSTOM_POST_TYPE_NAV_A : 'nava';

	$labels   = array(
		'name'               => __( 'One Page Navigation Pointer', 'apollo13-framework-extensions' ),
	);

	$args     = array(
		'labels'              => $labels,
		'exclude_from_search' => true,
		'public'              => true,
		'show_in_menu'        => false,
		'show_in_nav_menus'   => true,
		'publicly_queryable'  => true,
		'query_var'           => true,
		'rewrite'             => false,
		'supports'            => array(),
	);

	register_post_type( $nava_type, $args );
}
add_action( 'init', 'a13fe_vc_custom_post_type' );



add_action( 'vc_base_register_front_js', 'a13fe_remove_vc_conflicts' );
/**
 * remove some conflicts with Visual Composer
 */
function a13fe_remove_vc_conflicts(){
	/* REMOVE ISOTOPE CONFLICT */
	if( defined('A13FRAMEWORK_THEME_VERSION') ){
		global $wp_scripts;
		$wp_scripts->registered[ 'isotope' ]->src = get_theme_file_uri( 'js/isotope.pkgd.min.js' );
		$wp_scripts->registered[ 'isotope' ]->ver = '3.0.6';
	}
}



add_filter( 'vc_gitem_zone_image_block_link', 'a13fe_vc_change_media_grid_lightbox', 11, 1 );
/**
 * Changes default lightbox in Visual composer media grid element
 *
 * @param string $block HTML of whole link for grid item
 *
 * @return string
 */
function a13fe_vc_change_media_grid_lightbox( $block ) {
	if( defined('A13FRAMEWORK_THEME_VERSION') ){
		global $apollo13framework_a13;

		//'lightbox_vc_media_grid' option is deprecated and can only be changed via filter a13_options_lightbox_vc_media_grid
		if( $apollo13framework_a13->get_option( 'lightbox_vc_media_grid', 'on' ) !== 'off' ){
			$block = str_replace(
				array(
					'post_image_url',
					' prettyphoto',
					' data-vc-gitem-zone="prettyphotoLink"',
					' vc-prettyphoto-link',
					'><'
				),
				array(
					'post_image_url::full',
					' a13-lightbox-added',
					' data-sub-html=".vc-mg-item-desc"',
					'',
					'><div class="vc-mg-item-desc"><div class="customHtml"><h4>{{ post_title }}</h4></div></div><'
				),
				$block );
		}
	}
	return $block;
}