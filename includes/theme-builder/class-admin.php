<?php
/**
 * Pixeccte theme admin.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Theme_Builder;

use PixelsCoreCreativeToolsForElementor\Theme_Builder\Target_Rules_Fields;

defined( 'ABSPATH' ) || exit;

/**
 * Admin setup
 */
class Admin {

	/**
	 * Instance of Admin.
	 *
	 * @var mixed
	 */
	private static $instance = null;

	/**
	 * Instance of Admin
	 *
	 * @return Admin Instance of Admin
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) :
			self::$instance = new self();
		endif;

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'header_footer_posttype' ) );
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ), 21 );
		add_filter( 'parent_file', array( $this, 'highlight_parent_menu' ) );
		add_filter( 'submenu_file', array( $this, 'highlight_submenu' ) );
		add_action( 'add_meta_boxes', array( $this, 'register_metabox' ) );
		add_action( 'save_post', array( $this, 'save_meta' ) );
		add_action( 'admin_notices', array( $this, 'location_notice' ) );
		add_action( 'template_redirect', array( $this, 'block_template_frontend' ) );
		add_filter( 'manage_pixeccte-theme_posts_columns', array( $this, 'set_shortcode_columns' ) );
		add_action( 'manage_pixeccte-theme_posts_custom_column', array( $this, 'render_shortcode_column' ), 10, 2 );
		if ( defined( 'ELEMENTOR_PRO_VERSION' ) && ELEMENTOR_PRO_VERSION > 2.8 ) :
			add_action( 'elementor/editor/footer', array( $this, 'register_hf_epro_script' ), 99 );
		endif;

		if ( is_admin() ) :
			add_action( 'manage_pixeccte-theme_posts_custom_column', array( $this, 'column_content' ), 10, 2 );
			add_filter( 'manage_pixeccte-theme_posts_columns', array( $this, 'column_headings' ) );

			add_filter( 'views_edit-pixeccte-theme', array( $this, 'pixeccte_admin_theme_builder_tabs' ) );
			add_action( 'pre_get_posts', array( $this, 'pixeccte_hf_filter_post_type_by_meta' ) );
			add_action( 'admin_head-nav-menus.php', array( $this, 'register_mega_menu_nav_metabox' ) );

			// Register preview metabox and save handlers.
			add_action( 'add_meta_boxes', array( $this, 'register_preview_metabox' ) );
			add_action( 'save_post_pixeccte-theme', array( $this, 'save_preview_meta' ) );
		endif;
	}

	/**
	 * Script for Elementor Pro full site editing support.
	 */
	public function register_hf_epro_script() {
		$ids_array = array(
			array(
				'id'    => pixeccte_get_header_id(),
				'value' => 'Header',
			),
			array(
				'id'    => pixeccte_get_footer_id(),
				'value' => 'Footer',
			),
		);

		wp_enqueue_script( 'pixeccte-theme-elementor-pro-compatibility', PIXECCTE_URL . 'assets/js/pixeccte-theme-elementor-pro-compatibility.js', array( 'jquery' ), PIXECCTE_VERSION, true );

		wp_localize_script(
			'pixeccte-theme-elementor-pro-compatibility',
			'pixeccte_hf_admin',
			array(
				'ids_array' => wp_json_encode( $ids_array ),
			)
		);
	}

	/**
	 * Adds or removes list table column headings.
	 *
	 * @param array $columns Array of columns.
	 * @return array
	 */
	public function column_headings( $columns ) {
		unset( $columns['date'] );

		$columns['pixeccte_hf_display_rules'] = esc_html__( 'Display Rules', 'pixels-core-creative-tools-for-elementor' );
		$columns['date']                      = esc_html__( 'Date', 'pixels-core-creative-tools-for-elementor' );

		return $columns;
	}

	/**
	 * Adds the custom list table column content.
	 *
	 * @param mixed $column Column.
	 * @param mixed $post_id Post id.
	 */
	public function column_content( $column, $post_id ) {

		if ( 'pixeccte_hf_display_rules' === $column ) :

			$locations = get_post_meta( $post_id, 'pixeccte_hf_target_include_locations', true );
			if ( ! empty( $locations ) ) :
				echo '<div class="pixeccte-theme-advanced-headers-location-wrap" style="margin-bottom: 5px;">';
				echo '<strong>' . esc_html__( 'Display:', 'pixels-core-creative-tools-for-elementor' ) . ' </strong>';
				$this->column_display_location_rules( $locations );
				echo '</div>';
			endif;

			$locations = get_post_meta( $post_id, 'pixeccte_hf_target_exclude_locations', true );
			if ( ! empty( $locations ) ) :
				echo '<div class="pixeccte-theme-advanced-headers-exclusion-wrap" style="margin-bottom: 5px;">';
					echo '<strong>' . esc_html__( 'Exclusion:', 'pixels-core-creative-tools-for-elementor' ) . ' </strong>';
					$this->column_display_location_rules( $locations );
				echo '</div>';
			endif;
		endif;
	}

