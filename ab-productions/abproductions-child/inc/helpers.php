<?php
/**
 * Small helpers shared by templates and shortcodes.
 *
 * @package abp
 */

defined( 'ABSPATH' ) || exit;

/**
 * Theme option with default (values come from the Customizer, see customizer.php).
 */
function abp_opt( $key ) {
	$defaults = abp_option_defaults();
	return get_theme_mod( 'abp_' . $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

function abp_option_defaults() {
	return array(
		'phone_primary'   => '+971 55 591 6684',
		'phone_secondary' => '+971 6 805 2973',
		'email'           => 'Abercio@ABProductionsUAE.Com',
		'topbar_text'     => 'Dubai HQ — The Onyx Towers, The Greens   ·   Workshop — Al Sajaa, Sharjah',
		'hq_title'        => 'The Onyx Towers',
		'hq_address'      => "Office 313, P3 Floor, Tower 1, The Greens,\nP.O. Box 391186, Dubai, UAE",
		'hq_map'          => 'The Onyx Towers, The Greens, Dubai',
		'branch_title'    => 'Al Sajaa Facility',
		'branch_address'  => "Warehouse Shed 8, Plot No. 550,\nAl Sajaa, Sharjah, UAE",
		'branch_map'      => 'Al Sajaa Industrial Area, Sharjah',
		'footer_about'    => 'A multidisciplinary production and solutions company — interiors, events, rentals, printing, furniture, cleaning and landscape under one roof.',
		'instagram'       => '#',
		'facebook'        => '#',
		'linkedin'        => '#',
		'youtube'         => '#',
		'quote_url'       => '/contact/',
	);
}

/** tel: link from a display number. */
function abp_tel( $number ) {
	return 'tel:' . preg_replace( '/[^0-9+]/', '', $number );
}

/** Resolve a site-relative URL ("/contact/") against home_url. */
function abp_url( $url ) {
	if ( '' === $url || '#' === $url || preg_match( '#^(https?:|mailto:|tel:)#', $url ) ) {
		return $url;
	}
	return home_url( $url );
}

function abp_maps_link( $query ) {
	return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $query );
}

/** Inline SVG icons (stroke icons use currentColor). */
function abp_icon( $name ) {
	$icons = array(
		'instagram' => '<svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true"><path fill="currentColor" d="M12 7a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm0 8.2A3.2 3.2 0 1 1 12 8.8a3.2 3.2 0 0 1 0 6.4zM17.3 5.5a1.2 1.2 0 1 0 0 2.4 1.2 1.2 0 0 0 0-2.4zM21.9 7.1c-.1-1.6-.4-3-1.6-4.2S17.6 1.3 16 1.2C14.3 1.1 9.7 1.1 8 1.2c-1.6.1-3 .4-4.2 1.6S2.2 5.5 2.1 7.1C2 8.8 2 13.4 2.1 15.1c.1 1.6.4 3 1.6 4.2s2.6 1.5 4.2 1.6c1.7.1 6.3.1 8 0 1.6-.1 3-.4 4.2-1.6s1.5-2.6 1.6-4.2c.1-1.7.1-6.3 0-8zM19.7 17.5a3.3 3.3 0 0 1-1.8 1.8c-1.3.5-4.3.4-5.9.4s-4.6.1-5.9-.4a3.3 3.3 0 0 1-1.8-1.8c-.5-1.3-.4-4.3-.4-5.9s-.1-4.6.4-5.9a3.3 3.3 0 0 1 1.8-1.8C7.4 3.4 10.4 3.5 12 3.5s4.6-.1 5.9.4a3.3 3.3 0 0 1 1.8 1.8c.5 1.3.4 4.3.4 5.9s.1 4.6-.4 5.9z"/></svg>',
		'facebook'  => '<svg viewBox="0 0 320 512" width="10" height="16" aria-hidden="true"><path fill="currentColor" d="M279 288l14-93h-89v-60c0-25 13-50 53-50h41V6S262 0 225 0c-73 0-121 44-121 125v70H23v93h81v224h100V288z"/></svg>',
		'linkedin'  => '<svg viewBox="0 0 448 512" width="14" height="16" aria-hidden="true"><path fill="currentColor" d="M100 448H7V149h93zM54 108C24 108 0 83 0 53a54 54 0 0 1 107 0c0 30-24 55-53 55zM447 448h-92V302c0-35-1-79-48-79-48 0-56 37-56 77v148h-92V149h89v41h1c12-24 43-48 88-48 94 0 111 62 111 142v164z"/></svg>',
		'youtube'   => '<svg viewBox="0 0 576 512" width="18" height="16" aria-hidden="true"><path fill="currentColor" d="M550 124c-6-24-25-42-49-49C458 64 288 64 288 64S118 64 75 75c-24 7-43 25-49 49-11 43-11 132-11 132s0 90 11 133c6 23 25 41 49 48 43 11 213 11 213 11s170 0 213-11c24-7 43-25 49-48 11-43 11-133 11-133s0-89-11-132zM232 338V175l142 81z"/></svg>',
		'phone'     => '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/></svg>',
		'mail'      => '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/></svg>',
		'pin'       => '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg>',
		'home'      => '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m3 10 9-7 9 7v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/></svg>',
		'close'     => '<svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>',
		'prev'      => '<svg viewBox="0 0 24 24" width="26" height="26" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>',
		'next'      => '<svg viewBox="0 0 24 24" width="26" height="26" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>',
		'arrow-up-right' => '<svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 17 17 7M8 7h9v9"/></svg>',
	);
	return isset( $icons[ $name ] ) ? $icons[ $name ] : '';
}

/** Social links as an icon list. */
function abp_socials( $class = 'abp-socials' ) {
	$out = '<ul class="' . esc_attr( $class ) . '">';
	foreach ( array( 'instagram', 'facebook', 'linkedin', 'youtube' ) as $net ) {
		$url = abp_opt( $net );
		if ( '' === $url ) {
			continue;
		}
		$out .= sprintf(
			'<li><a href="%s" aria-label="%s" target="_blank" rel="noopener">%s</a></li>',
			esc_url( $url ),
			esc_attr( ucfirst( $net ) ),
			abp_icon( $net )
		);
	}
	return $out . '</ul>';
}

/** nl2br for escaped multi-line options. */
function abp_lines( $text ) {
	return nl2br( esc_html( $text ) );
}
