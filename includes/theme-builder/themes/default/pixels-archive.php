<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main pixels-theme-builder-archive" role="main">
	<?php pixels_render_archive_post(); ?>
</main>

<?php
get_footer();
