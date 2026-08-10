<?php
defined('ABSPATH') || exit; // Abort, if called directly.
/**
 * Header Footer Function
 */

use PixelsCore\Theme_Builder\Theme_Elementor;

/**
 * Checks if Header is enabled from HFE.
 *
 * @return bool True if header is enabled. False if header is not enabled
 */
function pixels_core_header_enabled() {
	$header_id = Theme_Elementor::get_settings( 'type_header', '' );
	$status    = false;

	if ( '' !== $header_id ) :
		$status = true;
	endif;

	return apply_filters( 'pixels_core_header_enabled', $status );
}

/**
 * Checks if Footer is enabled from HFE.
 *
 * @return bool True if header is enabled. False if header is not enabled.
 */
function pixels_core_footer_enabled() {
	$footer_id = Theme_Elementor::get_settings( 'type_footer', '' );
	$status    = false;

	if ( '' !== $footer_id ) :
		$status = true;
	endif;

	return apply_filters( 'pixels_core_footer_enabled', $status );
}

/**
 * Get HFE Header ID
 *
 * @return (String|boolean) header id if it is set else returns false.
 */
function pixels_core_get_header_id() {
	$header_id = Theme_Elementor::get_settings( 'type_header', '' );

	if ( '' === $header_id ) :
		$header_id = false;
	endif;

	return apply_filters( 'pixels_core_get_header_id', $header_id );
}

/**
 * Get HFE Footer ID
 *
 * @return (String|boolean) header id if it is set else returns false.
 */
function pixels_core_get_footer_id() {
	$footer_id = Theme_Elementor::get_settings( 'type_footer', '' );

	if ( '' === $footer_id ) :
		$footer_id = false;
	endif;

	return apply_filters( 'pixels_core_get_footer_id', $footer_id );
}

/**
 * Display header markup.
 *
 */
function pixels_core_render_header() {

	if ( false == apply_filters( 'pixels_core_enable_render_header', true ) ) :
		return;
	endif;

	$render_class = apply_filters( 'pixels_core_header_render_class', '' );
	$classes      = array_filter(
		array_map(
			'sanitize_html_class',
			array_merge( [ 'pixels-core-site-header' ], explode( ' ', (string) $render_class ) )
		)
	);
	?>
		<header itemtype="https://schema.org/WPHeader" itemscope="itemscope" id="pixels-core-masthead" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" role="banner">
			<?php PixelsCore\Theme_Builder\Theme_Elementor::get_header_content(); ?>
		</header>
	<?php
}

/**
 * Display footer markup.
 *
 */
function pixels_core_render_footer() {

	if ( false == apply_filters( 'pixels_core_enable_render_footer', true ) ) :
		return;
	endif;

	?>
		<footer itemtype="https://schema.org/WPFooter" itemscope="itemscope" id="colophon" role="contentinfo">
			<?php PixelsCore\Theme_Builder\Theme_Elementor::get_footer_content(); ?>
		</footer>
	<?php

}

/**
 * Get Single Post template ID.
 *
 * @return (string|bool) Template ID if set, or false.
 */
function pixels_core_get_single_post_id() {
	$single_id = \PixelsCore\Theme_Builder\Theme_Elementor::get_template_id( 'type_single_post' );

	if ( '' === $single_id ) {
		$single_id = false;
	}

	return apply_filters( 'pixels_core_get_single_post_id', $single_id );
}

/**
 * Checks if Single Post template is enabled.
 *
 * @return bool
 */
function pixels_core_single_post_enabled() {
	$single_id = \PixelsCore\Theme_Builder\Theme_Elementor::get_template_id( 'type_single_post' );
	$status    = ( '' !== $single_id );

	return apply_filters( 'pixels_core_single_post_enabled', $status );
}

/**
 * Get Archive Post template ID.
 *
 * @return (string|bool) Template ID if set, or false.
 */
function pixels_core_get_archive_post_id() {
	$archive_id = \PixelsCore\Theme_Builder\Theme_Elementor::get_template_id( 'type_archive_post' );

	if ( '' === $archive_id ) {
		$archive_id = false;
	}

	return apply_filters( 'pixels_core_get_archive_post_id', $archive_id );
}

/**
 * Checks if Archive Post template is enabled.
 *
 * @return bool
 */
function pixels_core_archive_post_enabled() {
	$archive_id = \PixelsCore\Theme_Builder\Theme_Elementor::get_template_id( 'type_archive_post' );
	$status     = ( '' !== $archive_id );

	return apply_filters( 'pixels_core_archive_post_enabled', $status );
}

