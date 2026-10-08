<?php
/**
 * Pages and single posts. Elementor pages render full width with no title;
 * everything else gets a simple readable layout.
 *
 * @package AIS_Global
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	if ( ais_global_is_elementor() ) :
		?>
		<main id="post-<?php the_ID(); ?>" <?php post_class( 'ais-elementor-main' ); ?>>
			<?php the_content(); ?>
		</main>
		<?php
	else :
		?>
		<main class="ais-container ais-main">
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'ais-entry' ); ?>>
				<h1 class="ais-page-title"><?php the_title(); ?></h1>
				<?php if ( has_post_thumbnail() && ! is_page() ) : ?>
					<div class="ais-entry__thumb"><?php the_post_thumbnail( 'large' ); ?></div>
				<?php endif; ?>
				<div class="ais-entry__content"><?php the_content(); ?></div>
				<?php wp_link_pages(); ?>
			</article>
			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
		</main>
		<?php
	endif;
endwhile;

get_footer();
