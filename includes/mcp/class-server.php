<?php
/**
 * MCP server (Streamable HTTP transport, stateless).
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp;

use PixelsCoreCreativeToolsForElementor\Mcp\Tools\Tool;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Exposes Pixels Core and Elementor page building to MCP clients at `POST /wp-json/pixeccte/v1/mcp`.
 *
 * Authentication is handled by WordPress REST (Application Passwords over Basic auth).
 */
final class Server {

	public const REST_NAMESPACE = 'pixeccte/v1';
	public const REST_ROUTE     = '/mcp';

	/**
	 * Newest first; the first entry is offered when the client asks for an unknown version.
	 */
	public const PROTOCOL_VERSIONS = array( '2025-11-25', '2025-06-18', '2025-03-26', '2024-11-05' );

	private const PARSE_ERROR      = -32700;
	private const INVALID_REQUEST  = -32600;
	private const METHOD_NOT_FOUND = -32601;
	private const INVALID_PARAMS   = -32602;

	/**
	 * Instance.
	 *
	 * @var mixed
	 */
	private static ?Server $instance = null;

	/**
	 * Instance.
	 *
	 * @return Server Result.
	 */
	public static function instance(): Server {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Construct.
	 */
	private function __construct() {
		self::require_files();

		Preview_Links::init();

		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Require files.
	 */
	private static function require_files(): void {
		$dir = PIXECCTE_PATH . 'includes/mcp/';

		require_once $dir . 'class-widget-catalog.php';
		require_once $dir . 'class-element-builder.php';
		require_once $dir . 'class-document-store.php';
		require_once $dir . 'class-theme-templates.php';
		require_once $dir . 'class-design-system.php';
		require_once $dir . 'class-connections.php';
		require_once $dir . 'class-effect-catalog.php';
		require_once $dir . 'class-preview-links.php';
		require_once $dir . 'class-tool-registry.php';
		require_once $dir . 'tools/abstract-class-tool.php';
		require_once $dir . 'tools/class-get-site-info.php';
		require_once $dir . 'tools/class-list-widgets.php';
		require_once $dir . 'tools/class-get-widget-schema.php';
		require_once $dir . 'tools/class-list-pages.php';
		require_once $dir . 'tools/class-get-page-structure.php';
		require_once $dir . 'tools/class-create-page.php';
		require_once $dir . 'tools/class-add-elements.php';
		require_once $dir . 'tools/class-update-element.php';
		require_once $dir . 'tools/class-delete-element.php';
		require_once $dir . 'tools/class-list-theme-templates.php';
		require_once $dir . 'tools/class-create-theme-template.php';
		require_once $dir . 'tools/class-list-menus.php';
		require_once $dir . 'tools/class-save-menu.php';
		require_once $dir . 'tools/class-update-page.php';
		require_once $dir . 'tools/class-trash-page.php';
		require_once $dir . 'tools/class-search-media.php';
		require_once $dir . 'tools/class-import-media-from-url.php';
		require_once $dir . 'tools/class-get-preview-url.php';
		require_once $dir . 'tools/class-update-design-system.php';
		require_once $dir . 'tools/class-list-effects.php';
	}

	/**
	 * Endpoint URL.
	 *
	 * @return string Result.
	 */
	public static function get_endpoint_url(): string {
		return rest_url( self::REST_NAMESPACE . self::REST_ROUTE );
	}

	/**
	 * Register routes.
	 */
	public function register_routes(): void {
		register_rest_route(
			self::REST_NAMESPACE,
			self::REST_ROUTE,
			array(
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'handle_post' ),
					'permission_callback' => array( $this, 'can_connect' ),
				),
				array(
					// No server-initiated stream and no sessions to delete.
					'methods'             => 'GET, DELETE',
					'callback'            => array( $this, 'handle_not_allowed' ),
					'permission_callback' => '__return_true',
				),
			)
		);
	}

