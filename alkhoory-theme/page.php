<?php
/**
 * Default page template. Pages built with Elementor should use the
 * "Elementor Full Width" template so sections can span the viewport.
 *
 * @package Alkhoory
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<div class="ak-container ak-content">
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'ak-entry' ); ?>>
			<h1 class="ak-content__title"><?php the_title(); ?></h1>
			<div class="ak-entry__content"><?php the_content(); ?></div>
		</article>
	</div>
	<?php
endwhile;

get_footer();
