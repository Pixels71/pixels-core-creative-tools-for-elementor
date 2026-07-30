<?php
namespace PixelsElementorAddons\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Text_Stroke;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Counter_Widget extends Widget_Base {

	use Widget_Assets_Trait;

	public function get_name(): string {
		return 'pixels-counter';
	}

	public function get_title(): string {
		return esc_html__( 'Counter', 'pixels-elementor-addons' );
	}

	public function get_icon(): string {
		return 'pixels-icon pixels-icon-counter';
	}

	public function get_categories(): array {
		return [ 'pixels-core' ];
	}

	public function get_keywords(): array {
		return [ 'counter', 'number', 'stats', 'animate', 'pixels', 'number flow' ];
	}

	protected function get_assets_slug(): string {
		return 'counter';
	}

	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	private function register_content_controls(): void {
		$this->start_controls_section(
			'section_counter',
			[
				'label' => esc_html__( 'Counter', 'pixels-elementor-addons' ),
			]
		);

		$this->add_control(
			'starting_number',
			[
				'label'   => esc_html__( 'Starting Number', 'pixels-elementor-addons' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 0,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'ending_number',
			[
				'label'   => esc_html__( 'Ending Number', 'pixels-elementor-addons' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 100,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'prefix',
			[
				'label'   => esc_html__( 'Number Prefix', 'pixels-elementor-addons' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'suffix',
			[
				'label'   => esc_html__( 'Number Suffix', 'pixels-elementor-addons' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'duration',
			[
				'label'   => esc_html__( 'Animation Duration', 'pixels-elementor-addons' ) . ' (ms)',
				'type'    => Controls_Manager::NUMBER,
				'default' => 2000,
				'min'     => 100,
				'step'    => 100,
			]
		);

		$this->add_control(
			'thousand_separator',
			[
				'label'        => esc_html__( 'Thousand Separator', 'pixels-elementor-addons' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => esc_html__( 'Show', 'pixels-elementor-addons' ),
				'label_off'    => esc_html__( 'Hide', 'pixels-elementor-addons' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => esc_html__( 'Title', 'pixels-elementor-addons' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => esc_html__( 'Cool Number', 'pixels-elementor-addons' ),
				'separator'   => 'before',
				'dynamic'     => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label'     => esc_html__( 'Title HTML Tag', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				],
				'default'   => 'div',
				'condition' => [
					'title!' => '',
				],
			]
		);

		$this->add_control(
			'layout',
			[
				'label'   => esc_html__( 'Layout', 'pixels-elementor-addons' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'stacked-before',
				'options' => [
					'stacked-before' => esc_html__( 'Stacked — Title Top', 'pixels-elementor-addons' ),
					'stacked-after'  => esc_html__( 'Stacked — Title Bottom', 'pixels-elementor-addons' ),
					'inline-start'   => esc_html__( 'Inline — Title Start', 'pixels-elementor-addons' ),
					'inline-end'     => esc_html__( 'Inline — Title End', 'pixels-elementor-addons' ),
				],
				'condition' => [
					'title!' => '',
				],
			]
		);

		$this->end_controls_section();
	}

	private function register_style_controls(): void {
		$this->start_controls_section(
			'section_counter_style',
			[
				'label' => esc_html__( 'Counter', 'pixels-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'                => esc_html__( 'Alignment', 'pixels-elementor-addons' ),
				'type'                 => Controls_Manager::CHOOSE,
				'options'              => [
					'left'   => [
						'title' => esc_html__( 'Left', 'pixels-elementor-addons' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'pixels-elementor-addons' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'pixels-elementor-addons' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'              => 'center',
				'selectors_dictionary' => [
					'left'   => '--pixels-counter-align: flex-start; --pixels-counter-justify: flex-start;',
					'center' => '--pixels-counter-align: center; --pixels-counter-justify: center;',
					'right'  => '--pixels-counter-align: flex-end; --pixels-counter-justify: flex-end;',
				],
				'selectors'            => [
					'{{WRAPPER}} .pixels-core-counter' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'title_vertical_alignment',
			[
				'label'     => esc_html__( 'Title Vertical Alignment', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => esc_html__( 'Top', 'pixels-elementor-addons' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center'     => [
						'title' => esc_html__( 'Middle', 'pixels-elementor-addons' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'flex-end'   => [
						'title' => esc_html__( 'Bottom', 'pixels-elementor-addons' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .pixels-core-counter' => 'align-items: {{VALUE}};',
				],
				'condition' => [
					'title!' => '',
					'layout' => [ 'inline-start', 'inline-end' ],
				],
			]
		);

		$this->add_responsive_control(
			'counter_gap',
			[
				'label'      => esc_html__( 'Gap', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-counter' => 'gap: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'title!' => '',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_number_style',
			[
				'label' => esc_html__( 'Number', 'pixels-elementor-addons' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'number_color',
			[
				'label'     => esc_html__( 'Color', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-counter__number-wrapper' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'number_typography',
				'selector' => '{{WRAPPER}} .pixels-core-counter__number-wrapper',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name'     => 'number_text_stroke',
				'selector' => '{{WRAPPER}} .pixels-core-counter__number-wrapper, {{WRAPPER}} .pixels-core-counter__flow, {{WRAPPER}} .pixels-core-counter__value--fallback, {{WRAPPER}} .pixels-core-counter__prefix, {{WRAPPER}} .pixels-core-counter__suffix',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'number_text_shadow',
				'selector' => '{{WRAPPER}} .pixels-core-counter__number-wrapper',
			]
		);

		$this->add_responsive_control(
			'number_gap',
			[
				'label'      => esc_html__( 'Prefix / Suffix Gap', 'pixels-elementor-addons' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .pixels-core-counter__number-wrapper' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			[
				'label'     => esc_html__( 'Title', 'pixels-elementor-addons' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'title!' => '',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Color', 'pixels-elementor-addons' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pixels-core-counter__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .pixels-core-counter__title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Stroke::get_type(),
			[
				'name'     => 'title_text_stroke',
				'selector' => '{{WRAPPER}} .pixels-core-counter__title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'title_text_shadow',
				'selector' => '{{WRAPPER}} .pixels-core-counter__title',
			]
		);

		$this->end_controls_section();
	}

	/**
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
	 * @param array<string, mixed> $settings Widget settings.
	 * @return array<string, mixed>
	 */
	private function get_counter_data( array $settings ): array {
		$start = isset( $settings['starting_number'] ) ? (float) $settings['starting_number'] : 0;
		$end   = isset( $settings['ending_number'] ) ? (float) $settings['ending_number'] : 0;

		return [
			'start'              => $start,
			'end'                => $end,
			'duration'           => max( 100, (int) ( $settings['duration'] ?? 2000 ) ),
			'prefix'             => (string) ( $settings['prefix'] ?? '' ),
			'suffix'             => (string) ( $settings['suffix'] ?? '' ),
			'thousand_separator' => 'yes' === ( $settings['thousand_separator'] ?? '' ) ? 'yes' : '',
			'decimals'           => max( $this->get_decimal_places( $start ), $this->get_decimal_places( $end ) ),
			'integer_digits'     => max( $this->get_integer_digit_count( $start ), $this->get_integer_digit_count( $end ) ),
		];
	}

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$data     = $this->get_counter_data( $settings );
		$title    = $settings['title'] ?? '';

		$layout = $settings['layout'] ?? 'stacked-before';

		if ( '' === $title ) {
			$layout = 'stacked-before';
		}

		$this->add_render_attribute( 'wrapper', 'class', 'pixels-core-counter' );
		$this->add_render_attribute( 'wrapper', 'class', 'pixels-core-counter--layout-' . sanitize_html_class( $layout ) );
		$this->add_render_attribute( 'number_wrapper', 'class', 'pixels-core-counter__number-wrapper' );
		$this->add_render_attribute( 'flow', 'class', 'pixels-core-counter__flow' );
		$this->add_render_attribute( 'flow', 'data-start', (string) $data['start'] );
		$this->add_render_attribute( 'flow', 'data-end', (string) $data['end'] );
		$this->add_render_attribute( 'flow', 'data-duration', (string) $data['duration'] );
		$this->add_render_attribute( 'flow', 'data-prefix', $data['prefix'] );
		$this->add_render_attribute( 'flow', 'data-suffix', $data['suffix'] );
		$this->add_render_attribute( 'flow', 'data-thousand-separator', $data['thousand_separator'] );
		$this->add_render_attribute( 'flow', 'data-decimals', (string) $data['decimals'] );
		$this->add_render_attribute( 'flow', 'data-integer-digits', (string) $data['integer_digits'] );

		if ( ! empty( $data['prefix'] ) ) {
			$this->add_render_attribute( 'prefix', 'class', 'pixels-core-counter__prefix' );
		}

		if ( ! empty( $data['suffix'] ) ) {
			$this->add_render_attribute( 'suffix', 'class', 'pixels-core-counter__suffix' );
		}

		if ( '' !== $title ) {
			$this->add_render_attribute( 'title', 'class', 'pixels-core-counter__title' );
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

				<span class="pixels-core-counter__value pixels-core-counter__value--fallback" aria-live="polite">
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
