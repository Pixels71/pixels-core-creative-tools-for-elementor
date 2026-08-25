<?php
/**
 * Assets manager.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor;

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

	/**
	 * Instance.
	 *
	 * @var mixed
	 */
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
	private array $definitions = array();

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
	private array $extension_definitions = array();

	/**
	 * Instance.
	 *
	 * @return Assets_Manager Result.
	 */
	public static function instance(): Assets_Manager {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Construct.
	 */
	private function __construct() {
		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_vendor_scripts' ), 5 );
		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_frontend_scripts' ) );
		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'add_preview_hooks_guard' ), 1 );
		add_action( 'elementor/frontend/after_register_styles', array( $this, 'register_frontend_styles' ) );
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'enqueue_editor_assets' ) );
		add_action( 'elementor/preview/enqueue_scripts', array( $this, 'add_preview_hooks_guard' ), 0 );
		add_action( 'elementor/preview/enqueue_scripts', array( $this, 'enqueue_preview_extension_assets' ) );
		add_filter( 'script_loader_tag', array( $this, 'add_module_type_to_scripts' ), 10, 3 );
	}

	/**
	 * Register vendor scripts.
	 */
	public function register_vendor_scripts(): void {
		wp_register_script(
			'pixeccte-number-flow',
			PIXECCTE_URL . 'assets/js/vendor/number-flow.js',
			array(),
			PIXECCTE_VERSION,
			true
		);

		/**
		 * Register additional vendor scripts (Pro: marquee, Matter.js, etc.).
		 */
		do_action( 'pixeccte_register_vendor_scripts' );
	}

	/**
	 * Add module type to scripts.
	 *
	 * @param string $tag    Script tag HTML.
	 * @param string $handle Script handle.
	 * @param string $src    Script source URL.
	 */
	public function add_module_type_to_scripts( string $tag, string $handle, string $src ): string { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- script_loader_tag filter signature.
		unset( $src );
		if ( 'pixeccte-number-flow' !== $handle ) {
			return $tag;
		}

		return str_replace( '<script ', '<script type="module" ', $tag );
	}

	/**
	 * Set widget asset definitions.
	 *
	 * @param array $definitions Definitions.
	 */
	public function set_definitions( array $definitions ): void {
		$this->definitions = $definitions;
	}

	/**
	 * Set extension definitions.
	 *
	 * @param array $definitions Definitions.
	 */
	public function set_extension_definitions( array $definitions ): void {
		$this->extension_definitions = $definitions;
	}

	/**
	 * Get script handle.
	 *
	 * @param string $slug Slug.
	 * @return string Result.
	 */
	public function get_script_handle( string $slug ): string {
		return 'pixeccte-' . $slug;
	}

	/**
	 * Get style handle.
	 *
	 * @param string $slug Slug.
	 * @return string Result.
	 */
	public function get_style_handle( string $slug ): string {
		return 'pixeccte-' . $slug;
	}

	/**
	 * Get extension script handle.
	 *
	 * @param string $slug Slug.
	 * @return string Result.
	 */
	public function get_extension_script_handle( string $slug ): string {
		return 'pixeccte-' . $slug;
	}

	/**
	 * Get extension style handle.
	 *
	 * @param string $slug Slug.
	 * @return string Result.
	 */
	public function get_extension_style_handle( string $slug ): string {
		return 'pixeccte-' . $slug;
	}

	/**
	 * Register frontend scripts.
	 */
	public function register_frontend_scripts(): void {
		$this->register_vendor_scripts();
		$this->ensure_elementor_script_dependencies();
		$this->register_asset_group_scripts( $this->definitions, array( $this, 'get_script_handle' ) );
		$this->register_asset_group_scripts( $this->extension_definitions, array( $this, 'get_extension_script_handle' ) );
	}

	/**
	 * Register frontend styles.
	 */
	public function register_frontend_styles(): void {
		$this->register_asset_group_styles( $this->definitions, array( $this, 'get_style_handle' ) );
		$this->register_asset_group_styles( $this->extension_definitions, array( $this, 'get_extension_style_handle' ) );
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
	 * Register asset group scripts.
	 *
	 * @param array    $definitions Definitions.
	 * @param callable $handle_callback Handle callback.
	 */
	private function register_asset_group_scripts( array $definitions, callable $handle_callback ): void {
		foreach ( $definitions as $slug => $definition ) {
			if ( empty( $definition['script'] ) ) {
				continue;
			}

			$base_path   = $definition['base_path'] ?? PIXECCTE_PATH;
			$base_url    = $definition['base_url'] ?? PIXECCTE_URL;
			$script_path = $base_path . $definition['script'];
			$version     = file_exists( $script_path ) ? (string) filemtime( $script_path ) : PIXECCTE_VERSION;

			wp_register_script(
				$handle_callback( $slug ),
				$base_url . $definition['script'],
				$definition['script_deps'] ?? array( 'jquery', 'elementor-frontend' ),
				$version,
				true
			);
		}
	}

	/**
	 * Ensure elementor script dependencies.
	 */
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

	/**
	 * Register style handles for an asset definition group.
	 *
	 * @param array    $definitions Definitions.
	 * @param callable $handle_callback Handle callback.
	 */
	private function register_asset_group_styles( array $definitions, callable $handle_callback ): void {
		foreach ( $definitions as $slug => $definition ) {
			if ( empty( $definition['style'] ) ) {
				continue;
			}

			$base_path  = $definition['base_path'] ?? PIXECCTE_PATH;
			$base_url   = $definition['base_url'] ?? PIXECCTE_URL;
			$style_path = $base_path . $definition['style'];
			$version    = file_exists( $style_path ) ? (string) filemtime( $style_path ) : PIXECCTE_VERSION;

			wp_register_style(
				$handle_callback( $slug ),
				$base_url . $definition['style'],
				$definition['style_deps'] ?? array(),
				$version
			);
		}
	}

	/**
	 * Enqueue editor-panel assets only.
	 *
	 * Frontend widget/extension scripts must not load in the editor parent.
	 * They depend on elementor-frontend (and Pro vendors like GSAP), which
	 * collide with the editor webpack runtime and break the canvas. Those
	 * scripts belong in the preview iframe via get_script_depends() and
	 * enqueue_preview_extension_assets().
	 */
	public function enqueue_editor_assets(): void {
		wp_enqueue_style(
			'pixeccte-editor-panel',
			PIXECCTE_URL . 'assets/css/editor-panel.css',
			array(),
			PIXECCTE_VERSION
		);

		$icon_css = PIXECCTE_PATH . 'assets/icons/dist/pixeccte-icons.css';
		if ( file_exists( $icon_css ) ) {
			wp_enqueue_style(
				'pixeccte-icons',
				PIXECCTE_URL . 'assets/icons/dist/pixeccte-icons.css',
				array(),
				PIXECCTE_VERSION
			);
		}

		$nested_slugs = array();

		foreach ( $this->definitions as $slug => $definition ) {
			if ( ! empty( $definition['nested'] ) && Plugin::is_nested_elements_active() ) {
				$nested_slugs[] = $slug;
			}
		}

		if ( empty( $nested_slugs ) ) {
			return;
		}

		wp_enqueue_script(
			'pixeccte-nested-widgets-editor',
			PIXECCTE_URL . 'assets/js/editor/nested-widgets.js',
			array( 'nested-elements' ),
			PIXECCTE_VERSION,
			true
		);

		wp_localize_script(
			'pixeccte-nested-widgets-editor',
			'pixeccteEditor',
			array(
				'enabledNestedWidgets' => $nested_slugs,
			)
		);
	}

	/**
	 * Isolate Elementor frontend hook callbacks so one throwing extension cannot skip widget handlers.
	 *
	 * Elementor's runReadyTrigger() calls doAction() for global, then widget type, with no try/catch.
	 * Pro extensions hook frontend/element_ready/global and .../widget; a throw there leaves every
	 * widget inert in the editor canvas while the published frontend still works.
	 */
	public function add_preview_hooks_guard(): void {
		static $added = false;

		if ( $added || ! wp_script_is( 'elementor-frontend', 'registered' ) ) {
			return;
		}

		$added = true;

		wp_add_inline_script(
			'elementor-frontend',
			'(function(){function patch(){if(!window.elementorFrontend||!elementorFrontend.hooks||elementorFrontend.hooks._pixeccteGuarded){return;}var hooks=elementorFrontend.hooks;var original=hooks.doAction.bind(hooks);hooks.doAction=function(action){try{return original.apply(null,arguments);}catch(error){if(window.console&&console.error){console.error("[Pixels] Elementor hook failed:",action,error);}}};hooks._pixeccteGuarded=true;}patch();window.addEventListener("elementor/frontend/init",patch);})();',
			'after'
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
		$this->register_vendor_scripts();
		$this->register_frontend_scripts();
		$this->register_frontend_styles();

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
