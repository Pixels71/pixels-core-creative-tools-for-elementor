<?php
/**
 * MCP tool: add_elements.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp\Tools;

use PixelsCoreCreativeToolsForElementor\Mcp\Document_Store;
use PixelsCoreCreativeToolsForElementor\Mcp\Element_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Inserts elements into an existing page.
 */
final class Add_Elements extends Tool {

	/**
	 * Name.
	 *
	 * @return string Result.
	 */
	public function name(): string {
		return 'add_elements';
	}

	/**
	 * Title.
	 *
	 * @return string Result.
	 */
	public function title(): string {
		return 'Add elements';
	}

	/**
	 * Description.
	 *
	 * @return string Result.
	 */
	public function description(): string {
		return 'Inserts containers or widgets into a page: as new top-level sections (no parent_id) or inside an existing container (parent_id). position is the 0-based index among the parent\'s children; omit it to append. Returns the new element ids and the new content_hash.';
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
				'page_id'       => array(
					'type'    => 'integer',
					'minimum' => 1,
				),
				'elements'      => array_merge(
					self::elements_schema( 'Elements to insert, in order.' ),
					array( 'minItems' => 1 )
				),
				'parent_id'     => array(
					'type'        => 'string',
					'default'     => '',
					'description' => 'Id of the container to insert into. Empty inserts top-level sections.',
				),
				'position'      => array(
					'type'        => 'integer',
					'minimum'     => 0,
					'description' => '0-based index to insert at. Omit to append.',
				),
				'expected_hash' => array(
					'type'        => 'string',
					'default'     => '',
					'description' => 'content_hash from your last read; the call fails if the page changed since.',
				),
			),
			'required'   => array( 'page_id', 'elements' ),
		);
	}

	/**
	 * Execute.
	 *
	 * @param array<string, mixed> $args Args.
	 * @return array<string, mixed>|\WP_Error Result.
	 */
	public function execute( array $args ) {
		$page_id  = (int) $args['page_id'];
		$document = Document_Store::get_document( $page_id, true );

		if ( is_wp_error( $document ) ) {
			return $document;
		}

		$current = Document_Store::get_elements( $document );
		$check   = Document_Store::check_hash( $current, (string) $args['expected_hash'] );

		if ( is_wp_error( $check ) ) {
			return $check;
		}

		$parent_id = '' === $args['parent_id'] ? null : (string) $args['parent_id'];

		if ( null !== $parent_id ) {
			$found = Document_Store::find( $current, $parent_id );

			if ( null === $found ) {
				return new \WP_Error( 'pixeccte_mcp_not_found', sprintf( 'No element with id "%s" on page %d.', $parent_id, $page_id ) );
			}

			if ( 'container' !== ( $found['element']['elType'] ?? '' ) ) {
				return new \WP_Error( 'pixeccte_mcp_bad_parent', sprintf( 'Element "%s" is not a container; only containers can hold elements.', $parent_id ) );
			}
		}

		$builder = new Element_Builder( $current );
		$new     = $builder->build_elements( $args['elements'] );

		if ( is_wp_error( $new ) ) {
			return $new;
		}

		// Containers placed inside another container are inner containers.
		if ( null !== $parent_id ) {
			foreach ( $new as $index => $element ) {
				if ( 'container' === $element['elType'] ) {
					$new[ $index ]['isInner'] = true;
				}
			}
		}

		$position = isset( $args['position'] ) ? (int) $args['position'] : null;
		$updated  = Document_Store::modify_children(
			$current,
			$parent_id,
			static function ( array $children ) use ( $new, $position ): array {
				$at = null === $position ? count( $children ) : min( $position, count( $children ) );
				array_splice( $children, $at, 0, $new );

				return $children;
			}
		);

		$saved = Document_Store::save( $document, $updated );

		if ( is_wp_error( $saved ) ) {
			return $saved;
		}

		return array(
			'page_id'      => $page_id,
			'content_hash' => Document_Store::hash( Document_Store::get_elements( $document ) ),
			'added'        => array_map(
				static function ( array $element ): array {
					return Document_Store::summarize( $element, false );
				},
				$new
			),
		);
	}
}
