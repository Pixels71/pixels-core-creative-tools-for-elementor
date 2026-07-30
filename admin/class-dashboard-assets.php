<?php
namespace PixelsElementorAddons\Admin;

use PixelsElementorAddons\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Dashboard_Assets {

	private const MANIFEST_ENTRY = 'src/main.tsx';

	/**
	 * Shared dashboard payload for inline bootstrap + REST consumers.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_config(): array {
		$config = [
			'restUrl'              => untrailingslashit( esc_url_raw( rest_url( 'pixels-core/v1' ) ) ),
			'nonce'                => wp_create_nonce( 'wp_rest' ),
			'adminUrl'             => esc_url_raw( admin_url( 'admin.php?page=pixels-core' ) ),
			'pluginUrl'            => esc_url_raw( PIXELS_CORE_URL ),
			'elementorActive'      => (bool) did_action( 'elementor/loaded' ),
			'nestedElementsActive' => (bool) Plugin::is_nested_elements_active(),
			'notices'              => self::get_notices(),
			'version'              => PIXELS_CORE_VERSION,
			'proActive'            => defined( 'PIXELS_CORE_PRO_VERSION' ),
			'showProUpsell'        => defined( 'PIXELS_CORE_SHOW_PRO_UPSSELL' ) && PIXELS_CORE_SHOW_PRO_UPSSELL,
			'upgradeUrl'           => defined( 'PIXELS_CORE_UPGRADE_URL' ) ? PIXELS_CORE_UPGRADE_URL : 'https://pixels71.com/pixels-core-pro/',
			'license'              => [
				'active'    => false,
				'maskedKey' => '',
				'managedBy' => 'pro',
			],
			'links'                => [
				'tutorials'     => 'https://pixels71.com',
				'help'          => 'https://pixels71.com',
				'community'     => 'https://pixels71.com',
				'knowledgeBase' => 'https://pixels71.com',
				'review'        => 'https://wordpress.org/support/plugin/pixels-elementor-addons/reviews/',
				'pro'           => defined( 'PIXELS_CORE_UPGRADE_URL' ) ? PIXELS_CORE_UPGRADE_URL : 'https://pixels71.com/pixels-core-pro/',
			],
			'widgets'              => self::get_widgets_payload(),
			'extensions'           => self::get_extensions_payload(),
			'formSettings'         => self::get_form_settings_payload(),
			'i18n'                 => Dashboard_I18n::get_strings(),
		];

		/**
		 * Filter dashboard bootstrap config (Pro adds license state).
		 *
		 * @param array<string, mixed> $config
		 */
		return apply_filters( 'pixels_core_dashboard_config', $config );
	}

	public static function enqueue( string $hook ): void {
		if ( 'toplevel_page_pixels-core' !== $hook ) {
			return;
		}

		$manifest_path = PIXELS_CORE_PATH . 'assets/dashboard/.vite/manifest.json';

		if ( ! file_exists( $manifest_path ) ) {
			add_action(
				'admin_notices',
				static function (): void {
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin page check for notice display.
					if ( ! isset( $_GET['page'] ) || 'pixels-core' !== sanitize_key( wp_unslash( $_GET['page'] ) ) ) {
						return;
					}

					printf(
						'<div class="notice notice-error"><p>%s</p></div>',
						esc_html__(
							'Pixels Addons for Elementor dashboard assets are missing. Run npm run build in the dashboard project.',
							'pixels-elementor-addons'
						)
					);
				}
			);

			return;
		}

		$manifest = json_decode( (string) file_get_contents( $manifest_path ), true );

		if ( ! is_array( $manifest ) || empty( $manifest[ self::MANIFEST_ENTRY ] ) ) {
			return;
		}

		$entry    = $manifest[ self::MANIFEST_ENTRY ];
		$base_url = PIXELS_CORE_URL . 'assets/dashboard/';
		$js_path  = PIXELS_CORE_PATH . 'assets/dashboard/' . $entry['file'];
		$version  = file_exists( $js_path ) ? (string) filemtime( $js_path ) : PIXELS_CORE_VERSION;

		if ( ! empty( $entry['css'] ) && is_array( $entry['css'] ) ) {
			foreach ( $entry['css'] as $index => $css_file ) {
				$css_path = PIXELS_CORE_PATH . 'assets/dashboard/' . $css_file;

				wp_enqueue_style(
					'pixels-core-dashboard' . ( 0 === $index ? '' : '-' . $index ),
					$base_url . $css_file,
					[],
					file_exists( $css_path ) ? (string) filemtime( $css_path ) : $version
				);
			}
		}

		wp_enqueue_script(
			'pixels-core-dashboard',
			$base_url . $entry['file'],
			[],
			$version,
			true
		);

		// Vite emits an ES module (uses import.meta); classic scripts cannot load it.
		add_filter(
			'script_loader_tag',
			static function ( string $tag, string $handle ): string {
				if ( 'pixels-core-dashboard' !== $handle ) {
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
			'pixels-core-dashboard',
			'window.pixelsCoreDashboard = ' . wp_json_encode( self::get_config() ) . ';',
			'before'
		);

		add_action(
			'admin_head',
			static function (): void {
				echo '<style>
					#wpcontent { padding-left: 0; }
					#wpbody-content { padding-bottom: 0; }
					#pixels-dashboard-root .wrap { margin: 0; }
					.pixels-core-dashboard-page .notice { margin: 16px 20px 0; }
				</style>';
			}
		);
	}

	/**
	 * @return array<int, array{type: string, message: string}>
	 */
	private static function get_notices(): array {
		$notices = [];

		if ( ! did_action( 'elementor/loaded' ) ) {
			$notices[] = [
				'type'    => 'warning',
				'message' => __( 'Pixels Addons for Elementor requires Elementor to be installed and activated.', 'pixels-elementor-addons' ),
			];
		} elseif ( ! Plugin::is_nested_elements_active() ) {
			$notices[] = [
				'type'    => 'warning',
				'message' => __( 'Some widgets require the Nested Elements experiment in Elementor → Settings → Features.', 'pixels-elementor-addons' ),
			];
		}

		return $notices;
	}

	/**
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_widgets_payload(): array {
		return array_map(
			[ Rest_Api::class, 'format_widget' ],
			Widget_Settings::instance()->get_admin_widgets()
		);
	}

	/**
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_extensions_payload(): array {
		return array_map(
			[ Rest_Api::class, 'format_extension' ],
			Extension_Settings::instance()->get_admin_extensions()
		);
	}

	/**
	 * @return array<string, string>|null
	 */
	public static function get_form_settings_payload(): ?array {
		if ( ! class_exists( '\\PixelsElementorAddons\\Form_Settings' ) ) {
			return null;
		}

		$settings = \PixelsElementorAddons\Form_Settings::get_all();

		return [
			'recaptchaV2SiteKey'   => $settings['recaptcha_v2_site_key'],
			'recaptchaV2SecretKey' => $settings['recaptcha_v2_secret_key'],
			'recaptchaV3SiteKey'   => $settings['recaptcha_v3_site_key'],
			'recaptchaV3SecretKey' => $settings['recaptcha_v3_secret_key'],
			'mailchimpApiKey'      => $settings['mailchimp_api_key'],
			'mailchimpListId'      => $settings['mailchimp_list_id'],
		];
	}
}
