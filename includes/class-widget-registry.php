<?php
/**
 * Widget registry.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Single source of truth for Pixels Core Creative Tools for Elementor (free) widgets.
 */
final class Widget_Registry {

	/**
	 * Instance.
	 *
	 * @var mixed
	 */
	private static ?Widget_Registry $instance = null;

	/**
	 * Free widgets that ship and register with this plugin.
	 *
	 * @var array<string, array<string, mixed>>
	 */
	private array $widgets = array(
		'button'          => array(
			'file'        => 'widgets/class-button-widget.php',
			'name'        => 'pixeccte-button',
			'title'       => 'Button',
			'description' => 'Animated button with multiple style variations, icons, and link options.',
			'icon'        => 'eicon-button',
			'assets'      => array(
				'script'      => 'assets/js/button.js',
				'style'       => 'assets/css/button.css',
				'script_deps' => array( 'jquery', 'elementor-frontend' ),
			),
		),
		'tabs'            => array(
			'file'            => 'widgets/class-tabs-widget.php',
			'name'            => 'pixeccte-tabs',
			'title'           => 'Tabs',
			'description'     => 'Tabbed content panels with nested Elementor containers.',
			'icon'            => 'eicon-tabs',
			'tier'            => 'free',
			'requires_nested' => true,
			'assets'          => array(
				'script' => 'assets/js/tabs.js',
				'style'  => 'assets/css/tabs.css',
				'nested' => true,
			),
		),
		'accordion'       => array(
			'file'            => 'widgets/class-accordion-widget.php',
			'name'            => 'pixeccte-accordion',
			'title'           => 'Accordion',
			'description'     => 'Collapsible accordion items with nested Elementor containers.',
			'icon'            => 'eicon-accordion',
			'tier'            => 'free',
			'requires_nested' => true,
			'assets'          => array(
				'script' => 'assets/js/accordion.js',
				'style'  => 'assets/css/accordion.css',
				'nested' => true,
			),
		),
		'carousel'        => array(
			'file'            => 'widgets/class-carousel-widget.php',
			'name'            => 'pixeccte-carousel',
			'title'           => 'Carousel',
			'description'     => 'Swiper-powered carousel with nested Elementor slide containers.',
			'icon'            => 'eicon-nested-carousel',
			'tier'            => 'free',
			'requires_nested' => true,
			'assets'          => array(
				'script'      => 'assets/js/carousel.js',
				'style'       => 'assets/css/carousel.css',
				'script_deps' => array( 'jquery', 'elementor-frontend', 'swiper' ),
				'style_deps'  => array( 'e-swiper', 'swiper' ),
				'nested'      => true,
			),
		),
		'heading'         => array(
			'file'        => 'widgets/class-heading-widget.php',
			'name'        => 'pixeccte-heading',
			'title'       => 'Heading',
			'description' => 'Customizable heading with optional subheading and link.',
			'icon'        => 'eicon-t-letter',
			'tier'        => 'free',
			'assets'      => array(
				'style' => 'assets/css/heading.css',
			),
		),
		'countdown_timer' => array(
			'file'        => 'widgets/class-countdown-timer-widget.php',
			'name'        => 'pixeccte-countdown-timer',
			'title'       => 'Countdown Timer',
			'description' => 'Countdown timer with customizable fields.',
			'icon'        => 'eicon-countdown',
			'tier'        => 'free',
			'assets'      => array(
				'script'      => 'assets/js/countdown-timer.js',
				'style'       => 'assets/css/countdown-timer.css',
				'script_deps' => array( 'jquery', 'elementor-frontend', 'pixeccte-number-flow' ),
			),
		),
		'progress_bar'    => array(
			'file'        => 'widgets/class-progress-bar-widget.php',
			'name'        => 'pixeccte-progress-bar',
			'title'       => 'Progress Bar',
			'description' => 'Skill and progress bars with horizontal, vertical, circle, and semi-circle styles.',
			'icon'        => 'eicon-skill-bar',
			'tier'        => 'free',
			'assets'      => array(
				'script' => 'assets/js/progress-bar.js',
				'style'  => 'assets/css/progress-bar.css',
			),
		),
		'counter'         => array(
			'file'        => 'widgets/class-counter-widget.php',
			'name'        => 'pixeccte-counter',
			'title'       => 'Counter',
			'description' => 'Animated counter with Number Flow digit transitions.',
			'icon'        => 'eicon-counter',
			'tier'        => 'free',
			'assets'      => array(
				'script'      => 'assets/js/counter.js',
				'style'       => 'assets/css/counter.css',
				'script_deps' => array( 'jquery', 'elementor-frontend', 'pixeccte-number-flow' ),
			),
		),
		'rotator_text'    => array(
			'file'        => 'widgets/class-rotator-text-widget.php',
			'name'        => 'pixeccte-rotator-text',
			'title'       => 'Rotator Text',
			'description' => 'Circular text rotator with single, dual, scroll, ripple, and rounded-square layouts.',
			'icon'        => 'eicon-circle-o',
			'tier'        => 'free',
			'assets'      => array(
				'script' => 'assets/js/rotator-text.js',
				'style'  => 'assets/css/rotator-text.css',
			),
		),
		'orbit_circle'    => array(
			'file'        => 'widgets/class-orbit-circle-widget.php',
			'name'        => 'pixeccte-orbit-circle',
			'title'       => 'Orbit Circle',
			'description' => 'Rotating integration logo ring with CSS animation.',
			'icon'        => 'eicon-integration',
			'tier'        => 'free',
			'assets'      => array(
				'script'      => 'assets/js/orbit-circle.js',
				'style'       => 'assets/css/orbit-circle.css',
				'script_deps' => array( 'jquery', 'elementor-frontend' ),
			),
		),
		'menu'            => array(
			'file'        => 'widgets/class-nav-menu-widget.php',
			'name'        => 'pixeccte-menu',
			'title'       => 'Menu',
			'description' => 'Responsive WordPress navigation menu with configurable mobile breakpoint.',
			'icon'        => 'eicon-nav-menu',
			'tier'        => 'free',
			'assets'      => array(
				'script' => 'assets/js/menu.js',
				'style'  => 'assets/css/menu.css',
			),
		),
		'site_logo'       => array(
			'file'        => 'widgets/class-site-logo-widget.php',
			'name'        => 'pixeccte-site-logo',
			'title'       => 'Site Logo',
			'description' => 'Display the site identity logo or a custom uploaded logo.',
			'icon'        => 'eicon-site-logo',
			'tier'        => 'free',
			'assets'      => array(
				'style' => 'assets/css/site-logo.css',
			),
		),
	);

