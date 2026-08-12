<?php
/**
 * Dashboard assets.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Admin;

use PixelsCoreCreativeToolsForElementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dashboard assets.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */
final class Dashboard_Assets {

	private const MANIFEST_ENTRY = 'src/main.tsx';

	/**
	 * Shared dashboard payload for inline bootstrap + REST consumers.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_config(): array {
		$config = array(
			'restUrl'              => untrailingslashit( esc_url_raw( rest_url( 'pixeccte/v1' ) ) ),
			'nonce'                => wp_create_nonce( 'wp_rest' ),
			'adminUrl'             => esc_url_raw( admin_url( 'admin.php?page=pixeccte' ) ),
			'pluginUrl'            => esc_url_raw( PIXECCTE_URL ),
			'elementorActive'      => (bool) did_action( 'elementor/loaded' ),
			'nestedElementsActive' => (bool) Plugin::is_nested_elements_active(),
			'notices'              => self::get_notices(),
			'version'              => PIXECCTE_VERSION,
			'proActive'            => defined( 'PIXECCTE_PRO_VERSION' ),
			'showProUpsell'        => defined( 'PIXECCTE_SHOW_PRO_UPSSELL' ) && PIXECCTE_SHOW_PRO_UPSSELL,
			'upgradeUrl'           => esc_url_raw( defined( 'PIXECCTE_UPGRADE_URL' ) ? PIXECCTE_UPGRADE_URL : 'https://pixels71.com/pixels-core-pro/' ),
			'license'              => array(
				'active'    => false,
				'maskedKey' => '',
				'managedBy' => 'pro',
			),
			'links'                => array(
				'tutorials'     => esc_url_raw( 'https://pixels71.com' ),
				'help'          => esc_url_raw( 'https://pixels71.com' ),
				'community'     => esc_url_raw( 'https://pixels71.com' ),
				'knowledgeBase' => esc_url_raw( 'https://pixels71.com' ),
				'review'        => esc_url_raw( 'https://wordpress.org/support/plugin/pixels-core-creative-tools-for-elementor/reviews/' ),
				'pro'           => esc_url_raw( defined( 'PIXECCTE_UPGRADE_URL' ) ? PIXECCTE_UPGRADE_URL : 'https://pixels71.com/pixels-core-pro/' ),
			),
			'widgets'              => self::get_widgets_payload(),
			'extensions'           => self::get_extensions_payload(),
			'formSettings'         => self::get_form_settings_payload(),
			'i18n'                 => Dashboard_I18n::get_strings(),
		);

		/**
		 * Filter dashboard bootstrap config (Pro adds license state).
		 *
		 * @param array<string, mixed> $config
		 */
		return apply_filters( 'pixeccte_dashboard_config', $config );
	}

	/**
	 * Enqueue.
	 *
	 * @param string $hook Hook.
	 */
	public static function enqueue( string $hook ): void {
		if ( 'toplevel_page_pixeccte' !== $hook ) {
			return;
		}

		$manifest_path = PIXECCTE_PATH . 'assets/dashboard/.vite/manifest.json';

		if ( ! file_exists( $manifest_path ) ) {
			add_action(
				'admin_notices',
				static function (): void {
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin page check for notice display.
					if ( ! isset( $_GET['page'] ) || 'pixeccte' !== sanitize_key( wp_unslash( $_GET['page'] ) ) ) {
						return;
					}

					printf(
						'<div class="notice notice-error"><p>%s</p></div>',
						esc_html__(
							'Pixels Core Creative Tools for Elementor dashboard assets are missing. Run npm run build in the dashboard project.',
							'pixels-core-creative-tools-for-elementor'
						)
					);
				}
			);

			return;
		}

		// Local Vite manifest path (not a remote URL).
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Reading a local plugin file.
		$manifest = json_decode( (string) file_get_contents( $manifest_path ), true );

		if ( ! is_array( $manifest ) || empty( $manifest[ self::MANIFEST_ENTRY ] ) ) {
			return;
		}

		$entry    = $manifest[ self::MANIFEST_ENTRY ];
		$base_url = PIXECCTE_URL . 'assets/dashboard/';
		$js_path  = PIXECCTE_PATH . 'assets/dashboard/' . $entry['file'];
		$version  = file_exists( $js_path ) ? (string) filemtime( $js_path ) : PIXECCTE_VERSION;

		if ( ! empty( $entry['css'] ) && is_array( $entry['css'] ) ) {
			foreach ( $entry['css'] as $index => $css_file ) {
				$css_path = PIXECCTE_PATH . 'assets/dashboard/' . $css_file;

				wp_enqueue_style(
					'pixeccte-dashboard' . ( 0 === $index ? '' : '-' . $index ),
					$base_url . $css_file,
					array(),
					file_exists( $css_path ) ? (string) filemtime( $css_path ) : $version
				);
			}
		}

		wp_enqueue_script(
			'pixeccte-dashboard',
			$base_url . $entry['file'],
			array(),
			$version,
			true
		);

		// Vite emits an ES module (uses import.meta); classic scripts cannot load it.
		add_filter(
			'script_loader_tag',
			static function ( string $tag, string $handle ): string {
				if ( 'pixeccte-dashboard' !== $handle ) {
					return $tag;
				}

				if ( false !== strpos( $tag, 'type=' ) ) {
					return $tag;
				}

				return str_replace( '<script ', '<script type="module" ', $tag );
			},
			10,
			2
		);

		wp_add_inline_script(
			'pixeccte-dashboard',
			'window.pixeccteDashboard = ' . wp_json_encode( self::get_config() ) . ';',
			'before'
		);

		wp_register_style( 'pixeccte-dashboard-layout', false, array(), PIXECCTE_VERSION );
		wp_enqueue_style( 'pixeccte-dashboard-layout' );
		wp_add_inline_style(
			'pixeccte-dashboard-layout',
			'#wpcontent{padding-left:0;}#wpbody-content{padding-bottom:0;}#pixeccte-dashboard-root .wrap{margin:0;}.pixeccte-dashboard-page .notice{margin:16px 20px 0;}'
		);
	}

	/**
	 * Get notices.
	 *
	 * @return array<int, array{type: string, message: string}>
	 */
	private static function get_notices(): array {
		$notices = array();

		if ( ! did_action( 'elementor/loaded' ) ) {
			$notices[] = array(
				'type'    => 'warning',
				'message' => __( 'Pixels Core Creative Tools for Elementor requires Elementor to be installed and activated.', 'pixels-core-creative-tools-for-elementor' ),
			);
		} elseif ( ! Plugin::is_nested_elements_active() ) {
			$notices[] = array(
				'type'    => 'warning',
				'message' => __( 'Some widgets require the Nested Elements experiment in Elementor → Settings → Features.', 'pixels-core-creative-tools-for-elementor' ),
			);
		}

		return $notices;
	}

	/**
	 * Class.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_widgets_payload(): array {
		return array_map(
			array( Rest_Api::class, 'format_widget' ),
			Widget_Settings::instance()->get_admin_widgets()
		);
	}

	/**
	 * Class.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_extensions_payload(): array {
		return array_map(
			array( Rest_Api::class, 'format_extension' ),
			Extension_Settings::instance()->get_admin_extensions()
		);
	}

	/**
	 * Get form settings payload.
	 *
	 * @return array<string, string>|null
	 */
	public static function get_form_settings_payload(): ?array {
		if ( ! class_exists( '\\PixelsCoreCreativeToolsForElementor\\Form_Settings' ) ) {
			return null;
		}

		$settings = \PixelsCoreCreativeToolsForElementor\Form_Settings::get_all();

		return array(
			'recaptchaV2SiteKey'   => $settings['recaptcha_v2_site_key'],
			'recaptchaV2SecretKey' => $settings['recaptcha_v2_secret_key'],
			'recaptchaV3SiteKey'   => $settings['recaptcha_v3_site_key'],
			'recaptchaV3SecretKey' => $settings['recaptcha_v3_secret_key'],
			'mailchimpApiKey'      => $settings['mailchimp_api_key'],
			'mailchimpListId'      => $settings['mailchimp_list_id'],
		);
	}
}
