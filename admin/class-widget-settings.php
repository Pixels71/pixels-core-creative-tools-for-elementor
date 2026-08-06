<?php
namespace PixelsCore\Admin;

use PixelsCore\Plugin;
use PixelsCore\Widget_Registry;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Widget_Settings {

	public const OPTION_KEY       = 'pixels_core_active_widgets';
	public const SYNCED_SLUGS_KEY = 'pixels_core_widgets_synced_slugs';

	private static ?Widget_Settings $instance = null;

	public static function instance(): Widget_Settings {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
	}

	/**
	 * Auto-enable widgets that are newly added to the plugin codebase.
	 *
	 * Does not re-enable widgets the user deliberately turned off.
	 */
	public static function sync_new_widgets(): void {
		$saved = get_option( self::OPTION_KEY, null );

		if ( ! is_array( $saved ) ) {
			return;
		}

		$registry     = Widget_Registry::instance();
		$all_slugs    = $registry->get_slugs();
		$synced_slugs = get_option( self::SYNCED_SLUGS_KEY, null );

		if ( ! is_array( $synced_slugs ) ) {
			update_option( self::SYNCED_SLUGS_KEY, $all_slugs );
			return;
		}

		$brand_new = array_diff( $all_slugs, $synced_slugs );

		if ( ! empty( $brand_new ) ) {
			update_option(
				self::OPTION_KEY,
				array_values( array_unique( array_merge( $saved, $brand_new ) ) )
			);
		}

		if ( $synced_slugs !== $all_slugs ) {
			update_option( self::SYNCED_SLUGS_KEY, $all_slugs );
		}
	}

	/**
	 * @return array<int, string> Enabled widget slugs.
	 */
	public function get_active_slugs(): array {
		$registry = Widget_Registry::instance();
		$all      = $registry->get_slugs();
		$saved    = get_option( self::OPTION_KEY, null );

		if ( null === $saved || ! is_array( $saved ) ) {
			return $all;
		}

		$saved = array_map( 'sanitize_key', $saved );

		return array_values( array_intersect( $saved, $all ) );
	}

	public function is_active( string $slug ): bool {
		return in_array( $slug, $this->get_active_slugs(), true );
	}

	/**
	 * @param array<int, string> $slugs
	 */
	public function save_active_slugs( array $slugs ): void {
		$registry  = Widget_Registry::instance();
		$all_slugs = $registry->get_slugs();
		$sanitized = array_map( 'sanitize_key', $slugs );
		$valid     = array_values( array_intersect( $sanitized, $all_slugs ) );

		update_option( self::OPTION_KEY, $valid );
		update_option( self::SYNCED_SLUGS_KEY, $all_slugs );
	}

	public static function activate_defaults(): void {
		$slugs = Widget_Registry::instance()->get_slugs();

		update_option( self::OPTION_KEY, $slugs );
		update_option( self::SYNCED_SLUGS_KEY, $slugs );
	}

	/**
	 * Widget list for admin UI.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function get_admin_widgets(): array {
		$registry     = Widget_Registry::instance();
		$active_slugs = $this->get_active_slugs();
		$widgets      = [];
		$upgrade_url  = defined( 'PIXELS_CORE_UPGRADE_URL' ) ? PIXELS_CORE_UPGRADE_URL : 'https://pixels71.com/pixels-core-pro/';

		foreach ( $registry->get_all() as $slug => $config ) {
			$requires_nested = ! empty( $config['requires_nested'] );
			$is_available    = ! $requires_nested || Plugin::is_nested_elements_active();
			$tier            = $config['tier'] ?? 'free';

			$widgets[] = [
				'slug'            => $slug,
				'name'            => $config['name'] ?? 'pixels-' . $slug,
				'title'           => $config['title'],
				'description'     => $config['description'],
				'icon'            => $config['icon'] ?? 'eicon-plug',
				'requires_nested' => $requires_nested,
				'is_active'       => in_array( $slug, $active_slugs, true ),
				'is_available'    => $is_available,
				'tier'            => $tier,
				'is_pro'          => 'pro' === $tier,
				'upgrade_url'     => $upgrade_url,
			];
		}

		usort(
			$widgets,
			static function ( array $a, array $b ): int {
				return strcasecmp( (string) $a['title'], (string) $b['title'] );
			}
		);

		return $widgets;
	}
}
