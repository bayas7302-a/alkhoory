<?php
/**
 * Al Khoory Automobiles theme functions.
 *
 * Lightweight classic theme built for Elementor (free). The header and footer
 * live in the theme because free Elementor has no Theme Builder; page bodies
 * are built in Elementor using the "Elementor Full Width" template.
 *
 * @package Alkhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ALKHOORY_VERSION', '1.0.0' );

/**
 * Theme setup.
 */
function alkhoory_setup() {
	load_theme_textdomain( 'alkhoory', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 55,
			'width'       => 480,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary'         => __( 'Primary (Header)', 'alkhoory' ),
			'footer_quick'    => __( 'Footer: Quick Links', 'alkhoory' ),
			'footer_brands'   => __( 'Footer: Our Brands', 'alkhoory' ),
			'footer_services' => __( 'Footer: Services', 'alkhoory' ),
			'footer_legal'    => __( 'Footer: Legal', 'alkhoory' ),
		)
	);
}
add_action( 'after_setup_theme', 'alkhoory_setup' );

/**
 * Front-end assets.
 */
function alkhoory_enqueue_assets() {
	wp_enqueue_style(
		'alkhoory-fonts',
		'https://fonts.googleapis.com/css2?family=Archivo:wght@400;500&family=Poppins:wght@500;600&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'alkhoory-style', get_stylesheet_uri(), array( 'alkhoory-fonts' ), ALKHOORY_VERSION );
	wp_enqueue_script( 'alkhoory-nav', get_template_directory_uri() . '/assets/js/navigation.js', array(), ALKHOORY_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'alkhoory_enqueue_assets' );

/**
 * Preconnect to Google Fonts.
 *
 * @param array  $urls          URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed for.
 * @return array
 */
function alkhoory_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'alkhoory_resource_hints', 10, 2 );

/**
 * Customizer: header button, group logo and footer contact details.
 *
 * @param WP_Customize_Manager $wp_customize Customizer object.
 */
function alkhoory_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'alkhoory_options',
		array(
			'title'    => __( 'Al Khoory Theme Options', 'alkhoory' ),
			'priority' => 30,
		)
	);

	$fields = array(
		'header_cta_label' => array( __( 'Header button label', 'alkhoory' ), 'Contact Us', 'sanitize_text_field' ),
		'header_cta_url'   => array( __( 'Header button URL', 'alkhoory' ), '#contact', 'esc_url_raw' ),
		'group_logo_url'   => array( __( 'Group logo link URL', 'alkhoory' ), '#', 'esc_url_raw' ),
		'footer_about'     => array( __( 'Footer description', 'alkhoory' ), 'Five decades of trusted mobility across the UAE — premium passenger cars, commercial vans and luxury coaches, backed by certified after-sales care.', 'sanitize_textarea_field' ),
		'contact_phone'    => array( __( 'Phone', 'alkhoory' ), '+971 4 314 6146', 'sanitize_text_field' ),
		'contact_email'    => array( __( 'Email', 'alkhoory' ), 'info@alkhoory.com', 'sanitize_email' ),
		'contact_address'  => array( __( 'Address', 'alkhoory' ), "23 Sheikh Zayed Rd, Al Quoz\nIndustrial Area 3, Dubai, UAE", 'sanitize_textarea_field' ),
		'copyright'        => array( __( 'Copyright text', 'alkhoory' ), '© {year} Al Khoory Automobiles LLC. All rights reserved.', 'sanitize_text_field' ),
	);

	foreach ( $fields as $id => $field ) {
		$wp_customize->add_setting(
			'alkhoory_' . $id,
			array(
				'default'           => $field[1],
				'sanitize_callback' => $field[2],
			)
		);
		$wp_customize->add_control(
			'alkhoory_' . $id,
			array(
				'label'   => $field[0],
				'section' => 'alkhoory_options',
				'type'    => in_array( $id, array( 'footer_about', 'contact_address' ), true ) ? 'textarea' : 'text',
			)
		);
	}

	$wp_customize->add_setting( 'alkhoory_footer_logo', array( 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'alkhoory_footer_logo',
			array(
				'label'     => __( 'Footer logo (light version)', 'alkhoory' ),
				'section'   => 'alkhoory_options',
				'mime_type' => 'image',
			)
		)
	);

	$wp_customize->add_setting( 'alkhoory_group_logo', array( 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'alkhoory_group_logo',
			array(
				'label'     => __( 'Group logo (header, right)', 'alkhoory' ),
				'section'   => 'alkhoory_options',
				'mime_type' => 'image',
			)
		)
	);
}
add_action( 'customize_register', 'alkhoory_customize_register' );

