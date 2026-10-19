<?php
/**
 * Menu rendering with built-in fallbacks, so the header/footer are complete even before menus are assigned
 * in Appearance > Menus. The header menu supports one dropdown level.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Fallback links per location. Each entry: label => path, or label => array( path, array( child label => path ) ).
 * Paths are relative to the site home.
 */
function yutong_uae_fallback_links( $location ) {
	$models = array(
		'Coaches'    => '/models/#coaches',
		'E-Coaches'  => '/models/#e-coaches',
		'City Buses' => '/models/#city-buses',
		'E-Buses'    => '/models/#e-buses',
		'Vans'       => '/models/#vans',
	);
	$support = array(
		'Service Centres' => '/ownership-support/service-centres/',
		'Spare Parts'     => '/ownership-support/spare-parts/',
		'Brochures'       => '/ownership-support/brochures/',
	);
	$news = array(
		'Press Releases' => '/news/',
		'Awards'         => '/awards/',
		'Media Library'  => '/library/',
	);
	$links = array(
		'yutong-primary'  => array(
			'Home'                => '/',
			'Models'              => array( '/models/', $models ),
			'About Us'            => '/about/',
			'Ownership & Support' => array( '/ownership-support/', $support ),
			'News'                => array( '/news/', $news ),
			'Contact Us'          => '/contact/',
		),
		'yutong-footer-quick' => array(
			'About Us'        => '/about/',
			'Contact Us'      => '/contact/',
			'Request a Quote' => '/request-a-quote/',
		),
		'yutong-footer-1'     => array_merge( array( 'All Models' => '/models/' ), $models ),
		'yutong-footer-2'     => $support,
		'yutong-footer-news'  => $news,
		'yutong-legal'        => array(
			'Privacy Policy' => '/privacy-policy/',
			'Terms of Use'   => '/terms-of-use/',
		),
	);
	return $links[ $location ] ?? array();
}

/**
 * Footer column headings: the assigned menu's name, else the default.
 */
function yutong_uae_footer_heading( $location ) {
	$headings = array(
		'yutong-footer-quick' => 'Quick Links',
		'yutong-footer-1'     => 'Models',
		'yutong-footer-2'     => 'Ownership & Support',
		'yutong-footer-news'  => 'News',
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
 * Is a fallback path the current page (or a section the current page sits under)?
 */
function yutong_uae_is_current( $path ) {
	if ( '#' === $path[0] || false !== strpos( $path, '#' ) ) {
		return false;
	}
	if ( '/' === $path ) {
		return is_front_page();
	}
	$current = trailingslashit( wp_parse_url( home_url( add_query_arg( array() ) ), PHP_URL_PATH ) ?: '/' );
	$target  = trailingslashit( wp_parse_url( home_url( $path ), PHP_URL_PATH ) ?: '/' );
	return 0 === strpos( $current, $target );
}

/**
 * Print a menu location as a <ul>. Uses the assigned WP menu, otherwise the fallback list.
 */
function yutong_uae_menu( $location, $class, $depth = 1 ) {
	if ( has_nav_menu( $location ) ) {
		wp_nav_menu(
			array(
				'theme_location' => $location,
				'container'      => false,
				'menu_class'     => $class,
				'depth'          => $depth,
				'fallback_cb'    => false,
			)
		);
		return;
	}
	yutong_uae_fallback_list( yutong_uae_fallback_links( $location ), $class, $depth );
}

function yutong_uae_fallback_list( $items, $class, $depth ) {
	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( $items as $label => $item ) {
		$path     = is_array( $item ) ? $item[0] : $item;
		$children = ( is_array( $item ) && $depth > 1 ) ? $item[1] : array();
		$classes  = 'menu-item';
		if ( yutong_uae_is_current( $path ) ) {
			$classes .= ' current-menu-item';
		}
		if ( $children ) {
			$classes .= ' menu-item-has-children';
		}
		$href = '#' === $path ? '#' : home_url( $path );
		printf( '<li class="%s"><a href="%s">%s</a>', esc_attr( $classes ), esc_url( $href ), esc_html( $label ) );
		if ( $children ) {
			yutong_uae_fallback_list( $children, 'sub-menu', 1 );
		}
		echo '</li>';
	}
	echo '</ul>';
}

/**
 * "Request a Quote" page URL (the header shows it as a button, not a menu link).
 */
function yutong_uae_quote_url() {
	$page = get_page_by_path( 'request-a-quote' );
	return $page ? get_permalink( $page ) : home_url( '/request-a-quote/' );
}

/**
 * Drop a "Request a Quote" item from the header menu: the header already has the button.
 */
add_filter( 'wp_nav_menu_objects', function ( $items, $args ) {
	if ( 'yutong-primary' !== ( $args->theme_location ?? '' ) ) {
		return $items;
	}
	$quote = untrailingslashit( yutong_uae_quote_url() );
	return array_filter( $items, function ( $item ) use ( $quote ) {
		return untrailingslashit( strtok( $item->url, '#?' ) ) !== $quote;
	} );
}, 10, 2 );
