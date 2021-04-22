<?php
add_filter( 'apollo13framework_meta_boxes_post', 'a13fe_custom_meta_boxes_post' );
/**
 * Adds meta fields creating content for posts
 *
 * @since  1.4.0
 *
 * @param array $meta_fields - array of current meta fields
 *
 * @return array updated meta fields
 */
function a13fe_custom_meta_boxes_post($meta_fields){
	//add field in posts_list tab
	array_splice( $meta_fields['posts_list'], 1, 0, array(
			array(
				'name'        => __( 'Alternative link', 'apollo13-framework-extensions' ),
				'description' => __( 'If the field is filled, opening this entry will take you to the given link.', 'apollo13-framework-extensions' ),
				'id'          => 'alt_link',
				'default'     => '',
				'type'        => 'text',
			)
		)
	);

	if ( defined('A13FRAMEWORK_THEME_VERSION') && version_compare( A13FRAMEWORK_THEME_VERSION, '2.2.1', '>=' ) ) {
		//add video option featured media
		$meta_fields['featured_media'][1]['options']['post_video'] = __( 'Video', 'apollo13-framework-extensions' );

		$meta_fields['featured_media'][] = array(
			'name'              => __( 'Link to the video', 'apollo13-framework-extensions' ),
			'description'       => __( 'Insert a video link here or upload it.', 'apollo13-framework-extensions' ) .' '. __( 'You can add videos from YouTube or Vimeo by pasting the link to the video here.', 'apollo13-framework-extensions' ),
			'id'                => 'post_video',
			'default'           => '',
			'type'              => 'upload',
			'button_text'       => __( 'Upload a media file', 'apollo13-framework-extensions' ),
			'media_button_text' => __( 'Insert a media file', 'apollo13-framework-extensions' ),
			'media_type'        => 'video', /* 'audio,video' */
			'required'          => array( 'image_or_video', '=', 'post_video' ),
		);


		/*
		 *
		 * Tab: Page background
		 *
		 */
		$meta_fields['background'] = array(
			array(
				'name' => __('Page background', 'apollo13-framework-extensions'),
				'type' => 'fieldset',
				'tab'   => true,
				'icon'  => 'fa fa-picture-o'
			),
			array(
				'name'        => __( 'Page background', 'apollo13-framework-extensions' ),
				'description' => __( 'You can use global settings or override them here', 'apollo13-framework-extensions' ),
				'id'          => 'page_bg_settings',
				'default'     => 'global',
				'type'        => 'radio',
				'options'     => array(
					'global' => __( 'Global settings', 'apollo13-framework-extensions' ),
					'custom' => __( 'Use custom settings', 'apollo13-framework-extensions' ),
				),
			),
			array(
				'name'        => __( 'Background image', 'apollo13-framework-extensions' ),
				'id'          => 'page_image',
				'default'     => '',
				'button_text' => __( 'Upload Image', 'apollo13-framework-extensions' ),
				'type'        => 'upload',
				'required'    => array( 'page_bg_settings', '=', 'custom' ),
			),
			array(
				'name'     => __( 'How to fit the background image', 'apollo13-framework-extensions' ),
				'id'       => 'page_image_fit',
				'default'  => 'cover',
				'options'  => array(
					'cover'    => __( 'Cover', 'apollo13-framework-extensions' ),
					'contain'  => __( 'Contain', 'apollo13-framework-extensions' ),
					'fitV'     => __( 'Fit Vertically', 'apollo13-framework-extensions' ),
					'fitH'     => __( 'Fit Horizontally', 'apollo13-framework-extensions' ),
					'center'   => __( 'Just center', 'apollo13-framework-extensions' ),
					'repeat'   => __( 'Repeat', 'apollo13-framework-extensions' ),
					'repeat-x' => __( 'Repeat X', 'apollo13-framework-extensions' ),
					'repeat-y' => __( 'Repeat Y', 'apollo13-framework-extensions' ),
				),
				'type'     => 'select',
				'required' => array( 'page_bg_settings', '=', 'custom' ),
			),
			array(
				'name'     => __( 'Page Background color', 'apollo13-framework-extensions' ),
				'id'       => 'page_bg_color',
				'default'  => '',
				'type'     => 'color',
				'required' => array( 'page_bg_settings', '=', 'custom' ),
			),
		);
	}


	return $meta_fields;
}



