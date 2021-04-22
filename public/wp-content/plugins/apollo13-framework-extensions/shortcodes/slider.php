<?php
// create shortcode for slider
add_shortcode( 'a13fe-slider', 'a13fe_slider_shortcode' );
function a13fe_slider_shortcode( $atts ) {
	//works only with apollo13 framework themes
	if(!function_exists('apollo13framework_make_slider')) {
		return '';
	}

	ob_start();

	$id          = '';
	$autoplay    = '';
	$texts       = '';
	$thumbs      = '';
	$window_high = '';
	$ratio       = '';
	$socials     = '';

	// define attributes and their defaults
	extract( shortcode_atts( array(
		'id'          => false,
		'autoplay'    => '',
		'texts'       => '',
		'thumbs'      => '',
		'window_high' => 'off',
		'ratio'       => '',
		'socials'     => 'off'
	), $atts ) );

	//without id
	if ( $id === false ) {
		return esc_html__( 'Please select proper album or work to use.', 'apollo13-framework-extensions' );
	}

	//it doesn't contain items to use
	$has_images = strlen( get_post_meta( $id, '_images_n_videos', true ) ) > 0;
	if ( ! $has_images ) {
		/* translators: %s: media type */
		return sprintf( esc_html__( 'Error: Selected post does not contain any media(%s) to use.', 'apollo13-framework-extensions' ), esc_html__( 'images &amp; videos', 'apollo13-framework-extensions' ) ) .' '. esc_html__( 'Please select proper album or work to use.', 'apollo13-framework-extensions' );
	}

	global $apollo13framework_a13;

	//lets merge shortcode params & post params

	//param not given
	if ( ! strlen( $autoplay ) ) {
		$autoplay = $apollo13framework_a13->get_meta( '_autoplay', $id );
	} //param set
	else {
		$autoplay = ( $autoplay === 'true' || $autoplay === '1' ) ? 'on' : 'off';
	}

	//param not given
	if ( ! strlen( $texts ) ) {
		$texts = get_post_meta( $id, '_enable_desc', true );
		$texts = $texts === 'on' ? $texts : 'off';
	} //param set
	else {
		$texts = ( $texts === 'true' || $texts === '1' ) ? 'on' : 'off';
	}

	//param not given
	if ( ! strlen( $thumbs ) ) {
		$thumbs = $apollo13framework_a13->get_meta( '_thumbs', $id );
		$thumbs = $thumbs === 'on' ? $thumbs : 'off';
	} //param set
	else {
		$thumbs = ( $thumbs === 'true' || $thumbs === '1' ) ? 'on' : 'off';
	}


	//param different then default
	if ( ! $window_high !== 'off' ) {
		$window_high = ( $window_high === 'true' || $window_high === '1' ) ? 'on' : $window_high;
	}

	//param different then default
	if ( ! $socials !== 'off' ) {
		$socials = ( $socials === 'true' || $socials === '1' ) ? 'on' : $socials;
	}

	//params from post in question
	$title_color = get_post_meta( $id, '_slide_title_bg_color', true );
	$title_color = ( $title_color === '' || $title_color === false || $title_color === 'transparent' ) ? '' : $title_color;
	$ken_scale   = $apollo13framework_a13->get_meta( '_ken_scale', $id );
	$ken_scale   = strlen( $ken_scale ) ? $ken_scale : 120;

	//get globals depending on post type
	$post_type = get_post_type( $id );
	if ( $post_type === 'work' ) {
		$transition_time = $apollo13framework_a13->get_option( 'work_slider_transition_time' );
		$slide_interval  = $apollo13framework_a13->get_option( 'work_slider_slide_interval' );
	} else {
		$transition_time = $apollo13framework_a13->get_option( 'album_slider_transition_time' );
		$slide_interval  = $apollo13framework_a13->get_option( 'album_slider_slide_interval' );
	}


	$slider_opts = array(
		'autoplay'        => $autoplay,
		'transition'      => $apollo13framework_a13->get_meta( '_transition', $id ),
		'fit_variant'     => $apollo13framework_a13->get_meta( '_fit_variant', $id ),
		'pattern'         => $apollo13framework_a13->get_meta( '_pattern', $id ),
		'gradient'        => $apollo13framework_a13->get_meta( '_gradient', $id ),
		'ken_burns_scale' => $ken_scale,
		'texts'           => $texts,
		'title_color'     => $title_color,
		'transition_time' => $transition_time,
		'slide_interval'  => $slide_interval,
		'thumbs'          => $thumbs,
		'thumbs_on_load'  => $apollo13framework_a13->get_meta( '_thumbs_on_load', $id ),
		'socials'         => $socials,
		'window_high'     => $window_high,
		'ratio'           => $ratio,
	);

	wp_enqueue_script('apollo13framework-slider');
	apollo13framework_make_slider($slider_opts, $id);

	$output = ob_get_clean();

	return $output;
}