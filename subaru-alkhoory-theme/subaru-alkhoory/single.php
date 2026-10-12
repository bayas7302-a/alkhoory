<?php
/**
 * Single post (Newsroom article).
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	$subaru_id   = get_the_ID();
	$subaru_cat  = subaru_primary_category( $subaru_id );
	$subaru_blog = subaru_blog_page();
	$subaru_img  = has_post_thumbnail() ? get_the_post_thumbnail_url( $subaru_id, 'subaru-hero' ) : '';
	$subaru_elem = subaru_is_elementor_page( $subaru_id );

	ob_start();
	?>
	<ul class="sa-postmeta">
		<?php if ( $subaru_cat ) : ?>
			<li><a class="sa-badge sa-badge--glass" href="<?php echo esc_url( get_category_link( $subaru_cat ) ); ?>"><?php echo esc_html( $subaru_cat->name ); ?></a></li>
		<?php endif; ?>
		<li><?php subaru_the_icon( 'calendar', 16 ); ?><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></li>
		<li><?php subaru_the_icon( 'clock', 16 ); ?>
			<?php
			/* translators: %d: minutes */
			echo esc_html( sprintf( _n( '%d min read', '%d min read', subaru_reading_time( $subaru_id ), 'subaru-alkhoory' ), subaru_reading_time( $subaru_id ) ) );
			?>
		</li>
		<li><?php subaru_the_icon( 'user', 16 ); ?><?php echo esc_html( get_the_author() ); ?></li>
	</ul>
	<?php
	$subaru_meta = ob_get_clean();
	?>
	<main id="content" <?php post_class( 'sa-main sa-article' ); ?>>
		<?php
		subaru_page_hero(
			array(
				'title'  => get_the_title(),
				'image'  => $subaru_img,
				'class'  => 'sa-phero--article',
				'meta'   => $subaru_meta,
				'crumbs' => array(
					array( $subaru_blog[0], $subaru_blog[1] ),
					$subaru_cat ? array( $subaru_cat->name, get_category_link( $subaru_cat ) ) : array( get_the_title(), null ),
					array( get_the_title(), null ),
				),
			)
		);
		?>

		<div class="sa-article__layout sa-wrap">
			<aside class="sa-article__aside">
				<?php subaru_share_buttons(); ?>
			</aside>

			<div class="sa-article__body">
				<?php if ( has_excerpt() ) : ?>
					<p class="sa-article__lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
				<div class="sa-prose<?php echo $subaru_elem ? ' sa-prose--elementor' : ''; ?>">
					<?php
					the_content();
					wp_link_pages(
						array(
							'before' => '<nav class="sa-pagelinks">' . esc_html__( 'Pages:', 'subaru-alkhoory' ),
							'after'  => '</nav>',
						)
					);
					?>
				</div>

				<?php
				$subaru_tags = get_the_tags();
				if ( $subaru_tags ) :
					?>
					<ul class="sa-tags" aria-label="<?php esc_attr_e( 'Tags', 'subaru-alkhoory' ); ?>">
						<?php foreach ( $subaru_tags as $subaru_tag ) : ?>
							<li><a href="<?php echo esc_url( get_tag_link( $subaru_tag ) ); ?>">#<?php echo esc_html( $subaru_tag->name ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<div class="sa-article__share-mobile"><?php subaru_share_buttons(); ?></div>

				<?php
				$subaru_prev = get_previous_post();
				$subaru_next = get_next_post();
				if ( $subaru_prev || $subaru_next ) :
					?>
					<nav class="sa-prevnext" aria-label="<?php esc_attr_e( 'More articles', 'subaru-alkhoory' ); ?>">
						<?php if ( $subaru_prev ) : ?>
							<a class="sa-prevnext__link sa-prevnext__link--prev" href="<?php echo esc_url( get_permalink( $subaru_prev ) ); ?>">
								<span class="sa-prevnext__dir"><?php subaru_the_icon( 'chevron-l', 16 ); ?><?php esc_html_e( 'Previous article', 'subaru-alkhoory' ); ?></span>
								<span class="sa-prevnext__title"><?php echo esc_html( get_the_title( $subaru_prev ) ); ?></span>
							</a>
						<?php endif; ?>
						<?php if ( $subaru_next ) : ?>
							<a class="sa-prevnext__link sa-prevnext__link--next" href="<?php echo esc_url( get_permalink( $subaru_next ) ); ?>">
								<span class="sa-prevnext__dir"><?php esc_html_e( 'Next article', 'subaru-alkhoory' ); ?><?php subaru_the_icon( 'chevron-r', 16 ); ?></span>
								<span class="sa-prevnext__title"><?php echo esc_html( get_the_title( $subaru_next ) ); ?></span>
							</a>
						<?php endif; ?>
					</nav>
				<?php endif; ?>

				<?php
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
				?>
			</div>
		</div>

		<?php
		$subaru_related = new WP_Query(
			array(
				'post_type'           => 'post',
				'posts_per_page'      => 3,
				'post__not_in'        => array( $subaru_id ),
				'category__in'        => $subaru_cat ? array( $subaru_cat->term_id ) : array(),
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);
		if ( $subaru_related->post_count < 3 ) {
			$subaru_related = new WP_Query(
				array(
					'post_type'           => 'post',
					'posts_per_page'      => 3,
					'post__not_in'        => array( $subaru_id ),
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
				)
			);
		}
		if ( $subaru_related->have_posts() ) :
			?>
			<section class="sa-related">
				<div class="sa-wrap">
					<div class="sa-related__head">
						<div>
							<p class="sa-eyebrow"><?php esc_html_e( 'Keep reading', 'subaru-alkhoory' ); ?></p>
							<h2 class="sa-h2"><?php esc_html_e( 'Related articles', 'subaru-alkhoory' ); ?></h2>
						</div>
						<a class="sa-link" href="<?php echo esc_url( $subaru_blog[1] ); ?>"><?php esc_html_e( 'View all news', 'subaru-alkhoory' ); ?> <?php subaru_the_icon( 'arrow', 16 ); ?></a>
					</div>
					<div class="sa-grid">
						<?php
						$subaru_i = 0;
						while ( $subaru_related->have_posts() ) {
							$subaru_related->the_post();
							subaru_post_card( get_post(), 'grid', $subaru_i );
							++$subaru_i;
						}
						wp_reset_postdata();
						?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<?php subaru_cta_band(); ?>
	</main>
	<?php
endwhile;

get_footer();
