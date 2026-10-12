<?php
/**
 * Shared Newsroom listing: toolbar, optional featured card, grid, pagination.
 *
 * Args: featured (int post ID or 0), empty (string message).
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$subaru_featured = isset( $args['featured'] ) ? (int) $args['featured'] : 0;
$subaru_empty    = isset( $args['empty'] ) ? $args['empty'] : __( 'No articles found.', 'subaru-alkhoory' );
?>
<div class="sa-listing sa-wrap">
	<?php subaru_blog_toolbar(); ?>

	<?php
	if ( $subaru_featured ) {
		subaru_post_card( get_post( $subaru_featured ), 'featured' );
	}
	?>

	<?php if ( have_posts() ) : ?>
		<div class="sa-grid">
			<?php
			$subaru_i = 0;
			while ( have_posts() ) {
				the_post();
				subaru_post_card( get_post(), 'grid', $subaru_i );
				++$subaru_i;
			}
			?>
		</div>
		<?php subaru_pagination(); ?>
	<?php elseif ( ! $subaru_featured ) : ?>
		<div class="sa-empty-state">
			<?php subaru_the_icon( 'search', 32 ); ?>
			<p><?php echo esc_html( $subaru_empty ); ?></p>
			<a class="sa-btn sa-btn--outline" href="<?php echo esc_url( subaru_blog_page()[1] ); ?>"><?php esc_html_e( 'Back to Newsroom', 'subaru-alkhoory' ); ?></a>
		</div>
	<?php endif; ?>
</div>
