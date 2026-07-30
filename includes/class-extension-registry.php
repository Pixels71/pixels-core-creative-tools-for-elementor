<?php
namespace PixelsElementorAddons;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Single source of truth for Pixels Addons for Elementor (free) extensions + Pro teasers.
 */
final class Extension_Registry {

	private static ?Extension_Registry $instance = null;

	/**
	 * @var array<string, array<string, mixed>>
	 */
	private array $extensions = [
		'custom_css' => [
			'file'        => 'extensions/class-custom-css.php',
			'class'       => 'PixelsElementorAddons\\Extensions\\Custom_CSS',
			'title'       => 'Custom CSS',
			'description' => 'Add custom CSS to any Elementor element using the selector keyword in the Advanced tab.',
			'tier'        => 'free',
		],
		'custom_js'  => [
			'file'        => 'extensions/class-custom-js.php',
			'class'       => 'PixelsElementorAddons\\Extensions\\Custom_JS',
			'title'       => 'Custom JS',
			'description' => 'Add custom JavaScript to any Elementor element from the Advanced tab.',
			'tier'        => 'free',
		],
		'live_copy_paste' => [
			'file'        => 'extensions/class-live-copy-paste.php',
			'class'       => 'PixelsElementorAddons\\Extensions\\Live_Copy_Paste',
			'title'       => 'Live Copy Paste',
			'description' => 'Paste Elementor sections, containers, and widgets from the clipboard in the editor.',
			'tier'        => 'free',
		],
	];

	/**
	 * @var array<string, array<string, mixed>>
	 */
	private array $teasers = [
		'animation_effects'       => [
			'title'       => 'GSAP Animations',
			'description' => 'GSAP scroll animations with fade, 3D move, and custom properties.',
		],
		'text_animation_effects'  => [
			'title'       => 'Text Animations',
			'description' => 'GSAP text animations with character, word, reveal, scale, and more.',
		],
		'starter_animations'      => [
			'title'       => 'Starter Animations',
			'description' => 'CSS-based entrance animations for headings, text, images, and containers.',
		],
		'image_animation_effects' => [
			'title'       => 'Image Animations',
			'description' => 'GSAP reveal, scale, and stretch scroll animations for image widgets.',
		],
		'cursor_hover_effect'     => [
			'title'       => 'Cursor Hover Effect',
			'description' => 'Custom cursor label that follows the mouse over a container.',
		],
		'sticky_element'          => [
			'title'       => 'Sticky / Pin Element',
			'description' => 'GSAP ScrollTrigger pin and header sticky for containers and sections.',
		],
	];

	public static function instance(): Extension_Registry {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * @return array<string, array<string, mixed>>
	 */
	public function get_all(): array {
		$extensions = $this->extensions;

		foreach ( $extensions as $slug => $config ) {
			$extensions[ $slug ] = $this->translate_extension_entry( $slug, $config );
		}

		/**
		 * Filter registered (loadable) extensions. Pro adds entries here.
		 *
		 * @param array<string, array<string, mixed>> $extensions
		 */
		return apply_filters( 'pixels_core_extensions', $extensions );
	}

	/**
	 * @return array<string, array<string, mixed>>
	 */
	public function get_teasers(): array {
		if ( ! defined( 'PIXELS_CORE_SHOW_PRO_UPSSELL' ) || ! PIXELS_CORE_SHOW_PRO_UPSSELL ) {
			return [];
		}

		/**
		 * Filter Pro extension teasers shown in the free dashboard.
		 *
		 * @param array<string, array<string, mixed>> $teasers
		 */
		$teasers = apply_filters( 'pixels_core_extension_teasers', $this->teasers );
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

	/**
	 * @param array<string, mixed> $config
	 * @return array<string, mixed>
	 */
	private function translate_extension_entry( string $slug, array $config ): array {
		$titles = [
			'custom_css'      => __( 'Custom CSS', 'pixels-elementor-addons' ),
			'custom_js'       => __( 'Custom JS', 'pixels-elementor-addons' ),
			'live_copy_paste' => __( 'Live Copy Paste', 'pixels-elementor-addons' ),
		];

		$descriptions = [
			'custom_css'      => __( 'Add custom CSS to any Elementor element using the selector keyword in the Advanced tab.', 'pixels-elementor-addons' ),
			'custom_js'       => __( 'Add custom JavaScript to any Elementor element from the Advanced tab.', 'pixels-elementor-addons' ),
			'live_copy_paste' => __( 'Paste Elementor sections, containers, and widgets from the clipboard in the editor.', 'pixels-elementor-addons' ),
		];

		if ( isset( $titles[ $slug ] ) ) {
			$config['title'] = $titles[ $slug ];
		}

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
		$titles = [
			'animation_effects'       => __( 'GSAP Animations', 'pixels-elementor-addons' ),
			'text_animation_effects'  => __( 'Text Animations', 'pixels-elementor-addons' ),
			'starter_animations'      => __( 'Starter Animations', 'pixels-elementor-addons' ),
			'image_animation_effects' => __( 'Image Animations', 'pixels-elementor-addons' ),
			'cursor_hover_effect'     => __( 'Cursor Hover Effect', 'pixels-elementor-addons' ),
			'sticky_element'          => __( 'Sticky / Pin Element', 'pixels-elementor-addons' ),
		];

		$descriptions = [
			'animation_effects'       => __( 'GSAP scroll animations with fade, 3D move, and custom properties.', 'pixels-elementor-addons' ),
			'text_animation_effects'  => __( 'GSAP text animations with character, word, reveal, scale, and more.', 'pixels-elementor-addons' ),
			'starter_animations'      => __( 'CSS-based entrance animations for headings, text, images, and containers.', 'pixels-elementor-addons' ),
			'image_animation_effects' => __( 'GSAP reveal, scale, and stretch scroll animations for image widgets.', 'pixels-elementor-addons' ),
			'cursor_hover_effect'     => __( 'Custom cursor label that follows the mouse over a container.', 'pixels-elementor-addons' ),
			'sticky_element'          => __( 'GSAP ScrollTrigger pin and header sticky for containers and sections.', 'pixels-elementor-addons' ),
		];

		if ( isset( $titles[ $slug ] ) ) {
			$config['title'] = $titles[ $slug ];
		}

		if ( isset( $descriptions[ $slug ] ) ) {
			$config['description'] = $descriptions[ $slug ];
		}

		return $config;
	}
}
