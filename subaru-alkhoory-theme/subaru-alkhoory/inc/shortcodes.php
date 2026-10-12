<?php
/**
 * Shortcodes: [subaru_form], [subaru_posts], [subaru_showrooms], [subaru_anchor].
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * [subaru_form type="test-drive|contact|service|newsletter|brochure|offer" model=""]
 *
 * @param array $atts Attributes.
 * @return string
 */
function subaru_form_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'type'  => 'contact',
			'model' => '',
			'class' => '',
		),
		$atts,
		'subaru_form'
	);
	return subaru_render_form( $atts['type'], $atts );
}
add_shortcode( 'subaru_form', 'subaru_form_shortcode' );

/**
 * [subaru_posts count="3" category="" layout="grid|list|featured"]
 *
 * @param array $atts Attributes.
 * @return string
 */
function subaru_posts_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'count'    => 3,
			'category' => '',
			'layout'   => 'grid',
			'exclude'  => '',
			'offset'   => 0,
		),
		$atts,
		'subaru_posts'
	);

	$layout = in_array( $atts['layout'], array( 'grid', 'list', 'featured' ), true ) ? $atts['layout'] : 'grid';
	$args   = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => max( 1, min( 24, (int) $atts['count'] ) ),
		'offset'              => max( 0, (int) $atts['offset'] ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);
	if ( $atts['category'] ) {
		$cats = array_map( 'trim', explode( ',', $atts['category'] ) );
		if ( is_numeric( $cats[0] ) ) {
			$args['cat'] = implode( ',', array_map( 'intval', $cats ) );
		} else {
			$args['category_name'] = implode( ',', array_map( 'sanitize_title', $cats ) );
		}
	}
	if ( $atts['exclude'] ) {
		$args['post__not_in'] = array_map( 'intval', explode( ',', $atts['exclude'] ) );
	}

	$query = new WP_Query( $args );
	if ( ! $query->have_posts() ) {
		return '<p class="sa-empty">' . esc_html__( 'News coming soon.', 'subaru-alkhoory' ) . '</p>';
	}

	ob_start();
	echo '<div class="sa-posts sa-posts--' . esc_attr( $layout ) . '">';
	$i = 0;
	while ( $query->have_posts() ) {
		$query->the_post();
		$variant = 'grid' === $layout ? 'home' : $layout;
		if ( 'featured' === $layout && $i > 0 ) {
			$variant = 'list';
		}
		subaru_post_card( get_post(), $variant, $i );
		++$i;
	}
	echo '</div>';
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'subaru_posts', 'subaru_posts_shortcode' );

/**
 * [subaru_showrooms type=""] — showroom & service-centre cards.
 *
 * @param array $atts Attributes.
 * @return string
 */
function subaru_showrooms_shortcode( $atts ) {
	$atts  = shortcode_atts(
		array(
			'type' => '',
		),
		$atts,
		'subaru_showrooms'
	);
	$items = subaru_showrooms();
	if ( $atts['type'] ) {
		$want  = strtolower( $atts['type'] );
		$items = array_filter(
			$items,
			function ( $s ) use ( $want ) {
				return false !== strpos( strtolower( $s['type'] . ' ' . $s['name'] ), $want );
			}
		);
	}
	if ( ! $items ) {
		return '';
	}
	ob_start();
	echo '<div class="sa-showrooms">';
	$i = 0;
	foreach ( $items as $s ) {
		$query = $s['map'] ? $s['map'] : ( $s['address'] ? $s['address'] : 'Al Khoory Automobiles Subaru Dubai' );
		$maps  = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $query );
		echo '<article class="sa-showroom" data-sa-reveal style="--d:' . (int) ( $i * 100 ) . 'ms">';
		echo '<div class="sa-showroom__head"><span class="sa-showroom__icon">';
		subaru_the_icon( false !== stripos( $s['type'], 'service' ) ? 'tools' : 'pin', 22 );
		echo '</span>';
		if ( $s['type'] ) {
			echo '<span class="sa-badge">' . esc_html( $s['type'] ) . '</span>';
		}
		echo '</div><h3 class="sa-showroom__name">' . esc_html( $s['name'] ) . '</h3>';
		if ( $s['area'] ) {
			echo '<p class="sa-showroom__area">' . esc_html( $s['area'] ) . '</p>';
		}
		echo '<ul class="sa-showroom__meta">';
		if ( $s['address'] ) {
			echo '<li>';
			subaru_the_icon( 'pin', 16 );
			echo '<span>' . nl2br( esc_html( $s['address'] ) ) . '</span></li>';
		}
		if ( $s['phone'] ) {
			echo '<li>';
			subaru_the_icon( 'phone', 16 );
			echo '<a href="' . esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $s['phone'] ) ) . '">' . esc_html( $s['phone'] ) . '</a></li>';
		}
		if ( $s['email'] ) {
			echo '<li>';
			subaru_the_icon( 'mail', 16 );
			echo '<a href="' . esc_url( 'mailto:' . $s['email'] ) . '">' . esc_html( $s['email'] ) . '</a></li>';
		}
		if ( $s['hours'] ) {
			echo '<li>';
			subaru_the_icon( 'clock', 16 );
			echo '<span>' . nl2br( esc_html( $s['hours'] ) ) . '</span></li>';
		}
		echo '</ul><a class="sa-link" href="' . esc_url( $maps ) . '" target="_blank" rel="noopener">' . esc_html__( 'Get directions', 'subaru-alkhoory' ) . ' ';
		subaru_the_icon( 'arrow', 16 );
		echo '<span class="screen-reader-text"> ' . esc_html__( '(opens Google Maps)', 'subaru-alkhoory' ) . '</span></a>';
		echo '</article>';
		++$i;
	}
	echo '</div>';
	return ob_get_clean();
}
add_shortcode( 'subaru_showrooms', 'subaru_showrooms_shortcode' );

/**
 * [subaru_anchor id="x"] — empty scroll target.
 *
 * @param array $atts Attributes.
 * @return string
 */
function subaru_anchor_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'id' => '' ), $atts, 'subaru_anchor' );
	$id   = sanitize_html_class( $atts['id'] );
	if ( ! $id ) {
		return '';
	}
	return '<span id="' . esc_attr( $id ) . '" class="sa-anchor sub-anchor" aria-hidden="true"></span>';
}
add_shortcode( 'subaru_anchor', 'subaru_anchor_shortcode' );
