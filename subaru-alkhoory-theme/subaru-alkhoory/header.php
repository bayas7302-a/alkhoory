<?php
/**
 * Site header: top bar, sticky main navigation (mega menu, dropdowns), search overlay, mobile menu.
 *
 * Overrides Hello Elementor's header.php. Used by every template, including Elementor's
 * "Elementor Full Width" (elementor_header_footer) template which calls get_header().
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$subaru_viewport = apply_filters( 'hello_elementor_viewport_content', 'width=device-width, initial-scale=1' );
$subaru_cta      = get_theme_mod( 'subaru_cta_label', 'Book a Test Drive' );
$subaru_topbar   = get_theme_mod( 'subaru_topbar_text', 'Official Subaru Distributor in Dubai & the Northern Emirates' );
$subaru_name     = get_bloginfo( 'name' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="<?php echo esc_attr( $subaru_viewport ); ?>">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#sa-main"><?php esc_html_e( 'Skip to content', 'subaru-alkhoory' ); ?></a>

<div class="sa-intro-screen" aria-hidden="true">
	<img src="<?php echo esc_url( subaru_logo_url( 'light' ) ); ?>" alt="" width="240" height="38">
	<span class="sa-intro-screen__line"></span>
</div>

<div class="sa-progress" aria-hidden="true"><span></span></div>

<?php
// Al Khoory Automobiles logo: Customizer upload (dark version), else the bundled pair.
$subaru_aka       = get_theme_mod( 'subaru_aka_logo' );
$subaru_aka_light = SUBARU_URI . '/assets/img/al-khoory-automobiles-white.png';
if ( ! $subaru_aka && file_exists( SUBARU_DIR . '/assets/img/al-khoory-automobiles.png' ) ) {
	$subaru_aka = SUBARU_URI . '/assets/img/al-khoory-automobiles.png';
}
?>
<header class="sa-header" id="sa-header" data-sa-header>
	<div class="sa-topbar">
		<div class="sa-topbar__inner">
			<p class="sa-topbar__text"><?php echo esc_html( $subaru_topbar ); ?></p>
			<nav class="sa-topbar__nav" aria-label="<?php esc_attr_e( 'Quick actions', 'subaru-alkhoory' ); ?>">
				<?php subaru_render_links( 'topbar', 'sa-topbar__list', true ); ?>
			</nav>
			<?php if ( $subaru_aka ) : ?>
				<img class="sa-topbar__aka" src="<?php echo esc_url( $subaru_aka_light ); ?>" alt="<?php esc_attr_e( 'Al Khoory Automobiles', 'subaru-alkhoory' ); ?>" width="201" height="18">
			<?php endif; ?>
		</div>
	</div>

	<div class="sa-mainbar">
		<div class="sa-mainbar__inner">
			<div class="sa-brand">
				<?php if ( get_theme_mod( 'subaru_show_badge', true ) ) : ?>
					<img class="sa-badge50" src="<?php echo esc_url( subaru_image_url( 'subaru_badge', '50-years-badge.png' ) ); ?>" alt="<?php esc_attr_e( '50 Years of trust — Al Khoory Automobiles', 'subaru-alkhoory' ); ?>" width="62" height="38">
					<span class="sa-brand__sep" aria-hidden="true"></span>
				<?php endif; ?>
				<a class="sa-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<img class="sa-logo__dark" src="<?php echo esc_url( subaru_logo_url( 'dark' ) ); ?>" alt="<?php echo esc_attr( $subaru_name ); ?>" width="150" height="24">
					<img class="sa-logo__light" src="<?php echo esc_url( subaru_logo_url( 'light' ) ); ?>" alt="" aria-hidden="true" width="150" height="24">
				</a>
			</div>

			<nav class="sa-nav" id="sa-nav" aria-label="<?php esc_attr_e( 'Main', 'subaru-alkhoory' ); ?>">
				<?php subaru_render_primary_nav(); ?>
			</nav>

			<div class="sa-actions">
				<button type="button" class="sa-iconbtn sa-search-toggle" aria-controls="sa-search" aria-expanded="false">
					<?php subaru_the_icon( 'search', 20 ); ?>
					<span class="screen-reader-text"><?php esc_html_e( 'Search', 'subaru-alkhoory' ); ?></span>
				</button>
				<a class="sa-btn sa-btn--sm sa-actions__cta" href="<?php echo esc_url( subaru_cta_url() ); ?>"><?php echo esc_html( $subaru_cta ); ?></a>
				<?php
				// Tablet: the nav collapses to the burger, so the logo moves into the main bar.
				if ( $subaru_aka ) :
					?>
					<span class="sa-actions__sep sa-aka-sep" aria-hidden="true"></span>
					<span class="sa-aka-logo" aria-hidden="true">
						<img class="sa-aka-logo__dark" src="<?php echo esc_url( $subaru_aka ); ?>" alt="" width="246" height="22">
						<img class="sa-aka-logo__light" src="<?php echo esc_url( $subaru_aka_light ); ?>" alt="" aria-hidden="true" width="246" height="22">
					</span>
				<?php endif; ?>
				<button type="button" class="sa-burger" aria-controls="sa-mmenu" aria-expanded="false">
					<span class="sa-burger__lines" aria-hidden="true"><span></span><span></span><span></span></span>
					<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'subaru-alkhoory' ); ?></span>
				</button>
			</div>
		</div>
	</div>
</header>

<div class="sa-search" id="sa-search" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Search the site', 'subaru-alkhoory' ); ?>" hidden>
	<div class="sa-search__panel">
		<form class="sa-search__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="sa-search__label" for="sa-search-input"><?php esc_html_e( 'What are you looking for?', 'subaru-alkhoory' ); ?></label>
			<div class="sa-search__row">
				<?php subaru_the_icon( 'search', 24 ); ?>
				<input type="search" id="sa-search-input" name="s" placeholder="<?php esc_attr_e( 'Search models, technology, news…', 'subaru-alkhoory' ); ?>" autocomplete="off">
				<button type="submit" class="sa-btn"><?php esc_html_e( 'Search', 'subaru-alkhoory' ); ?></button>
			</div>
			<div class="sa-search__suggest">
				<span><?php esc_html_e( 'Popular:', 'subaru-alkhoory' ); ?></span>
				<?php foreach ( subaru_models() as $subaru_m ) : ?>
					<a href="<?php echo esc_url( home_url( $subaru_m['path'] ) ); ?>"><?php echo esc_html( $subaru_m['name'] ); ?></a>
				<?php endforeach; ?>
				<a href="<?php echo esc_url( home_url( '/current-offers/' ) ); ?>"><?php esc_html_e( 'Offers', 'subaru-alkhoory' ); ?></a>
			</div>
		</form>
		<button type="button" class="sa-iconbtn sa-search__close" data-sa-close>
			<?php subaru_the_icon( 'close', 24 ); ?>
			<span class="screen-reader-text"><?php esc_html_e( 'Close search', 'subaru-alkhoory' ); ?></span>
		</button>
	</div>
	<div class="sa-search__backdrop" data-sa-close></div>
</div>

<div class="sa-mmenu" id="sa-mmenu" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Menu', 'subaru-alkhoory' ); ?>" hidden>
	<div class="sa-mmenu__inner">
		<nav class="sa-mnav" aria-label="<?php esc_attr_e( 'Mobile', 'subaru-alkhoory' ); ?>">
			<?php subaru_render_mobile_nav(); ?>
		</nav>
		<div class="sa-mmenu__cta">
			<a class="sa-btn" href="<?php echo esc_url( subaru_cta_url() ); ?>"><?php echo esc_html( $subaru_cta ); ?></a>
		</div>
		<div class="sa-mmenu__quick">
			<?php subaru_render_links( 'topbar', 'sa-mmenu__quicklist', true ); ?>
		</div>
	</div>
</div>

<div class="sa-header-spacer" aria-hidden="true"></div>
<span id="sa-main" class="sa-anchor" tabindex="-1"></span>