	/**
	 * Get Markup of Location rules for Display rule column.
	 *
	 * @param array $locations Array of locations.
	 * @return void
	 */
	public function column_display_location_rules( $locations ) {

		$location_label = array();

		$index = array_search( 'specifics', $locations['rule'], true );
		if ( false !== $index ) {
			unset( $locations['rule'][ $index ] );
		}

		if ( isset( $locations['rule'] ) && is_array( $locations['rule'] ) ) {
			foreach ( $locations['rule'] as $location ) {
				$label = Target_Rules_Fields::get_location_by_key( $location );
				if ( ! empty( $label ) ) {
					$location_label[] = sanitize_text_field( $label );
				}
			}
		}

		if ( isset( $locations['specific'] ) && is_array( $locations['specific'] ) ) {
			foreach ( $locations['specific'] as $location ) {
				$label = Target_Rules_Fields::get_location_by_key( $location );
				if ( ! empty( $label ) ) {
					$location_label[] = sanitize_text_field( $label );
				}
			}
		}
		echo esc_html( implode( ', ', $location_label ) );
	}


	/**
	 * Register Post type for header footer & blocks templates
	 */
	public function header_footer_posttype() {
		$labels = array(
			'name'               => esc_html__( 'Theme Builder', 'pixels-core-creative-tools-for-elementor' ),
			'singular_name'      => esc_html__( 'Theme Builder', 'pixels-core-creative-tools-for-elementor' ),
			'menu_name'          => esc_html__( 'Theme Builder', 'pixels-core-creative-tools-for-elementor' ),
			'name_admin_bar'     => esc_html__( 'Theme Builder', 'pixels-core-creative-tools-for-elementor' ),
			'add_new'            => esc_html__( 'Add New', 'pixels-core-creative-tools-for-elementor' ),
			'add_new_item'       => esc_html__( 'Add New Template', 'pixels-core-creative-tools-for-elementor' ),
			'new_item'           => esc_html__( 'New Template', 'pixels-core-creative-tools-for-elementor' ),
			'edit_item'          => esc_html__( 'Edit Template', 'pixels-core-creative-tools-for-elementor' ),
			'view_item'          => esc_html__( 'View Template', 'pixels-core-creative-tools-for-elementor' ),
			'all_items'          => esc_html__( 'All Templates', 'pixels-core-creative-tools-for-elementor' ),
			'search_items'       => esc_html__( 'Search Templates', 'pixels-core-creative-tools-for-elementor' ),
			'parent_item_colon'  => esc_html__( 'Parent Template', 'pixels-core-creative-tools-for-elementor' ),
			'not_found'          => esc_html__( 'No Templates found.', 'pixels-core-creative-tools-for-elementor' ),
			'not_found_in_trash' => esc_html__( 'No Templates found in Trash.', 'pixels-core-creative-tools-for-elementor' ),
		);

		$args = array(
			'labels'              => $labels,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => false,
			'show_in_nav_menus'   => false,
			'exclude_from_search' => true,
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'menu_icon'           => 'dashicons-editor-kitchensink',
			'supports'            => array( 'title', 'thumbnail', 'elementor' ),
		);

		register_post_type( 'pixeccte-theme', $args );
	}

	/**
	 * Register Theme Builder as a submenu under Pixels Core.
	 */
	public function register_admin_menu() {
		add_submenu_page(
			'pixeccte',
			esc_html__( 'Theme Builder', 'pixels-core-creative-tools-for-elementor' ),
			esc_html__( 'Theme Builder', 'pixels-core-creative-tools-for-elementor' ),
			'edit_pages',
			'edit.php?post_type=pixeccte-theme'
		);
	}

