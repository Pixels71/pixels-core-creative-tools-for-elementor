<?php
namespace PixelsCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ajax request handlers (Live Copy Paste).
 */
class Ajax_Request {

	/**
	 * Max accepted copy_content JSON payload size (bytes).
	 *
	 * @var int
	 */
	const COPY_CONTENT_MAX_BYTES = 1048576; // 1 MB.

	/**
	 * Allowed top-level keys on an Elementor element node.
	 *
	 * @var array<int, string>
	 */
	const COPY_CONTENT_ELEMENT_KEYS = [
		'id',
		'elType',
		'widgetType',
		'settings',
		'elements',
		'isInner',
		'isLocked',
	];

	public function __construct() {
		add_action( 'wp_ajax_pixels_core_live_paste', [ $this, 'live_paste' ] );
		add_action( 'wp_ajax_pixels_core_cross_cp_import', [ $this, 'cross_copy_paste_media_import' ] );
	}

	/**
	 * Handle Live Paste — enable widgets required by pasted content.
	 */
	public function live_paste() {
		if ( ! check_ajax_referer( 'pixels_core_cross_cp_import', 'nonce', false ) ) {
			wp_send_json(
				$this->set_response(
					false,
					__( 'Invalid nonce.', 'pixels-core-creative-tools-for-elementor' ),
					__( 'The security check failed. Please refresh the page and try again.', 'pixels-core-creative-tools-for-elementor' )
				)
			);
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json(
				$this->set_response(
					false,
					__( 'Permission denied.', 'pixels-core-creative-tools-for-elementor' ),
					__( 'You do not have permission to perform this action.', 'pixels-core-creative-tools-for-elementor' )
				)
			);
		}

		$type = isset( $_POST['type'] ) ? strtolower( sanitize_text_field( wp_unslash( $_POST['type'] ) ) ) : '';

		switch ( $type ) {
			case 'pixels_core_enable_widget':
				$widgets_name = [];
				if ( isset( $_POST['widgets_name'] ) ) {
					$raw_widgets = map_deep( wp_unslash( $_POST['widgets_name'] ), 'sanitize_text_field' );

					if ( is_string( $raw_widgets ) ) {
						$decoded     = json_decode( $raw_widgets, true );
						$raw_widgets = is_array( $decoded ) ? map_deep( $decoded, 'sanitize_text_field' ) : [ $raw_widgets ];
					}

					if ( is_array( $raw_widgets ) ) {
						$widgets_name = array_values(
							array_filter(
								$raw_widgets,
								static function ( $name ) {
									return is_string( $name ) && '' !== $name;
								}
							)
						);
					}
				}
				$response = $this->enable_widget( $widgets_name );
				break;
			default:
				$response = $this->set_response(
					false,
					__( 'Invalid type.', 'pixels-core-creative-tools-for-elementor' ),
					__( 'The action type is not valid.', 'pixels-core-creative-tools-for-elementor' )
				);
				break;
		}

		wp_send_json( $response );
	}

	/**
	 * Enable selected widgets when Elementor Flexbox Container is already active.
	 *
	 * Does not write Elementor-owned options; container must be enabled by the admin
	 * in Elementor → Settings → Features.
	 *
	 * @param array<int, string> $widgets_name Sanitized widget names.
	 * @return array{success: bool, message: string, description: string}
	 */
	public function enable_widget( $widgets_name = [] ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return $this->set_response(
				false,
				__( 'Permission denied.', 'pixels-core-creative-tools-for-elementor' ),
				__( 'Only administrators can enable widgets from Live Paste.', 'pixels-core-creative-tools-for-elementor' )
			);
		}

		if ( ! $this->is_elementor_container_active() ) {
			return $this->set_response(
				false,
				__( 'Flexbox Container is required for Live Paste.', 'pixels-core-creative-tools-for-elementor' ),
				__( 'Enable Flexbox Container in Elementor → Settings → Features, then try again.', 'pixels-core-creative-tools-for-elementor' )
			);
		}

		$widgets_name = is_array( $widgets_name ) ? map_deep( $widgets_name, 'sanitize_text_field' ) : [];

		/**
		 * Allow other plugins/themes to react when Live Paste enables widgets.
		 *
		 * @param array{widgets: array<int, string>, extensions: string} $type
		 */
		apply_filters(
			'pixels_core_enable_selected_widgets',
			[
				'widgets'    => $widgets_name,
				'extensions' => '',
			]
		);

