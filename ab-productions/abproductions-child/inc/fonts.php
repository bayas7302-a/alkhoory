<?php
/**
 * Brand fonts.
 *
 * Primary display font: Dharma Gothic E (self-hosted .otf).
 * Body font: Poppins, loaded by Elementor from Google Fonts.
 *
 * The .otf is looked up in this order:
 *   1. assets/fonts/DharmaGothicE.otf inside this theme (drop the file there), then
 *   2. the copy uploaded to the site root: /Dharma Gothic E.otf
 * If neither file can be loaded, the browser falls through to the bundled
 * Big Shoulders Display (SIL OFL), the condensed face used in the Figma file,
 * so headings never drop to a serif.
 *
 * @package abp
 */

defined( 'ABSPATH' ) || exit;

function abp_font_face_css() {
	$sources = array();
	if ( file_exists( ABP_DIR . '/assets/fonts/DharmaGothicE.otf' ) ) {
		$sources[] = 'url("' . esc_url( ABP_URI . '/assets/fonts/DharmaGothicE.otf' ) . '") format("opentype")';
	}
	$sources[] = 'url("' . esc_url( home_url( '/Dharma%20Gothic%20E.otf' ) ) . '") format("opentype")';
	$sources[] = 'url("' . esc_url( ABP_URI . '/assets/fonts/BigShouldersDisplay-latin.woff2' ) . '") format("woff2")';

	return '@font-face{font-family:"Dharma Gothic E";src:' . implode( ',', $sources ) . ';font-weight:100 900;font-style:normal;font-display:swap;}';
}

/**
 * Tell Elementor that "Dharma Gothic E" is a local font, so the font variable
 * does not trigger a Google Fonts request for it.
 */
add_filter( 'elementor/fonts/additional_fonts', function ( $fonts ) {
	$fonts['Dharma Gothic E'] = 'local';
	return $fonts;
} );

/** Make the @font-face available inside the Elementor editor preview too. */
add_action( 'elementor/preview/enqueue_styles', function () {
	wp_register_style( 'abp-fonts', false, array(), ABP_VERSION );
	wp_enqueue_style( 'abp-fonts' );
	wp_add_inline_style( 'abp-fonts', abp_font_face_css() );
} );
