<?php
/**
 * Pixeccte header.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

defined( 'ABSPATH' ) || exit; // Abort, if called directly.
/**
 * Header file in case of the elementor way
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- wp_body_open is a core WordPress hook
do_action( 'wp_body_open' );
?>
<div id="page" class="hfeed site">

<?php do_action( 'pixeccte_hf_header' ); ?>
