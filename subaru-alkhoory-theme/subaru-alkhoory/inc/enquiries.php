<?php
/**
 * Enquiries custom post type: storage, admin list columns, type filter, detail meta box, CSV export.
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the private enquiry post type.
 */
function subaru_register_enquiry_cpt() {
	register_post_type(
		'subaru_enquiry',
		array(
			'labels'              => array(
				'name'          => __( 'Enquiries', 'subaru-alkhoory' ),
				'singular_name' => __( 'Enquiry', 'subaru-alkhoory' ),
				'menu_name'     => __( 'Enquiries', 'subaru-alkhoory' ),
				'all_items'     => __( 'All enquiries', 'subaru-alkhoory' ),
				'edit_item'     => __( 'Enquiry details', 'subaru-alkhoory' ),
				'search_items'  => __( 'Search enquiries', 'subaru-alkhoory' ),
				'not_found'     => __( 'No enquiries yet.', 'subaru-alkhoory' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'menu_icon'           => 'dashicons-email-alt',
			'menu_position'       => 58,
			'supports'            => array( 'title' ),
			'capability_type'     => 'post',
			'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'        => true,
		)
	);
}
add_action( 'init', 'subaru_register_enquiry_cpt' );

/**
 * List-table columns.
 *
 * @param array $cols Columns.
 * @return array
 */
function subaru_enquiry_columns( $cols ) {
	return array(
		'cb'        => $cols['cb'],
		'title'     => __( 'Enquiry', 'subaru-alkhoory' ),
		'sa_type'   => __( 'Type', 'subaru-alkhoory' ),
		'sa_model'  => __( 'Model', 'subaru-alkhoory' ),
		'sa_name'   => __( 'Name', 'subaru-alkhoory' ),
		'sa_email'  => __( 'Email', 'subaru-alkhoory' ),
		'sa_phone'  => __( 'Phone', 'subaru-alkhoory' ),
		'date'      => __( 'Date', 'subaru-alkhoory' ),
	);
}
add_filter( 'manage_subaru_enquiry_posts_columns', 'subaru_enquiry_columns' );

/**
 * Column content.
 *
 * @param string $col     Column.
 * @param int    $post_id Post.
 */
function subaru_enquiry_column_content( $col, $post_id ) {
	$map = array(
		'sa_type'  => '_sa_type',
		'sa_model' => '_sa_model',
		'sa_name'  => '_sa_name',
		'sa_email' => '_sa_email',
		'sa_phone' => '_sa_phone',
	);
	if ( ! isset( $map[ $col ] ) ) {
		return;
	}
	$val = (string) get_post_meta( $post_id, $map[ $col ], true );
	if ( 'sa_type' === $col ) {
		$types = subaru_form_types();
		$label = isset( $types[ $val ] ) ? $types[ $val ] : $val;
		$url   = add_query_arg(
			array(
				'post_type' => 'subaru_enquiry',
				'sa_type'   => $val,
			),
			admin_url( 'edit.php' )
		);
		echo '<a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a>';
	} elseif ( 'sa_email' === $col && $val ) {
		echo '<a href="' . esc_url( 'mailto:' . $val ) . '">' . esc_html( $val ) . '</a>';
	} elseif ( 'sa_phone' === $col && $val ) {
		echo '<a href="' . esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $val ) ) . '">' . esc_html( $val ) . '</a>';
	} else {
		echo esc_html( $val ? $val : '—' );
	}
}
add_action( 'manage_subaru_enquiry_posts_custom_column', 'subaru_enquiry_column_content', 10, 2 );

/**
 * Type filter dropdown + CSV export button above the list.
 *
 * @param string $post_type Post type.
 */
