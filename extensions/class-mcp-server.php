<?php
/**
 * MCP Server extension.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Extensions;

use PixelsCoreCreativeToolsForElementor\Mcp\Server;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MCP Server — lets AI clients (Claude Code, Claude Desktop, claude.ai) build Elementor pages.
 */
final class Mcp_Server {

	/**
	 * Init.
	 */
	public static function init(): void {
		require_once PIXECCTE_PATH . 'includes/mcp/class-server.php';

		Server::instance();
	}
}
