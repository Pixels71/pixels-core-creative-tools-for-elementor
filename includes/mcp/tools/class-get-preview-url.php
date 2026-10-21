<?php
/**
 * MCP tool: get_preview_url.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp\Tools;

use Elementor\Plugin as Elementor;
use PixelsCoreCreativeToolsForElementor\Mcp\Preview_Links;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns links to view a page, including a draft, without signing in.
 */
final class Get_Preview_Url extends Tool {

	/**
	 * Name.
	 *
	 * @return string Result.
	 */
	public function name(): string {
		return 'get_preview_url';
	}

	/**
	 * Title.
	 *
	 * @return string Result.
	 */
	public function title(): string {
		return 'Get preview URL';
	}

	/**
	 * Description.
	 *
	 * @return string Result.
	 */
	public function description(): string {
		return 'Returns links for a page, post or theme template: "preview_url" opens it, even as a draft, in any browser without signing in until it expires, so you can check your work with a browser tool; "edit_url" opens the Elementor editor (needs sign-in). Anyone who has preview_url can see the page until then, so share it only with the user.';
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
				'page_id'          => array(
					'type'    => 'integer',
					'minimum' => 1,
				),
				'expires_in_hours' => array(
					'type'    => 'integer',
					'minimum' => 1,
					'maximum' => 168,
					'default' => 24,
				),
			),
			'required'   => array( 'page_id' ),
		);
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

		if ( ! $post || 'trash' === $post->post_status || ! is_post_type_viewable( $post->post_type ) ) {
			return new \WP_Error( 'pixeccte_mcp_not_found', sprintf( 'Page %d was not found.', $post_id ) );
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return new \WP_Error( 'pixeccte_mcp_forbidden', sprintf( 'You are not allowed to edit page %d.', $post_id ) );
		}

		$link     = Preview_Links::create( $post_id, (int) $args['expires_in_hours'] );
		$document = Elementor::$instance->documents->get( $post_id );

		return array(
			'page_id'     => $post_id,
			'status'      => $post->post_status,
			'preview_url' => $link['url'],
			'expires'     => $link['expires'],
			'public_url'  => 'publish' === $post->post_status ? get_permalink( $post ) : null,
			'edit_url'    => $document ? $document->get_edit_url() : get_edit_post_link( $post_id, 'raw' ),
		);
	}
}
