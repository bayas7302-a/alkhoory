<?php defined( 'ABSPATH' ) || exit; ?>
</main>

<footer class="onora-footer">
	<div class="onora-container">
		<div class="onora-footer__top">
			<div class="onora-footer__brand">
				<?php onora_logo( 'white' ); ?>
				<p class="onora-footer__about">ONORA Hotel Supplies brings world-class tableware, buffet, bar and service equipment to hotels, restaurants and cafés across the UAE.</p>
				<?php onora_newsletter_form(); ?>
				<p class="onora-footer__note">New collections and insider offers — straight to your inbox.</p>
			</div>
			<div class="onora-footer__cols">
				<div class="onora-footer__col">
					<h2 class="onora-footer__title">Explore</h2>
					<?php onora_menu( 'primary', 'onora-footer__list' ); ?>
				</div>
				<div class="onora-footer__col">
					<h2 class="onora-footer__title">Collections</h2>
					<?php onora_menu( 'collections', 'onora-footer__list' ); ?>
				</div>
				<div class="onora-footer__col onora-footer__col--wide">
					<h2 class="onora-footer__title">Get in touch</h2>
					<ul class="onora-footer__list">
						<li><a href="<?php echo esc_attr( onora_contact( 'phone_href' ) ); ?>"><?php echo esc_html( onora_contact( 'phone' ) ); ?></a></li>
						<li><a href="mailto:<?php echo esc_attr( onora_contact( 'email' ) ); ?>"><?php echo esc_html( onora_contact( 'email' ) ); ?></a></li>
						<li><a href="https://<?php echo esc_attr( onora_contact( 'website' ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( onora_contact( 'website' ) ); ?></a></li>
						<li><?php echo esc_html( onora_contact( 'address' ) ); ?></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="onora-footer__bottom">
			<p>© <?php echo esc_html( gmdate( 'Y' ) ); ?> ONORA Hotel Supplies. All rights reserved.</p>
			<p class="onora-footer__social">
				<a href="<?php echo esc_url( onora_contact( 'instagram' ) ); ?>" target="_blank" rel="noopener">Instagram</a><span aria-hidden="true">·</span>
				<a href="<?php echo esc_url( onora_contact( 'linkedin' ) ); ?>" target="_blank" rel="noopener">LinkedIn</a><span aria-hidden="true">·</span>
				<a href="<?php echo esc_url( onora_contact( 'facebook' ) ); ?>" target="_blank" rel="noopener">Facebook</a>
			</p>
			<p class="onora-footer__legal"><a href="<?php echo esc_url( get_privacy_policy_url() ?: home_url( '/contact-us/' ) ); ?>">Privacy Policy</a><span aria-hidden="true">·</span><a href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Terms of Use</a></p>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
