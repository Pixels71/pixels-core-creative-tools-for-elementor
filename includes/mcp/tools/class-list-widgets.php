<?php
/**
 * MCP tool: list_widgets.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp\Tools;

use PixelsCoreCreativeToolsForElementor\Mcp\Widget_Catalog;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Lists widgets available for building.
 */
final class List_Widgets extends Tool {

	/**
	 * Name.
	 *
	 * @return string Result.
	 */
	public function name(): string {
		return 'list_widgets';
	}

	/**
	 * Title.
	 *
	 * @return string Result.
	 */
	public function title(): string {
		return 'List widgets';
	}

	/**
	 * Description.
	 *
	 * @return string Result.
	 */
	public function description(): string {
		return 'Lists the widgets you can place on a page, Pixels widgets first. Use the "name" as widgetType. Widgets marked nested take child containers (one per item). Call get_widget_schema before using a widget.';
	}

	/**
	 * Input schema.
	 *
	 * @return array<string, mixed>
	 */
	public function input_schema(): array {
		return array(
			'type'       => 'object',
			'properties' => array(
				'source' => array(
					'type'        => 'string',
					'enum'        => array( 'all', 'pixels', 'elementor', 'other' ),
					'default'     => 'all',
					'description' => 'Filter by origin: pixels (Pixels Core / Pro), elementor (Elementor core), other (third-party plugins).',
				),
			),
		);
	}

	/**
	 * Read only.
	 *
	 * @return bool Result.
	 */
	public function is_read_only(): bool {
		return true;
	}

	/**
	 * Execute.
	 *
	 * @param array<string, mixed> $args Args.
	 * @return array<string, mixed> Result.
	 */
	public function execute( array $args ) {
		$widgets = Widget_Catalog::list_widgets( (string) $args['source'] );

		return array(
			'count'   => count( $widgets ),
			'widgets' => $widgets,
		);
	}
}
