<?php
/**
 * Subaru Al Khoory — child theme of Hello Elementor.
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SUBARU_DEFAULT_RECIPIENT', 'info@subaru-uae.com' );
define( 'SUBARU_VERSION', '1.2.2' );
define( 'SUBARU_DIR', get_stylesheet_directory() );
define( 'SUBARU_URI', get_stylesheet_directory_uri() );

$subaru_includes = array(
	'inc/helpers.php',       // Generic helpers, settings accessors, icons.
	'inc/setup.php',         // Theme supports, menus, image sizes, activation.
	'inc/enqueue.php',       // Fonts, CSS, JS.
	'inc/nav.php',           // Menu walker, fallbacks, mega menu.
	'inc/customizer.php',    // Logo overrides, top bar text.
	'inc/settings.php',      // "Subaru Settings" admin page.
	'inc/enquiries.php',     // Enquiry CPT, admin columns, CSV export.
	'inc/forms.php',         // Form rendering + AJAX handler.
	'inc/shortcodes.php',    // [subaru_form] [subaru_posts] [subaru_showrooms] [subaru_anchor].
	'inc/template-tags.php', // Blog helpers (cards, reading time, share, pagination).
	'inc/seo.php',           // Meta description, JSON-LD.
	'inc/security.php',      // SVG uploads for administrators.
	'inc/elementor/loader.php', // Custom Elementor widgets.
);

foreach ( $subaru_includes as $subaru_file ) {
	require_once SUBARU_DIR . '/' . $subaru_file;
}
unset( $subaru_includes, $subaru_file );
