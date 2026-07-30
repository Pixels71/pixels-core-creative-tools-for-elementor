<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main pixels-theme-builder-single" role="main">
	<?php
	while ( have_posts() ) :
		the_post();

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor builder content.
		echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( pixels_get_single_post_id() );
	endwhile;
	?>
</main>

<?php
get_footer();
