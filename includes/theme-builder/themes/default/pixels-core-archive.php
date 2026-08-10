<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main pixels-core-theme-builder-archive" role="main">
	<?php pixels_core_render_archive_post(); ?>
</main>

<?php
get_footer();
