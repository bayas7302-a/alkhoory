<?php
/**
 * Customizer → "AB Productions": contact details and social links used by the
 * header, footer and shortcodes.
 *
 * @package abp
 */

defined( 'ABSPATH' ) || exit;

add_action( 'customize_register', function ( WP_Customize_Manager $wp_customize ) {
	$wp_customize->add_section(
		'abp_contact',
		array(
			'title'    => __( 'AB Productions', 'abp' ),
			'priority' => 30,
		)
	);

	$fields = array(
		'topbar_text'     => array( __( 'Top bar text', 'abp' ), 'text' ),
		'phone_primary'   => array( __( 'Phone (mobile)', 'abp' ), 'text' ),
		'phone_secondary' => array( __( 'Phone (office)', 'abp' ), 'text' ),
		'email'           => array( __( 'Email', 'abp' ), 'text' ),
		'quote_url'       => array( __( '"Get a Quote" link', 'abp' ), 'text' ),
		'hq_title'        => array( __( 'HQ name', 'abp' ), 'text' ),
		'hq_address'      => array( __( 'HQ address', 'abp' ), 'textarea' ),
		'hq_map'          => array( __( 'HQ Google Maps search', 'abp' ), 'text' ),
		'branch_title'    => array( __( 'Branch name', 'abp' ), 'text' ),
		'branch_address'  => array( __( 'Branch address', 'abp' ), 'textarea' ),
		'branch_map'      => array( __( 'Branch Google Maps search', 'abp' ), 'text' ),
		'footer_about'    => array( __( 'Footer text', 'abp' ), 'textarea' ),
		'instagram'       => array( __( 'Instagram URL', 'abp' ), 'url' ),
		'facebook'        => array( __( 'Facebook URL', 'abp' ), 'url' ),
		'linkedin'        => array( __( 'LinkedIn URL', 'abp' ), 'url' ),
		'youtube'         => array( __( 'YouTube URL', 'abp' ), 'url' ),
	);
	$defaults = abp_option_defaults();

	foreach ( $fields as $key => $field ) {
		$wp_customize->add_setting(
			'abp_' . $key,
			array(
				'default'           => $defaults[ $key ],
				'sanitize_callback' => 'textarea' === $field[1] ? 'sanitize_textarea_field' : ( 'url' === $field[1] ? 'esc_url_raw' : 'sanitize_text_field' ),
			)
		);
		$wp_customize->add_control(
			'abp_' . $key,
			array(
				'label'   => $field[0],
				'section' => 'abp_contact',
				'type'    => 'url' === $field[1] ? 'url' : $field[1],
			)
		);
	}
} );
