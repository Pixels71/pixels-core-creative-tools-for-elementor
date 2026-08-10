<?php
/**
 * Entry point for the plugin. Checks if Elementor is installed and activated and loads it's own files and actions.
 */
namespace PixelsCore\Theme_Builder;

use PixelsCore\Theme_Builder\Target_Rules_Fields;
defined('ABSPATH') || exit; // Abort, if called directly.

/**
 * Class Theme_Elementor
 */
class Theme_Elementor {

	/**
	 * Current theme template
	 *
	 * @var String
	 */
	public $template;

	/**
	 * Instance of Elemenntor Frontend class.
	 *
	 * @var \Elementor\Frontend()
	 */
	private static $elementor_instance;

	/**
	 * Instance of Admin
	 *
	 * @var Theme_Elementor
	 */
	private static $_instance = null;

	/**
	 * Instance of Theme_Elementor
	 *
	 * @return Theme_Elementor Instance of Theme_Elementor
	 */
	public static function instance() {
		if ( ! isset( self::$_instance ) ) :
			self::$_instance = new self();
		endif;

		return self::$_instance;
	}
	/**
	 * Constructor
	 */
	function __construct() {
		$this->template = get_template();

		if ( defined( 'ELEMENTOR_VERSION' ) && is_callable( 'Elementor\Plugin::instance' ) ) :
			self::$elementor_instance = \Elementor\Plugin::instance();

			$this->includes();
			add_action( 'init', 'pixels_core_maybe_migrate_theme_template_type_meta', 5 );

			add_action( 'init', [ $this, 'setup_unsupported_theme' ] );
			add_action( 'elementor/documents/register_controls', [ $this, 'register_popup_document_controls' ] );
			add_action( 'elementor/documents/register_controls', [ $this, 'register_mega_menu_document_controls' ] );
			add_action( 'elementor/documents/register_controls', [ $this, 'register_single_post_preview_controls' ] );
			add_filter( 'elementor/document/save/data', [ $this, 'preserve_popup_position_settings_on_save' ], 10, 2 );
			add_filter( 'elementor/document/save/data', [ $this, 'save_template_preview_post_on_document_save' ], 10, 2 );

			// Scripts and styles.
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
			add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueue_popup_editor_live_preview' ] );
			add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueue_single_post_preview_editor_script' ] );

			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );

			add_filter( 'body_class', [ $this, 'body_class' ] );
			add_action( 'switch_theme', [ $this, 'reset_unsupported_theme_notice' ] );
			add_action( 'wp_footer', [ $this, 'render_popups' ], 40 );
			add_action( 'elementor/page_templates/canvas/before_content', [ $this, 'open_popup_editor_wrapper' ], 1 );
			add_action( 'elementor/page_templates/canvas/after_content', [ $this, 'close_popup_editor_wrapper' ], 99 );

			add_shortcode( 'pixels_core_hf_template', [ $this, 'render_template' ] );
			add_filter( 'nav_menu_css_class', [ $this, 'add_mega_menu_item_classes' ], 10, 4 );
			add_filter( 'nav_menu_link_attributes', [ $this, 'add_mega_menu_link_attributes' ], 10, 4 );
			add_filter( 'walker_nav_menu_start_el', [ $this, 'render_mega_menu_panel' ], 10, 4 );

			// Use Elementor theme builder templates as full page templates when available.
			add_filter( 'template_include', [ $this, 'maybe_use_popup_canvas_template' ], 12 );
			add_filter( 'template_include', [ $this, 'maybe_use_404_template' ], 19 );
			add_filter( 'template_include', [ $this, 'maybe_use_archive_post_template' ], 20 );
			add_filter( 'template_include', [ $this, 'maybe_use_single_post_template' ], 21 );

