<?php
/**
 * redirect to chosen page if needed
 * @since  1.4.0
 */
function a13fe_waiting_page(){
	global $apollo13framework_a13, $post;
	$waiting_mode = $apollo13framework_a13->get_option( 'maintenance_mode' );
	$wait_page = (int)$apollo13framework_a13->get_option( 'maintenance_mode_page' );

	if ( $waiting_mode == 'on' && $wait_page > 0 && ( 'publish' == get_post_status ( $wait_page ) ) ) {
		//only for existing pages
		if ( $post->ID != $wait_page ) {
			wp_redirect( get_permalink( $wait_page ) );
			exit;
		}
		else{
			add_filter('apollo13framework_only_content','a13fe_only_content');
		}
	}
}

/*
 * filter callback to inform that header and theme footer will not be displayed
 * @since  1.4.0
 */
function a13fe_only_content(){
	return true;
}

