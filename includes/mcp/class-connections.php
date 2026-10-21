<?php
/**
 * MCP connections: Application Passwords used by AI clients, their activity, setup and health.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MCP is stateless HTTP, so a "connection" is an Application Password an AI client sends with each request.
 * This class records which password each request used, which client sent it and when, so the dashboard
 * can show what is connected; it also creates passwords with ready-to-paste client setup, and checks
 * whether the site can accept MCP connections at all.
 *
 * Loaded on every request (the dashboard needs it while the MCP extension is off).
 */
final class Connections {

	/**
	 * Application Password app_id for passwords created from the dashboard.
	 */
	const APP_ID = '6f1c5d2e-8b7a-4c3e-9f10-5c0d3a1e7b42';

	/**
	 * Option holding usage per Application Password uuid.
	 */
	const USAGE_OPTION = 'pixeccte_mcp_connections';

	/**
	 * A connection used within this many seconds counts as active.
	 */
	const ACTIVE_WINDOW = 10 * MINUTE_IN_SECONDS;

	/**
	 * Clients the setup wizard knows.
	 */
	const CLIENTS = array(
		'claude-code'    => 'Claude Code',
		'claude-desktop' => 'Claude Desktop',
		'cursor'         => 'Cursor',
		'other'          => 'Other MCP client',
	);

	/**
	 * Record an MCP request made with an Application Password.
	 *
	 * @param string               $method JSON-RPC method.
	 * @param array<string, mixed> $params Params (clientInfo for initialize, name for tools/call).
	 */
	public static function record( string $method, array $params = array() ): void {
		$uuid = function_exists( 'rest_get_authenticated_app_password' ) ? (string) rest_get_authenticated_app_password() : '';

		// Cookie-authenticated requests (e.g. from wp-admin) are not AI clients.
		if ( '' === $uuid ) {
			return;
		}

		$usage = self::get_usage();
		$entry = $usage[ $uuid ] ?? array(
			'user_id'    => get_current_user_id(),
			'first_seen' => time(),
			'requests'   => 0,
			'tool_calls' => 0,
		);

		$entry['last_seen'] = time();
		++$entry['requests'];

		if ( 'initialize' === $method && isset( $params['clientInfo'] ) && is_array( $params['clientInfo'] ) ) {
			$entry['client']         = sanitize_text_field( (string) ( $params['clientInfo']['name'] ?? '' ) );
			$entry['client_version'] = sanitize_text_field( (string) ( $params['clientInfo']['version'] ?? '' ) );
		}

		if ( 'tools/call' === $method ) {
			++$entry['tool_calls'];
			$entry['last_tool'] = sanitize_key( (string) ( $params['name'] ?? '' ) );
		}

		$usage[ $uuid ] = $entry;

		update_option( self::USAGE_OPTION, $usage, false );
	}

	/**
	 * All MCP connections: passwords created for MCP plus any password that has called the MCP server.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_all(): array {
		$usage = self::get_usage();
		$items = array();
		$users = array_unique(
			array_merge(
				array_map( 'intval', array_column( $usage, 'user_id' ) ),
				self::users_with_mcp_passwords()
			)
		);

		foreach ( $users as $user_id ) {
			foreach ( (array) \WP_Application_Passwords::get_user_application_passwords( $user_id ) as $password ) {
				$uuid = (string) $password['uuid'];

				if ( self::APP_ID !== ( $password['app_id'] ?? '' ) && ! isset( $usage[ $uuid ] ) ) {
					continue;
				}

				$items[] = self::format( $user_id, $password, $usage[ $uuid ] ?? array() );
			}
		}

		usort(
			$items,
			static function ( array $a, array $b ): int {
				$by_seen = (int) $b['last_seen'] <=> (int) $a['last_seen'];

				return 0 !== $by_seen ? $by_seen : (int) $b['created'] <=> (int) $a['created'];
			}
		);

		return $items;
	}

	/**
	 * Create an Application Password for an MCP client and its setup instructions.
	 *
	 * The plain password is returned only here; WordPress stores a hash.
	 *
	 * @param int    $user_id User the client acts as.
	 * @param string $client  Client key from CLIENTS.
	 * @return array<string, mixed>|\WP_Error Result.
	 */
	public static function create( int $user_id, string $client ) {
		if ( ! isset( self::CLIENTS[ $client ] ) ) {
			return new \WP_Error( 'pixeccte_mcp_unknown_client', __( 'Unknown MCP client.', 'pixels-core-creative-tools-for-elementor' ), array( 'status' => 400 ) );
		}

		$user = get_userdata( $user_id );

		if ( ! $user || ! wp_is_application_passwords_available_for_user( $user ) ) {
			return new \WP_Error( 'pixeccte_mcp_app_passwords_unavailable', __( 'Application Passwords are not available for this user. Run the health check for details.', 'pixels-core-creative-tools-for-elementor' ), array( 'status' => 400 ) );
		}

		$created = \WP_Application_Passwords::create_new_application_password(
			$user_id,
			array(
				/* translators: %s: AI client name, e.g. Claude Code. */
				'name'   => sprintf( __( 'Pixels MCP · %s', 'pixels-core-creative-tools-for-elementor' ), self::CLIENTS[ $client ] ),
				'app_id' => self::APP_ID,
			)
		);

		if ( is_wp_error( $created ) ) {
			return $created;
		}

		list( $password, $item ) = $created;

		return array(
			'connection' => self::format( $user_id, $item, array() ),
			'password'   => \WP_Application_Passwords::chunk_password( $password ),
			'setup'      => self::get_setup( $client, $user->user_login, $password ),
		);
	}

