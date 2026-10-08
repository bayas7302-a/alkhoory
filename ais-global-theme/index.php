<?php
/**
 * Fallback template (blog index, archives, search).
 *
 * @package AIS_Global
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main class="ais-container ais-main">
	<?php if ( is_home() || is_archive() || is_search() ) : ?>
		<header class="ais-page-header">
			<h1 class="ais-page-title">
				<?php
				if ( is_search() ) {
					/* translators: %s: search term */
					printf( esc_html__( 'Search results for: %s', 'ais-global' ), esc_html( get_search_query() ) );
				} elseif ( is_archive() ) {
					the_archive_title();
				} else {
					single_post_title();
				}
				?>
			</h1>
		</header>
	<?php endif; ?>

	<?php if ( have_posts() ) : ?>
		<div class="ais-posts">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'ais-card' ); ?>>
					<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'large' ); ?></a>
					<?php endif; ?>
					<h2 class="ais-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<div class="ais-card__excerpt"><?php the_excerpt(); ?></div>
				</article>
			<?php endwhile; ?>
		</div>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'Nothing found.', 'ais-global' ); ?></p>
	<?php endif; ?>
</main>
<?php
get_footer();
