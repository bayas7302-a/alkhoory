<?php
/**
 * Site footer.
 *
 * @package abp
 */

defined( 'ABSPATH' ) || exit;
?>
</main>

<footer class="abp-footer">
	<div class="abp-wrap abp-footer__grid">
		<div class="abp-footer__brand">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="abp-footer__logo" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				<img src="<?php echo esc_url( ABP_URI . '/assets/img/ab-logo-white.png' ); ?>" width="252" height="84" alt="<?php esc_attr_e( 'AB Productions', 'abp' ); ?>" loading="lazy">
			</a>
			<p class="abp-footer__about"><?php echo esc_html( abp_opt( 'footer_about' ) ); ?></p>
			<?php echo abp_socials( 'abp-socials abp-socials--round' ); // phpcs:ignore ?>
		</div>

		<div class="abp-footer__col">
			<h3 class="abp-footer__title"><?php esc_html_e( 'Services', 'abp' ); ?></h3>
			<?php abp_menu( 'abp-footer-services', 'abp-footer__links' ); ?>
		</div>

		<div class="abp-footer__col">
			<h3 class="abp-footer__title"><?php esc_html_e( 'Company', 'abp' ); ?></h3>
			<?php abp_menu( 'abp-footer-company', 'abp-footer__links' ); ?>
		</div>

		<div class="abp-footer__col">
			<h3 class="abp-footer__title"><?php esc_html_e( 'Get in touch', 'abp' ); ?></h3>
			<ul class="abp-footer__links">
				<li><a href="<?php echo esc_attr( abp_tel( abp_opt( 'phone_primary' ) ) ); ?>"><?php echo esc_html( abp_opt( 'phone_primary' ) ); ?></a></li>
				<li><a href="<?php echo esc_attr( abp_tel( abp_opt( 'phone_secondary' ) ) ); ?>"><?php echo esc_html( abp_opt( 'phone_secondary' ) ); ?></a></li>
				<li><a href="mailto:<?php echo esc_attr( abp_opt( 'email' ) ); ?>"><?php echo esc_html( abp_opt( 'email' ) ); ?></a></li>
				<li><a href="<?php echo esc_url( abp_maps_link( abp_opt( 'hq_map' ) ) ); ?>" target="_blank" rel="noopener">HQ — <?php echo esc_html( abp_opt( 'hq_title' ) ); ?>, The Greens, Dubai</a></li>
				<li><a href="<?php echo esc_url( abp_maps_link( abp_opt( 'branch_map' ) ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Branch — Al Sajaa, Sharjah', 'abp' ); ?></a></li>
			</ul>
		</div>
	</div>

	<div class="abp-footer__bottom">
		<div class="abp-wrap abp-footer__bottom-inner">
			<p>© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php esc_html_e( 'AB Productions. All rights reserved.', 'abp' ); ?></p>
			<p class="abp-footer__tagline">Interior &nbsp;•&nbsp; Events &nbsp;•&nbsp; Production</p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
