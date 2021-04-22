<?php
/**
 * Filter that changes default permalinks for posts and custom post types
 *
 * Thanks to this, function like get_permalink will get link to custom link if one is set in post/work/etc.
 *
 * @since  1.4.0
 *
 * @param string $url  The post URL
 * @param object $post The post object
 *
 * @return string URL
 * @internal param bool $leave_name Whether to keep the post name or page name
 *
 */
function a13fe_custom_permalink( $url, $post ) {
	/* in case there is old version of theme which had this feature inside, we don't add it */
	if ( function_exists( 'apollo13framework_custom_permalink' ) ){
		return $url;
	}

	$album_type = defined( 'A13FRAMEWORK_CUSTOM_POST_TYPE_ALBUM' ) ? A13FRAMEWORK_CUSTOM_POST_TYPE_ALBUM : 'album';
	$work_type = defined( 'A13FRAMEWORK_CUSTOM_POST_TYPE_WORK' ) ? A13FRAMEWORK_CUSTOM_POST_TYPE_WORK : 'work';

	$custom_link_types = array( 'post', $album_type, $work_type );
	if ( in_array( $post->post_type, $custom_link_types ) ) {
		$custom_url = get_post_meta( $post->ID, '_alt_link', true );
		//use custom link if available
		if ( strlen( $custom_url ) ) {
			return $custom_url;
		}

		return $url;
	}

	return $url;
}

add_filter( 'post_link', 'a13fe_custom_permalink', 10, 3 );
add_filter( 'post_type_link', 'a13fe_custom_permalink', 10, 3 );
