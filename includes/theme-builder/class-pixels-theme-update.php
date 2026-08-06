<?php
/**
 * Theme Update
 */

namespace PixelsCore\Theme_Builder;

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Update' ) ) :

	/**
	 * Update initial setup
	 */
	class Update {

		/**
		 * Option key for stored version number.
		 *
		 * @var string
		 */
		private $db_option_key = '_pixels_hf_db_version';

		/**
		 *  Constructor
		 *
		 */
		public function __construct() {

			// Theme Updates.
			if ( is_admin() ) :
				add_action( 'admin_init', [ $this, 'init' ], 5 );
			else :
				add_action( 'wp', [ $this, 'init' ], 5 );
			endif;
		}

		/**
		 * Implement theme update logic.
		 *
		 */
		public function init() {
			do_action( 'pixels_hf_update_before' );

			if ( ! $this->needs_db_update() ) :
				return;
			endif;

			$db_version = get_option( $this->db_option_key, false );

			if ( version_compare( $db_version, '1.2.0-beta.2', '<' ) ) :
				$this->setup_default_terget_rules();
			endif;

			// flush rewrite rules on plugin update.
			flush_rewrite_rules();

			$this->update_db_version();

			do_action( 'pixels_hf_update_after' );
		}

		/**
		 * Set default target rules for header, footer being used before target rules were added to the plugin.
		 *
		 * @return void
		 */
		private function setup_default_terget_rules() {
			$default_include_locations = [
				'rule'     => [ 0 => 'basic-global' ],
				'specific' => []
			];

			$header_id        = $this->get_legacy_template_id( 'type_header' );
			$footer_id        = $this->get_legacy_template_id( 'type_footer' );

			// Header.
			if ( ! empty( $header_id ) ) :
				update_post_meta( $header_id, 'pixels_hf_target_include_locations', $default_include_locations );
			endif;

			// Footer.
			if ( ! empty( $footer_id ) ) :
				update_post_meta( $footer_id, 'pixels_hf_target_include_locations', $default_include_locations );
			endif;
		}

		/**
		 * Get header or footer template id based on the meta query.
		 *
		 * @param  String $type Type of the template header/footer.
		 *
		 * @return Mixed  Returns the header or footer template id if found, else returns string ''.
		 */
		public function get_legacy_template_id( $type ) {
			$type_sanitized = sanitize_text_field( $type );
			$cache_key      = 'pixels_legacy_template_' . md5( $type_sanitized );

			static $local_cache = [];
			if ( isset( $local_cache[ $type_sanitized ] ) ) {
				return $local_cache[ $type_sanitized ];
			}

			// Check transient cache
			$cached_id = get_transient( $cache_key );
			if ( false !== $cached_id ) {
				return (int) $cached_id;
			}

			$args = [
				'post_type'      => 'pixels-theme',
				'posts_per_page' => 1,
				'orderby'        => 'meta_value',
				'order'          => 'ASC',
				'meta_query'     => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					[
						'key'     => pixels_get_theme_template_type_meta_key(),
						'value'   => $type_sanitized,
						'compare' => '=',
					],
				],
			];

			$args     = apply_filters( 'pixels_hf_get_template_id_args', $args );
			$template = new \WP_Query( $args );

			if ( $template->have_posts() ) {
				$posts = wp_list_pluck( $template->posts, 'ID' );
				set_transient( $cache_key, (int) $posts[0], HOUR_IN_SECONDS );
				$local_cache[ $type_sanitized ] = (int) $posts[0];
				return (int) $posts[0];
			}

			set_transient( $cache_key, '', HOUR_IN_SECONDS );
			$local_cache[ $type_sanitized ] = '';
			return '';
		}

		/**
		 * Check if db upgrade is required.
		 *
		 * @return true|false True if stored database version is lower than constant; false if otherwise.
		 */
		private function needs_db_update() {
			$db_version = get_option( $this->db_option_key, false );

			if ( false === $db_version || version_compare( $db_version, PIXELS_CORE_VERSION ) ) :
				return true;
			endif;

			return false;
		}

		/**
		 * Update DB version.
		 *
		 * @return void
		 */
		private function update_db_version() {
			update_option( $this->db_option_key, PIXELS_CORE_VERSION );
		}
	}
endif;

new Update();
