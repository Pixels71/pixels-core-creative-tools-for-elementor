<?php
/**
 * Orbit circle widget.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Widgets;

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

/**
 * Orbit circle widget.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */
class Orbit_Circle_Widget extends Widget_Base {

	use Widget_Assets_Trait;

	/**
	 * Get name.
	 *
	 * @return string Result.
	 */
	public function get_name(): string {
		return 'pixeccte-orbit-circle';
	}

	/**
	 * Get title.
	 *
	 * @return string Result.
	 */
	public function get_title(): string {
		return esc_html__( 'Orbit Circle', 'pixels-core-creative-tools-for-elementor' );
	}

	/**
	 * Get icon.
	 *
	 * @return string Result.
	 */
	public function get_icon(): string {
		return 'pixeccte-icon pixeccte-icon-orbit-circle';
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
		return array( 'orbit', 'circle', 'wheel', 'rotation', 'integration', 'logo', 'ring', 'pixeccte' );
	}

	/**
	 * Get assets slug.
	 *
	 * @return string Result.
	 */
	protected function get_assets_slug(): string {
		return 'orbit_circle';
	}

	/**
	 * Register controls.
	 */
	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_settings_controls();
		$this->register_orbit_style_controls();
		$this->register_item_style_controls();
	}

	/**
	 * Register content controls.
	 */
	private function register_content_controls(): void {
		$this->start_controls_section(
			'section_items',
			array(
				'label' => esc_html__( 'Orbit Items', 'pixels-core-creative-tools-for-elementor' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'item_label',
			array(
				'label'       => esc_html__( 'Label', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Item', 'pixels-core-creative-tools-for-elementor' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'item_type',
			array(
				'label'   => esc_html__( 'Type', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'image',
				'options' => array(
					'image' => esc_html__( 'Image', 'pixels-core-creative-tools-for-elementor' ),
					'icon'  => esc_html__( 'Icon', 'pixels-core-creative-tools-for-elementor' ),
					'text'  => esc_html__( 'Text', 'pixels-core-creative-tools-for-elementor' ),
				),
			)
		);

		$repeater->add_control(
			'item_image',
			array(
				'label'     => esc_html__( 'Image', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'item_type' => 'image',
				),
				'dynamic'   => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'item_icon',
			array(
				'label'            => esc_html__( 'Icon', 'pixels-core-creative-tools-for-elementor' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'item_icon_legacy',
				'default'          => array(
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				),
				'condition'        => array(
					'item_type' => 'icon',
				),
			)
		);

		$repeater->add_control(
			'item_text',
			array(
				'label'       => esc_html__( 'Text', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Orbit', 'pixels-core-creative-tools-for-elementor' ),
				'label_block' => true,
				'condition'   => array(
					'item_type' => 'text',
				),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'item_link',
			array(
				'label'       => esc_html__( 'Link', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://',
				'separator'   => 'before',
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'Items', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => $this->get_default_items(),
				'title_field' => '<# if ( "text" === item_type ) { #>{{{ item_text || item_label }}}<# } else if ( "icon" === item_type ) { #>{{{ item_label }}}<# } else { #>{{{ item_label }}}<# } #>',
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'    => 'item_image',
				'default' => 'thumbnail',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get default items.
	 *
	 * @return array<int, array{item_label: string, item_type: string}>
	 */
	private function get_default_items(): array {
		$items = array();

		for ( $i = 1; $i <= 9; $i++ ) {
			$items[] = array(
				'item_label' => sprintf(
					/* translators: %d: Orbit item index. */
					esc_html__( 'Item #%d', 'pixels-core-creative-tools-for-elementor' ),
					$i
				),
				'item_type'  => 'image',
			);
		}

		return $items;
	}

	/**
	 * Register settings controls.
	 */
	private function register_settings_controls(): void {
		$this->start_controls_section(
			'section_settings',
			array(
				'label' => esc_html__( 'Orbit Settings', 'pixels-core-creative-tools-for-elementor' ),
			)
		);

		$this->add_control(
			'orbit_speed',
			array(
				'label'   => esc_html__( 'Speed', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0.3,
				'min'     => 0.01,
				'max'     => 20,
				'step'    => 0.01,
			)
		);

		$this->add_control(
			'orbit_duration',
			array(
				'label'       => esc_html__( 'Duration (seconds)', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 20,
				'min'         => 1,
				'max'         => 120,
				'step'        => 1,
				'description' => esc_html__( 'Time for one full rotation at speed 1.', 'pixels-core-creative-tools-for-elementor' ),
			)
		);

		$this->add_control(
			'orbit_direction',
			array(
				'label'   => esc_html__( 'Direction', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'clockwise',
				'options' => array(
					'clockwise'         => esc_html__( 'Clockwise', 'pixels-core-creative-tools-for-elementor' ),
					'counter-clockwise' => esc_html__( 'Counter Clockwise', 'pixels-core-creative-tools-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'fade_in',
			array(
				'label'        => esc_html__( 'Fade In', 'pixels-core-creative-tools-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'pixels-core-creative-tools-for-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'fade_in_duration',
			array(
				'label'     => esc_html__( 'Fade In Duration (seconds)', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'min'       => 0.1,
				'max'       => 5,
				'step'      => 0.1,
				'condition' => array(
					'fade_in' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register orbit style controls.
	 */
	private function register_orbit_style_controls(): void {
		$this->start_controls_section(
			'section_orbit_style',
			array(
				'label' => esc_html__( 'Orbit Ring', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'orbit_size',
			array(
				'label'      => esc_html__( 'Size', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vw', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 200,
						'max' => 1290,
					),
					'vw' => array(
						'min' => 20,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 400,
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-orbit-circle__orbit' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'wrapper_offset_top',
			array(
				'label'      => esc_html__( 'Offset Top', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'range'      => array(
					'%'  => array(
						'min' => -200,
						'max' => 200,
					),
					'px' => array(
						'min' => -800,
						'max' => 800,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-orbit-circle__layout' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'show_border',
			array(
				'label'        => esc_html__( 'Show Border', 'pixels-core-creative-tools-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'pixels-core-creative-tools-for-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'orbit_border',
				'selector'  => '{{WRAPPER}} .pixeccte-orbit-circle__orbit',
				'condition' => array(
					'show_border' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'orbit_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'default'    => array(
					'unit' => '%',
					'size' => 50,
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-orbit-circle__orbit' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'orbit_box_shadow',
				'selector' => '{{WRAPPER}} .pixeccte-orbit-circle__orbit',
			)
		);

		$this->add_responsive_control(
			'orbit_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
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
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-orbit-circle' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register item style controls.
	 */
	private function register_item_style_controls(): void {
		$this->start_controls_section(
			'section_item_style',
			array(
				'label' => esc_html__( 'Items', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'item_size',
			array(
				'label'      => esc_html__( 'Size', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 24,
						'max' => 160,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 64,
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-orbit-circle__basket--image' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixeccte-orbit-circle__basket--icon' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixeccte-orbit-circle__basket--text' => 'min-width: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_offset_top',
			array(
				'label'      => esc_html__( 'Radial Offset', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => -80,
						'max' => 80,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => -20,
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-orbit-circle__basket' => 'top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'item_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-orbit-circle__basket' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_padding',
			array(
				'label'      => esc_html__( 'Padding', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', 'rem', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-orbit-circle__basket' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'item_text_heading',
			array(
				'label'     => esc_html__( 'Text', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'item_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-orbit-circle__text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'items_text_typography',
				'selector' => '{{WRAPPER}} .pixeccte-orbit-circle__text',
			)
		);

		$this->add_control(
			'item_icon_heading',
			array(
				'label'     => esc_html__( 'Icon', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'item_icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-orbit-circle__icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pixeccte-orbit-circle__icon svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'item_icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 8,
						'max' => 120,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-orbit-circle__icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pixeccte-orbit-circle__icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'item_border',
				'selector' => '{{WRAPPER}} .pixeccte-orbit-circle__basket',
			)
		);

		$this->add_responsive_control(
			'item_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em', 'rem' ),
				'default'    => array(
					'top'    => '999',
					'right'  => '999',
					'bottom' => '999',
					'left'   => '999',
					'unit'   => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-orbit-circle__basket' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .pixeccte-orbit-circle__basket',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Is item empty.
	 *
	 * @param array $item Item.
	 */
	private function is_item_empty( array $item ): bool {
		$item_type = $item['item_type'] ?? 'image';

		if ( 'icon' === $item_type ) {
			return empty( $item['item_icon']['value'] );
		}

		if ( 'text' === $item_type ) {
			return '' === (string) ( $item['item_text'] ?? '' );
		}

		$image     = $item['item_image'] ?? array();
		$image_url = $image['url'] ?? '';

		return '' === $image_url;
	}

	/**
	 * Render item content.
	 *
	 * @param array $item Item.
	 */
	private function render_item_content( array $item ): void {
		$item_type = $item['item_type'] ?? 'image';

		if ( 'icon' === $item_type ) {
			$icon = $item['item_icon'] ?? array();

			if ( empty( $icon['value'] ) ) {
				return;
			}

			echo '<span class="pixeccte-orbit-circle__icon">';
			Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
			echo '</span>';

			return;
		}

		if ( 'text' === $item_type ) {
			$text = $item['item_text'] ?? '';

			if ( '' === $text ) {
				return;
			}

			echo '<span class="pixeccte-orbit-circle__text">' . esc_html( $text ) . '</span>';

			return;
		}

		$image     = $item['item_image'] ?? array();
		$image_url = $image['url'] ?? '';

		if ( '' === $image_url ) {
			return;
		}

		$image_html = Group_Control_Image_Size::get_attachment_image_html( $item, 'item_image', 'item_image' );

		if ( '' === $image_html ) {
			return;
		}

		echo wp_kses_post( $image_html );
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$items    = $settings['items'] ?? array();

		if ( empty( $items ) ) {
			return;
		}

		$speed     = isset( $settings['orbit_speed'] ) ? (float) $settings['orbit_speed'] : 0.3;
		$duration  = isset( $settings['orbit_duration'] ) ? (float) $settings['orbit_duration'] : 20;
		$direction = $settings['orbit_direction'] ?? 'clockwise';

		if ( ! in_array( $direction, array( 'clockwise', 'counter-clockwise' ), true ) ) {
			$direction = 'clockwise';
		}

		$fade_in       = ( $settings['fade_in'] ?? 'yes' ) === 'yes' ? 'yes' : 'no';
		$fade_duration = isset( $settings['fade_in_duration'] ) ? (float) $settings['fade_in_duration'] : 1;

		$this->add_render_attribute( 'wrapper', 'class', 'pixeccte-orbit-circle' );
		$this->add_render_attribute( 'wrapper', 'data-orbit-speed', (string) $speed );
		$this->add_render_attribute( 'wrapper', 'data-orbit-duration', (string) $duration );
		$this->add_render_attribute( 'wrapper', 'data-orbit-direction', $direction );
		$this->add_render_attribute( 'wrapper', 'data-orbit-fade-in', $fade_in );
		$this->add_render_attribute( 'wrapper', 'data-orbit-fade-duration', (string) $fade_duration );

		$this->add_render_attribute( 'layout', 'class', 'pixeccte-orbit-circle__layout' );
		$this->add_render_attribute( 'layout', 'data-orbit-layout', '' );

		$orbit_classes = array( 'pixeccte-orbit-circle__orbit' );

		if ( 'yes' !== ( $settings['show_border'] ?? 'yes' ) ) {
			$orbit_classes[] = 'pixeccte-orbit-circle__orbit--no-border';
		}

		$this->add_render_attribute( 'orbit', 'class', $orbit_classes );
		$this->add_render_attribute( 'orbit', 'data-orbit', '' );

		$this->add_render_attribute( 'center', 'class', 'pixeccte-orbit-circle__center' );
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
							$link      = $item['item_link'] ?? array();
							$link_url  = $link['url'] ?? '';

							$this->add_render_attribute(
								$item_key,
								'class',
								array(
									'pixeccte-orbit-circle__pivot',
									'orbit-pivot-outer',
									'elementor-repeater-item-' . $item['_id'],
								)
							);

							$basket_key = 'basket_' . $index;
							$this->add_render_attribute(
								$basket_key,
								'class',
								array(
									'pixeccte-orbit-circle__basket',
									'orbit-basket',
									'pixeccte-orbit-circle__basket--' . $item_type,
									'elementor-repeater-item-' . $item['_id'],
								)
							);
							?>
							<div <?php $this->print_render_attribute_string( $item_key ); ?>>
								<div <?php $this->print_render_attribute_string( $basket_key ); ?>>
									<?php if ( ! empty( $link_url ) ) : ?>
										<?php
										$link_key = 'link_' . $index;
										$this->add_link_attributes( $link_key, $link );
										$this->add_render_attribute( $link_key, 'class', 'pixeccte-orbit-circle__link' );
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
