<?php
namespace PixelsElementorAddons\Widgets;

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

class Accordion_Widget extends Widget_Nested_Base {

	use Widget_Assets_Trait;

	public function get_name(): string {
		return 'pixels-accordion';
	}

	public function get_title(): string {
		return esc_html__( 'Accordion', 'pixels-elementor-addons' );
	}

	public function get_icon(): string {
		return 'pixels-icon pixels-icon-accordion';
	}

	public function get_categories(): array {
		return [ 'pixels-core' ];
	}

	public function get_keywords(): array {
		return [ 'accordion', 'toggle', 'collapse', 'faq', 'pixels', 'nested' ];
	}

	protected function get_assets_slug(): string {
		return 'accordion';
	}

	public function show_in_panel(): bool {
		return \PixelsElementorAddons\Plugin::is_nested_elements_active();
	}

	protected function item_content_container( int $index ): array {
		return [
			'elType'   => 'container',
			'settings' => [
				'_title'        => sprintf(
					/* translators: %d: Item index. */
					esc_html__( 'Item #%d', 'pixels-elementor-addons' ),
					$index
				),
				'content_width' => 'full',
			],
		];
	}

	protected function get_default_children_elements(): array {
		return [
			$this->item_content_container( 1 ),
			$this->item_content_container( 2 ),
			$this->item_content_container( 3 ),
		];
	}

	protected function get_default_repeater_title_setting_key(): string {
		return 'item_title';
	}

	protected function get_default_children_title(): string {
		/* translators: %d: Item index. */
		return esc_html__( 'Item #%d', 'pixels-elementor-addons' );
	}

	protected function get_default_children_placeholder_selector(): string {
		return '.pixels-core-accordion';
	}

	protected function get_default_children_container_placeholder_selector(): string {
		return '.pixels-core-accordion__item';
	}

