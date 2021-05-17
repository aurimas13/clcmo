<?php
/* Customizations for mega menu */

/**
 * Create HTML list of nav menu input items.
 *
 * @package WordPress
 * @since 3.0.0
 * @uses Walker_Nav_Menu
 */
class A13fe_Admin_Edit_Menu_Walker extends Walker_Nav_Menu {
    /**
     * Starts the list before the elements are added.
     *
     * @see Walker_Nav_Menu::start_lvl()
     *
     * @since 3.0.0
     *
     * @param string $output Passed by reference.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   Not used.
     */
    function start_lvl( &$output, $depth = 0, $args = array() ) {}

    /**
     * Ends the list of after the elements are added.
     *
     * @see Walker_Nav_Menu::end_lvl()
     *
     * @since 3.0.0
     *
     * @param string $output Passed by reference.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   Not used.
     */
    function end_lvl( &$output, $depth = 0, $args = array() ) {}

    /**
     * Start the element output.
     *
     * @see Walker::start_el()
     *
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item   Menu item data object.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   An array of arguments. @see wp_nav_menu()
     * @param int    $id     Current item ID.
     */
    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        ob_start();
        $item_id = esc_attr( $item->ID );
        $removed_args = array(
            'action',
            'customlink-tab',
            'edit-menu-item',
            'menu-item',
            'page-tab',
            '_wpnonce',
        );

        $original_title = '';
        if ( 'taxonomy' == $item->type ) {
            $original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
            if ( is_wp_error( $original_title ) )
                $original_title = false;
        } elseif ( 'post_type' == $item->type ) {
            $original_object = get_post( $item->object_id );
            $original_title = get_the_title( $original_object->ID );
        }

        $classes = array(
            'menu-item menu-item-depth-' . $depth,
            'menu-item-' . esc_attr( $item->object ),
            'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
        );

        $title = $item->title;

