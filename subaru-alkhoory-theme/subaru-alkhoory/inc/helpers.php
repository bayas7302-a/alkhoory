<?php
/**
 * Generic helpers: settings accessors, model data, inline SVG icons.
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Option name used for all "Subaru Settings".
 */
define( 'SUBARU_OPTION', 'subaru_settings' );

/**
 * Default values for Subaru Settings.
 *
 * @return array
 */
function subaru_setting_defaults() {
	return array(
		'recipient_test-drive' => SUBARU_DEFAULT_RECIPIENT,
		'recipient_contact'    => SUBARU_DEFAULT_RECIPIENT,
		'recipient_service'    => SUBARU_DEFAULT_RECIPIENT,
		'recipient_newsletter' => SUBARU_DEFAULT_RECIPIENT,
		'recipient_brochure'   => SUBARU_DEFAULT_RECIPIENT,
		'recipient_offer'      => SUBARU_DEFAULT_RECIPIENT,
		'autoreply'            => 1,
		'from_name'            => 'Subaru UAE – Al Khoory Automobiles',
		'whatsapp_number'      => '',
		'whatsapp_message'     => 'Hello Subaru UAE, I would like more information.',
		'cookie_notice'        => 1,
		'cookie_text'          => 'We use cookies to improve your experience on our website. By continuing to browse, you agree to our use of cookies.',
		'social_instagram'     => '',
		'social_facebook'      => '',
		'social_linkedin'      => '',
		'social_youtube'       => '',
		'phone'                => '',
		'email'                => '',
		'showrooms'            => '',
		'page_intro'           => 0,
	);
}

/**
 * Get a Subaru setting.
 *
 * @param string $key     Setting key.
 * @param mixed  $default Optional default override.
 * @return mixed
 */
function subaru_setting( $key, $default = null ) {
	static $opts = null;
	if ( null === $opts ) {
		$opts = wp_parse_args( (array) get_option( SUBARU_OPTION, array() ), subaru_setting_defaults() );
	}
	if ( isset( $opts[ $key ] ) && '' !== $opts[ $key ] ) {
		return $opts[ $key ];
	}
	if ( null !== $default ) {
		return $default;
	}
	return isset( $opts[ $key ] ) ? $opts[ $key ] : '';
}

/**
 * Subaru model line-up used by forms, mega menu and fallbacks.
 *
 * @return array slug => array( name, tagline, path )
 */
function subaru_models() {
	$models = array(
		'forester'  => array(
			'name'    => 'Forester',
			'tagline' => __( 'Confidence for the Road Ahead.', 'subaru-alkhoory' ),
			'path'    => '/our-range/forester/',
			'type'    => 'SUV',
		),
		'crosstrek' => array(
			'name'    => 'Crosstrek',
			'tagline' => __( 'Go Further. Explore More.', 'subaru-alkhoory' ),
			'path'    => '/our-range/crosstrek/',
			'type'    => 'SUV',
		),
		'wrx'       => array(
			'name'    => 'WRX',
			'tagline' => __( 'Born from Rally. Built for Today.', 'subaru-alkhoory' ),
			'path'    => '/our-range/wrx/',
			'type'    => 'Sports',
		),
		'brz'       => array(
			'name'    => 'BRZ',
			'tagline' => __( 'Engineered for the Thrill.', 'subaru-alkhoory' ),
			'path'    => '/our-range/brz/',
			'type'    => 'Sports',
		),
	);
	return apply_filters( 'subaru_models', $models );
}

/**
 * Find the model slug for a URL (e.g. /our-range/wrx/ → wrx).
 *
 * @param string $url URL.
 * @return string|false
 */
function subaru_model_from_url( $url ) {
	$path = trailingslashit( (string) wp_parse_url( $url, PHP_URL_PATH ) );
	foreach ( subaru_models() as $slug => $m ) {
		if ( substr( $path, -strlen( $m['path'] ) ) === $m['path'] ) {
			return $slug;
		}
	}
	return false;
}

/**
 * Featured image URL of a model page, if set.
 *
 * @param string $slug Model slug.
 * @param string $size Image size.
 * @return string
 */
function subaru_model_thumb( $slug, $size = 'subaru-card' ) {
	$models = subaru_models();
	if ( empty( $models[ $slug ] ) ) {
		return '';
	}
	$page = get_page_by_path( trim( $models[ $slug ]['path'], '/' ) );
	if ( $page && has_post_thumbnail( $page ) ) {
		return (string) get_the_post_thumbnail_url( $page, $size );
	}
	return '';
}

/**
 * Showroom list (settings JSON or placeholder defaults).
 *
 * @return array
 */
