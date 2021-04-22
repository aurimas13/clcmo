<?php
// create shortcode for post list
add_shortcode( 'a13fe-post-list', 'a13fe_post_list_shortcode' );
function a13fe_post_list_shortcode( $atts ) {
	ob_start();

	$type = '';
	$order = '';
	$orderby = '';
	$posts = '';
	$columns = '';
	$category = '';
	$filter = '';
	$max_width = '';
	$margin = '';

	// define attributes and their defaults
	extract( shortcode_atts( array (
		'type' => 'post',
		'order' => 'ASC',
		'orderby' => 'date',
		'posts' => 9,
		'columns' => 3,
		'max_width' => 1920,
		'margin'    => 10,
		'category' => '',
		'filter' => false,
	), $atts ) );

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
	if(strlen($category)){
		//if querying custom post type
		if(in_array($type, $a13_custom_types)){
			$tax_query['field']    = 'slug';
			$tax_query['taxonomy'] = $a13_custom_taxonomies[ $type ];

			//OR operator
			if(strpos($category,',')){
				$tax_query['terms'] = explode(',', $category);
			}
			//AND operator
			elseif(strpos($category,'+')){
				$tax_query['terms'] = explode('+', $category);
				$tax_query['operator'] = 'AND';
			}
			//single category
			else{
				$tax_query['terms'] = $category;
			}

			$options['tax_query'] = array($tax_query);
		}
		//simple post or unknown post type
		else{
			$options['category_name'] = $category;
		}
	}

	//make query
	$query = new WP_Query( $options );

	$args = array(
		'columns' => $columns,
		'filter' => $filter,
		'display_post_id' => false,
		'max_width' => $max_width,
		'margin' => $margin
	);

	//check for special post types
	if(in_array($type, $a13_custom_types)){
		$function_name = 'apollo13framework_display_items_from_query_'.$type.'_list';
		if(function_exists($function_name)){
			$function_name($query, $args);
		}
	}
	//simple post or unknown post type
	else{
		$options['category_name'] = $category;
		if(function_exists('apollo13framework_display_items_from_query_post_list')){
			apollo13framework_display_items_from_query_post_list($query, $args);
		}
	}

	// Reset Post Data
	wp_reset_postdata();

	$output = ob_get_clean();

	return $output;
}