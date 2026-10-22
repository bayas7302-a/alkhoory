<?php
/**
 * Subaru image pack helpers. The pack lives outside the theme in wp-content/uploads/subaru-assets/
 * (7 folders, uploaded via cPanel) and is never registered in the Media Library.
 *
 * - subaru_asset_url() / subaru_asset_img(): URLs and complete <img> tags (width/height, alt, srcset).
 * - subaru_models() / subaru_model(): the data file (inc/subaru-models.php).
 * - subaru_spin_frames(): 360° frames, discovered on disk so folder/file naming can vary.
 * - Elementor output filter: adds width/height/loading/decoding/srcset to pack images placed in pages.
 * - GET /wp-json/subaru/v1/assets (administrators): health check of the pack against the data file.
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Public URL of a file in the asset pack.
 *
 * @param string $path Path relative to the pack root, e.g. "01_Logos/Model_names/BRZ_logo_white.png".
 * @return string
 */
function subaru_asset_url( $path ) {
	$base = apply_filters( 'subaru_assets_base_url', content_url( 'uploads/subaru-assets/' ) );
	return esc_url( $base . ltrim( str_replace( ' ', '%20', $path ), '/' ) );
}

/**
 * Filesystem directory of the asset pack (trailing slash).
 *
 * @return string
 */
function subaru_assets_dir() {
	return apply_filters( 'subaru_assets_base_dir', trailingslashit( WP_CONTENT_DIR ) . 'uploads/subaru-assets/' );
}

/**
 * Whether a pack file exists on the server.
 *
 * @param string $path Relative path.
 * @return bool
 */
function subaru_asset_exists( $path ) {
	return '' !== $path && is_file( subaru_assets_dir() . ltrim( $path, '/' ) );
}

/**
 * Model data (inc/subaru-models.php), loaded once per request.
 *
 * @return array
 */
function subaru_models() {
	static $data = null;
	if ( null === $data ) {
		$data = apply_filters( 'subaru_models_data', require SUBARU_DIR . '/inc/subaru-models.php' );
	}
	return $data;
}

/**
 * One model's data, or null.
 *
 * @param string $slug forester|crosstrek|wrx|brz.
 * @return array|null
 */
function subaru_model( $slug ) {
	$all = subaru_models();
	$slug = sanitize_key( $slug );
	return isset( $all['models'][ $slug ] ) ? $all['models'][ $slug ] : null;
}

/**
 * Display data for a paint colour key.
 *
 * @param string $key Colour key, e.g. Sapphire_Blue_Pearl.
 * @return array { name, hex }
 */
function subaru_colour( $key ) {
	$all = subaru_models();
	if ( isset( $all['colours'][ $key ] ) ) {
		return $all['colours'][ $key ];
	}
	return array(
		'name' => ucwords( str_replace( '_', ' ', $key ) ),
		'hex'  => '#cccccc',
	);
}

/**
 * Pixel size of a pack file: the generated map first, then the file itself (cached).
 *
 * @param string $path Relative path.
 * @return int[] array( width, height ) or array( 0, 0 ).
 */
function subaru_asset_dims( $path ) {
	static $map = null;
	static $measured = null;
	if ( null === $map ) {
		$map = require SUBARU_DIR . '/inc/subaru-asset-dims.php';
	}
	if ( isset( $map[ $path ] ) ) {
		return $map[ $path ];
	}
	if ( null === $measured ) {
		$measured = get_option( 'subaru_asset_dims', array() );
		$measured = is_array( $measured ) ? $measured : array();
	}
	if ( isset( $measured[ $path ] ) ) {
		return $measured[ $path ];
	}
	$size = subaru_asset_exists( $path ) ? @getimagesize( subaru_assets_dir() . $path ) : false; // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	$dims = $size ? array( (int) $size[0], (int) $size[1] ) : array( 0, 0 );
	if ( $size ) {
		$measured[ $path ] = $dims;
		update_option( 'subaru_asset_dims', $measured, false );
	}
	return $dims;
}

/**
 * Logo file for a pattern ("..._logo_%s.png") and tone.
 *
 * @param string $pattern File pattern from the data file.
 * @param string $tone    original-colour|white|dark.
 * @return string
 */
function subaru_logo_path( $pattern, $tone = 'dark' ) {
	$tone = in_array( $tone, array( 'original-colour', 'white', 'dark' ), true ) ? $tone : 'dark';
	return sprintf( $pattern, $tone );
}

/**
 * Picker (front three-quarter) image of a model in a colour.
 *
 * @param array  $model  Model data.
 * @param string $colour Colour key.
 * @return string
 */
function subaru_picker_path( $model, $colour ) {
	return sprintf( $model['picker'], $colour );
}

