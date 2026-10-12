<?php
/**
 * Subaru Feature Hotspots widget — image with positioned, pulsing hotspots and popovers.
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;

/**
 * Hotspots.
 */
class Subaru_Feature_Hotspots_Widget extends Subaru_Widget_Base {

	/**
	 * Name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'subaru-feature-hotspots';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return __( 'Subaru Feature Hotspots', 'subaru-alkhoory' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-image-hotspot';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => __( 'Hotspots', 'subaru-alkhoory' ) ) );
		$this->add_control( 'image', array( 'label' => __( 'Image', 'subaru-alkhoory' ), 'type' => Controls_Manager::MEDIA ) );

		$rep = new Repeater();
		$rep->add_control( 'title', array( 'label' => __( 'Title', 'subaru-alkhoory' ), 'type' => Controls_Manager::TEXT, 'default' => 'LED Headlights' ) );
		$rep->add_control( 'text', array( 'label' => __( 'Text', 'subaru-alkhoory' ), 'type' => Controls_Manager::TEXTAREA, 'default' => '' ) );
		$rep->add_control(
			'x',
			array(
				'label'   => __( 'Horizontal position (%)', 'subaru-alkhoory' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => array( '%' => array( 'min' => 0, 'max' => 100 ) ),
				'default' => array( 'unit' => '%', 'size' => 50 ),
			)
		);
		$rep->add_control(
			'y',
			array(
				'label'   => __( 'Vertical position (%)', 'subaru-alkhoory' ),
				'type'    => Controls_Manager::SLIDER,
				'range'   => array( '%' => array( 'min' => 0, 'max' => 100 ) ),
				'default' => array( 'unit' => '%', 'size' => 50 ),
			)
		);
		$this->add_control(
			'spots',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $rep->get_controls(),
				'title_field' => '{{{ title }}}',
				'default'     => array(
					array(
						'title' => 'LED Headlights',
						'x'     => array( 'unit' => '%', 'size' => 22 ),
						'y'     => array( 'unit' => '%', 'size' => 48 ),
					),
					array(
						'title' => 'Roof Rails',
						'x'     => array( 'unit' => '%', 'size' => 55 ),
						'y'     => array( 'unit' => '%', 'size' => 22 ),
					),
				),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'style',
			array(
				'label' => __( 'Hotspots', 'subaru-alkhoory' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'dot_color',
			array(
				'label'     => __( 'Dot colour', 'subaru-alkhoory' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sa-hot' => '--dot: {{VALUE}};' ),
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s     = $this->get_settings_for_display();
		$img   = $this->media_url( $s['image'], 'full' );
		$spots = ! empty( $s['spots'] ) ? $s['spots'] : array();
		$uid   = 'sa-hot-' . $this->get_id();
		?>
		<div class="sa-hot" data-sa-hotspots>
			<?php if ( $img ) : ?>
				<img class="sa-hot__img" src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $this->media_alt( $s['image'] ) ); ?>" loading="lazy" decoding="async">
			<?php else : ?>
				<div class="sa-hot__img sa-hot__img--ph"><?php subaru_the_icon( 'car', 120 ); ?></div>
			<?php endif; ?>
			<?php
			foreach ( $spots as $i => $sp ) :
				$x   = isset( $sp['x']['size'] ) ? (float) $sp['x']['size'] : 50;
				$y   = isset( $sp['y']['size'] ) ? (float) $sp['y']['size'] : 50;
				$pid = $uid . '-' . $i;
				?>
				<div class="sa-hot__spot<?php echo $x > 60 ? ' is-left' : ''; ?><?php echo $y > 60 ? ' is-up' : ''; ?>" style="left:<?php echo esc_attr( $x ); ?>%;top:<?php echo esc_attr( $y ); ?>%;--d:<?php echo (int) ( $i * 300 ); ?>ms">
					<button type="button" class="sa-hot__dot" aria-expanded="false" aria-controls="<?php echo esc_attr( $pid ); ?>">
						<span class="screen-reader-text"><?php echo esc_html( $sp['title'] ); ?></span>
						<?php subaru_the_icon( 'plus', 16 ); ?>
					</button>
					<div class="sa-hot__pop" id="<?php echo esc_attr( $pid ); ?>" role="tooltip">
						<strong><?php echo esc_html( $sp['title'] ); ?></strong>
						<?php if ( $sp['text'] ) : ?>
							<p><?php echo esc_html( $sp['text'] ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
