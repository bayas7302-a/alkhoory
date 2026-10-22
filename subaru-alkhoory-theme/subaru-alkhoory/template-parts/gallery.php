<?php
/**
 * Pack photo gallery.
 *
 * - feature: one large + two stacked tiles (model pages); the last tile shows "+N" and the lightbox
 *   steps through every photo of the model.
 * - masonry: every photo in three columns (Media Gallery page), optional model filter.
 *
 * Each tile links to the full-size file, so without JavaScript the photo opens directly;
 * with JavaScript the theme lightbox opens instead.
 *
 * @package SubaruAlKhoory
 *
 * @var array $args { items: array, layout: string, filter: bool }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$subaru_items  = $args['items'];
$subaru_layout = $args['layout'];
$subaru_models = array();
foreach ( $subaru_items as $subaru_it ) {
	$subaru_models[ $subaru_it['model'] ] = $subaru_it['name'];
}
$subaru_total = count( $subaru_items );
?>
<div class="sa-gal sa-gal--<?php echo esc_attr( $subaru_layout ); ?>" data-sa-gal>
	<?php if ( $args['filter'] && count( $subaru_models ) > 1 ) : ?>
		<div class="sa-gal__filters" role="group" aria-label="<?php esc_attr_e( 'Filter photos by model', 'subaru-alkhoory' ); ?>">
			<button type="button" class="sa-gal__filter" data-filter="all" aria-pressed="true"><?php esc_html_e( 'All Models', 'subaru-alkhoory' ); ?></button>
			<?php foreach ( $subaru_models as $subaru_slug => $subaru_name ) : ?>
				<button type="button" class="sa-gal__filter" data-filter="<?php echo esc_attr( $subaru_slug ); ?>" aria-pressed="false"><?php echo esc_html( $subaru_name ); ?></button>
			<?php endforeach; ?>
		</div>
		<p class="sa-gal__count screen-reader-text" aria-live="polite"></p>
	<?php endif; ?>

	<div class="sa-gal__grid">
		<?php foreach ( $subaru_items as $subaru_i => $subaru_it ) : ?>
			<?php
			$subaru_hidden = 'feature' === $subaru_layout && $subaru_i > 2;
			$subaru_more   = 'feature' === $subaru_layout && 2 === $subaru_i && $subaru_total > 3 ? $subaru_total - 3 : 0;
			?>
			<figure class="sa-gal__item" data-model="<?php echo esc_attr( $subaru_it['model'] ); ?>"<?php echo $subaru_hidden ? ' hidden' : ''; ?>>
				<?php
				$subaru_sizes = 'feature' === $subaru_layout && 0 === $subaru_i ? '(max-width: 767px) 100vw, 60vw' : '(max-width: 767px) 100vw, 33vw';
				if ( ! empty( $subaru_it['id'] ) ) {
					// Media Library photo (from the model pages).
					$subaru_href = wp_get_attachment_image_url( $subaru_it['id'], 'full' );
					$subaru_img  = wp_get_attachment_image(
						$subaru_it['id'],
						'large',
						false,
						array(
							'class'    => 'sa-gal__img',
							'loading'  => 'lazy',
							'decoding' => 'async',
							'sizes'    => $subaru_sizes,
						)
					);
				} else {
					$subaru_href = subaru_asset_url( $subaru_it['path'] );
					$subaru_img  = subaru_asset_img(
						$subaru_it['path'],
						array(
							'alt'   => $subaru_it['alt'],
							'class' => 'sa-gal__img',
							'sizes' => $subaru_sizes,
						)
					);
				}
				?>
				<a class="sa-gal__link" href="<?php echo esc_url( $subaru_href ); ?>">
					<?php echo $subaru_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php if ( $subaru_more ) : ?>
						<span class="sa-gal__more" aria-hidden="true">+<?php echo (int) $subaru_more; ?></span>
						<span class="screen-reader-text"><?php echo esc_html( sprintf( /* translators: %d: number of photos */ _n( 'and %d more photo', 'and %d more photos', $subaru_more, 'subaru-alkhoory' ), $subaru_more ) ); ?></span>
					<?php endif; ?>
				</a>
			</figure>
		<?php endforeach; ?>
	</div>
</div>