	/**
	 * Permission check for the MCP endpoint.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return bool|\WP_Error Result.
	 */
	public function can_connect( \WP_REST_Request $request ) {
		if ( ! $this->is_allowed_origin( (string) $request->get_header( 'origin' ) ) ) {
			return new \WP_Error(
				'pixeccte_mcp_forbidden_origin',
				__( 'This origin is not allowed to use the MCP server.', 'pixels-core-creative-tools-for-elementor' ),
				array( 'status' => 403 )
			);
		}

		if ( ! is_user_logged_in() ) {
			return new \WP_Error(
				'pixeccte_mcp_unauthorized',
				__( 'Authenticate with a WordPress Application Password (Basic auth) to use the MCP server.', 'pixels-core-creative-tools-for-elementor' ),
				array( 'status' => 401 )
			);
		}

		/**
		 * Filter whether the current user may connect to the MCP server.
		 *
		 * @param bool     $allowed Default: user can edit posts.
		 * @param \WP_User $user    Current user.
		 */
		$allowed = (bool) apply_filters( 'pixeccte_mcp_user_can_connect', current_user_can( 'edit_posts' ), wp_get_current_user() );

		if ( ! $allowed ) {
			return new \WP_Error(
				'pixeccte_mcp_forbidden',
				__( 'Your account is not allowed to use the MCP server.', 'pixels-core-creative-tools-for-elementor' ),
				array( 'status' => 403 )
			);
		}

		return true;
	}

	/**
	 * Browsers send Origin; reject cross-site browser requests (DNS rebinding protection).
	 * Desktop and server-side MCP clients send no Origin.
	 *
	 * @param string $origin Origin header.
	 * @return bool Result.
	 */
	private function is_allowed_origin( string $origin ): bool {
		if ( '' === $origin ) {
			return true;
		}

		$origin_host = wp_parse_url( $origin, PHP_URL_HOST );
		$allowed     = array(
			wp_parse_url( home_url(), PHP_URL_HOST ),
			wp_parse_url( site_url(), PHP_URL_HOST ),
		);

		/**
		 * Filter the hosts allowed to call the MCP server from a browser.
		 *
		 * @param array<int, string> $allowed
		 */
		$allowed = (array) apply_filters( 'pixeccte_mcp_allowed_origin_hosts', $allowed );

		return in_array( $origin_host, $allowed, true );
	}

	/**
	 * Handle GET/DELETE.
	 *
	 * @return \WP_REST_Response Result.
	 */
	public function handle_not_allowed(): \WP_REST_Response {
		$response = new \WP_REST_Response( null, 405 );
		$response->header( 'Allow', 'POST' );

		return $response;
	}

	/**
	 * Handle a JSON-RPC POST.
	 *
	 * @param \WP_REST_Request $request Request.
	 * @return \WP_REST_Response Result.
	 */
	public function handle_post( \WP_REST_Request $request ): \WP_REST_Response {
		$payload = json_decode( (string) $request->get_body(), true );

		if ( ! is_array( $payload ) ) {
			return new \WP_REST_Response( self::error( null, self::PARSE_ERROR, 'Parse error: body must be a JSON-RPC message.' ), 400 );
		}

		if ( ! empty( $payload ) && array_keys( $payload ) === range( 0, count( $payload ) - 1 ) ) {
			$responses = array();

			foreach ( $payload as $message ) {
				$response = is_array( $message )
					? $this->process_message( $message )
					: self::error( null, self::INVALID_REQUEST, 'Invalid Request.' );

				if ( null !== $response ) {
					$responses[] = $response;
				}
			}

			return empty( $responses ) ? new \WP_REST_Response( null, 202 ) : new \WP_REST_Response( $responses, 200 );
		}

		$response = $this->process_message( $payload );

		if ( null === $response ) {
			return new \WP_REST_Response( null, 202 );
		}

		return new \WP_REST_Response( $response, 200 );
	}