add_filter( 'apollo13framework_meta_boxes_page', 'a13fe_custom_meta_boxes_page' );
/**
 * Adds meta fields creating content for pages
 *
 * @since  1.4.0
 *
 * @param array $meta_fields - array of current meta fields
 *
 * @return array updated meta fields
 */
function a13fe_custom_meta_boxes_page($meta_fields){
	//add field in title_bar tab
	array_splice( $meta_fields['title_bar'], 1, 0, array(
			array(
				'name'    => __( 'Subtitle', 'apollo13-framework-extensions' ),
				'id'      => 'subtitle',
				'default' => '',
				'type'    => 'text'
			)
		)
	);

	if ( defined('A13FRAMEWORK_THEME_VERSION') && version_compare( A13FRAMEWORK_THEME_VERSION, '2.2.1', '>=' ) ) {

		//add video option featured media
		$meta_fields['featured_media'][1]['options']['post_video'] = __( 'Video', 'apollo13-framework-extensions' );

		$meta_fields['featured_media'][] = array(
			'name'              => __( 'Link to the video', 'apollo13-framework-extensions' ),
			'description'       => __( 'Insert a video link here or upload it.', 'apollo13-framework-extensions' ) .' '. __( 'You can add videos from YouTube or Vimeo by pasting the link to the video here.', 'apollo13-framework-extensions' ),
			'id'                => 'post_video',
			'default'           => '',
			'type'              => 'upload',
			'button_text'       => __( 'Upload a media file', 'apollo13-framework-extensions' ),
			'media_button_text' => __( 'Insert a media file', 'apollo13-framework-extensions' ),
			'media_type'        => 'video', /* 'audio,video' */
			'required'          => array( 'image_or_video', '=', 'post_video' ),
		);


		/*
		 *
		 * Tab: Page background
		 *
		 */
		$meta_fields['background'] = array(
			array(
				'name' => __('Page background', 'apollo13-framework-extensions'),
				'type' => 'fieldset',
				'tab'   => true,
				'icon'  => 'fa fa-picture-o'
			),
			array(
				'name'        => __( 'Page background', 'apollo13-framework-extensions' ),
				'description' => __( 'You can use global settings or override them here', 'apollo13-framework-extensions' ),
				'id'          => 'page_bg_settings',
				'default'     => 'global',
				'type'        => 'radio',
				'options'     => array(
					'global' => __( 'Global settings', 'apollo13-framework-extensions' ),
					'custom' => __( 'Use custom settings', 'apollo13-framework-extensions' ),
				),
			),
			array(
				'name'        => __( 'Background image', 'apollo13-framework-extensions' ),
				'id'          => 'page_image',
				'default'     => '',
				'button_text' => __( 'Upload Image', 'apollo13-framework-extensions' ),
				'type'        => 'upload',
				'required'    => array( 'page_bg_settings', '=', 'custom' ),
			),
			array(
				'name'     => __( 'How to fit the background image', 'apollo13-framework-extensions' ),
				'id'       => 'page_image_fit',
				'default'  => 'cover',
				'options'  => array(
					'cover'    => __( 'Cover', 'apollo13-framework-extensions' ),
					'contain'  => __( 'Contain', 'apollo13-framework-extensions' ),
					'fitV'     => __( 'Fit Vertically', 'apollo13-framework-extensions' ),
					'fitH'     => __( 'Fit Horizontally', 'apollo13-framework-extensions' ),
					'center'   => __( 'Just center', 'apollo13-framework-extensions' ),
					'repeat'   => __( 'Repeat', 'apollo13-framework-extensions' ),
					'repeat-x' => __( 'Repeat X', 'apollo13-framework-extensions' ),
					'repeat-y' => __( 'Repeat Y', 'apollo13-framework-extensions' ),
				),
				'type'     => 'select',
				'required' => array( 'page_bg_settings', '=', 'custom' ),
			),
			array(
				'name'     => __( 'Page Background color', 'apollo13-framework-extensions' ),
				'id'       => 'page_bg_color',
				'default'  => '',
				'type'     => 'color',
				'required' => array( 'page_bg_settings', '=', 'custom' ),
			),
		);
	}


	return $meta_fields;
}



