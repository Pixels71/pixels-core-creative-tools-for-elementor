<?php
/**
 * Element builder for MCP.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp;

use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Validates and normalizes element trees sent by AI clients into Elementor data.
 */
final class Element_Builder {

	public const MAX_ELEMENTS = 200;
	public const MAX_DEPTH    = 12;

	/**
	 * Keys allowed in any settings object besides the element's controls.
	 */
	private const SPECIAL_KEYS = array( '__globals__', '__dynamic__' );

	/**
	 * Element meta settings that are not controls (e.g. the Navigator title Elementor gives item containers).
	 */
	private const META_KEYS = array( '_title' );

	/**
	 * Element IDs already used in the target document.
	 *
	 * @var array<string, bool>
	 */
	private array $used_ids = array();

	/**
	 * Number of elements built in this run.
	 *
	 * @var int
	 */
	private int $count = 0;

	/**
	 * Construct.
	 *
	 * @param array<int, array<string, mixed>> $existing Existing document elements (their IDs are reserved).
	 */
	public function __construct( array $existing = array() ) {
		Document_Store::walk(
			$existing,
			function ( array $element ): void {
				if ( isset( $element['id'] ) ) {
					$this->used_ids[ (string) $element['id'] ] = true;
				}
			}
		);
	}

	/**
	 * Build a list of new elements.
	 *
	 * @param mixed  $elements Raw elements.
	 * @param string $path     Path for error messages.
	 * @param int    $depth    Depth.
	 * @return array<int, array<string, mixed>>|\WP_Error Result.
	 */
	public function build_elements( $elements, string $path = 'elements', int $depth = 0 ) {
		if ( ! is_array( $elements ) || ( ! empty( $elements ) && array_keys( $elements ) !== range( 0, count( $elements ) - 1 ) ) ) {
			return new \WP_Error( 'pixeccte_mcp_invalid_elements', sprintf( '%s must be an array of elements.', $path ) );
		}

		if ( $depth > self::MAX_DEPTH ) {
			return new \WP_Error( 'pixeccte_mcp_too_deep', sprintf( 'Elements are nested deeper than %d levels.', self::MAX_DEPTH ) );
		}

		$built = array();

		foreach ( $elements as $index => $element ) {
			$result = $this->build_element( $element, sprintf( '%s[%d]', $path, $index ), $depth );

			if ( is_wp_error( $result ) ) {
				return $result;
			}

			$built[] = $result;
		}

		return $built;
	}

	/**
	 * Build one element.
	 *
	 * @param mixed  $element Raw element.
	 * @param string $path    Path for error messages.
	 * @param int    $depth   Depth.
	 * @return array<string, mixed>|\WP_Error Result.
	 */
	private function build_element( $element, string $path, int $depth ) {
		if ( ! is_array( $element ) ) {
			return new \WP_Error( 'pixeccte_mcp_invalid_element', sprintf( '%s must be an object.', $path ) );
		}

		if ( ++$this->count > self::MAX_ELEMENTS ) {
			return new \WP_Error( 'pixeccte_mcp_too_many', sprintf( 'Send at most %d elements per call; split the page into several add_elements calls.', self::MAX_ELEMENTS ) );
		}

		$el_type = $element['elType'] ?? ( isset( $element['widgetType'] ) ? 'widget' : '' );

		if ( 'container' === $el_type ) {
			return $this->build_container( $element, $path, $depth );
		}

		if ( 'widget' === $el_type ) {
			return $this->build_widget( $element, $path, $depth );
		}

		return new \WP_Error(
			'pixeccte_mcp_invalid_eltype',
			sprintf( '%s: elType must be "container" or "widget" (sections and columns are not supported; use containers).', $path )
		);
	}

	/**
	 * Build a container.
	 *
	 * @param array<string, mixed> $element Raw element.
	 * @param string               $path    Path.
	 * @param int                  $depth   Depth.
	 * @return array<string, mixed>|\WP_Error Result.
	 */
	private function build_container( array $element, string $path, int $depth ) {
		$container = Widget_Catalog::get_container();

		if ( null === $container ) {
			return new \WP_Error( 'pixeccte_mcp_no_container', 'The Elementor Flexbox Container feature is not active on this site.' );
		}

		$settings = $this->normalize_settings( $element['settings'] ?? array(), $container->get_controls(), $path . ' (container)' );

		if ( is_wp_error( $settings ) ) {
			return $settings;
		}

		$settings = self::without_nulls( $settings );

		$children = $this->build_elements( $element['elements'] ?? array(), $path . '.elements', $depth + 1 );

		if ( is_wp_error( $children ) ) {
			return $children;
		}

		return array(
			'id'       => $this->new_id(),
			'elType'   => 'container',
			'isInner'  => $depth > 0,
			'settings' => $settings,
			'elements' => $children,
		);
	}

