<?php
namespace PixelsCore;

use PixelsCore\Admin\Extension_Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Extensions_Loader {

	private static ?Extensions_Loader $instance = null;

	public static function instance(): Extensions_Loader {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		$active_slugs = Extension_Settings::instance()->get_active_slugs();

		if ( empty( $active_slugs ) ) {
			return;
		}

		require_once PIXELS_CORE_PATH . 'includes/class-assets-manager.php';

		$definitions = Extension_Registry::instance()->get_asset_definitions( $active_slugs );

		if (
			in_array( 'image_animation_effects', $active_slugs, true )
			&& ! isset( $definitions['animation_effects'] )
		) {
			$animation_config = Extension_Registry::instance()->get( 'animation_effects' );

			if ( ! empty( $animation_config['assets'] ) ) {
				$assets = $animation_config['assets'];

				if ( ! empty( $animation_config['base_path'] ) ) {
					$assets['base_path'] = $animation_config['base_path'];
				}

				if ( ! empty( $animation_config['base_url'] ) ) {
					$assets['base_url'] = $animation_config['base_url'];
				}

				$definitions['animation_effects'] = $assets;
			}
		}

		Assets_Manager::instance()->set_extension_definitions( $definitions );

		$registry = Extension_Registry::instance();

		foreach ( $active_slugs as $slug ) {
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

			$class = $config['class'];

			if ( class_exists( $class ) && method_exists( $class, 'init' ) ) {
				$class::init();
			}
		}
	}
}
