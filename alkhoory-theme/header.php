<?php
/**
 * Site header.
 *
 * @package Alkhoory
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="ak-skip-link" href="#ak-content"><?php esc_html_e( 'Skip to content', 'alkhoory' ); ?></a>

<header class="ak-header" id="ak-header">
	<div class="ak-header__inner">
		<a class="ak-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php
			$logo_id = get_theme_mod( 'custom_logo' );
			if ( $logo_id ) {
				echo wp_get_attachment_image( $logo_id, 'full', false, array( 'alt' => get_bloginfo( 'name' ) ) );
			} else {
				printf(
					'<img src="%s" width="240" height="27" alt="%s">',
					esc_url( get_template_directory_uri() . '/assets/images/logo.png' ),
					esc_attr__( 'Al Khoory Automobiles LLC', 'alkhoory' )
				);
			}
			?>
		</a>

		<button class="ak-header__toggle" type="button" aria-controls="ak-nav" aria-expanded="false">
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'alkhoory' ); ?></span>
			<span class="ak-header__toggle-bar" aria-hidden="true"></span>
		</button>

		<div class="ak-header__panel" id="ak-nav">
			<nav class="ak-nav" aria-label="<?php esc_attr_e( 'Primary', 'alkhoory' ); ?>">
				<?php alkhoory_menu( 'primary', 'ak-nav__list' ); ?>
			</nav>

			<div class="ak-header__right">
				<a class="ak-btn ak-btn--primary" href="<?php echo esc_url( alkhoory_option( 'header_cta_url', '#contact' ) ); ?>">
					<?php echo esc_html( alkhoory_option( 'header_cta_label', 'Contact Us' ) ); ?>
				</a>
				<span class="ak-header__divider" aria-hidden="true"></span>
				<a class="ak-header__group" href="<?php echo esc_url( alkhoory_option( 'group_logo_url', '#' ) ); ?>">
					<img src="<?php echo esc_url( alkhoory_image_url( 'alkhoory_group_logo', 'logo-akg.png' ) ); ?>" width="32" height="49" alt="<?php esc_attr_e( 'Al Khoory Group', 'alkhoory' ); ?>">
				</a>
			</div>
		</div>
	</div>
</header>

<main id="ak-content" class="ak-main">
