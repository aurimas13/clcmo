<?php
/**
 * Fill all custom theme fields for current menu option
 * @param object $menu_item current menu item in loop
 *
 * @return mixed filled menu object
 */
function a13fe_admin_add_custom_menu_fields( $menu_item ) {
    if ( defined('A13FRAMEWORK_THEME_VERSION') && version_compare( A13FRAMEWORK_THEME_VERSION, '2.2.1', '>=' ) ) {
        //mega menu
        $menu_item->a13_mega_menu       = get_post_meta( $menu_item->ID, '_a13_mega_menu', true );
        $menu_item->a13_mm_columns      = get_post_meta( $menu_item->ID, '_a13_mm_columns', true );
        $menu_item->a13_mm_remove_item  = get_post_meta( $menu_item->ID, '_a13_mm_remove_item', true );
        $menu_item->a13_unlinkable      = get_post_meta( $menu_item->ID, '_a13_unlinkable', true );
        $menu_item->a13_mm_html         = get_post_meta( $menu_item->ID, '_a13_mm_html', true );

        //theme enhancement
        $menu_item->a13_item_icon       = get_post_meta( $menu_item->ID, '_a13_item_icon', true );
    }

    return $menu_item;
}
add_filter( 'wp_setup_nav_menu_item', 'a13fe_admin_add_custom_menu_fields' );