<?php
/**
 * Site logo widget.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Utils;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Site logo widget.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */
class Site_Logo_Widget extends Widget_Base {

	use Widget_Assets_Trait;

	/**
	 * Get name.
	 *
	 * @return string Result.
	 */
	public function get_name(): string {
		return 'pixeccte-site-logo';
	}

	/**
	 * Get title.
	 *
	 * @return string Result.
	 */
	public function get_title(): string {
		return esc_html__( 'Site Logo', 'pixels-core-creative-tools-for-elementor' );
	}

	/**
	 * Get icon.
	 *
	 * @return string Result.
	 */
	public function get_icon(): string {
		return 'pixeccte-icon pixeccte-icon-site-logo';
	}

	/**
	 * Get categories.
	 *
	 * @return array Result.
	 */
	public function get_categories(): array {
		return array( 'pixeccte' );
	}

	/**
	 * Get keywords.
	 *
	 * @return array Result.
	 */
	public function get_keywords(): array {
		return array( 'logo', 'site', 'brand', 'identity', 'header', 'pixeccte' );
	}

	/**
	 * Get assets slug.
	 *
	 * @return string Result.
	 */
	protected function get_assets_slug(): string {
		return 'site_logo';
	}

	/**
	 * Get script depends.
	 *
	 * @return array Result.
	 */
	public function get_script_depends(): array {
		return array();
	}

	/**
	 * Register controls.
	 */
	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	/**
	 * Register content controls.
	 */
	private function register_content_controls(): void {
		$site_logo = $this->get_site_logo_data();

		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Logo', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'logo_source',
			array(
				'label'   => esc_html__( 'Logo Source', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'site',
				'options' => array(
					'site'   => esc_html__( 'Site Identity', 'pixels-core-creative-tools-for-elementor' ),
					'custom' => esc_html__( 'Custom Upload', 'pixels-core-creative-tools-for-elementor' ),
				),
			)
		);

		$this->add_control(
			'site_logo_notice',
			array(
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(
					/* translators: %s: Site Settings link label. */
					esc_html__( 'Uses the logo from Site Settings → Site Identity. %s', 'pixels-core-creative-tools-for-elementor' ),
					'<a href="#" onclick="elementor.panel.$el.find(\'.elementor-panel-menu-item-settings-site-identity\').trigger(\'click\'); return false;">' . esc_html__( 'Edit Site Logo', 'pixels-core-creative-tools-for-elementor' ) . '</a>'
				),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'logo_source' => 'site',
				),
			)
		);

		$this->add_control(
			'custom_logo',
			array(
				'label'     => esc_html__( 'Custom Logo', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'id'  => $site_logo['id'],
					'url' => ! empty( $site_logo['url'] ) ? $site_logo['url'] : Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'logo_source' => 'custom',
				),
				'dynamic'   => array(
					'active' => true,
				),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'    => 'logo',
				'default' => 'full',
				'exclude' => array( 'custom' ), // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude -- Elementor control option, not a WP_Query exclude.
			)
		);

