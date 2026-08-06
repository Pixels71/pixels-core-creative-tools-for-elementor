<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pixels Ajax request handlers (Live Copy Paste).
 */
class Pixels_Ajax_Request {

	public function __construct() {
		add_action( 'wp_ajax_pixels_live_paste', [ $this, 'pixels_live_paste' ] );
		add_action( 'wp_ajax_pixels_cross_cp_import', [ $this, 'pixels_cross_copy_paste_media_import' ] );
	}

	/**
	 * Handle Live Paste — enable widgets required by pasted content.
	 */
	public function pixels_live_paste() {
		if ( ! check_ajax_referer( 'pixels_cross_cp_import', 'nonce', false ) ) {
			wp_send_json(
				$this->pixels_set_response(
					false,
					__( 'Invalid nonce.', 'pixels-core-creative-tools-for-elementor' ),
					__( 'The security check failed. Please refresh the page and try again.', 'pixels-core-creative-tools-for-elementor' )
				)
			);
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json(
				$this->pixels_set_response(
					false,
					__( 'Permission denied.', 'pixels-core-creative-tools-for-elementor' ),
					__( 'You do not have permission to perform this action.', 'pixels-core-creative-tools-for-elementor' )
				)
			);
		}

		$type = isset( $_POST['type'] ) ? strtolower( sanitize_text_field( wp_unslash( $_POST['type'] ) ) ) : '';

		switch ( $type ) {
			case 'pixels_enable_widget':
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
				$response = $this->pixels_enable_widget( $widgets_name );
				break;
			default:
				$response = $this->pixels_set_response(
					false,
					__( 'Invalid type.', 'pixels-core-creative-tools-for-elementor' ),
					__( 'The action type is not valid.', 'pixels-core-creative-tools-for-elementor' )
				);
				break;
		}

		wp_send_json( $response );
	}

	/**
	 * Enable selected widgets and ensure Elementor containers are available.
	 *
	 * @param array<int, string> $widgets_name Sanitized widget names.
	 * @return array{success: bool, message: string, description: string}
	 */
	public function pixels_enable_widget( $widgets_name = [] ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return $this->pixels_set_response(
				false,
				__( 'Permission denied.', 'pixels-core-creative-tools-for-elementor' ),
				__( 'Only administrators can enable widgets from Live Paste.', 'pixels-core-creative-tools-for-elementor' )
			);
		}

		$widgets_name = is_array( $widgets_name ) ? map_deep( $widgets_name, 'sanitize_text_field' ) : [];

		$option_value = get_option( 'elementor_experiment-container', false );
		if ( 'active' !== $option_value ) {
			if ( false === $option_value ) {
				add_option( 'elementor_experiment-container', 'active' );
			} else {
				update_option( 'elementor_experiment-container', 'active' );
			}
		}

		/**
		 * Allow other plugins/themes to react when Live Paste enables widgets.
		 *
		 * @param array{widgets: array<int, string>, extensions: string} $type
		 */
		apply_filters(
			'pixels_enable_selected_widgets',
			[
				'widgets'    => $widgets_name,
				'extensions' => '',
			]
		);

		return $this->pixels_set_response( true, __( 'Widgets enabled successfully.', 'pixels-core-creative-tools-for-elementor' ), '' );
	}

	/**
	 * Cross copy paste media import.
	 */
	public function pixels_cross_copy_paste_media_import() {
		check_ajax_referer( 'pixels_cross_cp_import', 'nonce' );

		if ( ! current_user_can( 'edit_posts' ) || ! current_user_can( 'upload_files' ) ) {
			wp_send_json_error( __( 'You do not have permission to import media.', 'pixels-core-creative-tools-for-elementor' ), 403 );
		}

		if ( ! isset( $_POST['copy_content'] ) ) {
			wp_send_json_error( __( 'Empty Content.', 'pixels-core-creative-tools-for-elementor' ) );
		}

		// Elementor element JSON may contain HTML in settings; sanitize_text_field would corrupt it.
		// Validate structure via json_decode, then process through Elementor's on_import handlers.
		$media_import = wp_unslash( $_POST['copy_content'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- JSON payload validated below; media handled by Elementor on_import.

		if ( ! is_string( $media_import ) || '' === $media_import ) {
			wp_send_json_error( __( 'Empty Content.', 'pixels-core-creative-tools-for-elementor' ) );
		}

		$decoded = json_decode( $media_import, true );

		if ( ! is_array( $decoded ) ) {
			wp_send_json_error( __( 'Invalid Content.', 'pixels-core-creative-tools-for-elementor' ) );
		}

		$media_import = [ $decoded ];
		$media_import = $this->pixels_elements_id_change( $media_import );
		$media_import = $this->pixels_import_media_copy_content( $media_import );

		wp_send_json_success( $media_import );
	}

	/**
	 * Replace element IDs with new unique IDs.
	 *
	 * @param array $media_import Element tree.
	 * @return array
	 */
	protected function pixels_elements_id_change( $media_import ) {
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
	protected function pixels_import_media_copy_content( $media_import ) {
		return \Elementor\Plugin::instance()->db->iterate_data(
			$media_import,
			function ( $element_data ) {
				$elements = \Elementor\Plugin::instance()->elements_manager->create_element_instance( $element_data );

				if ( ! $elements ) {
					return null;
				}

				return $this->pixels_element_copy_content_import_start( $elements );
			}
		);
	}

	/**
	 * Initiates the media import process for the copied element's content.
	 *
	 * @param \Elementor\Element_Base $element Element instance.
	 * @return array
	 */
	protected function pixels_element_copy_content_import_start( $element ) {
		$get_element_instance = $element->get_data();
		$pixels_mi_on_fun     = 'on_import';

		if ( method_exists( $element, $pixels_mi_on_fun ) ) {
			$get_element_instance = $element->{$pixels_mi_on_fun}( $get_element_instance );
		}

		foreach ( $element->get_controls() as $get_control ) {
			$control_type = \Elementor\Plugin::instance()->controls_manager->get_control( $get_control['type'] );
			$control_name = $get_control['name'];

			if ( ! $control_type ) {
				return $get_element_instance;
			}

			if ( method_exists( $control_type, $pixels_mi_on_fun ) ) {
				$get_element_instance['settings'][ $control_name ] = $control_type->{$pixels_mi_on_fun}( $element->get_settings( $control_name ), $get_control );
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
	public function pixels_set_response( $success = false, $message = '', $description = '' ) {
		return [
			'success'     => (bool) $success,
			'message'     => esc_html( $message ),
			'description' => esc_html( $description ),
		];
	}
}

new Pixels_Ajax_Request();
