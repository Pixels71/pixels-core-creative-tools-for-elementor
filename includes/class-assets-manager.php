<?php
namespace PixelsCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Central registry for widget frontend and editor assets.
 *
 * Frontend scripts/styles are registered here and enqueued by Elementor only
 * when a widget declares them via get_script_depends() / get_style_depends().
 */
final class Assets_Manager {

	private static ?Assets_Manager $instance = null;

	/**
	 * Widget asset definitions keyed by slug.
	 *
	 * @var array<string, array{
	 *     script?: string,
	 *     style?: string,
	 *     script_deps?: array<int, string>,
	 *     style_deps?: array<int, string>,
	 *     nested?: bool
	 * }>
	 */
	private array $definitions = [];

	/**
	 * Extension asset definitions keyed by slug.
	 *
	 * @var array<string, array{
	 *     script?: string,
	 *     style?: string,
	 *     script_deps?: array<int, string>,
	 *     style_deps?: array<int, string>
	 * }>
	 */
	private array $extension_definitions = [];

	public static function instance(): Assets_Manager {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_vendor_scripts' ], 5 );
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_frontend_scripts' ] );
		add_action( 'elementor/frontend/after_register_styles', [ $this, 'register_frontend_styles' ] );
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueue_editor_assets' ] );
		add_action( 'elementor/preview/enqueue_scripts', [ $this, 'enqueue_preview_extension_assets' ] );
		add_filter( 'script_loader_tag', [ $this, 'add_module_type_to_scripts' ], 10, 3 );
	}

	public function register_vendor_scripts(): void {
		wp_register_script(
			'pixels-number-flow',
			PIXELS_CORE_URL . 'assets/js/vendor/number-flow.js',
			[],
			PIXELS_CORE_VERSION,
			true
		);

		/**
		 * Register additional vendor scripts (Pro: marquee, Matter.js, etc.).
		 */
		do_action( 'pixels_core_register_vendor_scripts' );
	}

	/**
	 * @param string $tag    Script tag HTML.
	 * @param string $handle Script handle.
	 * @param string $src    Script source URL.
	 */
	public function add_module_type_to_scripts( string $tag, string $handle, string $src ): string {
		if ( 'pixels-number-flow' !== $handle ) {
			return $tag;
		}

		return str_replace( '<script ', '<script type="module" ', $tag );
	}

	/**
	 * @param array<string, array{
	 *     script?: string,
	 *     style?: string,
	 *     script_deps?: array<int, string>,
	 *     style_deps?: array<int, string>,
	 *     nested?: bool
	 * }> $definitions
	 */
	public function set_definitions( array $definitions ): void {
		$this->definitions = $definitions;
	}

	/**
	 * @param array<string, array{
	 *     script?: string,
	 *     style?: string,
	 *     script_deps?: array<int, string>,
	 *     style_deps?: array<int, string>
	 * }> $definitions
	 */
	public function set_extension_definitions( array $definitions ): void {
		$this->extension_definitions = $definitions;
	}

	public function get_script_handle( string $slug ): string {
		return 'pixels-core-' . $slug;
	}

	public function get_style_handle( string $slug ): string {
		return 'pixels-core-' . $slug;
	}

	public function get_extension_script_handle( string $slug ): string {
		return 'pixels-core-' . $slug;
	}

	public function get_extension_style_handle( string $slug ): string {
		return 'pixels-core-' . $slug;
	}

	public function register_frontend_scripts(): void {
		$this->register_vendor_scripts();
		$this->ensure_elementor_script_dependencies();
		$this->register_asset_group_scripts( $this->definitions, [ $this, 'get_script_handle' ] );
		$this->register_asset_group_scripts( $this->extension_definitions, [ $this, 'get_extension_script_handle' ] );
	}

	public function register_frontend_styles(): void {
		$this->register_asset_group_styles( $this->definitions, [ $this, 'get_style_handle' ] );
		$this->register_asset_group_styles( $this->extension_definitions, [ $this, 'get_extension_style_handle' ] );
	}

	/**
	 * Bootstrap Elementor frontend script/style handles.
	 *
	 * The editor resets the global scripts queue, so handles like
	 * elementor-frontend and swiper are unavailable unless we register them first.
	 */
	private function ensure_elementor_frontend_assets_registered(): void {
		if ( ! did_action( 'elementor/loaded' ) ) {
			return;
		}

		$frontend = \Elementor\Plugin::$instance->frontend;

		if ( ! wp_script_is( 'elementor-frontend', 'registered' ) ) {
			$frontend->register_scripts();
		}

		if ( ! wp_style_is( 'elementor-frontend', 'registered' ) ) {
			$frontend->register_styles();
		}
	}

