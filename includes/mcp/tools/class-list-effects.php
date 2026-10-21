<?php
/**
 * MCP tool: list_effects.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp\Tools;

use PixelsCoreCreativeToolsForElementor\Mcp\Effect_Catalog;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Describes the animation and interaction effects and their ready-made recipes.
 */
final class List_Effects extends Tool {

	/**
	 * Name.
	 *
	 * @return string Result.
	 */
	public function name(): string {
		return 'list_effects';
	}

	/**
	 * Title.
	 *
	 * @return string Result.
	 */
	public function title(): string {
		return 'List effects';
	}

	/**
	 * Description.
	 *
	 * @return string Result.
	 */
	public function description(): string {
		return 'Lists the motion and interaction effects this site supports (scroll animations with GSAP, entrance animations, image reveals, text animations, nested hover, sticky/pin, custom cursor, image masking, tooltips) with ready-made recipes. Without "effect" it returns a short index; with an effect id it returns how the effect works, its recipes with the exact settings to apply, and the pitfalls. Apply a recipe by merging its "settings" into the element named in "apply_to" with update_element (or include them when you add the element); recipes with "targets" also need those settings on the listed child elements. Use effects with restraint: one entrance animation style per page and hover effects on repeated cards usually look better than animating everything.';
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
				'effect' => array(
					'type'        => 'string',
					'default'     => '',
					'description' => 'Effect id from the index, e.g. "nested_hover". Empty returns the index.',
				),
			),
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
	 * @return array<string, mixed>|\WP_Error Result.
	 */
	public function execute( array $args ) {
		$effects = Effect_Catalog::get_all();
		$id      = (string) $args['effect'];

		if ( '' === $id ) {
			return array(
				'effects' => array_values( array_map( array( Effect_Catalog::class, 'summarize' ), $effects ) ),
				'note'    => empty( $effects )
					? 'No effect extensions are active. They come with Pixels Core Pro and are switched on in Pixels Core > Extensions.'
					: 'Call list_effects with an effect id for its recipes.',
			);
		}

		if ( ! isset( $effects[ $id ] ) ) {
			return new \WP_Error(
				'pixeccte_mcp_unknown_effect',
				sprintf( 'Unknown or inactive effect "%s". Available: %s.', $id, implode( ', ', array_keys( $effects ) ) )
			);
		}

		return $effects[ $id ];
	}
}
