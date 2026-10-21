<?php
/**
 * MCP tool base class.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp\Tools;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A single MCP tool. Pro and third parties extend this and register through `pixeccte_mcp_tools`.
 */
abstract class Tool {

	/**
	 * Unique tool name (snake_case), as the AI client sees it.
	 *
	 * @return string Result.
	 */
	abstract public function name(): string;

	/**
	 * Human-readable title.
	 *
	 * @return string Result.
	 */
	abstract public function title(): string;

	/**
	 * Description for the AI: what it does, when to use it, and how.
	 *
	 * @return string Result.
	 */
	abstract public function description(): string;

	/**
	 * JSON Schema (object) for the tool arguments.
	 *
	 * @return array<string, mixed>
	 */
	abstract public function input_schema(): array;

	/**
	 * Run the tool.
	 *
	 * @param array<string, mixed> $args Validated arguments.
	 * @return array<string, mixed>|\WP_Error Result.
	 */
	abstract public function execute( array $args );

	/**
	 * Capability the current user needs to see and call this tool.
	 *
	 * @return string Result.
	 */
	public function capability(): string {
		return 'edit_posts';
	}

	/**
	 * Whether the tool only reads data.
	 *
	 * @return bool Result.
	 */
	public function is_read_only(): bool {
		return false;
	}

	/**
	 * Whether the tool removes data.
	 *
	 * @return bool Result.
	 */
	public function is_destructive(): bool {
		return false;
	}

	/**
	 * Whether the current user may use this tool.
	 *
	 * @return bool Result.
	 */
	public function current_user_can_use(): bool {
		return current_user_can( $this->capability() );
	}

	/**
	 * Shared schema for an element tree argument.
	 *
	 * @param string $description Description.
	 * @return array<string, mixed>
	 */
	protected static function elements_schema( string $description ): array {
		return array(
			'type'        => 'array',
			'description' => $description . ' Each element is {"elType": "container"|"widget", "widgetType": "<widget name, widgets only>", "settings": {...}, "elements": [children]}. Containers hold widgets and inner containers; widgets have no children except nested widgets (one container per item). Do not send ids; they are generated.',
			'items'       => array(
				'type'       => 'object',
				'properties' => array(
					'elType'     => array(
						'type' => 'string',
						'enum' => array( 'container', 'widget' ),
					),
					'widgetType' => array( 'type' => 'string' ),
					'settings'   => array( 'type' => 'object' ),
					'elements'   => array( 'type' => 'array' ),
				),
				'required'   => array( 'elType' ),
			),
		);
	}

	/**
	 * MCP tool definition for `tools/list`.
	 *
	 * @return array<string, mixed>
	 */
	public function to_definition(): array {
		$schema = $this->input_schema();

		if ( empty( $schema['properties'] ) ) {
			$schema['properties'] = new \stdClass();
		}

		return array(
			'name'        => $this->name(),
			'title'       => $this->title(),
			'description' => $this->description(),
			'inputSchema' => $schema,
			'annotations' => array(
				'title'           => $this->title(),
				'readOnlyHint'    => $this->is_read_only(),
				'destructiveHint' => $this->is_destructive(),
				'openWorldHint'   => false,
			),
		);
	}
}
