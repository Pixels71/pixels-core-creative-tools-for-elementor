<?php
defined('ABSPATH') || exit; // Abort, if called directly.
/**
 * Header Footer Function
 */

use PixelsElementorAddons\Theme_Builder\Theme_Elementor;

/**
 * Checks if Header is enabled from HFE.
 *
 * @return bool True if header is enabled. False if header is not enabled
 */
function pixels_header_enabled() {
	$header_id = Theme_Elementor::get_settings( 'type_header', '' );
	$status    = false;

	if ( '' !== $header_id ) :
		$status = true;
	endif;

	return apply_filters( 'pixels_header_enabled', $status );
}

/**
 * Checks if Footer is enabled from HFE.
 *
 * @return bool True if header is enabled. False if header is not enabled.
 */
function pixels_footer_enabled() {
	$footer_id = Theme_Elementor::get_settings( 'type_footer', '' );
	$status    = false;

	if ( '' !== $footer_id ) :
		$status = true;
	endif;

	return apply_filters( 'pixels_footer_enabled', $status );
}

/**
 * Get HFE Header ID
 *
 * @return (String|boolean) header id if it is set else returns false.
 */
function pixels_get_header_id() {
	$header_id = Theme_Elementor::get_settings( 'type_header', '' );

	if ( '' === $header_id ) :
		$header_id = false;
	endif;

	return apply_filters( 'pixels_get_header_id', $header_id );
}

/**
 * Get HFE Footer ID
 *
 * @return (String|boolean) header id if it is set else returns false.
 */
function pixels_get_footer_id() {
	$footer_id = Theme_Elementor::get_settings( 'type_footer', '' );

	if ( '' === $footer_id ) :
		$footer_id = false;
	endif;

	return apply_filters( 'pixels_get_footer_id', $footer_id );
}

/**
 * Display header markup.
 *
 */
function pixels_render_header() {

	if ( false == apply_filters( 'pixels_enable_render_header', true ) ) :
		return;
	endif;

	$render_class = apply_filters( 'pixels_header_render_class', '' );
	$classes      = array_filter(
		array_map(
			'sanitize_html_class',
			array_merge( [ 'pixels-site-header' ], explode( ' ', (string) $render_class ) )
		)
	);
	?>
		<header itemtype="https://schema.org/WPHeader" itemscope="itemscope" id="pixels-masthead" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" role="banner">
			<?php PixelsElementorAddons\Theme_Builder\Theme_Elementor::get_header_content(); ?>
		</header>
	<?php
}

/**
 * Display footer markup.
 *
 */
function pixels_render_footer() {

	if ( false == apply_filters( 'pixels_enable_render_footer', true ) ) :
		return;
	endif;

	?>
		<footer itemtype="https://schema.org/WPFooter" itemscope="itemscope" id="colophon" role="contentinfo">
			<?php PixelsElementorAddons\Theme_Builder\Theme_Elementor::get_footer_content(); ?>
		</footer>
	<?php

}

/**
 * Get Single Post template ID.
 *
 * @return (string|bool) Template ID if set, or false.
 */
function pixels_get_single_post_id() {
	$single_id = \PixelsElementorAddons\Theme_Builder\Theme_Elementor::get_template_id( 'type_single_post' );

	if ( '' === $single_id ) {
		$single_id = false;
	}

	return apply_filters( 'pixels_get_single_post_id', $single_id );
}

/**
 * Checks if Single Post template is enabled.
 *
 * @return bool
 */
function pixels_single_post_enabled() {
	$single_id = \PixelsElementorAddons\Theme_Builder\Theme_Elementor::get_template_id( 'type_single_post' );
	$status    = ( '' !== $single_id );

	return apply_filters( 'pixels_single_post_enabled', $status );
}

/**
 * Get Archive Post template ID.
 *
 * @return (string|bool) Template ID if set, or false.
 */
function pixels_get_archive_post_id() {
	$archive_id = \PixelsElementorAddons\Theme_Builder\Theme_Elementor::get_template_id( 'type_archive_post' );

	if ( '' === $archive_id ) {
		$archive_id = false;
	}

	return apply_filters( 'pixels_get_archive_post_id', $archive_id );
}

/**
 * Checks if Archive Post template is enabled.
 *
 * @return bool
 */
function pixels_archive_post_enabled() {
	$archive_id = \PixelsElementorAddons\Theme_Builder\Theme_Elementor::get_template_id( 'type_archive_post' );
	$status     = ( '' !== $archive_id );

	return apply_filters( 'pixels_archive_post_enabled', $status );
}

function pixels_render_archive_post() {
	$archive_id = apply_filters( 'pixels_archive_post_template_id', pixels_get_archive_post_id() );

	if ( empty( $archive_id ) ) {
		return;
	}

	echo '<div class="pixels-archive-template">';
	// Elementor output is already sanitized.
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $archive_id );
	echo '</div>';
}

