<?php
/**
 * Theme setup, menus and assets.
 *
 * @package abp
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', function () {
	register_nav_menus(
		array(
			'abp-primary' => __( 'Header menu', 'abp' ),
			'abp-footer-services' => __( 'Footer: Services', 'abp' ),
			'abp-footer-company'  => __( 'Footer: Company', 'abp' ),
		)
	);
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'abp-card', 906, 760, false );
	add_image_size( 'abp-wide', 1600, 1000, false );
} );

add_action( 'wp_enqueue_scripts', function () {
	// Poppins comes from Elementor's Google Fonts loader; this registers the same
	// handle Elementor uses so pages without Elementor content still get it once.
	if ( ! wp_style_is( 'elementor-gf-poppins', 'registered' ) ) {
		wp_register_style( 'elementor-gf-poppins', 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap', array(), null );
	}
	wp_enqueue_style( 'elementor-gf-poppins' );

	wp_enqueue_style( 'abp-main', ABP_URI . '/assets/css/main.css', array(), ABP_VERSION );
	wp_add_inline_style( 'abp-main', abp_font_face_css() );
	wp_enqueue_script( 'abp-main', ABP_URI . '/assets/js/main.js', array(), ABP_VERSION, true );
}, 20 );

add_filter( 'body_class', function ( $classes ) {
	$classes[] = 'abp-site';
	return $classes;
} );

/**
 * Default menu items, used until a menu is assigned under Appearance → Menus.
 */
function abp_default_menu( $location ) {
	$menus = array(
		'abp-primary' => array(
			array( 'Home', '/' ),
			array( 'About', '/about/' ),
			array( 'Services', '/services/' ),
			array( 'Projects', '/projects/' ),
			array( 'Clients', '/about/#clientele' ),
			array( 'Contact', '/contact/' ),
		),
		'abp-footer-services' => array(
			array( 'Interior & Fitouts', '/services/#interior-fitouts' ),
			array( 'Rental Services', '/services/#rental-services' ),
			array( 'Events Production', '/services/#events-production' ),
			array( 'Bespoke Printing', '/services/#bespoke-printing' ),
			array( 'Bespoke Furniture', '/services/#bespoke-furniture' ),
			array( 'Deep Cleaning', '/services/#deep-cleaning' ),
			array( 'Landscape Designing', '/services/#landscape-designing' ),
		),
		'abp-footer-company' => array(
			array( 'About Us', '/about/' ),
			array( 'Our Projects', '/projects/' ),
			array( 'Clientele', '/about/#clientele' ),
			array( 'Industries', '/#industries' ),
			array( 'Contact', '/contact/' ),
		),
	);
	return isset( $menus[ $location ] ) ? $menus[ $location ] : array();
}

/**
 * Print a menu location, falling back to the default links.
 */
function abp_menu( $location, $class ) {
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
	global $wp;
	$current = trailingslashit( home_url( isset( $wp->request ) ? $wp->request : '' ) );
	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( abp_default_menu( $location ) as $item ) {
		$url    = home_url( $item[1] );
		$active = ( false === strpos( $item[1], '#' ) && trailingslashit( $url ) === $current ) ? ' current-menu-item' : '';
		printf( '<li class="menu-item%s"><a href="%s">%s</a></li>', esc_attr( $active ), esc_url( $url ), esc_html( $item[0] ) );
	}
	echo '</ul>';
}
