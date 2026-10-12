<?php
/**
 * Navigation: menu tree builder, hard-coded fallbacks, renderers for desktop/mega/mobile/footer menus.
 *
 * Menus are rendered from a normalised tree so that real WP menus and the built-in fallbacks
 * share exactly the same markup.
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build a site URL from a path.
 *
 * @param string $path Path.
 * @return string
 */
function subaru_url( $path = '/' ) {
	return home_url( $path );
}

/**
 * Fallback item helper.
 *
 * @param string $title    Title.
 * @param string $path     Path.
 * @param array  $children Children.
 * @param string $desc     Description.
 * @return array
 */
function subaru_nav_item( $title, $path, $children = array(), $desc = '' ) {
	return array(
		'id'          => 'fb-' . sanitize_title( $title ),
		'title'       => $title,
		'url'         => subaru_url( $path ),
		'description' => $desc,
		'target'      => '',
		'classes'     => array(),
		'children'    => $children,
	);
}

/**
 * Hard-coded fallback menus matching the site's page structure.
 *
 * @param string $location Location.
 * @return array
 */
function subaru_fallback_menu( $location ) {
	$models = array();
	foreach ( subaru_models() as $m ) {
		$models[] = subaru_nav_item( 'Subaru ' . $m['name'], $m['path'], array(), $m['tagline'] );
	}
	$after = array(
		subaru_nav_item( __( 'Schedule Service', 'subaru-alkhoory' ), '/after-sales-services/#schedule-service' ),
		subaru_nav_item( __( 'Service Maintenance', 'subaru-alkhoory' ), '/after-sales-services/#service-maintenance' ),
		subaru_nav_item( __( 'On-Road Assistance', 'subaru-alkhoory' ), '/after-sales-services/#on-road-assistance' ),
		subaru_nav_item( __( 'Warranty', 'subaru-alkhoory' ), '/after-sales-services/#warranty' ),
	);
	$resources = array(
		subaru_nav_item( __( 'FAQs', 'subaru-alkhoory' ), '/faqs/' ),
		subaru_nav_item( __( 'Newsroom', 'subaru-alkhoory' ), '/newsroom/' ),
		subaru_nav_item( __( 'Awards', 'subaru-alkhoory' ), '/awards/' ),
		subaru_nav_item( __( 'Media Gallery', 'subaru-alkhoory' ), '/media-gallery/' ),
	);

	switch ( $location ) {
		case 'primary':
			return array(
				subaru_nav_item( __( 'Our Range', 'subaru-alkhoory' ), '/our-range/', $models ),
				subaru_nav_item( __( 'About Us', 'subaru-alkhoory' ), '/about-us/' ),
				subaru_nav_item(
					__( 'Why Subaru?', 'subaru-alkhoory' ),
					'/subaru-technology/',
					array(
						subaru_nav_item( __( 'Subaru Technology', 'subaru-alkhoory' ), '/subaru-technology/' ),
						subaru_nav_item( __( 'Subaru Safety', 'subaru-alkhoory' ), '/subaru-safety/' ),
					)
				),
				subaru_nav_item( __( 'Offers', 'subaru-alkhoory' ), '/current-offers/' ),
				subaru_nav_item( __( 'After-Sales', 'subaru-alkhoory' ), '/after-sales-services/', $after ),
				subaru_nav_item( __( 'Resources', 'subaru-alkhoory' ), '/faqs/', $resources ),
				subaru_nav_item( __( 'Contact Us', 'subaru-alkhoory' ), '/contact-us/' ),
			);
		case 'topbar':
			return array(
				subaru_nav_item( __( 'Download A Brochure', 'subaru-alkhoory' ), '/contact-us/?enquiry=brochure' ),
				subaru_nav_item( __( 'Book A Service', 'subaru-alkhoory' ), '/after-sales-services/#schedule-service' ),
				subaru_nav_item( __( 'Browse Vehicles', 'subaru-alkhoory' ), '/our-range/' ),
				subaru_nav_item( __( 'Find a Showroom', 'subaru-alkhoory' ), '/contact-us/' ),
			);
		case 'footer_range':
			$out = array();
			foreach ( subaru_models() as $m ) {
				$out[] = subaru_nav_item( $m['name'], $m['path'] );
			}
			return $out;
		case 'footer_discover':
			return array(
				subaru_nav_item( __( 'About Us', 'subaru-alkhoory' ), '/about-us/' ),
				subaru_nav_item( __( 'Why Subaru?', 'subaru-alkhoory' ), '/subaru-technology/' ),
				subaru_nav_item( __( 'Subaru Technology', 'subaru-alkhoory' ), '/subaru-technology/' ),
				subaru_nav_item( __( 'Safety', 'subaru-alkhoory' ), '/subaru-safety/' ),
				subaru_nav_item( __( 'Current Offers', 'subaru-alkhoory' ), '/current-offers/' ),
			);
		case 'footer_owners':
			return array_merge( array( subaru_nav_item( __( 'Book a Test Drive', 'subaru-alkhoory' ), '/book-a-test-drive/' ) ), $after );
		case 'footer_resources':
			return array_merge( $resources, array( subaru_nav_item( __( 'Contact Us', 'subaru-alkhoory' ), '/contact-us/' ) ) );
		case 'legal':
			return array(
				subaru_nav_item( __( 'Privacy Policy', 'subaru-alkhoory' ), '/privacy-policy/' ),
				subaru_nav_item( __( 'Cookie Preferences', 'subaru-alkhoory' ), '/privacy-policy/#cookies' ),
				subaru_nav_item( __( 'Conditions of Use', 'subaru-alkhoory' ), '/privacy-policy/#terms' ),
			);
	}
	return array();
}

