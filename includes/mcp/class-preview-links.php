<?php
/**
 * Expiring public preview links for MCP.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Lets anyone with a link view one unpublished page until the link expires, so an AI client
 * (or a browser it drives) can check a draft without signing in.
 *
 * Only a hash of each token is stored, in post meta, together with its expiry.
 */
final class Preview_Links {

	const QUERY_VAR = 'pixeccte_preview';
	const META_KEY  = '_pixeccte_mcp_preview_tokens';

	/**
	 * Whether the current request is showing a page through a preview link.
	 *
	 * @var bool
	 */
	private static bool $active = false;

	/**
	 * Register the front-end hooks.
	 */
	public static function init(): void {
		add_filter( 'posts_results', array( self::class, 'show_previewed_post' ), 10, 2 );
		add_filter( 'redirect_canonical', array( self::class, 'keep_preview_url' ) );
		add_action( 'template_redirect', array( self::class, 'send_headers' ) );
		add_filter( 'wp_robots', array( self::class, 'no_robots' ) );
	}

	/**
	 * Create a link to a post that works without signing in.
	 *
	 * @param int $post_id Post ID.
	 * @param int $hours   Lifetime in hours.
	 * @return array{url: string, expires: string} Result.
	 */
	public static function create( int $post_id, int $hours ): array {
		$token   = wp_generate_password( 32, false, false );
		$expires = time() + $hours * HOUR_IN_SECONDS;
		$tokens  = self::get_tokens( $post_id );

		$tokens[ self::hash( $token ) ] = $expires;

		update_post_meta( $post_id, self::META_KEY, $tokens );

		$url = 'publish' === get_post_status( $post_id ) ? get_permalink( $post_id ) : self::draft_url( $post_id );

		return array(
			'url'     => add_query_arg( self::QUERY_VAR, $token, $url ),
			'expires' => gmdate( 'c', $expires ),
		);
	}

	/**
	 * Mark an unpublished post as viewable for this request when the link is valid.
	 *
	 * Runs before WP_Query checks the post status, like a logged-in preview does.
	 *
	 * @param array<int, \WP_Post> $posts Query results.
	 * @param \WP_Query            $query Query.
	 * @return array<int, \WP_Post> Result.
	 */
	public static function show_previewed_post( $posts, $query ) {
		if ( is_admin() || ! $query->is_main_query() || 1 !== count( (array) $posts ) || ! $query->is_singular() ) {
			return $posts;
		}

		$token = isset( $_GET[ self::QUERY_VAR ] ) ? sanitize_text_field( wp_unslash( $_GET[ self::QUERY_VAR ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- The token is the credential.

		if ( '' === $token || ! self::is_valid( (int) $posts[0]->ID, $token ) ) {
			return $posts;
		}

		self::$active = true;

		if ( in_array( $posts[0]->post_status, array( 'draft', 'pending', 'future', 'private' ), true ) ) {
			$posts[0]->post_status = 'publish';
		}

		return $posts;
	}

	/**
	 * Stay on the preview URL.
	 *
	 * The previewed post is marked published for this request, so WordPress would otherwise
	 * redirect to its pretty permalink, and a draft that has no slug yet would land on the home page.
	 *
	 * @param string|false $redirect_url Canonical URL.
	 * @return string|false Result.
	 */
	public static function keep_preview_url( $redirect_url ) {
		return self::$active ? false : $redirect_url;
	}

	/**
	 * Keep preview pages out of caches.
	 */
	public static function send_headers(): void {
		if ( self::$active ) {
			nocache_headers();
		}
	}

	/**
	 * Keep preview pages out of search engines.
	 *
	 * @param array<string, bool|string> $robots Robots directives.
	 * @return array<string, bool|string> Result.
	 */
	public static function no_robots( $robots ) {
		if ( self::$active ) {
			$robots['noindex']  = true;
			$robots['nofollow'] = true;
		}

		return $robots;
	}

	/**
	 * Whether a token opens a post now. Expired tokens are removed.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $token   Token from the URL.
	 * @return bool Result.
	 */
	private static function is_valid( int $post_id, string $token ): bool {
		$tokens = self::get_tokens( $post_id );
		$live   = array_filter(
			$tokens,
			static function ( $expires ): bool {
				return (int) $expires > time();
			}
		);

		if ( count( $live ) !== count( $tokens ) ) {
			update_post_meta( $post_id, self::META_KEY, $live );
		}

		$hash = self::hash( $token );

		foreach ( array_keys( $live ) as $stored ) {
			if ( hash_equals( (string) $stored, $hash ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Stored token hashes => expiry timestamps.
	 *
	 * @param int $post_id Post ID.
	 * @return array<string, int> Result.
	 */
	private static function get_tokens( int $post_id ): array {
		$tokens = get_post_meta( $post_id, self::META_KEY, true );

		return is_array( $tokens ) ? $tokens : array();
	}

	/**
	 * Hash a token for storage.
	 *
	 * @param string $token Token.
	 * @return string Result.
	 */
	private static function hash( string $token ): string {
		return hash_hmac( 'sha256', $token, wp_salt( 'auth' ) );
	}

	/**
	 * Plain link to an unpublished post (pretty permalinks only exist once it is published).
	 *
	 * @param int $post_id Post ID.
	 * @return string Result.
	 */
	private static function draft_url( int $post_id ): string {
		$post_type = get_post_type( $post_id );

		if ( 'page' === $post_type ) {
			return add_query_arg( 'page_id', $post_id, home_url( '/' ) );
		}

		if ( 'post' === $post_type ) {
			return add_query_arg( 'p', $post_id, home_url( '/' ) );
		}

		return add_query_arg(
			array(
				'post_type' => $post_type,
				'p'         => $post_id,
			),
			home_url( '/' )
		);
	}
}
