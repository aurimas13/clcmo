<?php
/**
 * Adding thumbnails to RSS feed
 *
 * @since  1.4.0
 *
 * @param $content
 *
 * @return string new content
 * @internal param string $content current content produced by WordPress
 */
function a13fe_rss_post_thumbnail($content) {
	/* in case there is old version of theme which had this feature inside, we don't add it */
	if ( function_exists( 'apollo13framework_rss_post_thumbnail' ) ){
		return $content;
	}

	global $post;
	if ( has_post_thumbnail( $post->ID ) ) {
		$content = '<p>' . get_the_post_thumbnail( $post->ID, 'medium' ) .
		           '</p>' . get_the_excerpt();
	} else {
		$content = get_the_excerpt();
	}

	return $content;
}
add_filter( 'the_excerpt_rss', 'a13fe_rss_post_thumbnail' );
add_filter( 'the_content_feed', 'a13fe_rss_post_thumbnail' );