/**
 * Get the menu tree for a location: assigned menu → menu with the default slug → fallback.
 *
 * @param string $location Location.
 * @return array
 */
function subaru_menu_tree( $location ) {
	static $cache = array();
	if ( isset( $cache[ $location ] ) ) {
		return $cache[ $location ];
	}

	$menu      = false;
	$locations = get_nav_menu_locations();
	if ( ! empty( $locations[ $location ] ) ) {
		$menu = wp_get_nav_menu_object( $locations[ $location ] );
	}
	if ( ! $menu ) {
		$defs = subaru_menu_locations();
		if ( isset( $defs[ $location ] ) ) {
			$menu = wp_get_nav_menu_object( $defs[ $location ][1] );
		}
	}

	$tree  = array();
	$items = $menu ? wp_get_nav_menu_items( $menu->term_id, array( 'update_post_term_cache' => false ) ) : array();

	if ( $items ) {
		$by_id = array();
		foreach ( $items as $item ) {
			$by_id[ $item->ID ] = array(
				'id'          => $item->ID,
				'title'       => $item->title,
				'url'         => $item->url,
				'description' => trim( (string) $item->description ),
				'target'      => $item->target,
				'classes'     => array_filter( (array) $item->classes ),
				'parent'      => (int) $item->menu_item_parent,
				'object_id'   => (int) $item->object_id,
				'children'    => array(),
			);
		}
		// Attach children (two levels are supported by the design).
		foreach ( array_reverse( $by_id, true ) as $id => $node ) {
			if ( $node['parent'] && isset( $by_id[ $node['parent'] ] ) ) {
				array_unshift( $by_id[ $node['parent'] ]['children'], $by_id[ $id ] );
			}
		}
		foreach ( $by_id as $node ) {
			if ( ! $node['parent'] || ! isset( $by_id[ $node['parent'] ] ) ) {
				$tree[] = $node;
			}
		}
		// Hide a plain "Home" link from the primary bar (the logo links home) unless it carries a class.
		if ( 'primary' === $location ) {
			$tree = array_values(
				array_filter(
					$tree,
					function ( $n ) {
						$is_home = untrailingslashit( $n['url'] ) === untrailingslashit( home_url( '/' ) );
						return ! ( $is_home && empty( $n['children'] ) && ! in_array( 'show', $n['classes'], true ) );
					}
				)
			);
		}
	} else {
		$tree = subaru_fallback_menu( $location );
	}

	$tree               = subaru_mark_current( $tree );
	$cache[ $location ] = apply_filters( 'subaru_menu_tree', $tree, $location );
	return $cache[ $location ];
}

