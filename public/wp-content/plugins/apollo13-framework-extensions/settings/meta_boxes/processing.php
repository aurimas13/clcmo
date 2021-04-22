<?php

/**
 * Process special meta fields on post save
 *
 * @since  1.5.2
 *
 * @param string $processed_value - sanitized value
 * @param string $field_id - processed field ID
 *
 * @return string value to save
 */
function a13fe_save_post_meta( $processed_value, $field_id ){
	$input_prefix = A13FRAMEWORK_INPUT_PREFIX;

	//this meta setting contain JSON and unslash can break it. Also it can have HTML
	$unslash_exceptions = array(
		'images_n_videos',
	);

	//these meta settings can contain HTML
	$sanitization_exceptions = array(
		'testimonial',
		'subtitle'
	);

	if( in_array( $field_id, $unslash_exceptions ) ){
		//Theme Sniffer - please see $unslash_exceptions comment above
		$processed_value = wp_kses_post( $_POST[ $input_prefix . $field_id ] );
	}
	elseif( in_array( $field_id, $sanitization_exceptions ) ){
		$processed_value = wp_kses_post( wp_unslash( $_POST[ $input_prefix . $field_id ] ) );
	}

	return $processed_value;
}
add_filter( 'apollo13framework_save_post_meta', 'a13fe_save_post_meta', 10, 2);