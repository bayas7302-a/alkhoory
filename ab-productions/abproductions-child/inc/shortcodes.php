<?php
/**
 * Content shortcodes used inside the Elementor pages.
 *
 * Put the shortcode on its own in an Elementor Paragraph (e.g. "[ab_clients_marquee]");
 * inc/elementor.php swaps the whole paragraph for the shortcode's output.
 *
 *   [ab_services_tabs]   Services: tabs on desktop, accordion on mobile
 *   [ab_clients_marquee] Scrolling client logos (home)
 *   [ab_clients_grid]    Client logo wall (about)
 *   [ab_find_us]         Google map with Dubai HQ / Sharjah toggle (contact)
 *   [ab_contact_card]    "Reach us directly" card (home + contact)
 *   [ab_anchor id="x"]   Jump target for menu links like /#industries
 *
 * @package abp
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * Services (content from the AB Productions company profile)
 * ---------------------------------------------------------------------- */

function abp_services() {
	return array(
		'interior-fitouts'    => array(
			'title' => 'Interior & Fitouts',
			'intro' => 'Commercial, residential and exhibition interiors, from space planning to the final finish.',
			'items' => array( 'Commercial Fit-Out', 'Residential Fit-Out', 'Exhibition Interiors', 'Space Planning', 'Partitioning', 'Ceiling & Flooring Solutions', 'Wall Treatments', 'Joinery', 'Custom Installations', 'Renovation Solutions', 'Refurbishment Solutions' ),
		),
		'rental-services'     => array(
			'title' => 'Rental Services',
			'intro' => 'A/V, LED, lighting, sound and staging equipment, delivered, installed and operated.',
			'items' => array( 'A/V Rentals', 'LED Screens', 'Displays & Monitors', 'Projectors & Screens', 'Sound Systems', 'Lighting Equipment', 'Stage Equipment', 'Event Furniture Rentals' ),
		),
		'events-production'   => array(
			'title' => 'Events Production',
			'intro' => 'From setup and staging to activations, conferences and award nights.',
			'items' => array( 'Event Setup & Execution', 'Stage Production', 'Exhibition Stands', 'Brand Activations', 'Corporate Events', 'Conferences', 'Product Launches', 'Gala & Award Events', 'Backdrops & Set Design', 'LED & AV Solutions', 'Event Furniture', 'Signage & Branding', 'Temporary Structures' ),
		),
		'bespoke-printing'    => array(
			'title' => 'Bespoke Printing',
			'intro' => 'Corporate print, event graphics and large format, in any shape, size or finish.',
			'items' => array( 'Corporate Printing', 'Marketing Collateral', 'Brochures & Profiles', 'Stationeries', 'Large Format Printing', 'Event Graphics & Signage', 'Wall Graphics', 'Stickers & Labels', 'Packaging', 'Custom Printed Materials', 'Special Finishes', 'Custom Shapes & Sizes' ),
		),
		'bespoke-furniture'   => array(
			'title' => 'Bespoke Furniture',
			'intro' => 'Custom furniture and fixtures built in our own workshop for retail, offices, hospitality and events.',
			'items' => array( 'Custom Tables', 'Reception Counters', 'Cabinets', 'Shelving', 'Retail Fixtures', 'Exhibition Furniture', 'Office Furniture', 'Hospitality Furniture', 'Custom Joinery', 'Branded Furniture', 'Decorative Pieces', 'Custom Fabrication' ),
		),
		'deep-cleaning'       => array(
			'title' => 'Deep Cleaning',
			'intro' => 'Detailed, commercial and post-event cleaning that leaves every space ready to use.',
			'items' => array( 'Post-Event Cleaning', 'Detailed Cleaning', 'Commercial Cleaning', 'Office Cleaning', 'Floor & Surface Cleaning', 'Upholstery Cleaning', 'Carpet Cleaning', 'Glass & Window Cleaning', 'Kitchen Deep Cleaning', 'Event Cleaning' ),
		),
		'landscape-designing' => array(
			'title' => 'Landscape Designing',
			'intro' => 'Landscape plans, planting and outdoor spaces, designed, installed and maintained.',
			'items' => array( 'Landscape Plan & Design', 'Soft & Hard Scape', 'Planting', 'Garden Development', 'Outdoor Spaces', 'Planters & Greenery', 'Irrigation Solutions', 'Landscape Installation', 'Landscape Maintenance', 'Outdoor Feature Elements', 'Commercial Landscaping', 'Residential Landscaping' ),
		),
	);
}

/**
 * [ab_services_tabs active="events-production" button="Discuss Your Project" button_url="/contact/"]
 */
