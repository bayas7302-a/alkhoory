<?php
/**
 * Subaru Model Card widget.
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

/**
 * Model card.
 */
class Subaru_Model_Card_Widget extends Subaru_Widget_Base {

	/**
	 * Name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'subaru-model-card';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return __( 'Subaru Model Card', 'subaru-alkhoory' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-image-box';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => __( 'Model', 'subaru-alkhoory' ) ) );
		$this->add_control( 'image', array( 'label' => __( 'Image', 'subaru-alkhoory' ), 'type' => Controls_Manager::MEDIA ) );
		$this->add_control( 'badge', array( 'label' => __( 'Badge', 'subaru-alkhoory' ), 'type' => Controls_Manager::TEXT, 'default' => 'SUV' ) );
		$this->add_control( 'name', array( 'label' => __( 'Name', 'subaru-alkhoory' ), 'type' => Controls_Manager::TEXT, 'default' => 'Subaru Forester' ) );
		$this->add_control( 'tagline', array( 'label' => __( 'Tagline', 'subaru-alkhoory' ), 'type' => Controls_Manager::TEXT, 'default' => 'Confidence for the Road Ahead.' ) );
		$defaults = array(
			1 => array( '7.9 L/100km', 'Fuel consumption' ),
			2 => array( 'Symmetrical AWD', 'Drivetrain' ),
			3 => array( '2.5i-S', 'EyeSight' ),
		);
		foreach ( $defaults as $n => $d ) {
			/* translators: %d: spec number */
			$this->add_control( 'spec' . $n . '_value', array( 'label' => sprintf( __( 'Spec %d value', 'subaru-alkhoory' ), $n ), 'type' => Controls_Manager::TEXT, 'default' => $d[0], 'separator' => 'before' ) );
			/* translators: %d: spec number */
			$this->add_control( 'spec' . $n . '_label', array( 'label' => sprintf( __( 'Spec %d label', 'subaru-alkhoory' ), $n ), 'type' => Controls_Manager::TEXT, 'default' => $d[1] ) );
		}
		$this->add_control( 'btn1', array( 'label' => __( 'Primary button', 'subaru-alkhoory' ), 'type' => Controls_Manager::TEXT, 'default' => 'Explore', 'separator' => 'before' ) );
		$this->add_control( 'btn1_link', array( 'label' => __( 'Primary link', 'subaru-alkhoory' ), 'type' => Controls_Manager::URL ) );
		$this->add_control( 'btn2', array( 'label' => __( 'Secondary button', 'subaru-alkhoory' ), 'type' => Controls_Manager::TEXT, 'default' => 'Book a Test Drive' ) );
		$this->add_control( 'btn2_link', array( 'label' => __( 'Secondary link', 'subaru-alkhoory' ), 'type' => Controls_Manager::URL ) );
		$this->end_controls_section();

		$this->start_controls_section(
			'style',
			array(
				'label' => __( 'Card', 'subaru-alkhoory' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_responsive_control(
			'ratio',
			array(
				'label'     => __( 'Image ratio', 'subaru-alkhoory' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '16/10',
				'options'   => array(
					'16/10' => '16:10',
					'16/9'  => '16:9',
					'4/3'   => '4:3',
					'1/1'   => '1:1',
				),
				'selectors' => array( '{{WRAPPER}} .sa-mcard__media' => 'aspect-ratio: {{VALUE}};' ),
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s   = $this->get_settings_for_display();
		$img = $this->media_url( $s['image'], 'large' );
		?>
		<article class="sa-mcard">
			<div class="sa-mcard__media">
				<?php if ( $img ) : ?>
					<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $this->media_alt( $s['image'], $s['name'] ) ); ?>" loading="lazy" decoding="async">
				<?php endif; ?>
				<?php if ( $s['badge'] ) : ?>
					<span class="sa-badge sa-mcard__badge"><?php echo esc_html( $s['badge'] ); ?></span>
				<?php endif; ?>
			</div>
			<div class="sa-mcard__body">
				<h3 class="sa-mcard__name"><?php echo esc_html( $s['name'] ); ?></h3>
				<?php if ( $s['tagline'] ) : ?>
					<p class="sa-mcard__tagline"><?php echo esc_html( $s['tagline'] ); ?></p>
				<?php endif; ?>
				<dl class="sa-mcard__specs">
					<?php
					for ( $n = 1; $n <= 3; $n++ ) {
						if ( '' === trim( (string) $s[ 'spec' . $n . '_value' ] ) ) {
							continue;
						}
						echo '<div><dt>' . esc_html( $s[ 'spec' . $n . '_label' ] ) . '</dt><dd>' . esc_html( $s[ 'spec' . $n . '_value' ] ) . '</dd></div>';
					}
					?>
				</dl>
				<div class="sa-mcard__buttons">
					<?php
					$this->button( $s['btn1'], $s['btn1_link'], 'sa-btn sa-btn--sm' );
					$this->button( $s['btn2'], $s['btn2_link'], 'sa-btn sa-btn--sm sa-btn--outline' );
					?>
				</div>
			</div>
		</article>
		<?php
	}
}
