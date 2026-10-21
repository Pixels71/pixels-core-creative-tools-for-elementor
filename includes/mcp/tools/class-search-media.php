<?php
/**
 * MCP tool: search_media.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp\Tools;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Finds images in the media library, ready to drop into image settings.
 */
final class Search_Media extends Tool {

	/**
	 * Most attachments scanned when filtering by size or orientation.
	 */
	const SCAN_LIMIT = 1000;

	/**
	 * Name.
	 *
	 * @return string Result.
	 */
	public function name(): string {
		return 'search_media';
	}

	/**
	 * Title.
	 *
	 * @return string Result.
	 */
	public function title(): string {
		return 'Search media';
	}

	/**
	 * Description.
	 *
	 * @return string Result.
	 */
	public function description(): string {
		return 'Searches the media library for images by title, file name, alt text or caption, newest first, optionally only large enough or of one orientation. Each result has "setting": pass it as-is to an image setting (e.g. {"image": <setting>} or "background_image"). The library has no idea what is in a picture beyond its title and alt text, so look at the thumbnail URLs when it matters. Use import_media_from_url when nothing fits.';
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
				'search'      => array(
					'type'    => 'string',
					'default' => '',
				),
				'min_width'   => array(
					'type'        => 'integer',
					'minimum'     => 0,
					'default'     => 0,
					'description' => 'Only images at least this wide (px), e.g. 1600 for a full-width hero.',
				),
				'orientation' => array(
					'type'    => 'string',
					'enum'    => array( 'any', 'landscape', 'portrait', 'square' ),
					'default' => 'any',
				),
				'per_page'    => array(
					'type'    => 'integer',
					'minimum' => 1,
					'maximum' => 50,
					'default' => 20,
				),
				'page'        => array(
					'type'    => 'integer',
					'minimum' => 1,
					'default' => 1,
				),
			),
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
	 * Read only.
	 *
	 * @return bool Result.
	 */
	public function is_read_only(): bool {
		return true;
	}

	/**
	 * Execute.
	 *
	 * @param array<string, mixed> $args Args.
	 * @return array<string, mixed> Result.
	 */
	public function execute( array $args ) {
		$per_page    = (int) $args['per_page'];
		$page        = (int) $args['page'];
		$min_width   = (int) $args['min_width'];
		$orientation = (string) $args['orientation'];
		$filtered    = $min_width > 0 || 'any' !== $orientation;

		$query = array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'post_mime_type' => 'image',
			's'              => (string) $args['search'],
			'orderby'        => 'date',
			'order'          => 'DESC',
		);

		// Size and orientation live in serialized metadata, so filter in PHP over the newest attachments.
		if ( $filtered ) {
			$query['posts_per_page'] = self::SCAN_LIMIT;
			$query['fields']         = 'ids';
			$query['no_found_rows']  = true;

			$ids = array_values(
				array_filter(
					( new \WP_Query( $query ) )->posts,
					static function ( $id ) use ( $min_width, $orientation ): bool {
						$meta   = wp_get_attachment_metadata( (int) $id );
						$width  = (int) ( $meta['width'] ?? 0 );
						$height = (int) ( $meta['height'] ?? 0 );

						return $width >= $min_width && self::matches_orientation( $width, $height, $orientation );
					}
				)
			);

			$total = count( $ids );
			$ids   = array_slice( $ids, ( $page - 1 ) * $per_page, $per_page );
		} else {
			$query['posts_per_page'] = $per_page;
			$query['paged']          = $page;
			$query['fields']         = 'ids';

			$result = new \WP_Query( $query );
			$ids    = $result->posts;
			$total  = (int) $result->found_posts;
		}

		return array(
			'total'       => $total,
			'total_pages' => (int) ceil( $total / $per_page ),
			'page'        => $page,
			'items'       => array_map(
				static function ( $id ): array {
					return self::summarize( (int) $id );
				},
				$ids
			),
		);
	}

	/**
	 * Whether a size matches an orientation.
	 *
	 * @param int    $width       Width.
	 * @param int    $height      Height.
	 * @param string $orientation Orientation.
	 * @return bool Result.
	 */
	private static function matches_orientation( int $width, int $height, string $orientation ): bool {
		if ( 'any' === $orientation ) {
			return true;
		}

		if ( ! $width || ! $height ) {
			return false;
		}

		$ratio = $width / $height;

		if ( 'square' === $orientation ) {
			return $ratio >= 0.9 && $ratio <= 1.1;
		}

		return 'landscape' === $orientation ? $ratio > 1.1 : $ratio < 0.9;
	}

	/**
	 * Image summary shared by the media tools.
	 *
	 * @param int $id Attachment ID.
	 * @return array<string, mixed> Result.
	 */
	public static function summarize( int $id ): array {
		$meta = wp_get_attachment_metadata( $id );
		$url  = (string) wp_get_attachment_url( $id );

		return array(
			'id'        => $id,
			'title'     => get_the_title( $id ),
			'alt'       => (string) get_post_meta( $id, '_wp_attachment_image_alt', true ),
			'mime'      => (string) get_post_mime_type( $id ),
			'width'     => (int) ( $meta['width'] ?? 0 ),
			'height'    => (int) ( $meta['height'] ?? 0 ),
			'url'       => $url,
			'thumbnail' => (string) wp_get_attachment_image_url( $id, 'medium' ),
			'setting'   => array(
				'id'  => $id,
				'url' => $url,
			),
		);
	}
}
