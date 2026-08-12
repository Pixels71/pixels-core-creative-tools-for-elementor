<?php
/**
 * Heading widget.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Heading widget.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */
class Heading_Widget extends Widget_Base {

	use Widget_Assets_Trait;

	/**
	 * Get name.
	 *
	 * @return string Result.
	 */
	public function get_name(): string {
		return 'pixeccte-heading';
	}

	/**
	 * Get title.
	 *
	 * @return string Result.
	 */
	public function get_title(): string {
		return esc_html__( 'Heading', 'pixels-core-creative-tools-for-elementor' );
	}

	/**
	 * Get icon.
	 *
	 * @return string Result.
	 */
	public function get_icon(): string {
		return 'pixeccte-icon pixeccte-icon-heading';
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
		return array( 'heading', 'title', 'headline', 'pixeccte' );
	}

	/**
	 * Get assets slug.
	 *
	 * @return string Result.
	 */
	protected function get_assets_slug(): string {
		return 'heading';
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
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Heading', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Add Your Heading Text Here', 'pixels-core-creative-tools-for-elementor' ),
				'placeholder' => esc_html__( 'Enter your title', 'pixels-core-creative-tools-for-elementor' ),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'link',
			array(
				'label'       => esc_html__( 'Link', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'pixels-core-creative-tools-for-elementor' ),
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'show_subheading',
			array(
				'label'        => esc_html__( 'Subheading', 'pixels-core-creative-tools-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'pixels-core-creative-tools-for-elementor' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'subheading',
			array(
				'label'       => esc_html__( 'Subheading Text', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Optional subheading text goes here.', 'pixels-core-creative-tools-for-elementor' ),
				'placeholder' => esc_html__( 'Enter subheading', 'pixels-core-creative-tools-for-elementor' ),
				'dynamic'     => array(
					'active' => true,
				),
				'condition'   => array(
					'show_subheading' => 'yes',
				),
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'   => esc_html__( 'HTML Tag', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
				'default' => 'h2',
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => esc_html__( 'Alignment', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'    => array(
						'title' => esc_html__( 'Left', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => esc_html__( 'Center', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => esc_html__( 'Right', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => esc_html__( 'Justified', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'default'   => 'left',
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-heading' => 'text-align: {{VALUE}};',
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
			'section_style_title',
			array(
				'label' => esc_html__( 'Title', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-heading__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .pixeccte-heading__title',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'title_text_shadow',
				'selector' => '{{WRAPPER}} .pixeccte-heading__title',
			)
		);

		$this->add_responsive_control(
			'title_spacing',
			array(
				'label'      => esc_html__( 'Spacing', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-heading__title' => 'margin: 0 0 {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_subheading',
			array(
				'label'     => esc_html__( 'Subheading', 'pixels-core-creative-tools-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_subheading' => 'yes',
				),
			)
		);

		$this->add_control(
			'subheading_color',
			array(
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-heading__subheading' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'subheading_typography',
				'selector' => '{{WRAPPER}} .pixeccte-heading__subheading',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['title'] ) && ( empty( $settings['show_subheading'] ) || empty( $settings['subheading'] ) ) ) {
			return;
		}

		$this->add_render_attribute( 'wrapper', 'class', 'pixeccte-heading' );

		$title_tag = Utils::validate_html_tag( $settings['title_tag'] );

		$this->add_render_attribute( 'title', 'class', 'pixeccte-heading__title' );

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'title', $settings['link'] );
		}

		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( ! empty( $settings['title'] ) ) : ?>
				<?php if ( ! empty( $settings['link']['url'] ) ) : ?>
					<a <?php $this->print_render_attribute_string( 'title' ); ?>>
						<?php echo wp_kses_post( $settings['title'] ); ?>
					</a>
				<?php else : ?>
					<?php
					echo '<' . tag_escape( $title_tag ) . ' ';
					$this->print_render_attribute_string( 'title' );
					echo '>' . wp_kses_post( $settings['title'] ) . '</' . tag_escape( $title_tag ) . '>';
					?>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ( 'yes' === $settings['show_subheading'] && ! empty( $settings['subheading'] ) ) : ?>
				<p class="pixeccte-heading__subheading"><?php echo wp_kses_post( $settings['subheading'] ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Content template.
	 */
	protected function content_template(): void {
		?>
		<#
		const titleTag = elementor.helpers.validateHTMLTag( settings.title_tag ),
			hasLink = settings.link && settings.link.url,
			hasTitle = settings.title,
			hasSubheading = 'yes' === settings.show_subheading && settings.subheading;
		#>
		<# if ( hasTitle || hasSubheading ) { #>
			<div class="pixeccte-heading">
				<# if ( hasTitle ) { #>
					<# if ( hasLink ) { #>
						<a href="{{ settings.link.url }}" class="pixeccte-heading__title elementor-heading-title">{{{ settings.title }}}</a>
					<# } else { #>
						<{{ titleTag }} class="pixeccte-heading__title elementor-heading-title">{{{ settings.title }}}</{{ titleTag }}>
					<# } #>
				<# } #>
				<# if ( hasSubheading ) { #>
					<p class="pixeccte-heading__subheading">{{{ settings.subheading }}}</p>
				<# } #>
			</div>
		<# } #>
		<?php
	}
}
