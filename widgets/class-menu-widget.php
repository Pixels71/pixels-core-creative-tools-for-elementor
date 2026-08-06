<?php
namespace PixelsCore\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Nav_Menu_Widget extends Widget_Base {

	use Widget_Assets_Trait;

	public function get_name(): string {
		return 'pixels-menu';
	}

	public function get_title(): string {
		return esc_html__( 'Menu', 'pixels-core-creative-tools-for-elementor' );
	}

	public function get_icon(): string {
		return 'pixels-icon pixels-icon-menu';
	}

	public function get_categories(): array {
		return [ 'pixels-core' ];
	}

	public function get_keywords(): array {
		return [ 'menu', 'nav', 'navigation', 'header', 'pixels' ];
	}

	protected function get_assets_slug(): string {
		return 'menu';
	}

	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	private function register_content_controls(): void {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Menu', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'nav_menu',
			[
				'label'   => esc_html__( 'WordPress Menu', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $this->get_wp_nav_menus(),
				'default' => '',
			]
		);

		$this->add_control(
			'layout',
			[
				'label'   => esc_html__( 'Layout', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'horizontal' => esc_html__( 'Horizontal', 'pixels-core-creative-tools-for-elementor' ),
					'vertical'   => esc_html__( 'Vertical', 'pixels-core-creative-tools-for-elementor' ),
				],
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'   => esc_html__( 'Alignment', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
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
				'default'              => 'left',
				'selectors_dictionary' => [
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				],
				'selectors'            => [
					'{{WRAPPER}} .pixels-core-menu--layout-horizontal:not(.is-hamburger) .pixels-core-menu__wrap' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .pixels-core-menu--layout-vertical:not(.is-hamburger) .pixels-core-menu__wrap'   => 'align-items: {{VALUE}};',
					'{{WRAPPER}} .pixels-core-menu.is-hamburger'                                                    => 'align-items: {{VALUE}};',
					'{{WRAPPER}} .pixels-core-menu.is-hamburger .pixels-core-menu__list'                            => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_responsive',
			[
				'label' => esc_html__( 'Responsive', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'desktop_hamburger',
			[
				'label'              => esc_html__( 'Hamburger on Desktop', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'pixels-core-creative-tools-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => '',
				'frontend_available' => true,
				'description'        => esc_html__( 'Use the hamburger toggle instead of an inline menu on desktop.', 'pixels-core-creative-tools-for-elementor' ),
			]
		);

		$this->add_control(
			'mobile_hamburger',
			[
				'label'              => esc_html__( 'Hamburger on Mobile', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'pixels-core-creative-tools-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'frontend_available' => true,
				'description'        => esc_html__( 'Use the hamburger toggle instead of an inline menu below the breakpoint.', 'pixels-core-creative-tools-for-elementor' ),
			]
		);

		$this->add_control(
			'breakpoint',
			[
				'label'              => esc_html__( 'Breakpoint', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 1024,
				'min'                => 320,
				'max'                => 1920,
				'frontend_available' => true,
				'description'        => esc_html__( 'Viewport width (px) that separates desktop and mobile menu behavior.', 'pixels-core-creative-tools-for-elementor' ),
			]
		);

		$this->add_control(
			'hamburger_panel_type',
			[
				'label'   => esc_html__( 'Hamburger Panel Type', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'aside',
				'options' => [
					'aside'    => esc_html__( 'Aside (Slide-in)', 'pixels-core-creative-tools-for-elementor' ),
					'dropdown' => esc_html__( 'Dropdown', 'pixels-core-creative-tools-for-elementor' ),
				],
			]
		);

		$this->add_control(
			'aside_position',
			[
				'label'     => esc_html__( 'Aside Position', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'left',
				'options'   => [
					'left'  => [
						'title' => esc_html__( 'Left', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'toggle'    => false,
				'condition' => [
					'hamburger_panel_type' => 'aside',
				],
			]
		);

		$this->add_control(
			'lock_body_scroll',
			[
				'label'              => esc_html__( 'Lock Body Scroll', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'pixels-core-creative-tools-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'frontend_available' => true,
				'condition'          => [
					'hamburger_panel_type' => 'aside',
				],
			]
		);

		$this->add_control(
			'mobile_toggle_label',
			[
				'label'   => esc_html__( 'Hamburger Label', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Menu', 'pixels-core-creative-tools-for-elementor' ),
			]
		);

		$this->add_control(
			'close_on_outside_click',
			[
				'label'              => esc_html__( 'Close on Outside Click', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'pixels-core-creative-tools-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'close_on_link_click',
			[
				'label'              => esc_html__( 'Close on Link Click', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'pixels-core-creative-tools-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'frontend_available' => true,
			]
		);

		$this->end_controls_section();
	}

	private function register_style_controls(): void {
		$top_level_link = '{{WRAPPER}} .pixels-core-menu__list > .menu-item > a';
		$top_level_active = '{{WRAPPER}} .pixels-core-menu__list > .menu-item.current-menu-item > a, {{WRAPPER}} .pixels-core-menu__list > .menu-item.current-menu-ancestor > a, {{WRAPPER}} .pixels-core-menu__list > .menu-item.current-menu-parent > a';
		$submenu_link     = '{{WRAPPER}} .pixels-core-menu__list .sub-menu a';
		$submenu_panel    = '{{WRAPPER}} .pixels-core-menu__list .sub-menu';
		$hamburger_panel  = '{{WRAPPER}} .pixels-core-menu.is-hamburger .pixels-core-menu__wrap';
		$dropdown_panel   = '{{WRAPPER}} .pixels-core-menu--panel-dropdown.is-hamburger.is-open .pixels-core-menu__wrap';
		$overlay          = '{{WRAPPER}} .pixels-core-menu--panel-aside.is-hamburger .pixels-core-menu__overlay';
		$dropdown_arrow   = '{{WRAPPER}} .pixels-core-menu__list .menu-item-has-children > a::after, {{WRAPPER}} .pixels-core-menu__list .pixels-mega-menu-item > a::after';

		$this->start_controls_section(
			'section_style_menu',
			[
				'label' => esc_html__( 'Menu Items', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'menu_typography',
				'selector' => $top_level_link,
			]
		);

		$this->start_controls_tabs( 'menu_link_tabs' );

		$this->start_controls_tab(
			'menu_link_normal',
			[ 'label' => esc_html__( 'Normal', 'pixels-core-creative-tools-for-elementor' ) ]
		);

		$this->add_control(
			'menu_link_color',
			[
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$top_level_link => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'menu_link_background',
			[
				'label'     => esc_html__( 'Background', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$top_level_link => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'menu_link_hover',
			[ 'label' => esc_html__( 'Hover', 'pixels-core-creative-tools-for-elementor' ) ]
		);

		$this->add_control(
			'menu_link_hover_color',
			[
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$top_level_link . ':hover, ' . $top_level_link . ':focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'menu_link_hover_background',
			[
				'label'     => esc_html__( 'Background', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$top_level_link . ':hover, ' . $top_level_link . ':focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'menu_link_active',
			[ 'label' => esc_html__( 'Active', 'pixels-core-creative-tools-for-elementor' ) ]
		);

		$this->add_control(
			'menu_link_active_color',
			[
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$top_level_active => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'menu_link_active_background',
			[
				'label'     => esc_html__( 'Background', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$top_level_active => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'menu_text_shadow',
				'selector' => $top_level_link,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'menu_link_border',
				'selector' => $top_level_link,
			]
		);

		$this->add_responsive_control(
			'menu_link_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					$top_level_link => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'menu_link_shadow',
				'selector' => $top_level_link,
			]
		);

		$this->add_responsive_control(
			'menu_item_gap',
			[
				'label'      => esc_html__( 'Item Spacing', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 80 ],
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-menu--layout-horizontal .pixels-core-menu__list > .menu-item' => 'margin-inline: calc({{SIZE}}{{UNIT}} / 2);',
					'{{WRAPPER}} .pixels-core-menu--layout-vertical .pixels-core-menu__list > .menu-item + .menu-item' => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-menu.is-hamburger .pixels-core-menu__list > .menu-item + .menu-item' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'menu_item_padding',
			[
				'label'      => esc_html__( 'Item Padding', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-menu__list > .menu-item > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'menu_divider_heading',
			[
				'label'     => esc_html__( 'Divider', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'menu_divider_color',
			[
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-menu__list > .menu-item + .menu-item' => 'border-top-color: {{VALUE}};',
				],
				'condition' => [
					'menu_divider_style!' => '',
				],
			]
		);

		$this->add_control(
			'menu_divider_style',
			[
				'label'   => esc_html__( 'Style', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''       => esc_html__( 'None', 'pixels-core-creative-tools-for-elementor' ),
					'solid'  => esc_html__( 'Solid', 'pixels-core-creative-tools-for-elementor' ),
					'dashed' => esc_html__( 'Dashed', 'pixels-core-creative-tools-for-elementor' ),
					'dotted' => esc_html__( 'Dotted', 'pixels-core-creative-tools-for-elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}} .pixels-core-menu__list > .menu-item + .menu-item' => 'border-top-style: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'menu_divider_width',
			[
				'label'      => esc_html__( 'Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [ 'min' => 1, 'max' => 10 ],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-menu__list > .menu-item + .menu-item' => 'border-top-width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'menu_divider_style!' => '',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_submenu',
			[
				'label' => esc_html__( 'Submenu', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'submenu_min_width',
			[
				'label'      => esc_html__( 'Min Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px' => [ 'min' => 120, 'max' => 500 ],
				],
				'selectors'  => [
					$submenu_panel => 'min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'submenu_background',
				'selector' => $submenu_panel,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'submenu_border',
				'selector' => $submenu_panel,
			]
		);

		$this->add_responsive_control(
			'submenu_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					$submenu_panel => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'submenu_shadow',
				'selector' => $submenu_panel,
			]
		);

		$this->add_responsive_control(
			'submenu_padding',
			[
				'label'      => esc_html__( 'Panel Padding', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					$submenu_panel => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'submenu_typography',
				'selector' => $submenu_link,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'submenu_link_tabs' );

		$this->start_controls_tab(
			'submenu_link_normal',
			[ 'label' => esc_html__( 'Normal', 'pixels-core-creative-tools-for-elementor' ) ]
		);

		$this->add_control(
			'submenu_link_color',
			[
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$submenu_link => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'submenu_link_background',
			[
				'label'     => esc_html__( 'Background', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$submenu_link => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'submenu_link_hover',
			[ 'label' => esc_html__( 'Hover', 'pixels-core-creative-tools-for-elementor' ) ]
		);

		$this->add_control(
			'submenu_link_hover_color',
			[
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$submenu_link . ':hover, ' . $submenu_link . ':focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'submenu_link_hover_background',
			[
				'label'     => esc_html__( 'Background', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$submenu_link . ':hover, ' . $submenu_link . ':focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'submenu_link_active',
			[ 'label' => esc_html__( 'Active', 'pixels-core-creative-tools-for-elementor' ) ]
		);

		$this->add_control(
			'submenu_link_active_color',
			[
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-menu__list .sub-menu .current-menu-item > a, {{WRAPPER}} .pixels-core-menu__list .sub-menu .current-menu-ancestor > a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'submenu_link_active_background',
			[
				'label'     => esc_html__( 'Background', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-menu__list .sub-menu .current-menu-item > a, {{WRAPPER}} .pixels-core-menu__list .sub-menu .current-menu-ancestor > a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'submenu_item_padding',
			[
				'label'      => esc_html__( 'Item Padding', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'separator'  => 'before',
				'selectors'  => [
					$submenu_link => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_dropdown_indicator',
			[
				'label' => esc_html__( 'Dropdown Indicator', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'show_dropdown_indicator',
			[
				'label'        => esc_html__( 'Show Indicator', 'pixels-core-creative-tools-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'pixels-core-creative-tools-for-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'selectors_dictionary' => [
					'yes' => 'inline-block',
					''    => 'none',
				],
				'selectors' => [
					$dropdown_arrow => 'display: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'dropdown_indicator_color',
			[
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					$dropdown_arrow => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'show_dropdown_indicator' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'dropdown_indicator_size',
			[
				'label'      => esc_html__( 'Size', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [ 'min' => 4, 'max' => 20 ],
				],
				'selectors'  => [
					$dropdown_arrow => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'show_dropdown_indicator' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_toggle',
			[
				'label' => esc_html__( 'Hamburger Toggle', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'toggle_tabs' );

		$this->start_controls_tab(
			'toggle_normal',
			[ 'label' => esc_html__( 'Normal', 'pixels-core-creative-tools-for-elementor' ) ]
		);

		$this->add_control(
			'toggle_color',
			[
				'label'     => esc_html__( 'Icon Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-menu__toggle' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'toggle_background',
			[
				'label'     => esc_html__( 'Background', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-menu__toggle' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'toggle_hover',
			[ 'label' => esc_html__( 'Hover', 'pixels-core-creative-tools-for-elementor' ) ]
		);

		$this->add_control(
			'toggle_hover_color',
			[
				'label'     => esc_html__( 'Icon Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-menu__toggle:hover, {{WRAPPER}} .pixels-core-menu__toggle:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'toggle_hover_background',
			[
				'label'     => esc_html__( 'Background', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-menu__toggle:hover, {{WRAPPER}} .pixels-core-menu__toggle:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'toggle_border',
				'selector'  => '{{WRAPPER}} .pixels-core-menu__toggle',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'toggle_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-menu__toggle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'toggle_shadow',
				'selector' => '{{WRAPPER}} .pixels-core-menu__toggle',
			]
		);

		$this->add_responsive_control(
			'toggle_size',
			[
				'label'      => esc_html__( 'Button Size', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [ 'min' => 24, 'max' => 80 ],
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-menu__toggle' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'toggle_bar_width',
			[
				'label'      => esc_html__( 'Bar Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [ 'min' => 12, 'max' => 40 ],
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-menu__toggle-icon' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'toggle_bar_thickness',
			[
				'label'      => esc_html__( 'Bar Thickness', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [ 'min' => 1, 'max' => 8 ],
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-menu__toggle-bar' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'toggle_bar_gap',
			[
				'label'      => esc_html__( 'Bar Gap', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [ 'min' => 2, 'max' => 12 ],
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-menu__toggle-icon' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_mobile_panel',
			[
				'label' => esc_html__( 'Hamburger Panel', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'aside_width',
			[
				'label'      => esc_html__( 'Aside Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'vw' ],
				'range'      => [
					'px' => [ 'min' => 200, 'max' => 600 ],
					'%'  => [ 'min' => 20, 'max' => 100 ],
					'vw' => [ 'min' => 20, 'max' => 100 ],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 320,
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-menu--panel-aside.is-hamburger .pixels-core-menu__wrap' => 'width: {{SIZE}}{{UNIT}}; max-width: 100vw;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'mobile_panel_background',
				'selector' => $hamburger_panel,
			]
		);

		$this->add_responsive_control(
			'mobile_panel_padding',
			[
				'label'      => esc_html__( 'Padding', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					$hamburger_panel => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'mobile_panel_border',
				'selector' => $hamburger_panel,
			]
		);

		$this->add_responsive_control(
			'mobile_panel_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					$hamburger_panel => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'mobile_panel_shadow',
				'selector' => $hamburger_panel,
			]
		);

		$this->add_responsive_control(
			'mobile_panel_offset',
			[
				'label'      => esc_html__( 'Dropdown Top Offset', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [ 'min' => 0, 'max' => 60 ],
				],
				'selectors'  => [
					$dropdown_panel => 'top: calc(100% + {{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'overlay_heading',
			[
				'label'     => esc_html__( 'Aside Overlay', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'overlay_color',
			[
				'label'     => esc_html__( 'Overlay Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0, 0, 0, 0.5)',
				'selectors' => [
					$overlay => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return array<string, string>
	 */
	private function get_wp_nav_menus(): array {
		$options = [
			'' => esc_html__( '— Select —', 'pixels-core-creative-tools-for-elementor' ),
		];

		if ( ! function_exists( 'wp_get_nav_menus' ) ) {
			return $options;
		}

		$menus = wp_get_nav_menus();

		if ( empty( $menus ) || is_wp_error( $menus ) ) {
			return $options;
		}

		foreach ( $menus as $menu ) {
			if ( empty( $menu->slug ) ) {
				continue;
			}

			$options[ $menu->slug ] = $menu->name;
		}

		return $options;
	}

	private function is_editor_preview(): bool {
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return false;
		}

		$editor = \Elementor\Plugin::$instance->editor ?? null;

		return $editor && $editor->is_edit_mode();
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['nav_menu'] ) ) {
			if ( $this->is_editor_preview() ) {
				echo '<div class="pixels-core-menu pixels-core-menu--placeholder">';
				echo esc_html__( 'Select a WordPress menu in the widget settings.', 'pixels-core-creative-tools-for-elementor' );
				echo '</div>';
			}

			return;
		}

		$breakpoint   = max( 320, min( 1920, (int) ( $settings['breakpoint'] ?? 1024 ) ) );
		$layout       = 'vertical' === ( $settings['layout'] ?? 'horizontal' ) ? 'vertical' : 'horizontal';
		$panel_type   = 'dropdown' === ( $settings['hamburger_panel_type'] ?? 'aside' ) ? 'dropdown' : 'aside';
		$aside_side   = 'right' === ( $settings['aside_position'] ?? 'left' ) ? 'right' : 'left';
		$menu_id      = 'pixels-core-menu-' . $this->get_id();
		$toggle_id    = $menu_id . '-toggle';
		$overlay_id   = $menu_id . '-overlay';
		$close_id     = $menu_id . '-close';
		$toggle_label = ! empty( $settings['mobile_toggle_label'] )
			? $settings['mobile_toggle_label']
			: esc_html__( 'Menu', 'pixels-core-creative-tools-for-elementor' );
		$nav_classes  = [
			'pixels-core-menu',
			'pixels-core-menu--layout-' . $layout,
			'pixels-core-menu--panel-' . $panel_type,
		];

		if ( 'aside' === $panel_type ) {
			$nav_classes[] = 'pixels-core-menu--aside-' . $aside_side;
		}

		$this->add_render_attribute( 'nav', 'class', $nav_classes );
		$this->add_render_attribute( 'nav', 'style', '--pixels-menu-breakpoint: ' . $breakpoint . 'px;' );
		$this->add_render_attribute( 'nav', 'data-breakpoint', (string) $breakpoint );
		$this->add_render_attribute( 'nav', 'data-desktop-hamburger', 'yes' === ( $settings['desktop_hamburger'] ?? '' ) ? '1' : '0' );
		$this->add_render_attribute( 'nav', 'data-mobile-hamburger', 'yes' === ( $settings['mobile_hamburger'] ?? 'yes' ) ? '1' : '0' );

		$this->add_render_attribute( 'toggle', 'class', 'pixels-core-menu__toggle' );
		$this->add_render_attribute( 'toggle', 'type', 'button' );
		$this->add_render_attribute( 'toggle', 'id', $toggle_id );
		$this->add_render_attribute( 'toggle', 'aria-controls', $menu_id );
		$this->add_render_attribute( 'toggle', 'aria-expanded', 'false' );
		$this->add_render_attribute( 'toggle', 'aria-label', $toggle_label );

		$this->add_render_attribute( 'wrap', 'class', 'pixels-core-menu__wrap' );
		$this->add_render_attribute( 'wrap', 'id', $menu_id );
		$this->add_render_attribute( 'overlay', 'class', 'pixels-core-menu__overlay' );
		$this->add_render_attribute( 'overlay', 'id', $overlay_id );
		$this->add_render_attribute( 'overlay', 'aria-hidden', 'true' );

		$this->add_render_attribute( 'close', 'class', 'pixels-core-menu__close' );
		$this->add_render_attribute( 'close', 'type', 'button' );
		$this->add_render_attribute( 'close', 'id', $close_id );
		$this->add_render_attribute( 'close', 'aria-label', esc_html__( 'Close menu', 'pixels-core-creative-tools-for-elementor' ) );

		?>
		<nav <?php $this->print_render_attribute_string( 'nav' ); ?>>
			<button <?php $this->print_render_attribute_string( 'toggle' ); ?>>
				<span class="pixels-core-menu__toggle-icon" aria-hidden="true">
					<span class="pixels-core-menu__toggle-bar"></span>
					<span class="pixels-core-menu__toggle-bar"></span>
					<span class="pixels-core-menu__toggle-bar"></span>
				</span>
				<span class="screen-reader-text"><?php echo esc_html( $toggle_label ); ?></span>
			</button>
			<?php if ( 'aside' === $panel_type ) : ?>
				<div <?php $this->print_render_attribute_string( 'overlay' ); ?>></div>
			<?php endif; ?>
			<div <?php $this->print_render_attribute_string( 'wrap' ); ?>>
				<?php if ( 'aside' === $panel_type ) : ?>
					<button <?php $this->print_render_attribute_string( 'close' ); ?>>
						<span aria-hidden="true">&times;</span>
						<span class="screen-reader-text"><?php esc_html_e( 'Close menu', 'pixels-core-creative-tools-for-elementor' ); ?></span>
					</button>
				<?php endif; ?>
				<?php
				wp_nav_menu(
					[
						'menu'        => $settings['nav_menu'],
						'container'   => false,
						'menu_class'  => 'pixels-core-menu__list',
						'fallback_cb' => false,
						'depth'       => 0,
						'items_wrap'  => '<ul id="%1$s" class="%2$s">%3$s</ul>',
						'menu_id'     => $menu_id . '-list',
					]
				);
				?>
			</div>
		</nav>
		<?php
	}
}
