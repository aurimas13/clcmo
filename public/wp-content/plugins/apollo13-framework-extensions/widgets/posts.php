<?php
/**
 * @param WP_Query $r        post query
 * @param array    $instance current widget options
 * @param string   $type     type of widget it will be used for
 * @since 1.2.0
 */
function a13fe_widget_posts( $r, $instance, $type = 'normal' ) {
	while ( $r->have_posts() ) : $r->the_post();
		$page_title = get_the_title();

		echo '<div class="item">';

		echo '<a class="post-title" href="' . esc_url( get_permalink() ) . '" title="' . esc_attr( $page_title ) . '">' . esc_html( $page_title ) . '</a>';
		if ( $type === 'popular' ) {
			$comments_number = get_comments_number();
			/* translators: %d - number of comments */
			$comments_string = sprintf( esc_html( _n( '%d comment', '%d comments', $comments_number, 'apollo13-framework-extensions' ) ), esc_html( $comments_number ) );
			echo '<a class="comments" href="' . esc_url( get_comments_link() ) . '" title="' . esc_attr( $comments_string ) .'">' . esc_html( $comments_string ) . '</a>';

		} else {
			echo function_exists('apollo13framework_posted_on') ? apollo13framework_posted_on() : '';
		}

		//if user want excerpt also and post is not password protected
		if ( ! empty( $instance['content'] ) && ! post_password_required() ) {
			echo '<a class="content" href="' . esc_url( get_permalink() ) . '" title="' . esc_attr( $page_title ) . '">';
			$text = get_the_content( '' );
			$text = strip_shortcodes( $text );
			$text = wp_trim_words( $text, 30, '' );
			echo esc_html($text);
			echo '</a>';
		}
		echo '</div>';

	endwhile;
}

/**
 * Adds post widgets: Recent, Popular, Related
 * @since 1.2.0
 */
