<?php
use Elementor\Utils;
use PixelsElementorAddons\Theme_Builder\Theme_Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

\Elementor\Plugin::$instance->frontend->add_body_class( 'elementor-template-canvas' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<?php if ( ! current_theme_supports( 'title-tag' ) ) : ?>
		<title><?php echo wp_get_document_title(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></title>
	<?php endif; ?>
	<?php wp_head(); ?>
	<?php Utils::print_unescaped_internal_string( Utils::get_meta_viewport( 'canvas' ) ); ?>
</head>
<body <?php body_class(); ?>>
	<?php
	wp_body_open();

	do_action( 'elementor/page_templates/canvas/before_content' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Elementor core hook.

	Theme_Elementor::instance()->render_popup_editor_template();

	do_action( 'elementor/page_templates/canvas/after_content' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Elementor core hook.

	wp_footer();
	?>
</body>
</html>
