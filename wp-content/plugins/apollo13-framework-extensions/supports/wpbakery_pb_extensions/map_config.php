<?php


function a13fe_vc_config_map(){

	$nava_list = array();

	/* Nava extensions
	---------------------------------------------------------- */
	//try to find only unassigned nava post
	$args                               = array( 'numberposts' => - 1, "post_type" => defined( 'A13FRAMEWORK_CUSTOM_POST_TYPE_NAV_A' ) ? A13FRAMEWORK_CUSTOM_POST_TYPE_NAV_A : 'nava' );
	$posts                              = get_posts( $args );
	$nava_list[ esc_html__( 'Pick one', 'apollo13-framework-extensions' ) ] = - 1;

	foreach ( $posts as $post ) {
		$nava_page_slug = get_post_meta( $post->ID, 'a13_nava_page_slug', true );
		$nava_title     = $post->post_title;
		if ( $nava_page_slug != '' ) {
			$nava_page_title = get_the_title( get_page_by_path( $nava_page_slug ) );
			$nava_title .= ' (' . esc_html__( 'already used in page', 'apollo13-framework-extensions' ) . ': ' . $nava_page_title . ')';
		}
		$nava_list[ $nava_title ] = $post->ID;
	}

	vc_add_param( "vc_row", array(
		'type'        => 'dropdown',
		'value'       => array(
			__( 'As it is on the page', 'apollo13-framework-extensions' )     => 'none',
			__( 'Normal', 'apollo13-framework-extensions' ) => 'normal',
			__( 'Light', 'apollo13-framework-extensions' )  => 'light',
			__( 'Dark', 'apollo13-framework-extensions' )   => 'dark',
		),
		'param_name'  => 'a13_header_color_variant',
		'heading'     => esc_html__( 'Header color variant', 'apollo13-framework-extensions' ),
		'description' => esc_html__( 'Works only with the horizontal header.', 'apollo13-framework-extensions' ),
	) );

	vc_add_param( "vc_row", array(
		"type"        => "checkbox",
		"weight"      => 0,
		"heading"     => esc_html__( 'One Page Navigation', 'apollo13-framework-extensions' ),
		"param_name"  => "a13_one_page_mode",
		"value"       => Array( esc_html__( 'Yes, please', 'apollo13-framework-extensions' ) => true ),
		"description" => '',
		"group"       => esc_html__( 'One Page Navigation', 'apollo13-framework-extensions' ),
	) );

	vc_add_param( "vc_row", array(
		"type"        => "dropdown",
		"weight"      => 0,
		"group"       => esc_html__( 'One Page Navigation', 'apollo13-framework-extensions' ),
		"heading"     => esc_html__( 'This row is pointed by', 'apollo13-framework-extensions' ),
		"param_name"  => "a13_nava_id",
		"value"       => $nava_list,
		/* translators: %s: link "delete the selected pointer" */
		"description" => sprintf( esc_html__( 'Pick one pointer or %s (before deleting make sure it is not used anywhere else).', 'apollo13-framework-extensions' ), '<a href="#" class="a13_delete_selected_nava">' . esc_html__( 'delete the selected pointer', 'apollo13-framework-extensions' ) . '</a>' ),
		"dependency"  => array(
			"element"   => "a13_one_page_mode",
			"not_empty" => true
		)

	) );

	vc_add_param( "vc_row", array(
		"type"        => "textfield",
		"weight"      => 0,
		"heading"     => esc_html__( 'Here you can add another pointer for One Page Navigation', 'apollo13-framework-extensions' ),
		"param_name"  => "a13_new_nava_id",
		"value"       => '',
		"description" => esc_html__( 'Enter the name and press the Enter key.', 'apollo13-framework-extensions' ),
		"group"       => esc_html__( 'One Page Navigation', 'apollo13-framework-extensions' ),
		"dependency"  => array(
			"element"   => "a13_one_page_mode",
			"not_empty" => true
		)
	) );

	vc_add_params( "vc_row", array(
		array(
			"type"        => "textfield",
			"weight"      => 0,
			"heading"     => esc_html__( 'Navigation bullet title', 'apollo13-framework-extensions' ),
			"param_name"  => "a13_sticky_one_page_title",
			"value"       => '',
			"description" => '',
			"group"       => esc_html__( 'Sticky One Page', 'apollo13-framework-extensions' ),
		),
		array(
			"type"        => "checkbox",
			"heading"     => esc_html__( 'Change the bullet icon?', 'apollo13-framework-extensions' ),
			"param_name"  => "a13_sticky_one_page_mode",
			"value"       => Array( esc_html__( 'Yes, please', 'apollo13-framework-extensions' ) => true ),
			"description" => '',
			"group"       => esc_html__( 'Sticky One Page', 'apollo13-framework-extensions' ),
		),
		array(
			'type'        => 'dropdown',
			'heading'     => esc_html__( 'Library', 'apollo13-framework-extensions' ),
			'value'       => array(
				__( 'Pick one', 'apollo13-framework-extensions' ) => '0',
				'Font Awesome'                        => 'fontawesome',
				'Open Iconic'                         => 'openiconic',
				'Typicons'                            => 'typicons',
				'Entypo'                              => 'entypo',
				'Linecons'                            => 'linecons',
			),
			"group"       => esc_html__( 'Sticky One Page', 'apollo13-framework-extensions' ),
			'param_name'  => 'type',
			"dependency"  => array(
				"element"   => "a13_sticky_one_page_mode",
				"not_empty" => true
			)
		),
		array(
			'type'        => 'iconpicker',
			'heading'     => esc_html__( 'Icon', 'apollo13-framework-extensions' ),
			"group"       => esc_html__( 'Sticky One Page', 'apollo13-framework-extensions' ),
			'param_name'  => 'icon_fontawesome',
			'value'       => 'fa fa-adjust', // default value to backend editor admin_label
			'settings'    => array(
				'emptyIcon'    => false,
				// default true, display an "EMPTY" icon?
				'iconsPerPage' => 4000,
				// default 100, how many icons per/page to display, we use (big number) to display all icons in single page
			),
			'dependency'  => array(
				'element' => 'type',
				'value'   => 'fontawesome',
			),
		),
		array(
			'type'        => 'iconpicker',
			'heading'     => esc_html__( 'Icon', 'apollo13-framework-extensions' ),
			"group"       => esc_html__( 'Sticky One Page', 'apollo13-framework-extensions' ),
			'param_name'  => 'icon_openiconic',
			'value'       => 'vc-oi vc-oi-dial', // default value to backend editor admin_label
			'settings'    => array(
				'emptyIcon'    => false, // default true, display an "EMPTY" icon?
				'type'         => 'openiconic',
				'iconsPerPage' => 4000, // default 100, how many icons per/page to display
			),
			'dependency'  => array(
				'element' => 'type',
				'value'   => 'openiconic',
			),
		),
		array(
			'type'        => 'iconpicker',
			'heading'     => esc_html__( 'Icon', 'apollo13-framework-extensions' ),
			"group"       => esc_html__( 'Sticky One Page', 'apollo13-framework-extensions' ),
			'param_name'  => 'icon_typicons',
			'value'       => 'typcn typcn-adjust-brightness', // default value to backend editor admin_label
			'settings'    => array(
				'emptyIcon'    => false, // default true, display an "EMPTY" icon?
				'type'         => 'typicons',
				'iconsPerPage' => 4000, // default 100, how many icons per/page to display
			),
			'dependency'  => array(
				'element' => 'type',
				'value'   => 'typicons',
			),
		),
		array(
			'type'       => 'iconpicker',
			'heading'    => esc_html__( 'Icon', 'apollo13-framework-extensions' ),
			"group"      => esc_html__( 'Sticky One Page', 'apollo13-framework-extensions' ),
			'param_name' => 'icon_entypo',
			'value'      => 'entypo-icon entypo-icon-note', // default value to backend editor admin_label
			'settings'   => array(
				'emptyIcon'    => false, // default true, display an "EMPTY" icon?
				'type'         => 'entypo',
				'iconsPerPage' => 4000, // default 100, how many icons per/page to display
			),
			'dependency' => array(
				'element' => 'type',
				'value'   => 'entypo',
			),
		),
		array(
			'type'        => 'iconpicker',
			'heading'     => esc_html__( 'Icon', 'apollo13-framework-extensions' ),
			"group"       => esc_html__( 'Sticky One Page', 'apollo13-framework-extensions' ),
			'param_name'  => 'icon_linecons',
			'value'       => 'vc_li vc_li-heart', // default value to backend editor admin_label
			'settings'    => array(
				'emptyIcon'    => false, // default true, display an "EMPTY" icon?
				'type'         => 'linecons',
				'iconsPerPage' => 4000, // default 100, how many icons per/page to display
			),
			'dependency'  => array(
				'element' => 'type',
				'value'   => 'linecons',
			),
		),
		array(
			'type'        => 'colorpicker',
			"group"       => esc_html__( 'Sticky One Page', 'apollo13-framework-extensions' ),
			'heading'     => esc_html__( 'Color', 'apollo13-framework-extensions' ),
			'param_name'  => 'color',
			'value'       => '#444444',
			'description' => esc_html__( 'Select icon color.', 'apollo13-framework-extensions' ),
			"dependency"  => array(
				"element"   => "a13_sticky_one_page_mode",
				"not_empty" => true
			)
		),
	) );




	/* CountDown shortcode
	---------------------------------------------------------- */

	vc_map( array(
		"name"     => esc_html__( 'Countdown', 'apollo13-framework-extensions' ),
		"base"     => "a13_countdown",
		'icon'     => 'icon-wpb-layer-shape-text',
		"category" => esc_html__( 'Content', 'apollo13-framework-extensions' ),
		"params"   => array(
			array(
				"type"        => "dropdown",
				"heading"     => esc_html__( 'Style', 'apollo13-framework-extensions' ),
				"param_name"  => "style",
				"value"       => array(
					__( 'Pick one', 'apollo13-framework-extensions' ) => '0',
					__( 'Simple', 'apollo13-framework-extensions' )   => 'simple',
					__( 'Flipping', 'apollo13-framework-extensions' ) => 'flipping',
				),
				"description" => ''
			),
			array(
				"type"       => "colorpicker",
				"heading"    => esc_html__( 'Text color', 'apollo13-framework-extensions' ),
				"param_name" => "fcolor",
				"value"      => '',
				"dependency" => Array( 'element' => 'style', 'value' => 'simple' )
			),
			array(
				"type"       => "colorpicker",
				"heading"    => esc_html__( 'Background color', 'apollo13-framework-extensions' ),
				"param_name" => "bcolor",
				"value"      => '',
				"dependency" => Array( 'element' => 'style', 'value' => 'simple' )
			),
			array(
				"type"        => "textfield",
				"heading"     => esc_html__( 'Year', 'apollo13-framework-extensions' ),
				"param_name"  => "year",
				"value"       => '',
				"description" => esc_html__( 'Use four-digits format like 2016 or 2020.', 'apollo13-framework-extensions' ),
			),
			array(
				"type"        => "textfield",
				"heading"     => esc_html__( 'Month', 'apollo13-framework-extensions' ),
				"param_name"  => "month",
				"value"       => '',
				"description" => esc_html__( 'Use two-digits format like 12 or 05.', 'apollo13-framework-extensions' ),
			),
			array(
				"type"        => "textfield",
				"heading"     => esc_html__( 'Day', 'apollo13-framework-extensions' ),
				"param_name"  => "day",
				"value"       => '',
				"description" => esc_html__( 'Use two-digits format like 12 or 05.', 'apollo13-framework-extensions' ),
			),
			array(
				"type"        => "textfield",
				"heading"     => esc_html__( 'Hour', 'apollo13-framework-extensions' ),
				"param_name"  => "hour",
				"value"       => '',
				"description" => esc_html__( 'Use two-digits format like 12 or 05.', 'apollo13-framework-extensions' ),
			),
			array(
				"type"        => "textfield",
				"heading"     => esc_html__( 'Minute', 'apollo13-framework-extensions' ),
				"param_name"  => "minute",
				"value"       => '',
				"description" => esc_html__( 'Use two-digits format like 12 or 05.', 'apollo13-framework-extensions' ),
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_html__( 'Extra class name', 'apollo13-framework-extensions' ),
				'param_name'  => 'el_class',
			),
			array(
				'type'        => 'textfield',
				'heading'     => esc_html__( 'The gap between the elements of the counter', 'apollo13-framework-extensions' ),
				'param_name'  => 'gap',
				'description' => esc_html__( 'Set the value in pixels.', 'apollo13-framework-extensions' ),
			)
		),
	) );




	/* Counter shortcode
	---------------------------------------------------------- */
	vc_map( array(
		"name"     => esc_html__( 'Counter', 'apollo13-framework-extensions' ),
		"base"     => "a13_counter",
		"icon"     => "icon-a13_counter",
		"category" => esc_html__( 'Content', 'apollo13-framework-extensions' ),
		"params"   => array(
			array(
				"type"        => "textfield",
				"heading"     => esc_html__( 'The initial value of the counter', 'apollo13-framework-extensions' ),
				"param_name"  => "from",
				"value"       => '',
				"admin_label" => true,
			),
			array(
				"type"        => "textfield",
				"heading"     => esc_html__( 'The final value of the counter', 'apollo13-framework-extensions' ),
				"param_name"  => "to",
				"value"       => '',
				"admin_label" => true,
			),
			array(
				"type"        => "textfield",
				"heading"     => esc_html__( 'Font size', 'apollo13-framework-extensions' ),
				"param_name"  => "digits_font_size",
				"value"       => "",
				"description" => esc_html__( 'Set the value in pixels.', 'apollo13-framework-extensions' )
			),
			array(
				"type"       => 'checkbox',
				"heading"    => esc_html__( 'Bold text', 'apollo13-framework-extensions' ),
				"param_name" => "digits_bold",
				"value"      => Array( esc_html__( 'Yes, please', 'apollo13-framework-extensions' ) => true )
			),
			array(
				"type"        => "colorpicker",
				"heading"     => esc_html__( 'Text color', 'apollo13-framework-extensions' ),
				"param_name"  => "digits_color",
			),
			array(
				"type"        => "textfield",
				"heading"     => esc_html__( 'Duration', 'apollo13-framework-extensions' ),
				"param_name"  => "speed",
				"value"       => '3000',
				"description" => esc_html__( 'How long it should take to count to end value.', 'apollo13-framework-extensions' ).' '.esc_html__( 'Value in milliseconds.', 'apollo13-framework-extensions' ),
			),
			array(
				"type"        => "textfield",
				"heading"     => esc_html__( 'Refreshing time', 'apollo13-framework-extensions' ),
				"param_name"  => "refresh_interval",
				"value"       => '100',
				"description" => esc_html__( 'How much time should pass between the printing of each value during counting.', 'apollo13-framework-extensions' ).' '.esc_html__( 'Value in milliseconds.', 'apollo13-framework-extensions' ),
			),
			array(
				"type"        => "textfield",
				"heading"     => esc_html__( 'Finish text', 'apollo13-framework-extensions' ),
				"param_name"  => "finish_text",
				"value"       => '',
				"description" => esc_html__( 'This text will be displayed after the counting. Optional.', 'apollo13-framework-extensions' ),
				"admin_label" => true,
			),
			array(
				"type"        => "textfield",
				"heading"     => esc_html__( 'Font size', 'apollo13-framework-extensions' ),
				"param_name"  => "text_font_size",
				"value"       => "",
				"description" => esc_html__( 'Set the value in pixels.', 'apollo13-framework-extensions' )
			),
			array(
				"type"       => 'checkbox',
				"heading"    => esc_html__( 'Bold text', 'apollo13-framework-extensions' ),
				"param_name" => "text_bold",
				"value"      => Array( esc_html__( 'Yes, please', 'apollo13-framework-extensions' ) => true )
			),
			array(
				"type"        => "colorpicker",
				"heading"     => esc_html__( 'Text color', 'apollo13-framework-extensions' ),
				"param_name"  => "text_color",
			),
			array(
				"type"        => "dropdown",
				"heading"     => esc_html__( 'Text align', 'apollo13-framework-extensions' ),
				"param_name"  => "align",
				"value"       => array(
						__( 'Pick one', 'apollo13-framework-extensions' ) => '0',
						__( 'Left', 'apollo13-framework-extensions' )     => 'left',
						__( 'Center', 'apollo13-framework-extensions' )   => 'center',
						__( 'Right', 'apollo13-framework-extensions' )    => 'right',
				),
				"description" => ''
			),
			array(
				"type"       => 'checkbox',
				"heading"    => esc_html__( 'Uppercase', 'apollo13-framework-extensions' ),
				"param_name" => "uppercase",
				"value"      => Array( esc_html__( 'Yes, please', 'apollo13-framework-extensions' ) => true )
			),
			array(
				"type"        => "textfield",
				"heading"     => esc_html__( 'Extra class name', 'apollo13-framework-extensions' ),
				"param_name"  => "el_class",
			)
		),
	) );




	/* Image carousel
	---------------------------------------------------------- */
	vc_remove_param("vc_images_carousel", "mode");
	vc_remove_param("vc_images_carousel", "speed");
	vc_remove_param("vc_images_carousel", "slides_per_view");
	vc_remove_param("vc_images_carousel", "autoplay");
	vc_remove_param("vc_images_carousel", "hide_pagination_control");
	vc_remove_param("vc_images_carousel", "hide_prev_next_buttons");
	vc_remove_param("vc_images_carousel", "partial_view");
	vc_remove_param("vc_images_carousel", "wrap");
	vc_remove_param("vc_images_carousel", "el_class");


	vc_add_param("vc_images_carousel", array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Time between slides', 'apollo13-framework-extensions' ),
			'param_name' => 'interval',
			'value' => '2000',
			'description' => esc_html__( 'Value in milliseconds.', 'apollo13-framework-extensions' )
	));

	vc_add_param("vc_images_carousel", array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Time of slide', 'apollo13-framework-extensions' ),
			'param_name' => 'speed',
			'value' => '1000',
			'description' => esc_html__( 'Value in milliseconds.', 'apollo13-framework-extensions' )
	));

	vc_add_param("vc_images_carousel", array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Number of items to scroll at once', 'apollo13-framework-extensions' ),
			'param_name' => 'scroll',
			'value' => '1',
			'description' => ''
	));

	vc_add_param("vc_images_carousel", array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Number of items to show at once', 'apollo13-framework-extensions' ),
			'param_name' => 'slides_per_view',
			'value' => '3',
	));

	vc_add_param("vc_images_carousel", array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Autoplay', 'apollo13-framework-extensions' ),
			'param_name' => 'autoplay',
			'value' => array( esc_html__( 'Yes, please', 'apollo13-framework-extensions' ) => '1' )
	));

	vc_add_param("vc_images_carousel", array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Hide pagination controls', 'apollo13-framework-extensions' ),
			'param_name' => 'hide_pagination_control',
			'value' => array( esc_html__( 'Yes, please', 'apollo13-framework-extensions' ) => '1' )
	));

	vc_add_param("vc_images_carousel", array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Hide prev/next controls', 'apollo13-framework-extensions' ),
			'param_name' => 'hide_prev_next_buttons',
			'value' => array( esc_html__( 'Yes, please', 'apollo13-framework-extensions' ) => '1' )
	));

	vc_add_param("vc_images_carousel", array(
			'type' => 'checkbox',
			'heading' => esc_html__( 'Loop', 'apollo13-framework-extensions' ),
			'param_name' => 'wrap',
			'value' => array( esc_html__( 'Yes, please', 'apollo13-framework-extensions' ) => '1' )
	));

	vc_add_param("vc_images_carousel", array(
			'type' => 'textfield',
			'heading' => esc_html__( 'Extra class name', 'apollo13-framework-extensions' ),
			'param_name' => 'el_class',
	));





	/* Custom heading
	---------------------------------------------------------- */
	vc_remove_param( 'vc_custom_heading', 'el_class' );

	vc_add_params( 'vc_custom_heading', array(
		array(
			'type'        => 'textfield',
			'heading'     => esc_html__( 'Letter Spacing', 'apollo13-framework-extensions' ),
			'param_name'  => 'letter_spacing',
			'description' => esc_html__( 'Set the value in pixels.', 'apollo13-framework-extensions' )
		),
		array(
			'type'        => 'checkbox',
			'heading'     => esc_html__( 'Writing effect', 'apollo13-framework-extensions' ),
			'param_name'  => 'enable_typed',
			/* translators: %1$s: <code>{write}, %2$s: {/write}</code> */
			"description" => sprintf( esc_html__( 'When checked use this text format: %1$s[font color]|[background color]|[first sentence]|[second sentence]|[third sentence]%2$s', 'apollo13-framework-extensions' ), '<code>{write}', '{/write}</code>' ),
		),
		array(
				'type'        => 'checkbox',
				'heading'     => esc_html__( 'Loop', 'apollo13-framework-extensions' ),
				'param_name'  => 'loop_typed',
				'description' => '',
				"dependency"  => array(
						"element"   => "enable_typed",
						"not_empty" => true
				)
		),
		array(
				'type'        => 'checkbox',
				'heading'     => esc_html__( 'Enable responsive font size?', 'apollo13-framework-extensions' ),
				'param_name'  => 'enable_fit',
				'description' => esc_html__( 'It is useful to make the heading fit into a single line, regardless of the width of the screen.', 'apollo13-framework-extensions' ),
		),
		array(
				'type'        => 'textfield',
				'heading'     => esc_html__( 'Minimum font size', 'apollo13-framework-extensions' ),
				'param_name'  => 'fit_min_font_size',
				'description' => esc_html__( 'Set the value in pixels.', 'apollo13-framework-extensions' ),
				"dependency"  => array(
						"element"   => "enable_fit",
						"not_empty" => true
				)
		),
		array(
				'type'        => 'textfield',
				'heading'     => esc_html__( 'Maximum font size', 'apollo13-framework-extensions' ),
				'param_name'  => 'fit_max_font_size',
				'description' => esc_html__( 'Set the value in pixels.', 'apollo13-framework-extensions' ),
				"dependency"  => array(
						"element"   => "enable_fit",
						"not_empty" => true
				)
		),
		array(
				'type'        => 'textfield',
				'heading'     => esc_html__( 'Compression ratio', 'apollo13-framework-extensions' ),
				'param_name'  => 'fit_compress',
				'description' => esc_html__( 'Enter a value greater than 0, can be a fraction. The higher the compression, the longer the header will fit on one line.', 'apollo13-framework-extensions' ),
				"dependency"  => array(
						"element"   => "enable_fit",
						"not_empty" => true
				)
		),
		array(
				'type'        => 'textfield',
				'heading'     => esc_html__( 'Extra class name', 'apollo13-framework-extensions' ),
				'param_name'  => 'el_class',
		)
	) );
}
add_action( 'vc_before_init', 'a13fe_vc_config_map' );




/* add a13 style to tabs shortcode
---------------------------------------------------------- */
function a13fe_vc_modify_shortcodes_params() {
	$vc_tta_tabs_style_param          = WPBMap::getParam( 'vc_tta_tabs', 'style' );
	$vc_tta_tabs_style_param['value'][__( 'Theme style', 'apollo13-framework-extensions' )] = 'a13_framework_tabs';
	vc_update_shortcode_param( 'vc_tta_tabs', $vc_tta_tabs_style_param );
}

add_action( 'vc_after_init', 'a13fe_vc_modify_shortcodes_params' );