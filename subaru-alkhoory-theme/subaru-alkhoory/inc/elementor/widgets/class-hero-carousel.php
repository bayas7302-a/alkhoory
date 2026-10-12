<?php
/**
 * Subaru Hero Carousel widget — same look & behaviour as the atomic .sub-hero-carousel build.
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;

/**
 * Hero carousel.
 */
class Subaru_Hero_Carousel_Widget extends Subaru_Widget_Base {

	/**
	 * Name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'subaru-hero-carousel';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return __( 'Subaru Hero Carousel', 'subaru-alkhoory' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-slides';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => __( 'Slides', 'subaru-alkhoory' ) ) );

		$rep = new Repeater();
		$rep->add_control( 'image', array( 'label' => __( 'Background image', 'subaru-alkhoory' ), 'type' => Controls_Manager::MEDIA ) );
		$rep->add_control( 'eyebrow', array( 'label' => __( 'Eyebrow', 'subaru-alkhoory' ), 'type' => Controls_Manager::TEXT, 'default' => 'Subaru Forester' ) );
		$rep->add_control( 'title', array( 'label' => __( 'Title', 'subaru-alkhoory' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 2, 'default' => 'Confidence for the road ahead.' ) );
		$rep->add_control( 'text', array( 'label' => __( 'Text', 'subaru-alkhoory' ), 'type' => Controls_Manager::TEXTAREA, 'default' => '' ) );
		$rep->add_control( 'btn1', array( 'label' => __( 'Primary button', 'subaru-alkhoory' ), 'type' => Controls_Manager::TEXT, 'default' => 'Explore' ) );
		$rep->add_control( 'btn1_link', array( 'label' => __( 'Primary link', 'subaru-alkhoory' ), 'type' => Controls_Manager::URL ) );
		$rep->add_control( 'btn2', array( 'label' => __( 'Secondary button', 'subaru-alkhoory' ), 'type' => Controls_Manager::TEXT, 'default' => 'Book a Test Drive' ) );
		$rep->add_control( 'btn2_link', array( 'label' => __( 'Secondary link', 'subaru-alkhoory' ), 'type' => Controls_Manager::URL ) );
		$rep->add_control( 'tab_label', array( 'label' => __( 'Tab label', 'subaru-alkhoory' ), 'type' => Controls_Manager::TEXT, 'default' => 'Forester', 'separator' => 'before' ) );
		$rep->add_control( 'tab_tagline', array( 'label' => __( 'Tab tagline', 'subaru-alkhoory' ), 'type' => Controls_Manager::TEXT, 'default' => 'Confidence for the Road Ahead.' ) );

		$this->add_control(
			'slides',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $rep->get_controls(),
				'title_field' => '{{{ tab_label }}}',
				'default'     => array(
					array(
						'eyebrow'     => 'Subaru Forester',
						'title'       => 'Confidence for the road ahead.',
						'tab_label'   => 'Forester',
						'tab_tagline' => 'Confidence for the Road Ahead.',
					),
					array(
						'eyebrow'     => 'Subaru BRZ',
						'title'       => 'Engineered for the thrill.',
						'tab_label'   => 'BRZ',
						'tab_tagline' => 'Engineered for the Thrill.',
					),
				),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section( 'settings', array( 'label' => __( 'Behaviour', 'subaru-alkhoory' ) ) );
		$this->add_control(
			'autoplay',
			array(
				'label'        => __( 'Autoplay', 'subaru-alkhoory' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			)
		);
		$this->add_control(
			'interval',
			array(
				'label'   => __( 'Interval (seconds)', 'subaru-alkhoory' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 6,
				'min'     => 3,
				'max'     => 20,
			)
		);
		$this->add_responsive_control(
			'height',
			array(
				'label'      => __( 'Min height', 'subaru-alkhoory' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array(
					'px' => array( 'min' => 400, 'max' => 1200 ),
					'vh' => array( 'min' => 40, 'max' => 100 ),
				),
				'default'    => array( 'unit' => 'px', 'size' => 820 ),
				'selectors'  => array( '{{WRAPPER}} .sa-hcw .sub-hero-slide' => 'min-height: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s      = $this->get_settings_for_display();
		$slides = ! empty( $s['slides'] ) ? $s['slides'] : array();
		if ( ! $slides ) {
			return;
		}
		$uid      = 'sa-hcw-' . $this->get_id();
		$interval = max( 3, (int) $s['interval'] ) * 1000;
		?>
		<div class="sa-hcw sub-hero-carousel" data-sa-hero data-interval="<?php echo (int) $interval; ?>" data-autoplay="<?php echo 'yes' === $s['autoplay'] ? '1' : '0'; ?>">
			<div class="sub-hero-tabs" role="tablist" aria-label="<?php esc_attr_e( 'Featured models', 'subaru-alkhoory' ); ?>">
				<?php foreach ( $slides as $i => $sl ) : ?>
					<button type="button" class="sub-hero-tab<?php echo 0 === $i ? ' e--selected' : ''; ?>" role="tab" id="<?php echo esc_attr( $uid . '-tab-' . $i ); ?>" aria-controls="<?php echo esc_attr( $uid . '-panel-' . $i ); ?>" aria-selected="<?php echo 0 === $i ? 'true' : 'false'; ?>" tabindex="<?php echo 0 === $i ? '0' : '-1'; ?>">
						<span class="sa-hcw__label"><strong><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></strong> <?php echo esc_html( $sl['tab_label'] ); ?></span>
						<?php if ( $sl['tab_tagline'] ) : ?>
							<span class="sa-hcw__tagline"><?php echo esc_html( $sl['tab_tagline'] ); ?></span>
						<?php endif; ?>
					</button>
				<?php endforeach; ?>
			</div>
			<div class="sa-hcw__slides">
				<?php
				foreach ( $slides as $i => $sl ) :
					$img = $this->media_url( isset( $sl['image'] ) ? $sl['image'] : array(), 'full' );
					?>
					<div class="sub-hero-slide<?php echo 0 === $i ? ' e--selected' : ''; ?>" role="tabpanel" id="<?php echo esc_attr( $uid . '-panel-' . $i ); ?>" aria-labelledby="<?php echo esc_attr( $uid . '-tab-' . $i ); ?>" <?php echo 0 === $i ? '' : 'hidden'; ?> <?php echo $img ? 'style="background-image:url(' . esc_url( $img ) . ')"' : ''; ?>>
						<div class="sa-hcw__content">
							<?php if ( $sl['eyebrow'] ) : ?>
								<p class="sa-eyebrow sa-eyebrow--light"><?php echo esc_html( $sl['eyebrow'] ); ?></p>
							<?php endif; ?>
							<?php if ( 0 === $i ) : ?>
								<h1 class="sa-hcw__title"><?php echo esc_html( $sl['title'] ); ?></h1>
							<?php else : ?>
								<h2 class="sa-hcw__title"><?php echo esc_html( $sl['title'] ); ?></h2>
							<?php endif; ?>
							<?php if ( $sl['text'] ) : ?>
								<p class="sa-hcw__text"><?php echo esc_html( $sl['text'] ); ?></p>
							<?php endif; ?>
							<div class="sa-hcw__buttons">
								<?php
								$this->button( $sl['btn1'], $sl['btn1_link'], 'sa-btn sub-btn' );
								$this->button( $sl['btn2'], $sl['btn2_link'], 'sa-btn sa-btn--outline-light sub-btn' );
								?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
