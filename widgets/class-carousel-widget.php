<?php
/**
 * Carousel widget.
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
use Elementor\Modules\NestedElements\Base\Widget_Nested_Base;
use Elementor\Modules\NestedElements\Controls\Control_Nested_Repeater;
use Elementor\Repeater;
use PixelsCoreCreativeToolsForElementor\Assets_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Carousel widget.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */
class Carousel_Widget extends Widget_Nested_Base {

	use Widget_Assets_Trait;

	/**
	 * Get name.
	 *
	 * @return string Result.
	 */
	public function get_name(): string {
		return 'pixeccte-carousel';
	}

	/**
	 * Get title.
	 *
	 * @return string Result.
	 */
	public function get_title(): string {
		return esc_html__( 'Carousel', 'pixels-core-creative-tools-for-elementor' );
	}

	/**
	 * Get icon.
	 *
	 * @return string Result.
	 */
	public function get_icon(): string {
		return 'pixeccte-icon pixeccte-icon-carousel';
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
		return array( 'carousel', 'slider', 'swiper', 'slideshow', 'pixeccte', 'nested' );
	}

	/**
	 * Get assets slug.
	 *
	 * @return string Result.
	 */
	protected function get_assets_slug(): string {
		return 'carousel';
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
	 * Get script depends.
	 *
	 * @return array Result.
	 */
	public function get_script_depends(): array {
		return array(
			'swiper',
			Assets_Manager::instance()->get_script_handle( $this->get_assets_slug() ),
		);
	}

	/**
	 * Get style depends.
	 *
	 * @return array Result.
	 */
	public function get_style_depends(): array {
		return array(
			'e-swiper',
			'swiper',
			Assets_Manager::instance()->get_style_handle( $this->get_assets_slug() ),
		);
	}

	/**
	 * Slide content container.
	 *
	 * @param int $index Index.
	 * @return array Result.
	 */
	protected function slide_content_container( int $index ): array {
		return array(
			'elType'   => 'container',
			'settings' => array(
				'_title'        => sprintf(
					/* translators: %d: Slide index. */
					esc_html__( 'Slide #%d', 'pixels-core-creative-tools-for-elementor' ),
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
			$this->slide_content_container( 1 ),
			$this->slide_content_container( 2 ),
			$this->slide_content_container( 3 ),
		);
	}

	/**
	 * Get default repeater title setting key.
	 *
	 * @return string Result.
	 */
	protected function get_default_repeater_title_setting_key(): string {
		return 'slide_title';
	}

	/**
	 * Get default children title.
	 *
	 * @return string Result.
	 */
	protected function get_default_children_title(): string {
		/* translators: %d: Slide index. */
		return esc_html__( 'Slide #%d', 'pixels-core-creative-tools-for-elementor' );
	}

	/**
	 * Get default children placeholder selector.
	 *
	 * @return string Result.
	 */
	protected function get_default_children_placeholder_selector(): string {
		return '.pixeccte-carousel__slides';
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
			)
		);
	}

	/**
	 * Register controls.
	 */
	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_style_navigation_controls();
		$this->register_style_pagination_controls();
		$this->register_style_pagination_bullets_controls();
		$this->register_style_pagination_progress_controls();
		$this->register_style_pagination_fraction_controls();
		$this->register_style_thumb_gallery_controls();
		$this->register_style_slide_controls();
	}

