<?php

/**
 * Adds menu widget
 * @since 1.2.0
 */
function a13fe_register_menu_widgets(){
	class A13fe_Nav_Menu_Widget extends WP_Widget {

		function __construct() {
			$widget_ops = array( 'description' => esc_html__( 'Use this widget to add one of your custom menus as a widget.', 'apollo13-framework-extensions' ) );
			parent::__construct( 'nav_menu', esc_html__( 'Apollo13Themes', 'apollo13-framework-extensions' ) . ' - ' . esc_html__( 'Custom Menu', 'apollo13-framework-extensions' ), $widget_ops );
		}

		function widget( $args, $instance ) {
			// Get menu
			$nav_menu = ! empty( $instance['nav_menu'] ) ? wp_get_nav_menu_object( $instance['nav_menu'] ) : false;

			if ( ! $nav_menu ) {
				return;
			}

			$instance['title'] = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

			print $args['before_widget'];

			if ( ! empty( $instance['title'] ) ) {
				print $args['before_title'] . $instance['title'] . $args['after_title'];
			}

			if( !class_exists('A13FRAMEWORK_custom_menu_widget_walker') ){
				require_once( A13FE_BASE_DIR.'widgets/widget-menu-walker.php' );
			}

			wp_nav_menu( array( 'fallback_cb' => '', 'menu' => $nav_menu, 'walker' => new A13FRAMEWORK_custom_menu_widget_walker ) );

			print $args['after_widget'];
		}

		function update( $new_instance, $old_instance ) {
			$instance['title']    = strip_tags( wp_unslash( $new_instance['title'] ) );
			$instance['nav_menu'] = (int) $new_instance['nav_menu'];

			return $instance;
		}

		function form( $instance ) {
			$title    = isset( $instance['title'] ) ? $instance['title'] : '';
			$nav_menu = isset( $instance['nav_menu'] ) ? $instance['nav_menu'] : '';

			// Get menus
			$menus = get_terms( array( 'taxonomy' => 'nav_menu', 'hide_empty' => false ) );

			// If no menus exists, direct the user to go and create some.
			if ( ! $menus ) {
				/* translators: %s - link to admin area */
				echo '<p>' . sprintf( wp_kses_data( __( 'No menus have been created yet. <a href="%s">Create some</a>.', 'apollo13-framework-extensions' ) ), esc_url( admin_url( 'nav-menus.php' ) ) ) . '</p>';

				return;
			}
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'apollo13-framework-extensions' ); ?></label>
				<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'nav_menu' ) ); ?>"><?php esc_html_e( 'Select Menu', 'apollo13-framework-extensions' ); ?></label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'nav_menu' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'nav_menu' ) ); ?>">
					<?php
					foreach ( $menus as $menu ) {
						echo '<option value="' . esc_attr( $menu->term_id ) . '"'
						     . selected( $nav_menu, $menu->term_id, false )
						     . '>' . $menu->name . '</option>';
					}
					?>
				</select>
			</p>
			<?php
		}
	}

	register_widget( 'A13fe_Nav_Menu_Widget' );
}

add_action( 'widgets_init', 'a13fe_register_menu_widgets' );