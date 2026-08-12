<?php
/**
 * Accordion widget.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Modules\NestedElements\Base\Widget_Nested_Base;
use Elementor\Modules\NestedElements\Controls\Control_Nested_Repeater;
use Elementor\Repeater;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Accordion widget.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */
class Accordion_Widget extends Widget_Nested_Base {

	use Widget_Assets_Trait;

	/**
	 * Get name.
	 *
	 * @return string Result.
	 */
	public function get_name(): string {
		return 'pixeccte-accordion';
	}

	/**
	 * Get title.
	 *
	 * @return string Result.
	 */
	public function get_title(): string {
		return esc_html__( 'Accordion', 'pixels-core-creative-tools-for-elementor' );
	}

	/**
	 * Get icon.
	 *
	 * @return string Result.
	 */
	public function get_icon(): string {
		return 'pixeccte-icon pixeccte-icon-accordion';
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
		return array( 'accordion', 'toggle', 'collapse', 'faq', 'pixeccte', 'nested' );
	}

	/**
	 * Get assets slug.
	 *
	 * @return string Result.
	 */
	protected function get_assets_slug(): string {
		return 'accordion';
	}

	/**
	 * Show in panel.
	 *
	 * @return bool Result.
	 */
	public function show_in_panel(): bool {
		return \PixelsCoreCreativeToolsForElementor\Plugin::is_nested_elements_active();
	}

	/**
	 * Item content container.
	 *
	 * @param int $index Index.
	 * @return array Result.
	 */
	protected function item_content_container( int $index ): array {
		return array(
			'elType'   => 'container',
			'settings' => array(
				'_title'        => sprintf(
					/* translators: %d: Item index. */
					esc_html__( 'Item #%d', 'pixels-core-creative-tools-for-elementor' ),
					$index
				),
				'content_width' => 'full',
			),
		);
	}

	/**
	 * Get default children elements.
	 *
	 * @return array Result.
	 */
	protected function get_default_children_elements(): array {
		return array(
			$this->item_content_container( 1 ),
			$this->item_content_container( 2 ),
			$this->item_content_container( 3 ),
		);
	}

	/**
	 * Get default repeater title setting key.
	 *
	 * @return string Result.
	 */
	protected function get_default_repeater_title_setting_key(): string {
		return 'item_title';
	}

	/**
	 * Get default children title.
	 *
	 * @return string Result.
	 */
	protected function get_default_children_title(): string {
		/* translators: %d: Item index. */
		return esc_html__( 'Item #%d', 'pixels-core-creative-tools-for-elementor' );
	}

	/**
	 * Get default children placeholder selector.
	 *
	 * @return string Result.
	 */
	protected function get_default_children_placeholder_selector(): string {
		return '.pixeccte-accordion';
	}

	/**
	 * Get default children container placeholder selector.
	 *
	 * @return string Result.
	 */
	protected function get_default_children_container_placeholder_selector(): string {
		return '.pixeccte-accordion__item';
	}

	/**
	 * Get initial config.
	 *
	 * @return array Result.
	 */
	protected function get_initial_config(): array {
		return array_merge(
			parent::get_initial_config(),
			array(
				'support_improved_repeaters' => true,
				'target_container'           => array( '.pixeccte-accordion' ),
				'node'                       => 'details',
				'is_interlaced'              => true,
			)
		);
	}

