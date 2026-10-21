<?php
/**
 * Widget catalog for MCP.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp;

use Elementor\Plugin as Elementor;
use Elementor\Controls_Stack;
use PixelsCoreCreativeToolsForElementor\Admin\Widget_Settings;
use PixelsCoreCreativeToolsForElementor\Widget_Registry;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Describes the registered Elementor widgets and their controls in a compact, AI-friendly form.
 */
final class Widget_Catalog {

	/**
	 * Control types that only shape the editor UI and hold no value.
	 */
	private const UI_CONTROL_TYPES = array(
		'section',
		'tab',
		'tabs',
		'heading',
		'divider',
		'raw_html',
		'button',
		'alert',
		'notice',
		'deprecated_notice',
	);

	/**
	 * Value format hints, sent once per schema for the control types it uses.
	 */
	private const VALUE_FORMATS = array(
		'slider'                   => '{"unit":"px","size":24} (a bare number is accepted and treated as px)',
		'dimensions'               => '{"unit":"px","top":"10","right":"10","bottom":"10","left":"10","isLinked":false}',
		'gaps'                     => '{"unit":"px","column":"20","row":"20","isLinked":true}',
		'media'                    => '{"url":"https://...","id":123} (a URL string is accepted)',
		'url'                      => '{"url":"https://...","is_external":"on","nofollow":""} (a URL string is accepted)',
		'icons'                    => '{"value":"fas fa-star","library":"fa-solid"}',
		'color'                    => '"#1f2937" or "rgba(0,0,0,0.5)"; prefer __globals__ with a global color',
		'switcher'                 => '"yes" for on, "" for off (true/false accepted)',
		'choose'                   => 'one of the option keys',
		'select'                   => 'one of the option keys',
		'select2'                  => 'an option key, or an array of keys when multiple',
		'repeater'                 => 'array of item objects using the listed fields (_id is generated)',
		'nested-elements-repeater' => 'array of item objects using the listed fields (_id is generated); one child container per item',
		'gallery'                  => 'array of {"id":123,"url":"https://..."}',
		'number'                   => 'number',
		'wysiwyg'                  => 'HTML string',
		'popover_toggle'           => '"custom" (or "yes") to enable the group, "" to leave it off',
	);

	/**
	 * Active Pixels widget classes mapped to their registry info.
	 *
	 * @var array<string, array<string, mixed>>|null
	 */
	private static ?array $pixels_classes = null;

	/**
	 * Elementor widget instance by name.
	 *
	 * @param string $name Widget name.
	 * @return \Elementor\Widget_Base|null Result.
	 */
	public static function get_widget( string $name ) {
		$widget = Elementor::$instance->widgets_manager->get_widget_types( $name );

		return $widget instanceof \Elementor\Widget_Base && self::is_supported( $name, $widget ) ? $widget : null;
	}

	/**
	 * Whether the MCP builder can produce data for this widget.
	 *
	 * Elementor v4 atomic widgets store typed props instead of control values, and the legacy
	 * Inner Section needs sections/columns; both are left out until they are supported.
	 *
	 * @param string                 $name   Widget name.
	 * @param \Elementor\Widget_Base $widget Widget.
	 * @return bool Result.
	 */
	private static function is_supported( string $name, $widget ): bool {
		if ( 'inner-section' === $name || 0 === strpos( $name, 'wp-widget-' ) ) {
			return false;
		}

		return ! $widget instanceof \Elementor\Modules\AtomicWidgets\Elements\Base\Atomic_Widget_Base;
	}

	/**
	 * The container element type.
	 *
	 * @return \Elementor\Element_Base|null Result.
	 */
	public static function get_container() {
		$container = Elementor::$instance->elements_manager->get_element_types( 'container' );

		return $container instanceof \Elementor\Element_Base ? $container : null;
	}

	/**
	 * Whether a control holds a list of items (regular or nested-elements repeater).
	 *
	 * @param array<string, mixed> $control Control.
	 * @return bool Result.
	 */
	public static function is_repeater( array $control ): bool {
		return in_array( $control['type'] ?? '', array( 'repeater', 'nested-elements-repeater' ), true );
	}

