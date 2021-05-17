<?php
// create shortcode for bricks gallery
add_shortcode( 'a13fe-gallery', 'a13fe_gallery_shortcode' );
function a13fe_gallery_shortcode( $atts ) {
	//works only with apollo13 framework themes
	if(!function_exists('apollo13framework_make_bricks_gallery')) {
		return '';
	}

	ob_start();

	$id             = '';
	$cover_color    = '';
	$filter         = '';
	$lightbox       = '';
	$texts_position = '';
	$hover_effect   = '';
	$cover          = '';
	$cover_hover    = '';
	$gradient       = '';
	$gradient_hover = '';
	$texts          = '';
	$texts_hover    = '';
	$socials        = '';
	$margin         = '';
	$ratio          = '';
	$columns        = '';

	// define attributes and their defaults
	extract( shortcode_atts( array(
		'id'             => false,
		'columns'        => 3,
		'margin'         => '5px',
		'ratio'          => '0',
		'filter'         => 'off',
		'lightbox'       => 'on',
		'hover_effect'   => 'drop',
		'cover'          => '',
		'cover_hover'    => '',
		'cover_color'    => 'rgba(0,0,0,0.7)',
		'texts'          => '',
		'texts_hover'    => '',
		'texts_position' => 'bottom_center',
		'gradient'       => '',
		'gradient_hover' => '',
		'socials'        => 'off',
	), $atts ) );

	//make sure on/off params have proper values
	$on_off_attrs = array(
		'filter',
		'lightbox',
		'cover',
		'cover_hover',
		'gradient',
		'gradient_hover',
		'texts',
		'texts_hover',
		'socials'
	);

	foreach($on_off_attrs as $attribute ){
		if($$attribute === '1' || $$attribute === 'true' ){
			$$attribute = 'on';
		}
		elseif($$attribute === '0' || $$attribute === 'false' ){
			$$attribute = 'off';
		}
	}


	//without id
	if ( $id === false ) {
		return esc_html__( 'Please select proper album or work to use.', 'apollo13-framework-extensions' );
	}

	//it doesn't contain items to use
	$has_images = strlen( get_post_meta( $id, '_images_n_videos', true ) ) > 0;
	if ( ! $has_images ) {
		return sprintf( esc_html__( 'Error: Selected post does not contain any media(%s) to use.', 'apollo13-framework-extensions' ), esc_html__( 'images &amp; videos', 'apollo13-framework-extensions' ) ) .' '. esc_html__( 'Please select proper album or work to use.', 'apollo13-framework-extensions' );
	}

	$gallery_opts = array(
		'cover_color'            => $cover_color,
		'filter'                 => $filter,
		'lightbox'               => $lightbox,
		'title_position'         => $texts_position,
		'hover_effect'           => $hover_effect,
		'overlay_cover'          => $cover,
		'overlay_cover_hover'    => $cover_hover,
		'overlay_gradient'       => $gradient,
		'overlay_gradient_hover' => $gradient_hover,
		'overlay_texts'          => $texts,
		'overlay_texts_hover'    => $texts_hover,
		'socials'                => $socials,
		'margin'                 => $margin,
		'proportion'             => $ratio,
		'columns'                => $columns,
		'max_width'              => get_post_meta( $id, '_bricks_max_width', true ),
		'show_desc'              => get_post_meta( $id, '_enable_desc', true )
	);

	apollo13framework_make_bricks_gallery($gallery_opts, $id);

	$output = ob_get_clean();

	return $output;
}