<?php
namespace PixelsCore\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Progress_Bar_Widget extends Widget_Base {

	use Widget_Assets_Trait;

	private const RING_RADIUS = 45;

	public function get_name(): string {
		return 'pixels-progress-bar';
	}

	public function get_title(): string {
		return esc_html__( 'Progress Bar', 'pixels-core-creative-tools-for-elementor' );
	}

	public function get_icon(): string {
		return 'pixels-icon pixels-icon-progress-bar';
	}

	public function get_categories(): array {
		return [ 'pixels-core' ];
	}

	public function get_keywords(): array {
		return [ 'progress', 'bar', 'skill', 'horizontal', 'vertical', 'circle', 'chart', 'pixels' ];
	}

	/**
	 * @return array<string, string>
	 */
	private function get_variant_definitions(): array {
		return [
			'horizontal'  => esc_html__( 'Horizontal', 'pixels-core-creative-tools-for-elementor' ),
			'vertical'    => esc_html__( 'Vertical', 'pixels-core-creative-tools-for-elementor' ),
			'circle'      => esc_html__( 'Circle', 'pixels-core-creative-tools-for-elementor' ),
			'semi-circle' => esc_html__( 'Semi Circle', 'pixels-core-creative-tools-for-elementor' ),
		];
	}

	/**
	 * @return list<string>
	 */
	private function get_ring_bar_types(): array {
		return [ 'circle', 'semi-circle' ];
	}

	private function is_ring_bar_type( string $bar_type ): bool {
		return in_array( $bar_type, $this->get_ring_bar_types(), true );
	}