	/**
	 * Whether the widget nests child containers (tabs, accordion, carousel, ...).
	 *
	 * @param \Elementor\Widget_Base $widget Widget.
	 * @return bool Result.
	 */
	public static function is_nested( $widget ): bool {
		return $widget instanceof \Elementor\Modules\NestedElements\Base\Widget_Nested_Base;
	}

	/**
	 * Nested widget wiring: which repeater maps to the child containers, and the default children.
	 *
	 * @param \Elementor\Widget_Base $widget Widget.
	 * @return array<string, mixed>|null Result.
	 */
	public static function get_nested_info( $widget ): ?array {
		if ( ! self::is_nested( $widget ) ) {
			return null;
		}

		$config        = $widget->get_config();
		$defaults      = $config['defaults'] ?? array();
		$title_setting = (string) ( $defaults['repeater_title_setting'] ?? '' );
		$repeater      = '';

		foreach ( $widget->get_controls() as $name => $control ) {
			if ( ! self::is_repeater( $control ) || empty( $control['fields'] ) ) {
				continue;
			}

			foreach ( (array) $control['fields'] as $field_key => $field ) {
				$field_name = (string) ( $field['name'] ?? $field_key );

				if ( $field_name === $title_setting ) {
					$repeater = (string) $name;
					break 2;
				}
			}
		}

		return array(
			'repeater'         => $repeater,
			'title_setting'    => $title_setting,
			'default_children' => is_array( $defaults['elements'] ?? null ) ? $defaults['elements'] : array(),
		);
	}

	/**
	 * Active responsive suffixes, e.g. array( '_tablet', '_mobile' ).
	 *
	 * @return array<int, string>
	 */
	public static function get_responsive_suffixes(): array {
		$suffixes = array();

		foreach ( array_keys( Elementor::$instance->breakpoints->get_active_breakpoints() ) as $device ) {
			$suffixes[] = '_' . $device;
		}

		return $suffixes;
	}

	/**
	 * Active breakpoints for site info.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function get_breakpoints(): array {
		$breakpoints = array();

		foreach ( Elementor::$instance->breakpoints->get_active_breakpoints() as $device => $breakpoint ) {
			$breakpoints[ $device ] = array(
				'label'     => $breakpoint->get_label(),
				'value'     => $breakpoint->get_value(),
				'direction' => $breakpoint->get_direction(),
				'suffix'    => '_' . $device,
			);
		}

		return $breakpoints;
	}

	/**
	 * Map of active Pixels widget classes.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	private static function get_pixels_classes(): array {
		if ( null !== self::$pixels_classes ) {
			return self::$pixels_classes;
		}

		self::$pixels_classes = array();
		$registry             = Widget_Registry::instance();

		foreach ( Widget_Settings::instance()->get_active_slugs() as $slug ) {
			$config = $registry->get( $slug );

			if ( null === $config ) {
				continue;
			}

			self::$pixels_classes[ ltrim( $registry->get_widget_class( $slug ), '\\' ) ] = array(
				'slug'        => $slug,
				'tier'        => $config['tier'] ?? 'free',
				'description' => $config['description'] ?? '',
			);
		}

		return self::$pixels_classes;
	}

	/**
	 * Where a widget comes from: pixels, elementor or other.
	 *
	 * @param \Elementor\Widget_Base $widget Widget.
	 * @return string Result.
	 */
	public static function get_source( $widget ): string {
		$class = get_class( $widget );

		if ( isset( self::get_pixels_classes()[ $class ] ) ) {
			return 'pixels';
		}

		if ( 0 === strpos( $class, 'Elementor\\' ) ) {
			return 'elementor';
		}

		return 'other';
	}

