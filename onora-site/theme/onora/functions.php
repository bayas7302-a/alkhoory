<?php
/**
 * ONORA Hotel Supplies theme functions.
 */

defined( 'ABSPATH' ) || exit;

define( 'ONORA_VERSION', '1.0.1' );

/** Business details shown in the header and footer. */
function onora_contact( $key ) {
	$data = array(
		'phone'      => '+971 50 203 8005',
		'phone_href' => 'tel:+971502038005',
		'email'      => 'jason@onorahs.com',
		'website'    => 'onorahs.com',
		'city'       => 'Karama, Dubai — UAE',
		'address'    => 'Office M-A98, Hamra Building, Karama, Dubai — UAE',
		'tagline'    => 'Premium supplies for hotels, restaurants & cafés',
		'instagram'  => 'https://www.instagram.com/',
		'linkedin'   => 'https://www.linkedin.com/',
		'facebook'   => 'https://www.facebook.com/',
	);
	return isset( $data[ $key ] ) ? $data[ $key ] : '';
}

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	register_nav_menus( array(
		'primary'     => __( 'Main menu', 'onora' ),
		'collections' => __( 'Footer collections', 'onora' ),
	) );
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'onora-fonts', 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600&family=Montserrat:wght@400;500;600&display=swap', array(), null );
	wp_enqueue_style( 'onora-theme', get_template_directory_uri() . '/assets/css/theme.css', array(), ONORA_VERSION );
	wp_enqueue_script( 'onora-theme', get_template_directory_uri() . '/assets/js/theme.js', array(), ONORA_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );
} );

/** Preconnect to Google Fonts. */
add_filter( 'wp_resource_hints', function ( $urls, $relation ) {
	if ( 'preconnect' === $relation ) {
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}
	return $urls;
}, 10, 2 );

/** Default menu items used until menus are assigned in Appearance → Menus. */
function onora_default_menu( $location ) {
	$base = home_url( '/' );
	if ( 'collections' === $location ) {
		$items = array( 'Chinaware' => 'chinaware', 'Cutlery' => 'cutlery', 'Glassware' => 'glassware', 'Buffet' => 'buffet', 'Bar Items' => 'bar-items', 'Table Lamps' => 'table-lamps' );
		return array_map( function ( $slug ) use ( $base ) { return $base . 'products/#' . $slug; }, $items );
	}
	return array(
		'Home'       => $base,
		'About Us'   => $base . 'about-us/',
		'Brands'     => $base . 'brands/',
		'Products'   => $base . 'products/',
		'Contact Us' => $base . 'contact-us/',
	);
}

/** Prints a menu location as a plain list of links (assigned WP menu, or the defaults). */
function onora_menu( $location, $class ) {
	if ( has_nav_menu( $location ) ) {
		wp_nav_menu( array(
			'theme_location' => $location,
			'container'      => false,
			'menu_class'     => $class,
			'depth'          => 1,
		) );
		return;
	}
	$current = is_front_page() ? home_url( '/' ) : ( is_singular() ? get_permalink() : '' );
	$current = $current ? trailingslashit( $current ) : '';
	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( onora_default_menu( $location ) as $label => $url ) {
		$active = ( 'primary' === $location && trailingslashit( $url ) === $current ) ? ' current-menu-item' : '';
		printf( '<li class="menu-item%s"><a href="%s"%s>%s</a></li>', esc_attr( $active ), esc_url( $url ), $active ? ' aria-current="page"' : '', esc_html( $label ) );
	}
	echo '</ul>';
}

/** Logo image from the theme folder (or the Customizer logo if one is set). */
function onora_logo( $variant = 'navy' ) {
	if ( 'navy' === $variant && has_custom_logo() ) {
		the_custom_logo();
		return;
	}
	printf(
		'<a class="onora-logo onora-logo--%1$s" href="%2$s" rel="home"><img src="%3$s" alt="%4$s" width="%5$d" height="%6$d"></a>',
		esc_attr( $variant ),
		esc_url( home_url( '/' ) ),
		esc_url( get_template_directory_uri() . '/assets/img/onora-logo-' . $variant . '.png' ),
		esc_attr( get_bloginfo( 'name' ) ?: 'ONORA Hotel Supplies' ),
		'navy' === $variant ? 172 : 238,
		'navy' === $variant ? 72 : 98
	);
}
