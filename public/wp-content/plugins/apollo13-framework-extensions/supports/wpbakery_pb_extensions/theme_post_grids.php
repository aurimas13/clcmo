<?php
/**
 * imports framework post grids for WPBakery
 *
 * @since  1.4.0
 */
function a13fe_autoimport_wpbakery_theme_grid_items() {
	// check if it was imported
	$check = get_option( 'apollo13_grid_elements_loaded' );
	if( $check == 1 ){
		return;
	}

	//file does not exist
	$import_file = A13FE_BASE_DIR.'supports/wpbakery_pb_extensions/grids/import.xml';
	if( !file_exists($import_file) ){
		return;
	}

	/** @noinspection PhpIncludeInspection */
	require_once A13FE_BASE_DIR.'/design_importer/a13-wordpress-importer/class-apollo13-framework-import.php';

	$importer = new Apollo13Framework_Import();
	$importer->import( $import_file, 1);

	update_option( 'apollo13_grid_elements_loaded', '1', false);
}
add_action( 'admin_init', 'a13fe_autoimport_wpbakery_theme_grid_items' );