/**
 * Normalise a URL for comparison (no scheme/host, no query, trailing slash).
 *
 * @param string $url URL.
 * @return string
 */
function subaru_norm_url( $url ) {
	$path = (string) wp_parse_url( $url, PHP_URL_PATH );
	return trailingslashit( $path );
}

/**
 * Mark current & ancestor items.
 *
 * @param array $tree Tree.
 * @return array
 */
function subaru_mark_current( $tree ) {
	global $wp;
	$current = subaru_norm_url( home_url( isset( $wp->request ) ? $wp->request : '/' ) );
	$blog    = (int) get_option( 'page_for_posts' );
	$blogurl = $blog ? subaru_norm_url( get_permalink( $blog ) ) : '';
	$is_post = is_singular( 'post' ) || is_category() || is_tag() || is_author() || is_date();

	foreach ( $tree as &$node ) {
		$node['current']  = false;
		$node['ancestor'] = false;
		$has_hash         = false !== strpos( $node['url'], '#' );
		$url              = subaru_norm_url( $node['url'] );
		if ( ! $has_hash && ( $url === $current || ( $is_post && $blogurl && $url === $blogurl ) ) ) {
			$node['current'] = true;
		}
		if ( ! empty( $node['children'] ) ) {
			$node['children'] = subaru_mark_current( $node['children'] );
			foreach ( $node['children'] as $child ) {
				if ( $child['current'] || $child['ancestor'] ) {
					$node['ancestor'] = true;
				}
			}
			// Parent of a section (e.g. /our-range/forester/ under /our-range/).
			if ( ! $node['current'] && ! $has_hash && '/' !== $url && 0 === strpos( $current, $url ) ) {
				$node['ancestor'] = true;
			}
		}
	}
	unset( $node );
	return $tree;
}

/**
 * Link attributes for a node.
 *
 * @param array  $node  Node.
 * @param string $class Class.
 * @return string Escaped attribute string.
 */
function subaru_link_attrs( $node, $class = '' ) {
	$attrs = ' href="' . esc_url( $node['url'] ) . '"';
	if ( $class ) {
		$attrs .= ' class="' . esc_attr( $class ) . '"';
	}
	if ( ! empty( $node['target'] ) ) {
		$attrs .= ' target="' . esc_attr( $node['target'] ) . '" rel="noopener"';
	}
	if ( ! empty( $node['current'] ) ) {
		$attrs .= ' aria-current="page"';
	}
	return $attrs;
}

/**
 * Whether a top-level item should use the "Our Range" mega menu layout.
 *
 * @param array $node Node.
 * @return bool
 */
function subaru_is_mega( $node ) {
	if ( empty( $node['children'] ) ) {
		return false;
	}
	if ( in_array( 'mega', $node['classes'], true ) ) {
		return true;
	}
	$hits = 0;
	foreach ( $node['children'] as $child ) {
		if ( subaru_model_from_url( $child['url'] ) ) {
			++$hits;
		}
	}
	return $hits >= 2;
}

/**
 * Thumbnail for a model menu item: featured image of the linked page, else model page lookup.
 *
 * @param array $node Node.
 * @return string URL or ''.
 */
function subaru_menu_item_thumb( $node ) {
	$id = ! empty( $node['object_id'] ) ? (int) $node['object_id'] : url_to_postid( $node['url'] );
	if ( $id && has_post_thumbnail( $id ) ) {
		return (string) get_the_post_thumbnail_url( $id, 'subaru-thumb' );
	}
	$slug = subaru_model_from_url( $node['url'] );
	return $slug ? subaru_model_thumb( $slug, 'subaru-thumb' ) : '';
}

/**
 * Render the desktop primary navigation.
 */
