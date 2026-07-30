<?php
namespace PixelsElementorAddons\Extensions;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use Elementor\Core\DynamicTags\Dynamic_CSS;
use Elementor\Core\Files\CSS\Post;
use Elementor\Element_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Per-element custom CSS in the Elementor Advanced tab.
 */
final class Custom_CSS {

	public static function init(): void {
		add_action( 'elementor/element/after_section_end', [ __CLASS__, 'register_controls' ], 10, 2 );
		add_action( 'elementor/element/parse_css', [ __CLASS__, 'add_post_css' ], 10, 2 );
	}

	public static function register_controls( Controls_Stack $element, string $section_id ): void {
		if ( 'section_custom_css_pro' !== $section_id ) {
			return;
		}

		$element->start_controls_section(
			'pixels_section_custom_css',
			[
				'label' => esc_html__( 'Custom CSS', 'pixels-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			]
		);

		$element->add_control(
			'pixels_custom_css',
			[
				'label'       => esc_html__( 'Add your own custom CSS', 'pixels-elementor-addons' ),
				'type'        => Controls_Manager::CODE,
				'description' => sprintf(
					/* translators: 1: Link opening tag, 2: Link opening tag, 3: Link closing tag. */
					esc_html__( 'Use %1$scustom CSS%3$s to style your content or add %2$sthe "selector" prefix%3$s to target specific elements.', 'pixels-elementor-addons' ),
					'<a href="https://go.elementor.com/learn-more-panel-custom-css/" target="_blank">',
					'<a href="https://go.elementor.com/learn-more-panel-custom-css-selectors/" target="_blank">',
					'</a>'
				),
				'language'    => 'css',
				'render_type' => 'ui',
			]
		);

		$element->end_controls_section();
	}

	public static function add_post_css( Post $post_css, Element_Base $element ): void {
		if ( $post_css instanceof Dynamic_CSS ) {
			return;
		}

		$element_settings = $element->get_settings();

		if ( empty( $element_settings['pixels_custom_css'] ) ) {
			return;
		}

		$css = trim( $element_settings['pixels_custom_css'] );

		if ( '' === $css ) {
			return;
		}

		$css = str_replace( 'selector', $post_css->get_element_unique_selector( $element ), $css );
		$css = sprintf(
			'/* Start custom CSS for %s, class: %s */%s/* End custom CSS */',
			$element->get_name(),
			$element->get_unique_selector(),
			$css
		);

		$post_css->get_stylesheet()->add_raw_css( $css );
	}
}