	/**
	 * Process one JSON-RPC message.
	 *
	 * @param array<string, mixed> $message Message.
	 * @return array<string, mixed>|null Response, or null for notifications and client responses.
	 */
	private function process_message( array $message ): ?array {
		$has_id = array_key_exists( 'id', $message );
		$id     = $has_id ? $message['id'] : null;

		if ( ! isset( $message['method'] ) ) {
			// A response to a server request; this server never sends any.
			return ( isset( $message['result'] ) || isset( $message['error'] ) ) ? null : self::error( $id, self::INVALID_REQUEST, 'Invalid Request.' );
		}

		if ( '2.0' !== ( $message['jsonrpc'] ?? null ) || ! is_string( $message['method'] ) ) {
			return $has_id ? self::error( $id, self::INVALID_REQUEST, 'Invalid Request.' ) : null;
		}

		$params = $message['params'] ?? array();

		if ( ! is_array( $params ) ) {
			return $has_id ? self::error( $id, self::INVALID_PARAMS, 'Invalid params.' ) : null;
		}

		Connections::record( $message['method'], $params );

		if ( ! $has_id ) {
			// Notifications (notifications/initialized, notifications/cancelled, ...) need no reply.
			return null;
		}

		switch ( $message['method'] ) {
			case 'initialize':
				return self::result( $id, $this->initialize( $params ) );

			case 'ping':
				return self::result( $id, new \stdClass() );

			case 'tools/list':
				return self::result( $id, array( 'tools' => $this->list_tools() ) );

			case 'tools/call':
				return $this->call_tool( $id, $params );

			default:
				return self::error( $id, self::METHOD_NOT_FOUND, sprintf( 'Method not found: %s', $message['method'] ) );
		}
	}

	/**
	 * Initialize.
	 *
	 * @param array<string, mixed> $params Params.
	 * @return array<string, mixed> Result.
	 */
	private function initialize( array $params ): array {
		$requested = isset( $params['protocolVersion'] ) && is_string( $params['protocolVersion'] ) ? $params['protocolVersion'] : '';
		$version   = in_array( $requested, self::PROTOCOL_VERSIONS, true ) ? $requested : self::PROTOCOL_VERSIONS[0];

		return array(
			'protocolVersion' => $version,
			'capabilities'    => array(
				'tools' => array( 'listChanged' => false ),
			),
			'serverInfo'      => array(
				'name'    => 'pixels-core',
				'title'   => 'Pixels Core for Elementor',
				'version' => PIXECCTE_VERSION,
			),
			'instructions'    => self::get_instructions(),
		);
	}

	/**
	 * Guidance sent to the AI client on connect.
	 *
	 * @return string Result.
	 */
	public static function get_instructions(): string {
		$instructions = implode(
			"\n",
			array(
				'This server builds pages on a WordPress site with Elementor and the Pixels Core widget library.',
				'',
				'Workflow:',
				'1. Call get_site_info once to learn the global colors, global fonts, breakpoints and plugin features.',
				'2. Call list_widgets to see what is available. Prefer Pixels widgets (source "pixels") when one fits; use Elementor core widgets for everything else.',
				'3. Before using a widget, call get_widget_schema for it (scope "content" first, "style" only when you need styling). Never guess setting names; unknown settings are rejected.',
				'4. Build with create_page (new page, always draft unless the user asked to publish) or add_elements (existing page). Read existing pages with get_page_structure, change them with update_element / delete_element, and pass the returned content_hash as expected_hash.',
				'5. For motion and interaction (scroll animations, entrance effects, hover effects on cards, sticky columns, custom cursors, image masks), call list_effects and apply its recipes instead of guessing the Advanced-tab settings.',
				'',
				'Design rules:',
				'- Every section is a top-level container (content_width "boxed") holding inner containers; use flex_direction "row"/"column" and flex_gap for layout instead of spacer widgets.',
				'- Use global colors and fonts through "__globals__" (for example {"__globals__": {"title_color": "globals/colors?id=primary"}}) rather than hard-coded values, so the page follows the site design.',
				'- Use a consistent spacing scale (multiples of 8px) and give each section generous vertical padding.',
				'- Set tablet and mobile values for layout, font size and spacing with the responsive suffixes from get_site_info (for example padding_mobile).',
				'- Nested widgets (tabs, accordion, carousel and similar) take one child container per repeater item, in the same order.',
				'- Write real, specific copy for the user\'s business; avoid lorem ipsum.',
			)
		);

		/**
		 * Filter the instructions sent to MCP clients on initialize.
		 *
		 * @param string $instructions
		 */
		return (string) apply_filters( 'pixeccte_mcp_instructions', $instructions );
	}

