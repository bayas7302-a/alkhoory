<?php
/**
 * Site search: header search form, query scope and result snippets.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Search form (header dropdown and results page).
 */
function yutong_uae_search_form( $context = 'page' ) {
	$id = 'yt-search-' . sanitize_key( $context );
	?>
	<form class="yt-search-form yt-search-form--<?php echo esc_attr( $context ); ?>" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label class="screen-reader-text" for="<?php echo esc_attr( $id ); ?>"><?php esc_html_e( 'Search the site', 'yutong-uae' ); ?></label>
		<input id="<?php echo esc_attr( $id ); ?>" class="yt-search-form__input" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search models, news, awards…', 'yutong-uae' ); ?>" required>
		<button class="yt-search-form__submit" type="submit" aria-label="<?php esc_attr_e( 'Search', 'yutong-uae' ); ?>">
			<?php echo yutong_uae_icon( 'search', 18 ); ?>
		</button>
	</form>
	<?php
}

/**
 * Search only the site's published pages (the whole site is built from pages),
 * leaving out WordPress placeholder pages.
 */
add_action( 'pre_get_posts', function ( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
		return;
	}
	$query->set( 'post_type', array( 'page', 'post' ) );
	$query->set( 'post_status', 'publish' );
	$query->set( 'posts_per_page', 10 );
	$exclude = array();
	foreach ( array( 'sample-page' ) as $slug ) {
		$page = get_page_by_path( $slug );
		if ( $page ) {
			$exclude[] = $page->ID;
		}
	}
	if ( $exclude ) {
		$query->set( 'post__not_in', $exclude );
	}
} );

/**
 * Plain text of a page (Elementor stores the rendered page body in post_content).
 */
function yutong_uae_plain_text( $post ) {
	$text = strip_shortcodes( $post->post_content );
	$text = preg_replace( '#<(script|style|form)\b[^>]*>.*?</\1>#is', ' ', $text );
	$text = wp_strip_all_tags( preg_replace( '/<[^>]+>/', ' $0 ', $text ) ); // keep words in adjacent elements apart
	$text = html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );
	return trim( preg_replace( '/\s+/u', ' ', $text ) );
}

/**
 * Snippet around the first match of the search terms, with the terms highlighted.
 */
function yutong_uae_search_snippet( $post, $length = 220 ) {
	$text  = yutong_uae_plain_text( $post );
	$terms = array_filter( preg_split( '/\s+/u', (string) get_search_query( false ) ) );
	$start = 0;
	foreach ( $terms as $term ) {
		$pos = mb_stripos( $text, $term );
		if ( false !== $pos ) {
			$start = max( 0, $pos - 70 );
			break;
		}
	}
	$snippet = mb_substr( $text, $start, $length );
	if ( $start > 0 ) {
		$snippet = '… ' . preg_replace( '/^\S*\s/u', '', $snippet );
	}
	if ( mb_strlen( $text ) > $start + $length ) {
		$snippet = preg_replace( '/\s\S*$/u', '', $snippet ) . ' …';
	}
	$snippet = esc_html( $snippet );
	foreach ( $terms as $term ) {
		$snippet = preg_replace( '/(' . preg_quote( esc_html( $term ), '/' ) . ')/iu', '<mark>$1</mark>', $snippet );
	}
	return $snippet;
}

/**
 * Section label for a result: its parent page ("News", "Awards") or "Page".
 */
function yutong_uae_result_label( $post ) {
	if ( $post->post_parent ) {
		return get_the_title( $post->post_parent );
	}
	return 'page' === $post->post_type ? __( 'Page', 'yutong-uae' ) : __( 'Article', 'yutong-uae' );
}