/**
 * Allowed HTML for Elementor builder markup.
 *
 * Starts from the post allowlist and extends it for SVG/iframe markup that
 * Elementor widgets commonly render. Content is produced by Elementor's
 * get_builder_content_for_display() for validated theme-builder templates.
 *
 * @return array<string, array<string, bool|array>>
 */
function pixels_core_get_builder_allowed_html() {
	static $allowed = null;

	if ( null === $allowed ) {
		$allowed = wp_kses_allowed_html( 'post' );

		$svg_attrs = [
			'aria-hidden'       => true,
			'aria-labelledby'   => true,
			'class'             => true,
			'fill'              => true,
			'fill-opacity'      => true,
			'fill-rule'         => true,
			'height'            => true,
			'id'                => true,
			'opacity'           => true,
			'role'              => true,
			'stroke'            => true,
			'stroke-dasharray'  => true,
			'stroke-linecap'    => true,
			'stroke-linejoin'   => true,
			'stroke-miterlimit' => true,
			'stroke-opacity'    => true,
			'stroke-width'      => true,
			'style'             => true,
			'transform'         => true,
			'viewbox'           => true,
			'width'             => true,
			'x'                 => true,
			'xmlns'             => true,
			'xmlns:xlink'       => true,
			'y'                 => true,
		];

		$allowed['svg'] = array_merge(
			$svg_attrs,
			[
				'focusable'           => true,
				'preserveaspectratio' => true,
				'version'             => true,
			]
		);
		$allowed['g']        = $svg_attrs;
		$allowed['path']     = array_merge( $svg_attrs, [ 'd' => true, 'clip-rule' => true ] );
		$allowed['circle']   = array_merge( $svg_attrs, [ 'cx' => true, 'cy' => true, 'r' => true ] );
		$allowed['ellipse']  = array_merge( $svg_attrs, [ 'cx' => true, 'cy' => true, 'rx' => true, 'ry' => true ] );
		$allowed['line']     = array_merge( $svg_attrs, [ 'x1' => true, 'x2' => true, 'y1' => true, 'y2' => true ] );
		$allowed['polygon']  = array_merge( $svg_attrs, [ 'points' => true ] );
		$allowed['polyline'] = array_merge( $svg_attrs, [ 'points' => true ] );
		$allowed['rect']     = array_merge( $svg_attrs, [ 'rx' => true, 'ry' => true ] );
		$allowed['defs']     = $svg_attrs;
		$allowed['clippath'] = $svg_attrs;
		$allowed['use']      = array_merge( $svg_attrs, [ 'href' => true, 'xlink:href' => true ] );
		$allowed['symbol']   = $svg_attrs;
		$allowed['title']    = [ 'title' => true ];
		$allowed['desc']     = true;

		$allowed['iframe'] = [
			'allow'           => true,
			'allowfullscreen' => true,
			'class'           => true,
			'frameborder'     => true,
			'height'          => true,
			'id'              => true,
			'loading'         => true,
			'name'            => true,
			'referrerpolicy'  => true,
			'sandbox'         => true,
			'src'             => true,
			'style'           => true,
			'title'           => true,
			'width'           => true,
		];
	}

	/**
	 * Filter the KSES allowlist used for Elementor builder HTML.
	 *
	 * @param array<string, array<string, bool|array>> $allowed Allowed tags/attributes.
	 */
	return apply_filters( 'pixels_core_builder_allowed_html', $allowed );
}

/**
 * Escape Elementor builder HTML for safe return/echo.
 *
 * @param string $html Raw builder HTML from Elementor.
 * @return string
 */
function pixels_core_escape_builder_html( $html ) {
	if ( ! is_string( $html ) || '' === $html ) {
		return '';
	}

	return wp_kses( $html, pixels_core_get_builder_allowed_html() );
}

function pixels_core_render_archive_post() {
	$archive_id = apply_filters( 'pixels_core_archive_post_template_id', pixels_core_get_archive_post_id() );

	if ( empty( $archive_id ) ) {
		return;
	}

	echo '<div class="pixels-core-archive-template">';
	echo wp_kses(
		\Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $archive_id ),
		pixels_core_get_builder_allowed_html()
	);
	echo '</div>';
}

function pixels_core_get_404_id() {
	$page_404 = \PixelsCore\Theme_Builder\Theme_Elementor::get_settings( 'type_404', '' );
	return ( '' === $page_404 ) ? false : apply_filters( 'pixels_core_get_404_id', $page_404 );
}

