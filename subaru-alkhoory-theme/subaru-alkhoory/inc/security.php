<?php
/**
 * SVG uploads (administrators only) with basic sanitisation.
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allow SVG mime for administrators.
 *
 * @param array $mimes Mimes.
 * @return array
 */
function subaru_svg_mimes( $mimes ) {
	if ( current_user_can( 'manage_options' ) ) {
		$mimes['svg'] = 'image/svg+xml';
	}
	return $mimes;
}
add_filter( 'upload_mimes', 'subaru_svg_mimes' );

/**
 * WordPress' real-mime check rejects SVG; confirm the extension for admins.
 *
 * @param array  $data     File data.
 * @param string $file     Path.
 * @param string $filename Name.
 * @return array
 */
function subaru_svg_filetype( $data, $file, $filename ) {
	if ( current_user_can( 'manage_options' ) && 'svg' === strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) ) ) {
		$data['ext']  = 'svg';
		$data['type'] = 'image/svg+xml';
	}
	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'subaru_svg_filetype', 10, 3 );

/**
 * Sanitise an SVG file in place.
 *
 * @param string $path File path.
 * @return bool
 */
function subaru_sanitize_svg_file( $path ) {
	$svg = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( false === $svg || false === stripos( $svg, '<svg' ) ) {
		return false;
	}
	// Remove doctype/entities (XXE / billion laughs).
	$svg = preg_replace( '/<!DOCTYPE[^>]*(\[[\s\S]*?\])?>/i', '', $svg );
	$svg = preg_replace( '/<!ENTITY[\s\S]*?>/i', '', $svg );

	if ( ! class_exists( 'DOMDocument' ) ) {
		// Regex fallback.
		$svg = preg_replace( '#<(script|foreignObject|iframe|embed|object)[\s\S]*?</\1>#i', '', $svg );
		$svg = preg_replace( '/\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $svg );
		$svg = preg_replace( '/(href\s*=\s*["\']?)\s*javascript:[^"\'>\s]*/i', '$1#', $svg );
		return false !== file_put_contents( $path, $svg ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	}

	$prev = libxml_use_internal_errors( true );
	$dom  = new DOMDocument();
	$ok   = $dom->loadXML( $svg, LIBXML_NONET | LIBXML_NOBLANKS );
	libxml_clear_errors();
	libxml_use_internal_errors( $prev );
	if ( ! $ok ) {
		return false;
	}

	$bad_tags = array( 'script', 'foreignobject', 'iframe', 'embed', 'object', 'handler', 'listener' );
	$xpath    = new DOMXPath( $dom );
	$remove   = array();
	foreach ( $xpath->query( '//*' ) as $node ) {
		if ( in_array( strtolower( $node->localName ), $bad_tags, true ) ) {
			$remove[] = $node;
			continue;
		}
		if ( $node->hasAttributes() ) {
			$attrs = array();
			foreach ( $node->attributes as $attr ) {
				$attrs[] = $attr;
			}
			foreach ( $attrs as $attr ) {
				$name  = strtolower( $attr->nodeName );
				$value = strtolower( preg_replace( '/\s+/', '', $attr->nodeValue ) );
				if ( 0 === strpos( $name, 'on' ) ) {
					$node->removeAttributeNode( $attr );
				} elseif ( in_array( $name, array( 'href', 'xlink:href', 'src' ), true ) && ( 0 === strpos( $value, 'javascript:' ) || 0 === strpos( $value, 'data:text/html' ) ) ) {
					$node->removeAttributeNode( $attr );
				}
			}
		}
	}
	foreach ( $remove as $node ) {
		if ( $node->parentNode ) {
			$node->parentNode->removeChild( $node );
		}
	}
	return false !== file_put_contents( $path, $dom->saveXML( $dom->documentElement ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
}

/**
 * Sanitise SVGs before they are moved into uploads.
 *
 * @param array $file Upload.
 * @return array
 */
function subaru_svg_prefilter( $file ) {
	$ext = strtolower( pathinfo( isset( $file['name'] ) ? $file['name'] : '', PATHINFO_EXTENSION ) );
	if ( 'svg' !== $ext ) {
		return $file;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		$file['error'] = __( 'Only administrators may upload SVG files.', 'subaru-alkhoory' );
		return $file;
	}
	if ( ! subaru_sanitize_svg_file( $file['tmp_name'] ) ) {
		$file['error'] = __( 'This SVG file could not be sanitised and was rejected.', 'subaru-alkhoory' );
	}
	return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'subaru_svg_prefilter' );

/**
 * Show SVG previews in the media library grid.
 *
 * @param array   $response   Response.
 * @param WP_Post $attachment Attachment.
 * @return array
 */
function subaru_svg_media_preview( $response, $attachment ) {
	if ( 'image/svg+xml' === $response['mime'] ) {
		$url               = wp_get_attachment_url( $attachment->ID );
		$response['image'] = array( 'src' => $url );
		$response['sizes'] = array(
			'full' => array(
				'url'         => $url,
				'orientation' => 'landscape',
			),
		);
	}
	return $response;
}
add_filter( 'wp_prepare_attachment_for_js', 'subaru_svg_media_preview', 10, 2 );
