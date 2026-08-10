<?php
namespace PixelsCoreCreativeToolsForElementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Single source of truth for Pixels Core Creative Tools for Elementor (free) extensions.
 */
final class Extension_Registry {

	private static ?Extension_Registry $instance = null;

	/**
	 * @var array<string, array<string, mixed>>
	 */
	private array $extensions = [
		'live_copy_paste' => [
			'file'        => 'extensions/class-live-copy-paste.php',
			'class'       => 'PixelsCoreCreativeToolsForElementor\\Extensions\\Live_Copy_Paste',
			'title'       => 'Live Copy Paste',
			'description' => 'Paste Elementor sections, containers, and widgets from the clipboard in the editor.',
			'tier'        => 'free',
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
		return apply_filters( 'pixeccte_extensions', $extensions );
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
			'live_copy_paste' => __( 'Live Copy Paste', 'pixels-core-creative-tools-for-elementor' ),
		];

		$descriptions = [
			'live_copy_paste' => __( 'Paste Elementor sections, containers, and widgets from the clipboard in the editor.', 'pixels-core-creative-tools-for-elementor' ),
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
