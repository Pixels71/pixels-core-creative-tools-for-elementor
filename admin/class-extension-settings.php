<?php
namespace PixelsElementorAddons\Admin;

use PixelsElementorAddons\Extension_Registry;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Extension_Settings {

	public const OPTION_KEY       = 'pixels_core_active_extensions';
	public const SYNCED_SLUGS_KEY = 'pixels_core_extensions_synced_slugs';

	private static ?Extension_Settings $instance = null;

	public static function instance(): Extension_Settings {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
	}

	/**
	 * Auto-enable extensions that are newly added to the plugin codebase.
	 */
	public static function sync_new_extensions(): void {
		$saved = get_option( self::OPTION_KEY, null );

		if ( ! is_array( $saved ) ) {
			return;
		}

		$registry     = Extension_Registry::instance();
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
	 * @return array<int, string> Enabled extension slugs.
	 */
	public function get_active_slugs(): array {
		$registry = Extension_Registry::instance();
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
		$registry  = Extension_Registry::instance();
		$all_slugs = $registry->get_slugs();
		$sanitized = array_map( 'sanitize_key', $slugs );
		$valid     = array_values( array_intersect( $sanitized, $all_slugs ) );

		update_option( self::OPTION_KEY, $valid );
		update_option( self::SYNCED_SLUGS_KEY, $all_slugs );
	}

	public static function activate_defaults(): void {
		$slugs = Extension_Registry::instance()->get_slugs();

		update_option( self::OPTION_KEY, $slugs );
		update_option( self::SYNCED_SLUGS_KEY, $slugs );
	}

	/**
	 * @return array<int, array<string, mixed>>
	 */
	public function get_admin_extensions(): array {
		$registry     = Extension_Registry::instance();
		$active_slugs = $this->get_active_slugs();
		$extensions   = [];
		$upgrade_url  = defined( 'PIXELS_CORE_UPGRADE_URL' ) ? PIXELS_CORE_UPGRADE_URL : 'https://pixels71.com/pixels-core-pro/';

		foreach ( $registry->get_all() as $slug => $config ) {
			$tier = $config['tier'] ?? 'free';

			$extensions[] = [
				'slug'        => $slug,
				'title'       => $config['title'],
				'description' => $config['description'],
				'is_active'   => in_array( $slug, $active_slugs, true ),
				'is_available'=> true,
				'tier'        => $tier,
				'is_pro'      => 'pro' === $tier,
				'upgrade_url' => $upgrade_url,
			];
		}

		foreach ( $registry->get_teasers() as $slug => $config ) {
			$extensions[] = [
				'slug'         => $slug,
				'title'        => $config['title'],
				'description'  => $config['description'],
				'is_active'    => false,
				'is_available' => false,
				'tier'         => 'pro',
				'is_pro'       => true,
				'upgrade_url'  => $upgrade_url,
			];
		}

		usort(
			$extensions,
			static function ( array $a, array $b ): int {
				return strcasecmp( (string) $a['title'], (string) $b['title'] );
			}
		);

		return $extensions;
	}
}