add_shortcode( 'ab_services_tabs', function ( $atts ) {
	$atts     = shortcode_atts( array( 'active' => 'events-production', 'button' => 'Discuss Your Project', 'button_url' => '/contact/' ), $atts, 'ab_services_tabs' );
	$services = abp_services();
	$active   = isset( $services[ $atts['active'] ] ) ? $atts['active'] : key( $services );
	$uid      = 'abp-svc-' . wp_unique_id();

	$out = '<div class="abp-svc" data-abp-tabs>';
	foreach ( $services as $slug => $s ) {
		$on    = $slug === $active;
		$items = $s['items'];
		$half  = (int) ceil( count( $items ) / 2 );
		$cols  = array( array_slice( $items, 0, $half ), array_slice( $items, $half ) );

		$out .= sprintf(
			'<button type="button" class="abp-svc__tab%s" id="%s-tab-%s" aria-controls="%s-panel-%s" aria-expanded="%s" data-slug="%s"><span>%s</span><i aria-hidden="true"></i></button>',
			$on ? ' is-active' : '',
			esc_attr( $uid ),
			esc_attr( $slug ),
			esc_attr( $uid ),
			esc_attr( $slug ),
			$on ? 'true' : 'false',
			esc_attr( $slug ),
			esc_html( $s['title'] )
		);
		$out .= sprintf(
			'<div class="abp-svc__panel%s" id="%s-panel-%s" role="region" aria-labelledby="%s-tab-%s"%s>',
			$on ? ' is-active' : '',
			esc_attr( $uid ),
			esc_attr( $slug ),
			esc_attr( $uid ),
			esc_attr( $slug ),
			$on ? '' : ' hidden'
		);
		$out .= '<h3 class="abp-svc__title">' . esc_html( $s['title'] ) . '</h3>';
		$out .= '<p class="abp-svc__intro">' . esc_html( $s['intro'] ) . '</p><div class="abp-svc__cols">';
		foreach ( $cols as $col ) {
			$out .= '<ul>';
			foreach ( $col as $item ) {
				$out .= '<li>' . esc_html( $item ) . '</li>';
			}
			$out .= '</ul>';
		}
		$out .= '</div>';
		if ( $atts['button'] ) {
			$out .= '<a class="abp-btn abp-btn--grad" href="' . esc_url( abp_url( $atts['button_url'] ) ) . '">' . esc_html( $atts['button'] ) . ' <span aria-hidden="true">→</span></a>';
		}
		$out .= '</div>';
	}
	return $out . '</div>';
} );

/* -------------------------------------------------------------------------
 * Clients
 * ---------------------------------------------------------------------- */

function abp_clients() {
	// file => name. Entries without a file show the name as text.
	return array(
		'emirates'           => 'Emirates',
		'dubai-airports'     => 'Dubai Airports',
		'emaar'              => 'Emaar',
		'majid-al-futtaim'   => 'Majid Al Futtaim',
		'formula-1'          => 'Formula 1',
		'marriott'           => 'Marriott',
		'siemens'            => 'Siemens',
		'dubai-tourism'      => 'Dubai Tourism',
		'abu-dhabi-culture'  => 'Abu Dhabi Culture & Tourism',
		'wasl-properties'    => 'Wasl Properties',
		'sinopec'            => 'Sinopec',
		'weatherford'        => 'Weatherford',
		'sunset-hospitality' => 'Sunset Hospitality',
		'five-luxe'          => 'Five Luxe',
		'mmi'                => 'MMI',
		'alphamind'          => 'AlphaMind',
		'7-management'       => '7 Management',
	);
}

function abp_client_logo( $slug, $name ) {
	$file = '/assets/img/clients/client-' . $slug . '.png';
	if ( file_exists( ABP_DIR . $file ) ) {
		return '<img src="' . esc_url( ABP_URI . $file ) . '" alt="' . esc_attr( $name ) . '" loading="lazy">';
	}
	return '<span class="abp-client__name">' . esc_html( $name ) . '</span>';
}

/** [ab_clients_marquee title="Trusted by those who expect more"] */
add_shortcode( 'ab_clients_marquee', function ( $atts ) {
	$atts  = shortcode_atts( array( 'title' => 'Trusted by those who expect more' ), $atts, 'ab_clients_marquee' );
	$items = '';
	foreach ( abp_clients() as $slug => $name ) {
		$items .= '<li class="abp-marquee__item">' . abp_client_logo( $slug, $name ) . '</li><li class="abp-marquee__star" aria-hidden="true">✦</li>';
	}
	$out  = '<div class="abp-marquee">';
	if ( $atts['title'] ) {
		$out .= '<p class="abp-marquee__title">' . esc_html( $atts['title'] ) . '</p>';
	}
	$out .= '<div class="abp-marquee__viewport"><ul class="abp-marquee__track">' . $items . '</ul><ul class="abp-marquee__track" aria-hidden="true">' . $items . '</ul></div></div>';
	return $out;
} );

/** [ab_clients_grid more="& Many More"] */
add_shortcode( 'ab_clients_grid', function ( $atts ) {
	$atts = shortcode_atts( array( 'more' => '& Many More' ), $atts, 'ab_clients_grid' );
	$out  = '<ul class="abp-clients">';
	foreach ( abp_clients() as $slug => $name ) {
		$out .= '<li class="abp-client">' . abp_client_logo( $slug, $name ) . '</li>';
	}
	if ( $atts['more'] ) {
		$out .= '<li class="abp-client abp-client--more"><span class="abp-client__name">' . esc_html( $atts['more'] ) . '</span></li>';
	}
	return $out . '</ul>';
} );

