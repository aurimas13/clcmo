<?php
function a13fe_get_extra_class( $el_class ) {
	$output = '';
	if ( $el_class != '' ) {
		$output = ' ' . str_replace( '.', '', $el_class );
	}

	return $output;
}


/**
 * Load plugin text domain.
 *
 * @since 1.3.0
 *
 * @return void
 */
function a13fe_load_plugin_textdomain() {
	load_plugin_textdomain( 'apollo13-framework-extensions' );
}

/**
 * Load Image resizing
 *
 * @since 1.4.0
 *
 * @return void
 */
function a13fe_load_image_resizing_library() {

	require_once A13FE_BASE_DIR . 'supports/image_resize/class-apollo13-image-resize.php';
}


