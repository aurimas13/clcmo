<?php
function a13fe_vc_gitem_template_attribute_a13_item_overlay_color( $value, $data ) {
	global $post;

	extract( array_merge( array(
		'post' => null,
	), $data ) );
	$color = get_post_meta( $post->ID, '_overlay_bg_color', true );
	if ( $color == '' ) {
		$color = 'background-color: rgba(0,0,0,0.5);';
	} else {
		$color = 'background-color: ' . $color . ';';
	}

	return $color;
}
add_filter( 'vc_gitem_template_attribute_a13_item_overlay_color', 'a13fe_vc_gitem_template_attribute_a13_item_overlay_color', 10, 2 );



function a13fe_vc_gitem_template_attribute_a13_item_overlay_font_color( $value, $data ) {
	global $post;

	extract( array_merge( array(
		'post' => null,
	), $data ) );
	$color = get_post_meta( $post->ID, '_overlay_font_color', true );
	if ( $color == '' ) {
		$color = 'color: rgba(1,1,1,0.8);';
	} else {
		$color = 'color: ' . $color . ';';
	}

	return $color;
}
add_filter( 'vc_gitem_template_attribute_a13_item_overlay_font_color', 'a13fe_vc_gitem_template_attribute_a13_item_overlay_font_color', 10, 2 );



function a13fe_vc_gitem_template_attribute_a13_item_meta_data( $value, $data ) {
	global $post;

	extract( array_merge( array(
		'post' => null,
	), $data ) );
	$meta_val = get_post_meta( $post->ID, $data, true );

	return $meta_val;
}
add_filter( 'vc_gitem_template_attribute_a13_item_meta_data', 'a13fe_vc_gitem_template_attribute_a13_item_meta_data', 10, 2 );



function a13fe_vc_gitem_template_attribute_a13_post_categories( $value, $data ) {
	global $post;

	extract( array_merge( array(
		'post' => null,
	), $data ) );
	$cats = get_the_category_list( ' & ', '', $post->ID );

	return $cats;
}
add_filter( 'vc_gitem_template_attribute_a13_post_categories', 'a13fe_vc_gitem_template_attribute_a13_post_categories', 10, 2 );



/*
 * When do_shortcode( '[product id="67"]' is called by VC by AJAX, is_admin() returns true
 * and that don't add class "product" to product(post-template.php, function get_post_class).
 * So we add and remove filter for printing product shortcode
 * */
function a13fe_vc_fix_post_grid_product($classes){
	$classes[] = 'product';
	return $classes;
}

function a13fe_vc_gitem_template_attribute_a13_product_body( $value, $data ) {
	global $post;
	extract( array_merge( array(
		'post' => null,
	), $data ) );
	add_filter( 'post_class', 'a13fe_vc_fix_post_grid_product', 20, 1 );
	$out = do_shortcode( '[product id="' . $post->ID . '"]' );
	remove_filter( 'post_class', 'a13fe_vc_fix_post_grid_product', 20 );

	return $out;
}
add_filter( 'vc_gitem_template_attribute_a13_product_body', 'a13fe_vc_gitem_template_attribute_a13_product_body', 10, 2 );



function a13fe_add_grid_shortcodes( $shortcodes ) {
	$shortcodes['vc_a13_team_member'] = array(
		'name'        => __( 'Team member', 'apollo13-framework-extensions' ),
		'base'        => 'vc_a13_team_member',
		'category'    => __( 'Content', 'apollo13-framework-extensions' ),
		'description' => '',
		'post_type'   => Vc_Grid_Item_Editor::postType(),
		'params'      => array(
			array(
				"type"       => 'checkbox',
				"heading"    => __( 'Title', 'apollo13-framework-extensions' ),
				"param_name" => "title_block",
				"value"      => Array( __( 'Yes, please', 'apollo13-framework-extensions' ) => true )
			),
			array(
				"type"       => 'checkbox',
				"heading"    => __( 'Content', 'apollo13-framework-extensions' ),
				"param_name" => "content_block",
				"value"      => Array( __( 'Yes, please', 'apollo13-framework-extensions' ) => true )
			),
		)
	);

	$shortcodes['vc_a13_testimonial_signature'] = array(
		'name'        => __( 'Testimonial signature', 'apollo13-framework-extensions' ),
		'base'        => 'vc_a13_testimonial_signature',
		'category'    => __( 'Content', 'apollo13-framework-extensions' ),
		'description' => '',
		'post_type'   => Vc_Grid_Item_Editor::postType(),
	);

	$shortcodes['vc_a13_post_meta_1'] = array(
		'name'        => __( 'Post information bar', 'apollo13-framework-extensions' ),
		'base'        => 'vc_a13_post_meta_1',
		'category'    => __( 'Content', 'apollo13-framework-extensions' ),
		'description' => '',
		'post_type'   => Vc_Grid_Item_Editor::postType(),
	);

	$shortcodes['vc_a13_work_body'] = array(
		'name'        => __( 'Work body', 'apollo13-framework-extensions' ),
		'base'        => 'vc_a13_work_body',
		'category'    => __( 'Content', 'apollo13-framework-extensions' ),
		'description' => '',
		'post_type'   => Vc_Grid_Item_Editor::postType(),
	);

	$shortcodes['vc_a13_product_body'] = array(
		'name'        => __( 'Product body', 'apollo13-framework-extensions' ),
		'base'        => 'vc_a13_product_body',
		'category'    => __( 'Content', 'apollo13-framework-extensions' ),
		'description' => '',
		'post_type'   => Vc_Grid_Item_Editor::postType(),
	);

	$shortcodes['vc_a13_album_body'] = array(
		'name'        => __( 'Album body', 'apollo13-framework-extensions' ),
		'base'        => 'vc_a13_album_body',
		'category'    => __( 'Content', 'apollo13-framework-extensions' ),
		'description' => '',
		'post_type'   => Vc_Grid_Item_Editor::postType(),
	);
	$shortcodes['vc_a13_post_body']  = array(
		'name'        => __( 'Post body', 'apollo13-framework-extensions' ),
		'base'        => 'vc_a13_post_body',
		'category'    => __( 'Content', 'apollo13-framework-extensions' ),
		'description' => '',
		'post_type'   => Vc_Grid_Item_Editor::postType(),
	);

	return $shortcodes;
}
add_filter( 'vc_grid_item_shortcodes', 'a13fe_add_grid_shortcodes' );



//filters left in theme cause of theme functions usage
add_filter( 'vc_gitem_template_attribute_a13_team_member_socials', 'vc_gitem_template_attribute_a13_team_member_socials', 10, 2 );
add_filter( 'vc_gitem_template_attribute_a13_work_body', 'vc_gitem_template_attribute_a13_work_body', 10, 2 );
add_filter( 'vc_gitem_template_attribute_a13_album_body', 'vc_gitem_template_attribute_a13_album_body', 10, 2 );
add_filter( 'vc_gitem_template_attribute_a13_post_body', 'vc_gitem_template_attribute_a13_post_body', 10, 2 );