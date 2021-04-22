<?php
namespace Apollo13_REE;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Widget_Base;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Widget_Writing_Effect_Headline extends Widget_Base {

	public function get_name() {
		return 'writing-effect-headline';
	}

	public function get_title() {
		return __( 'Writing Effect Headline', 'rife-elementor-extensions' );
	}

	public function get_icon() {
		return 'fa fa-i-cursor';
	}

	public function get_categories() {
		return [ 'basic' ];
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'text_elements',
			[
				'label' => __( 'Headline', 'rife-elementor-extensions' ),
			]
		);

		$this->add_control(
			'before_text',
			[
				'label' => __( 'Before Text', 'rife-elementor-extensions' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'Static text part before',
				'placeholder' => __( 'Enter your headline', 'rife-elementor-extensions' ),
				'label_block' => true,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'written_text',
			[
				'label' => __( 'Written Text', 'rife-elementor-extensions' ),
				'type' => Controls_Manager::TEXTAREA,
				'placeholder' => __( 'Enter each word in a separate line', 'rife-elementor-extensions' ),
				'description' => __( 'Enter each word in a separate line', 'rife-elementor-extensions' ),
				'separator' => 'none',
				'default' => "First line\n2nd line\nlast line",
				'rows' => 5,
			]
		);

		$this->add_control(
			'after_text',
			[
				'label' => __( 'After Text', 'rife-elementor-extensions' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter your headline', 'rife-elementor-extensions' ),
				'label_block' => true,
				'separator' => 'none',
			]
		);

		$this->add_control(
			'writing_speed',
			[
				'label' => __( 'Writing speed', 'rife-elementor-extensions' ),
				'type' => Controls_Manager::SLIDER,
				'description' => __( 'Lower is faster', 'rife-elementor-extensions' ),
				'default' => [
					'size' => 10,
					'unit' => 'ms'
				],
				'range' => [
					'ms' => [
						'min' => 10,
						'max' => 1000,
						'step' => 1,
					],
				],
			]
		);

		$this->add_control(
			'loop',
			[
				'label' => __( 'Loop writing effect', 'rife-elementor-extensions' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'off' => __( 'Off', 'rife-elementor-extensions' ),
				'on' => __( 'On', 'rife-elementor-extensions' ),
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label' => __( 'Alignment', 'rife-elementor-extensions' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'rife-elementor-extensions' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'rife-elementor-extensions' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'rife-elementor-extensions' ),
						'icon' => 'fa fa-align-right',
					],
				],
				'default' => 'center',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .a13ree-written-headline' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'tag',
			[
				'label' => __( 'HTML Tag', 'rife-elementor-extensions' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h3',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_text',
			[
				'label' => __( 'Headline', 'rife-elementor-extensions' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Text Color', 'rife-elementor-extensions' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				],
				'selectors' => [
					'{{WRAPPER}} .a13ree-written-headline' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .a13ree-written-headline',
			]
		);

		$this->add_control(
			'heading_words_style',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Written Text', 'rife-elementor-extensions' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'words_color',
			[
				'label' => __( 'Text Color', 'rife-elementor-extensions' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .a13ree-written-headline .written-lines, {{WRAPPER}} .typed-cursor' => 'color: {{VALUE}}',
				],

			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'words_typography',
				'selector' => '{{WRAPPER}} .a13ree-written-headline .written-lines, {{WRAPPER}} .typed-cursor',
				'exclude' => ['font_size'],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$tag = method_exists('\Elementor\Utils', 'validate_html_tag' ) ? Utils::validate_html_tag( $settings['tag'] ) : $settings['tag'];

		$this->add_render_attribute( 'headline',
			[
				'class' => 'a13ree-written-headline',
				'data-speed' => $settings['writing_speed']['size'],
				'data-loop' => $settings['loop'] === 'yes' ? 1 : 0,
			]
		);

		wp_enqueue_script( 'jquery-typed' );
		wp_enqueue_script( 'a13ree-frontend' );

		?>
		<<?php echo $tag; ?> <?php echo $this->get_render_attribute_string( 'headline' ); ?>>
			<?php if ( ! empty( $settings['before_text'] ) ) : ?>
				<span class="before-written"><?php echo $settings['before_text']; ?></span>
			<?php endif; ?>

			<?php if ( ! empty( $settings['written_text'] ) ) : ?>
				<span class="written-lines elementor-screen-only"><?php echo $settings['written_text']; ?></span>
			<?php endif; ?>

			<?php if ( ! empty( $settings['after_text'] ) ) : ?>
				<span class="after-written"><?php echo $settings['after_text']; ?></span>
			<?php endif; ?>
		</<?php echo $tag; ?>>
		<?php
	}

	protected function _content_template() {
		?>
		<#
		var headlineClasses = 'a13ree-written-headline';
		var speed = settings.writing_speed.size;
		var loop = settings.loop === 'yes' ? 1 : 0;
		#>
		<{{{ settings.tag }}} class="{{{ headlineClasses }}}" data-speed="{{{ speed }}}" data-loop="{{{ loop }}}">
			<# if ( settings.before_text ) { #>
				<span class="before-written">{{{ settings.before_text }}}</span>
			<# } #>

			<# if ( settings.written_text ) { #>
				<span class="written-lines elementor-screen-only">{{{ settings.written_text }}}</span>
			<# } #>

			<# if ( settings.after_text ) { #>
				<span class="after-written">{{{ settings.after_text }}}</span>
			<# } #>
		</{{{ settings.tag }}}>
		<?php
	}
}