	/**
	 * Build a widget.
	 *
	 * @param array<string, mixed> $element Raw element.
	 * @param string               $path    Path.
	 * @param int                  $depth   Depth.
	 * @return array<string, mixed>|\WP_Error Result.
	 */
	private function build_widget( array $element, string $path, int $depth ) {
		$type   = isset( $element['widgetType'] ) && is_string( $element['widgetType'] ) ? $element['widgetType'] : '';
		$widget = '' === $type ? null : Widget_Catalog::get_widget( $type );

		if ( null === $widget ) {
			return new \WP_Error(
				'pixeccte_mcp_unknown_widget',
				sprintf( '%s: unknown widgetType "%s". Call list_widgets for the available names.', $path, $type )
			);
		}

		$settings = $this->normalize_settings( $element['settings'] ?? array(), $widget->get_controls(), sprintf( '%s (%s)', $path, $type ) );

		if ( is_wp_error( $settings ) ) {
			return $settings;
		}

		$settings = self::without_nulls( $settings );

		$nested   = Widget_Catalog::get_nested_info( $widget );
		$children = array();

		if ( null !== $nested ) {
			$raw_children = $element['elements'] ?? array();

			if ( empty( $raw_children ) ) {
				$raw_children = self::default_nested_children( $widget, $nested, $settings );
			}

			foreach ( (array) $raw_children as $index => $child ) {
				if ( ! is_array( $child ) || 'container' !== ( $child['elType'] ?? 'container' ) ) {
					return new \WP_Error(
						'pixeccte_mcp_nested_child',
						sprintf( '%s.elements[%d]: children of %s must be containers (one per "%s" item).', $path, $index, $type, $nested['repeater'] )
					);
				}
			}

			$children = $this->build_elements( $raw_children, $path . '.elements', $depth + 1 );

			if ( is_wp_error( $children ) ) {
				return $children;
			}

			$check = self::check_nested_count( $widget, $nested, $settings, count( $children ), $path );

			if ( is_wp_error( $check ) ) {
				return $check;
			}
		} elseif ( ! empty( $element['elements'] ) ) {
			return new \WP_Error(
				'pixeccte_mcp_widget_children',
				sprintf( '%s: %s cannot contain child elements. Wrap widgets in a container instead.', $path, $type )
			);
		}

		return array(
			'id'         => $this->new_id(),
			'elType'     => 'widget',
			'widgetType' => $type,
			'settings'   => $settings,
			'elements'   => $children,
		);
	}

	/**
	 * Empty item containers for a nested widget sent without children: one per repeater item,
	 * based on the widget's own default children.
	 *
	 * @param \Elementor\Widget_Base $widget   Widget.
	 * @param array<string, mixed>   $nested   Nested info.
	 * @param array<string, mixed>   $settings Normalized settings.
	 * @return array<int, array<string, mixed>>
	 */
	private static function default_nested_children( $widget, array $nested, array $settings ): array {
		$defaults = array_values( (array) $nested['default_children'] );
		$items    = '' === $nested['repeater'] ? null : ( $settings[ $nested['repeater'] ] ?? null );

		if ( ! is_array( $items ) ) {
			$control = '' === $nested['repeater'] ? array() : $widget->get_controls( $nested['repeater'] );
			$items   = is_array( $control['default'] ?? null ) ? $control['default'] : $defaults;
		}

		$children = array();

		foreach ( array_keys( array_values( $items ) ) as $index ) {
			$children[] = $defaults[ $index ] ?? ( $defaults[0] ?? array( 'elType' => 'container' ) );
		}

		return $children;
	}

