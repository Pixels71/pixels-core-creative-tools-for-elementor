<?php
/**
 * Rotator text widget.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rotator text widget.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */
class Rotator_Text_Widget extends Widget_Base {

	use Widget_Assets_Trait;

	private const VIEWBOX_SIZE = 200;

	private const INNER_CIRCLE_RADIUS = 68;

	private const INNER_SQUARE_SIZE = 118;

	/**
	 * Get name.
	 *
	 * @return string Result.
	 */
	public function get_name(): string {
		return 'pixeccte-rotator-text';
	}

	/**
	 * Get title.
	 *
	 * @return string Result.
	 */
	public function get_title(): string {
		return esc_html__( 'Rotator Text', 'pixels-core-creative-tools-for-elementor' );
	}

	/**
	 * Get icon.
	 *
	 * @return string Result.
	 */
	public function get_icon(): string {
		return 'pixeccte-icon pixeccte-icon-rotator-text';
	}

	/**
	 * Get categories.
	 *
	 * @return array Result.
	 */
	public function get_categories(): array {
		return array( 'pixeccte' );
	}

	/**
	 * Get keywords.
	 *
	 * @return array Result.
	 */
	public function get_keywords(): array {
		return array( 'rotator', 'circular', 'text', 'rotation', 'scroll', 'branding', 'logo', 'pixeccte' );
	}

	/**
	 * Get assets slug.
	 *
	 * @return string Result.
	 */
	protected function get_assets_slug(): string {
		return 'rotator_text';
	}

	/**
	 * Get layout options.
	 *
	 * @return array Result.
	 */
	private function getLayoutOptions(): array {
		return array(
			'single'         => esc_html__( 'Single Rotation Text', 'pixels-core-creative-tools-for-elementor' ),
			'dual'           => esc_html__( 'Dual Rotation Text', 'pixels-core-creative-tools-for-elementor' ),
			'scroll'         => esc_html__( 'Scroll-Triggered Gauge', 'pixels-core-creative-tools-for-elementor' ),
			'ripple'         => esc_html__( 'Concentric Ripple & Pulse', 'pixels-core-creative-tools-for-elementor' ),
			'rounded-square' => esc_html__( 'Rounded Square', 'pixels-core-creative-tools-for-elementor' ),
		);
	}

	/**
	 * Normalize layout.
	 *
	 * @param string $layout Layout.
	 * @return string Result.
	 */
	private function normalizeLayout( string $layout ): string {
		if ( 'orbit' === $layout ) {
			return 'dual';
		}

		if ( 'shape-shifter' === $layout ) {
			return 'rounded-square';
		}

		return array_key_exists( $layout, $this->getLayoutOptions() ) ? $layout : 'single';
	}

	/**
	 * Register controls.
	 */
	protected function register_controls(): void {
		$this->registerContentControls();
		$this->registerAnimationControls();
		$this->registerContainerStyleControls();
		$this->registerRingStyleControls();
		$this->registerCenterStyleControls();
	}

	/**
	 * Register content controls.
	 */
	private function registerContentControls(): void {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Rotator Text', 'pixels-core-creative-tools-for-elementor' ),
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => esc_html__( 'Layout Variation', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'single',
				'options' => $this->getLayoutOptions(),
			)
		);

