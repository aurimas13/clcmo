<?php
/**
 * Minimum Elementor version.
 *
 * Minimum Elementor version needed for Elementor extensions to work.
 *
 * @since 1.0.0
 */
const A13FE_MINIMUM_ELEMENTOR_VERSION = '2.0.0';

// Check for required Elementor version
if ( defined('ELEMENTOR_VERSION') && ! version_compare( ELEMENTOR_VERSION, A13FE_MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
	add_action( 'admin_notices', 'a13fe_admin_notice_minimum_elementor_version' );
	return;
}
else{
	require_once A13FE_BASE_DIR.'supports/elementor_extensions/widgets.php';
	new \Apollo13_FE\Widgets();
}

function a13fe_admin_notice_minimum_elementor_version() {
	echo '<div class="notice notice-warning is-dismissible">'.
	     wpautop(
		     sprintf(
		     /* translators: %s: Required Elementor version */
			     esc_html__( 'Apollo13 Framework Extensions plugin requires Elementor version %s or greater.', 'apollo13-framework-extensions' ),
			     A13FE_MINIMUM_ELEMENTOR_VERSION
		     )
	     ).'</div>';
}