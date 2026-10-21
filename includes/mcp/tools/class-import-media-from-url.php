<?php
/**
 * MCP tool: import_media_from_url.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp\Tools;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Downloads an image from a public URL into the media library.
 */
final class Import_Media_From_Url extends Tool {

	/**
	 * Largest file accepted, in bytes.
	 */
	const MAX_BYTES = 15 * MB_IN_BYTES;

	/**
	 * Image types accepted, by MIME type => extension.
	 */
	const TYPES = array(
		'image/jpeg' => 'jpg',
		'image/png'  => 'png',
		'image/gif'  => 'gif',
		'image/webp' => 'webp',
		'image/avif' => 'avif',
	);

	/**
	 * Meta key remembering where an import came from, so the same URL is not imported twice.
	 */
	const SOURCE_META = '_pixeccte_mcp_source_url';

	/**
	 * Name.
	 *
	 * @return string Result.
	 */
	public function name(): string {
		return 'import_media_from_url';
	}

	/**
	 * Title.
	 *
	 * @return string Result.
	 */
	public function title(): string {
		return 'Import media from URL';
	}

	/**
	 * Description.
	 *
	 * @return string Result.
	 */
	public function description(): string {
		return 'Downloads one image (JPEG, PNG, GIF, WebP or AVIF, up to 15 MB) from a public http(s) URL into the media library and returns it with a "setting" for image controls. Only import images the user may use, such as free stock photos (Unsplash, Pexels) or their own files; give a descriptive title and alt text. Importing the same URL again returns the existing image. Search the library with search_media first.';
	}

	/**
	 * Input schema.
	 *
	 * @return array<string, mixed>
	 */
	public function input_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'url'   => array(
					'type'   => 'string',
					'format' => 'uri',
				),
				'title' => array(
					'type'        => 'string',
					'default'     => '',
					'description' => 'Media title, also used for the file name.',
				),
				'alt'   => array(
					'type'        => 'string',
					'default'     => '',
					'description' => 'Alt text describing the image.',
				),
			),
			'required'   => array( 'url' ),
		);
	}

	/**
	 * Capability.
	 *
	 * @return string Result.
	 */
	public function capability(): string {
		return 'upload_files';
	}

	/**
	 * Execute.
	 *
	 * @param array<string, mixed> $args Args.
	 * @return array<string, mixed>|\WP_Error Result.
	 */
	public function execute( array $args ) {
		$url = esc_url_raw( trim( (string) $args['url'] ), array( 'http', 'https' ) );

		if ( '' === $url ) {
			return new \WP_Error( 'pixeccte_mcp_invalid_url', 'Give a public http or https image URL.' );
		}

		$existing = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::SOURCE_META, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => $url, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( ! empty( $existing ) ) {
			return Search_Media::summarize( (int) $existing[0] ) + array( 'already_imported' => true );
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		// download_url() uses wp_safe_remote_get(), which refuses private and local addresses.
		$tmp = download_url( $url, 60 );

		if ( is_wp_error( $tmp ) ) {
			return new \WP_Error( 'pixeccte_mcp_download_failed', sprintf( 'The image could not be downloaded: %s', $tmp->get_error_message() ) );
		}

		$size = (int) filesize( $tmp );
		$mime = (string) wp_get_image_mime( $tmp );

		if ( $size > self::MAX_BYTES || ! isset( self::TYPES[ $mime ] ) ) {
			wp_delete_file( $tmp );

			return new \WP_Error(
				'pixeccte_mcp_invalid_file',
				$size > self::MAX_BYTES
					? sprintf( 'The file is %s; the limit is 15 MB.', size_format( $size ) )
					: 'The URL did not return a JPEG, PNG, GIF, WebP or AVIF image.'
			);
		}

		$title = sanitize_text_field( (string) $args['title'] );
		$base  = sanitize_file_name( '' !== $title ? sanitize_title( $title ) : (string) pathinfo( (string) wp_parse_url( $url, PHP_URL_PATH ), PATHINFO_FILENAME ) );
		$base  = '' !== $base ? $base : 'image';

		$file = array(
			'name'     => $base . '.' . self::TYPES[ $mime ],
			'tmp_name' => $tmp,
		);

		$id = media_handle_sideload( $file, 0, '' !== $title ? $title : null );

		if ( is_wp_error( $id ) ) {
			wp_delete_file( $tmp );

			return $id;
		}

		$alt = sanitize_text_field( (string) $args['alt'] );

		if ( '' !== $alt ) {
			update_post_meta( $id, '_wp_attachment_image_alt', $alt );
		}

		update_post_meta( $id, self::SOURCE_META, $url );

		return Search_Media::summarize( (int) $id );
	}
}