        if ( ! empty( $item->_invalid ) ) {
            $classes[] = 'menu-item-invalid';
            /* translators: %s: title of menu item which is invalid */
            $title = sprintf(  esc_html__( '%s (Invalid)', 'apollo13-framework-extensions' ), $item->title );
        } elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
            $classes[] = 'pending';
            /* translators: %s: title of menu item in draft status */
            $title = sprintf(  esc_html__( '%s (Pending)', 'apollo13-framework-extensions' ), $item->title );
        }

        $title = ( ! isset( $item->label ) || '' == $item->label ) ? $title : $item->label;

        $sub_menu_style = '';
        if ( 0 == $depth )
            $sub_menu_style = 'display: none;';
        ?>


    <li id="menu-item-<?php echo esc_attr($item_id); ?>" class="<?php echo esc_attr(implode(' ', $classes )); ?>">
    <dl class="menu-item-bar">
        <dt class="menu-item-handle">
            <span class="item-title"><span class="menu-item-title"><?php echo esc_html( $title ); ?></span> <span class="is-submenu" style="<?php echo esc_attr($sub_menu_style); ?>"><?php esc_html_e( 'sub item', 'apollo13-framework-extensions' ); ?></span></span>
					<span class="item-controls">
						<span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
						<span class="item-order hide-if-js">
							<a href="<?php
                                echo esc_url( wp_nonce_url(
                                    add_query_arg(
                                        array(
                                            'action' => 'move-up-menu-item',
                                            'menu-item' => $item_id,
                                        ),
                                        remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
                                    ),
                                    'move-menu_item'
                                ) );
                                ?>" class="item-move-up"><abbr title="<?php echo esc_attr__( 'Move up', 'apollo13-framework-extensions' ); ?>">&#8593;</abbr></a>
							|
							<a href="<?php
                                echo esc_url( wp_nonce_url(
                                    add_query_arg(
                                        array(
                                            'action' => 'move-down-menu-item',
                                            'menu-item' => $item_id,
                                        ),
                                        remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
                                    ),
                                    'move-menu_item'
                                ) );
                                ?>" class="item-move-down"><abbr title="<?php echo esc_attr__( 'Move down', 'apollo13-framework-extensions' ); ?>">&#8595;</abbr></a>
						</span>
						<a class="item-edit" id="edit-<?php echo esc_attr($item_id); ?>" title="<?php echo esc_attr__( 'Edit Menu Item', 'apollo13-framework-extensions' ); ?>" href="<?php
                            echo esc_url( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) ) );
                            ?>"><span class="screen-reader-text"><?php esc_html_e( 'Edit', 'apollo13-framework-extensions' ); ?></span></a>
					</span>
        </dt>
    </dl>

    <div class="menu-item-settings wp-clearfix" id="menu-item-settings-<?php echo esc_attr($item_id); ?>">
        <?php if( 'custom' == $item->type ) : ?>
        <p class="field-url description description-wide">
            <label for="edit-menu-item-url-<?php echo esc_attr( $item_id); ?>">
                <?php esc_html_e( 'URL', 'apollo13-framework-extensions' ); ?><br />
                <input type="text" id="edit-menu-item-url-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->url ); ?>" />
            </label>
        </p>
        <?php endif; ?>
        <p class="description description-thin">
            <label for="edit-menu-item-title-<?php echo esc_attr($item_id); ?>">
                <?php esc_html_e( 'Navigation Label', 'apollo13-framework-extensions' ); ?><br />
                <input type="text" id="edit-menu-item-title-<?php echo esc_attr($item_id); ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->title ); ?>" />
            </label>
        </p>
        <p class="description description-thin">
            <label for="edit-menu-item-attr-title-<?php echo esc_attr($item_id); ?>">
                <?php esc_html_e( 'Title Attribute', 'apollo13-framework-extensions' ); ?><br />
                <input type="text" id="edit-menu-item-attr-title-<?php echo esc_attr($item_id); ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->post_excerpt ); ?>" />
            </label>
        </p>
        <p class="field-link-target description">
            <label for="edit-menu-item-target-<?php echo esc_attr($item_id); ?>">
                <input type="checkbox" id="edit-menu-item-target-<?php echo esc_attr($item_id); ?>" value="_blank" name="menu-item-target[<?php echo esc_attr($item_id); ?>]"<?php checked( $item->target, '_blank' ); ?> />
                <?php esc_html_e( 'Open link in a new window/tab', 'apollo13-framework-extensions' ); ?>
            </label>
        </p>
        <p class="field-css-classes description description-thin">
            <label for="edit-menu-item-classes-<?php echo esc_attr($item_id); ?>">
                <?php esc_html_e( 'CSS Classes (optional)', 'apollo13-framework-extensions' ); ?><br />
                <input type="text" id="edit-menu-item-classes-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( implode(' ', $item->classes ) ); ?>" />
            </label>
        </p>
        <p class="field-xfn description description-thin">
            <label for="edit-menu-item-xfn-<?php echo esc_attr($item_id); ?>">
                <?php esc_html_e( 'Link Relationship (XFN)', 'apollo13-framework-extensions' ); ?><br />
                <input type="text" id="edit-menu-item-xfn-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->xfn ); ?>" />
            </label>
        </p>
        <p class="field-description description description-wide">
            <label for="edit-menu-item-description-<?php echo esc_attr($item_id); ?>">
                <?php esc_html_e( 'Description', 'apollo13-framework-extensions' ); ?><br />
                <textarea id="edit-menu-item-description-<?php echo esc_attr($item_id); ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo esc_attr($item_id); ?>]"><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
                <span class="description"><?php esc_html_e('The description will be displayed in the menu if the current theme supports it.', 'apollo13-framework-extensions' ); ?></span>
            </label>
        </p>

        <?php
        /**
         * @since  1.8.4
         */
        a13fe_admin_menu_custom_field( $item_id, $item, $depth, $args );
        ?>

        <p class="field-move hide-if-no-js description description-wide">
            <label>
                <span><?php esc_html_e( 'Move', 'apollo13-framework-extensions' ); ?></span>
                <a href="#" class="menus-move-up"><?php esc_html_e( 'Up one', 'apollo13-framework-extensions' ); ?></a>
                <a href="#" class="menus-move-down"><?php esc_html_e( 'Down one', 'apollo13-framework-extensions' ); ?></a>
                <a href="#" class="menus-move-left"></a>
                <a href="#" class="menus-move-right"></a>
                <a href="#" class="menus-move-top"><?php esc_html_e( 'To the top', 'apollo13-framework-extensions' ); ?></a>
            </label>
        </p>

        <div class="menu-item-actions description-wide submitbox">
            <?php if( 'custom' != $item->type && $original_title !== false ) : ?>
            <p class="link-to-original">
                <?php
                /* translators: %s: original menu item title */
                printf(  esc_html__( 'Original: %s', 'apollo13-framework-extensions' ), '<a href="' . esc_url( $item->url ) . '">' . esc_html( $original_title ) . '</a>' );
                ?>
            </p>
            <?php endif; ?>
            <a class="item-delete submitdelete deletion" id="delete-<?php echo esc_attr($item_id); ?>" href="<?php
                echo esc_url( wp_nonce_url(
                    add_query_arg(
                        array(
                            'action' => 'delete-menu-item',
                            'menu-item' => $item_id,
                        ),
                        admin_url( 'nav-menus.php' )
                    ),
                    'delete-menu_item_' . $item_id
                ) ); ?>"><?php esc_html_e( 'Remove', 'apollo13-framework-extensions' ); ?></a> <span class="meta-sep hide-if-no-js"> | </span> <a class="item-cancel submitcancel hide-if-no-js" id="cancel-<?php echo esc_attr($item_id); ?>" href="<?php echo esc_url( add_query_arg( array( 'edit-menu-item' => $item_id, 'cancel' => time() ), admin_url( 'nav-menus.php' ) ) );
            ?>#menu-item-settings-<?php echo esc_attr($item_id); ?>"><?php esc_html_e( 'Cancel', 'apollo13-framework-extensions' ); ?></a>
        </div>

        <input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr($item_id); ?>" />
        <input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
        <input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
        <input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
        <input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
        <input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->type ); ?>" />
    </div><!-- .menu-item-settings-->
    <ul class="menu-item-transport"></ul>
    <?php
        $output .= ob_get_clean();
    }

}

