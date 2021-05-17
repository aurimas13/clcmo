<?php
/* Sending mail in photo proofing feature */

/** change content type for desired e-mail
 * @since 1.0.8
 */
function a13fe_album_mail_content_type() {
	return 'text/html';
}



/**
 * @param $to
 * @param $subject
 * @param $message
 *
 * @since 1.0.8
 */
function a13fe_album_send_admin_mail($to, $subject, $message) {
	add_filter( 'wp_mail_content_type', 'a13fe_album_mail_content_type' );
	wp_mail( $to, $subject, $message );
	remove_filter( 'wp_mail_content_type', 'a13fe_album_mail_content_type' );
}




/** function to send mail about new proofed album
 * @since 1.0.8
 *
 * @param $album_id
 */
function a13fe_album_proofing_admin_mail($album_id) {
	global $apollo13framework_a13;

	//no e-mail if disabled
	if( $apollo13framework_a13->get_option('proofing_send_email' ) === 'off' ){
		return;
	}

	$album_title = get_the_title($album_id);
	$album_link = get_permalink($album_id);
	$approved_array = a13fe_album_proofing_return_approved_files($album_id);

	$to = $apollo13framework_a13->get_option( 'proofing_email' );
	if( empty( $to ) || ! is_email( $to ) ){
		$to = get_option('admin_email');
	}
	/* translators: %s: Album name */
	$subject = sprintf( __('The album %s is marked as completed in the photo proofing process', 'apollo13-framework-extensions'), $album_title );
	/** @noinspection HtmlUnknownTarget */
	$message = sprintf( __('Your client has marked the <a href="%1$s">%2$s</a> album as completed in the photo proofing process. Visit it to see what has been selected.', 'apollo13-framework-extensions'), esc_url($album_link), esc_html($album_title) );

	$message .= "\r\n<br />\r\n<br />".__('List of selected files:', 'apollo13-framework-extensions');
	$message .= "\r\n<br />\r\n<br /><strong>".__('Adobe Lightroom', 'apollo13-framework-extensions')."</strong>";

	$message .= "\r\n<br /><code>".implode(', ', $approved_array).'</code>';

	$message .= "\r\n<br />\r\n<br /><strong>".__('Windows Explorer or Mac Finder', 'apollo13-framework-extensions')."</strong>";
	$message .= "\r\n<br /><code>\"".implode('" OR "', $approved_array).'"</code>';

	//try to send e-mail
	a13fe_album_send_admin_mail( $to, $subject, $message );
}

/** function returns selected files by a client
 * @since 1.8.3
 *
 * @param $album_id
 *
 * @return array of files names
 */
function a13fe_album_proofing_return_approved_files($album_id){
	//filter only approved items
	$proofing_meta       = get_post_meta( $album_id, '_images_n_videos_proofing', true );
	$proofing_array      = strlen( $proofing_meta ) === 0 ? array() : json_decode( $proofing_meta, true );
	$all_items_meta      = get_post_meta( $album_id, '_images_n_videos', true );
	$images_videos_array = strlen( $all_items_meta ) === 0 ? array() : json_decode( $all_items_meta, true );

	$approved_array = array();

	foreach($images_videos_array as $item){
		$identifier = ( $item['type'] === 'videolink' ) ? $item['videolink_link'] : $item['id'];

		if( isset( $proofing_array[$identifier]) && $proofing_array[$identifier]['approved'] == '1' ){
			if(is_int($identifier)){
				$approved_array[$identifier] = basename( wp_get_attachment_image_src( $identifier, 'full' )[0] );
			}
			else{
				$approved_array[$identifier] = $identifier;
			}
		}
	}

	return $approved_array;
}
