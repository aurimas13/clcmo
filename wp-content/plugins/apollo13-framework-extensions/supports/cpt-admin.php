<?php
add_action( 'wp_ajax_apollo13framework_prepare_gallery_items_html', 'a13fe_prepare_gallery_items_html' );

/**
 * Prints HTML for new items selected from WordPress media uploader
 *
 * @since  1.5.2
 *
 */
function a13fe_prepare_gallery_items_html() {
	//returned value is array from attachment upload, so array_map( 'sanitize_text_field', wp_unslash( $_POST['items'] ) ) would break array
	$items = isset( $_POST['items'] )? wp_unslash( $_POST['items'] ) : array();
	apollo13framework_prepare_admin_gallery_html( $items );

	die(); // this is required to return a proper result
}