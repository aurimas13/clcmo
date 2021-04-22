<?php
namespace Apollo13_FE\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Socials extends Widget_Base {

	public function get_name() {
		return 'a13fe-socials';
	}

	public function get_title() {
		return __( 'Theme Social icons', 'apollo13-framework-extensions' );
	}

	public function get_icon() {
		return 'eicon-social-icons';
	}

	public function get_categories() {
		return [ 'general', 'apollo13-framework' ];
	}

	protected function _register_controls() {
		$social_colors = array(
			'black'            => esc_html__( 'Black', 'apollo13-framework-extensions' ),
			'color'            => esc_html__( 'Color', 'apollo13-framework-extensions' ),
			'white'            => esc_html__( 'White', 'apollo13-framework-extensions' ),
			'semi-transparent' => esc_html__( 'Semi transparent', 'apollo13-framework-extensions' ),
		);

		$this->start_controls_section(
			'colors',
			[
				'label' => __( 'Color', 'apollo13-framework-extensions' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);


		/** @noinspection HtmlUnknownTarget */
		$this->add_control(
			'anchor_description',
			[
				/* translators: %s: "Social icons" Customizer link */
				'raw' => sprintf( esc_html__( 'If you need to edit social links, you can do it in the Customizer in the %s settings.', 'apollo13-framework-extensions' ), '<a href="'.esc_url( admin_url( '/customize.php?autofocus[section]=section_social' ) ).'">'.esc_html__( 'Social icons', 'apollo13-framework-extensions' ).'</a>' ),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
			]
		);

		$this->add_control(
			'color',
			[
				'label' => __( 'Normal color', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::SELECT,
				'options' => $social_colors,
				'default' => 'color',
			]
		);

		$this->add_control(
			'hover_color',
			[
				'label' => __( 'Hover color', 'apollo13-framework-extensions' ),
				'type' => Controls_Manager::SELECT,
				'options' => $social_colors,
				'default' => 'semi-transparent',
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		echo apollo13framework_social_icons( $settings['color'], $settings['hover_color'] );
	}
}
