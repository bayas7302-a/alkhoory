<?php
/**
 * Site footer.
 *
 * @package Alkhoory
 */

$alkhoory_columns = array(
	'footer_quick'    => __( 'Quick Links', 'alkhoory' ),
	'footer_brands'   => __( 'Our Brands', 'alkhoory' ),
	'footer_services' => __( 'Services', 'alkhoory' ),
);
$alkhoory_phone   = alkhoory_option( 'contact_phone', '+971 4 314 6146' );
$alkhoory_email   = alkhoory_option( 'contact_email', 'info@alkhoory.com' );
?>
</main>

<footer class="ak-footer" id="contact">
	<div class="ak-footer__inner">
		<div class="ak-footer__top">
			<div class="ak-footer__brand">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="ak-footer__logo">
					<img src="<?php echo esc_url( alkhoory_image_url( 'alkhoory_footer_logo', 'logo-white.png' ) ); ?>" width="240" height="27" alt="<?php esc_attr_e( 'Al Khoory Automobiles LLC', 'alkhoory' ); ?>">
				</a>
				<p><?php echo esc_html( alkhoory_option( 'footer_about', 'Five decades of trusted mobility across the UAE — premium passenger cars, commercial vans and luxury coaches, backed by certified after-sales care.' ) ); ?></p>
			</div>

			<div class="ak-footer__columns">
				<?php foreach ( $alkhoory_columns as $alkhoory_location => $alkhoory_title ) : ?>
					<div class="ak-footer__col">
						<h2 class="ak-footer__title"><?php echo esc_html( $alkhoory_title ); ?></h2>
						<?php alkhoory_menu( $alkhoory_location, 'ak-footer__list' ); ?>
					</div>
				<?php endforeach; ?>

				<div class="ak-footer__col">
					<h2 class="ak-footer__title"><?php esc_html_e( 'Contact Us', 'alkhoory' ); ?></h2>
					<ul class="ak-footer__list">
						<li><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $alkhoory_phone ) ); ?>"><?php echo esc_html( $alkhoory_phone ); ?></a></li>
						<li><a href="mailto:<?php echo esc_attr( $alkhoory_email ); ?>"><?php echo esc_html( $alkhoory_email ); ?></a></li>
						<li><address><?php echo nl2br( esc_html( alkhoory_option( 'contact_address', "23 Sheikh Zayed Rd, Al Quoz\nIndustrial Area 3, Dubai, UAE" ) ) ); ?></address></li>
					</ul>
				</div>
			</div>
		</div>

		<div class="ak-footer__bottom">
			<p><?php echo esc_html( str_replace( '{year}', wp_date( 'Y' ), alkhoory_option( 'copyright', '© {year} Al Khoory Automobiles LLC. All rights reserved.' ) ) ); ?></p>
			<nav aria-label="<?php esc_attr_e( 'Legal', 'alkhoory' ); ?>">
				<?php alkhoory_menu( 'footer_legal', 'ak-footer__legal' ); ?>
			</nav>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
