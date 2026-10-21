<?php
/**
 * MCP tool: trash_page.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp\Tools;

use PixelsCoreCreativeToolsForElementor\Mcp\Document_Store;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Moves a page or post to the trash (it can be restored from WordPress admin).
 */
final class Trash_Page extends Tool {

	/**
	 * Name.
	 *
	 * @return string Result.
	 */
	public function name(): string {
		return 'trash_page';
	}

	/**
	 * Title.
	 *
	 * @return string Result.
	 */
	public function title(): string {
		return 'Trash page';
	}

	/**
	 * Description.
	 *
	 * @return string Result.
	 */
	public function description(): string {
		return 'Moves a page or post to the trash. It can be restored from Pages > Trash in WordPress admin; nothing is deleted permanently. Only trash pages the user asked to remove, or drafts you created yourself in this conversation. The site\'s front page and posts page are refused.';
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
			),
			'required'   => array( 'page_id' ),
		);
	}

	/**
	 * Removes a page from the site.
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
		$post_id = (int) $args['page_id'];
		$post    = get_post( $post_id );

		if ( ! $post || 'trash' === $post->post_status || ! in_array( $post->post_type, Update_Page::POST_TYPES, true ) ) {
			return new \WP_Error( 'pixeccte_mcp_not_found', sprintf( 'Page %d was not found. Call list_pages for the page ids.', $post_id ) );
		}

		if ( ! current_user_can( 'delete_post', $post_id ) ) {
			return new \WP_Error( 'pixeccte_mcp_forbidden', sprintf( 'You are not allowed to trash page %d.', $post_id ) );
		}

		if ( in_array( $post_id, array( (int) get_option( 'page_on_front' ), (int) get_option( 'page_for_posts' ) ), true ) ) {
			return new \WP_Error( 'pixeccte_mcp_protected_page', sprintf( 'Page %d is the site\'s front page or posts page. Change Settings > Reading first.', $post_id ) );
		}

		$lock = Document_Store::check_lock( $post_id );

		if ( is_wp_error( $lock ) ) {
			return $lock;
		}

		$trashed = wp_trash_post( $post_id );

		if ( ! $trashed ) {
			return new \WP_Error( 'pixeccte_mcp_trash_failed', sprintf( 'Page %d could not be moved to the trash.', $post_id ) );
		}

		return array(
			'page_id' => $post_id,
			'title'   => get_the_title( $post_id ),
			'status'  => 'trash',
			'restore' => 'WordPress admin > Pages (or Posts) > Trash > Restore.',
		);
	}
}
