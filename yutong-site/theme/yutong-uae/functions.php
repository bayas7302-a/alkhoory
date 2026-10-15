<?php
/**
 * Yutong UAE child theme.
 *
 * Common header + footer for every page (Elementor Full Width template), menu locations,
 * contact details in the Customizer, Contact Form 7 styling.
 */

defined( 'ABSPATH' ) || exit;

define( 'YUTONG_UAE_VERSION', '1.2.0' );

require_once __DIR__ . '/inc/customizer.php';
require_once __DIR__ . '/inc/menus.php';
require_once __DIR__ . '/inc/icons.php';
require_once __DIR__ . '/inc/search.php';

/**
 * Theme setup: menu locations and logo support.
 */
add_action( 'after_setup_theme', function () {
	register_nav_menus(
		array(
			'yutong-primary'  => __( 'Header menu', 'yutong-uae' ),
			'yutong-footer-1' => __( 'Footer column 1 (Quick Links)', 'yutong-uae' ),
			'yutong-footer-2' => __( 'Footer column 2 (Models)', 'yutong-uae' ),
			'yutong-footer-3' => __( 'Footer column 3 (Ownership & Support)', 'yutong-uae' ),
			'yutong-footer-4' => __( 'Footer column 4 (News)', 'yutong-uae' ),
			'yutong-legal'    => __( 'Footer legal links', 'yutong-uae' ),
		)
	);
	add_theme_support( 'custom-logo' );
}, 20 );

/**
 * Hello Elementor prints its own header/footer and page title; this theme replaces them.
 */
add_filter( 'hello_elementor_page_title', '__return_false' );
add_filter( 'hello_elementor_header_footer', '__return_false' );

/**
 * Styles and fonts.
 */
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style(
		'yutong-uae-fonts',
		'https://fonts.googleapis.com/css2?family=Lexend:wght@500;600;700&family=Poppins:wght@300;400;500;600&display=swap',
		array(),
		null
	);
	wp_enqueue_style(
		'yutong-uae',
		get_stylesheet_directory_uri() . '/assets/css/theme.css',
		array( 'hello-elementor' ),
		YUTONG_UAE_VERSION . '.' . filemtime( __DIR__ . '/assets/css/theme.css' )
	);
}, 20 );

add_action( 'wp_head', function () {
	echo '<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}, 1 );

/**
 * Contact Form 7: no automatic <p>/<br> so the form markup stays a clean flex layout.
 */
add_filter( 'wpcf7_autop_or_not', '__return_false' );

/**
 * Asset URL inside the theme.
 */
function yutong_uae_asset( $path ) {
	return esc_url( get_stylesheet_directory_uri() . '/assets/' . ltrim( $path, '/' ) );
}