/**
 * Get a theme option with its default.
 *
 * @param string $key     Option key without the alkhoory_ prefix.
 * @param string $default Fallback value.
 * @return string
 */
function alkhoory_option( $key, $default = '' ) {
	return get_theme_mod( 'alkhoory_' . $key, $default );
}

/**
 * URL of a bundled image, or of a Customizer media override.
 *
 * @param string $mod  Theme mod holding an attachment ID.
 * @param string $file Bundled file name in assets/images.
 * @return string
 */
function alkhoory_image_url( $mod, $file ) {
	$id = absint( get_theme_mod( $mod ) );
	if ( $id ) {
		$url = wp_get_attachment_image_url( $id, 'full' );
		if ( $url ) {
			return $url;
		}
	}
	return get_template_directory_uri() . '/assets/images/' . $file;
}

/**
 * Fallback links used until real menus are assigned in Appearance → Menus.
 *
 * @param string $location Menu location.
 * @return array<string,string> Label => URL.
 */
function alkhoory_default_links( $location ) {
	$home = home_url( '/' );
	$sets = array(
		'primary'         => array(
			'Home'          => $home,
			'About Us'      => '#about',
			'Our Divisions' => '#brands',
			'News & Events' => '#insights',
			'Careers'       => '#',
		),
		'footer_quick'    => array(
			'Home'          => $home,
			'About Us'      => '#about',
			'Our Divisions' => '#brands',
			'News & Events' => '#insights',
			'Careers'       => '#',
			'Contact Us'    => '#contact',
		),
		'footer_brands'   => array(
			'Subaru'    => '#brands',
			'Yutong'    => '#brands',
			'King Long' => '#brands',
		),
		'footer_services' => array(
			'Vehicle Sales'       => '#services',
			'Book a Test Drive'   => '#contact',
			'After-Sales Service' => '#services',
			'Genuine Spare Parts' => '#services',
			'Warranty Support'    => '#services',
		),
		'footer_legal'    => array(
			'Privacy Policy'            => get_privacy_policy_url() ? get_privacy_policy_url() : '#',
			'Conditions of Use'         => '#',
			'Cookie & Ad Preferences'   => '#',
			'Your Privacy Choices'      => '#',
		),
	);
	return isset( $sets[ $location ] ) ? $sets[ $location ] : array();
}

/**
 * Print a nav menu, or the design's default links when none is assigned.
 *
 * @param string $location Menu location.
 * @param string $class    Class for the <ul>.
 */
function alkhoory_menu( $location, $class ) {
	if ( has_nav_menu( $location ) ) {
		wp_nav_menu(
			array(
				'theme_location' => $location,
				'container'      => false,
				'menu_class'     => $class,
				'depth'          => 'primary' === $location ? 2 : 1,
				'fallback_cb'    => false,
			)
		);
		return;
	}

	echo '<ul class="' . esc_attr( $class ) . '">';
	$first = true;
	foreach ( alkhoory_default_links( $location ) as $label => $url ) {
		$current = ( 'primary' === $location && $first && is_front_page() ) ? ' current-menu-item' : '';
		printf( '<li class="menu-item%s"><a href="%s">%s</a></li>', esc_attr( $current ), esc_url( $url ), esc_html( $label ) );
		$first = false;
	}
	echo '</ul>';
}
