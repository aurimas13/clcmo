<?php


function a13fe_countdown_render( $atts ) {
	$output = $fcolor = $bcolor = $style = $year = $month = $day = $hour = $minute = $el_class = $gap = '';

	extract( shortcode_atts( array(
		'style'    => 'simple',
		'year'     => '',
		'month'    => '',
		'day'      => '',
		'hour'     => '00',
		'minute'   => '00',
		'fcolor'   => '',
		'bcolor'   => '',
		'gap'      => '',
		'el_class' => ''
	), $atts ) );

	$gap       = ( $gap != '' ? '0 ' . $gap . 'px' : '0' );
	$unique_class = 'a13_class_' . rand();

	//check for script in theme version < 1.8.0
	if(wp_script_is( 'jquery.countdown', 'registered' ) || wp_script_is( 'jquery.countdown', 'enqueued' )){
		wp_enqueue_script( 'jquery.countdown' );
	}
	//check for script in theme version >= 1.8.0
	elseif(wp_script_is( 'jquery-countdown', 'registered' ) || wp_script_is( 'jquery-countdown', 'enqueued' )){
		wp_enqueue_script( 'jquery-countdown' );
	}

	$date_str = sprintf( '%s/%s/%s %s:%s:00', $year, $month, $day, $hour, $minute );

	$output .= '<style> .' . $unique_class . ' .block{margin:' . $gap . ';color:' . $fcolor . ';background-color:' . $bcolor . ';} </style>';

	$output .= '<div class="a13_count_down ' . $unique_class . ' ' . $style . ' ' . $el_class . '" data-style="' . $style . '" data-weeks="' . esc_html__( 'Weeks', 'apollo13-framework-extensions' ) . '" data-days="' . esc_html__( 'Days', 'apollo13-framework-extensions' ) . '" data-hours="' . esc_html__( 'Hours', 'apollo13-framework-extensions' ) . '" data-minutes="' . esc_html__( 'Minutes', 'apollo13-framework-extensions' ) . '" data-seconds="' . esc_html__( 'Seconds', 'apollo13-framework-extensions' ) . '" data-date="' . $date_str . '"></div>';

	if ( $style == 'flipping' ) {
		$output .= '<script type="text/template" id="main-example-template">';
		$output .= '<div class="time <%= label %>" style="margin:' . $gap . ';">';
		$output .= '<span class="count curr top"><%= curr %></span>';
		$output .= '<span class="count next top"><%= next %></span>';
		$output .= '<span class="count next bottom"><%= next %></span>';
		$output .= '<span class="count curr bottom"><%= curr %></span>';
		$output .= '<span class="label"><%= label  %></span>';
		$output .= '</div>';
		$output .= '</script>';
	}


	return $output;
}
//@deprecated
add_shortcode( 'a13_countdown', 'a13fe_countdown_render' );
//@since 1.0.8
add_shortcode( 'a13fe_countdown', 'a13fe_countdown_render' );