add_filter( 'apollo13framework_meta_boxes_album', 'a13fe_custom_meta_boxes_album' );
/**
 * Adds meta fields creating content for albums
 *
 * @since  1.4.0
 *
 * @param array $meta_fields - array of current meta fields
 *
 * @return array updated meta fields
 */
function a13fe_custom_meta_boxes_album($meta_fields){
	/*
	 *
	 * Tab: Album info
	 *
	 */
	$meta_fields['album_info'] = array(
		array(
			'name' => __('Album info', 'apollo13-framework-extensions'),
			'type' => 'fieldset',
			'tab'   => true,
			'icon'  => 'fa fa-info-circle'
		),
		array(
			'name'        => __( 'Website', 'apollo13-framework-extensions' ),
			'description' => __( 'If left empty then it will not be displayed.', 'apollo13-framework-extensions' ),
			'id'          => 'www',
			'default'     => '',
			'placeholder' => 'http://link-to-somewhere.com',
			'type'        => 'text'
		),
	);

	/**
	 * Increase number of custom fields in albums & works
	 *
	 *
	add_filter('apollo13framework_custom_fields_number', function(){
		return 13;//change this number to any value you need
	});
	 *
	 *
	 */
	$custom_fields_number = apply_filters('apollo13framework_custom_fields_number', 5);

	for($i=1; $i <= $custom_fields_number; $i++){
		array_push($meta_fields['album_info'],
			array(
				/* translators: %d - index of field  */
				'name'        => sprintf( __( 'Custom info %d', 'apollo13-framework-extensions' ), $i ),
				'description' => __( 'If left empty then it will not be displayed.', 'apollo13-framework-extensions' ) .' '. __( 'Use the pattern <strong>Field name: Field value</strong>.', 'apollo13-framework-extensions' ),
				'id'          => 'custom_'.$i,
				'default'     => '',
				'placeholder' => 'Label: value',
				'type'        => 'text'
			)
		);
	}

	//add field in albums_list tab
	array_splice( $meta_fields['albums_list'], 1, 0, array(
			array(
				'name'        => __( 'Alternative link', 'apollo13-framework-extensions' ),
				'description' => __( 'If the field is filled, opening this entry will take you to the given link.', 'apollo13-framework-extensions' ),
				'id'          => 'alt_link',
				'default'     => '',
				'type'        => 'text',
			),
			array(
				'name'    => __( 'Subtitle', 'apollo13-framework-extensions' ),
				'id'      => 'subtitle',
				'default' => '',
				'type'    => 'text'
			)
		)
	);

	return $meta_fields;
}



add_filter( 'apollo13framework_meta_boxes_work', 'a13fe_custom_meta_boxes_work' );
/**
 * Adds meta fields creating content for works
 *
 * @since  1.4.0
 *
 * @param array $meta_fields - array of current meta fields
 *
 * @return array updated meta fields
 */