/* -------------------------------------------------------------------------
 * Find us (contact)
 * ---------------------------------------------------------------------- */

add_shortcode( 'ab_find_us', function () {
	$locations = array(
		'hq'     => array( 'Dubai HQ', 'Headquarters', abp_opt( 'hq_title' ), abp_opt( 'hq_address' ), abp_opt( 'hq_map' ) ),
		'branch' => array( 'Sharjah Branch', 'Branch & Workshop', abp_opt( 'branch_title' ), abp_opt( 'branch_address' ), abp_opt( 'branch_map' ) ),
	);
	$first = $locations['hq'];

	$out  = '<div class="abp-findus" data-abp-findus>';
	$out .= '<iframe class="abp-findus__map" title="' . esc_attr__( 'Map', 'abp' ) . '" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="' . esc_url( 'https://www.google.com/maps?q=' . rawurlencode( $first[4] ) . '&output=embed' ) . '"></iframe>';
	$out .= '<div class="abp-findus__card"><div class="abp-findus__toggle" role="tablist">';
	$i    = 0;
	foreach ( $locations as $key => $l ) {
		$out .= sprintf(
			'<button type="button" role="tab" class="%s" aria-selected="%s" data-eyebrow="%s" data-title="%s" data-address="%s" data-map="%s">%s</button>',
			0 === $i ? 'is-active' : '',
			0 === $i ? 'true' : 'false',
			esc_attr( $l[1] ),
			esc_attr( $l[2] ),
			esc_attr( $l[3] ),
			esc_attr( $l[4] ),
			esc_html( $l[0] )
		);
		$i++;
	}
	$out .= '</div>';
	$out .= '<p class="abp-findus__eyebrow">' . esc_html( $first[1] ) . '</p>';
	$out .= '<h3 class="abp-findus__title">' . esc_html( $first[2] ) . '</h3>';
	$out .= '<p class="abp-findus__address">' . abp_lines( $first[3] ) . '</p>';
	$out .= '<a class="abp-findus__btn" target="_blank" rel="noopener" href="' . esc_url( abp_maps_link( $first[4] ) ) . '">' . esc_html__( 'Open in Google Maps', 'abp' ) . ' <span aria-hidden="true">→</span></a>';
	$out .= '</div></div>';
	return $out;
} );

/** [ab_contact_card] — "Reach us directly" dark card (home + contact). */
add_shortcode( 'ab_contact_card', function () {
	$p1 = abp_opt( 'phone_primary' );
	$p2 = abp_opt( 'phone_secondary' );
	$em = abp_opt( 'email' );

	$rows = array(
		array( 'phone', __( 'Call us', 'abp' ), '<a href="' . esc_attr( abp_tel( $p1 ) ) . '">' . esc_html( $p1 ) . '</a>  ·  <a href="' . esc_attr( abp_tel( $p2 ) ) . '">' . esc_html( $p2 ) . '</a>' ),
		array( 'mail', __( 'Email', 'abp' ), '<a href="mailto:' . esc_attr( $em ) . '">' . esc_html( $em ) . '</a>' ),
		array( 'pin', __( 'Headquarters', 'abp' ), esc_html( abp_opt( 'hq_title' ) ) . ', ' . abp_lines( abp_opt( 'hq_address' ) ) ),
		array( 'home', __( 'Branch office · Workshop', 'abp' ), abp_lines( abp_opt( 'branch_address' ) ) ),
	);

	$out = '<div class="abp-reach"><p class="abp-reach__head"><i></i>' . esc_html__( 'Reach us directly', 'abp' ) . '</p>';
	foreach ( $rows as $r ) {
		$out .= '<div class="abp-reach__row"><span class="abp-reach__icon">' . abp_icon( $r[0] ) . '</span><div><p class="abp-reach__label">' . esc_html( $r[1] ) . '</p><p class="abp-reach__value">' . $r[2] . '</p></div></div>';
	}
	$out .= '<div class="abp-reach__actions"><a class="abp-btn abp-btn--grad abp-btn--sm" href="' . esc_attr( abp_tel( $p1 ) ) . '">' . esc_html__( 'Call now', 'abp' ) . ' <span aria-hidden="true">→</span></a>';
	$out .= '<a class="abp-btn abp-btn--ghost abp-btn--sm" target="_blank" rel="noopener" href="' . esc_url( abp_maps_link( abp_opt( 'hq_map' ) ) ) . '">' . esc_html__( 'Get directions', 'abp' ) . ' <span aria-hidden="true">→</span></a></div>';
	return $out . '</div>';
} );

/** [ab_anchor id="industries"] — an in-page link target (Elementor V4 has no CSS ID field). */
add_shortcode( 'ab_anchor', function ( $atts ) {
	$atts = shortcode_atts( array( 'id' => '' ), $atts, 'ab_anchor' );
	return $atts['id'] ? '<span class="abp-anchor" id="' . esc_attr( sanitize_title( $atts['id'] ) ) . '"></span>' : '';
} );