		endif;
	}

	/**
	 * Reset the Unsupported theme nnotice after a theme is switched.
	 *
	 * @return void
	 */
	public function reset_unsupported_theme_notice() {
		delete_user_meta( get_current_user_id(), 'unsupported-theme' );
	}

	/**
	 * Loads the globally required files for the plugin.
	 */
	public function includes() {

		// Load WPML & Polylang Compatibility if WPML is installed and activated.
		// if ( defined( 'ICL_SITEPRESS_VERSION' ) || defined( 'POLYLANG_BASENAME' ) ) {
		// 	require_once PIXELS_CORE_PATH . '/inc/multilang-compatibility/class-multilang-compatibility.php';
		// }

		require_once PIXELS_CORE_PATH . 'includes/theme-builder/pixels-core-theme-functions.php';
		require_once PIXELS_CORE_PATH . 'includes/theme-builder/class-pixels-core-theme-admin.php';

		// Load Target rules.
		require_once PIXELS_CORE_PATH . 'includes/theme-builder/class-pixels-core-theme-target-rules-fields.php';
		// Setup upgrade routines.
		require_once PIXELS_CORE_PATH . 'includes/theme-builder/class-pixels-core-theme-update.php';
	}

	public function register_popup_document_controls( $document ) {
		if ( ! is_object( $document ) || ! method_exists( $document, 'get_main_post' ) ) {
			return;
		}

		$post = $document->get_main_post();

		if ( ! $post || 'pixels-core-theme' !== $post->post_type || 'type_popup' !== pixels_core_get_theme_template_type( $post->ID ) ) {
			return;
		}

		$options = $this->get_popup_options( $post->ID );

		$document->start_controls_section(
			'pixels_core_popup_layout_section',
			[
				'label' => esc_html__( 'Pixels Popup Layout', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$document->add_responsive_control(
			'pixels_core_popup_width',
			[
				'label'      => esc_html__( 'Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'vw' ],
				'range'      => [
					'px' => [ 'min' => 100, 'max' => 2000 ],
					'%'  => [ 'min' => 10, 'max' => 100 ],
					'vw' => [ 'min' => 10, 'max' => 100 ],
				],
				'default'    => $this->popup_css_size_to_dimension( $options['width'], 420, 'px' ),
				'selectors'  => [
					'.pixels-core-theme-popup-editor-preview .pixels-core-theme-popup__dialog' => '--pixels-core-popup-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$document->add_control(
			'pixels_core_popup_height_type',
			[
				'label'   => esc_html__( 'Height', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => $options['height_type'],
				'options' => [
					'fit'    => esc_html__( 'Fit To Content', 'pixels-core-creative-tools-for-elementor' ),
					'custom' => esc_html__( 'Custom', 'pixels-core-creative-tools-for-elementor' ),
				],
				'render_type' => 'template',
			]
		);

		$document->add_control(
			'pixels_core_popup_height',
			[
				'label'      => esc_html__( 'Custom Height', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range'      => [
					'px' => [ 'min' => 100, 'max' => 2000 ],
					'vh' => [ 'min' => 10, 'max' => 100 ],
				],
				'default'    => [ 'size' => $options['height'] ? (int) $options['height'] : 500, 'unit' => 'px' ],
				'condition'  => [ 'pixels_core_popup_height_type' => 'custom' ],
				'selectors'  => [
					'.pixels-core-theme-popup-editor-preview .pixels-core-theme-popup__dialog' => '--pixels-core-popup-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$document->add_responsive_control(
			'pixels_core_popup_horizontal_position',
			[
				'label'   => esc_html__( 'Horizontal Position', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'default' => $this->popup_flex_to_horizontal_position( $options['horizontal_position'] ),
				'options' => [
					'left'   => [ 'title' => esc_html__( 'Left', 'pixels-core-creative-tools-for-elementor' ), 'icon' => 'eicon-h-align-left' ],
					'center' => [ 'title' => esc_html__( 'Center', 'pixels-core-creative-tools-for-elementor' ), 'icon' => 'eicon-h-align-center' ],
					'right'  => [ 'title' => esc_html__( 'Right', 'pixels-core-creative-tools-for-elementor' ), 'icon' => 'eicon-h-align-right' ],
				],
				'render_type' => 'template',
			]
		);

		$document->add_responsive_control(
			'pixels_core_popup_vertical_position',
			[
				'label'   => esc_html__( 'Vertical Position', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'default' => $this->popup_flex_to_vertical_position( $options['vertical_position'] ),
				'options' => [
					'top'    => [ 'title' => esc_html__( 'Top', 'pixels-core-creative-tools-for-elementor' ), 'icon' => 'eicon-v-align-top' ],
					'center' => [ 'title' => esc_html__( 'Center', 'pixels-core-creative-tools-for-elementor' ), 'icon' => 'eicon-v-align-middle' ],
					'bottom' => [ 'title' => esc_html__( 'Bottom', 'pixels-core-creative-tools-for-elementor' ), 'icon' => 'eicon-v-align-bottom' ],
				],
				'render_type' => 'template',
			]
		);

		$document->end_controls_section();

		$document->start_controls_section(
			'pixels_core_popup_settings_section',
			[
				'label' => esc_html__( 'Pixels Popup Settings', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		foreach ( $this->get_popup_trigger_options() as $key => $trigger_options ) {
			$document->add_control(
				'pixels_core_popup_trigger_' . $key,
				[
					'label'        => $trigger_options['label'],
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'pixels-core-creative-tools-for-elementor' ),
					'label_off'    => esc_html__( 'No', 'pixels-core-creative-tools-for-elementor' ),
					'return_value' => 'yes',
					'default'      => $options[ 'trigger_' . $key ],
					'render_type'  => 'template',
				]
			);

			foreach ( $trigger_options['controls'] as $control_key => $control_options ) {
				$document->add_control(
					'pixels_core_popup_' . $control_key,
					array_merge(
						$control_options,
						[
							'default'     => $options[ $control_key ],
							'condition'   => [ 'pixels_core_popup_trigger_' . $key => 'yes' ],
							'render_type' => 'template',
						]
					)
				);
			}
		}

		$document->add_control(
			'pixels_core_popup_show_overlay',
			[
				'label'        => esc_html__( 'Overlay', 'pixels-core-creative-tools-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'pixels-core-creative-tools-for-elementor' ),
				'return_value' => 'yes',
				'default'      => $options['show_overlay'],
				'render_type'  => 'template',
			]
		);

		$document->add_control(
			'pixels_core_popup_overlay_color',
			[
				'label'       => esc_html__( 'Overlay Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::COLOR,
				'default'     => $options['overlay_color'],
				'condition'   => [ 'pixels_core_popup_show_overlay' => 'yes' ],
				'selectors'   => [
					'.pixels-core-theme-popup-editor-preview .pixels-core-theme-popup__overlay' => '--pixels-core-popup-overlay-color: {{VALUE}};',
				],
				'render_type' => 'template',
			]
		);

		$document->add_control(
			'pixels_core_popup_show_close_button',
			[
				'label'        => esc_html__( 'Close Button', 'pixels-core-creative-tools-for-elementor' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'pixels-core-creative-tools-for-elementor' ),
				'return_value' => 'yes',
				'default'      => $options['show_close_button'],
				'render_type'  => 'template',
			]
		);

		foreach ( [ 'entrance' => esc_html__( 'Entrance Animation', 'pixels-core-creative-tools-for-elementor' ), 'exit' => esc_html__( 'Exit Animation', 'pixels-core-creative-tools-for-elementor' ) ] as $key => $label ) {
			$document->add_control(
				'pixels_core_popup_' . $key . '_animation',
				[
					'label'   => $label,
					'type'    => \Elementor\Controls_Manager::SELECT,
					'default' => $options[ $key . '_animation' ],
					'options' => $this->get_popup_animation_options(),
					'render_type' => 'template',
				]
			);
		}

		$document->add_control(
			'pixels_core_popup_auto_close_after',
			[
				'label'   => esc_html__( 'Automatically Close After (sec)', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'min'     => 0,
				'step'    => 0.1,
				'default' => $options['auto_close_after'],
				'render_type' => 'template',
			]
		);

		$document->end_controls_section();

		$document->start_controls_section(
			'pixels_core_popup_close_button_style_section',
			[
				'label' => esc_html__( 'Pixels Popup Close Button', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$document->add_control(
			'pixels_core_popup_close_button_placement',
			[
				'label'       => esc_html__( 'Placement', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => $options['close_button_placement'],
				'options'     => [
					'inside'  => esc_html__( 'Inside Dialog', 'pixels-core-creative-tools-for-elementor' ),
					'outside' => esc_html__( 'Outside Dialog', 'pixels-core-creative-tools-for-elementor' ),
				],
				'condition'   => [ 'pixels_core_popup_show_close_button' => 'yes' ],
				'render_type' => 'template',
			]
		);

		$document->add_control(
			'pixels_core_popup_close_button_horizontal_position',
			[
				'label'       => esc_html__( 'Horizontal Position', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::CHOOSE,
				'default'     => $options['close_button_horizontal_position'],
				'options'     => [
					'left'  => [ 'title' => esc_html__( 'Left', 'pixels-core-creative-tools-for-elementor' ), 'icon' => 'eicon-h-align-left' ],
					'right' => [ 'title' => esc_html__( 'Right', 'pixels-core-creative-tools-for-elementor' ), 'icon' => 'eicon-h-align-right' ],
				],
				'condition'   => [ 'pixels_core_popup_show_close_button' => 'yes' ],
				'render_type' => 'template',
			]
		);

		$document->add_control(
			'pixels_core_popup_close_button_vertical_position',
			[
				'label'       => esc_html__( 'Vertical Position', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::CHOOSE,
				'default'     => $options['close_button_vertical_position'],
				'options'     => [
					'top'    => [ 'title' => esc_html__( 'Top', 'pixels-core-creative-tools-for-elementor' ), 'icon' => 'eicon-v-align-top' ],
					'bottom' => [ 'title' => esc_html__( 'Bottom', 'pixels-core-creative-tools-for-elementor' ), 'icon' => 'eicon-v-align-bottom' ],
				],
				'condition'   => [ 'pixels_core_popup_show_close_button' => 'yes' ],
				'render_type' => 'template',
			]
		);

		$document->add_control(
			'pixels_core_popup_close_button_delay',
			[
				'label'       => esc_html__( 'Show After (sec)', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'min'         => 0,
				'step'        => 0.1,
				'default'     => $options['close_button_delay'],
				'condition'   => [ 'pixels_core_popup_show_close_button' => 'yes' ],
				'render_type' => 'template',
			]
		);

		foreach ( [
			'close_button_offset_x'      => esc_html__( 'Horizontal Offset', 'pixels-core-creative-tools-for-elementor' ),
			'close_button_offset_y'      => esc_html__( 'Vertical Offset', 'pixels-core-creative-tools-for-elementor' ),
			'close_button_size'          => esc_html__( 'Button Size', 'pixels-core-creative-tools-for-elementor' ),
			'close_button_icon_size'     => esc_html__( 'Icon Size', 'pixels-core-creative-tools-for-elementor' ),
			'close_button_border_width'  => esc_html__( 'Border Width', 'pixels-core-creative-tools-for-elementor' ),
			'close_button_border_radius' => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
		] as $key => $label ) {
			$document->add_control(
				'pixels_core_popup_' . $key,
				[
					'label'       => $label,
					'type'        => \Elementor\Controls_Manager::SLIDER,
					'size_units'  => [ 'px' ],
					'range'       => [
						'px' => [ 'min' => 0, 'max' => in_array( $key, [ 'close_button_offset_x', 'close_button_offset_y' ], true ) ? 100 : 200 ],
					],
					'default'     => $this->popup_css_size_to_dimension( $options[ $key ], (int) $options[ $key ], 'px' ),
					'condition'   => [ 'pixels_core_popup_show_close_button' => 'yes' ],
					'render_type' => 'template',
				]
			);
		}

		foreach ( [
			'close_button_color'            => esc_html__( 'Icon Color', 'pixels-core-creative-tools-for-elementor' ),
			'close_button_background_color' => esc_html__( 'Background Color', 'pixels-core-creative-tools-for-elementor' ),
			'close_button_border_color'     => esc_html__( 'Border Color', 'pixels-core-creative-tools-for-elementor' ),
		] as $key => $label ) {
			$document->add_control(
				'pixels_core_popup_' . $key,
				[
					'label'       => $label,
					'type'        => \Elementor\Controls_Manager::COLOR,
					'default'     => $options[ $key ],
					'condition'   => [ 'pixels_core_popup_show_close_button' => 'yes' ],
					'render_type' => 'template',
				]
			);
		}

		$document->end_controls_section();

		$document->start_controls_section(
			'pixels_core_popup_advanced_section',
			[
				'label' => esc_html__( 'Pixels Popup Advanced', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		foreach ( [
			'prevent_overlay_close' => esc_html__( 'Prevent Closing on Overlay', 'pixels-core-creative-tools-for-elementor' ),
			'prevent_esc_close'     => esc_html__( 'Prevent Closing on ESC key', 'pixels-core-creative-tools-for-elementor' ),
			'disable_scroll'        => esc_html__( 'Disable Page Scrolling', 'pixels-core-creative-tools-for-elementor' ),
			'avoid_multiple'        => esc_html__( 'Avoid Multiple Popups', 'pixels-core-creative-tools-for-elementor' ),
			'accessible_navigation' => esc_html__( 'Accessible Navigation', 'pixels-core-creative-tools-for-elementor' ),
		] as $key => $label ) {
			$document->add_control(
				'pixels_core_popup_' . $key,
				[
					'label'        => $label,
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'pixels-core-creative-tools-for-elementor' ),
					'label_off'    => esc_html__( 'No', 'pixels-core-creative-tools-for-elementor' ),
					'return_value' => 'yes',
					'default'      => $options[ $key ],
					'render_type'  => 'template',
				]
			);
		}

		$document->add_control(
			'pixels_core_popup_open_selector',
			[
				'label'       => esc_html__( 'Open By Selector', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => $options['open_selector'],
				'placeholder' => '#id, .class',
				'description' => esc_html__( 'Clicking this selector opens the popup.', 'pixels-core-creative-tools-for-elementor' ),
				'render_type' => 'template',
			]
		);

		

		$document->add_control(
			'pixels_core_popup_margin',
			[
				'label'      => esc_html__( 'Margin', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'default'    => $this->popup_spacing_to_dimensions( $options['margin'] ),
				'selectors'  => [
					'.pixels-core-theme-popup-editor-preview .pixels-core-theme-popup__dialog' => '--pixels-core-popup-margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$document->add_control(
			'pixels_core_popup_padding',
			[
				'label'      => esc_html__( 'Content Padding', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'default'    => $this->popup_spacing_to_dimensions( $options['padding'] ),
				'selectors'  => [
					'.pixels-core-theme-popup-editor-preview .pixels-core-theme-popup__content' => '--pixels-core-popup-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		foreach ( [
			'content_background_color' => esc_html__( 'Content Background Color', 'pixels-core-creative-tools-for-elementor' ),
			'content_border_color'     => esc_html__( 'Content Border Color', 'pixels-core-creative-tools-for-elementor' ),
		] as $key => $label ) {
			$document->add_control(
				'pixels_core_popup_' . $key,
				[
					'label'       => $label,
					'type'        => \Elementor\Controls_Manager::COLOR,
					'default'     => $options[ $key ],
					'selectors'   => [
						'.pixels-core-theme-popup-editor-preview .pixels-core-theme-popup__content' => ( 'content_background_color' === $key ? '--pixels-core-popup-content-background-color' : '--pixels-core-popup-content-border-color' ) . ': {{VALUE}};',
					],
					'render_type' => 'template',
				]
			);
		}

		$document->add_control(
			'pixels_core_popup_content_border_style',
			[
				'label'       => esc_html__( 'Content Border Style', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => $options['content_border_style'],
				'options'     => [
					'none'   => esc_html__( 'None', 'pixels-core-creative-tools-for-elementor' ),
					'solid'  => esc_html__( 'Solid', 'pixels-core-creative-tools-for-elementor' ),
					'dashed' => esc_html__( 'Dashed', 'pixels-core-creative-tools-for-elementor' ),
					'dotted' => esc_html__( 'Dotted', 'pixels-core-creative-tools-for-elementor' ),
					'double' => esc_html__( 'Double', 'pixels-core-creative-tools-for-elementor' ),
				],
				'selectors'   => [
					'.pixels-core-theme-popup-editor-preview .pixels-core-theme-popup__content' => '--pixels-core-popup-content-border-style: {{VALUE}};',
				],
				'render_type' => 'template',
			]
		);

		foreach ( [
			'content_border_width'  => esc_html__( 'Content Border Width', 'pixels-core-creative-tools-for-elementor' ),
			'content_border_radius' => esc_html__( 'Content Border Radius', 'pixels-core-creative-tools-for-elementor' ),
		] as $key => $label ) {
			$document->add_control(
				'pixels_core_popup_' . $key,
				[
					'label'       => $label,
					'type'        => \Elementor\Controls_Manager::SLIDER,
					'size_units'  => [ 'px' ],
					'range'       => [
						'px' => [ 'min' => 0, 'max' => 200 ],
					],
					'default'     => $this->popup_css_size_to_dimension( $options[ $key ], (int) $options[ $key ], 'px' ),
					'selectors'   => [
						'.pixels-core-theme-popup-editor-preview .pixels-core-theme-popup__content' => ( 'content_border_width' === $key ? '--pixels-core-popup-content-border-width' : '--pixels-core-popup-content-border-radius' ) . ': {{SIZE}}{{UNIT}};',
					],
					'render_type' => 'template',
				]
			);
		}

		$document->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'pixels_core_popup_content_box_shadow',
				'label'    => esc_html__( 'Content Box Shadow', 'pixels-core-creative-tools-for-elementor' ),
				'selector' => '.pixels-core-theme-popup-editor-preview .pixels-core-theme-popup__content',
			]
		);

		$document->add_control(
			'pixels_core_popup_css_classes',
			[
				'label'   => esc_html__( 'CSS Classes', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => $options['css_classes'],
				'render_type' => 'template',
			]
		);

		$document->end_controls_section();
	}

	public function register_mega_menu_document_controls( $document ) {
		if ( ! is_object( $document ) || ! method_exists( $document, 'get_main_post' ) ) {
			return;
		}

		$post = $document->get_main_post();

		if ( ! $post || 'pixels-core-theme' !== $post->post_type || 'type_mega_menu' !== pixels_core_get_theme_template_type( $post->ID ) ) {
			return;
		}

		$options = $this->get_mega_menu_options( $post->ID );

		$document->start_controls_section(
			'pixels_core_mega_menu_layout_section',
			[
				'label' => esc_html__( 'Pixels Mega Menu Layout', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$document->add_control(
			'pixels_core_mega_menu_width_type',
			[
				'label'   => esc_html__( 'Width Type', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => $options['width_type'],
				'options' => [
					'custom'     => esc_html__( 'Custom Width', 'pixels-core-creative-tools-for-elementor' ),
					'container'  => esc_html__( 'Container Width', 'pixels-core-creative-tools-for-elementor' ),
					'full_width' => esc_html__( 'Full Screen Width', 'pixels-core-creative-tools-for-elementor' ),
				],
			]
		);

		$document->add_responsive_control(
			'pixels_core_mega_menu_width',
			[
				'label'      => esc_html__( 'Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'vw' ],
				'range'      => [
					'px' => [ 'min' => 100, 'max' => 2000 ],
					'%'  => [ 'min' => 10, 'max' => 100 ],
					'vw' => [ 'min' => 10, 'max' => 100 ],
				],
				'default'    => $options['width'],
				'condition'  => [
					'pixels_core_mega_menu_width_type' => 'custom',
				],
			]
		);

		$document->add_responsive_control(
			'pixels_core_mega_menu_container_width',
			[
				'label'      => esc_html__( 'Container Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vw' ],
				'range'      => [
					'px' => [ 'min' => 320, 'max' => 2000 ],
					'vw' => [ 'min' => 10, 'max' => 100 ],
				],
				'default'    => $options['container_width'],
				'condition'  => [
					'pixels_core_mega_menu_width_type' => 'container',
				],
			]
		);

		$document->end_controls_section();
	}

	public function preserve_popup_position_settings_on_save( $data, $document ) {
		if ( ! is_object( $document ) || ! method_exists( $document, 'get_main_id' ) ) {
			return $data;
		}

		$post_id = (int) $document->get_main_id();

		if ( ! $post_id || 'pixels-core-theme' !== get_post_type( $post_id ) || 'type_popup' !== pixels_core_get_theme_template_type( $post_id ) ) {
			return $data;
		}

		if ( empty( $data['settings'] ) || ! is_array( $data['settings'] ) ) {
			return $data;
		}

		$old_settings = get_post_meta( $post_id, '_elementor_page_settings', true );
		$old_settings = is_array( $old_settings ) ? $old_settings : [];

		foreach ( $old_settings as $key => $value ) {
			if ( 0 !== strpos( $key, 'pixels_core_popup_' ) ) {
				continue;
			}

			if ( ! array_key_exists( $key, $data['settings'] ) ) {
				$data['settings'][ $key ] = $value;
			}
		}

		return $data;
	}

	/**
	 * Register preview post selector for Single Post templates.
	 *
	 * @param \Elementor\Core\Base\Document $document Elementor document.
	 */
	public function register_single_post_preview_controls( $document ) {
		if ( ! is_object( $document ) || ! method_exists( $document, 'get_main_post' ) ) {
			return;
		}

		$post = $document->get_main_post();

		if ( ! $post || 'pixels-core-theme' !== $post->post_type || 'type_single_post' !== pixels_core_get_theme_template_type( $post->ID ) ) {
			return;
		}

		$preview_post_id = (int) pixels_core_get_theme_builder_preview_post_id( $post->ID );
		$page_settings   = get_post_meta( $post->ID, '_elementor_page_settings', true );

		if ( is_array( $page_settings ) && ! empty( $page_settings['pixels_core_preview_post_id'] ) ) {
			$preview_post_id = (int) $page_settings['pixels_core_preview_post_id'];
		}

		$document->start_controls_section(
			'pixels_core_single_post_preview_section',
			[
				'label' => esc_html__( 'Preview', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => \Elementor\Controls_Manager::TAB_SETTINGS,
			]
		);

		$document->add_control(
			'pixels_core_preview_post_id',
			[
				'label'       => esc_html__( 'Preview with Post', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => $preview_post_id ? (string) $preview_post_id : '',
				'options'     => pixels_core_get_theme_builder_preview_post_select_options(),
				'description' => esc_html__( 'Choose a post to preview dynamic widgets. The editor preview stays on this template.', 'pixels-core-creative-tools-for-elementor' ),
			]
		);

		$document->end_controls_section();
	}

	/**
	 * Persist preview post selection when saving from Elementor.
	 *
	 * @param array                         $data     Document save data.
	 * @param \Elementor\Core\Base\Document $document Elementor document.
	 * @return array
	 */
	public function save_template_preview_post_on_document_save( $data, $document ) {
		if ( ! is_object( $document ) || ! method_exists( $document, 'get_main_id' ) ) {
			return $data;
		}

		$post_id = (int) $document->get_main_id();

		if ( ! $post_id || 'pixels-core-theme' !== get_post_type( $post_id ) ) {
			return $data;
		}

		$template_type = pixels_core_get_theme_template_type( $post_id );

		if ( ! in_array( $template_type, [ 'type_single_post', 'type_archive_post' ], true ) ) {
			return $data;
		}

		if ( empty( $data['settings'] ) || ! is_array( $data['settings'] ) || ! array_key_exists( 'pixels_core_preview_post_id', $data['settings'] ) ) {
			return $data;
		}

		$preview_post_id = absint( $data['settings']['pixels_core_preview_post_id'] );

		if ( $preview_post_id > 0 ) {
			update_post_meta( $post_id, '_pixels_core_preview_post_id', $preview_post_id );
		} else {
			delete_post_meta( $post_id, '_pixels_core_preview_post_id' );
		}

		return $data;
	}

	/**
	 * Enqueue styles and scripts.
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'pixels-core-theme-style', PIXELS_CORE_URL . 'assets/css/pixels-core-header-footer.css', [], PIXELS_CORE_VERSION );
		wp_enqueue_script( 'pixels-core-mega-menu', PIXELS_CORE_URL . 'assets/js/pixels-core-mega-menu.js', [], PIXELS_CORE_VERSION, true );

		if ( class_exists( '\Elementor\Plugin' ) ) :
			$elementor = \Elementor\Plugin::instance();
			$elementor->frontend->enqueue_styles();
			$elementor->frontend->enqueue_scripts();
		endif;

		if ( class_exists( '\ElementorPro\Plugin' ) ) :
			$elementor_pro = \ElementorPro\Plugin::instance();
			$elementor_pro->enqueue_styles();
		endif;

		if ( is_singular( 'pixels-core-theme' ) && 'type_popup' === pixels_core_get_theme_template_type( get_the_ID() ) ) :
			wp_enqueue_style( 'pixels-core-theme-popup', PIXELS_CORE_URL . 'assets/theme-builder/css/pixels-core-theme-popup.css', [], PIXELS_CORE_VERSION );
		endif;

		if ( pixels_core_header_enabled() ) :
			if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) :
				$css_file = new \Elementor\Core\Files\CSS\Post( pixels_core_get_header_id() );
			elseif ( class_exists( '\Elementor\Post_CSS_File' ) ) :
				$css_file = new \Elementor\Post_CSS_File( pixels_core_get_header_id() );
			endif;

			$css_file->enqueue();
		endif;

		if ( pixels_core_footer_enabled() ) :
			if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) :
				$css_file = new \Elementor\Core\Files\CSS\Post( pixels_core_get_footer_id() );
			elseif ( class_exists( '\Elementor\Post_CSS_File' ) ) :
				$css_file = new \Elementor\Post_CSS_File( pixels_core_get_footer_id() );
			endif;

			$css_file->enqueue();
		endif;

		$mega_menu_ids = $this->get_mega_menu_template_ids();

		foreach ( $mega_menu_ids as $mega_menu_id ) :
			$this->enqueue_template_css( $mega_menu_id );
			self::$elementor_instance->frontend->get_builder_content_for_display( $mega_menu_id, false );
		endforeach;

		$popup_ids = self::get_template_ids( 'type_popup' );

		if ( ! empty( $popup_ids ) ) :
			wp_enqueue_style( 'pixels-core-theme-popup', PIXELS_CORE_URL . 'assets/theme-builder/css/pixels-core-theme-popup.css', [], PIXELS_CORE_VERSION );
			wp_enqueue_script( 'pixels-core-theme-popup', PIXELS_CORE_URL . 'assets/theme-builder/js/pixels-core-theme-popup.js', [], PIXELS_CORE_VERSION, true );

			foreach ( $popup_ids as $popup_id ) :
				if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) :
					$css_file = new \Elementor\Core\Files\CSS\Post( $popup_id );
				elseif ( class_exists( '\Elementor\Post_CSS_File' ) ) :
					$css_file = new \Elementor\Post_CSS_File( $popup_id );
				else :
					$css_file = null;
				endif;

				if ( $css_file ) :
					$css_file->enqueue();
				endif;

				self::$elementor_instance->frontend->get_builder_content_for_display( $popup_id, false );
			endforeach;
		endif;
	}

	/**
	 * Load admin styles on header footer elementor edit screen.
	 */
	public function enqueue_admin_scripts() {
		global $pagenow;
		$screen = get_current_screen();

		if ( ( 'pixels-core-theme' == $screen->id && ( 'post.php' == $pagenow || 'post-new.php' == $pagenow ) ) || ( 'edit.php' == $pagenow && 'edit-pixels-core-theme' == $screen->id ) ) :
			wp_enqueue_style( 'pixels-core-theme-admin-style', PIXELS_CORE_URL . 'assets/theme-builder/css/pixels-core-theme-admin.css', [], PIXELS_CORE_VERSION );
			wp_enqueue_script( 'pixels-core-theme-admin-script', PIXELS_CORE_URL . 'assets/theme-builder/js/pixels-core-theme-admin.js', [], PIXELS_CORE_VERSION, true );
		endif;

		$this->enqueue_popup_editor_live_preview();
	}

	public function enqueue_popup_editor_live_preview() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only check to decide which editor assets to enqueue.
		$post_id = isset( $_GET['post'] ) ? absint( wp_unslash( $_GET['post'] ) ) : 0;

		if ( $post_id && 'pixels-core-theme' === get_post_type( $post_id ) && 'type_popup' === pixels_core_get_theme_template_type( $post_id ) ) :
			wp_enqueue_script(
				'pixels-core-theme-popup-editor',
				PIXELS_CORE_URL . 'assets/theme-builder/js/pixels-core-theme-popup-editor.js',
				[ 'jquery' ],
				PIXELS_CORE_VERSION,
				true
			);
		endif;
	}

	/**
	 * Enqueue preview post selector script for Single Post templates.
	 */
	public function enqueue_single_post_preview_editor_script() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only check to decide which editor assets to enqueue.
		$post_id = isset( $_GET['post'] ) ? absint( wp_unslash( $_GET['post'] ) ) : 0;

		if ( ! $post_id || 'pixels-core-theme' !== get_post_type( $post_id ) || 'type_single_post' !== pixels_core_get_theme_template_type( $post_id ) ) {
			return;
		}

		wp_enqueue_script(
			'pixels-core-theme-single-preview',
			PIXELS_CORE_URL . 'assets/theme-builder/js/pixels-core-theme-single-preview.js',
			[ 'jquery' ],
			PIXELS_CORE_VERSION,
			true
		);
	}

	/**
	 * Adds classes to the body tag conditionally.
	 *
	 * @param  Array $classes array with class names for the body tag.
	 *
	 * @return Array          array with class names for the body tag.
	 */
	public function body_class( $classes ) {
		if ( pixels_core_header_enabled() ) :
			$classes[] = 'pixels-core-theme-header';
		endif;

		if ( pixels_core_footer_enabled() ) :
			$classes[] = 'pixels-core-theme-footer';
		endif;

		$classes[] = 'pixels-core-theme-template-' . $this->template;
		$classes[] = 'pixels-core-theme-stylesheet-' . get_stylesheet();

		return $classes;
	}

	/**
	 * Display Unsupported theme notice if the current theme does add support for 'pixels-core'
	 *
	 */
	public function setup_unsupported_theme() {
		if ( ! current_theme_supports( 'pixels-core' ) ) :
			require_once PIXELS_CORE_PATH . 'includes/theme-builder/themes/default/class-pixels-default-compat.php';
		endif;
	}

	/**
	 * Prints the Header content.
	 */
	public static function get_header_content() {
		$header_content = self::$elementor_instance->frontend->get_builder_content_for_display( pixels_core_get_header_id() );

		if ( ! empty( $header_content ) ) {
			echo wp_kses( $header_content, pixels_core_get_builder_allowed_html() );
		}
	}

	/**
	 * Prints the Footer content.
	 */
	public static function get_footer_content() {
		$footer_content = self::$elementor_instance->frontend->get_builder_content_for_display( pixels_core_get_footer_id() );

		if ( ! empty( $footer_content ) ) {
			echo wp_kses( $footer_content, pixels_core_get_builder_allowed_html() );
		}
	}

	/**
	 * Get all published Mega Menu template IDs.
	 *
	 * @return array
	 */
	private function get_mega_menu_template_ids() {
		return array_map(
			'absint',
			get_posts(
				[
					'post_type'      => 'pixels-core-theme',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'fields'         => 'ids',
					'meta_key'       => pixels_core_get_theme_template_type_meta_key(), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					'meta_value'     => 'type_mega_menu', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				]
			)
		);
	}

	/**
	 * Enqueue Elementor CSS for a Theme Builder template.
	 *
	 * @param int $template_id Template post ID.
	 */
	private function enqueue_template_css( $template_id ) {
		$template_id = absint( $template_id );

		if ( ! $template_id ) :
			return;
		endif;

		if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) :
			$css_file = new \Elementor\Core\Files\CSS\Post( $template_id );
		elseif ( class_exists( '\Elementor\Post_CSS_File' ) ) :
			$css_file = new \Elementor\Post_CSS_File( $template_id );
		else :
			$css_file = null;
		endif;

		if ( $css_file ) :
			$css_file->enqueue();
		endif;
	}

	/**
	 * Get the Mega Menu template ID attached to a nav menu item.
	 *
	 * @param \WP_Post $item Menu item object.
	 *
	 * @return int
	 */
	private function get_mega_menu_template_id_from_item( $item ) {
		if ( ! is_object( $item ) || 'post_type' !== $item->type || 'pixels-core-theme' !== $item->object ) :
			return 0;
		endif;

		$template_id = absint( $item->object_id );

		if ( ! $template_id || 'type_mega_menu' !== pixels_core_get_theme_template_type( $template_id ) ) :
			return 0;
		endif;

		return $template_id;
	}

	/**
	 * Get Mega Menu layout options from Elementor page settings.
	 *
	 * @param int $template_id Template post ID.
	 *
	 * @return array
	 */
	private function get_mega_menu_options( $template_id ) {
		$settings = get_post_meta( absint( $template_id ), '_elementor_page_settings', true );
		$settings = is_array( $settings ) ? $settings : [];
		$width_type = isset( $settings['pixels_core_mega_menu_width_type'] ) ? sanitize_key( $settings['pixels_core_mega_menu_width_type'] ) : 'custom';

		if ( ! in_array( $width_type, [ 'custom', 'container', 'full_width' ], true ) ) :
			$width_type = 'custom';
		endif;

		return [
			'width_type'      => $width_type,
			'width'           => $this->normalize_mega_menu_responsive_width(
				$settings,
				'pixels_core_mega_menu_width',
				[
					'size' => 100,
					'unit' => '%',
				],
				[ 'px', '%', 'vw' ],
				[
					'px' => [ 100, 2000 ],
					'%'  => [ 10, 100 ],
					'vw' => [ 10, 100 ],
				]
			),
			'container_width' => $this->normalize_mega_menu_responsive_width(
				$settings,
				'pixels_core_mega_menu_container_width',
				[
					'size' => 1140,
					'unit' => 'px',
				],
				[ 'px', 'vw' ],
				[
					'px' => [ 320, 2000 ],
					'vw' => [ 10, 100 ],
				]
			),
		];
	}

	/**
	 * Normalize responsive Elementor slider values for inline CSS usage.
	 *
	 * @param array  $settings Settings array.
	 * @param string $key      Setting key.
	 * @param array  $default  Default slider value.
	 * @param array  $units    Allowed units.
	 * @param array  $ranges   Unit ranges.
	 *
	 * @return array
	 */
	private function normalize_mega_menu_responsive_width( $settings, $key, $default, $units, $ranges ) {
		return [
			'desktop' => $this->normalize_mega_menu_width_value(
				isset( $settings[ $key ] ) ? $settings[ $key ] : [],
				$default,
				$units,
				$ranges
			),
			'tablet'  => $this->normalize_mega_menu_width_value(
				isset( $settings[ $key . '_tablet' ] ) ? $settings[ $key . '_tablet' ] : [],
				$default,
				$units,
				$ranges
			),
			'mobile'  => $this->normalize_mega_menu_width_value(
				isset( $settings[ $key . '_mobile' ] ) ? $settings[ $key . '_mobile' ] : [],
				$default,
				$units,
				$ranges
			),
		];
	}

	/**
	 * Normalize one Elementor slider value.
	 *
	 * @param mixed $width   Width setting.
	 * @param array $default Default slider value.
	 * @param array $units   Allowed units.
	 * @param array $ranges  Unit ranges.
	 *
	 * @return array
	 */
	private function normalize_mega_menu_width_value( $width, $default, $units, $ranges ) {
		if ( ! is_array( $width ) || ! isset( $width['size'], $width['unit'] ) ) :
			return $default;
		endif;

		$unit = in_array( $width['unit'], $units, true ) ? $width['unit'] : $default['unit'];
		$size = is_numeric( $width['size'] ) ? (float) $width['size'] : $default['size'];
		$range = isset( $ranges[ $unit ] ) ? $ranges[ $unit ] : [ 0, 2000 ];

		$size = max( $range[0], min( $range[1], $size ) );

		return [
			'size' => $size,
			'unit' => $unit,
		];
	}

	/**
	 * Build inline style for a rendered Mega Menu wrapper.
	 *
	 * @param int $template_id Template post ID.
	 *
	 * @return string
	 */
	private function get_mega_menu_wrapper_style( $template_id ) {
		$options = $this->get_mega_menu_options( $template_id );

		$width = [
			'desktop' => $options['width']['desktop']['size'] . $options['width']['desktop']['unit'],
			'tablet'  => $options['width']['tablet']['size'] . $options['width']['tablet']['unit'],
			'mobile'  => $options['width']['mobile']['size'] . $options['width']['mobile']['unit'],
		];

		$container_width = [
			'desktop' => $options['container_width']['desktop']['size'] . $options['container_width']['desktop']['unit'],
			'tablet'  => $options['container_width']['tablet']['size'] . $options['container_width']['tablet']['unit'],
			'mobile'  => $options['container_width']['mobile']['size'] . $options['container_width']['mobile']['unit'],
		];

		return sprintf(
			'--pixels-core-mega-menu-width:%1$s;--pixels-core-mega-menu-width-tablet:%2$s;--pixels-core-mega-menu-width-mobile:%3$s;--pixels-core-mega-menu-container-width:%4$s;--pixels-core-mega-menu-container-width-tablet:%5$s;--pixels-core-mega-menu-container-width-mobile:%6$s;',
			esc_attr( $width['desktop'] ),
			esc_attr( $width['tablet'] ),
			esc_attr( $width['mobile'] ),
			esc_attr( $container_width['desktop'] ),
			esc_attr( $container_width['tablet'] ),
			esc_attr( $container_width['mobile'] )
		);
	}

	/**
	 * Get layout class for a Mega Menu wrapper.
	 *
	 * @param int $template_id Template post ID.
	 *
	 * @return string
	 */
	private function get_mega_menu_layout_class( $template_id ) {
		$options = $this->get_mega_menu_options( $template_id );

		return 'pixels-core-mega-menu-layout-' . sanitize_html_class( $options['width_type'] );
	}

	/**
	 * Add classes to nav items that render a Mega Menu template.
	 *
	 * @param array    $classes Menu item classes.
	 * @param \WP_Post $item    Menu item object.
	 *
	 * @return array
	 */
	public function add_mega_menu_item_classes( $classes, $item, $args = null, $depth = 0 ) {
		if ( $this->get_mega_menu_template_id_from_item( $item ) ) :
			$classes[] = 'pixels-core-mega-menu-item';

			if ( 0 === (int) $depth ) :
				$classes[] = 'menu-item-has-children';
			else :
				$classes[] = 'pixels-core-mega-menu-content-item';
			endif;
		endif;

		return $classes;
	}

	/**
	 * Add accessibility attributes to Mega Menu links.
	 *
	 * @param array    $atts Link attributes.
	 * @param \WP_Post $item Menu item object.
	 *
	 * @return array
	 */
	public function add_mega_menu_link_attributes( $atts, $item, $args = null, $depth = 0 ) {
		$template_id = $this->get_mega_menu_template_id_from_item( $item );

		if ( ! $template_id || 0 < (int) $depth ) :
			return $atts;
		endif;

		$panel_id = 'pixels-core-mega-menu-panel-' . absint( $item->ID );

		$atts['href']          = '#' . $panel_id;
		$atts['aria-haspopup'] = 'true';
		$atts['aria-expanded'] = 'false';
		$atts['aria-controls'] = $panel_id;

		return $atts;
	}

	/**
	 * Append Elementor Mega Menu content to matching nav menu items.
	 *
	 * @param string   $item_output Menu item HTML.
	 * @param \WP_Post $item        Menu item object.
	 *
	 * @return string
	 */
	public function render_mega_menu_panel( $item_output, $item, $depth = 0, $args = null ) {
		$template_id = $this->get_mega_menu_template_id_from_item( $item );

		if ( ! $template_id ) :
			return $item_output;
		endif;

		$mega_menu_content = self::$elementor_instance->frontend->get_builder_content_for_display( $template_id );

		if ( empty( $mega_menu_content ) ) :
			return $item_output;
		endif;

		$panel_id = 'pixels-core-mega-menu-panel-' . absint( $item->ID );
		$style    = $this->get_mega_menu_wrapper_style( $template_id );
		$layout_class = $this->get_mega_menu_layout_class( $template_id );

		$mega_menu_content = wp_kses( $mega_menu_content, pixels_core_get_builder_allowed_html() );

		if ( 0 < (int) $depth ) :
			return sprintf(
				'<div id="%1$s" class="pixels-core-mega-menu-content %2$s" role="region" aria-label="%3$s" style="%4$s">%5$s</div>',
				esc_attr( $panel_id ),
				esc_attr( $layout_class ),
				esc_attr( get_the_title( $template_id ) ),
				esc_attr( $style ),
				$mega_menu_content
			);
		endif;

		$item_output .= sprintf(
			'<div id="%1$s" class="pixels-core-mega-menu-panel %2$s" role="region" aria-label="%3$s" style="%4$s">%5$s</div>',
			esc_attr( $panel_id ),
			esc_attr( $layout_class ),
			esc_attr( get_the_title( $template_id ) ),
			esc_attr( $style ),
			$mega_menu_content
		);

		return $item_output;
	}

	/**
	 * Get option for the plugin settings
	 *
	 * @param  mixed $setting Option name.
	 * @param  mixed $default Default value to be received if the option value is not stored in the option.
	 *
	 * @return mixed.
	 */
	public static function get_settings( $setting = '', $default = '' ) {
		if ( 'type_header' == $setting || 'type_footer' == $setting || 'type_archive_post' == $setting || 'type_404' == $setting || 'type_popup' == $setting ) :
			$templates = self::get_template_id( $setting );

			$template = ! is_array( $templates ) ? $templates : $templates[0];

			$template = apply_filters( "pixels_core_hf_get_settings_{$setting}", $template );

			return $template;
		endif;
	}

	/**
	 * Get header or footer template id based on the meta query.
	 *
	 * @param  String $type Type of the template header/footer.
	 *
	 * @return Mixed       Returns the header or footer template id if found, else returns string ''.
	 */
	public static function get_template_id( $type ) {
		$template_ids = self::get_template_ids( $type );

		return ! empty( $template_ids ) ? $template_ids[0] : '';
	}

	/**
	 * Get template ids based on the meta query.
	 *
	 * @param String $type Type of template.
	 *
	 * @return array
	 */
	public static function get_template_ids( $type ) {
		$option = [
			'location'  => 'pixels_core_hf_target_include_locations',
			'exclusion' => 'pixels_core_hf_target_exclude_locations'
		];

		$pixels_core_hf_templates = Target_Rules_Fields::get_instance()->get_posts_by_conditions( 'pixels-core-theme', $option );
		$template_ids        = [];

		foreach ( $pixels_core_hf_templates as $template ) :
			if ( pixels_core_get_theme_template_type( absint( $template['id'] ) ) === $type ) :
				$template_ids[] = absint( $template['id'] );
			endif;
		endforeach;

		return $template_ids;
	}

	/**
	 * Render matching popup templates at the end of the page.
	 */
	public function render_popups() {
		if ( is_singular( 'pixels-core-theme' ) && 'type_popup' === pixels_core_get_theme_template_type( get_the_ID() ) ) :
			return;
		endif;

		$popup_ids = self::get_template_ids( 'type_popup' );

		foreach ( $popup_ids as $popup_id ) :
			$options  = $this->get_popup_options( $popup_id );
			$triggers = $this->get_enabled_popup_triggers( $options );

			$classes = [
				'pixels-core-theme-popup',
				'pixels-core-theme-popup-h-' . $options['horizontal_position'],
				'pixels-core-theme-popup-v-' . $options['vertical_position'],
				'pixels-core-theme-popup-enter-' . $options['entrance_animation'],
				'pixels-core-theme-popup-exit-' . $options['exit_animation'],
			];

			if ( ! empty( $options['css_classes'] ) ) :
				$classes = array_merge( $classes, array_filter( array_map( 'sanitize_html_class', explode( ' ', $options['css_classes'] ) ) ) );
			endif;

			$dialog_style       = $this->get_popup_dialog_style( $options );
			$overlay_style      = $this->get_popup_overlay_style( $options );
			$content_style      = $this->get_popup_content_style( $options );
			$close_button_style = $this->get_popup_close_button_style( $options );
			?>
			<?php $this->enqueue_popup_responsive_css( '#pixels-core-theme-popup-' . absint( $popup_id ), $popup_id, $options ); ?>
			<div
				class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
				id="pixels-core-theme-popup-<?php echo esc_attr( (string) $popup_id ); ?>"
				data-pixels-core-popup
				data-popup-id="<?php echo esc_attr( (string) $popup_id ); ?>"
				data-triggers="<?php echo esc_attr( implode( ',', $triggers ) ); ?>"
				data-trigger="<?php echo esc_attr( in_array( 'page_load', $triggers, true ) ? 'page_load' : 'manual' ); ?>"
				data-delay="<?php echo esc_attr( (string) absint( $options['delay'] ) ); ?>"
				data-page-load-delay="<?php echo esc_attr( (string) $options['page_load_delay'] ); ?>"
				data-scroll-direction="<?php echo esc_attr( $options['scroll_direction'] ); ?>"
				data-scroll-percent="<?php echo esc_attr( (string) $options['scroll_percent'] ); ?>"
				data-scroll-to-selector="<?php echo esc_attr( (string) $options['scroll_to_selector'] ); ?>"
				data-class-click-selector="<?php echo esc_attr( (string) $options['class_click_selector'] ); ?>"
				data-click-count="<?php echo esc_attr( (string) $options['click_count'] ); ?>"
				data-inactivity-delay="<?php echo esc_attr( (string) $options['inactivity_delay'] ); ?>"
				data-adblock-delay="<?php echo esc_attr( (string) $options['adblock_delay'] ); ?>"
				data-open-selector="<?php echo esc_attr( (string) $options['open_selector'] ); ?>"
				data-auto-close-after="<?php echo esc_attr( (string) $options['auto_close_after'] ); ?>"
				data-close-button-delay="<?php echo esc_attr( (string) $options['close_button_delay'] ); ?>"
				data-prevent-overlay-close="<?php echo esc_attr( $options['prevent_overlay_close'] ); ?>"
				data-prevent-esc-close="<?php echo esc_attr( $options['prevent_esc_close'] ); ?>"
				data-disable-scroll="<?php echo esc_attr( $options['disable_scroll'] ); ?>"
				data-avoid-multiple="<?php echo esc_attr( $options['avoid_multiple'] ); ?>"
				data-accessible-navigation="<?php echo esc_attr( $options['accessible_navigation'] ); ?>"
				aria-hidden="true"
			>
				<?php if ( 'yes' === $options['show_overlay'] ) : ?>
					<div class="pixels-core-theme-popup__overlay" <?php echo 'yes' === $options['prevent_overlay_close'] ? '' : 'data-pixels-core-popup-close'; ?> style="<?php echo esc_attr( $overlay_style ); ?>"></div>
				<?php endif; ?>
				<div class="pixels-core-theme-popup__dialog" role="dialog" aria-modal="true" tabindex="-1" style="<?php echo esc_attr( $dialog_style ); ?>">
					<?php if ( 'yes' === $options['show_close_button'] ) : ?>
						<button type="button" class="pixels-core-theme-popup__close" data-pixels-core-popup-close aria-label="<?php esc_attr_e( 'Close popup', 'pixels-core-creative-tools-for-elementor' ); ?>" style="<?php echo esc_attr( $close_button_style ); ?>">
							<span aria-hidden="true"></span>
						</button>
					<?php endif; ?>
					<div class="pixels-core-theme-popup__content" style="<?php echo esc_attr( $content_style ); ?>">
						<?php
						$popup_content = self::$elementor_instance->frontend->get_builder_content_for_display( $popup_id );

						if ( ! empty( $popup_content ) ) :
							echo wp_kses( $popup_content, pixels_core_get_builder_allowed_html() );
						endif;
						?>
					</div>
				</div>
			</div>
			<?php
		endforeach;
	}

	public function render_popup_editor_template() {
		$popup_id = get_the_ID();

		if ( ! $popup_id ) :
			return;
		endif;

		$options = $this->get_popup_options( $popup_id );
		$classes = [
			'pixels-core-theme-popup',
			'pixels-core-theme-popup-editor-preview',
			'pixels-core-theme-popup-h-' . $options['horizontal_position'],
			'pixels-core-theme-popup-v-' . $options['vertical_position'],
			'is-open',
		];

		$dialog_style       = $this->get_popup_dialog_style( $options );
		$overlay_style      = $this->get_popup_overlay_style( $options );
		$content_style      = $this->get_popup_content_style( $options );
		$close_button_style = $this->get_popup_close_button_style( $options );
		?>
		<?php $this->enqueue_popup_responsive_css( '#pixels-core-theme-popup-editor-preview-' . absint( $popup_id ), $popup_id, $options ); ?>
		<div id="pixels-core-theme-popup-editor-preview-<?php echo esc_attr( (string) $popup_id ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" aria-hidden="false">
			<div class="pixels-core-theme-popup__overlay" style="<?php echo esc_attr( $overlay_style . ( 'yes' === $options['show_overlay'] ? '' : 'display:none;' ) ); ?>"></div>
			<div class="pixels-core-theme-popup__dialog" role="dialog" aria-modal="true" tabindex="-1" style="<?php echo esc_attr( $dialog_style ); ?>">
				<button type="button" class="pixels-core-theme-popup__close" aria-label="<?php esc_attr_e( 'Close popup', 'pixels-core-creative-tools-for-elementor' ); ?>" style="<?php echo esc_attr( $close_button_style . ( 'yes' === $options['show_close_button'] ? '' : 'display:none;' ) ); ?>">
					<span aria-hidden="true"></span>
				</button>
				<div class="pixels-core-theme-popup__content" style="<?php echo esc_attr( $content_style ); ?>">
					<?php
					$module = apply_filters( 'elementor/render_mode/module', 'page-templates' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Elementor core hook.
					\Elementor\Plugin::$instance->modules_manager->get_modules( $module )->print_content();
					?>
				</div>
			</div>
		</div>
		<?php
	}

	public function open_popup_editor_wrapper() {
		$popup_id = get_the_ID();

		if ( ! $popup_id || 'pixels-core-theme' !== get_post_type( $popup_id ) || 'type_popup' !== pixels_core_get_theme_template_type( $popup_id ) ) :
			return;
		endif;

		$options = $this->get_popup_options( $popup_id );
		$classes = [
			'pixels-core-theme-popup',
			'pixels-core-theme-popup-editor-preview',
			'pixels-core-theme-popup-h-' . $options['horizontal_position'],
			'pixels-core-theme-popup-v-' . $options['vertical_position'],
			'is-open',
		];
		$dialog_style       = $this->get_popup_dialog_style( $options );
		$overlay_style      = $this->get_popup_overlay_style( $options );
		$content_style      = $this->get_popup_content_style( $options );
		$close_button_style = $this->get_popup_close_button_style( $options );
		?>
		<?php $this->enqueue_popup_responsive_css( '#pixels-core-theme-popup-editor-preview-' . absint( $popup_id ), $popup_id, $options ); ?>
		<div id="pixels-core-theme-popup-editor-preview-<?php echo esc_attr( (string) $popup_id ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" aria-hidden="false">
			<div class="pixels-core-theme-popup__overlay" style="<?php echo esc_attr( $overlay_style . ( 'yes' === $options['show_overlay'] ? '' : 'display:none;' ) ); ?>"></div>
			<div class="pixels-core-theme-popup__dialog" role="dialog" aria-modal="true" tabindex="-1" style="<?php echo esc_attr( $dialog_style ); ?>">
				<button type="button" class="pixels-core-theme-popup__close" aria-label="<?php esc_attr_e( 'Close popup', 'pixels-core-creative-tools-for-elementor' ); ?>" style="<?php echo esc_attr( $close_button_style . ( 'yes' === $options['show_close_button'] ? '' : 'display:none;' ) ); ?>">
					<span aria-hidden="true"></span>
				</button>
				<div class="pixels-core-theme-popup__content" style="<?php echo esc_attr( $content_style ); ?>">
		<?php
	}

	public function close_popup_editor_wrapper() {
		$popup_id = get_the_ID();

		if ( ! $popup_id || 'pixels-core-theme' !== get_post_type( $popup_id ) || 'type_popup' !== pixels_core_get_theme_template_type( $popup_id ) ) :
			return;
		endif;
		?>
				</div>
			</div>
		</div>
		<?php
	}

	private function get_default_popup_options() {
		return [
			'width'                  => 420,
			'trigger'                => 'page_load',
			'delay'                  => 0,
			'trigger_page_load'      => 'yes',
			'page_load_delay'        => 0,
			'trigger_scroll'         => 'no',
			'scroll_direction'       => 'down',
			'scroll_percent'         => 50,
			'trigger_scroll_to'      => 'no',
			'scroll_to_selector'     => '',
			'trigger_class_click'    => 'no',
			'class_click_selector'   => '',
			'trigger_click'          => 'no',
			'click_count'            => 1,
			'trigger_inactivity'     => 'no',
			'inactivity_delay'       => 30,
			'trigger_exit_intent'    => 'no',
			'trigger_adblock'        => 'no',
			'adblock_delay'          => 0,
			'height_type'            => 'fit',
			'height'                 => '',
			'horizontal_position'    => 'center',
			'vertical_position'      => 'center',
			'show_overlay'           => 'yes',
			'overlay_color'          => 'rgba(0, 0, 0, 0.78)',
			'show_close_button'      => 'yes',
			'close_button_placement' => 'inside',
			'close_button_horizontal_position' => 'right',
			'close_button_vertical_position' => 'top',
			'close_button_offset_x'  => '11px',
			'close_button_offset_y'  => '11px',
			'close_button_size'      => '18px',
			'close_button_icon_size' => '20px',
			'close_button_color'     => '#303030',
			'close_button_background_color' => 'transparent',
			'close_button_border_color' => 'transparent',
			'close_button_border_width' => '0px',
			'close_button_border_radius' => '0px',
			'entrance_animation'     => 'default',
			'exit_animation'         => 'default',
			'close_button_delay'     => '',
			'auto_close_after'       => '',
			'prevent_overlay_close'  => 'no',
			'prevent_esc_close'      => 'no',
			'disable_scroll'         => 'yes',
			'avoid_multiple'         => 'no',
			'accessible_navigation'  => 'yes',
			'open_selector'          => '',
			'margin'                 => [ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0 ],
			'padding'                => [ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0 ],
			'content_background_color' => '#fff',
			'content_border_color'   => '#dcdcdc',
			'content_border_style'   => 'dashed',
			'content_border_width'   => '1px',
			'content_border_radius'  => '0px',
			'content_box_shadow'     => 'none',
			'css_classes'            => '',
		];
	}

	private function get_popup_options( $popup_id ) {
		$options = get_post_meta( $popup_id, 'pixels_core_popup_options', true );
		$options = wp_parse_args( is_array( $options ) ? $options : [], $this->get_default_popup_options() );
		$page_settings = get_post_meta( $popup_id, '_elementor_page_settings', true );
		$page_settings = is_array( $page_settings ) ? $page_settings : [];

		$options['trigger'] = ! empty( $page_settings['pixels_core_popup_trigger'] )
			? sanitize_key( $page_settings['pixels_core_popup_trigger'] )
			: ( get_post_meta( $popup_id, 'pixels_core_popup_trigger', true ) ?: $options['trigger'] );

		$options['delay'] = isset( $page_settings['pixels_core_popup_delay'] )
			? absint( $page_settings['pixels_core_popup_delay'] )
			: absint( get_post_meta( $popup_id, 'pixels_core_popup_delay', true ) );

		$options['page_load_delay'] = isset( $page_settings['pixels_core_popup_page_load_delay'] )
			? max( 0, (float) $page_settings['pixels_core_popup_page_load_delay'] )
			: $options['page_load_delay'];

		if ( empty( $page_settings['pixels_core_popup_page_load_delay'] ) && ! empty( $options['delay'] ) ) {
			$options['page_load_delay'] = $options['delay'] / 1000;
		}

		foreach ( array_keys( $this->get_popup_trigger_options() ) as $key ) {
			$setting_key = 'pixels_core_popup_trigger_' . $key;

			if ( array_key_exists( $setting_key, $page_settings ) ) {
				$options[ 'trigger_' . $key ] = 'yes' === $page_settings[ $setting_key ] ? 'yes' : 'no';
			}
		}

		if ( ! array_key_exists( 'pixels_core_popup_trigger_page_load', $page_settings ) && 'manual' === $options['trigger'] ) {
			$options['trigger_page_load'] = 'no';
		}

		$options['width']  = $this->popup_dimension_to_css( $page_settings['pixels_core_popup_width'] ?? null, $options['width'] . 'px' );
		$options['height'] = $this->popup_dimension_to_css( $page_settings['pixels_core_popup_height'] ?? null, $options['height'] ? $options['height'] . 'px' : '' );

		foreach ( [
			'height_type',
			'entrance_animation',
			'exit_animation',
			'close_button_delay',
			'auto_close_after',
			'scroll_direction',
			'scroll_to_selector',
			'class_click_selector',
			'overlay_color',
			'close_button_placement',
			'close_button_horizontal_position',
			'close_button_vertical_position',
			'close_button_color',
			'close_button_background_color',
			'close_button_border_color',
			'content_background_color',
			'content_border_color',
			'content_border_style',
			'open_selector',
			'css_classes',
		] as $key ) {
			$setting_key = 'pixels_core_popup_' . $key;

			if ( array_key_exists( $setting_key, $page_settings ) && '' !== $page_settings[ $setting_key ] ) {
				$options[ $key ] = $page_settings[ $setting_key ];
			}
		}

		foreach ( [
			'scroll_percent'     => [ 0, 100 ],
			'click_count'        => [ 1, 100 ],
			'inactivity_delay'   => [ 0, 3600 ],
			'adblock_delay'      => [ 0, 3600 ],
			'page_load_delay'    => [ 0, 3600 ],
		] as $key => $range ) {
			$setting_key = 'pixels_core_popup_' . $key;

			if ( array_key_exists( $setting_key, $page_settings ) && '' !== $page_settings[ $setting_key ] ) {
				$options[ $key ] = min( $range[1], max( $range[0], (float) $page_settings[ $setting_key ] ) );
			}
		}

		$options['scroll_direction'] = in_array( $options['scroll_direction'], [ 'down', 'up' ], true ) ? $options['scroll_direction'] : 'down';
		$options['close_button_placement'] = in_array( $options['close_button_placement'], [ 'inside', 'outside' ], true ) ? $options['close_button_placement'] : 'inside';
		$options['close_button_horizontal_position'] = in_array( $options['close_button_horizontal_position'], [ 'left', 'right' ], true ) ? $options['close_button_horizontal_position'] : 'right';
		$options['close_button_vertical_position'] = in_array( $options['close_button_vertical_position'], [ 'top', 'bottom' ], true ) ? $options['close_button_vertical_position'] : 'top';
		$options['content_border_style'] = in_array( $options['content_border_style'], [ 'none', 'solid', 'dashed', 'dotted', 'double' ], true ) ? $options['content_border_style'] : 'dashed';

		foreach ( [ 'close_button_offset_x', 'close_button_offset_y', 'close_button_size', 'close_button_icon_size', 'close_button_border_width', 'close_button_border_radius', 'content_border_width', 'content_border_radius' ] as $key ) {
			$setting_key = 'pixels_core_popup_' . $key;

			if ( array_key_exists( $setting_key, $page_settings ) ) {
				$options[ $key ] = $this->popup_dimension_to_css( $page_settings[ $setting_key ], $options[ $key ] );
			}
		}

		if ( array_key_exists( 'pixels_core_popup_horizontal_position', $page_settings ) && '' !== $page_settings['pixels_core_popup_horizontal_position'] ) {
			$options['horizontal_position'] = $this->popup_flex_to_horizontal_position( $page_settings['pixels_core_popup_horizontal_position'] );
		}

		if ( array_key_exists( 'pixels_core_popup_vertical_position', $page_settings ) && '' !== $page_settings['pixels_core_popup_vertical_position'] ) {
			$options['vertical_position'] = $this->popup_flex_to_vertical_position( $page_settings['pixels_core_popup_vertical_position'] );
		}

		foreach ( [ 'show_overlay', 'show_close_button', 'prevent_overlay_close', 'prevent_esc_close', 'disable_scroll', 'avoid_multiple', 'accessible_navigation' ] as $key ) {
			$setting_key = 'pixels_core_popup_' . $key;

			if ( array_key_exists( $setting_key, $page_settings ) ) {
				$options[ $key ] = 'yes' === $page_settings[ $setting_key ] ? 'yes' : 'no';
			}
		}

		if ( isset( $page_settings['pixels_core_popup_margin'] ) ) {
			$options['margin'] = $this->popup_dimensions_to_spacing( $page_settings['pixels_core_popup_margin'] );
		}

		if ( isset( $page_settings['pixels_core_popup_padding'] ) ) {
			$options['padding'] = $this->popup_dimensions_to_spacing( $page_settings['pixels_core_popup_padding'] );
		}

		if ( array_key_exists( 'pixels_core_popup_content_box_shadow_box_shadow_type', $page_settings ) ) {
			$options['content_box_shadow'] = $this->popup_box_shadow_to_css(
				$page_settings['pixels_core_popup_content_box_shadow_box_shadow'] ?? null,
				$page_settings['pixels_core_popup_content_box_shadow_box_shadow_type']
			);
		}

		$options['margin']  = wp_parse_args( is_array( $options['margin'] ) ? $options['margin'] : [], [ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0 ] );
		$options['padding'] = wp_parse_args( is_array( $options['padding'] ) ? $options['padding'] : [], [ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0 ] );

		return $options;
	}

	private function enqueue_popup_responsive_css( $selector, $popup_id, $options ) {
		$page_settings = get_post_meta( $popup_id, '_elementor_page_settings', true );
		$page_settings = is_array( $page_settings ) ? $page_settings : [];
		$selector      = preg_replace( '/[^#.\-_a-zA-Z0-9]/', '', (string) $selector );

		if ( empty( $selector ) ) {
			return;
		}

		$desktop_width      = $this->sanitize_popup_css_size( $options['width'], '420px' );
		$desktop_horizontal = $this->popup_horizontal_position_to_flex( $options['horizontal_position'] );
		$desktop_vertical   = $this->popup_vertical_position_to_flex( $options['vertical_position'] );
		$css                = sprintf(
			'%1$s{justify-content:%2$s;align-items:%3$s;}%1$s .pixels-core-theme-popup__dialog{--pixels-core-popup-width:%4$s;}',
			$selector,
			$desktop_horizontal,
			$desktop_vertical,
			$desktop_width
		);

		$responsive_devices = [
			'tablet' => '1024px',
			'mobile' => '767px',
		];

		foreach ( $responsive_devices as $device => $breakpoint ) {
			$breakpoint = $this->sanitize_popup_css_size( $breakpoint, '' );

			if ( '' === $breakpoint ) {
				continue;
			}

			$device_width      = $this->sanitize_popup_css_size(
				$this->popup_dimension_to_css( $page_settings[ 'pixels_core_popup_width_' . $device ] ?? null, '' ),
				''
			);
			$device_horizontal = $this->popup_get_responsive_position( $page_settings, 'pixels_core_popup_horizontal_position_' . $device, 'horizontal' );
			$device_vertical   = $this->popup_get_responsive_position( $page_settings, 'pixels_core_popup_vertical_position_' . $device, 'vertical' );
			$device_css        = '';

			if ( $device_horizontal ) {
				$device_css .= sprintf( 'justify-content:%s;', $device_horizontal );
			}

			if ( $device_vertical ) {
				$device_css .= sprintf( 'align-items:%s;', $device_vertical );
			}

			if ( $device_css ) {
				$css .= sprintf( '@media (max-width:%1$s){%2$s{%3$s}}', $breakpoint, $selector, $device_css );
			}

			if ( $device_width ) {
				$css .= sprintf(
					'@media (max-width:%1$s){%2$s .pixels-core-theme-popup__dialog{--pixels-core-popup-width:%3$s;}}',
					$breakpoint,
					$selector,
					$device_width
				);
			}
		}

		$handle = 'pixels-core-theme-popup-' . absint( $popup_id );
		wp_register_style( $handle, false, [], PIXELS_CORE_VERSION );
		wp_enqueue_style( $handle );
		wp_add_inline_style( $handle, $css );
	}

	private function popup_get_responsive_position( $settings, $key, $axis ) {
		if ( empty( $settings[ $key ] ) ) {
			return '';
		}

		if ( 'vertical' === $axis ) {
			return $this->popup_vertical_position_to_flex( $settings[ $key ] );
		}

		return $this->popup_horizontal_position_to_flex( $settings[ $key ] );
	}

	private function get_popup_trigger_options() {
		return [
			'page_load'   => [
				'label'    => esc_html__( 'On Page Load', 'pixels-core-creative-tools-for-elementor' ),
				'controls' => [
					'page_load_delay' => [
						'label' => esc_html__( 'Within (sec)', 'pixels-core-creative-tools-for-elementor' ),
						'type'  => \Elementor\Controls_Manager::NUMBER,
						'min'   => 0,
						'step'  => 0.1,
					],
				],
			],
			'scroll'      => [
				'label'    => esc_html__( 'On Scroll', 'pixels-core-creative-tools-for-elementor' ),
				'controls' => [
					'scroll_direction' => [
						'label'   => esc_html__( 'Direction', 'pixels-core-creative-tools-for-elementor' ),
						'type'    => \Elementor\Controls_Manager::SELECT,
						'options' => [
							'down' => esc_html__( 'Down', 'pixels-core-creative-tools-for-elementor' ),
							'up'   => esc_html__( 'Up', 'pixels-core-creative-tools-for-elementor' ),
						],
					],
					'scroll_percent'   => [
						'label' => esc_html__( 'Within (%)', 'pixels-core-creative-tools-for-elementor' ),
						'type'  => \Elementor\Controls_Manager::NUMBER,
						'min'   => 0,
						'max'   => 100,
						'step'  => 1,
					],
				],
			],
			'scroll_to'   => [
				'label'    => esc_html__( 'On Scroll To Element', 'pixels-core-creative-tools-for-elementor' ),
				'controls' => [
					'scroll_to_selector' => [
						'label'       => esc_html__( 'Selector', 'pixels-core-creative-tools-for-elementor' ),
						'type'        => \Elementor\Controls_Manager::TEXT,
						'placeholder' => '.my-class',
					],
				],
			],
			'class_click' => [
				'label'    => esc_html__( 'On Class Click', 'pixels-core-creative-tools-for-elementor' ),
				'controls' => [
					'class_click_selector' => [
						'label'       => esc_html__( 'Class / Selector', 'pixels-core-creative-tools-for-elementor' ),
						'type'        => \Elementor\Controls_Manager::TEXT,
						'placeholder' => '.my-class',
					],
				],
			],
			'click'       => [
				'label'    => esc_html__( 'On Click', 'pixels-core-creative-tools-for-elementor' ),
				'controls' => [
					'click_count' => [
						'label' => esc_html__( 'Clicks', 'pixels-core-creative-tools-for-elementor' ),
						'type'  => \Elementor\Controls_Manager::NUMBER,
						'min'   => 1,
						'step'  => 1,
					],
				],
			],
			'inactivity'  => [
				'label'    => esc_html__( 'After Inactivity', 'pixels-core-creative-tools-for-elementor' ),
				'controls' => [
					'inactivity_delay' => [
						'label' => esc_html__( 'Within (sec)', 'pixels-core-creative-tools-for-elementor' ),
						'type'  => \Elementor\Controls_Manager::NUMBER,
						'min'   => 0,
						'step'  => 1,
					],
				],
			],
			'exit_intent' => [
				'label'    => esc_html__( 'On Page Exit Intent', 'pixels-core-creative-tools-for-elementor' ),
				'controls' => [],
			],
			'adblock'     => [
				'label'    => esc_html__( 'AdBlock Detection', 'pixels-core-creative-tools-for-elementor' ),
				'controls' => [
					'adblock_delay' => [
						'label' => esc_html__( 'Within (sec)', 'pixels-core-creative-tools-for-elementor' ),
						'type'  => \Elementor\Controls_Manager::NUMBER,
						'min'   => 0,
						'step'  => 1,
					],
				],
			],
		];
	}

	private function get_enabled_popup_triggers( $options ) {
		$triggers = [];

		foreach ( array_keys( $this->get_popup_trigger_options() ) as $key ) {
			if ( 'yes' === ( $options[ 'trigger_' . $key ] ?? 'no' ) ) {
				$triggers[] = $key;
			}
		}

		return $triggers;
	}

	private function get_popup_animation_options() {
		return [
			'default'     => esc_html__( 'Default', 'pixels-core-creative-tools-for-elementor' ),
			'fade'        => esc_html__( 'Fade', 'pixels-core-creative-tools-for-elementor' ),
			'zoom'        => esc_html__( 'Zoom', 'pixels-core-creative-tools-for-elementor' ),
			'slide-up'    => esc_html__( 'Slide Up', 'pixels-core-creative-tools-for-elementor' ),
			'slide-down'  => esc_html__( 'Slide Down', 'pixels-core-creative-tools-for-elementor' ),
			'slide-left'  => esc_html__( 'Slide Left', 'pixels-core-creative-tools-for-elementor' ),
			'slide-right' => esc_html__( 'Slide Right', 'pixels-core-creative-tools-for-elementor' ),
		];
	}

	private function popup_spacing_to_dimensions( $spacing ) {
		$spacing = wp_parse_args( is_array( $spacing ) ? $spacing : [], [ 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0 ] );

		return [
			'top'      => (string) $spacing['top'],
			'right'    => (string) $spacing['right'],
			'bottom'   => (string) $spacing['bottom'],
			'left'     => (string) $spacing['left'],
			'unit'     => 'px',
			'isLinked' => false,
		];
	}

	private function popup_dimensions_to_spacing( $dimensions ) {
		$dimensions = is_array( $dimensions ) ? $dimensions : [];
		$unit       = ! empty( $dimensions['unit'] ) ? $dimensions['unit'] : 'px';

		return [
			'top'    => ( $dimensions['top'] ?? 0 ) . $unit,
			'right'  => ( $dimensions['right'] ?? 0 ) . $unit,
			'bottom' => ( $dimensions['bottom'] ?? 0 ) . $unit,
			'left'   => ( $dimensions['left'] ?? 0 ) . $unit,
		];
	}

	private function popup_dimension_to_css( $dimension, $fallback ) {
		if ( ! is_array( $dimension ) || ! isset( $dimension['size'] ) || '' === $dimension['size'] ) {
			return $fallback;
		}

		$unit           = ! empty( $dimension['unit'] ) ? (string) $dimension['unit'] : 'px';
		$allowed_units  = [ 'px', '%', 'vw', 'vh', 'em', 'rem' ];

		if ( ! in_array( $unit, $allowed_units, true ) ) {
			$unit = 'px';
		}

		return $this->sanitize_popup_css_size( $dimension['size'] . $unit, $fallback );
	}

	private function popup_box_shadow_to_css( $shadow, $type ) {
		if ( 'yes' !== $type || ! is_array( $shadow ) ) {
			return 'none';
		}

		$horizontal = $this->popup_box_shadow_length_to_css( $shadow['horizontal'] ?? 0 );
		$vertical   = $this->popup_box_shadow_length_to_css( $shadow['vertical'] ?? 0 );
		$blur       = $this->popup_box_shadow_length_to_css( $shadow['blur'] ?? 0 );
		$spread     = $this->popup_box_shadow_length_to_css( $shadow['spread'] ?? 0 );
		$color      = $this->sanitize_popup_color( $shadow['color'] ?? 'rgba(0, 0, 0, 0.5)', 'rgba(0, 0, 0, 0.5)' );
		$position   = ! empty( $shadow['position'] ) && 'inset' === $shadow['position'] ? 'inset ' : '';

		return $position . implode( ' ', [ $horizontal, $vertical, $blur, $spread, $color ] );
	}

	private function popup_box_shadow_length_to_css( $value ) {
		if ( is_array( $value ) ) {
			return $this->popup_dimension_to_css( $value, '0px' );
		}

		if ( is_numeric( $value ) ) {
			return $value . 'px';
		}

		return $this->sanitize_popup_css_size( $value, '0px' );
	}

	private function popup_css_size_to_dimension( $value, $default_size, $default_unit = 'px' ) {
		$value = (string) $value;

		if ( preg_match( '/^(\d+(?:\.\d+)?)(px|%|vw|vh|em|rem)$/', $value, $matches ) ) {
			return [
				'size' => (float) $matches[1],
				'unit' => $matches[2],
			];
		}

		return [
			'size' => $default_size,
			'unit' => $default_unit,
		];
	}

	private function popup_horizontal_position_to_flex( $value ) {
		$allowed = [ 'flex-start', 'center', 'flex-end' ];

		if ( 'left' === $value || 'flex-start' === $value ) {
			$value = 'flex-start';
		} elseif ( 'right' === $value || 'flex-end' === $value ) {
			$value = 'flex-end';
		} else {
			$value = 'center';
		}

		return in_array( $value, $allowed, true ) ? $value : 'center';
	}

	private function popup_vertical_position_to_flex( $value ) {
		$allowed = [ 'flex-start', 'center', 'flex-end' ];

		if ( 'top' === $value || 'flex-start' === $value ) {
			$value = 'flex-start';
		} elseif ( 'bottom' === $value || 'flex-end' === $value ) {
			$value = 'flex-end';
		} else {
			$value = 'center';
		}

		return in_array( $value, $allowed, true ) ? $value : 'center';
	}

	private function popup_flex_to_horizontal_position( $value ) {
		if ( 'flex-start' === $value || 'left' === $value ) {
			return 'left';
		}

		if ( 'flex-end' === $value || 'right' === $value ) {
			return 'right';
		}

		return 'center';
	}

	private function popup_flex_to_vertical_position( $value ) {
		if ( 'flex-start' === $value || 'top' === $value ) {
			return 'top';
		}

		if ( 'flex-end' === $value || 'bottom' === $value ) {
			return 'bottom';
		}

		return 'center';
	}

	private function get_popup_dialog_style( $options ) {
		$style = [
			'--pixels-core-popup-width:' . $this->sanitize_popup_css_size( $options['width'], '420px' ),
			'--pixels-core-popup-height:' . ( 'custom' === $options['height_type'] && $options['height'] ? $this->sanitize_popup_css_size( $options['height'], 'auto' ) : 'auto' ),
			'--pixels-core-popup-margin:' . implode(
				' ',
				[
					$this->sanitize_popup_css_size( $options['margin']['top'], '0px' ),
					$this->sanitize_popup_css_size( $options['margin']['right'], '0px' ),
					$this->sanitize_popup_css_size( $options['margin']['bottom'], '0px' ),
					$this->sanitize_popup_css_size( $options['margin']['left'], '0px' ),
				]
			),
		];

		return implode( ';', $style ) . ';';
	}

	private function get_popup_overlay_style( $options ) {
		return '--pixels-core-popup-overlay-color:' . $this->sanitize_popup_color( $options['overlay_color'], 'rgba(0, 0, 0, 0.78)' ) . ';';
	}

	private function get_popup_content_style( $options ) {
		$style = [
			'--pixels-core-popup-padding:' . implode(
				' ',
				[
					$this->sanitize_popup_css_size( $options['padding']['top'], '0px' ),
					$this->sanitize_popup_css_size( $options['padding']['right'], '0px' ),
					$this->sanitize_popup_css_size( $options['padding']['bottom'], '0px' ),
					$this->sanitize_popup_css_size( $options['padding']['left'], '0px' ),
				]
			),
			'--pixels-core-popup-content-background-color:' . $this->sanitize_popup_color( $options['content_background_color'], '#fff' ),
			'--pixels-core-popup-content-border-style:' . $this->sanitize_popup_border_style( $options['content_border_style'], 'dashed' ),
			'--pixels-core-popup-content-border-color:' . $this->sanitize_popup_color( $options['content_border_color'], '#dcdcdc' ),
			'--pixels-core-popup-content-border-width:' . $this->sanitize_popup_css_size( $options['content_border_width'], '1px' ),
			'--pixels-core-popup-content-border-radius:' . $this->sanitize_popup_css_size( $options['content_border_radius'], '0px' ),
			'--pixels-core-popup-content-box-shadow:' . $this->sanitize_popup_box_shadow( $options['content_box_shadow'], 'none' ),
		];

		return implode( ';', $style ) . ';';
	}

	private function get_popup_close_button_style( $options ) {
		$horizontal = in_array( $options['close_button_horizontal_position'], [ 'left', 'right' ], true ) ? $options['close_button_horizontal_position'] : 'right';
		$vertical   = in_array( $options['close_button_vertical_position'], [ 'top', 'bottom' ], true ) ? $options['close_button_vertical_position'] : 'top';
		$outside    = 'outside' === $options['close_button_placement'];
		$offset_x   = $this->sanitize_popup_css_size( $options['close_button_offset_x'], '11px' );
		$offset_y   = $this->sanitize_popup_css_size( $options['close_button_offset_y'], '11px' );

		$style = [
			'left:auto',
			'right:auto',
			'top:auto',
			'bottom:auto',
			$horizontal . ':' . ( $outside ? 'calc(' . $offset_x . ' * -1)' : $offset_x ),
			$vertical . ':' . ( $outside ? 'calc(' . $offset_y . ' * -1)' : $offset_y ),
			'width:' . $this->sanitize_popup_css_size( $options['close_button_size'], '18px' ),
			'height:' . $this->sanitize_popup_css_size( $options['close_button_size'], '18px' ),
			'font-size:' . $this->sanitize_popup_css_size( $options['close_button_icon_size'], '20px' ),
			'color:' . $this->sanitize_popup_color( $options['close_button_color'], '#303030' ),
			'background:' . $this->sanitize_popup_color( $options['close_button_background_color'], 'transparent' ),
			'border-style:solid',
			'border-color:' . $this->sanitize_popup_color( $options['close_button_border_color'], 'transparent' ),
			'border-width:' . $this->sanitize_popup_css_size( $options['close_button_border_width'], '0px' ),
			'border-radius:' . $this->sanitize_popup_css_size( $options['close_button_border_radius'], '0px' ),
		];

		return implode( ';', $style ) . ';';
	}

	private function sanitize_popup_css_size( $value, $fallback ) {
		if ( is_numeric( $value ) ) {
			return $value . 'px';
		}

		$value = (string) $value;

		if ( preg_match( '/^-?\d+(\.\d+)?(px|%|vw|vh|em|rem)$/', $value ) ) {
			return $value;
		}

		return $fallback;
	}

	private function sanitize_popup_color( $value, $fallback ) {
		$value = trim( (string) $value );

		if ( in_array( strtolower( $value ), [ 'transparent', 'currentcolor' ], true ) ) {
			return $value;
		}

		if ( function_exists( 'sanitize_hex_color' ) ) {
			$hex = sanitize_hex_color( $value );

			if ( $hex ) {
				return $hex;
			}
		}

		if ( preg_match( '/^rgba?\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}(?:\s*,\s*(?:0|1|0?\.\d+))?\s*\)$/', $value ) ) {
			return $value;
		}

		if ( preg_match( '/^var\(\s*--[a-zA-Z0-9_-]+\s*\)$/', $value ) ) {
			return $value;
		}

		return $fallback;
	}

	private function sanitize_popup_border_style( $value, $fallback ) {
		$value = sanitize_key( $value );

		return in_array( $value, [ 'none', 'solid', 'dashed', 'dotted', 'double' ], true ) ? $value : $fallback;
	}

	private function sanitize_popup_box_shadow( $value, $fallback ) {
		$value = trim( (string) $value );

		if ( 'none' === strtolower( $value ) ) {
			return 'none';
		}

		if ( preg_match( '/^(?:inset\s+)?-?\d+(?:\.\d+)?(?:px|em|rem)?\s+-?\d+(?:\.\d+)?(?:px|em|rem)?\s+\d+(?:\.\d+)?(?:px|em|rem)?\s+-?\d+(?:\.\d+)?(?:px|em|rem)?\s+(?:#[0-9a-fA-F]{3,8}|rgba?\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}(?:\s*,\s*(?:0|1|0?\.\d+))?\s*\))$/', $value ) ) {
			return $value;
		}

		return $fallback;
	}

	/**
	 * Callback to shortcode.
	 *
	 * @param array $atts attributes for shortcode.
	 */
	public function render_template( $atts ) {
		$atts = shortcode_atts(
			[
				'id' => '',
			],
			$atts,
			'pixels_core_hf_template'
		);

		$id = ! empty( $atts['id'] ) ? apply_filters( 'pixels_core_hf_render_template_id', absint( $atts['id'] ) ) : 0;

		if ( empty( $id ) ) {
			return '';
		}

		$post = get_post( $id );

		if ( ! $post || 'pixels-core-theme' !== $post->post_type ) {
			return '';
		}

		if ( ! in_array( $post->post_status, [ 'publish' ], true ) && ! current_user_can( 'edit_post', $id ) ) {
			return '';
		}

		if ( ! class_exists( '\Elementor\Plugin' ) || empty( self::$elementor_instance ) ) {
			return '';
		}

		if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
			$css_file = new \Elementor\Core\Files\CSS\Post( $id );
			$css_file->enqueue();
		} elseif ( class_exists( '\Elementor\Post_CSS_File' ) ) {
			$css_file = new \Elementor\Post_CSS_File( $id );
			$css_file->enqueue();
		}

		// Trusted Elementor builder markup for a validated pixels-core-theme CPT ID.
		return wp_kses(
			self::$elementor_instance->frontend->get_builder_content_for_display( $id ),
			pixels_core_get_builder_allowed_html()
		);
	}

	/**
	 * Use the popup editor template while editing or previewing popup templates.
	 *
	 * @param string $template Default template path.
	 *
	 * @return string
	 */
	public function maybe_use_popup_canvas_template( $template ) {
		if ( ! is_singular( 'pixels-core-theme' ) || is_admin() ) {
			return $template;
		}

		$post_id = get_the_ID();

		if ( ! $post_id || 'type_popup' !== pixels_core_get_theme_template_type( $post_id ) ) {
			return $template;
		}

		$popup_template = defined( 'ELEMENTOR_PATH' ) ? ELEMENTOR_PATH . 'modules/page-templates/templates/canvas.php' : '';

		return file_exists( $popup_template ) ? $popup_template : $template;
	}

	/**
	 * Maybe use the Elementor 404 template as the full page template.
	 *
	 * @param string $template Default template path.
	 *
	 * @return string
	 */
	public function maybe_use_404_template( $template ) {
		if ( ! is_404() || is_admin() ) {
			return $template;
		}

		$page_404 = pixels_core_get_404_id();

		if ( empty( $page_404 ) ) {
			return $template;
		}

		/**
		 * Filter the 404 template ID before rendering.
		 *
		 * @param int $page_404 Template post ID.
		 */
		$page_404 = apply_filters( 'pixels_core_404_template_id', $page_404 );

		if ( empty( $page_404 ) ) {
			return $template;
		}

		// Enqueue Elementor CSS for the 404 template so styles are available in the head.
		if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
			$css_file = new \Elementor\Core\Files\CSS\Post( $page_404 );
		} elseif ( class_exists( '\Elementor\Post_CSS_File' ) ) {
			$css_file = new \Elementor\Post_CSS_File( $page_404 );
		} else {
			$css_file = null;
		}

		if ( $css_file ) {
			$css_file->enqueue();
		}

		$not_found_template = PIXELS_CORE_PATH . 'includes/theme-builder/themes/default/pixels-core-theme-404.php';

		if ( file_exists( $not_found_template ) ) {
			return $not_found_template;
		}

		return $template;
	}

	/**
	 * Maybe use the Elementor Archive Post template as the full page template.
	 *
	 * This respects the display rules configured for templates of type `type_archive_post`.
	 *
	 * @param string $template Default template path.
	 *
	 * @return string
	 */
	public function maybe_use_archive_post_template( $template ) {
		if ( ( ! is_archive() && ! is_home() && ! is_search() ) || is_admin() ) {
			return $template;
		}

		$archive_id = self::get_template_id( 'type_archive_post' );

		if ( empty( $archive_id ) ) {
			return $template;
		}

		/**
		 * Filter the archive post template ID before rendering.
		 *
		 * @param int $archive_id Template post ID.
		 */
		$archive_id = apply_filters( 'pixels_core_archive_post_template_id', $archive_id );

		if ( empty( $archive_id ) ) {
			return $template;
		}

		// Enqueue Elementor CSS for the archive template so styles are available in the head.
		if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
			$css_file = new \Elementor\Core\Files\CSS\Post( $archive_id );
		} elseif ( class_exists( '\Elementor\Post_CSS_File' ) ) {
			$css_file = new \Elementor\Post_CSS_File( $archive_id );
		} else {
			$css_file = null;
		}

		if ( $css_file ) {
			$css_file->enqueue();
		}

		$archive_template = PIXELS_CORE_PATH . 'includes/theme-builder/themes/default/pixels-archive.php';

		if ( file_exists( $archive_template ) ) {
			return $archive_template;
		}

		return $template;
	}

	/**
	 * Maybe use the Elementor Single Post template as the full page template.
	 *
	 * This respects the display rules configured for templates of type `type_single_post`.
	 *
	 * @param string $template Default template path.
	 *
	 * @return string
	 */
	public function maybe_use_single_post_template( $template ) {
		// Only affect singular views on the frontend main query.
		if ( ! is_singular() || is_admin() ) {
			return $template;
		}

		// Never hijack the template post type itself.
		if ( is_singular( 'pixels-core-theme' ) ) {
			return $template;
		}

		// Get matching single post template based on display rules.
		$single_id = self::get_template_id( 'type_single_post' );

		if ( empty( $single_id ) ) {
			return $template;
		}

		/**
		 * Filter the single post template ID before rendering.
		 *
		 * @param int $single_id Template post ID.
		 */
		$single_id = apply_filters( 'pixels_core_single_post_template_id', $single_id );

		if ( empty( $single_id ) ) {
			return $template;
		}

		// Enqueue Elementor CSS for the single template so styles are available in the head.
		if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
			$css_file = new \Elementor\Core\Files\CSS\Post( $single_id );
		} elseif ( class_exists( '\Elementor\Post_CSS_File' ) ) {
			$css_file = new \Elementor\Post_CSS_File( $single_id );
		} else {
			$css_file = null;
		}

		if ( $css_file ) {
			$css_file->enqueue();
		}

		// Use our custom single template which outputs the full HTML markup.
		$single_template = PIXELS_CORE_PATH . 'includes/theme-builder/themes/default/pixels-single.php';

		if ( file_exists( $single_template ) ) {
			return $single_template;
		}

		return $template;
	}

}