	/**
	 * Revoke a connection: delete its Application Password and its usage record.
	 *
	 * @param string $uuid Application Password uuid.
	 * @return true|\WP_Error Result.
	 */
	public static function revoke( string $uuid ) {
		foreach ( self::get_all() as $connection ) {
			if ( $connection['uuid'] !== $uuid ) {
				continue;
			}

			$deleted = \WP_Application_Passwords::delete_application_password( (int) $connection['user_id'], $uuid );

			if ( is_wp_error( $deleted ) ) {
				return $deleted;
			}

			$usage = self::get_usage();
			unset( $usage[ $uuid ] );
			update_option( self::USAGE_OPTION, $usage, false );

			return true;
		}

		return new \WP_Error( 'pixeccte_mcp_connection_not_found', __( 'This connection no longer exists.', 'pixels-core-creative-tools-for-elementor' ), array( 'status' => 404 ) );
	}

	/**
	 * Setup instructions for one client, with the credentials filled in.
	 *
	 * @param string $client   Client key.
	 * @param string $login    User login.
	 * @param string $password Plain Application Password.
	 * @return array<string, mixed>
	 */
	public static function get_setup( string $client, string $login, string $password ): array {
		$url    = self::get_endpoint_url();
		$header = 'Basic ' . base64_encode( $login . ':' . $password ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- HTTP Basic auth encoding.
		$name   = 'pixels';
		$json   = static function ( array $data ): string {
			return (string) wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
		};

		switch ( $client ) {
			case 'claude-code':
				return array(
					'format'  => 'shell',
					'where'   => __( 'Run this in a terminal, in the folder where you use Claude Code. Start a new Claude Code session afterwards.', 'pixels-core-creative-tools-for-elementor' ),
					'snippet' => sprintf( 'claude mcp add --transport http %s %s --header "Authorization: %s"', $name, $url, $header ),
				);

			case 'claude-desktop':
				return array(
					'format'  => 'json',
					'where'   => __( 'In Claude Desktop open Settings > Developer > Edit Config, add this to claude_desktop_config.json (merge it with any servers already there) and restart Claude Desktop. Needs Node.js.', 'pixels-core-creative-tools-for-elementor' ),
					'snippet' => $json(
						array(
							'mcpServers' => array(
								$name => array(
									'command' => 'npx',
									'args'    => array( '-y', 'mcp-remote', $url, '--header', 'Authorization:${PIXELS_AUTH}' ),
									'env'     => array( 'PIXELS_AUTH' => $header ),
								),
							),
						)
					),
				);

			case 'cursor':
				return array(
					'format'  => 'json',
					'where'   => __( 'In Cursor open Settings > MCP > Add new MCP server, or add this to .cursor/mcp.json in your project.', 'pixels-core-creative-tools-for-elementor' ),
					'snippet' => $json(
						array(
							'mcpServers' => array(
								$name => array(
									'url'     => $url,
									'headers' => array( 'Authorization' => $header ),
								),
							),
						)
					),
				);

			default:
				return array(
					'format'  => 'text',
					'where'   => __( 'Add a remote MCP server using Streamable HTTP with this URL and header.', 'pixels-core-creative-tools-for-elementor' ),
					'snippet' => sprintf( "URL: %s\nHeader: Authorization: %s", $url, $header ),
				);
		}
	}

	/**
	 * MCP endpoint URL (the route exists only while the extension is on).
	 *
	 * @return string Result.
	 */
	public static function get_endpoint_url(): string {
		return rest_url( 'pixeccte/v1/mcp' );
	}

	/**
	 * Check whether this site can accept MCP connections.
	 *
	 * @param bool $extension_active Whether the MCP Server extension is on.
	 * @return array<int, array<string, string>> Checks: id, status (pass|warn|fail), label, message.
	 */
	public static function health( bool $extension_active ): array {
		$checks = array();

		$checks[] = self::check(
			'extension',
			$extension_active ? 'pass' : 'fail',
			__( 'MCP Server extension', 'pixels-core-creative-tools-for-elementor' ),
			$extension_active
				? __( 'The MCP Server is switched on.', 'pixels-core-creative-tools-for-elementor' )
				: __( 'Switch on the MCP Server to accept connections.', 'pixels-core-creative-tools-for-elementor' )
		);

		$supported = wp_is_application_passwords_supported();
		$available = wp_is_application_passwords_available();

		if ( ! $supported ) {
			$app_status  = 'fail';
			$app_message = __( 'Application Passwords need HTTPS. Install an SSL certificate (local sites work when WP_ENVIRONMENT_TYPE is "local").', 'pixels-core-creative-tools-for-elementor' );
		} elseif ( ! $available ) {
			$app_status  = 'fail';
			$app_message = __( 'Application Passwords are turned off, usually by a security plugin or the wp_is_application_passwords_available filter.', 'pixels-core-creative-tools-for-elementor' );
		} else {
			$app_status  = 'pass';
			$app_message = __( 'AI clients can sign in with an Application Password.', 'pixels-core-creative-tools-for-elementor' );
		}

		$checks[] = self::check( 'app_passwords', $app_status, __( 'Application Passwords', 'pixels-core-creative-tools-for-elementor' ), $app_message );

		$checks[] = $extension_active
			? self::check_endpoint()
			: self::check( 'endpoint', 'warn', __( 'MCP endpoint', 'pixels-core-creative-tools-for-elementor' ), __( 'Checked once the MCP Server is on.', 'pixels-core-creative-tools-for-elementor' ) );

		$checks[] = self::check_authorization_header();

		return $checks;
	}

	/**
	 * Unauthenticated loopback request: the endpoint must answer 401 with our error code.
	 *
	 * @return array<string, string> Check.
	 */
	private static function check_endpoint(): array {
		$label    = __( 'MCP endpoint', 'pixels-core-creative-tools-for-elementor' );
		$response = wp_remote_post(
			self::get_endpoint_url(),
			array(
				'timeout'   => 10,
				'headers'   => array( 'Content-Type' => 'application/json' ),
				'body'      => '{"jsonrpc":"2.0","id":1,"method":"ping"}',
				'sslverify' => apply_filters( 'https_local_ssl_verify', false ), // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core filter.
			)
		);

		if ( is_wp_error( $response ) ) {
			/* translators: %s: error message. */
			return self::check( 'endpoint', 'fail', $label, sprintf( __( 'The site could not reach its own MCP endpoint: %s', 'pixels-core-creative-tools-for-elementor' ), $response->get_error_message() ) );
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		$body = json_decode( (string) wp_remote_retrieve_body( $response ), true );

		if ( 401 === $code && 'pixeccte_mcp_unauthorized' === ( $body['code'] ?? '' ) ) {
			return self::check( 'endpoint', 'pass', $label, __( 'The endpoint answers and asks for authentication, as expected.', 'pixels-core-creative-tools-for-elementor' ) );
		}

		if ( 404 === $code ) {
			return self::check( 'endpoint', 'fail', $label, __( 'The endpoint was not found. Save Settings > Permalinks once, and check that no plugin disables the REST API.', 'pixels-core-creative-tools-for-elementor' ) );
		}

		/* translators: %d: HTTP status code. */
		return self::check( 'endpoint', 'fail', $label, sprintf( __( 'The endpoint answered with HTTP %d. A security plugin or firewall may be blocking the REST API.', 'pixels-core-creative-tools-for-elementor' ), $code ) );
	}

	/**
	 * Some servers (often Apache with CGI/FastCGI) drop the Authorization header before PHP sees it.
	 *
	 * @return array<string, string> Check.
	 */
	private static function check_authorization_header(): array {
		$label    = __( 'Authorization header', 'pixels-core-creative-tools-for-elementor' );
		$response = wp_remote_get(
			rest_url( 'pixeccte/v1/mcp-connections/echo' ),
			array(
				'timeout'   => 10,
				'headers'   => array( 'Authorization' => 'Basic ' . base64_encode( 'pixeccte-check:not-a-password' ) ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Dummy Basic auth value.
				'sslverify' => apply_filters( 'https_local_ssl_verify', false ), // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Core filter.
			)
		);

		if ( is_wp_error( $response ) ) {
			/* translators: %s: error message. */
			return self::check( 'auth_header', 'warn', $label, sprintf( __( 'Could not be tested: %s', 'pixels-core-creative-tools-for-elementor' ), $response->get_error_message() ) );
		}

		$body = json_decode( (string) wp_remote_retrieve_body( $response ), true );

		if ( ! empty( $body['received'] ) ) {
			return self::check( 'auth_header', 'pass', $label, __( 'The server passes the Authorization header to WordPress.', 'pixels-core-creative-tools-for-elementor' ) );
		}

		if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			return self::check( 'auth_header', 'warn', $label, __( 'Could not be tested because the REST API did not answer.', 'pixels-core-creative-tools-for-elementor' ) );
		}

		return self::check(
			'auth_header',
			'fail',
			$label,
			__( 'Your server removes the Authorization header, so AI clients cannot sign in. On Apache add "CGIPassAuth On" or "SetEnvIf Authorization (.+) HTTP_AUTHORIZATION=$1" to .htaccess, or ask your host.', 'pixels-core-creative-tools-for-elementor' )
		);
	}

	/**
	 * One check result.
	 *
	 * @param string $id      Id.
	 * @param string $status  pass|warn|fail.
	 * @param string $label   Label.
	 * @param string $message Message.
	 * @return array<string, string> Result.
	 */
	private static function check( string $id, string $status, string $label, string $message ): array {
		return array(
			'id'      => $id,
			'status'  => $status,
			'label'   => $label,
			'message' => $message,
		);
	}

	/**
	 * One connection for the dashboard.
	 *
	 * @param int                  $user_id  User ID.
	 * @param array<string, mixed> $password Application Password item.
	 * @param array<string, mixed> $usage    Usage record.
	 * @return array<string, mixed> Result.
	 */
	private static function format( int $user_id, array $password, array $usage ): array {
		$user      = get_userdata( $user_id );
		$last_seen = max( (int) ( $usage['last_seen'] ?? 0 ), (int) ( $password['last_used'] ?? 0 ) );

		if ( ! $last_seen ) {
			$state = 'never';
		} elseif ( time() - $last_seen <= self::ACTIVE_WINDOW ) {
			$state = 'active';
		} else {
			$state = 'idle';
		}

		return array(
			'uuid'           => (string) $password['uuid'],
			'name'           => (string) $password['name'],
			'user_id'        => $user_id,
			'user'           => $user ? $user->display_name : '',
			'client'         => (string) ( $usage['client'] ?? '' ),
			'client_version' => (string) ( $usage['client_version'] ?? '' ),
			'created'        => (int) ( $password['created'] ?? 0 ),
			'last_seen'      => $last_seen,
			'last_ip'        => (string) ( $password['last_ip'] ?? '' ),
			'requests'       => (int) ( $usage['requests'] ?? 0 ),
			'tool_calls'     => (int) ( $usage['tool_calls'] ?? 0 ),
			'last_tool'      => (string) ( $usage['last_tool'] ?? '' ),
			'state'          => $state,
		);
	}

	/**
	 * Users who have an Application Password created from the dashboard.
	 *
	 * @return array<int, int>
	 */
	private static function users_with_mcp_passwords(): array {
		$ids = get_users(
			array(
				'fields'   => 'ID',
				'meta_key' => \WP_Application_Passwords::USERMETA_KEY_APPLICATION_PASSWORDS, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			)
		);

		return array_map( 'intval', $ids );
	}

	/**
	 * Usage records keyed by Application Password uuid.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	private static function get_usage(): array {
		$usage = get_option( self::USAGE_OPTION, array() );

		return is_array( $usage ) ? $usage : array();
	}
}
