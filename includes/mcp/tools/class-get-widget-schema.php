<?php
/**
 * MCP tool: get_widget_schema.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp\Tools;

use PixelsCoreCreativeToolsForElementor\Mcp\Widget_Catalog;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Describes a widget's settings.
 */
final class Get_Widget_Schema extends Tool {

	/**
	 * Name.
	 *
	 * @return string Result.
	 */
	public function name(): string {
		return 'get_widget_schema';
	}

	/**
	 * Title.
	 *
	 * @return string Result.
	 */
	public function title(): string {
		return 'Get widget schema';
	}

	/**
	 * Description.
	 *
	 * @return string Result.
	 */
	public function description(): string {
		return 'Returns the settings a widget accepts, grouped by panel section: setting name, type, options, default, whether it is responsive, and the conditions under which it applies. Use "container" as the widget to get the layout container settings (flex direction, gap, padding, background, ...). Start with scope "content"; request "style" when you need colors, typography, borders or spacing. Large scopes come back as an index of sections; then ask again with "sections".';
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
				'widget'   => array(
					'type'        => 'string',
					'description' => 'Widget name from list_widgets (e.g. "pixeccte-heading"), or "container".',
				),
				'scope'    => array(
					'type'        => 'string',
					'enum'        => array( 'content', 'style', 'advanced', 'all' ),
					'default'     => 'content',
					'description' => 'Which panel tab to describe. content also includes the container Layout tab.',
				),
				'sections' => array(
					'type'        => 'array',
					'items'       => array( 'type' => 'string' ),
					'default'     => array(),
					'description' => 'Optional section ids (or labels) to describe. Large scopes return an index of sections first; use this to fetch the ones you need.',
				),
			),
			'required'   => array( 'widget' ),
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
	 * @return array<string, mixed>|\WP_Error Result.
	 */
	public function execute( array $args ) {
		return Widget_Catalog::get_schema( (string) $args['widget'], (string) $args['scope'], (array) $args['sections'] );
	}
}
