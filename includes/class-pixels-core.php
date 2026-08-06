<?php
namespace PixelsCore;

use PixelsCore\Admin\Admin_Loader;
use PixelsCore\Admin\Extension_Settings;
use PixelsCore\Admin\Widget_Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Plugin {

	private static ?Plugin $instance = null;

	public static function instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		add_action( 'plugins_loaded', [ $this, 'on_plugins_loaded' ] );
		add_action( 'init', [ $this, 'load_admin' ] );
	}

	public function load_admin(): void {
		require_once PIXELS_CORE_ADMIN_PATH . 'class-admin-loader.php';

		Admin_Loader::init();
	}

	public function on_plugins_loaded(): void {
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_elementor' ] );
			return;
		}

		add_action( 'elementor/init', [ $this, 'init' ] );
	}

	public function init(): void {
		if ( ! version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return;
		}

		if ( ! self::is_nested_elements_active() ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_nested_elements' ] );
		}

		require_once PIXELS_CORE_PATH . 'includes/class-widget-registry.php';
		require_once PIXELS_CORE_PATH . 'includes/class-extension-registry.php';
		require_once PIXELS_CORE_PATH . 'includes/class-widgets-loader.php';
		require_once PIXELS_CORE_PATH . 'includes/theme-builder/class-pixels-theme-elementor.php';
		require_once PIXELS_CORE_PATH . 'includes/class-extensions-loader.php';
		require_once PIXELS_CORE_ADMIN_PATH . 'class-admin-loader.php';

		Admin_Loader::load_settings();
		Widget_Settings::sync_new_widgets();
		Extension_Settings::sync_new_extensions();
		Widgets_Loader::instance();
		Theme_Builder\Theme_Elementor::instance();
		Extensions_Loader::instance();

		/**
		 * Fires after Pixels Core Creative Tools for Elementor has initialized Elementor integrations.
		 * Used by Pixels Core Pro to boot form handlers and related features.
		 */
		do_action( 'pixels_core_loaded' );
	}

	/**
	 * Theme builder template types available without Pro.
	 *
	 * @return array<string, string> type => label
	 */
	public static function get_theme_template_types(): array {
		$types = [
			'type_header' => __( 'Header', 'pixels-core-creative-tools-for-elementor' ),
			'type_footer' => __( 'Footer', 'pixels-core-creative-tools-for-elementor' ),
		];

		/**
		 * Filter theme builder template types. Pro adds Single, Archive, 404, Popup, Mega Menu.
		 *
		 * @param array<string, string> $types
		 */
		return apply_filters( 'pixels_core_theme_template_types', $types );
	}

	public static function is_nested_elements_active(): bool {
		if ( ! did_action( 'elementor/loaded' ) ) {
			return false;
		}

		$experiments = \Elementor\Plugin::$instance->experiments ?? null;

		if ( ! $experiments ) {
			return false;
		}

		return $experiments->is_feature_active( 'nested-elements', true );
	}

	public function admin_notice_missing_elementor(): void {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$message = sprintf(
			/* translators: 1: Plugin name, 2: Elementor */
			esc_html__( '%1$s requires %2$s to be installed and activated.', 'pixels-core-creative-tools-for-elementor' ),
			'<strong>' . esc_html__( 'Pixels Core Creative Tools for Elementor', 'pixels-core-creative-tools-for-elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'pixels-core-creative-tools-for-elementor' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', wp_kses_post( $message ) );
	}

	public function admin_notice_minimum_elementor_version(): void {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$message = sprintf(
			/* translators: 1: Plugin name, 2: Elementor, 3: Required version */
			esc_html__( '%1$s requires %2$s version %3$s or greater.', 'pixels-core-creative-tools-for-elementor' ),
			'<strong>' . esc_html__( 'Pixels Core Creative Tools for Elementor', 'pixels-core-creative-tools-for-elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'pixels-core-creative-tools-for-elementor' ) . '</strong>',
			'3.5.0'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', wp_kses_post( $message ) );
	}

	public function admin_notice_missing_nested_elements(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$message = sprintf(
			/* translators: 1: Plugin name, 2: Elementor feature name */
			esc_html__( '%1$s nested widgets require the %2$s experiment to be enabled in Elementor → Settings → Features.', 'pixels-core-creative-tools-for-elementor' ),
			'<strong>' . esc_html__( 'Pixels Core Creative Tools for Elementor', 'pixels-core-creative-tools-for-elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'Nested Elements', 'pixels-core-creative-tools-for-elementor' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', wp_kses_post( $message ) );
	}
}