function subaru_showrooms() {
	$raw  = subaru_setting( 'showrooms' );
	$list = array();
	if ( $raw ) {
		$decoded = json_decode( $raw, true );
		if ( is_array( $decoded ) ) {
			$list = $decoded;
		}
	}
	if ( ! $list ) {
		$list = array(
			array(
				'name'  => 'Al Khoory Automobiles Showroom — Dubai',
				'type'  => 'Showroom',
				'area'  => 'Dubai',
				'map'   => 'Al Khoory Automobiles Subaru Dubai',
			),
			array(
				'name'  => 'Al Khoory Automobiles Service Centre — Dubai',
				'type'  => 'Service Centre',
				'area'  => 'Dubai',
				'map'   => 'Al Khoory Automobiles Subaru Service Centre Dubai',
			),
			array(
				'name'  => 'Al Khoory Automobiles — Northern Emirates',
				'type'  => 'Showroom & Service',
				'area'  => 'Northern Emirates',
				'map'   => 'Al Khoory Automobiles Subaru Northern Emirates',
			),
		);
	}
	$clean = array();
	foreach ( $list as $item ) {
		if ( empty( $item['name'] ) ) {
			continue;
		}
		$clean[] = wp_parse_args(
			$item,
			array(
				'name'    => '',
				'type'    => '',
				'area'    => '',
				'address' => '',
				'phone'   => '',
				'email'   => '',
				'hours'   => '',
				'map'     => '',
			)
		);
	}
	return apply_filters( 'subaru_showrooms', $clean );
}

/**
 * Inline SVG icon markup. Icons are decorative (aria-hidden).
 *
 * @param string $name  Icon name.
 * @param int    $size  Pixel size.
 * @param string $class Extra class.
 * @return string
 */
function subaru_icon( $name, $size = 18, $class = '' ) {
	$s = 'fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"';
	$paths = array(
		'search'    => '<circle cx="11" cy="11" r="7" ' . $s . '/><path d="m20 20-3.6-3.6" ' . $s . '/>',
		'chevron'   => '<path d="m6 9 6 6 6-6" ' . $s . '/>',
		'chevron-r' => '<path d="m9 6 6 6-6 6" ' . $s . '/>',
		'chevron-l' => '<path d="m15 6-6 6 6 6" ' . $s . '/>',
		'arrow'     => '<path d="M5 12h14M13 6l6 6-6 6" ' . $s . '/>',
		'arrow-up'  => '<path d="M12 19V5M6 11l6-6 6 6" ' . $s . '/>',
		'book'      => '<path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5zM4 5.5v16" ' . $s . '/><path d="M8 7h8" ' . $s . '/>',
		'tools'     => '<path d="M14.7 6.3a4 4 0 0 0-5.4 5.2L3 17.8 6.2 21l6.3-6.3a4 4 0 0 0 5.2-5.4l-2.6 2.6-2.4-.6-.6-2.4z" ' . $s . '/>',
		'car'       => '<path d="M3 13l2-5.5A2 2 0 0 1 6.9 6h10.2a2 2 0 0 1 1.9 1.5L21 13v5h-2.5M3 13v5h2.5M3 13h18" ' . $s . '/><circle cx="7.5" cy="17.5" r="1.8" ' . $s . '/><circle cx="16.5" cy="17.5" r="1.8" ' . $s . '/>',
		'pin'       => '<path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21z" ' . $s . '/><circle cx="12" cy="9.5" r="2.5" ' . $s . '/>',
		'phone'     => '<path d="M5 3h4l2 5-2.5 1.5a11 11 0 0 0 6 6L16 13l5 2v4a2 2 0 0 1-2 2A17 17 0 0 1 3 5a2 2 0 0 1 2-2z" ' . $s . '/>',
		'mail'      => '<rect x="3" y="5" width="18" height="14" rx="2" ' . $s . '/><path d="m3 7 9 6 9-6" ' . $s . '/>',
		'clock'     => '<circle cx="12" cy="12" r="9" ' . $s . '/><path d="M12 7v5l3 2" ' . $s . '/>',
		'calendar'  => '<rect x="3" y="5" width="18" height="16" rx="2" ' . $s . '/><path d="M3 10h18M8 3v4M16 3v4" ' . $s . '/>',
		'user'      => '<circle cx="12" cy="8" r="4" ' . $s . '/><path d="M4 21a8 8 0 0 1 16 0" ' . $s . '/>',
		'close'     => '<path d="M6 6l12 12M18 6 6 18" ' . $s . '/>',
		'check'     => '<path d="m5 12.5 4.5 4.5L19 7.5" ' . $s . '/>',
		'link'      => '<path d="M10 14a5 5 0 0 0 7 0l3-3a5 5 0 0 0-7-7l-1 1M14 10a5 5 0 0 0-7 0l-3 3a5 5 0 0 0 7 7l1-1" ' . $s . '/>',
		'plus'      => '<path d="M12 5v14M5 12h14" ' . $s . '/>',
		'rotate'    => '<path d="M21 12a9 9 0 1 1-3-6.7L21 8M21 3v5h-5" ' . $s . '/>',
		'expand'    => '<path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5" ' . $s . '/>',
		'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5" ' . $s . '/><circle cx="12" cy="12" r="4" ' . $s . '/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/>',
		'facebook'  => '<path fill="currentColor" d="M13.5 21v-7.5h2.5l.4-3h-2.9V8.6c0-.9.3-1.5 1.5-1.5h1.6V4.4c-.3 0-1.2-.1-2.3-.1-2.3 0-3.8 1.4-3.8 3.9v2.3H8v3h2.5V21z"/>',
		'linkedin'  => '<path fill="currentColor" d="M4.5 9h3v11h-3zM6 3.8a1.8 1.8 0 1 1 0 3.6 1.8 1.8 0 0 1 0-3.6zM10 9h2.9v1.5h.1c.4-.8 1.4-1.7 3-1.7 3.1 0 3.7 2 3.7 4.7V20h-3v-5.6c0-1.3 0-3-1.9-3s-2.1 1.4-2.1 2.9V20H10z"/>',
		'youtube'   => '<path fill="currentColor" d="M21.6 7.2a2.5 2.5 0 0 0-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4A2.5 2.5 0 0 0 2.4 7.2C2 8.8 2 12 2 12s0 3.2.4 4.8a2.5 2.5 0 0 0 1.8 1.8C5.8 19 12 19 12 19s6.2 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.8c.4-1.6.4-4.8.4-4.8s0-3.2-.4-4.8zM10 15V9l5.2 3z"/>',
		'x'         => '<path fill="currentColor" d="M17.8 3h3.1l-6.8 7.8 8 10.2h-6.3l-4.9-6.4L5.3 21H2.2l7.3-8.3L1.8 3h6.4l4.4 5.9zm-1.1 16.2h1.7L7.4 4.7H5.6z"/>',
		'whatsapp'  => '<path fill="currentColor" d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm0 18.2a8.2 8.2 0 0 1-4.2-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8s-.4-.1-.6.1-.7.8-.8 1-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.3-.4.3-.4.7-1.4.1-.2 0-.3 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.2 5.2 0 0 0 1.1 2.7 11.8 11.8 0 0 0 4.5 4c1.7.7 2.3.8 3.2.6a2.7 2.7 0 0 0 1.8-1.3 2.2 2.2 0 0 0 .2-1.3c-.1-.1-.3-.2-.5-.3z"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return sprintf(
		'<svg class="sa-icon sa-icon--%1$s %2$s" width="%3$d" height="%3$d" viewBox="0 0 24 24" aria-hidden="true" focusable="false">%4$s</svg>',
		esc_attr( $name ),
		esc_attr( $class ),
		(int) $size,
		$paths[ $name ] // Static, theme-defined markup.
	);
}