function a13fe_custom_meta_boxes_work($meta_fields){
	/*
	 *
	 * Tab: Work info
	 *
	 */
	$meta_fields['work_info'] = array(
		array(
			'name' => __('Work info', 'apollo13-framework-extensions'),
			'type' => 'fieldset',
			'tab'   => true,
			'icon'  => 'fa fa-info-circle'
		),
		array(
			'name'        => __( 'Website', 'apollo13-framework-extensions' ),
			'description' => __( 'If left empty then it will not be displayed.', 'apollo13-framework-extensions' ),
			'id'          => 'www',
			'default'     => '',
			'placeholder' => 'http://link-to-somewhere.com',
			'type'        => 'text'
		),
	);

	$custom_fields_number = apply_filters('apollo13framework_custom_fields_number', 5);

	for($i=1; $i <= $custom_fields_number; $i++){
		array_push($meta_fields['work_info'],
			array(
				/* translators: %d - index of field  */
				'name'        => sprintf( __( 'Custom info %d', 'apollo13-framework-extensions' ), $i ),
				'description' => __( 'If left empty then it will not be displayed.', 'apollo13-framework-extensions' ) .' '. __( 'Use the pattern <strong>Field name: Field value</strong>.', 'apollo13-framework-extensions' ),
				'id'          => 'custom_'.$i,
				'default'     => '',
				'placeholder' => 'Label: value',
				'type'        => 'text'
			)
		);
	}




	//add field in works_list tab
	array_splice( $meta_fields['works_list'], 1, 0, array(
			array(
				'name'        => __( 'Alternative link', 'apollo13-framework-extensions' ),
				'description' => __( 'If the field is filled, opening this entry will take you to the given link.', 'apollo13-framework-extensions' ),
				'id'          => 'alt_link',
				'default'     => '',
				'type'        => 'text',
			),
			array(
				'name'    => __( 'Subtitle', 'apollo13-framework-extensions' ),
				'id'      => 'subtitle',
				'default' => '',
				'type'    => 'text'
			)
		)
	);

	return $meta_fields;
}

add_filter( 'apollo13framework_meta_boxes_images_manager', 'a13fe_custom_meta_images_manager' );
/**
 * Adds meta fields creating content for images manager
 *
 * @since  1.4.0
 *
 * @return array updated meta fields
 */
