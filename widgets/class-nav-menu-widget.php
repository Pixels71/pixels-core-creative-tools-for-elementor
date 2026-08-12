<?php
/**
 * Menu widget.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Widgets;

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

/**
 * Nav menu widget.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */
class Nav_Menu_Widget extends Widget_Base {

	use Widget_Assets_Trait;

	/**
	 * Get name.
	 *
	 * @return string Result.
	 */
	public function get_name(): string {
		return 'pixeccte-menu';
	}

	/**
	 * Get title.
	 *
	 * @return string Result.
	 */
	public function get_title(): string {
		return esc_html__( 'Menu', 'pixels-core-creative-tools-for-elementor' );
	}

	/**
	 * Get icon.
	 *
	 * @return string Result.
	 */
	public function get_icon(): string {
		return 'pixeccte-icon pixeccte-icon-menu';
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
		return array( 'menu', 'nav', 'navigation', 'header', 'pixeccte' );
	}

	/**
	 * Get assets slug.
	 *
	 * @return string Result.
	 */
	protected function get_assets_slug(): string {
		return 'menu';
	}

	/**
	 * Register controls.
	 */
	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	/**
	 * Register content controls.
	 */
	private function register_content_controls(): void {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Menu', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'nav_menu',
			array(
				'label'   => esc_html__( 'WordPress Menu', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $this->get_wp_nav_menus(),
				'default' => '',
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'   => esc_html__( 'Layout', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => array(
					'horizontal' => esc_html__( 'Horizontal', 'pixels-core-creative-tools-for-elementor' ),
					'vertical'   => esc_html__( 'Vertical', 'pixels-core-creative-tools-for-elementor' ),
				),
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'                => esc_html__( 'Alignment', 'pixels-core-creative-tools-for-elementor' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'              => 'left',
				'selectors_dictionary' => array(
					'left'   => 'flex-start',
					'center' => 'center',
					'right'  => 'flex-end',
				),
				'selectors'            => array(
					'{{WRAPPER}} .pixeccte-menu--layout-horizontal:not(.is-hamburger) .pixeccte-menu__wrap' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .pixeccte-menu--layout-vertical:not(.is-hamburger) .pixeccte-menu__wrap'   => 'align-items: {{VALUE}};',
					'{{WRAPPER}} .pixeccte-menu.is-hamburger'                                                    => 'align-items: {{VALUE}};',
					'{{WRAPPER}} .pixeccte-menu.is-hamburger .pixeccte-menu__list'                            => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_responsive',
			array(
				'label' => esc_html__( 'Responsive', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'desktop_hamburger',
			array(
				'label'              => esc_html__( 'Hamburger on Desktop', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'pixels-core-creative-tools-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => '',
				'frontend_available' => true,
				'description'        => esc_html__( 'Use the hamburger toggle instead of an inline menu on desktop.', 'pixels-core-creative-tools-for-elementor' ),
			)
		);

		$this->add_control(
			'mobile_hamburger',
			array(
				'label'              => esc_html__( 'Hamburger on Mobile', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'pixels-core-creative-tools-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'frontend_available' => true,
				'description'        => esc_html__( 'Use the hamburger toggle instead of an inline menu below the breakpoint.', 'pixels-core-creative-tools-for-elementor' ),
			)
		);

		$this->add_control(
			'breakpoint',
			array(
				'label'              => esc_html__( 'Breakpoint', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 1024,
				'min'                => 320,
				'max'                => 1920,
				'frontend_available' => true,
				'description'        => esc_html__( 'Viewport width (px) that separates desktop and mobile menu behavior.', 'pixels-core-creative-tools-for-elementor' ),
			)
		);

		$this->add_control(
			'hamburger_panel_type',
			array(
				'label'   => esc_html__( 'Hamburger Panel Type', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'aside',
				'options' => array(
					'aside'    => esc_html__( 'Aside (Slide-in)', 'pixels-core-creative-tools-for-elementor' ),
					'dropdown' => esc_html__( 'Dropdown', 'pixels-core-creative-tools-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'aside_position',
			array(
				'label'     => esc_html__( 'Aside Position', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'left',
				'options'   => array(
					'left'  => array(
						'title' => esc_html__( 'Left', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'toggle'    => false,
				'condition' => array(
					'hamburger_panel_type' => 'aside',
				),
			)
		);

		$this->add_control(
			'lock_body_scroll',
			array(
				'label'              => esc_html__( 'Lock Body Scroll', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'pixels-core-creative-tools-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'frontend_available' => true,
				'condition'          => array(
					'hamburger_panel_type' => 'aside',
				),
			)
		);

		$this->add_control(
			'mobile_toggle_label',
			array(
				'label'   => esc_html__( 'Hamburger Label', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Menu', 'pixels-core-creative-tools-for-elementor' ),
			)
		);

		$this->add_control(
			'close_on_outside_click',
			array(
				'label'              => esc_html__( 'Close on Outside Click', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'pixels-core-creative-tools-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'close_on_link_click',
			array(
				'label'              => esc_html__( 'Close on Link Click', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'pixels-core-creative-tools-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => 'yes',
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register style controls.
	 */
	private function register_style_controls(): void {
		$top_level_link   = '{{WRAPPER}} .pixeccte-menu__list > .menu-item > a';
		$top_level_active = '{{WRAPPER}} .pixeccte-menu__list > .menu-item.current-menu-item > a, {{WRAPPER}} .pixeccte-menu__list > .menu-item.current-menu-ancestor > a, {{WRAPPER}} .pixeccte-menu__list > .menu-item.current-menu-parent > a';
		$submenu_link     = '{{WRAPPER}} .pixeccte-menu__list .sub-menu a';
		$submenu_panel    = '{{WRAPPER}} .pixeccte-menu__list .sub-menu';
		$hamburger_panel  = '{{WRAPPER}} .pixeccte-menu.is-hamburger .pixeccte-menu__wrap';
		$dropdown_panel   = '{{WRAPPER}} .pixeccte-menu--panel-dropdown.is-hamburger.is-open .pixeccte-menu__wrap';
		$overlay          = '{{WRAPPER}} .pixeccte-menu--panel-aside.is-hamburger .pixeccte-menu__overlay';
		$dropdown_arrow   = '{{WRAPPER}} .pixeccte-menu__list .menu-item-has-children > a::after, {{WRAPPER}} .pixeccte-menu__list .pixeccte-mega-menu-item > a::after';

		$this->start_controls_section(
			'section_style_menu',
			array(
				'label' => esc_html__( 'Menu Items', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'menu_typography',
				'selector' => $top_level_link,
			)
		);

		$this->start_controls_tabs( 'menu_link_tabs' );

		$this->start_controls_tab(
			'menu_link_normal',
			array( 'label' => esc_html__( 'Normal', 'pixels-core-creative-tools-for-elementor' ) )
		);

		$this->add_control(
			'menu_link_color',
			array(
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$top_level_link => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'menu_link_background',
			array(
				'label'     => esc_html__( 'Background', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$top_level_link => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'menu_link_hover',
			array( 'label' => esc_html__( 'Hover', 'pixels-core-creative-tools-for-elementor' ) )
		);

		$this->add_control(
			'menu_link_hover_color',
			array(
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$top_level_link . ':hover, ' . $top_level_link . ':focus' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'menu_link_hover_background',
			array(
				'label'     => esc_html__( 'Background', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$top_level_link . ':hover, ' . $top_level_link . ':focus' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'menu_link_active',
			array( 'label' => esc_html__( 'Active', 'pixels-core-creative-tools-for-elementor' ) )
		);

		$this->add_control(
			'menu_link_active_color',
			array(
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$top_level_active => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'menu_link_active_background',
			array(
				'label'     => esc_html__( 'Background', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$top_level_active => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'menu_text_shadow',
				'selector' => $top_level_link,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'menu_link_border',
				'selector' => $top_level_link,
			)
		);

		$this->add_responsive_control(
			'menu_link_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					$top_level_link => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'menu_link_shadow',
				'selector' => $top_level_link,
			)
		);

		$this->add_responsive_control(
			'menu_item_gap',
			array(
				'label'      => esc_html__( 'Item Spacing', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-menu--layout-horizontal .pixeccte-menu__list > .menu-item' => 'margin-inline: calc({{SIZE}}{{UNIT}} / 2);',
					'{{WRAPPER}} .pixeccte-menu--layout-vertical .pixeccte-menu__list > .menu-item + .menu-item' => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixeccte-menu.is-hamburger .pixeccte-menu__list > .menu-item + .menu-item' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'menu_item_padding',
			array(
				'label'      => esc_html__( 'Item Padding', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-menu__list > .menu-item > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'menu_divider_heading',
			array(
				'label'     => esc_html__( 'Divider', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'menu_divider_color',
			array(
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-menu__list > .menu-item + .menu-item' => 'border-top-color: {{VALUE}};',
				),
				'condition' => array(
					'menu_divider_style!' => '',
				),
			)
		);

		$this->add_control(
			'menu_divider_style',
			array(
				'label'     => esc_html__( 'Style', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => array(
					''       => esc_html__( 'None', 'pixels-core-creative-tools-for-elementor' ),
					'solid'  => esc_html__( 'Solid', 'pixels-core-creative-tools-for-elementor' ),
					'dashed' => esc_html__( 'Dashed', 'pixels-core-creative-tools-for-elementor' ),
					'dotted' => esc_html__( 'Dotted', 'pixels-core-creative-tools-for-elementor' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-menu__list > .menu-item + .menu-item' => 'border-top-style: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'menu_divider_width',
			array(
				'label'      => esc_html__( 'Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 10,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 1,
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-menu__list > .menu-item + .menu-item' => 'border-top-width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'menu_divider_style!' => '',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_submenu',
			array(
				'label' => esc_html__( 'Submenu', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'submenu_min_width',
			array(
				'label'      => esc_html__( 'Min Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 120,
						'max' => 500,
					),
				),
				'selectors'  => array(
					$submenu_panel => 'min-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'submenu_background',
				'selector' => $submenu_panel,
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'submenu_border',
				'selector' => $submenu_panel,
			)
		);

		$this->add_responsive_control(
			'submenu_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					$submenu_panel => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'submenu_shadow',
				'selector' => $submenu_panel,
			)
		);

		$this->add_responsive_control(
			'submenu_padding',
			array(
				'label'      => esc_html__( 'Panel Padding', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					$submenu_panel => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'submenu_typography',
				'selector'  => $submenu_link,
				'separator' => 'before',
			)
		);

		$this->start_controls_tabs( 'submenu_link_tabs' );

		$this->start_controls_tab(
			'submenu_link_normal',
			array( 'label' => esc_html__( 'Normal', 'pixels-core-creative-tools-for-elementor' ) )
		);

		$this->add_control(
			'submenu_link_color',
			array(
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$submenu_link => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'submenu_link_background',
			array(
				'label'     => esc_html__( 'Background', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$submenu_link => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'submenu_link_hover',
			array( 'label' => esc_html__( 'Hover', 'pixels-core-creative-tools-for-elementor' ) )
		);

		$this->add_control(
			'submenu_link_hover_color',
			array(
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$submenu_link . ':hover, ' . $submenu_link . ':focus' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'submenu_link_hover_background',
			array(
				'label'     => esc_html__( 'Background', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$submenu_link . ':hover, ' . $submenu_link . ':focus' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'submenu_link_active',
			array( 'label' => esc_html__( 'Active', 'pixels-core-creative-tools-for-elementor' ) )
		);

		$this->add_control(
			'submenu_link_active_color',
			array(
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-menu__list .sub-menu .current-menu-item > a, {{WRAPPER}} .pixeccte-menu__list .sub-menu .current-menu-ancestor > a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'submenu_link_active_background',
			array(
				'label'     => esc_html__( 'Background', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-menu__list .sub-menu .current-menu-item > a, {{WRAPPER}} .pixeccte-menu__list .sub-menu .current-menu-ancestor > a' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'submenu_item_padding',
			array(
				'label'      => esc_html__( 'Item Padding', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'separator'  => 'before',
				'selectors'  => array(
					$submenu_link => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_dropdown_indicator',
			array(
				'label' => esc_html__( 'Dropdown Indicator', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'show_dropdown_indicator',
			array(
				'label'                => esc_html__( 'Show Indicator', 'pixels-core-creative-tools-for-elementor' ),
				'type'                 => Controls_Manager::SWITCHER,
				'label_on'             => esc_html__( 'Show', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'            => esc_html__( 'Hide', 'pixels-core-creative-tools-for-elementor' ),
				'return_value'         => 'yes',
				'default'              => 'yes',
				'selectors_dictionary' => array(
					'yes' => 'inline-block',
					''    => 'none',
				),
				'selectors'            => array(
					$dropdown_arrow => 'display: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'dropdown_indicator_color',
			array(
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					$dropdown_arrow => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'show_dropdown_indicator' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'dropdown_indicator_size',
			array(
				'label'      => esc_html__( 'Size', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 4,
						'max' => 20,
					),
				),
				'selectors'  => array(
					$dropdown_arrow => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'show_dropdown_indicator' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_toggle',
			array(
				'label' => esc_html__( 'Hamburger Toggle', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'toggle_tabs' );

		$this->start_controls_tab(
			'toggle_normal',
			array( 'label' => esc_html__( 'Normal', 'pixels-core-creative-tools-for-elementor' ) )
		);

		$this->add_control(
			'toggle_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-menu__toggle' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'toggle_background',
			array(
				'label'     => esc_html__( 'Background', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-menu__toggle' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'toggle_hover',
			array( 'label' => esc_html__( 'Hover', 'pixels-core-creative-tools-for-elementor' ) )
		);

		$this->add_control(
			'toggle_hover_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-menu__toggle:hover, {{WRAPPER}} .pixeccte-menu__toggle:focus' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'toggle_hover_background',
			array(
				'label'     => esc_html__( 'Background', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-menu__toggle:hover, {{WRAPPER}} .pixeccte-menu__toggle:focus' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'toggle_border',
				'selector'  => '{{WRAPPER}} .pixeccte-menu__toggle',
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'toggle_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-menu__toggle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'toggle_shadow',
				'selector' => '{{WRAPPER}} .pixeccte-menu__toggle',
			)
		);

		$this->add_responsive_control(
			'toggle_size',
			array(
				'label'      => esc_html__( 'Button Size', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 24,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-menu__toggle' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'toggle_bar_width',
			array(
				'label'      => esc_html__( 'Bar Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 12,
						'max' => 40,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-menu__toggle-icon' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'toggle_bar_thickness',
			array(
				'label'      => esc_html__( 'Bar Thickness', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 8,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-menu__toggle-bar' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'toggle_bar_gap',
			array(
				'label'      => esc_html__( 'Bar Gap', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 2,
						'max' => 12,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-menu__toggle-icon' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_mobile_panel',
			array(
				'label' => esc_html__( 'Hamburger Panel', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'aside_width',
			array(
				'label'      => esc_html__( 'Aside Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw' ),
				'range'      => array(
					'px' => array(
						'min' => 200,
						'max' => 600,
					),
					'%'  => array(
						'min' => 20,
						'max' => 100,
					),
					'vw' => array(
						'min' => 20,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 320,
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-menu--panel-aside.is-hamburger .pixeccte-menu__wrap' => 'width: {{SIZE}}{{UNIT}}; max-width: 100vw;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'mobile_panel_background',
				'selector' => $hamburger_panel,
			)
		);

		$this->add_responsive_control(
			'mobile_panel_padding',
			array(
				'label'      => esc_html__( 'Padding', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					$hamburger_panel => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'mobile_panel_border',
				'selector' => $hamburger_panel,
			)
		);

		$this->add_responsive_control(
			'mobile_panel_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					$hamburger_panel => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'mobile_panel_shadow',
				'selector' => $hamburger_panel,
			)
		);

		$this->add_responsive_control(
			'mobile_panel_offset',
			array(
				'label'      => esc_html__( 'Dropdown Top Offset', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'selectors'  => array(
					$dropdown_panel => 'top: calc(100% + {{SIZE}}{{UNIT}});',
				),
			)
		);

		$this->add_control(
			'overlay_heading',
			array(
				'label'     => esc_html__( 'Aside Overlay', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'overlay_color',
			array(
				'label'     => esc_html__( 'Overlay Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0, 0, 0, 0.5)',
				'selectors' => array(
					$overlay => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get wp nav menus.
	 *
	 * @return array<string, string>
	 */
	private function get_wp_nav_menus(): array {
		$options = array(
			'' => esc_html__( '— Select —', 'pixels-core-creative-tools-for-elementor' ),
		);

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

	/**
	 * Is editor preview.
	 *
	 * @return bool Result.
	 */
	private function is_editor_preview(): bool {
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return false;
		}

		$editor = \Elementor\Plugin::$instance->editor ?? null;

		return $editor && $editor->is_edit_mode();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['nav_menu'] ) ) {
			if ( $this->is_editor_preview() ) {
				echo '<div class="pixeccte-menu pixeccte-menu--placeholder">';
				echo esc_html__( 'Select a WordPress menu in the widget settings.', 'pixels-core-creative-tools-for-elementor' );
				echo '</div>';
			}

			return;
		}

		$breakpoint   = max( 320, min( 1920, (int) ( $settings['breakpoint'] ?? 1024 ) ) );
		$layout       = 'vertical' === ( $settings['layout'] ?? 'horizontal' ) ? 'vertical' : 'horizontal';
		$panel_type   = 'dropdown' === ( $settings['hamburger_panel_type'] ?? 'aside' ) ? 'dropdown' : 'aside';
		$aside_side   = 'right' === ( $settings['aside_position'] ?? 'left' ) ? 'right' : 'left';
		$menu_id      = 'pixeccte-menu-' . $this->get_id();
		$toggle_id    = $menu_id . '-toggle';
		$overlay_id   = $menu_id . '-overlay';
		$close_id     = $menu_id . '-close';
		$toggle_label = ! empty( $settings['mobile_toggle_label'] )
			? $settings['mobile_toggle_label']
			: esc_html__( 'Menu', 'pixels-core-creative-tools-for-elementor' );
		$nav_classes  = array(
			'pixeccte-menu',
			'pixeccte-menu--layout-' . $layout,
			'pixeccte-menu--panel-' . $panel_type,
		);

		if ( 'aside' === $panel_type ) {
			$nav_classes[] = 'pixeccte-menu--aside-' . $aside_side;
		}

		$this->add_render_attribute( 'nav', 'class', $nav_classes );
		$this->add_render_attribute( 'nav', 'style', '--pixeccte-menu-breakpoint: ' . $breakpoint . 'px;' );
		$this->add_render_attribute( 'nav', 'data-breakpoint', (string) $breakpoint );
		$this->add_render_attribute( 'nav', 'data-desktop-hamburger', 'yes' === ( $settings['desktop_hamburger'] ?? '' ) ? '1' : '0' );
		$this->add_render_attribute( 'nav', 'data-mobile-hamburger', 'yes' === ( $settings['mobile_hamburger'] ?? 'yes' ) ? '1' : '0' );

		$this->add_render_attribute( 'toggle', 'class', 'pixeccte-menu__toggle' );
		$this->add_render_attribute( 'toggle', 'type', 'button' );
		$this->add_render_attribute( 'toggle', 'id', $toggle_id );
		$this->add_render_attribute( 'toggle', 'aria-controls', $menu_id );
		$this->add_render_attribute( 'toggle', 'aria-expanded', 'false' );
		$this->add_render_attribute( 'toggle', 'aria-label', $toggle_label );

		$this->add_render_attribute( 'wrap', 'class', 'pixeccte-menu__wrap' );
		$this->add_render_attribute( 'wrap', 'id', $menu_id );
		$this->add_render_attribute( 'overlay', 'class', 'pixeccte-menu__overlay' );
		$this->add_render_attribute( 'overlay', 'id', $overlay_id );
		$this->add_render_attribute( 'overlay', 'aria-hidden', 'true' );

		$this->add_render_attribute( 'close', 'class', 'pixeccte-menu__close' );
		$this->add_render_attribute( 'close', 'type', 'button' );
		$this->add_render_attribute( 'close', 'id', $close_id );
		$this->add_render_attribute( 'close', 'aria-label', esc_html__( 'Close menu', 'pixels-core-creative-tools-for-elementor' ) );

		?>
		<nav <?php $this->print_render_attribute_string( 'nav' ); ?>>
			<button <?php $this->print_render_attribute_string( 'toggle' ); ?>>
				<span class="pixeccte-menu__toggle-icon" aria-hidden="true">
					<span class="pixeccte-menu__toggle-bar"></span>
					<span class="pixeccte-menu__toggle-bar"></span>
					<span class="pixeccte-menu__toggle-bar"></span>
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
					array(
						'menu'        => $settings['nav_menu'],
						'container'   => false,
						'menu_class'  => 'pixeccte-menu__list',
						'fallback_cb' => false,
						'depth'       => 0,
						'items_wrap'  => '<ul id="%1$s" class="%2$s">%3$s</ul>',
						'menu_id'     => $menu_id . '-list',
					)
				);
				?>
			</div>
		</nav>
		<?php
	}
}