/**
 * Allowed tags for echoing icon markup through wp_kses.
 *
 * @return array
 */
function subaru_svg_kses() {
	$common = array(
		'fill'            => true,
		'stroke'          => true,
		'stroke-width'    => true,
		'stroke-linecap'  => true,
		'stroke-linejoin' => true,
		'd'               => true,
	);
	return array(
		'svg'    => array(
			'class'       => true,
			'width'       => true,
			'height'      => true,
			'viewbox'     => true,
			'aria-hidden' => true,
			'focusable'   => true,
			'xmlns'       => true,
		),
		'path'   => $common,
		'circle' => $common + array(
			'cx' => true,
			'cy' => true,
			'r'  => true,
		),
		'rect'   => $common + array(
			'x'      => true,
			'y'      => true,
			'width'  => true,
			'height' => true,
			'rx'     => true,
		),
	);
}

/**
 * Echo an icon safely.
 *
 * @param string $name Icon.
 * @param int    $size Size.
 * @param string $class Class.
 */
function subaru_the_icon( $name, $size = 18, $class = '' ) {
	echo wp_kses( subaru_icon( $name, $size, $class ), subaru_svg_kses() );
}

/**
 * Logo URLs (Customizer → WP Site Logo → bundled theme asset).
 *
 * @param string $variant 'dark' (for light backgrounds) or 'light' (white logo for dark backgrounds).
 * @return string
 */
function subaru_logo_url( $variant = 'dark' ) {
	if ( 'light' === $variant ) {
		$url = get_theme_mod( 'subaru_logo_light' );
		return $url ? $url : SUBARU_URI . '/assets/img/subaru-logo-white.png';
	}
	$url = get_theme_mod( 'subaru_logo_dark' );
	if ( ! $url && has_custom_logo() ) {
		$url = wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' );
	}
	return $url ? $url : SUBARU_URI . '/assets/img/subaru-logo.png';
}

/**
 * Generic theme image (badge / group logos) with Customizer override.
 *
 * @param string $key     Theme mod key.
 * @param string $default File name in assets/img.
 * @return string
 */
function subaru_image_url( $key, $default ) {
	$url = get_theme_mod( $key );
	return $url ? $url : SUBARU_URI . '/assets/img/' . $default;
}

/**
 * Whether the current page is built with Elementor.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function subaru_is_elementor_page( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_queried_object_id();
	if ( ! $post_id ) {
		return false;
	}
	return 'builder' === get_post_meta( $post_id, '_elementor_edit_mode', true );
}