	/**
	 * Keep Pixels Core expanded when viewing Theme Builder screens.
	 *
	 * @param string $parent_file Current parent file.
	 * @return string
	 */
	public function highlight_parent_menu( $parent_file ) {
		$screen = get_current_screen();

		if ( $screen && 'pixeccte-theme' === $screen->post_type ) {
			return 'pixeccte';
		}

		return $parent_file;
	}

	/**
	 * Highlight the Theme Builder submenu on CPT list/edit screens.
	 *
	 * @param string|null $submenu_file Current submenu file.
	 * @return string|null
	 */
	public function highlight_submenu( $submenu_file ) {
		$screen = get_current_screen();

		if ( $screen && 'pixeccte-theme' === $screen->post_type ) {
			return 'edit.php?post_type=pixeccte-theme';
		}

		return $submenu_file;
	}

	/**
	 * Add Mega Menu templates to Appearance > Menus.
	 */
	public function register_mega_menu_nav_metabox() {
		add_meta_box(
			'pixeccte-mega-menu-nav-metabox',
			esc_html__( 'Mega Menu', 'pixels-core-creative-tools-for-elementor' ),
			array( $this, 'render_mega_menu_nav_metabox' ),
			'nav-menus',
			'side',
			'default'
		);
	}

	/**
	 * Render Mega Menu template choices for the WordPress menu editor.
	 */
	public function render_mega_menu_nav_metabox() {
		$mega_menus = get_posts(
			array(
				'post_type'      => 'pixeccte-theme',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'meta_key'       => pixeccte_get_theme_template_type_meta_key(), // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'type_mega_menu', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);

		if ( empty( $mega_menus ) ) :
			?>
			<p><?php esc_html_e( 'No published Mega Menu templates found.', 'pixels-core-creative-tools-for-elementor' ); ?></p>
			<p>
				<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=pixeccte-theme' ) ); ?>">
					<?php esc_html_e( 'Create a Mega Menu template', 'pixels-core-creative-tools-for-elementor' ); ?>
				</a>
			</p>
			<?php
			return;
		endif;
		?>
		<div id="pixeccte-mega-menu" class="posttypediv">
			<div id="tabs-panel-pixeccte-mega-menu" class="tabs-panel tabs-panel-active">
				<ul id="pixeccte-mega-menu-checklist" class="categorychecklist form-no-clear">
					<?php foreach ( $mega_menus as $mega_menu ) : ?>
						<?php $menu_item_id = -1 * absint( $mega_menu->ID ); ?>
						<li>
							<label class="menu-item-title">
								<input
									type="checkbox"
									class="menu-item-checkbox"
									name="menu-item[<?php echo esc_attr( (string) $menu_item_id ); ?>][menu-item-object-id]"
									value="<?php echo esc_attr( (string) $mega_menu->ID ); ?>"
								>
								<?php echo esc_html( $mega_menu->post_title ); ?>
							</label>
							<input type="hidden" name="menu-item[<?php echo esc_attr( (string) $menu_item_id ); ?>][menu-item-db-id]" value="0">
							<input type="hidden" name="menu-item[<?php echo esc_attr( (string) $menu_item_id ); ?>][menu-item-object]" value="pixeccte-theme">
							<input type="hidden" name="menu-item[<?php echo esc_attr( (string) $menu_item_id ); ?>][menu-item-parent-id]" value="0">
							<input type="hidden" name="menu-item[<?php echo esc_attr( (string) $menu_item_id ); ?>][menu-item-type]" value="post_type">
							<input type="hidden" name="menu-item[<?php echo esc_attr( (string) $menu_item_id ); ?>][menu-item-title]" value="<?php echo esc_attr( $mega_menu->post_title ); ?>">
							<input type="hidden" name="menu-item[<?php echo esc_attr( (string) $menu_item_id ); ?>][menu-item-url]" value="<?php echo esc_url( get_permalink( $mega_menu ) ); ?>">
							<input type="hidden" name="menu-item[<?php echo esc_attr( (string) $menu_item_id ); ?>][menu-item-target]" value="">
							<input type="hidden" name="menu-item[<?php echo esc_attr( (string) $menu_item_id ); ?>][menu-item-attr-title]" value="">
							<input type="hidden" name="menu-item[<?php echo esc_attr( (string) $menu_item_id ); ?>][menu-item-classes]" value="">
							<input type="hidden" name="menu-item[<?php echo esc_attr( (string) $menu_item_id ); ?>][menu-item-xfn]" value="">
							<input type="hidden" name="menu-item[<?php echo esc_attr( (string) $menu_item_id ); ?>][menu-item-status]" value="publish">
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<p class="button-controls wp-clearfix">
				<span class="list-controls hide-if-no-js">
					<input type="checkbox" id="pixeccte-mega-menu-tab" class="select-all">
					<label for="pixeccte-mega-menu-tab"><?php esc_html_e( 'Select All', 'pixels-core-creative-tools-for-elementor' ); ?></label>
				</span>
				<span class="add-to-menu">
					<input type="submit" class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu', 'pixels-core-creative-tools-for-elementor' ); ?>" name="add-pixeccte-mega-menu-menu-item" id="submit-pixeccte-mega-menu">
					<span class="spinner"></span>
				</span>
			</p>
		</div>
		<?php
	}

	/**
	 * Register meta box(es).
	 */
	public function register_metabox() {
		add_meta_box(
			'pixeccte-theme-meta-box',
			__( 'Pixels Theme Builder', 'pixels-core-creative-tools-for-elementor' ),
			array(
				$this,
				'metabox_render',
			),
			'pixeccte-theme',
			'normal',
			'high'
		);
	}

	/**
	 * Render Meta field.
	 *
	 * @param  POST $post Currennt post object which is being displayed.
	 */
	public function metabox_render( $post ) {
		$template_type = esc_attr( pixeccte_get_theme_template_type( $post->ID ) );

		wp_nonce_field( 'pixeccte_hf_meta_nounce', 'pixeccte_hf_meta_nounce' );
		?>
		<table class="pixeccte-theme-options-table widefat">
			<tbody>
				<tr class="pixeccte-theme-options-row type-of-template">
					<td class="pixeccte-theme-options-row-heading">
						<label for="pixeccte_hf_template_type"><?php esc_html_e( 'Type of Template', 'pixels-core-creative-tools-for-elementor' ); ?></label>
					</td>
					<td class="pixeccte-theme-options-row-content">
						<select name="pixeccte_hf_template_type" id="pixeccte_hf_template_type">
							<option value="" <?php selected( $template_type, '' ); ?>><?php esc_html_e( 'Select Option', 'pixels-core-creative-tools-for-elementor' ); ?></option>
							<?php foreach ( \PixelsCoreCreativeToolsForElementor\Plugin::get_theme_template_types() as $type_key => $type_label ) : ?>
								<option value="<?php echo esc_attr( $type_key ); ?>" <?php selected( $template_type, $type_key ); ?>><?php echo esc_html( $type_label ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>

				<?php $this->display_rules_tab(); ?>
			</tbody>
		</table>
		<?php
	}

	/**
	 * Markup for Display Rules Tabs.
	 */
	public function display_rules_tab() {
		// Load Target Rule assets.
		Target_Rules_Fields::get_instance()->admin_styles();

		$include_locations = get_post_meta( get_the_id(), 'pixeccte_hf_target_include_locations', true );
		$exclude_locations = get_post_meta( get_the_id(), 'pixeccte_hf_target_exclude_locations', true );
		?>
		<tr class="pixeccte-theme-target-rules-row pixeccte-theme-options-row">
			<td class="pixeccte-theme-target-rules-row-heading pixeccte-theme-options-row-heading">
				<label><?php esc_html_e( 'Display On', 'pixels-core-creative-tools-for-elementor' ); ?></label>
				<i class="pixeccte-theme-target-rules-heading-help dashicons dashicons-editor-help"
					title="<?php echo esc_attr__( 'Add locations for where this template should appear.', 'pixels-core-creative-tools-for-elementor' ); ?>"></i>
			</td>
			<td class="pixeccte-theme-target-rules-row-content pixeccte-theme-options-row-content">
				<?php
				Target_Rules_Fields::target_rule_settings_field(
					'pixeccte-target-rules-location',
					array(
						'title'          => esc_html__( 'Display Rules', 'pixels-core-creative-tools-for-elementor' ),
						'value'          => '[{"type":"basic-global","specific":null}]',
						'tags'           => 'site,enable,target,pages',
						'rule_type'      => 'display',
						'add_rule_label' => esc_html__( 'Add Display Rule', 'pixels-core-creative-tools-for-elementor' ),
					),
					$include_locations
				);
				?>
			</td>
		</tr>
		<tr class="pixeccte-theme-target-rules-row pixeccte-theme-options-row">
			<td class="pixeccte-theme-target-rules-row-heading pixeccte-theme-options-row-heading">
				<label><?php esc_html_e( 'Do Not Display On', 'pixels-core-creative-tools-for-elementor' ); ?></label>
				<i class="pixeccte-theme-target-rules-heading-help dashicons dashicons-editor-help"
					title="<?php echo esc_attr__( 'Add locations for where this template should not appear.', 'pixels-core-creative-tools-for-elementor' ); ?>"></i>
			</td>
			<td class="pixeccte-theme-target-rules-row-content pixeccte-theme-options-row-content">
				<?php
				Target_Rules_Fields::target_rule_settings_field(
					'pixeccte-theme-target-rules-exclusion',
					array(
						'title'          => esc_html__( 'Exclude On', 'pixels-core-creative-tools-for-elementor' ),
						'value'          => '[]',
						'tags'           => 'site,enable,target,pages',
						'add_rule_label' => esc_html__( 'Add Exclusion Rule', 'pixels-core-creative-tools-for-elementor' ),
						'rule_type'      => 'exclude',
					),
					$exclude_locations
				);
				?>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save meta field.
	 *
	 * @param  POST $post_id Currennt post object which is being displayed.
	 *
	 * @return Void
	 */
	public function save_meta( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		$post_id = absint( $post_id );

		if ( ! $post_id || 'pixeccte-theme' !== get_post_type( $post_id ) ) {
			return;
		}

		// Verify nonce safely.
		if ( ! isset( $_POST['pixeccte_hf_meta_nounce'] ) ) {
			return;
		}

		$nonce = sanitize_text_field( wp_unslash( $_POST['pixeccte_hf_meta_nounce'] ) );

		if ( ! wp_verify_nonce( $nonce, 'pixeccte_hf_meta_nounce' ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Unslashed once; get_format_rule_value sanitizes.
		$post_data = wp_unslash( $_POST );

		$target_locations = Target_Rules_Fields::get_format_rule_value( $post_data, 'pixeccte-target-rules-location' );
		$target_exclusion = Target_Rules_Fields::get_format_rule_value( $post_data, 'pixeccte-theme-target-rules-exclusion' );

		update_post_meta( $post_id, 'pixeccte_hf_target_include_locations', $target_locations );
		update_post_meta( $post_id, 'pixeccte_hf_target_exclude_locations', $target_exclusion );

		if ( isset( $post_data['pixeccte_hf_template_type'] ) ) {
			$template_type = sanitize_text_field( $post_data['pixeccte_hf_template_type'] );
			$allowed_types = array_keys( \PixelsCoreCreativeToolsForElementor\Plugin::get_theme_template_types() );

			if ( '' !== $template_type && ! in_array( $template_type, $allowed_types, true ) ) {
				$template_type = '';
			}

			pixeccte_update_theme_template_type( $post_id, $template_type );

			if ( 'type_popup' === $template_type ) {
				update_post_meta( $post_id, '_wp_page_template', 'elementor_canvas' );
			}
		}
	}


	/**
	 * Display notice when editing the header or footer when there is one more of similar layout is active on the site.
	 */
	public function location_notice() {
		global $pagenow;
		global $post;

		if ( 'post.php' !== $pagenow || ! is_object( $post ) || 'pixeccte-theme' !== $post->post_type ) :
			return;
		endif;

		$template_type = pixeccte_get_theme_template_type( $post->ID );

		if ( '' !== $template_type ) :
			$templates = \PixelsCoreCreativeToolsForElementor\Theme_Builder\Theme_Elementor::get_template_id( $template_type );

			// Check if more than one template is selected for current template type.
			if ( is_array( $templates ) && isset( $templates[1] ) && (int) $post->ID !== (int) $templates[0] ) :
				$post_title        = get_the_title( $templates[0] );
				$template_location = $this->template_location( $template_type );
				$message           = sprintf(
					/* translators: 1: Post title, 2: Template location */
					__( 'Template "%1$s" is already assigned to the location "%2$s".', 'pixels-core-creative-tools-for-elementor' ),
					$post_title,
					$template_location
				);

				printf( '<div class="error"><p>%s</p></div>', esc_html( $message ) );
			endif;
		endif;
	}

	/**
	 * Convert the Template name to be added in the notice.
	 *
	 * @param mixed $template_type Template type.
	 */
	public function template_location( $template_type ) {
		$labels = array(
			'type_header'       => __( 'Header', 'pixels-core-creative-tools-for-elementor' ),
			'type_footer'       => __( 'Footer', 'pixels-core-creative-tools-for-elementor' ),
			'type_archive_post' => __( 'Archive Post', 'pixels-core-creative-tools-for-elementor' ),
			'type_404'          => __( '404', 'pixels-core-creative-tools-for-elementor' ),
			'type_single_post'  => __( 'Single Post', 'pixels-core-creative-tools-for-elementor' ),
			'type_popup'        => __( 'Popup', 'pixels-core-creative-tools-for-elementor' ),
			'type_mega_menu'    => __( 'Mega Menu', 'pixels-core-creative-tools-for-elementor' ),
		);

		if ( isset( $labels[ $template_type ] ) ) {
			return $labels[ $template_type ];
		}

		return ucfirst( str_replace( 'type_', '', (string) $template_type ) );
	}

	/**
	 * Don't display the elementor header footer & blocks templates on the frontend for non edit_posts capable users.
	 */
	public function block_template_frontend() {
		if ( is_singular( 'pixeccte-theme' ) && ! current_user_can( 'edit_posts' ) ) :
			wp_safe_redirect( site_url(), 301 );
			exit;
		endif;
	}

	/**
	 * Set shortcode column for template list.
	 *
	 * @param array $columns template list columns.
	 */
	public function set_shortcode_columns( $columns ) {
		$date_column = $columns['date'];

		unset( $columns['date'] );

		$columns['shortcode'] = esc_html__( 'Shortcode', 'pixels-core-creative-tools-for-elementor' );
		$columns['type']      = esc_html__( 'Type', 'pixels-core-creative-tools-for-elementor' );
		$columns['date']      = $date_column;

		return $columns;
	}

	/**
	 * Display shortcode in template list column.
	 *
	 * @param array $column template list column.
	 * @param int   $post_id post id.
	 */
	public function render_shortcode_column( $column, $post_id ) {
		switch ( $column ) {
			case 'shortcode':
				?>
				<span class="pixeccte-theme-shortcode-col-wrap">
					<input type="text" onfocus="this.select();" readonly="readonly" value="[pixeccte_hf_template id='<?php echo esc_attr( $post_id ); ?>']" class="pixeccte-theme-large-text code">
				</span>
				<?php
				break;
			case 'type':
				$type = pixeccte_get_theme_template_type( $post_id );

				if ( ! empty( $type ) ) {
					$label   = $this->template_location( $type );
					$classes = array(
						'type_header'       => 'hf-type-header',
						'type_footer'       => 'hf-type-footer',
						'type_archive_post' => 'hf-type-archive-post',
						'type_404'          => 'hf-type-404',
						'type_single_post'  => 'hf-type-single-post',
						'type_popup'        => 'hf-type-popup',
						'type_mega_menu'    => 'hf-type-mega-menu',
					);
					$class   = $classes[ $type ] ?? 'hf-type-default';

					printf(
						'<span class="hf-type-label %1$s">%2$s</span>',
						esc_attr( $class ),
						esc_html( $label )
					);
				} else {
					echo '<em>&mdash;</em>';
				}

				break;
		}
	}

	/**
	 * Pixeccte hf filter post type by meta.
	 *
	 * @param mixed $query Query.
	 */
	public function pixeccte_hf_filter_post_type_by_meta( $query ) {
		global $pagenow;

		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Read-only admin list table filter; no state change.
		$post_type  = isset( $_GET['post_type'] ) ? sanitize_key( wp_unslash( $_GET['post_type'] ) ) : '';
		$meta_value = isset( $_GET['filter'] ) ? sanitize_key( wp_unslash( $_GET['filter'] ) ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		if ( is_admin() && 'edit.php' === $pagenow && 'pixeccte-theme' === $post_type && ! empty( $meta_value ) ) {
			$query->query_vars['meta_key']   = pixeccte_get_theme_template_type_meta_key(); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$query->query_vars['meta_value'] = $meta_value; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		}
	}

	/**
	 * Pixeccte admin theme builder tabs.
	 *
	 * @param mixed $views Views.
	 */
	public function pixeccte_admin_theme_builder_tabs( $views ) {
		global $typenow;

		if ( 'pixeccte-theme' === $typenow ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin list table filter; no state change.
			$current_tab = isset( $_GET['filter'] ) ? sanitize_key( wp_unslash( $_GET['filter'] ) ) : 'all';
			$types       = \PixelsCoreCreativeToolsForElementor\Plugin::get_theme_template_types();
			?>
			<div class="nav-tab-wrapper">
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=pixeccte-theme' ) ); ?>" class="nav-tab <?php echo 'all' === $current_tab ? 'nav-tab-active' : ''; ?>" data-template-type=""><?php esc_html_e( 'All', 'pixels-core-creative-tools-for-elementor' ); ?></a>
				<?php foreach ( $types as $type_key => $type_label ) : ?>
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=pixeccte-theme&filter=' . rawurlencode( $type_key ) ) ); ?>" class="nav-tab <?php echo $current_tab === $type_key ? 'nav-tab-active' : ''; ?>" data-template-type="<?php echo esc_attr( $type_key ); ?>"><?php echo esc_html( $type_label ); ?></a>
				<?php endforeach; ?>
			</div>
			<?php
		}

		return $views;
	}

	/**
	 * Register preview metabox.
	 */
	public function register_preview_metabox() {
		global $post;

		if ( ! $post || 'pixeccte-theme' !== $post->post_type ) {
			return;
		}

		$template_type = pixeccte_get_theme_template_type( $post->ID );

		if ( ! in_array( $template_type, array( 'type_single_post', 'type_archive_post' ), true ) ) {
			return;
		}

		add_meta_box(
			'pixeccte-theme-preview-meta-box',
			__( 'Preview Settings', 'pixels-core-creative-tools-for-elementor' ),
			array( $this, 'render_preview_metabox' ),
			'pixeccte-theme',
			'side',
			'default'
		);
	}

	/**
	 * Render preview metabox.
	 *
	 * @param mixed $post Post.
	 */
	public function render_preview_metabox( $post ) {
		$preview_post_id = (int) get_post_meta( $post->ID, '_pixeccte_preview_post_id', true );
		$template_type   = pixeccte_get_theme_template_type( $post->ID );
		wp_nonce_field( 'pixeccte_hf_preview_meta', 'pixeccte_hf_preview_meta' );

		$posts_by_type     = pixeccte_get_theme_builder_preview_posts_by_type();
		$post_type_objects = get_post_types(
			array(
				'name'   => array_keys( $posts_by_type ),
				'public' => true,
			),
			'objects'
		);
		?>
		<p>
			<?php
			if ( 'type_single_post' === $template_type ) {
				esc_html_e( 'Choose a post to preview dynamic widgets and the Elementor live preview.', 'pixels-core-creative-tools-for-elementor' );
			} else {
				esc_html_e( 'Choose a post to determine which archive page is used for preview.', 'pixels-core-creative-tools-for-elementor' );
			}
			?>
		</p>
		<p><label for="pixeccte_preview_post_id"><?php esc_html_e( 'Preview with post:', 'pixels-core-creative-tools-for-elementor' ); ?></label></p>
		<select name="pixeccte_preview_post_id" id="pixeccte_preview_post_id" class="widefat">
			<option value=""><?php esc_html_e( 'Select a post', 'pixels-core-creative-tools-for-elementor' ); ?></option>
			<?php
			foreach ( $posts_by_type as $type => $type_posts ) :
				$label = isset( $post_type_objects[ $type ] ) ? $post_type_objects[ $type ]->labels->singular_name : ucfirst( $type );
				?>
				<optgroup label="<?php echo esc_attr( $label ); ?>">
					<?php foreach ( $type_posts as $p ) : ?>
						<option value="<?php echo esc_attr( $p->ID ); ?>" <?php selected( $preview_post_id, $p->ID ); ?>>
							<?php echo esc_html( $p->post_title ); ?>
						</option>
					<?php endforeach; ?>
				</optgroup>
				<?php
			endforeach;
			?>
		</select>
		<?php
	}

	/**
	 * Save preview meta.
	 *
	 * @param mixed $post_id Post id.
	 */
	public function save_preview_meta( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! isset( $_POST['pixeccte_hf_preview_meta'] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['pixeccte_hf_preview_meta'] ) ), 'pixeccte_hf_preview_meta' )
		) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$preview_post_id = isset( $_POST['pixeccte_preview_post_id'] ) ? absint( wp_unslash( $_POST['pixeccte_preview_post_id'] ) ) : 0;

		if ( $preview_post_id > 0 ) {
			update_post_meta( $post_id, '_pixeccte_preview_post_id', $preview_post_id );
		} else {
			delete_post_meta( $post_id, '_pixeccte_preview_post_id' );
		}
	}
}

Admin::instance();
