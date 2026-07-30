<?php
namespace PixelsElementorAddons\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Orbit_Circle_Widget extends Widget_Base {

	use Widget_Assets_Trait;

	public function get_name(): string {
		return 'pixels-orbit-circle';
	}

	public function get_title(): string {
		return esc_html__( 'Orbit Circle', 'pixels-elementor-addons' );
	}

	public function get_icon(): string {
		return 'pixels-icon pixels-icon-orbit-circle';
	}

	public function get_categories(): array {
		return [ 'pixels-core' ];
	}

	public function get_keywords(): array {
		return [ 'orbit', 'circle', 'wheel', 'rotation', 'integration', 'logo', 'ring', 'pixels' ];
	}

	protected function get_assets_slug(): string {
		return 'orbit_circle';
	}

	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_settings_controls();
		$this->register_orbit_style_controls();
		$this->register_item_style_controls();
	}

	private function register_content_controls(): void {
		$this->start_controls_section(
			'section_items',
			[
				'label' => esc_html__( 'Orbit Items', 'pixels-elementor-addons' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'item_label',
			[
				'label'       => esc_html__( 'Label', 'pixels-elementor-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Item', 'pixels-elementor-addons' ),
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'item_type',
			[
				'label'   => esc_html__( 'Type', 'pixels-elementor-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'image',
				'options' => [
					'image' => esc_html__( 'Image', 'pixels-elementor-addons' ),
					'icon'  => esc_html__( 'Icon', 'pixels-elementor-addons' ),
					'text'  => esc_html__( 'Text', 'pixels-elementor-addons' ),
				],
			]
		);

		$repeater->add_control(
			'item_image',
			[
				'label'     => esc_html__( 'Image', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'item_type' => 'image',
				],
				'dynamic'   => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'item_icon',
			[
				'label'            => esc_html__( 'Icon', 'pixels-elementor-addons' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'item_icon_legacy',
				'default'          => [
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				],
				'condition'        => [
					'item_type' => 'icon',
				],
			]
		);

		$repeater->add_control(
			'item_text',
			[
				'label'       => esc_html__( 'Text', 'pixels-elementor-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Orbit', 'pixels-elementor-addons' ),
				'label_block' => true,
				'condition'   => [
					'item_type' => 'text',
				],
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'item_link',
			[
				'label'       => esc_html__( 'Link', 'pixels-elementor-addons' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://',
				'separator'   => 'before',
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'items',
			[
				'label'       => esc_html__( 'Items', 'pixels-elementor-addons' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => $this->get_default_items(),
				'title_field' => '<# if ( "text" === item_type ) { #>{{{ item_text || item_label }}}<# } else if ( "icon" === item_type ) { #>{{{ item_label }}}<# } else { #>{{{ item_label }}}<# } #>',
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'    => 'item_image',
				'default' => 'thumbnail',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return array<int, array{item_label: string, item_type: string}>
	 */
	private function get_default_items(): array {
		$items = [];

		for ( $i = 1; $i <= 9; $i++ ) {
			$items[] = [
				'item_label' => sprintf(
					/* translators: %d: Orbit item index. */
					esc_html__( 'Item #%d', 'pixels-elementor-addons' ),
					$i
				),
				'item_type'  => 'image',
			];
		}

		return $items;
	}

	private function register_settings_controls(): void {
		$this->start_controls_section(
			'section_settings',
			[
				'label' => esc_html__( 'Orbit Settings', 'pixels-elementor-addons' ),
			]
		);

		$this->add_control(
			'orbit_speed',
			[
				'label'   => esc_html__( 'Speed', 'pixels-elementor-addons' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0.3,
				'min'     => 0.01,
				'max'     => 20,
				'step'    => 0.01,
			]
		);

		$this->add_control(
			'orbit_duration',
			[
				'label'       => esc_html__( 'Duration (seconds)', 'pixels-elementor-addons' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 20,
				'min'         => 1,
				'max'         => 120,
				'step'        => 1,
				'description' => esc_html__( 'Time for one full rotation at speed 1.', 'pixels-elementor-addons' ),
			]
		);

		$this->add_control(
			'orbit_direction',
			[
				'label'   => esc_html__( 'Direction', 'pixels-elementor-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'clockwise',
				'options' => [
					'clockwise'         => esc_html__( 'Clockwise', 'pixels-elementor-addons' ),
					'counter-clockwise' => esc_html__( 'Counter Clockwise', 'pixels-elementor-addons' ),
				],
			]
		);

		$this->add_control(
			'fade_in',
			[
				'label'        => esc_html__( 'Fade In', 'pixels-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'pixels-elementor-addons' ),
				'label_off'    => esc_html__( 'No', 'pixels-elementor-addons' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'fade_in_duration',
			[
				'label'     => esc_html__( 'Fade In Duration (seconds)', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'min'       => 0.1,
				'max'       => 5,
				'step'      => 0.1,
				'condition' => [
					'fade_in' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_orbit_style_controls(): void {
		$this->start_controls_section(
			'section_orbit_style',
			[
				'label' => esc_html__( 'Orbit Ring', 'pixels-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'orbit_size',
			[
				'label'      => esc_html__( 'Size', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vw', 'rem' ],
				'range'      => [
					'px' => [
						'min' => 200,
						'max' => 1290,
					],
					'vw' => [
						'min' => 20,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 400,
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-orbit-circle__orbit' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'wrapper_offset_top',
			[
				'label'      => esc_html__( 'Offset Top', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'range'      => [
					'%' => [
						'min' => -200,
						'max' => 200,
					],
					'px' => [
						'min' => -800,
						'max' => 800,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-orbit-circle__layout' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'show_border',
			[
				'label'        => esc_html__( 'Show Border', 'pixels-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'pixels-elementor-addons' ),
				'label_off'    => esc_html__( 'No', 'pixels-elementor-addons' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'orbit_border',
				'selector'  => '{{WRAPPER}} .pixels-core-orbit-circle__orbit',
				'condition' => [
					'show_border' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'orbit_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'unit' => '%',
					'size' => 50,
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-orbit-circle__orbit' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'orbit_box_shadow',
				'selector' => '{{WRAPPER}} .pixels-core-orbit-circle__orbit',
			]
		);

		$this->add_responsive_control(
			'orbit_alignment',
			[
				'label'     => esc_html__( 'Alignment', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'pixels-elementor-addons' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'pixels-elementor-addons' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'pixels-elementor-addons' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .pixels-core-orbit-circle' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_item_style_controls(): void {
		$this->start_controls_section(
			'section_item_style',
			[
				'label' => esc_html__( 'Items', 'pixels-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'item_size',
			[
				'label'      => esc_html__( 'Size', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min' => 24,
						'max' => 160,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 64,
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-orbit-circle__basket--image' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-orbit-circle__basket--icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-orbit-circle__basket--text' => 'min-width: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_offset_top',
			[
				'label'      => esc_html__( 'Radial Offset', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min' => -80,
						'max' => 80,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => -20,
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-orbit-circle__basket' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'item_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-orbit-circle__basket' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => esc_html__( 'Padding', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', 'rem', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-orbit-circle__basket' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'item_text_heading',
			[
				'label'     => esc_html__( 'Text', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'item_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-orbit-circle__text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'items_text_typography',
				'selector' => '{{WRAPPER}} .pixels-core-orbit-circle__text',
			]
		);

		$this->add_control(
			'item_icon_heading',
			[
				'label'     => esc_html__( 'Icon', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'item_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-orbit-circle__icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pixels-core-orbit-circle__icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_icon_size',
			[
				'label'      => esc_html__( 'Icon Size', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min' => 8,
						'max' => 120,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-orbit-circle__icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixels-core-orbit-circle__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'item_border',
				'selector' => '{{WRAPPER}} .pixels-core-orbit-circle__basket',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'default'    => [
					'top'    => '999',
					'right'  => '999',
					'bottom' => '999',
					'left'   => '999',
					'unit'   => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-orbit-circle__basket' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .pixels-core-orbit-circle__basket',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @param array<string, mixed> $item Repeater item settings.
	 */
	private function is_item_empty( array $item ): bool {
		$item_type = $item['item_type'] ?? 'image';

		if ( 'icon' === $item_type ) {
			return empty( $item['item_icon']['value'] );
		}

		if ( 'text' === $item_type ) {
			return '' === (string) ( $item['item_text'] ?? '' );
		}

		$image     = $item['item_image'] ?? [];
		$image_url = $image['url'] ?? '';

		return '' === $image_url;
	}

	/**
	 * @param array<string, mixed> $item Repeater item settings.
	 */
	private function render_item_content( array $item ): void {
		$item_type = $item['item_type'] ?? 'image';

		if ( 'icon' === $item_type ) {
			$icon = $item['item_icon'] ?? [];

			if ( empty( $icon['value'] ) ) {
				return;
			}

			echo '<span class="pixels-core-orbit-circle__icon">';
			Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] );
			echo '</span>';

			return;
		}

		if ( 'text' === $item_type ) {
			$text = $item['item_text'] ?? '';

			if ( '' === $text ) {
				return;
			}

			echo '<span class="pixels-core-orbit-circle__text">' . esc_html( $text ) . '</span>';

			return;
		}

		$image     = $item['item_image'] ?? [];
		$image_url = $image['url'] ?? '';

		if ( '' === $image_url ) {
			return;
		}

		$image_html = Group_Control_Image_Size::get_attachment_image_html( $item, 'item_image', 'item_image' );

		if ( '' === $image_html ) {
			return;
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor image HTML.
		echo $image_html;
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$items    = $settings['items'] ?? [];

		if ( empty( $items ) ) {
			return;
		}

		$speed     = isset( $settings['orbit_speed'] ) ? (float) $settings['orbit_speed'] : 0.3;
		$duration  = isset( $settings['orbit_duration'] ) ? (float) $settings['orbit_duration'] : 20;
		$direction = $settings['orbit_direction'] ?? 'clockwise';

		if ( ! in_array( $direction, [ 'clockwise', 'counter-clockwise' ], true ) ) {
			$direction = 'clockwise';
		}

		$fade_in  = ( $settings['fade_in'] ?? 'yes' ) === 'yes' ? 'yes' : 'no';
		$fade_duration = isset( $settings['fade_in_duration'] ) ? (float) $settings['fade_in_duration'] : 1;

		$this->add_render_attribute( 'wrapper', 'class', 'pixels-core-orbit-circle' );
		$this->add_render_attribute( 'wrapper', 'data-orbit-speed', (string) $speed );
		$this->add_render_attribute( 'wrapper', 'data-orbit-duration', (string) $duration );
		$this->add_render_attribute( 'wrapper', 'data-orbit-direction', $direction );
		$this->add_render_attribute( 'wrapper', 'data-orbit-fade-in', $fade_in );
		$this->add_render_attribute( 'wrapper', 'data-orbit-fade-duration', (string) $fade_duration );

		$this->add_render_attribute( 'layout', 'class', 'pixels-core-orbit-circle__layout' );
		$this->add_render_attribute( 'layout', 'data-orbit-layout', '' );

		$orbit_classes = [ 'pixels-core-orbit-circle__orbit' ];

		if ( 'yes' !== ( $settings['show_border'] ?? 'yes' ) ) {
			$orbit_classes[] = 'pixels-core-orbit-circle__orbit--no-border';
		}

		$this->add_render_attribute( 'orbit', 'class', $orbit_classes );
		$this->add_render_attribute( 'orbit', 'data-orbit', '' );

		$this->add_render_attribute( 'center', 'class', 'pixels-core-orbit-circle__center' );
		$this->add_render_attribute( 'center', 'data-orbit-center', '' );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div <?php $this->print_render_attribute_string( 'layout' ); ?>>
				<div <?php $this->print_render_attribute_string( 'orbit' ); ?>>
					<div <?php $this->print_render_attribute_string( 'center' ); ?>>
						<?php foreach ( $items as $index => $item ) : ?>
							<?php
							if ( $this->is_item_empty( $item ) ) {
								continue;
							}

							$item_type = $item['item_type'] ?? 'image';
							$item_key  = 'item_' . $index;
							$link      = $item['item_link'] ?? [];
							$link_url  = $link['url'] ?? '';

							$this->add_render_attribute( $item_key, 'class', [
								'pixels-core-orbit-circle__pivot',
								'orbit-pivot-outer',
								'elementor-repeater-item-' . $item['_id'],
							] );

							$basket_key = 'basket_' . $index;
							$this->add_render_attribute( $basket_key, 'class', [
								'pixels-core-orbit-circle__basket',
								'orbit-basket',
								'pixels-core-orbit-circle__basket--' . $item_type,
								'elementor-repeater-item-' . $item['_id'],
							] );
							?>
							<div <?php $this->print_render_attribute_string( $item_key ); ?>>
								<div <?php $this->print_render_attribute_string( $basket_key ); ?>>
									<?php if ( ! empty( $link_url ) ) : ?>
										<?php
										$link_key = 'link_' . $index;
										$this->add_link_attributes( $link_key, $link );
										$this->add_render_attribute( $link_key, 'class', 'pixels-core-orbit-circle__link' );
										?>
										<a <?php $this->print_render_attribute_string( $link_key ); ?>>
											<?php $this->render_item_content( $item ); ?>
										</a>
									<?php else : ?>
										<?php $this->render_item_content( $item ); ?>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
