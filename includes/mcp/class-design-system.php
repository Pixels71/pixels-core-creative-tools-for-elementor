<?php
/**
 * Global colors and fonts (Elementor active kit) for MCP.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp;

use Elementor\Plugin as Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Reads and writes the global colors and typography of the active Elementor kit (Site Settings).
 */
final class Design_System {

	/**
	 * Built-in global ids every kit has.
	 */
	const SYSTEM_IDS = array( 'primary', 'secondary', 'text', 'accent' );

	/**
	 * Global colors and typography of the active kit.
	 *
	 * @return array<string, mixed> Result.
	 */
	public static function get(): array {
		$kit = Elementor::$instance->kits_manager->get_active_kit_for_frontend();

		if ( ! $kit ) {
			return array();
		}

		// Documents are cached per request; read the saved settings, not a copy from before an update.
		$kit = Elementor::$instance->documents->get( $kit->get_id(), false );

		$colors = array();

		foreach ( array( 'system_colors', 'custom_colors' ) as $key ) {
			foreach ( (array) $kit->get_settings_for_display( $key ) as $color ) {
				if ( empty( $color['_id'] ) ) {
					continue;
				}

				$colors[] = array(
					'title' => $color['title'] ?? $color['_id'],
					'value' => $color['color'] ?? '',
					'ref'   => 'globals/colors?id=' . $color['_id'],
				);
			}
		}

		$fonts = array();

		foreach ( array( 'system_typography', 'custom_typography' ) as $key ) {
			foreach ( (array) $kit->get_settings_for_display( $key ) as $font ) {
				if ( empty( $font['_id'] ) ) {
					continue;
				}

				$fonts[] = array(
					'title'  => $font['title'] ?? $font['_id'],
					'family' => $font['typography_font_family'] ?? '',
					'weight' => $font['typography_font_weight'] ?? '',
					'ref'    => 'globals/typography?id=' . $font['_id'],
				);
			}
		}

		$width = $kit->get_settings_for_display( 'container_width' );

		return array(
			'colors'          => $colors,
			'typography'      => $fonts,
			'container_width' => is_array( $width ) ? ( $width['size'] ?? null ) : null,
			'usage'           => 'Reference globals in settings, e.g. {"__globals__": {"title_color": "globals/colors?id=primary", "title_typography_typography": "globals/typography?id=primary"}}.',
		);
	}

	/**
	 * Update global colors and fonts.
	 *
	 * @param array<int, array<string, mixed>> $colors Items: {id?, title?, color}. A system id (primary, ...) updates that color,
	 *                                                 an existing custom title updates it, anything else adds a custom color.
	 * @param array<int, array<string, mixed>> $fonts  Items: {id?, title?, family?, weight?}, matched the same way.
	 * @return array<string, mixed>|\WP_Error Updated design.
	 */
	public static function update( array $colors, array $fonts ) {
		$kit = Elementor::$instance->kits_manager->get_active_kit();

		if ( ! $kit || ! $kit->get_id() ) {
			return new \WP_Error( 'pixeccte_mcp_no_kit', 'Elementor has no active kit (Site Settings) on this site.' );
		}

		if ( ! $kit->is_editable_by_current_user() ) {
			return new \WP_Error( 'pixeccte_mcp_forbidden', 'You are not allowed to change the Elementor Site Settings.' );
		}

		$settings = $kit->get_settings();
		$settings = is_array( $settings ) ? $settings : array();

		foreach ( $colors as $index => $item ) {
			$value = self::sanitize_color( (string) ( $item['color'] ?? '' ) );

			if ( '' === $value ) {
				return new \WP_Error( 'pixeccte_mcp_invalid_color', sprintf( 'Color %d: "%s" is not a valid color. Use #RRGGBB, #RRGGBBAA or rgba(r,g,b,a).', $index + 1, (string) ( $item['color'] ?? '' ) ) );
			}

			$settings = self::upsert( $settings, 'system_colors', 'custom_colors', $item, array( 'color' => $value ) );

			if ( is_wp_error( $settings ) ) {
				return $settings;
			}
		}

		foreach ( $fonts as $index => $item ) {
			$fields = array( 'typography_typography' => 'custom' );

			if ( isset( $item['family'] ) && '' !== trim( (string) $item['family'] ) ) {
				$fields['typography_font_family'] = sanitize_text_field( (string) $item['family'] );
			}

			if ( isset( $item['weight'] ) && '' !== (string) $item['weight'] ) {
				$weight = (string) $item['weight'];

				if ( ! in_array( $weight, array( '100', '200', '300', '400', '500', '600', '700', '800', '900', 'normal', 'bold' ), true ) ) {
					return new \WP_Error( 'pixeccte_mcp_invalid_weight', sprintf( 'Font %d: weight must be 100-900, normal or bold.', $index + 1 ) );
				}

				$fields['typography_font_weight'] = $weight;
			}

			if ( 1 === count( $fields ) ) {
				return new \WP_Error( 'pixeccte_mcp_invalid_font', sprintf( 'Font %d: give a family, a weight or both.', $index + 1 ) );
			}

			$settings = self::upsert( $settings, 'system_typography', 'custom_typography', $item, $fields );

			if ( is_wp_error( $settings ) ) {
				return $settings;
			}
		}

		$kit->save( array( 'settings' => $settings ) );

		// Every page's CSS embeds the globals, so regenerate it.
		Elementor::$instance->files_manager->clear_cache();

		return self::get();
	}