		$this->add_control(
			'inner_text',
			array(
				'label'       => esc_html__( 'Ring Text', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'CREATING IMPACT BUILDING BRANDS, AND', 'pixels-core-creative-tools-for-elementor' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'outer_text',
			array(
				'label'       => esc_html__( 'Outer Ring Text', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'DESIGN STRATEGY DIGITAL EXPERIENCE INNOVATION', 'pixels-core-creative-tools-for-elementor' ),
				'label_block' => true,
				'condition'   => array(
					'layout' => 'dual',
				),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'center_type',
			array(
				'label'   => esc_html__( 'Center Content', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'image',
				'options' => array(
					'image' => esc_html__( 'Image / Logo', 'pixels-core-creative-tools-for-elementor' ),
					'text'  => esc_html__( 'Text', 'pixels-core-creative-tools-for-elementor' ),
					'icon'  => esc_html__( 'Icon', 'pixels-core-creative-tools-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'center_image',
			array(
				'label'     => esc_html__( 'Center Image', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'center_type' => 'image',
				),
				'dynamic'   => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'center_text',
			array(
				'label'     => esc_html__( 'Center Text', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'PX', 'pixels-core-creative-tools-for-elementor' ),
				'condition' => array(
					'center_type' => 'text',
				),
				'dynamic'   => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'center_icon',
			array(
				'label'     => esc_html__( 'Center Icon', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'center_type' => 'icon',
				),
			)
		);

		$this->add_control(
			'center_link',
			array(
				'label'       => esc_html__( 'Center Link', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'pixels-core-creative-tools-for-elementor' ),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register animation controls.
	 */
	private function registerAnimationControls(): void {
		$this->start_controls_section(
			'section_animation',
			array(
				'label' => esc_html__( 'Animation', 'pixels-core-creative-tools-for-elementor' ),
			)
		);

		$this->add_control(
			'rotation_speed',
			array(
				'label'      => esc_html__( 'Rotation Duration (seconds)', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 's' ),
				'range'      => array(
					's' => array(
						'min'  => 4,
						'max'  => 60,
						'step' => 1,
					),
				),
				'default'    => array(
					'unit' => 's',
					'size' => 20,
				),
				'condition'  => array(
					'layout!' => 'scroll',
				),
			)
		);

		$this->add_control(
			'scroll_sensitivity',
			array(
				'label'      => esc_html__( 'Scroll Sensitivity', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min'  => 0.1,
						'max'  => 3,
						'step' => 0.1,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 1,
				),
				'condition'  => array(
					'layout' => 'scroll',
				),
			)
		);

		$this->add_control(
			'show_outer_ring',
			array(
				'label'        => esc_html__( 'Show Outer Ring', 'pixels-core-creative-tools-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => array(
					'layout' => 'scroll',
				),
			)
		);

		$this->add_control(
			'ripple_animation',
			array(
				'label'     => esc_html__( 'Ripple Animation', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'pulse',
				'options'   => array(
					'pulse'   => esc_html__( 'Pulse', 'pixels-core-creative-tools-for-elementor' ),
					'expand'  => esc_html__( 'Expand & Contract', 'pixels-core-creative-tools-for-elementor' ),
					'breathe' => esc_html__( 'Breathe', 'pixels-core-creative-tools-for-elementor' ),
				),
				'condition' => array(
					'layout' => 'ripple',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register container style controls.
	 */
	private function registerContainerStyleControls(): void {
		$this->start_controls_section(
			'section_container_style',
			array(
				'label' => esc_html__( 'Container', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'container_size',
			array(
				'label'      => esc_html__( 'Size', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vw', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 120,
						'max' => 600,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 320,
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-rotator-text' => '--pixeccte-rotator-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'container_background',
				'selector' => '{{WRAPPER}} .pixeccte-rotator-text__stage',
			)
		);

		$this->add_responsive_control(
			'container_padding',
			array(
				'label'      => esc_html__( 'Padding', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-rotator-text__stage' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'container_margin',
			array(
				'label'      => esc_html__( 'Margin', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-rotator-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'container_border',
				'selector' => '{{WRAPPER}} .pixeccte-rotator-text__stage',
			)
		);

		$this->add_control(
			'container_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 300,
					),
					'%'  => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'default'    => array(
					'unit' => '%',
					'size' => 50,
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-rotator-text__stage' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'container_box_shadow',
				'selector' => '{{WRAPPER}} .pixeccte-rotator-text__stage',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register ring style controls.
	 */
	private function registerRingStyleControls(): void {
		$this->start_controls_section(
			'section_ring_style',
			array(
				'label' => esc_html__( 'Text Rings', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'inner_typography',
				'label'    => esc_html__( 'Inner Ring Typography', 'pixels-core-creative-tools-for-elementor' ),
				'selector' => '{{WRAPPER}} .pixeccte-rotator-text__ring--inner text',
			)
		);

		$this->add_control(
			'inner_text_color',
			array(
				'label'     => esc_html__( 'Inner Ring Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111111',
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-rotator-text' => '--pixeccte-rotator-inner-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'outer_typography',
				'label'     => esc_html__( 'Outer Ring Typography', 'pixels-core-creative-tools-for-elementor' ),
				'selector'  => '{{WRAPPER}} .pixeccte-rotator-text__ring--outer text',
				'condition' => array(
					'layout' => array( 'dual', 'scroll' ),
				),
			)
		);

		$this->add_control(
			'outer_text_color',
			array(
				'label'     => esc_html__( 'Outer Ring Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-rotator-text' => '--pixeccte-rotator-outer-color: {{VALUE}};',
				),
				'condition' => array(
					'layout' => array( 'dual', 'scroll' ),
				),
			)
		);

		$this->add_responsive_control(
			'ring_gap',
			array(
				'label'      => esc_html__( 'Ring Spacing', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 18,
				),
				'condition'  => array(
					'layout' => array( 'dual', 'scroll' ),
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_ripple_style',
			array(
				'label'     => esc_html__( 'Ripple Rings', 'pixels-core-creative-tools-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'layout' => 'ripple',
				),
			)
		);

		$this->add_control(
			'ripple_color',
			array(
				'label'     => esc_html__( 'Border Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0, 0, 0, 0.15)',
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-rotator-text__ripple' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ripple_width',
			array(
				'label'      => esc_html__( 'Border Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 6,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 1,
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-rotator-text__ripple' => 'border-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'ripple_duration',
			array(
				'label'      => esc_html__( 'Animation Duration (seconds)', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 's' ),
				'range'      => array(
					's' => array(
						'min'  => 2,
						'max'  => 12,
						'step' => 0.5,
					),
				),
				'default'    => array(
					'unit' => 's',
					'size' => 4,
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-rotator-text' => '--pixeccte-rotator-ripple-duration: {{SIZE}}s;',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register center style controls.
	 */
	private function registerCenterStyleControls(): void {
		$this->start_controls_section(
			'section_center_style',
			array(
				'label' => esc_html__( 'Center Content', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'center_size',
			array(
				'label'      => esc_html__( 'Size', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 24,
						'max' => 200,
					),
					'%'  => array(
						'min' => 10,
						'max' => 60,
					),
				),
				'default'    => array(
					'unit' => '%',
					'size' => 28,
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-rotator-text' => '--pixeccte-rotator-center-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'center_background',
				'selector' => '{{WRAPPER}} .pixeccte-rotator-text__center',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'center_border',
				'selector' => '{{WRAPPER}} .pixeccte-rotator-text__center',
			)
		);

		$this->add_control(
			'center_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'%' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'default'    => array(
					'unit' => '%',
					'size' => 50,
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-rotator-text__center' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'center_text_typography',
				'selector'  => '{{WRAPPER}} .pixeccte-rotator-text__center-text',
				'condition' => array(
					'center_type' => 'text',
				),
			)
		);

		$this->add_control(
			'center_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-rotator-text__center-text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pixeccte-rotator-text__center-icon' => 'color: {{VALUE}}; fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'center_icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 12,
						'max' => 120,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-rotator-text__center-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixeccte-rotator-text__center-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'center_type' => 'icon',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get ring gap.
	 *
	 * @param array $settings Settings.
	 * @return float Result.
	 */
	private function getRingGap( array $settings ): float {
		$gap = $settings['ring_gap']['size'] ?? 18;

		return max( 0, (float) $gap );
	}

	/**
	 * Get text radial offset.
	 *
	 * @param string $ring Ring.
	 * @param float  $ring_gap Ring gap.
	 * @return float Result.
	 */
	private function getTextRadialOffset( string $ring, float $ring_gap ): float {
		if ( $ring_gap <= 0 ) {
			return 0.0;
		}

		$offset = $ring_gap * 0.1;

		return 'outer' === $ring ? $offset : -$offset;
	}

	/**
	 * Get path d.
	 *
	 * @param string $ring Ring.
	 * @param bool   $rounded_square Rounded square.
	 * @param float  $ring_gap Ring gap.
	 * @return string Result.
	 */
	private function getPathD( string $ring, bool $rounded_square, float $ring_gap ): string {
		$cx  = self::VIEWBOX_SIZE / 2;
		$cy  = self::VIEWBOX_SIZE / 2;
		$gap = max( 0, $ring_gap );

		if ( $rounded_square ) {
			$size   = self::INNER_SQUARE_SIZE + ( 'outer' === $ring ? $gap * 2 : 0 );
			$radius = 22 * ( $size / self::INNER_SQUARE_SIZE );
			$x      = ( self::VIEWBOX_SIZE - $size ) / 2;
			$y      = ( self::VIEWBOX_SIZE - $size ) / 2;
			$right  = $x + $size;
			$bottom = $y + $size;

			return sprintf(
				'M %1$.2F,%2$.2F H %3$.2F A %4$.2F,%4$.2F 0 0 1 %5$.2F,%6$.2F V %7$.2F A %4$.2F,%4$.2F 0 0 1 %3$.2F,%8$.2F H %1$.2F A %4$.2F,%4$.2F 0 0 1 %9$.2F,%7$.2F V %6$.2F A %4$.2F,%4$.2F 0 0 1 %1$.2F,%2$.2F Z',
				$x + $radius,
				$y,
				$right - $radius,
				$radius,
				$right,
				$y + $radius,
				$bottom - $radius,
				$bottom,
				$x
			);
		}

		$radius = self::INNER_CIRCLE_RADIUS + ( 'outer' === $ring ? $gap : 0 );

		return sprintf(
			'M %1$.2F,%2$.2F A %3$.2F,%3$.2F 0 1,1 %4$.2F,%5$.2F',
			$cx,
			$cy - $radius,
			$radius,
			$cx - 0.01,
			$cy - $radius
		);
	}

	/**
	 * Prepare ring text.
	 *
	 * @param string $text Text.
	 * @return string Result.
	 */
	private function prepareRingText( string $text ): string {
		$text = trim( preg_replace( '/\s+/', ' ', $text ) ?? '' );

		return $text;
	}

	/**
	 * Get widget data.
	 *
	 * @param array $settings Settings.
	 * @return array Result.
	 */
	private function getWidgetData( array $settings ): array {
		$layout = $this->normalizeLayout( $settings['layout'] ?? 'single' );

		return array(
			'layout'              => $layout,
			'rounded_square_path' => 'rounded-square' === $layout,
			'ring_gap'            => $this->getRingGap( $settings ),
			'rotation_speed'      => max( 4, (float) ( $settings['rotation_speed']['size'] ?? 20 ) ),
			'scroll_sensitivity'  => max( 0.1, (float) ( $settings['scroll_sensitivity']['size'] ?? 1 ) ),
			'ripple_animation'    => $settings['ripple_animation'] ?? 'pulse',
			'show_outer_ring'     => ( $settings['show_outer_ring'] ?? 'yes' ) === 'yes',
			'inner_text'          => $this->prepareRingText( (string) ( $settings['inner_text'] ?? '' ) ),
			'outer_text'          => $this->prepareRingText( (string) ( $settings['outer_text'] ?? '' ) ),
		);
	}

	/**
	 * Render center content.
	 *
	 * @param array $settings Settings.
	 */
	private function renderCenterContent( array $settings ): void {
		$center_type = $settings['center_type'] ?? 'image';
		$link        = $settings['center_link'] ?? array();
		$has_link    = ! empty( $link['url'] );

		if ( $has_link ) {
			$this->add_link_attributes( 'center_link', $link );
			$this->add_render_attribute( 'center_link', 'class', 'pixeccte-rotator-text__center-link' );
			echo '<a ';
			$this->print_render_attribute_string( 'center_link' );
			echo '>';
		}

		switch ( $center_type ) {
			case 'text':
				$text = $settings['center_text'] ?? '';
				if ( '' !== $text ) {
					echo '<span class="pixeccte-rotator-text__center-text">' . esc_html( $text ) . '</span>';
				}
				break;

			case 'icon':
				$icon = $settings['center_icon'] ?? array();
				if ( ! empty( $icon['value'] ) ) {
					echo '<span class="pixeccte-rotator-text__center-icon">';
					\Elementor\Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
					echo '</span>';
				}
				break;

			case 'image':
			default:
				$image = $settings['center_image'] ?? array();
				$url   = $image['url'] ?? '';

				if ( '' !== $url ) {
					printf(
						'<img class="pixeccte-rotator-text__center-image" src="%1$s" alt="%2$s" loading="lazy" decoding="async" />',
						esc_url( $url ),
						esc_attr( $image['alt'] ?? '' )
					);
				}
				break;
		}

		if ( $has_link ) {
			echo '</a>';
		}
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$settings            = $this->get_settings_for_display();
		$data                = $this->getWidgetData( $settings );
		$widget_id           = $this->get_id();
		$layout              = $data['layout'];
		$rounded_square_path = $data['rounded_square_path'];
		$ring_gap            = $data['ring_gap'];
		$is_dual_ring        = 'dual' === $layout;

		if ( '' === $data['inner_text'] && ( ! $is_dual_ring || '' === $data['outer_text'] ) ) {
			return;
		}

		$show_outer    = $is_dual_ring || ( 'scroll' === $layout && $data['show_outer_ring'] && '' !== $data['outer_text'] );
		$show_ripple   = 'ripple' === $layout;
		$inner_text_dy = $show_outer ? $this->getTextRadialOffset( 'inner', $ring_gap ) : 0.0;
		$outer_text_dy = $this->getTextRadialOffset( 'outer', $ring_gap );

		$inner_path_id = 'pixeccte-rotator-inner-' . $widget_id;
		$outer_path_id = 'pixeccte-rotator-outer-' . $widget_id;

		$this->add_render_attribute( 'wrapper', 'class', 'pixeccte-rotator-text' );
		$this->add_render_attribute( 'wrapper', 'class', 'pixeccte-rotator-text--layout-' . sanitize_html_class( $layout ) );

		if ( $show_ripple ) {
			$this->add_render_attribute( 'wrapper', 'class', 'pixeccte-rotator-text--ripple-' . sanitize_html_class( $data['ripple_animation'] ) );
		}

		$this->add_render_attribute( 'wrapper', 'data-layout', esc_attr( $layout ) );
		$this->add_render_attribute( 'wrapper', 'data-rotation-speed', (string) $data['rotation_speed'] );
		$this->add_render_attribute( 'wrapper', 'data-scroll-sensitivity', (string) $data['scroll_sensitivity'] );
		$this->add_render_attribute( 'wrapper', 'data-inner-text', esc_attr( $data['inner_text'] ) );

		if ( $show_outer ) {
			$this->add_render_attribute( 'wrapper', 'data-outer-text', esc_attr( $data['outer_text'] ) );
		}

		$this->add_render_attribute( 'stage', 'class', 'pixeccte-rotator-text__stage' );
		$this->add_render_attribute( 'svg', 'class', 'pixeccte-rotator-text__svg' );
		$this->add_render_attribute( 'svg', 'viewBox', '0 0 ' . self::VIEWBOX_SIZE . ' ' . self::VIEWBOX_SIZE );
		$this->add_render_attribute( 'svg', 'aria-hidden', 'true' );
		$this->add_render_attribute( 'svg', 'focusable', 'false' );
		$this->add_render_attribute( 'center', 'class', 'pixeccte-rotator-text__center' );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div <?php $this->print_render_attribute_string( 'stage' ); ?>>
				<?php if ( $show_ripple ) : ?>
					<div class="pixeccte-rotator-text__ripples" aria-hidden="true">
						<span class="pixeccte-rotator-text__ripple pixeccte-rotator-text__ripple--inner"></span>
						<span class="pixeccte-rotator-text__ripple pixeccte-rotator-text__ripple--mid"></span>
						<span class="pixeccte-rotator-text__ripple pixeccte-rotator-text__ripple--outer"></span>
					</div>
				<?php endif; ?>

				<svg <?php $this->print_render_attribute_string( 'svg' ); ?>>
					<defs>
						<path id="<?php echo esc_attr( $inner_path_id ); ?>" d="<?php echo esc_attr( $this->getPathD( 'inner', $rounded_square_path, $ring_gap ) ); ?>" />
						<?php if ( $show_outer ) : ?>
							<path id="<?php echo esc_attr( $outer_path_id ); ?>" d="<?php echo esc_attr( $this->getPathD( 'outer', $rounded_square_path, $ring_gap ) ); ?>" />
						<?php endif; ?>
					</defs>

					<?php if ( $show_outer && '' !== $data['outer_text'] ) : ?>
						<g class="pixeccte-rotator-text__ring pixeccte-rotator-text__ring--outer" data-direction="ccw">
							<text class="pixeccte-rotator-text__ring-text" dy="<?php echo esc_attr( (string) $outer_text_dy ); ?>">
								<textPath href="#<?php echo esc_attr( $outer_path_id ); ?>" startOffset="0%">
									<?php echo esc_html( $data['outer_text'] ); ?>
								</textPath>
							</text>
						</g>
					<?php endif; ?>

					<?php if ( '' !== $data['inner_text'] ) : ?>
						<g class="pixeccte-rotator-text__ring pixeccte-rotator-text__ring--inner" data-direction="cw">
							<text class="pixeccte-rotator-text__ring-text" dy="<?php echo esc_attr( (string) $inner_text_dy ); ?>">
								<textPath href="#<?php echo esc_attr( $inner_path_id ); ?>" startOffset="0%">
									<?php echo esc_html( $data['inner_text'] ); ?>
								</textPath>
							</text>
						</g>
					<?php endif; ?>
				</svg>

				<div <?php $this->print_render_attribute_string( 'center' ); ?>>
					<?php $this->renderCenterContent( $settings ); ?>
				</div>
			</div>
		</div>
		<?php
	}
}
