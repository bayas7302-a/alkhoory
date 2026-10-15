<?php
/**
 * Small inline SVG icons used by the header and footer (stroke icons, inherit currentColor).
 */

defined( 'ABSPATH' ) || exit;

function yutong_uae_icon( $name, $size = 16 ) {
	$paths = array(
		'search' => '<circle cx="11" cy="11" r="6.5"/><path d="m16 16 4 4"/>',
		'arrow'  => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'pin'    => '<path d="M12 21s-6.5-5.6-6.5-11a6.5 6.5 0 0 1 13 0c0 5.4-6.5 11-6.5 11z"/><circle cx="12" cy="10" r="2.4"/>',
		'clock'  => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
		'mail'   => '<rect x="3.5" y="5.5" width="17" height="13" rx="2"/><path d="m4 7 8 6 8-6"/>',
		'phone'  => '<path d="M6.6 3.5h3l1.5 4-2 1.3a11 11 0 0 0 6.1 6.1l1.3-2 4 1.5v3a2 2 0 0 1-2.2 2A17 17 0 0 1 4.6 5.7a2 2 0 0 1 2-2.2z"/>',
		'menu'   => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'  => '<path d="M6 6l12 12M18 6 6 18"/>',
	);
	if ( empty( $paths[ $name ] ) ) {
		return '';
	}
	return sprintf(
		'<svg class="yt-icon yt-icon--%1$s" width="%2$d" height="%2$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%3$s</svg>',
		esc_attr( $name ),
		(int) $size,
		$paths[ $name ]
	);
}
