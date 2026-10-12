<?php
/**
 * Subaru Spec Strip widget — icon + value + label stats with count-up.
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Repeater;

/**
 * Spec strip.
 */
class Subaru_Spec_Strip_Widget extends Subaru_Widget_Base {

	/**
	 * Name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'subaru-spec-strip';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return __( 'Subaru Spec Strip', 'subaru-alkhoory' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-counter';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => __( 'Specs', 'subaru-alkhoory' ) ) );
		$rep = new Repeater();
		$rep->add_control(
			'icon',
			array(
				'label' => __( 'Icon', 'subaru-alkhoory' ),
				'type'  => Controls_Manager::ICONS,
			)
		);
		$rep->add_control(
			'value',
			array(
				'label'       => __( 'Value', 'subaru-alkhoory' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '202 kW',
				'description' => __( 'Numbers count up (decimals and thousand separators are kept), e.g. "6.0 sec", "5,600 rpm".', 'subaru-alkhoory' ),
			)
		);
		$rep->add_control(
			'label',
			array(
				'label'   => __( 'Label', 'subaru-alkhoory' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Max. output',
			)
		);
		$this->add_control(
			'items',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $rep->get_controls(),
				'title_field' => '{{{ value }}} — {{{ label }}}',
				'default'     => array(
					array(
						'value' => '202 kW',
						'label' => 'Max. output (DIN)',
					),
					array(
						'value' => '6.0 sec',
						'label' => '0–100 km/h',
					),
					array(
						'value' => '350 Nm',
						'label' => 'Max. torque',
					),
					array(
						'value' => '4',
						'label' => 'Symmetrical AWD wheels',
					),
				),
			)
		);
		$this->add_control(
			'count_up',
			array(
				'label'        => __( 'Count-up animation', 'subaru-alkhoory' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'style',
			array(
				'label' => __( 'Layout', 'subaru-alkhoory' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_theme_control();
		$this->add_responsive_control(
			'columns',
			array(
				'label'     => __( 'Columns', 'subaru-alkhoory' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 6,
				'default'   => 4,
				'selectors' => array( '{{WRAPPER}} .sa-spec' => '--cols: {{VALUE}};' ),
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s     = $this->get_settings_for_display();
		$items = ! empty( $s['items'] ) ? $s['items'] : array();
		if ( ! $items ) {
			return;
		}
		echo '<ul class="sa-spec">';
		foreach ( $items as $i => $it ) {
			echo '<li class="sa-spec__item" data-sa-reveal style="--d:' . (int) ( $i * 100 ) . 'ms">';
			if ( ! empty( $it['icon']['value'] ) ) {
				echo '<span class="sa-spec__icon" aria-hidden="true">';
				Icons_Manager::render_icon( $it['icon'], array( 'aria-hidden' => 'true' ) );
				echo '</span>';
			}
			echo '<span class="sa-spec__value' . ( 'yes' === $s['count_up'] ? ' sub-counter' : '' ) . '">' . esc_html( $it['value'] ) . '</span>';
			echo '<span class="sa-spec__label">' . esc_html( $it['label'] ) . '</span>';
			echo '</li>';
		}
		echo '</ul>';
	}
}
