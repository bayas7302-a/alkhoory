<?php
/**
 * Model configurator: Exterior (colour swatches + optional 360° view) / Interior (grade + trim swatches).
 * Without JavaScript the default colour image is shown; models.js adds switching, 360° and the interior tab.
 *
 * @package SubaruAlKhoory
 *
 * @var array $args { model: array, data: array } (see subaru_configurator_data()).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$subaru_model = $args['model'];
$subaru_data  = $args['data'];
$subaru_uid   = 'sa-cfg-' . $subaru_model['slug'];
$subaru_def   = wp_list_filter( $subaru_data['colours'], array( 'key' => $subaru_data['default'] ) );
$subaru_def   = $subaru_def ? reset( $subaru_def ) : $subaru_data['colours'][0];
$subaru_int   = $subaru_data['interiors'];
$subaru_spin  = (bool) array_filter( wp_list_pluck( $subaru_data['colours'], 'frames' ) );
?>
<div class="sa-cfg" id="<?php echo esc_attr( $subaru_uid ); ?>" data-sa-cfg>
	<?php if ( $subaru_int ) : ?>
		<div class="sa-cfg__tabs" role="tablist" aria-label="<?php esc_attr_e( 'View', 'subaru-alkhoory' ); ?>">
			<button type="button" class="sa-cfg__tab" role="tab" id="<?php echo esc_attr( $subaru_uid ); ?>-tab-ext" aria-controls="<?php echo esc_attr( $subaru_uid ); ?>-ext" aria-selected="true"><?php esc_html_e( 'Exterior', 'subaru-alkhoory' ); ?></button>
			<button type="button" class="sa-cfg__tab" role="tab" id="<?php echo esc_attr( $subaru_uid ); ?>-tab-int" aria-controls="<?php echo esc_attr( $subaru_uid ); ?>-int" aria-selected="false" tabindex="-1"><?php esc_html_e( 'Interior', 'subaru-alkhoory' ); ?></button>
		</div>
	<?php endif; ?>

	<div class="sa-cfg__panel" id="<?php echo esc_attr( $subaru_uid ); ?>-ext" role="tabpanel" aria-labelledby="<?php echo esc_attr( $subaru_uid ); ?>-tab-ext">
		<div class="sa-cfg__stage" data-stage="ext">
			<div class="sa-cfg__layers">
				<?php
				echo subaru_asset_img( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$subaru_def['path'],
					array(
						'alt'   => $subaru_def['alt'],
						'class' => 'sa-cfg__img is-on',
						'sizes' => '(max-width: 1250px) 100vw, 1250px',
					)
				);
				?>
			</div>
			<?php if ( $subaru_spin ) : ?>
				<button type="button" class="sa-cfg__spin-btn" aria-pressed="false" hidden>
					<svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12a9 9 0 1 1-3-6.7M21 4v5h-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
					<span><?php esc_html_e( '360° view', 'subaru-alkhoory' ); ?></span>
				</button>
				<p class="sa-cfg__hint" hidden><?php esc_html_e( 'Drag to rotate', 'subaru-alkhoory' ); ?></p>
				<div class="sa-cfg__progress" hidden><span></span></div>
			<?php endif; ?>
			<p class="sa-cfg__name" aria-live="polite"><?php echo esc_html( $subaru_def['name'] ); ?></p>
		</div>
		<div class="sa-cfg__swatches" role="group" aria-label="<?php esc_attr_e( 'Exterior colour', 'subaru-alkhoory' ); ?>">
			<?php foreach ( $subaru_data['colours'] as $subaru_c ) : ?>
				<?php $subaru_on = $subaru_c['key'] === $subaru_def['key']; ?>
				<button type="button" class="sa-cfg__swatch" style="--sw: <?php echo esc_attr( $subaru_c['hex'] ); ?>" data-key="<?php echo esc_attr( $subaru_c['key'] ); ?>" aria-pressed="<?php echo $subaru_on ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: colour name */ __( 'Colour: %s', 'subaru-alkhoory' ), $subaru_c['name'] ) ); ?>" title="<?php echo esc_attr( $subaru_c['name'] ); ?>"<?php echo $subaru_on ? '' : ' tabindex="-1"'; ?>></button>
			<?php endforeach; ?>
		</div>
	</div>

	<?php if ( $subaru_int ) : ?>
		<?php $subaru_first = $subaru_int[0]; ?>
		<div class="sa-cfg__panel" id="<?php echo esc_attr( $subaru_uid ); ?>-int" role="tabpanel" aria-labelledby="<?php echo esc_attr( $subaru_uid ); ?>-tab-int" hidden>
			<?php if ( count( $subaru_int ) > 1 ) : ?>
				<div class="sa-cfg__grades" role="group" aria-label="<?php esc_attr_e( 'Grade', 'subaru-alkhoory' ); ?>">
					<?php foreach ( $subaru_int as $subaru_i => $subaru_set ) : ?>
						<button type="button" class="sa-cfg__grade" data-set="<?php echo esc_attr( $subaru_set['id'] ); ?>" aria-pressed="<?php echo 0 === $subaru_i ? 'true' : 'false'; ?>"><?php echo esc_html( $subaru_set['label'] ); ?></button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<div class="sa-cfg__stage sa-cfg__stage--int" data-stage="int">
				<div class="sa-cfg__layers">
					<?php
					echo subaru_asset_img( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						$subaru_first['views'][0]['path'],
						array(
							'alt'   => $subaru_first['views'][0]['alt'],
							'class' => 'sa-cfg__img is-on',
							'sizes' => '(max-width: 1250px) 100vw, 1250px',
						)
					);
					?>
				</div>
				<p class="sa-cfg__name" aria-live="polite"><?php echo esc_html( $subaru_first['views'][0]['label'] ); ?></p>
			</div>
			<div class="sa-cfg__swatches sa-cfg__swatches--trim" role="group" aria-label="<?php esc_attr_e( 'Interior view', 'subaru-alkhoory' ); ?>"></div>
		</div>
	<?php endif; ?>

	<script type="application/json" class="sa-cfg__data"><?php echo wp_json_encode( $subaru_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP ); ?></script>
</div>
