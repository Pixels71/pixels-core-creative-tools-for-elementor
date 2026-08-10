<?php
namespace PixelsCore\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Button_Widget extends Widget_Base {

	use Widget_Assets_Trait;

	public function get_name(): string {
		return 'pixels-core-button';
	}

	public function get_title(): string {
		return esc_html__( 'Button', 'pixels-core-creative-tools-for-elementor' );
	}

	public function get_icon(): string {
		return 'pixels-icon pixels-icon-button';
	}

	public function get_categories(): array {
		return [ 'pixels-core-creative-tools-for-elementor' ];
	}

	public function get_keywords(): array {
		return [ 'button', 'cta', 'link', 'pixels' ];
	}

	protected function get_assets_slug(): string {
		return 'button';
	}

	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	private function register_content_controls(): void {
		$this->start_controls_section(
			'button_section',
			[
				'label' => esc_html__( 'Button', 'pixels-core-creative-tools-for-elementor' ),
			]
		);

		$this->add_control(
			'variation',
			[
				'label'   => esc_html__( 'Variation', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'variation_1',
				'options' => [
					'variation_1'  => esc_html__( 'Variation 1', 'pixels-core-creative-tools-for-elementor' ),
					'variation_2'  => esc_html__( 'Variation 2', 'pixels-core-creative-tools-for-elementor' ),
					'variation_3'  => esc_html__( 'Variation 3', 'pixels-core-creative-tools-for-elementor' ),
					'variation_4'  => esc_html__( 'Variation 4', 'pixels-core-creative-tools-for-elementor' ),
					'variation_5'  => esc_html__( 'Variation 5', 'pixels-core-creative-tools-for-elementor' ),
					'variation_6'  => esc_html__( 'Variation 6', 'pixels-core-creative-tools-for-elementor' ),
					'variation_7'  => esc_html__( 'Variation 7', 'pixels-core-creative-tools-for-elementor' ),
					'variation_8'  => esc_html__( 'Variation 8', 'pixels-core-creative-tools-for-elementor' ),
					'variation_9'  => esc_html__( 'Variation 9', 'pixels-core-creative-tools-for-elementor' ),
					'variation_10' => esc_html__( 'Variation 10', 'pixels-core-creative-tools-for-elementor' ),
				],
			]
		);

		$this->add_control(
			'btn_title',
			[
				'label'       => esc_html__( 'Button Text', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Get Started', 'pixels-core-creative-tools-for-elementor' ),
				'placeholder' => esc_html__( 'Type your title here', 'pixels-core-creative-tools-for-elementor' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'btn_url',
			[
				'label'       => esc_html__( 'Link', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'options'     => [ 'url', 'is_external', 'nofollow' ],
				'default'     => [
					'url'         => '',
					'is_external' => true,
					'nofollow'    => false,
				],
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'btn_size',
			[
				'label'     => esc_html__( 'Size', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'btn-xl',
				'options'   => [
					'btn-md' => esc_html__( 'Small', 'pixels-core-creative-tools-for-elementor' ),
					'btn-lg' => esc_html__( 'Medium', 'pixels-core-creative-tools-for-elementor' ),
					'btn-xl' => esc_html__( 'Large (Default)', 'pixels-core-creative-tools-for-elementor' ),
				],
				'condition' => [
					'variation' => 'variation_1',
				],
			]
		);

		$this->add_control(
			'v1_show_icon',
			[
				'label'     => esc_html__( 'Show Icon', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'variation' => 'variation_1',
				],
			]
		);

		$this->add_control(
			'v1_icon_source',
			[
				'label'     => esc_html__( 'Icon Source', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'default',
				'options'   => [
					'default' => esc_html__( 'Default Icon', 'pixels-core-creative-tools-for-elementor' ),
					'upload'  => esc_html__( 'Upload Icon', 'pixels-core-creative-tools-for-elementor' ),
					'library' => esc_html__( 'Icon Library', 'pixels-core-creative-tools-for-elementor' ),
				],
				'condition' => [
					'variation'    => 'variation_1',
					'v1_show_icon' => 'yes',
				],
			]
		);

		$this->add_control(
			'v1_icon_upload',
			[
				'label'     => esc_html__( 'Upload Icon', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					'variation'      => 'variation_1',
					'v1_show_icon'   => 'yes',
					'v1_icon_source' => 'upload',
				],
			]
		);

		$this->add_control(
			'v1_selected_icon',
			[
				'label'     => esc_html__( 'Icon Library', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => [
					'variation'      => 'variation_1',
					'v1_show_icon'   => 'yes',
					'v1_icon_source' => 'library',
				],
			]
		);

		$this->add_control(
			'btn_show_icon',
			[
				'label'     => esc_html__( 'Show Icon', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'variation' => [ 'variation_6', 'variation_2' ],
				],
			]
		);

		$this->add_control(
			'v6_icon_source',
			[
				'label'     => esc_html__( 'Icon Source', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'default',
				'options'   => [
					'default' => esc_html__( 'Default Icon', 'pixels-core-creative-tools-for-elementor' ),
					'upload'  => esc_html__( 'Upload Icon', 'pixels-core-creative-tools-for-elementor' ),
					'library' => esc_html__( 'Icon Library', 'pixels-core-creative-tools-for-elementor' ),
				],
				'condition' => [
					'variation'     => 'variation_6',
					'btn_show_icon' => 'yes',
				],
			]
		);

		$this->add_control(
			'v6_icon_upload',
			[
				'label'     => esc_html__( 'Upload Icon', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					'variation'      => 'variation_6',
					'btn_show_icon'  => 'yes',
					'v6_icon_source' => 'upload',
				],
			]
		);

		$this->add_control(
			'v6_selected_icon',
			[
				'label'     => esc_html__( 'Icon Library', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => [
					'variation'      => 'variation_6',
					'btn_show_icon'  => 'yes',
					'v6_icon_source' => 'library',
				],
			]
		);

		$this->add_control(
			'v2_icon_source',
			[
				'label'     => esc_html__( 'Icon Source', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'default',
				'options'   => [
					'default' => esc_html__( 'Default Icon', 'pixels-core-creative-tools-for-elementor' ),
					'upload'  => esc_html__( 'Upload Icon', 'pixels-core-creative-tools-for-elementor' ),
					'library' => esc_html__( 'Icon Library', 'pixels-core-creative-tools-for-elementor' ),
				],
				'condition' => [
					'variation'     => 'variation_2',
					'btn_show_icon' => 'yes',
				],
			]
		);

		$this->add_control(
			'v2_icon_upload',
			[
				'label'     => esc_html__( 'Upload Icon', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					'variation'      => 'variation_2',
					'btn_show_icon'  => 'yes',
					'v2_icon_source' => 'upload',
				],
			]
		);

		$this->add_control(
			'v2_selected_icon',
			[
				'label'     => esc_html__( 'Icon Library', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => [
					'variation'      => 'variation_2',
					'btn_show_icon'  => 'yes',
					'v2_icon_source' => 'library',
				],
			]
		);

		$this->add_control(
			'v3_icon_source',
			[
				'label'     => esc_html__( 'Icon Source', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'default',
				'options'   => [
					'default' => esc_html__( 'Default Icon', 'pixels-core-creative-tools-for-elementor' ),
					'upload'  => esc_html__( 'Upload Icon', 'pixels-core-creative-tools-for-elementor' ),
					'library' => esc_html__( 'Icon Library', 'pixels-core-creative-tools-for-elementor' ),
				],
				'condition' => [
					'variation' => 'variation_3',
				],
			]
		);

		$this->add_control(
			'v3_icon_upload',
			[
				'label'     => esc_html__( 'Upload Icon', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					'variation'      => 'variation_3',
					'v3_icon_source' => 'upload',
				],
			]
		);

		$this->add_control(
			'v3_selected_icon',
			[
				'label'     => esc_html__( 'Icon Library', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => [
					'variation'      => 'variation_3',
					'v3_icon_source' => 'library',
				],
			]
		);

		$this->add_control(
			'v5_icon_source',
			[
				'label'     => esc_html__( 'Icon Source', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'default',
				'options'   => [
					'default' => esc_html__( 'Default Icon', 'pixels-core-creative-tools-for-elementor' ),
					'upload'  => esc_html__( 'Upload Icon', 'pixels-core-creative-tools-for-elementor' ),
					'library' => esc_html__( 'Icon Library', 'pixels-core-creative-tools-for-elementor' ),
				],
				'condition' => [
					'variation' => 'variation_5',
				],
			]
		);

		$this->add_control(
			'v5_icon_upload',
			[
				'label'     => esc_html__( 'Upload Icon', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					'variation'      => 'variation_5',
					'v5_icon_source' => 'upload',
				],
			]
		);

		$this->add_control(
			'v5_selected_icon',
			[
				'label'     => esc_html__( 'Icon Library', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => [
					'variation'      => 'variation_5',
					'v5_icon_source' => 'library',
				],
			]
		);

		$this->add_control(
			'v10_icon_source',
			[
				'label'     => esc_html__( 'Icon Source', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'default',
				'options'   => [
					'default' => esc_html__( 'Default Icon', 'pixels-core-creative-tools-for-elementor' ),
					'upload'  => esc_html__( 'Upload Icon', 'pixels-core-creative-tools-for-elementor' ),
					'library' => esc_html__( 'Icon Library', 'pixels-core-creative-tools-for-elementor' ),
				],
				'condition' => [
					'variation' => 'variation_10',
				],
			]
		);

		$this->add_control(
			'v10_icon_upload',
			[
				'label'     => esc_html__( 'Upload Icon', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					'variation'       => 'variation_10',
					'v10_icon_source' => 'upload',
				],
			]
		);

		$this->add_control(
			'v10_selected_icon',
			[
				'label'     => esc_html__( 'Icon Library', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => [
					'variation'       => 'variation_10',
					'v10_icon_source' => 'library',
				],
			]
		);

		$this->add_control(
			'v4_icon_source',
			[
				'label'     => esc_html__( 'Icon Source', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'default',
				'options'   => [
					'default' => esc_html__( 'Default Icon', 'pixels-core-creative-tools-for-elementor' ),
					'upload'  => esc_html__( 'Upload Icon', 'pixels-core-creative-tools-for-elementor' ),
					'library' => esc_html__( 'Icon Library', 'pixels-core-creative-tools-for-elementor' ),
				],
				'condition' => [
					'variation' => [ 'variation_4', 'variation_9' ],
				],
			]
		);

		$this->add_control(
			'v4_icon_upload',
			[
				'label'     => esc_html__( 'Upload Icon', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					'variation'      => [ 'variation_4', 'variation_9' ],
					'v4_icon_source' => 'upload',
				],
			]
		);

		$this->add_control(
			'v4_selected_icon',
			[
				'label'            => esc_html__( 'Icon Library', 'pixels-core-creative-tools-for-elementor' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'condition'        => [
					'variation'      => [ 'variation_4', 'variation_9' ],
					'v4_icon_source' => 'library',
				],
			]
		);

		$this->add_control(
			'v7_icon_source',
			[
				'label'     => esc_html__( 'Icon Source', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'default',
				'options'   => [
					'default' => esc_html__( 'Default Icon', 'pixels-core-creative-tools-for-elementor' ),
					'upload'  => esc_html__( 'Upload Icon', 'pixels-core-creative-tools-for-elementor' ),
					'library' => esc_html__( 'Icon Library', 'pixels-core-creative-tools-for-elementor' ),
				],
				'condition' => [
					'variation' => 'variation_7',
				],
			]
		);

		$this->add_control(
			'v7_icon_upload',
			[
				'label'     => esc_html__( 'Upload Icon', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					'variation'      => 'variation_7',
					'v7_icon_source' => 'upload',
				],
			]
		);

		$this->add_control(
			'v7_selected_icon',
			[
				'label'     => esc_html__( 'Icon Library', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => [
					'variation'      => 'variation_7',
					'v7_icon_source' => 'library',
				],
			]
		);

		$this->add_control(
			'v8_icon_source',
			[
				'label'     => esc_html__( 'Icon Source', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'default',
				'options'   => [
					'default' => esc_html__( 'Default Icon', 'pixels-core-creative-tools-for-elementor' ),
					'upload'  => esc_html__( 'Upload Icon', 'pixels-core-creative-tools-for-elementor' ),
					'library' => esc_html__( 'Icon Library', 'pixels-core-creative-tools-for-elementor' ),
				],
				'condition' => [
					'variation' => 'variation_8',
				],
			]
		);

		$this->add_control(
			'v8_icon_upload',
			[
				'label'     => esc_html__( 'Upload Icon', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					'variation'      => 'variation_8',
					'v8_icon_source' => 'upload',
				],
			]
		);

		$this->add_control(
			'v8_selected_icon',
			[
				'label'     => esc_html__( 'Icon Library', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'condition' => [
					'variation'      => 'variation_8',
					'v8_icon_source' => 'library',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_style_controls(): void {
		$this->start_controls_section(
			'btn_outerstyle',
			[
				'label'     => esc_html__( 'Outer Style', 'pixels-core-creative-tools-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'variation' => 'variation_9',
				],
			]
		);

		$this->add_responsive_control(
			'btn_outer_padding',
			[
				'label'      => esc_html__( 'Padding', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-button__v9-outer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'btn_outer_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-button__v9-outer' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'btn_outer_background',
			[
				'label'     => esc_html__( 'Background', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-button__v9-outer' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'btn_outer_border',
				'selector' => '{{WRAPPER}} .pixels-core-button__v9-outer',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'btn_outer_shadow',
				'selector' => '{{WRAPPER}} .pixels-core-button__v9-outer',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'btn_style',
			[
				'label' => esc_html__( 'Style', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'enable_full_width',
			[
				'label'        => esc_html__( 'Enable Full Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'pixels-core-creative-tools-for-elementor' ),
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => [
					'variation' => 'variation_2',
				],
			]
		);

		$this->add_responsive_control(
			'btn_alignment',
			[
				'label'     => esc_html__( 'Alignment', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pixels-core-button' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'btn_typography',
				'selector' => '{{WRAPPER}} .pixels-core-button__link, {{WRAPPER}} .pixels-core-button__link--v5, {{WRAPPER}} .pixels-core-button__v6-text, {{WRAPPER}} .pixels-core-button__v7-text, {{WRAPPER}} .pixels-core-button__v8-text, {{WRAPPER}} .pixels-core-button__v9-text, {{WRAPPER}} .pixels-core-button__v10-text, {{WRAPPER}} .pixels-core-button__v4-text',
			]
		);

		$this->add_responsive_control(
			'btn_padding',
			[
				'label'      => esc_html__( 'Padding', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-button__link:not(.pixels-core-button__link--v10)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v10-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'btn_margin',
			[
				'label'      => esc_html__( 'Margin', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-button__link' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'btn_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-button__link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v10-body' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'circle_icon_style_heading',
			[
				'label'     => esc_html__( 'Icon Style', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'variation' => 'variation_7',
				],
			]
		);

		$this->add_responsive_control(
			'btn_circle_size',
			[
				'label'      => esc_html__( 'Icon Box Size', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-button__v7-circle' => '--pc-v7-circle-size: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'variation' => 'variation_7',
				],
			]
		);

		$this->add_responsive_control(
			'btn_circle_border_radius',
			[
				'label'      => esc_html__( 'Icon Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-button__v7-circle-bg' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'variation' => 'variation_7',
				],
			]
		);

		$this->add_responsive_control(
			'btn_circle_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-button__v7-circle-icon' => '--pc-v7-icon-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v7-circle-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v7-circle-icon img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v7-circle-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'variation' => 'variation_7',
				],
			]
		);

		$this->add_control(
			'btn_circle_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-button__v7-circle-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v7-circle-icon svg' => 'stroke: {{VALUE}}; color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v7-circle-icon svg path' => 'stroke: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v7-circle-icon i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'variation' => 'variation_7',
				],
			]
		);

		$this->add_control(
			'btn_circle_background',
			[
				'label'     => esc_html__( 'Icon Box Background', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-button__v7-circle-bg' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'variation' => 'variation_7',
				],
			]
		);

		$this->start_controls_tabs( 'btn_style_tabs' );

		$this->start_controls_tab(
			'btn_style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'pixels-core-creative-tools-for-elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'btn_normal_background',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .pixels-core-button__link:not(.pixels-core-button__link--v10), {{WRAPPER}} .pixels-core-button__v7-body, {{WRAPPER}} .pixels-core-button__v10-body',
			]
		);

		$this->add_control(
			'v10_gradient_heading',
			[
				'label'     => esc_html__( 'Border Gradient', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'variation' => 'variation_10',
				],
			]
		);

		$this->add_control(
			'v10_gradient_from',
			[
				'label'     => esc_html__( 'Gradient From', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#2BC0E4',
				'selectors' => [
					'{{WRAPPER}} .pixels-core-button__link--v10' => '--pc-v10-gradient-from: {{VALUE}};',
				],
				'condition' => [
					'variation' => 'variation_10',
				],
			]
		);

		$this->add_control(
			'v10_gradient_to',
			[
				'label'     => esc_html__( 'Gradient To', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#EAECC6',
				'selectors' => [
					'{{WRAPPER}} .pixels-core-button__link--v10' => '--pc-v10-gradient-to: {{VALUE}};',
				],
				'condition' => [
					'variation' => 'variation_10',
				],
			]
		);

		$this->add_control(
			'v10_glow_heading',
			[
				'label'     => esc_html__( 'Glow', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'variation' => 'variation_10',
				],
			]
		);

		$this->add_responsive_control(
			'v10_glow_width',
			[
				'label'      => esc_html__( 'Glow Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min'  => 10,
						'max'  => 120,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 39,
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-button__link--v10' => '--pc-v10-glow-width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'variation' => 'variation_10',
				],
			]
		);

		$this->add_responsive_control(
			'v10_glow_height',
			[
				'label'      => esc_html__( 'Glow Height', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min'  => 10,
						'max'  => 120,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 39,
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-button__link--v10' => '--pc-v10-glow-height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'variation' => 'variation_10',
				],
			]
		);

		$this->add_control(
			'btn_normal_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-button__v6-arrow' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v6-arrow svg' => 'fill: {{VALUE}}; color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v6-arrow svg path' => 'fill: {{VALUE}}; stroke: {{VALUE}}',
				],
				'condition' => [
					'btn_show_icon' => 'yes',
					'variation'     => 'variation_6',
				],
			]
		);

		$this->add_control(
			'btn_normal_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min'  => 10,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-button__v6-arrow' => '--pc-v6-icon-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v6-arrow svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v6-arrow img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v6-arrow i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'btn_show_icon' => 'yes',
					'variation'     => 'variation_6',
				],
			]
		);

		$this->add_control(
			'btn_normal_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-button__link' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v2-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v2-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v2-icon svg path' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v3-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v3-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v3-icon svg path' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link--v5 .pixels-core-button__v5-text' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v6-text' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v7-text' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v8-text' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v9-text' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v10-text' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'btn_normal_border',
				'selector' => '{{WRAPPER}} .pixels-core-button__link',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'btn_normal_shadow',
				'selector' => '{{WRAPPER}} .pixels-core-button__link',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'btn_style_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'pixels-core-creative-tools-for-elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'btn_hover_background',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .pixels-core-button__link:not(.pixels-core-button__link--v10):hover, {{WRAPPER}} .pixels-core-button__link--v5:hover, {{WRAPPER}} .pixels-core-button__link--v7:hover .pixels-core-button__v7-body, {{WRAPPER}} .pixels-core-button__link--v10:hover .pixels-core-button__v10-body',
			]
		);

		$this->add_control(
			'btn_hover_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-button__link:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link:hover .pixels-core-button__v2-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link:hover .pixels-core-button__v2-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link:hover .pixels-core-button__v2-icon svg path' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link:hover .pixels-core-button__v3-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link:hover .pixels-core-button__v3-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link:hover .pixels-core-button__v3-icon svg path' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link--v5:hover .pixels-core-button__v5-text' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link:hover .pixels-core-button__v6-text' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link:hover .pixels-core-button__v7-text' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link:hover .pixels-core-button__v8-text' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link:hover .pixels-core-button__v9-text' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link:hover .pixels-core-button__v10-text' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'btn_hover_border',
				'selector' => '{{WRAPPER}} .pixels-core-button__link:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'btn_hover_shadow',
				'selector' => '{{WRAPPER}} .pixels-core-button__link:hover',
			]
		);

		$this->add_control(
			'btn_hover_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-button__link:hover .pixels-core-button__v6-arrow' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link:hover .pixels-core-button__v6-arrow svg' => 'fill: {{VALUE}}; color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link:hover .pixels-core-button__v6-arrow svg path' => 'fill: {{VALUE}}; stroke: {{VALUE}}',
				],
				'condition' => [
					'btn_show_icon' => 'yes',
					'variation'     => 'variation_6',
				],
			]
		);

		$this->add_control(
			'btn_shine_through_color',
			[
				'label'     => esc_html__( 'Shine Through Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-button__v5-shine' => 'background-image: linear-gradient(180deg, hsla(21,63%,73%,0), {{VALUE}} 50%, hsla(21,63%,73%,0));',
				],
				'condition' => [
					'variation' => 'variation_5',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		$this->register_v1_icon_style_controls();
		$this->register_v2_icon_style_controls();
		$this->register_v3_icon_style_controls();
		$this->register_v4_icon_style_controls();
		$this->register_v5_icon_style_controls();
		$this->register_v8_icon_style_controls();
		$this->register_v10_icon_style_controls();
	}

	private function register_v3_icon_style_controls(): void {
		$this->start_controls_section(
			'btn_v3_icon_style',
			[
				'label'     => esc_html__( 'Icon Style', 'pixels-core-creative-tools-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'variation' => 'variation_3',
				],
			]
		);

		$this->add_responsive_control(
			'v3_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min'  => 8,
						'max'  => 64,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 24,
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-button__v3-figure' => '--pc-v3-icon-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v3-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v3-icon img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v3-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'v3_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-button__v3-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v3-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v3-icon svg path' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'v3_icon_hover_color',
			[
				'label'     => esc_html__( 'Icon Hover Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-button__link--v3:hover .pixels-core-button__v3-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link--v3:hover .pixels-core-button__v3-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link--v3:hover .pixels-core-button__v3-icon svg path' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_v2_icon_style_controls(): void {
		$this->start_controls_section(
			'btn_v2_icon_style',
			[
				'label'     => esc_html__( 'Icon Style', 'pixels-core-creative-tools-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'variation'     => 'variation_2',
					'btn_show_icon' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'v2_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min'  => 8,
						'max'  => 64,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 24,
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-button__v2-icon' => '--pc-v2-icon-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v2-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v2-icon img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v2-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_v1_icon_style_controls(): void {
		$this->start_controls_section(
			'btn_v1_icon_style',
			[
				'label'     => esc_html__( 'Icon Style', 'pixels-core-creative-tools-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'variation'    => 'variation_1',
					'v1_show_icon' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'v1_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min'  => 8,
						'max'  => 48,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-button__link--v1:hover .pixels-core-button__v1-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v1-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v1-icon img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v1-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v1-icon span' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'v1_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-button__v1-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v1-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v1-icon svg path' => 'fill: {{VALUE}}; stroke: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'v1_icon_hover_color',
			[
				'label'     => esc_html__( 'Icon Hover Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-button__link--v1:hover .pixels-core-button__v1-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link--v1:hover .pixels-core-button__v1-icon svg' => 'fill: {{VALUE}}; color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link--v1:hover .pixels-core-button__v1-icon svg path' => 'fill: {{VALUE}}; stroke: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_v5_icon_style_controls(): void {
		$this->start_controls_section(
			'btn_v5_icon_style',
			[
				'label'     => esc_html__( 'Icon Style', 'pixels-core-creative-tools-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'variation' => 'variation_5',
				],
			]
		);

		$this->add_responsive_control(
			'v5_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min'  => 8,
						'max'  => 64,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 18,
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-button__v5-arrow' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v5-arrow svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v5-arrow img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v5-arrow i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'v5_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-button__v5-arrow' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v5-arrow i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v5-arrow svg' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v5-arrow svg[fill="none"] path' => 'stroke: {{VALUE}}; fill: none',
					'{{WRAPPER}} .pixels-core-button__v5-arrow svg:not([fill="none"])' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v5-arrow svg:not([fill="none"]) path' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'v5_icon_hover_color',
			[
				'label'     => esc_html__( 'Icon Hover Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-button__link--v5:hover .pixels-core-button__v5-arrow' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link--v5:hover .pixels-core-button__v5-arrow i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link--v5:hover .pixels-core-button__v5-arrow svg' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link--v5:hover .pixels-core-button__v5-arrow svg[fill="none"] path' => 'stroke: {{VALUE}}; fill: none',
					'{{WRAPPER}} .pixels-core-button__link--v5:hover .pixels-core-button__v5-arrow svg:not([fill="none"])' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link--v5:hover .pixels-core-button__v5-arrow svg:not([fill="none"]) path' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_v10_icon_style_controls(): void {
		$this->start_controls_section(
			'btn_v10_icon_style',
			[
				'label'     => esc_html__( 'Icon Style', 'pixels-core-creative-tools-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'variation' => 'variation_10',
				],
			]
		);

		$this->add_responsive_control(
			'v10_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min'  => 8,
						'max'  => 64,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 18,
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-button__v10-arrow' => '--pc-v10-icon-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v10-arrow svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v10-arrow img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v10-arrow i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'v10_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-button__v10-arrow' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v10-arrow i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v10-arrow svg' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v10-arrow svg[fill="none"] path' => 'stroke: {{VALUE}}; fill: none',
					'{{WRAPPER}} .pixels-core-button__v10-arrow svg:not([fill="none"])' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v10-arrow svg:not([fill="none"]) path' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'v10_icon_hover_color',
			[
				'label'     => esc_html__( 'Icon Hover Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-button__link--v10:hover .pixels-core-button__v10-arrow' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link--v10:hover .pixels-core-button__v10-arrow i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link--v10:hover .pixels-core-button__v10-arrow svg' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link--v10:hover .pixels-core-button__v10-arrow svg[fill="none"] path' => 'stroke: {{VALUE}}; fill: none',
					'{{WRAPPER}} .pixels-core-button__link--v10:hover .pixels-core-button__v10-arrow svg:not([fill="none"])' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link--v10:hover .pixels-core-button__v10-arrow svg:not([fill="none"]) path' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_v4_icon_style_controls(): void {
		$this->start_controls_section(
			'btn_icon_style',
			[
				'label'     => esc_html__( 'Icon Style', 'pixels-core-creative-tools-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'variation' => [ 'variation_4', 'variation_9' ],
				],
			]
		);

		$this->add_responsive_control(
			'v4_gap',
			[
				'label'      => esc_html__( 'Gap Between Icon & Text', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-button__v4-row' => 'gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v9-row' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'v4_icon_box_size',
			[
				'label'      => esc_html__( 'Icon Box Size', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'range'      => [
					'px' => [
						'min'  => 20,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-button__v4-icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v9-icon-box' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'v4_custom_icon_box_size',
			[
				'label'        => esc_html__( 'Custom Width/Height', 'pixels-core-creative-tools-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'pixels-core-creative-tools-for-elementor' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_responsive_control(
			'v4_icon_box_width',
			[
				'label'      => esc_html__( 'Icon Box Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-button__v4-icon' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v9-icon-box' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'v4_custom_icon_box_size' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'v4_icon_box_height',
			[
				'label'      => esc_html__( 'Icon Box Height', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-button__v4-icon' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v9-icon-box' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'v4_custom_icon_box_size' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'v4_icon_box_padding',
			[
				'label'      => esc_html__( 'Icon Box Padding', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-button__v4-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v9-icon-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'v4_icon_size',
			[
				'label'      => esc_html__( 'Size', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min'  => 10,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-button__v4-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v4-icon span' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v4-icon img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v9-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v9-icon span' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v9-icon img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'v4_icon_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-button__v4-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-button__v9-icon-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'v4_icon_style_tabs' );

		$this->start_controls_tab(
			'v4_icon_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'pixels-core-creative-tools-for-elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'v4_icon_background',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .pixels-core-button__v4-icon, {{WRAPPER}} .pixels-core-button__v9-icon-box',
			]
		);

		$this->add_control(
			'v4_icon_fill_color',
			[
				'label'     => esc_html__( 'Icon Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-button__v4-icon' => 'color: {{VALUE}}; fill: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v4-icon svg path' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v9-icon-box' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v9-icon-box svg path' => 'fill: {{VALUE}}; stroke: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'v4_icon_shadow',
				'selector' => '{{WRAPPER}} .pixels-core-button__v4-icon, {{WRAPPER}} .pixels-core-button__v9-icon-box',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'v4_icon_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'pixels-core-creative-tools-for-elementor' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'v4_icon_hover_background',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .pixels-core-button__link--v4:hover .pixels-core-button__v4-icon, {{WRAPPER}} .pixels-core-button__link--v9:hover .pixels-core-button__v9-icon-box',
			]
		);

		$this->add_control(
			'v4_icon_hover_fill_color',
			[
				'label'     => esc_html__( 'Icon Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-button__link--v4:hover .pixels-core-button__v4-icon svg path' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link--v9:hover .pixels-core-button__v9-icon-box svg path' => 'fill: {{VALUE}}; stroke: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
	}

	private function register_v8_icon_style_controls(): void {
		$this->start_controls_section(
			'btn_v8_icon_style',
			[
				'label'     => esc_html__( 'Icon', 'pixels-core-creative-tools-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'variation' => 'variation_8',
				],
			]
		);

		$this->add_control(
			'v8_circle_background',
			[
				'label'     => esc_html__( 'Icon Box Background', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-button__v8-circle' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'v8_circle_background_hover',
			[
				'label'     => esc_html__( 'Icon Box Hover Background', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-button__link--v8:hover .pixels-core-button__v8-circle' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'v8_circle_size',
			[
				'label'      => esc_html__( 'Icon Box Size', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min'  => 8,
						'max'  => 80,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-button__v8-circle' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'v8_circle_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-button__v8-circle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'btn_v8_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-button__v8-arrow' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v8-arrow i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v8-arrow svg' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v8-arrow svg[fill="none"] path' => 'stroke: {{VALUE}}; fill: none',
					'{{WRAPPER}} .pixels-core-button__v8-arrow svg:not([fill="none"])' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__v8-arrow svg:not([fill="none"]) path' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'btn_v8_icon_hover_color',
			[
				'label'     => esc_html__( 'Icon Hover Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-button__link--v8:hover .pixels-core-button__v8-arrow' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link--v8:hover .pixels-core-button__v8-arrow i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link--v8:hover .pixels-core-button__v8-arrow svg' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link--v8:hover .pixels-core-button__v8-arrow svg[fill="none"] path' => 'stroke: {{VALUE}}; fill: none',
					'{{WRAPPER}} .pixels-core-button__link--v8:hover .pixels-core-button__v8-arrow svg:not([fill="none"])' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .pixels-core-button__link--v8:hover .pixels-core-button__v8-arrow svg:not([fill="none"]) path' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'v8_arrow_size',
			[
				'label'      => esc_html__( 'Icon Size', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min'  => 8,
						'max'  => 48,
						'step' => 1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 16,
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-button__v8-icon' => '--pc-v8-icon-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return array{url:string,is_external:bool,nofollow:bool}
	 */
	private function get_link_data( array $settings ): array {
		$link = $settings['btn_url'] ?? [];

		return [
			'url'         => ! empty( $link['url'] ) ? (string) $link['url'] : '#',
			'is_external' => ! empty( $link['is_external'] ),
			'nofollow'    => ! empty( $link['nofollow'] ),
		];
	}

	/**
	 * @param array<string, string> $extra_attrs Extra HTML attributes.
	 */
	private function print_button_link_open( array $link, string $class, array $extra_attrs = [] ): void {
		$attrs = array_merge(
			[
				'href'  => esc_url( $link['url'] ),
				'class' => $class,
			],
			$extra_attrs
		);

		if ( $link['is_external'] ) {
			$attrs['target'] = '_blank';
			$attrs['rel']    = 'noopener noreferrer';
		}

		if ( $link['nofollow'] ) {
			$attrs['rel'] = isset( $attrs['rel'] )
				? $attrs['rel'] . ' nofollow'
				: 'nofollow';
		}

		echo '<a';
		foreach ( $attrs as $key => $value ) {
			printf( ' %1$s="%2$s"', esc_attr( $key ), esc_attr( $value ) );
		}
		echo '>';
	}

	private function render_pixel_icon_svg(): void {
		?>
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
			<path d="M11 5H13V7H11V5Z"></path>
			<path d="M5 5H7V7H5V5Z"></path>
			<path d="M14 8H16V10H14V8Z"></path>
			<path d="M8 8H10V10H8V8Z"></path>
			<path d="M17 11H19V13H17V11Z"></path>
			<path d="M11 11H13V13H11V11Z"></path>
			<path d="M14 14H16V16H14V14Z"></path>
			<path d="M8 14H10V16H8V14Z"></path>
			<path d="M11 17H13V19H11V17Z"></path>
			<path d="M5 17H7V19H5V17Z"></path>
		</svg>
		<?php
	}

	private function render_chevron_svg(): void {
		?>
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
			<path d="M8 5H10V7H8V5Z" fill="currentColor" />
			<path d="M11 8H13V10H11V8Z" fill="currentColor" />
			<path d="M14 11H16V13H14V11Z" fill="currentColor" />
			<path d="M11 14H13V16H11V14Z" fill="currentColor" />
			<path d="M8 17H10V19H8V17Z" fill="currentColor" />
		</svg>
		<?php
	}

	/**
	 * @param array $settings Widget settings.
	 */
	private function render_v3_icon( array $settings ): void {
		$icon_source = ! empty( $settings['v3_icon_source'] ) ? $settings['v3_icon_source'] : 'default';

		if ( 'upload' === $icon_source && ! empty( $settings['v3_icon_upload']['url'] ) ) {
			echo '<img src="' . esc_url( $settings['v3_icon_upload']['url'] ) . '" alt="" />';
			return;
		}

		if ( 'library' === $icon_source && ! empty( $settings['v3_selected_icon']['value'] ) ) {
			Icons_Manager::render_icon( $settings['v3_selected_icon'], [ 'aria-hidden' => 'true' ] );
			return;
		}

		$this->render_chevron_svg();
	}

	private function render_arrow_svg( int $size = 18 ): void {
		?>
		<svg xmlns="http://www.w3.org/2000/svg" width="<?php echo esc_attr( (string) $size ); ?>" height="<?php echo esc_attr( (string) $size ); ?>" viewBox="0 0 18 18" fill="none" aria-hidden="true">
			<path d="M6.75 13.5L11.25 9L6.75 4.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path>
		</svg>
		<?php
	}

	/**
	 * @param array $settings Widget settings.
	 */
	private function render_v5_icon( array $settings ): void {
		$icon_source = ! empty( $settings['v5_icon_source'] ) ? $settings['v5_icon_source'] : 'default';

		if ( 'upload' === $icon_source && ! empty( $settings['v5_icon_upload']['url'] ) ) {
			echo '<img src="' . esc_url( $settings['v5_icon_upload']['url'] ) . '" alt="" />';
			return;
		}

		if ( 'library' === $icon_source && ! empty( $settings['v5_selected_icon']['value'] ) ) {
			Icons_Manager::render_icon( $settings['v5_selected_icon'], [ 'aria-hidden' => 'true' ] );
			return;
		}

		$this->render_arrow_svg();
	}

	/**
	 * @param array $settings Widget settings.
	 */
	private function render_v6_icon( array $settings ): void {
		$icon_source = ! empty( $settings['v6_icon_source'] ) ? $settings['v6_icon_source'] : 'default';

		if ( 'upload' === $icon_source && ! empty( $settings['v6_icon_upload']['url'] ) ) {
			echo '<img src="' . esc_url( $settings['v6_icon_upload']['url'] ) . '" alt="" />';
			return;
		}

		if ( 'library' === $icon_source && ! empty( $settings['v6_selected_icon']['value'] ) ) {
			Icons_Manager::render_icon( $settings['v6_selected_icon'], [ 'aria-hidden' => 'true' ] );
			return;
		}

		$this->render_arrow_svg();
	}

	/**
	 * @param array $settings Widget settings.
	 */
	private function render_v7_icon( array $settings ): void {
		$icon_source = ! empty( $settings['v7_icon_source'] ) ? $settings['v7_icon_source'] : 'default';

		if ( 'upload' === $icon_source && ! empty( $settings['v7_icon_upload']['url'] ) ) {
			echo '<img src="' . esc_url( $settings['v7_icon_upload']['url'] ) . '" alt="" />';
			return;
		}

		if ( 'library' === $icon_source && ! empty( $settings['v7_selected_icon']['value'] ) ) {
			Icons_Manager::render_icon( $settings['v7_selected_icon'], [ 'aria-hidden' => 'true' ] );
			return;
		}

		?>
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
			<path d="M7 17L17 7" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
			<path d="M7 7H17V17" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
		</svg>
		<?php
	}

	/**
	 * @param array $settings Widget settings.
	 */
	private function render_v8_icon( array $settings ): void {
		$icon_source = ! empty( $settings['v8_icon_source'] ) ? $settings['v8_icon_source'] : 'default';

		if ( 'upload' === $icon_source && ! empty( $settings['v8_icon_upload']['url'] ) ) {
			echo '<img src="' . esc_url( $settings['v8_icon_upload']['url'] ) . '" alt="" />';
			return;
		}

		if ( 'library' === $icon_source && ! empty( $settings['v8_selected_icon']['value'] ) ) {
			Icons_Manager::render_icon( $settings['v8_selected_icon'], [ 'aria-hidden' => 'true' ] );
			return;
		}

		$this->render_arrow_svg( 16 );
	}

	/**
	 * @param array $settings Widget settings.
	 */
	private function render_v2_icon( array $settings ): void {
		$icon_source = ! empty( $settings['v2_icon_source'] ) ? $settings['v2_icon_source'] : 'default';

		if ( 'upload' === $icon_source && ! empty( $settings['v2_icon_upload']['url'] ) ) {
			echo '<img src="' . esc_url( $settings['v2_icon_upload']['url'] ) . '" alt="" />';
			return;
		}

		if ( 'library' === $icon_source && ! empty( $settings['v2_selected_icon']['value'] ) ) {
			Icons_Manager::render_icon( $settings['v2_selected_icon'], [ 'aria-hidden' => 'true' ] );
			return;
		}

		$this->render_pixel_icon_svg();
	}

	/**
	 * @param array $settings Widget settings.
	 */
	private function render_v1_icon( array $settings ): void {
		$icon_source = ! empty( $settings['v1_icon_source'] ) ? $settings['v1_icon_source'] : 'default';

		if ( 'upload' === $icon_source && ! empty( $settings['v1_icon_upload']['url'] ) ) {
			echo '<img src="' . esc_url( $settings['v1_icon_upload']['url'] ) . '" alt="" />';
			return;
		}

		if ( 'library' === $icon_source && ! empty( $settings['v1_selected_icon']['value'] ) ) {
			Icons_Manager::render_icon( $settings['v1_selected_icon'], [ 'aria-hidden' => 'true' ] );
			return;
		}

		?>
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 256 256" aria-hidden="true">
			<path d="M181.66,133.66l-80,80a8,8,0,0,1-11.32-11.32L164.69,128,90.34,53.66a8,8,0,0,1,11.32-11.32l80,80A8,8,0,0,1,181.66,133.66Z"></path>
		</svg>
		<?php
	}

	/**
	 * @param array $settings Widget settings.
	 */
	private function render_v4_icon( array $settings ): void {
		$icon_source = ! empty( $settings['v4_icon_source'] ) ? $settings['v4_icon_source'] : 'default';

		if ( 'upload' === $icon_source && ! empty( $settings['v4_icon_upload']['url'] ) ) {
			echo '<img src="' . esc_url( $settings['v4_icon_upload']['url'] ) . '" alt="' . esc_attr__( 'Button Icon', 'pixels-core-creative-tools-for-elementor' ) . '" />';
			return;
		}

		if ( 'library' === $icon_source && ! empty( $settings['v4_selected_icon']['value'] ) ) {
			Icons_Manager::render_icon( $settings['v4_selected_icon'], [ 'aria-hidden' => 'true' ], 'span' );
			return;
		}

		if ( 'variation_9' === ( $settings['variation'] ?? '' ) ) {
			?>
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
				<path d="M7 17L17 7" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M7 7H17V17" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>
			<?php
			return;
		}

		$this->render_pixel_icon_svg();
	}

	/**
	 * @param array $settings Widget settings.
	 */
	private function render_v10_icon( array $settings ): void {
		$icon_source = ! empty( $settings['v10_icon_source'] ) ? $settings['v10_icon_source'] : 'default';

		if ( 'upload' === $icon_source && ! empty( $settings['v10_icon_upload']['url'] ) ) {
			echo '<img src="' . esc_url( $settings['v10_icon_upload']['url'] ) . '" alt="" />';
			return;
		}

		if ( 'library' === $icon_source && ! empty( $settings['v10_selected_icon']['value'] ) ) {
			Icons_Manager::render_icon( $settings['v10_selected_icon'], [ 'aria-hidden' => 'true' ] );
			return;
		}

		$this->render_arrow_svg();
	}

	protected function render(): void {
		$settings  = $this->get_settings_for_display();
		$variation = $settings['variation'] ?? 'variation_1';
		$link      = $this->get_link_data( $settings );
		$title     = (string) ( $settings['btn_title'] ?? '' );
		$show_icon = ( $settings['btn_show_icon'] ?? 'yes' ) === 'yes';
		?>
		<div class="pixels-core-button">
			<?php
			switch ( $variation ) {
				case 'variation_2':
					$full = ( $settings['enable_full_width'] ?? '' ) === 'yes';
					?>
					<div class="pixels-core-button__inner<?php echo $full ? ' pixels-core-button__inner--full' : ''; ?>">
						<?php
						$this->print_button_link_open(
							$link,
							'pixels-core-button__link pixels-core-button__link--v2' . ( $full ? ' is-full' : '' )
						);
						?>
							<?php if ( '' !== $title ) : ?>
								<span class="pixels-core-button__link--v2-text"><?php echo esc_html( $title ); ?></span>
							<?php endif; ?>
							<?php if ( $show_icon ) : ?>
								<span class="pixels-core-button__v2-icon" aria-hidden="true">
									<span class="pixels-core-button__v2-icon-layer pixels-core-button__v2-icon-layer--out"><?php $this->render_v2_icon( $settings ); ?></span>
									<span class="pixels-core-button__v2-icon-layer pixels-core-button__v2-icon-layer--in"><?php $this->render_v2_icon( $settings ); ?></span>
								</span>
							<?php endif; ?>
						</a>
					</div>
					<?php
					break;

				case 'variation_3':
					?>
					<div class="pixels-core-button__inner">
						<?php $this->print_button_link_open( $link, 'pixels-core-button__link pixels-core-button__link--v3' ); ?>
							<figure class="pixels-core-button__v3-figure">
								<span class="pixels-core-button__v3-icon pixels-core-button__v3-icon--out"><?php $this->render_v3_icon( $settings ); ?></span>
								<span class="pixels-core-button__v3-icon pixels-core-button__v3-icon--in"><?php $this->render_v3_icon( $settings ); ?></span>
							</figure>
						</a>
					</div>
					<?php
					break;

				case 'variation_4':
					?>
					<div class="pixels-core-button__inner">
						<?php
						$this->print_button_link_open(
							$link,
							'pixels-core-button__link pixels-core-button__link--v4',
							[ 'data-pixels-button-v4' => '' ]
						);
						?>
							<span class="pixels-core-button__v4-row">
								<span class="pixels-core-button__v4-icon" data-pixels-button-v4-icon aria-hidden="true">
									<?php $this->render_v4_icon( $settings ); ?>
								</span>
								<span class="pixels-core-button__v4-text" data-pixels-button-v4-text>
									<?php echo esc_html( $title ); ?>
								</span>
							</span>
						</a>
					</div>
					<?php
					break;

				case 'variation_5':
					?>
					<div class="pixels-core-button__inner">
						<?php $this->print_button_link_open( $link, 'pixels-core-button__link pixels-core-button__link--v5' ); ?>
							<span class="pixels-core-button__v5-shine" aria-hidden="true"></span>
							<span class="pixels-core-button__v5-text"><?php echo esc_html( $title ); ?></span>
							<span class="pixels-core-button__v5-arrow" aria-hidden="true">
								<span class="pixels-core-button__v5-arrow-layer pixels-core-button__v5-arrow-layer--out"><?php $this->render_v5_icon( $settings ); ?></span>
								<span class="pixels-core-button__v5-arrow-layer pixels-core-button__v5-arrow-layer--in"><?php $this->render_v5_icon( $settings ); ?></span>
							</span>
						</a>
					</div>
					<?php
					break;

				case 'variation_6':
					?>
					<div class="pixels-core-button__inner pixels-core-button__inner--v6">
						<?php $this->print_button_link_open( $link, 'pixels-core-button__link pixels-core-button__link--v6' ); ?>
							<span class="pixels-core-button__v6-text-wrap">
								<span class="pixels-core-button__v6-line">
									<span class="pixels-core-button__v6-text pixels-core-button__v6-text--upper"><?php echo esc_html( $title ); ?></span>
								</span>
								<span class="pixels-core-button__v6-line pixels-core-button__v6-line--lower">
									<span class="pixels-core-button__v6-text pixels-core-button__v6-text--lower"><?php echo esc_html( $title ); ?></span>
								</span>
							</span>
							<?php if ( $show_icon ) : ?>
								<span class="pixels-core-button__v6-arrow" aria-hidden="true">
									<span class="pixels-core-button__v6-arrow-layer pixels-core-button__v6-arrow-layer--out"><?php $this->render_v6_icon( $settings ); ?></span>
									<span class="pixels-core-button__v6-arrow-layer pixels-core-button__v6-arrow-layer--in"><?php $this->render_v6_icon( $settings ); ?></span>
								</span>
							<?php endif; ?>
						</a>
					</div>
					<?php
					break;

				case 'variation_7':
					$v7_icon_source = ! empty( $settings['v7_icon_source'] ) ? $settings['v7_icon_source'] : 'default';
					$v7_icon_class  = 'pixels-core-button__v7-circle-icon';
					if ( 'default' === $v7_icon_source ) {
						$v7_icon_class .= ' pixels-core-button__v7-circle-icon--default';
					}
					?>
					<div class="pixels-core-button__inner">
						<?php $this->print_button_link_open( $link, 'pixels-core-button__link pixels-core-button__link--v7' ); ?>
							<span class="pixels-core-button__v7-shell">
								<span class="pixels-core-button__v7-body">
									<span class="pixels-core-button__v7-text-wrap">
										<span class="pixels-core-button__v7-line">
											<span class="pixels-core-button__v7-text pixels-core-button__v7-text--upper"><?php echo esc_html( $title ); ?></span>
										</span>
										<span class="pixels-core-button__v7-line pixels-core-button__v7-line--lower">
											<span class="pixels-core-button__v7-text pixels-core-button__v7-text--lower"><?php echo esc_html( $title ); ?></span>
										</span>
									</span>
									<span class="pixels-core-button__v7-circle">
										<span class="pixels-core-button__v7-circle-bg" aria-hidden="true"></span>
										<span class="<?php echo esc_attr( $v7_icon_class ); ?>" aria-hidden="true">
											<?php $this->render_v7_icon( $settings ); ?>
										</span>
									</span>
									<span class="pixels-core-button__v7-ring pixels-core-button__v7-ring--1" aria-hidden="true"></span>
									<span class="pixels-core-button__v7-ring pixels-core-button__v7-ring--2" aria-hidden="true"></span>
									<span class="pixels-core-button__v7-ring pixels-core-button__v7-ring--3" aria-hidden="true"></span>
								</span>
							</span>
						</a>
					</div>
					<?php
					break;

				case 'variation_8':
					?>
					<div class="pixels-core-button__inner">
						<?php $this->print_button_link_open( $link, 'pixels-core-button__link pixels-core-button__link--v8' ); ?>
							<span class="pixels-core-button__v8-text-wrap">
								<span class="pixels-core-button__v8-text pixels-core-button__v8-text--upper"><?php echo esc_html( $title ); ?></span>
								<span class="pixels-core-button__v8-text"><?php echo esc_html( $title ); ?></span>
							</span>
							<span class="pixels-core-button__v8-icon" aria-hidden="true">
								<span class="pixels-core-button__v8-circle"></span>
								<span class="pixels-core-button__v8-arrow pixels-core-button__v8-arrow--inner"><?php $this->render_v8_icon( $settings ); ?></span>
								<span class="pixels-core-button__v8-arrow pixels-core-button__v8-arrow--outer"><?php $this->render_v8_icon( $settings ); ?></span>
							</span>
						</a>
					</div>
					<?php
					break;

				case 'variation_9':
					?>
					<div class="pixels-core-button__inner">
						<div class="pixels-core-button__v9-outer">
							<?php $this->print_button_link_open( $link, 'pixels-core-button__link pixels-core-button__link--v9' ); ?>
								<span class="pixels-core-button__v9-row">
									<span class="pixels-core-button__v9-text"><?php echo esc_html( $title ); ?></span>
									<span class="pixels-core-button__v9-icon-box" aria-hidden="true">
										<span class="pixels-core-button__v9-icon-stage">
											<span class="pixels-core-button__v9-icon pixels-core-button__v9-icon--primary">
												<?php $this->render_v4_icon( $settings ); ?>
											</span>
											<span class="pixels-core-button__v9-icon pixels-core-button__v9-icon--secondary">
												<?php $this->render_v4_icon( $settings ); ?>
											</span>
										</span>
									</span>
								</span>
							</a>
						</div>
					</div>
					<?php
					break;

				case 'variation_10':
					?>
					<div class="pixels-core-button__inner">
						<?php $this->print_button_link_open( $link, 'pixels-core-button__link pixels-core-button__link--v10' ); ?>
							<span class="pixels-core-button__v10-body">
								<span class="pixels-core-button__v10-glow pixels-core-button__v10-glow--left" aria-hidden="true"></span>
								<span class="pixels-core-button__v10-glow pixels-core-button__v10-glow--right" aria-hidden="true"></span>
								<span class="pixels-core-button__v10-text"><?php echo esc_html( $title ); ?></span>
								<span class="pixels-core-button__v10-arrow" aria-hidden="true">
									<span class="pixels-core-button__v10-arrow-layer pixels-core-button__v10-arrow-layer--out">
										<?php $this->render_v10_icon( $settings ); ?>
									</span>
									<span class="pixels-core-button__v10-arrow-layer pixels-core-button__v10-arrow-layer--in">
										<?php $this->render_v10_icon( $settings ); ?>
									</span>
								</span>
							</span>
						</a>
					</div>
					<?php
					break;

				case 'variation_1':
				default:
					$size_map  = [
						'btn-md' => 'md',
						'btn-lg' => 'lg',
						'btn-xl' => 'xl',
					];
					$size      = $size_map[ $settings['btn_size'] ?? 'btn-xl' ] ?? 'xl';
					$v1_show_icon = ( $settings['v1_show_icon'] ?? 'yes' ) === 'yes';
					?>
					<div class="pixels-core-button__inner">
						<?php
						$this->print_button_link_open(
							$link,
							'pixels-core-button__link pixels-core-button__link--v1 pixels-core-button__link--' . $size . ( $v1_show_icon ? '' : ' pixels-core-button__link--v1-no-icon' )
						);
						?>
							<span class="pixels-core-button__v1-text"><?php echo esc_html( $title ); ?></span>
							<?php if ( $v1_show_icon ) : ?>
								<span class="pixels-core-button__v1-icon" aria-hidden="true">
									<?php $this->render_v1_icon( $settings ); ?>
								</span>
							<?php endif; ?>
						</a>
					</div>
					<?php
					break;
			}
			?>
		</div>
		<?php
	}
}