function a13fe_register_posts_widgets(){

	class A13fe_Widget_Recent_Posts extends WP_Widget {

		function __construct() {
			$widget_ops = array( 'classname'   => 'widget_recent_posts widget_about_posts',
			                     'description' => esc_html__( 'The most recent posts on your site', 'apollo13-framework-extensions' )
			);
			parent::__construct( 'recent-posts', esc_html__( 'Apollo13Themes', 'apollo13-framework-extensions' ) . ' - ' . esc_html__( 'Recent Posts', 'apollo13-framework-extensions' ), $widget_ops );
			$this->alt_option_name = 'widget_recent_entries';

			add_action( 'save_post', array( &$this, 'flush_midget_cache' ) );
			add_action( 'deleted_post', array( &$this, 'flush_midget_cache' ) );
			add_action( 'switch_theme', array( &$this, 'flush_midget_cache' ) );
		}

		function widget( $args, $instance ) {
			$before_widget = $after_widget = $before_title = $after_title = '';
			$cache         = wp_cache_get( 'widget_recent_entries', 'widget' );

			if ( ! is_array( $cache ) ) {
				$cache = array();
			}

			if ( isset( $cache[ $args['widget_id'] ] ) ) {
				print $cache[ $args['widget_id'] ];

				return;
			}

			ob_start();
			extract( $args );

			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? esc_html__( 'Recent Posts', 'apollo13-framework-extensions' ) : $instance['title'], $instance, $this->id_base );
			if ( ! $number = absint( $instance['number'] ) ) {
				$number = 10;
			}

			$r = new WP_Query( array( 'posts_per_page'      => $number,
			                          'no_found_rows'       => true,
			                          'post_status'         => 'publish',
			                          'ignore_sticky_posts' => true
			) );
			if ( $r->have_posts() ) :
				print $before_widget;

				if ( $title ) {
					print $before_title . $title . $after_title;
				}

				a13fe_widget_posts( $r, $instance );

				print $after_widget;

				// Reset the global $the_post as this query will have stomped on it
				wp_reset_postdata();

			endif;

			$cache[ $args['widget_id'] ] = ob_get_flush();
			wp_cache_set( 'widget_recent_entries', $cache, 'widget' );
		}

		function update( $new_instance, $old_instance ) {
			$instance            = $old_instance;
			$instance['title']   = strip_tags( $new_instance['title'] );
			$instance['number']  = (int) $new_instance['number'];
			$instance['content'] = isset( $new_instance['content'] );

			$this->flush_midget_cache();

			$alloptions = wp_cache_get( 'alloptions', 'options' );
			if ( isset( $alloptions['widget_recent_entries'] ) ) {
				delete_option( 'widget_recent_entries' );
			}

			return $instance;
		}

		function flush_midget_cache() {
			wp_cache_delete( 'widget_recent_entries', 'widget' );
		}

		function form( $instance ) {
			$title  = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
			$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'apollo13-framework-extensions' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number of posts to show', 'apollo13-framework-extensions' ); ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" />
			</p>

			<p>
				<input id="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'content' ) ); ?>" type="checkbox" <?php checked( isset( $instance['content'] ) ? $instance['content'] : 0 ); ?> />&nbsp;<label for="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>"><?php esc_html_e( 'Add posts excerpt', 'apollo13-framework-extensions' ); ?></label>
			</p>
			<?php
		}
	}

	register_widget( 'A13fe_Widget_Recent_Posts' );

	class A13fe_Widget_Popular_Posts extends WP_Widget {

		function __construct() {
			$widget_ops = array( 'classname'   => 'widget_popular_entries widget_about_posts',
			                     'description' => esc_html__( 'The most popular posts on your site', 'apollo13-framework-extensions' )
			);
			parent::__construct( 'popular-posts', esc_html__( 'Apollo13Themes', 'apollo13-framework-extensions' ) . ' - ' . esc_html__( 'Popular Posts', 'apollo13-framework-extensions' ), $widget_ops );
			$this->alt_option_name = 'widget_popular_entries';

			add_action( 'save_post', array( &$this, 'flush_midget_cache' ) );
			add_action( 'deleted_post', array( &$this, 'flush_midget_cache' ) );
			add_action( 'switch_theme', array( &$this, 'flush_midget_cache' ) );
		}

		function widget( $args, $instance ) {
			$before_widget = $after_widget = $before_title = $after_title = '';
			$cache         = wp_cache_get( 'widget_popular_entries', 'widget' );

			if ( ! is_array( $cache ) ) {
				$cache = array();
			}

			if ( isset( $cache[ $args['widget_id'] ] ) ) {
				print $cache[ $args['widget_id'] ];

				return;
			}

			ob_start();
			extract( $args );

			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? esc_html__( 'Popular Posts', 'apollo13-framework-extensions' ) : $instance['title'], $instance, $this->id_base );
			if ( ! $number = absint( $instance['number'] ) ) {
				$number = 10;
			}

			$r = new WP_Query( array( 'posts_per_page'      => $number,
			                          'no_found_rows'       => true,
			                          'orderby'             => 'comment_count',
			                          'post_status'         => 'publish',
			                          'ignore_sticky_posts' => true,
			                          'ignore_custom_sort'  => true
			) );
			if ( $r->have_posts() ) :
				print $before_widget;

				if ( $title ) {
					print $before_title . $title . $after_title;
				}

				a13fe_widget_posts( $r, $instance, 'popular' );

				print $after_widget;

				// Reset the global $the_post as this query will have stomped on it
				wp_reset_postdata();

			endif;

			$cache[ $args['widget_id'] ] = ob_get_flush();
			wp_cache_set( 'widget_popular_entries', $cache, 'widget' );
		}

		function update( $new_instance, $old_instance ) {
			$instance            = $old_instance;
			$instance['title']   = strip_tags( $new_instance['title'] );
			$instance['number']  = (int) $new_instance['number'];
			$instance['content'] = isset( $new_instance['content'] );

			$this->flush_midget_cache();

			$alloptions = wp_cache_get( 'alloptions', 'options' );
			if ( isset( $alloptions['widget_popular_entries'] ) ) {
				delete_option( 'widget_popular_entries' );
			}

			return $instance;
		}

		function flush_midget_cache() {
			wp_cache_delete( 'widget_popular_entries', 'widget' );
		}

		function form( $instance ) {
			$title  = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
			$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'apollo13-framework-extensions' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number of posts to show', 'apollo13-framework-extensions' ); ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" />
			</p>

			<p>
				<input id="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'content' ) ); ?>" type="checkbox" <?php checked( isset( $instance['content'] ) ? $instance['content'] : 0 ); ?> />&nbsp;<label for="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>"><?php esc_html_e( 'Add posts excerpt', 'apollo13-framework-extensions' ); ?></label>
			</p>
			<?php
		}
	}

	register_widget( 'A13fe_Widget_Popular_Posts' );


	class A13fe_Widget_Related_Posts extends WP_Widget {

		function __construct() {
			$widget_ops = array( 'classname'   => 'widget_related_entries widget_about_posts',
			                     'description' => esc_html__( 'Related posts to current post', 'apollo13-framework-extensions' )
			);
			parent::__construct( 'related-posts', esc_html__( 'Apollo13Themes', 'apollo13-framework-extensions' ) . ' - ' . esc_html__( 'Related Posts', 'apollo13-framework-extensions' ), $widget_ops );
			$this->alt_option_name = 'widget_related_entries';

			add_action( 'save_post', array( &$this, 'flush_midget_cache' ) );
			add_action( 'deleted_post', array( &$this, 'flush_midget_cache' ) );
			add_action( 'switch_theme', array( &$this, 'flush_midget_cache' ) );
		}

		function widget( $args, $instance ) {
			$before_widget = $after_widget = $before_title = $after_title = '';
			$cache         = wp_cache_get( 'widget_related_entries', 'widget' );

			if ( ! is_array( $cache ) ) {
				$cache = array();
			}

			if ( isset( $cache[ $args['widget_id'] ] ) ) {
				print $cache[ $args['widget_id'] ];

				return;
			}

			ob_start();
			extract( $args );

			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? esc_html__( 'Related Posts', 'apollo13-framework-extensions' ) : $instance['title'], $instance, $this->id_base );
			if ( ! $number = absint( $instance['number'] ) ) {
				$number = 10;
			}

			global $post;

			$__search      = wp_get_post_tags( $post->ID );
			$search_string = 'tags__in';
			//if no tags try categories
			if ( ! count( $__search ) ) {
				$__search      = wp_get_post_categories( $post->ID );
				$search_string = 'category__in';
			}

			if ( count( $__search ) ) {

				$r = new WP_Query( array( $search_string        => $__search,
				                          'post__not_in'        => array( $post->ID ),
				                          'posts_per_page'      => $number,
				                          'no_found_rows'       => true,
				                          'post_status'         => 'publish',
				                          'ignore_sticky_posts' => true
				) );
				if ( $r->have_posts() ) :
					print $before_widget;

					if ( $title ) {
						print $before_title . $title . $after_title;
					}

					a13fe_widget_posts( $r, $instance );

					print $after_widget;

					// Reset the global $the_post as this query will have stomped on it
					wp_reset_postdata();

				endif;

			}

			$cache[ $args['widget_id'] ] = ob_get_flush();
			wp_cache_set( 'widget_related_entries', $cache, 'widget' );
		}

		function update( $new_instance, $old_instance ) {
			$instance            = $old_instance;
			$instance['title']   = strip_tags( $new_instance['title'] );
			$instance['number']  = (int) $new_instance['number'];
			$instance['content'] = isset( $new_instance['content'] );

			$this->flush_midget_cache();

			$alloptions = wp_cache_get( 'alloptions', 'options' );
			if ( isset( $alloptions['widget_related_entries'] ) ) {
				delete_option( 'widget_related_entries' );
			}

			return $instance;
		}

		function flush_midget_cache() {
			wp_cache_delete( 'widget_related_entries', 'widget' );
		}

		function form( $instance ) {
			$title  = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
			$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'apollo13-framework-extensions' ); ?></label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number of posts to show', 'apollo13-framework-extensions' ); ?></label>
				<input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="3" />
			</p>

			<p>
				<input id="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'content' ) ); ?>" type="checkbox" <?php checked( isset( $instance['content'] ) ? $instance['content'] : 0 ); ?> />&nbsp;<label for="<?php echo esc_attr( $this->get_field_id( 'content' ) ); ?>"><?php esc_html_e( 'Add posts excerpt', 'apollo13-framework-extensions' ); ?></label>
			</p>
			<?php
		}
	}

	register_widget( 'A13fe_Widget_Related_Posts' );
}

add_action( 'widgets_init', 'a13fe_register_posts_widgets' );