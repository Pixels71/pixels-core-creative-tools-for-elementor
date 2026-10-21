<?php
/**
 * MCP tool: get_site_info.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp\Tools;

use Elementor\Plugin as Elementor;
use PixelsCoreCreativeToolsForElementor\Mcp\Design_System;
use PixelsCoreCreativeToolsForElementor\Mcp\Widget_Catalog;
use PixelsCoreCreativeToolsForElementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Site, plugin and design-system overview.
 */
final class Get_Site_Info extends Tool {

	/**
	 * Name.
	 *
	 * @return string Result.
	 */
	public function name(): string {
		return 'get_site_info';
	}

	/**
	 * Title.
	 *
	 * @return string Result.
	 */
	public function title(): string {
		return 'Get site info';
	}

	/**
	 * Description.
	 *
	 * @return string Result.
	 */
	public function description(): string {
		return 'Overview of the WordPress site: name, URLs, versions, whether Pixels Core Pro is active, Elementor features, responsive breakpoints, and the global colors and fonts (with the __globals__ references to use them). Call this first.';
	}

	/**
	 * Input schema.
	 *
	 * @return array<string, mixed>
	 */
	public function input_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(),
		);
	}

	/**
	 * Read only.
	 *
	 * @return bool Result.
	 */
	public function is_read_only(): bool {
		return true;
	}

	/**
	 * Execute.
	 *
	 * @param array<string, mixed> $args Args.
	 * @return array<string, mixed> Result.
	 */
	public function execute( array $args ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Tool signature.
		$experiments = Elementor::$instance->experiments;

		return array(
			'site'           => array(
				'name'        => get_bloginfo( 'name' ),
				'description' => get_bloginfo( 'description' ),
				'url'         => home_url( '/' ),
				'language'    => get_bloginfo( 'language' ),
				'theme'       => wp_get_theme()->get( 'Name' ),
			),
			'versions'       => array(
				'wordpress'   => get_bloginfo( 'version' ),
				'elementor'   => ELEMENTOR_VERSION,
				'pixels_core' => PIXECCTE_VERSION,
				'pixels_pro'  => defined( 'PIXECCTE_PRO_VERSION' ) ? PIXECCTE_PRO_VERSION : null,
			),
			'features'       => array(
				'container'       => $experiments->is_feature_active( 'container' ),
				'nested_elements' => $experiments->is_feature_active( 'nested-elements' ),
				'theme_templates' => array_keys( Plugin::get_theme_template_types() ),
			),
			'page_templates' => array(
				'elementor_header_footer' => 'Theme header and footer with a full-width Elementor body (recommended).',
				'elementor_canvas'        => 'Blank page: no theme header or footer.',
				'default'                 => 'The theme\'s default page template.',
			),
			'breakpoints'    => Widget_Catalog::get_breakpoints(),
			'design'         => Design_System::get(),
			'current_user'   => array(
				'name'            => wp_get_current_user()->display_name,
				'can_publish'     => current_user_can( 'publish_pages' ),
				'unfiltered_html' => current_user_can( 'unfiltered_html' ),
			),
		);
	}
}
