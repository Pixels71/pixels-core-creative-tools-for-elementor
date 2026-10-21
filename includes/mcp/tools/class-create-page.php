<?php
/**
 * MCP tool: create_page.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp\Tools;

use Elementor\Plugin as Elementor;
use PixelsCoreCreativeToolsForElementor\Mcp\Document_Store;
use PixelsCoreCreativeToolsForElementor\Mcp\Element_Builder;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Creates a new Elementor page.
 */
final class Create_Page extends Tool {

	/**
	 * Name.
	 *
	 * @return string Result.
	 */
	public function name(): string {
		return 'create_page';
	}

	/**
	 * Title.
	 *
	 * @return string Result.
	 */
	public function title(): string {
		return 'Create page';
	}

	/**
	 * Description.
	 *
	 * @return string Result.
	 */
	public function description(): string {
		return 'Creates a new Elementor page (or post), as a draft unless told otherwise, optionally with its content. For long pages create it with the first sections and add the rest with add_elements. Returns the page id, editor link and content_hash.';
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
				'title'     => array(
					'type'      => 'string',
					'minLength' => 1,
				),
				'post_type' => array(
					'type'    => 'string',
					'enum'    => array( 'page', 'post' ),
					'default' => 'page',
				),
				'status'    => array(
					'type'        => 'string',
					'enum'        => array( 'draft', 'pending', 'private', 'publish' ),
					'default'     => 'draft',
					'description' => 'Use publish only when the user explicitly asked to publish.',
				),
				'template'  => array(
					'type'        => 'string',
					'enum'        => array( 'elementor_header_footer', 'elementor_canvas', 'default' ),
					'default'     => 'elementor_header_footer',
					'description' => 'elementor_header_footer keeps the site header/footer with a full-width body; elementor_canvas is a blank page.',
				),
				'elements'  => self::elements_schema( 'Optional page content: top-level containers (sections).' ),
			),
			'required'   => array( 'title' ),
		);
	}

	/**
	 * Execute.
	 *
	 * @param array<string, mixed> $args Args.
	 * @return array<string, mixed>|\WP_Error Result.
	 */
	public function execute( array $args ) {
		$post_type = (string) $args['post_type'];
		$type_obj  = get_post_type_object( $post_type );

		if ( ! $type_obj || ! current_user_can( $type_obj->cap->create_posts ) ) {
			return new \WP_Error( 'pixeccte_mcp_forbidden', sprintf( 'You are not allowed to create %s items.', $post_type ) );
		}

		$status = (string) $args['status'];

		if ( in_array( $status, array( 'publish', 'private' ), true ) && ! current_user_can( $type_obj->cap->publish_posts ) ) {
			return new \WP_Error( 'pixeccte_mcp_forbidden', 'You are not allowed to publish. Create the page as a draft instead.' );
		}

		// Validate the content before creating anything.
		$elements = array();

		if ( ! empty( $args['elements'] ) ) {
			$elements = ( new Element_Builder() )->build_elements( $args['elements'] );

			if ( is_wp_error( $elements ) ) {
				return $elements;
			}
		}

		$post_id = wp_insert_post(
			array(
				'post_title'   => sanitize_text_field( (string) $args['title'] ),
				'post_type'    => $post_type,
				'post_status'  => $status,
				'post_content' => '',
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		if ( 'default' !== $args['template'] ) {
			update_post_meta( $post_id, '_wp_page_template', sanitize_key( (string) $args['template'] ) );
		}

		$document = Document_Store::get_document( (int) $post_id, true );

		if ( is_wp_error( $document ) ) {
			wp_delete_post( (int) $post_id, true );

			return $document;
		}

		$saved = Document_Store::save( $document, $elements );

		if ( is_wp_error( $saved ) ) {
			wp_delete_post( (int) $post_id, true );

			return $saved;
		}

		$document = Elementor::$instance->documents->get( (int) $post_id, false );

		return array(
			'page_id'      => (int) $post_id,
			'title'        => get_the_title( $post_id ),
			'status'       => get_post_status( $post_id ),
			'url'          => get_permalink( $post_id ),
			'preview_url'  => get_preview_post_link( $post_id ),
			'edit_url'     => $document->get_edit_url(),
			'content_hash' => Document_Store::hash( Document_Store::get_elements( $document ) ),
			'elements'     => array_map(
				static function ( array $element ): array {
					return Document_Store::summarize( $element, false );
				},
				$elements
			),
		);
	}
}
