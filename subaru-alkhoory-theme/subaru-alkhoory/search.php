<?php
/**
 * Search results.
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

global $wp_query;
$subaru_blog = subaru_blog_page();
?>
<main id="content" class="sa-main sa-blog sa-search-results">
	<?php
	subaru_page_hero(
		array(
			'eyebrow' => __( 'Search', 'subaru-alkhoory' ),
			/* translators: %s: search query */
			'title'   => sprintf( __( 'Results for “%s”', 'subaru-alkhoory' ), get_search_query() ),
			/* translators: %d: number of results */
			'text'    => sprintf( _n( '%d result found.', '%d results found.', (int) $wp_query->found_posts, 'subaru-alkhoory' ), (int) $wp_query->found_posts ),
			'crumbs'  => array( array( __( 'Search', 'subaru-alkhoory' ), null ) ),
		)
	);
	?>
	<div class="sa-listing sa-wrap">
		<?php subaru_blog_toolbar(); ?>
		<?php if ( have_posts() ) : ?>
			<div class="sa-grid">
				<?php
				$subaru_i = 0;
				while ( have_posts() ) :
					the_post();
					if ( 'post' === get_post_type() ) {
						subaru_post_card( get_post(), 'grid', $subaru_i );
					} else {
						?>
						<article class="sa-card sa-card--page" data-sa-reveal style="--d:<?php echo (int) ( ( $subaru_i % 3 ) * 120 ); ?>ms">
							<div class="sa-card__body">
								<div class="sa-card__meta"><span class="sa-card__cat"><?php echo esc_html( get_post_type_object( get_post_type() )->labels->singular_name ); ?></span></div>
								<h3 class="sa-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
								<p class="sa-card__excerpt"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 22 ) ); ?></p>
								<a class="sa-link sa-card__more" href="<?php the_permalink(); ?>"><?php esc_html_e( 'View page', 'subaru-alkhoory' ); ?> <?php subaru_the_icon( 'arrow', 16 ); ?></a>
							</div>
						</article>
						<?php
					}
					++$subaru_i;
				endwhile;
				?>
			</div>
			<?php subaru_pagination(); ?>
		<?php else : ?>
			<div class="sa-empty-state">
				<?php subaru_the_icon( 'search', 32 ); ?>
				<p><?php esc_html_e( 'Sorry, nothing matched your search. Try different keywords or explore our range.', 'subaru-alkhoory' ); ?></p>
				<a class="sa-btn" href="<?php echo esc_url( home_url( '/our-range/' ) ); ?>"><?php esc_html_e( 'Explore Our Range', 'subaru-alkhoory' ); ?></a>
			</div>
		<?php endif; ?>
	</div>
	<?php subaru_cta_band(); ?>
</main>
<?php
get_footer();
