<?php
/* remade to our needs from bfi thumb source code */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/* in case there is old version of theme which had this feature inside, we don't add it */
if( class_exists('A13IR_Image_Editor_Imagick') ){
    return;
}
else{
    /*
     * Include the WP Image classes
     */
    require_once ABSPATH . WPINC . '/class-wp-image-editor.php';
    require_once ABSPATH . WPINC . '/class-wp-image-editor-imagick.php';
    require_once ABSPATH . WPINC . '/class-wp-image-editor-gd.php';

    class A13IR_Image_Editor_Imagick extends WP_Image_Editor_Imagick {
    }

    class A13IR_Image_Editor_GD extends WP_Image_Editor_GD {
    }

    /*
     * Change the default image editors
     * using own classes, even empty, prevents Strict errors
     */
    add_filter( 'wp_image_editors', 'a13fe_ir_wp_image_editor' );

    function a13fe_ir_wp_image_editor() {
        return array(
            'A13IR_Image_Editor_Imagick',
            'A13IR_Image_Editor_GD',
        );
    }


    /**
     * check for ImageMagick or GD
     */
    add_action( 'admin_init', 'a13fe_ir_wp_image_editor_check' );

    function a13fe_ir_wp_image_editor_check() {
        $arg = array( 'mime_type' => 'image/jpeg' );
        if ( wp_image_editor_supports( $arg ) !== true ) {
            add_filter( 'admin_notices', 'a13fe_ir_wp_image_editor_check_notice' );
        }
    }

    function a13fe_ir_wp_image_editor_check_notice() {
        printf( "<div class='error'><p>%s</div>", 'Error 1001' );
    }


    /*
     * Main Class
     */

    class Apollo13_Image_Resize {

        /** Uses WP's Image Editor Class to resize and filter images
         * Inspired by: https://github.com/sy4mil/Aqua-Resizer/blob/master/aq_resizer.php
         *
         * @param $url string the local image URL to manipulate
         * @param $params array the options to perform on the image. Keys and values supported:
         *          'width' int pixels
         *          'height' int pixels
         *          'crop' bool
         *			'quality' int 1-100
         *
         * @return string|array
         */
        public static function thumb( $url, $params = array() ) {
            extract( $params );

            //validate inputs
            if ( ! $url ) {
                return false;
            }

            //define upload path & dir
            $upload_info = wp_upload_dir();
            $upload_dir = $upload_info['basedir'];
            $upload_url = $upload_info['baseurl'];
            $theme_url = get_template_directory_uri();
            $theme_dir = get_template_directory();

            // find the path of the image. Perform 2 checks:
            // #1 check if the image is in the uploads folder
            if ( strpos( $url, $upload_url ) !== false ) {
                $rel_path = str_replace( $upload_url, '', $url );
                $img_path = $upload_dir . $rel_path;

            // #2 check if the image is in the current theme folder
            } else if ( strpos( $url, $theme_url ) !== false ) {
                $rel_path = str_replace( $theme_url, '', $url );
                $img_path = $theme_dir . $rel_path;
            }

            // Fail if we can't find the image in our WP local directory
            if ( empty( $img_path ) ) {
                return $url;
            }

            // check if img path exists, and is an image indeed
            if( ! file_exists( $img_path ) || ! getimagesize( $img_path ) ) {
                return $url;
            }

            //get image info
            $info = pathinfo( $img_path );
            $ext = $info['extension'];
            list( $orig_w, $orig_h ) = getimagesize( $img_path );

            // support percentage dimensions. compute percentage based on
            // the original dimensions
            if ( isset( $width ) ) {
                if ( stripos( $width, '%' ) !== false ) {
                    $width = (int) ( (float) str_replace( '%', '', $width ) / 100 * $orig_w );
                }
            }
            if ( isset( $height ) ) {
                if ( stripos( $height, '%' ) !== false ) {
                    $height = (int) ( (float) str_replace( '%', '', $height ) / 100 * $orig_h );
                }
            }

            // The only purpose of this is to determine the final width and height
            // without performing any actual image manipulation, which will be used
            // to check whether a resize was previously done.
            if ( isset( $width ) ) {
                //get image size after cropping
                $dims = image_resize_dimensions( $orig_w, $orig_h, $width, isset( $height ) ? $height : null, isset( $crop ) ? $crop : false );
                $dst_w = $dims[4];
                $dst_h = $dims[5];

            }

            // create the suffix for the saved file
            // we can use this to check whether we need to create a new file or just use an existing one.
            $suffix = (string) filemtime( $img_path ) .
                ( isset( $width ) ? str_pad( (string) $width, 5, '0', STR_PAD_LEFT ) : '00000' ) .
                ( isset( $height ) ? str_pad( (string) $height, 5, '0', STR_PAD_LEFT ) : '00000' ) .
                ( isset( $crop ) ? ( $crop ? '1' : '0' ) : '0' ) .
                ( isset( $src_x ) ? str_pad( (string) $src_x, 5, '0', STR_PAD_LEFT ) : '00000' ) .
                ( isset( $src_y ) ? str_pad( (string) $src_y, 5, '0', STR_PAD_LEFT ) : '00000' ) .
                ( isset( $src_w ) ? str_pad( (string) $src_w, 5, '0', STR_PAD_LEFT ) : '00000' ) .
                ( isset( $src_h ) ? str_pad( (string) $src_h, 5, '0', STR_PAD_LEFT ) : '00000' ) .
                ( isset( $dst_w ) ? str_pad( (string) $dst_w, 5, '0', STR_PAD_LEFT ) : '00000' ) .
                ( isset( $dst_h ) ? str_pad( (string) $dst_h, 5, '0', STR_PAD_LEFT ) : '00000' ) .
                ( ( isset ( $quality ) && $quality > 0 && $quality <= 100 ) ? ( $quality ? (string) $quality : '0' ) : '0' );
            $suffix = self::base_convert_arbitrary( $suffix, 10, 36 );

            // use this to check if cropped image already exists, so we can return that instead
            $dst_rel_path = str_replace( '.' . $ext, '', basename( $img_path ) );


            // Create the upload subdirectory, this is where
            // we store all our generated images
            $upload_dir .= "/apollo13_images";
            $upload_url .= "/apollo13_images";
            if ( ! is_dir( $upload_dir ) ) {
                wp_mkdir_p( $upload_dir );
            }


            // desination paths and urls
            $destfilename = "{$upload_dir}/{$dst_rel_path}-{$suffix}.{$ext}";

            // The urls generated have lower case extensions regardless of the original case
            $ext = strtolower( $ext );
            $img_url = "{$upload_url}/{$dst_rel_path}-{$suffix}.{$ext}";

            // if file exists, just return it
            if ( file_exists( $destfilename ) && getimagesize( $destfilename ) ) {
            } else {
                // perform resizing
                $editor = wp_get_image_editor( $img_path );

                if ( is_wp_error( $editor ) ) return false;

                /*
                 * Perform image manipulations
                 */
                if ( ( isset( $width ) && $width ) || ( isset( $height ) && $height ) ) {
                    if ( is_wp_error( $editor->resize( isset( $width ) ? $width : null, isset( $height ) ? $height : null, isset( $crop ) ? $crop : false ) ) ) {
                        return false;
                    }
                }

                // set the image quality (1-100) to save this image at
                if ( isset( $quality ) && $quality > 0 && $quality <= 100 && $ext != 'png' ) {
                    $editor->set_quality( $quality );
                }

                // save our new image
                $editor->save( $destfilename );
            }

            return $img_url;
        }


        /** Shortens a number into a base 36 string
         *
         * @param $number   string  a string of numbers to convert
         * @param $fromBase int     starting base
         * @param $toBase   int     base to convert the number to
         * @return string base converted characters
         */
        protected static function base_convert_arbitrary( $number, $fromBase, $toBase ) {
            $digits = '0123456789abcdefghijklmnopqrstuvwxyz';
            $length = strlen( $number );
            $result = '';

            $nibbles = array();
            for ( $i = 0; $i < $length; ++$i ) {
                $nibbles[ $i ] = strpos( $digits, $number[ $i ] );
            }

            do {
                $value = 0;
                $newlen = 0;

                for ( $i = 0; $i < $length; ++$i ) {

                    $value = $value * $fromBase + $nibbles[ $i ];

                    if ( $value >= $toBase ) {
                        $nibbles[ $newlen++ ] = (int) ( $value / $toBase );
                        $value %= $toBase;

                    } else if ( $newlen > 0 ) {
                        $nibbles[ $newlen++ ] = 0;
                    }
                }

                $length = $newlen;
                $result = $digits[ $value ] . $result;
            }
            while ( $newlen != 0 );

            return $result;
        }
    }



    // don't use the default resizer since we want to allow resizing to larger sizes (than the original one)
    // Parts are copied from media.php
    // Crop is always applied
    // Don't use this inside the admin since sometimes images in the media library get resized
    $ajax_for_vc_grid = defined( 'DOING_AJAX' ) && DOING_AJAX && isset($_POST['action']) && ($_POST['action'] === 'vc_get_vc_grid_data');
    $elementor_edit_mode = defined('ELEMENTOR_VERSION') && ( isset( $_REQUEST['action'] ) && ( $_REQUEST['action'] === 'elementor' || $_REQUEST['action'] === 'elementor_ajax') );
    if ( ! is_admin() || $ajax_for_vc_grid || $elementor_edit_mode) {
        add_filter( 'image_resize_dimensions', 'a13fe_ir_image_resize_dimensions', 10, 6 );
    }

    function a13fe_ir_image_resize_dimensions( /** @noinspection PhpUnusedParameterInspection */ $payload, $orig_w, $orig_h, $dest_w, $dest_h, /** @noinspection PhpUnusedParameterInspection */ $crop = false ) {
        $aspect_ratio = $orig_w / $orig_h;

        $new_w = $dest_w;
        $new_h = $dest_h;

        if ( empty( $new_w ) || $new_w < 0  ) {
            $new_w = (int)( $new_h * $aspect_ratio );
        }

        if ( empty( $new_h ) || $new_h < 0 ) {
            $new_h = (int)( $new_w / $aspect_ratio );
        }

        if( $crop ){
            if( $new_w > $orig_w ){
                $dest_aspect_ratio = $dest_w / $dest_h;
                $new_w = $orig_w;
                $new_h = (int)( $new_w / $dest_aspect_ratio );
            }
        }
        else{
            //if no crop then set both sizes to max possible value if needed
            if( $new_w > $orig_w ){
                $new_w = $orig_w;
                $new_h = $orig_h;
            }
        }


        $size_ratio = max( $new_w / $orig_w, $new_h / $orig_h );

        $crop_w = round( $new_w / $size_ratio );
        $crop_h = round( $new_h / $size_ratio );
        $s_x = floor( ( $orig_w - $crop_w ) / 2 );
        $s_y = floor( ( $orig_h - $crop_h ) / 2 );

        // Safe guard against super large or zero images which might cause 500 errors
        if ( $new_w > 5000 || $new_h > 5000 || $new_w <= 0 || $new_h <= 0 ) {
            return null;
        }

        // the return array matches the parameters to imagecopyresampled()
        // int dst_x, int dst_y, int src_x, int src_y, int dst_w, int dst_h, int src_w, int src_h
        return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );
    }


    add_filter( 'image_downsize', 'a13fe_ir_image_downsize', 2, 3 );


    function a13fe_ir_image_downsize( /** @noinspection PhpUnusedParameterInspection */ $out, $id, $size ) {
        if ( is_array($size) && array_key_exists( 'bfi_thumb', $size ) && is_array( $out ) ) {
            return $out;
        }
        if ( ! is_array( $size ) ) {
            return false;
        }
        if ( ! array_key_exists( 'apollo13_image', $size ) ) {
            return false;
        }
        if ( empty( $size['apollo13_image'] ) ) {
            return false;
        }

        $img_url = wp_get_attachment_url( $id );

        $params = $size;
        $params['width'] = $size[0];
        $params['height'] = $size[1];

        $resized_img_url = Apollo13_Image_Resize::thumb( $img_url, $params );

        return array( $resized_img_url, $size[0], $size[1], false );
    }
}