	/**
	 * Register content controls.
	 */
	private function register_content_controls(): void {
		$this->start_controls_section(
			'section_slides',
			array(
				'label' => esc_html__( 'Slides', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'slide_title',
			array(
				'label'       => esc_html__( 'Title', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Slide Title', 'pixels-core-creative-tools-for-elementor' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$repeater->add_control(
			'thumb_image',
			array(
				'label'       => esc_html__( 'Thumb Image', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => Controls_Manager::MEDIA,
				'label_block' => true,
				'description' => esc_html__( 'Used when Thumb Gallery is enabled.', 'pixels-core-creative-tools-for-elementor' ),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'slides',
			array(
				'label'       => esc_html__( 'Slide Items', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => Control_Nested_Repeater::CONTROL_TYPE,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'slide_title' => esc_html__( 'Slide #1', 'pixels-core-creative-tools-for-elementor' ),
					),
					array(
						'slide_title' => esc_html__( 'Slide #2', 'pixels-core-creative-tools-for-elementor' ),
					),
					array(
						'slide_title' => esc_html__( 'Slide #3', 'pixels-core-creative-tools-for-elementor' ),
					),
				),
				'title_field' => '{{{ slide_title }}}',
				'button_text' => esc_html__( 'Add Slide', 'pixels-core-creative-tools-for-elementor' ),
			)
		);

		$this->add_control(
			'carousel_name',
			array(
				'label'   => esc_html__( 'Carousel Name', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Carousel', 'pixels-core-creative-tools-for-elementor' ),
			)
		);

		$slides_to_show = array_combine( range( 1, 10 ), range( 1, 10 ) );

		$this->add_responsive_control(
			'slides_to_show',
			array(
				'label'              => esc_html__( 'Slides to Show', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => '1',
				'tablet_default'     => '1',
				'mobile_default'     => '1',
				'options'            => $slides_to_show,
				'frontend_available' => true,
				'selectors'          => array(
					'{{WRAPPER}} .pixeccte-carousel__wrapper' => '--pixeccte-carousel-slides-to-show: {{VALUE}};',
				),
				'content_classes'    => 'elementor-control-field-select-small',
			)
		);

		$this->add_responsive_control(
			'slides_to_scroll',
			array(
				'label'              => esc_html__( 'Slides to Scroll', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => '1',
				'options'            => $slides_to_show,
				'frontend_available' => true,
				'content_classes'    => 'elementor-control-field-select-small',
				'condition'          => array(
					'slides_to_show!' => '1',
				),
			)
		);

		$this->add_responsive_control(
			'slide_gap',
			array(
				'label'              => esc_html__( 'Gap', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => array( 'px' ),
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'            => array(
					'size' => 16,
					'unit' => 'px',
				),
				'frontend_available' => true,
				'selectors'          => array(
					'{{WRAPPER}} .pixeccte-carousel__wrapper' => '--pixeccte-carousel-slide-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'direction',
			array(
				'label'              => esc_html__( 'Direction', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'horizontal',
				'options'            => array(
					'horizontal' => esc_html__( 'Horizontal', 'pixels-core-creative-tools-for-elementor' ),
					'vertical'   => esc_html__( 'Vertical', 'pixels-core-creative-tools-for-elementor' ),
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'infinite',
			array(
				'label'              => esc_html__( 'Infinite Loop', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'yes',
				'return_value'       => 'yes',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'effect',
			array(
				'label'              => esc_html__( 'Effect', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'slide',
				'options'            => array(
					'slide' => esc_html__( 'Slide', 'pixels-core-creative-tools-for-elementor' ),
					'fade'  => esc_html__( 'Fade', 'pixels-core-creative-tools-for-elementor' ),
				),
				'condition'          => array(
					'slides_to_show' => '1',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'              => esc_html__( 'Animation Speed', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 500,
				'min'                => 0,
				'max'                => 10000,
				'step'               => 50,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'autoplay_heading',
			array(
				'label'     => esc_html__( 'Autoplay', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'              => esc_html__( 'Autoplay', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => '',
				'return_value'       => 'yes',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'autoplay_speed',
			array(
				'label'              => esc_html__( 'Autoplay Speed', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::NUMBER,
				'default'            => 5000,
				'min'                => 0,
				'step'               => 500,
				'condition'          => array(
					'autoplay' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'pause_on_hover',
			array(
				'label'              => esc_html__( 'Pause on Hover', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'yes',
				'return_value'       => 'yes',
				'condition'          => array(
					'autoplay' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'pause_on_interaction',
			array(
				'label'              => esc_html__( 'Pause on Interaction', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'yes',
				'return_value'       => 'yes',
				'condition'          => array(
					'autoplay' => 'yes',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'navigation',
			array(
				'label'              => esc_html__( 'Navigation', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'both',
				'options'            => array(
					'both'   => esc_html__( 'Arrows and Pagination', 'pixels-core-creative-tools-for-elementor' ),
					'arrows' => esc_html__( 'Arrows', 'pixels-core-creative-tools-for-elementor' ),
					'dots'   => esc_html__( 'Pagination', 'pixels-core-creative-tools-for-elementor' ),
					'none'   => esc_html__( 'None', 'pixels-core-creative-tools-for-elementor' ),
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'pagination_type',
			array(
				'label'              => esc_html__( 'Pagination Type', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'bullets',
				'options'            => array(
					'bullets'  => esc_html__( 'Bullets', 'pixels-core-creative-tools-for-elementor' ),
					'progress' => esc_html__( 'Progress', 'pixels-core-creative-tools-for-elementor' ),
					'fraction' => esc_html__( 'Fraction', 'pixels-core-creative-tools-for-elementor' ),
				),
				'prefix_class'       => 'pixeccte-carousel-pagination-type--',
				'frontend_available' => true,
				'condition'          => array(
					'navigation' => array( 'both', 'dots' ),
				),
			)
		);

		$this->add_control(
			'navigation_previous_icon',
			array(
				'label'     => esc_html__( 'Previous Arrow Icon', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'skin'      => 'inline',
				'condition' => array(
					'navigation' => array( 'both', 'arrows' ),
				),
			)
		);

		$this->add_control(
			'navigation_next_icon',
			array(
				'label'     => esc_html__( 'Next Arrow Icon', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::ICONS,
				'skin'      => 'inline',
				'condition' => array(
					'navigation' => array( 'both', 'arrows' ),
				),
			)
		);

		$this->add_control(
			'pagination_dynamic_bullets',
			array(
				'label'              => esc_html__( 'Dynamic Bullets', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'pixels-core-creative-tools-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => '',
				'description'        => esc_html__( 'Show a compact set of bullets that updates as you slide.', 'pixels-core-creative-tools-for-elementor' ),
				'frontend_available' => true,
				'condition'          => array(
					'navigation'      => array( 'both', 'dots' ),
					'pagination_type' => 'bullets',
				),
			)
		);

		$this->end_controls_section();

		$this->register_thumb_gallery_content_controls();
	}

	/**
	 * Register thumb gallery content controls.
	 */
	private function register_thumb_gallery_content_controls(): void {
		$this->start_controls_section(
			'section_thumb_gallery',
			array(
				'label' => esc_html__( 'Thumb Gallery', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'thumb_gallery',
			array(
				'label'              => esc_html__( 'Thumb Gallery', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'          => esc_html__( 'No', 'pixels-core-creative-tools-for-elementor' ),
				'return_value'       => 'yes',
				'default'            => '',
				'prefix_class'       => 'pixeccte-carousel--has-thumbs',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'thumb_position',
			array(
				'label'              => esc_html__( 'Position', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'bottom',
				'options'            => array(
					'bottom' => esc_html__( 'Bottom', 'pixels-core-creative-tools-for-elementor' ),
					'top'    => esc_html__( 'Top', 'pixels-core-creative-tools-for-elementor' ),
					'left'   => esc_html__( 'Left', 'pixels-core-creative-tools-for-elementor' ),
					'right'  => esc_html__( 'Right', 'pixels-core-creative-tools-for-elementor' ),
				),
				'prefix_class'       => 'pixeccte-carousel-thumbs--',
				'frontend_available' => true,
				'condition'          => array(
					'thumb_gallery' => 'yes',
				),
			)
		);

		$this->add_control(
			'thumb_images_help',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => '<div class="elementor-panel-alert elementor-panel-alert-info">' . esc_html__( 'Add a thumb image for each slide in the Slide Items list above.', 'pixels-core-creative-tools-for-elementor' ) . '</div>',
				'content_classes' => 'elementor-descriptor',
				'condition'       => array(
					'thumb_gallery' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'      => 'thumb_gallery_image',
				'default'   => 'thumbnail',
				'condition' => array(
					'thumb_gallery' => 'yes',
				),
			)
		);

		$thumb_slides_to_show = array_combine( range( 1, 10 ), range( 1, 10 ) );

		$this->add_responsive_control(
			'thumb_slides_to_show',
			array(
				'label'              => esc_html__( 'Thumbs to Show', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => '4',
				'tablet_default'     => '4',
				'mobile_default'     => '3',
				'options'            => $thumb_slides_to_show,
				'frontend_available' => true,
				'selectors'          => array(
					'{{WRAPPER}} .pixeccte-carousel__thumbs-wrapper' => '--pixeccte-carousel-thumbs-to-show: {{VALUE}};',
				),
				'condition'          => array(
					'thumb_gallery' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'thumb_gap',
			array(
				'label'              => esc_html__( 'Gap', 'pixels-core-creative-tools-for-elementor' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => array( 'px' ),
				'range'              => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'default'            => array(
					'size' => 10,
					'unit' => 'px',
				),
				'frontend_available' => true,
				'selectors'          => array(
					'{{WRAPPER}} .pixeccte-carousel__thumbs-wrapper' => '--pixeccte-carousel-thumb-gap: {{SIZE}}{{UNIT}};',
				),
				'condition'          => array(
					'thumb_gallery' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register style navigation controls.
	 */
	private function register_style_navigation_controls(): void {
		$this->start_controls_section(
			'section_style_navigation',
			array(
				'label'     => esc_html__( 'Arrows', 'pixels-core-creative-tools-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'navigation' => array( 'both', 'arrows' ),
				),
			)
		);

		$this->add_responsive_control(
			'arrows_size',
			array(
				'label'      => esc_html__( 'Size', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .elementor-swiper-button' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'prev_arrow_heading',
			array(
				'label'     => esc_html__( 'Previous Arrow', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'prev_arrow_vertical_align',
			array(
				'label'                => esc_html__( 'Vertical Position', 'pixels-core-creative-tools-for-elementor' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'center',
				'options'              => array(
					'center' => esc_html__( 'Center', 'pixels-core-creative-tools-for-elementor' ),
					'top'    => esc_html__( 'Top', 'pixels-core-creative-tools-for-elementor' ),
					'bottom' => esc_html__( 'Bottom', 'pixels-core-creative-tools-for-elementor' ),
				),
				'selectors_dictionary' => array(
					'center' => 'top: 50%; bottom: auto; transform: translateY(-50%);',
					'top'    => 'top: 0; bottom: auto; transform: none;',
					'bottom' => 'top: auto; bottom: 0; transform: none;',
				),
				'selectors'            => array(
					'{{WRAPPER}} .elementor-swiper-button-prev' => '{{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'prev_arrow_vertical_offset',
			array(
				'label'      => esc_html__( 'Vertical Offset', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em', 'vh' ),
				'range'      => array(
					'px' => array(
						'min' => -300,
						'max' => 300,
					),
					'%'  => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'default'    => array(
					'size' => 16,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .elementor-swiper-button-prev' => 'top: {{SIZE}}{{UNIT}}; bottom: auto; transform: none;',
				),
				'condition'  => array(
					'prev_arrow_vertical_align' => 'top',
				),
			)
		);

		$this->add_responsive_control(
			'prev_arrow_vertical_offset_bottom',
			array(
				'label'      => esc_html__( 'Vertical Offset', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em', 'vh' ),
				'range'      => array(
					'px' => array(
						'min' => -300,
						'max' => 300,
					),
					'%'  => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'default'    => array(
					'size' => 16,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .elementor-swiper-button-prev' => 'bottom: {{SIZE}}{{UNIT}}; top: auto; transform: none;',
				),
				'condition'  => array(
					'prev_arrow_vertical_align' => 'bottom',
				),
			)
		);

		$this->add_responsive_control(
			'prev_arrow_horizontal_offset',
			array(
				'label'      => esc_html__( 'Horizontal Offset', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -300,
						'max' => 300,
					),
					'%'  => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'default'    => array(
					'size' => 16,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .elementor-swiper-button-prev' => 'left: {{SIZE}}{{UNIT}}; right: auto;',
				),
			)
		);

		$this->add_control(
			'next_arrow_heading',
			array(
				'label'     => esc_html__( 'Next Arrow', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'next_arrow_vertical_align',
			array(
				'label'                => esc_html__( 'Vertical Position', 'pixels-core-creative-tools-for-elementor' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'center',
				'options'              => array(
					'center' => esc_html__( 'Center', 'pixels-core-creative-tools-for-elementor' ),
					'top'    => esc_html__( 'Top', 'pixels-core-creative-tools-for-elementor' ),
					'bottom' => esc_html__( 'Bottom', 'pixels-core-creative-tools-for-elementor' ),
				),
				'selectors_dictionary' => array(
					'center' => 'top: 50%; bottom: auto; transform: translateY(-50%);',
					'top'    => 'top: 0; bottom: auto; transform: none;',
					'bottom' => 'top: auto; bottom: 0; transform: none;',
				),
				'selectors'            => array(
					'{{WRAPPER}} .elementor-swiper-button-next' => '{{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'next_arrow_vertical_offset',
			array(
				'label'      => esc_html__( 'Vertical Offset', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em', 'vh' ),
				'range'      => array(
					'px' => array(
						'min' => -300,
						'max' => 300,
					),
					'%'  => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'default'    => array(
					'size' => 16,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .elementor-swiper-button-next' => 'top: {{SIZE}}{{UNIT}}; bottom: auto; transform: none;',
				),
				'condition'  => array(
					'next_arrow_vertical_align' => 'top',
				),
			)
		);

		$this->add_responsive_control(
			'next_arrow_vertical_offset_bottom',
			array(
				'label'      => esc_html__( 'Vertical Offset', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em', 'vh' ),
				'range'      => array(
					'px' => array(
						'min' => -300,
						'max' => 300,
					),
					'%'  => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'default'    => array(
					'size' => 16,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .elementor-swiper-button-next' => 'bottom: {{SIZE}}{{UNIT}}; top: auto; transform: none;',
				),
				'condition'  => array(
					'next_arrow_vertical_align' => 'bottom',
				),
			)
		);

		$this->add_responsive_control(
			'next_arrow_horizontal_offset',
			array(
				'label'      => esc_html__( 'Horizontal Offset', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -300,
						'max' => 300,
					),
					'%'  => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'default'    => array(
					'size' => 16,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .elementor-swiper-button-next' => 'right: {{SIZE}}{{UNIT}}; left: auto;',
				),
			)
		);

		$this->start_controls_tabs( 'arrows_colors' );

		$this->start_controls_tab(
			'arrows_normal',
			array(
				'label' => esc_html__( 'Normal', 'pixels-core-creative-tools-for-elementor' ),
			)
		);

		$this->add_control(
			'arrows_color',
			array(
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-swiper-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-swiper-button svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'arrows_hover',
			array(
				'label' => esc_html__( 'Hover', 'pixels-core-creative-tools-for-elementor' ),
			)
		);

		$this->add_control(
			'arrows_hover_color',
			array(
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .elementor-swiper-button:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-swiper-button:hover svg' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register style pagination controls.
	 */
	private function register_style_pagination_controls(): void {
		$this->start_controls_section(
			'section_style_pagination',
			array(
				'label'     => esc_html__( 'Pagination', 'pixels-core-creative-tools-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'navigation' => array( 'both', 'dots' ),
				),
			)
		);

		$this->add_responsive_control(
			'pagination_placement',
			array(
				'label'        => esc_html__( 'Placement', 'pixels-core-creative-tools-for-elementor' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'outside',
				'options'      => array(
					'outside' => esc_html__( 'Outside', 'pixels-core-creative-tools-for-elementor' ),
					'inside'  => esc_html__( 'Inside', 'pixels-core-creative-tools-for-elementor' ),
				),
				'prefix_class' => 'pixeccte-carousel-pagination--',
			)
		);

		$this->add_responsive_control(
			'pagination_vertical_align',
			array(
				'label'                => esc_html__( 'Vertical Position', 'pixels-core-creative-tools-for-elementor' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'bottom',
				'options'              => array(
					'top'    => esc_html__( 'Top', 'pixels-core-creative-tools-for-elementor' ),
					'center' => esc_html__( 'Center', 'pixels-core-creative-tools-for-elementor' ),
					'bottom' => esc_html__( 'Bottom', 'pixels-core-creative-tools-for-elementor' ),
				),
				'selectors_dictionary' => array(
					'top'    => 'top: 0; bottom: auto; margin-top: 0; margin-bottom: 0;',
					'center' => 'top: 50%; bottom: auto; margin-top: 0; margin-bottom: 0;',
					'bottom' => 'top: auto; bottom: 0; margin-top: 0; margin-bottom: 0;',
				),
				'selectors'            => array(
					'{{WRAPPER}} .swiper-pagination' => '{{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_vertical_offset',
			array(
				'label'      => esc_html__( 'Vertical Offset', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -300,
						'max' => 300,
					),
					'%'  => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'default'    => array(
					'size' => 16,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}}.pixeccte-carousel-pagination--inside .swiper-pagination' => 'top: {{SIZE}}{{UNIT}}; bottom: auto; transform: none;',
					'{{WRAPPER}}.pixeccte-carousel-pagination--outside .swiper-pagination' => 'bottom: 100%; top: auto; margin-bottom: {{SIZE}}{{UNIT}}; margin-top: 0; transform: none;',
				),
				'condition'  => array(
					'pagination_vertical_align' => 'top',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_vertical_offset_bottom',
			array(
				'label'      => esc_html__( 'Vertical Offset', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -300,
						'max' => 300,
					),
					'%'  => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'default'    => array(
					'size' => 16,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}}.pixeccte-carousel-pagination--inside .swiper-pagination' => 'bottom: {{SIZE}}{{UNIT}}; top: auto; transform: none;',
					'{{WRAPPER}}.pixeccte-carousel-pagination--outside .swiper-pagination' => 'top: 100%; bottom: auto; margin-top: {{SIZE}}{{UNIT}}; margin-bottom: 0; transform: none;',
				),
				'condition'  => array(
					'pagination_vertical_align' => 'bottom',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_vertical_offset_center',
			array(
				'label'      => esc_html__( 'Vertical Offset', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -300,
						'max' => 300,
					),
					'%'  => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'default'    => array(
					'size' => 0,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .swiper-pagination' => 'top: calc(50% + {{SIZE}}{{UNIT}}); bottom: auto; transform: translateY(-50%);',
				),
				'condition'  => array(
					'pagination_vertical_align'    => 'center',
					'pagination_horizontal_align!' => 'center',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_horizontal_align',
			array(
				'label'                => esc_html__( 'Horizontal Position', 'pixels-core-creative-tools-for-elementor' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'center',
				'options'              => array(
					'left'   => esc_html__( 'Left', 'pixels-core-creative-tools-for-elementor' ),
					'center' => esc_html__( 'Center', 'pixels-core-creative-tools-for-elementor' ),
					'right'  => esc_html__( 'Right', 'pixels-core-creative-tools-for-elementor' ),
				),
				'selectors_dictionary' => array(
					'left'   => 'left: 0; right: auto;',
					'center' => 'left: 50%; right: auto;',
					'right'  => 'left: auto; right: 0;',
				),
				'selectors'            => array(
					'{{WRAPPER}} .swiper-pagination' => '{{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_horizontal_offset',
			array(
				'label'      => esc_html__( 'Horizontal Offset', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -300,
						'max' => 300,
					),
					'%'  => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'default'    => array(
					'size' => 0,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .swiper-pagination' => 'left: {{SIZE}}{{UNIT}}; right: auto; transform: none;',
				),
				'condition'  => array(
					'pagination_horizontal_align' => 'left',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_horizontal_offset_right',
			array(
				'label'      => esc_html__( 'Horizontal Offset', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -300,
						'max' => 300,
					),
					'%'  => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'default'    => array(
					'size' => 0,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .swiper-pagination' => 'right: {{SIZE}}{{UNIT}}; left: auto; transform: none;',
				),
				'condition'  => array(
					'pagination_horizontal_align' => 'right',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_horizontal_offset_center',
			array(
				'label'      => esc_html__( 'Horizontal Offset', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -300,
						'max' => 300,
					),
					'%'  => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'default'    => array(
					'size' => 0,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .swiper-pagination' => 'left: calc(50% + {{SIZE}}{{UNIT}}); right: auto; transform: translateX(-50%);',
				),
				'condition'  => array(
					'pagination_horizontal_align' => 'center',
					'pagination_vertical_align!'  => 'center',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_center_offset',
			array(
				'label'      => esc_html__( 'Center Offset', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'default'    => array(
					'top'  => '0',
					'left' => '0',
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}} .swiper-pagination' => 'top: calc(50% + {{TOP}}{{UNIT}}); left: calc(50% + {{LEFT}}{{UNIT}}); right: auto; bottom: auto; transform: translate(-50%, -50%);',
				),
				'condition'  => array(
					'pagination_vertical_align'   => 'center',
					'pagination_horizontal_align' => 'center',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register style pagination bullets controls.
	 */
	private function register_style_pagination_bullets_controls(): void {
		$this->start_controls_section(
			'section_style_pagination_bullets',
			array(
				'label'     => esc_html__( 'Pagination Bullets', 'pixels-core-creative-tools-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'navigation'      => array( 'both', 'dots' ),
					'pagination_type' => 'bullets',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_gap',
			array(
				'label'      => esc_html__( 'Gap', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .swiper-pagination-bullet' => '--swiper-pagination-bullet-horizontal-gap: {{SIZE}}{{UNIT}}; --swiper-pagination-bullet-vertical-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'pagination_bullets_colors' );

		$this->start_controls_tab(
			'pagination_normal',
			array(
				'label' => esc_html__( 'Normal', 'pixels-core-creative-tools-for-elementor' ),
			)
		);

		$this->add_responsive_control(
			'pagination_normal_width',
			array(
				'label'      => esc_html__( 'Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .swiper-pagination-bullet:not(.swiper-pagination-bullet-active)' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_normal_height',
			array(
				'label'      => esc_html__( 'Height', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .swiper-pagination-bullet:not(.swiper-pagination-bullet-active)' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'pagination_color',
			array(
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .swiper-pagination-bullet:not(.swiper-pagination-bullet-active)' => 'background: {{VALUE}}; opacity: 1;',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_active',
			array(
				'label' => esc_html__( 'Active', 'pixels-core-creative-tools-for-elementor' ),
			)
		);

		$this->add_responsive_control(
			'pagination_active_width',
			array(
				'label'      => esc_html__( 'Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_active_height',
			array(
				'label'      => esc_html__( 'Height', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'pagination_active_color',
			array(
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'background: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register style pagination progress controls.
	 */
	private function register_style_pagination_progress_controls(): void {
		$this->start_controls_section(
			'section_style_pagination_progress',
			array(
				'label'     => esc_html__( 'Pagination Progress', 'pixels-core-creative-tools-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'navigation'      => array( 'both', 'dots' ),
					'pagination_type' => 'progress',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_progress_width',
			array(
				'label'      => esc_html__( 'Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 1200,
					),
					'%'  => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => '%',
					'size' => 100,
				),
				'selectors'  => array(
					'{{WRAPPER}} .swiper-pagination' => 'width: {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_progress_height',
			array(
				'label'      => esc_html__( 'Height', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 50,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 4,
				),
				'selectors'  => array(
					'{{WRAPPER}} .swiper-pagination-progressbar' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_progress_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .swiper-pagination-progressbar' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .swiper-pagination-progressbar .swiper-pagination-progressbar-fill' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'pagination_progress_track_color',
			array(
				'label'     => esc_html__( 'Track Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .swiper-pagination-progressbar' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pagination_progress_fill_color',
			array(
				'label'     => esc_html__( 'Fill Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .swiper-pagination-progressbar .swiper-pagination-progressbar-fill' => 'background: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register style pagination fraction controls.
	 */
	private function register_style_pagination_fraction_controls(): void {
		$this->start_controls_section(
			'section_style_pagination_fraction',
			array(
				'label'     => esc_html__( 'Pagination Fraction', 'pixels-core-creative-tools-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'navigation'      => array( 'both', 'dots' ),
					'pagination_type' => 'fraction',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pagination_fraction_typography',
				'selector' => '{{WRAPPER}} .swiper-pagination-fraction',
			)
		);

		$this->add_control(
			'pagination_fraction_color',
			array(
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .swiper-pagination-fraction' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pagination_fraction_current_color',
			array(
				'label'     => esc_html__( 'Current Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .swiper-pagination-current' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pagination_fraction_total_color',
			array(
				'label'     => esc_html__( 'Total Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .swiper-pagination-total' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'pagination_fraction_background',
			array(
				'label'     => esc_html__( 'Background Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .swiper-pagination-fraction' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_fraction_padding',
			array(
				'label'      => esc_html__( 'Padding', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .swiper-pagination-fraction' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_fraction_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .swiper-pagination-fraction' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register style thumb gallery controls.
	 */
	private function register_style_thumb_gallery_controls(): void {
		$this->start_controls_section(
			'section_style_thumb_gallery',
			array(
				'label'     => esc_html__( 'Thumb Gallery', 'pixels-core-creative-tools-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'thumb_gallery' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'thumb_gallery_spacing',
			array(
				'label'      => esc_html__( 'Spacing', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default'    => array(
					'size' => 16,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}}.pixeccte-carousel-thumbs--bottom .pixeccte-carousel__thumbs-wrapper' => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.pixeccte-carousel-thumbs--top .pixeccte-carousel__thumbs-wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.pixeccte-carousel-thumbs--left .pixeccte-carousel__thumbs-wrapper' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.pixeccte-carousel-thumbs--right .pixeccte-carousel__thumbs-wrapper' => 'margin-left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'thumb_gallery_width',
			array(
				'label'      => esc_html__( 'Gallery Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 20,
						'max' => 400,
					),
				),
				'default'    => array(
					'size' => 80,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}}.pixeccte-carousel-thumbs--left .pixeccte-carousel__thumbs-wrapper, {{WRAPPER}}.pixeccte-carousel-thumbs--right .pixeccte-carousel__thumbs-wrapper' => 'width: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'thumb_gallery'  => 'yes',
					'thumb_position' => array( 'left', 'right' ),
				),
			)
		);

		$this->add_responsive_control(
			'thumb_gallery_item_width',
			array(
				'label'      => esc_html__( 'Thumb Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 20,
						'max' => 400,
					),
				),
				'default'    => array(
					'size' => 80,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}}.pixeccte-carousel-thumbs--bottom .pixeccte-carousel__thumb' => 'width: {{SIZE}}{{UNIT}} !important;',
				),
			)
		);

		$this->add_responsive_control(
			'thumb_gallery_height',
			array(
				'label'      => esc_html__( 'Thumb Height', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 20,
						'max' => 400,
					),
				),
				'default'    => array(
					'size' => 80,
					'unit' => 'px',
				),
				'selectors'  => array(
					'{{WRAPPER}}.pixeccte-carousel-thumbs--bottom .pixeccte-carousel__thumb, {{WRAPPER}}.pixeccte-carousel-thumbs--top .pixeccte-carousel__thumb' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'thumb_gallery_colors' );

		$this->start_controls_tab(
			'thumb_gallery_normal',
			array(
				'label' => esc_html__( 'Normal', 'pixels-core-creative-tools-for-elementor' ),
			)
		);

		$this->add_control(
			'thumb_gallery_opacity',
			array(
				'label'     => esc_html__( 'Opacity', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.01,
					),
				),
				'default'   => array(
					'size' => 0.5,
				),
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-carousel__thumb:not(.swiper-slide-thumb-active)' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'thumb_gallery_border',
				'selector' => '{{WRAPPER}} .pixeccte-carousel__thumb',
			)
		);

		$this->add_responsive_control(
			'thumb_gallery_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-carousel__thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
					'{{WRAPPER}} .pixeccte-carousel__thumb img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'thumb_gallery_active',
			array(
				'label' => esc_html__( 'Active', 'pixels-core-creative-tools-for-elementor' ),
			)
		);

		$this->add_control(
			'thumb_gallery_active_opacity',
			array(
				'label'     => esc_html__( 'Opacity', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.01,
					),
				),
				'default'   => array(
					'size' => 1,
				),
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-carousel__thumb.swiper-slide-thumb-active' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'thumb_gallery_active_border',
				'selector' => '{{WRAPPER}} .pixeccte-carousel__thumb.swiper-slide-thumb-active',
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register style slide controls.
	 */
	private function register_style_slide_controls(): void {
		$this->start_controls_section(
			'section_style_slides',
			array(
				'label' => esc_html__( 'Slides', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'slide_min_height',
			array(
				'label'      => esc_html__( 'Min Height', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
					'vh' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-carousel__slides > .e-con' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'slide_border',
				'selector' => '{{WRAPPER}} .pixeccte-carousel__slides > .e-con',
			)
		);

		$this->add_responsive_control(
			'slide_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-carousel__slides > .e-con' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'slide_shadow',
				'selector' => '{{WRAPPER}} .pixeccte-carousel__slides > .e-con',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Print child.
	 *
	 * @param mixed $index Index.
	 * @param mixed $item_settings Item settings.
	 */
	public function print_child( $index, $item_settings = array() ): void {
		$children  = $this->get_children();
		$child_ids = array();

		foreach ( $children as $child ) {
			$child_ids[] = $child->get_id();
		}

		$add_attribute_to_container = function ( $should_render, $container ) use ( $child_ids ) {
			if ( in_array( $container->get_id(), $child_ids, true ) ) {
				$container->add_render_attribute(
					'_wrapper',
					array(
						'class'                => array(
							'swiper-slide',
							'pixeccte-carousel__slide',
						),
						'role'                 => 'group',
						'aria-roledescription' => 'slide',
					)
				);
			}

			return $should_render;
		};

		add_filter( 'elementor/frontend/container/should_render', $add_attribute_to_container, 10, 3 );

		if ( isset( $children[ $index ] ) ) {
			$children[ $index ]->print_element();
		}

		remove_filter( 'elementor/frontend/container/should_render', $add_attribute_to_container );
	}

	/**
	 * Render slides html.
	 *
	 * @param array $settings Settings.
	 */
	protected function render_slides_html( array $settings ): void {
		$slides = $settings['slides'] ?? array();

		foreach ( $slides as $index => $item ) {
			unset( $item );
			$this->print_child( $index );
		}
	}

	/**
	 * Get thumb slide data.
	 *
	 * @param array $slide Slide.
	 * @param int   $index Index.
	 * @return array Result.
	 */
	private function get_thumb_slide_data( array $slide, int $index ): array {
		$image = $slide['thumb_image'] ?? array();
		$title = $slide['slide_title'] ?? sprintf(
			/* translators: %d: Slide number. */
			esc_html__( 'Slide %d', 'pixels-core-creative-tools-for-elementor' ),
			$index + 1
		);

		return array(
			'image' => $image,
			'title' => $title,
		);
	}

	/**
	 * Get thumb image settings.
	 *
	 * @param array $image Image.
	 * @param array $settings Settings.
	 * @return array Result.
	 */
	private function get_thumb_image_settings( array $image, array $settings ): array {
		$thumb_settings = $settings;

		$thumb_settings['thumb_gallery_image'] = $image;

		if ( empty( $thumb_settings['thumb_gallery_image_size'] ) ) {
			$thumb_settings['thumb_gallery_image_size'] = 'thumbnail';
		}

		return $thumb_settings;
	}

	/**
	 * Render thumb slide.
	 *
	 * @param array $slide Slide.
	 * @param int   $index Index.
	 * @param array $settings Settings.
	 */
	private function render_thumb_slide( array $slide, int $index, array $settings ): void {
		$thumb_data = $this->get_thumb_slide_data( $slide, $index );
		$image      = $thumb_data['image'];
		$thumb_alt  = $thumb_data['title'];
		$has_image  = ! empty( $image['id'] ) || ! empty( $image['url'] );

		if ( $thumb_alt && empty( $image['alt'] ) ) {
			$image['alt'] = $thumb_alt;
		}
		?>
		<div class="swiper-slide pixeccte-carousel__thumb" role="button" tabindex="0" aria-label="<?php echo esc_attr( $thumb_alt ); ?>">
			<?php if ( $has_image && ! empty( $image['id'] ) ) : ?>
				<?php
				$thumb_html = Group_Control_Image_Size::get_attachment_image_html(
					$this->get_thumb_image_settings( $image, $settings ),
					'thumb_gallery_image'
				);
				echo wp_kses_post( $thumb_html );
				?>
			<?php elseif ( $has_image && ! empty( $image['url'] ) ) : ?>
				<img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $thumb_alt ); ?>" loading="lazy" />
			<?php else : ?>
				<span class="pixeccte-carousel__thumb-placeholder" aria-hidden="true"><?php echo esc_html( (string) ( $index + 1 ) ); ?></span>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render thumbs html.
	 *
	 * @param array $settings Settings.
	 */
	private function render_thumbs_html( array $settings ): void {
		$slides = $settings['slides'] ?? array();

		foreach ( $slides as $index => $slide ) {
			$this->render_thumb_slide( $slide, (int) $index, $settings );
		}
	}

	/**
	 * Render swiper button.
	 *
	 * @param string $type Type.
	 */
	private function render_swiper_button( string $type ): void {
		$direction     = 'next' === $type ? 'right' : 'left';
		$icon_settings = $this->get_settings_for_display( 'navigation_' . $type . '_icon' );

		if ( empty( $icon_settings['value'] ) ) {
			$icon_settings = array(
				'library' => 'eicons',
				'value'   => 'eicon-chevron-' . $direction,
			);
		}

		Icons_Manager::render_icon( $icon_settings, array( 'aria-hidden' => 'true' ) );
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$settings    = $this->get_settings_for_display();
		$slides      = $settings['slides'] ?? array();
		$slide_count = count( $slides );

		if ( empty( $slides ) ) {
			return;
		}

		$show_arrows     = in_array( $settings['navigation'], array( 'arrows', 'both' ), true ) && $slide_count > 1;
		$show_pagination = in_array( $settings['navigation'], array( 'dots', 'both' ), true ) && $slide_count > 1;
		$show_thumbs     = 'yes' === ( $settings['thumb_gallery'] ?? '' ) && $slide_count > 1;
		$has_autoplay    = 'yes' === ( $settings['autoplay'] ?? '' );

		$this->add_render_attribute(
			'wrapper',
			array(
				'class' => array(
					'pixeccte-carousel',
					'pixeccte-carousel--' . ( $settings['direction'] ?? 'horizontal' ),
				),
			)
		);

		$this->add_render_attribute(
			'stage',
			array(
				'class'                => 'pixeccte-carousel__stage',
				'role'                 => 'region',
				'aria-roledescription' => 'carousel',
				'aria-label'           => $settings['carousel_name'] ?? esc_html__( 'Carousel', 'pixels-core-creative-tools-for-elementor' ),
			)
		);

		if ( $has_autoplay ) {
			$this->add_render_attribute( 'stage', 'aria-live', 'off' );
		} else {
			$this->add_render_attribute( 'stage', 'aria-live', 'polite' );
		}

		$this->add_render_attribute(
			'carousel',
			array(
				'class' => array(
					'pixeccte-carousel__wrapper',
					'swiper',
				),
			)
		);

		$this->add_render_attribute(
			'slides',
			array(
				'class' => array(
					'pixeccte-carousel__slides',
					'swiper-wrapper',
				),
			)
		);
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<div <?php $this->print_render_attribute_string( 'stage' ); ?>>
				<div <?php $this->print_render_attribute_string( 'carousel' ); ?>>
					<div <?php $this->print_render_attribute_string( 'slides' ); ?>>
						<?php $this->render_slides_html( $settings ); ?>
					</div>
				</div>

				<?php if ( $show_arrows ) : ?>
					<div class="elementor-swiper-button elementor-swiper-button-prev" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'Previous slide', 'pixels-core-creative-tools-for-elementor' ); ?>">
						<?php $this->render_swiper_button( 'previous' ); ?>
					</div>
					<div class="elementor-swiper-button elementor-swiper-button-next" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'Next slide', 'pixels-core-creative-tools-for-elementor' ); ?>">
						<?php $this->render_swiper_button( 'next' ); ?>
					</div>
				<?php endif; ?>

				<?php if ( $show_pagination ) : ?>
					<div class="swiper-pagination"></div>
				<?php endif; ?>
			</div>

			<?php if ( $show_thumbs ) : ?>
				<div class="pixeccte-carousel__thumbs-wrapper swiper">
					<div class="pixeccte-carousel__thumbs swiper-wrapper">
						<?php $this->render_thumbs_html( $settings ); ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Content template.
	 */
	protected function content_template(): void {
		?>
		<#
		const slideCount = settings.slides ? settings.slides.length : 0;
		const showArrows = slideCount > 1 && ( 'arrows' === settings.navigation || 'both' === settings.navigation );
		const showPagination = slideCount > 1 && ( 'dots' === settings.navigation || 'both' === settings.navigation );
		const showThumbs = slideCount > 1 && 'yes' === settings.thumb_gallery;
		const direction = settings.direction || 'horizontal';
		#>
		<div class="pixeccte-carousel pixeccte-carousel--{{{ direction }}}">
			<div class="pixeccte-carousel__stage" role="region" aria-roledescription="carousel" aria-label="{{{ settings.carousel_name || '<?php echo esc_js( esc_html__( 'Carousel', 'pixels-core-creative-tools-for-elementor' ) ); ?>' }}}">
				<div class="pixeccte-carousel__wrapper swiper">
					<div class="pixeccte-carousel__slides swiper-wrapper"></div>
				</div>
				<# if ( showArrows ) {
					const prevIconSettings = settings.navigation_previous_icon && settings.navigation_previous_icon.value
						? settings.navigation_previous_icon
						: { library: 'eicons', value: 'eicon-chevron-left' };
					const nextIconSettings = settings.navigation_next_icon && settings.navigation_next_icon.value
						? settings.navigation_next_icon
						: { library: 'eicons', value: 'eicon-chevron-right' };
					const prevIconHTML = elementor.helpers.renderIcon( view, prevIconSettings, { 'aria-hidden': true }, 'i', 'object' );
					const nextIconHTML = elementor.helpers.renderIcon( view, nextIconSettings, { 'aria-hidden': true }, 'i', 'object' );
				#>
					<div class="elementor-swiper-button elementor-swiper-button-prev" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'Previous slide', 'pixels-core-creative-tools-for-elementor' ); ?>">
						<# if ( prevIconHTML && prevIconHTML.rendered ) { #>
							{{{ elementor.helpers.sanitize( prevIconHTML.value ) }}}
						<# } #>
					</div>
					<div class="elementor-swiper-button elementor-swiper-button-next" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'Next slide', 'pixels-core-creative-tools-for-elementor' ); ?>">
						<# if ( nextIconHTML && nextIconHTML.rendered ) { #>
							{{{ elementor.helpers.sanitize( nextIconHTML.value ) }}}
						<# } #>
					</div>
				<# } #>
				<# if ( showPagination ) { #>
					<div class="swiper-pagination"></div>
				<# } #>
			</div>
			<# if ( showThumbs && settings.slides ) { #>
				<div class="pixeccte-carousel__thumbs-wrapper swiper">
					<div class="pixeccte-carousel__thumbs swiper-wrapper">
						<# _.each( settings.slides, function( slide, index ) {
							const image = slide.thumb_image || {};
							const thumbTitle = slide.slide_title || '<?php echo esc_js( esc_html__( 'Slide', 'pixels-core-creative-tools-for-elementor' ) ); ?> ' + ( index + 1 );
							const imageSettings = jQuery.extend( {}, settings, {
								thumb_gallery_image: image,
							} );
							let thumbUrl = '';

							if ( image && image.id ) {
								thumbUrl = elementor.imagesManager.getImageUrl( imageSettings, imageSettings, 'thumb_gallery_image' ) || image.url || '';
							} else if ( image && image.url ) {
								thumbUrl = image.url;
							}
							#>
							<div class="swiper-slide pixeccte-carousel__thumb" role="button" tabindex="0" aria-label="{{{ thumbTitle }}}">
								<# if ( thumbUrl ) { #>
									<img src="{{{ thumbUrl }}}" alt="{{{ thumbTitle }}}" loading="lazy" />
								<# } else { #>
									<span class="pixeccte-carousel__thumb-placeholder" aria-hidden="true">{{{ index + 1 }}}</span>
								<# } #>
							</div>
						<# } ); #>
					</div>
				</div>
			<# } #>
		</div>
		<?php
	}
}