/**
 * register custom walker for main menu
 * @return string name of walker class
 */
function a13fe_admin_change_walker_class($walker) {
    if ( defined('A13FRAMEWORK_THEME_VERSION') && version_compare( A13FRAMEWORK_THEME_VERSION, '2.2.1', '>=' ) ){
        return 'A13fe_Admin_Edit_Menu_Walker';
    }
    return $walker;
}
//add custom walker for admin menu only for WordPress versions < 5.4
global $wp_version;
if(version_compare(preg_replace("/[^0-9\.]/","",$wp_version), '5.4', '<')){
    add_filter( 'wp_edit_nav_menu_walker', 'a13fe_admin_change_walker_class', 10, 4 );
}

/**
 * replaces custom walker since WordPress 5.4
 * prints custom menu fields
 * @since  1.8.4
 *
 * @param $item_id
 * @param $item
 * @param $depth
 * @param $args
 *
 */
function a13fe_admin_menu_custom_field( $item_id, $item, $depth, $args ) {
    static $mega_menu_enabled = false;

    $classes = array();

    //mega menu check
    if( $depth === 0 ){
        if($item->a13_mega_menu){
            $mega_menu_enabled = true;
            $classes[] = 'mega-menu-enabled';
        }
        else{
            $mega_menu_enabled = false;
        }
    }
    if( $depth === 1 && $mega_menu_enabled){
        $classes[] = 'mega-menu-enabled';
    }

    ?>
    <!-- Apollo13 Framework Theme enhancement options-->

        <fieldset class="a13-theme-enhancement-options"><legend><?php esc_html_e( 'Enhancement options from the theme', 'apollo13-framework-extensions' ); ?></legend>
        <p class="field-a13_item_icon description">
            <label for="edit-menu-item-a13_item_icon-<?php echo esc_attr($item_id); ?>">
                <?php esc_html_e( 'Item icon', 'apollo13-framework-extensions' ); ?><br />
                <input type="text" id="edit-menu-item-a13_item_icon-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-a13_item_icon a13-fa-icon" name="menu-item-a13_item_icon[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->a13_item_icon ); ?>" />
            </label>
        </p>
        <p class="field-a13_unlinkable">
            <label for="edit-menu-item-a13_unlinkable-<?php echo esc_attr($item_id); ?>">
                <input id="edit-menu-item-a13_unlinkable-<?php echo esc_attr($item_id); ?>" type="checkbox" name="menu-item-a13_unlinkable[<?php echo esc_attr($item_id); ?>]" <?php checked( $item->a13_unlinkable ); ?>/>
                <?php esc_html_e( 'Set this item as plain text (will not be a link)', 'apollo13-framework-extensions' ); ?>
            </label>
        </p>


        <!-- Apollo13 Framework Mega menu settings-->

            <fieldset class="a13-megamenu-options <?php echo esc_attr(implode(' ', $classes )); ?>"><legend><?php esc_html_e( 'Mega menu options', 'apollo13-framework-extensions' ); ?></legend>
                <p class="field-enable-a13_mega_menu description level-0">
                    <label for="edit-menu-item-a13_mega_menu-<?php echo esc_attr($item_id); ?>">
                        <input type="checkbox" class="enable-mega-menu" id="edit-menu-item-a13_mega_menu-<?php echo esc_attr($item_id); ?>" name="menu-item-a13_mega_menu[<?php echo esc_attr($item_id); ?>]"<?php checked( $item->a13_mega_menu ); ?> />
                        <?php esc_html_e( 'Enable mega menu?', 'apollo13-framework-extensions' ); ?>
                    </label>
                </p>

                <p class="field-a13_mm_columns description level-0 if-mm-enabled">
                    <label for="edit-menu-item-a13_mm_columns-<?php echo esc_attr($item_id); ?>">
                        <?php esc_html_e( 'Number of columns', 'apollo13-framework-extensions' ); ?><br />
                        <select name="menu-item-a13_mm_columns[<?php echo esc_attr($item_id); ?>]" id="edit-menu-item-a13_mm_columns-<?php echo esc_attr($item_id); ?>">
                            <?php
                            $column_options =  array( 2, 3, 4 );
                            foreach($column_options as $val){
                                echo '<option value="'.esc_attr($val).'" '.selected($val, $item->a13_mm_columns).'>'.esc_html($val).'</option>';
                            }
                            ?>
                        </select>
                    </label>
                </p>

                <p class="field-a13_mm_remove_item level-1">
                    <label for="edit-menu-item-a13_mm_remove_item-<?php echo esc_attr($item_id); ?>">
                        <input id="edit-menu-item-a13_mm_remove_item-<?php echo esc_attr($item_id); ?>" type="checkbox" name="menu-item-a13_mm_remove_item[<?php echo esc_attr($item_id); ?>]" <?php checked( $item->a13_mm_remove_item ); ?>/>
                        <?php esc_html_e( 'Do not show this item in the mega menu. Show only the options of the sub menu.', 'apollo13-framework-extensions' ); ?>
                    </label>
                </p>

                <p class="field-a13_mm_html level-1">
                    <label for="edit-menu-item-a13_mm_html-<?php echo esc_attr($item_id); ?>">
                        <?php esc_html_e( 'Use HTML instead', 'apollo13-framework-extensions' ); ?><br />
                        <textarea id="edit-menu-item-a13_mm_html-<?php echo esc_attr($item_id); ?>" class="widefat edit-menu-item-a13_mm_html" rows="3" cols="20" name="menu-item-a13_mm_html[<?php echo esc_attr($item_id); ?>]"><?php echo esc_html( $item->a13_mm_html ); // textarea_escaped ?></textarea>
                        <span class="description"><?php esc_html_e('If this field is filled, then this HTML will be used instead of a menu option. Also, no children menu options will be displayed.', 'apollo13-framework-extensions' ); ?></span>
                    </label>
                </p>
            </fieldset><!-- .a13-megamenu-options-->
        </fieldset>
        <?php
        do_action( 'apollo13framework_edit_menu_walker_print_item_settings', $item, $depth, $args, get_the_ID(), $item_id );
}
add_action( 'wp_nav_menu_item_custom_fields', 'a13fe_admin_menu_custom_field', 10, 4 );


