<?php
/**
 * Fallback template for posts, archives and non-Elementor pages.
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>
<div class="onora-container onora-content">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class( 'onora-entry' ); ?>>
				<?php if ( ! is_singular() ) : ?>
					<h2 class="onora-entry__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php the_excerpt(); ?>
				<?php else : ?>
					<h1 class="onora-entry__title"><?php the_title(); ?></h1>
					<?php the_content(); ?>
				<?php endif; ?>
			</article>
		<?php endwhile; ?>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<h1 class="onora-entry__title"><?php esc_html_e( 'Nothing found', 'onora' ); ?></h1>
		<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to the home page', 'onora' ); ?></a></p>
	<?php endif; ?>
</div>
<?php
get_footer();
