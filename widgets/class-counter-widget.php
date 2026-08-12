<?php
/**
 * Counter widget.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */

namespace PixelsCoreCreativeToolsForElementor\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Counter widget.
 *
 * @package PixelsCoreCreativeToolsForElementor
 */
class Counter_Widget extends Widget_Base {

	use Widget_Assets_Trait;

	/**
	 * Get name.
	 *
	 * @return string Result.
	 */
	public function get_name(): string {
		return 'pixeccte-counter';
	}

	/**
	 * Get title.
	 *
	 * @return string Result.
	 */
	public function get_title(): string {
		return esc_html__( 'Counter', 'pixels-core-creative-tools-for-elementor' );
	}

	/**
	 * Get icon.
	 *
	 * @return string Result.
	 */
	public function get_icon(): string {
		return 'pixeccte-icon pixeccte-icon-counter';
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
		return array( 'counter', 'number', 'stats', 'animate', 'pixeccte', 'number flow' );
	}

	/**
	 * Get assets slug.
	 *
	 * @return string Result.
	 */
	protected function get_assets_slug(): string {
		return 'counter';
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
			'section_counter',
			array(
				'label' => esc_html__( 'Counter', 'pixels-core-creative-tools-for-elementor' ),
			)
		);

		$this->add_control(
			'starting_number',
			array(
				'label'   => esc_html__( 'Starting Number', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'ending_number',
			array(
				'label'   => esc_html__( 'Ending Number', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 100,
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'prefix',
			array(
				'label'   => esc_html__( 'Number Prefix', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'suffix',
			array(
				'label'   => esc_html__( 'Number Suffix', 'pixels-core-creative-tools-for-elementor' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'duration',
			array(
				'label'   => esc_html__( 'Animation Duration', 'pixels-core-creative-tools-for-elementor' ) . ' (ms)',
				'type'    => Controls_Manager::NUMBER,
				'default' => 2000,
				'min'     => 100,
				'step'    => 100,
			)
		);

		$this->add_control(
			'thousand_separator',
			array(
				'label'        => esc_html__( 'Thousand Separator', 'pixels-core-creative-tools-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Show', 'pixels-core-creative-tools-for-elementor' ),
				'label_off'    => esc_html__( 'Hide', 'pixels-core-creative-tools-for-elementor' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'pixels-core-creative-tools-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__( 'Cool Number', 'pixels-core-creative-tools-for-elementor' ),
				'separator'   => 'before',
				'dynamic'     => array(
					'active' => true,
				),
			)
		);

		$this->add_control(
			'title_tag',
			array(
				'label'     => esc_html__( 'Title HTML Tag', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
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
				'default'   => 'div',
				'condition' => array(
					'title!' => '',
				),
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'     => esc_html__( 'Layout', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'stacked-before',
				'options'   => array(
					'stacked-before' => esc_html__( 'Stacked — Title Top', 'pixels-core-creative-tools-for-elementor' ),
					'stacked-after'  => esc_html__( 'Stacked — Title Bottom', 'pixels-core-creative-tools-for-elementor' ),
					'inline-start'   => esc_html__( 'Inline — Title Start', 'pixels-core-creative-tools-for-elementor' ),
					'inline-end'     => esc_html__( 'Inline — Title End', 'pixels-core-creative-tools-for-elementor' ),
				),
				'condition' => array(
					'title!' => '',
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
			'section_counter_style',
			array(
				'label' => esc_html__( 'Counter', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'                => esc_html__( 'Alignment', 'pixels-core-creative-tools-for-elementor' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => array(
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
				'default'              => 'center',
				'selectors_dictionary' => array(
					'left'   => '--pixeccte-counter-align: flex-start; --pixeccte-counter-justify: flex-start;',
					'center' => '--pixeccte-counter-align: center; --pixeccte-counter-justify: center;',
					'right'  => '--pixeccte-counter-align: flex-end; --pixeccte-counter-justify: flex-end;',
				),
				'selectors'            => array(
					'{{WRAPPER}} .pixeccte-counter' => '{{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'title_vertical_alignment',
			array(
				'label'     => esc_html__( 'Title Vertical Alignment', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Top', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center'     => array(
						'title' => esc_html__( 'Middle', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'Bottom', 'pixels-core-creative-tools-for-elementor' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-counter' => 'align-items: {{VALUE}};',
				),
				'condition' => array(
					'title!' => '',
					'layout' => array( 'inline-start', 'inline-end' ),
				),
			)
		);

		$this->add_responsive_control(
			'counter_gap',
			array(
				'label'      => esc_html__( 'Gap', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-counter' => 'gap: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'title!' => '',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_number_style',
			array(
				'label' => esc_html__( 'Number', 'pixels-core-creative-tools-for-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'number_color',
			array(
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-counter__number-wrapper' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'number_typography',
				'selector' => '{{WRAPPER}} .pixeccte-counter__number-wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			array(
				'name'     => 'number_text_stroke',
				'selector' => '{{WRAPPER}} .pixeccte-counter__number-wrapper, {{WRAPPER}} .pixeccte-counter__flow, {{WRAPPER}} .pixeccte-counter__value--fallback, {{WRAPPER}} .pixeccte-counter__prefix, {{WRAPPER}} .pixeccte-counter__suffix',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'number_text_shadow',
				'selector' => '{{WRAPPER}} .pixeccte-counter__number-wrapper',
			)
		);

		$this->add_responsive_control(
			'number_gap',
			array(
				'label'      => esc_html__( 'Prefix / Suffix Gap', 'pixels-core-creative-tools-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'rem' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .pixeccte-counter__number-wrapper' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			array(
				'label'     => esc_html__( 'Title', 'pixels-core-creative-tools-for-elementor' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'title!' => '',
				),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Color', 'pixels-core-creative-tools-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pixeccte-counter__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .pixeccte-counter__title',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			array(
				'name'     => 'title_text_stroke',
				'selector' => '{{WRAPPER}} .pixeccte-counter__title',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'title_text_shadow',
				'selector' => '{{WRAPPER}} .pixeccte-counter__title',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get integer digit count.
	 *
	 * @param float $number Number value.
	 */
	private function get_integer_digit_count( float $number ): int {
		$int_part = (int) abs( floor( $number ) );

		if ( 0 === $int_part ) {
			return 1;
		}

		return strlen( (string) $int_part );
	}

	/**
	 * Format counter display.
	 *
	 * @param float $value          Number value.
	 * @param int   $integer_digits Minimum integer digit count.
	 * @param int   $decimals       Decimal places.
	 */
	private function format_counter_display( float $value, int $integer_digits, int $decimals ): string {
		$formatted = number_format( abs( $value ), $decimals, '.', '' );

		if ( $decimals > 0 ) {
			$parts    = explode( '.', $formatted );
			$parts[0] = str_pad( $parts[0], $integer_digits, '0', STR_PAD_LEFT );

			return implode( '.', $parts );
		}

		return str_pad( (string) (int) $value, $integer_digits, '0', STR_PAD_LEFT );
	}

	/**
	 * Get decimal places.
	 *
	 * @param float|int|string $number Number value.
	 */
	private function get_decimal_places( $number ): int {
		$parts = explode( '.', (string) $number );

		if ( count( $parts ) < 2 ) {
			return 0;
		}

		return strlen( rtrim( $parts[1], '0' ) );
	}

	/**
	 * Get counter data.
	 *
	 * @param array $settings Settings.
	 * @return array<string, mixed>
	 */
	private function get_counter_data( array $settings ): array {
		$start = isset( $settings['starting_number'] ) ? (float) $settings['starting_number'] : 0;
		$end   = isset( $settings['ending_number'] ) ? (float) $settings['ending_number'] : 0;

		return array(
			'start'              => $start,
			'end'                => $end,
			'duration'           => max( 100, (int) ( $settings['duration'] ?? 2000 ) ),
			'prefix'             => (string) ( $settings['prefix'] ?? '' ),
			'suffix'             => (string) ( $settings['suffix'] ?? '' ),
			'thousand_separator' => 'yes' === ( $settings['thousand_separator'] ?? '' ) ? 'yes' : '',
			'decimals'           => max( $this->get_decimal_places( $start ), $this->get_decimal_places( $end ) ),
			'integer_digits'     => max( $this->get_integer_digit_count( $start ), $this->get_integer_digit_count( $end ) ),
		);
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$data     = $this->get_counter_data( $settings );
		$title    = $settings['title'] ?? '';

		$layout = $settings['layout'] ?? 'stacked-before';

		if ( '' === $title ) {
			$layout = 'stacked-before';
		}

		$this->add_render_attribute( 'wrapper', 'class', 'pixeccte-counter' );
		$this->add_render_attribute( 'wrapper', 'class', 'pixeccte-counter--layout-' . sanitize_html_class( $layout ) );
		$this->add_render_attribute( 'number_wrapper', 'class', 'pixeccte-counter__number-wrapper' );
		$this->add_render_attribute( 'flow', 'class', 'pixeccte-counter__flow' );
		$this->add_render_attribute( 'flow', 'data-start', (string) $data['start'] );
		$this->add_render_attribute( 'flow', 'data-end', (string) $data['end'] );
		$this->add_render_attribute( 'flow', 'data-duration', (string) $data['duration'] );
		$this->add_render_attribute( 'flow', 'data-prefix', $data['prefix'] );
		$this->add_render_attribute( 'flow', 'data-suffix', $data['suffix'] );
		$this->add_render_attribute( 'flow', 'data-thousand-separator', $data['thousand_separator'] );
		$this->add_render_attribute( 'flow', 'data-decimals', (string) $data['decimals'] );
		$this->add_render_attribute( 'flow', 'data-integer-digits', (string) $data['integer_digits'] );

		if ( ! empty( $data['prefix'] ) ) {
			$this->add_render_attribute( 'prefix', 'class', 'pixeccte-counter__prefix' );
		}

		if ( ! empty( $data['suffix'] ) ) {
			$this->add_render_attribute( 'suffix', 'class', 'pixeccte-counter__suffix' );
		}

		if ( '' !== $title ) {
			$this->add_render_attribute( 'title', 'class', 'pixeccte-counter__title' );
			$this->add_inline_editing_attributes( 'title' );
		}

		$display_start = $this->format_counter_display( 0, $data['integer_digits'], $data['decimals'] );

		$title_tag = Utils::validate_html_tag( $settings['title_tag'] ?? 'div' );
		?>
		<div <?php $this->print_render_attribute_string( 'wrapper' ); ?>>
			<?php if ( '' !== $title ) : ?>
				<<?php Utils::print_validated_html_tag( $title_tag ); ?> <?php $this->print_render_attribute_string( 'title' ); ?>>
					<?php echo wp_kses_post( $title ); ?>
				</<?php Utils::print_validated_html_tag( $title_tag ); ?>>
			<?php endif; ?>

			<div <?php $this->print_render_attribute_string( 'number_wrapper' ); ?>>
				<?php if ( ! empty( $data['prefix'] ) ) : ?>
					<span <?php $this->print_render_attribute_string( 'prefix' ); ?>><?php echo esc_html( $data['prefix'] ); ?></span>
				<?php endif; ?>

				<number-flow <?php $this->print_render_attribute_string( 'flow' ); ?>><?php echo esc_html( $display_start ); ?></number-flow>

				<span class="pixeccte-counter__value pixeccte-counter__value--fallback" aria-live="polite">
					<?php echo esc_html( $display_start ); ?>
				</span>

				<?php if ( ! empty( $data['suffix'] ) ) : ?>
					<span <?php $this->print_render_attribute_string( 'suffix' ); ?>><?php echo esc_html( $data['suffix'] ); ?></span>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