/**
 * Standard alt text for a car image.
 *
 * @param array  $model  Model data.
 * @param string $colour Colour key.
 * @param string $view   View description.
 * @return string
 */
function subaru_car_alt( $model, $colour, $view = 'front three-quarter view' ) {
	$c = subaru_colour( $colour );
	/* translators: 1: model name, 2: colour name, 3: camera view */
	return sprintf( __( 'Subaru %1$s in %2$s, %3$s', 'subaru-alkhoory' ), $model['name'], $c['name'], $view );
}

/* ---------------------------------------------------------------- resized copies */

/**
 * URL of a downscaled copy (wp-content/uploads/subaru-assets-cache/{width}/{path}), created on demand
 * with the WordPress image editor. A small time budget per request keeps first page loads fast;
 * remaining copies are made on later visits. Disable with add_filter( 'subaru_assets_resize', '__return_false' ).
 *
 * @param string $path  Relative path.
 * @param int    $width Target width.
 * @return string Empty when no copy is available (yet).
 */
function subaru_asset_resized_url( $path, $width ) {
	static $spent = 0.0;
	if ( ! apply_filters( 'subaru_assets_resize', true ) ) {
		return '';
	}
	$rel  = $width . '/' . ltrim( $path, '/' );
	$dest = trailingslashit( WP_CONTENT_DIR ) . 'uploads/subaru-assets-cache/' . $rel;
	$url  = content_url( 'uploads/subaru-assets-cache/' . str_replace( ' ', '%20', $rel ) );
	if ( is_file( $dest ) ) {
		return $url;
	}
	if ( $spent > (float) apply_filters( 'subaru_assets_resize_budget', 2.5 ) || ! subaru_asset_exists( $path ) ) {
		return '';
	}
	$start  = microtime( true );
	$editor = wp_get_image_editor( subaru_assets_dir() . $path );
	$ok     = false;
	if ( ! is_wp_error( $editor ) ) {
		$editor->set_quality( 82 );
		if ( ! is_wp_error( $editor->resize( $width, null, false ) ) ) {
			wp_mkdir_p( dirname( $dest ) );
			$ok = ! is_wp_error( $editor->save( $dest ) );
		}
	}
	$spent += microtime( true ) - $start;
	return $ok ? $url : '';
}

/**
 * srcset for a pack image (available resized copies + the original).
 *
 * @param string $path Relative path.
 * @return string
 */
function subaru_asset_srcset( $path ) {
	list( $w ) = subaru_asset_dims( $path );
	if ( ! $w ) {
		return '';
	}
	$set = array();
	foreach ( apply_filters( 'subaru_assets_widths', array( 640, 1024, 1600 ) ) as $tw ) {
		if ( $tw < $w ) {
			$u = subaru_asset_resized_url( $path, $tw );
			if ( $u ) {
				$set[] = esc_url( $u ) . ' ' . $tw . 'w';
			}
		}
	}
	if ( ! $set ) {
		return '';
	}
	$set[] = subaru_asset_url( $path ) . ' ' . $w . 'w';
	return implode( ', ', $set );
}

/**
 * Complete <img> tag for a pack file.
 *
 * @param string $path Relative path.
 * @param array  $args {
 *     @type string $alt      Alt text (required for meaningful images; '' marks decorative).
 *     @type string $class    Class attribute.
 *     @type string $sizes    sizes attribute (default 100vw).
 *     @type bool   $priority LCP image: eager + fetchpriority=high.
 *     @type bool   $srcset   Add a srcset (default true).
 *     @type array  $attrs    Extra attributes.
 * }
 * @return string
 */
function subaru_asset_img( $path, $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'alt'      => '',
			'class'    => '',
			'sizes'    => '100vw',
			'priority' => false,
			'srcset'   => true,
			'attrs'    => array(),
		)
	);
	list( $w, $h ) = subaru_asset_dims( $path );
	$attrs = array(
		'src'   => subaru_asset_url( $path ),
		'alt'   => $args['alt'],
		'class' => $args['class'],
	);
	if ( $w && $h ) {
		$attrs['width']  = $w;
		$attrs['height'] = $h;
	}
	if ( $args['priority'] ) {
		$attrs['fetchpriority'] = 'high';
		$attrs['loading']       = 'eager';
	} else {
		$attrs['loading'] = 'lazy';
	}
	$attrs['decoding'] = 'async';
	if ( $args['srcset'] ) {
		$srcset = subaru_asset_srcset( $path );
		if ( $srcset ) {
			$attrs['srcset'] = $srcset;
			$attrs['sizes']  = $args['sizes'];
		}
	}
	$attrs = array_merge( $attrs, $args['attrs'] );

	$html = '<img';
	foreach ( $attrs as $k => $v ) {
		if ( '' === $v && 'alt' !== $k ) {
			continue;
		}
		$html .= ' ' . $k . '="' . ( 'src' === $k ? $v : esc_attr( $v ) ) . '"';
	}
	return $html . '>';
}

