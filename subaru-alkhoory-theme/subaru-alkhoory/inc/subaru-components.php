<?php
/**
 * Data-driven model components (shortcodes → template parts):
 *
 *   [subaru_configurator model="forester"]                 Exterior colours + 360° view / Interior trims.
 *   [subaru_gallery model="crosstrek" layout="feature"]    Model gallery (1 large + 2), lightbox with all photos.
 *   [subaru_gallery layout="masonry" filter="yes"]         Media Gallery page: every pack photo, filter by model.
 *
 * Assets (assets/css/models.css, assets/js/models.js) load only on pages that use these shortcodes.
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the component assets.
 */
function subaru_components_register_assets() {
	wp_register_style( 'subaru-models', SUBARU_URI . '/assets/css/models.css', array( 'subaru-theme' ), subaru_asset_ver( 'assets/css/models.css' ) );
	wp_register_script( 'subaru-models', SUBARU_URI . '/assets/js/models.js', array( 'subaru-theme' ), subaru_asset_ver( 'assets/js/models.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_localize_script(
		'subaru-models',
		'SubaruModels',
		array(
			'i18n' => array(
				'colour'      => __( 'Colour', 'subaru-alkhoory' ),
				'spinOn'      => __( '360° view', 'subaru-alkhoory' ),
				'spinOff'     => __( 'Exit 360° view', 'subaru-alkhoory' ),
				'spinLabel'   => __( '360° view of the Subaru %s. Drag or use the arrow keys to rotate.', 'subaru-alkhoory' ),
				'spinMissing' => __( '360° view is not available for this colour.', 'subaru-alkhoory' ),
				'frame'       => __( 'Frame %1$s of %2$s', 'subaru-alkhoory' ),
				'loading'     => __( 'Loading 360° view', 'subaru-alkhoory' ),
				'showing'     => __( 'Showing %s photos', 'subaru-alkhoory' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'subaru_components_register_assets', 15 );

/**
 * Enqueue the component assets early (in <head>) when the current page uses a component shortcode.
 */
function subaru_components_maybe_enqueue() {
	if ( ! is_singular() ) {
		return;
	}
	$id     = get_queried_object_id();
	$source = (string) get_post_field( 'post_content', $id ) . (string) get_post_meta( $id, '_elementor_data', true );
	if ( preg_match( '/subaru_(configurator|gallery)/', $source ) ) {
		subaru_components_enqueue();
	}
}
add_action( 'wp_enqueue_scripts', 'subaru_components_maybe_enqueue', 25 );

/**
 * Enqueue component CSS/JS (safe to call more than once).
 */
function subaru_components_enqueue() {
	wp_enqueue_style( 'subaru-models' );
	wp_enqueue_script( 'subaru-models' );
}

/**
 * Render a template part to a string.
 *
 * @param string $slug Template part (template-parts/{slug}.php).
 * @param array  $args Arguments.
 * @return string
 */
function subaru_component_render( $slug, $args ) {
	ob_start();
	get_template_part( 'template-parts/' . $slug, null, $args );
	return ob_get_clean();
}

/**
 * Configurator data for a model (passed to the template and, as JSON, to models.js).
 *
 * @param array $model Model data.
 * @return array
 */
function subaru_configurator_data( $model ) {
	$all     = subaru_models();
	$colours = array();
	foreach ( $model['colours'] as $key ) {
		$path = subaru_picker_path( $model, $key );
		if ( ! subaru_asset_exists( $path ) ) {
			continue;
		}
		$c      = subaru_colour( $key );
		$frames = subaru_spin_frames( $model['slug'], $key );
		list( $fw, $fh ) = $frames ? subaru_asset_dims( $frames[0] ) : array( 0, 0 );
		$colours[] = array(
			'key'    => $key,
			'name'   => $c['name'],
			'hex'    => $c['hex'],
			'path'   => $path,
			'src'    => subaru_asset_url( $path ),
			'srcset' => subaru_asset_srcset( $path ),
			'alt'    => subaru_car_alt( $model, $key ),
			'frames' => array_map( 'subaru_asset_url', $frames ),
			'fw'     => $fw,
			'fh'     => $fh,
		);
	}
	$default = $model['default_colour'];
	if ( ! wp_list_filter( $colours, array( 'key' => $default ) ) && $colours ) {
		$default = $colours[0]['key'];
	}

	$interiors = array();
	foreach ( $model['interiors'] as $id => $set ) {
		$views = array();
		foreach ( $set['views'] as $v ) {
			if ( ! subaru_asset_exists( $v['file'] ) ) {
				continue;
			}
			$views[] = array(
				'label'  => $v['label'],
				'trim'   => $v['trim'],
				'hex'    => isset( $all['trims'][ $v['trim'] ] ) ? $all['trims'][ $v['trim'] ] : '',
				'path'   => $v['file'],
				'src'    => subaru_asset_url( $v['file'] ),
				'srcset' => subaru_asset_srcset( $v['file'] ),
				/* translators: 1: model (and grade), 2: view label */
				'alt'    => sprintf( __( 'Subaru %1$s interior: %2$s', 'subaru-alkhoory' ), $set['label'] === $model['name'] ? $model['name'] : $model['name'] . ' ' . $set['label'], strtolower( $v['label'] ) ),
			);
		}
		if ( $views ) {
			$interiors[] = array(
				'id'    => (string) $id,
				'label' => $set['label'],
				'views' => $views,
			);
		}
	}

	$grades = array();
	foreach ( $model['grades'] as $g ) {
		$grades[] = array(
			'name'     => $g['name'],
			'interior' => (string) $g['interior'],
		);
	}

	return array(
		'model'     => $model['slug'],
		'name'      => $model['name'],
		'default'   => $default,
		'colours'   => $colours,
		'interiors' => $interiors,
		'grades'    => $grades,
	);
}

/**
 * [subaru_configurator model="forester"]
 *
 * @param array $atts Attributes.
 * @return string
 */
function subaru_configurator_shortcode( $atts ) {
	$atts  = shortcode_atts( array( 'model' => '' ), $atts, 'subaru_configurator' );
	$model = subaru_model( $atts['model'] );
	if ( ! $model ) {
		return '';
	}
	$data = subaru_configurator_data( $model );
	if ( ! $data['colours'] ) {
		return '';
	}
	subaru_components_enqueue();
	return subaru_component_render(
		'configurator',
		array(
			'model' => $model,
			'data'  => $data,
		)
	);
}
add_shortcode( 'subaru_configurator', 'subaru_configurator_shortcode' );

/**
 * [subaru_gallery model="" layout="feature|masonry" filter="yes|no" link=""]
 *
 * @param array $atts Attributes.
 * @return string
 */
function subaru_gallery_shortcode( $atts ) {
	$atts   = shortcode_atts(
		array(
			'model'  => '',
			'layout' => 'masonry',
			'filter' => 'no',
		),
		$atts,
		'subaru_gallery'
	);
	$all    = subaru_models();
	$models = $atts['model'] ? array_filter( array( subaru_model( $atts['model'] ) ) ) : $all['models'];
	$items  = array();
	foreach ( $models as $m ) {
		foreach ( $m['gallery'] as $g ) {
			if ( subaru_asset_exists( $g[0] ) ) {
				$items[] = array(
					'path'  => $g[0],
					'alt'   => $g[1],
					'model' => $m['slug'],
					'name'  => $m['name'],
				);
			}
		}
	}
	if ( ! $items ) {
		return '';
	}
	subaru_components_enqueue();
	return subaru_component_render(
		'gallery',
		array(
			'items'  => $items,
			'layout' => 'feature' === $atts['layout'] ? 'feature' : 'masonry',
			'filter' => 'yes' === $atts['filter'],
		)
	);
}
add_shortcode( 'subaru_gallery', 'subaru_gallery_shortcode' );
