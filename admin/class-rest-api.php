<?php
/**
 * Rest api.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rest api.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */
final class Rest_Api {

	/**
	 * Instance.
	 *
	 * @var mixed
	 */
	private static ?Rest_Api $instance = null;

	/**
	 * Instance.
	 *
	 * @return Rest_Api Result.
	 */
	public static function instance(): Rest_Api {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Construct.
	 */
	private function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Can manage.
	 *
	 * @return bool Result.
	 */
	public function can_manage(): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Register routes.
	 */
	public function register_routes(): void {
		$active_slugs_arg = array(
			'required'          => false,
			'default'           => array(),
			'type'              => 'array',
			'items'             => array(
				'type' => 'string',
			),
			'sanitize_callback' => array( $this, 'sanitize_active_slugs' ),
			'validate_callback' => static function ( $value ): bool {
				return is_array( $value );
			},
		);

		$form_settings_arg = array(
			'required'          => false,
			'default'           => array(),
			'type'              => 'object',
			'sanitize_callback' => array( $this, 'sanitize_form_settings_params' ),
			'validate_callback' => static function ( $value ): bool {
				return is_array( $value );
			},
		);

		register_rest_route(
			'pixeccte/v1',
			'/widgets',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_widgets' ),
					'permission_callback' => array( $this, 'can_manage' ),
				),
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'save_widgets' ),
					'permission_callback' => array( $this, 'can_manage' ),
					'args'                => array(
						'active'       => $active_slugs_arg,
						'formSettings' => $form_settings_arg,
					),
				),
			)
		);

		register_rest_route(
			'pixeccte/v1',
			'/extensions',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_extensions' ),
					'permission_callback' => array( $this, 'can_manage' ),
				),
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'save_extensions' ),
					'permission_callback' => array( $this, 'can_manage' ),
					'args'                => array(
						'active' => $active_slugs_arg,
					),
				),
			)
		);

		register_rest_route(
			'pixeccte/v1',
			'/form-settings',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_form_settings' ),
					'permission_callback' => array( $this, 'can_manage' ),
				),
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'save_form_settings' ),
					'permission_callback' => array( $this, 'can_manage' ),
					'args'                => array(
						'recaptchaV2SiteKey'   => array(
							'required'          => false,
							'type'              => 'string',
							'default'           => '',
							'sanitize_callback' => 'sanitize_text_field',
						),
						'recaptchaV2SecretKey' => array(
							'required'          => false,
							'type'              => 'string',
							'default'           => '',
							'sanitize_callback' => 'sanitize_text_field',
						),
						'recaptchaV3SiteKey'   => array(
							'required'          => false,
							'type'              => 'string',
							'default'           => '',
							'sanitize_callback' => 'sanitize_text_field',
						),
						'recaptchaV3SecretKey' => array(
							'required'          => false,
							'type'              => 'string',
							'default'           => '',
							'sanitize_callback' => 'sanitize_text_field',
						),
						'mailchimpApiKey'      => array(
							'required'          => false,
							'type'              => 'string',
							'default'           => '',
							'sanitize_callback' => 'sanitize_text_field',
						),
						'mailchimpListId'      => array(
							'required'          => false,
							'type'              => 'string',
							'default'           => '',
							'sanitize_callback' => 'sanitize_text_field',
						),
					),
				),
			)
		);
	}

	/**
	 * Sanitize active slugs.
	 *
	 * @param mixed            $value   Raw value.
	 * @param \WP_REST_Request $request Request.
	 * @param string           $param   Param name.
	 * @return array<int, string>
	 */
	public function sanitize_active_slugs( $value, $request = null, $param = '' ): array { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- WP REST sanitize callback signature.
		unset( $request, $param );
		if ( ! is_array( $value ) ) {
			return array();
		}

		return array_values( array_filter( array_map( 'sanitize_key', $value ) ) );
	}

	/**
	 * Sanitize form settings params.
	 *
	 * @param mixed            $value   Raw value.
	 * @param \WP_REST_Request $request Request.
	 * @param string           $param   Param name.
	 * @return array<string, string>
	 */
	public function sanitize_form_settings_params( $value, $request = null, $param = '' ): array { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- WP REST sanitize callback signature.
		unset( $request, $param );
		if ( ! is_array( $value ) ) {
			return array();
		}

		$keys = array(
			'recaptchaV2SiteKey',
			'recaptchaV2SecretKey',
			'recaptchaV3SiteKey',
			'recaptchaV3SecretKey',
			'mailchimpApiKey',
			'mailchimpListId',
		);

		$sanitized = array();

		foreach ( $keys as $key ) {
			if ( array_key_exists( $key, $value ) ) {
				$sanitized[ $key ] = sanitize_text_field( (string) $value[ $key ] );
			}
		}

		return $sanitized;
	}

	/**
	 * Get widgets.
	 *
	 * @return \WP_REST_Response
	 */
	public function get_widgets() {
		$widgets = array_map(
			array( __CLASS__, 'format_widget' ),
			Widget_Settings::instance()->get_admin_widgets()
		);

		return rest_ensure_response( $widgets );
	}

	/**
	 * Save widgets.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function save_widgets( \WP_REST_Request $request ) {
		$active = $request->get_param( 'active' );
		if ( ! is_array( $active ) ) {
			$active = array();
		}

		Widget_Settings::instance()->save_active_slugs( $active );

		$form_settings = $request->get_param( 'formSettings' );
		if ( is_array( $form_settings ) && ! empty( $form_settings ) ) {
			$this->persist_form_settings( $form_settings );
		}

		return rest_ensure_response(
			array(
				'success' => true,
				'message' => __( 'Widget settings saved.', 'pixels-core-creative-tools-for-elementor' ),
			)
		);
	}

	/**
	 * Get extensions.
	 *
	 * @return \WP_REST_Response
	 */
	public function get_extensions() {
		$extensions = array_map(
			array( __CLASS__, 'format_extension' ),
			Extension_Settings::instance()->get_admin_extensions()
		);

		return rest_ensure_response( $extensions );
	}

	/**
	 * Save extensions.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function save_extensions( \WP_REST_Request $request ) {
		$active = $request->get_param( 'active' );
		if ( ! is_array( $active ) ) {
			$active = array();
		}

		Extension_Settings::instance()->save_active_slugs( $active );

		return rest_ensure_response(
			array(
				'success' => true,
				'message' => __( 'Extension settings saved.', 'pixels-core-creative-tools-for-elementor' ),
			)
		);
	}

	/**
	 * Get form settings.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_form_settings() {
		if ( ! class_exists( '\\PixelsCoreCreativeToolsForElementor\\Form_Settings' ) ) {
			return new \WP_Error(
				'pixeccte_form_pro_required',
				__( 'Form settings require Pixels Core Pro.', 'pixels-core-creative-tools-for-elementor' ),
				array( 'status' => 404 )
			);
		}

		return rest_ensure_response( self::format_form_settings( \PixelsCoreCreativeToolsForElementor\Form_Settings::get_all() ) );
	}

	/**
	 * Save form settings.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function save_form_settings( \WP_REST_Request $request ) {
		if ( ! class_exists( '\\PixelsCoreCreativeToolsForElementor\\Form_Settings' ) ) {
			return new \WP_Error(
				'pixeccte_form_pro_required',
				__( 'Form settings require Pixels Core Pro.', 'pixels-core-creative-tools-for-elementor' ),
				array( 'status' => 404 )
			);
		}

		$params = array(
			'recaptchaV2SiteKey'   => (string) $request->get_param( 'recaptchaV2SiteKey' ),
			'recaptchaV2SecretKey' => (string) $request->get_param( 'recaptchaV2SecretKey' ),
			'recaptchaV3SiteKey'   => (string) $request->get_param( 'recaptchaV3SiteKey' ),
			'recaptchaV3SecretKey' => (string) $request->get_param( 'recaptchaV3SecretKey' ),
			'mailchimpApiKey'      => (string) $request->get_param( 'mailchimpApiKey' ),
			'mailchimpListId'      => (string) $request->get_param( 'mailchimpListId' ),
		);

		$this->persist_form_settings( $params );

		return rest_ensure_response(
			array(
				'success' => true,
				'message' => __( 'Form settings saved.', 'pixels-core-creative-tools-for-elementor' ),
			)
		);
	}

	/**
	 * Persist form settings.
	 *
	 * @param array $settings Settings.
	 */
	private function persist_form_settings( array $settings ): void {
		if ( ! class_exists( '\\PixelsCoreCreativeToolsForElementor\\Form_Settings' ) ) {
			return;
		}

		\PixelsCoreCreativeToolsForElementor\Form_Settings::save(
			array(
				'recaptcha_v2_site_key'   => sanitize_text_field( $settings['recaptchaV2SiteKey'] ?? '' ),
				'recaptcha_v2_secret_key' => sanitize_text_field( $settings['recaptchaV2SecretKey'] ?? '' ),
				'recaptcha_v3_site_key'   => sanitize_text_field( $settings['recaptchaV3SiteKey'] ?? '' ),
				'recaptcha_v3_secret_key' => sanitize_text_field( $settings['recaptchaV3SecretKey'] ?? '' ),
				'mailchimp_api_key'       => sanitize_text_field( $settings['mailchimpApiKey'] ?? '' ),
				'mailchimp_list_id'       => sanitize_text_field( $settings['mailchimpListId'] ?? '' ),
			)
		);
	}

	/**
	 * Format widget.
	 *
	 * @param array $widget Widget.
	 * @return array<string, mixed>
	 */
	public static function format_widget( array $widget ): array {
		return array(
			'id'          => $widget['slug'],
			'name'        => $widget['title'],
			'description' => $widget['description'],
			'category'    => self::widget_category( $widget['slug'] ),
			'enabled'     => (bool) $widget['is_active'],
			'available'   => (bool) $widget['is_available'],
			'tier'        => $widget['tier'] ?? 'free',
			'isPro'       => ! empty( $widget['is_pro'] ),
			'upgradeUrl'  => esc_url_raw( $widget['upgrade_url'] ?? ( defined( 'PIXECCTE_UPGRADE_URL' ) ? PIXECCTE_UPGRADE_URL : '' ) ),
		);
	}

	/**
	 * Format extension.
	 *
	 * @param array $extension Extension.
	 * @return array<string, mixed>
	 */
	public static function format_extension( array $extension ): array {
		return array(
			'id'          => $extension['slug'],
			'name'        => $extension['title'],
			'description' => $extension['description'],
			'category'    => self::extension_category( $extension['slug'] ),
			'enabled'     => (bool) $extension['is_active'],
			'available'   => (bool) ( $extension['is_available'] ?? true ),
			'tier'        => $extension['tier'] ?? 'free',
			'isPro'       => ! empty( $extension['is_pro'] ),
			'upgradeUrl'  => esc_url_raw( $extension['upgrade_url'] ?? ( defined( 'PIXECCTE_UPGRADE_URL' ) ? PIXECCTE_UPGRADE_URL : '' ) ),
		);
	}

	/**
	 * Format form settings.
	 *
	 * @param array $settings Settings.
	 * @return array<string, string>
	 */
	private static function format_form_settings( array $settings ): array {
		return array(
			'recaptchaV2SiteKey'   => $settings['recaptcha_v2_site_key'],
			'recaptchaV2SecretKey' => $settings['recaptcha_v2_secret_key'],
			'recaptchaV3SiteKey'   => $settings['recaptcha_v3_site_key'],
			'recaptchaV3SecretKey' => $settings['recaptcha_v3_secret_key'],
			'mailchimpApiKey'      => $settings['mailchimp_api_key'],
			'mailchimpListId'      => $settings['mailchimp_list_id'],
		);
	}

	/**
	 * Widget category.
	 *
	 * @param string $slug Slug.
	 * @return string Result.
	 */
	public static function widget_category( string $slug ): string {
		$map = array(
			'tabs'              => 'content',
			'accordion'         => 'content',
			'carousel'          => 'content',
			'heading'           => 'content',
			'timeline'          => 'content',
			'menu'              => 'content',
			'site_logo'         => 'content',
			'physics_tag_cloud' => 'creative',
			'rotator_text'      => 'creative',
			'orbit_circle'      => 'creative',
			'stack_card'        => 'creative',
			'expanding_card'    => 'creative',
			'marquee'           => 'creative',
			'countdown_timer'   => 'marketing',
			'counter'           => 'marketing',
			'progress_bar'      => 'marketing',
			'video'             => 'marketing',
			'form'              => 'form',
			'post_title'        => 'content',
			'feature_image'     => 'content',
			'post_content'      => 'content',
			'post_meta'         => 'content',
			'post_author'       => 'content',
			'post_navigation'   => 'content',
			'social_share'      => 'social',
			'post_comment'      => 'content',
			'author_box'        => 'content',
			'table_of_content'  => 'content',
		);

		return $map[ $slug ] ?? 'content';
	}

	/**
	 * Extension category.
	 *
	 * @param string $slug Slug.
	 * @return string Result.
	 */
	public static function extension_category( string $slug ): string {
		$map = array(
			'starter_animations'      => 'performance',
			'sticky_element'          => 'performance',
			'animation_effects'       => 'utility',
			'text_animation_effects'  => 'utility',
			'image_animation_effects' => 'utility',
			'cursor_hover_effect'     => 'utility',
			'live_copy_paste'         => 'utility',
		);

		return $map[ $slug ] ?? 'utility';
	}
}
