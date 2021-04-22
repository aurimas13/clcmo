<?php
//
add_action( 'save_post', 'a13fe_nava_after_page_save' );
/**
 * nava - add page slug to nava post
 *
 * @since  1.4.0
 */
function a13fe_nava_after_page_save( $post_id ) {

	// If this is just a revision - exit
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}
	// avoid generating nava in case of page import
	if ( isset( $_SESSION['import_is_runnig'] ) && $_SESSION['import_is_runnig'] == 1 ) {
		return;
	}
	$page = get_post( $post_id );

	$a13_nava_page_slug = $page->post_name;
	//prepare array of params with a13_one_page_mode = 1
	//search for vc_row shortcodes inside page
	preg_match_all( '/\[(\[?)(vc_row)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+)\[\/\2\])?)(\]?)/s', $page->post_content, $matches );
	// array of shortcode's params
	$param_sets = $matches[3];

	if ( empty( $param_sets ) ) {

		//return;
	}

	foreach ( $param_sets as $param_set ) {
		if ( stripos( $param_set, 'a13_one_page_mode="1"' ) === false ) {
			continue;
		}
		$found       = false;
		$a13_nava_id = '';
		//get shortcode's params
		$params = explode( '" ', $param_set );
		foreach ( $params as $param ) {
			$parts = explode( '=', $param );
			if ( $parts[0] == 'a13_nava_id' ) {
				$a13_nava_id = str_replace( '"', '', $parts[1] );
				$found       = true;
			}
		}

		if ( $found ) {
			update_post_meta( $a13_nava_id, 'a13_nava_page_slug', $a13_nava_page_slug );
		}
	}

	//search for navas with this page slug - means that those navas were assigned to this page
	//and remove orphans
}



add_action( 'wp_ajax_apollo13framework_nava_add_post', 'a13fe_nava_add_post' );
/**
 * add NAVA CPT post
 *
 * @since  1.4.0
 */
function a13fe_nava_add_post() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		exit;
	}
	$title = isset( $_REQUEST['title'] )? sanitize_title( wp_unslash( $_REQUEST['title'] ) ) : '';

	$new_nava    = array(
		'post_title'   => $title,
		'post_status'  => 'publish',
		'post_content' => '',
		'post_type'    => 'nava'
	);
	$new_post_ID = wp_insert_post( $new_nava );


	$response = array(
		'status'         => '200',
		'message'        => 'OK',
		'new_post_ID'    => $new_post_ID,
		'new_post_title' => $title
	);

	// normally, the script expects a json response
	header( 'Content-Type: application/json; charset=utf-8' );
	echo json_encode( $response );

	exit; // important

}



add_action( 'wp_ajax_apollo13framework_nava_delete_post', 'a13fe_nava_delete_post' );
/**
 * remove NAVA CPT post
 *
 * @since  1.4.0
 */
function a13fe_nava_delete_post() {
	if ( ! current_user_can( 'delete_posts' ) ) {
		exit;
	}

	if( isset( $_POST['id'] )){
		$post_id = (int) wp_unslash( $_POST['id'] );
		wp_delete_post( $post_id );
	}
	echo 'success';

	die();

}