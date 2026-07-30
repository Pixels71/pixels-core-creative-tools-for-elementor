<?php
namespace PixelsElementorAddons;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Single source of truth for Pixels Addons for Elementor (free) widgets + Pro teasers.
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
			'name'        => 'pixels-button',
			'title'       => 'Button (Pixels)',
			'description' => 'Animated button with multiple style variations, icons, and link options.',
			'icon'        => 'eicon-button',
			'assets'      => [
				'script'      => 'assets/js/button.js',
				'style'       => 'assets/css/button.css',
				'script_deps' => [ 'jquery', 'elementor-frontend', 'pixels-core-gsap', 'pixels-core-gsap-split-text' ],
			],
		],
		'tabs'            => [
			'file'            => 'widgets/class-tabs-widget.php',
			'name'            => 'pixels-tabs',
			'title'           => 'Tabs (Pixels)',
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
			'name'            => 'pixels-accordion',
			'title'           => 'Accordion (Pixels)',
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
			'name'            => 'pixels-carousel',
			'title'           => 'Carousel (Pixels)',
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
			'name'        => 'pixels-heading',
			'title'       => 'Heading (Pixels)',
			'description' => 'Customizable heading with optional subheading and link.',
			'icon'        => 'eicon-t-letter',
			'tier'        => 'free',
			'assets'      => [
				'style' => 'assets/css/heading.css',
			],
		],
		'countdown_timer' => [
			'file'        => 'widgets/class-countdown-timer.php',
			'name'        => 'pixels-countdown-timer',
			'title'       => 'Countdown Timer (Pixels)',
			'description' => 'Countdown timer with customizable fields.',
			'icon'        => 'eicon-countdown',
			'tier'        => 'free',
			'assets'      => [
				'script'      => 'assets/js/countdown-timer.js',
				'style'       => 'assets/css/countdown-timer.css',
				'script_deps' => [ 'jquery', 'elementor-frontend', 'number-flow' ],
			],
		],
		'progress_bar'    => [
			'file'        => 'widgets/class-progress-bar-widget.php',
			'name'        => 'pixels-progress-bar',
			'title'       => 'Progress Bar (Pixels)',
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
			'name'        => 'pixels-counter',
			'title'       => 'Counter (Pixels)',
			'description' => 'Animated counter with Number Flow digit transitions.',
			'icon'        => 'eicon-counter',
			'tier'        => 'free',
			'assets'      => [
				'script'      => 'assets/js/counter.js',
				'style'       => 'assets/css/counter.css',
				'script_deps' => [ 'jquery', 'elementor-frontend', 'number-flow' ],
			],
		],
		'rotator_text'    => [
			'file'        => 'widgets/class-rotator-text-widget.php',
			'name'        => 'pixels-rotator-text',
			'title'       => 'Rotator Text (Pixels)',
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
			'name'        => 'pixels-orbit-circle',
			'title'       => 'Orbit Circle (Pixels)',
			'description' => 'Rotating integration logo ring powered by GSAP.',
			'icon'        => 'eicon-integration',
			'tier'        => 'free',
			'assets'      => [
				'script'      => 'assets/js/orbit-circle.js',
				'style'       => 'assets/css/orbit-circle.css',
				'script_deps' => [ 'jquery', 'elementor-frontend', 'pixels-core-gsap' ],
			],
		],
		'menu'            => [
			'file'        => 'widgets/class-menu-widget.php',
			'name'        => 'pixels-menu',
			'title'       => 'Menu (Pixels)',
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
			'name'        => 'pixels-site-logo',
			'title'       => 'Site Logo (Pixels)',
			'description' => 'Display the site identity logo or a custom uploaded logo.',
			'icon'        => 'eicon-site-logo',
			'tier'        => 'free',
			'assets'      => [
				'style' => 'assets/css/site-logo.css',
			],
		],
	];

	/**
	 * Pro-only widgets shown in the dashboard as upsell teasers (no PHP shipped).
	 *
	 * @var array<string, array<string, mixed>>
	 */
	private array $teasers = [
		'form'              => [
			'title'           => 'Form (Pixels)',
			'description'     => 'Contact form with customizable fields and email notifications.',
			'icon'            => 'eicon-form-horizontal',
			'requires_nested' => false,
		],
		'video'             => [
			'title'           => 'Video (Pixels)',
			'description'     => 'Video embed widget for YouTube/Vimeo or self-hosted MP4.',
			'icon'            => 'eicon-video-camera',
			'requires_nested' => false,
		],
		'marquee'           => [
			'title'           => 'Marquee (Pixels)',
			'description'     => 'Infinite marquee with nested Elementor content items.',
			'icon'            => 'eicon-marquee',
			'requires_nested' => true,
		],
		'physics_tag_cloud' => [
			'title'           => 'Physics Tag Cloud (Pixels)',
			'description'     => 'Interactive physics tag cloud powered by Matter.js and GSAP.',
			'icon'            => 'eicon-tags',
			'requires_nested' => false,
		],
		'timeline'          => [
			'title'           => 'Timeline (Pixels)',
			'description'     => 'Vertical timeline with alternating or side-by-side layouts.',
			'icon'            => 'eicon-time-line',
			'requires_nested' => true,
		],
		'stack_card'        => [
			'title'           => 'Stack Cards (Pixels)',
			'description'     => 'Scroll-pinned stacked cards with GSAP and nested containers.',
			'icon'            => 'eicon-posts-grid',
			'requires_nested' => true,
		],
		'expanding_card'    => [
			'title'           => 'Expanding Card (Pixels)',
			'description'     => 'Expanding card with nested Elementor containers.',
			'icon'            => 'eicon-expanding-card',
			'requires_nested' => true,
		],
		'post_title'        => [
			'title'           => 'Post Title (Pixels)',
			'description'     => 'Display the current post title in Single Post templates.',
			'icon'            => 'eicon-post-title',
			'requires_nested' => false,
		],
		'feature_image'     => [
			'title'           => 'Feature Image (Pixels)',
			'description'     => 'Display the current post featured image.',
			'icon'            => 'eicon-featured-image',
			'requires_nested' => false,
		],
		'post_content'      => [
			'title'           => 'Post Content (Pixels)',
			'description'     => 'Display the current post content.',
			'icon'            => 'eicon-post-content',
			'requires_nested' => false,
		],
		'post_meta'         => [
			'title'           => 'Post Meta (Pixels)',
			'description'     => 'Display the current post meta.',
			'icon'            => 'eicon-post-meta',
			'requires_nested' => false,
		],
		'post_author'       => [
			'title'           => 'Post Author (Pixels)',
			'description'     => 'Display the current post author with avatar and bio.',
			'icon'            => 'eicon-person',
			'requires_nested' => false,
		],
		'post_navigation'   => [
			'title'           => 'Post Navigation (Pixels)',
			'description'     => 'Previous and next post links.',
			'icon'            => 'eicon-post-navigation',
			'requires_nested' => false,
		],
		'social_share'      => [
			'title'           => 'Social Share (Pixels)',
			'description'     => 'Social share buttons for the current post.',
			'icon'            => 'eicon-share',
			'requires_nested' => false,
		],
		'post_comment'      => [
			'title'           => 'Post Comments (Pixels)',
			'description'     => 'Post comments and comment form.',
			'icon'            => 'eicon-comments',
			'requires_nested' => false,
		],
		'author_box'        => [
			'title'           => 'Author Box (Pixels)',
			'description'     => 'Author profile box with avatar, bio, and archive button.',
			'icon'            => 'eicon-person',
			'requires_nested' => false,
		],
		'table_of_content'  => [
			'title'           => 'Table of Contents (Pixels)',
			'description'     => 'Auto-generated table of contents from post headings.',
			'icon'            => 'eicon-table-of-contents',
			'requires_nested' => false,
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
		return apply_filters( 'pixels_core_widgets', $widgets );
	}

	/**
	 * Pro teasers for the dashboard (excluded when the widget is already registered).
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public function get_teasers(): array {
		if ( ! defined( 'PIXELS_CORE_SHOW_PRO_UPSSELL' ) || ! PIXELS_CORE_SHOW_PRO_UPSSELL ) {
			return [];
		}

		/**
		 * Filter Pro widget teasers shown in the free dashboard.
		 *
		 * @param array<string, array<string, mixed>> $teasers
		 */
		$teasers = apply_filters( 'pixels_core_widget_teasers', $this->teasers );
		$live    = $this->get_all();

		$translated = [];

		foreach ( array_diff_key( $teasers, $live ) as $slug => $config ) {
			$translated[ $slug ] = $this->translate_teaser_entry( $slug, $config );
		}

		return $translated;
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
	 * @param array<string, mixed> $config
	 * @return array<string, mixed>
	 */
	private function translate_teaser_entry( string $slug, array $config ): array {
		$titles = $this->get_teaser_titles();

		if ( isset( $titles[ $slug ] ) ) {
			$config['title'] = $titles[ $slug ];
		}

		$descriptions = $this->get_teaser_descriptions();

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
			'button'          => __( 'Button (Pixels)', 'pixels-elementor-addons' ),
			'tabs'            => __( 'Tabs (Pixels)', 'pixels-elementor-addons' ),
			'accordion'       => __( 'Accordion (Pixels)', 'pixels-elementor-addons' ),
			'carousel'        => __( 'Carousel (Pixels)', 'pixels-elementor-addons' ),
			'heading'         => __( 'Heading (Pixels)', 'pixels-elementor-addons' ),
			'countdown_timer' => __( 'Countdown Timer (Pixels)', 'pixels-elementor-addons' ),
			'progress_bar'    => __( 'Progress Bar (Pixels)', 'pixels-elementor-addons' ),
			'counter'         => __( 'Counter (Pixels)', 'pixels-elementor-addons' ),
			'rotator_text'    => __( 'Rotator Text (Pixels)', 'pixels-elementor-addons' ),
			'orbit_circle'    => __( 'Orbit Circle (Pixels)', 'pixels-elementor-addons' ),
			'menu'            => __( 'Menu (Pixels)', 'pixels-elementor-addons' ),
			'site_logo'       => __( 'Site Logo (Pixels)', 'pixels-elementor-addons' ),
		];
	}

	/**
	 * @return array<string, string>
	 */
	private function get_widget_descriptions(): array {
		return [
			'button'          => __( 'Animated button with multiple style variations, icons, and link options.', 'pixels-elementor-addons' ),
			'tabs'            => __( 'Tabbed content panels with nested Elementor containers.', 'pixels-elementor-addons' ),
			'accordion'       => __( 'Collapsible accordion items with nested Elementor containers.', 'pixels-elementor-addons' ),
			'carousel'        => __( 'Swiper-powered carousel with nested Elementor slide containers.', 'pixels-elementor-addons' ),
			'heading'         => __( 'Customizable heading with optional subheading and link.', 'pixels-elementor-addons' ),
			'countdown_timer' => __( 'Countdown timer with customizable fields.', 'pixels-elementor-addons' ),
			'progress_bar'    => __( 'Skill and progress bars with horizontal, vertical, circle, and semi-circle styles.', 'pixels-elementor-addons' ),
			'counter'         => __( 'Animated counter with Number Flow digit transitions.', 'pixels-elementor-addons' ),
			'rotator_text'    => __( 'Circular text rotator with single, dual, scroll, ripple, and rounded-square layouts.', 'pixels-elementor-addons' ),
			'orbit_circle'    => __( 'Rotating integration logo ring powered by GSAP.', 'pixels-elementor-addons' ),
			'menu'            => __( 'Responsive WordPress navigation menu with configurable mobile breakpoint.', 'pixels-elementor-addons' ),
			'site_logo'       => __( 'Display the site identity logo or a custom uploaded logo.', 'pixels-elementor-addons' ),
		];
	}

	/**
	 * @return array<string, string>
	 */
	private function get_teaser_titles(): array {
		return [
			'form'              => __( 'Form (Pixels)', 'pixels-elementor-addons' ),
			'video'             => __( 'Video (Pixels)', 'pixels-elementor-addons' ),
			'marquee'           => __( 'Marquee (Pixels)', 'pixels-elementor-addons' ),
			'physics_tag_cloud' => __( 'Physics Tag Cloud (Pixels)', 'pixels-elementor-addons' ),
			'timeline'          => __( 'Timeline (Pixels)', 'pixels-elementor-addons' ),
			'stack_card'        => __( 'Stack Cards (Pixels)', 'pixels-elementor-addons' ),
			'expanding_card'    => __( 'Expanding Card (Pixels)', 'pixels-elementor-addons' ),
			'post_title'        => __( 'Post Title (Pixels)', 'pixels-elementor-addons' ),
			'feature_image'     => __( 'Feature Image (Pixels)', 'pixels-elementor-addons' ),
			'post_content'      => __( 'Post Content (Pixels)', 'pixels-elementor-addons' ),
			'post_meta'         => __( 'Post Meta (Pixels)', 'pixels-elementor-addons' ),
			'post_author'       => __( 'Post Author (Pixels)', 'pixels-elementor-addons' ),
			'post_navigation'   => __( 'Post Navigation (Pixels)', 'pixels-elementor-addons' ),
			'social_share'      => __( 'Social Share (Pixels)', 'pixels-elementor-addons' ),
			'post_comment'      => __( 'Post Comments (Pixels)', 'pixels-elementor-addons' ),
			'author_box'        => __( 'Author Box (Pixels)', 'pixels-elementor-addons' ),
			'table_of_content'  => __( 'Table of Contents (Pixels)', 'pixels-elementor-addons' ),
		];
	}

	/**
	 * @return array<string, string>
	 */
	private function get_teaser_descriptions(): array {
		return [
			'form'              => __( 'Contact form with customizable fields and email notifications.', 'pixels-elementor-addons' ),
			'video'             => __( 'Video embed widget for YouTube/Vimeo or self-hosted MP4.', 'pixels-elementor-addons' ),
			'marquee'           => __( 'Infinite marquee with nested Elementor content items.', 'pixels-elementor-addons' ),
			'physics_tag_cloud' => __( 'Interactive physics tag cloud powered by Matter.js and GSAP.', 'pixels-elementor-addons' ),
			'timeline'          => __( 'Vertical timeline with alternating or side-by-side layouts.', 'pixels-elementor-addons' ),
			'stack_card'        => __( 'Scroll-pinned stacked cards with GSAP and nested containers.', 'pixels-elementor-addons' ),
			'expanding_card'    => __( 'Expanding card with nested Elementor containers.', 'pixels-elementor-addons' ),
			'post_title'        => __( 'Display the current post title in Single Post templates.', 'pixels-elementor-addons' ),
			'feature_image'     => __( 'Display the current post featured image.', 'pixels-elementor-addons' ),
			'post_content'      => __( 'Display the current post content.', 'pixels-elementor-addons' ),
			'post_meta'         => __( 'Display the current post meta.', 'pixels-elementor-addons' ),
			'post_author'       => __( 'Display the current post author with avatar and bio.', 'pixels-elementor-addons' ),
			'post_navigation'   => __( 'Previous and next post links.', 'pixels-elementor-addons' ),
			'social_share'      => __( 'Social share buttons for the current post.', 'pixels-elementor-addons' ),
			'post_comment'      => __( 'Post comments and comment form.', 'pixels-elementor-addons' ),
			'author_box'        => __( 'Author profile box with avatar, bio, and archive button.', 'pixels-elementor-addons' ),
			'table_of_content'  => __( 'Auto-generated table of contents from post headings.', 'pixels-elementor-addons' ),
		];
	}
}
