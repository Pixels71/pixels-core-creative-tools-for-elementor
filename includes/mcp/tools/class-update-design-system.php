<?php
/**
 * MCP tool: update_design_system.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp\Tools;

use PixelsCoreCreativeToolsForElementor\Mcp\Design_System;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sets the site's global colors and fonts (Elementor Site Settings).
 */
final class Update_Design_System extends Tool {

	/**
	 * Name.
	 *
	 * @return string Result.
	 */
	public function name(): string {
		return 'update_design_system';
	}

	/**
	 * Title.
	 *
	 * @return string Result.
	 */
	public function title(): string {
		return 'Update design system';
	}

	/**
	 * Description.
	 *
	 * @return string Result.
	 */
	public function description(): string {
		return 'Sets the site-wide global colors and fonts in Elementor Site Settings. Every element that references a global through "__globals__" changes with it, across all pages, so confirm with the user before changing a site that is already designed. Update the four built-in globals by id (primary, secondary, text, accent); add custom ones with a title (an existing custom title is updated). Then style widgets with {"__globals__": {"title_color": "globals/colors?id=primary", "title_typography_typography": "globals/typography?id=primary"}} instead of fixed values. Returns the resulting design, with the refs to use.';
	}

	/**
	 * Input schema.
	 *
	 * @return array<string, mixed>
	 */
	public function input_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'colors'     => array(
					'type'        => 'array',
					'default'     => array(),
					'description' => 'E.g. [{"id": "primary", "color": "#0B1F2A"}, {"title": "Surface", "color": "#F4F6F5"}].',
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'    => array( 'type' => 'string' ),
							'title' => array( 'type' => 'string' ),
							'color' => array( 'type' => 'string' ),
						),
						'required'   => array( 'color' ),
					),
				),
				'typography' => array(
					'type'        => 'array',
					'default'     => array(),
					'description' => 'E.g. [{"id": "primary", "family": "Manrope", "weight": "700"}, {"id": "text", "family": "Inter", "weight": "400"}]. Families are Google Fonts names or fonts already on the site.',
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'id'     => array( 'type' => 'string' ),
							'title'  => array( 'type' => 'string' ),
							'family' => array( 'type' => 'string' ),
							'weight' => array( 'type' => 'string' ),
						),
					),
				),
			),
		);
	}

	/**
	 * Capability: changes the whole site.
	 *
	 * @return string Result.
	 */
	public function capability(): string {
		return 'manage_options';
	}

	/**
	 * Execute.
	 *
	 * @param array<string, mixed> $args Args.
	 * @return array<string, mixed>|\WP_Error Result.
	 */
	public function execute( array $args ) {
		$colors = (array) $args['colors'];
		$fonts  = (array) $args['typography'];

		if ( empty( $colors ) && empty( $fonts ) ) {
			return new \WP_Error( 'pixeccte_mcp_nothing_to_update', 'Give at least one color or font.' );
		}

		return Design_System::update( $colors, $fonts );
	}
}
