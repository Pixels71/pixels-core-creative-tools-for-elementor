<?php
/**
 * Elementor document access for MCP.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Mcp;

use Elementor\Plugin as Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Reads and writes Elementor documents through Elementor's Document API, so revisions,
 * sanitizing and CSS regeneration behave exactly as when saving from the editor.
 */
final class Document_Store {

	/**
	 * Load a document the current user may edit.
	 *
	 * @param int  $post_id  Post ID.
	 * @param bool $for_save Whether the caller is going to write.
	 * @return \Elementor\Core\Base\Document|\WP_Error Result.
	 */
	public static function get_document( int $post_id, bool $for_save = false ) {
		$post = get_post( $post_id );

		if ( ! $post || 'trash' === $post->post_status ) {
			return new \WP_Error( 'pixeccte_mcp_not_found', sprintf( 'Page %d was not found.', $post_id ) );
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return new \WP_Error( 'pixeccte_mcp_forbidden', sprintf( 'You are not allowed to edit page %d.', $post_id ) );
		}

		$document = Elementor::$instance->documents->get( $post_id, false );

		if ( ! $document ) {
			return new \WP_Error( 'pixeccte_mcp_not_elementor', sprintf( 'Page %d cannot be edited with Elementor.', $post_id ) );
		}

		if ( $for_save ) {
			if ( ! $document->is_editable_by_current_user() ) {
				return new \WP_Error( 'pixeccte_mcp_not_editable', sprintf( 'Elementor does not allow you to edit page %d (check Elementor > Settings > Post Types and Role Manager).', $post_id ) );
			}

			$lock = self::check_lock( $post_id );

			if ( is_wp_error( $lock ) ) {
				return $lock;
			}
		}

		return $document;
	}

	/**
	 * Refuse to write while someone else has the page open in an editor.
	 *
	 * @param int $post_id Post ID.
	 * @return true|\WP_Error Result.
	 */
	public static function check_lock( int $post_id ) {
		if ( ! function_exists( 'wp_check_post_lock' ) ) {
			require_once ABSPATH . 'wp-admin/includes/post.php';
		}

		$locked_by = wp_check_post_lock( $post_id );

		if ( $locked_by ) {
			$user = get_userdata( $locked_by );

			return new \WP_Error(
				'pixeccte_mcp_locked',
				sprintf(
					'Page %d is open in the editor by %s. Ask them to close it, then try again.',
					$post_id,
					$user ? $user->display_name : 'another user'
				)
			);
		}

		return true;
	}

	/**
	 * Elements of a document.
	 *
	 * @param \Elementor\Core\Base\Document $document Document.
	 * @return array<int, array<string, mixed>>
	 */
	public static function get_elements( $document ): array {
		$elements = $document->get_elements_data();

		return is_array( $elements ) ? $elements : array();
	}

	/**
	 * Fingerprint of the element tree, used for optimistic concurrency.
	 *
	 * @param array<int, array<string, mixed>> $elements Elements.
	 * @return string Result.
	 */
	public static function hash( array $elements ): string {
		return substr( md5( (string) wp_json_encode( $elements ) ), 0, 12 );
	}

	/**
	 * Fail when the page changed since the client read it.
	 *
	 * @param array<int, array<string, mixed>> $elements      Current elements.
	 * @param string                           $expected_hash Hash the client saw ('' skips the check).
	 * @return true|\WP_Error Result.
	 */
	public static function check_hash( array $elements, string $expected_hash ) {
		if ( '' === $expected_hash || self::hash( $elements ) === $expected_hash ) {
			return true;
		}

		return new \WP_Error(
			'pixeccte_mcp_stale',
			'The page changed since you last read it. Call get_page_structure again and retry with the new content_hash.'
		);
	}

	/**
	 * Save elements.
	 *
	 * @param \Elementor\Core\Base\Document    $document Document.
	 * @param array<int, array<string, mixed>> $elements Elements.
	 * @return true|\WP_Error Result.
	 */
	public static function save( $document, array $elements ) {
		$document->set_is_built_with_elementor( true );

		if ( ! $document->save( array( 'elements' => $elements ) ) ) {
			return new \WP_Error( 'pixeccte_mcp_save_failed', 'Elementor could not save the page.' );
		}

		return true;
	}

	/**
	 * Call a callback for every element in a tree (depth first).
	 *
	 * @param array<int, array<string, mixed>> $elements Elements.
	 * @param callable                         $callback Receives ( array $element, ?array $parent ).
	 * @param array<string, mixed>|null        $parent_element Parent element.
	 */
	public static function walk( array $elements, callable $callback, ?array $parent_element = null ): void {
		foreach ( $elements as $element ) {
			if ( ! is_array( $element ) ) {
				continue;
			}

			$callback( $element, $parent_element );

			if ( ! empty( $element['elements'] ) && is_array( $element['elements'] ) ) {
				self::walk( $element['elements'], $callback, $element );
			}
		}
	}

