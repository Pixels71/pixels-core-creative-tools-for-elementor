<?php
/**
 * Dashboard REST API for the AI / MCP tab.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Admin;

use PixelsCoreCreativeToolsForElementor\Mcp\Connections;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Routes under pixeccte/v1/mcp-connections: status, health check, connect wizard and revoke.
 */
final class Mcp_Api {

	const EXTENSION_SLUG = 'mcp_server';

	/**
	 * Instance.
	 *
	 * @var mixed
	 */
	private static ?Mcp_Api $instance = null;

	/**
	 * Instance.
	 *
	 * @return Mcp_Api Result.
	 */
	public static function instance(): Mcp_Api {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Construct.
	 */
	private function __construct() {
		require_once PIXECCTE_PATH . 'includes/mcp/class-connections.php';

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
		register_rest_route(
			'pixeccte/v1',
			'/mcp-connections',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_status' ),
					'permission_callback' => array( $this, 'can_manage' ),
				),
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_connection' ),
					'permission_callback' => array( $this, 'can_manage' ),
					'args'                => array(
						'client' => array(
							'required' => true,
							'type'     => 'string',
							'enum'     => array_keys( Connections::CLIENTS ),
						),
					),
				),
			)
		);

		register_rest_route(
			'pixeccte/v1',
			'/mcp-connections/(?P<uuid>[a-f0-9-]{36})',
			array(
				'methods'             => \WP_REST_Server::DELETABLE,
				'callback'            => array( $this, 'revoke_connection' ),
				'permission_callback' => array( $this, 'can_manage' ),
			)
		);

		register_rest_route(
			'pixeccte/v1',
			'/mcp-connections/health',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_health' ),
				'permission_callback' => array( $this, 'can_manage' ),
			)
		);

		register_rest_route(
			'pixeccte/v1',
			'/mcp-connections/server',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'set_server_enabled' ),
				'permission_callback' => array( $this, 'can_manage' ),
				'args'                => array(
					'enabled' => array(
						'required' => true,
						'type'     => 'boolean',
					),
				),
			)
		);

		// Used by the health check: reports only whether an Authorization header arrived, never its value.
		register_rest_route(
			'pixeccte/v1',
			'/mcp-connections/echo',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'echo_authorization' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Server state and connections.
	 *
	 * @return \WP_REST_Response Result.
	 */
	public function get_status(): \WP_REST_Response {
		$user = wp_get_current_user();

		return rest_ensure_response(
			array(
				'enabled'               => $this->is_enabled(),
				'endpoint'              => Connections::get_endpoint_url(),
				'clients'               => Connections::CLIENTS,
				'activeWindow'          => Connections::ACTIVE_WINDOW,
				'appPasswordsAvailable' => wp_is_application_passwords_available_for_user( $user ),
				'connections'           => Connections::get_all(),
				'now'                   => time(),
			)
		);
	}

	/**
	 * Run the health check.
	 *
	 * @return \WP_REST_Response Result.
	 */
	public function get_health(): \WP_REST_Response {
		return rest_ensure_response( array( 'checks' => Connections::health( $this->is_enabled() ) ) );
	}

	/**
	 * Create a connection for the current user.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error Result.
	 */
	public function create_connection( \WP_REST_Request $request ) {
		if ( ! $this->is_enabled() ) {
			return new \WP_Error( 'pixeccte_mcp_disabled', __( 'Switch on the MCP Server first.', 'pixels-core-creative-tools-for-elementor' ), array( 'status' => 400 ) );
		}

		$result = Connections::create( get_current_user_id(), (string) $request->get_param( 'client' ) );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$response = rest_ensure_response( $result );
		$response->header( 'Cache-Control', 'no-store' );

		return $response;
	}

	/**
	 * Revoke a connection.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response|\WP_Error Result.
	 */
	public function revoke_connection( \WP_REST_Request $request ) {
		$revoked = Connections::revoke( (string) $request->get_param( 'uuid' ) );

		if ( is_wp_error( $revoked ) ) {
			return $revoked;
		}

		return rest_ensure_response( array( 'connections' => Connections::get_all() ) );
	}

	/**
	 * Switch the MCP Server extension on or off.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response Result.
	 */
	public function set_server_enabled( \WP_REST_Request $request ): \WP_REST_Response {
		$settings = Extension_Settings::instance();
		$active   = array_diff( $settings->get_active_slugs(), array( self::EXTENSION_SLUG ) );

		if ( $request->get_param( 'enabled' ) ) {
			$active[] = self::EXTENSION_SLUG;
		}

		$settings->save_active_slugs( array_values( $active ) );

		return rest_ensure_response( array( 'enabled' => $this->is_enabled() ) );
	}

	/**
	 * Whether an Authorization header reached WordPress.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response Result.
	 */
	public function echo_authorization( \WP_REST_Request $request ): \WP_REST_Response {
		$received = '' !== (string) $request->get_header( 'authorization' )
			|| ! empty( $_SERVER['PHP_AUTH_USER'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- Only checked for presence.

		$response = rest_ensure_response( array( 'received' => $received ) );
		$response->header( 'Cache-Control', 'no-store' );

		return $response;
	}

	/**
	 * Whether the MCP Server extension is on.
	 *
	 * @return bool Result.
	 */
	private function is_enabled(): bool {
		return Extension_Settings::instance()->is_active( self::EXTENSION_SLUG );
	}
}
