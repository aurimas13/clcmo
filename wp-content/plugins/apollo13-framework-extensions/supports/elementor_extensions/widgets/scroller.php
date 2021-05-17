<?php
namespace Apollo13_FE\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Scroller extends Widget_Base {

	public function get_name() {
		return 'a13fe-scroller';
	}

	public function get_title() {
		return __( 'Theme Scroller', 'apollo13-framework-extensions' );
	}

	public function get_icon() {
		return 'eicon-post-navigation';
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
			'scroller_settings',
			[
				'label' => __( 'Settings', 'apollo13-framework-extensions' ),
			]
		);


		/** @noinspection HtmlUnknownTarget */
		$this->add_control(
			'paid_only_description',
			[
				/* translators: %s: link to Rife Pro website */
				'raw' => sprintf(__( 'This works only with the <a href="%s">Rife Pro theme</a>.', 'apollo13-framework-extensions' ), esc_url( 'https://apollo13themes.com/rife/' ) ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
			]
		);


		$this->add_control(
			'post_id',
			[
				'label' => __( 'Album/Work', 'apollo13-framework-extensions' ),
				/* translators: %s: media type */
				'description' => sprintf( esc_html__( 'Album or Work from which media(%s) should be taken.', 'apollo13-framework-extensions' ), esc_html__( 'images', 'apollo13-framework-extensions' ) ),
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
				'raw' => sprintf( esc_html__( 'You can edit the media(%s) used in the widget by editing selected Album or Work.', 'apollo13-framework-extensions' ), esc_html__( 'images', 'apollo13-framework-extensions' ) ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'scroller_look',
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
			'scroller_interface',
			[
				'label' => __( 'Interface', 'apollo13-framework-extensions' ),
			]
		);

		$this->add_control(
			'parallax',
			[
				'label' => __( 'Parallax', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);

		$this->add_control(
			'effect',
			[
				'label' => __( 'Effect on not active elements', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'off',
				'options' => [
					'off'        => __( 'Disabled', 'apollo13-framework-extensions' ),
					'opacity'    => __( 'Opacity', 'apollo13-framework-extensions' ),
					'scale-down' => __( 'Scale down', 'apollo13-framework-extensions' ),
					'grayscale'  => __( 'Grayscale', 'apollo13-framework-extensions' ),
					'blur'       => __( 'Blur', 'apollo13-framework-extensions' ),
				],
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => __( 'Autoplay', 'apollo13-framework-extensions' ),
				'description' => __( '0 for no autoplay, any other positive number to set the number of seconds between "scrolls".', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 3,
					'unit' => 's'
				],
				'range' => [
					's' => [
						'min' => 0,
						'max' => 15,
						'step' => 0.5,
					],
				],
				'separator' => 'before',
			]
		);


		$this->add_control(
			'texts',
			[
				'label' => __( 'Show texts', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before',
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

		//without id
		if ( $id === "" ) {
			echo esc_html__( 'Please select proper album or work to use.', 'apollo13-framework-extensions' );
			return;
		}


		//it doesn't contain items to use
		$has_images = strlen( get_post_meta( $id, '_images_n_videos', true ) ) > 2; //2 => [] - empty array
		if ( ! $has_images ) {
			/* translators: %s: media type */
			echo sprintf( esc_html__( 'Error: Selected post does not contain any media(%s) to use.', 'apollo13-framework-extensions' ), esc_html__( 'images', 'apollo13-framework-extensions' ) ) .' '. esc_html__( 'Please select proper album or work to use.', 'apollo13-framework-extensions' );
			return;
		}

		$opts = [];

		$opts['autoPlay']      = $settings['autoplay']['size'] * 1000;
		$opts['a13ShowDesc']   = $settings['texts'] === 'yes';
		$opts['a13WindowHigh'] = $settings['window_high'] === 'yes';
		$opts['a13Socials']    = $settings['socials'] === 'yes';
		$opts['a13Ratio']      = $settings['ratio'];
		$opts['a13Parallax']   = $settings['parallax'] === 'yes';
		$opts['a13Effect']     = $settings['effect'];


		//check if such options are defined in parent post. If not don't set them so they will return to theme defaults
		$test = strlen( get_post_meta( $id, '_scroller_wrap_around', true ) );


		//other settings from parent album
		if ( strlen( $test ) ) {
			$opts['wrapAround']         = get_post_meta( $id, '_scroller_wrap_around', true ) === 'on';
			$opts['contain']            = get_post_meta( $id, '_scroller_contain', true ) === 'on';
			$opts['freeScroll']         = get_post_meta( $id, '_scroller_free_scroll', true ) === 'on';
			$opts['prevNextButtons']    = get_post_meta( $id, '_scroller_arrows', true ) === 'on';
			$opts['pageDots']           = get_post_meta( $id, '_scroller_dots', true ) === 'on';
			$opts['a13CellWidth']       = get_post_meta( $id, '_scroller_cell_width', true );
			$opts['a13CellWidthMobile'] = get_post_meta( $id, '_scroller_cell_width_mobile', true );
		}


		if(function_exists('apollo13framework_make_scroller')) {
			wp_enqueue_script('flickity');
			apollo13framework_make_scroller( $opts, $id );
		}
	}

}
