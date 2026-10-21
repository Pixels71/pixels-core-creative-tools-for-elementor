<?php
/**
 * MCP tool: list_pages.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp\Tools;

use Elementor\Plugin as Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Lists pages and posts.
 */
final class List_Pages extends Tool {

	/**
	 * Name.
	 *
	 * @return string Result.
	 */
	public function name(): string {
		return 'list_pages';
	}

	/**
	 * Title.
	 *
	 * @return string Result.
	 */
	public function title(): string {
		return 'List pages';
	}

	/**
	 * Description.
	 *
	 * @return string Result.
	 */
	public function description(): string {
		return 'Lists pages (or posts) you can edit, newest first, with their status, URL, Elementor editor link and whether they are built with Elementor.';
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
				'post_type' => array(
					'type'    => 'string',
					'enum'    => array( 'page', 'post' ),
					'default' => 'page',
				),
				'status'    => array(
					'type'    => 'string',
					'enum'    => array( 'any', 'publish', 'draft', 'pending', 'private' ),
					'default' => 'any',
				),
				'search'    => array(
					'type'        => 'string',
					'default'     => '',
					'description' => 'Search in titles and content.',
				),
				'per_page'  => array(
					'type'    => 'integer',
					'minimum' => 1,
					'maximum' => 50,
					'default' => 20,
				),
				'page'      => array(
					'type'    => 'integer',
					'minimum' => 1,
					'default' => 1,
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
	 * @return array<string, mixed> Result.
	 */
	public function execute( array $args ) {
		$status = 'any' === $args['status'] ? array( 'publish', 'draft', 'pending', 'private', 'future' ) : $args['status'];

		$query = new \WP_Query(
			array(
				'post_type'      => $args['post_type'],
				'post_status'    => $status,
				's'              => $args['search'],
				'posts_per_page' => (int) $args['per_page'],
				'paged'          => (int) $args['page'],
				'orderby'        => 'modified',
				'order'          => 'DESC',
				'no_found_rows'  => false,
			)
		);

		$items = array();

		foreach ( $query->posts as $post ) {
			if ( ! current_user_can( 'edit_post', $post->ID ) ) {
				continue;
			}

			$document = Elementor::$instance->documents->get( $post->ID );

			$items[] = array(
				'id'        => $post->ID,
				'title'     => get_the_title( $post ),
				'status'    => $post->post_status,
				'url'       => get_permalink( $post ),
				'edit_url'  => $document ? $document->get_edit_url() : get_edit_post_link( $post->ID, 'raw' ),
				'elementor' => $document ? (bool) $document->is_built_with_elementor() : false,
				'modified'  => get_post_modified_time( 'c', true, $post ),
			);
		}

		return array(
			'total'       => (int) $query->found_posts,
			'total_pages' => (int) $query->max_num_pages,
			'page'        => (int) $args['page'],
			'items'       => $items,
		);
	}
}
