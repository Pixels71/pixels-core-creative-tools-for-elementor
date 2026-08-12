<?php
/**
 * Widgets loader.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor;

use Elementor\Widgets_Manager;
use PixelsCoreCreativeToolsForElementor\Admin\Widget_Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widgets loader.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */
final class Widgets_Loader {

	/**
	 * Instance.
	 *
	 * @var mixed
	 */
	private static ?Widgets_Loader $instance = null;

	/**
	 * Instance.
	 *
	 * @return Widgets_Loader Result.
	 */
	public static function instance(): Widgets_Loader {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Construct.
	 */
	private function __construct() {
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );

		require_once PIXECCTE_PATH . 'includes/class-assets-manager.php';

		$active_slugs = Widget_Settings::instance()->get_active_slugs();
		$definitions  = Widget_Registry::instance()->get_asset_definitions( $active_slugs );

		Assets_Manager::instance()->set_definitions( $definitions );
	}

	/**
	 * Register category.
	 *
	 * @param mixed $elements_manager Elements manager.
	 */
	public function register_category( $elements_manager ): void {
		if ( empty( Widget_Settings::instance()->get_active_slugs() ) ) {
			return;
		}

		$category = array(
			'pixeccte' => array(
				'title' => esc_html__( 'Pixels Core Creative Tools', 'pixels-core-creative-tools-for-elementor' ),
				'icon'  => 'eicon-plug',
			),
		);

		$existing = $elements_manager->get_categories();
		unset( $existing['pixeccte'] );

		$categories = array_merge( $category, $existing );

		$set_categories = function ( $categories ) {
			$this->categories = $categories;
		};
		$set_categories->call( $elements_manager, $categories );
	}

	/**
	 * Register widgets.
	 *
	 * @param Widgets_Manager $widgets_manager Widgets manager.
	 */
	public function register_widgets( Widgets_Manager $widgets_manager ): void {
		require_once PIXECCTE_PATH . 'widgets/trait-widget-assets.php';

		$registry = Widget_Registry::instance();
		$active   = Widget_Settings::instance()->get_active_slugs();

		foreach ( $active as $slug ) {
			$config = $registry->get( $slug );

			if ( null === $config || empty( $config['file'] ) ) {
				continue;
			}

			$base = $config['base_path'] ?? PIXECCTE_PATH;
			$path = $base . $config['file'];

			if ( ! file_exists( $path ) ) {
				continue;
			}

			require_once $path;

			$class = $registry->get_widget_class( $slug );

			if ( class_exists( $class ) ) {
				$widgets_manager->register( new $class() );
			}
		}
	}
}
