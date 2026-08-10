<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main pixeccte-theme-builder-archive" role="main">
	<?php pixeccte_render_archive_post(); ?>
</main>

<?php
get_footer();
