<?php
/**
 * Widget assets.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Widgets;

use PixelsCoreCreativeToolsForElementor\Assets_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shared asset dependency helpers for Pixels Core Creative Tools for Elementor widgets.
 *
 * Widgets using this trait must implement get_assets_slug().
 */
trait Widget_Assets_Trait {

	/**
	 * Get assets slug.
	 *
	 * @return string Result.
	 */
	abstract protected function get_assets_slug(): string;

	/**
	 * Get script depends.
	 *
	 * @return array Result.
	 */
	public function get_script_depends(): array {
		$handle = Assets_Manager::instance()->get_script_handle( $this->get_assets_slug() );

		return array( $handle );
	}

	/**
	 * Get style depends.
	 *
	 * @return array Result.
	 */
	public function get_style_depends(): array {
		$handle = Assets_Manager::instance()->get_style_handle( $this->get_assets_slug() );

		return array( $handle );
	}
}
