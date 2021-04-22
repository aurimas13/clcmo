<?php
function a13fe_vc_album_body_render() {
	return '{{ a13_album_body }}';
}
add_shortcode( 'vc_a13_album_body', 'a13fe_vc_album_body_render' );



function a13fe_vc_product_body_render() {
	return '{{ a13_product_body }}';
}
add_shortcode( 'vc_a13_product_body', 'a13fe_vc_product_body_render' );



function a13fe_vc_work_body_render() {
	return '{{ a13_work_body }}';
}
add_shortcode( 'vc_a13_work_body', 'a13fe_vc_work_body_render' );



function a13fe_vc_post_body_render() {
	return '{{ a13_post_body }}';
}
add_shortcode( 'vc_a13_post_body', 'a13fe_vc_post_body_render' );



function a13fe_vc_team_member_render( $atts ) {

	$output = $title_block = $content_block = '';

	extract( shortcode_atts( array(
		'title_block' => '',
		'content_block' => '',
	), $atts ) );

	$output .= '
<div class="team_grid_item_content_box" style="{{ a13_item_overlay_color }}{{ a13_item_overlay_font_color }}">';

	if ( $title_block == 1 ) {
		$output .= '
	<div class="team_grid_item_top_panel">';
		$output .= '
		<div class="grid_item_subtitle">{{ a13_item_meta_data:_subtitle }}</div>
		';
		$output .= '
		<div class="grid_item_title">{{ post_data:post_title }}</div>
		';
		$output .= '
	</div>
	';
	}
	if ( $content_block == 1 ) {
		$output .= '
	<div class="team_grid_item_bottom_panel">';
		$output .= '
		<div class="grid_item_content">{{ post_data:post_content }}</div>
		';
		$output .= '
		<div class="grid_item_socials">{{ a13_team_member_socials }}</div>
		';
		$output .= '
	</div>
	';
	}

	$output .= '
</div>';

	return $output;
}
add_shortcode( 'vc_a13_team_member', 'a13fe_vc_team_member_render' );



function a13fe_vc_testimonial_signature_render() {
	return '<div class="a13_testimonial_signature">{{ post_data:post_title }} - {{ a13_item_meta_data:_subtitle }}</div>';
}
add_shortcode( 'vc_a13_testimonial_signature', 'a13fe_vc_testimonial_signature_render' );


//function left in theme cause of theme function usage
add_shortcode( 'vc_a13_post_meta_1', 'vc_a13_post_meta_1_render' );