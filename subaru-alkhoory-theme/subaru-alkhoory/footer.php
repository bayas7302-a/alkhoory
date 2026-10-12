<?php
/**
 * Site footer: newsletter, link columns, logos, social, legal, back-to-top, WhatsApp, cookie bar.
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$subaru_columns = array(
	'footer_range'     => __( 'Our Range', 'subaru-alkhoory' ),
	'footer_discover'  => __( 'Discover', 'subaru-alkhoory' ),
	'footer_owners'    => __( 'Owners', 'subaru-alkhoory' ),
	'footer_resources' => __( 'Resources', 'subaru-alkhoory' ),
);
$subaru_social  = array(
	'instagram' => 'Instagram',
	'facebook'  => 'Facebook',
	'linkedin'  => 'LinkedIn',
	'youtube'   => 'YouTube',
);
$subaru_wa      = subaru_setting( 'whatsapp_number' );
?>
<footer class="sa-footer" id="sa-footer">
	<div class="sa-footer__inner">
		<div class="sa-footer__top">
			<div class="sa-footer__news">
				<p class="sa-eyebrow sa-eyebrow--light"><?php esc_html_e( 'Newsletter', 'subaru-alkhoory' ); ?></p>
				<h2 class="sa-footer__title"><?php esc_html_e( 'Stay up to date on the latest Subaru news', 'subaru-alkhoory' ); ?></h2>
				<p class="sa-footer__text"><?php esc_html_e( 'Be the first to hear about new models, exclusive offers and events from Subaru UAE.', 'subaru-alkhoory' ); ?></p>
				<?php echo do_shortcode( '[subaru_form type="newsletter" class="sa-form--dark"]' ); ?>
			</div>
			<div class="sa-footer__cols">
				<?php foreach ( $subaru_columns as $subaru_loc => $subaru_label ) : ?>
					<nav class="sa-footer__col" aria-label="<?php echo esc_attr( $subaru_label ); ?>">
						<h3 class="sa-footer__heading"><?php echo esc_html( $subaru_label ); ?></h3>
						<?php subaru_render_links( $subaru_loc, 'sa-footer__links' ); ?>
					</nav>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="sa-footer__rule" aria-hidden="true"></div>

		<div class="sa-footer__bottom">
			<div class="sa-footer__brand">
				<img class="sa-footer__group" src="<?php echo esc_url( subaru_image_url( 'subaru_group_logo', 'al-khoory-group.png' ) ); ?>" alt="<?php esc_attr_e( 'Al Khoory Group', 'subaru-alkhoory' ); ?>" width="47" height="71" loading="lazy">
				<div class="sa-footer__legal-text">
					<p class="sa-footer__copy-line"><span class="sa-footer__copy">&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( get_theme_mod( 'subaru_copyright', 'Al Khoory Automobiles LLC' ) ); ?>. <?php esc_html_e( 'All rights reserved.', 'subaru-alkhoory' ); ?></span>
					<span class="sa-footer__member"><span><?php esc_html_e( 'A member of', 'subaru-alkhoory' ); ?></span>
						<img src="<?php echo esc_url( subaru_image_url( 'subaru_member_logo', 'subaru-wordmark.png' ) ); ?>" alt="Subaru" width="81" height="14" loading="lazy"></span></p>
				</div>
			</div>

			<?php
			$subaru_has_social = false;
			foreach ( $subaru_social as $subaru_key => $subaru_net ) {
				if ( subaru_setting( 'social_' . $subaru_key ) ) {
					$subaru_has_social = true;
				}
			}
			if ( $subaru_has_social ) :
				?>
				<ul class="sa-social" aria-label="<?php esc_attr_e( 'Social media', 'subaru-alkhoory' ); ?>">
					<?php
					foreach ( $subaru_social as $subaru_key => $subaru_net ) :
						$subaru_url = subaru_setting( 'social_' . $subaru_key );
						if ( ! $subaru_url ) {
							continue;
						}
						?>
						<li><a href="<?php echo esc_url( $subaru_url ); ?>" target="_blank" rel="noopener"><?php subaru_the_icon( $subaru_key, 18 ); ?><span class="screen-reader-text"><?php echo esc_html( $subaru_net ); ?></span></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<nav class="sa-footer__legal" aria-label="<?php esc_attr_e( 'Legal', 'subaru-alkhoory' ); ?>">
				<?php subaru_render_links( 'legal', 'sa-legal' ); ?>
			</nav>
		</div>
	</div>
</footer>

<button type="button" class="sa-totop" aria-label="<?php esc_attr_e( 'Back to top', 'subaru-alkhoory' ); ?>">
	<svg class="sa-totop__ring" viewBox="0 0 48 48" aria-hidden="true"><circle cx="24" cy="24" r="22"></circle></svg>
	<?php subaru_the_icon( 'arrow-up', 18 ); ?>
</button>

<?php if ( $subaru_wa ) : ?>
	<a class="sa-whatsapp" href="<?php echo esc_url( 'https://wa.me/' . $subaru_wa . '?text=' . rawurlencode( subaru_setting( 'whatsapp_message' ) ) ); ?>" target="_blank" rel="noopener">
		<?php subaru_the_icon( 'whatsapp', 26 ); ?>
		<span class="screen-reader-text"><?php esc_html_e( 'Chat with us on WhatsApp', 'subaru-alkhoory' ); ?></span>
	</a>
<?php endif; ?>

<?php if ( subaru_setting( 'cookie_notice' ) ) : ?>
	<div class="sa-cookie" role="region" aria-label="<?php esc_attr_e( 'Cookie notice', 'subaru-alkhoory' ); ?>" hidden>
		<p><?php echo esc_html( subaru_setting( 'cookie_text' ) ); ?>
			<a href="<?php echo esc_url( get_privacy_policy_url() ? get_privacy_policy_url() : home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'subaru-alkhoory' ); ?></a></p>
		<button type="button" class="sa-btn sa-btn--sm sa-cookie__ok"><?php esc_html_e( 'Accept', 'subaru-alkhoory' ); ?></button>
	</div>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