	/**
	 * Widgets usable on a page.
	 *
	 * @param string $source all|pixels|elementor|other.
	 * @return array<int, array<string, mixed>>
	 */
	public static function list_widgets( string $source = 'all' ): array {
		$widgets = array();
		$pixels  = self::get_pixels_classes();

		foreach ( Elementor::$instance->widgets_manager->get_widget_types() as $name => $widget ) {
			if ( ! self::is_supported( (string) $name, $widget ) || ! $widget->show_in_panel() ) {
				continue;
			}

			$widget_source = self::get_source( $widget );

			if ( 'all' !== $source && $source !== $widget_source ) {
				continue;
			}

			$entry = array(
				'name'       => (string) $name,
				'title'      => (string) $widget->get_title(),
				'source'     => $widget_source,
				'categories' => array_values( (array) $widget->get_categories() ),
			);

			if ( 'pixels' === $widget_source ) {
				$info                 = $pixels[ get_class( $widget ) ];
				$entry['tier']        = $info['tier'];
				$entry['description'] = $info['description'];
			}

			if ( self::is_nested( $widget ) ) {
				$entry['nested'] = true;
			}

			$widgets[] = $entry;
		}

		// Pixels first, then Elementor, then everything else.
		$order = array(
			'pixels'    => 0,
			'elementor' => 1,
			'other'     => 2,
		);

		usort(
			$widgets,
			static function ( array $a, array $b ) use ( $order ): int {
				return array( $order[ $a['source'] ], $a['title'] ) <=> array( $order[ $b['source'] ], $b['title'] );
			}
		);

		return $widgets;
	}

	/**
	 * Above this size (JSON characters) a schema without a section filter is returned as an index.
	 */
	public const MAX_SCHEMA_CHARS = 20000;

	/**
	 * Compact schema for a widget (or "container").
	 *
	 * @param string             $name     Widget name or "container".
	 * @param string             $scope    content|style|advanced|all.
	 * @param array<int, string> $only     Optional section ids or labels to limit the output to.
	 * @return array<string, mixed>|\WP_Error Result.
	 */
	public static function get_schema( string $name, string $scope = 'content', array $only = array() ) {
		$element = 'container' === $name ? self::get_container() : self::get_widget( $name );

		if ( null === $element ) {
			return new \WP_Error(
				'pixeccte_mcp_unknown_widget',
				sprintf( 'Unknown widget "%s". Call list_widgets to see the available names.', $name )
			);
		}

		$sections   = array();
		$current    = null;
		$used_types = array();

		foreach ( $element->get_controls() as $control_name => $control ) {
			$type = (string) ( $control['type'] ?? '' );
			$tab  = (string) ( $control['tab'] ?? Controls_Stack::TAB_CONTENT );

			if ( ! self::tab_in_scope( $tab, $scope ) ) {
				continue;
			}

			if ( 'section' === $type ) {
				$current              = (string) $control_name;
				$sections[ $current ] = array(
					'id'       => $current,
					'section'  => wp_strip_all_tags( (string) ( $control['label'] ?? $control_name ) ),
					'tab'      => $tab,
					'controls' => array(),
				);
				continue;
			}

			if ( in_array( $type, self::UI_CONTROL_TYPES, true ) || 'hidden' === $type ) {
				continue;
			}

			$section_key = (string) ( $control['section'] ?? $current ?? 'general' );

			if ( ! isset( $sections[ $section_key ] ) ) {
				$sections[ $section_key ] = array(
					'id'       => $section_key,
					'section'  => $section_key,
					'tab'      => $tab,
					'controls' => array(),
				);
			}

			$controls = &$sections[ $section_key ]['controls'];

			if ( ! empty( $control['groupPrefix'] ) ) {
				self::add_group_field( $controls, (string) $control_name, $control, $used_types );
				unset( $controls );
				continue;
			}

			$controls[]          = self::describe_control( (string) $control_name, $control, $used_types );
			$used_types[ $type ] = true;
			unset( $controls );
		}

		$only     = array_map( 'strtolower', array_filter( array_map( 'strval', $only ) ) );
		$sections = array_values(
			array_filter(
				$sections,
				static function ( array $section ) use ( $only ): bool {
					if ( empty( $section['controls'] ) ) {
						return false;
					}

					return empty( $only )
						|| in_array( strtolower( $section['id'] ), $only, true )
						|| in_array( strtolower( $section['section'] ), $only, true );
				}
			)
		);

		$effect_notes = class_exists( Effect_Catalog::class ) ? Effect_Catalog::section_notes() : array();

		foreach ( $sections as &$section ) {
			$section['controls'] = array_values( $section['controls'] );

			if ( isset( $effect_notes[ $section['id'] ] ) ) {
				$section['about'] = $effect_notes[ $section['id'] ]['about'];
			}
		}
		unset( $section );

		$schema = array(
			'name'     => $name,
			'title'    => (string) $element->get_title(),
			'scope'    => $scope,
			'sections' => $sections,
		);

		if ( 'container' !== $name ) {
			$schema['source'] = self::get_source( $element );
		}

		$nested = 'container' === $name ? null : self::get_nested_info( $element );

		if ( null !== $nested ) {
			$schema['nested'] = array(
				'repeater'      => $nested['repeater'],
				'title_setting' => $nested['title_setting'],
				'rule'          => sprintf( 'Give this widget one child container per item in "%s", in the same order. Put the content of each item inside its container.', $nested['repeater'] ),
			);
		}

		if ( empty( $only ) && strlen( (string) wp_json_encode( $sections ) ) > self::MAX_SCHEMA_CHARS ) {
			$schema['sections'] = self::index_sections( $sections );
			$schema['index']    = 'This scope is large, so only section ids and setting names are listed. Call get_widget_schema again with "sections": ["<id>", ...] for the full details of the sections you need.';

			return $schema;
		}

		$schema['responsive_suffixes'] = self::get_responsive_suffixes();
		$schema['value_formats']       = array_intersect_key( self::VALUE_FORMATS, $used_types );
		$schema['notes']               = array(
			'Only set what differs from the default; omitted settings use their defaults.',
			'Responsive controls accept device variants by suffix, e.g. "align_mobile".',
			'Group fields are set by prefix + field, e.g. "title_typography_font_size"; set the group toggle (e.g. "title_typography_typography": "custom") to enable it.',
			'Color and typography controls can use globals: {"__globals__": {"title_color": "globals/colors?id=primary"}}.',
		);

		return $schema;
	}