	/**
	 * Instance.
	 *
	 * @return Widget_Registry Result.
	 */
	public static function instance(): Widget_Registry {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get all.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public function get_all(): array {
		$widgets = $this->widgets;

		foreach ( $widgets as $slug => $config ) {
			$widgets[ $slug ] = $this->translate_widget_entry( $slug, $config );
		}

		/**
		 * Filter registered (loadable) widgets. Pro adds entries here.
		 *
		 * @param array<string, array<string, mixed>> $widgets
		 */
		return apply_filters( 'pixeccte_widgets', $widgets );
	}

	/**
	 * Get slugs.
	 *
	 * @return array<int, string>
	 */
	public function get_slugs(): array {
		return array_keys( $this->get_all() );
	}

	/**
	 * Get.
	 *
	 * @param string $slug Slug.
	 * @return array Result.
	 */
	public function get( string $slug ): ?array {
		$all = $this->get_all();

		return $all[ $slug ] ?? null;
	}

	/**
	 * Exists.
	 *
	 * @param string $slug Slug.
	 * @return bool Result.
	 */
	public function exists( string $slug ): bool {
		return null !== $this->get( $slug );
	}

	/**
	 * Get asset definitions.
	 *
	 * @param array $slugs Slugs.
	 * @return array<string, array<string, mixed>>
	 */
	public function get_asset_definitions( array $slugs ): array {
		$definitions = array();

		foreach ( $slugs as $slug ) {
			$config = $this->get( $slug );

			if ( empty( $config['assets'] ) ) {
				continue;
			}

			$assets = $config['assets'];

			if ( ! empty( $config['base_path'] ) ) {
				$assets['base_path'] = $config['base_path'];
			}

			if ( ! empty( $config['base_url'] ) ) {
				$assets['base_url'] = $config['base_url'];
			}

			$definitions[ $slug ] = $assets;
		}

		return $definitions;
	}

	/**
	 * Get widget class.
	 *
	 * @param string $slug Slug.
	 * @return string Result.
	 */
	public function get_widget_class( string $slug ): string {
		$config = $this->get( $slug );

		if ( ! empty( $config['class'] ) && is_string( $config['class'] ) ) {
			return $config['class'];
		}

		if ( 'rotator_text' === $slug ) {
			return __NAMESPACE__ . '\\Widgets\\Rotator_Text_Widget';
		}

		if ( 'menu' === $slug ) {
			return __NAMESPACE__ . '\\Widgets\\Nav_Menu_Widget';
		}

		$parts = preg_split( '/[-_]/', $slug );
		$parts = array_map( 'ucfirst', $parts );

		return __NAMESPACE__ . '\\Widgets\\' . implode( '_', $parts ) . '_Widget';
	}

	/**
	 * Translate widget entry.
	 *
	 * @param string $slug Slug.
	 * @param array  $config Config.
	 * @return array<string, mixed>
	 */
	private function translate_widget_entry( string $slug, array $config ): array {
		$titles = $this->get_widget_titles();

		if ( isset( $titles[ $slug ] ) ) {
			$config['title'] = $titles[ $slug ];
		}

		$descriptions = $this->get_widget_descriptions();

		if ( isset( $descriptions[ $slug ] ) ) {
			$config['description'] = $descriptions[ $slug ];
		}

		return $config;
	}

	/**
	 * Get widget titles.
	 *
	 * @return array<string, string>
	 */
	private function get_widget_titles(): array {
		return array(
			'button'          => __( 'Button', 'pixels-core-creative-tools-for-elementor' ),
			'tabs'            => __( 'Tabs', 'pixels-core-creative-tools-for-elementor' ),
			'accordion'       => __( 'Accordion', 'pixels-core-creative-tools-for-elementor' ),
			'carousel'        => __( 'Carousel', 'pixels-core-creative-tools-for-elementor' ),
			'heading'         => __( 'Heading', 'pixels-core-creative-tools-for-elementor' ),
			'countdown_timer' => __( 'Countdown Timer', 'pixels-core-creative-tools-for-elementor' ),
			'progress_bar'    => __( 'Progress Bar', 'pixels-core-creative-tools-for-elementor' ),
			'counter'         => __( 'Counter', 'pixels-core-creative-tools-for-elementor' ),
			'rotator_text'    => __( 'Rotator Text', 'pixels-core-creative-tools-for-elementor' ),
			'orbit_circle'    => __( 'Orbit Circle', 'pixels-core-creative-tools-for-elementor' ),
			'menu'            => __( 'Menu', 'pixels-core-creative-tools-for-elementor' ),
			'site_logo'       => __( 'Site Logo', 'pixels-core-creative-tools-for-elementor' ),
		);
	}

	/**
	 * Get widget descriptions.
	 *
	 * @return array<string, string>
	 */
	private function get_widget_descriptions(): array {
		return array(
			'button'          => __( 'Animated button with multiple style variations, icons, and link options.', 'pixels-core-creative-tools-for-elementor' ),
			'tabs'            => __( 'Tabbed content panels with nested Elementor containers.', 'pixels-core-creative-tools-for-elementor' ),
			'accordion'       => __( 'Collapsible accordion items with nested Elementor containers.', 'pixels-core-creative-tools-for-elementor' ),
			'carousel'        => __( 'Swiper-powered carousel with nested Elementor slide containers.', 'pixels-core-creative-tools-for-elementor' ),
			'heading'         => __( 'Customizable heading with optional subheading and link.', 'pixels-core-creative-tools-for-elementor' ),
			'countdown_timer' => __( 'Countdown timer with customizable fields.', 'pixels-core-creative-tools-for-elementor' ),
			'progress_bar'    => __( 'Skill and progress bars with horizontal, vertical, circle, and semi-circle styles.', 'pixels-core-creative-tools-for-elementor' ),
			'counter'         => __( 'Animated counter with Number Flow digit transitions.', 'pixels-core-creative-tools-for-elementor' ),
			'rotator_text'    => __( 'Circular text rotator with single, dual, scroll, ripple, and rounded-square layouts.', 'pixels-core-creative-tools-for-elementor' ),
			'orbit_circle'    => __( 'Rotating integration logo ring with CSS animation.', 'pixels-core-creative-tools-for-elementor' ),
			'menu'            => __( 'Responsive WordPress navigation menu with configurable mobile breakpoint.', 'pixels-core-creative-tools-for-elementor' ),
			'site_logo'       => __( 'Display the site identity logo or a custom uploaded logo.', 'pixels-core-creative-tools-for-elementor' ),
		);
	}
}
