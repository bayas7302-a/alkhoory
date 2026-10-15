<?php
/**
 * Menu rendering with built-in fallbacks, so the header/footer are complete even before menus are assigned
 * in Appearance > Menus.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Fallback links per location: label => path relative to the site home.
 */
function yutong_uae_fallback_links( $location ) {
	$links = array(
		'yutong-primary'  => array(
			'Home'         => '/',
			'Models'       => '/models/',
			'Technologies' => '/models/#technology',
			'About'        => '/about/',
			'News'         => '/news/',
			'Contact'      => '/contact/',
		),
		'yutong-footer-1' => array(
			'About Us'        => '/about/',
			'Contact Us'      => '/contact/',
			'Media Library'   => '/library/',
		),
		'yutong-footer-2' => array(
			'All Models' => '/models/',
			'Coaches'    => '/models/',
			'Vans'       => '/models/',
			'E-Buses'    => '/models/',
		),
		'yutong-footer-3' => array(
			'Service Centres' => '/ownership-support/#service-network',
			'Spare Parts'     => '/ownership-support/#spare-parts',
		),
		'yutong-footer-4' => array(
			'Press Releases' => '/news/',
			'Awards'         => '/awards/',
			'Media Library'  => '/library/',
		),
		'yutong-legal'    => array(
			'Your Privacy Choices'      => '#',
			'Cookie and Ad Preferences' => '#',
			'Conditions of Use'         => '#',
			'Privacy Policy'            => '#',
		),
	);
	return $links[ $location ] ?? array();
}

/**
 * Footer column headings (used when the assigned menu has no name override).
 */
function yutong_uae_footer_heading( $location ) {
	$headings = array(
		'yutong-footer-1' => 'Quick Links',
		'yutong-footer-2' => 'Models',
		'yutong-footer-3' => 'Ownership & Support',
		'yutong-footer-4' => 'News',
	);
	$locations = get_nav_menu_locations();
	if ( ! empty( $locations[ $location ] ) ) {
		$menu = wp_get_nav_menu_object( $locations[ $location ] );
		if ( $menu ) {
			return html_entity_decode( $menu->name, ENT_QUOTES, 'UTF-8' ); // stored escaped; printed with esc_html()
		}
	}
	return $headings[ $location ] ?? '';
}

/**
 * Is a fallback path the current page? (Mirrors WP's current-menu-item for the fallback list.)
 */
function yutong_uae_is_current( $path ) {
	if ( '#' === $path[0] || false !== strpos( $path, '#' ) ) {
		return false;
	}
	$current = trailingslashit( wp_parse_url( home_url( add_query_arg( array() ) ), PHP_URL_PATH ) ?: '/' );
	$target  = trailingslashit( wp_parse_url( home_url( $path ), PHP_URL_PATH ) ?: '/' );
	if ( '/' === $path ) {
		return is_front_page();
	}
	return 0 === strpos( $current, $target );
}

/**
 * Print a menu location as a <ul>. Uses the assigned WP menu, otherwise the fallback list.
 */
function yutong_uae_menu( $location, $class ) {
	if ( has_nav_menu( $location ) ) {
		wp_nav_menu(
			array(
				'theme_location' => $location,
				'container'      => false,
				'menu_class'     => $class,
				'depth'          => 1,
				'fallback_cb'    => false,
			)
		);
		return;
	}
	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( yutong_uae_fallback_links( $location ) as $label => $path ) {
		$href    = '#' === $path ? '#' : home_url( $path );
		$current = yutong_uae_is_current( $path ) ? ' current-menu-item' : '';
		printf( '<li class="menu-item%s"><a href="%s">%s</a></li>', esc_attr( $current ), esc_url( $href ), esc_html( $label ) );
	}
	echo '</ul>';
}
