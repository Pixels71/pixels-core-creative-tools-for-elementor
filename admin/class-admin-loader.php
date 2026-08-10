<?php
namespace PixelsCoreCreativeToolsForElementor\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Admin_Loader {

	public static function init(): void {
		require_once PIXECCTE_PATH . 'includes/class-widget-registry.php';
		require_once PIXECCTE_PATH . 'includes/class-extension-registry.php';

		self::require_files();

		self::load_settings();
		Widget_Settings::instance();
		Extension_Settings::instance();
		Rest_Api::instance();

		if ( ! is_admin() ) {
			return;
		}

		Admin::instance();
	}

	public static function load_settings(): void {
		require_once PIXECCTE_ADMIN_PATH . 'class-widget-settings.php';
		require_once PIXECCTE_ADMIN_PATH . 'class-extension-settings.php';
	}

	/**
	 * @return array<int, string>
	 */
	public static function get_active_widget_slugs(): array {
		self::load_settings();

		return Widget_Settings::instance()->get_active_slugs();
	}

	private static function require_files(): void {
		self::load_settings();
		require_once PIXECCTE_ADMIN_PATH . 'class-dashboard-i18n.php';
		require_once PIXECCTE_ADMIN_PATH . 'class-dashboard-assets.php';
		require_once PIXECCTE_ADMIN_PATH . 'class-rest-api.php';
		require_once PIXECCTE_ADMIN_PATH . 'class-admin.php';
	}
}
