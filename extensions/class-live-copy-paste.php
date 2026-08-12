<?php
/**
 * Live copy paste.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Extensions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Live Copy Paste — paste Elementor designs from clipboard in the editor.
 */
final class Live_Copy_Paste {

	/**
	 * Init.
	 */
	public static function init(): void {
		require_once PIXECCTE_PATH . 'includes/class-ajax-request.php';

		add_action( 'elementor/editor/after_enqueue_scripts', array( __CLASS__, 'enqueue_editor_assets' ) );
	}

	/**
	 * Enqueue editor assets.
	 */
	public static function enqueue_editor_assets(): void {
		$css_path = PIXECCTE_PATH . 'assets/css/live-copy-paste.css';
		$js_path  = PIXECCTE_PATH . 'assets/js/live-copy-paste-editor.js';

		wp_enqueue_style(
			'pixeccte-live-copy-paste',
			PIXECCTE_URL . 'assets/css/live-copy-paste.css',
			array(),
			file_exists( $css_path ) ? (string) filemtime( $css_path ) : PIXECCTE_VERSION
		);

		wp_enqueue_script(
			'pixeccte-live-copy-paste-editor',
			PIXECCTE_URL . 'assets/js/live-copy-paste-editor.js',
			array( 'jquery', 'elementor-editor' ),
			file_exists( $js_path ) ? (string) filemtime( $js_path ) : PIXECCTE_VERSION,
			true
		);

		wp_localize_script(
			'pixeccte-live-copy-paste-editor',
			'pixeccte_cross_cp',
			array(
				'ajax_url'  => esc_url_raw( admin_url( 'admin-ajax.php' ) ),
				'nonce'     => wp_create_nonce( 'pixeccte_cross_cp_import' ),
				'asset_url' => esc_url_raw( PIXECCTE_URL_ASSETS ),
			)
		);
	}
}
