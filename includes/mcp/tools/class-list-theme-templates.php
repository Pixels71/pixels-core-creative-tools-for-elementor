<?php
/**
 * MCP tool: list_theme_templates.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp\Tools;

use PixelsCoreCreativeToolsForElementor\Mcp\Theme_Templates;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Lists Theme Builder templates and the display rules they can use.
 */
final class List_Theme_Templates extends Tool {

	/**
	 * Name.
	 *
	 * @return string Result.
	 */
	public function name(): string {
		return 'list_theme_templates';
	}

	/**
	 * Title.
	 *
	 * @return string Result.
	 */
	public function title(): string {
		return 'List theme templates';
	}

	/**
	 * Description.
	 *
	 * @return string Result.
	 */
	public function description(): string {
		return 'Lists Theme Builder templates (header, footer and, with Pro, single, archive, 404, popup, mega menu) with their status and display conditions. Also returns the template types and the valid display rule keys for create_theme_template. Call this before creating a header or footer: only one template of a type is shown per page, so check whether one already covers the entire website. Edit a template\'s content with get_page_structure, add_elements and update_element, using its id as page_id.';
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
				'type'   => array(
					'type'        => 'string',
					'default'     => '',
					'description' => 'Only list templates of this type (e.g. "type_header"). Empty lists all.',
				),
				'status' => array(
					'type'    => 'string',
					'enum'    => array( 'any', 'publish', 'draft', 'pending', 'private' ),
					'default' => 'any',
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
	 * Hidden when the Theme Builder is switched off.
	 *
	 * @return bool Result.
	 */
	public function current_user_can_use(): bool {
		return Theme_Templates::is_available() && parent::current_user_can_use();
	}

	/**
	 * Execute.
	 *
	 * @param array<string, mixed> $args Args.
	 * @return array<string, mixed>|\WP_Error Result.
	 */
	public function execute( array $args ) {
		$types = Theme_Templates::get_types();
		$type  = (string) $args['type'];

		if ( '' !== $type && ! isset( $types[ $type ] ) ) {
			return new \WP_Error( 'pixeccte_mcp_invalid_type', sprintf( 'Unknown template type "%s". Available: %s.', $type, implode( ', ', array_keys( $types ) ) ) );
		}

		$query = array(
			'post_type'      => Theme_Templates::POST_TYPE,
			'post_status'    => 'any' === $args['status'] ? array( 'publish', 'draft', 'pending', 'private', 'future' ) : $args['status'],
			'posts_per_page' => 100,
			'orderby'        => 'modified',
			'order'          => 'DESC',
		);

		if ( '' !== $type ) {
			$query['meta_key']   = pixeccte_get_theme_template_type_meta_key(); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$query['meta_value'] = $type; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		}

		$templates = array();

		foreach ( get_posts( $query ) as $post ) {
			if ( current_user_can( 'edit_post', $post->ID ) ) {
				$templates[] = Theme_Templates::summarize( $post );
			}
		}

		return array(
			'types'          => $types,
			'templates'      => $templates,
			'display_rules'  => Theme_Templates::get_rule_options(),
			'specific_rules' => 'For single pages, posts or terms put targets in "specific": post-<id>, tax-<term id> (term archive), tax-<term id>-single (posts in that term).',
		);
	}
}