function pixels_get_404_id() {
	$page_404 = \PixelsElementorAddons\Theme_Builder\Theme_Elementor::get_settings( 'type_404', '' );
	return ( '' === $page_404 ) ? false : apply_filters( 'pixels_get_404_id', $page_404 );
}

function pixels_404_enabled() {
	$page_404 = \PixelsElementorAddons\Theme_Builder\Theme_Elementor::get_settings( 'type_404', '' );
	return apply_filters( 'pixels_404_enabled', $page_404 !== '' );
}

function pixels_render_404_page() {
	$page_404 = apply_filters( 'pixels_404_template_id', pixels_get_404_id() );

	if ( empty( $page_404 ) ) {
		return;
	}

	echo '<div class="pixels-404-template">';
	// Elementor output is already sanitized.
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $page_404 );
	echo '</div>';
}

/**
 * Post types available for theme template preview dropdowns.
 *
 * @return array<int, string>
 */
function pixels_get_theme_builder_preview_post_types() {
	$post_types = get_post_types( [ 'public' => true ], 'names' );

	unset( $post_types['attachment'], $post_types['pixels-theme'], $post_types['page'] );

	/**
	 * Filter previewable post types for theme builder templates.
	 *
	 * @param array<int, string> $post_types Post type slugs.
	 */
	return apply_filters( 'pixels_theme_builder_preview_post_types', array_values( $post_types ) );
}

/**
 * Get published posts grouped by post type for preview dropdowns.
 *
 * @return array<string, array<int, \WP_Post>>
 */
function pixels_get_theme_builder_preview_posts_by_type() {
	$posts = get_posts(
		[
			'post_type'      => pixels_get_theme_builder_preview_post_types(),
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
function pixels_get_theme_builder_preview_post_select_options() {
	$posts_by_type   = pixels_get_theme_builder_preview_posts_by_type();
	$options         = [ '' => esc_html__( 'Select a post', 'pixels-elementor-addons' ) ];
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
 * Get the template type for a pixels-theme document.
 *
 * @param int $post_id Optional template post ID.
 * @return string
 */
function pixels_get_theme_template_type( $post_id = 0 ) {
	if ( ! $post_id && class_exists( '\Elementor\Plugin' ) ) {
		$document = \Elementor\Plugin::$instance->documents->get_current();

		if ( $document ) {
			$post_id = $document->get_main_id();
		}
	}

	if ( ! $post_id && is_singular( 'pixels-theme' ) ) {
		$post_id = get_queried_object_id();
	}

	if ( ! $post_id || 'pixels-theme' !== get_post_type( $post_id ) ) {
		return '';
	}

	return (string) get_post_meta( $post_id, 'ehf_template_type', true );
}

/**
 * Whether the current editor or request is a Single Post theme template.
 *
 * @param int $post_id Optional template post ID.
 * @return bool
 */
function pixels_is_single_post_template_context( $post_id = 0 ) {
	return 'type_single_post' === pixels_get_theme_template_type( $post_id );
}

/**
 * Get the preview post ID configured on a theme template.
 *
 * @param int $template_id Optional template post ID.
 * @return int
 */
function pixels_get_theme_builder_preview_post_id( $template_id = 0 ) {
	if ( ! $template_id && class_exists( '\Elementor\Plugin' ) ) {
		$document = \Elementor\Plugin::$instance->documents->get_current();

		if ( $document ) {
			$template_id = $document->get_main_id();
		}
	}

	if ( ! $template_id && is_singular( 'pixels-theme' ) ) {
		$template_id = get_queried_object_id();
	}

	if ( ! $template_id || 'pixels-theme' !== get_post_type( $template_id ) ) {
		return 0;
	}

	return (int) get_post_meta( $template_id, '_pixels_preview_post_id', true );
}

/**
 * Resolve the post ID used by single-template dynamic widgets.
 *
 * @return int
 */
function pixels_get_theme_builder_post_id() {
	if ( is_singular() && ! is_singular( 'pixels-theme' ) ) {
		return (int) get_queried_object_id();
	}

	$preview_id = pixels_get_theme_builder_preview_post_id();

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
function pixels_get_theme_builder_post_title() {
	$post_id = pixels_get_theme_builder_post_id();

	if ( $post_id ) {
		return get_the_title( $post_id );
	}

	return esc_html__( 'Sample Post Title', 'pixels-elementor-addons' );
}

/**
 * Get the post permalink for single-template dynamic widgets.
 *
 * @return string
 */
function pixels_get_theme_builder_post_permalink() {
	$post_id = pixels_get_theme_builder_post_id();

	if ( $post_id ) {
		$permalink = get_permalink( $post_id );

		if ( $permalink ) {
			return $permalink;
		}
	}

	return '#';
}