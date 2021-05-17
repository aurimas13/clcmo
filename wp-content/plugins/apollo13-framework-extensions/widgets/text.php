<?php

/**
 * Adds various widgets based on text content:
 * Contact Info, Shortcodes displaying widget, Social Icons
 * @since 1.2.0
 */
function a13fe_register_text_widgets(){

	class A13fe_Widget_Contact_Info extends WP_Widget {

		function __construct() {
			$widget_ops = array( 'classname'   => 'widget_contact_info',
			                     'description' => esc_html__( 'Contact information', 'apollo13-framework-extensions' )
			);
			parent::__construct( 'contact-info',  esc_html__( 'Apollo13Themes', 'apollo13-framework-extensions' ) . ' - ' . esc_html__( 'Contact information', 'apollo13-framework-extensions' ), $widget_ops );
			$this->alt_option_name = 'widget_contact_info';

			add_action( 'save_post', array( &$this, 'flush_midget_cache' ) );
			add_action( 'deleted_post', array( &$this, 'flush_midget_cache' ) );
			add_action( 'switch_theme', array( &$this, 'flush_midget_cache' ) );
		}

		function widget( $args, $instance ) {
			$before_widget = $after_widget = $before_title = $after_title = '';
			$cache         = wp_cache_get( 'widget_contact_info', 'widget' );

			if ( ! is_array( $cache ) ) {
				$cache = array();
			}

			if ( isset( $cache[ $args['widget_id'] ] ) ) {
				print $cache[ $args['widget_id'] ];

				return;
			}

			ob_start();
			extract( $args );

			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? esc_html__( 'Contact information', 'apollo13-framework-extensions' ) : $instance['title'], $instance, $this->id_base );

			print $before_widget;

			if ( $title ) {
				print $before_title . $title . $after_title;
			}

			echo '<div class="info">';

			if ( ! empty( $instance['content'] ) ) {
				echo '<div class="content-text">' . nl2br( wp_kses_data( balanceTags( $instance['content'], true ) ) )  . '</div>';
			}
			if ( ! empty( $instance['phone'] ) ) {
				echo '<div class="phone with_icon"><i class="fa fa-phone"></i>' . esc_html( $instance['phone'] ) . '</div>';
			}
			if ( ! empty( $instance['fax'] ) ) {
				echo '<div class="fax with_icon"><i class="fa fa-print"></i>' . esc_html( $instance['fax'] ) . '</div>';
			}
			if ( ! empty( $instance['email'] ) ) {
				$email = $instance['email'];
				$email = sanitize_email($email);
				echo '<a class="email with_icon" href="mailto:'.esc_attr( antispambot($email,1) ).'"><i class="fa fa-envelope-o"></i>'.esc_html( antispambot($email) ).'</a>';
			}
			if ( ! empty( $instance['www'] ) ) {
				echo '<a class="www with_icon" href="' . esc_url( $instance['www'] ) . '"><i class="fa fa-external-link"></i>' . esc_html( $instance['www'] ) . '</a>';
			}
			if ( ! empty( $instance['open'] ) ) {
				echo '<div class="content-open with_icon"><i class="fa fa-clock-o"></i>' . nl2br( wp_kses_data( balanceTags( $instance['open'], true ) ) ) . '</div>';
			}

			echo '</div>';

			print $after_widget;

			$cache[ $args['widget_id'] ] = ob_get_flush();
			wp_cache_set( 'widget_related_entries', $cache, 'widget' );
		}

		function update( $new_instance, $old_instance ) {
			$instance            = $old_instance;
			$instance['title']   = strip_tags( $new_instance['title'] );
			$instance['phone']   = strip_tags( $new_instance['phone'] );
			$instance['email']   = strip_tags( $new_instance['email'] );
			$instance['fax']     = strip_tags( $new_instance['fax'] );
			$instance['www']     = strip_tags( $new_instance['www'] );
			$instance['content'] = $new_instance['content'];
			$instance['open']    = strip_tags( $new_instance['open'] );

			$this->flush_midget_cache();

			$alloptions = wp_cache_get( 'alloptions', 'options' );
			if ( isset( $alloptions['widget_contact_info'] ) ) {
				delete_option( 'widget_contact_info' );
			}

			return $instance;
		}

		function flush_midget_cache() {
			wp_cache_delete( 'widget_contact_info', 'widget' );
		}

		function form( $instance ) {
			$title   = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
			$phone   = isset( $instance['phone'] ) ? esc_attr( $instance['phone'] ) : '';
			$email   = isset( $instance['email'] ) ? esc_attr( $instance['email'] ) : '';
			$fax     = isset( $instance['fax'] ) ? esc_attr( $instance['fax'] ) : '';
			$www     = isset( $instance['www'] ) ? esc_attr( $instance['www'] ) : '';
			$content = isset( $instance['content'] ) ? esc_textarea( $instance['content'] ) : '';
			$open    = isset( $instance['open'] ) ? esc_textarea( $instance['open'] ) : '';
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'apollo13-framework-extensions' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>"><?php esc_html_e( 'Content', 'apollo13-framework-extensions' ); ?></label>
				<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'content' ) ); ?>" cols="20" rows="8"><?php echo esc_textarea( $content ); ?></textarea>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'phone' ) ); ?>"><?php esc_html_e( 'Phone', 'apollo13-framework-extensions' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'phone' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'phone' ) ); ?>" type="text" value="<?php echo esc_attr( $phone ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'fax' ) ); ?>"><?php esc_html_e( 'Fax', 'apollo13-framework-extensions' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'fax' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'fax' ) ); ?>" type="text" value="<?php echo esc_attr( $fax ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'email' ) ); ?>"><?php esc_html_e( 'E-mail', 'apollo13-framework-extensions' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'email' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'email' ) ); ?>" type="text" value="<?php echo esc_attr( $email ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'www' ) ); ?>"><?php esc_html_e( 'Website', 'apollo13-framework-extensions' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'www' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'www' ) ); ?>" type="text" value="<?php echo esc_attr( $www ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'open' ) ); ?>"><?php esc_html_e( 'Open hours', 'apollo13-framework-extensions' ); ?></label>
				<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'open' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'open' ) ); ?>" cols="20" rows="8"><?php echo esc_textarea( $open ); ?></textarea>
			</p>

			<?php
		}
	}

	register_widget( 'A13fe_Widget_Contact_Info' );


	class A13fe_Widget_Shortcodes extends WP_Widget {

		function __construct() {
			$widget_ops  = array(
				'classname'   => 'widget_shortcodes',
				'description' => esc_html__( 'Widget for placing shortcodes.', 'apollo13-framework-extensions' )
			);
			$control_ops = array( 'width' => 400, 'height' => 350 );
			parent::__construct( 'a13-shortcodes',  esc_html__( 'Apollo13Themes', 'apollo13-framework-extensions' ) . ' - ' . esc_html__( 'Shortcodes', 'apollo13-framework-extensions' ), $widget_ops, $control_ops );
		}

		function widget( $args, $instance ) {
			$before_widget = $after_widget = $before_title = $after_title = '';
			extract( $args );
			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
			$text  = apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance );
			print $before_widget;
			if ( ! empty( $title ) ) {
				print $before_title . $title . $after_title;
			} ?>
			<div class="textwidget"><?php echo do_shortcode( $text ); ?></div>
			<?php
			print $after_widget;
		}

		function update( $new_instance, $old_instance ) {
			$instance          = $old_instance;
			$instance['title'] = strip_tags( $new_instance['title'] );
			if ( current_user_can( 'unfiltered_html' ) ) {
				$instance['text'] = $new_instance['text'];
			} else {
				$instance['text'] = wp_unslash( wp_filter_post_kses( addslashes( $new_instance['text'] ) ) );
			} // wp_filter_post_kses() expects slashed
			$instance['filter'] = isset( $new_instance['filter'] );

			return $instance;
		}

		function form( $instance ) {
			$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
			$title    = strip_tags( $instance['title'] );
			$text     = esc_textarea( $instance['text'] );
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'apollo13-framework-extensions' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<label for="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>"><?php esc_html_e( 'Content', 'apollo13-framework-extensions' ); ?></label>
			<textarea class="widefat" rows="16" cols="20" id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>"><?php echo esc_textarea( $text ); ?></textarea>

			<p>
				<input id="<?php echo esc_attr( $this->get_field_id( 'filter' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'filter' ) ); ?>" type="checkbox" <?php checked( isset( $instance['filter'] ) ? $instance['filter'] : 0 ); ?> />&nbsp;<label for="<?php echo esc_attr( $this->get_field_id( 'filter' ) ); ?>"><?php esc_html_e( 'Automatically add paragraphs', 'apollo13-framework-extensions' ); ?></label>
			</p>
			<?php
		}
	}

	register_widget( 'A13fe_Widget_Shortcodes' );

	class A13fe_Widget_Social_Icons extends WP_Widget {

		function __construct() {
			$widget_ops = array( 'classname'   => 'widget_a13_social_icons',
			                     'description' => esc_html__( 'Social icons from the theme settings', 'apollo13-framework-extensions' )
			);
			parent::__construct( 'a13-social-icons', esc_html__( 'Apollo13Themes', 'apollo13-framework-extensions' ) . ' - ' . esc_html__( 'Social icons', 'apollo13-framework-extensions' ), $widget_ops );
			$this->alt_option_name = 'widget_a13_social_icons';

			add_action( 'save_post', array( &$this, 'flush_midget_cache' ) );
			add_action( 'deleted_post', array( &$this, 'flush_midget_cache' ) );
			add_action( 'switch_theme', array( &$this, 'flush_midget_cache' ) );
		}

		function widget( $args, $instance ) {
			$before_widget = $after_widget = $before_title = $after_title = '';
			$cache         = wp_cache_get( 'widget_a13_social_icons', 'widget' );

			if ( ! is_array( $cache ) ) {
				$cache = array();
			}

			if ( isset( $cache[ $args['widget_id'] ] ) ) {
				print $cache[ $args['widget_id'] ];

				return;
			}

			ob_start();
			extract( $args );

			$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

			//without theme function it is useless, as icons come from theme settings
			if(function_exists('apollo13framework_social_icons')){
				$icons = apollo13framework_social_icons( $instance['icons_color'], $instance['icons_color_hover'] );
				if ( strlen( $icons ) ) :
					print $before_widget;

					if ( $title ) {
						print $before_title . $title . $after_title;
					}

					print $icons;

					print $after_widget;

				endif;
			}

			$cache[ $args['widget_id'] ] = ob_get_flush();
			wp_cache_set( 'widget_a13_social_icons', $cache, 'widget' );
		}

		function update( $new_instance, $old_instance ) {
			$instance                = $old_instance;
			$instance['title']       = strip_tags( $new_instance['title'] );
			$instance['icons_color'] = $new_instance['icons_color'];
			$instance['icons_color_hover'] = $new_instance['icons_color_hover'];

			$this->flush_midget_cache();

			$alloptions = wp_cache_get( 'alloptions', 'options' );
			if ( isset( $alloptions['widget_a13_social_icons'] ) ) {
				delete_option( 'widget_a13_social_icons' );
			}

			return $instance;
		}

		function flush_midget_cache() {
			wp_cache_delete( 'widget_a13_social_icons', 'widget' );
		}

		function form( $instance ) {
			$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
			$color = isset( $instance['icons_color'] ) ? esc_attr( $instance['icons_color'] ) : '';
			$color_hover = isset( $instance['icons_color_hover'] ) ? esc_attr( $instance['icons_color_hover'] ) : '';
			$options = array(
				'black'            => esc_html__( 'Black', 'apollo13-framework-extensions' ),
				'color'            => esc_html__( 'Color', 'apollo13-framework-extensions' ),
				'white'            => esc_html__( 'White', 'apollo13-framework-extensions' ),
				'semi-transparent' => esc_html__( 'Semi transparent', 'apollo13-framework-extensions' ),
			);
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'apollo13-framework-extensions' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'icons_color' ) ); ?>"><?php esc_html_e( 'Normal color', 'apollo13-framework-extensions' ); ?></label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'icons_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'icons_color' ) ); ?>">
					<?php foreach($options as $id => $name){
						echo '<option value="'.esc_attr( $id ).'"'.selected( $color, $id ).'>'.esc_html( $name ).'</option>';
					} ?>
				</select>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'icons_color_hover' ) ); ?>"><?php esc_html_e( 'Hover color', 'apollo13-framework-extensions' ); ?></label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'icons_color_hover' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'icons_color_hover' ) ); ?>">
					<?php foreach($options as $id => $name){
						echo '<option value="'.esc_attr( $id ).'"'.selected( $color_hover, $id ).'>'.esc_html( $name ).'</option>';
					} ?>
				</select>
			</p>
			<?php
		}
	}

	register_widget( 'A13fe_Widget_Social_Icons' );
}

add_action( 'widgets_init', 'a13fe_register_text_widgets' );