<?php
/**
 * MCP tool: save_menu.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp\Tools;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Creates a navigation menu, or replaces the items of an existing one.
 */
final class Save_Menu extends Tool {

	/**
	 * Deepest submenu level accepted.
	 */
	const MAX_DEPTH = 3;

	/**
	 * Name.
	 *
	 * @return string Result.
	 */
	public function name(): string {
		return 'save_menu';
	}

	/**
	 * Title.
	 *
	 * @return string Result.
	 */
	public function title(): string {
		return 'Save menu';
	}

	/**
	 * Description.
	 *
	 * @return string Result.
	 */
	public function description(): string {
		return 'Creates a WordPress navigation menu with its items, or, with menu_id, renames an existing menu and replaces all of its items with the given list (read it with list_menus first and send the full list). Each item links to a page or post (page_id, so the link follows the page when its slug changes) or to a url, and may have children for a submenu. Use the returned slug as the Pixels Menu widget\'s "nav_menu" setting.';
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
				'menu_id' => array(
					'type'        => 'integer',
					'minimum'     => 0,
					'default'     => 0,
					'description' => 'Existing menu to update. 0 creates a new menu.',
				),
				'name'    => array(
					'type'        => 'string',
					'default'     => '',
					'description' => 'Menu name. Required for a new menu; renames an existing one.',
				),
				'items'   => array(
					'type'        => 'array',
					'description' => 'Menu items in order. Each item is {"title": "...", "url": "https://..." or "#section", "page_id": 12, "children": [items]}; give either url or page_id. title may be left out for page_id items to use the page title.',
					'items'       => array(
						'type'       => 'object',
						'properties' => array(
							'title'    => array( 'type' => 'string' ),
							'url'      => array( 'type' => 'string' ),
							'page_id'  => array(
								'type'    => 'integer',
								'minimum' => 1,
							),
							'children' => array( 'type' => 'array' ),
						),
					),
				),
			),
			'required'   => array( 'items' ),
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
	 * Replaces existing items.
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
		$menu_id = (int) $args['menu_id'];
		$name    = sanitize_text_field( (string) $args['name'] );

		// Validate every item before touching the menu.
		$checked = $this->check_items( (array) $args['items'], 1 );

		if ( is_wp_error( $checked ) ) {
			return $checked;
		}

		if ( $menu_id ) {
			$menu = wp_get_nav_menu_object( $menu_id );

			if ( ! $menu ) {
				return new \WP_Error( 'pixeccte_mcp_not_found', sprintf( 'Menu %d was not found. Call list_menus for the menu ids.', $menu_id ) );
			}

			if ( '' !== $name && $name !== $menu->name ) {
				$renamed = wp_update_nav_menu_object( $menu_id, array( 'menu-name' => $name ) );

				if ( is_wp_error( $renamed ) ) {
					return $renamed;
				}

				// Renaming regenerates the slug, which would disconnect every Menu widget that uses it.
				wp_update_term( $menu_id, 'nav_menu', array( 'slug' => $menu->slug ) );
			}

			foreach ( (array) wp_get_nav_menu_items( $menu_id, array( 'post_status' => 'any' ) ) as $item ) {
				wp_delete_post( (int) $item->ID, true );
			}
		} else {
			if ( '' === $name ) {
				return new \WP_Error( 'pixeccte_mcp_missing_name', 'Give a name for the new menu.' );
			}

			$menu_id = wp_create_nav_menu( $name );

			if ( is_wp_error( $menu_id ) ) {
				return $menu_id;
			}
		}

		$added = $this->add_items( (int) $menu_id, (array) $args['items'], 0 );

		if ( is_wp_error( $added ) ) {
			return $added;
		}

		return List_Menus::summarize( wp_get_nav_menu_object( $menu_id ) );
	}

	/**
	 * Validate items recursively.
	 *
	 * @param array<int, mixed> $items Items.
	 * @param int               $depth Current depth (1 = top level).
	 * @return true|\WP_Error Result.
	 */
	private function check_items( array $items, int $depth ) {
		if ( $depth > self::MAX_DEPTH ) {
			return new \WP_Error( 'pixeccte_mcp_menu_depth', sprintf( 'Menus can be at most %d levels deep.', self::MAX_DEPTH ) );
		}

		foreach ( $items as $index => $item ) {
			if ( ! is_array( $item ) ) {
				return new \WP_Error( 'pixeccte_mcp_invalid_item', sprintf( 'Menu item %d must be an object.', $index + 1 ) );
			}

			$title   = trim( (string) ( $item['title'] ?? '' ) );
			$page_id = (int) ( $item['page_id'] ?? 0 );
			$url     = trim( (string) ( $item['url'] ?? '' ) );
			$label   = '' !== $title ? sprintf( '"%s"', $title ) : sprintf( '%d (level %d)', $index + 1, $depth );

			if ( $page_id ) {
				$post = get_post( $page_id );

				if ( ! $post || ! is_post_type_viewable( $post->post_type ) || 'trash' === $post->post_status ) {
					return new \WP_Error( 'pixeccte_mcp_not_found', sprintf( 'Menu item %s: page %d was not found.', $label, $page_id ) );
				}
			} elseif ( '' === $url ) {
				return new \WP_Error( 'pixeccte_mcp_invalid_item', sprintf( 'Menu item %s needs a url or a page_id.', $label ) );
			} elseif ( '' === $title ) {
				return new \WP_Error( 'pixeccte_mcp_invalid_item', sprintf( 'Menu item with url "%s" needs a title.', $url ) );
			}

			if ( ! empty( $item['children'] ) ) {
				$checked = $this->check_items( (array) $item['children'], $depth + 1 );

				if ( is_wp_error( $checked ) ) {
					return $checked;
				}
			}
		}

		return true;
	}

	/**
	 * Add items recursively.
	 *
	 * @param int               $menu_id   Menu term id.
	 * @param array<int, mixed> $items     Validated items.
	 * @param int               $parent_id Parent menu item id.
	 * @return true|\WP_Error Result.
	 */
	private function add_items( int $menu_id, array $items, int $parent_id ) {
		foreach ( $items as $item ) {
			$page_id = (int) ( $item['page_id'] ?? 0 );
			$data    = array(
				'menu-item-title'     => sanitize_text_field( (string) ( $item['title'] ?? '' ) ),
				'menu-item-status'    => 'publish',
				'menu-item-parent-id' => $parent_id,
			);

			if ( $page_id ) {
				$data['menu-item-type']      = 'post_type';
				$data['menu-item-object']    = get_post_type( $page_id );
				$data['menu-item-object-id'] = $page_id;
			} else {
				$data['menu-item-type'] = 'custom';
				$data['menu-item-url']  = esc_url_raw( (string) $item['url'] );
			}

			$item_id = wp_update_nav_menu_item( $menu_id, 0, $data );

			if ( is_wp_error( $item_id ) ) {
				return $item_id;
			}

			if ( ! empty( $item['children'] ) ) {
				$added = $this->add_items( $menu_id, (array) $item['children'], (int) $item_id );

				if ( is_wp_error( $added ) ) {
					return $added;
				}
			}
		}

		return true;
	}
}
