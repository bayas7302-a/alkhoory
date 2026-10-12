<?php
/**
 * Customizer: logo overrides, header texts & CTA.
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Customizer settings.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function subaru_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'subaru_branding',
		array(
			'title'       => __( 'Subaru Header & Footer', 'subaru-alkhoory' ),
			'priority'    => 25,
			'description' => __( 'Leave images empty to use the bundled defaults. The Site Logo (Site Identity) is used for the dark logo when set.', 'subaru-alkhoory' ),
		)
	);

	$images = array(
		'subaru_logo_dark'  => __( 'Logo (for white header)', 'subaru-alkhoory' ),
		'subaru_logo_light' => __( 'Logo — white version (transparent header / dark backgrounds)', 'subaru-alkhoory' ),
		'subaru_badge'      => __( '50 Years badge', 'subaru-alkhoory' ),
		'subaru_group_logo' => __( 'Footer group logo (Al Khoory Group)', 'subaru-alkhoory' ),
		'subaru_member_logo' => __( 'Footer "A member of" logo', 'subaru-alkhoory' ),
	);
	foreach ( $images as $key => $label ) {
		$wp_customize->add_setting(
			$key,
			array(
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				$key,
				array(
					'label'   => $label,
					'section' => 'subaru_branding',
				)
			)
		);
	}

	$texts = array(
		'subaru_topbar_text' => array( __( 'Top bar text', 'subaru-alkhoory' ), 'Official Subaru Distributor in the UAE  ·  Al Khoory Automobiles' ),
		'subaru_cta_label'   => array( __( 'Header button label', 'subaru-alkhoory' ), 'Book a Test Drive' ),
		'subaru_cta_url'     => array( __( 'Header button URL', 'subaru-alkhoory' ), '/book-a-test-drive/' ),
		'subaru_copyright'   => array( __( 'Copyright holder', 'subaru-alkhoory' ), 'Al Khoory Automobiles LLC' ),
	);
	foreach ( $texts as $key => $data ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $data[1],
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$wp_customize->add_control(
			$key,
			array(
				'label'   => $data[0],
				'section' => 'subaru_branding',
				'type'    => 'text',
			)
		);
	}

	$wp_customize->add_setting(
		'subaru_show_badge',
		array(
			'default'           => true,
			'sanitize_callback' => 'wp_validate_boolean',
		)
	);
	$wp_customize->add_control(
		'subaru_show_badge',
		array(
			'label'   => __( 'Show 50 Years badge in header', 'subaru-alkhoory' ),
			'section' => 'subaru_branding',
			'type'    => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'subaru_customize_register' );

/**
 * Header CTA URL (relative paths resolved against home).
 *
 * @return string
 */
function subaru_cta_url() {
	$url = get_theme_mod( 'subaru_cta_url', '/book-a-test-drive/' );
	if ( $url && '/' === $url[0] ) {
		$url = home_url( $url );
	}
	return $url;
}