function pixels_core_404_enabled() {
	$page_404 = \PixelsCore\Theme_Builder\Theme_Elementor::get_settings( 'type_404', '' );
	return apply_filters( 'pixels_core_404_enabled', $page_404 !== '' );
}

function pixels_core_render_404_page() {
	$page_404 = apply_filters( 'pixels_core_404_template_id', pixels_core_get_404_id() );

	if ( empty( $page_404 ) ) {
		return;
	}

	echo '<div class="pixels-core-404-template">';
	echo wp_kses(
		\Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $page_404 ),
		pixels_core_get_builder_allowed_html()
	);
	echo '</div>';
}

/**
 * Post types available for theme template preview dropdowns.
 *
 * @return array<int, string>
 */
function pixels_core_get_theme_builder_preview_post_types() {
	$post_types = get_post_types( [ 'public' => true ], 'names' );

	unset( $post_types['attachment'], $post_types['pixels-core-theme'], $post_types['page'] );

	/**
	 * Filter previewable post types for theme builder templates.
	 *
	 * @param array<int, string> $post_types Post type slugs.
	 */
	return apply_filters( 'pixels_core_theme_builder_preview_post_types', array_values( $post_types ) );
}

/**
 * Get published posts grouped by post type for preview dropdowns.
 *
 * @return array<string, array<int, \WP_Post>>
 */
function pixels_core_get_theme_builder_preview_posts_by_type() {
	$posts = get_posts(
		[
			'post_type'      => pixels_core_get_theme_builder_preview_post_types(),
			'posts_per_page' => 100,
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'ASC',
		]
	);

	$posts_by_type = [];

	foreach ( $posts as $post ) {
		$posts_by_type[ $post->post_type ][] = $post;
	}

	return $posts_by_type;
}

/**
 * Build select options for preview post dropdowns.
 *
 * @return array<string, string>
 */
function pixels_core_get_theme_builder_preview_post_select_options() {
	$posts_by_type   = pixels_core_get_theme_builder_preview_posts_by_type();
	$options         = [ '' => esc_html__( 'Select a post', 'pixels-core-creative-tools-for-elementor' ) ];
	$post_type_names = get_post_types(
		[
			'name'   => array_keys( $posts_by_type ),
			'public' => true,
		],
		'objects'
	);

	foreach ( $posts_by_type as $type => $type_posts ) {
		$label = isset( $post_type_names[ $type ] ) ? $post_type_names[ $type ]->labels->singular_name : ucfirst( $type );

		foreach ( $type_posts as $post ) {
			$options[ (string) $post->ID ] = sprintf(
				'%1$s: %2$s',
				$label,
				$post->post_title
			);
		}
	}

	return $options;
}

/**
 * Prefixed post meta key for theme builder template type.
 *
 * @return string
 */
function pixels_core_get_theme_template_type_meta_key() {
	return 'pixels_core_hf_template_type';
}

/**
 * Resolve a pixels-core-theme post ID for template-type helpers.
 *
 * @param int $post_id Optional template post ID.
 * @return int
 */
function pixels_core_resolve_theme_template_post_id( $post_id = 0 ) {
	$post_id = absint( $post_id );

	if ( ! $post_id && class_exists( '\Elementor\Plugin' ) ) {
		$document = \Elementor\Plugin::$instance->documents->get_current();

		if ( $document ) {
			$post_id = absint( $document->get_main_id() );
		}
	}

	if ( ! $post_id && is_singular( 'pixels-core-theme' ) ) {
		$post_id = absint( get_queried_object_id() );
	}

	return $post_id;
}

/**
 * Get the template type for a pixels-core-theme document.
 *
 * Falls back to the legacy unprefixed meta key and migrates it when found.
 *
 * @param int $post_id Optional template post ID.
 * @return string
 */
function pixels_core_get_theme_template_type( $post_id = 0 ) {
	$post_id = pixels_core_resolve_theme_template_post_id( $post_id );

	if ( ! $post_id || 'pixels-core-theme' !== get_post_type( $post_id ) ) {
		return '';
	}

	$meta_key = pixels_core_get_theme_template_type_meta_key();
	$type     = get_post_meta( $post_id, $meta_key, true );

	if ( '' === $type || false === $type ) {
		$legacy = get_post_meta( $post_id, 'ehf_template_type', true );

		if ( is_string( $legacy ) && '' !== $legacy ) {
			update_post_meta( $post_id, $meta_key, $legacy );
			delete_post_meta( $post_id, 'ehf_template_type' );
			$type = $legacy;
		}
	}

	return is_string( $type ) ? $type : '';
}