		$this->add_control(
			'link_to_home',
			array(
				'label'        => esc_html__( 'Link to Home', 'pixels-core-creative-tools-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'pixels-core-creative-tools-for-elementor' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'link',
			array(
				'label'       => esc_html__( 'Custom Link', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'pixels-core-creative-tools-for-elementor' ),
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'link_to_home!' => 'yes',
				),
			)
		);

		$this->add_control(
			'caption',
			array(
				'label'        => esc_html__( 'Caption', 'pixels-core-creative-tools-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'pixels-core-creative-tools-for-elementor' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'caption_text',
			array(
				'label'       => esc_html__( 'Caption Text', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => get_bloginfo( 'name' ),
				'placeholder' => esc_html__( 'Site name', 'pixels-core-creative-tools-for-elementor' ),
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'caption' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => esc_html__( 'Alignment', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'left',
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-site-logo' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register style controls.
	 */
	private function register_style_controls(): void {
		$this->start_controls_section(
			'section_style_logo',
			array(
				'label' => esc_html__( 'Logo', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'width',
			array(
				'label'      => esc_html__( 'Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-site-logo__image' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'max_width',
			array(
				'label'      => esc_html__( 'Max Width', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'vw', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1000,
					),
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-site-logo__image' => 'max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'height',
			array(
				'label'      => esc_html__( 'Height', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh', 'custom' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-site-logo__image' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'object_fit',
			array(
				'label'     => esc_html__( 'Object Fit', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '',
				'options'   => array(
					''        => esc_html__( 'Default', 'pixels-core-creative-tools-for-elementor' ),
					'fill'    => esc_html__( 'Fill', 'pixels-core-creative-tools-for-elementor' ),
					'cover'   => esc_html__( 'Cover', 'pixels-core-creative-tools-for-elementor' ),
					'contain' => esc_html__( 'Contain', 'pixels-core-creative-tools-for-elementor' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-site-logo__image' => 'object-fit: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			array(
				'name'     => 'logo_css_filters',
				'selector' => '{{WRAPPER}} .pixeccte-site-logo__image',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'logo_border',
				'selector' => '{{WRAPPER}} .pixeccte-site-logo__image',
			)
		);

		$this->add_responsive_control(
			'logo_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-site-logo__image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'logo_box_shadow',
				'selector' => '{{WRAPPER}} .pixeccte-site-logo__image',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_caption',
			array(
				'label'     => esc_html__( 'Caption', 'pixels-core-creative-tools-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'caption' => 'yes',
				),
			)
		);

		$this->add_control(
			'caption_color',
			array(
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-site-logo__caption' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'caption_spacing',
			array(
				'label'      => esc_html__( 'Spacing', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-site-logo__caption' => 'margin-top: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			array(
				'label' => esc_html__( 'Site Title', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-site-logo__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .pixeccte-site-logo__title',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get site logo data.
	 *
	 * @return array{id: int, url: string}
	 */
	private function get_site_logo_data(): array {
		$logo_id = (int) get_theme_mod( 'custom_logo' );

		if ( ! $logo_id && class_exists( Plugin::class ) ) {
			$kit = Plugin::$instance->kits_manager->get_active_kit();

			if ( $kit ) {
				$kit_logo = $kit->get_settings( 'site_logo' );

				if ( ! empty( $kit_logo['id'] ) ) {
					$logo_id = (int) $kit_logo['id'];
				}
			}
		}

		if ( ! $logo_id ) {
			return array(
				'id'  => 0,
				'url' => '',
			);
		}

		$src = wp_get_attachment_image_src( $logo_id, 'full' );

		return array(
			'id'  => $logo_id,
			'url' => $src ? $src[0] : '',
		);
	}

	/**
	 * Get logo render settings.
	 *
	 * @param array $settings Settings.
	 * @return array<string, mixed>
	 */
	private function get_logo_render_settings( array $settings ): array {
		if ( 'custom' !== $settings['logo_source'] ) {
			$settings['custom_logo'] = $this->get_site_logo_data();
		}

		return $settings;
	}

	/**
	 * Get logo link attributes.
	 *
	 * @param array $settings Settings.
	 */
	private function get_logo_link_attributes( array $settings ): void {
		if ( 'yes' === $settings['link_to_home'] ) {
			$this->add_render_attribute( 'link', 'href', esc_url( home_url( '/' ) ) );
			return;
		}

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'link', $settings['link'] );
		}
	}

	/**
	 * Has logo image.
	 *
	 * @param array $logo Logo.
	 */
	private function has_logo_image( array $logo ): bool {
		if ( ! empty( $logo['id'] ) ) {
			return true;
		}

		if ( empty( $logo['url'] ) ) {
			return false;
		}

		return Utils::get_placeholder_image_src() !== $logo['url'];
	}

	/**
	 * Get site title.
	 *
	 * @return string Result.
	 */
	private function get_site_title(): string {
		if ( class_exists( Plugin::class ) ) {
			$kit = Plugin::$instance->kits_manager->get_active_kit();

			if ( $kit ) {
				$site_name = $kit->get_settings( 'site_name' );

				if ( ! empty( $site_name ) ) {
					return $site_name;
				}
			}
		}

		return (string) get_bloginfo( 'name' );
	}

	/**
	 * Render site title markup.
	 *
	 * @param array $settings Settings.
	 */
	private function render_site_title_markup( array $settings ): void {
		$site_title = $this->get_site_title();

		if ( '' === $site_title ) {
			return;
		}

		$this->add_render_attribute( 'title', 'class', 'pixeccte-site-logo__title' );

		$title_html = sprintf(
			'<span %1$s>%2$s</span>',
			$this->get_render_attribute_string( 'title' ),
			esc_html( $site_title )
		);

		$has_link = 'yes' === $settings['link_to_home'] || ! empty( $settings['link']['url'] );

		if ( $has_link ) {
			$this->get_logo_link_attributes( $settings );
			$this->add_render_attribute( 'link', 'class', 'pixeccte-site-logo__link' );
			echo '<a ';
			$this->print_render_attribute_string( 'link' );
			echo '>' . wp_kses_post( $title_html ) . '</a>';
			return;
		}

		echo wp_kses_post( $title_html );
	}

	/**
	 * Render logo markup.
	 *
	 * @param array $settings Settings.
	 */
	private function render_logo_markup( array $settings ): void {
		$logo_settings = $this->get_logo_render_settings( $settings );
		$logo          = $logo_settings['custom_logo'] ?? array();

		if ( ! $this->has_logo_image( $logo ) ) {
			$this->render_site_title_markup( $settings );
			return;
		}

		$image_html = Group_Control_Image_Size::get_attachment_image_html( $logo_settings, 'logo', 'custom_logo' );

		if ( empty( $image_html ) && ! empty( $logo['url'] ) ) {
			$alt = get_bloginfo( 'name' );

			$image_html = sprintf(
				'<img src="%1$s" alt="%2$s" class="pixeccte-site-logo__image" loading="lazy" />',
				esc_url( $logo['url'] ),
				esc_attr( $alt )
			);
		} else {
			$this->add_render_attribute( 'image', 'class', 'pixeccte-site-logo__image' );
			$image_html = str_replace( '<img ', '<img ' . $this->get_render_attribute_string( 'image' ) . ' ', $image_html );
		}

		$has_link = 'yes' === $settings['link_to_home'] || ! empty( $settings['link']['url'] );

		if ( $has_link ) {
			$this->get_logo_link_attributes( $settings );
			$this->add_render_attribute( 'link', 'class', 'pixeccte-site-logo__link' );
			echo '<a ';
			$this->print_render_attribute_string( 'link' );
			echo '>' . wp_kses_post( $image_html ) . '</a>';
		} else {
			echo wp_kses_post( $image_html );
		}

		if ( 'yes' === $settings['caption'] && ! empty( $settings['caption_text'] ) ) {
			printf(
				'<div class="pixeccte-site-logo__caption">%s</div>',
				esc_html( $settings['caption_text'] )
			);
		}
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', 'pixeccte-site-logo' );

		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php $this->render_logo_markup( $settings ); ?>
		</div>
		<?php
	}

	/**
	 * Content template.
	 */
	protected function content_template(): void {
		?>
		<#
		const hasCustomLogo = settings.custom_logo && settings.custom_logo.url,
			hasLink = 'yes' === settings.link_to_home || ( settings.link && settings.link.url ),
			showCaption = 'yes' === settings.caption && settings.caption_text,
			linkUrl = 'yes' === settings.link_to_home ? '<?php echo esc_url( home_url( '/' ) ); ?>' : ( settings.link ? settings.link.url : '' ),
			placeholderUrl = '<?php echo esc_url( Utils::get_placeholder_image_src() ); ?>',
			siteTitle = ( elementor.config.document.settings.settings && elementor.config.document.settings.settings.site_name )
				? elementor.config.document.settings.settings.site_name
				: '<?php echo esc_js( get_bloginfo( 'name' ) ); ?>';

		let imageUrl = '';

		if ( 'custom' === settings.logo_source ) {
			imageUrl = hasCustomLogo ? settings.custom_logo.url : '';
		} else if ( elementor.config.document.settings.settings && elementor.config.document.settings.settings.site_logo ) {
			imageUrl = elementor.config.document.settings.settings.site_logo.url || '';
		}

		if ( imageUrl === placeholderUrl ) {
			imageUrl = '';
		}
		#>
		<div class="pixeccte-site-logo">
			<# if ( imageUrl ) { #>
				<# if ( hasLink && linkUrl ) { #>
					<a href="{{ linkUrl }}" class="pixeccte-site-logo__link">
						<img src="{{ imageUrl }}" alt="" class="pixeccte-site-logo__image" />
					</a>
				<# } else { #>
					<img src="{{ imageUrl }}" alt="" class="pixeccte-site-logo__image" />
				<# } #>
			<# } else if ( siteTitle ) { #>
				<# if ( hasLink && linkUrl ) { #>
					<a href="{{ linkUrl }}" class="pixeccte-site-logo__link">
						<span class="pixeccte-site-logo__title">{{{ siteTitle }}}</span>
					</a>
				<# } else { #>
					<span class="pixeccte-site-logo__title">{{{ siteTitle }}}</span>
				<# } #>
			<# } #>

			<# if ( showCaption ) { #>
				<div class="pixeccte-site-logo__caption">{{{ settings.caption_text }}}</div>
			<# } #>
		</div>
		<?php
	}
}
