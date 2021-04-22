<?php
// create shortcode for socials icons
add_shortcode( 'a13fe-socials', 'a13fe_socials_shortcode' );
function a13fe_socials_shortcode( $atts ) {
	//works only with apollo13 framework themes
	if(!function_exists('apollo13framework_social_icons')) {
		return '';
	}

	ob_start();

	$normal = '';
	$hover  = '';

	// define attributes and their defaults
	extract( shortcode_atts( array(
		'normal' => 'black',
		'hover'  => 'color',
	), $atts ) );

	echo apollo13framework_social_icons( $normal, $hover );

	$output = ob_get_clean();

	return $output;
}