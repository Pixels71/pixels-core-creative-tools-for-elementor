<?php
/**
 * Motion and interaction effects (animation extensions) for MCP.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp;

use PixelsCoreCreativeToolsForElementor\Admin\Extension_Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Collects descriptions and ready-made recipes for the extensions that add animation and
 * interaction controls to the Advanced tab (GSAP animations, nested hover, sticky, ...).
 *
 * The extensions live in Pixels Core Pro, which registers them through `pixeccte_mcp_effects`.
 * Only effects whose extension is switched on are returned.
 */
final class Effect_Catalog {

	/**
	 * Registered effects whose extension is active, keyed by id.
	 *
	 * Each effect: id, extension (slug), title, applies_to, sections (schema section ids it adds),
	 * description, how_to (list), recipes (list of {id, title, description, apply_to, settings, targets?}),
	 * gotchas (list).
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function get_all(): array {
		/**
		 * Filter the animation and interaction effects described to MCP clients.
		 *
		 * @param array<int, array<string, mixed>> $effects
		 */
		$effects = (array) apply_filters( 'pixeccte_mcp_effects', array() );
		$active  = array();

		foreach ( $effects as $effect ) {
			if ( ! is_array( $effect ) || empty( $effect['id'] ) ) {
				continue;
			}

			$extension = (string) ( $effect['extension'] ?? '' );

			if ( '' !== $extension && class_exists( Extension_Settings::class ) && ! Extension_Settings::instance()->is_active( $extension ) ) {
				continue;
			}

			$active[ (string) $effect['id'] ] = $effect;
		}

		return $active;
	}

	/**
	 * Short guidance per schema section id, attached to get_widget_schema output.
	 *
	 * @return array<string, array<string, string>> section id => {effect, about}
	 */
	public static function section_notes(): array {
		$notes = array();

		foreach ( self::get_all() as $id => $effect ) {
			foreach ( (array) ( $effect['sections'] ?? array() ) as $section_id ) {
				$notes[ (string) $section_id ] = array(
					'effect' => $id,
					'about'  => sprintf( '%s Ready-made settings: list_effects {"effect": "%s"}.', (string) ( $effect['description'] ?? '' ), $id ),
				);
			}
		}

		return $notes;
	}

	/**
	 * Compact index entry for one effect.
	 *
	 * @param array<string, mixed> $effect Effect.
	 * @return array<string, mixed> Result.
	 */
	public static function summarize( array $effect ): array {
		return array(
			'id'          => (string) $effect['id'],
			'title'       => (string) ( $effect['title'] ?? $effect['id'] ),
			'applies_to'  => (string) ( $effect['applies_to'] ?? '' ),
			'description' => (string) ( $effect['description'] ?? '' ),
			'recipes'     => array_map(
				static function ( array $recipe ): string {
					return (string) $recipe['id'] . ': ' . (string) ( $recipe['title'] ?? '' );
				},
				(array) ( $effect['recipes'] ?? array() )
			),
		);
	}
}