	protected function get_initial_config(): array {
		return array_merge( parent::get_initial_config(), [
			'support_improved_repeaters' => true,
			'target_container'           => [ '.pixels-core-accordion' ],
			'node'                       => 'details',
			'is_interlaced'              => true,
		] );
	}

	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_style_title_controls();
		$this->register_style_icon_controls();
		$this->register_style_content_controls();
		$this->register_style_item_controls();
	}

	private function register_content_controls(): void {
		$this->start_controls_section(
			'section_items',
			[
				'label' => esc_html__( 'Accordion', 'pixels-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'item_title',
			[
				'label'       => esc_html__( 'Title', 'pixels-elementor-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Accordion Title', 'pixels-elementor-addons' ),
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'element_id',
			[
				'label'       => esc_html__( 'CSS ID', 'pixels-elementor-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'dynamic'     => [
					'active' => true,
				],
				'title'       => esc_html__( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'pixels-elementor-addons' ),
				'description' => esc_html__( 'Please make sure the ID is unique and not used elsewhere on the page.', 'pixels-elementor-addons' ),
			]
		);

		$this->add_control(
			'items',
			[
				'label'       => esc_html__( 'Items', 'pixels-elementor-addons' ),
				'type'        => Control_Nested_Repeater::CONTROL_TYPE,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'item_title' => esc_html__( 'Item #1', 'pixels-elementor-addons' ),
					],
					[
						'item_title' => esc_html__( 'Item #2', 'pixels-elementor-addons' ),
					],
					[
						'item_title' => esc_html__( 'Item #3', 'pixels-elementor-addons' ),
					],
				],
				'title_field' => '{{{ item_title }}}',
				'button_text' => esc_html__( 'Add Item', 'pixels-elementor-addons' ),
			]
		);

		$this->add_control(
			'default_state',
			[
				'label'   => esc_html__( 'Default State', 'pixels-elementor-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'expanded',
				'options' => [
					'expanded'  => esc_html__( 'First Item Expanded', 'pixels-elementor-addons' ),
					'collapsed' => esc_html__( 'All Collapsed', 'pixels-elementor-addons' ),
				],
			]
		);

		$this->add_control(
			'allow_multiple',
			[
				'label'        => esc_html__( 'Multiple Items Open', 'pixels-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'pixels-elementor-addons' ),
				'label_off'    => esc_html__( 'No', 'pixels-elementor-addons' ),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label'   => esc_html__( 'Title HTML Tag', 'pixels-elementor-addons' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'h1'  => 'H1',
					'h2'  => 'H2',
					'h3'  => 'H3',
					'h4'  => 'H4',
					'h5'  => 'H5',
					'h6'  => 'H6',
					'div' => 'div',
				],
				'default' => 'div',
			]
		);

		$this->add_control(
			'icon_heading',
			[
				'label'     => esc_html__( 'Toggle Icon', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'icon_collapsed',
			[
				'label'   => esc_html__( 'Collapsed', 'pixels-elementor-addons' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'fas fa-plus',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'icon_expanded',
			[
				'label'   => esc_html__( 'Expanded', 'pixels-elementor-addons' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'fas fa-minus',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'icon_position',
			[
				'label'        => esc_html__( 'Icon Position', 'pixels-elementor-addons' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'end',
				'options'      => [
					'start' => esc_html__( 'Before Title', 'pixels-elementor-addons' ),
					'end'   => esc_html__( 'After Title', 'pixels-elementor-addons' ),
				],
				'prefix_class' => 'pixels-core-accordion--icon-',
			]
		);

		$this->end_controls_section();
	}

	private function register_style_title_controls(): void {
		$this->start_controls_section(
			'section_style_title',
			[
				'label' => esc_html__( 'Title', 'pixels-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .pixels-core-accordion__label',
			]
		);

		$this->start_controls_tabs( 'title_style_tabs' );

		$this->start_controls_tab(
			'title_style_normal',
			[
				'label' => esc_html__( 'Normal', 'pixels-elementor-addons' ),
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Color', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-accordion__label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_bg',
			[
				'label'     => esc_html__( 'Background', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-accordion__title' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'title_style_active',
			[
				'label' => esc_html__( 'Active', 'pixels-elementor-addons' ),
			]
		);

		$this->add_control(
			'title_active_color',
			[
				'label'     => esc_html__( 'Color', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-accordion__item[open] .pixels-core-accordion__label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_active_bg',
			[
				'label'     => esc_html__( 'Background', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-accordion__item[open] .pixels-core-accordion__title' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'title_padding',
			[
				'label'      => esc_html__( 'Padding', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-accordion__title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->end_controls_section();
	}

	private function register_style_icon_controls(): void {
		$this->start_controls_section(
			'section_style_icon',
			[
				'label' => esc_html__( 'Icon', 'pixels-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'      => esc_html__( 'Size', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min' => 8,
						'max' => 80,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-accordion__icon'     => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-accordion__icon svg' => 'width: 1em; height: 1em;',
					'{{WRAPPER}} .pixels-core-accordion__icons'    => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_box_size',
			[
				'label'      => esc_html__( 'Box Size', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min' => 16,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-accordion__icons' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'icon_style_tabs' );

		$this->start_controls_tab(
			'icon_style_normal',
			[
				'label' => esc_html__( 'Normal', 'pixels-elementor-addons' ),
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => esc_html__( 'Color', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-accordion__icon'       => 'color: {{VALUE}};',
					'{{WRAPPER}} .pixels-core-accordion__icon svg'   => 'fill: {{VALUE}};',
					'{{WRAPPER}} .pixels-core-accordion__icon svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_bg',
			[
				'label'     => esc_html__( 'Background', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-accordion__icons' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'accordion_icon_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-accordion__icons' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_style_active',
			[
				'label' => esc_html__( 'Active', 'pixels-elementor-addons' ),
			]
		);

		$this->add_control(
			'accordion_icon_active_color',
			[
				'label'     => esc_html__( 'Color', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-accordion__item[open] .pixels-core-accordion__icon'       => 'color: {{VALUE}};',
					'{{WRAPPER}} .pixels-core-accordion__item[open] .pixels-core-accordion__icon svg'   => 'fill: {{VALUE}};',
					'{{WRAPPER}} .pixels-core-accordion__item[open] .pixels-core-accordion__icon svg *' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .pixels-core-accordion__item.is-expanded .pixels-core-accordion__icon'       => 'color: {{VALUE}};',
					'{{WRAPPER}} .pixels-core-accordion__item.is-expanded .pixels-core-accordion__icon svg'   => 'fill: {{VALUE}};',
					'{{WRAPPER}} .pixels-core-accordion__item.is-expanded .pixels-core-accordion__icon svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'accordion_icon_active_bg',
			[
				'label'     => esc_html__( 'Background', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-accordion__item[open] .pixels-core-accordion__icons'       => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .pixels-core-accordion__item.is-expanded .pixels-core-accordion__icons' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'accordion_icon_active_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-accordion__item[open] .pixels-core-accordion__icons'       => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .pixels-core-accordion__item.is-expanded .pixels-core-accordion__icons' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'icon_border',
				'selector'  => '{{WRAPPER}} .pixels-core-accordion__icons',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'icon_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-accordion__icons' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[
				'label'      => esc_html__( 'Padding', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-accordion__icons' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; box-sizing: content-box;',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_style_content_controls(): void {
		$this->start_controls_section(
			'section_style_content',
			[
				'label' => esc_html__( 'Content', 'pixels-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'content_background',
				'selector' => '{{WRAPPER}} .pixels-core-accordion__item > .e-con',
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => esc_html__( 'Padding', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-accordion__item > .e-con' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_style_item_controls(): void {
		$this->start_controls_section(
			'section_style_item',
			[
				'label' => esc_html__( 'Item', 'pixels-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'item_gap',
			[
				'label'      => esc_html__( 'Gap Between Items', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-accordion' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'item_border',
				'selector' => '{{WRAPPER}} .pixels-core-accordion__item',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_shadow',
				'selector' => '{{WRAPPER}} .pixels-core-accordion__item',
			]
		);

		$this->add_control(
			'item_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-accordion__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->end_controls_section();
	}

	private function render_toggle_icons(): string {
		$settings = $this->get_settings_for_display();

		ob_start();
		?>
		<span class="pixels-core-accordion__icon pixels-core-accordion__icon--collapsed">
			<?php Icons_Manager::render_icon( $settings['icon_collapsed'], [ 'aria-hidden' => 'true' ] ); ?>
		</span>
		<span class="pixels-core-accordion__icon pixels-core-accordion__icon--expanded">
			<?php
			$expanded_icon = ! empty( $settings['icon_expanded']['value'] )
				? $settings['icon_expanded']
				: $settings['icon_collapsed'];
			Icons_Manager::render_icon( $expanded_icon, [ 'aria-hidden' => 'true' ] );
			?>
		</span>
		<?php
		return ob_get_clean();
	}

	public function print_child( $index, $item_id = null ): void {
		$children = $this->get_children();

		if ( empty( $children[ $index ] ) ) {
			return;
		}

		$add_attribute_to_container = function ( $should_render, $container ) use ( $item_id ) {
			if ( $item_id ) {
				$container->add_render_attribute(
					'_wrapper',
					[
						'role'              => 'region',
						'aria-labelledby'   => $item_id,
						'class'             => 'pixels-core-accordion__panel',
					]
				);
			}

			return $should_render;
		};

		add_filter( 'elementor/frontend/container/should_render', $add_attribute_to_container, 10, 3 );
		$children[ $index ]->print_element();
		remove_filter( 'elementor/frontend/container/should_render', $add_attribute_to_container );
	}

	protected function render(): void {
		$settings       = $this->get_settings_for_display();
		$items          = $settings['items'] ?? [];
		$id_int         = substr( $this->get_id_int(), 0, 3 );
		$title_tag      = Utils::validate_html_tag( $settings['title_tag'] ?? 'div' );
		$default_state  = $settings['default_state'] ?? 'expanded';
		$allow_multiple = ( $settings['allow_multiple'] ?? '' ) === 'yes';
		$icons_html     = $this->render_toggle_icons();

		if ( empty( $items ) ) {
			return;
		}

		$this->add_render_attribute(
			'accordion',
			[
				'class'                   => 'pixels-core-accordion',
				'data-allow-multiple'     => $allow_multiple ? 'true' : 'false',
				'aria-label'              => esc_attr__( 'Accordion. Open items with Enter or Space, close with Escape and navigate using Arrow Keys.', 'pixels-elementor-addons' ),
			]
		);
		?>
		<div <?php $this->print_render_attribute_string( 'accordion' ); ?>>
			<?php foreach ( $items as $index => $item ) : ?>
				<?php
				$item_count   = $index + 1;
				$item_id      = ! empty( $item['element_id'] )
					? $item['element_id']
					: 'pixels-accordion-item-' . $id_int . $index;
				$title_id     = 'pixels-accordion-title-' . $id_int . $index;
				$is_open      = 'expanded' === $default_state && 0 === $index;
				$summary_key  = 'summary-' . $index;
				$details_key  = 'details-' . $index;

				$this->add_render_attribute(
					$details_key,
					[
						'id'    => $item_id,
						'class' => 'pixels-core-accordion__item',
					]
				);

				if ( $is_open ) {
					$this->add_render_attribute( $details_key, 'open', 'open' );
				}

				$this->add_render_attribute(
					$summary_key,
					[
						'id'               => $title_id,
						'class'            => 'pixels-core-accordion__title',
						'role'             => 'button',
						'data-item-index'  => (string) $item_count,
						'aria-expanded'    => $is_open ? 'true' : 'false',
						'aria-controls'    => $item_id,
						'tabindex'         => 0 === $index ? '0' : '-1',
					]
				);
				?>
				<details <?php $this->print_render_attribute_string( $details_key ); ?>>
					<summary <?php $this->print_render_attribute_string( $summary_key ); ?>>
						<span class="pixels-core-accordion__label-wrap">
							<?php
							printf(
								'<%1$s class="pixels-core-accordion__label">%2$s</%1$s>',
								$title_tag, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								esc_html( $item['item_title'] )
							);
							?>
						</span>
						<span class="pixels-core-accordion__icons"><?php echo $icons_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</summary>
					<?php $this->print_child( $index, $title_id ); ?>
				</details>
			<?php endforeach; ?>
		</div>
		<?php
	}

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

	protected function content_template(): void {
		?>
		<# const elementUid = view.getIDInt().toString().substr( 0, 3 ); #>
		<div class="pixels-core-accordion" data-allow-multiple="{{ settings.allow_multiple === 'yes' ? 'true' : 'false' }}">
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

	private function content_template_single_item( bool $is_repeater_insert = false ): void {
		?>
		<#
		const itemCount = itemIndex + 1,
			isRepeaterInsert = <?php echo $is_repeater_insert ? 'true' : 'false'; ?>,
			itemUid = isRepeaterInsert
				? view.getIDInt().toString().substr( 0, 3 ) + itemIndex
				: elementUid + itemIndex,
			itemId = item.element_id ? item.element_id : 'pixels-accordion-item-' + itemUid,
			titleId = 'pixels-accordion-title-' + itemUid,
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
			class: [ 'pixels-core-accordion__item' ],
		}, null, true );

		if ( isOpen ) {
			view.addRenderAttribute( detailsKey, 'open', 'open' );
		}

		view.addRenderAttribute( summaryKey, {
			id: titleId,
			class: [ 'pixels-core-accordion__title' ],
			role: 'button',
			'data-item-index': itemCount,
			'aria-expanded': isOpen ? 'true' : 'false',
			'aria-controls': itemId,
			tabindex: 0 === itemIndex && ! isRepeaterInsert ? '0' : '-1',
		}, null, true );

		view.addRenderAttribute( labelKey, {
			class: [ 'pixels-core-accordion__label' ],
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
				<span class="pixels-core-accordion__label-wrap">
					<{{ titleTag }} {{{ view.getRenderAttributeString( labelKey ) }}}>{{{ item.item_title }}}</{{ titleTag }}>
				</span>
				<span class="pixels-core-accordion__icons">
					<span class="pixels-core-accordion__icon pixels-core-accordion__icon--collapsed">
						<# if ( collapsedIcon && collapsedIcon.rendered ) { #>{{{ collapsedIcon.value }}}<# } #>
					</span>
					<span class="pixels-core-accordion__icon pixels-core-accordion__icon--expanded">
						<# if ( expandedIcon && expandedIcon.rendered ) { #>{{{ expandedIcon.value }}}<# } #>
					</span>
				</span>
			</summary>
		</details>
		<?php
	}
}
