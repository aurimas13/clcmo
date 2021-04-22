<?php
// create shortcode for scroller
add_shortcode( 'a13fe-scroller', 'a13fe_scroller_shortcode' );
function a13fe_scroller_shortcode( $atts ) {
	ob_start();

	$id          = '';
	$autoplay    = '';
	$texts       = '';
	$window_high = '';
	$ratio       = '';
	$socials     = '';
	$effect     = '';
	$parallax     = '';

	// define attributes and their defaults
	extract( shortcode_atts( array(
		'id'          => false,
		'autoplay'    => '',
		'texts'       => '',
		'window_high' => 'off',
		'ratio'       => '',
		'socials'     => 'off',
		'effect'      => '',
		'parallax'    => ''
	), $atts ) );

	//without id
	if ( $id === false ) {
		return esc_html__( 'Please select proper album or work to use.', 'apollo13-framework-extensions' );
	}

	//it doesn't contain items to use
	$has_images = strlen( get_post_meta( $id, '_images_n_videos', true ) ) > 0;
	if ( ! $has_images ) {
		return sprintf( esc_html__( 'Error: Selected post does not contain any media(%s) to use.', 'apollo13-framework-extensions' ), esc_html__( 'images', 'apollo13-framework-extensions' ) ) .' '. esc_html__( 'Please select proper album or work to use.', 'apollo13-framework-extensions' );
	}

	//check if such options are defined in parent post. If not don't set them so they will return to theme defaults
	$test = strlen( get_post_meta( $id, '_scroller_wrap_around', true ) );

	//lets merge shortcode params & post params
	$opts = array();


	//autoplay
	//not provided - try to get from parent post
	if ( $test && ! strlen( $autoplay ) ) {
		if (  get_post_meta( $id, '_scroller_autoplay', true ) === 'on' ) {
			$time                                     = (float) get_post_meta( $id, '_scroller_autoplay_time', true ) * 1000;
			$opts['autoPlay']             = $time;
			$opts['pauseAutoPlayOnHover'] = get_post_meta( $id, '_scroller_pause_autoplay', true ) === 'on';
		}
	}
	//use from shortcode
	else{
		$opts['autoPlay'] = $autoplay * 1000;
	}


	//texts
	//not provided - try to get from parent post
	if ( ! strlen( $texts ) ) {
		$texts = get_post_meta( $id, '_enable_desc', true );
		$texts = $texts === 'on';
	}
	//use from shortcode
	else {
		$texts = ( $texts === 'true' || $texts === '1' );
	}
	$opts['a13ShowDesc'] = $texts;


	//effect
	//use from shortcode
	if ( strlen( $effect ) ) {
		$opts['a13Effect'] = $effect;
	}
	//not provided - try to get from parent post
	elseif($test){
		$opts['a13Effect'] = get_post_meta( $id, '_scroller_effect', true );
	}


	//parallax
	//use from shortcode
	if ( strlen( $parallax ) ) {
		$opts['a13Parallax'] = ( $parallax === 'true' || $parallax === '1' );
	}
	//not provided - try to get from parent post
	elseif($test){
		$opts['a13Parallax'] = get_post_meta( $id, '_theme', true ) === 'scroller-parallax';
	}

	//parallax
	//use from shortcode
	if ( ! $window_high !== 'off' ) {
		$window_high = ( $window_high === 'true' || $window_high === '1' );
	}
	//not provided
	else{
		$window_high = false;
	}
	$opts['a13WindowHigh'] = $window_high;


	//socials
	//use from shortcode
	if ( ! $socials !== 'off' ) {
		$socials = ( $socials === 'true' || $socials === '1' );
	}
	//not provided
	else{
		$socials = false;
	}
	$opts['a13Socials'] = $socials;


	//ratio
	//from shortcode
	$opts['a13Ratio'] = $ratio;


	//other settings from parent album
	if ( strlen( $test ) ) {
		$opts['wrapAround']         = get_post_meta( $id, '_scroller_wrap_around', true ) === 'on';
		$opts['contain']            = get_post_meta( $id, '_scroller_contain', true ) === 'on';
		$opts['freeScroll']         = get_post_meta( $id, '_scroller_free_scroll', true ) === 'on';
		$opts['prevNextButtons']    = get_post_meta( $id, '_scroller_arrows', true ) === 'on';
		$opts['pageDots']           = get_post_meta( $id, '_scroller_dots', true ) === 'on';
		$opts['a13CellWidth']       = get_post_meta( $id, '_scroller_cell_width', true );
		$opts['a13CellWidthMobile'] = get_post_meta( $id, '_scroller_cell_width_mobile', true );
	}


	wp_enqueue_script('flickity');
	if(function_exists('apollo13framework_make_scroller')) {
		apollo13framework_make_scroller( $opts, $id );
	}

	$output = ob_get_clean();

	return $output;
}