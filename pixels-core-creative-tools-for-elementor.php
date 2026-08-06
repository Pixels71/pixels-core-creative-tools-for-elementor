<?php

/**
 * Plugin Name:       Pixels Core Creative Tools for Elementor
 * Plugin URI:        https://addons.pixels71.com
 * Description:       Free Elementor addon with essential widgets, Live Copy Paste, and a Header/Footer theme builder from Pixels71.
 * Version:           1.0.1
 * Author:            Pixels71
 * Author URI:        https://profiles.wordpress.org/pixels71
 * Text Domain:       pixels-core-creative-tools-for-elementor
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Requires Plugins:  elementor
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if (! defined('ABSPATH')) {
	exit;
}

define('PIXELS_CORE_VERSION', '1.0.1');
define('PIXELS_CORE_FILE', __FILE__);
define('PIXELS_CORE_PATH', plugin_dir_path(__FILE__));
define('PIXELS_CORE_URL', plugin_dir_url(__FILE__));
define('PIXELS_CORE_URL_ASSETS', PIXELS_CORE_URL . 'assets/');
define('PIXELS_CORE_ADMIN_PATH', PIXELS_CORE_PATH . 'admin/');
define('PIXELS_CORE_ADMIN_URL', PIXELS_CORE_URL . 'admin/');
define('PIXELS_CORE_UPGRADE_URL', 'https://pixels71.com/pixels-core-pro/');
// Set to true when Pixels Core Pro is available for purchase/activation.
define('PIXELS_CORE_SHOW_PRO_UPSSELL', false);

require_once PIXELS_CORE_PATH . 'includes/class-pixels-core.php';

register_activation_hook(
	__FILE__,
	static function (): void {
		require_once PIXELS_CORE_PATH . 'includes/class-widget-registry.php';
		require_once PIXELS_CORE_PATH . 'includes/class-extension-registry.php';
		require_once PIXELS_CORE_ADMIN_PATH . 'class-widget-settings.php';
		require_once PIXELS_CORE_ADMIN_PATH . 'class-extension-settings.php';

		\PixelsCore\Admin\Widget_Settings::activate_defaults();
		\PixelsCore\Admin\Extension_Settings::activate_defaults();
		flush_rewrite_rules();

		set_transient('pixels_core_activation_redirect', true, 30);
	}
);

PixelsCore\Plugin::instance();
