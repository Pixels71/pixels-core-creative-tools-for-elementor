<?php
/**
 * Default_Compat setup
 */

namespace PixelsCore\Theme_Builder;
defined('ABSPATH') || exit; // Abort, if called directly.
/**
 * pixels theme compatibility.
 */
class Default_Compat {

	/**
	 *  Initiator
	 */
	public function __construct() {
		add_action( 'wp', [ $this, 'hooks' ] );
	}

	/**
	 * Run all the Actions / Filters.
	 */
	public function hooks() {
		if ( pixels_header_enabled() ) :
			// Replace header.php template.
			add_action( 'get_header', [ $this, 'override_header' ] );

			// Display pixels_HF's header in the replaced header.
			add_action( 'pixels_hf_header', 'pixels_render_header' );
		endif;

		if ( pixels_footer_enabled() ) :
			// Replace header.php template.
			add_action( 'get_footer', [ $this, 'override_footer' ] );

			// Display pixels_HF's footer in the replaced header.
			add_action( 'pixels_hf_footer', 'pixels_render_footer' );
		endif;
	}

	/**
	 * Function for overriding the header in the elmentor way.
	 *
	 * @return void
	 */
	public function override_header() {
		require_once PIXELS_CORE_PATH . 'includes/theme-builder/themes/default/pixels-header.php';
		$templates   = [];
		$templates[] = 'header.php';
		// Avoid running wp_head hooks again.
		remove_all_actions( 'wp_head' );
		ob_start();
		locate_template( $templates, true );
		ob_get_clean();
	}

	/**
	 * Function for overriding the footer in the elmentor way.
	 *
	 * @return void
	 */
	public function override_footer() {
		require_once PIXELS_CORE_PATH . 'includes/theme-builder/themes/default/pixels-footer.php';
		$templates   = [];
		$templates[] = 'footer.php';
		// Avoid running wp_footer hooks again.
		remove_all_actions( 'wp_footer' );
		ob_start();
		locate_template( $templates, true );
		ob_get_clean();
	}

}

new Default_Compat();
