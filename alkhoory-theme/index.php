<?php
/**
 * Fallback template for posts, archives and search.
 *
 * @package Alkhoory
 */

get_header();
?>
<div class="ak-container ak-content">
	<?php if ( have_posts() ) : ?>
		<?php if ( ! is_singular() ) : ?>
			<h1 class="ak-content__title">
				<?php
				if ( is_home() ) {
					single_post_title();
				} elseif ( is_search() ) {
					/* translators: %s: search query. */
					printf( esc_html__( 'Search results for “%s”', 'alkhoory' ), esc_html( get_search_query() ) );
				} else {
					the_archive_title();
				}
				?>
			</h1>
		<?php endif; ?>

		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( is_singular() ? 'ak-entry' : 'ak-entry ak-entry--summary' ); ?>>
				<?php if ( is_singular() ) : ?>
					<h1 class="ak-content__title"><?php the_title(); ?></h1>
					<div class="ak-entry__content"><?php the_content(); ?></div>
				<?php else : ?>
					<h2 class="ak-entry__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<p class="ak-entry__meta"><?php echo esc_html( get_the_date() ); ?></p>
					<div class="ak-entry__content"><?php the_excerpt(); ?></div>
				<?php endif; ?>
			</article>
		<?php endwhile; ?>

		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<h1 class="ak-content__title"><?php esc_html_e( 'Nothing found', 'alkhoory' ); ?></h1>
	<?php endif; ?>
</div>
<?php
get_footer();
