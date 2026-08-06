<?php
namespace PixelsElementorAddons\Extensions;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Element_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Per-element custom JS in the Elementor Advanced tab.
 */
final class Custom_JS {

	public static function init(): void {
		add_action( 'elementor/element/after_section_end', [ __CLASS__, 'register_controls' ], 10, 2 );
		add_action( 'elementor/frontend/after_render', [ __CLASS__, 'print_element_js' ] );
		add_filter( 'elementor/document/save/data', [ __CLASS__, 'preserve_custom_js_for_non_admins' ], 10, 2 );
	}

	public static function register_controls( Controls_Stack $element, string $section_id ): void {
		if ( 'section_custom_css_pro' !== $section_id ) {
			return;
		}

		$element->start_controls_section(
			'pixels_section_custom_js',
			[
				'label' => esc_html__( 'Custom JS', 'pixels-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			]
		);

		$element->add_control(
			'pixels_custom_js_label',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => esc_html__( 'Add your own custom JS here', 'pixels-elementor-addons' ),
			]
		);

		$element->add_control(
			'pixels_custom_js',
			[
				'type'        => Controls_Manager::CODE,
				'show_label'  => false,
				'language'    => 'javascript',
				'render_type' => 'ui',
				'dynamic'     => [
					'active' => false,
				],
			]
		);

		if ( ! current_user_can( 'manage_options' ) ) {
			$element->add_control(
				'pixels_custom_js_permission_note',
				[
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( '<strong>Note:</strong> Only administrators can add or edit JavaScript from here.', 'pixels-elementor-addons' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				]
			);
		}

		$element->add_control(
			'pixels_custom_js_usage',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'You may use both jQuery selectors (e.g. $( ".selector" )) or vanilla JS (e.g. document.querySelector( ".selector" )).', 'pixels-elementor-addons' ),
				'content_classes' => 'elementor-descriptor',
			]
		);

		$element->end_controls_section();
	}

	public static function print_element_js( Element_Base $element ): void {
		$custom_js = $element->get_settings_for_display( 'pixels_custom_js' );

		if ( ! is_string( $custom_js ) ) {
			return;
		}

		$custom_js = trim( $custom_js );

		if ( '' === $custom_js ) {
			return;
		}

		printf(
			'<script id="pixels-custom-js-%1$s">%2$s</script>',
			esc_attr( $element->get_id() ),
			// Custom JS is intentionally output unescaped; only manage_options users can save it.
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			$custom_js . ';'
		);
	}

	/**
	 * Prevent non-admins from changing saved custom JS on any element.
	 *
	 * @param array                              $data     Document data being saved.
	 * @param \Elementor\Core\Base\Document|null $document Document instance.
	 * @return array
	 */
	public static function preserve_custom_js_for_non_admins( $data, $document = null ) {
		if ( current_user_can( 'manage_options' ) || empty( $data['elements'] ) || ! is_array( $data['elements'] ) ) {
			return $data;
		}

		$post_id = 0;

		if ( $document && method_exists( $document, 'get_main_id' ) ) {
			$post_id = (int) $document->get_main_id();
		}

		if ( $post_id < 1 ) {
			$post_id = (int) get_the_ID();
		}

		$existing_map = self::collect_existing_custom_js( $post_id );

		$data['elements'] = self::restore_custom_js_in_elements( $data['elements'], $existing_map );

		return $data;
	}

	/**
	 * @return array<string, string>
	 */
	private static function collect_existing_custom_js( int $post_id ): array {
		if ( $post_id < 1 || ! did_action( 'elementor/loaded' ) ) {
			return [];
		}

		$document = \Elementor\Plugin::$instance->documents->get( $post_id );

		if ( ! $document ) {
			return [];
		}

		$data = $document->get_elements_data();

		if ( empty( $data ) || ! is_array( $data ) ) {
			return [];
		}

		$map = [];
		self::walk_elements_for_custom_js( $data, $map );

		return $map;
	}

	/**
	 * @param array<int, array>   $elements Elements tree.
	 * @param array<string, string> $map     Element ID => custom JS.
	 */
	private static function walk_elements_for_custom_js( array $elements, array &$map ): void {
		foreach ( $elements as $element ) {
			if ( empty( $element['id'] ) ) {
				continue;
			}

			$id = (string) $element['id'];

			if ( isset( $element['settings']['pixels_custom_js'] ) && is_string( $element['settings']['pixels_custom_js'] ) ) {
				$map[ $id ] = $element['settings']['pixels_custom_js'];
			}

			if ( ! empty( $element['elements'] ) && is_array( $element['elements'] ) ) {
				self::walk_elements_for_custom_js( $element['elements'], $map );
			}
		}
	}

	/**
	 * @param array<int, array>     $elements Elements tree being saved.
	 * @param array<string, string> $existing Existing custom JS by element ID.
	 * @return array<int, array>
	 */
	private static function restore_custom_js_in_elements( array $elements, array $existing ): array {
		foreach ( $elements as &$element ) {
			if ( empty( $element['id'] ) ) {
				continue;
			}

			$id = (string) $element['id'];

			if ( isset( $element['settings']['pixels_custom_js'] ) ) {
				$element['settings']['pixels_custom_js'] = $existing[ $id ] ?? '';
			}

			if ( ! empty( $element['elements'] ) && is_array( $element['elements'] ) ) {
				$element['elements'] = self::restore_custom_js_in_elements( $element['elements'], $existing );
			}
		}
		unset( $element );

		return $elements;
	}
}