	protected function get_assets_slug(): string {
		return 'progress_bar';
	}

	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_horizontal_style_controls();
		$this->register_vertical_style_controls();
		$this->register_circle_style_controls();
		$this->register_semi_circle_style_controls();
		$this->register_title_style_controls();
		$this->register_percent_style_controls();
		$this->register_description_style_controls();
	}

	private function register_content_controls(): void {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Progress Bar', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'bar_type',
			[
				'label'   => esc_html__( 'Type', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => $this->get_variant_definitions(),
			]
		);

		$this->add_control(
			'fill_type',
			[
				'label'     => esc_html__( 'Fill Style', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'solid',
				'options'   => [
					'solid'    => esc_html__( 'Solid', 'pixels-core-creative-tools-for-elementor' ),
					'gradient' => esc_html__( 'Gradient', 'pixels-core-creative-tools-for-elementor' ),
					'striped'  => esc_html__( 'Striped', 'pixels-core-creative-tools-for-elementor' ),
				],
				'condition' => [
					'bar_type' => [ 'horizontal', 'vertical' ],
				],
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Skill Name', 'pixels-core-creative-tools-for-elementor' ),
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'percent',
			[
				'label'   => esc_html__( 'Percentage', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 75,
				],
				'range'   => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
			]
		);

		$this->add_control(
			'description',
			[
				'label'       => esc_html__( 'Description', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => '',
				'placeholder' => esc_html__( 'Optional description', 'pixels-core-creative-tools-for-elementor' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'show_title',
			[
				'label'        => esc_html__( 'Show Title', 'pixels-core-creative-tools-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'pixels-core-creative-tools-for-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_percent',
			[
				'label'        => esc_html__( 'Show Percentage', 'pixels-core-creative-tools-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'pixels-core-creative-tools-for-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'show_description',
			[
				'label'        => esc_html__( 'Show Description', 'pixels-core-creative-tools-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'pixels-core-creative-tools-for-elementor' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'percent_position',
			[
				'label'     => esc_html__( 'Percentage Position', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'outside',
				'options'   => [
					'outside' => esc_html__( 'Outside Bar', 'pixels-core-creative-tools-for-elementor' ),
					'inside'  => esc_html__( 'Inside Bar', 'pixels-core-creative-tools-for-elementor' ),
				],
				'condition' => [
					'bar_type'     => [ 'horizontal', 'vertical' ],
					'show_percent' => 'yes',
				],
			]
		);

		$this->add_control(
			'animate',
			[
				'label'        => esc_html__( 'Animate on Scroll', 'pixels-core-creative-tools-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'pixels-core-creative-tools-for-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'animation_duration',
			[
				'label'      => esc_html__( 'Animation Duration (ms)', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 200,
						'max'  => 3000,
						'step' => 50,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 1200,
				],
				'condition'  => [
					'animate' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_linear_variant_style_controls( string $variant ): void {
		$track_selector = '{{WRAPPER}} .pixels-core-progress-bar__track';
		$fill_selector  = '{{WRAPPER}} .pixels-core-progress-bar__fill';

		if ( 'horizontal' === $variant ) {
			$this->add_responsive_control(
				'horizontal_height',
				[
					'label'      => esc_html__( 'Height', 'pixels-core-creative-tools-for-elementor' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range'      => [
						'px' => [
							'min' => 2,
							'max' => 60,
						],
					],
					'default'    => [
						'unit' => 'px',
						'size' => 8,
					],
					'selectors'  => [
						$track_selector => 'height: {{SIZE}}{{UNIT}};',
					],
				]
			);
		} else {
			$this->add_responsive_control(
				'vertical_width',
				[
					'label'      => esc_html__( 'Width', 'pixels-core-creative-tools-for-elementor' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range'      => [
						'px' => [
							'min' => 40,
							'max' => 200,
						],
					],
					'default'    => [
						'unit' => 'px',
						'size' => 80,
					],
					'selectors'  => [
						$track_selector => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'vertical_height',
				[
					'label'      => esc_html__( 'Height', 'pixels-core-creative-tools-for-elementor' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ 'px' ],
					'range'      => [
						'px' => [
							'min' => 100,
							'max' => 400,
						],
					],
					'default'    => [
						'unit' => 'px',
						'size' => 220,
					],
					'selectors'  => [
						$track_selector => 'height: {{SIZE}}{{UNIT}};',
					],
				]
			);
		}

		$this->add_control(
			$variant . '_track_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ececec',
				'selectors' => [
					$track_selector => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$variant . '_progress_color',
			[
				'label'     => esc_html__( 'Progress Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'horizontal' === $variant ? '#6c5ce7' : '#0984e3',
				'selectors' => [
					'{{WRAPPER}}' => '--pixels-progress-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => $variant . '_fill_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => $fill_selector,
				'condition' => [
					'fill_type!' => 'striped',
				],
			]
		);

		$this->add_responsive_control(
			$variant . '_track_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$track_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			$variant . '_fill_border_radius',
			[
				'label'      => esc_html__( 'Progress Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$fill_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => $variant . '_track_shadow',
				'selector' => $track_selector,
			]
		);
	}

	private function register_horizontal_style_controls(): void {
		$this->start_controls_section(
			'section_horizontal_style',
			[
				'label'     => esc_html__( 'Horizontal', 'pixels-core-creative-tools-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'bar_type' => 'horizontal',
				],
			]
		);

		$this->register_linear_variant_style_controls( 'horizontal' );

		$this->end_controls_section();
	}

	private function register_vertical_style_controls(): void {
		$this->start_controls_section(
			'section_vertical_style',
			[
				'label'     => esc_html__( 'Vertical', 'pixels-core-creative-tools-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'bar_type' => 'vertical',
				],
			]
		);

		$this->register_linear_variant_style_controls( 'vertical' );

		$this->end_controls_section();
	}

	private function register_ring_variant_style_controls( string $variant ): void {
		$ring_selector = '{{WRAPPER}} .pixels-core-progress-bar__ring';

		$size_control = [
			'label'      => esc_html__( 'Size', 'pixels-core-creative-tools-for-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [
				'px' => [
					'min' => 80,
					'max' => 260,
				],
			],
			'default'    => [
				'unit' => 'px',
				'size' => 140,
			],
			'selectors'  => [
				$ring_selector => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
			],
		];

		if ( 'semi-circle' === $variant ) {
			$size_control['selectors']['{{WRAPPER}} .pixels-core-progress-bar--semi-circle .pixels-core-progress-bar__ring'] = 'height: calc({{SIZE}}{{UNIT}} / 2);';
		}

		$this->add_responsive_control( $variant . '_size', $size_control );

		$this->add_control(
			$variant . '_track_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e8e8e8',
				'selectors' => [
					'{{WRAPPER}} .pixels-core-progress-bar__ring-track' => 'stroke: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			$variant . '_progress_color',
			[
				'label'     => esc_html__( 'Progress Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#00cec9',
				'selectors' => [
					'{{WRAPPER}}' => '--pixels-progress-color: {{VALUE}};',
					'{{WRAPPER}} .pixels-core-progress-bar__ring-progress' => 'stroke: {{VALUE}};',
				],
			]
		);

		$stroke_defaults = [
			'circle'      => 4,
			'semi-circle' => 6,
		];

		$this->add_control(
			$variant . '_stroke_width',
			[
				'label'     => esc_html__( 'Stroke Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 2,
						'max' => 20,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => $stroke_defaults[ $variant ] ?? 6,
				],
				'selectors' => [
					'{{WRAPPER}} .pixels-core-progress-bar__ring-track, {{WRAPPER}} .pixels-core-progress-bar__ring-progress' => 'stroke-width: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => $variant . '_ring_shadow',
				'selector' => $ring_selector,
			]
		);
	}

	private function register_circle_style_controls(): void {
		$this->start_controls_section(
			'section_circle_style',
			[
				'label'     => esc_html__( 'Circle', 'pixels-core-creative-tools-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'bar_type' => 'circle',
				],
			]
		);

		$this->register_ring_variant_style_controls( 'circle' );

		$this->end_controls_section();
	}

	private function register_semi_circle_style_controls(): void {
		$this->start_controls_section(
			'section_semi_circle_style',
			[
				'label'     => esc_html__( 'Semi Circle', 'pixels-core-creative-tools-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'bar_type' => 'semi-circle',
				],
			]
		);

		$this->register_ring_variant_style_controls( 'semi-circle' );

		$this->end_controls_section();
	}

	private function register_title_style_controls(): void {
		$this->start_controls_section(
			'section_title_style',
			[
				'label'     => esc_html__( 'Title', 'pixels-core-creative-tools-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .pixels-core-progress-bar__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .pixels-core-progress-bar__title',
			]
		);

		$this->add_responsive_control(
			'title_padding',
			[
				'label'      => esc_html__( 'Padding', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-progress-bar__title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->add_responsive_control(
			'title_margin',
			[
				'label'      => esc_html__( 'Margin', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-progress-bar__title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_percent_style_controls(): void {
		$this->start_controls_section(
			'section_percent_style',
			[
				'label'     => esc_html__( 'Percentage', 'pixels-core-creative-tools-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_percent' => 'yes',
				],
			]
		);

		$this->add_control(
			'percent_color',
			[
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .pixels-core-progress-bar__percent' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'percent_typography',
				'selector' => '{{WRAPPER}} .pixels-core-progress-bar__percent',
			]
		);

		$this->add_responsive_control(
			'percent_padding',
			[
				'label'      => esc_html__( 'Padding', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-progress-bar__percent' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->add_responsive_control(
			'percent_margin',
			[
				'label'      => esc_html__( 'Margin', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-progress-bar__percent' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_description_style_controls(): void {
		$this->start_controls_section(
			'section_description_style',
			[
				'label'     => esc_html__( 'Description', 'pixels-core-creative-tools-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_description' => 'yes',
				],
			]
		);

		$this->add_control(
			'description_color',
			[
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#777777',
				'selectors' => [
					'{{WRAPPER}} .pixels-core-progress-bar__description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .pixels-core-progress-bar__description',
			]
		);

		$this->add_responsive_control(
			'description_padding',
			[
				'label'      => esc_html__( 'Padding', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-progress-bar__description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->add_responsive_control(
			'description_margin',
			[
				'label'      => esc_html__( 'Margin', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-progress-bar__description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function get_ring_circumference(): float {
		return 2 * M_PI * self::RING_RADIUS;
	}

	private function get_semi_circumference(): float {
		return M_PI * self::RING_RADIUS;
	}

	/**
	 * @param array<string, mixed> $settings Widget settings.
	 */
	private function get_percent_value( array $settings ): int {
		$percent = $settings['percent']['size'] ?? 0;

		return max( 0, min( 100, (int) $percent ) );
	}

	/**
	 * @param array<string, mixed> $settings Widget settings.
	 */
	private function render_percent_text( array $settings, int $percent ): void {
		if ( 'yes' !== ( $settings['show_percent'] ?? '' ) ) {
			return;
		}

		printf(
			'<span class="pixels-core-progress-bar__percent">%s</span>',
			esc_html( $percent . '%' )
		);
	}

	/**
	 * @param array<string, mixed> $settings Widget settings.
	 */
	private function render_title( array $settings ): void {
		if ( 'yes' !== ( $settings['show_title'] ?? '' ) || empty( $settings['title'] ) ) {
			return;
		}

		printf(
			'<span class="pixels-core-progress-bar__title">%s</span>',
			esc_html( $settings['title'] )
		);
	}

	/**
	 * @param array<string, mixed> $settings Widget settings.
	 */
	private function render_description( array $settings ): void {
		if ( 'yes' !== ( $settings['show_description'] ?? '' ) || empty( $settings['description'] ) ) {
			return;
		}

		printf(
			'<p class="pixels-core-progress-bar__description">%s</p>',
			esc_html( $settings['description'] )
		);
	}

	/**
	 * @param array<string, mixed> $settings Widget settings.
	 */
	private function render_ring_item( array $settings, string $bar_type ): void {
		$percent       = $this->get_percent_value( $settings );
		$is_semi       = 'semi-circle' === $bar_type;
		$circumference = $is_semi ? $this->get_semi_circumference() : $this->get_ring_circumference();
		$offset        = $circumference * ( 1 - ( $percent / 100 ) );

		$this->add_render_attribute( 'item', 'class', 'pixels-core-progress-bar__item' );
		$this->add_render_attribute( 'item', 'data-percent', (string) $percent );
		?>
		<div <?php $this->print_render_attribute_string( 'item' ); ?>>
			<div class="pixels-core-progress-bar__ring" data-circumference="<?php echo esc_attr( (string) $circumference ); ?>">
				<svg class="pixels-core-progress-bar__ring-svg" viewBox="<?php echo esc_attr( $is_semi ? '0 0 100 55' : '0 0 100 100' ); ?>" aria-hidden="true">
					<?php if ( $is_semi ) : ?>
						<path
							class="pixels-core-progress-bar__ring-track"
							d="M 5 50 A 45 45 0 0 1 95 50"
							fill="none"
						/>
						<path
							class="pixels-core-progress-bar__ring-progress"
							d="M 5 50 A 45 45 0 0 1 95 50"
							fill="none"
							stroke-dasharray="<?php echo esc_attr( (string) $circumference ); ?>"
							stroke-dashoffset="<?php echo esc_attr( (string) $offset ); ?>"
						/>
					<?php else : ?>
						<circle class="pixels-core-progress-bar__ring-track" cx="50" cy="50" r="<?php echo esc_attr( (string) self::RING_RADIUS ); ?>" />
						<circle
							class="pixels-core-progress-bar__ring-progress"
							cx="50"
							cy="50"
							r="<?php echo esc_attr( (string) self::RING_RADIUS ); ?>"
							stroke-dasharray="<?php echo esc_attr( (string) $circumference ); ?>"
							stroke-dashoffset="<?php echo esc_attr( (string) $offset ); ?>"
						/>
					<?php endif; ?>
				</svg>
				<div class="pixels-core-progress-bar__ring-center">
					<?php $this->render_percent_text( $settings, $percent ); ?>
				</div>
			</div>
			<?php $this->render_title( $settings ); ?>
			<?php $this->render_description( $settings ); ?>
		</div>
		<?php
	}

	protected function render(): void {
		$settings        = $this->get_settings_for_display();
		$bar_type        = $settings['bar_type'] ?? 'horizontal';
		$fill_type       = $settings['fill_type'] ?? 'solid';
		$percent         = $this->get_percent_value( $settings );
		$percent_outside = 'outside' === ( $settings['percent_position'] ?? 'outside' );
		$is_vertical     = 'vertical' === $bar_type;
		$is_ring         = $this->is_ring_bar_type( $bar_type );

		$wrapper_classes = [
			'pixels-core-progress-bar',
			'pixels-core-progress-bar--' . $bar_type,
		];

		if ( ! $is_ring ) {
			$wrapper_classes[] = 'pixels-core-progress-bar--fill-' . $fill_type;
			$wrapper_classes[] = 'pixels-core-progress-bar--percent-' . ( $settings['percent_position'] ?? 'outside' );
		}

		if ( 'yes' === ( $settings['animate'] ?? '' ) ) {
			$wrapper_classes[] = 'pixels-core-progress-bar--animate';
		}

		$this->add_render_attribute( 'wrapper', 'class', $wrapper_classes );

		if ( 'yes' === ( $settings['animate'] ?? '' ) ) {
			$duration = (int) ( $settings['animation_duration']['size'] ?? 1200 );
			$this->add_render_attribute( 'wrapper', 'data-duration', (string) $duration );
		}

		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="pixels-core-progress-bar__list">
				<?php if ( $is_ring ) : ?>
					<?php $this->render_ring_item( $settings, $bar_type ); ?>
				<?php else : ?>
					<?php
					$item_classes = [ 'pixels-core-progress-bar__item' ];

					if ( $is_vertical ) {
						$item_classes[] = 'pixels-core-progress-bar__item--vertical';
					}

					$this->add_render_attribute( 'item', 'class', $item_classes );
					$this->add_render_attribute( 'item', 'data-percent', (string) $percent );
					?>
					<div <?php $this->print_render_attribute_string( 'item' ); ?>>
						<?php if ( $is_vertical ) : ?>
							<div class="pixels-core-progress-bar__track">
								<div class="pixels-core-progress-bar__fill" style="height: <?php echo esc_attr( (string) $percent ); ?>%;">
									<?php if ( ! $percent_outside ) : ?>
										<?php $this->render_percent_text( $settings, $percent ); ?>
									<?php endif; ?>
								</div>
							</div>
							<div class="pixels-core-progress-bar__meta">
								<?php if ( $percent_outside ) : ?>
									<?php $this->render_percent_text( $settings, $percent ); ?>
								<?php endif; ?>
								<?php $this->render_title( $settings ); ?>
								<?php $this->render_description( $settings ); ?>
							</div>
						<?php else : ?>
							<?php if ( $percent_outside && ( 'yes' === ( $settings['show_title'] ?? '' ) || 'yes' === ( $settings['show_percent'] ?? '' ) ) ) : ?>
								<div class="pixels-core-progress-bar__header">
									<?php $this->render_title( $settings ); ?>
									<?php $this->render_percent_text( $settings, $percent ); ?>
								</div>
							<?php endif; ?>
							<div class="pixels-core-progress-bar__track">
								<div class="pixels-core-progress-bar__fill" style="width: <?php echo esc_attr( (string) $percent ); ?>%;">
									<?php if ( ! $percent_outside ) : ?>
										<?php $this->render_percent_text( $settings, $percent ); ?>
									<?php endif; ?>
								</div>
							</div>
							<?php $this->render_description( $settings ); ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	protected function content_template(): void {
		?>
		<#
		const barType = settings.bar_type || 'horizontal',
			fillType = settings.fill_type || 'solid',
			showTitle = settings.show_title === 'yes',
			showPercent = settings.show_percent === 'yes',
			showDescription = settings.show_description === 'yes',
			percentPosition = settings.percent_position || 'outside',
			animate = settings.animate === 'yes',
			duration = settings.animation_duration ? settings.animation_duration.size : 1200,
			percent = Math.max( 0, Math.min( 100, settings.percent ? settings.percent.size : 0 ) ),
			isVertical = barType === 'vertical',
			isRing = [ 'circle', 'semi-circle' ].indexOf( barType ) !== -1,
			ringRadius = 45,
			fullCirc = 2 * Math.PI * ringRadius,
			semiCirc = Math.PI * ringRadius,
			circumference = barType === 'semi-circle' ? semiCirc : fullCirc,
			offset = circumference * ( 1 - ( percent / 100 ) );

		let wrapperClass = 'pixels-core-progress-bar pixels-core-progress-bar--' + barType;

		if ( ! isRing ) {
			wrapperClass += ' pixels-core-progress-bar--fill-' + fillType + ' pixels-core-progress-bar--percent-' + percentPosition;
		}

		if ( animate ) {
			wrapperClass += ' pixels-core-progress-bar--animate';
		}
		#>
		<div class="{{ wrapperClass }}"<# if ( animate ) { #> data-duration="{{ duration }}"<# } #>>
			<div class="pixels-core-progress-bar__list">
				<# if ( isRing ) { #>
					<div class="pixels-core-progress-bar__item" data-percent="{{ percent }}">
						<div class="pixels-core-progress-bar__ring" data-circumference="{{ circumference }}">
							<svg class="pixels-core-progress-bar__ring-svg" viewBox="<# if ( barType === 'semi-circle' ) { #>0 0 100 55<# } else { #>0 0 100 100<# } #>" aria-hidden="true">
								<# if ( barType === 'semi-circle' ) { #>
									<path class="pixels-core-progress-bar__ring-track" d="M 5 50 A 45 45 0 0 1 95 50" fill="none" />
									<path class="pixels-core-progress-bar__ring-progress" d="M 5 50 A 45 45 0 0 1 95 50" fill="none" stroke-dasharray="{{ circumference }}" stroke-dashoffset="{{ offset }}" />
								<# } else { #>
									<circle class="pixels-core-progress-bar__ring-track" cx="50" cy="50" r="45" />
									<circle class="pixels-core-progress-bar__ring-progress" cx="50" cy="50" r="45" stroke-dasharray="{{ circumference }}" stroke-dashoffset="{{ offset }}" />
								<# } #>
							</svg>
							<div class="pixels-core-progress-bar__ring-center">
								<# if ( showPercent ) { #>
									<span class="pixels-core-progress-bar__percent">{{ percent }}%</span>
								<# } #>
							</div>
						</div>
						<# if ( showTitle && settings.title ) { #>
							<span class="pixels-core-progress-bar__title">{{{ settings.title }}}</span>
						<# } #>
						<# if ( showDescription && settings.description ) { #>
							<p class="pixels-core-progress-bar__description">{{{ settings.description }}}</p>
						<# } #>
					</div>
				<# } else { #>
					<div class="pixels-core-progress-bar__item<# if ( isVertical ) { #> pixels-core-progress-bar__item--vertical<# } #>" data-percent="{{ percent }}">
						<# if ( isVertical ) { #>
							<div class="pixels-core-progress-bar__track">
								<div class="pixels-core-progress-bar__fill" style="height: {{ percent }}%;">
									<# if ( showPercent && percentPosition === 'inside' ) { #>
										<span class="pixels-core-progress-bar__percent">{{ percent }}%</span>
									<# } #>
								</div>
							</div>
							<div class="pixels-core-progress-bar__meta">
								<# if ( showPercent && percentPosition === 'outside' ) { #>
									<span class="pixels-core-progress-bar__percent">{{ percent }}%</span>
								<# } #>
								<# if ( showTitle && settings.title ) { #>
									<span class="pixels-core-progress-bar__title">{{{ settings.title }}}</span>
								<# } #>
								<# if ( showDescription && settings.description ) { #>
									<p class="pixels-core-progress-bar__description">{{{ settings.description }}}</p>
								<# } #>
							</div>
						<# } else { #>
							<# if ( percentPosition === 'outside' && ( showTitle || showPercent ) ) { #>
								<div class="pixels-core-progress-bar__header">
									<# if ( showTitle && settings.title ) { #>
										<span class="pixels-core-progress-bar__title">{{{ settings.title }}}</span>
									<# } #>
									<# if ( showPercent ) { #>
										<span class="pixels-core-progress-bar__percent">{{ percent }}%</span>
									<# } #>
								</div>
							<# } #>
							<div class="pixels-core-progress-bar__track">
								<div class="pixels-core-progress-bar__fill" style="width: {{ percent }}%;">
									<# if ( showPercent && percentPosition === 'inside' ) { #>
										<span class="pixels-core-progress-bar__percent">{{ percent }}%</span>
									<# } #>
								</div>
							</div>
							<# if ( showDescription && settings.description ) { #>
								<p class="pixels-core-progress-bar__description">{{{ settings.description }}}</p>
							<# } #>
						<# } #>
					</div>
				<# } #>
			</div>
		</div>
		<?php
	}
}
