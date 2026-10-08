<?php
/**
 * Site header.
 *
 * @package AIS_Global
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'ais-global' ); ?></a>

<header id="site-header" class="ais-header<?php echo get_theme_mod( 'ais_header_sticky', false ) ? ' is-sticky' : ''; ?>">
	<div class="ais-header__inner">
		<div class="ais-header__brand">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="ais-header__title" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
			<?php endif; ?>
		</div>

		<button class="ais-burger" aria-controls="ais-primary-nav" aria-expanded="false">
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'ais-global' ); ?></span>
			<span class="ais-burger__bar"></span><span class="ais-burger__bar"></span><span class="ais-burger__bar"></span>
		</button>

		<nav id="ais-primary-nav" class="ais-nav" aria-label="<?php esc_attr_e( 'Primary', 'ais-global' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'ais-menu',
					'fallback_cb'    => 'ais_global_menu_fallback',
					'depth'          => 3,
				)
			);
			$btn_text = get_theme_mod( 'ais_header_btn_text', 'Contact Us' );
			if ( $btn_text ) :
				?>
				<a class="ais-btn ais-header__cta ais-header__cta--mobile" href="<?php echo esc_url( ais_global_header_btn_url() ); ?>"><?php echo esc_html( $btn_text ); ?></a>
			<?php endif; ?>
		</nav>

		<?php if ( $btn_text ) : ?>
			<a class="ais-btn ais-header__cta" href="<?php echo esc_url( ais_global_header_btn_url() ); ?>"><?php echo esc_html( $btn_text ); ?></a>
		<?php endif; ?>
	</div>
</header>

<div id="content" class="ais-site-content">
