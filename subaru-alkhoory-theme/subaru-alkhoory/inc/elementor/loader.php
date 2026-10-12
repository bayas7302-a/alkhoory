<?php
/**
 * Custom Elementor (classic) widgets: category "Subaru", asset registration, widget registration.
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the widget assets (enqueued by Elementor only when a widget declares them).
 */
function subaru_register_widget_assets() {
	wp_register_style( 'subaru-widgets', SUBARU_URI . '/assets/css/widgets.css', array(), subaru_asset_ver( 'assets/css/widgets.css' ) );
	wp_register_script( 'subaru-widgets', SUBARU_URI . '/assets/js/widgets.js', array( 'subaru-theme' ), subaru_asset_ver( 'assets/js/widgets.js' ), true );
}
add_action( 'wp_enqueue_scripts', 'subaru_register_widget_assets', 5 );
add_action( 'elementor/frontend/after_register_scripts', 'subaru_register_widget_assets' );
add_action( 'elementor/frontend/after_register_styles', 'subaru_register_widget_assets' );

/**
 * Editor preview: always load widget assets so panels render immediately.
 */
function subaru_widget_preview_assets() {
	subaru_register_widget_assets();
	wp_enqueue_style( 'subaru-widgets' );
	wp_enqueue_script( 'subaru-widgets' );
}
add_action( 'elementor/preview/enqueue_styles', 'subaru_widget_preview_assets' );

/**
 * Widget category.
 *
 * @param \Elementor\Elements_Manager $manager Manager.
 */
function subaru_elementor_category( $manager ) {
	$manager->add_category(
		'subaru',
		array(
			'title' => __( 'Subaru', 'subaru-alkhoory' ),
			'icon'  => 'eicon-car',
		)
	);
}
add_action( 'elementor/elements/categories_registered', 'subaru_elementor_category' );

/**
 * Register widgets.
 *
 * @param \Elementor\Widgets_Manager $widgets_manager Manager.
 */
function subaru_register_widgets( $widgets_manager ) {
	require_once __DIR__ . '/class-subaru-widget-base.php';
	$widgets = array(
		'class-colour-configurator.php' => 'Subaru_Colour_Configurator_Widget',
		'class-hero-carousel.php'       => 'Subaru_Hero_Carousel_Widget',
		'class-spec-strip.php'          => 'Subaru_Spec_Strip_Widget',
		'class-model-card.php'          => 'Subaru_Model_Card_Widget',
		'class-feature-hotspots.php'    => 'Subaru_Feature_Hotspots_Widget',
	);
	foreach ( $widgets as $file => $class ) {
		require_once __DIR__ . '/widgets/' . $file;
		$widgets_manager->register( new $class() );
	}
}
add_action( 'elementor/widgets/register', 'subaru_register_widgets' );
