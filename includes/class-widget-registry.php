<?php
namespace PixelsCoreCreativeToolsForElementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Single source of truth for Pixels Core Creative Tools for Elementor (free) widgets.
 */
final class Widget_Registry {

	private static ?Widget_Registry $instance = null;

	/**
	 * Free widgets that ship and register with this plugin.
	 *
	 * @var array<string, array<string, mixed>>
	 */
	private array $widgets = [
		'button'    => [
			'file'        => 'widgets/class-button-widget.php',
			'name'        => 'pixeccte-button',
			'title'       => 'Button',
			'description' => 'Animated button with multiple style variations, icons, and link options.',
			'icon'        => 'eicon-button',
			'assets'      => [
				'script'      => 'assets/js/button.js',
				'style'       => 'assets/css/button.css',
				'script_deps' => [ 'jquery', 'elementor-frontend' ],
			],
		],
		'tabs'            => [
			'file'            => 'widgets/class-tabs-widget.php',
			'name'            => 'pixeccte-tabs',
			'title'           => 'Tabs',
			'description'     => 'Tabbed content panels with nested Elementor containers.',
			'icon'            => 'eicon-tabs',
			'tier'            => 'free',
			'requires_nested' => true,
			'assets'          => [
				'script' => 'assets/js/tabs.js',
				'style'  => 'assets/css/tabs.css',
				'nested' => true,
			],
		],
		'accordion'       => [
			'file'            => 'widgets/class-accordion-widget.php',
			'name'            => 'pixeccte-accordion',
			'title'           => 'Accordion',
			'description'     => 'Collapsible accordion items with nested Elementor containers.',
			'icon'            => 'eicon-accordion',
			'tier'            => 'free',
			'requires_nested' => true,
			'assets'          => [
				'script' => 'assets/js/accordion.js',
				'style'  => 'assets/css/accordion.css',
				'nested' => true,
			],
		],
		'carousel'        => [
			'file'            => 'widgets/class-carousel-widget.php',
			'name'            => 'pixeccte-carousel',
			'title'           => 'Carousel',
			'description'     => 'Swiper-powered carousel with nested Elementor slide containers.',
			'icon'            => 'eicon-nested-carousel',
			'tier'            => 'free',
			'requires_nested' => true,
			'assets'          => [
				'script'      => 'assets/js/carousel.js',
				'style'       => 'assets/css/carousel.css',
				'script_deps' => [ 'jquery', 'elementor-frontend', 'swiper' ],
				'style_deps'  => [ 'e-swiper', 'swiper' ],
				'nested'      => true,
			],
		],
		'heading'         => [
			'file'        => 'widgets/class-heading-widget.php',
			'name'        => 'pixeccte-heading',
			'title'       => 'Heading',
			'description' => 'Customizable heading with optional subheading and link.',
			'icon'        => 'eicon-t-letter',
			'tier'        => 'free',
			'assets'      => [
				'style' => 'assets/css/heading.css',
			],
		],
		'countdown_timer' => [
			'file'        => 'widgets/class-countdown-timer.php',
			'name'        => 'pixeccte-countdown-timer',
			'title'       => 'Countdown Timer',
			'description' => 'Countdown timer with customizable fields.',
			'icon'        => 'eicon-countdown',
			'tier'        => 'free',
			'assets'      => [
				'script'      => 'assets/js/countdown-timer.js',
				'style'       => 'assets/css/countdown-timer.css',
				'script_deps' => [ 'jquery', 'elementor-frontend', 'pixeccte-number-flow' ],
			],
		],
		'progress_bar'    => [
			'file'        => 'widgets/class-progress-bar-widget.php',
			'name'        => 'pixeccte-progress-bar',
			'title'       => 'Progress Bar',
			'description' => 'Skill and progress bars with horizontal, vertical, circle, and semi-circle styles.',
			'icon'        => 'eicon-skill-bar',
			'tier'        => 'free',
			'assets'      => [
				'script' => 'assets/js/progress-bar.js',
				'style'  => 'assets/css/progress-bar.css',
			],
		],
		'counter'         => [
			'file'        => 'widgets/class-counter-widget.php',
			'name'        => 'pixeccte-counter',
			'title'       => 'Counter',
			'description' => 'Animated counter with Number Flow digit transitions.',
			'icon'        => 'eicon-counter',
			'tier'        => 'free',
			'assets'      => [
				'script'      => 'assets/js/counter.js',
				'style'       => 'assets/css/counter.css',
				'script_deps' => [ 'jquery', 'elementor-frontend', 'pixeccte-number-flow' ],
			],
		],
		'rotator_text'    => [
			'file'        => 'widgets/class-rotator-text-widget.php',
			'name'        => 'pixeccte-rotator-text',
			'title'       => 'Rotator Text',
			'description' => 'Circular text rotator with single, dual, scroll, ripple, and rounded-square layouts.',
			'icon'        => 'eicon-circle-o',
			'tier'        => 'free',
			'assets'      => [
				'script' => 'assets/js/rotator-text.js',
				'style'  => 'assets/css/rotator-text.css',
			],
		],
		'orbit_circle'    => [
			'file'        => 'widgets/class-orbit-circle-widget.php',
			'name'        => 'pixeccte-orbit-circle',
			'title'       => 'Orbit Circle',
			'description' => 'Rotating integration logo ring with CSS animation.',
			'icon'        => 'eicon-integration',
			'tier'        => 'free',
			'assets'      => [
				'script'      => 'assets/js/orbit-circle.js',
				'style'       => 'assets/css/orbit-circle.css',
				'script_deps' => [ 'jquery', 'elementor-frontend' ],
			],
		],
		'menu'            => [
			'file'        => 'widgets/class-menu-widget.php',
			'name'        => 'pixeccte-menu',
			'title'       => 'Menu',
			'description' => 'Responsive WordPress navigation menu with configurable mobile breakpoint.',
			'icon'        => 'eicon-nav-menu',
			'tier'        => 'free',
			'assets'      => [
				'script' => 'assets/js/menu.js',
				'style'  => 'assets/css/menu.css',
			],
		],
		'site_logo'       => [
			'file'        => 'widgets/class-site-logo-widget.php',
			'name'        => 'pixeccte-site-logo',
			'title'       => 'Site Logo',
			'description' => 'Display the site identity logo or a custom uploaded logo.',
			'icon'        => 'eicon-site-logo',
			'tier'        => 'free',
			'assets'      => [
				'style' => 'assets/css/site-logo.css',
			],
		],
	];

