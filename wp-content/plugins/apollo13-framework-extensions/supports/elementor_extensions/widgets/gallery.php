<?php
namespace Apollo13_FE\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Gallery extends Widget_Base {

	public function get_name() {
		return 'a13fe-gallery';
	}

	public function get_title() {
		return __( 'Theme Gallery', 'apollo13-framework-extensions' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return [ 'general', 'apollo13-framework' ];
	}

	private function get_albums_works(){
		$options = [ '' => '' ];

		//list all Albums
		$wp_query_params = array(
			'posts_per_page' => -1,
			'no_found_rows' => true,
			'post_type' => defined( 'A13FRAMEWORK_CUSTOM_POST_TYPE_ALBUM' ) ? A13FRAMEWORK_CUSTOM_POST_TYPE_ALBUM : 'album',
			'post_status' => 'publish',
			'ignore_sticky_posts' => true,
			'orderby' => 'date'
		);

		$r = new \WP_Query($wp_query_params);

		if ($r->have_posts()){
			while ($r->have_posts()){
				$r->the_post();
				$options[ get_the_ID() ] = __( 'Album', 'apollo13-framework-extensions' ) . ': '. get_the_title();
			}
		}

		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		//list all Works
		$wp_query_params = array(
			'posts_per_page' => -1,
			'no_found_rows' => true,
			'post_type' => defined( 'A13FRAMEWORK_CUSTOM_POST_TYPE_WORK' ) ? A13FRAMEWORK_CUSTOM_POST_TYPE_WORK : 'work',
			'post_status' => 'publish',
			'ignore_sticky_posts' => true,
			'orderby' => 'date'
		);

		$r = new \WP_Query($wp_query_params);

		if ($r->have_posts()){
			while ($r->have_posts()){
				$r->the_post();
				$options[ get_the_ID() ] = __( 'Work', 'apollo13-framework-extensions' ) . ': '. get_the_title();
			}
		}

		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		return $options;
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'gallery_settings',
			[
				'label' => __( 'Settings', 'apollo13-framework-extensions' ),
			]
		);

		$this->add_control(
			'post_id',
			[
				'label' => __( 'Album/Work', 'apollo13-framework-extensions' ),
				/* translators: %s: media type */
				'description' => sprintf( esc_html__( 'Album or Work from which media(%s) should be taken.', 'apollo13-framework-extensions' ), esc_html__( 'images &amp; videos', 'apollo13-framework-extensions' ) ),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'default' => "",
				'options' => $this->get_albums_works(),
			]
		);

		$this->add_control(
			'post_description',
			[
				/* translators: %s: media type */
				'raw' => sprintf( esc_html__( 'You can edit the media(%s) used in the widget by editing selected Album or Work.', 'apollo13-framework-extensions' ), esc_html__( 'images &amp; videos', 'apollo13-framework-extensions' ) ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
			]
		);


		$this->add_control(
			'filter',
			[
				'label' => __( 'Filter', 'apollo13-framework-extensions' ),
				'description' => __( 'Useful only if the items have tags.', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'gallery_layout',
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
						'max' => 6,
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
			'ratio',
			[
				'label' => __( 'Aspect ratio', 'apollo13-framework-extensions' ),
				'description' => __( 'Proportion of width to the height of the bricks. Please enter as 2 numbers separated with "/". Example: 21/9. Use "0" to get a masonry appearance with a natural ratio of image width and height.', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::TEXT,
				'default' => '0',
				'placeholder' => __( '16/9, 4/3, 21/9...', 'apollo13-framework-extensions' ),
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'brick_look',
			[
				'label' => __( 'Bricks behaviour', 'apollo13-framework-extensions' ),
			]
		);

		$this->add_control(
			'lightbox',
			[
				'label' => __( 'Open bricks to lightbox', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'hover_effect',
			[
				'label' => __( 'Effect on hover', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'drop',
				'options' => [
					'cross'  => __( 'Show cross', 'apollo13-framework-extensions' ),
					'drop'   => __( 'Drop', 'apollo13-framework-extensions' ),
					'shift'  => __( 'Shift', 'apollo13-framework-extensions' ),
					'pop'    => __( 'Pop Text', 'apollo13-framework-extensions' ),
					'border' => __( 'Border', 'apollo13-framework-extensions' ),
					'none'   => __( 'None', 'apollo13-framework-extensions' ),
				],
			]
		);

		$this->add_control(
			'cover',
			[
				'label' => __( 'Show overlay', 'apollo13-framework-extensions' ). ' - ' .esc_html__( 'without hover', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'cover_hover',
			[
				'label' => __( 'Show overlay', 'apollo13-framework-extensions' ). ' - ' .esc_html__( 'on hover', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);

		$this->add_control(
			'cover_color',
			[
				'label' => __( 'Overlay color', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(0,0,0,0.7)',
			]
		);

		$this->add_control(
			'texts',
			[
				'label' => __( 'Show texts', 'apollo13-framework-extensions' ). ' - ' .esc_html__( 'without hover', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'texts_hover',
			[
				'label' => __( 'Show texts', 'apollo13-framework-extensions' ). ' - ' .esc_html__( 'on hover', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);

		$this->add_control(
			'texts_position',
			[
				'label' => __( 'Texts position', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'bottom_center',
				'options' => [
					'top_left'      => __( 'Top left', 'apollo13-framework-extensions' ),
					'top_center'    => __( 'Top center', 'apollo13-framework-extensions' ),
					'top_right'     => __( 'Top right', 'apollo13-framework-extensions' ),
					'mid_left'      => __( 'Middle left', 'apollo13-framework-extensions' ),
					'mid_center'    => __( 'Middle center', 'apollo13-framework-extensions' ),
					'mid_right'     => __( 'Middle right', 'apollo13-framework-extensions' ),
					'bottom_left'   => __( 'Bottom left', 'apollo13-framework-extensions' ),
					'bottom_center' => __( 'Bottom center', 'apollo13-framework-extensions' ),
					'bottom_right'  => __( 'Bottom right', 'apollo13-framework-extensions' ),
				],
			]
		);

		$this->add_control(
			'gradient',
			[
				'label' => esc_html__( 'Show gradient', 'apollo13-framework-extensions' ). ' - ' .esc_html__( 'without hover', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'gradient_hover',
			[
				'label' => esc_html__( 'Show gradient', 'apollo13-framework-extensions' ). ' - ' .esc_html__( 'on hover', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);

		/** @noinspection HtmlUnknownTarget */
		$this->add_control(
			'socials',
			[
				'label' => __( 'Social icons', 'apollo13-framework-extensions' ),
				/* translators: %s: linked plugin name */
				'description' => sprintf(__( 'It requires the %s plugin.', 'apollo13-framework-extensions' ), '<a href="'.esc_url( 'https://rifetheme.com/help/docs/plugins-recommendations/addtoany-share-icons/' ).'">AddToAny</a>' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$id          = $settings['post_id'];

		//without id
		if ( $id === "" ) {
			echo esc_html__( 'Please select proper album or work to use.', 'apollo13-framework-extensions' );
			return;
		}


		//it doesn't contain items to use
		$has_images = strlen( get_post_meta( $id, '_images_n_videos', true ) ) > 2; //2 => [] - empty array
		if ( ! $has_images ) {
			/* translators: %s: media type */
			echo sprintf( esc_html__( 'Error: Selected post does not contain any media(%s) to use.', 'apollo13-framework-extensions' ), esc_html__( 'images &amp; videos', 'apollo13-framework-extensions' ) ) .' '. esc_html__( 'Please select proper album or work to use.', 'apollo13-framework-extensions' );
			return;
		}

		$cover_color    = $settings['cover_color'];
		$filter         = $settings['filter'];
		$lightbox       = $settings['lightbox'];
		$texts_position = $settings['texts_position'];
		$hover_effect   = $settings['hover_effect'];
		$cover          = $settings['cover'];
		$cover_hover    = $settings['cover_hover'];
		$gradient       = $settings['gradient'];
		$gradient_hover = $settings['gradient_hover'];
		$texts          = $settings['texts'];
		$texts_hover    = $settings['texts_hover'];
		$socials        = $settings['socials'];
		$margin         = $settings['margin']['size'].$settings['margin']['unit'];
		$ratio          = $settings['ratio'];
		$columns        = $settings['columns']['size'];

		//make sure on/off params have proper values
		$on_off_attrs = array(
			'filter',
			'lightbox',
			'cover',
			'cover_hover',
			'gradient',
			'gradient_hover',
			'texts',
			'texts_hover',
			'socials'
		);

		foreach($on_off_attrs as $attribute ){
			$$attribute = $$attribute === 'yes'? 'on' : 'off';
		}

		//check if we need to force texts to display
		$texts_needed = $texts === 'on' || $texts_hover === 'on';

		$gallery_opts = array(
			'cover_color'            => $cover_color,
			'filter'                 => $filter,
			'lightbox'               => $lightbox,
			'title_position'         => $texts_position,
			'hover_effect'           => $hover_effect,
			'overlay_cover'          => $cover,
			'overlay_cover_hover'    => $cover_hover,
			'overlay_gradient'       => $gradient,
			'overlay_gradient_hover' => $gradient_hover,
			'overlay_texts'          => $texts,
			'overlay_texts_hover'    => $texts_hover,
			'socials'                => $socials,
			'margin'                 => $margin,
			'proportion'             => $ratio,
			'columns'                => $columns,
			'max_width'              => get_post_meta( $id, '_bricks_max_width', true ),
			'show_desc'              => $texts_needed ? 'on' : get_post_meta( $id, '_enable_desc', true )
		);

		apollo13framework_make_bricks_gallery($gallery_opts, $id);
	}

}
