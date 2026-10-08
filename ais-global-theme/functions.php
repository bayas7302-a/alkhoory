<?php
/**
 * AIS Global theme functions.
 *
 * @package AIS_Global
 */

defined( 'ABSPATH' ) || exit;

define( 'AIS_GLOBAL_VERSION', '1.0.0' );

require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/security.php';

/**
 * Theme setup.
 */
function ais_global_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 100,
			'width'       => 260,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu (header)', 'ais-global' ),
		)
	);
}
add_action( 'after_setup_theme', 'ais_global_setup' );

/**
 * Front-end assets.
 */
function ais_global_assets() {
	wp_enqueue_style( 'ais-global-fonts', 'https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300..800;1,9..40,400&display=swap', array(), null );
	wp_enqueue_style( 'ais-global', get_template_directory_uri() . '/assets/css/theme.css', array( 'ais-global-fonts' ), AIS_GLOBAL_VERSION );

	// "Cal Sans" headline font (self-hosted file already in the Media Library).
	$cal_sans = get_theme_mod( 'ais_cal_sans_url', content_url( 'uploads/2026/03/CalSans-Regular.ttf' ) );
	if ( $cal_sans ) {
		wp_add_inline_style(
			'ais-global',
			sprintf( "@font-face{font-family:'Cal Sans';font-style:normal;font-weight:400 700;font-display:swap;src:url('%s') format('truetype');}", esc_url( $cal_sans ) )
		);
	}

	wp_enqueue_script( 'ais-global', get_template_directory_uri() . '/assets/js/theme.js', array(), AIS_GLOBAL_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'ais_global_assets' );

/**
 * Let Elementor know the theme's content width.
 */
function ais_global_content_width() {
	$GLOBALS['content_width'] = 1240;
}
add_action( 'after_setup_theme', 'ais_global_content_width', 0 );

/**
 * Is the current singular view built with Elementor?
 */
function ais_global_is_elementor( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	return $post_id && 'builder' === get_post_meta( $post_id, '_elementor_edit_mode', true );
}

/**
 * Render an Elementor document (used for the footer) with a plain fallback.
 */
function ais_global_render_elementor_part( $post_id ) {
	$post_id = absint( $post_id );
	if ( ! $post_id || ! did_action( 'elementor/loaded' ) || ! ais_global_is_elementor( $post_id ) ) {
		return false;
	}
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor output.
	echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $post_id, true );
	return true;
}

/**
 * Contact Form 7 placeholder.
 *
 * Elementor's free V4 text widgets cannot hold a <form>, so pages contain the
 * marker [ais_cf7 id="13"] which is swapped for the real form here.
 */
function ais_global_cf7_shortcode( $atts ) {
	$atts = shortcode_atts( array( 'id' => '' ), $atts, 'ais_cf7' );
	if ( ! $atts['id'] || ! shortcode_exists( 'contact-form-7' ) ) {
		return '';
	}
	return '<div class="ais-cf7">' . do_shortcode( '[contact-form-7 id="' . esc_attr( $atts['id'] ) . '"]' ) . '</div>';
}
add_shortcode( 'ais_cf7', 'ais_global_cf7_shortcode' );

/**
 * Replace the CF7 marker inside rendered Elementor content.
 *
 * @param string $content Rendered content.
 */
function ais_global_replace_cf7_marker( $content ) {
	if ( false === strpos( $content, 'ais_cf7' ) ) {
		return $content;
	}
	return preg_replace_callback(
		'#<(p|span)([^>]*)>\s*\[ais_cf7 id=(?:&quot;|&\#8221;|&\#8243;|["\'])?(\d+)(?:&quot;|&\#8221;|&\#8243;|["\'])?\]\s*</\1>#',
		function ( $m ) {
			return '<div' . $m[2] . '>' . ais_global_cf7_shortcode( array( 'id' => $m[3] ) ) . '</div>';
		},
		$content
	);
}
add_filter( 'elementor/frontend/the_content', 'ais_global_replace_cf7_marker', 20 );
add_filter( 'the_content', 'ais_global_replace_cf7_marker', 20 );

/**
 * Body classes.
 *
 * @param array $classes Classes.
 */
function ais_global_body_class( $classes ) {
	if ( is_singular() && ais_global_is_elementor() ) {
		$classes[] = 'ais-elementor-page';
	}
	return $classes;
}
add_filter( 'body_class', 'ais_global_body_class' );

/**
 * Add a dropdown toggle button to menu items that have children.
 *
 * @param string   $output Item output.
 * @param WP_Post  $item   Menu item.
 * @param int      $depth  Depth.
 * @param stdClass $args   Args.
 */
function ais_global_submenu_toggle( $output, $item, $depth, $args ) {
	if ( isset( $args->theme_location ) && 'primary' === $args->theme_location && in_array( 'menu-item-has-children', (array) $item->classes, true ) ) {
		$output .= '<button class="ais-sub-toggle" aria-expanded="false" aria-label="' . esc_attr__( 'Show submenu', 'ais-global' ) . '"><svg width="12" height="12" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></button>';
	}
	return $output;
}
add_filter( 'walker_nav_menu_start_el', 'ais_global_submenu_toggle', 10, 4 );

/**
 * Fallback when no menu is assigned: list top-level pages.
 */
function ais_global_menu_fallback() {
	echo '<ul class="ais-menu">';
	wp_list_pages(
		array(
			'title_li' => '',
			'depth'    => 1,
		)
	);
	echo '</ul>';
}

/**
 * First-run setup when the theme is activated: assign the existing
 * "Main Menu", the AIS logo and the Elementor footer page, if found.
 * Nothing is overwritten if it is already set.
 */
function ais_global_first_run() {
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	if ( empty( $locations['primary'] ) ) {
		$menu = wp_get_nav_menu_object( 'main-menu' );
		if ( $menu ) {
			$locations['primary'] = $menu->term_id;
			set_theme_mod( 'nav_menu_locations', $locations );
		}
	}

	if ( ! get_theme_mod( 'custom_logo' ) ) {
		$logo = get_posts(
			array(
				'post_type'      => 'attachment',
				'name'           => 'ais-logo',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		if ( $logo ) {
			set_theme_mod( 'custom_logo', $logo[0] );
		}
	}

	if ( ! get_theme_mod( 'ais_footer_page' ) ) {
		$footer = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => array( 'publish', 'draft', 'private' ),
				'title'          => 'Site Footer (used by AIS Global theme)',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		if ( $footer ) {
			set_theme_mod( 'ais_footer_page', $footer[0] );
		}
	}
}
add_action( 'after_switch_theme', 'ais_global_first_run' );
