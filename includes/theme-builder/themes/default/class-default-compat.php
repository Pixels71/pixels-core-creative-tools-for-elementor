<?php
/**
 * Default_Compat setup
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Theme_Builder;

defined( 'ABSPATH' ) || exit; // Abort, if called directly.
/**
 * Pixeccte theme compatibility.
 */
class Default_Compat {

	/**
	 *  Initiator
	 */
	public function __construct() {
		add_action( 'wp', array( $this, 'hooks' ) );
	}

	/**
	 * Run all the Actions / Filters.
	 */
	public function hooks() {
		if ( pixeccte_header_enabled() ) :
			// Replace header.php template.
			add_action( 'get_header', array( $this, 'override_header' ) );

			// Display pixeccte_HF's header in the replaced header.
			add_action( 'pixeccte_hf_header', 'pixeccte_render_header' );
		endif;

		if ( pixeccte_footer_enabled() ) :
			// Replace header.php template.
			add_action( 'get_footer', array( $this, 'override_footer' ) );

			// Display pixeccte_HF's footer in the replaced header.
			add_action( 'pixeccte_hf_footer', 'pixeccte_render_footer' );
		endif;
	}

	/**
	 * Function for overriding the header in the elmentor way.
	 *
	 * @return void
	 */
	public function override_header() {
		require_once PIXECCTE_PATH . 'includes/theme-builder/themes/default/pixeccte-header.php';
		$templates   = array();
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
		require_once PIXECCTE_PATH . 'includes/theme-builder/themes/default/pixeccte-footer.php';
		$templates   = array();
		$templates[] = 'footer.php';
		// Avoid running wp_footer hooks again.
		remove_all_actions( 'wp_footer' );
		ob_start();
		locate_template( $templates, true );
		ob_get_clean();
	}
}

new Default_Compat();
