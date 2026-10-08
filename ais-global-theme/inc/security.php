<?php
/**
 * Small hardening tweaks that are safe for any site.
 *
 * @package AIS_Global
 */

defined( 'ABSPATH' ) || exit;

// Do not advertise the WordPress version.
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

// Disable XML-RPC (commonly abused for brute force / pingback attacks).
add_filter( 'xmlrpc_enabled', '__return_false' );
remove_action( 'wp_head', 'rsd_link' );
add_filter(
	'wp_headers',
	function ( $headers ) {
		unset( $headers['X-Pingback'] );
		$headers['X-Content-Type-Options'] = 'nosniff';
		$headers['Referrer-Policy']        = 'strict-origin-when-cross-origin';
		$headers['X-Frame-Options']        = 'SAMEORIGIN';
		return $headers;
	}
);

// Hide the user list from anonymous REST requests (prevents username enumeration).
add_filter(
	'rest_endpoints',
	function ( $endpoints ) {
		if ( ! is_user_logged_in() ) {
			unset( $endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
		}
		return $endpoints;
	}
);

// Block ?author=N enumeration.
add_action(
	'template_redirect',
	function () {
		if ( is_author() && ! is_user_logged_in() ) {
			wp_safe_redirect( home_url( '/' ), 301 );
			exit;
		}
	}
);

// Generic login error message.
add_filter(
	'login_errors',
	function () {
		return __( 'Incorrect login details.', 'ais-global' );
	}
);
