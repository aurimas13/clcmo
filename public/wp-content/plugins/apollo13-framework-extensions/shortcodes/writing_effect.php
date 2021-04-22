<?php
// create shortcode for slider
add_shortcode( 'a13fe-writing-effect', 'a13fe_writing_shortcode' );
function a13fe_writing_shortcode( $atts, $content ) {
	ob_start();

	$color         = '';
	$bg_color    = '';
	$loop       = '';

	// define attributes and their defaults
	extract( shortcode_atts( array(
		'color'      => '',
		'bg_color'    => '',
		'loop'       => 1,
	), $atts ) );

	//format text for type effect
	$typed_style = '';
	//colors
	if( $color != ''){
		$typed_style .= 'color:'.$color.';';
	}
	if( $bg_color != ''){
		$typed_style .= 'background-color:'.$bg_color.';';
	}

	//get style together
	if( $typed_style != ''){
		$typed_style = 'style="'.$typed_style.'"';
	}

	//what to write
	$sentences = explode('|', $content);

	$sentences_html = '';
	foreach( $sentences as $sentence ){
		$sentences_html .= '<span>'.$sentence.'</span>';
	}

	echo '<span class="a13-to-type" data-loop="'.$loop.'"><span class="sentences-to-type">'.$sentences_html.'</span><span class="typing-area" '.$typed_style.'></span></span>';

	//we need waypoints script, lets check for:
	//elementor version
	if (wp_script_is( 'elementor-waypoints', 'registered' )) {
		wp_enqueue_script('elementor-waypoints');
	}
	//from theme
	else {
		wp_enqueue_script('noframework-waypoints');
	}

	$output = ob_get_clean();

	return $output;
}