	/**
	 * Locate an element: its parent (null for top level) and index within the parent's list.
	 *
	 * @param array<int, array<string, mixed>> $elements Elements.
	 * @param string                           $id       Element ID.
	 * @return array{element: array<string, mixed>, parent: array<string, mixed>|null, index: int}|null Result.
	 */
	public static function find( array $elements, string $id ): ?array {
		foreach ( $elements as $index => $element ) {
			if ( ( $element['id'] ?? '' ) === $id ) {
				return array(
					'element' => $element,
					'parent'  => null,
					'index'   => (int) $index,
				);
			}
		}

		foreach ( $elements as $element ) {
			if ( empty( $element['elements'] ) || ! is_array( $element['elements'] ) ) {
				continue;
			}

			$found = self::find( $element['elements'], $id );

			if ( null !== $found ) {
				if ( null === $found['parent'] ) {
					$found['parent'] = $element;
				}

				return $found;
			}
		}

		return null;
	}

	/**
	 * Return a copy of the tree with the element's list transformed by a callback.
	 *
	 * @param array<int, array<string, mixed>> $elements  Elements.
	 * @param string|null                      $parent_id Parent whose children change (null = top level).
	 * @param callable                         $callback  Receives and returns the children list.
	 * @param bool                             $found     Set to true when the parent was found (by reference).
	 * @return array<int, array<string, mixed>>
	 */
	public static function modify_children( array $elements, ?string $parent_id, callable $callback, bool &$found = false ): array {
		if ( null === $parent_id ) {
			$found = true;

			return array_values( $callback( $elements ) );
		}

		foreach ( $elements as $index => $element ) {
			if ( ( $element['id'] ?? '' ) === $parent_id ) {
				$found                          = true;
				$elements[ $index ]['elements'] = array_values( $callback( $element['elements'] ?? array() ) );

				return $elements;
			}

			if ( ! empty( $element['elements'] ) && is_array( $element['elements'] ) ) {
				$elements[ $index ]['elements'] = self::modify_children( $element['elements'], $parent_id, $callback, $found );

				if ( $found ) {
					return $elements;
				}
			}
		}

		return $elements;
	}

	/**
	 * Return a copy of the tree with one element replaced by a callback.
	 *
	 * @param array<int, array<string, mixed>> $elements Elements.
	 * @param string                           $id       Element ID.
	 * @param callable                         $callback Receives and returns the element.
	 * @return array<int, array<string, mixed>>
	 */
	public static function modify_element( array $elements, string $id, callable $callback ): array {
		foreach ( $elements as $index => $element ) {
			if ( ( $element['id'] ?? '' ) === $id ) {
				$elements[ $index ] = $callback( $element );

				return $elements;
			}

			if ( ! empty( $element['elements'] ) && is_array( $element['elements'] ) ) {
				$elements[ $index ]['elements'] = self::modify_element( $element['elements'], $id, $callback );
			}
		}

		return $elements;
	}

	/**
	 * Short summary of an element (for outlines and tool results).
	 *
	 * @param array<string, mixed> $element      Element.
	 * @param bool                 $with_settings Include full settings.
	 * @return array<string, mixed>
	 */
	public static function summarize( array $element, bool $with_settings ): array {
		$summary = array(
			'id'     => (string) ( $element['id'] ?? '' ),
			'elType' => (string) ( $element['elType'] ?? '' ),
		);

		if ( ! empty( $element['widgetType'] ) ) {
			$summary['widgetType'] = (string) $element['widgetType'];
		}

		$settings = is_array( $element['settings'] ?? null ) ? $element['settings'] : array();

		if ( $with_settings ) {
			$summary['settings'] = empty( $settings ) ? new \stdClass() : $settings;
		} else {
			$label = self::label( $settings );

			if ( '' !== $label ) {
				$summary['label'] = $label;
			}
		}

		$children = array();

		foreach ( (array) ( $element['elements'] ?? array() ) as $child ) {
			if ( is_array( $child ) ) {
				$children[] = self::summarize( $child, $with_settings );
			}
		}

		if ( ! empty( $children ) ) {
			$summary['elements'] = $children;
		}

		return $summary;
	}

	/**
	 * First meaningful text in an element's settings.
	 *
	 * @param array<string, mixed> $settings Settings.
	 * @return string Result.
	 */
	private static function label( array $settings ): string {
		foreach ( array( '_title', 'title', 'heading', 'text', 'editor', 'button_text', 'description', 'content' ) as $key ) {
			if ( ! empty( $settings[ $key ] ) && is_string( $settings[ $key ] ) ) {
				$text = trim( wp_strip_all_tags( $settings[ $key ] ) );

				if ( '' !== $text ) {
					return mb_strimwidth( $text, 0, 60, '…' );
				}
			}
		}

		return '';
	}
}
