<?php
/**
 * Pixeccte single.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

defined( 'ABSPATH' ) || exit; // Abort, if called directly.

/**
 * Single template file for Elementor Single Post builder.
 *
 * Used when a `type_single_post` template is active for the current singular
 * request via the `maybe_use_single_post_template` filter.
 *
 * Header and footer are delegated to the active theme, so:
 * - If Header/Footer Builder is enabled, the theme will render the Elementor
 *   header/footer.
 * - Otherwise, the native theme header and footer are used.
 */

get_header();
?>

<main id="primary" class="site-main" role="main">
	<?php
	while ( have_posts() ) :
		the_post();

		echo wp_kses(
			\Elementor\Plugin::instance()->frontend->get_builder_content_for_display(
				pixeccte_get_single_post_id()
			),
			pixeccte_get_builder_allowed_html()
		);

	endwhile;
	?>
</main><!-- #primary -->

<?php
get_footer();

