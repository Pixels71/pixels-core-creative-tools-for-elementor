<?php
/**
 * MCP tool: create_theme_template.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp\Tools;

use Elementor\Plugin as Elementor;
use PixelsCoreCreativeToolsForElementor\Mcp\Document_Store;
use PixelsCoreCreativeToolsForElementor\Mcp\Element_Builder;
use PixelsCoreCreativeToolsForElementor\Mcp\Theme_Templates;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Creates a Theme Builder template (header, footer, ...) with display conditions.
 */
final class Create_Theme_Template extends Tool {

	/**
	 * Name.
	 *
	 * @return string Result.
	 */
	public function name(): string {
		return 'create_theme_template';
	}

	/**
	 * Title.
	 *
	 * @return string Result.
	 */
	public function title(): string {
		return 'Create theme template';
	}

	/**
	 * Description.
	 *
	 * @return string Result.
	 */
	public function description(): string {
		return 'Creates a Theme Builder template such as a site header or footer, with its display conditions and optional content. Call list_theme_templates first for the template types and valid rules. A template is only shown on the site when it is published and its conditions match, so it is created as a draft unless the user asked for it to go live. Only one template of a type renders per page: the result lists other published templates of the same type that also cover the entire website. Use the Pixels Site Logo and Menu widgets (see list_menus for menu slugs) in headers and footers. Returns the template id (use it as page_id with add_elements and update_element), editor link and content_hash.';
	}

	/**
	 * Input schema.
	 *
	 * @return array<string, mixed>
	 */
	public function input_schema(): array {
		$rule_list = array(
			'type'  => 'array',
			'items' => array( 'type' => 'string' ),
		);

		return array(
			'type'       => 'object',
			'properties' => array(
				'title'            => array(
					'type'      => 'string',
					'minLength' => 1,
				),
				'type'             => array(
					'type'        => 'string',
					'enum'        => array_keys( Theme_Templates::get_types() ),
					'description' => 'Template type, e.g. type_header or type_footer.',
				),
				'status'           => array(
					'type'        => 'string',
					'enum'        => array( 'draft', 'publish' ),
					'default'     => 'draft',
					'description' => 'Use publish only when the user asked for the template to go live.',
				),
				'include'          => $rule_list + array(
					'default'     => array( 'basic-global' ),
					'description' => 'Where to show the template: rule keys from list_theme_templates (e.g. "basic-global" = entire website, "page|all", "special-front").',
				),
				'include_specific' => $rule_list + array(
					'default'     => array(),
					'description' => 'Specific targets to show it on: post-<id>, tax-<term id>, tax-<term id>-single.',
				),
				'exclude'          => $rule_list + array(
					'default'     => array(),
					'description' => 'Rule keys where it must not show.',
				),
				'exclude_specific' => $rule_list + array(
					'default'     => array(),
					'description' => 'Specific targets where it must not show, e.g. ["post-42"].',
				),
				'elements'         => self::elements_schema( 'Optional template content: top-level containers.' ),
			),
			'required'   => array( 'title', 'type' ),
		);
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
		$type_obj = get_post_type_object( Theme_Templates::POST_TYPE );

		if ( ! $type_obj || ! current_user_can( $type_obj->cap->create_posts ) ) {
			return new \WP_Error( 'pixeccte_mcp_forbidden', 'You are not allowed to create theme templates.' );
		}

		$status = (string) $args['status'];

		if ( 'publish' === $status && ! current_user_can( $type_obj->cap->publish_posts ) ) {
			return new \WP_Error( 'pixeccte_mcp_forbidden', 'You are not allowed to publish. Create the template as a draft instead.' );
		}

		$include = Theme_Templates::format_rules( (array) $args['include'], (array) $args['include_specific'], 'include' );

		if ( is_wp_error( $include ) ) {
			return $include;
		}

		if ( empty( $include ) ) {
			return new \WP_Error( 'pixeccte_mcp_no_conditions', 'Give at least one include rule or specific target, otherwise the template is never shown.' );
		}

		$exclude = Theme_Templates::format_rules( (array) $args['exclude'], (array) $args['exclude_specific'], 'exclude' );

		if ( is_wp_error( $exclude ) ) {
			return $exclude;
		}

		// Validate the content before creating anything.
		$elements = array();

		if ( ! empty( $args['elements'] ) ) {
			$elements = ( new Element_Builder() )->build_elements( $args['elements'] );

			if ( is_wp_error( $elements ) ) {
				return $elements;
			}
		}

		$type    = (string) $args['type'];
		$post_id = wp_insert_post(
			array(
				'post_title'   => sanitize_text_field( (string) $args['title'] ),
				'post_type'    => Theme_Templates::POST_TYPE,
				'post_status'  => $status,
				'post_content' => '',
			),
			true
		);

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		$post_id = (int) $post_id;

		pixeccte_update_theme_template_type( $post_id, $type );
		update_post_meta( $post_id, Theme_Templates::META_INCLUDE, $include );
		update_post_meta( $post_id, Theme_Templates::META_EXCLUDE, $exclude );

		// Same as saving from the Theme Builder screen.
		if ( 'type_popup' === $type ) {
			update_post_meta( $post_id, '_wp_page_template', 'elementor_canvas' );
		}

		$document = Document_Store::get_document( $post_id, true );

		if ( is_wp_error( $document ) ) {
			wp_delete_post( $post_id, true );

			return $document;
		}

		$saved = Document_Store::save( $document, $elements );

		if ( is_wp_error( $saved ) ) {
			wp_delete_post( $post_id, true );

			return $saved;
		}

		$document = Elementor::$instance->documents->get( $post_id, false );
		$result   = Theme_Templates::summarize( get_post( $post_id ) );

		$result['content_hash'] = Document_Store::hash( Document_Store::get_elements( $document ) );
		$result['elements']     = array_map(
			static function ( array $element ): array {
				return Document_Store::summarize( $element, false );
			},
			$elements
		);

		$conflicts = in_array( 'basic-global', (array) $include['rule'], true ) ? Theme_Templates::find_global_conflicts( $type, $post_id ) : array();

		if ( ! empty( $conflicts ) ) {
			$result['conflicts'] = $conflicts;
			$result['notice']    = 'Other published templates of this type also show on the entire website. Only one renders per page, so ask the user whether to unpublish them or narrow their conditions.';
		}

		return $result;
	}
}
