<?php
namespace PixelsCoreCreativeToolsForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Countdown_Timer_Widget extends Widget_Base {

	use Widget_Assets_Trait;

	public function get_name(): string {
		return 'pixeccte-countdown-timer';
	}

	public function get_title(): string {
		return esc_html__( 'Countdown Timer', 'pixels-core-creative-tools-for-elementor' );
	}

	public function get_icon(): string {
		return 'pixeccte-icon pixeccte-icon-countdown-timer';
	}

	public function get_categories(): array {
		return [ 'pixeccte' ];
	}

	public function get_keywords(): array {
		return [ 'countdown', 'timer', 'pixeccte' ];
	}

	protected function get_assets_slug(): string {
		return 'countdown_timer';
	}

	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	private function register_content_controls(): void {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Countdown Timer', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

        $this->add_control('due_date', [
            'label' => esc_html__('Due Date', 'pixels-core-creative-tools-for-elementor'),
            'type' => Controls_Manager::DATE_TIME,
            'default' => gmdate('Y-m-d H:i', strtotime('+7 days')),
            'description' => esc_html__('Set the target date and time for the countdown.', 'pixels-core-creative-tools-for-elementor'),
        ]);

        $this->add_control('show_days', [
            'label' => esc_html__('Show Days', 'pixels-core-creative-tools-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Show', 'pixels-core-creative-tools-for-elementor'),
            'label_off' => esc_html__('Hide', 'pixels-core-creative-tools-for-elementor'),
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->add_control('show_hours', [
            'label' => esc_html__('Show Hours', 'pixels-core-creative-tools-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Show', 'pixels-core-creative-tools-for-elementor'),
            'label_off' => esc_html__('Hide', 'pixels-core-creative-tools-for-elementor'),
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->add_control('show_minutes', [
            'label' => esc_html__('Show Minutes', 'pixels-core-creative-tools-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Show', 'pixels-core-creative-tools-for-elementor'),
            'label_off' => esc_html__('Hide', 'pixels-core-creative-tools-for-elementor'),
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->add_control('show_seconds', [
            'label' => esc_html__('Show Seconds', 'pixels-core-creative-tools-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Show', 'pixels-core-creative-tools-for-elementor'),
            'label_off' => esc_html__('Hide', 'pixels-core-creative-tools-for-elementor'),
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->add_control('show_separator', [
            'label' => esc_html__('Show Separator', 'pixels-core-creative-tools-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Show', 'pixels-core-creative-tools-for-elementor'),
            'label_off' => esc_html__('Hide', 'pixels-core-creative-tools-for-elementor'),
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->add_control(
            'show_circle_progress',
            [
                'label' => esc_html__( 'Circle Progress', 'pixels-core-creative-tools-for-elementor' ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'pixels-core-creative-tools-for-elementor' ),
                'label_off' => esc_html__( 'Hide', 'pixels-core-creative-tools-for-elementor' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control('days_label', [
            'label' => esc_html__('Days Label', 'pixels-core-creative-tools-for-elementor'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Days', 'pixels-core-creative-tools-for-elementor'),
            'condition' => [
                'show_days' => 'yes',
            ],
        ]);

        $this->add_control('hours_label', [
            'label' => esc_html__('Hours Label', 'pixels-core-creative-tools-for-elementor'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Hours', 'pixels-core-creative-tools-for-elementor'),
            'condition' => [
                'show_hours' => 'yes',
            ],
        ]);

        $this->add_control('minutes_label', [
            'label' => esc_html__('Minutes Label', 'pixels-core-creative-tools-for-elementor'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Minutes', 'pixels-core-creative-tools-for-elementor'),
            'condition' => [
                'show_minutes' => 'yes',
            ],
        ]);

        $this->add_control('seconds_label', [
            'label' => esc_html__('Seconds Label', 'pixels-core-creative-tools-for-elementor'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__('Seconds', 'pixels-core-creative-tools-for-elementor'),
            'condition' => [
                'show_seconds' => 'yes',
            ],
        ]);

        $this->add_control('expiry_action', [
            'label' => esc_html__('When Timer Ends', 'pixels-core-creative-tools-for-elementor'),
            'type' => Controls_Manager::SELECT,
            'default' => 'show_message',
            'options' => [
                'none' => esc_html__('Do Nothing', 'pixels-core-creative-tools-for-elementor'),
                'hide' => esc_html__('Hide Timer', 'pixels-core-creative-tools-for-elementor'),
                'show_message' => esc_html__('Show Message', 'pixels-core-creative-tools-for-elementor'),
            ],
        ]);

        $this->add_control('expiry_message', [
            'label' => esc_html__('Expiry Message', 'pixels-core-creative-tools-for-elementor'),
            'type' => Controls_Manager::TEXTAREA,
            'default' => esc_html__('Countdown completed.', 'pixels-core-creative-tools-for-elementor'),
            'condition' => [
                'expiry_action' => 'show_message',
            ],
        ]);

		$this->end_controls_section();
	}

	private function register_style_controls(): void {
		$this->start_controls_section(
			'countdown_timer_items_style',
			[
				'label' => esc_html__( 'Countdown Timer Items', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'unit_item_columns',
			[
				'label'                => esc_html__( 'Columns', 'pixels-core-creative-tools-for-elementor' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'auto',
				'tablet_default'       => 'auto',
				'mobile_default'       => '1',
				'options'              => [
					'auto' => esc_html__( 'Auto', 'pixels-core-creative-tools-for-elementor' ),
					'1'    => '1',
					'2'    => '2',
					'3'    => '3',
					'4'    => '4',
				],
				'prefix_class'         => 'pixeccte-countdown-timer--cols-',
				'selectors_dictionary' => [
					'auto' => 'display: flex; flex-wrap: wrap; justify-content: center; align-items: flex-start;',
					'1'    => 'display: grid; grid-template-columns: repeat(1, minmax(0, 1fr)); justify-items: center; align-items: start;',
					'2'    => 'display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); justify-items: center; align-items: start;',
					'3'    => 'display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); justify-items: center; align-items: start;',
					'4'    => 'display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); justify-items: center; align-items: start;',
				],
				'selectors'            => [
					'{{WRAPPER}} .pixeccte-timer' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'unit_item_gap',
			[
				'label'      => esc_html__( 'Gap', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 80,
					],
					'em' => [
						'min' => 0,
						'max' => 5,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 16,
				],
				'selectors'  => [
					'{{WRAPPER}} .pixeccte-timer' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'unit_item_width',
			[
				'label'      => esc_html__( 'Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 80,
						'max' => 300,
					],
					'%'  => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .pixeccte-timer-unit' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'unit_item_background',
				'selector' => '{{WRAPPER}} .pixeccte-timer-unit',
			]
		);

		$this->add_responsive_control(
			'unit_item_padding',
			[
				'label'      => esc_html__( 'Padding', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pixeccte-timer-unit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'unit_item_border',
				'selector' => '{{WRAPPER}} .pixeccte-timer-unit',
			]
		);

		$this->add_responsive_control(
			'unit_item_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pixeccte-timer-unit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'unit_item_shadow',
				'selector' => '{{WRAPPER}} .pixeccte-timer-unit',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_countdown_timer',
			[
				'label' => esc_html__( 'Countdown Timer Rings', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_circle_progress' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'ring_size',
			[
				'label'      => esc_html__( 'Circle Size', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 80,
						'max' => 220,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 120,
				],
				'selectors'  => [
					'{{WRAPPER}} .pixeccte-timer-ring' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ring_track_color',
			[
				'label'     => esc_html__( 'Circle Track Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e5e5e5',
				'selectors' => [
					'{{WRAPPER}} .pixeccte-timer-ring__track' => 'stroke: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ring_progress_color',
			[
				'label'     => esc_html__( 'Circle Progress Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b3b25',
				'selectors' => [
					'{{WRAPPER}} .pixeccte-timer-ring__progress' => 'stroke: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ring_stroke_width',
			[
				'label'     => esc_html__( 'Circle Stroke Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 2,
						'max' => 12,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => 6,
				],
				'selectors' => [
					'{{WRAPPER}} .pixeccte-timer-ring__track, {{WRAPPER}} .pixeccte-timer-ring__progress' => 'stroke-width: {{SIZE}};',
				],
			]
		);

		$this->add_control(
			'digit_color',
			[
				'label'     => esc_html__( 'Digit Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#222222',
				'selectors' => [
					'{{WRAPPER}} .pixeccte-timer-unit-digits' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'digit_typography',
				'selector'  => '{{WRAPPER}} .pixeccte-timer-unit-digits',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'digit_box_style',
			[
				'label'     => esc_html__( 'Digit Boxes', 'pixels-core-creative-tools-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_circle_progress!' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'digit_box_background',
				'selector' => '{{WRAPPER}} .pixeccte-timer-digit',
			]
		);

		$this->add_control(
			'digit_box_color',
			[
				'label'     => esc_html__( 'Text Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => [
					'{{WRAPPER}} .pixeccte-timer-digit' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'digit_box_typography',
				'selector' => '{{WRAPPER}} .pixeccte-timer-digit',
			]
		);

		$this->add_control(
			'digit_box_custom_size',
			[
				'label'        => esc_html__( 'Custom Width & Height', 'pixels-core-creative-tools-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'pixels-core-creative-tools-for-elementor' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_responsive_control(
			'digit_box_width',
			[
				'label'      => esc_html__( 'Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 24,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 54,
				],
				'selectors'  => [
					'{{WRAPPER}} .pixeccte-timer-digit' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
				'condition'  => [
					'digit_box_custom_size' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'digit_box_height',
			[
				'label'      => esc_html__( 'Height', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 24,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors'  => [
					'{{WRAPPER}} .pixeccte-timer-digit' => 'height: {{SIZE}}{{UNIT}} !important;',
				],
				'condition'  => [
					'digit_box_custom_size' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'digit_box_gap',
			[
				'label'      => esc_html__( 'Gap Between Digits', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 30,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors'  => [
					'{{WRAPPER}} .pixeccte-timer-unit-digits' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'digit_box_padding',
			[
				'label'      => esc_html__( 'Padding', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pixeccte-timer-digit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'digit_box_border',
				'selector' => '{{WRAPPER}} .pixeccte-timer-digit',
			]
		);

		$this->add_responsive_control(
			'digit_box_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pixeccte-timer-digit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'digit_box_shadow',
				'selector' => '{{WRAPPER}} .pixeccte-timer-digit',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_labels',
			[
				'label' => esc_html__( 'Labels', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'label_color',
			[
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#222222',
				'selectors' => [
					'{{WRAPPER}} .pixeccte-timer-label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} .pixeccte-timer-label',
			]
		);

		$this->add_control(
			'label_position',
			[
				'label'        => esc_html__( 'Position', 'pixels-core-creative-tools-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'bottom',
				'options'      => [
					'top'    => esc_html__( 'Top', 'pixels-core-creative-tools-for-elementor' ),
					'bottom' => esc_html__( 'Bottom', 'pixels-core-creative-tools-for-elementor' ),
				],
				'prefix_class' => 'pixeccte-countdown-timer--label-',
				'condition'    => [
					'show_circle_progress!' => 'yes',
				],
			]
		);

		$this->add_control(
			'ring_label_position',
			[
				'label'        => esc_html__( 'Inside Ring Position', 'pixels-core-creative-tools-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'bottom',
				'options'      => [
					'top'    => esc_html__( 'Top', 'pixels-core-creative-tools-for-elementor' ),
					'bottom' => esc_html__( 'Bottom', 'pixels-core-creative-tools-for-elementor' ),
				],
				'prefix_class' => 'pixeccte-countdown-timer--ring-label-',
				'condition'    => [
					'show_circle_progress' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'label_margin_top',
			[
				'label'      => esc_html__( 'Margin Top', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
					'em' => [
						'min' => 0,
						'max' => 3,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .pixeccte-timer-label' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'label_margin_bottom',
			[
				'label'      => esc_html__( 'Margin Bottom', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
					'em' => [
						'min' => 0,
						'max' => 3,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors'  => [
					'{{WRAPPER}} .pixeccte-timer-label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_separator',
			[
				'label'     => esc_html__( 'Separator', 'pixels-core-creative-tools-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_separator' => 'yes',
				],
			]
		);

		$this->add_control(
			'separator_style',
			[
				'label'   => esc_html__( 'Style', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'colon',
				'options' => [
					'colon' => esc_html__( 'Colon', 'pixels-core-creative-tools-for-elementor' ),
					'line'  => esc_html__( 'Line', 'pixels-core-creative-tools-for-elementor' ),
				],
			]
		);

		$this->add_control(
			'separator_color',
			[
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#111111',
				'selectors' => [
					'{{WRAPPER}} .pixeccte-timer-separator--colon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pixeccte-timer-separator--line' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'separator_spacing',
			[
				'label'      => esc_html__( 'Spacing', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 40,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 8,
				],
				'selectors'  => [
					'{{WRAPPER}} .pixeccte-timer-separator' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'separator_colon_heading',
			[
				'label'     => esc_html__( 'Colon', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'separator_style' => 'colon',
				],
			]
		);

		$this->add_responsive_control(
			'separator_colon_size',
			[
				'label'      => esc_html__( 'Size', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 16,
						'max' => 72,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 40,
				],
				'selectors'  => [
					'{{WRAPPER}} .pixeccte-timer-separator--colon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'separator_style' => 'colon',
				],
			]
		);

		$this->add_control(
			'separator_line_heading',
			[
				'label'     => esc_html__( 'Line', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'separator_style' => 'line',
				],
			]
		);

		$this->add_responsive_control(
			'separator_line_width',
			[
				'label'      => esc_html__( 'Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 8,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .pixeccte-timer-separator--line' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'separator_style' => 'line',
				],
			]
		);

		$this->add_responsive_control(
			'separator_line_height',
			[
				'label'      => esc_html__( 'Height', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 20,
						'max' => 200,
					],
					'%'  => [
						'min' => 10,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors'  => [
					'{{WRAPPER}} .pixeccte-timer-separator--line' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'separator_style' => 'line',
				],
			]
		);

		$this->add_responsive_control(
			'separator_top_offset',
			[
				'label'      => esc_html__( 'Top Offset', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .pixeccte-timer-separator--colon' => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixeccte-timer-separator--line' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		foreach ( $this->get_timer_unit_definitions() as $unit => $label ) {
			$this->register_individual_unit_item_style_controls( $unit, $label );
			$this->register_individual_unit_digit_style_controls( $unit, $label );
			$this->register_individual_unit_label_style_controls( $unit, $label );
		}
	}

	/**
	 * @return array<string, string>
	 */
	private function get_timer_unit_definitions(): array {
		return [
			'days'    => esc_html__( 'Days', 'pixels-core-creative-tools-for-elementor' ),
			'hours'   => esc_html__( 'Hours', 'pixels-core-creative-tools-for-elementor' ),
			'minutes' => esc_html__( 'Minutes', 'pixels-core-creative-tools-for-elementor' ),
			'seconds' => esc_html__( 'Seconds', 'pixels-core-creative-tools-for-elementor' ),
		];
	}

	private function register_individual_unit_item_style_controls( string $unit, string $label ): void {
		$selector   = '{{WRAPPER}} .pixeccte-timer-unit-' . $unit;
		$visibility = [ 'show_' . $unit => 'yes' ];

		$this->start_controls_section(
			'section_style_unit_item_' . $unit,
			[
				'label'     => sprintf(
					/* translators: %s: countdown unit label such as Days or Hours */
					esc_html__( '%s Item', 'pixels-core-creative-tools-for-elementor' ),
					$label
				),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => $visibility,
			]
		);

		$this->add_responsive_control(
			$unit . '_item_width',
			[
				'label'      => esc_html__( 'Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 80,
						'max' => 300,
					],
					'%'  => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors'  => [
					$selector => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => $unit . '_item_background',
				'selector' => $selector,
			]
		);

		$this->add_responsive_control(
			$unit . '_item_padding',
			[
				'label'      => esc_html__( 'Padding', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					$selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => $unit . '_item_border',
				'selector' => $selector,
			]
		);

		$this->add_responsive_control(
			$unit . '_item_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => $unit . '_item_shadow',
				'selector' => $selector,
			]
		);

		$this->end_controls_section();
	}

	private function register_individual_unit_digit_style_controls( string $unit, string $label ): void {
		$unit_selector   = '{{WRAPPER}} .pixeccte-timer-unit-' . $unit;
		$digit_selector  = $unit_selector . ' .pixeccte-timer-digit';
		$digits_selector = $unit_selector . ' .pixeccte-timer-unit-digits';
		$custom_class    = 'pixeccte-countdown-timer--' . $unit . '-digit-custom-size';
		$custom_digit_selector = '{{WRAPPER}}.' . $custom_class . ' .pixeccte-timer-unit-' . $unit . ' .pixeccte-timer-digit';
		$visibility      = [ 'show_' . $unit => 'yes' ];

		$this->start_controls_section(
			'section_style_unit_digit_' . $unit,
			[
				'label'     => sprintf(
					/* translators: %s: countdown unit label such as Days or Hours */
					esc_html__( '%s Digits', 'pixels-core-creative-tools-for-elementor' ),
					$label
				),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => $visibility,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => $unit . '_digit_background',
				'selector'  => $digit_selector,
				'condition' => [
					'show_circle_progress!' => 'yes',
				],
			]
		);

		$this->add_control(
			$unit . '_digit_color',
			[
				'label'     => esc_html__( 'Text Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$digit_selector  => 'color: {{VALUE}};',
					$digits_selector => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => $unit . '_digit_typography',
				'selector' => $digit_selector . ', ' . $digits_selector,
			]
		);

		$this->add_control(
			$unit . '_ring_track_color',
			[
				'label'     => esc_html__( 'Circle Track Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$unit_selector . ' .pixeccte-timer-ring__track' => 'stroke: {{VALUE}};',
				],
				'condition' => [
					'show_circle_progress' => 'yes',
				],
			]
		);

		$this->add_control(
			$unit . '_ring_progress_color',
			[
				'label'     => esc_html__( 'Circle Progress Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$unit_selector . ' .pixeccte-timer-ring__progress' => 'stroke: {{VALUE}};',
				],
				'condition' => [
					'show_circle_progress' => 'yes',
				],
			]
		);

		$this->add_control(
			$unit . '_digit_custom_size',
			[
				'label'        => esc_html__( 'Custom Width & Height', 'pixels-core-creative-tools-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'pixels-core-creative-tools-for-elementor' ),
				'return_value' => 'yes',
				'default'      => '',
				'prefix_class' => $custom_class,
				'condition'    => [
					'show_circle_progress!' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			$unit . '_digit_width',
			[
				'label'      => esc_html__( 'Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 24,
						'max' => 100,
					],
				],
				'selectors'  => [
					$custom_digit_selector => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'show_circle_progress!' => 'yes',
					$unit . '_digit_custom_size' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			$unit . '_digit_height',
			[
				'label'      => esc_html__( 'Height', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 24,
						'max' => 100,
					],
				],
				'selectors'  => [
					$custom_digit_selector => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'show_circle_progress!' => 'yes',
					$unit . '_digit_custom_size' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			$unit . '_digit_gap',
			[
				'label'      => esc_html__( 'Gap Between Digits', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 30,
					],
				],
				'selectors'  => [
					$digits_selector => 'gap: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'show_circle_progress!' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			$unit . '_digit_padding',
			[
				'label'      => esc_html__( 'Padding', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					$digit_selector => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'show_circle_progress!' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => $unit . '_digit_border',
				'selector'  => $digit_selector,
				'condition' => [
					'show_circle_progress!' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			$unit . '_digit_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					$digit_selector => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
				'condition'  => [
					'show_circle_progress!' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => $unit . '_digit_shadow',
				'selector'  => $digit_selector,
				'condition' => [
					'show_circle_progress!' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_individual_unit_label_style_controls( string $unit, string $label ): void {
		$selector   = '{{WRAPPER}} .pixeccte-timer-unit-' . $unit . ' .pixeccte-timer-label';
		$visibility = [ 'show_' . $unit => 'yes' ];

		$this->start_controls_section(
			'section_style_unit_label_' . $unit,
			[
				'label'     => sprintf(
					/* translators: %s: countdown unit label such as Days or Hours */
					esc_html__( '%s Label', 'pixels-core-creative-tools-for-elementor' ),
					$label
				),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => $visibility,
			]
		);

		$this->add_control(
			$unit . '_label_color',
			[
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$selector => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => $unit . '_label_typography',
				'selector' => $selector,
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => $unit . '_label_text_shadow',
				'selector' => $selector,
			]
		);

		$this->add_responsive_control(
			$unit . '_label_margin_top',
			[
				'label'      => esc_html__( 'Margin Top', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
					'em' => [
						'min' => 0,
						'max' => 3,
					],
				],
				'selectors'  => [
					$selector => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			$unit . '_label_margin_bottom',
			[
				'label'      => esc_html__( 'Margin Bottom', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
					'em' => [
						'min' => 0,
						'max' => 3,
					],
				],
				'selectors'  => [
					$selector => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private const RING_RADIUS = 45;

	private function get_ring_circumference(): float {
		return 2 * M_PI * self::RING_RADIUS;
	}

	/**
	 * @return array<string, int>
	 */
	private function get_unit_ring_max_map(): array {
		return [
			'days'    => 99,
			'hours'   => 24,
			'minutes' => 60,
			'seconds' => 60,
		];
	}

	private function get_unit_ring_max( string $unit ): int {
		$map = $this->get_unit_ring_max_map();

		return $map[ $unit ] ?? 60;
	}

	private function render_unit_digits(): void {
		?>
		<div class="pixeccte-timer-unit-digits">
			<span class="pixeccte-timer-digit pixeccte-timer-digit-tens">
				<number-flow class="pixeccte-timer-digit-flow" aria-hidden="true">0</number-flow>
			</span>
			<span class="pixeccte-timer-digit pixeccte-timer-digit-ones">
				<number-flow class="pixeccte-timer-digit-flow" aria-hidden="true">0</number-flow>
			</span>
		</div>
		<?php
	}

	private function render_unit_ring( string $label_text ): void {
		$circumference = $this->get_ring_circumference();
		?>
		<div class="pixeccte-timer-ring">
			<svg class="pixeccte-timer-ring__svg" viewBox="0 0 100 100" aria-hidden="true">
				<circle class="pixeccte-timer-ring__track" cx="50" cy="50" r="<?php echo esc_attr( (string) self::RING_RADIUS ); ?>" />
				<circle
					class="pixeccte-timer-ring__progress"
					cx="50"
					cy="50"
					r="<?php echo esc_attr( (string) self::RING_RADIUS ); ?>"
					stroke-dasharray="<?php echo esc_attr( (string) $circumference ); ?>"
					stroke-dashoffset="0"
				/>
			</svg>
			<div class="pixeccte-timer-ring__center">
				<?php $this->render_unit_digits(); ?>
				<span class="pixeccte-timer-label pixeccte-timer-ring__label"><?php echo esc_html( $label_text ); ?></span>
			</div>
		</div>
		<?php
	}

	/**
	 * @param array<string, mixed> $settings Widget settings.
	 * @return array<string, mixed>|null
	 */
	private function prepare_timer_data( array $settings ): ?array {
		$due_date = $settings['due_date'] ?? '';

		if ( empty( $due_date ) ) {
			return null;
		}

		$datetime = \DateTimeImmutable::createFromFormat( 'Y-m-d H:i', $due_date, wp_timezone() );

		if ( false === $datetime ) {
			$datetime = \DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $due_date, wp_timezone() );
		}

		if ( false === $datetime ) {
			return null;
		}

		$active_units = [];

		foreach (
			[
				'days'    => 'show_days',
				'hours'   => 'show_hours',
				'minutes' => 'show_minutes',
				'seconds' => 'show_seconds',
			] as $unit => $control
		) {
			if ( 'yes' === ( $settings[ $control ] ?? '' ) ) {
				$active_units[] = $unit;
			}
		}

		if ( empty( $active_units ) ) {
			$active_units = [ 'days', 'hours', 'minutes', 'seconds' ];
		}

		$labels = [];

		foreach ( [ 'days', 'hours', 'minutes', 'seconds' ] as $unit ) {
			$label_key = $unit . '_label';

			if ( ! empty( $settings[ $label_key ] ) ) {
				$labels[ $unit ] = $settings[ $label_key ];
			}
		}

		return [
			'target_ms'            => $datetime->getTimestamp() * 1000,
			'active_units'         => $active_units,
			'expiry_action'        => $settings['expiry_action'] ?? 'show_message',
			'expiry_message'       => $settings['expiry_message'] ?? esc_html__( 'Countdown completed.', 'pixels-core-creative-tools-for-elementor' ),
			'show_separator'       => $settings['show_separator'] ?? 'yes',
			'separator_style'      => $settings['separator_style'] ?? 'colon',
			'show_circle_progress' => $settings['show_circle_progress'] ?? 'yes',
			'labels'               => $labels,
		];
	}

	protected function render(): void {
		$settings   = $this->get_settings_for_display();
		$timer_data = $this->prepare_timer_data( $settings );

		if ( null === $timer_data ) {
			return;
		}

		$this->add_render_attribute( 'wrapper', 'class', 'pixeccte-countdown-timer' );

		$target_ms            = (int) $timer_data['target_ms'];
		$active_units         = $timer_data['active_units'];
		$expiry_action        = $timer_data['expiry_action'];
		$expiry_message       = $timer_data['expiry_message'];
		$show_separator       = 'yes' === $timer_data['show_separator'];
		$separator_style      = $timer_data['separator_style'];
		$show_circle_progress = 'yes' === $timer_data['show_circle_progress'];
		$custom_labels        = $timer_data['labels'];

		if ( $show_circle_progress ) {
			$this->add_render_attribute( 'wrapper', 'class', 'pixeccte-countdown-timer--has-rings' );

			$ring_label_position = $settings['ring_label_position'] ?? 'bottom';
			if ( in_array( $ring_label_position, [ 'top', 'bottom' ], true ) ) {
				$this->add_render_attribute( 'wrapper', 'class', 'pixeccte-countdown-timer--ring-label-' . $ring_label_position );
			}
		} else {
			$label_position = $settings['label_position'] ?? 'bottom';
			if ( in_array( $label_position, [ 'top', 'bottom' ], true ) ) {
				$this->add_render_attribute( 'wrapper', 'class', 'pixeccte-countdown-timer--label-' . $label_position );
			}
		}

		if ( $show_separator && in_array( $separator_style, [ 'colon', 'line' ], true ) ) {
			$this->add_render_attribute( 'wrapper', 'class', 'pixeccte-countdown-timer--separator-' . $separator_style );
		}

		$label_map = [
			'days'    => ! empty( $custom_labels['days'] ) ? $custom_labels['days'] : esc_html__( 'Days', 'pixels-core-creative-tools-for-elementor' ),
			'hours'   => ! empty( $custom_labels['hours'] ) ? $custom_labels['hours'] : esc_html__( 'Hours', 'pixels-core-creative-tools-for-elementor' ),
			'minutes' => ! empty( $custom_labels['minutes'] ) ? $custom_labels['minutes'] : esc_html__( 'Minutes', 'pixels-core-creative-tools-for-elementor' ),
			'seconds' => ! empty( $custom_labels['seconds'] ) ? $custom_labels['seconds'] : esc_html__( 'Seconds', 'pixels-core-creative-tools-for-elementor' ),
		];

		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="pixeccte-timer" data-end-time="<?php echo esc_attr( (string) $target_ms ); ?>" data-expiry-action="<?php echo esc_attr( $expiry_action ); ?>" data-expiry-message="<?php echo esc_attr( $expiry_message ); ?>">
				<?php foreach ( $active_units as $index => $unit ) : ?>
					<?php
					if ( ! isset( $label_map[ $unit ] ) ) {
						continue;
					}
					?>
					<div class="pixeccte-timer-unit pixeccte-timer-unit-<?php echo esc_attr( $unit ); ?>" data-unit="<?php echo esc_attr( $unit ); ?>"<?php echo $show_circle_progress ? ' data-ring-max="' . esc_attr( (string) $this->get_unit_ring_max( $unit ) ) . '"' : ''; ?>>
						<?php if ( $show_circle_progress ) : ?>
							<?php $this->render_unit_ring( $label_map[ $unit ] ); ?>
						<?php else : ?>
							<?php $this->render_unit_digits(); ?>
							<span class="pixeccte-timer-label"><?php echo esc_html( $label_map[ $unit ] ); ?></span>
						<?php endif; ?>
					</div>
					<?php if ( $show_separator && $index < ( count( $active_units ) - 1 ) ) : ?>
						<span class="pixeccte-timer-separator pixeccte-timer-separator--<?php echo esc_attr( $separator_style ); ?>" aria-hidden="true">
							<?php if ( 'colon' === $separator_style ) : ?>
								:
							<?php endif; ?>
						</span>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
			<div class="pixeccte-timer-expired-message is-hidden">
				<?php echo esc_html( $expiry_message ); ?>
			</div>
		</div>
		<?php
	}
}