/* ------------------------------------------------------------------ 360 frames */

/**
 * Normalised match tokens for a colour key: "Mag_Grey_Metallic" → maggreymetallic, magnetitegreymetallic, maggrey, magnetitegrey.
 *
 * @param string $key Colour key.
 * @return string[]
 */
function subaru_colour_tokens( $key ) {
	$c      = subaru_colour( $key );
	$tokens = array();
	foreach ( array( $key, $c['name'] ) as $s ) {
		$n        = strtolower( preg_replace( '/[^a-z0-9]/i', '', $s ) );
		$tokens[] = $n;
		$tokens[] = preg_replace( '/(metallic|pearl|silica|opal)$/', '', $n );
	}
	return array_values( array_unique( array_filter( $tokens ) ) );
}

/**
 * 360° frames for a model, grouped by colour key, discovered on disk under the model's spin folder.
 * Matches folders/files to colours by name (any separator or case), orders frames naturally and caches
 * the result for 12 hours (cleared by GET /wp-json/subaru/v1/assets?flush=1).
 *
 * @param string $slug Model slug.
 * @return array colour key => string[] relative frame paths.
 */
function subaru_spin_index( $slug ) {
	$model = subaru_model( $slug );
	if ( ! $model || empty( $model['spin_dir'] ) ) {
		return array();
	}
	$cache_key = 'subaru_spins_' . $slug . '_' . md5( SUBARU_VERSION . $model['spin_dir'] );
	$cached    = get_transient( $cache_key );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	$root  = subaru_assets_dir();
	$dir   = $root . trim( $model['spin_dir'], '/' );
	$index = array();
	if ( is_dir( $dir ) ) {
		$tokens = array();
		foreach ( $model['colours'] as $key ) {
			foreach ( subaru_colour_tokens( $key ) as $tk ) {
				$tokens[ $tk ] = $key;
			}
		}
		// Longest tokens first so "crystalwhitepearl" wins over "crystalwhite".
		uksort(
			$tokens,
			function ( $a, $b ) {
				return strlen( $b ) - strlen( $a );
			}
		);
		$it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ) );
		$n  = 0;
		foreach ( $it as $file ) {
			if ( ++$n > 3000 ) {
				break;
			}
			if ( ! $file->isFile() || ! preg_match( '/\.(jpe?g|png|webp)$/i', $file->getFilename() ) ) {
				continue;
			}
			$rel  = ltrim( str_replace( '\\', '/', substr( $file->getPathname(), strlen( $root ) ) ), '/' );
			$hay  = strtolower( preg_replace( '/[^a-z0-9]/i', '', substr( $rel, strlen( $model['spin_dir'] ) ) ) );
			foreach ( $tokens as $tk => $key ) {
				if ( false !== strpos( $hay, $tk ) ) {
					$index[ $key ][] = $rel;
					break;
				}
			}
		}
		foreach ( $index as $key => $frames ) {
			natcasesort( $frames );
			$index[ $key ] = array_values( $frames );
		}
	}
	set_transient( $cache_key, $index, 12 * HOUR_IN_SECONDS );
	return $index;
}

/**
 * 360° frames for one colour.
 *
 * @param string $slug   Model slug.
 * @param string $colour Colour key.
 * @return string[]
 */
function subaru_spin_frames( $slug, $colour ) {
	$index = subaru_spin_index( $slug );
	return isset( $index[ $colour ] ) ? $index[ $colour ] : array();
}

/* -------------------------------------------- pack images placed in Elementor pages */

/**
 * Pack images placed directly in Elementor (image elements with the asset URL) get width/height,
 * decoding, loading and srcset. Images with the "sub-lcp" class are the page's hero (eager, high priority).
 *
 * @param string $content Builder HTML.
 * @return string
 */