	/**
	 * Update a system item by id, a custom item by id or title, or append a new custom item.
	 *
	 * @param array<string, mixed> $settings   Kit settings.
	 * @param string               $system_key Repeater of built-in items.
	 * @param string               $custom_key Repeater of custom items.
	 * @param array<string, mixed> $item       Requested item.
	 * @param array<string, mixed> $fields     Fields to write.
	 * @return array<string, mixed>|\WP_Error Result.
	 */
	private static function upsert( array $settings, string $system_key, string $custom_key, array $item, array $fields ) {
		$id    = sanitize_key( (string) ( $item['id'] ?? '' ) );
		$title = sanitize_text_field( (string) ( $item['title'] ?? '' ) );

		if ( '' === $id && '' === $title ) {
			return new \WP_Error( 'pixeccte_mcp_missing_id', 'Each item needs an id (primary, secondary, text, accent or a custom id) or a title for a new custom global.' );
		}

		foreach ( array( $system_key, $custom_key ) as $key ) {
			$items = isset( $settings[ $key ] ) && is_array( $settings[ $key ] ) ? $settings[ $key ] : array();

			foreach ( $items as $i => $existing ) {
				$matches_id    = '' !== $id && ( $existing['_id'] ?? '' ) === $id;
				$matches_title = '' === $id && $custom_key === $key && '' !== $title && 0 === strcasecmp( (string) ( $existing['title'] ?? '' ), $title );

				if ( $matches_id || $matches_title ) {
					// A title renames a global found by id; a global found by its title keeps its own spelling.
					$items[ $i ] = array_merge( $existing, $fields, $matches_id && '' !== $title ? array( 'title' => $title ) : array() );

					$settings[ $key ] = $items;

					return $settings;
				}
			}
		}

		if ( in_array( $id, self::SYSTEM_IDS, true ) ) {
			// A system global missing from an old kit: add it back.
			$settings[ $system_key ][] = array_merge(
				array(
					'_id'   => $id,
					'title' => '' !== $title ? $title : ucfirst( $id ),
				),
				$fields
			);

			return $settings;
		}

		if ( '' === $title ) {
			return new \WP_Error( 'pixeccte_mcp_not_found', sprintf( 'No global with id "%s". Give a title to add it as a new custom global.', $id ) );
		}

		$settings[ $custom_key ][] = array_merge(
			array(
				'_id'   => '' !== $id ? $id : substr( md5( $custom_key . $title . microtime() ), 0, 7 ),
				'title' => $title,
			),
			$fields
		);

		return $settings;
	}

	/**
	 * Accept hex (#RGB, #RRGGBB, #RRGGBBAA) and rgb()/rgba() colors.
	 *
	 * @param string $color Color.
	 * @return string Sanitized color or ''.
	 */
	private static function sanitize_color( string $color ): string {
		$color = trim( $color );

		if ( preg_match( '/^#([0-9a-f]{3}|[0-9a-f]{6}|[0-9a-f]{8})$/i', $color ) ) {
			return strtoupper( $color );
		}

		if ( preg_match( '/^rgba?\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*(,\s*(0|1|0?\.\d+)\s*)?\)$/i', $color ) ) {
			return strtolower( preg_replace( '/\s+/', '', $color ) );
		}

		return '';
	}
}
