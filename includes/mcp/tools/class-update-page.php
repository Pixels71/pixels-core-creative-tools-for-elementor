<?php
/**
 * MCP tool: update_page.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp\Tools;

use PixelsCoreCreativeToolsForElementor\Mcp\Document_Store;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Changes a page's title, slug, status or page template.
 */
final class Update_Page extends Tool {

	/**
	 * Post types this tool edits.
	 */
	const POST_TYPES = array( 'page', 'post' );

	/**
	 * Name.
	 *
	 * @return string Result.
	 */
	public function name(): string {
		return 'update_page';
	}

	/**
	 * Title.
	 *
	 * @return string Result.
	 */
	public function title(): string {
		return 'Update page';
	}

	/**
	 * Description.
	 *
	 * @return string Result.
	 */
	public function description(): string {
		return 'Changes a page or post\'s title, URL slug, status or page template. Only the fields you give change. Set status "publish" only when the user asked to publish; publishing makes the page public at its URL. Edit the page content with add_elements, update_element and delete_element instead.';
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
				'page_id'  => array(
					'type'    => 'integer',
					'minimum' => 1,
				),
				'title'    => array(
					'type'      => 'string',
					'minLength' => 1,
				),
				'slug'     => array(
					'type'        => 'string',
					'minLength'   => 1,
					'description' => 'URL slug, e.g. "about-us". WordPress adds a suffix if it is taken.',
				),
				'status'   => array(
					'type' => 'string',
					'enum' => array( 'draft', 'pending', 'private', 'publish' ),
				),
				'template' => array(
					'type'        => 'string',
					'enum'        => array( 'elementor_header_footer', 'elementor_canvas', 'default' ),
					'description' => 'elementor_header_footer keeps the site header/footer; elementor_canvas is a blank page; default is the theme template.',
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

		if ( ! $post || 'trash' === $post->post_status || ! in_array( $post->post_type, self::POST_TYPES, true ) ) {
			return new \WP_Error( 'pixeccte_mcp_not_found', sprintf( 'Page %d was not found. Call list_pages for the page ids.', $post_id ) );
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return new \WP_Error( 'pixeccte_mcp_forbidden', sprintf( 'You are not allowed to edit page %d.', $post_id ) );
		}

		if ( ! isset( $args['title'] ) && ! isset( $args['slug'] ) && ! isset( $args['status'] ) && ! isset( $args['template'] ) ) {
			return new \WP_Error( 'pixeccte_mcp_nothing_to_update', 'Give at least one of title, slug, status or template.' );
		}

		$lock = Document_Store::check_lock( $post_id );

		if ( is_wp_error( $lock ) ) {
			return $lock;
		}

		$data = array( 'ID' => $post_id );

		if ( isset( $args['title'] ) ) {
			$data['post_title'] = sanitize_text_field( (string) $args['title'] );
		}

		if ( isset( $args['slug'] ) ) {
			$slug = sanitize_title( (string) $args['slug'] );

			if ( '' === $slug ) {
				return new \WP_Error( 'pixeccte_mcp_invalid_slug', 'The slug must contain letters or numbers.' );
			}

			$data['post_name'] = $slug;
		}

		if ( isset( $args['status'] ) ) {
			$status   = (string) $args['status'];
			$type_obj = get_post_type_object( $post->post_type );

			if ( in_array( $status, array( 'publish', 'private' ), true ) && ! current_user_can( $type_obj->cap->publish_posts ) ) {
				return new \WP_Error( 'pixeccte_mcp_forbidden', 'You are not allowed to publish.' );
			}

			$data['post_status'] = $status;
		}

		if ( count( $data ) > 1 ) {
			$updated = wp_update_post( wp_slash( $data ), true );

			if ( is_wp_error( $updated ) ) {
				return $updated;
			}
		}

		if ( isset( $args['template'] ) ) {
			update_post_meta( $post_id, '_wp_page_template', sanitize_key( (string) $args['template'] ) );
		}

		return self::summarize( (int) $post_id );
	}

	/**
	 * Page summary returned by the page tools.
	 *
	 * @param int $post_id Post ID.
	 * @return array<string, mixed> Result.
	 */
	public static function summarize( int $post_id ): array {
		$post     = get_post( $post_id );
		$template = get_post_meta( $post_id, '_wp_page_template', true );

		return array(
			'page_id'  => $post_id,
			'title'    => get_the_title( $post ),
			'status'   => $post->post_status,
			'slug'     => $post->post_name,
			'template' => '' !== $template ? $template : 'default',
			'url'      => get_permalink( $post ),
			'is_front' => 'page' === get_option( 'show_on_front' ) && (int) get_option( 'page_on_front' ) === $post_id,
		);
	}
}
