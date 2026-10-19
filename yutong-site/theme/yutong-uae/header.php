<?php
/**
 * Common site header (floats over each page's hero).
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'yt-site' ); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'yutong-uae' ); ?></a>

<header class="yt-header" role="banner">
	<div class="yt-header__bar">
		<a class="yt-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<img src="<?php echo yutong_uae_asset( 'img/logo-yutong.png' ); ?>" width="148" height="32" alt="Yutong">
		</a>

		<nav class="yt-header__nav" aria-label="<?php esc_attr_e( 'Main', 'yutong-uae' ); ?>">
			<?php yutong_uae_menu( 'yutong-primary', 'yt-menu', 2 ); ?>
		</nav>

		<div class="yt-header__actions">
			<details class="yt-search">
				<summary class="yt-header__search" aria-label="<?php esc_attr_e( 'Search', 'yutong-uae' ); ?>">
					<?php echo yutong_uae_icon( 'search', 18 ) . yutong_uae_icon( 'close', 18 ); ?>
				</summary>
				<div class="yt-search__panel">
					<?php yutong_uae_search_form( 'header' ); ?>
				</div>
			</details>
			<a class="yt-btn yt-btn--sm yt-header__quote" href="<?php echo esc_url( yutong_uae_quote_url() ); ?>">
				<?php esc_html_e( 'Request a Quote', 'yutong-uae' ); ?><?php echo yutong_uae_icon( 'arrow', 15 ); ?>
			</a>
			<img class="yt-header__dealer" src="<?php echo yutong_uae_asset( 'img/logo-ak-automobiles.png' ); ?>" width="188" height="17" alt="Al Khoory Automobiles LLC">

			<details class="yt-mobile-menu">
				<summary aria-label="<?php esc_attr_e( 'Menu', 'yutong-uae' ); ?>">
					<?php echo yutong_uae_icon( 'menu', 20 ) . yutong_uae_icon( 'close', 20 ); ?>
				</summary>
				<div class="yt-mobile-menu__panel">
					<div class="yt-mobile-menu__search"><?php yutong_uae_search_form( 'mobile' ); ?></div>
					<?php yutong_uae_menu( 'yutong-primary', 'yt-mobile-menu__list', 2 ); ?>
					<a class="yt-btn yt-mobile-menu__quote" href="<?php echo esc_url( yutong_uae_quote_url() ); ?>">
						<?php esc_html_e( 'Request a Quote', 'yutong-uae' ); ?><?php echo yutong_uae_icon( 'arrow', 15 ); ?>
					</a>
					<div class="yt-mobile-menu__dealer">
						<span><?php esc_html_e( 'Official distributor in the UAE', 'yutong-uae' ); ?></span>
						<img src="<?php echo yutong_uae_asset( 'img/logo-ak-automobiles.png' ); ?>" width="188" height="17" alt="Al Khoory Automobiles LLC">
					</div>
				</div>
			</details>
		</div>
	</div>
</header>

<main id="content" class="yt-main">
