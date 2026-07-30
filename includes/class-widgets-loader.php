<?php
namespace PixelsElementorAddons;

use Elementor\Widgets_Manager;
use PixelsElementorAddons\Admin\Widget_Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Widgets_Loader {

	private static ?Widgets_Loader $instance = null;

	public static function instance(): Widgets_Loader {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		add_action( 'elementor/elements/categories_registered', [ $this, 'register_category' ] );
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );

		require_once PIXELS_CORE_PATH . 'includes/class-assets-manager.php';

		$active_slugs = Widget_Settings::instance()->get_active_slugs();
		$definitions  = Widget_Registry::instance()->get_asset_definitions( $active_slugs );

		Assets_Manager::instance()->set_definitions( $definitions );
	}

	public function register_category( $elements_manager ): void {
		if ( empty( Widget_Settings::instance()->get_active_slugs() ) ) {
			return;
		}

		$category = [
			'pixels-core' => [
				'title' => esc_html__( 'Pixels Addons', 'pixels-elementor-addons' ),
				'icon'  => 'eicon-plug',
			],
		];

		$existing = $elements_manager->get_categories();
		unset( $existing['pixels-core'] );

		$categories = array_merge( $category, $existing );

		$set_categories = function ( $categories ) {
			$this->categories = $categories;
		};
		$set_categories->call( $elements_manager, $categories );
	}

	public function register_widgets( Widgets_Manager $widgets_manager ): void {
		require_once PIXELS_CORE_PATH . 'widgets/trait-widget-assets.php';

		$registry = Widget_Registry::instance();
		$active   = Widget_Settings::instance()->get_active_slugs();

		foreach ( $active as $slug ) {
			$config = $registry->get( $slug );

			if ( null === $config || empty( $config['file'] ) ) {
				continue;
			}

			$base = $config['base_path'] ?? PIXELS_CORE_PATH;
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
