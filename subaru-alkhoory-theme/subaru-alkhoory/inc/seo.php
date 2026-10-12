<?php
/**
 * SEO helpers: meta description fallback, Open Graph basics, AutoDealer JSON-LD.
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Is a dedicated SEO plugin active?
 *
 * @return bool
 */
function subaru_has_seo_plugin() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || class_exists( 'The_SEO_Framework\Load' );
}

// Hello outputs its own description tag; ours replaces it (richer fallback chain).
add_filter( 'hello_elementor_description_meta_tag', '__return_false' );

/**
 * Description for the current view.
 *
 * @return string
 */
function subaru_meta_description() {
	$desc = '';
	if ( is_singular() ) {
		$post = get_queried_object();
		if ( $post instanceof WP_Post ) {
			$desc = has_excerpt( $post ) ? $post->post_excerpt : '';
			if ( ! $desc && 'post' === $post->post_type ) {
				$desc = wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 30, '…' );
			}
			if ( ! $desc && is_front_page() ) {
				$desc = get_bloginfo( 'description' );
			}
			if ( ! $desc ) {
				// Elementor pages: first meaningful paragraph of the rendered text.
				$desc = wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 28, '…' );
			}
		}
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$desc = term_description();
	} elseif ( is_home() ) {
		$id   = (int) get_option( 'page_for_posts' );
		$desc = $id && has_excerpt( $id ) ? get_the_excerpt( $id ) : __( 'The latest Subaru UAE news, model spotlights, driving tips, technology insights and adventure guides.', 'subaru-alkhoory' );
	}
	if ( ! $desc ) {
		$desc = get_bloginfo( 'description' );
	}
	$desc = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $desc ) ) );
	return apply_filters( 'subaru_meta_description', $desc );
}

/**
 * Print description + OG tags when no SEO plugin handles them.
 */
function subaru_head_meta() {
	if ( subaru_has_seo_plugin() ) {
		return;
	}
	$desc = subaru_meta_description();
	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	$title = wp_get_document_title();
	$url   = is_singular() ? get_permalink() : home_url( add_query_arg( array() ) );
	$image = '';
	if ( is_singular() && has_post_thumbnail() ) {
		$image = get_the_post_thumbnail_url( null, 'large' );
	}
	echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
	echo '<meta property="og:type" content="' . ( is_singular( 'post' ) ? 'article' : 'website' ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $desc ) {
		echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	if ( $image ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
	}
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
}
add_action( 'wp_head', 'subaru_head_meta', 2 );

/**
 * AutoDealer structured data.
 */
function subaru_jsonld() {
	$data = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'AutoDealer',
		'name'       => 'Al Khoory Automobiles – Subaru',
		'url'        => home_url( '/' ),
		'logo'       => subaru_logo_url( 'dark' ),
		'brand'      => array(
			'@type' => 'Brand',
			'name'  => 'Subaru',
		),
		'areaServed' => array(
			array(
				'@type' => 'City',
				'name'  => 'Dubai',
			),
			array(
				'@type' => 'AdministrativeArea',
				'name'  => 'Northern Emirates',
			),
		),
		'address'    => array(
			'@type'          => 'PostalAddress',
			'addressCountry' => 'AE',
		),
	);
	$phone = subaru_setting( 'phone' );
	if ( $phone ) {
		$data['telephone'] = $phone;
	}
	$email = subaru_setting( 'email' );
	if ( $email ) {
		$data['email'] = $email;
	}
	$same = array_values( array_filter( array( subaru_setting( 'social_instagram' ), subaru_setting( 'social_facebook' ), subaru_setting( 'social_linkedin' ), subaru_setting( 'social_youtube' ) ) ) );
	if ( $same ) {
		$data['sameAs'] = $same;
	}
	$data = apply_filters( 'subaru_jsonld', $data );
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'subaru_jsonld', 30 );