function subaru_enquiry_filters( $post_type ) {
	if ( 'subaru_enquiry' !== $post_type ) {
		return;
	}
	$current = isset( $_GET['sa_type'] ) ? sanitize_key( wp_unslash( $_GET['sa_type'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	echo '<label class="screen-reader-text" for="sa-type-filter">' . esc_html__( 'Filter by type', 'subaru-alkhoory' ) . '</label>';
	echo '<select name="sa_type" id="sa-type-filter"><option value="">' . esc_html__( 'All types', 'subaru-alkhoory' ) . '</option>';
	foreach ( subaru_form_types() as $key => $label ) {
		echo '<option value="' . esc_attr( $key ) . '" ' . selected( $current, $key, false ) . '>' . esc_html( $label ) . '</option>';
	}
	echo '</select>';
}
add_action( 'restrict_manage_posts', 'subaru_enquiry_filters' );

/**
 * Export button next to the bulk actions.
 *
 * @param string $which top|bottom.
 */
function subaru_enquiry_export_button( $which ) {
	$screen = get_current_screen();
	if ( 'top' !== $which || ! $screen || 'edit-subaru_enquiry' !== $screen->id ) {
		return;
	}
	$type = isset( $_GET['sa_type'] ) ? sanitize_key( wp_unslash( $_GET['sa_type'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$url  = wp_nonce_url(
		add_query_arg(
			array(
				'action'  => 'subaru_export_enquiries',
				'sa_type' => $type,
			),
			admin_url( 'admin-post.php' )
		),
		'subaru_export'
	);
	echo '<div class="alignleft actions"><a class="button" href="' . esc_url( $url ) . '">' . esc_html__( 'Export CSV', 'subaru-alkhoory' ) . '</a></div>';
}
add_action( 'manage_posts_extra_tablenav', 'subaru_enquiry_export_button' );

/**
 * Apply the type filter.
 *
 * @param WP_Query $query Query.
 */
function subaru_enquiry_filter_query( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() || 'subaru_enquiry' !== $query->get( 'post_type' ) ) {
		return;
	}
	$type = isset( $_GET['sa_type'] ) ? sanitize_key( wp_unslash( $_GET['sa_type'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( $type ) {
		$query->set(
			'meta_query',
			array(
				array(
					'key'   => '_sa_type',
					'value' => $type,
				),
			)
		);
	}
}
add_action( 'pre_get_posts', 'subaru_enquiry_filter_query' );

/**
 * CSV export.
 */
function subaru_export_enquiries() {
	if ( ! current_user_can( 'edit_others_posts' ) ) {
		wp_die( esc_html__( 'You are not allowed to export enquiries.', 'subaru-alkhoory' ), 403 );
	}
	check_admin_referer( 'subaru_export' );
	$type = isset( $_GET['sa_type'] ) ? sanitize_key( wp_unslash( $_GET['sa_type'] ) ) : '';

	$args = array(
		'post_type'      => 'subaru_enquiry',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'no_found_rows'  => true,
	);
	if ( $type ) {
		$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			array(
				'key'   => '_sa_type',
				'value' => $type,
			),
		);
	}
	$posts = get_posts( $args );

	// Collect all field keys for consistent columns.
	$keys = array();
	foreach ( $posts as $p ) {
		$fields = (array) get_post_meta( $p->ID, '_sa_fields', true );
		$keys   = array_unique( array_merge( $keys, array_keys( $fields ) ) );
	}

	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=subaru-enquiries-' . ( $type ? $type . '-' : '' ) . gmdate( 'Y-m-d' ) . '.csv' );
	$out = fopen( 'php://output', 'w' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
	fwrite( $out, "\xEF\xBB\xBF" ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- UTF-8 BOM for Excel.
	fputcsv( $out, array_merge( array( 'ID', 'Date', 'Type', 'Model', 'Name', 'Email', 'Phone', 'Page' ), $keys ) );
	foreach ( $posts as $p ) {
		$fields = (array) get_post_meta( $p->ID, '_sa_fields', true );
		$row    = array(
			$p->ID,
			get_the_date( 'Y-m-d H:i', $p ),
			get_post_meta( $p->ID, '_sa_type', true ),
			get_post_meta( $p->ID, '_sa_model', true ),
			get_post_meta( $p->ID, '_sa_name', true ),
			get_post_meta( $p->ID, '_sa_email', true ),
			get_post_meta( $p->ID, '_sa_phone', true ),
			get_post_meta( $p->ID, '_sa_page', true ),
		);
		foreach ( $keys as $k ) {
			$row[] = isset( $fields[ $k ] ) ? $fields[ $k ] : '';
		}
		// Neutralise spreadsheet formula injection.
		$row = array_map(
			function ( $v ) {
				$v = (string) $v;
				return ( '' !== $v && in_array( $v[0], array( '=', '+', '-', '@' ), true ) ) ? "'" . $v : $v;
			},
			$row
		);
		fputcsv( $out, $row );
	}
	fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
	exit;
}
add_action( 'admin_post_subaru_export_enquiries', 'subaru_export_enquiries' );

/**
 * Read-only details meta box.
 */
function subaru_enquiry_meta_boxes() {
	add_meta_box( 'subaru-enquiry-details', __( 'Enquiry details', 'subaru-alkhoory' ), 'subaru_enquiry_details_box', 'subaru_enquiry', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'subaru_enquiry_meta_boxes' );

/**
 * Meta box content.
 *
 * @param WP_Post $post Post.
 */
function subaru_enquiry_details_box( $post ) {
	$fields = (array) get_post_meta( $post->ID, '_sa_fields', true );
	$types  = subaru_form_types();
	$type   = get_post_meta( $post->ID, '_sa_type', true );
	echo '<table class="widefat striped"><tbody>';
	echo '<tr><th style="width:200px">' . esc_html__( 'Type', 'subaru-alkhoory' ) . '</th><td>' . esc_html( isset( $types[ $type ] ) ? $types[ $type ] : $type ) . '</td></tr>';
	foreach ( $fields as $label => $value ) {
		echo '<tr><th>' . esc_html( $label ) . '</th><td>' . nl2br( esc_html( $value ) ) . '</td></tr>';
	}
	$page = get_post_meta( $post->ID, '_sa_page', true );
	if ( $page ) {
		echo '<tr><th>' . esc_html__( 'Submitted from', 'subaru-alkhoory' ) . '</th><td><a href="' . esc_url( $page ) . '" target="_blank" rel="noopener">' . esc_html( $page ) . '</a></td></tr>';
	}
	echo '<tr><th>' . esc_html__( 'Received', 'subaru-alkhoory' ) . '</th><td>' . esc_html( get_the_date( 'j F Y, g:i a', $post ) ) . '</td></tr>';
	echo '</tbody></table>';
}

/**
 * Count of new (unread = pending status) enquiries in the menu bubble.
 */
function subaru_enquiry_menu_bubble() {
	global $menu;
	$count = (int) wp_count_posts( 'subaru_enquiry' )->pending;
	if ( ! $count || ! is_array( $menu ) ) {
		return;
	}
	foreach ( $menu as $i => $item ) {
		if ( isset( $item[2] ) && 'edit.php?post_type=subaru_enquiry' === $item[2] ) {
			$menu[ $i ][0] .= ' <span class="awaiting-mod"><span class="pending-count">' . $count . '</span></span>'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		}
	}
}
add_action( 'admin_menu', 'subaru_enquiry_menu_bubble', 99 );

/**
 * Mark an enquiry as read (private) when an editor opens it.
 */
function subaru_enquiry_mark_read() {
	$screen = get_current_screen();
	if ( ! $screen || 'subaru_enquiry' !== $screen->post_type || 'post' !== $screen->base ) {
		return;
	}
	$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( $post_id && 'pending' === get_post_status( $post_id ) && current_user_can( 'edit_post', $post_id ) ) {
		wp_update_post(
			array(
				'ID'          => $post_id,
				'post_status' => 'private',
			)
		);
	}
}
add_action( 'current_screen', 'subaru_enquiry_mark_read' );