	/**
	 * Register controls.
	 */
	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_style_title_controls();
		$this->register_style_icon_controls();
		$this->register_style_content_controls();
		$this->register_style_item_controls();
	}

	/**
	 * Register content controls.
	 */
	private function register_content_controls(): void {
		$this->start_controls_section(
			'section_items',
			array(
				'label' => esc_html__( 'Accordion', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'item_title',
			array(
				'label'       => esc_html__( 'Title', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Accordion Title', 'pixels-core-creative-tools-for-elementor' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'element_id',
			array(
				'label'       => esc_html__( 'CSS ID', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'dynamic'     => array(
					'active' => true,
				),
				'title'       => esc_html__( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'pixels-core-creative-tools-for-elementor' ),
				'description' => esc_html__( 'Please make sure the ID is unique and not used elsewhere on the page.', 'pixels-core-creative-tools-for-elementor' ),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'Items', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => Control_Nested_Repeater::CONTROL_TYPE,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'item_title' => esc_html__( 'Item #1', 'pixels-core-creative-tools-for-elementor' ),
					),
					array(
						'item_title' => esc_html__( 'Item #2', 'pixels-core-creative-tools-for-elementor' ),
					),
					array(
						'item_title' => esc_html__( 'Item #3', 'pixels-core-creative-tools-for-elementor' ),
					),
				),
				'title_field' => '{{{ item_title }}}',
				'button_text' => esc_html__( 'Add Item', 'pixels-core-creative-tools-for-elementor' ),
			)
		);

		$this->add_control(
			'default_state',
			array(
				'label'   => esc_html__( 'Default State', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'expanded',
				'options' => array(
					'expanded'  => esc_html__( 'First Item Expanded', 'pixels-core-creative-tools-for-elementor' ),
					'collapsed' => esc_html__( 'All Collapsed', 'pixels-core-creative-tools-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'allow_multiple',
			array(
				'label'        => esc_html__( 'Multiple Items Open', 'pixels-core-creative-tools-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'pixels-core-creative-tools-for-elementor' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'   => esc_html__( 'Title HTML Tag', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'h1'  => 'H1',
					'h2'  => 'H2',
					'h3'  => 'H3',
					'h4'  => 'H4',
					'h5'  => 'H5',
					'h6'  => 'H6',
					'div' => 'div',
				),
				'default' => 'div',
			)
		);

		$this->add_control(
			'icon_heading',
			array(
				'label'     => esc_html__( 'Toggle Icon', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'icon_collapsed',
			array(
				'label'   => esc_html__( 'Collapsed', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-plus',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'icon_expanded',
			array(
				'label'   => esc_html__( 'Expanded', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fas fa-minus',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'icon_position',
			array(
				'label'        => esc_html__( 'Icon Position', 'pixels-core-creative-tools-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'end',
				'options'      => array(
					'start' => esc_html__( 'Before Title', 'pixels-core-creative-tools-for-elementor' ),
					'end'   => esc_html__( 'After Title', 'pixels-core-creative-tools-for-elementor' ),
				),
				'prefix_class' => 'pixeccte-accordion--icon-',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register style title controls.
	 */
	private function register_style_title_controls(): void {
		$this->start_controls_section(
			'section_style_title',
			array(
				'label' => esc_html__( 'Title', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .pixeccte-accordion__label',
			)
		);

		$this->start_controls_tabs( 'title_style_tabs' );

		$this->start_controls_tab(
			'title_style_normal',
			array(
				'label' => esc_html__( 'Normal', 'pixels-core-creative-tools-for-elementor' ),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-accordion__label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_bg',
			array(
				'label'     => esc_html__( 'Background', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-accordion__title' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'title_style_active',
			array(
				'label' => esc_html__( 'Active', 'pixels-core-creative-tools-for-elementor' ),
			)
		);

		$this->add_control(
			'title_active_color',
			array(
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-accordion__item[open] .pixeccte-accordion__label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_active_bg',
			array(
				'label'     => esc_html__( 'Background', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-accordion__item[open] .pixeccte-accordion__title' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'title_padding',
			array(
				'label'      => esc_html__( 'Padding', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-accordion__title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'before',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register style icon controls.
	 */
	private function register_style_icon_controls(): void {
		$this->start_controls_section(
			'section_style_icon',
			array(
				'label' => esc_html__( 'Icon', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'Size', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 8,
						'max' => 80,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-accordion__icon'     => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixeccte-accordion__icon svg' => 'width: 1em; height: 1em;',
					'{{WRAPPER}} .pixeccte-accordion__icons'    => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_box_size',
			array(
				'label'      => esc_html__( 'Box Size', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 16,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-accordion__icons' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'icon_style_tabs' );

		$this->start_controls_tab(
			'icon_style_normal',
			array(
				'label' => esc_html__( 'Normal', 'pixels-core-creative-tools-for-elementor' ),
			)
		);

		$this->add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-accordion__icon'       => 'color: {{VALUE}};',
					'{{WRAPPER}} .pixeccte-accordion__icon svg'   => 'fill: {{VALUE}};',
					'{{WRAPPER}} .pixeccte-accordion__icon svg *' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'icon_bg',
			array(
				'label'     => esc_html__( 'Background', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-accordion__icons' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'accordion_icon_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-accordion__icons' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_style_active',
			array(
				'label' => esc_html__( 'Active', 'pixels-core-creative-tools-for-elementor' ),
			)
		);

		$this->add_control(
			'accordion_icon_active_color',
			array(
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-accordion__item[open] .pixeccte-accordion__icon'       => 'color: {{VALUE}};',
					'{{WRAPPER}} .pixeccte-accordion__item[open] .pixeccte-accordion__icon svg'   => 'fill: {{VALUE}};',
					'{{WRAPPER}} .pixeccte-accordion__item[open] .pixeccte-accordion__icon svg *' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .pixeccte-accordion__item.is-expanded .pixeccte-accordion__icon'       => 'color: {{VALUE}};',
					'{{WRAPPER}} .pixeccte-accordion__item.is-expanded .pixeccte-accordion__icon svg'   => 'fill: {{VALUE}};',
					'{{WRAPPER}} .pixeccte-accordion__item.is-expanded .pixeccte-accordion__icon svg *' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'accordion_icon_active_bg',
			array(
				'label'     => esc_html__( 'Background', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-accordion__item[open] .pixeccte-accordion__icons'       => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .pixeccte-accordion__item.is-expanded .pixeccte-accordion__icons' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'accordion_icon_active_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-accordion__item[open] .pixeccte-accordion__icons'       => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .pixeccte-accordion__item.is-expanded .pixeccte-accordion__icons' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'icon_border',
				'selector'  => '{{WRAPPER}} .pixeccte-accordion__icons',
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'icon_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-accordion__icons' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'icon_padding',
			array(
				'label'      => esc_html__( 'Padding', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-accordion__icons' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; box-sizing: content-box;',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register style content controls.
	 */
	private function register_style_content_controls(): void {
		$this->start_controls_section(
			'section_style_content',
			array(
				'label' => esc_html__( 'Content', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'content_background',
				'selector' => '{{WRAPPER}} .pixeccte-accordion__item > .e-con',
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label'      => esc_html__( 'Padding', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-accordion__item > .e-con' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register style item controls.
	 */
	private function register_style_item_controls(): void {
		$this->start_controls_section(
			'section_style_item',
			array(
				'label' => esc_html__( 'Item', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'item_gap',
			array(
				'label'      => esc_html__( 'Gap Between Items', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-accordion' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'item_border',
				'selector' => '{{WRAPPER}} .pixeccte-accordion__item',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'item_shadow',
				'selector' => '{{WRAPPER}} .pixeccte-accordion__item',
			)
		);

		$this->add_control(
			'item_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-accordion__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Print toggle icons.
	 */
	private function print_toggle_icons(): void {
		$settings = $this->get_settings_for_display();
		?>
		<span class="pixeccte-accordion__icon pixeccte-accordion__icon--collapsed">
			<?php Icons_Manager::render_icon( $settings['icon_collapsed'], array( 'aria-hidden' => 'true' ) ); ?>
		</span>
		<span class="pixeccte-accordion__icon pixeccte-accordion__icon--expanded">
			<?php
			$expanded_icon = ! empty( $settings['icon_expanded']['value'] )
				? $settings['icon_expanded']
				: $settings['icon_collapsed'];
			Icons_Manager::render_icon( $expanded_icon, array( 'aria-hidden' => 'true' ) );
			?>
		</span>
		<?php
	}

	/**
	 * Print child.
	 *
	 * @param mixed $index Index.
	 * @param mixed $item_id Item id.
	 */
	public function print_child( $index, $item_id = null ): void {
		$children = $this->get_children();

		if ( empty( $children[ $index ] ) ) {
			return;
		}

		$add_attribute_to_container = function ( $should_render, $container ) use ( $item_id ) {
			if ( $item_id ) {
				$container->add_render_attribute(
					'_wrapper',
					array(
						'role'            => 'region',
						'aria-labelledby' => $item_id,
						'class'           => 'pixeccte-accordion__panel',
					)
				);
			}

			return $should_render;
		};

		add_filter( 'elementor/frontend/container/should_render', $add_attribute_to_container, 10, 3 );
		$children[ $index ]->print_element();
		remove_filter( 'elementor/frontend/container/should_render', $add_attribute_to_container );
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$settings       = $this->get_settings_for_display();
		$items          = $settings['items'] ?? array();
		$id_int         = substr( $this->get_id_int(), 0, 3 );
		$title_tag      = Utils::validate_html_tag( $settings['title_tag'] ?? 'div' );
		$default_state  = $settings['default_state'] ?? 'expanded';
		$allow_multiple = ( $settings['allow_multiple'] ?? '' ) === 'yes';

		if ( empty( $items ) ) {
			return;
		}

		$this->add_render_attribute(
			'accordion',
			array(
				'class'               => 'pixeccte-accordion',
				'data-allow-multiple' => $allow_multiple ? 'true' : 'false',
				'aria-label'          => esc_attr__( 'Accordion. Open items with Enter or Space, close with Escape and navigate using Arrow Keys.', 'pixels-core-creative-tools-for-elementor' ),
			)
		);
		?>
		<div <?php $this->print_render_attribute_string( 'accordion' ); ?>>
			<?php foreach ( $items as $index => $item ) : ?>
				<?php
				$item_count  = $index + 1;
				$item_id     = ! empty( $item['element_id'] )
					? $item['element_id']
					: 'pixeccte-accordion-item-' . $id_int . $index;
				$title_id    = 'pixeccte-accordion-title-' . $id_int . $index;
				$is_open     = 'expanded' === $default_state && 0 === $index;
				$summary_key = 'summary-' . $index;
				$details_key = 'details-' . $index;

				$this->add_render_attribute(
					$details_key,
					array(
						'id'    => $item_id,
						'class' => 'pixeccte-accordion__item',
					)
				);

				if ( $is_open ) {
					$this->add_render_attribute( $details_key, 'open', 'open' );
				}

				$this->add_render_attribute(
					$summary_key,
					array(
						'id'              => $title_id,
						'class'           => 'pixeccte-accordion__title',
						'role'            => 'button',
						'data-item-index' => (string) $item_count,
						'aria-expanded'   => $is_open ? 'true' : 'false',
						'aria-controls'   => $item_id,
						'tabindex'        => 0 === $index ? '0' : '-1',
					)
				);
				?>
				<details <?php $this->print_render_attribute_string( $details_key ); ?>>
					<summary <?php $this->print_render_attribute_string( $summary_key ); ?>>
						<span class="pixeccte-accordion__label-wrap">
							<?php
							printf(
								'<%1$s class="pixeccte-accordion__label">%2$s</%1$s>',
								tag_escape( $title_tag ),
								esc_html( $item['item_title'] )
							);
							?>
						</span>
						<span class="pixeccte-accordion__icons"><?php $this->print_toggle_icons(); ?></span>
					</summary>
					<?php $this->print_child( $index, $title_id ); ?>
				</details>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Content template single repeater item.
	 */
	protected function content_template_single_repeater_item(): void {
		?>
		<#
		const itemIndex = view.collection.length,
			item = data,
			elementUid = view.getIDInt().toString().substr( 0, 3 ) + itemIndex,
			defaultState = settings.default_state || 'expanded',
			isOpen = 'expanded' === defaultState && 0 === itemIndex;
		#>
		<?php $this->content_template_single_item( true ); ?>
		<?php
	}

	/**
	 * Content template.
	 */
	protected function content_template(): void {
		?>
		<# const elementUid = view.getIDInt().toString().substr( 0, 3 ); #>
		<div class="pixeccte-accordion" data-allow-multiple="{{ settings.allow_multiple === 'yes' ? 'true' : 'false' }}">
			<# if ( settings.items && settings.items.length ) { #>
				<# _.each( settings.items, function( item, index ) {
					const itemIndex = index;
				#>
					<?php $this->content_template_single_item( false ); ?>
				<# } ); #>
			<# } #>
		</div>
		<?php
	}

	/**
	 * Content template single item.
	 *
	 * @param bool $is_repeater_insert Is repeater insert.
	 */
	private function content_template_single_item( bool $is_repeater_insert = false ): void {
		?>
		<#
		const itemCount = itemIndex + 1,
			isRepeaterInsert = <?php echo $is_repeater_insert ? 'true' : 'false'; ?>,
			itemUid = isRepeaterInsert
				? view.getIDInt().toString().substr( 0, 3 ) + itemIndex
				: elementUid + itemIndex,
			itemId = item.element_id ? item.element_id : 'pixeccte-accordion-item-' + itemUid,
			titleId = 'pixeccte-accordion-title-' + itemUid,
			defaultState = settings.default_state || 'expanded',
			isOpen = ! isRepeaterInsert && 'expanded' === defaultState && 0 === itemIndex,
			titleTag = elementor.helpers.validateHTMLTag( settings.title_tag || 'div' ),
			collapsedIcon = elementor.helpers.renderIcon( view, settings.icon_collapsed, { 'aria-hidden': true }, 'i', 'object' ),
			expandedIcon = settings.icon_expanded && settings.icon_expanded.value
				? elementor.helpers.renderIcon( view, settings.icon_expanded, { 'aria-hidden': true }, 'i', 'object' )
				: collapsedIcon,
			detailsKey = isRepeaterInsert ? 'details-new' : 'details-' + itemIndex,
			summaryKey = isRepeaterInsert ? 'summary-new' : 'summary-' + itemIndex,
			labelKey = isRepeaterInsert ? 'label-new' : 'label-' + itemIndex;

		view.addRenderAttribute( detailsKey, {
			id: itemId,
			class: [ 'pixeccte-accordion__item' ],
		}, null, true );

		if ( isOpen ) {
			view.addRenderAttribute( detailsKey, 'open', 'open' );
		}

		view.addRenderAttribute( summaryKey, {
			id: titleId,
			class: [ 'pixeccte-accordion__title' ],
			role: 'button',
			'data-item-index': itemCount,
			'aria-expanded': isOpen ? 'true' : 'false',
			'aria-controls': itemId,
			tabindex: 0 === itemIndex && ! isRepeaterInsert ? '0' : '-1',
		}, null, true );

		view.addRenderAttribute( labelKey, {
			class: [ 'pixeccte-accordion__label' ],
			'data-binding-type': 'repeater-item',
			'data-binding-repeater-name': 'items',
			'data-binding-setting': [ 'item_title', 'element_id' ],
			'data-binding-index': itemCount,
			'data-binding-config': JSON.stringify({
				element_id: {
					attr: 'id',
					selector: 'details',
					editType: 'attribute',
				},
				item_title: {
					editType: 'text',
				},
			}),
		}, null, true );
		#>
		<details {{{ view.getRenderAttributeString( detailsKey ) }}}>
			<summary {{{ view.getRenderAttributeString( summaryKey ) }}}>
				<span class="pixeccte-accordion__label-wrap">
					<{{ titleTag }} {{{ view.getRenderAttributeString( labelKey ) }}}>{{{ item.item_title }}}</{{ titleTag }}>
				</span>
				<span class="pixeccte-accordion__icons">
					<span class="pixeccte-accordion__icon pixeccte-accordion__icon--collapsed">
						<# if ( collapsedIcon && collapsedIcon.rendered ) { #>{{{ collapsedIcon.value }}}<# } #>
					</span>
					<span class="pixeccte-accordion__icon pixeccte-accordion__icon--expanded">
						<# if ( expandedIcon && expandedIcon.rendered ) { #>{{{ expandedIcon.value }}}<# } #>
					</span>
				</span>
			</summary>
		</details>
		<?php
	}
}
