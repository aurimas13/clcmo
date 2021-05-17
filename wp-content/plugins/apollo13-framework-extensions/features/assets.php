<?php
/**
 * Registering frontend theme scripts
 * @since 1.5.6
 */
function a13fe_theme_scripts(){
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	//Animation library
	wp_register_script( 'anime', A13FE_ASSETS_URL . 'js/anime.min.js', false, '3.1.0', true);

	//Apollo13Themes Slider
	wp_register_script( 'apollo13framework-slider', A13FE_ASSETS_URL . 'js/a13-slider' . $suffix . '.js', array('jquery', 'anime'), A13FE_VERSION, true);
}
add_action( 'wp_enqueue_scripts', 'a13fe_theme_scripts', 25 );