function a13fe_custom_meta_images_manager(){
	$meta =
		array(
			'images_manager' => array(
				array(
					'name' => '',
					'type' => 'fieldset'
				),
				array(
					'name'       => __( 'Multi upload', 'apollo13-framework-extensions' ),
					'id'         => 'images_n_videos',
					'type'       => 'multi-upload',
					'default'    => '[]', //empty JSON
					'media_type' => 'image,video', /* 'audio,video' */
				),
				array(
					'name'         => '',
					'type'         => 'fieldset',
					'is_prototype' => true,
					'id'           => 'mu-prototype-image',
				),
				array(
					'name'        => __( 'Tags', 'apollo13-framework-extensions' ),
					'description' => __( 'Separate tags with commas', 'apollo13-framework-extensions' ),
					'id'          => 'image_tags',
					'default'     => '',
					'type'        => 'tag_media',
				),
				array(
					'name'        => __( 'Alternative link', 'apollo13-framework-extensions' ),
					'id'          => 'image_link',
					'default'     => '',
					'type'        => 'text',
				),
				array(
					'name'        => __( 'Open link in a new window/tab', 'apollo13-framework-extensions' ),
					/* translators: %1$s: <code>target="_blank"</code> */
					'description' => sprintf( esc_html__( 'It will add %1$s to link', 'apollo13-framework-extensions' ), '<code>target="_blank"</code>' ),
					'id'          => 'image_link_target',
					'default'     => '0',
					'options'     => array(
						'1' => __( 'On', 'apollo13-framework-extensions' ),
						'0' => __( 'Off', 'apollo13-framework-extensions' ),
					),
					'type'        => 'radio',
				),
				array(
					'name'    => esc_html__( 'Background color', 'apollo13-framework-extensions' ),
					'id'      => 'image_bg_color',
					'default' => '',
					'type'    => 'color'
				),
				array(
					'name'        => __( 'Size of brick', 'apollo13-framework-extensions' ),
					'description' => __( 'What should be the width of this brick in the Bricks layout?', 'apollo13-framework-extensions' ),
					'id'          => 'image_ratio_x',
					'default'     => 1,
					'unit'        => '',
					'min'         => 1,
					'max'         => 6,
					'type'        => 'slider'
				),
				array(
					'name'         => '',
					'type'         => 'fieldset',
					'is_prototype' => true,
					'id'           => 'mu-prototype-video',
				),
				array(
					'name'        => __( 'Tags', 'apollo13-framework-extensions' ),
					'description' => __( 'Separate tags with commas', 'apollo13-framework-extensions' ),
					'id'          => 'video_tags',
					'default'     => '',
					'type'        => 'tag_media',
				),
				array(
					'name'        => __( 'Autoplay video', 'apollo13-framework-extensions' ),
					'description' => __( 'Works only in the Slider layout.', 'apollo13-framework-extensions' ),
					'id'          => 'video_autoplay',
					'default'     => '0',
					'options'     => array(
						'1' => __( 'On', 'apollo13-framework-extensions' ),
						'0' => __( 'Off', 'apollo13-framework-extensions' ),
					),
					'type'        => 'radio',
				),
				array(
					'name'        => __( 'Size of brick', 'apollo13-framework-extensions' ),
					'description' => __( 'What should be the width of this brick in the Bricks layout?', 'apollo13-framework-extensions' ),
					'id'          => 'video_ratio_x',
					'default'     => 1,
					'unit'        => '',
					'min'         => 1,
					'max'         => 6,
					'type'        => 'slider'
				),
				array(
					'name'         => '',
					'type'         => 'fieldset',
					'is_prototype' => true,
					'id'           => 'mu-prototype-videolink',
				),
				array(
					'name'        => __( 'Link to the video', 'apollo13-framework-extensions' ),
					'description' => __( 'You can add videos from YouTube or Vimeo by pasting the link to the video here.', 'apollo13-framework-extensions' ),
					'id'          => 'videolink_link',
					'default'     => '',
					'type'        => 'text',
				),
				array(
					'name'             => __( 'Video thumbnail', 'apollo13-framework-extensions' ),
					'description'      => __( 'In some cases it is displayed instead of the video placeholder.', 'apollo13-framework-extensions' ),
					'id'               => 'videolink_poster',
					'default'          => '',
					'button_text'      => __( 'Upload Image', 'apollo13-framework-extensions' ),
					'attachment_field' => 'videolink_attachment_id',
					'type'             => 'upload'
				),
				array(
					'name'    => 'ID',
					'id'      => 'videolink_attachment_id',
					'default' => '',
					'type'    => 'hidden'
				),
				array(
					'name'        => __( 'Tags', 'apollo13-framework-extensions' ),
					'description' => __( 'Separate tags with commas', 'apollo13-framework-extensions' ),
					'id'          => 'videolink_tags',
					'default'     => '',
					'type'        => 'tag_media',
				),
				array(
					'name'        => __( 'Autoplay video', 'apollo13-framework-extensions' ),
					'description' => __( 'Works only in the Slider layout.', 'apollo13-framework-extensions' ),
					'id'          => 'videolink_autoplay',
					'default'     => '0',
					'options'     => array(
						'1' => __( 'On', 'apollo13-framework-extensions' ),
						'0' => __( 'Off', 'apollo13-framework-extensions' ),
					),
					'type'        => 'radio',
				),
				array(
					'name'        => __( 'Size of brick', 'apollo13-framework-extensions' ),
					'description' => __( 'What should be the width of this brick in the Bricks layout?', 'apollo13-framework-extensions' ),
					'id'          => 'videolink_ratio_x',
					'default'     => 1,
					'unit'        => '',
					'min'         => 1,
					'max'         => 6,
					'type'        => 'slider'
				),
				array(
					'name'    => __( 'Title', 'apollo13-framework-extensions' ),
					'id'      => 'videolink_title',
					'default' => '',
					'type'    => 'text'
				),
				array(
					'name'    => __( 'Description', 'apollo13-framework-extensions' ),
					'id'      => 'videolink_desc',
					'default' => '',
					'type'    => 'textarea',
				),
			)
		);

	return $meta;
}