function subaru_render_primary_nav() {
	$tree = subaru_menu_tree( 'primary' );
	echo '<ul class="sa-nav__list">';
	foreach ( $tree as $i => $node ) {
		$has  = ! empty( $node['children'] );
		$mega = subaru_is_mega( $node );
		$cls  = array( 'sa-nav__item' );
		if ( $has ) {
			$cls[] = 'has-children';
		}
		if ( $mega ) {
			$cls[] = 'has-mega';
		}
		if ( $node['current'] ) {
			$cls[] = 'is-current';
		}
		if ( $node['ancestor'] ) {
			$cls[] = 'is-ancestor';
		}
		$sub_id = 'sa-sub-' . $i;
		echo '<li class="' . esc_attr( implode( ' ', array_merge( $cls, $node['classes'] ) ) ) . '">';
		echo '<a' . subaru_link_attrs( $node, 'sa-nav__link' ) . '>' . esc_html( $node['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper.
		echo '</a>';
		if ( $has ) {
			/* translators: %s: menu item title */
			echo '<button class="sa-nav__toggle" type="button" aria-expanded="false" aria-controls="' . esc_attr( $sub_id ) . '"><span class="screen-reader-text">' . esc_html( sprintf( __( 'Show %s submenu', 'subaru-alkhoory' ), $node['title'] ) ) . '</span>';
			subaru_the_icon( 'chevron', 14 );
			echo '</button>';
			if ( $mega ) {
				subaru_render_mega( $node, $sub_id );
			} else {
				echo '<div class="sa-dropdown" id="' . esc_attr( $sub_id ) . '"><ul class="sa-dropdown__list">';
				foreach ( $node['children'] as $child ) {
					echo '<li class="sa-dropdown__item' . ( $child['current'] ? ' is-current' : '' ) . '"><a' . subaru_link_attrs( $child, 'sa-dropdown__link' ) . '><span class="sa-dropdown__title">' . esc_html( $child['title'] ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					if ( $child['description'] ) {
						echo '<span class="sa-dropdown__desc">' . esc_html( $child['description'] ) . '</span>';
					}
					echo '</a></li>';
				}
				echo '</ul></div>';
			}
		}
		echo '</li>';
	}
	echo '</ul>';
}

/**
 * Mega menu panel for "Our Range".
 *
 * @param array  $node   Parent node.
 * @param string $sub_id Panel id.
 */
function subaru_render_mega( $node, $sub_id ) {
	$models = subaru_models();
	echo '<div class="sa-mega" id="' . esc_attr( $sub_id ) . '"><div class="sa-mega__inner">';
	echo '<div class="sa-mega__intro"><p class="sa-mega__eyebrow">' . esc_html( $node['title'] ) . '</p>';
	echo '<p class="sa-mega__title">' . esc_html( $node['description'] ? $node['description'] : __( 'Find the Subaru built for you.', 'subaru-alkhoory' ) ) . '</p>';
	echo '<a class="sa-mega__all" href="' . esc_url( $node['url'] ) . '">' . esc_html__( 'View all models', 'subaru-alkhoory' ) . ' ';
	subaru_the_icon( 'arrow', 16 );
	echo '</a></div><ul class="sa-mega__grid">';
	foreach ( $node['children'] as $child ) {
		$slug    = subaru_model_from_url( $child['url'] );
		$thumb   = subaru_menu_item_thumb( $child );
		$tagline = $child['description'] ? $child['description'] : ( $slug ? $models[ $slug ]['tagline'] : '' );
		$type    = $slug ? $models[ $slug ]['type'] : '';
		echo '<li class="sa-mega__item' . ( $child['current'] ? ' is-current' : '' ) . '"><a' . subaru_link_attrs( $child, 'sa-mega__card' ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<span class="sa-mega__media">';
		if ( $thumb ) {
			echo '<img src="' . esc_url( $thumb ) . '" alt="" loading="lazy" decoding="async">';
		} else {
			echo '<span class="sa-mega__placeholder" aria-hidden="true">' . esc_html( $slug ? $models[ $slug ]['name'] : $child['title'] ) . '</span>';
		}
		echo '</span>';
		if ( $type ) {
			echo '<span class="sa-mega__type">' . esc_html( $type ) . '</span>';
		}
		echo '<span class="sa-mega__name">' . esc_html( $child['title'] ) . '</span>';
		if ( $tagline ) {
			echo '<span class="sa-mega__tagline">' . esc_html( $tagline ) . '</span>';
		}
		echo '<span class="sa-mega__cta">' . esc_html__( 'Explore', 'subaru-alkhoory' ) . ' ';
		subaru_the_icon( 'arrow', 14 );
		echo '</span></a></li>';
	}
	echo '</ul></div></div>';
}

/**
 * Render the mobile (full-screen) navigation list.
 */
function subaru_render_mobile_nav() {
	$tree = subaru_menu_tree( 'primary' );
	echo '<ul class="sa-mnav__list">';
	$i = 0;
	foreach ( $tree as $k => $node ) {
		$has = ! empty( $node['children'] );
		$cls = 'sa-mnav__item' . ( $has ? ' has-children' : '' ) . ( $node['current'] || $node['ancestor'] ? ' is-current' : '' );
		echo '<li class="' . esc_attr( $cls ) . '" style="--i:' . (int) $i . '">';
		if ( $has ) {
			$pid = 'sa-msub-' . $k;
			echo '<button type="button" class="sa-mnav__link sa-mnav__acc" aria-expanded="false" aria-controls="' . esc_attr( $pid ) . '">' . esc_html( $node['title'] );
			subaru_the_icon( 'plus', 20, 'sa-mnav__plus' );
			echo '</button><div class="sa-mnav__sub" id="' . esc_attr( $pid ) . '"><ul>';
			echo '<li><a' . subaru_link_attrs( $node, 'sa-mnav__sublink sa-mnav__overview' ) . '>' . esc_html__( 'Overview', 'subaru-alkhoory' ) . '</a></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			foreach ( $node['children'] as $child ) {
				echo '<li><a' . subaru_link_attrs( $child, 'sa-mnav__sublink' ) . '>' . esc_html( $child['title'] ) . '</a></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			echo '</ul></div>';
		} else {
			echo '<a' . subaru_link_attrs( $node, 'sa-mnav__link' ) . '>' . esc_html( $node['title'] ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</li>';
		++$i;
	}
	echo '</ul>';
}

/**
 * Icon for a top-bar quick action, guessed from its URL/title.
 *
 * @param array $node Node.
 * @return string Icon name.
 */
function subaru_topbar_icon( $node ) {
	$hay = strtolower( $node['title'] . ' ' . $node['url'] );
	if ( false !== strpos( $hay, 'brochure' ) ) {
		return 'book';
	}
	if ( false !== strpos( $hay, 'service' ) ) {
		return 'tools';
	}
	if ( false !== strpos( $hay, 'vehicle' ) || false !== strpos( $hay, 'range' ) ) {
		return 'car';
	}
	if ( false !== strpos( $hay, 'showroom' ) || false !== strpos( $hay, 'contact' ) ) {
		return 'pin';
	}
	return 'arrow';
}

/**
 * Render a flat link list (top bar, footer columns, legal).
 *
 * @param string $location   Location.
 * @param string $class      List class.
 * @param bool   $with_icons Add top-bar icons.
 */
function subaru_render_links( $location, $class, $with_icons = false ) {
	$tree = subaru_menu_tree( $location );
	if ( ! $tree ) {
		return;
	}
	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( $tree as $node ) {
		echo '<li' . ( $node['current'] ? ' class="is-current"' : '' ) . '><a' . subaru_link_attrs( $node ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( $with_icons ) {
			subaru_the_icon( subaru_topbar_icon( $node ), 15 );
		}
		echo '<span>' . esc_html( $node['title'] ) . '</span></a></li>';
	}
	echo '</ul>';
}

/**
 * Footer column title: the menu's name if assigned, else a default label.
 *
 * @param string $location Location.
 * @param string $default  Default label.
 * @return string
 */
function subaru_menu_title( $location, $default ) {
	$locations = get_nav_menu_locations();
	if ( ! empty( $locations[ $location ] ) ) {
		$menu = wp_get_nav_menu_object( $locations[ $location ] );
		if ( $menu && 0 !== strpos( strtolower( $menu->name ), 'footer' ) ) {
			return $menu->name;
		}
	}
	return $default;
}
