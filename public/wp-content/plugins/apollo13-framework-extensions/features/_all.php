<?php
if( is_admin() ){
	require_once( A13FE_BASE_DIR.'features/exporter.php' );
}

//load all other files
require_once( A13FE_BASE_DIR.'features/assets.php' );
require_once( A13FE_BASE_DIR.'features/maintenance.php' );
require_once( A13FE_BASE_DIR.'features/permalinks.php' );
require_once( A13FE_BASE_DIR.'features/rss.php' );
require_once( A13FE_BASE_DIR.'features/photoproffing.php' );
require_once( A13FE_BASE_DIR.'features/mega-menu-fe.php' );

if( is_admin() || is_customize_preview() ){
	// ADD MEGA MENU option to menu screen
	global $pagenow;
	if($pagenow === 'nav-menus.php' || defined( 'DOING_AJAX' ) ){
		/** @noinspection PhpIncludeInspection */
		require_once( A13FE_BASE_DIR.'features/mega-menu.php' );
	}
}
