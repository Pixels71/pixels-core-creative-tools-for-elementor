<?php
/**
 * MCP tool: delete_element.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp\Tools;

use PixelsCoreCreativeToolsForElementor\Mcp\Document_Store;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Removes one element (and its children) from a page.
 */
final class Delete_Element extends Tool {

	/**
	 * Name.
	 *
	 * @return string Result.
	 */
	public function name(): string {
		return 'delete_element';
	}

	/**
	 * Title.
	 *
	 * @return string Result.
	 */
	public function title(): string {
		return 'Delete element';
	}

	/**
	 * Description.
	 *
	 * @return string Result.
	 */
	public function description(): string {
		return 'Removes a container or widget, with everything inside it, from a page. The previous version stays available in the page revisions. Item containers of nested widgets (tabs, accordion, ...) cannot be removed on their own; update or replace the widget instead.';
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
				'element_id'    => array(
					'type'      => 'string',
					'minLength' => 1,
				),
				'expected_hash' => array(
					'type'        => 'string',
					'default'     => '',
					'description' => 'content_hash from your last read; the call fails if the page changed since.',
				),
			),
			'required'   => array( 'page_id', 'element_id' ),
		);
	}

	/**
	 * Destructive.
	 *
	 * @return bool Result.
	 */
	public function is_destructive(): bool {
		return true;
	}

	/**
	 * Execute.
	 *
	 * @param array<string, mixed> $args Args.
	 * @return array<string, mixed>|\WP_Error Result.
	 */
	public function execute( array $args ) {
		$page_id    = (int) $args['page_id'];
		$element_id = (string) $args['element_id'];
		$document   = Document_Store::get_document( $page_id, true );

		if ( is_wp_error( $document ) ) {
			return $document;
		}

		$current = Document_Store::get_elements( $document );
		$check   = Document_Store::check_hash( $current, (string) $args['expected_hash'] );

		if ( is_wp_error( $check ) ) {
			return $check;
		}

		$found = Document_Store::find( $current, $element_id );

		if ( null === $found ) {
			return new \WP_Error( 'pixeccte_mcp_not_found', sprintf( 'No element with id "%s" on page %d.', $element_id, $page_id ) );
		}

		if ( null !== $found['parent'] && 'widget' === ( $found['parent']['elType'] ?? '' ) ) {
			return new \WP_Error(
				'pixeccte_mcp_nested_child',
				sprintf( 'Element "%s" is an item container of %s; removing it alone would break the widget. Update the widget\'s items or delete the whole widget.', $element_id, $found['parent']['widgetType'] ?? 'a nested widget' )
			);
		}

		$parent_id = null === $found['parent'] ? null : (string) $found['parent']['id'];
		$index     = $found['index'];
		$updated   = Document_Store::modify_children(
			$current,
			$parent_id,
			static function ( array $children ) use ( $index ): array {
				array_splice( $children, $index, 1 );

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
			'deleted'      => Document_Store::summarize( $found['element'], false ),
		);
	}
}