	/**
	 * Ensure a nested widget has one child container per repeater item.
	 *
	 * @param \Elementor\Widget_Base $widget         Widget.
	 * @param array<string, mixed>   $nested         Nested info.
	 * @param array<string, mixed>   $settings       Normalized settings.
	 * @param int                    $children_count Children count.
	 * @param string                 $path           Path.
	 * @return true|\WP_Error Result.
	 */
	public static function check_nested_count( $widget, array $nested, array $settings, int $children_count, string $path ) {
		if ( '' === $nested['repeater'] ) {
			return true;
		}

		$items = $settings[ $nested['repeater'] ] ?? null;

		if ( ! is_array( $items ) ) {
			$control = $widget->get_controls( $nested['repeater'] );
			$items   = is_array( $control['default'] ?? null ) ? $control['default'] : array();
		}

		if ( count( $items ) !== $children_count ) {
			return new \WP_Error(
				'pixeccte_mcp_nested_mismatch',
				sprintf(
					'%s: "%s" has %d items but %d child containers were given. Send one container per item, in the same order.',
					$path,
					$nested['repeater'],
					count( $items ),
					$children_count
				)
			);
		}

		return true;
	}

	/**
	 * Validate settings against an element's controls and coerce common shorthand values.
	 *
	 * @param mixed                               $settings Raw settings.
	 * @param array<string, array<string, mixed>> $controls Element controls.
	 * @param string                              $label    Label for error messages.
	 * @return array<string, mixed>|\WP_Error Result.
	 */
	public function normalize_settings( $settings, array $controls, string $label ) {
		if ( ! is_array( $settings ) ) {
			return new \WP_Error( 'pixeccte_mcp_invalid_settings', sprintf( '%s: settings must be an object.', $label ) );
		}

		$lookup  = self::build_lookup( $controls );
		$unknown = array();

		foreach ( $settings as $key => $value ) {
			$key = (string) $key;

			if ( in_array( $key, self::SPECIAL_KEYS, true ) ) {
				if ( ! is_array( $value ) ) {
					return new \WP_Error( 'pixeccte_mcp_invalid_globals', sprintf( '%s: %s must be an object of setting => reference.', $label, $key ) );
				}

				foreach ( array_keys( $value ) as $ref_key ) {
					if ( ! isset( $lookup[ (string) $ref_key ] ) ) {
						$unknown[] = $key . '.' . $ref_key;
					}
				}

				continue;
			}

			if ( null === $value || in_array( $key, self::META_KEYS, true ) ) {
				continue;
			}

			if ( ! isset( $lookup[ $key ] ) ) {
				$unknown[] = $key;
				continue;
			}

			$control = $lookup[ $key ];

			if ( Widget_Catalog::is_repeater( $control ) ) {
				$items = $this->normalize_repeater( $value, $control, $label . ' ' . $key );

				if ( is_wp_error( $items ) ) {
					return $items;
				}

				$settings[ $key ] = $items;
				continue;
			}

			$settings[ $key ] = self::coerce_value( $value, $control );
		}

		if ( ! empty( $unknown ) ) {
			return new \WP_Error( 'pixeccte_mcp_unknown_settings', self::unknown_message( $unknown, array_keys( $lookup ), $label ) );
		}

		return $settings;
	}

	/**
	 * Normalize repeater items.
	 *
	 * @param mixed                $items   Raw items.
	 * @param array<string, mixed> $control Repeater control.
	 * @param string               $label   Label.
	 * @return array<int, array<string, mixed>>|\WP_Error Result.
	 */
	private function normalize_repeater( $items, array $control, string $label ) {
		if ( ! is_array( $items ) || ( ! empty( $items ) && array_keys( $items ) !== range( 0, count( $items ) - 1 ) ) ) {
			return new \WP_Error( 'pixeccte_mcp_invalid_repeater', sprintf( '%s must be an array of item objects.', $label ) );
		}

		$fields = array();

		foreach ( (array) ( $control['fields'] ?? array() ) as $field_name => $field ) {
			$fields[ (string) ( $field['name'] ?? $field_name ) ] = $field;
		}

		foreach ( $items as $index => $item ) {
			$item_label = sprintf( '%s[%d]', $label, $index );

			if ( ! is_array( $item ) ) {
				return new \WP_Error( 'pixeccte_mcp_invalid_repeater', sprintf( '%s must be an object.', $item_label ) );
			}

			$id = isset( $item['_id'] ) && is_string( $item['_id'] ) && '' !== $item['_id'] ? $item['_id'] : '';
			unset( $item['_id'] );

			$item = $this->normalize_settings( $item, $fields, $item_label );

			if ( is_wp_error( $item ) ) {
				return $item;
			}

			$items[ $index ] = array_merge( array( '_id' => '' !== $id ? $id : $this->new_id() ), $item );
		}

		return $items;
	}

