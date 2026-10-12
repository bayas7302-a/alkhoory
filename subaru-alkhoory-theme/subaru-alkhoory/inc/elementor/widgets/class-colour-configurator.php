<?php
/**
 * Subaru Colour Configurator widget: stage + colour swatches, optional 360° frame sequence per colour.
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Repeater;

/**
 * Colour configurator.
 */
class Subaru_Colour_Configurator_Widget extends Subaru_Widget_Base {

	/**
	 * Name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'subaru-colour-configurator';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return __( 'Subaru Colour Configurator', 'subaru-alkhoory' );
	}

	/**
	 * Icon.
	 *
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-paint-brush';
	}

	/**
	 * Controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section( 'content', array( 'label' => __( 'Configurator', 'subaru-alkhoory' ) ) );

		$this->add_control(
			'model',
			array(
				'label'   => __( 'Model name', 'subaru-alkhoory' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Subaru Crosstrek',
			)
		);

		$rep = new Repeater();
		$rep->add_control(
			'name',
			array(
				'label'   => __( 'Colour name', 'subaru-alkhoory' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'Crystal White Pearl',
			)
		);
		$rep->add_control(
			'hex',
			array(
				'label'   => __( 'Swatch colour', 'subaru-alkhoory' ),
				'type'    => Controls_Manager::COLOR,
				'default' => '#f2f2ef',
			)
		);
		$rep->add_control(
			'image',
			array(
				'label' => __( 'Car image', 'subaru-alkhoory' ),
				'type'  => Controls_Manager::MEDIA,
			)
		);
		$rep->add_control(
			'frames',
			array(
				'label'       => __( '360° frames (optional)', 'subaru-alkhoory' ),
				'type'        => Controls_Manager::GALLERY,
				'description' => __( 'Ordered frame sequence (e.g. 24–36 images) — visitors drag to rotate.', 'subaru-alkhoory' ),
				'default'     => array(),
			)
		);
		$this->add_control(
			'colours',
			array(
				'label'       => __( 'Colours', 'subaru-alkhoory' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $rep->get_controls(),
				'title_field' => '{{{ name }}}',
				'default'     => array(
					array(
						'name' => 'Crystal White Pearl',
						'hex'  => '#f2f2ef',
					),
					array(
						'name' => 'Magnetite Grey Metallic',
						'hex'  => '#53575a',
					),
					array(
						'name' => 'Crystal Black Silica',
						'hex'  => '#141518',
					),
				),
			)
		);
		$this->add_control(
			'disclaimer',
			array(
				'label'   => __( 'Disclaimer', 'subaru-alkhoory' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'Colours shown are indicative. Please visit a showroom to view actual paint finishes.', 'subaru-alkhoory' ),
			)
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'style',
			array(
				'label' => __( 'Stage', 'subaru-alkhoory' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_theme_control();
		$this->add_responsive_control(
			'ratio',
			array(
				'label'     => __( 'Aspect ratio', 'subaru-alkhoory' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '16/9',
				'options'   => array(
					'16/9'  => '16:9',
					'21/9'  => '21:9',
					'16/10' => '16:10',
					'4/3'   => '4:3',
					'1/1'   => '1:1',
				),
				'selectors' => array( '{{WRAPPER}} .sa-cc__stage' => 'aspect-ratio: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'stage_bg',
			array(
				'label'     => __( 'Stage background', 'subaru-alkhoory' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .sa-cc__stage' => 'background: {{VALUE}};' ),
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Render.
	 */
	protected function render(): void {
		$s       = $this->get_settings_for_display();
		$colours = ! empty( $s['colours'] ) ? $s['colours'] : array();
		if ( ! $colours ) {
			return;
		}
		$uid = 'sa-cc-' . $this->get_id();
		?>
		<div class="sa-cc" data-sa-cc>
			<div class="sa-cc__stage" aria-live="polite">
				<?php
				foreach ( $colours as $i => $c ) :
					$img    = $this->media_url( isset( $c['image'] ) ? $c['image'] : array(), 'full' );
					$frames = array();
					if ( ! empty( $c['frames'] ) ) {
						foreach ( $c['frames'] as $f ) {
							$u = ! empty( $f['id'] ) ? wp_get_attachment_image_url( (int) $f['id'], 'large' ) : ( isset( $f['url'] ) ? $f['url'] : '' );
							if ( $u ) {
								$frames[] = esc_url_raw( $u );
							}
						}
					}
					if ( ! $img && $frames ) {
						$img = $frames[0];
					}
					$alt = trim( $s['model'] . ' — ' . $c['name'] );
					?>
					<figure class="sa-cc__frame<?php echo 0 === $i ? ' is-active' : ''; ?>" data-index="<?php echo (int) $i; ?>" <?php echo $frames ? 'data-frames="' . esc_attr( wp_json_encode( $frames ) ) . '"' : ''; ?> <?php echo 0 === $i ? '' : 'aria-hidden="true"'; ?>>
						<?php if ( $img ) : ?>
							<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="<?php echo 0 === $i ? 'eager' : 'lazy'; ?>" decoding="async" draggable="false">
						<?php else : ?>
							<span class="sa-cc__placeholder" style="--c:<?php echo esc_attr( $c['hex'] ); ?>"><?php subaru_the_icon( 'car', 96 ); ?></span>
						<?php endif; ?>
						<?php if ( $frames ) : ?>
							<span class="sa-cc__hint"><?php subaru_the_icon( 'rotate', 16 ); ?> 360° <span><?php esc_html_e( 'Drag to rotate', 'subaru-alkhoory' ); ?></span></span>
						<?php endif; ?>
					</figure>
				<?php endforeach; ?>
			</div>
			<div class="sa-cc__bar">
				<div class="sa-cc__info">
					<?php if ( $s['model'] ) : ?>
						<p class="sa-cc__model"><?php echo esc_html( $s['model'] ); ?></p>
					<?php endif; ?>
					<p class="sa-cc__name" id="<?php echo esc_attr( $uid ); ?>-name"><?php echo esc_html( $colours[0]['name'] ); ?></p>
				</div>
				<div class="sa-cc__swatches" role="radiogroup" aria-labelledby="<?php echo esc_attr( $uid ); ?>-name">
					<?php foreach ( $colours as $i => $c ) : ?>
						<button type="button" class="sa-cc__swatch<?php echo 0 === $i ? ' is-active' : ''; ?>" role="radio" aria-checked="<?php echo 0 === $i ? 'true' : 'false'; ?>" tabindex="<?php echo 0 === $i ? '0' : '-1'; ?>" style="--c:<?php echo esc_attr( $c['hex'] ); ?>" data-index="<?php echo (int) $i; ?>" data-name="<?php echo esc_attr( $c['name'] ); ?>" aria-label="<?php echo esc_attr( $c['name'] ); ?>">
							<span class="sa-tip" aria-hidden="true"><?php echo esc_html( $c['name'] ); ?></span>
						</button>
					<?php endforeach; ?>
				</div>
			</div>
			<?php if ( $s['disclaimer'] ) : ?>
				<p class="sa-cc__note"><?php echo esc_html( $s['disclaimer'] ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}
}
