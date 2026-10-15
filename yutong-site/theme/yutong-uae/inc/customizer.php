<?php
/**
 * Contact details shown in the header/footer, editable in Appearance > Customize > Yutong UAE.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Defaults (also used before anything is saved in the Customizer).
 */
function yutong_uae_defaults() {
	return array(
		'about'       => 'Official Yutong distributor in the UAE, with sales, servicing and genuine spare parts for fleet operators, schools and transport companies.',
		'address'     => 'Showroom 12, Sheikh Zayed Road, Al Quoz Industrial Area 3, Dubai, UAE',
		'hours'       => 'Showroom Timing: 09:30 AM to 8:00 PM — Open 7 days a week',
		'email'       => 'info@subaru-uae.com',
		'phone_label' => 'Spare Parts — Tel: 04 3146235',
		'phone'       => '+97143146235',
		'map_url'     => 'https://maps.google.com/?q=Al+Khoory+Automobiles+Al+Quoz+Industrial+Area+3+Dubai',
		'copyright'   => '© {year} Al Khoory Automobiles LLC. All rights reserved.',
	);
}

/**
 * Read one setting with its default.
 */
function yutong_uae_opt( $key ) {
	$defaults = yutong_uae_defaults();
	$value    = get_theme_mod( 'yutong_' . $key, $defaults[ $key ] ?? '' );
	return '' === $value ? ( $defaults[ $key ] ?? '' ) : $value;
}

add_action( 'customize_register', function ( $wp_customize ) {
	$wp_customize->add_section(
		'yutong_uae',
		array(
			'title'    => __( 'Yutong UAE: header & footer', 'yutong-uae' ),
			'priority' => 30,
		)
	);
	$fields = array(
		'about'       => array( __( 'Footer intro text', 'yutong-uae' ), 'textarea', 'sanitize_textarea_field' ),
		'address'     => array( __( 'Showroom address', 'yutong-uae' ), 'textarea', 'sanitize_textarea_field' ),
		'hours'       => array( __( 'Showroom timing', 'yutong-uae' ), 'text', 'sanitize_text_field' ),
		'email'       => array( __( 'E-mail', 'yutong-uae' ), 'email', 'sanitize_email' ),
		'phone_label' => array( __( 'Phone line (as displayed)', 'yutong-uae' ), 'text', 'sanitize_text_field' ),
		'phone'       => array( __( 'Phone number (for tel: link)', 'yutong-uae' ), 'text', 'sanitize_text_field' ),
		'map_url'     => array( __( '"View Location" map link', 'yutong-uae' ), 'url', 'esc_url_raw' ),
		'copyright'   => array( __( 'Copyright line ({year} = current year)', 'yutong-uae' ), 'text', 'sanitize_text_field' ),
	);
	$defaults = yutong_uae_defaults();
	foreach ( $fields as $key => $f ) {
		$wp_customize->add_setting(
			'yutong_' . $key,
			array(
				'default'           => $defaults[ $key ],
				'sanitize_callback' => $f[2],
			)
		);
		$wp_customize->add_control(
			'yutong_' . $key,
			array(
				'label'   => $f[0],
				'section' => 'yutong_uae',
				'type'    => $f[1],
			)
		);
	}
} );