	/**
	 * Map of every valid setting key to its control (responsive variants included).
	 *
	 * @param array<string, array<string, mixed>> $controls Controls.
	 * @return array<string, array<string, mixed>>
	 */
	private static function build_lookup( array $controls ): array {
		$lookup   = array();
		$suffixes = Widget_Catalog::get_responsive_suffixes();

		foreach ( $controls as $name => $control ) {
			$name = (string) ( $control['name'] ?? $name );
			$type = (string) ( $control['type'] ?? '' );

			if ( in_array( $type, array( 'section', 'tab', 'tabs', 'heading', 'divider', 'raw_html', 'button', 'alert', 'notice', 'deprecated_notice' ), true ) ) {
				continue;
			}

			$lookup[ $name ] = $control;

			if ( ! empty( $control['is_responsive'] ) ) {
				foreach ( $suffixes as $suffix ) {
					$lookup[ $name . $suffix ] = $control;
				}
			}
		}

		return $lookup;
	}

	/**
	 * Coerce shorthand values into Elementor's stored shapes.
	 *
	 * @param mixed                $value   Value.
	 * @param array<string, mixed> $control Control.
	 * @return mixed Result.
	 */
	private static function coerce_value( $value, array $control ) {
		switch ( $control['type'] ?? '' ) {
			case 'slider':
				if ( is_int( $value ) || is_float( $value ) || ( is_string( $value ) && is_numeric( $value ) ) ) {
					return array(
						'unit'  => 'px',
						'size'  => 0 + $value,
						'sizes' => array(),
					);
				}
				break;

			case 'media':
				if ( is_string( $value ) ) {
					return array(
						'url' => esc_url_raw( $value ),
						'id'  => '',
					);
				}
				break;

			case 'url':
				if ( is_string( $value ) ) {
					return array(
						'url'         => esc_url_raw( $value ),
						'is_external' => '',
						'nofollow'    => '',
					);
				}
				break;

			case 'switcher':
				if ( is_bool( $value ) ) {
					return $value ? ( $control['return_value'] ?? 'yes' ) : '';
				}
				break;
		}

		return $value;
	}

	/**
	 * Error message for unknown setting keys, with close matches.
	 *
	 * @param array<int, string> $unknown Unknown keys.
	 * @param array<int, string> $known   Known keys.
	 * @param string             $label   Label.
	 * @return string Result.
	 */
	private static function unknown_message( array $unknown, array $known, string $label ): string {
		$parts = array();

		foreach ( $unknown as $key ) {
			$bare    = preg_replace( '/^__(globals|dynamic)__\./', '', $key );
			$best    = '';
			$best_d  = PHP_INT_MAX;
			$max_gap = max( 3, (int) floor( strlen( $bare ) / 3 ) );

			foreach ( $known as $candidate ) {
				$distance = levenshtein( $bare, $candidate );

				if ( $distance < $best_d ) {
					$best_d = $distance;
					$best   = $candidate;
				}
			}

			$parts[] = ( '' !== $best && $best_d <= $max_gap ) ? sprintf( '%s (did you mean %s?)', $key, $best ) : $key;
		}

		return sprintf(
			'%s: unknown settings: %s. Call get_widget_schema for the valid names.',
			$label,
			implode( ', ', $parts )
		);
	}

	/**
	 * A new element ID unique within the document.
	 *
	 * @return string Result.
	 */
	public function new_id(): string {
		do {
			$id = Utils::generate_random_string();
		} while ( isset( $this->used_ids[ $id ] ) );

		$this->used_ids[ $id ] = true;

		return $id;
	}

	/**
	 * Drop null values (they only mean "use the default").
	 *
	 * @param array<string, mixed> $settings Settings.
	 * @return array<string, mixed> Result.
	 */
	private static function without_nulls( array $settings ): array {
		return array_filter(
			$settings,
			static function ( $value ): bool {
				return null !== $value;
			}
		);
	}
}