	public static function instance(): Widget_Registry {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
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
	 * @return array<int, string>
	 */
	public function get_slugs(): array {
		return array_keys( $this->get_all() );
	}

	public function get( string $slug ): ?array {
		$all = $this->get_all();

		return $all[ $slug ] ?? null;
	}

	public function exists( string $slug ): bool {
		return null !== $this->get( $slug );
	}

	/**
	 * @param array<int, string> $slugs
	 * @return array<string, array<string, mixed>>
	 */
	public function get_asset_definitions( array $slugs ): array {
		$definitions = [];

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

	public function get_widget_class( string $slug ): string {
		$config = $this->get( $slug );

		if ( ! empty( $config['class'] ) && is_string( $config['class'] ) ) {
			return $config['class'];
		}

		if ( 'rotator_text' === $slug ) {
			return __NAMESPACE__ . '\\Widgets\\RotatorTextWidget';
		}

		if ( 'menu' === $slug ) {
			return __NAMESPACE__ . '\\Widgets\\Nav_Menu_Widget';
		}

		$parts = preg_split( '/[-_]/', $slug );
		$parts = array_map( 'ucfirst', $parts );

		return __NAMESPACE__ . '\\Widgets\\' . implode( '_', $parts ) . '_Widget';
	}

	/**
	 * @param array<string, mixed> $config
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
	 * @return array<string, string>
	 */
	private function get_widget_titles(): array {
		return [
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
		];
	}

	/**
	 * @return array<string, string>
	 */
	private function get_widget_descriptions(): array {
		return [
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
		];
	}
}
