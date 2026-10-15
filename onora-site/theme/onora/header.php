<?php defined( 'ABSPATH' ) || exit; ?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#content"><?php esc_html_e( 'Skip to content', 'onora' ); ?></a>

<div class="onora-utility">
	<div class="onora-container onora-utility__inner">
		<div class="onora-utility__contacts">
			<a href="<?php echo esc_attr( onora_contact( 'phone_href' ) ); ?>"><?php echo esc_html( onora_contact( 'phone' ) ); ?></a>
			<a href="mailto:<?php echo esc_attr( onora_contact( 'email' ) ); ?>"><?php echo esc_html( onora_contact( 'email' ) ); ?></a>
			<span class="onora-utility__city"><?php echo esc_html( onora_contact( 'city' ) ); ?></span>
		</div>
		<p class="onora-utility__tagline"><?php echo esc_html( onora_contact( 'tagline' ) ); ?></p>
	</div>
</div>

<header class="onora-header" id="onora-header">
	<div class="onora-container onora-header__inner">
		<?php onora_logo( 'navy' ); ?>
		<nav class="onora-nav" id="onora-nav" aria-label="<?php esc_attr_e( 'Main menu', 'onora' ); ?>">
			<?php onora_menu( 'primary', 'onora-menu' ); ?>
			<a class="onora-btn onora-btn--navy onora-nav__cta-mobile" href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Request a quote <span aria-hidden="true">→</span></a>
		</nav>
		<a class="onora-btn onora-btn--navy onora-header__cta" href="<?php echo esc_url( home_url( '/contact-us/' ) ); ?>">Request a quote <span aria-hidden="true">→</span></a>
		<button class="onora-burger" type="button" aria-controls="onora-nav" aria-expanded="false">
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'onora' ); ?></span><span></span><span></span><span></span>
		</button>
	</div>
</header>
<main id="content" class="onora-main">
