<?php
/**
 * Plugin Name:       Pixels Core Creative Tools for Elementor
 * Plugin URI:        https://addons.pixels71.com
 * Description:       Free Elementor addon with essential widgets, Live Copy Paste, and a Header/Footer theme builder from Pixels71.
 * Version:           1.0.3
 * Author:            Pixels71
 * Author URI:        https://profiles.wordpress.org/pixels71
 * Text Domain:       pixels-core-creative-tools-for-elementor
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Requires Plugins:  elementor
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'PIXECCTE_VERSION', '1.0.3' );
define( 'PIXECCTE_FILE', __FILE__ );
define( 'PIXECCTE_PATH', plugin_dir_path( __FILE__ ) );
define( 'PIXECCTE_URL', plugin_dir_url( __FILE__ ) );
define( 'PIXECCTE_URL_ASSETS', PIXECCTE_URL . 'assets/' );
define( 'PIXECCTE_ADMIN_PATH', PIXECCTE_PATH . 'admin/' );
define( 'PIXECCTE_ADMIN_URL', PIXECCTE_URL . 'admin/' );
define( 'PIXECCTE_UPGRADE_URL', 'https://pixels71.com/pixels-core-pro/' );
// Set to true when Pixels Core Pro is available for purchase/activation.
define( 'PIXECCTE_SHOW_PRO_UPSSELL', false );

require_once PIXECCTE_PATH . 'includes/class-plugin.php';

register_activation_hook(
	__FILE__,
	static function (): void {
		require_once PIXECCTE_PATH . 'includes/class-widget-registry.php';
		require_once PIXECCTE_PATH . 'includes/class-extension-registry.php';
		require_once PIXECCTE_ADMIN_PATH . 'class-widget-settings.php';
		require_once PIXECCTE_ADMIN_PATH . 'class-extension-settings.php';

		\PixelsCoreCreativeToolsForElementor\Admin\Widget_Settings::activate_defaults();
		\PixelsCoreCreativeToolsForElementor\Admin\Extension_Settings::activate_defaults();
		flush_rewrite_rules();

		set_transient( 'pixeccte_activation_redirect', true, 30 );
	}
);

PixelsCoreCreativeToolsForElementor\Plugin::instance();
