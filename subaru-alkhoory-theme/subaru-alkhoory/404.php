<?php
/**
 * 404.
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="content" class="sa-main sa-404">
	<section class="sa-404__hero">
		<div class="sa-404__road" aria-hidden="true"><span></span></div>
		<div class="sa-wrap sa-404__inner">
			<p class="sa-eyebrow sa-eyebrow--light"><?php esc_html_e( 'Error 404', 'subaru-alkhoory' ); ?></p>
			<h1 class="sa-404__title"><?php esc_html_e( 'Off the beaten path.', 'subaru-alkhoory' ); ?></h1>
			<p class="sa-phero__text"><?php esc_html_e( 'Even Symmetrical All-Wheel Drive can’t reach this page. It may have moved or no longer exists — let’s get you back on the road.', 'subaru-alkhoory' ); ?></p>
			<div class="sa-404__search"><?php get_search_form(); ?></div>
			<div class="sa-404__buttons">
				<a class="sa-btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to Home', 'subaru-alkhoory' ); ?></a>
				<a class="sa-btn sa-btn--outline-light" href="<?php echo esc_url( home_url( '/our-range/' ) ); ?>"><?php esc_html_e( 'Explore Our Range', 'subaru-alkhoory' ); ?></a>
				<a class="sa-btn sa-btn--outline-light" href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>"><?php esc_html_e( 'Contact Us', 'subaru-alkhoory' ); ?></a>
			</div>
		</div>
	</section>
</main>
<?php
get_footer();
