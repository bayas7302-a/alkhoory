<?php
/**
 * Search form.
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$subaru_sid = wp_unique_id( 'sa-s-' );
?>
<form class="sa-search-inline" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $subaru_sid ); ?>"><?php esc_html_e( 'Search for:', 'subaru-alkhoory' ); ?></label>
	<?php subaru_the_icon( 'search', 16 ); ?>
	<input type="search" id="<?php echo esc_attr( $subaru_sid ); ?>" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search…', 'subaru-alkhoory' ); ?>">
</form>