	/**
	 * Section index: ids, labels and setting names only.
	 *
	 * @param array<int, array<string, mixed>> $sections Sections.
	 * @return array<int, array<string, mixed>>
	 */
	private static function index_sections( array $sections ): array {
		$index = array();

		foreach ( $sections as $section ) {
			$names = array();

			foreach ( $section['controls'] as $control ) {
				$names[] = isset( $control['group'] ) ? $control['prefix'] . '*' : $control['name'];
			}

			$entry = array(
				'id'      => $section['id'],
				'section' => $section['section'],
				'tab'     => $section['tab'],
			);

			// Effect sections list dozens of cryptic keys; point to the recipes instead.
			if ( isset( $section['about'] ) ) {
				$entry['about'] = $section['about'];
			}

			$entry['settings'] = $names;
			$index[]           = $entry;
		}

		return $index;
	}

	/**
	 * Whether a control tab is in the requested scope.
	 *
	 * @param string $tab   Tab.
	 * @param string $scope Scope.
	 * @return bool Result.
	 */
	private static function tab_in_scope( string $tab, string $scope ): bool {
		switch ( $scope ) {
			case 'all':
				return true;
			case 'style':
				return 'style' === $tab;
			case 'advanced':
				return ! in_array( $tab, array( 'content', 'layout', 'style' ), true );
			default:
				return in_array( $tab, array( 'content', 'layout' ), true );
		}
	}

