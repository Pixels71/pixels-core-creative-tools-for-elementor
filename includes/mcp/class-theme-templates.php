<?php
/**
 * Theme Builder template access for MCP.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp;

use Elementor\Plugin as Elementor;
use PixelsCoreCreativeToolsForElementor\Plugin;
use PixelsCoreCreativeToolsForElementor\Theme_Builder\Target_Rules_Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Reads and writes Theme Builder templates (pixeccte-theme posts) and their display conditions,
 * using the same meta the Theme Builder admin screen saves.
 */
final class Theme_Templates {

	const POST_TYPE    = 'pixeccte-theme';
	const META_INCLUDE = 'pixeccte_hf_target_include_locations';
	const META_EXCLUDE = 'pixeccte_hf_target_exclude_locations';

	/**
	 * Whether the Theme Builder is loaded (it can be switched off from Pro).
	 *
	 * @return bool Result.
	 */
	public static function is_available(): bool {
		return post_type_exists( self::POST_TYPE ) && class_exists( Target_Rules_Fields::class );
	}

	/**
	 * Template types the site supports (type => label).
	 *
	 * @return array<string, string>
	 */
	public static function get_types(): array {
		return Plugin::get_theme_template_types();
	}

	/**
	 * Display condition rules, as rule key => label (flattened from the admin select groups).
	 *
	 * @return array<string, string>
	 */
	public static function get_rule_options(): array {
		$options = array();

		foreach ( Target_Rules_Fields::get_location_selections() as $group ) {
			foreach ( (array) ( $group['value'] ?? array() ) as $key => $label ) {
				$options[ (string) $key ] = wp_strip_all_tags( (string) $label );
			}
		}

		return $options;
	}

	/**
	 * Build a stored condition value from rule keys and specific targets.
	 *
	 * @param array<int, string> $rules    Rule keys (e.g. basic-global, page|all).
	 * @param array<int, string> $specific Specific targets (post-12, tax-5, tax-5-single).
	 * @param string             $label    Argument name, for error messages.
	 * @return array<string, array<int, string>>|\WP_Error Result.
	 */
	public static function format_rules( array $rules, array $specific, string $label ) {
		$options  = self::get_rule_options();
		$rules    = array_values( array_unique( array_filter( array_map( 'strval', $rules ) ) ) );
		$specific = array_values( array_unique( array_filter( array_map( 'strval', $specific ) ) ) );

		foreach ( $rules as $rule ) {
			if ( ! isset( $options[ $rule ] ) ) {
				return new \WP_Error(
					'pixeccte_mcp_invalid_rule',
					sprintf( '"%s" is not a valid %s rule. Call list_theme_templates to see the valid rules.', $rule, $label )
				);
			}
		}

		foreach ( $specific as $target ) {
			if ( ! preg_match( '/^(post-\d+|tax-\d+(-single)?)$/', $target ) ) {
				return new \WP_Error(
					'pixeccte_mcp_invalid_target',
					sprintf( '"%s" is not a valid specific target. Use post-<id>, tax-<term id> (term archive) or tax-<term id>-single (posts in that term).', $target )
				);
			}
		}

		// Same shape as Target_Rules_Fields::get_format_rule_value(): "specifics" is a rule only when targets exist.
		$rules = array_values( array_diff( $rules, array( 'specifics' ) ) );

		if ( ! empty( $specific ) ) {
			$rules[] = 'specifics';
		}

		if ( empty( $rules ) ) {
			return array();
		}

		return array(
			'rule'     => $rules,
			'specific' => $specific,
		);
	}

	/**
	 * Human-readable list of a stored condition value.
	 *
	 * @param mixed $value Stored meta value.
	 * @return array<int, string>
	 */
	public static function describe_rules( $value ): array {
		if ( ! is_array( $value ) || empty( $value['rule'] ) ) {
			return array();
		}

		$labels = array();

		foreach ( (array) $value['rule'] as $rule ) {
			if ( 'specifics' === $rule ) {
				foreach ( (array) ( $value['specific'] ?? array() ) as $target ) {
					$labels[] = wp_strip_all_tags( (string) Target_Rules_Fields::get_location_by_key( (string) $target ) );
				}

				continue;
			}

			$labels[] = wp_strip_all_tags( (string) Target_Rules_Fields::get_location_by_key( (string) $rule ) );
		}

		return array_values( array_filter( $labels ) );
	}

	/**
	 * Summary of one template.
	 *
	 * @param \WP_Post $post Template post.
	 * @return array<string, mixed>
	 */
	public static function summarize( \WP_Post $post ): array {
		$include  = get_post_meta( $post->ID, self::META_INCLUDE, true );
		$exclude  = get_post_meta( $post->ID, self::META_EXCLUDE, true );
		$document = Elementor::$instance->documents->get( $post->ID );

		return array(
			'id'           => $post->ID,
			'title'        => get_the_title( $post ),
			'type'         => (string) pixeccte_get_theme_template_type( $post->ID ),
			'status'       => $post->post_status,
			'include'      => is_array( $include ) ? $include : array(),
			'exclude'      => is_array( $exclude ) ? $exclude : array(),
			'display_on'   => self::describe_rules( $include ),
			'hidden_on'    => self::describe_rules( $exclude ),
			'edit_url'     => $document ? $document->get_edit_url() : get_edit_post_link( $post->ID, 'raw' ),
			'has_elements' => $document ? ! empty( Document_Store::get_elements( $document ) ) : false,
		);
	}

	/**
	 * Other published templates of the same type that also display on the whole site.
	 *
	 * Only one header (or footer, ...) is rendered per page, so these compete with each other.
	 *
	 * @param string $type       Template type.
	 * @param int    $exclude_id Template to leave out.
	 * @return array<int, array<string, mixed>>
	 */
	public static function find_global_conflicts( string $type, int $exclude_id ): array {
		$conflicts = array();

		$posts = get_posts(
			array(
				'post_type'      => self::POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'post__not_in'   => array( $exclude_id ),
				'meta_key'       => pixeccte_get_theme_template_type_meta_key(), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => $type, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		foreach ( $posts as $post ) {
			$include = get_post_meta( $post->ID, self::META_INCLUDE, true );

			if ( is_array( $include ) && in_array( 'basic-global', (array) ( $include['rule'] ?? array() ), true ) ) {
				$conflicts[] = array(
					'id'    => $post->ID,
					'title' => get_the_title( $post ),
				);
			}
		}

		return $conflicts;
	}
}