function subaru_assets_filter_html( $content ) {
	if ( false === strpos( $content, 'subaru-assets/' ) ) {
		return $content;
	}
	$base = apply_filters( 'subaru_assets_base_url', content_url( 'uploads/subaru-assets/' ) );
	return preg_replace_callback(
		'/<img\b[^>]*>/i',
		function ( $m ) use ( $base ) {
			$tag = $m[0];
			if ( ! preg_match( '/\ssrc="([^"]+)"/i', $tag, $src ) ) {
				return $tag;
			}
			$url = html_entity_decode( $src[1] );
			$pos = strpos( $url, 'uploads/subaru-assets/' );
			if ( false === $pos || 0 !== strpos( $url, preg_replace( '#uploads/subaru-assets/$#', '', $base ) ) ) {
				return $tag;
			}
			$path  = rawurldecode( strtok( substr( $url, $pos + strlen( 'uploads/subaru-assets/' ) ), '?' ) );
			$hero  = (bool) preg_match( '/class="[^"]*\bsub-lcp\b/', $tag );
			$add   = '';
			list( $w, $h ) = subaru_asset_dims( $path );
			if ( $w && ! preg_match( '/\swidth=/i', $tag ) ) {
				$add .= ' width="' . (int) $w . '" height="' . (int) $h . '"';
			}
			if ( ! preg_match( '/\sloading=/i', $tag ) ) {
				$add .= $hero ? ' loading="eager" fetchpriority="high"' : ' loading="lazy"';
			}
			if ( ! preg_match( '/\sdecoding=/i', $tag ) ) {
				$add .= ' decoding="async"';
			}
			if ( ! preg_match( '/\ssrcset=/i', $tag ) && ! preg_match( '#^01_Logos/#', $path ) ) {
				$srcset = subaru_asset_srcset( $path );
				if ( $srcset ) {
					$add .= ' srcset="' . esc_attr( $srcset ) . '" sizes="' . ( $hero ? '100vw' : '(max-width: 767px) 100vw, 50vw' ) . '"';
				}
			}
			return $add ? preg_replace( '/\s*\/?>$/', $add . '>', $tag ) : $tag;
		},
		$content
	);
}
add_filter( 'elementor/frontend/the_content', 'subaru_assets_filter_html', 20 );

/* -------------------------------------------------------------- health check (REST) */

/**
 * GET /wp-json/subaru/v1/assets — administrators only. Reports missing files referenced by the data
 * file and what the 360 discovery found. ?flush=1 clears the 360 cache first.
 */
function subaru_assets_rest_routes() {
	register_rest_route(
		'subaru/v1',
		'/assets',
		array(
			'methods'             => 'GET',
			'permission_callback' => function () {
				return current_user_can( 'manage_options' );
			},
			'callback'            => 'subaru_assets_rest_report',
		)
	);
}
add_action( 'rest_api_init', 'subaru_assets_rest_routes' );

/**
 * Build the health report.
 *
 * @param WP_REST_Request $req Request.
 * @return array
 */
function subaru_assets_rest_report( $req ) {
	$data = subaru_models();
	$out  = array(
		'base_url' => subaru_asset_url( '' ),
		'base_dir' => is_dir( subaru_assets_dir() ),
		'models'   => array(),
	);
	foreach ( $data['models'] as $slug => $m ) {
		if ( $req->get_param( 'flush' ) ) {
			delete_transient( 'subaru_spins_' . $slug . '_' . md5( SUBARU_VERSION . $m['spin_dir'] ) );
		}
		$files = array( subaru_logo_path( $m['logo'], 'white' ), subaru_logo_path( $m['logo'], 'dark' ), $m['home_image'], $m['hero'] );
		foreach ( $m['colours'] as $c ) {
			$files[] = subaru_picker_path( $m, $c );
		}
		foreach ( $m['grades'] as $g ) {
			$files[] = $g['image'];
		}
		foreach ( $m['interiors'] as $set ) {
			foreach ( $set['views'] as $v ) {
				$files[] = $v['file'];
			}
		}
		foreach ( $m['gallery'] as $g ) {
			$files[] = $g[0];
		}
		$missing = array_values(
			array_filter(
				array_unique( array_filter( $files ) ),
				function ( $f ) {
					return ! subaru_asset_exists( $f );
				}
			)
		);
		$spins = array();
		foreach ( subaru_spin_index( $slug ) as $key => $frames ) {
			$spins[ $key ] = array(
				'frames' => count( $frames ),
				'first'  => $frames ? $frames[0] : '',
			);
		}
		$sample = array();
		$dir    = subaru_assets_dir() . $m['spin_dir'];
		if ( is_dir( $dir ) ) {
			$it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir, FilesystemIterator::SKIP_DOTS ), RecursiveIteratorIterator::SELF_FIRST );
			foreach ( $it as $f ) {
				$sample[] = substr( str_replace( '\\', '/', $f->getPathname() ), strlen( subaru_assets_dir() ) );
				if ( count( $sample ) >= 15 ) {
					break;
				}
			}
		}
		$out['models'][ $slug ] = array(
			'missing'       => $missing,
			'spins'         => $spins,
			'spin_expected' => $m['spin_frames'],
			'spin_dir_list' => $sample,
		);
	}
	return $out;
}
