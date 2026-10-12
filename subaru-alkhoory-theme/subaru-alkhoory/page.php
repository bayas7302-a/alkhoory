<?php
/**
 * Pages using the default template.
 *
 * Elementor-built pages render full-width without a theme title (the page builds its own hero);
 * classic pages (e.g. Privacy Policy) get a branded hero and readable content column.
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	if ( subaru_is_elementor_page() ) :
		?>
		<main id="content" <?php post_class( 'sa-main sa-elementor-page' ); ?>>
			<?php the_content(); ?>
		</main>
		<?php
	else :
		?>
		<main id="content" <?php post_class( 'sa-main sa-page' ); ?>>
			<?php
			subaru_page_hero(
				array(
					'title'  => get_the_title(),
					'text'   => has_excerpt() ? get_the_excerpt() : '',
					'image'  => has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'subaru-hero' ) : '',
					'crumbs' => array( array( get_the_title(), null ) ),
				)
			);
			?>
			<div class="sa-wrap sa-page__body">
				<div class="sa-prose">
					<?php
					the_content();
					wp_link_pages();
					?>
				</div>
				<?php
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
				?>
			</div>
		</main>
		<?php
	endif;
endwhile;

get_footer();
