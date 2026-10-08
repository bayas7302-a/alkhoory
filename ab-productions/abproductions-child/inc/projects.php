<?php
/**
 * Projects: post type, categories, admin fields and front-end output.
 *
 * Backend: Projects → Add New. Give the project a title, a featured image and
 * one or more Project Categories. The cards on the site show the centre third
 * of the image (left and right thirds are hidden); clicking a card opens the
 * full image in a pop-up.
 *
 * Front end: [ab_projects] (grid with category tabs + load more) and
 * [ab_projects layout="featured"] (the home page mosaic).
 *
 * @package abp
 */

defined( 'ABSPATH' ) || exit;

const ABP_PROJECT = 'ab_project';
const ABP_PROJECT_CAT = 'ab_project_category';

add_action( 'init', function () {
	register_post_type(
		ABP_PROJECT,
		array(
			'labels'              => array(
				'name'               => __( 'Projects', 'abp' ),
				'singular_name'      => __( 'Project', 'abp' ),
				'add_new_item'       => __( 'Add New Project', 'abp' ),
				'edit_item'          => __( 'Edit Project', 'abp' ),
				'new_item'           => __( 'New Project', 'abp' ),
				'view_item'          => __( 'View Project', 'abp' ),
				'search_items'       => __( 'Search Projects', 'abp' ),
				'not_found'          => __( 'No projects found', 'abp' ),
				'all_items'          => __( 'All Projects', 'abp' ),
				'featured_image'     => __( 'Project image', 'abp' ),
				'set_featured_image' => __( 'Set project image', 'abp' ),
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => true,
			'menu_position'       => 21,
			'menu_icon'           => 'dashicons-format-gallery',
			'supports'            => array( 'title', 'thumbnail', 'page-attributes', 'custom-fields' ),
			'has_archive'         => false,
			'rewrite'             => false,
		)
	);

	register_taxonomy(
		ABP_PROJECT_CAT,
		ABP_PROJECT,
		array(
			'labels'            => array(
				'name'          => __( 'Project Categories', 'abp' ),
				'singular_name' => __( 'Project Category', 'abp' ),
				'menu_name'     => __( 'Categories', 'abp' ),
				'add_new_item'  => __( 'Add New Category', 'abp' ),
			),
			'public'            => false,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'hierarchical'      => true,
			'rewrite'           => false,
		)
	);

	foreach ( array( 'abp_subtitle', 'abp_tag' ) as $key ) {
		register_post_meta(
			ABP_PROJECT,
			$key,
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
	register_post_meta(
		ABP_PROJECT,
		'abp_featured',
		array(
			'type'          => 'boolean',
			'single'        => true,
			'show_in_rest'  => true,
			'auth_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
} );

/* -------------------------------------------------------------------------
 * Admin: details meta box + list columns
 * ---------------------------------------------------------------------- */

add_action( 'add_meta_boxes', function () {
	add_meta_box( 'abp_project_details', __( 'Project details', 'abp' ), 'abp_project_details_box', ABP_PROJECT, 'normal', 'high' );
} );

function abp_project_details_box( $post ) {
	wp_nonce_field( 'abp_project_details', 'abp_project_nonce' );
	$subtitle = get_post_meta( $post->ID, 'abp_subtitle', true );
	$tag      = get_post_meta( $post->ID, 'abp_tag', true );
	$featured = (bool) get_post_meta( $post->ID, 'abp_featured', true );
	?>
	<p>
		<label for="abp_subtitle"><strong><?php esc_html_e( 'Subtitle', 'abp' ); ?></strong></label><br>
		<input type="text" class="widefat" id="abp_subtitle" name="abp_subtitle" value="<?php echo esc_attr( $subtitle ); ?>" placeholder="Seasonal Décor  |  Abu Dhabi">
		<span class="description"><?php esc_html_e( 'Shown under the project name on the Projects page.', 'abp' ); ?></span>
	</p>
	<p>
		<label for="abp_tag"><strong><?php esc_html_e( 'Home page tag', 'abp' ); ?></strong></label><br>
		<input type="text" class="widefat" id="abp_tag" name="abp_tag" value="<?php echo esc_attr( $tag ); ?>" placeholder="Events · LED &amp; AV">
		<span class="description"><?php esc_html_e( 'Small label on the home page card. Leave empty to use the category names.', 'abp' ); ?></span>
	</p>
	<p>
		<label><input type="checkbox" name="abp_featured" value="1" <?php checked( $featured ); ?>> <?php esc_html_e( 'Show on the home page ("Our Projects" mosaic)', 'abp' ); ?></label>
	</p>
	<p class="description">
		<?php esc_html_e( 'Image: use the "Project image" box on the right. Upload a wide rectangle (for example 2700 × 1140 px). Cards show only the centre third; the left and right thirds are hidden and appear when the image opens in the pop-up. Order: use "Order" under Page Attributes (lower numbers first).', 'abp' ); ?>
	</p>
	<?php
}

add_action( 'save_post_' . ABP_PROJECT, function ( $post_id ) {
	if ( ! isset( $_POST['abp_project_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['abp_project_nonce'] ), 'abp_project_details' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	update_post_meta( $post_id, 'abp_subtitle', isset( $_POST['abp_subtitle'] ) ? sanitize_text_field( wp_unslash( $_POST['abp_subtitle'] ) ) : '' );
	update_post_meta( $post_id, 'abp_tag', isset( $_POST['abp_tag'] ) ? sanitize_text_field( wp_unslash( $_POST['abp_tag'] ) ) : '' );
	update_post_meta( $post_id, 'abp_featured', empty( $_POST['abp_featured'] ) ? 0 : 1 );
} );

add_filter( 'manage_' . ABP_PROJECT . '_posts_columns', function ( $cols ) {
	$new = array();
	foreach ( $cols as $key => $label ) {
		if ( 'title' === $key ) {
			$new['abp_thumb'] = __( 'Image', 'abp' );
		}
		$new[ $key ] = $label;
	}
	$new['abp_featured'] = __( 'Home', 'abp' );
	$new['menu_order']   = __( 'Order', 'abp' );
	return $new;
} );

add_action( 'manage_' . ABP_PROJECT . '_posts_custom_column', function ( $col, $post_id ) {
	if ( 'abp_thumb' === $col ) {
		echo get_the_post_thumbnail( $post_id, array( 80, 60 ), array( 'style' => 'width:80px;height:60px;object-fit:cover;border-radius:6px' ) );
	} elseif ( 'abp_featured' === $col ) {
		echo get_post_meta( $post_id, 'abp_featured', true ) ? '★' : '—';
	} elseif ( 'menu_order' === $col ) {
		echo (int) get_post_field( 'menu_order', $post_id );
	}
}, 10, 2 );

add_action( 'pre_get_posts', function ( WP_Query $q ) {
	if ( is_admin() && $q->is_main_query() && ABP_PROJECT === $q->get( 'post_type' ) && ! $q->get( 'orderby' ) ) {
		$q->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
	}
} );

/* -------------------------------------------------------------------------
 * Default categories and sample projects (first activation only)
 * ---------------------------------------------------------------------- */

function abp_default_project_categories() {
	return array(
		'events'            => 'Events',
		'exhibitions'       => 'Exhibitions',
		'brand-activations' => 'Brand Activations',
		'hospitality'       => 'Hospitality',
		'interiors-fit-outs' => 'Interiors & Fit-Outs',
	);
}

function abp_sample_projects() {
	return array(
		array( 'Abu Dhabi Mall Christmas', 'events', 'Seasonal Décor  |  Abu Dhabi', 'Events · Seasonal Décor', 'project-abu-dhabi-mall-christmas.jpg', false ),
		array( 'Yas Island Fan Zone', 'events', 'World Cup Fan Zone  |  Abu Dhabi', 'Events · LED & AV', 'project-yas-island-fan-zone.jpg', true ),
		array( 'CÉ LA VI New Year’s', 'hospitality', 'Hospitality Event  |  Dubai', 'Hospitality · Production', 'project-ce-la-vi-new-years.jpg', true ),
		array( 'Moët & Chandon', 'brand-activations', '#ToastWithMoet Activation', 'Brand Activation', 'project-moet-chandon.jpg', true ),
		array( 'UAE National Day', 'events', 'Branding & Structures  |  Emaar', 'Branding & Structures', 'project-uae-national-day.jpg', true ),
		array( 'Montblanc Anniversary', 'brand-activations', 'Backdrop & Floral Set Design', 'Backdrop & Set Design', 'project-montblanc-anniversary.jpg', true ),
		array( 'Mirbad Jewellery Exhibition', 'exhibitions', 'Exhibition Stand', 'Exhibition Stand', 'project-mirbad-jewellery.jpg', true ),
		array( 'Sharjah Bridal Fair', 'exhibitions', 'Event Branding  |  Sharjah', 'Event Branding', 'project-sharjah-bridal-fair.jpg', false ),
	);
}

function abp_seed_projects() {
	foreach ( abp_default_project_categories() as $slug => $name ) {
		if ( ! term_exists( $slug, ABP_PROJECT_CAT ) ) {
			wp_insert_term( $name, ABP_PROJECT_CAT, array( 'slug' => $slug ) );
		}
	}

	if ( get_option( 'abp_projects_seeded' ) ) {
		return;
	}
	$existing = get_posts( array( 'post_type' => ABP_PROJECT, 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids' ) );
	if ( $existing ) {
		update_option( 'abp_projects_seeded', 1 );
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	// Order on the home mosaic: Yas Island (large), CÉ LA VI, Mirbad, Moët, UAE National Day, Montblanc.
	$home_order = array( 'Yas Island Fan Zone' => 1, 'CÉ LA VI New Year’s' => 2, 'Mirbad Jewellery Exhibition' => 3, 'Moët & Chandon' => 4, 'UAE National Day' => 5, 'Montblanc Anniversary' => 6 );

	foreach ( abp_sample_projects() as $i => $p ) {
		list( $title, $cat, $subtitle, $tag, $file, $featured ) = $p;
		$post_id = wp_insert_post(
			array(
				'post_type'   => ABP_PROJECT,
				'post_status' => 'publish',
				'post_title'  => $title,
				'menu_order'  => isset( $home_order[ $title ] ) ? $home_order[ $title ] : 10 + $i,
			)
		);
		if ( ! $post_id || is_wp_error( $post_id ) ) {
			continue;
		}
		wp_set_object_terms( $post_id, $cat, ABP_PROJECT_CAT );
		update_post_meta( $post_id, 'abp_subtitle', $subtitle );
		update_post_meta( $post_id, 'abp_tag', $tag );
		update_post_meta( $post_id, 'abp_featured', $featured ? 1 : 0 );

		$src = ABP_DIR . '/assets/img/projects/' . $file;
		if ( file_exists( $src ) ) {
			$tmp = wp_tempnam( $file );
			copy( $src, $tmp );
			$att = media_handle_sideload( array( 'name' => $file, 'tmp_name' => $tmp ), $post_id, $title );
			if ( ! is_wp_error( $att ) ) {
				set_post_thumbnail( $post_id, $att );
			}
		}
	}
	update_option( 'abp_projects_seeded', 1 );
}

add_action( 'after_switch_theme', function () {
	abp_seed_projects();
} );
// Also covers installs where the theme was already active when this file shipped.
add_action( 'admin_init', function () {
	if ( ! get_option( 'abp_projects_seeded' ) && current_user_can( 'manage_options' ) ) {
		abp_seed_projects();
	}
} );

/* -------------------------------------------------------------------------
 * Front end
 * ---------------------------------------------------------------------- */

/**
 * Query projects.
 */
function abp_get_projects( $args = array() ) {
	$query = array(
		'post_type'      => ABP_PROJECT,
		'post_status'    => 'publish',
		'posts_per_page' => isset( $args['limit'] ) ? (int) $args['limit'] : -1,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		'no_found_rows'  => true,
	);
	if ( ! empty( $args['featured'] ) ) {
		$query['meta_query'] = array( array( 'key' => 'abp_featured', 'value' => '1' ) );
	}
	if ( ! empty( $args['category'] ) ) {
		$query['tax_query'] = array(
			array(
				'taxonomy' => ABP_PROJECT_CAT,
				'field'    => 'slug',
				'terms'    => array_map( 'trim', explode( ',', $args['category'] ) ),
			),
		);
	}
	return get_posts( $query );
}

/**
 * Categories that have at least one published project, in admin order.
 */
function abp_project_terms( $projects ) {
	$used = array();
	foreach ( $projects as $p ) {
		foreach ( (array) get_the_terms( $p, ABP_PROJECT_CAT ) as $t ) {
			if ( $t instanceof WP_Term ) {
				$used[ $t->term_id ] = $t;
			}
		}
	}
	$out = array_values( $used );
	// Default categories first, in the designed order; others alphabetically.
	$order = array_keys( abp_default_project_categories() );
	usort( $out, function ( $a, $b ) use ( $order ) {
		$ia = array_search( $a->slug, $order, true );
		$ib = array_search( $b->slug, $order, true );
		$ia = false === $ia ? 99 : $ia;
		$ib = false === $ib ? 99 : $ib;
		return $ia === $ib ? strcmp( $a->name, $b->name ) : $ia - $ib;
	} );
	return $out;
}

function abp_project_data( WP_Post $p ) {
	$terms = get_the_terms( $p, ABP_PROJECT_CAT );
	$terms = is_array( $terms ) ? $terms : array();
	$thumb = get_post_thumbnail_id( $p );
	$full  = $thumb ? wp_get_attachment_image_src( $thumb, 'full' ) : false;
	$tag   = get_post_meta( $p->ID, 'abp_tag', true );
	return array(
		'id'       => $p->ID,
		'title'    => get_the_title( $p ),
		'subtitle' => get_post_meta( $p->ID, 'abp_subtitle', true ),
		'tag'      => $tag ? $tag : implode( ' · ', wp_list_pluck( $terms, 'name' ) ),
		'cat'      => $terms ? $terms[0]->name : '',
		'slugs'    => implode( ' ', wp_list_pluck( $terms, 'slug' ) ),
		'thumb'    => $thumb,
		'full'     => $full ? $full[0] : '',
	);
}

/**
 * Filter buttons. $style = tabs (Projects page) | pills (home, dark).
 */
function abp_project_filters_html( $terms, $target, $style ) {
	$html = '<div class="abp-filters abp-filters--' . esc_attr( $style ) . '" role="tablist" data-abp-filter-for="' . esc_attr( $target ) . '">';
	$html  .= '<button type="button" class="abp-filter is-active" role="tab" aria-selected="true" data-filter="*">' . esc_html__( 'All', 'abp' ) . '</button>';
	foreach ( $terms as $t ) {
		$name  = 'pills' === $style && 'brand-activations' === $t->slug ? 'Activations' : $t->name;
		$html .= '<button type="button" class="abp-filter" role="tab" aria-selected="false" data-filter="' . esc_attr( $t->slug ) . '">' . esc_html( $name ) . '</button>';
	}
	return $html . '</div>';
}

/**
 * [ab_projects layout="grid|featured" limit="" per_page="8" filter="yes|no"
 *              filter_style="tabs|pills" category="" featured="" id=""]
 */
add_shortcode( 'ab_projects', function ( $atts ) {
	$atts = shortcode_atts(
		array(
			'layout'       => 'grid',
			'limit'        => '',
			'per_page'     => '8',
			'filter'       => 'yes',
			'filter_style' => '',
			'category'     => '',
			'featured'     => '',
			'id'           => '',
			'button'       => '',
			'button_url'   => '/projects/',
		),
		$atts,
		'ab_projects'
	);

	$featured_layout = 'featured' === $atts['layout'];
	$id              = $atts['id'] ? sanitize_html_class( $atts['id'] ) : 'abp-projects-' . wp_unique_id();
	$projects        = abp_get_projects(
		array(
			'limit'    => '' !== $atts['limit'] ? (int) $atts['limit'] : ( $featured_layout ? 6 : -1 ),
			'featured' => '' !== $atts['featured'] ? 'yes' === $atts['featured'] : $featured_layout,
			'category' => $atts['category'],
		)
	);
	// Home mosaic falls back to the latest projects if none are marked "featured".
	if ( $featured_layout && ! $projects ) {
		$projects = abp_get_projects( array( 'limit' => 6 ) );
	}
	if ( ! $projects ) {
		return '<p class="abp-empty">' . esc_html__( 'Projects coming soon.', 'abp' ) . '</p>';
	}

	abp_need_lightbox();
	$out = '';

	if ( 'yes' === $atts['filter'] && ! $featured_layout ) {
		$style = $atts['filter_style'] ? $atts['filter_style'] : 'tabs';
		$out  .= abp_project_filters_html( abp_project_terms( $projects ), $id, $style );
	}

	$per_page = $featured_layout ? 0 : max( 0, (int) $atts['per_page'] );
	$out     .= sprintf(
		'<div id="%s" class="abp-projects abp-projects--%s" data-per-page="%d">',
		esc_attr( $id ),
		$featured_layout ? 'featured' : 'grid',
		$per_page
	);

	foreach ( $projects as $i => $p ) {
		$d   = abp_project_data( $p );
		$img = $d['thumb'] ? wp_get_attachment_image(
			$d['thumb'],
			$featured_layout && 0 === $i ? 'abp-wide' : 'abp-card',
			false,
			array(
				'class'   => 'abp-card__img',
				'loading' => 'lazy',
				'sizes'   => $featured_layout ? '(max-width: 767px) 100vw, 760px' : '(max-width: 767px) 100vw, 610px',
			)
		) : '<span class="abp-card__img abp-card__img--empty"></span>';

		$hidden = $per_page && $i >= $per_page ? ' is-paged-out' : '';
		$attrs  = sprintf(
			'class="abp-project%s" data-cats="%s" data-full="%s" data-title="%s" role="button" tabindex="0" aria-label="%s"',
			esc_attr( $hidden ),
			esc_attr( $d['slugs'] ),
			esc_url( $d['full'] ),
			esc_attr( $d['title'] ),
			/* translators: %s: project name */
			esc_attr( sprintf( __( 'Open image: %s', 'abp' ), $d['title'] ) )
		);

		if ( $featured_layout ) {
			$out .= '<article ' . $attrs . '><div class="abp-project__media">' . $img . '<span class="abp-project__shade"></span></div>';
			$out .= '<div class="abp-project__body">';
			if ( $d['tag'] ) {
				$out .= '<span class="abp-project__tag">' . esc_html( $d['tag'] ) . '</span>';
			}
			$out .= '<h3 class="abp-project__title">' . esc_html( $d['title'] ) . '</h3></div>';
			$out .= '<span class="abp-project__arrow">' . abp_icon( 'arrow-up-right' ) . '</span></article>';
		} else {
			$out .= '<article ' . $attrs . '><div class="abp-project__media">' . $img;
			if ( $d['cat'] ) {
				$out .= '<span class="abp-project__cat">' . esc_html( $d['cat'] ) . '</span>';
			}
			$out .= '<span class="abp-project__overlay"></span><span class="abp-project__view">' . esc_html__( 'View Project', 'abp' ) . ' <span aria-hidden="true">→</span></span></div>';
			$out .= '<h3 class="abp-project__title">' . esc_html( $d['title'] ) . '</h3>';
			if ( $d['subtitle'] ) {
				$out .= '<p class="abp-project__subtitle">' . esc_html( $d['subtitle'] ) . '</p>';
			}
			$out .= '</article>';
		}
	}
	$out .= '</div>';

	if ( $per_page && count( $projects ) > $per_page ) {
		$out .= '<div class="abp-loadmore-wrap"><button type="button" class="abp-loadmore" data-target="' . esc_attr( $id ) . '">' . esc_html__( 'Load More Projects', 'abp' ) . ' <span aria-hidden="true">↓</span></button></div>';
	}
	if ( $atts['button'] ) {
		$out .= '<div class="abp-viewall-wrap"><a class="abp-viewall" href="' . esc_url( abp_url( $atts['button_url'] ) ) . '">' . esc_html( $atts['button'] ) . ' <span aria-hidden="true">→</span></a></div>';
	}

	return '<div class="abp-projects-wrap">' . $out . '</div>';
} );

/**
 * [ab_project_filters target="home-projects" style="pills"] — filter buttons
 * placed separately from the grid (home page header row).
 */
add_shortcode( 'ab_project_filters', function ( $atts ) {
	$atts     = shortcode_atts( array( 'target' => '', 'style' => 'pills', 'featured' => 'yes' ), $atts, 'ab_project_filters' );
	$projects = abp_get_projects( array( 'limit' => 6, 'featured' => 'yes' === $atts['featured'] ) );
	if ( ! $projects ) {
		$projects = abp_get_projects( array( 'limit' => 6 ) );
	}
	return abp_project_filters_html( abp_project_terms( $projects ), sanitize_html_class( $atts['target'] ), $atts['style'] );
} );

/* Lightbox markup, printed once in the footer when a projects block is on the page. */
function abp_need_lightbox() {
	$GLOBALS['abp_lightbox'] = true;
}

add_action( 'wp_footer', function () {
	if ( empty( $GLOBALS['abp_lightbox'] ) ) {
		return;
	}
	?>
	<div class="abp-lightbox" id="abp-lightbox" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Project image', 'abp' ); ?>" hidden>
		<button type="button" class="abp-lightbox__close" data-abp-lb="close" aria-label="<?php esc_attr_e( 'Close', 'abp' ); ?>"><?php echo abp_icon( 'close' ); // phpcs:ignore ?></button>
		<button type="button" class="abp-lightbox__nav abp-lightbox__nav--prev" data-abp-lb="prev" aria-label="<?php esc_attr_e( 'Previous image', 'abp' ); ?>"><?php echo abp_icon( 'prev' ); // phpcs:ignore ?></button>
		<figure class="abp-lightbox__figure"><img class="abp-lightbox__img" src="data:image/gif;base64,R0lGODlhAQABAAAAACw=" alt=""></figure>
		<button type="button" class="abp-lightbox__nav abp-lightbox__nav--next" data-abp-lb="next" aria-label="<?php esc_attr_e( 'Next image', 'abp' ); ?>"><?php echo abp_icon( 'next' ); // phpcs:ignore ?></button>
	</div>
	<?php
}, 5 );
