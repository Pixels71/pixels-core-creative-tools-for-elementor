<?php
/**
 * MCP tool registry.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp;

use PixelsCoreCreativeToolsForElementor\Mcp\Tools\Tool;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Collects the tools the MCP server exposes.
 */
final class Tool_Registry {

	/**
	 * Instance.
	 *
	 * @var mixed
	 */
	private static ?Tool_Registry $instance = null;

	/**
	 * Tools keyed by name.
	 *
	 * @var array<string, Tool>|null
	 */
	private ?array $tools = null;

	/**
	 * Instance.
	 *
	 * @return Tool_Registry Result.
	 */
	public static function instance(): Tool_Registry {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * All registered tools.
	 *
	 * @return array<string, Tool>
	 */
	public function get_all(): array {
		if ( null !== $this->tools ) {
			return $this->tools;
		}

		$tools = array(
			new Tools\Get_Site_Info(),
			new Tools\List_Widgets(),
			new Tools\Get_Widget_Schema(),
			new Tools\List_Pages(),
			new Tools\Get_Page_Structure(),
			new Tools\Create_Page(),
			new Tools\Update_Page(),
			new Tools\Trash_Page(),
			new Tools\Get_Preview_Url(),
			new Tools\Add_Elements(),
			new Tools\Update_Element(),
			new Tools\Delete_Element(),
			new Tools\List_Theme_Templates(),
			new Tools\Create_Theme_Template(),
			new Tools\List_Menus(),
			new Tools\Save_Menu(),
			new Tools\Search_Media(),
			new Tools\Import_Media_From_Url(),
			new Tools\Update_Design_System(),
			new Tools\List_Effects(),
		);

		/**
		 * Filter the MCP tools. Pro adds its tools here.
		 *
		 * @param array<int, Tool> $tools
		 */
		$tools = apply_filters( 'pixeccte_mcp_tools', $tools );

		$this->tools = array();

		foreach ( (array) $tools as $tool ) {
			if ( $tool instanceof Tool ) {
				$this->tools[ $tool->name() ] = $tool;
			}
		}

		return $this->tools;
	}

	/**
	 * Get a tool by name.
	 *
	 * @param string $name Name.
	 * @return Tool|null Result.
	 */
	public function get( string $name ): ?Tool {
		$tools = $this->get_all();

		return $tools[ $name ] ?? null;
	}

	/**
	 * Tools the current user may use.
	 *
	 * @return array<string, Tool>
	 */
	public function get_available(): array {
		return array_filter(
			$this->get_all(),
			static function ( Tool $tool ): bool {
				return $tool->current_user_can_use();
			}
		);
	}
}