	/**
	 * @param array<string, array{
	 *     script?: string,
	 *     style?: string,
	 *     script_deps?: array<int, string>,
	 *     style_deps?: array<int, string>
	 * }> $definitions
	 * @param callable(string): string $handle_callback
	 */
	private function register_asset_group_scripts( array $definitions, callable $handle_callback ): void {
		foreach ( $definitions as $slug => $definition ) {
			if ( empty( $definition['script'] ) ) {
				continue;
			}

			$base_path   = $definition['base_path'] ?? PIXELS_CORE_PATH;
			$base_url    = $definition['base_url'] ?? PIXELS_CORE_URL;
			$script_path = $base_path . $definition['script'];
			$version     = file_exists( $script_path ) ? (string) filemtime( $script_path ) : PIXELS_CORE_VERSION;

			wp_register_script(
				$handle_callback( $slug ),
				$base_url . $definition['script'],
				$definition['script_deps'] ?? [ 'jquery', 'elementor-frontend' ],
				$version,
				true
			);
		}
	}

	private function ensure_elementor_script_dependencies(): void {
		static $is_registering = false;

		if ( wp_script_is( 'elementor-frontend', 'registered' ) && wp_script_is( 'swiper', 'registered' ) ) {
			return;
		}

		if ( $is_registering ) {
			return;
		}

		if ( class_exists( '\Elementor\Plugin' ) ) {
			$elementor = \Elementor\Plugin::instance();

			if ( isset( $elementor->frontend ) && method_exists( $elementor->frontend, 'register_scripts' ) ) {
				$is_registering = true;
				$elementor->frontend->register_scripts();
				$is_registering = false;
			}
		}
	}

	// public function register_frontend_styles(): void {
	// 	foreach ( $this->definitions as $slug => $definition ) {
	// /**
	//  * @param array<string, array{
	//  *     script?: string,
	//  *     style?: string,
	//  *     script_deps?: array<int, string>,
	//  *     style_deps?: array<int, string>
	//  * }> $definitions
	//  * @param callable(string): string $handle_callback
	//  */
	private function register_asset_group_styles( array $definitions, callable $handle_callback ): void {
		foreach ( $definitions as $slug => $definition ) {
			if ( empty( $definition['style'] ) ) {
				continue;
			}

			$base_path  = $definition['base_path'] ?? PIXELS_CORE_PATH;
			$base_url   = $definition['base_url'] ?? PIXELS_CORE_URL;
			$style_path = $base_path . $definition['style'];
			$version    = file_exists( $style_path ) ? (string) filemtime( $style_path ) : PIXELS_CORE_VERSION;

			wp_register_style(
				$handle_callback( $slug ),
				$base_url . $definition['style'],
				$definition['style_deps'] ?? [],
				$version
			);
		}
	}

	public function enqueue_editor_assets(): void {
		wp_enqueue_style(
			'pixels-editor-panel',
			PIXELS_CORE_URL . 'assets/css/editor-panel.css',
			[],
			PIXELS_CORE_VERSION
		);

		$icon_css = PIXELS_CORE_PATH . 'assets/icons/dist/pixels-icons.css';
		if ( file_exists( $icon_css ) ) {
			wp_enqueue_style(
				'pixels-icons',
				PIXELS_CORE_URL . 'assets/icons/dist/pixels-icons.css',
				[],
				PIXELS_CORE_VERSION
			);
		}

		$this->ensure_elementor_frontend_assets_registered();

		$nested_slugs = [];

		foreach ( $this->definitions as $slug => $definition ) {
			if ( ! empty( $definition['style'] ) ) {
				wp_enqueue_style( $this->get_style_handle( $slug ) );
			}

			if ( ! empty( $definition['script'] ) ) {
				wp_enqueue_script( $this->get_script_handle( $slug ) );
			}

			if ( ! empty( $definition['nested'] ) && Plugin::is_nested_elements_active() ) {
				$nested_slugs[] = $slug;
			}
		}

		foreach ( $this->extension_definitions as $slug => $definition ) {
			if ( ! empty( $definition['style'] ) ) {
				wp_enqueue_style( $this->get_extension_style_handle( $slug ) );
			}

			if ( ! empty( $definition['script'] ) ) {
				wp_enqueue_script( $this->get_extension_script_handle( $slug ) );
			}
		}

		if ( empty( $nested_slugs ) ) {
			return;
		}

		wp_enqueue_script(
			'pixels-core-nested-widgets-editor',
			PIXELS_CORE_URL . 'assets/js/editor/nested-widgets.js',
			[ 'nested-elements' ],
			PIXELS_CORE_VERSION,
			true
		);

		wp_localize_script(
			'pixels-core-nested-widgets-editor',
			'pixelsCoreEditor',
			[
				'enabledNestedWidgets' => $nested_slugs,
			]
		);
	}

	/**
	 * Load extension assets inside the Elementor preview iframe so editor toggles work live.
	 */
	public function enqueue_preview_extension_assets(): void {
		if ( empty( $this->extension_definitions ) ) {
			return;
		}

		$this->ensure_elementor_frontend_assets_registered();

		foreach ( $this->extension_definitions as $slug => $definition ) {
			if ( ! empty( $definition['style'] ) ) {
				wp_enqueue_style( $this->get_extension_style_handle( $slug ) );
			}

			if ( ! empty( $definition['script'] ) ) {
				wp_enqueue_script( $this->get_extension_script_handle( $slug ) );
			}
		}
	}
}