		return $this->set_response( true, __( 'Widgets enabled successfully.', 'pixels-core-creative-tools-for-elementor' ), '' );
	}

	/**
	 * Read-only check: whether Elementor's Flexbox Container feature is active.
	 *
	 * @return bool
	 */
	private function is_elementor_container_active() {
		if ( did_action( 'elementor/loaded' ) && isset( \Elementor\Plugin::$instance->experiments ) ) {
			return (bool) \Elementor\Plugin::$instance->experiments->is_feature_active( 'container' );
		}

		// Fallback when Elementor is not fully bootstrapped: read option only, never write it.
		return 'active' === get_option( 'elementor_experiment-container', '' );
	}

	/**
	 * Cross copy paste media import.
	 */
	public function cross_copy_paste_media_import() {
		check_ajax_referer( 'pixels_core_cross_cp_import', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) || ! current_user_can( 'upload_files' ) ) {
			wp_send_json_error( __( 'You do not have permission to import media.', 'pixels-core-creative-tools-for-elementor' ), 403 );
		}

		$raw_copy_content = filter_input( INPUT_POST, 'copy_content', FILTER_UNSAFE_RAW );
		if ( null === $raw_copy_content || false === $raw_copy_content ) {
			wp_send_json_error( __( 'Empty Content.', 'pixels-core-creative-tools-for-elementor' ) );
		}

		$raw_copy_content = wp_unslash( $raw_copy_content );
		if ( ! is_string( $raw_copy_content ) || '' === $raw_copy_content ) {
			wp_send_json_error( __( 'Empty Content.', 'pixels-core-creative-tools-for-elementor' ) );
		}

		$decoded = $this->parse_and_sanitize_copy_content( $raw_copy_content );
		if ( null === $decoded ) {
			wp_send_json_error( __( 'Invalid Content.', 'pixels-core-creative-tools-for-elementor' ) );
		}

		$media_import = [ $decoded ];
		$media_import = $this->elements_id_change( $media_import );
		$media_import = $this->import_media_copy_content( $media_import );

		wp_send_json_success( $media_import );
	}

	/**
	 * Parse copy_content JSON and return a sanitized Elementor element tree.
	 *
	 * @param mixed $raw_copy_content Unslashed request payload.
	 * @return array<string, mixed>|null Sanitized element node, or null on failure.
	 */
	protected function parse_and_sanitize_copy_content( $raw_copy_content ) {
		if ( ! is_string( $raw_copy_content ) || '' === $raw_copy_content ) {
			return null;
		}

		if ( strlen( $raw_copy_content ) > self::COPY_CONTENT_MAX_BYTES ) {
			return null;
		}

		$decoded = json_decode( $raw_copy_content, true );

		// Reject non-objects and bare element lists; the editor posts a single element object.
		if ( ! is_array( $decoded ) || $this->is_list_array( $decoded ) ) {
			return null;
		}

		$sanitized = $this->sanitize_copy_content_tree( $decoded );

		return is_array( $sanitized ) ? $sanitized : null;
	}

	/**
	 * Recursively sanitize an Elementor element node from copy/paste JSON.
	 *
	 * Only known element keys are kept. Settings values are sanitized by type;
	 * HTML-capable strings use wp_kses_post so builder markup is preserved.
	 *
	 * @param mixed $data Decoded element node.
	 * @return array<string, mixed>|null
	 */
	protected function sanitize_copy_content_tree( $data ) {
		if ( ! is_array( $data ) || $this->is_list_array( $data ) ) {
			return null;
		}

		$sanitized = [];

		foreach ( self::COPY_CONTENT_ELEMENT_KEYS as $key ) {
			if ( ! array_key_exists( $key, $data ) ) {
				continue;
			}

			$value = $data[ $key ];

			switch ( $key ) {
				case 'id':
				case 'elType':
				case 'widgetType':
					if ( ! is_string( $value ) && ! is_numeric( $value ) ) {
						break;
					}
					$sanitized[ $key ] = sanitize_text_field( (string) $value );
					break;

				case 'isInner':
				case 'isLocked':
					$sanitized[ $key ] = (bool) $value;
					break;

				case 'settings':
					if ( ! is_array( $value ) ) {
						$sanitized[ $key ] = [];
						break;
					}
					$sanitized[ $key ] = $this->sanitize_copy_content_settings( $value );
					break;

				case 'elements':
					if ( ! is_array( $value ) ) {
						$sanitized[ $key ] = [];
						break;
					}
					$sanitized[ $key ] = $this->sanitize_copy_content_elements( $value );
					break;
			}
		}

		// A valid element must at least declare its type.
		if ( empty( $sanitized['elType'] ) ) {
			return null;
		}

		if ( ! isset( $sanitized['settings'] ) || ! is_array( $sanitized['settings'] ) ) {
			$sanitized['settings'] = [];
		}

		if ( ! isset( $sanitized['elements'] ) || ! is_array( $sanitized['elements'] ) ) {
			$sanitized['elements'] = [];
		}

		return $sanitized;
	}

	/**
	 * Sanitize a list of child Elementor elements.
	 *
	 * @param array<int|string, mixed> $elements Child elements.
	 * @return array<int, array<string, mixed>>
	 */
	protected function sanitize_copy_content_elements( array $elements ) {
		$sanitized = [];

		foreach ( $elements as $element ) {
			$clean = $this->sanitize_copy_content_tree( $element );
			if ( null !== $clean ) {
				$sanitized[] = $clean;
			}
		}

		return $sanitized;
	}

	/**
	 * Recursively sanitize Elementor settings values.
	 *
	 * @param mixed $settings Settings array or scalar.
	 * @return mixed
	 */
	protected function sanitize_copy_content_settings( $settings ) {
		if ( is_array( $settings ) ) {
			$sanitized = [];

			foreach ( $settings as $key => $value ) {
				if ( ! is_string( $key ) && ! is_int( $key ) ) {
					continue;
				}

				$clean_key = is_string( $key ) ? sanitize_text_field( $key ) : $key;
				if ( '' === $clean_key && 0 !== $clean_key ) {
					continue;
				}

				$sanitized[ $clean_key ] = $this->sanitize_copy_content_settings( $value );
			}

			return $sanitized;
		}

		if ( is_string( $settings ) ) {
			// Preserve safe HTML used by Elementor text/editor controls.
			return wp_kses_post( $settings );
		}

		if ( is_int( $settings ) ) {
			return $settings;
		}

		if ( is_float( $settings ) ) {
			return $settings;
		}

		if ( is_bool( $settings ) ) {
			return $settings;
		}

		if ( null === $settings ) {
			return null;
		}

		// Drop unexpected scalar types (resources, objects, etc.).
		return null;
	}

	/**
	 * Whether the array is a list (0..n sequential keys).
	 *
	 * @param array<mixed> $array Array to check.
	 * @return bool
	 */
	protected function is_list_array( array $array ) {
		// Polyfill only: do not call array_is_list() (WP 6.5+) while Requires at least is 6.0.
		if ( [] === $array ) {
			return true;
		}

		return array_keys( $array ) === range( 0, count( $array ) - 1 );
	}

	/**
	 * Replace element IDs with new unique IDs.
	 *
	 * @param array $media_import Element tree.
	 * @return array
	 */
	protected function elements_id_change( $media_import ) {
		return \Elementor\Plugin::instance()->db->iterate_data(
			$media_import,
			function ( $element ) {
				$element['id'] = \Elementor\Utils::generate_random_string();
				return $element;
			}
		);
	}

	/**
	 * Media import copy content.
	 *
	 * @param array $media_import Element tree.
	 * @return array
	 */
	protected function import_media_copy_content( $media_import ) {
		return \Elementor\Plugin::instance()->db->iterate_data(
			$media_import,
			function ( $element_data ) {
				$elements = \Elementor\Plugin::instance()->elements_manager->create_element_instance( $element_data );

				if ( ! $elements ) {
					return null;
				}

				return $this->element_copy_content_import_start( $elements );
			}
		);
	}

	/**
	 * Initiates the media import process for the copied element's content.
	 *
	 * @param \Elementor\Element_Base $element Element instance.
	 * @return array
	 */
	protected function element_copy_content_import_start( $element ) {
		$get_element_instance = $element->get_data();
		$on_import            = 'on_import';

		if ( method_exists( $element, $on_import ) ) {
			$get_element_instance = $element->{$on_import}( $get_element_instance );
		}

		foreach ( $element->get_controls() as $get_control ) {
			$control_type = \Elementor\Plugin::instance()->controls_manager->get_control( $get_control['type'] );
			$control_name = $get_control['name'];

			if ( ! $control_type ) {
				return $get_element_instance;
			}

			if ( method_exists( $control_type, $on_import ) ) {
				$get_element_instance['settings'][ $control_name ] = $control_type->{$on_import}( $element->get_settings( $control_name ), $get_control );
			}
		}

		return $get_element_instance;
	}

	/**
	 * Set the response data.
	 *
	 * @param bool   $success     Whether the request succeeded.
	 * @param string $message     Short message.
	 * @param string $description Longer description.
	 * @return array{success: bool, message: string, description: string}
	 */
	public function set_response( $success = false, $message = '', $description = '' ) {
		return [
			'success'     => (bool) $success,
			'message'     => esc_html( $message ),
			'description' => esc_html( $description ),
		];
	}
}

new Ajax_Request();
