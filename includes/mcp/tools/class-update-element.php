<?php
/**
 * MCP tool: update_element.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp\Tools;

use PixelsCoreCreativeToolsForElementor\Mcp\Document_Store;
use PixelsCoreCreativeToolsForElementor\Mcp\Element_Builder;
use PixelsCoreCreativeToolsForElementor\Mcp\Widget_Catalog;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Changes settings of one element.
 */
final class Update_Element extends Tool {

	/**
	 * Name.
	 *
	 * @return string Result.
	 */
	public function name(): string {
		return 'update_element';
	}

	/**
	 * Title.
	 *
	 * @return string Result.
	 */
	public function title(): string {
		return 'Update element';
	}

	/**
	 * Description.
	 *
	 * @return string Result.
	 */
	public function description(): string {
		return 'Changes the settings of one container or widget. Only the given settings change; set a setting to null to reset it to its default. "__globals__" entries are merged the same way. Repeater settings (e.g. a list of tabs) are replaced as a whole.';
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
				'page_id'       => array(
					'type'    => 'integer',
					'minimum' => 1,
				),
				'element_id'    => array(
					'type'      => 'string',
					'minLength' => 1,
				),
				'settings'      => array(
					'type'        => 'object',
					'description' => 'Settings to change, e.g. {"title": "New heading", "align_mobile": "center"}.',
				),
				'expected_hash' => array(
					'type'        => 'string',
					'default'     => '',
					'description' => 'content_hash from your last read; the call fails if the page changed since.',
				),
			),
			'required'   => array( 'page_id', 'element_id', 'settings' ),
		);
	}

	/**
	 * Execute.
	 *
	 * @param array<string, mixed> $args Args.
	 * @return array<string, mixed>|\WP_Error Result.
	 */
	public function execute( array $args ) {
		$page_id    = (int) $args['page_id'];
		$element_id = (string) $args['element_id'];
		$document   = Document_Store::get_document( $page_id, true );

		if ( is_wp_error( $document ) ) {
			return $document;
		}

		$current = Document_Store::get_elements( $document );
		$check   = Document_Store::check_hash( $current, (string) $args['expected_hash'] );

		if ( is_wp_error( $check ) ) {
			return $check;
		}

		$found = Document_Store::find( $current, $element_id );

		if ( null === $found ) {
			return new \WP_Error( 'pixeccte_mcp_not_found', sprintf( 'No element with id "%s" on page %d.', $element_id, $page_id ) );
		}

		$element = $found['element'];
		$widget  = null;

		if ( 'widget' === ( $element['elType'] ?? '' ) ) {
			$widget = Widget_Catalog::get_widget( (string) ( $element['widgetType'] ?? '' ) );

			if ( null === $widget ) {
				return new \WP_Error( 'pixeccte_mcp_unknown_widget', sprintf( 'Widget "%s" is not available (disabled or its plugin is inactive).', $element['widgetType'] ?? '' ) );
			}

			$controls = $widget->get_controls();
			$label    = sprintf( '%s (%s)', $element_id, $element['widgetType'] );
		} else {
			$container = Widget_Catalog::get_container();
			$controls  = $container ? $container->get_controls() : array();
			$label     = $element_id . ' (container)';
		}

		$changes = ( new Element_Builder( $current ) )->normalize_settings( (array) $args['settings'], $controls, $label );

		if ( is_wp_error( $changes ) ) {
			return $changes;
		}

		$settings = self::merge_settings( is_array( $element['settings'] ?? null ) ? $element['settings'] : array(), $changes );

		if ( null !== $widget ) {
			$nested = Widget_Catalog::get_nested_info( $widget );

			if ( null !== $nested && array_key_exists( $nested['repeater'], $changes ) ) {
				$count = Element_Builder::check_nested_count( $widget, $nested, $settings, count( (array) ( $element['elements'] ?? array() ) ), $label );

				if ( is_wp_error( $count ) ) {
					return new \WP_Error(
						'pixeccte_mcp_nested_mismatch',
						$count->get_error_message() . ' To change the number of items, delete the widget and add it again with the new items and one container per item.'
					);
				}
			}
		}

		$updated = Document_Store::modify_element(
			$current,
			$element_id,
			static function ( array $item ) use ( $settings ): array {
				$item['settings'] = $settings;

				return $item;
			}
		);

		$saved = Document_Store::save( $document, $updated );

		if ( is_wp_error( $saved ) ) {
			return $saved;
		}

		$after = Document_Store::get_elements( $document );
		$found = Document_Store::find( $after, $element_id );

		return array(
			'page_id'      => $page_id,
			'content_hash' => Document_Store::hash( $after ),
			'element'      => null === $found ? null : Document_Store::summarize( $found['element'], true ),
		);
	}

	/**
	 * Merge changes into settings; null removes a key.
	 *
	 * @param array<string, mixed> $settings Current settings.
	 * @param array<string, mixed> $changes  Changes.
	 * @return array<string, mixed> Result.
	 */
	private static function merge_settings( array $settings, array $changes ): array {
		foreach ( $changes as $key => $value ) {
			if ( in_array( $key, array( '__globals__', '__dynamic__' ), true ) && is_array( $value ) ) {
				$merged = is_array( $settings[ $key ] ?? null ) ? $settings[ $key ] : array();

				foreach ( $value as $ref_key => $ref ) {
					if ( null === $ref || '' === $ref ) {
						unset( $merged[ $ref_key ] );
					} else {
						$merged[ $ref_key ] = $ref;
					}
				}

				$settings[ $key ] = $merged;
				continue;
			}

			if ( null === $value ) {
				unset( $settings[ $key ] );
				continue;
			}

			$settings[ $key ] = $value;
		}

		return $settings;
	}
}
