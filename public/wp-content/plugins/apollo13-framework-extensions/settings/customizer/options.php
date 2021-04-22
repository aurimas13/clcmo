<?php

/**
 * Adds customizer options for Custom CSS and Custom Sidebars
 *
 * @since  1.4.0
 */
function a13fe_add_theme_options() {
	global $apollo13framework_a13;
//CUSTOM CSS
	$apollo13framework_a13->set_sections( array(
		'title'         => esc_html__( 'Custom CSS', 'apollo13-framework-extensions' ),
		'desc'          => '',
		'id'            => 'section_custom_css',
		'icon'          => 'fa fa-css3',
		'priority'      => 25,
		'without_panel' => true,
		'fields'        => array(
			array(
				'id'    => 'custom_css',
				'type'  => 'code_editor',
				'title' => esc_html__( 'Custom CSS', 'apollo13-framework-extensions' ),
				'js'    => true
			)
		)
	) );

//ADD SIDEBARS
	$apollo13framework_a13->set_sections( array(
		'title'         => esc_html__( 'Add custom sidebars', 'apollo13-framework-extensions' ),
		'id'            => 'section_sidebars',
		'icon'          => 'fa fa-columns',
		'priority'      => 26,
		'without_panel' => true,
		'fields'        => array(
			array(
				'id'      => 'custom_sidebars',
				'type'    => 'custom_sidebars',
				'title'   => esc_html__( 'Add custom sidebars', 'apollo13-framework-extensions' ),
				'default' => array(),
			),
		)
	) );
}
add_action( 'apollo13framework_additional_theme_options', 'a13fe_add_theme_options' );



/**
 * Adds customizer options for Image Quality for plugin image resize & People post type
 *
 * @since  1.5.5
 */
function a13fe_add_theme_options_before_subsection_anchors() {
	global $apollo13framework_a13;

	$apollo13framework_a13->set_sections( array(
		'title'      => esc_html__( 'People Custom Post Type', 'apollo13-framework-extensions' ),
		'desc'       => '',
		'id'         => 'subsection_people',
		'icon'       => 'fa fa-users',
		'subsection' => true,
		'fields'     => array(
			array(
				'id'      => 'people_socials_color',
				'type'    => 'select',
				'title'   => esc_html__( 'Social icons', 'apollo13-framework-extensions' ). ' : ' .esc_html__( 'Color', 'apollo13-framework-extensions' ),
				'default' => 'semi-transparent',
				'options' => $apollo13framework_a13->get_settings_set( 'social_colors' ),
			),
			array(
				'id'      => 'people_socials_color_hover',
				'type'    => 'select',
				'title'   => esc_html__( 'Social icons', 'apollo13-framework-extensions' ). ' : ' .esc_html__( 'Color', 'apollo13-framework-extensions' ). ' - ' .esc_html__( 'on hover', 'apollo13-framework-extensions' ),
				'options' => $apollo13framework_a13->get_settings_set( 'social_colors' ),
				'default' => 'color',
			),
		)
	) );

	$apollo13framework_a13->set_sections( array(
		'title'      => esc_html__( 'Apollo13 Image Resize', 'apollo13-framework-extensions' ),
		'desc'       => '',
		'id'         => 'subsection_a13ir',
		'icon'       => 'fa fa-file-image-o',
		'subsection' => true,
		'fields'     => array(
			array(
				'id'          => 'a13ir_image_quality',
				'type'        => 'slider',
				'title'       => esc_html__( 'Image quality', 'apollo13-framework-extensions' ),
				'description' => esc_html__( 'Use it to change the quality of images used throughout the theme. 100 is the maximum quality.', 'apollo13-framework-extensions' ),
				'min'         => 1,
				'max'         => 100,
				'step'        => 1,
				'default'     => 90,
			),
		)
	) );
}
add_action( 'apollo13framework_options_before_subsection_anchors', 'a13fe_add_theme_options_before_subsection_anchors' );



/**
 * Adds customizer options changing work post type slug
 *
 * @since  1.5.5
 */
function a13fe_add_theme_options_after_subsection_single_work_slider() {
	global $apollo13framework_a13;

	$apollo13framework_a13->set_sections( array(
		'title'      => esc_html__( 'Single work slug', 'apollo13-framework-extensions' ),
		'desc'       => '',
		'id'         => 'subsection_work_slug',
		'icon'       => 'fa fa-pencil-square-o',
		'subsection' => true,
		'fields'     => array(
			array(
				'id'          => 'cpt_post_type_work',
				'type'        => 'text',
				'title'       => esc_html__( 'Single work slug', 'apollo13-framework-extensions' ),
				'description' => esc_html__( 'Do not change it if you really do not have to.', 'apollo13-framework-extensions' ),
				'default'     => 'work',
			),
		)
	) );
}
add_action( 'apollo13framework_options_after_subsection_single_work_slider', 'a13fe_add_theme_options_after_subsection_single_work_slider' );



/**
 * Adds customizer options changing album post type slug
 *
 * @since  1.5.5
 */
function a13fe_add_theme_options_after_subsection_album_socials() {
	global $apollo13framework_a13;

	$apollo13framework_a13->set_sections( array(
		'title'      => esc_html__( 'Single album slug', 'apollo13-framework-extensions' ),
		'desc'       => '',
		'id'         => 'subsection_album_slug',
		'icon'       => 'fa fa-pencil-square-o',
		'subsection' => true,
		'fields'     => array(
			array(
				'id'          => 'cpt_post_type_album',
				'type'        => 'text',
				'title'       => esc_html__( 'Single album slug', 'apollo13-framework-extensions' ),
				'description' => esc_html__( 'Do not change it if you really do not have to.', 'apollo13-framework-extensions' ),
				'default'     => 'album',
			),
		)
	) );
}
add_action( 'apollo13framework_options_after_subsection_album_socials', 'a13fe_add_theme_options_after_subsection_album_socials' );

