<?php
namespace Apollo13_FE\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Slider extends Widget_Base {

	public function get_name() {
		return 'a13fe-slider';
	}

	public function get_title() {
		return __( 'Theme Slider', 'apollo13-framework-extensions' );
	}

	public function get_icon() {
		return 'eicon-slider-push';
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
			'slider_settings',
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

		$this->end_controls_section();


		$this->start_controls_section(
			'slider_look',
			[
				'label' => __( 'Layout', 'apollo13-framework-extensions' ),
			]
		);


		$this->add_control(
			'window_high',
			[
				'label' => __( 'Stretch the slider to the window height', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);

		$this->add_control(
			'ratio',
			[
				'label' => __( 'Aspect ratio', 'apollo13-framework-extensions' ),
				'description' => __( 'Proportion of width to the height of the slider. Please enter as 2 numbers separated with "/". Example: 21/9. Takes effect when the slider is not stretched to the full height.', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::TEXT,
				'default' => '16/9',
				'placeholder' => __( '16/9, 4/3, 21/9...', 'apollo13-framework-extensions' ),
				'separator' => 'before',
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'slider_interface',
			[
				'label' => __( 'Interface', 'apollo13-framework-extensions' ),
			]
		);


		$this->add_control(
			'autoplay',
			[
				'label' => __( 'Autoplay', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'texts',
			[
				'label' => __( 'Show texts', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'thumbs',
			[
				'label' => __( 'Thumbnails', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
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
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$id          = $settings['post_id'];
		$autoplay    = $settings['autoplay'] === 'yes' ? 'on' : 'off';
		$texts       = $settings['texts'] === 'yes' ? 'on' : 'off';
		$thumbs      = $settings['thumbs'] === 'yes' ? 'on' : 'off';
		$window_high = $settings['window_high'] === 'yes' ? 'on' : 'off';
		$socials     = $settings['socials'] === 'yes' ? 'on' : 'off';
		$ratio       = $settings['ratio'];

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

		global $apollo13framework_a13;

		//params from post in question
		$title_color = get_post_meta( $id, '_slide_title_bg_color', true );
		$title_color = ( $title_color === '' || $title_color === false || $title_color === 'transparent' ) ? '' : $title_color;
		$ken_scale   = $apollo13framework_a13->get_meta( '_ken_scale', $id );
		$ken_scale   = strlen( $ken_scale ) ? $ken_scale : 120;

		//get globals depending on post type
		$post_type = get_post_type( $id );
		if ( $post_type === 'work' ) {
			$transition_time = $apollo13framework_a13->get_option( 'work_slider_transition_time' );
			$slide_interval  = $apollo13framework_a13->get_option( 'work_slider_slide_interval' );
		} else {
			$transition_time = $apollo13framework_a13->get_option( 'album_slider_transition_time' );
			$slide_interval  = $apollo13framework_a13->get_option( 'album_slider_slide_interval' );
		}


		$slider_opts = array(
			'autoplay'        => $autoplay,
			'transition'      => $apollo13framework_a13->get_meta( '_transition', $id ),
			'fit_variant'     => $apollo13framework_a13->get_meta( '_fit_variant', $id ),
			'pattern'         => $apollo13framework_a13->get_meta( '_pattern', $id ),
			'gradient'        => $apollo13framework_a13->get_meta( '_gradient', $id ),
			'ken_burns_scale' => $ken_scale,
			'texts'           => $texts,
			'title_color'     => $title_color,
			'transition_time' => $transition_time,
			'slide_interval'  => $slide_interval,
			'thumbs'          => $thumbs,
			'thumbs_on_load'  => $apollo13framework_a13->get_meta( '_thumbs_on_load', $id ),
			'socials'         => $socials,
			'window_high'     => $window_high,
			'ratio'           => $ratio,
		);

		wp_enqueue_script('apollo13framework-slider');
		apollo13framework_make_slider($slider_opts, $id);
	}
}
