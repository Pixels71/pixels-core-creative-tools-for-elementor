<?php
/**
 * MCP tool: list_menus.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp\Tools;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Lists WordPress navigation menus and their items.
 */
final class List_Menus extends Tool {

	/**
	 * Name.
	 *
	 * @return string Result.
	 */
	public function name(): string {
		return 'list_menus';
	}

	/**
	 * Title.
	 *
	 * @return string Result.
	 */
	public function title(): string {
		return 'List menus';
	}

	/**
	 * Description.
	 *
	 * @return string Result.
	 */
	public function description(): string {
		return 'Lists the WordPress navigation menus with their items (title, url, parent). The menu "slug" is the value for the "nav_menu" setting of the Pixels Menu widget. Create or change menus with save_menu.';
	}

	/**
	 * Input schema.
	 *
	 * @return array<string, mixed>
	 */
	public function input_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(),
		);
	}

	/**
	 * Capability.
	 *
	 * @return string Result.
	 */
	public function capability(): string {
		return 'edit_theme_options';
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
		$menus = array();

		foreach ( wp_get_nav_menus() as $menu ) {
			$menus[] = self::summarize( $menu );
		}

		return array( 'menus' => $menus );
	}

	/**
	 * Summary of one menu.
	 *
	 * @param \WP_Term $menu Menu term.
	 * @return array<string, mixed>
	 */
	public static function summarize( \WP_Term $menu ): array {
		$items = array();

		foreach ( (array) wp_get_nav_menu_items( $menu->term_id ) as $item ) {
			$items[] = array(
				'id'        => (int) $item->ID,
				'title'     => $item->title,
				'url'       => $item->url,
				'type'      => $item->type,
				'object_id' => (int) $item->object_id,
				'parent_id' => (int) $item->menu_item_parent,
			);
		}

		return array(
			'id'        => $menu->term_id,
			'name'      => $menu->name,
			'slug'      => $menu->slug,
			'locations' => array_keys( array_filter( (array) get_nav_menu_locations() ), $menu->term_id, true ),
			'items'     => $items,
		);
	}
}
