<?php
/**
 * Common site footer.
 */

defined( 'ABSPATH' ) || exit;
?>
</main>

<footer class="yt-footer" role="contentinfo">
	<div class="yt-footer__inner">
		<a class="yt-footer__logos" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<img src="<?php echo yutong_uae_asset( 'img/logo-yutong-white.png' ); ?>" width="140" height="30" alt="Yutong">
			<span class="yt-footer__divider" aria-hidden="true"></span>
			<img src="<?php echo yutong_uae_asset( 'img/logo-ak-automobiles-white.png' ); ?>" width="277" height="28" alt="Al Khoory Automobiles LLC">
		</a>

		<div class="yt-footer__top">
			<p class="yt-footer__about"><?php echo esc_html( yutong_uae_opt( 'about' ) ); ?></p>

			<?php foreach ( array( 'yutong-footer-quick', 'yutong-footer-1', 'yutong-footer-2', 'yutong-footer-news' ) as $location ) : ?>
				<div class="yt-footer__col">
					<p class="yt-footer__heading"><?php echo esc_html( yutong_uae_footer_heading( $location ) ); ?></p>
					<?php yutong_uae_menu( $location, 'yt-footer__links' ); ?>
				</div>
			<?php endforeach; ?>

			<div class="yt-footer__contact">
				<p class="yt-footer__heading"><?php esc_html_e( 'Contact Information', 'yutong-uae' ); ?></p>
				<ul class="yt-footer__info">
					<li><?php echo yutong_uae_icon( 'pin' ); ?><span><?php echo esc_html( yutong_uae_opt( 'address' ) ); ?></span></li>
					<li><?php echo yutong_uae_icon( 'clock' ); ?><span><?php echo esc_html( yutong_uae_opt( 'hours' ) ); ?></span></li>
					<li><?php echo yutong_uae_icon( 'mail' ); ?><a href="mailto:<?php echo antispambot( esc_attr( yutong_uae_opt( 'email' ) ) ); ?>"><?php echo antispambot( esc_html( yutong_uae_opt( 'email' ) ) ); ?></a></li>
					<li><?php echo yutong_uae_icon( 'phone' ); ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', yutong_uae_opt( 'phone' ) ) ); ?>"><?php echo esc_html( yutong_uae_opt( 'phone_label' ) ); ?></a></li>
				</ul>
				<a class="yt-btn yt-btn--sm yt-btn--ghost yt-footer__map" href="<?php echo esc_url( yutong_uae_opt( 'map_url' ) ); ?>" target="_blank" rel="noopener">
					<?php esc_html_e( 'View Location', 'yutong-uae' ); ?><?php echo yutong_uae_icon( 'pin', 15 ); ?>
				</a>
			</div>
		</div>

		<div class="yt-footer__bottom">
			<p class="yt-footer__copy"><?php echo esc_html( str_replace( '{year}', gmdate( 'Y' ), yutong_uae_opt( 'copyright' ) ) ); ?></p>
			<?php yutong_uae_menu( 'yutong-legal', 'yt-footer__legal' ); ?>
		</div>

		<div class="yt-footer__group">
			<img src="<?php echo yutong_uae_asset( 'img/logo-akg-group-white.png' ); ?>" width="63" height="96" alt="Al Khoory Group">
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
