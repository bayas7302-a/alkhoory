<?php
/**
 * Site header: top bar + main bar with logo, menu, phone and quote button.
 *
 * @package abp
 */

defined( 'ABSPATH' ) || exit;

$abp_phone = abp_opt( 'phone_secondary' );
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'abp' ); ?></a>

<header class="abp-header" id="abp-header">
	<div class="abp-topbar">
		<div class="abp-wrap abp-topbar__inner">
			<p class="abp-topbar__text"><?php echo esc_html( abp_opt( 'topbar_text' ) ); ?></p>
			<div class="abp-topbar__contacts">
				<a href="<?php echo esc_attr( abp_tel( abp_opt( 'phone_primary' ) ) ); ?>"><?php echo esc_html( abp_opt( 'phone_primary' ) ); ?></a>
				<a href="mailto:<?php echo esc_attr( abp_opt( 'email' ) ); ?>"><?php echo esc_html( abp_opt( 'email' ) ); ?></a>
				<?php echo abp_socials( 'abp-socials abp-socials--top' ); // phpcs:ignore ?>
			</div>
		</div>
	</div>

	<div class="abp-mainbar">
		<div class="abp-wrap abp-mainbar__inner">
			<a class="abp-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				<?php if ( has_custom_logo() ) : ?>
					<?php echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'medium', false, array( 'class' => 'abp-logo__img' ) ); ?>
				<?php else : ?>
					<img class="abp-logo__img" src="<?php echo esc_url( ABP_URI . '/assets/img/ab-logo.png' ); ?>" width="222" height="74" alt="<?php esc_attr_e( 'AB Productions — Turning on colors', 'abp' ); ?>">
				<?php endif; ?>
			</a>

			<nav class="abp-nav" id="abp-nav" aria-label="<?php esc_attr_e( 'Main menu', 'abp' ); ?>">
				<?php abp_menu( 'abp-primary', 'abp-nav__list' ); ?>
				<div class="abp-nav__mobile-extra">
					<a class="abp-btn abp-btn--grad" href="<?php echo esc_url( abp_url( abp_opt( 'quote_url' ) ) ); ?>"><?php esc_html_e( 'Get a Quote', 'abp' ); ?> <span aria-hidden="true">→</span></a>
					<a class="abp-nav__phone" href="<?php echo esc_attr( abp_tel( $abp_phone ) ); ?>"><?php echo esc_html( $abp_phone ); ?></a>
					<?php echo abp_socials( 'abp-socials abp-socials--nav' ); // phpcs:ignore ?>
				</div>
			</nav>

			<div class="abp-mainbar__cta">
				<a class="abp-call" href="<?php echo esc_attr( abp_tel( $abp_phone ) ); ?>">
					<span class="abp-call__label"><?php esc_html_e( 'Talk to our team', 'abp' ); ?></span>
					<span class="abp-call__number"><?php echo esc_html( $abp_phone ); ?></span>
				</a>
				<a class="abp-btn abp-btn--grad abp-quote" href="<?php echo esc_url( abp_url( abp_opt( 'quote_url' ) ) ); ?>"><?php esc_html_e( 'Get a Quote', 'abp' ); ?> <span aria-hidden="true">→</span></a>
				<button class="abp-burger" type="button" aria-controls="abp-nav" aria-expanded="false" aria-label="<?php esc_attr_e( 'Open menu', 'abp' ); ?>"><span></span><span></span><span></span></button>
			</div>
		</div>
	</div>
</header>

<main id="content" class="abp-content">
