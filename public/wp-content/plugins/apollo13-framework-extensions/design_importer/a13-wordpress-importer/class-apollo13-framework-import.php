<?php
/*
Modified version of "WordPress Importer"  plugin
Plugin URI: https://wordpress.org/extend/plugins/wordpress-importer/
*/


// Load Importer API
require_once ABSPATH . 'wp-admin/includes/import.php';

if ( ! class_exists( 'WP_Importer' ) ) {
	$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
	if ( file_exists( $class_wp_importer ) )
		require $class_wp_importer;
}

// include WXR file parsers
require A13FE_BASE_DIR.'design_importer/a13-wordpress-importer/parsers.php';

/**
 * WordPress Importer class for managing the import process of a WXR file
 *
 * @package WordPress
 * @subpackage Importer
 */

if ( class_exists( 'WP_Importer' ) ) {
class Apollo13Framework_Import extends WP_Importer {
	var $max_wxr_version = 1.2; // max. supported WXR version

	var $id; // WXR attachment ID

	// information to import from WXR file
	var $version;
	var $posts = array();
	var $terms = array();
	var $categories = array();
	var $tags = array();
	var $base_url = '';

	// mappings from old information to new
	var $processed_authors = array();
	var $author_mapping = array();
	var $processed_terms = array();
	var $processed_posts = array();
	var $post_orphans = array();
	var $processed_menu_items = array();
	var $menu_item_orphans = array();
	var $missing_menu_items = array();

	var $fetch_attachments = true;
	var $url_remap = array();
	var $featured_images = array();
	var $max_time_running = 20; //in seconds
	var $last_post_did = 0;

	function __construct() {

	}


	/**
	 * The main controller for the actual import stage.
	 *
	 * @param string $file Path to the WXR file for importing
	 * @param bool   $install_attachments should attachments be imported
	 *
	 * @return bool
	 */
	function import( $file, $install_attachments = true ) {
		add_filter( 'import_post_meta_key', array( $this, 'is_valid_meta_key' ) );
		add_filter( 'http_request_timeout', array( &$this, 'bump_request_timeout' ) );

		$this->fetch_attachments = $install_attachments;
		$this->import_start( $file );

		wp_suspend_cache_invalidation( true );
		$this->process_categories();
		$this->process_tags();
		$this->process_terms();
		$is_done = $this->process_posts();
		wp_suspend_cache_invalidation( false );

		if($is_done === true){

			// update incorrect/missing information in the DB
			$this->backfill_parents();
			$this->backfill_attachment_urls();
			$this->remap_featured_images();

			$this->import_end();
			return true;
		}
		//not done
		return false;
	}

	/**
	 * Parses the WXR file and prepares us for the task of processing parsed data
	 *
	 * @param string $file Path to the WXR file for importing
	 */
	function import_start( $file ) {
		if ( ! is_file($file) ) {
			echo '<p><strong>' . esc_html__( 'Sorry, there has been an error.', 'apollo13-framework-extensions' ) . '</strong> FILE_EXISTS_FAIL</p>';
			die();
		}

		$import_data = $this->parse( $file );

		if ( is_wp_error( $import_data ) ) {
			echo '<p><strong>' . esc_html__( 'Sorry, there has been an error.', 'apollo13-framework-extensions' ) . '</strong><br />';
			/* @var WP_Error $import_data */
			echo esc_html( $import_data->get_error_message() ) . '</p>';
			die();
		}

		$this->version = $import_data['version'];
		$this->posts = $import_data['posts'];
		$this->terms = $import_data['terms'];
		$this->categories = $import_data['categories'];
		$this->tags = $import_data['tags'];
		$this->base_url = esc_url( $import_data['base_url'] );

		wp_defer_term_counting( true );
		wp_defer_comment_counting( true );

		do_action( 'import_start' );
	}

	/**
	 * Performs post-import cleanup of files and the cache
	 */
	function import_end() {
		wp_import_cleanup( $this->id );

		wp_cache_flush();
		foreach ( get_taxonomies() as $tax ) {
			delete_option( "{$tax}_children" );
			_get_term_hierarchy( $tax );
		}

		wp_defer_term_counting( false );
		wp_defer_comment_counting( false );


		do_action( 'import_end' );
	}


	/**
	 * Create new categories based on import information
	 *
	 * Doesn't create a new category if its slug already exists
	 */
	function process_categories() {
		$this->categories = apply_filters( 'wp_import_categories', $this->categories );

		if ( empty( $this->categories ) )
			return;

		foreach ( $this->categories as $cat ) {
			// if the category already exists leave it alone
			$term_id = term_exists( $cat['category_nicename'], 'category' );
			if ( $term_id ) {
				if ( is_array($term_id) ) $term_id = $term_id['term_id'];
				if ( isset($cat['term_id']) )
					$this->processed_terms[(int)$cat['term_id']] = (int) $term_id;
				continue;
			}

			$category_parent = empty( $cat['category_parent'] ) ? 0 : category_exists( $cat['category_parent'] );
			$category_description = isset( $cat['category_description'] ) ? $cat['category_description'] : '';
			$catarr = array(
				'category_nicename' => $cat['category_nicename'],
				'category_parent' => $category_parent,
				'cat_name' => $cat['cat_name'],
				'category_description' => $category_description
			);

			$id = wp_insert_category( $catarr );
			if ( ! is_wp_error( $id ) ) {
				if ( isset($cat['term_id']) )
					$this->processed_terms[(int)$cat['term_id']] = $id;
			} else {
				printf( 'CATEGORY_FAIL %s', esc_html($cat['category_nicename']) );
				echo '<br />';
				continue;
			}
		}

		unset( $this->categories );
	}

	/**
	 * Create new post tags based on import information
	 *
	 * Doesn't create a tag if its slug already exists
	 */
	function process_tags() {
		$this->tags = apply_filters( 'wp_import_tags', $this->tags );

		if ( empty( $this->tags ) )
			return;

		foreach ( $this->tags as $tag ) {
			// if the tag already exists leave it alone
			$term_id = term_exists( $tag['tag_slug'], 'post_tag' );
			if ( $term_id ) {
				if ( is_array($term_id) ) $term_id = $term_id['term_id'];
				if ( isset($tag['term_id']) )
					$this->processed_terms[(int)$tag['term_id']] = (int) $term_id;
				continue;
			}

			$tag_desc = isset( $tag['tag_description'] ) ? $tag['tag_description'] : '';
			$tagarr = array( 'slug' => $tag['tag_slug'], 'description' => $tag_desc );

			$id = wp_insert_term( $tag['tag_name'], 'post_tag', $tagarr );
			if ( ! is_wp_error( $id ) ) {
				if ( isset($tag['term_id']) )
					$this->processed_terms[(int)$tag['term_id']] = $id['term_id'];
			} else {
				/* translators: %$: tag name */
				printf( 'TAG_FAIL %s', esc_html($tag['tag_name']) );
				echo '<br />';
				continue;
			}
		}

		unset( $this->tags );
	}

	/**
	 * Create new terms based on import information
	 *
	 * Doesn't create a term its slug already exists
	 */
	function process_terms() {
		$this->terms = apply_filters( 'wp_import_terms', $this->terms );

		if ( empty( $this->terms ) )
			return;

		foreach ( $this->terms as $term ) {
			// if the term already exists in the correct taxonomy leave it alone
			$term_id = term_exists( $term['slug'], $term['term_taxonomy'] );
			if ( $term_id ) {
				if ( is_array($term_id) ) $term_id = $term_id['term_id'];
				if ( isset($term['term_id']) )
					$this->processed_terms[(int)$term['term_id']] = (int) $term_id;
				continue;
			}

			if ( empty( $term['term_parent'] ) ) {
				$parent = 0;
			} else {
				$parent = term_exists( $term['term_parent'], $term['term_taxonomy'] );
				if ( is_array( $parent ) ) $parent = $parent['term_id'];
			}
			$description = isset( $term['term_description'] ) ? $term['term_description'] : '';
			$termarr = array( 'slug' => $term['slug'], 'description' => $description, 'parent' => (int)$parent );

			$id = wp_insert_term( $term['term_name'], $term['term_taxonomy'], $termarr );
			if ( ! is_wp_error( $id ) ) {
				if ( isset($term['term_id']) )
					$this->processed_terms[(int)$term['term_id']] = $id['term_id'];
			} else {
				/* translators: %1$s: term_taxonomy, 2$s: term_name */
				printf( 'TAX_TERM_FAIL %1$s %2$s', esc_html($term['term_taxonomy']), esc_html($term['term_name']) );
				echo '<br />';
				continue;
			}
		}

		unset( $this->terms );
	}

	/**
	 * Create new posts based on import information
	 *
	 * Posts marked as having a parent which doesn't exist will become top level items.
	 * Doesn't create a new post if: the post type doesn't exist, the given post ID
	 * is already noted as imported or a post with the same title and date already exists.
	 * Note that new/updated terms, comments and meta are imported for the last of the above.
	 */
	function process_posts() {
		$start_time = microtime(true);
		$this->posts = apply_filters( 'wp_import_posts', $this->posts );

		//used in rewriting links in elementor content
		$current_content_path = trim( json_encode( content_url() ), '"' );
		$possible_demo_paths = array(
			'https:\/\/rifetheme.com\/wp-content',
			'http:\/\/rifetheme.com\/wp-content',
		);

		//Elementor not doing slashing on every import since 2.9.14
		//Additionaly behaviour is dependant on version of WordPress Importer installed on the system
		$wp_importer = get_plugins( '/wordpress-importer' );
		$wp_importer_need_slash = true;

		if ( ! empty( $wp_importer ) ) {
			$wp_importer_version = $wp_importer['wordpress-importer.php']['Version'];

			if ( version_compare( $wp_importer_version, '0.7', '<' ) ) {
				$wp_importer_need_slash = false;
			}
		}

		$wp_slash_elementor = (defined('ELEMENTOR_VERSION') && version_compare(ELEMENTOR_VERSION, '2.9.14', '>=')) && $wp_importer_need_slash;

		foreach ( $this->posts as $post ) {
			/* @var array $post*/

			//if this is next ajax call for importing posts,
			//then last post did will be different then 0
			//so we have to skip until last_post_did
			if($this->last_post_did !== 0){
				if($this->last_post_did === $post['post_id']){
					//now we continue normally
					$this->last_post_did = 0;
				}
				else{
					continue;
				}
			}

			//checks if script isn't importing posts for too long
			//if it does, then we will come back here in next AJAX call
			$how_long = (microtime(true) - $start_time);
			if($how_long > $this->max_time_running ){
				/* translators: %s: time in seconds */
				printf( 'TIME %s', esc_html( round( $how_long, 1 ) ) );
				echo '<br />';
				$this->last_post_did = $post['post_id'];
				return false;
			}

			$post = apply_filters( 'wp_import_post_data_raw', $post );

			if ( ! post_type_exists( $post['post_type'] ) ) {
				printf( 'POST_TYPE_FAIL %s', esc_html($post['post_type']) );
				echo '<br />';
				do_action( 'wp_import_post_exists', $post );
				continue;
			}

			if ( isset( $this->processed_posts[$post['post_id']] ) && ! empty( $post['post_id'] ) )
				continue;

			if ( $post['status'] == 'auto-draft' )
				continue;

			if ( 'nav_menu_item' == $post['post_type'] ) {
				$this->process_menu_item( $post );
				continue;
			}

			$post_type_object = get_post_type_object( $post['post_type'] );

			$post_exists = post_exists( $post['post_title'], '', $post['post_date'] );
			if ( $post_exists && get_post_type( $post_exists ) == $post['post_type'] ) {
				printf( 'POST_EXISTS_FAIL %1$s &#8220;%2$s&#8221;', esc_html( $post_type_object->labels->singular_name ), esc_html( $post['post_title'] ) );
				echo '<br />';
				$comment_post_ID = $post_id = $post_exists;
			} else {
				$post_parent = (int) $post['post_parent'];
				if ( $post_parent ) {
					// if we already know the parent, map it to the new local ID
					if ( isset( $this->processed_posts[$post_parent] ) ) {
						$post_parent = $this->processed_posts[$post_parent];
					// otherwise record the parent for later
					} else {
						$this->post_orphans[(int)$post['post_id']] = $post_parent;
						$post_parent = 0;
					}
				}

				// map the post author
//				$author = sanitize_user( $post['post_author'], true );
//				if ( isset( $this->author_mapping[$author] ) )
//					$author = $this->author_mapping[$author];
//				else
					$author = (int) get_current_user_id();

				//rewrite guids to current site
				$post['guid'] = str_replace($this->base_url, home_url(), $post['guid'] );

				$postdata = array(
					'import_id' => $post['post_id'],
					'post_author' => $author,
					'post_date' => $post['post_date'],
					'post_date_gmt' => $post['post_date_gmt'],
					'post_content' => $post['post_content'],
					'post_excerpt' => $post['post_excerpt'],
					'post_title' => $post['post_title'],
					'post_status' => $post['status'],
					'post_name' => $post['post_name'],
					'comment_status' => $post['comment_status'],
					'ping_status' => $post['ping_status'],
					'guid' => $post['guid'],
					'post_parent' => $post_parent,
					'menu_order' => $post['menu_order'],
					'post_type' => $post['post_type'],
					'post_password' => $post['post_password']
				);

				$original_post_ID = $post['post_id'];
				$postdata = apply_filters( 'wp_import_post_data_processed', $postdata, $post );

				if ( 'attachment' == $postdata['post_type'] ) {
					$remote_url = ! empty($post['attachment_url']) ? $post['attachment_url'] : $post['guid'];

					// try to use _wp_attached file for upload folder placement to ensure the same location as the export site
					// e.g. location is 2003/05/image.jpg but the attachment post_date is 2010/09, see media_handle_upload()
					$postdata['upload_date'] = $post['post_date'];
					if ( isset( $post['postmeta'] ) ) {
						foreach( $post['postmeta'] as $meta ) {
							if ( $meta['key'] == '_wp_attached_file' ) {
								if ( preg_match( '%^[0-9]{4}/[0-9]{2}%', $meta['value'], $matches ) )
									$postdata['upload_date'] = $matches[0];
								break;
							}
						}
					}

					$comment_post_ID = $post_id = $this->process_attachment( $postdata, $remote_url );
				} else {
					$comment_post_ID = $post_id = wp_insert_post( $postdata, true );
					do_action( 'wp_import_insert_post', $post_id, $original_post_ID, $postdata, $post );
				}

				if ( is_wp_error( $post_id ) ) {
					printf( 'POST_IMPORT_FAIL %1$s &#8220;%2$s&#8221; - %3$s', esc_html( $post_type_object->labels->singular_name ), esc_html( $post['post_title'] ), $post_id->get_error_message() );
					//skip further processing of this post
					continue;
				}

				if ( $post['is_sticky'] == 1 )
					stick_post( $post_id );
			}

			// map pre-import ID to local ID
			$this->processed_posts[(int)$post['post_id']] = (int) $post_id;

			if ( ! isset( $post['terms'] ) )
				$post['terms'] = array();

			$post['terms'] = apply_filters( 'wp_import_post_terms', $post['terms'], $post_id, $post );

			// add categories, tags and other terms
			if ( ! empty( $post['terms'] ) ) {
				$terms_to_set = array();
				foreach ( $post['terms'] as $term ) {
					// back compat with WXR 1.0 map 'tag' to 'post_tag'
					$taxonomy = ( 'tag' == $term['domain'] ) ? 'post_tag' : $term['domain'];
					$term_exists = term_exists( $term['slug'], $taxonomy );
					$term_id = is_array( $term_exists ) ? $term_exists['term_id'] : $term_exists;
					if ( ! $term_id ) {
						$t = wp_insert_term( $term['name'], $taxonomy, array( 'slug' => $term['slug'] ) );
						if ( ! is_wp_error( $t ) ) {
							$term_id = $t['term_id'];
							do_action( 'wp_import_insert_term', $t, $term, $post_id, $post );
						} else {
							printf(  'TERM_IMPORT_FAIL %1$s %2$s', esc_html($taxonomy), esc_html($term['name']) );
							echo '<br />';
							do_action( 'wp_import_insert_term_failed', $t, $term, $post_id, $post );
							continue;
						}
					}
					$terms_to_set[$taxonomy][] = (int)$term_id;
				}

				foreach ( $terms_to_set as $tax => $ids ) {
					$tt_ids = wp_set_post_terms( $post_id, $ids, $tax );
					do_action( 'wp_import_set_post_terms', $tt_ids, $ids, $tax, $post_id, $post );
				}
				unset( $post['terms'], $terms_to_set );
			}

			if ( ! isset( $post['comments'] ) )
				$post['comments'] = array();

			$post['comments'] = apply_filters( 'wp_import_post_comments', $post['comments'], $post_id, $post );

			// add/update comments
			if ( ! empty( $post['comments'] ) ) {
				$num_comments = 0;
				$inserted_comments = array();
				foreach ( $post['comments'] as $comment ) {
					$comment_id	= $comment['comment_id'];
					$newcomments[$comment_id]['comment_post_ID']      = $comment_post_ID;
					$newcomments[$comment_id]['comment_author']       = $comment['comment_author'];
					$newcomments[$comment_id]['comment_author_email'] = $comment['comment_author_email'];
					$newcomments[$comment_id]['comment_author_IP']    = $comment['comment_author_IP'];
					$newcomments[$comment_id]['comment_author_url']   = $comment['comment_author_url'];
					$newcomments[$comment_id]['comment_date']         = $comment['comment_date'];
					$newcomments[$comment_id]['comment_date_gmt']     = $comment['comment_date_gmt'];
					$newcomments[$comment_id]['comment_content']      = $comment['comment_content'];
					$newcomments[$comment_id]['comment_approved']     = $comment['comment_approved'];
					$newcomments[$comment_id]['comment_type']         = $comment['comment_type'];
					$newcomments[$comment_id]['comment_parent'] 	  = $comment['comment_parent'];
					$newcomments[$comment_id]['commentmeta']          = isset( $comment['commentmeta'] ) ? $comment['commentmeta'] : array();
					if ( isset( $this->processed_authors[$comment['comment_user_id']] ) )
						$newcomments[$comment_id]['user_id'] = $this->processed_authors[$comment['comment_user_id']];
				}
				ksort( $newcomments );

				foreach ( $newcomments as $key => $comment ) {
					// if this is a new post we can skip the comment_exists() check
					if ( ! $post_exists || ! comment_exists( $comment['comment_author'], $comment['comment_date'] ) ) {
						if ( isset( $inserted_comments[$comment['comment_parent']] ) )
							$comment['comment_parent'] = $inserted_comments[$comment['comment_parent']];
						$comment = wp_filter_comment( $comment );
						$inserted_comments[$key] = wp_insert_comment( $comment );
						do_action( 'wp_import_insert_comment', $inserted_comments[$key], $comment, $comment_post_ID, $post );

						foreach( $comment['commentmeta'] as $meta ) {
							$value = maybe_unserialize( $meta['value'] );
							add_comment_meta( $inserted_comments[$key], $meta['key'], $value );
						}

						$num_comments++;
					}
				}
				unset( $newcomments, $inserted_comments, $post['comments'] );
			}

			if ( ! isset( $post['postmeta'] ) )
				$post['postmeta'] = array();

			//replace images URLs in elementor content
			foreach ( $post['postmeta'] as &$meta ) {
				if ( '_elementor_data' === $meta['key'] ) {
					foreach($possible_demo_paths as $old_content_path){
						$meta['value'] = str_replace($old_content_path, $current_content_path, $meta['value']);
					}
					if( $wp_slash_elementor ){
						$meta['value'] = wp_slash( $meta['value'] );
					}
					break;
				}
			}
			unset($meta);// be safe, don't loose your hair :-)


			$post['postmeta'] = apply_filters( 'wp_import_post_meta', $post['postmeta'], $post_id, $post );

			// add/update post meta
			if ( ! empty( $post['postmeta'] ) ) {
				foreach ( $post['postmeta'] as $meta ) {
					$key = apply_filters( 'import_post_meta_key', $meta['key'], $post_id, $post );
					$value = false;

					if ( '_edit_last' == $key ) {
						if ( isset( $this->processed_authors[(int)$meta['value']] ) )
							$value = $this->processed_authors[(int)$meta['value']];
						else
							$key = false;
					}

					if ( $key ) {
						// export gets meta straight from the DB so could have a serialized string
						if ( ! $value )
							$value = maybe_unserialize( $meta['value'] );

						add_post_meta( $post_id, $key, $value );
						do_action( 'import_post_meta', $post_id, $key, $value );

						// if the post has a featured image, take note of this in case of remap
						if ( '_thumbnail_id' == $key )
							$this->featured_images[$post_id] = (int) $value;
					}
				}
			}


		}

		unset( $this->posts );

		return true;
	}

	/**
	 * Attempt to create a new menu item from import data
	 *
	 * Fails for draft, orphaned menu items and those without an associated nav_menu
	 * or an invalid nav_menu term. If the post type or term object which the menu item
	 * represents doesn't exist then the menu item will not be imported (waits until the
	 * end of the import to retry again before discarding).
	 *
	 * @param array $item Menu item details from WXR file
	 */
	function process_menu_item( $item ) {
		// skip draft, orphaned menu items
		if ( 'draft' == $item['status'] )
			return;

		$menu_slug = false;
		if ( isset($item['terms']) ) {
			// loop through terms, assume first nav_menu term is correct menu
			foreach ( $item['terms'] as $term ) {
				if ( 'nav_menu' == $term['domain'] ) {
					$menu_slug = $term['slug'];
					break;
				}
			}
		}

		// no nav_menu term associated with this menu item
		if ( ! $menu_slug ) {
			echo 'MENU_ITEM_FAIL';
			echo '<br />';
			return;
		}

		$menu_id = term_exists( $menu_slug, 'nav_menu' );
		if ( ! $menu_id ) {
			printf( 'MENU_ITEM_FAIL %s', esc_html( $menu_slug ) );
			echo '<br />';
			return;
		} else {
			$menu_id = is_array( $menu_id ) ? $menu_id['term_id'] : $menu_id;
		}

		$all_meta = array();
		$_menu_item_type = $_menu_item_object_id = $_menu_item_menu_item_parent = $_menu_item_classes = '';
		$_menu_item_object = $_menu_item_url = $_menu_item_target = $_menu_item_xfn = '';

		foreach ( $item['postmeta'] as $meta ){
			${$meta['key']} = $meta['value']; //for later overwrite od default meta fields
			$all_meta[substr($meta['key'], 1)] = $meta['value']; //for non standard extensions
		}

		if ( 'taxonomy' == $_menu_item_type && isset( $this->processed_terms[(int)$_menu_item_object_id] ) ) {
			$_menu_item_object_id = $this->processed_terms[(int)$_menu_item_object_id];
		} else if ( 'post_type' == $_menu_item_type && isset( $this->processed_posts[(int)$_menu_item_object_id] ) ) {
			$_menu_item_object_id = $this->processed_posts[(int)$_menu_item_object_id];
		} else if ( 'custom' != $_menu_item_type ) {
			// associated object is missing or not imported yet, we'll retry later
			$this->missing_menu_items[] = $item;
			return;
		}
		elseif('custom' === $_menu_item_type ){
			//if it is custom menu link + we can find whole address of base_url then we want to rewrite it
			$_menu_item_url = str_replace($this->base_url, home_url(), $_menu_item_url );
		}

		if ( isset( $this->processed_menu_items[(int)$_menu_item_menu_item_parent] ) ) {
			$_menu_item_menu_item_parent = $this->processed_menu_items[(int)$_menu_item_menu_item_parent];
		} else if ( $_menu_item_menu_item_parent ) {
			$this->menu_item_orphans[(int)$item['post_id']] = (int) $_menu_item_menu_item_parent;
			$_menu_item_menu_item_parent = 0;
		}

		// wp_update_nav_menu_item expects CSS classes as a space separated string
		$_menu_item_classes = maybe_unserialize( $_menu_item_classes );
		if ( is_array( $_menu_item_classes ) )
			$_menu_item_classes = implode( ' ', $_menu_item_classes );

		$args = array(
			'menu-item-object-id' => $_menu_item_object_id,
			'menu-item-object' => $_menu_item_object,
			'menu-item-parent-id' => $_menu_item_menu_item_parent,
			'menu-item-position' => (int)$item['menu_order'],
			'menu-item-type' => $_menu_item_type,
			'menu-item-title' => $item['post_title'],
			'menu-item-url' => $_menu_item_url,
			'menu-item-description' => $item['post_content'],
			'menu-item-attr-title' => $item['post_excerpt'],
			'menu-item-target' => $_menu_item_target,
			'menu-item-classes' => $_menu_item_classes,
			'menu-item-xfn' => $_menu_item_xfn,
			'menu-item-status' => $item['status']
		);

		$id = wp_update_nav_menu_item( $menu_id, 0, $args );

		//here we add on standard extensions
		foreach($all_meta as $key => $value){
			if($key !== 'menu_item_menu_item_parent' && !array_key_exists(str_replace('_','-',$key), $args)){
				update_post_meta( $id, '_'.$key, $value );
			}
		}

		if ( $id && ! is_wp_error( $id ) )
			$this->processed_menu_items[(int)$item['post_id']] = (int) $id;
	}

	/**
	 * If fetching attachments is enabled then attempt to create a new attachment
	 *
	 * @param array $post Attachment post details from WXR
	 * @param string $url URL to fetch attachment from
	 * @return int|WP_Error Post ID on success, WP_Error otherwise
	 */
	function process_attachment( $post, $url ) {
		if ( ! $this->fetch_attachments )
			return new WP_Error( 'attachment_processing_error' );

		// if the URL is absolute, but does not contain address, then upload it assuming base_site_url
		if ( preg_match( '|^/[\w\W]+$|', $url ) )
			$url = rtrim( $this->base_url, '/' ) . $url;


		/* make sure server will not remove any files from other demos
		 * while we are importing them on home server.
		 * To prevent this we fake import of files that already exist,
		 * as we also don't want to create copies of them
		 * */
		//demo server only
		if(apollo13framework_is_home_server()){
			$__upload = wp_upload_dir( $post['upload_date'] );
			$__file_name = basename( $url );
			$__new_file = $__upload['path'] . "/$__file_name";
			$__url	= $__upload['url'] . "/$__file_name";
			$__wp_filetype = wp_check_filetype( $__file_name );

			$upload = array( 'file' => $__new_file, 'url' => $__url, 'type' => $__wp_filetype['type'], 'error' => false );
		}
		//not while using importer on demo server
		else{
			/* @var array $post*/
			$upload = $this->fetch_remote_file( $url, $post );
			if ( is_wp_error( $upload ) )
				return $upload;
		}



		if ( $info = wp_check_filetype( $upload['file'] ) )
			$post['post_mime_type'] = $info['type'];
		else
			return new WP_Error( 'attachment_processing_error', 'FILE_TYPE_FAIL' );

		$post['guid'] = $upload['url'];

		// as per wp-admin/includes/upload.php
		$post_id = wp_insert_attachment( $post, $upload['file'] );
		wp_update_attachment_metadata( $post_id, wp_generate_attachment_metadata( $post_id, $upload['file'] ) );

		// remap resized image URLs, works by stripping the extension and remapping the URL stub.
		if ( preg_match( '!^image/!', $info['type'] ) ) {
			$parts = pathinfo( $url );
			$name = basename( $parts['basename'], ".{$parts['extension']}" ); // PATHINFO_FILENAME in PHP 5.2

			$parts_new = pathinfo( $upload['url'] );
			$name_new = basename( $parts_new['basename'], ".{$parts_new['extension']}" );

			$this->url_remap[$parts['dirname'] . '/' . $name] = $parts_new['dirname'] . '/' . $name_new;
		}

		return $post_id;
	}

	/**
	 * Attempt to download a remote file attachment
	 *
	 * @param string $url URL of item to fetch
	 * @param array $post Attachment details
	 * @return array|WP_Error Local file location details on success, WP_Error otherwise
	 */
	function fetch_remote_file( $url, $post ) {
		if( get_option('a13fe_import_by_http') === 'on'){
			//switch to http
			$url = str_replace( 'https:', 'http:', $url );
		}

		// extract the file name and extension from the url
		$file_name = basename( $url );

		// get placeholder file in the upload dir with a unique, sanitized filename
		$upload = wp_upload_bits( $file_name, 0, '', $post['upload_date'] );
		if ( $upload['error'] )
			return new WP_Error( 'upload_dir_error', $upload['error'] );

		// fetch the remote url and write it to the placeholder file
		$response = wp_remote_get( $url, array(
				'stream' => true,
				'filename' => $upload['file']
		) );

		// request failed
		if ( is_wp_error( $response ) ) {
			if ( file_exists( $upload['file'] ) ) {
				unlink( $upload['file'] );
			}
			return $response;
		}

		$code = (int) wp_remote_retrieve_response_code( $response );

		// make sure the fetch was successful
		if ( $code !== 200 ) {
			if ( file_exists( $upload['file'] ) ) {
				unlink( $upload['file'] );
			}
			return new WP_Error(
					'import_file_error',
					sprintf(
						'%1$d %2$s - %3$s',
						$code,
						get_status_header_desc( $code ),
						$url
					)
			);
		}

		$filesize = filesize( $upload['file'] );
		$headers = wp_remote_retrieve_headers( $response );

		if ( isset( $headers['content-length'] ) && $filesize != $headers['content-length'] ) {
			if ( file_exists( $upload['file'] ) ) {
				unlink( $upload['file'] );
			}
			return new WP_Error( 'import_file_error', 'INCORRECT_FILE_SIZE' );
		}

		if ( 0 == $filesize ) {
			if ( file_exists( $upload['file'] ) ) {
				unlink( $upload['file'] );
			}
			return new WP_Error( 'import_file_error', 'ZERO_FILE_SIZE' );
		}

		$max_size = (int) $this->max_attachment_size();
		if ( ! empty( $max_size ) && $filesize > $max_size ) {
			if ( file_exists( $upload['file'] ) ) {
				unlink( $upload['file'] );
			}
			return new WP_Error( 'import_file_error', 'TOO_LARGE_FILE_SIZE' );
		}

		// keep track of the old and new urls so we can substitute them later
		$this->url_remap[$url] = $upload['url'];
		$this->url_remap[$post['guid']] = $upload['url']; // r13735, really needed?
		// keep track of the destination if the remote url is redirected somewhere else
		if ( isset($headers['x-final-location']) && $headers['x-final-location'] != $url ){
			$this->url_remap[$headers['x-final-location']] = $upload['url'];
		}

		return $upload;
	}

	/**
	 * Attempt to associate posts and menu items with previously missing parents
	 *
	 * An imported post's parent may not have been imported when it was first created
	 * so try again. Similarly for child menu items and menu items which were missing
	 * the object (e.g. post) they represent in the menu
	 */
	function backfill_parents() {
		global $wpdb;

		// find parents for post orphans
		foreach ( $this->post_orphans as $child_id => $parent_id ) {
			$local_child_id = $local_parent_id = false;
			if ( isset( $this->processed_posts[$child_id] ) )
				$local_child_id = $this->processed_posts[$child_id];
			if ( isset( $this->processed_posts[$parent_id] ) )
				$local_parent_id = $this->processed_posts[$parent_id];

			if ( $local_child_id && $local_parent_id )
				$wpdb->update( $wpdb->posts, array( 'post_parent' => $local_parent_id ), array( 'ID' => $local_child_id ), '%d', '%d' );
		}

		// all other posts/terms are imported, retry menu items with missing associated object
		$missing_menu_items = $this->missing_menu_items;
		foreach ( $missing_menu_items as $item )
			$this->process_menu_item( $item );

		// find parents for menu item orphans
		foreach ( $this->menu_item_orphans as $child_id => $parent_id ) {
			$local_child_id = $local_parent_id = 0;
			if ( isset( $this->processed_menu_items[$child_id] ) )
				$local_child_id = $this->processed_menu_items[$child_id];
			if ( isset( $this->processed_menu_items[$parent_id] ) )
				$local_parent_id = $this->processed_menu_items[$parent_id];

			if ( $local_child_id && $local_parent_id )
				update_post_meta( $local_child_id, '_menu_item_menu_item_parent', (int) $local_parent_id );
		}
	}

	/**
	 * Use stored mapping information to update old attachment URLs
	 */
	function backfill_attachment_urls() {
		global $wpdb;
		// make sure we do the longest urls first, in case one is a substring of another
		uksort( $this->url_remap, array(&$this, 'cmpr_strlen') );

		foreach ( $this->url_remap as $from_url => $to_url ) {
			// remap urls in post_content
			/** @noinspection SqlNoDataSourceInspection */
			$wpdb->query( $wpdb->prepare("UPDATE {$wpdb->posts} SET post_content = REPLACE(post_content, %s, %s)", $from_url, $to_url) );
			// remap enclosure urls
			/** @noinspection SqlNoDataSourceInspection */
			$wpdb->query( $wpdb->prepare("UPDATE {$wpdb->postmeta} SET meta_value = REPLACE(meta_value, %s, %s) WHERE meta_key='enclosure'", $from_url, $to_url) );
		}
	}

	/**
	 * Update _thumbnail_id meta to new, imported attachment IDs
	 */
	function remap_featured_images() {
		// cycle through posts that have a featured image
		foreach ( $this->featured_images as $post_id => $value ) {
			if ( isset( $this->processed_posts[$value] ) ) {
				$new_id = $this->processed_posts[$value];
				// only update if there's a difference
				if ( $new_id != $value )
					update_post_meta( $post_id, '_thumbnail_id', $new_id );
			}
		}
	}

	/**
	 * Parse a WXR file
	 *
	 * @param string $file Path to WXR file for parsing
	 * @return array Information gathered from the WXR file
	 */
	function parse( $file ) {
		$parser = new WXR_Parser();
		return $parser->parse( $file );
	}


	/**
	 * Decide if the given meta key maps to information we will want to import
	 *
	 * @param string $key The meta key to check
	 * @return string|bool The key if we do want to import, false if not
	 */
	function is_valid_meta_key( $key ) {
		// skip attachment metadata since we'll regenerate it from scratch
		// skip _edit_lock as not relevant for import
		if ( in_array( $key, array( '_wp_attached_file', '_wp_attachment_metadata', '_edit_lock' ) ) )
			return false;
		return $key;
	}


	/**
	 * Decide what the maximum file size for downloaded attachments is.
	 * Default is 0 (unlimited), can be filtered via import_attachment_size_limit
	 *
	 * @return int Maximum attachment file size to import
	 */
	function max_attachment_size() {
		return apply_filters( 'import_attachment_size_limit', 0 );
	}

	/**
	 * Added to http_request_timeout filter to force timeout at 60 seconds during import
     * @param int $val
	 * @return int 60
	 */
	function bump_request_timeout($val) {
		return 60;
	}

	// return the difference in length between two strings
	function cmpr_strlen( $a, $b ) {
		return strlen($b) - strlen($a);
	}

	function return_bytes ($size_str)
	{
		switch (substr ($size_str, -1))
		{
			case 'M': case 'm': return (int)$size_str * 1048576;
			case 'K': case 'k': return (int)$size_str * 1024;
			case 'G': case 'g': return (int)$size_str * 1073741824;
			default: return $size_str;
		}
	}
}

}