/**
 * Persist the template type using the prefixed meta key.
 *
 * @param int    $post_id Template post ID.
 * @param string $type    Template type slug.
 * @return void
 */
function pixels_core_update_theme_template_type( $post_id, $type ) {
	$post_id = absint( $post_id );

	if ( ! $post_id ) {
		return;
	}

	update_post_meta( $post_id, pixels_core_get_theme_template_type_meta_key(), (string) $type );
	delete_post_meta( $post_id, 'ehf_template_type' );
}

/**
 * One-time migration from ehf_template_type to pixels_core_hf_template_type.
 *
 * @return void
 */
function pixels_core_maybe_migrate_theme_template_type_meta() {
	if ( get_option( 'pixels_core_template_type_meta_migrated' ) ) {
		return;
	}

	$post_ids = get_posts(
		[
			'post_type'              => 'pixels-core-theme',
			'post_status'            => 'any',
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- One-time migration query.
			'meta_key'               => 'ehf_template_type',
		]
	);

	foreach ( $post_ids as $post_id ) {
		$legacy = get_post_meta( $post_id, 'ehf_template_type', true );

		if ( ! is_string( $legacy ) || '' === $legacy ) {
			delete_post_meta( $post_id, 'ehf_template_type' );
			continue;
		}

		if ( '' === (string) get_post_meta( $post_id, pixels_core_get_theme_template_type_meta_key(), true ) ) {
			update_post_meta( $post_id, pixels_core_get_theme_template_type_meta_key(), $legacy );
		}

		delete_post_meta( $post_id, 'ehf_template_type' );
	}

	update_option( 'pixels_core_template_type_meta_migrated', '1', false );
}

/**
 * Whether the current editor or request is a Single Post theme template.
 *
 * @param int $post_id Optional template post ID.
 * @return bool
 */
function pixels_core_is_single_post_template_context( $post_id = 0 ) {
	return 'type_single_post' === pixels_core_get_theme_template_type( $post_id );
}

/**
 * Get the preview post ID configured on a theme template.
 *
 * @param int $template_id Optional template post ID.
 * @return int
 */
function pixels_core_get_theme_builder_preview_post_id( $template_id = 0 ) {
	if ( ! $template_id && class_exists( '\Elementor\Plugin' ) ) {
		$document = \Elementor\Plugin::$instance->documents->get_current();

		if ( $document ) {
			$template_id = $document->get_main_id();
		}
	}

	if ( ! $template_id && is_singular( 'pixels-core-theme' ) ) {
		$template_id = get_queried_object_id();
	}

	if ( ! $template_id || 'pixels-core-theme' !== get_post_type( $template_id ) ) {
		return 0;
	}

	return (int) get_post_meta( $template_id, '_pixels_core_preview_post_id', true );
}

/**
 * Resolve the post ID used by single-template dynamic widgets.
 *
 * @return int
 */
function pixels_core_get_theme_builder_post_id() {
	if ( is_singular() && ! is_singular( 'pixels-core-theme' ) ) {
		return (int) get_queried_object_id();
	}

	$preview_id = pixels_core_get_theme_builder_preview_post_id();

	if ( $preview_id ) {
		return $preview_id;
	}

	$posts = get_posts(
		[
			'post_type'      => 'post',
			'posts_per_page' => 1,
			'post_status'    => 'publish',
		]
	);

	return ! empty( $posts ) ? (int) $posts[0]->ID : 0;
}

/**
 * Get the post title for single-template dynamic widgets.
 *
 * @return string
 */
function pixels_core_get_theme_builder_post_title() {
	$post_id = pixels_core_get_theme_builder_post_id();

	if ( $post_id ) {
		return get_the_title( $post_id );
	}

	return esc_html__( 'Sample Post Title', 'pixels-core-creative-tools-for-elementor' );
}

/**
 * Get the post permalink for single-template dynamic widgets.
 *
 * @return string
 */
function pixels_core_get_theme_builder_post_permalink() {
	$post_id = pixels_core_get_theme_builder_post_id();

	if ( $post_id ) {
		$permalink = get_permalink( $post_id );

		if ( $permalink ) {
			return $permalink;
		}
	}

	return '#';
}