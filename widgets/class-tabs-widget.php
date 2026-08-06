<?php
namespace PixelsCore\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Modules\NestedElements\Base\Widget_Nested_Base;
use Elementor\Modules\NestedElements\Controls\Control_Nested_Repeater;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Tabs_Widget extends Widget_Nested_Base {

	use Widget_Assets_Trait;

	/** @var array<int, array<string, mixed>> */
	private array $tab_item_settings = [];

	public function get_name(): string {
		return 'pixels-tabs';
	}

	public function get_title(): string {
		return esc_html__( 'Tabs', 'pixels-core-creative-tools-for-elementor' );
	}

	public function get_icon(): string {
		return 'pixels-icon pixels-icon-tabs';
	}

	public function get_categories(): array {
		return [ 'pixels-core' ];
	}

	public function get_keywords(): array {
		return [ 'tabs', 'toggle', 'accordion', 'pixels', 'nested' ];
	}

	protected function get_assets_slug(): string {
		return 'tabs';
	}

	public function show_in_panel(): bool {
		return \PixelsCore\Plugin::is_nested_elements_active();
	}

	protected function tab_content_container( int $index ): array {
		return [
			'elType'   => 'container',
			'settings' => [
				'_title'        => sprintf(
					/* translators: %d: Tab index. */
					esc_html__( 'Tab #%d', 'pixels-core-creative-tools-for-elementor' ),
					$index
				),
				'content_width' => 'full',
			],
		];
	}

	protected function get_default_children_elements(): array {
		return [
			$this->tab_content_container( 1 ),
			$this->tab_content_container( 2 ),
			$this->tab_content_container( 3 ),
		];
	}

	protected function get_default_repeater_title_setting_key(): string {
		return 'tab_title';
	}

	protected function get_default_children_title(): string {
		/* translators: %d: Tab index. */
		return esc_html__( 'Tab #%d', 'pixels-core-creative-tools-for-elementor' );
	}

	protected function get_default_children_placeholder_selector(): string {
		return '.pixels-core-tabs__panels';
	}

	protected function get_initial_config(): array {
		return array_merge( parent::get_initial_config(), [
			'support_improved_repeaters' => true,
			'target_container'           => [ '.pixels-core-tabs__nav' ],
			'node'                       => 'button',
		] );
	}

	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_style_tab_controls();
		$this->register_style_content_controls();
	}

	private function register_content_controls(): void {
		$this->start_controls_section(
			'section_tabs',
			[
				'label' => esc_html__( 'Tabs', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'tab_title',
			[
				'label'       => esc_html__( 'Title', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Tab Title', 'pixels-core-creative-tools-for-elementor' ),
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'tab_icon',
			[
				'label' => esc_html__( 'Icon', 'pixels-core-creative-tools-for-elementor' ),
				'type'  => Controls_Manager::ICONS,
			]
		);

		$repeater->add_control(
			'element_id',
			[
				'label'       => esc_html__( 'CSS ID', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'dynamic'     => [
					'active' => true,
				],
				'title'       => esc_html__( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'pixels-core-creative-tools-for-elementor' ),
				'description' => esc_html__( 'Please make sure the ID is unique and not used elsewhere on the page.', 'pixels-core-creative-tools-for-elementor' ),
			]
		);

		$this->add_control(
			'tabs',
			[
				'label'       => esc_html__( 'Tab Items', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => Control_Nested_Repeater::CONTROL_TYPE,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'tab_title' => esc_html__( 'Tab #1', 'pixels-core-creative-tools-for-elementor' ),
					],
					[
						'tab_title' => esc_html__( 'Tab #2', 'pixels-core-creative-tools-for-elementor' ),
					],
					[
						'tab_title' => esc_html__( 'Tab #3', 'pixels-core-creative-tools-for-elementor' ),
					],
				],
				'title_field' => '{{{ tab_title }}}',
				'button_text' => esc_html__( 'Add Tab', 'pixels-core-creative-tools-for-elementor' ),
			]
		);

		$this->add_control(
			'default_active',
			[
				'label'   => esc_html__( 'Default Active Tab', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 20,
				'step'    => 1,
				'default' => 1,
			]
		);

		$this->add_control(
			'layout',
			[
				'label'        => esc_html__( 'Layout', 'pixels-core-creative-tools-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'horizontal',
				'options'      => [
					'horizontal' => esc_html__( 'Horizontal', 'pixels-core-creative-tools-for-elementor' ),
					'vertical'   => esc_html__( 'Vertical', 'pixels-core-creative-tools-for-elementor' ),
				],
				'prefix_class' => 'pixels-core-tabs--',
			]
		);

		$this->add_control(
			'vertical_position',
			[
				'label'        => esc_html__( 'Tabs Position', 'pixels-core-creative-tools-for-elementor' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'start' => [
						'title' => esc_html__( 'Left', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-h-align-left',
					],
					'end'   => [
						'title' => esc_html__( 'Right', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'      => 'start',
				'toggle'       => false,
				'prefix_class' => 'pixels-core-tabs--vertical-pos-',
				'condition'    => [
					'layout' => 'vertical',
				],
			]
		);

		$this->add_responsive_control(
			'vertical_nav_width',
			[
				'label'      => esc_html__( 'Tabs Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'range'      => [
					'px' => [
						'min' => 80,
						'max' => 500,
					],
					'%'  => [
						'min' => 10,
						'max' => 60,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 220,
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-tabs__nav' => 'width: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}}; flex: 0 0 {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'layout' => 'vertical',
				],
			]
		);

		$this->add_responsive_control(
			'vertical_gap',
			[
				'label'      => esc_html__( 'Gap', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 80,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-tabs' => 'gap: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'layout' => 'vertical',
				],
			]
		);

		$this->add_responsive_control(
			'vertical_tabs_align',
			[
				'label'     => esc_html__( 'Tabs Alignment', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => esc_html__( 'Start', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center'     => [
						'title' => esc_html__( 'Center', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'flex-end'   => [
						'title' => esc_html__( 'End', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-v-align-bottom',
					],
					'stretch'    => [
						'title' => esc_html__( 'Stretch', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-v-align-stretch',
					],
				],
				'default'   => 'flex-start',
				'selectors' => [
					'{{WRAPPER}} .pixels-core-tabs__nav' => 'justify-content: {{VALUE}};',
				],
				'condition' => [
					'layout' => 'vertical',
				],
			]
		);

		$this->add_responsive_control(
			'vertical_content_align',
			[
				'label'     => esc_html__( 'Content Vertical Align', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => esc_html__( 'Top', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center'     => [
						'title' => esc_html__( 'Middle', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'flex-end'   => [
						'title' => esc_html__( 'Bottom', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-v-align-bottom',
					],
					'stretch'    => [
						'title' => esc_html__( 'Stretch', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-v-align-stretch',
					],
				],
				'default'   => 'stretch',
				'selectors' => [
					'{{WRAPPER}} .pixels-core-tabs' => 'align-items: {{VALUE}};',
					'{{WRAPPER}} .pixels-core-tabs__panels' => 'align-self: {{VALUE}};',
				],
				'condition' => [
					'layout' => 'vertical',
				],
			]
		);

		$this->add_responsive_control(
			'tabs_align',
			[
				'label'     => esc_html__( 'Tabs Alignment', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => esc_html__( 'Start', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'     => [
						'title' => esc_html__( 'Center', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					],
					'flex-end'   => [
						'title' => esc_html__( 'End', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					],
					'stretch'    => [
						'title' => esc_html__( 'Stretch', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'default'   => 'flex-start',
				'selectors' => [
					'{{WRAPPER}} .pixels-core-tabs__nav' => 'justify-content: {{VALUE}};',
				],
				'condition' => [
					'layout' => 'horizontal',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_style_tab_controls(): void {
		$this->start_controls_section(
			'section_style_tabs',
			[
				'label' => esc_html__( 'Tab Titles', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'tab_title_typography',
				'selector' => '{{WRAPPER}} .pixels-core-tabs__title',
			]
		);

		$this->start_controls_tabs( 'tabs_title_states' );

		$this->start_controls_tab(
			'tab_title_normal',
			[
				'label' => esc_html__( 'Normal', 'pixels-core-creative-tools-for-elementor' ),
			]
		);

		$this->add_control(
			'tab_title_color',
			[
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-tabs__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-tabs__title .pixels-core-tabs__icon, {{WRAPPER}} .pixels-core-tabs__title .pixels-core-tabs__icon svg' => 'color: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_title_bg',
			[
				'label'     => esc_html__( 'Background', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-tabs__title' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_title_active',
			[
				'label' => esc_html__( 'Active', 'pixels-core-creative-tools-for-elementor' ),
			]
		);

		$this->add_control(
			'tab_title_active_color',
			[
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-tabs__title.is-active' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_icon_active_color',
			[
				'label'     => esc_html__( 'Icon Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-tabs__title.is-active .pixels-core-tabs__icon, {{WRAPPER}} .pixels-core-tabs__title.is-active .pixels-core-tabs__icon svg' => 'color: {{VALUE}}; fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_title_active_bg',
			[
				'label'     => esc_html__( 'Background', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-tabs__title.is-active' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_title_active_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-tabs__title.is-active' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'tab_title_padding',
			[
				'label'      => esc_html__( 'Padding', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-tabs__title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'tab_title_border',
				'selector' => '{{WRAPPER}} .pixels-core-tabs__title',
			]
		);

		$this->add_responsive_control(
			'tab_title_gap',
			[
				'label'      => esc_html__( 'Gap', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-tabs__nav' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_style_content_controls(): void {
		$this->start_controls_section(
			'section_style_content',
			[
				'label' => esc_html__( 'Tab Content', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'content_color',
			[
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-tabs__panels > .e-con' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'content_typography',
				'selector' => '{{WRAPPER}} .pixels-core-tabs__panels > .e-con',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'content_background',
				'selector' => '{{WRAPPER}} .pixels-core-tabs__panels > .e-con',
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => esc_html__( 'Padding', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-tabs__panels > .e-con' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'content_border',
				'selector' => '{{WRAPPER}} .pixels-core-tabs__panels > .e-con',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'content_shadow',
				'selector' => '{{WRAPPER}} .pixels-core-tabs__panels > .e-con',
			]
		);

		$this->end_controls_section();
	}

	protected function render_tab_titles_html( array $item_settings ): void {
		$item        = $item_settings['item'];
		$tab_count   = $item_settings['tab_count'];
		$title_id    = $item_settings['tab_title_id'];
		$content_id  = $item_settings['container_id'];
		$default_tab = max( 1, (int) ( $item_settings['settings']['default_active'] ?? 1 ) );
		$is_active   = $tab_count === $default_tab;
		$tab_id      = ! empty( $item['element_id'] ) ? $item['element_id'] : $title_id;
		$title_key   = 'tab-title-' . $item_settings['index'];

		$this->add_render_attribute(
			$title_key,
			[
				'id'            => $tab_id,
				'class'         => [
					'pixels-core-tabs__title',
					$is_active ? 'is-active' : '',
				],
				'role'          => 'tab',
				'aria-selected' => $is_active ? 'true' : 'false',
				'aria-controls' => $content_id,
				'tabindex'      => $is_active ? '0' : '-1',
				'data-tab'      => (string) $tab_count,
			]
		);
		?>
		<button type="button" <?php $this->print_render_attribute_string( $title_key ); ?>>
			<?php if ( ! empty( $item['tab_icon']['value'] ) ) : ?>
				<span class="pixels-core-tabs__icon">
					<?php Icons_Manager::render_icon( $item['tab_icon'], [ 'aria-hidden' => 'true' ] ); ?>
				</span>
			<?php endif; ?>
			<span class="pixels-core-tabs__label"><?php echo esc_html( $item['tab_title'] ); ?></span>
		</button>
		<?php
	}

	protected function render_tab_panels_html( array $settings ): void {
		foreach ( $settings['tabs'] as $index => $item ) {
			$item_settings = $this->tab_item_settings[ $index ];
			$this->print_child( $item_settings['index'], $item_settings );
		}
	}

	/**
	 * @param int                  $index
	 * @param array<string, mixed> $item_settings
	 */
	public function print_child( $index, $item_settings = [] ): void {
		$children  = $this->get_children();
		$child_ids = [];

		foreach ( $children as $child ) {
			$child_ids[] = $child->get_id();
		}

		$default_tab = max( 1, (int) ( $item_settings['settings']['default_active'] ?? 1 ) );
		$is_active   = (int) $item_settings['tab_count'] === $default_tab;

		$add_attribute_to_container = function ( $should_render, $container ) use ( $item_settings, $child_ids, $is_active ) {
			if ( in_array( $container->get_id(), $child_ids, true ) ) {
				$attributes = [
					'id'                => $item_settings['container_id'],
					'class'             => array_filter( [
						'pixels-core-tabs__panel',
						$is_active ? 'is-active' : '',
					] ),
					'role'              => 'tabpanel',
					'aria-labelledby'   => $item_settings['tab_id'],
					'data-tab'          => (string) $item_settings['tab_count'],
					'data-tab-index'    => (string) $item_settings['tab_count'],
				];

				$container->add_render_attribute( '_wrapper', $attributes );

				if ( ! $is_active ) {
					$container->add_render_attribute( '_wrapper', 'hidden', 'hidden' );
				}
			}

			return $should_render;
		};

		add_filter( 'elementor/frontend/container/should_render', $add_attribute_to_container, 10, 3 );

		if ( isset( $children[ $index ] ) ) {
			$children[ $index ]->print_element();
		}

		remove_filter( 'elementor/frontend/container/should_render', $add_attribute_to_container );
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$tabs     = $settings['tabs'] ?? [];

		if ( empty( $tabs ) ) {
			return;
		}

		$widget_number  = $this->get_id_int();
		$default_active = max( 1, (int) ( $settings['default_active'] ?? 1 ) );

		$this->add_render_attribute(
			'wrapper',
			[
				'class'            => 'pixels-core-tabs',
				'data-default-tab' => (string) $default_active,
				'data-widget-number' => (string) $widget_number,
			]
		);
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div class="pixels-core-tabs__nav" role="tablist">
				<?php
				foreach ( $tabs as $index => $item ) {
					$tab_count   = $index + 1;
					$tab_title_id = 'pixels-tab-title-' . $widget_number . $tab_count;
					$tab_id      = ! empty( $item['element_id'] ) ? $item['element_id'] : $tab_title_id;

					$item_settings = [
						'index'        => $index,
						'tab_count'    => $tab_count,
						'tab_id'       => $tab_id,
						'tab_title_id' => $tab_title_id,
						'container_id' => 'pixels-tab-content-' . $widget_number . $tab_count,
						'item'         => $item,
						'settings'     => $settings,
					];

					$this->tab_item_settings[] = $item_settings;
					$this->render_tab_titles_html( $item_settings );
				}
				?>
			</div>
			<div class="pixels-core-tabs__panels">
				<?php $this->render_tab_panels_html( $settings ); ?>
			</div>
		</div>
		<?php
	}

	protected function content_template_single_repeater_item(): void {
		?>
		<#
		const tabIndex = view.collection.length,
			item = data,
			elementUid = view.getIDInt().toString();
		#>
		<?php $this->content_template_single_item( true ); ?>
		<?php
	}

	protected function content_template(): void {
		?>
		<# const elementUid = view.getIDInt().toString(); #>
		<div class="pixels-core-tabs" data-widget-number="{{ elementUid }}" data-default-tab="{{ settings.default_active || 1 }}">
			<# if ( settings.tabs && settings.tabs.length ) { #>
				<div class="pixels-core-tabs__nav" role="tablist">
					<# _.each( settings.tabs, function( item, index ) {
						const tabIndex = index;
					#>
						<?php $this->content_template_single_item( false ); ?>
					<# } ); #>
				</div>
				<div class="pixels-core-tabs__panels"></div>
			<# } #>
		</div>
		<?php
	}

	private function content_template_single_item( bool $is_repeater_insert = false ): void {
		?>
		<#
		const tabCount = tabIndex + 1,
			isRepeaterInsert = <?php echo $is_repeater_insert ? 'true' : 'false'; ?>,
			defaultActive = ! isRepeaterInsert && settings.default_active
				? parseInt( settings.default_active, 10 )
				: 1,
			isActive = ! isRepeaterInsert && tabCount === defaultActive,
			tabTitleId = 'pixels-tab-title-' + elementUid + tabCount,
			tabId = item.element_id ? item.element_id : tabTitleId,
			contentId = 'pixels-tab-content-' + elementUid + tabCount,
			iconHTML = elementor.helpers.renderIcon( view, item.tab_icon, { 'aria-hidden': true }, 'i' , 'object' ),
			titleKey = isRepeaterInsert ? 'tab-title' : 'tab-title-' + tabIndex,
			labelKey = isRepeaterInsert ? 'tab-label' : 'tab-label-' + tabIndex;

		view.addRenderAttribute( titleKey, {
			id: tabId,
			class: [ 'pixels-core-tabs__title', isActive ? 'is-active' : '' ],
			role: 'tab',
			'aria-selected': isActive ? 'true' : 'false',
			'aria-controls': contentId,
			tabindex: isActive ? '0' : '-1',
			'data-tab': tabCount,
		}, null, true );

		view.addRenderAttribute( labelKey, {
			class: [ 'pixels-core-tabs__label' ],
			'data-binding-type': 'repeater-item',
			'data-binding-repeater-name': 'tabs',
			'data-binding-setting': [ 'tab_title', 'element_id' ],
			'data-binding-index': tabCount,
			'data-binding-config': JSON.stringify({
				element_id: {
					attr: 'id',
					selector: 'button',
					editType: 'attribute',
				},
				tab_title: {
					editType: 'text',
				},
			}),
		}, null, true );
		#>
		<button {{{ view.getRenderAttributeString( titleKey ) }}}>
			<# if ( iconHTML && iconHTML.rendered ) { #>
				<span class="pixels-core-tabs__icon">{{{ iconHTML.value }}}</span>
			<# } #>
			<span {{{ view.getRenderAttributeString( labelKey ) }}}>{{{ item.tab_title }}}</span>
		</button>
		<?php
	}
}
