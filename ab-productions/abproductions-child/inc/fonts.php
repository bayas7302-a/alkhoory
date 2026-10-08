<?php
/**
 * Brand fonts.
 *
 * Primary display font: Dharma Gothic E (self-hosted .otf).
 * Body font: Poppins, loaded by Elementor from Google Fonts.
 *
 * The .otf is looked up in this order:
 *   1. assets/fonts/DharmaGothicE.otf inside this theme (drop the file there), then
 *   2. the copy uploaded to the WordPress folder: /Dharma Gothic E.otf
 * If neither file exists, the browser uses the bundled
 * Big Shoulders Display (SIL OFL), the condensed face used in the Figma file,
 * so headings never drop to a serif.
 *
 * @package abp
 */

defined( 'ABSPATH' ) || exit;

/**
 * Font files for "Dharma Gothic E", best first. Only files that exist are
 * listed, so the browser never waits on a 404 before falling back.
 */
function abp_display_font_files() {
	$files = array();
	if ( file_exists( ABP_DIR . '/assets/fonts/DharmaGothicE.otf' ) ) {
		$files[] = array( ABP_URI . '/assets/fonts/DharmaGothicE.otf', 'opentype', 'font/otf' );
	}
	if ( file_exists( ABSPATH . 'Dharma Gothic E.otf' ) ) {
		$files[] = array( home_url( '/Dharma%20Gothic%20E.otf' ), 'opentype', 'font/otf' );
	}
	$files[] = array( ABP_URI . '/assets/fonts/BigShouldersDisplay-latin.woff2', 'woff2', 'font/woff2' );
	return $files;
}

function abp_font_face_css() {
	$sources = array();
	foreach ( abp_display_font_files() as $f ) {
		$sources[] = 'url("' . esc_url( $f[0] ) . '") format("' . $f[1] . '")';
	}
	// "block" hides headings for the split second the font loads instead of
	// flashing them in a fallback font (the glitch on first load).
	return '@font-face{font-family:"Dharma Gothic E";src:' . implode( ',', $sources ) . ';font-weight:100 900;font-style:normal;font-display:block;}';
}

/** Preload the display font and warm up the Google Fonts connection for Poppins. */
add_action( 'wp_head', function () {
	$files = abp_display_font_files();
	printf( '<link rel="preload" href="%s" as="font" type="%s" crossorigin>' . "\n", esc_url( $files[0][0] ), esc_attr( $files[0][2] ) );
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}, 1 );

/** Elementor's kit defaults pull in every weight of Roboto and Roboto Slab, which the site never uses. */
add_action( 'wp_enqueue_scripts', function () {
	foreach ( array( 'elementor-gf-roboto', 'elementor-gf-robotoslab' ) as $handle ) {
		wp_dequeue_style( $handle );
	}
}, 100 );
add_action( 'wp_print_styles', function () {
	foreach ( array( 'elementor-gf-roboto', 'elementor-gf-robotoslab' ) as $handle ) {
		wp_dequeue_style( $handle );
	}
}, 100 );

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
