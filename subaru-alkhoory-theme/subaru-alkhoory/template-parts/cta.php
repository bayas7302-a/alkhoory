<?php
/**
 * "Let's start your Subaru journey" CTA band.
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$subaru_img = isset( $args['image'] ) ? $args['image'] : '';
?>
<section class="sa-cta">
	<div class="sa-cta__card" data-sa-reveal>
		<?php if ( $subaru_img ) : ?>
			<img class="sa-cta__img" src="<?php echo esc_url( $subaru_img ); ?>" alt="" loading="lazy" decoding="async">
		<?php endif; ?>
		<div class="sa-cta__content">
			<p class="sa-eyebrow sa-eyebrow--light"><?php esc_html_e( 'Your journey starts here', 'subaru-alkhoory' ); ?></p>
			<h2 class="sa-cta__title"><?php esc_html_e( 'Let’s start your Subaru journey', 'subaru-alkhoory' ); ?></h2>
			<p class="sa-cta__text"><?php esc_html_e( 'Whether you are exploring a new Subaru, booking a test drive or looking for expert after-sales care, our team is ready to help you find the right fit.', 'subaru-alkhoory' ); ?></p>
			<div class="sa-cta__buttons">
				<a class="sa-btn" href="<?php echo esc_url( home_url( '/book-a-test-drive/' ) ); ?>"><?php esc_html_e( 'Book a Test Drive', 'subaru-alkhoory' ); ?></a>
				<a class="sa-btn sa-btn--outline-light" href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>"><?php esc_html_e( 'Find a Showroom', 'subaru-alkhoory' ); ?></a>
			</div>
		</div>
	</div>
</section>
