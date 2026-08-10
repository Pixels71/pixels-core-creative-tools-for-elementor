<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main pixels-core-theme-builder-single" role="main">
	<?php
	while ( have_posts() ) :
		the_post();

		echo wp_kses(
			\Elementor\Plugin::instance()->frontend->get_builder_content_for_display( pixels_core_get_single_post_id() ),
			pixels_core_get_builder_allowed_html()
		);
	endwhile;
	?>
</main>

<?php
get_footer();
