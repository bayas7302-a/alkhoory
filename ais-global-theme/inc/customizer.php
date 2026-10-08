<?php
/**
 * Customizer options: header button, footer source, fonts.
 *
 * @package AIS_Global
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register settings.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function ais_global_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'ais_global_options',
		array(
			'title'    => __( 'AIS Theme Options', 'ais-global' ),
			'priority' => 30,
		)
	);

	$fields = array(
		'ais_header_btn_text' => array( __( 'Header button text', 'ais-global' ), 'Contact Us', 'sanitize_text_field', 'text' ),
		'ais_header_btn_url'  => array( __( 'Header button link', 'ais-global' ), '/contact-us/', 'esc_url_raw', 'url' ),
		'ais_header_sticky'   => array( __( 'Sticky header', 'ais-global' ), false, 'wp_validate_boolean', 'checkbox' ),
		'ais_footer_page'     => array( __( 'Footer: Elementor page/template ID (leave 0 for the built-in footer)', 'ais-global' ), 0, 'absint', 'number' ),
		'ais_copyright'       => array( __( 'Built-in footer copyright text', 'ais-global' ), '© Copyright 2026, All Rights Reserved by AIS Global Group', 'sanitize_text_field', 'text' ),
		'ais_cal_sans_url'    => array( __( 'Cal Sans font file URL (.ttf/.woff2)', 'ais-global' ), content_url( 'uploads/2026/03/CalSans-Regular.ttf' ), 'esc_url_raw', 'url' ),
	);
	foreach ( $fields as $id => $f ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $f[1],
				'sanitize_callback' => $f[2],
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $f[0],
				'section' => 'ais_global_options',
				'type'    => $f[3],
			)
		);
	}
}
add_action( 'customize_register', 'ais_global_customize_register' );

/**
 * Header button URL (relative URLs are resolved against home).
 */
function ais_global_header_btn_url() {
	$url = get_theme_mod( 'ais_header_btn_url', '/contact-us/' );
	if ( $url && 0 === strpos( $url, '/' ) ) {
		$url = home_url( $url );
	}
	return $url;
}
