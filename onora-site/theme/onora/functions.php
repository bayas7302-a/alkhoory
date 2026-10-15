<?php
/**
 * ONORA Hotel Supplies theme functions.
 */

defined( 'ABSPATH' ) || exit;

define( 'ONORA_VERSION', '1.3.1' );

/** Business details shown in the header and footer. */
function onora_contact( $key ) {
	$data = array(
		'phone'      => '+971 50 203 8005',
		'phone_href' => 'tel:+971502038005',
		'email'      => 'jason@onorahs.com',
		'website'    => 'onorahs.com',
		'city'       => 'Karama, Dubai — UAE',
		'address'    => 'Office M-A98, Hamra Building, Karama, Dubai — UAE',
		'tagline'    => 'Premium supplies for hotels, restaurants & cafés',
		'instagram'  => 'https://www.instagram.com/',
		'linkedin'   => 'https://www.linkedin.com/',
		'facebook'   => 'https://www.facebook.com/',
	);
	return isset( $data[ $key ] ) ? $data[ $key ] : '';
}

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	register_nav_menus( array(
		'primary'     => __( 'Main menu', 'onora' ),
		'collections' => __( 'Footer collections', 'onora' ),
	) );
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'onora-fonts', 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600&family=Montserrat:wght@400;500;600&display=swap', array(), null );
	wp_enqueue_style( 'onora-theme', get_template_directory_uri() . '/assets/css/theme.css', array(), ONORA_VERSION );
	wp_enqueue_script( 'onora-theme', get_template_directory_uri() . '/assets/js/theme.js', array(), ONORA_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );
} );

/** Preconnect to Google Fonts. */
add_filter( 'wp_resource_hints', function ( $urls, $relation ) {
	if ( 'preconnect' === $relation ) {
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}
	return $urls;
}, 10, 2 );

/** Default menu items used until menus are assigned in Appearance → Menus. */
function onora_default_menu( $location ) {
	$base = home_url( '/' );
	if ( 'collections' === $location ) {
		$items = array( 'Chinaware' => 'chinaware', 'Cutlery' => 'cutlery', 'Glassware' => 'glassware', 'Buffet' => 'buffet', 'Bar Items' => 'bar-items', 'Table Lamps' => 'table-lamps' );
		return array_map( function ( $slug ) use ( $base ) { return $base . 'products/#' . $slug; }, $items );
	}
	return array(
		'Home'       => $base,
		'About Us'   => $base . 'about-us/',
		'Brands'     => $base . 'brands/',
		'Products'   => $base . 'products/',
		'Contact Us' => $base . 'contact-us/',
	);
}

/** Prints a menu location as a plain list of links (assigned WP menu, or the defaults). */
function onora_menu( $location, $class ) {
	if ( has_nav_menu( $location ) ) {
		wp_nav_menu( array(
			'theme_location' => $location,
			'container'      => false,
			'menu_class'     => $class,
			'depth'          => 1,
		) );
		return;
	}
	$current = is_front_page() ? home_url( '/' ) : ( is_singular() ? get_permalink() : '' );
	$current = $current ? trailingslashit( $current ) : '';
	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( onora_default_menu( $location ) as $label => $url ) {
		$active = ( 'primary' === $location && trailingslashit( $url ) === $current ) ? ' current-menu-item' : '';
		printf( '<li class="menu-item%s"><a href="%s"%s>%s</a></li>', esc_attr( $active ), esc_url( $url ), $active ? ' aria-current="page"' : '', esc_html( $label ) );
	}
	echo '</ul>';
}

/** Logo image from the theme folder (or the Customizer logo if one is set). */
function onora_logo( $variant = 'navy' ) {
	if ( 'navy' === $variant && has_custom_logo() ) {
		the_custom_logo();
		return;
	}
	printf(
		'<a class="onora-logo onora-logo--%1$s" href="%2$s" rel="home"><img src="%3$s" alt="%4$s" width="%5$d" height="%6$d"></a>',
		esc_attr( $variant ),
		esc_url( home_url( '/' ) ),
		esc_url( get_template_directory_uri() . '/assets/img/onora-logo-' . $variant . '.png' ),
		esc_attr( get_bloginfo( 'name' ) ?: 'ONORA Hotel Supplies' ),
		'navy' === $variant ? 172 : 238,
		'navy' === $variant ? 72 : 98
	);
}

/* -------------------------------------------------------------------------
 * Contact Form 7: the "ONORA quotation" form on the Contact page.
 * The form markup and email body live in /cf7/*.txt and are synced into CF7
 * once per theme version (CF7's REST API cannot save forms).
 * ---------------------------------------------------------------------- */

