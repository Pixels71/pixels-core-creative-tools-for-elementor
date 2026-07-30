<?php
namespace PixelsElementorAddons\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Admin {

	private static ?Admin $instance = null;

	public static function instance(): Admin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		add_action( 'admin_menu', [ $this, 'register_menu' ] );
		add_action( 'admin_init', [ $this, 'maybe_redirect_after_activation' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_filter( 'plugin_action_links_' . plugin_basename( PIXELS_CORE_FILE ), [ $this, 'add_settings_link' ] );
		add_filter( 'admin_body_class', [ $this, 'add_body_class' ] );
	}

	public function maybe_redirect_after_activation(): void {
		if ( ! get_transient( 'pixels_core_activation_redirect' ) ) {
			return;
		}

		delete_transient( 'pixels_core_activation_redirect' );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Activation redirect guard; read-only GET flag from WP core.
		if ( wp_doing_ajax() || is_network_admin() || isset( $_GET['activate-multi'] ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		wp_safe_redirect( admin_url( 'admin.php?page=pixels-core' ) );
		exit;
	}

	/**
	 * @param array<string, string> $links
	 * @return array<string, string>
	 */
	public function add_settings_link( array $links ): array {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'admin.php?page=pixels-core' ) ),
			esc_html__( 'Settings', 'pixels-elementor-addons' )
		);

		array_unshift( $links, $settings_link );

		return $links;
	}

	public function register_menu(): void {
		add_menu_page(
			esc_html__( 'Pixels Addons for Elementor', 'pixels-elementor-addons' ),
			esc_html__( 'Pixels Addons', 'pixels-elementor-addons' ),
			'manage_options',
			'pixels-core',
			[ $this, 'render_page' ],
			PIXELS_CORE_URL . 'assets/images/pixels-logo.svg',
			58
		);
	}

	public function enqueue_assets( string $hook ): void {
		wp_enqueue_style(
			'pixels-core-admin-menu',
			PIXELS_CORE_ADMIN_URL . 'assets/css/admin-menu.css',
			[],
			PIXELS_CORE_VERSION
		);

		if ( 'toplevel_page_pixels-core' === $hook ) {
			Dashboard_Assets::enqueue( $hook );
			return;
		}

		if ( 'pixels-core_page_pixels-core-submissions' === $hook ) {
			wp_enqueue_style(
				'pixels-core-admin',
				PIXELS_CORE_ADMIN_URL . 'assets/css/admin.css',
				[],
				PIXELS_CORE_VERSION
			);
		}
	}

	/**
	 * @param string $classes
	 * @return string
	 */
	public function add_body_class( string $classes ): string {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin page check for body class.
		if ( ! isset( $_GET['page'] ) || 'pixels-core' !== sanitize_key( wp_unslash( $_GET['page'] ) ) ) {
			return $classes;
		}

		return $classes . ' pixels-core-dashboard-page';
	}

	public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$config = Dashboard_Assets::get_config();

		printf(
			'<script type="application/json" id="pixels-dashboard-bootstrap">%s</script>',
			wp_json_encode( $config )
		);

		echo '<div id="pixels-dashboard-root"></div>';
	}
}
