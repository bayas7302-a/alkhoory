<?php
/**
 * Theme setup: supports, menus, image sizes, activation routine, body classes.
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Menu locations => default menu slug (used for auto-assignment and render fallback).
 *
 * @return array
 */
function subaru_menu_locations() {
	return array(
		'primary'          => array( __( 'Primary navigation', 'subaru-alkhoory' ), 'primary-menu' ),
		'topbar'           => array( __( 'Top bar quick actions', 'subaru-alkhoory' ), 'top-bar-menu' ),
		'footer_range'     => array( __( 'Footer — Our Range', 'subaru-alkhoory' ), 'footer-our-range' ),
		'footer_discover'  => array( __( 'Footer — Discover', 'subaru-alkhoory' ), 'footer-discover' ),
		'footer_owners'    => array( __( 'Footer — Owners', 'subaru-alkhoory' ), 'footer-owners' ),
		'footer_resources' => array( __( 'Footer — Resources', 'subaru-alkhoory' ), 'footer-resources' ),
		'legal'            => array( __( 'Legal links', 'subaru-alkhoory' ), 'legal-menu' ),
	);
}

/**
 * Theme supports & menus.
 */
function subaru_setup() {
	load_child_theme_textdomain( 'subaru-alkhoory', SUBARU_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 96,
			'width'       => 300,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_image_size( 'subaru-card', 800, 520, true );
	add_image_size( 'subaru-thumb', 400, 260, true );
	add_image_size( 'subaru-hero', 1920, 1080, true );

	$locations = array();
	foreach ( subaru_menu_locations() as $loc => $data ) {
		$locations[ $loc ] = $data[0];
	}
	register_nav_menus( $locations );
}
add_action( 'after_setup_theme', 'subaru_setup', 11 );

/**
 * Human-readable names for custom image sizes in the media modal.
 *
 * @param array $sizes Sizes.
 * @return array
 */
function subaru_image_size_names( $sizes ) {
	$sizes['subaru-card'] = __( 'Subaru card', 'subaru-alkhoory' );
	$sizes['subaru-hero'] = __( 'Subaru hero', 'subaru-alkhoory' );
	return $sizes;
}
add_filter( 'image_size_names_choose', 'subaru_image_size_names' );

/**
 * On activation, assign existing menus (matched by slug) to empty locations.
 */
function subaru_assign_menus() {
	$assigned = get_theme_mod( 'nav_menu_locations', array() );
	$assigned = is_array( $assigned ) ? $assigned : array();
	$changed  = false;
	foreach ( subaru_menu_locations() as $loc => $data ) {
		if ( ! empty( $assigned[ $loc ] ) && wp_get_nav_menu_object( $assigned[ $loc ] ) ) {
			continue;
		}
		$menu = wp_get_nav_menu_object( $data[1] );
		if ( $menu ) {
			$assigned[ $loc ] = (int) $menu->term_id;
			$changed          = true;
		}
	}
	if ( $changed ) {
		set_theme_mod( 'nav_menu_locations', $assigned );
	}
}
add_action( 'after_switch_theme', 'subaru_assign_menus' );

/**
 * Body classes used by CSS/JS.
 *
 * @param array $classes Classes.
 * @return array
 */
function subaru_body_classes( $classes ) {
	$classes[] = 'sa-theme';
	// Theme templates that start with a dark hero → transparent header.
	if ( is_home() || is_archive() || is_search() || is_404() || ( is_singular( 'post' ) ) ) {
		$classes[] = 'sa-hero-first';
	}
	if ( is_page() && ! subaru_is_elementor_page() && ! is_front_page() ) {
		$classes[] = 'sa-hero-first';
	}
	return $classes;
}
add_filter( 'body_class', 'subaru_body_classes' );

/**
 * Hide Hello Elementor's page title on Elementor-built pages.
 *
 * @param bool $show Show title.
 * @return bool
 */
function subaru_hide_hello_title( $show ) {
	if ( is_singular() && subaru_is_elementor_page() ) {
		return false;
	}
	return $show;
}
add_filter( 'hello_elementor_page_title', 'subaru_hide_hello_title', 20 );

/**
 * The theme renders its own header & footer; Hello's must stay enabled so
 * get_header()/get_footer() run, but we skip Hello's header-footer CSS.
 */
add_filter( 'hello_elementor_header_footer', '__return_true' );

/**
 * Excerpt length / more for cards.
 *
 * @return int
 */
function subaru_excerpt_length() {
	return 24;
}
add_filter( 'excerpt_length', 'subaru_excerpt_length', 20 );

/**
 * Excerpt "more" string.
 *
 * @return string
 */
function subaru_excerpt_more() {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'subaru_excerpt_more', 20 );

/**
 * Content width for embeds in posts.
 */
function subaru_content_width() {
	$GLOBALS['content_width'] = 760;
}
add_action( 'after_setup_theme', 'subaru_content_width', 20 );
