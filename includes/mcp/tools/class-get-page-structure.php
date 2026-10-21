<?php
/**
 * MCP tool: get_page_structure.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp\Tools;

use PixelsCoreCreativeToolsForElementor\Mcp\Document_Store;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Reads a page's element tree.
 */
final class Get_Page_Structure extends Tool {

	/**
	 * Name.
	 *
	 * @return string Result.
	 */
	public function name(): string {
		return 'get_page_structure';
	}

	/**
	 * Title.
	 *
	 * @return string Result.
	 */
	public function title(): string {
		return 'Get page structure';
	}

	/**
	 * Description.
	 *
	 * @return string Result.
	 */
	public function description(): string {
		return 'Returns the Elementor element tree of a page (element ids, types, widgets and settings) and its content_hash. Use detail "outline" for a short overview of large pages. Pass content_hash as expected_hash to the editing tools.';
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
				'page_id' => array(
					'type'    => 'integer',
					'minimum' => 1,
				),
				'detail'  => array(
					'type'        => 'string',
					'enum'        => array( 'outline', 'full' ),
					'default'     => 'full',
					'description' => 'outline: ids, types and a short text label; full: every setting.',
				),
			),
			'required'   => array( 'page_id' ),
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
		$page_id  = (int) $args['page_id'];
		$document = Document_Store::get_document( $page_id );

		if ( is_wp_error( $document ) ) {
			return $document;
		}

		$elements = Document_Store::get_elements( $document );
		$full     = 'full' === $args['detail'];

		return array(
			'page_id'      => $page_id,
			'title'        => get_the_title( $page_id ),
			'status'       => get_post_status( $page_id ),
			'template'     => get_page_template_slug( $page_id ),
			'elementor'    => (bool) $document->is_built_with_elementor(),
			'url'          => get_permalink( $page_id ),
			'edit_url'     => $document->get_edit_url(),
			'content_hash' => Document_Store::hash( $elements ),
			'elements'     => array_map(
				static function ( array $element ) use ( $full ): array {
					return Document_Store::summarize( $element, $full );
				},
				$elements
			),
		);
	}
}