/**
 * Save all custom theme fields for menu options
 *
 * @param $menu_id
 * @param $menu_item_db_id
 * @param $args
 */
function a13fe_admin_update_custom_menu_fields( /** @noinspection PhpUnusedParameterInspection */
	$menu_id, $menu_item_db_id, $args ) {
    if ( defined('A13FRAMEWORK_THEME_VERSION') && version_compare( A13FRAMEWORK_THEME_VERSION, '2.2.1', '<' ) ) {
        return;
    }

    //mega menu
    $value = isset($_REQUEST['menu-item-a13_mega_menu'], $_REQUEST['menu-item-a13_mega_menu'][$menu_item_db_id]);
    update_post_meta( $menu_item_db_id, '_a13_mega_menu', $value );

    $value = isset($_REQUEST['menu-item-a13_mm_remove_item'], $_REQUEST['menu-item-a13_mm_remove_item'][$menu_item_db_id]);
    update_post_meta( $menu_item_db_id, '_a13_mm_remove_item', $value );

    $value = isset($_REQUEST['menu-item-a13_mm_html'], $_REQUEST['menu-item-a13_mm_html'][$menu_item_db_id])? wp_kses_post( wp_unslash( $_REQUEST['menu-item-a13_mm_html'][$menu_item_db_id] ) ) : '';
    update_post_meta( $menu_item_db_id, '_a13_mm_html', $value );

    if ( isset($_REQUEST['menu-item-a13_mm_columns']) && is_array( $_REQUEST['menu-item-a13_mm_columns'] ) ) {
        $columns = absint( $_REQUEST['menu-item-a13_mm_columns'][$menu_item_db_id] );
        update_post_meta( $menu_item_db_id, '_a13_mm_columns', $columns );
    }

    //theme enhancement
    $value = isset($_REQUEST['menu-item-a13_item_icon'], $_REQUEST['menu-item-a13_item_icon'][$menu_item_db_id])? sanitize_text_field( wp_unslash( $_REQUEST['menu-item-a13_item_icon'][$menu_item_db_id] ) ) : '';
    update_post_meta( $menu_item_db_id, '_a13_item_icon', $value );

	$value = isset($_REQUEST['menu-item-a13_unlinkable'], $_REQUEST['menu-item-a13_unlinkable'][$menu_item_db_id]);
    update_post_meta( $menu_item_db_id, '_a13_unlinkable', $value );
}
if(!is_customize_preview()){
    add_action( 'wp_update_nav_menu_item', 'a13fe_admin_update_custom_menu_fields', 10, 3 );
}

