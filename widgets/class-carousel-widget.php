<?php
namespace PixelsElementorAddons\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Modules\NestedElements\Base\Widget_Nested_Base;
use Elementor\Modules\NestedElements\Controls\Control_Nested_Repeater;
use Elementor\Repeater;
use PixelsElementorAddons\Assets_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Carousel_Widget extends Widget_Nested_Base {

	use Widget_Assets_Trait;

	public function get_name(): string {
		return 'pixels-carousel';
	}

	public function get_title(): string {
		return esc_html__( 'Carousel', 'pixels-elementor-addons' );
	}

	public function get_icon(): string {
		return 'pixels-icon pixels-icon-carousel';
	}

	public function get_categories(): array {
		return [ 'pixels-core' ];
	}

	public function get_keywords(): array {
		return [ 'carousel', 'slider', 'swiper', 'slideshow', 'pixels', 'nested' ];
	}

	protected function get_assets_slug(): string {
		return 'carousel';
	}

	public function show_in_panel(): bool {
		return \PixelsElementorAddons\Plugin::is_nested_elements_active();
	}

	public function get_script_depends(): array {
		return [
			'swiper',
			Assets_Manager::instance()->get_script_handle( $this->get_assets_slug() ),
		];
	}

	public function get_style_depends(): array {
		return [
			'e-swiper',
			'swiper',
			Assets_Manager::instance()->get_style_handle( $this->get_assets_slug() ),
		];
	}

	protected function slide_content_container( int $index ): array {
		return [
			'elType'   => 'container',
			'settings' => [
				'_title'        => sprintf(
					/* translators: %d: Slide index. */
					esc_html__( 'Slide #%d', 'pixels-elementor-addons' ),
					$index
				),
				'content_width' => 'full',
			],
		];
	}

	protected function get_default_children_elements(): array {
		return [
			$this->slide_content_container( 1 ),
			$this->slide_content_container( 2 ),
			$this->slide_content_container( 3 ),
		];
	}

	protected function get_default_repeater_title_setting_key(): string {
		return 'slide_title';
	}

	protected function get_default_children_title(): string {
		/* translators: %d: Slide index. */
		return esc_html__( 'Slide #%d', 'pixels-elementor-addons' );
	}

	protected function get_default_children_placeholder_selector(): string {
		return '.pixels-core-carousel__slides';
	}

	protected function get_initial_config(): array {
		return array_merge( parent::get_initial_config(), [
			'support_improved_repeaters' => true,
		] );
	}

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

	private function register_content_controls(): void {
		$this->start_controls_section(
			'section_slides',
			[
				'label' => esc_html__( 'Slides', 'pixels-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'slide_title',
			[
				'label'       => esc_html__( 'Title', 'pixels-elementor-addons' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Slide Title', 'pixels-elementor-addons' ),
				'label_block' => true,
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'thumb_image',
			[
				'label'       => esc_html__( 'Thumb Image', 'pixels-elementor-addons' ),
				'type'        => Controls_Manager::MEDIA,
				'label_block' => true,
				'description' => esc_html__( 'Used when Thumb Gallery is enabled.', 'pixels-elementor-addons' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'slides',
			[
				'label'       => esc_html__( 'Slide Items', 'pixels-elementor-addons' ),
				'type'        => Control_Nested_Repeater::CONTROL_TYPE,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[
						'slide_title' => esc_html__( 'Slide #1', 'pixels-elementor-addons' ),
					],
					[
						'slide_title' => esc_html__( 'Slide #2', 'pixels-elementor-addons' ),
					],
					[
						'slide_title' => esc_html__( 'Slide #3', 'pixels-elementor-addons' ),
					],
				],
				'title_field' => '{{{ slide_title }}}',
				'button_text' => esc_html__( 'Add Slide', 'pixels-elementor-addons' ),
			]
		);

		$this->add_control(
			'carousel_name',
			[
				'label'   => esc_html__( 'Carousel Name', 'pixels-elementor-addons' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Carousel', 'pixels-elementor-addons' ),
			]
		);

		$slides_to_show = array_combine( range( 1, 10 ), range( 1, 10 ) );

		$this->add_responsive_control(
			'slides_to_show',
			[
				'label'              => esc_html__( 'Slides to Show', 'pixels-elementor-addons' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => '1',
				'tablet_default'     => '1',
				'mobile_default'     => '1',
				'options'            => $slides_to_show,
				'frontend_available' => true,
				'selectors'          => [
					'{{WRAPPER}} .pixels-core-carousel__wrapper' => '--pixels-carousel-slides-to-show: {{VALUE}};',
				],
				'content_classes'    => 'elementor-control-field-select-small',
			]
		);

		$this->add_responsive_control(
			'slides_to_scroll',
			[
				'label'              => esc_html__( 'Slides to Scroll', 'pixels-elementor-addons' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => '1',
				'options'          => $slides_to_show,
				'frontend_available' => true,
				'content_classes'    => 'elementor-control-field-select-small',
				'condition'          => [
					'slides_to_show!' => '1',
				],
			]
		);

		$this->add_responsive_control(
			'slide_gap',
			[
				'label'      => esc_html__( 'Gap', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'size' => 16,
					'unit' => 'px',
				],
				'frontend_available' => true,
				'selectors'          => [
					'{{WRAPPER}} .pixels-core-carousel__wrapper' => '--pixels-carousel-slide-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'direction',
			[
				'label'   => esc_html__( 'Direction', 'pixels-elementor-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'horizontal' => esc_html__( 'Horizontal', 'pixels-elementor-addons' ),
					'vertical'   => esc_html__( 'Vertical', 'pixels-elementor-addons' ),
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'infinite',
			[
				'label'        => esc_html__( 'Infinite Loop', 'pixels-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'effect',
			[
				'label'   => esc_html__( 'Effect', 'pixels-elementor-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => [
					'slide' => esc_html__( 'Slide', 'pixels-elementor-addons' ),
					'fade'  => esc_html__( 'Fade', 'pixels-elementor-addons' ),
				],
				'condition' => [
					'slides_to_show' => '1',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'speed',
			[
				'label'   => esc_html__( 'Animation Speed', 'pixels-elementor-addons' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 500,
				'min'     => 0,
				'max'     => 10000,
				'step'    => 50,
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay_heading',
			[
				'label'     => esc_html__( 'Autoplay', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'        => esc_html__( 'Autoplay', 'pixels-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label'     => esc_html__( 'Autoplay Speed', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'min'       => 0,
				'step'      => 500,
				'condition' => [
					'autoplay' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label'        => esc_html__( 'Pause on Hover', 'pixels-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => [
					'autoplay' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'pause_on_interaction',
			[
				'label'        => esc_html__( 'Pause on Interaction', 'pixels-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'condition'    => [
					'autoplay' => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'navigation',
			[
				'label'   => esc_html__( 'Navigation', 'pixels-elementor-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'both',
				'options' => [
					'both'   => esc_html__( 'Arrows and Pagination', 'pixels-elementor-addons' ),
					'arrows' => esc_html__( 'Arrows', 'pixels-elementor-addons' ),
					'dots'   => esc_html__( 'Pagination', 'pixels-elementor-addons' ),
					'none'   => esc_html__( 'None', 'pixels-elementor-addons' ),
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'pagination_type',
			[
				'label'        => esc_html__( 'Pagination Type', 'pixels-elementor-addons' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'bullets',
				'options'      => [
					'bullets'    => esc_html__( 'Bullets', 'pixels-elementor-addons' ),
					'progress'   => esc_html__( 'Progress', 'pixels-elementor-addons' ),
					'fraction'   => esc_html__( 'Fraction', 'pixels-elementor-addons' ),
				],
				'prefix_class' => 'pixels-core-carousel-pagination-type--',
				'frontend_available' => true,
				'condition'    => [
					'navigation' => [ 'both', 'dots' ],
				],
			]
		);

		$this->add_control(
			'navigation_previous_icon',
			[
				'label'   => esc_html__( 'Previous Arrow Icon', 'pixels-elementor-addons' ),
				'type'    => Controls_Manager::ICONS,
				'skin'    => 'inline',
				'condition' => [
					'navigation' => [ 'both', 'arrows' ],
				],
			]
		);

		$this->add_control(
			'navigation_next_icon',
			[
				'label'   => esc_html__( 'Next Arrow Icon', 'pixels-elementor-addons' ),
				'type'    => Controls_Manager::ICONS,
				'skin'    => 'inline',
				'condition' => [
					'navigation' => [ 'both', 'arrows' ],
				],
			]
		);

		$this->add_control(
			'pagination_dynamic_bullets',
			[
				'label'        => esc_html__( 'Dynamic Bullets', 'pixels-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'pixels-elementor-addons' ),
				'label_off'    => esc_html__( 'No', 'pixels-elementor-addons' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => esc_html__( 'Show a compact set of bullets that updates as you slide.', 'pixels-elementor-addons' ),
				'frontend_available' => true,
				'condition'    => [
					'navigation'      => [ 'both', 'dots' ],
					'pagination_type' => 'bullets',
				],
			]
		);

		$this->end_controls_section();

		$this->register_thumb_gallery_content_controls();
	}

	private function register_thumb_gallery_content_controls(): void {
		$this->start_controls_section(
			'section_thumb_gallery',
			[
				'label' => esc_html__( 'Thumb Gallery', 'pixels-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'thumb_gallery',
			[
				'label'              => esc_html__( 'Thumb Gallery', 'pixels-elementor-addons' ),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__( 'Yes', 'pixels-elementor-addons' ),
				'label_off'          => esc_html__( 'No', 'pixels-elementor-addons' ),
				'return_value'       => 'yes',
				'default'            => '',
				'prefix_class'       => 'pixels-core-carousel--has-thumbs',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'thumb_position',
			[
				'label'              => esc_html__( 'Position', 'pixels-elementor-addons' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'bottom',
				'options'            => [
					'bottom' => esc_html__( 'Bottom', 'pixels-elementor-addons' ),
					'top'    => esc_html__( 'Top', 'pixels-elementor-addons' ),
					'left'   => esc_html__( 'Left', 'pixels-elementor-addons' ),
					'right'  => esc_html__( 'Right', 'pixels-elementor-addons' ),
				],
				'prefix_class'       => 'pixels-core-carousel-thumbs--',
				'frontend_available' => true,
				'condition'          => [
					'thumb_gallery' => 'yes',
				],
			]
		);

		$this->add_control(
			'thumb_images_help',
			[
				'type'              => Controls_Manager::RAW_HTML,
				'raw'               => '<div class="elementor-panel-alert elementor-panel-alert-info">' . esc_html__( 'Add a thumb image for each slide in the Slide Items list above.', 'pixels-elementor-addons' ) . '</div>',
				'content_classes'   => 'elementor-descriptor',
				'condition'         => [
					'thumb_gallery' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'thumb_gallery_image',
				'default'   => 'thumbnail',
				'condition' => [
					'thumb_gallery' => 'yes',
				],
			]
		);

		$thumb_slides_to_show = array_combine( range( 1, 10 ), range( 1, 10 ) );

		$this->add_responsive_control(
			'thumb_slides_to_show',
			[
				'label'              => esc_html__( 'Thumbs to Show', 'pixels-elementor-addons' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => '4',
				'tablet_default'     => '4',
				'mobile_default'     => '3',
				'options'            => $thumb_slides_to_show,
				'frontend_available' => true,
				'selectors'          => [
					'{{WRAPPER}} .pixels-core-carousel__thumbs-wrapper' => '--pixels-carousel-thumbs-to-show: {{VALUE}};',
				],
				'condition'          => [
					'thumb_gallery' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'thumb_gap',
			[
				'label'              => esc_html__( 'Gap', 'pixels-elementor-addons' ),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => [ 'px' ],
				'range'              => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default'            => [
					'size' => 10,
					'unit' => 'px',
				],
				'frontend_available' => true,
				'selectors'          => [
					'{{WRAPPER}} .pixels-core-carousel__thumbs-wrapper' => '--pixels-carousel-thumb-gap: {{SIZE}}{{UNIT}};',
				],
				'condition'          => [
					'thumb_gallery' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_style_navigation_controls(): void {
		$this->start_controls_section(
			'section_style_navigation',
			[
				'label'     => esc_html__( 'Arrows', 'pixels-elementor-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'navigation' => [ 'both', 'arrows' ],
				],
			]
		);

		$this->add_responsive_control(
			'arrows_size',
			[
				'label'      => esc_html__( 'Size', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .elementor-swiper-button' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'prev_arrow_heading',
			[
				'label'     => esc_html__( 'Previous Arrow', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'prev_arrow_vertical_align',
			[
				'label'                => esc_html__( 'Vertical Position', 'pixels-elementor-addons' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'center',
				'options'              => [
					'center' => esc_html__( 'Center', 'pixels-elementor-addons' ),
					'top'    => esc_html__( 'Top', 'pixels-elementor-addons' ),
					'bottom' => esc_html__( 'Bottom', 'pixels-elementor-addons' ),
				],
				'selectors_dictionary' => [
					'center' => 'top: 50%; bottom: auto; transform: translateY(-50%);',
					'top'    => 'top: 0; bottom: auto; transform: none;',
					'bottom' => 'top: auto; bottom: 0; transform: none;',
				],
				'selectors'            => [
					'{{WRAPPER}} .elementor-swiper-button-prev' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'prev_arrow_vertical_offset',
			[
				'label'      => esc_html__( 'Vertical Offset', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'vh' ],
				'range'      => [
					'px' => [
						'min' => -300,
						'max' => 300,
					],
					'%'  => [
						'min' => -50,
						'max' => 50,
					],
				],
				'default'    => [
					'size' => 16,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .elementor-swiper-button-prev' => 'top: {{SIZE}}{{UNIT}}; bottom: auto; transform: none;',
				],
				'condition'  => [
					'prev_arrow_vertical_align' => 'top',
				],
			]
		);

		$this->add_responsive_control(
			'prev_arrow_vertical_offset_bottom',
			[
				'label'      => esc_html__( 'Vertical Offset', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'vh' ],
				'range'      => [
					'px' => [
						'min' => -300,
						'max' => 300,
					],
					'%'  => [
						'min' => -50,
						'max' => 50,
					],
				],
				'default'    => [
					'size' => 16,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .elementor-swiper-button-prev' => 'bottom: {{SIZE}}{{UNIT}}; top: auto; transform: none;',
				],
				'condition'  => [
					'prev_arrow_vertical_align' => 'bottom',
				],
			]
		);

		$this->add_responsive_control(
			'prev_arrow_horizontal_offset',
			[
				'label'      => esc_html__( 'Horizontal Offset', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'range'      => [
					'px' => [
						'min' => -300,
						'max' => 300,
					],
					'%'  => [
						'min' => -50,
						'max' => 50,
					],
				],
				'default'    => [
					'size' => 16,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .elementor-swiper-button-prev' => 'left: {{SIZE}}{{UNIT}}; right: auto;',
				],
			]
		);

		$this->add_control(
			'next_arrow_heading',
			[
				'label'     => esc_html__( 'Next Arrow', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'next_arrow_vertical_align',
			[
				'label'                => esc_html__( 'Vertical Position', 'pixels-elementor-addons' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'center',
				'options'              => [
					'center' => esc_html__( 'Center', 'pixels-elementor-addons' ),
					'top'    => esc_html__( 'Top', 'pixels-elementor-addons' ),
					'bottom' => esc_html__( 'Bottom', 'pixels-elementor-addons' ),
				],
				'selectors_dictionary' => [
					'center' => 'top: 50%; bottom: auto; transform: translateY(-50%);',
					'top'    => 'top: 0; bottom: auto; transform: none;',
					'bottom' => 'top: auto; bottom: 0; transform: none;',
				],
				'selectors'            => [
					'{{WRAPPER}} .elementor-swiper-button-next' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'next_arrow_vertical_offset',
			[
				'label'      => esc_html__( 'Vertical Offset', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'vh' ],
				'range'      => [
					'px' => [
						'min' => -300,
						'max' => 300,
					],
					'%'  => [
						'min' => -50,
						'max' => 50,
					],
				],
				'default'    => [
					'size' => 16,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .elementor-swiper-button-next' => 'top: {{SIZE}}{{UNIT}}; bottom: auto; transform: none;',
				],
				'condition'  => [
					'next_arrow_vertical_align' => 'top',
				],
			]
		);

		$this->add_responsive_control(
			'next_arrow_vertical_offset_bottom',
			[
				'label'      => esc_html__( 'Vertical Offset', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'vh' ],
				'range'      => [
					'px' => [
						'min' => -300,
						'max' => 300,
					],
					'%'  => [
						'min' => -50,
						'max' => 50,
					],
				],
				'default'    => [
					'size' => 16,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .elementor-swiper-button-next' => 'bottom: {{SIZE}}{{UNIT}}; top: auto; transform: none;',
				],
				'condition'  => [
					'next_arrow_vertical_align' => 'bottom',
				],
			]
		);

		$this->add_responsive_control(
			'next_arrow_horizontal_offset',
			[
				'label'      => esc_html__( 'Horizontal Offset', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'range'      => [
					'px' => [
						'min' => -300,
						'max' => 300,
					],
					'%'  => [
						'min' => -50,
						'max' => 50,
					],
				],
				'default'    => [
					'size' => 16,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .elementor-swiper-button-next' => 'right: {{SIZE}}{{UNIT}}; left: auto;',
				],
			]
		);

		$this->start_controls_tabs( 'arrows_colors' );

		$this->start_controls_tab(
			'arrows_normal',
			[
				'label' => esc_html__( 'Normal', 'pixels-elementor-addons' ),
			]
		);

		$this->add_control(
			'arrows_color',
			[
				'label'     => esc_html__( 'Color', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-swiper-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-swiper-button svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'arrows_hover',
			[
				'label' => esc_html__( 'Hover', 'pixels-elementor-addons' ),
			]
		);

		$this->add_control(
			'arrows_hover_color',
			[
				'label'     => esc_html__( 'Color', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-swiper-button:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .elementor-swiper-button:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function register_style_pagination_controls(): void {
		$this->start_controls_section(
			'section_style_pagination',
			[
				'label'     => esc_html__( 'Pagination', 'pixels-elementor-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'navigation' => [ 'both', 'dots' ],
				],
			]
		);

		$this->add_responsive_control(
			'pagination_placement',
			[
				'label'        => esc_html__( 'Placement', 'pixels-elementor-addons' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'outside',
				'options'      => [
					'outside' => esc_html__( 'Outside', 'pixels-elementor-addons' ),
					'inside'  => esc_html__( 'Inside', 'pixels-elementor-addons' ),
				],
				'prefix_class' => 'pixels-core-carousel-pagination--',
			]
		);

		$this->add_responsive_control(
			'pagination_vertical_align',
			[
				'label'                => esc_html__( 'Vertical Position', 'pixels-elementor-addons' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'bottom',
				'options'              => [
					'top'    => esc_html__( 'Top', 'pixels-elementor-addons' ),
					'center' => esc_html__( 'Center', 'pixels-elementor-addons' ),
					'bottom' => esc_html__( 'Bottom', 'pixels-elementor-addons' ),
				],
				'selectors_dictionary' => [
					'top'    => 'top: 0; bottom: auto; margin-top: 0; margin-bottom: 0;',
					'center' => 'top: 50%; bottom: auto; margin-top: 0; margin-bottom: 0;',
					'bottom' => 'top: auto; bottom: 0; margin-top: 0; margin-bottom: 0;',
				],
				'selectors'            => [
					'{{WRAPPER}} .swiper-pagination' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_vertical_offset',
			[
				'label'      => esc_html__( 'Vertical Offset', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'range'      => [
					'px' => [
						'min' => -300,
						'max' => 300,
					],
					'%'  => [
						'min' => -50,
						'max' => 50,
					],
				],
				'default'    => [
					'size' => 16,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}}.pixels-core-carousel-pagination--inside .swiper-pagination' => 'top: {{SIZE}}{{UNIT}}; bottom: auto; transform: none;',
					'{{WRAPPER}}.pixels-core-carousel-pagination--outside .swiper-pagination' => 'bottom: 100%; top: auto; margin-bottom: {{SIZE}}{{UNIT}}; margin-top: 0; transform: none;',
				],
				'condition'  => [
					'pagination_vertical_align' => 'top',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_vertical_offset_bottom',
			[
				'label'      => esc_html__( 'Vertical Offset', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'range'      => [
					'px' => [
						'min' => -300,
						'max' => 300,
					],
					'%'  => [
						'min' => -50,
						'max' => 50,
					],
				],
				'default'    => [
					'size' => 16,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}}.pixels-core-carousel-pagination--inside .swiper-pagination' => 'bottom: {{SIZE}}{{UNIT}}; top: auto; transform: none;',
					'{{WRAPPER}}.pixels-core-carousel-pagination--outside .swiper-pagination' => 'top: 100%; bottom: auto; margin-top: {{SIZE}}{{UNIT}}; margin-bottom: 0; transform: none;',
				],
				'condition'  => [
					'pagination_vertical_align' => 'bottom',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_vertical_offset_center',
			[
				'label'      => esc_html__( 'Vertical Offset', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'range'      => [
					'px' => [
						'min' => -300,
						'max' => 300,
					],
					'%'  => [
						'min' => -50,
						'max' => 50,
					],
				],
				'default'    => [
					'size' => 0,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination' => 'top: calc(50% + {{SIZE}}{{UNIT}}); bottom: auto; transform: translateY(-50%);',
				],
				'condition'  => [
					'pagination_vertical_align'   => 'center',
					'pagination_horizontal_align!' => 'center',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_horizontal_align',
			[
				'label'                => esc_html__( 'Horizontal Position', 'pixels-elementor-addons' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'center',
				'options'              => [
					'left'   => esc_html__( 'Left', 'pixels-elementor-addons' ),
					'center' => esc_html__( 'Center', 'pixels-elementor-addons' ),
					'right'  => esc_html__( 'Right', 'pixels-elementor-addons' ),
				],
				'selectors_dictionary' => [
					'left'   => 'left: 0; right: auto;',
					'center' => 'left: 50%; right: auto;',
					'right'  => 'left: auto; right: 0;',
				],
				'selectors'            => [
					'{{WRAPPER}} .swiper-pagination' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_horizontal_offset',
			[
				'label'      => esc_html__( 'Horizontal Offset', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'range'      => [
					'px' => [
						'min' => -300,
						'max' => 300,
					],
					'%'  => [
						'min' => -50,
						'max' => 50,
					],
				],
				'default'    => [
					'size' => 0,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination' => 'left: {{SIZE}}{{UNIT}}; right: auto; transform: none;',
				],
				'condition'  => [
					'pagination_horizontal_align' => 'left',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_horizontal_offset_right',
			[
				'label'      => esc_html__( 'Horizontal Offset', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'range'      => [
					'px' => [
						'min' => -300,
						'max' => 300,
					],
					'%'  => [
						'min' => -50,
						'max' => 50,
					],
				],
				'default'    => [
					'size' => 0,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination' => 'right: {{SIZE}}{{UNIT}}; left: auto; transform: none;',
				],
				'condition'  => [
					'pagination_horizontal_align' => 'right',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_horizontal_offset_center',
			[
				'label'      => esc_html__( 'Horizontal Offset', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'range'      => [
					'px' => [
						'min' => -300,
						'max' => 300,
					],
					'%'  => [
						'min' => -50,
						'max' => 50,
					],
				],
				'default'    => [
					'size' => 0,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination' => 'left: calc(50% + {{SIZE}}{{UNIT}}); right: auto; transform: translateX(-50%);',
				],
				'condition'  => [
					'pagination_horizontal_align' => 'center',
					'pagination_vertical_align!'  => 'center',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_center_offset',
			[
				'label'      => esc_html__( 'Center Offset', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default'    => [
					'top'  => '0',
					'left' => '0',
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination' => 'top: calc(50% + {{TOP}}{{UNIT}}); left: calc(50% + {{LEFT}}{{UNIT}}); right: auto; bottom: auto; transform: translate(-50%, -50%);',
				],
				'condition'  => [
					'pagination_vertical_align'   => 'center',
					'pagination_horizontal_align' => 'center',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_style_pagination_bullets_controls(): void {
		$this->start_controls_section(
			'section_style_pagination_bullets',
			[
				'label'     => esc_html__( 'Pagination Bullets', 'pixels-elementor-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'navigation'      => [ 'both', 'dots' ],
					'pagination_type' => 'bullets',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_gap',
			[
				'label'      => esc_html__( 'Gap', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination-bullet' => '--swiper-pagination-bullet-horizontal-gap: {{SIZE}}{{UNIT}}; --swiper-pagination-bullet-vertical-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'pagination_bullets_colors' );

		$this->start_controls_tab(
			'pagination_normal',
			[
				'label' => esc_html__( 'Normal', 'pixels-elementor-addons' ),
			]
		);

		$this->add_responsive_control(
			'pagination_normal_width',
			[
				'label'      => esc_html__( 'Width', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination-bullet:not(.swiper-pagination-bullet-active)' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_normal_height',
			[
				'label'      => esc_html__( 'Height', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination-bullet:not(.swiper-pagination-bullet-active)' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'pagination_color',
			[
				'label'     => esc_html__( 'Color', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet:not(.swiper-pagination-bullet-active)' => 'background: {{VALUE}}; opacity: 1;',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_active',
			[
				'label' => esc_html__( 'Active', 'pixels-elementor-addons' ),
			]
		);

		$this->add_responsive_control(
			'pagination_active_width',
			[
				'label'      => esc_html__( 'Width', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_active_height',
			[
				'label'      => esc_html__( 'Height', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'pagination_active_color',
			[
				'label'     => esc_html__( 'Color', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function register_style_pagination_progress_controls(): void {
		$this->start_controls_section(
			'section_style_pagination_progress',
			[
				'label'     => esc_html__( 'Pagination Progress', 'pixels-elementor-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'navigation'      => [ 'both', 'dots' ],
					'pagination_type' => 'progress',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_progress_width',
			[
				'label'      => esc_html__( 'Width', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 1200,
					],
					'%'  => [
						'min' => 1,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination' => 'width: {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_progress_height',
			[
				'label'      => esc_html__( 'Height', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [
						'min' => 1,
						'max' => 50,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 4,
				],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination-progressbar' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_progress_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination-progressbar' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .swiper-pagination-progressbar .swiper-pagination-progressbar-fill' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'pagination_progress_track_color',
			[
				'label'     => esc_html__( 'Track Color', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-progressbar' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_progress_fill_color',
			[
				'label'     => esc_html__( 'Fill Color', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-progressbar .swiper-pagination-progressbar-fill' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_style_pagination_fraction_controls(): void {
		$this->start_controls_section(
			'section_style_pagination_fraction',
			[
				'label'     => esc_html__( 'Pagination Fraction', 'pixels-elementor-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'navigation'      => [ 'both', 'dots' ],
					'pagination_type' => 'fraction',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'pagination_fraction_typography',
				'selector' => '{{WRAPPER}} .swiper-pagination-fraction',
			]
		);

		$this->add_control(
			'pagination_fraction_color',
			[
				'label'     => esc_html__( 'Color', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-fraction' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_fraction_current_color',
			[
				'label'     => esc_html__( 'Current Color', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-current' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_fraction_total_color',
			[
				'label'     => esc_html__( 'Total Color', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-total' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_fraction_background',
			[
				'label'     => esc_html__( 'Background Color', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-fraction' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_fraction_padding',
			[
				'label'      => esc_html__( 'Padding', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination-fraction' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_fraction_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination-fraction' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_style_thumb_gallery_controls(): void {
		$this->start_controls_section(
			'section_style_thumb_gallery',
			[
				'label'     => esc_html__( 'Thumb Gallery', 'pixels-elementor-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'thumb_gallery' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'thumb_gallery_spacing',
			[
				'label'      => esc_html__( 'Spacing', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'size' => 16,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}}.pixels-core-carousel-thumbs--bottom .pixels-core-carousel__thumbs-wrapper' => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.pixels-core-carousel-thumbs--top .pixels-core-carousel__thumbs-wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.pixels-core-carousel-thumbs--left .pixels-core-carousel__thumbs-wrapper' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.pixels-core-carousel-thumbs--right .pixels-core-carousel__thumbs-wrapper' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'thumb_gallery_width',
			[
				'label'      => esc_html__( 'Gallery Width', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 20,
						'max' => 400,
					],
				],
				'default'    => [
					'size' => 80,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}}.pixels-core-carousel-thumbs--left .pixels-core-carousel__thumbs-wrapper, {{WRAPPER}}.pixels-core-carousel-thumbs--right .pixels-core-carousel__thumbs-wrapper' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'thumb_gallery' => 'yes',
					'thumb_position' => [ 'left', 'right' ],
				],
			]
		);

		$this->add_responsive_control(
			'thumb_gallery_item_width',
			[
				'label'      => esc_html__( 'Thumb Width', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 20,
						'max' => 400,
					],
				],
				'default'    => [
					'size' => 80,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}}.pixels-core-carousel-thumbs--bottom .pixels-core-carousel__thumb' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'thumb_gallery_height',
			[
				'label'      => esc_html__( 'Thumb Height', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min' => 20,
						'max' => 400,
					],
				],
				'default'    => [
					'size' => 80,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}}.pixels-core-carousel-thumbs--bottom .pixels-core-carousel__thumb, {{WRAPPER}}.pixels-core-carousel-thumbs--top .pixels-core-carousel__thumb' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'thumb_gallery_colors' );

		$this->start_controls_tab(
			'thumb_gallery_normal',
			[
				'label' => esc_html__( 'Normal', 'pixels-elementor-addons' ),
			]
		);

		$this->add_control(
			'thumb_gallery_opacity',
			[
				'label'     => esc_html__( 'Opacity', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.01,
					],
				],
				'default'   => [
					'size' => 0.5,
				],
				'selectors' => [
					'{{WRAPPER}} .pixels-core-carousel__thumb:not(.swiper-slide-thumb-active)' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'thumb_gallery_border',
				'selector' => '{{WRAPPER}} .pixels-core-carousel__thumb',
			]
		);

		$this->add_responsive_control(
			'thumb_gallery_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-carousel__thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
					'{{WRAPPER}} .pixels-core-carousel__thumb img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'thumb_gallery_active',
			[
				'label' => esc_html__( 'Active', 'pixels-elementor-addons' ),
			]
		);

		$this->add_control(
			'thumb_gallery_active_opacity',
			[
				'label'     => esc_html__( 'Opacity', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.01,
					],
				],
				'default'   => [
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .pixels-core-carousel__thumb.swiper-slide-thumb-active' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'thumb_gallery_active_border',
				'selector' => '{{WRAPPER}} .pixels-core-carousel__thumb.swiper-slide-thumb-active',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function register_style_slide_controls(): void {
		$this->start_controls_section(
			'section_style_slides',
			[
				'label' => esc_html__( 'Slides', 'pixels-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'slide_min_height',
			[
				'label'      => esc_html__( 'Min Height', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'vh' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-carousel__slides > .e-con' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'slide_border',
				'selector' => '{{WRAPPER}} .pixels-core-carousel__slides > .e-con',
			]
		);

		$this->add_responsive_control(
			'slide_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-carousel__slides > .e-con' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'slide_shadow',
				'selector' => '{{WRAPPER}} .pixels-core-carousel__slides > .e-con',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @param int $index
	 */
	public function print_child( $index, $item_settings = [] ): void {
		$children  = $this->get_children();
		$child_ids = [];

		foreach ( $children as $child ) {
			$child_ids[] = $child->get_id();
		}

		$add_attribute_to_container = function ( $should_render, $container ) use ( $child_ids ) {
			if ( in_array( $container->get_id(), $child_ids, true ) ) {
				$container->add_render_attribute(
					'_wrapper',
					[
						'class' => [
							'swiper-slide',
							'pixels-core-carousel__slide',
						],
						'role'                 => 'group',
						'aria-roledescription' => 'slide',
					]
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

	protected function render_slides_html( array $settings ): void {
		$slides = $settings['slides'] ?? [];

		foreach ( $slides as $index => $item ) {
			unset( $item );
			$this->print_child( $index );
		}
	}

	private function get_thumb_slide_data( array $slide, int $index ): array {
		$image = $slide['thumb_image'] ?? [];
		$title = $slide['slide_title'] ?? sprintf(
			/* translators: %d: Slide number. */
			esc_html__( 'Slide %d', 'pixels-elementor-addons' ),
			$index + 1
		);

		return [
			'image' => $image,
			'title' => $title,
		];
	}

	private function get_thumb_image_settings( array $image, array $settings ): array {
		$thumb_settings = $settings;

		$thumb_settings['thumb_gallery_image'] = $image;

		if ( empty( $thumb_settings['thumb_gallery_image_size'] ) ) {
			$thumb_settings['thumb_gallery_image_size'] = 'thumbnail';
		}

		return $thumb_settings;
	}

	private function render_thumb_slide( array $slide, int $index, array $settings ): void {
		$thumb_data = $this->get_thumb_slide_data( $slide, $index );
		$image      = $thumb_data['image'];
		$thumb_alt  = $thumb_data['title'];
		$has_image  = ! empty( $image['id'] ) || ! empty( $image['url'] );

		if ( $thumb_alt && empty( $image['alt'] ) ) {
			$image['alt'] = $thumb_alt;
		}
		?>
		<div class="swiper-slide pixels-core-carousel__thumb" role="button" tabindex="0" aria-label="<?php echo esc_attr( $thumb_alt ); ?>">
			<?php if ( $has_image && ! empty( $image['id'] ) ) : ?>
				<?php
				$thumb_html = Group_Control_Image_Size::get_attachment_image_html(
					$this->get_thumb_image_settings( $image, $settings ),
					'thumb_gallery_image'
				);
				echo $thumb_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor image HTML.
				?>
			<?php elseif ( $has_image && ! empty( $image['url'] ) ) : ?>
				<img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $thumb_alt ); ?>" loading="lazy" />
			<?php else : ?>
				<span class="pixels-core-carousel__thumb-placeholder" aria-hidden="true"><?php echo esc_html( (string) ( $index + 1 ) ); ?></span>
			<?php endif; ?>
		</div>
		<?php
	}

	private function render_thumbs_html( array $settings ): void {
		$slides = $settings['slides'] ?? [];

		foreach ( $slides as $index => $slide ) {
			$this->render_thumb_slide( $slide, (int) $index, $settings );
		}
	}

	private function render_swiper_button( string $type ): void {
		$direction     = 'next' === $type ? 'right' : 'left';
		$icon_settings = $this->get_settings_for_display( 'navigation_' . $type . '_icon' );

		if ( empty( $icon_settings['value'] ) ) {
			$icon_settings = [
				'library' => 'eicons',
				'value'   => 'eicon-chevron-' . $direction,
			];
		}

		Icons_Manager::render_icon( $icon_settings, [ 'aria-hidden' => 'true' ] );
	}

	protected function render(): void {
		$settings    = $this->get_settings_for_display();
		$slides      = $settings['slides'] ?? [];
		$slide_count = count( $slides );

		if ( empty( $slides ) ) {
			return;
		}

		$show_arrows     = in_array( $settings['navigation'], [ 'arrows', 'both' ], true ) && $slide_count > 1;
		$show_pagination = in_array( $settings['navigation'], [ 'dots', 'both' ], true ) && $slide_count > 1;
		$show_thumbs     = 'yes' === ( $settings['thumb_gallery'] ?? '' ) && $slide_count > 1;
		$has_autoplay = 'yes' === ( $settings['autoplay'] ?? '' );

		$this->add_render_attribute(
			'wrapper',
			[
				'class' => [
					'pixels-core-carousel',
					'pixels-core-carousel--' . ( $settings['direction'] ?? 'horizontal' ),
				],
			]
		);

		$this->add_render_attribute(
			'stage',
			[
				'class' => 'pixels-core-carousel__stage',
				'role'  => 'region',
				'aria-roledescription' => 'carousel',
				'aria-label'           => $settings['carousel_name'] ?? esc_html__( 'Carousel', 'pixels-elementor-addons' ),
			]
		);

		if ( $has_autoplay ) {
			$this->add_render_attribute( 'stage', 'aria-live', 'off' );
		} else {
			$this->add_render_attribute( 'stage', 'aria-live', 'polite' );
		}

		$this->add_render_attribute(
			'carousel',
			[
				'class' => [
					'pixels-core-carousel__wrapper',
					'swiper',
				],
			]
		);

		$this->add_render_attribute(
			'slides',
			[
				'class' => [
					'pixels-core-carousel__slides',
					'swiper-wrapper',
				],
			]
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
					<div class="elementor-swiper-button elementor-swiper-button-prev" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'Previous slide', 'pixels-elementor-addons' ); ?>">
						<?php $this->render_swiper_button( 'previous' ); ?>
					</div>
					<div class="elementor-swiper-button elementor-swiper-button-next" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'Next slide', 'pixels-elementor-addons' ); ?>">
						<?php $this->render_swiper_button( 'next' ); ?>
					</div>
				<?php endif; ?>

				<?php if ( $show_pagination ) : ?>
					<div class="swiper-pagination"></div>
				<?php endif; ?>
			</div>

			<?php if ( $show_thumbs ) : ?>
				<div class="pixels-core-carousel__thumbs-wrapper swiper">
					<div class="pixels-core-carousel__thumbs swiper-wrapper">
						<?php $this->render_thumbs_html( $settings ); ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	protected function content_template(): void {
		?>
		<#
		const slideCount = settings.slides ? settings.slides.length : 0;
		const showArrows = slideCount > 1 && ( 'arrows' === settings.navigation || 'both' === settings.navigation );
		const showPagination = slideCount > 1 && ( 'dots' === settings.navigation || 'both' === settings.navigation );
		const showThumbs = slideCount > 1 && 'yes' === settings.thumb_gallery;
		const direction = settings.direction || 'horizontal';
		#>
		<div class="pixels-core-carousel pixels-core-carousel--{{{ direction }}}">
			<div class="pixels-core-carousel__stage" role="region" aria-roledescription="carousel" aria-label="{{{ settings.carousel_name || '<?php echo esc_js( esc_html__( 'Carousel', 'pixels-elementor-addons' ) ); ?>' }}}">
				<div class="pixels-core-carousel__wrapper swiper">
					<div class="pixels-core-carousel__slides swiper-wrapper"></div>
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
					<div class="elementor-swiper-button elementor-swiper-button-prev" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'Previous slide', 'pixels-elementor-addons' ); ?>">
						<# if ( prevIconHTML && prevIconHTML.rendered ) { #>
							{{{ elementor.helpers.sanitize( prevIconHTML.value ) }}}
						<# } #>
					</div>
					<div class="elementor-swiper-button elementor-swiper-button-next" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'Next slide', 'pixels-elementor-addons' ); ?>">
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
				<div class="pixels-core-carousel__thumbs-wrapper swiper">
					<div class="pixels-core-carousel__thumbs swiper-wrapper">
						<# _.each( settings.slides, function( slide, index ) {
							const image = slide.thumb_image || {};
							const thumbTitle = slide.slide_title || '<?php echo esc_js( esc_html__( 'Slide', 'pixels-elementor-addons' ) ); ?> ' + ( index + 1 );
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
							<div class="swiper-slide pixels-core-carousel__thumb" role="button" tabindex="0" aria-label="{{{ thumbTitle }}}">
								<# if ( thumbUrl ) { #>
									<img src="{{{ thumbUrl }}}" alt="{{{ thumbTitle }}}" loading="lazy" />
								<# } else { #>
									<span class="pixels-core-carousel__thumb-placeholder" aria-hidden="true">{{{ index + 1 }}}</span>
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
