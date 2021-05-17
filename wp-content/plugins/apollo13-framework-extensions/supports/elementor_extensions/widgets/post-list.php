<?php
namespace Apollo13_FE\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Post_List extends Widget_Base {

	public function get_name() {
		return 'a13fe-post-list';
	}

	public function get_title() {
		return __( 'Theme Post List', 'apollo13-framework-extensions' );
	}

	public function get_icon() {
		return 'eicon-post-list';
	}

	public function get_categories() {
		return [ 'general', 'apollo13-framework' ];
	}

	private function add_taxonomies(){

		$album_tax  = defined( 'A13FRAMEWORK_CPT_ALBUM_TAXONOMY' ) ? A13FRAMEWORK_CPT_ALBUM_TAXONOMY : 'genre';
		$work_tax  = defined( 'A13FRAMEWORK_CPT_WORK_TAXONOMY' ) ? A13FRAMEWORK_CPT_WORK_TAXONOMY : 'work_genre';
		$people_tax  = defined( 'A13FRAMEWORK_CPT_PEOPLE_TAXONOMY' ) ? A13FRAMEWORK_CPT_PEOPLE_TAXONOMY : 'group';


		$taxonomies = [
			'category' => get_taxonomy('category'),
			$album_tax => get_taxonomy($album_tax),
			$work_tax => get_taxonomy($work_tax),
			$people_tax => get_taxonomy($people_tax),
		];

		foreach ( $taxonomies as $taxonomy => $object ) {
			$taxonomy_args = [
				'label' => $object->label,
				'description' => __( 'Categories in which items must be. By default, all items will be displayed.', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'object_type' => $taxonomy,
				'options' => [],
				'condition' => [
					'post_type' => $object->object_type,
				],
			];

			$options = [];

			$terms = get_terms( [
				'taxonomy' => $taxonomy,
				'hide_empty' => false,
			] );

			foreach ( $terms as $term ) {
				$options[ $term->term_id ] = $term->name;
			}

			$taxonomy_args['options'] = $options;

			$this->add_control(
				'taxonomy_'.$object->object_type[0],
				$taxonomy_args
			);
		}
	}

	private function default_filter(){
		function_exists( 'apollo13framework_cpt_categories_list' );

		$taxonomies = [
			'post', 'album', 'work', 'people',
		];

		foreach ( $taxonomies as $taxonomy ) {
			$taxonomy_args = [
				'label' => esc_html__( 'Default filter', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => false,
				'options' => [],
				'default' => '__all',
				'condition' => [
					'filter' => 'yes',
					'post_type' => $taxonomy,
				],
			];

			$options = apollo13framework_cpt_categories_list( $taxonomy );

			$taxonomy_args['options'] = $options;

			$this->add_control(
				'terms_of_'.$taxonomy,
				$taxonomy_args
			);
		}
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'post_list_settings',
			[
				'label' => __( 'Data source settings', 'apollo13-framework-extensions' ),
			]
		);

		$this->add_control(
			'post_type',
			[
				'label' => __( 'Post type', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'default' => "post",
				'options' => [
					'post'   => __( 'Post', 'apollo13-framework-extensions' ),
					'album'  => __( 'Album', 'apollo13-framework-extensions' ),
					'work'   => __( 'Work', 'apollo13-framework-extensions' ),
					'people' => __( 'People', 'apollo13-framework-extensions' )
				],
			]
		);

		$this->add_taxonomies();

		$this->add_control(
			'category_operator',
			[
				'label' => __( 'Categories operator', 'apollo13-framework-extensions' ),
				'description' => __( 'If you have selected more than one category, select whether the items must have all categories(AND) or at least one of them(IN).', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::SELECT,
				'label_block' => true,
				'default' => "IN",
				'options' => [
					'IN'     => __( 'IN', 'apollo13-framework-extensions' ),
					'AND'  => __( 'AND', 'apollo13-framework-extensions' ),
				],
			]
		);


		$this->add_control(
			'order',
			[
				'label'       => __( 'Order', 'apollo13-framework-extensions' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'separator'   => 'before',
				'default'     => "asc",
				'options'     => [
					'asc'  => __( 'Ascending', 'apollo13-framework-extensions' ),
					'desc' => __( 'Descending', 'apollo13-framework-extensions' ),
				],
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'       => __( 'Order by', 'apollo13-framework-extensions' ),
				'description' => __( 'If the value is left as "Not Set", the plugins can affect the ordering.', 'apollo13-framework-extensions' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'default'     => "",
				'options'     => [
					''              => __( 'Not Set', 'apollo13-framework-extensions' ),
					'date'          => __( 'Date', 'apollo13-framework-extensions' ),
					'title'         => __( 'Title', 'apollo13-framework-extensions' ),
					'name'          => __( 'Slug', 'apollo13-framework-extensions' ),
					'rand'          => __( 'Random', 'apollo13-framework-extensions' ),
					'modified'      => __( 'Modified date', 'apollo13-framework-extensions' ),
					'comment_count' => __( 'Comments number', 'apollo13-framework-extensions' ),
				],
			]
		);


		$this->add_control(
			'posts_number',
			[
				'label' => __( 'Posts Number', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 9,
				'separator'   => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'filter_settings',
			[
				'label' => __( 'Filter', 'apollo13-framework-extensions' ),
			]
		);

		$this->add_control(
			'filter',
			[
				'label' => __( 'Filter', 'apollo13-framework-extensions' ),
				'description' => __( 'Useful only if the items have categories.', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);

		//only if Pro version is used
		if( function_exists( 'apollo13framework_cpt_categories_list' ) ){
			$this->default_filter();

			$this->add_control(
				'all_filter',
				[
					'label'     => esc_html__( 'Display "All" filter', 'apollo13-framework-extensions' ),
					'type'      => Controls_Manager::SWITCHER,
					'default'   => 'yes',
					'condition' => [
						'filter' => 'yes',
					],
				]
			);
		}

		$this->end_controls_section();

		$this->start_controls_section(
			'layout_settings',
			[
				'label' => __( 'Layout', 'apollo13-framework-extensions' ),
			]
		);

		$this->add_control(
			'columns',
			[
				'label' => __( 'Columns', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 3,
					'unit' => 'cols'
				],
				'range' => [
					'cols' => [
						'min' => 1,
						'max' => 4,
						'step' => 1,
					],
				],
			]
		);


		$this->add_control(
			'margin',
			[
				'label' => __( 'Margin', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::SLIDER,
				'description' => __( 'Space between bricks.', 'apollo13-framework-extensions' ),
				'default' => [
					'size' => 5,
					'unit' => 'px'
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
			]
		);

		$this->add_control(
			'max_width',
			[
				'label' => __( 'Max width', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::SLIDER,
				'description' => __( 'Depending on the actual width of the screen, the available space for bricks may be smaller, but never greater than this number.', 'apollo13-framework-extensions' ),
				'default' => [
					'size' => 1920,
					'unit' => 'px'
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 2500,
						'step' => 1,
					],
				],
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$type = $settings['post_type'];
		$order = $settings['order'];
		$orderby = $settings['orderby'];
		$posts = $settings['posts_number'];
		$columns = $settings['columns']['size'];
		$categories = empty( $settings['taxonomy_'.$type] ) ? [] : $settings['taxonomy_'.$type];
		$category_operator = $settings['category_operator'];
		$filter = $settings['filter'] === 'yes';
		$max_width = $settings['max_width']['size'];
		$margin = $settings['margin']['size'];

		// define query parameters based on attributes
		$options = array(
			'post_type'           => $type,
			'order'               => $order,
			'posts_per_page'      => $posts,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
		);

		//add orderby only if needed so plugins sorting CPT could act
		if( strlen($orderby) ){
			$options['orderby'] = $orderby;
		}

		//do not list excluded custom post types
		if( $type === 'album' ){
			$options['meta_key']   = '_exclude_in_albums_list';
			$options['meta_value'] = 'off';
		}
		elseif( $type === 'work' ){
			$options['meta_key']   = '_exclude_in_works_list';
			$options['meta_value'] = 'off';
		}

		//define custom post types & taxonomies from theme
		$a13_custom_types = array(
			defined( 'A13FRAMEWORK_CUSTOM_POST_TYPE_ALBUM' ) ? A13FRAMEWORK_CUSTOM_POST_TYPE_ALBUM : 'album',
			defined( 'A13FRAMEWORK_CUSTOM_POST_TYPE_PEOPLE' ) ? A13FRAMEWORK_CUSTOM_POST_TYPE_PEOPLE : 'people',
			defined( 'A13FRAMEWORK_CUSTOM_POST_TYPE_WORK' ) ? A13FRAMEWORK_CUSTOM_POST_TYPE_WORK : 'work',
		);
		$a13_custom_taxonomies = array(
			'album' => defined( 'A13FRAMEWORK_CPT_ALBUM_TAXONOMY' ) ? A13FRAMEWORK_CPT_ALBUM_TAXONOMY : 'genre',
			'work' => defined( 'A13FRAMEWORK_CPT_WORK_TAXONOMY' ) ? A13FRAMEWORK_CPT_WORK_TAXONOMY : 'work_genre',
			'people' => defined( 'A13FRAMEWORK_CPT_PEOPLE_TAXONOMY' ) ? A13FRAMEWORK_CPT_PEOPLE_TAXONOMY : 'group'
		);

		//filtering by category name
		if(count($categories)){
			//if querying custom post type
			if(in_array($type, $a13_custom_types)){
				$tax_query['field']    = 'term_id';
				$tax_query['taxonomy'] = $a13_custom_taxonomies[ $type ];
				$tax_query['terms']    = $categories;
				$tax_query['operator'] = $category_operator;

				$options['tax_query'] = array( $tax_query );
			}
			//simple post or unknown post type
			else{
				$options[ ($category_operator === 'AND' ? 'category__and' : 'category__in' ) ] = $categories;
			}
		}

		//make query
		$query = new \WP_Query( $options );

		$args = array(
			'columns' => $columns,
			'filter' => $filter,
			'display_post_id' => false,
			'max_width' => $max_width,
			'margin' => $margin
		);

		if( function_exists( 'apollo13framework_cpt_categories_list' ) ){
			$default_filter = empty( $settings['terms_of_'.$type] ) ? '__all' : $settings['terms_of_'.$type];
			$all_filter = $settings['all_filter'] === 'yes';
			$args['default_filter'] = $default_filter;
			$args['all_filter'] = $all_filter;
		}

		//check for special post types
		if(in_array($type, $a13_custom_types)){
			$function_name = 'apollo13framework_display_items_from_query_'.$type.'_list';
			if(function_exists($function_name)){
				$function_name($query, $args);
			}
		}
		//simple post or unknown post type
		else{
			$options['category_name'] = $categories;
			if(function_exists('apollo13framework_display_items_from_query_post_list')){
				apollo13framework_display_items_from_query_post_list($query, $args);
			}
		}

		// Reset Post Data
		wp_reset_postdata();
	}

}
