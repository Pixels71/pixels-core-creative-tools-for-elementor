<?php
namespace PixelsElementorAddons\Extensions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Live Copy Paste — paste Elementor designs from clipboard in the editor.
 */
final class Live_Copy_Paste {

	public static function init(): void {
		require_once PIXELS_CORE_PATH . 'includes/class-ajax-request.php';

		add_action( 'elementor/editor/after_enqueue_scripts', [ __CLASS__, 'enqueue_editor_assets' ] );
	}

	public static function enqueue_editor_assets(): void {
		$css_path = PIXELS_CORE_PATH . 'assets/css/live-copy-paste.css';
		$js_path  = PIXELS_CORE_PATH . 'assets/js/live-copy-paste-editor.js';

		wp_enqueue_style(
			'pixels-live-copy-paste',
			PIXELS_CORE_URL . 'assets/css/live-copy-paste.css',
			[],
			file_exists( $css_path ) ? (string) filemtime( $css_path ) : PIXELS_CORE_VERSION
		);

		wp_enqueue_script(
			'pixels-live-copy-paste-editor',
			PIXELS_CORE_URL . 'assets/js/live-copy-paste-editor.js',
			[ 'jquery', 'elementor-editor' ],
			file_exists( $js_path ) ? (string) filemtime( $js_path ) : PIXELS_CORE_VERSION,
			true
		);

		wp_localize_script(
			'pixels-live-copy-paste-editor',
			'pixels_cross_cp',
			[
				'ajax_url'  => admin_url( 'admin-ajax.php' ),
				'nonce'     => wp_create_nonce( 'pixels_cross_cp_import' ),
				'asset_url' => PIXELS_CORE_URL_ASSETS,
			]
		);
	}
}