define( 'ONORA_QUOTE_FORM', 'ONORA quotation' );
define( 'ONORA_NEWSLETTER_FORM', 'ONORA newsletter' );

add_filter( 'wpcf7_autop_or_not', '__return_false' );

/** Saves one CF7 form from the theme's /cf7 files (creates it, or updates the form with the same title). */
function onora_save_cf7_form( $title, $slug, $subject, $reply_to, $messages, $fallback_title = '' ) {
	$find = function ( $t ) {
		$ids = get_posts( array( 'post_type' => 'wpcf7_contact_form', 'title' => $t, 'posts_per_page' => 1, 'fields' => 'ids', 'post_status' => 'any' ) );
		return $ids ? (int) $ids[0] : 0;
	};
	$id  = $find( $title ) ?: ( $fallback_title ? $find( $fallback_title ) : 0 ) ?: -1;
	$dir = get_template_directory() . '/cf7/';
	wpcf7_save_contact_form( array(
		'id'       => $id,
		'title'    => $title,
		'locale'   => 'en_US',
		'form'     => file_get_contents( $dir . $slug . '-form.txt' ),
		'mail'     => array(
			'active'             => true,
			'recipient'          => onora_contact( 'email' ),
			'sender'             => 'ONORA Website <wordpress@' . wp_parse_url( home_url(), PHP_URL_HOST ) . '>',
			'subject'            => $subject,
			'additional_headers' => 'Reply-To: ' . $reply_to,
			'body'               => file_get_contents( $dir . $slug . '-mail.txt' ),
			'attachments'        => '',
			'use_html'           => false,
			'exclude_blank'      => true,
		),
		'mail_2'   => array( 'active' => false ),
		'messages' => $messages,
	) );
}

add_action( 'init', function () {
	if ( ! function_exists( 'wpcf7_save_contact_form' ) || get_option( 'onora_cf7_version' ) === ONORA_VERSION ) {
		return;
	}
	$common = array(
		'validation_error' => 'Please check the highlighted fields and try again.',
		'invalid_required' => 'Please fill in this field.',
		'invalid_email'    => 'Please enter a valid email address.',
		'invalid_tel'      => 'Please enter a valid phone number.',
	);
	// The quotation form reuses CF7's default "Contact form 1" the first time.
	onora_save_cf7_form( ONORA_QUOTE_FORM, 'quotation', 'Quotation request: [your-company] ([your-name])', '[your-name] <[your-email]>', array(
		'mail_sent_ok' => 'Thank you — your enquiry has been sent. We will reply within one working day.',
		'mail_sent_ng' => 'Sorry, your enquiry could not be sent. Please email ' . onora_contact( 'email' ) . ' or call ' . onora_contact( 'phone' ) . '.',
	) + $common, 'Contact form 1' );
	onora_save_cf7_form( ONORA_NEWSLETTER_FORM, 'newsletter', 'Newsletter signup: [newsletter-email]', '[newsletter-email]', array(
		'mail_sent_ok'     => 'Thank you — you are on the list.',
		'mail_sent_ng'     => 'Sorry, that did not work. Please try again or email ' . onora_contact( 'email' ) . '.',
		'invalid_required' => 'Please enter your email address.',
		'validation_error' => 'Please enter a valid email address.',
	) + $common );
	update_option( 'onora_cf7_version', ONORA_VERSION, false );
}, 20 );

/** Footer newsletter: the CF7 form, or a plain mailto form if CF7 is not active. */
function onora_newsletter_form() {
	if ( shortcode_exists( 'contact-form-7' ) ) {
		echo do_shortcode( '[contact-form-7 title="' . ONORA_NEWSLETTER_FORM . '" html_class="onora-newsletter-cf7"]' );
		return;
	}
	printf( '<form class="onora-newsletter" action="mailto:%1$s?subject=Newsletter%%20signup" method="post" enctype="text/plain"><input type="email" name="email" placeholder="Your email address" required aria-label="Your email address"><button type="submit">Subscribe</button></form>', esc_attr( onora_contact( 'email' ) ) );
}

/** Puts the quotation form inside the Elementor element whose ID is "onora-quote-form" (Contact page). */
add_filter( 'elementor/frontend/the_content', function ( $content ) {
	if ( ! shortcode_exists( 'contact-form-7' ) || false === strpos( $content, 'id="onora-quote-form"' ) ) {
		return $content;
	}
	$form = do_shortcode( '[contact-form-7 title="' . ONORA_QUOTE_FORM . '" html_class="onora-quote"]' );
	return preg_replace( '/(<[^>]+\sid="onora-quote-form"[^>]*>)/', '$1' . str_replace( '$', '\$', $form ), $content, 1 );
} );
