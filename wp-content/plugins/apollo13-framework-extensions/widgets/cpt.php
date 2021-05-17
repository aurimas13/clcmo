<?php

/**
 * Adds custom post type widgets: Recent Works, Recent Albums, Filter
 * @since 1.2.0
 */
function a13fe_register_cpt_widgets(){

	class A13fe_Widget_Recent_Albums extends WP_Widget {

		function __construct() {
			$widget_ops = array( 'classname'   => 'widget_recent_albums widget_recent_cpt',
			                     'description' => esc_html__( 'Your most recent added albums', 'apollo13-framework-extensions' )
			);
			parent::__construct( 'recent-albums', esc_html__( 'Apollo13Themes', 'apollo13-framework-extensions' ) . ' - ' . esc_html__( 'Recent Albums', 'apollo13-framework-extensions' ), $widget_ops );
			$this->alt_option_name = 'widget_recent_albums';

			add_action( 'save_post', array( &$this, 'flush_midget_cache' ) );
			add_action( 'deleted_post', array( &$this, 'flush_midget_cache' ) );
			add_action( 'switch_theme', array( &$this, 'flush_midget_cache' ) );
		}

		function widget( $args, $instance ) {
			$before_widget = $after_widget = $before_title = $after_title = '';
			$cache         = wp_cache_get( 'widget_recent_albums', 'widget' );

			if ( ! is_array( $cache ) ) {
				$cache = array();
			}

			if ( isset( $cache[ $args['widget_id'] ] ) ) {
				print $cache[ $args['widget_id'] ];

				return;
			}

			ob_start();
			extract( $args );

			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? esc_html__( 'Recent Albums', 'apollo13-framework-extensions' ) : $instance['title'], $instance, $this->id_base );
			if ( ! $number = absint( $instance['number'] ) ) {
				$number = 10;
			}

			$r = new WP_Query( array(
				'posts_per_page'      => $number,
				'no_found_rows'       => true,
				'post_type'           => defined( 'A13FRAMEWORK_CUSTOM_POST_TYPE_ALBUM' ) ? A13FRAMEWORK_CUSTOM_POST_TYPE_ALBUM : 'album',
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'orderby'             => 'date'
			) );
			if ( $r->have_posts() ) :
				print $before_widget;

				if ( $title ) {
					print $before_title . $title . $after_title;
				}

				echo '<div class="items clearfix">';

				while ( $r->have_posts() ) : $r->the_post();
					//title
					$page_title = get_the_title();

					//image
					$img = function_exists('apollo13framework_make_album_image') ? apollo13framework_make_album_image( get_the_ID(), array( 100, 100 ) ) : '';
					echo '<div class="item"><a href="' . esc_url( get_permalink() ) . '" title="' . esc_attr( $page_title ) . '">' . $img . '</a></div>';

				endwhile;

				echo '</div>';

				print $after_widget;

				// Reset the global $the_post as this query will have stomped on it
				wp_reset_postdata();

			endif;

			$cache[ $args['widget_id'] ] = ob_get_flush();
			wp_cache_set( 'widget_recent_albums', $cache, 'widget' );
		}

		function update( $new_instance, $old_instance ) {
			$instance           = $old_instance;
			$instance['title']  = strip_tags( $new_instance['title'] );
			$instance['number'] = (int) $new_instance['number'];

			$this->flush_midget_cache();

			$alloptions = wp_cache_get( 'alloptions', 'options' );
			if ( isset( $alloptions['widget_recent_albums'] ) ) {
				delete_option( 'widget_recent_albums' );
			}

			return $instance;
		}

		function flush_midget_cache() {
			wp_cache_delete( 'widget_recent_albums', 'widget' );
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
			<?php
		}
	}

	register_widget( 'A13fe_Widget_Recent_Albums' );


	class A13fe_Widget_Recent_Works extends WP_Widget {

		function __construct() {
			$widget_ops = array( 'classname'   => 'widget_recent_works widget_recent_cpt',
			                     'description' => esc_html__( 'Your most recent added works', 'apollo13-framework-extensions' )
			);
			parent::__construct( 'recent-works', esc_html__( 'Apollo13Themes', 'apollo13-framework-extensions' ) . ' - ' . esc_html__( 'Recent Works', 'apollo13-framework-extensions' ), $widget_ops );
			$this->alt_option_name = 'widget_recent_works';

			add_action( 'save_post', array( &$this, 'flush_midget_cache' ) );
			add_action( 'deleted_post', array( &$this, 'flush_midget_cache' ) );
			add_action( 'switch_theme', array( &$this, 'flush_midget_cache' ) );
		}

		function widget( $args, $instance ) {
			$before_widget = $after_widget = $before_title = $after_title = '';
			$cache         = wp_cache_get( 'widget_recent_works', 'widget' );

			if ( ! is_array( $cache ) ) {
				$cache = array();
			}

			if ( isset( $cache[ $args['widget_id'] ] ) ) {
				print $cache[ $args['widget_id'] ];

				return;
			}

			ob_start();
			extract( $args );

			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? esc_html__( 'Recent Works', 'apollo13-framework-extensions' ) : $instance['title'], $instance, $this->id_base );
			if ( ! $number = absint( $instance['number'] ) ) {
				$number = 10;
			}

			$r = new WP_Query( array(
				'posts_per_page'      => $number,
				'no_found_rows'       => true,
				'post_type'           => defined( 'A13FRAMEWORK_CUSTOM_POST_TYPE_WORK' ) ? A13FRAMEWORK_CUSTOM_POST_TYPE_WORK : 'work',
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'orderby'             => 'date'
			) );
			if ( $r->have_posts() ) :
				print $before_widget;

				if ( $title ) {
					print $before_title . $title . $after_title;
				}

				echo '<div class="items clearfix">';

				while ( $r->have_posts() ) : $r->the_post();
					//title
					$page_title = get_the_title();

					//image
					$img = function_exists('apollo13framework_make_work_image') ? apollo13framework_make_work_image( get_the_ID(), array( 100, 100 ) ) : '';
					echo '<div class="item"><a href="' . esc_url( get_permalink() ) . '" title="' . esc_attr( $page_title ) . '">' . $img . '</a></div>';

				endwhile;

				echo '</div>';

				print $after_widget;

				// Reset the global $the_post as this query will have stomped on it
				wp_reset_postdata();

			endif;

			$cache[ $args['widget_id'] ] = ob_get_flush();
			wp_cache_set( 'widget_recent_works', $cache, 'widget' );
		}

		function update( $new_instance, $old_instance ) {
			$instance           = $old_instance;
			$instance['title']  = strip_tags( $new_instance['title'] );
			$instance['number'] = (int) $new_instance['number'];

			$this->flush_midget_cache();

			$alloptions = wp_cache_get( 'alloptions', 'options' );
			if ( isset( $alloptions['widget_recent_works'] ) ) {
				delete_option( 'widget_recent_works' );
			}

			return $instance;
		}

		function flush_midget_cache() {
			wp_cache_delete( 'widget_recent_works', 'widget' );
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
			<?php
		}
	}

	register_widget( 'A13fe_Widget_Recent_Works' );

	/** don't register widget is theme is not available. Widget relies strongly on theme to use filter.
	 * It is useless without theme.
	 */
	if(function_exists('apollo13framework_what_page_type_is_it') ){
		class A13fe_Widget_Filter extends WP_Widget {

			function __construct() {
				$widget_ops = array( 'classname'   => 'widget_filter',
				                     'description' => esc_html__( 'Filter Albums, Works or Posts by categories.', 'apollo13-framework-extensions' )
				);
				parent::__construct( 'filter', esc_html__( 'Apollo13Themes', 'apollo13-framework-extensions' ) . ' - ' . esc_html__( 'Filter', 'apollo13-framework-extensions' ), $widget_ops );
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

				$page_type = apollo13framework_what_page_type_is_it();
				//get field
				$filter      = $instance['filter'];
				$albums_list = $filter === 'albums' && $page_type['albums_list'];
				$works_list = $filter === 'works' && $page_type['works_list'];
				$posts_list  = $filter === 'blog' && $page_type['blog_type'];

				if($page_type['search']){
					//we don't display filter on search reasult page
					return;
				}

				//filter will be usable here?
				if ( $albums_list || $works_list || $posts_list ) {
					$terms = array();
					//albums
					if ( $albums_list ) {
						$category_template = defined( 'A13FRAMEWORK_ALBUM_GENRE_TEMPLATE' );

						//prepare filter
						$query_args = array(
							'taxonomy' => A13FRAMEWORK_CPT_ALBUM_TAXONOMY,
							'hide_empty' => true,
							'parent'     => 0,
						);

						if ( $category_template === true ) {
							$term_slug = get_query_var( 'term' );
							if ( ! empty( $term_slug ) ) {
								$term_obj             = get_term_by( 'slug', $term_slug, A13FRAMEWORK_CPT_ALBUM_TAXONOMY );
								$term_id              = $term_obj->term_id;
								$query_args['parent'] = $term_id;
							}
						}
						$terms = get_terms( $query_args );
					}
					//works
					if ( $works_list ) {
						$category_template = defined( 'A13FRAMEWORK_WORK_GENRE_TEMPLATE' );

						//prepare filter
						$query_args = array(
							'taxonomy' => A13FRAMEWORK_CPT_WORK_TAXONOMY,
							'hide_empty' => true,
							'parent'     => 0,
						);

						if ( $category_template === true ) {
							$term_slug = get_query_var( 'term' );
							if ( ! empty( $term_slug ) ) {
								$term_obj             = get_term_by( 'slug', $term_slug, A13FRAMEWORK_CPT_WORK_TAXONOMY );
								$term_id              = $term_obj->term_id;
								$query_args['parent'] = $term_id;
							}
						}
						$terms = get_terms( $query_args );
					}
					//blog
					elseif ( $posts_list ) {
						$category_template = is_category();

						$query_args = array(
							'hide_empty' => true,
							'parent'     => 0,
						);

						if ( $category_template === true ) {
							$term_id = get_query_var( 'cat' );
							if ( ! empty( $term_id ) ) {
								$query_args['parent'] = $term_id;
							}
						}

						$terms = get_categories( $query_args );
					}

					if ( count( $terms ) ):
						$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? esc_html__( 'Filter', 'apollo13-framework-extensions' ) : $instance['title'], $instance, $this->id_base );

						print $before_widget;

						if ( $title ) {
							print $before_title . $title . $after_title;
						}


						echo '<ul class="' . esc_attr( $filter ) . '-filter">';

						echo '<li class="selected" data-filter="__all"><a href="' . esc_url( apollo13framework_current_url() ) . '"><i class="fa fa-square-o"></i>' . esc_html__( 'All', 'apollo13-framework-extensions' ) . '</a></li>';
						foreach ( $terms as $term ) {
							echo '<li data-filter="' . esc_attr( $term->term_id ) . '"><a href="' . esc_url( get_term_link( $term ) ) . '"><i class="fa fa-square-o"></i>' . esc_html( $term->name ) . '</a></li>';
						}

						echo '</ul>';

						print $after_widget;
					endif;


				}

				$cache[ $args['widget_id'] ] = ob_get_flush();
				wp_cache_set( 'widget_related_entries', $cache, 'widget' );
			}

			function update( $new_instance, $old_instance ) {
				$instance           = $old_instance;
				$instance['filter'] = $new_instance['filter'];

				$this->flush_midget_cache();

				$alloptions = wp_cache_get( 'alloptions', 'options' );
				if ( isset( $alloptions['widget_filter'] ) ) {
					delete_option( 'widget_filter' );
				}

				return $instance;
			}

			function flush_midget_cache() {
				wp_cache_delete( 'widget_filter', 'widget' );
			}

			function form( $instance ) {
				$filter  = isset( $instance['filter'] ) ? esc_attr( $instance['filter'] ) : '';
				$options = array(
					'blog'   => esc_html__( 'Blog - will appear in the sidebar only if the current page is the main blog page.', 'apollo13-framework-extensions' ),
					'albums' => esc_html__( 'Albums - will appear the in sidebar only if the current page is the Albums list, or category of Albums.', 'apollo13-framework-extensions' ),
					'works' => esc_html__( 'Works - will appear in the sidebar only if the current page is the Works list, or category of Works.', 'apollo13-framework-extensions' )
				);
				?>
				<p><?php esc_html_e( 'Filter', 'apollo13-framework-extensions' ); ?>:</p>
				<?php
				foreach ( $options as $id => $name ) {
					echo '<p><label><input class="widefat" name="' . esc_attr( $this->get_field_name( 'filter' ) ) . '" type="radio" value="' . esc_attr( $id ) . '" ' . ( $id === $filter ? 'checked="checked"' : '' ) . ' />' . esc_html( $name ) . '</label></p>';
				}
				?>

				<?php
			}
		}

		register_widget( 'A13fe_Widget_Filter' );
	}
}

add_action( 'widgets_init', 'a13fe_register_cpt_widgets' );