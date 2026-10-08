<?php
/**
 * Elementor integration.
 *
 * Elementor's V4 (atomic) widgets have no Shortcode widget, so dynamic blocks
 * (projects, services tabs, client logos, map, contact form) are placed as a
 * Paragraph whose whole text is the shortcode, e.g. "[ab_projects]".
 * This filter replaces that paragraph element with the shortcode output.
 *
 * @package abp
 */

defined( 'ABSPATH' ) || exit;

function abp_allowed_inline_shortcodes() {
	return array( 'ab_projects', 'ab_project_filters', 'ab_services_tabs', 'ab_clients_marquee', 'ab_clients_grid', 'ab_find_us', 'ab_contact_card', 'ab_anchor', 'contact-form-7' );
}

function abp_expand_paragraph_shortcodes( $html ) {
	if ( false === strpos( $html, '[' ) ) {
		return $html;
	}
	$names = implode( '|', array_map( 'preg_quote', abp_allowed_inline_shortcodes() ) );
	$re    = '#<(p|span|h[1-6])\b([^>]*)>\s*(\[(?:' . $names . ')\b[^\]]*\])\s*</\1>#';

	return preg_replace_callback(
		$re,
		function ( $m ) {
			// Elementor stores text HTML-escaped; shortcode attributes need plain quotes.
			$code = html_entity_decode( $m[3], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
			$code = str_replace( array( '“', '”', '″', '‘', '’' ), array( '"', '"', '"', "'", "'" ), $code );
			// Keep the element's own style class (margins, width) but not Elementor's
			// paragraph/heading base classes, which reset every link inside them.
			$attrs = '';
			$class = 'abp-sc';
			if ( preg_match( '#\bclass="([^"]*)"#', $m[2], $c ) ) {
				foreach ( preg_split( '/\s+/', trim( $c[1] ) ) as $name ) {
					if ( '' !== $name && ! preg_match( '/^e-(paragraph|heading)-base$|^e-default-/', $name ) ) {
						$class .= ' ' . $name;
					}
				}
			}
			if ( preg_match( '#data-interaction-id="([^"]+)"#', $m[2], $id ) ) {
				$attrs = ' data-interaction-id="' . esc_attr( $id[1] ) . '"';
			}
			return '<div class="' . esc_attr( $class ) . '"' . $attrs . '>' . do_shortcode( $code ) . '</div>';
		},
		$html
	);
}

add_filter( 'elementor/frontend/the_content', 'abp_expand_paragraph_shortcodes', 20 );
