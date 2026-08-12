<?php
/**
 * Admin.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */
final class Admin {

	/**
	 * Instance.
	 *
	 * @var mixed
	 */
	private static ?Admin $instance = null;

	/**
	 * Instance.
	 *
	 * @return Admin Result.
	 */
	public static function instance(): Admin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Construct.
	 */
	private function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'maybe_redirect_after_activation' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( PIXECCTE_FILE ), array( $this, 'add_settings_link' ) );
		add_filter( 'admin_body_class', array( $this, 'add_body_class' ) );
	}

	/**
	 * Maybe redirect after activation.
	 */
	public function maybe_redirect_after_activation(): void {
		if ( ! get_transient( 'pixeccte_activation_redirect' ) ) {
			return;
		}

		delete_transient( 'pixeccte_activation_redirect' );

		// Core bulk-activation flag: presence-only check (value unused).
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Activation redirect guard; read-only GET flag from WP core.
		$is_bulk_activate = isset( $_GET['activate-multi'] );

		if ( wp_doing_ajax() || is_network_admin() || $is_bulk_activate ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		wp_safe_redirect( admin_url( 'admin.php?page=pixeccte' ) );
		exit;
	}

	/**
	 * Add settings link.
	 *
	 * @param array $links Links.
	 * @return array<string, string>
	 */
	public function add_settings_link( array $links ): array {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'admin.php?page=pixeccte' ) ),
			esc_html__( 'Settings', 'pixels-core-creative-tools-for-elementor' )
		);

		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Register menu.
	 */
	public function register_menu(): void {
		add_menu_page(
			esc_html__( 'Pixels Core Creative Tools for Elementor', 'pixels-core-creative-tools-for-elementor' ),
			esc_html__( 'Pixels Core', 'pixels-core-creative-tools-for-elementor' ),
			'manage_options',
			'pixeccte',
			array( $this, 'render_page' ),
			PIXECCTE_URL . 'assets/images/pixeccte-logo.svg',
			58
		);
	}

	/**
	 * Enqueue assets.
	 *
	 * @param string $hook Hook.
	 */
	public function enqueue_assets( string $hook ): void {
		$menu_css = PIXECCTE_ADMIN_PATH . 'assets/css/admin-menu.css';

		wp_enqueue_style(
			'pixeccte-admin-menu',
			PIXECCTE_ADMIN_URL . 'assets/css/admin-menu.css',
			array(),
			file_exists( $menu_css ) ? (string) filemtime( $menu_css ) : PIXECCTE_VERSION
		);

		if ( 'toplevel_page_pixeccte' === $hook ) {
			Dashboard_Assets::enqueue( $hook );
			return;
		}

		if ( 'pixeccte_page_pixeccte-submissions' === $hook ) {
			wp_enqueue_style(
				'pixeccte-admin',
				PIXECCTE_ADMIN_URL . 'assets/css/admin.css',
				array(),
				PIXECCTE_VERSION
			);
		}
	}

	/**
	 * Class.
	 *
	 * @param string $classes Classes.
	 * @return string
	 */
	public function add_body_class( string $classes ): string {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin page check for body class.
		if ( ! isset( $_GET['page'] ) || 'pixeccte' !== sanitize_key( wp_unslash( $_GET['page'] ) ) ) {
			return $classes;
		}

		return $classes . ' pixeccte-dashboard-page';
	}

	/**
	 * Render page.
	 */
	public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		echo '<div id="pixeccte-dashboard-root"></div>';
	}
}