	/**
	 * Tool definitions for the current user.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private function list_tools(): array {
		return array_values(
			array_map(
				static function ( Tool $tool ): array {
					return $tool->to_definition();
				},
				Tool_Registry::instance()->get_available()
			)
		);
	}

	/**
	 * Call a tool.
	 *
	 * @param mixed                $id     Request id.
	 * @param array<string, mixed> $params Params.
	 * @return array<string, mixed> Response.
	 */
	private function call_tool( $id, array $params ): array {
		$name = isset( $params['name'] ) && is_string( $params['name'] ) ? $params['name'] : '';
		$tool = Tool_Registry::instance()->get( $name );

		if ( null === $tool ) {
			return self::error( $id, self::INVALID_PARAMS, sprintf( 'Unknown tool: %s', $name ) );
		}

		$args = $params['arguments'] ?? array();

		if ( ! is_array( $args ) ) {
			return self::error( $id, self::INVALID_PARAMS, 'Tool arguments must be an object.' );
		}

		if ( ! $tool->current_user_can_use() ) {
			return self::result( $id, self::tool_error( 'You do not have permission to use this tool.' ) );
		}

		$schema = $tool->input_schema();
		$valid  = rest_validate_value_from_schema( $args, $schema, 'arguments' );

		if ( is_wp_error( $valid ) ) {
			return self::result( $id, self::tool_error( self::format_wp_error( $valid ) ) );
		}

		$args = rest_sanitize_value_from_schema( $args, $schema, 'arguments' );

		if ( is_wp_error( $args ) ) {
			return self::result( $id, self::tool_error( self::format_wp_error( $args ) ) );
		}

		foreach ( $schema['properties'] ?? array() as $key => $property ) {
			if ( ! array_key_exists( $key, $args ) && array_key_exists( 'default', $property ) ) {
				$args[ $key ] = $property['default'];
			}
		}

		try {
			$output = $tool->execute( $args );
		} catch ( \Throwable $e ) {
			return self::result( $id, self::tool_error( sprintf( 'The tool failed: %s', $e->getMessage() ) ) );
		}

		if ( is_wp_error( $output ) ) {
			return self::result( $id, self::tool_error( self::format_wp_error( $output ) ) );
		}

		return self::result(
			$id,
			array(
				'content' => array(
					array(
						'type' => 'text',
						'text' => (string) wp_json_encode( $output, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ),
					),
				),
				'isError' => false,
			)
		);
	}

	/**
	 * Flatten a WP_Error into one message.
	 *
	 * @param \WP_Error $error Error.
	 * @return string Result.
	 */
	private static function format_wp_error( \WP_Error $error ): string {
		return implode( "\n", array_unique( $error->get_error_messages() ) );
	}

	/**
	 * Tool-level error result (shown to the model so it can correct itself).
	 *
	 * @param string $message Message.
	 * @return array<string, mixed> Result.
	 */
	private static function tool_error( string $message ): array {
		return array(
			'content' => array(
				array(
					'type' => 'text',
					'text' => $message,
				),
			),
			'isError' => true,
		);
	}

	/**
	 * JSON-RPC result.
	 *
	 * @param mixed $id     Id.
	 * @param mixed $result Result.
	 * @return array<string, mixed> Response.
	 */
	private static function result( $id, $result ): array {
		return array(
			'jsonrpc' => '2.0',
			'id'      => $id,
			'result'  => $result,
		);
	}

	/**
	 * JSON-RPC error.
	 *
	 * @param mixed  $id      Id.
	 * @param int    $code    Code.
	 * @param string $message Message.
	 * @return array<string, mixed> Response.
	 */
	private static function error( $id, int $code, string $message ): array {
		return array(
			'jsonrpc' => '2.0',
			'id'      => $id,
			'error'   => array(
				'code'    => $code,
				'message' => $message,
			),
		);
	}
}