	/**
	 * Describe a single control.
	 *
	 * @param string               $name       Name.
	 * @param array<string, mixed> $control    Control.
	 * @param array<string, bool>  $used_types Collected types (by reference).
	 * @return array<string, mixed> Result.
	 */
	private static function describe_control( string $name, array $control, array &$used_types ): array {
		$type  = (string) ( $control['type'] ?? '' );
		$entry = array(
			'name' => $name,
			'type' => $type,
		);

		$label = wp_strip_all_tags( (string) ( $control['label'] ?? '' ) );

		if ( '' !== $label ) {
			$entry['label'] = $label;
		}

		if ( ! empty( $control['is_responsive'] ) ) {
			$entry['responsive'] = true;
		}

		// The url control's "options" lists its link toggles, not values.
		$options = 'url' === $type ? null : self::describe_options( $control['options'] ?? null );

		if ( null !== $options ) {
			$entry['options'] = array_map( 'strval', array_keys( $options ) );
		}

		if ( ! empty( $control['multiple'] ) ) {
			$entry['multiple'] = true;
		}

		if ( ! self::is_empty_default( $control['default'] ?? null ) && ! self::is_repeater( $control ) ) {
			$entry['default'] = $control['default'];
		}

		if ( ! empty( $control['condition'] ) ) {
			$entry['condition'] = $control['condition'];
		}

		if ( self::is_repeater( $control ) && ! empty( $control['fields'] ) && is_array( $control['fields'] ) ) {
			$fields = array();

			foreach ( $control['fields'] as $field_name => $field ) {
				$field_type = (string) ( $field['type'] ?? '' );

				if ( in_array( $field_type, self::UI_CONTROL_TYPES, true ) || 'hidden' === $field_type ) {
					continue;
				}

				$fields[]                  = self::describe_control( (string) ( $field['name'] ?? $field_name ), $field, $used_types );
				$used_types[ $field_type ] = true;
			}

			$entry['fields'] = $fields;

			if ( is_array( $control['default'] ?? null ) ) {
				$entry['default_items'] = count( $control['default'] );
			}
		}

		return $entry;
	}

	/**
	 * Collapse a group control field (typography, border, background, ...) into one entry per prefix.
	 *
	 * @param array<int|string, array<string, mixed>> $controls   Section controls (by reference).
	 * @param string                                  $name       Control name.
	 * @param array<string, mixed>                    $control    Control.
	 * @param array<string, bool>                     $used_types Collected types (by reference).
	 */
	private static function add_group_field( array &$controls, string $name, array $control, array &$used_types ): void {
		$prefix = (string) $control['groupPrefix'];
		$key    = 'group:' . $prefix;
		$field  = substr( $name, strlen( $prefix ) );
		$type   = (string) ( $control['type'] ?? '' );

		if ( ! isset( $controls[ $key ] ) ) {
			$controls[ $key ] = array(
				'group'  => (string) ( $control['groupType'] ?? rtrim( $prefix, '_' ) ),
				'prefix' => $prefix,
				'fields' => array(),
			);
		}

		if ( 'popover_toggle' === $type ) {
			$controls[ $key ]['toggle'] = $name;
			$used_types[ $type ]        = true;
			return;
		}

		$description = $field . ':' . $type;

		if ( ! empty( $control['is_responsive'] ) ) {
			$description .= ':responsive';
		}

		$options = self::describe_options( $control['options'] ?? null );

		if ( null !== $options && count( $options ) <= 12 ) {
			$description .= ':' . implode( '|', array_map( 'strval', array_keys( $options ) ) );
		}

		$controls[ $key ]['fields'][] = $description;
		$used_types[ $type ]          = true;
	}

	/**
	 * Option list as key => label.
	 *
	 * @param mixed $options Options.
	 * @return array<string, string>|null Result.
	 */
	private static function describe_options( $options ): ?array {
		if ( ! is_array( $options ) || empty( $options ) ) {
			return null;
		}

		$described = array();

		foreach ( $options as $key => $option ) {
			if ( is_array( $option ) ) {
				$option = $option['title'] ?? $option['label'] ?? (string) $key;
			}

			$described[ (string) $key ] = wp_strip_all_tags( (string) $option );
		}

		return $described;
	}

	/**
	 * Whether a default carries no information.
	 *
	 * @param mixed $value Value.
	 * @return bool Result.
	 */
	private static function is_empty_default( $value ): bool {
		if ( null === $value || '' === $value || array() === $value ) {
			return true;
		}

		if ( is_array( $value ) ) {
			foreach ( $value as $key => $item ) {
				if ( 'unit' === $key || 'sizes' === $key || 'isLinked' === $key ) {
					continue;
				}

				if ( ! self::is_empty_default( $item ) ) {
					return false;
				}
			}

			return true;
		}

		return false;
	}
}
