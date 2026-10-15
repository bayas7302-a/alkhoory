<?php
/**
 * Pages: Elementor pages render edge to edge (sections handle the 1350px width themselves).
 */
defined( 'ABSPATH' ) || exit;
get_header();
while ( have_posts() ) :
	the_post();
	$onora_is_elementor = class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->documents->get( get_the_ID() ) && \Elementor\Plugin::$instance->documents->get( get_the_ID() )->is_built_with_elementor();
	if ( $onora_is_elementor ) :
		the_content();
	else :
		?>
		<div class="onora-container onora-content">
			<h1 class="onora-entry__title"><?php the_title(); ?></h1>
			<?php the_content(); ?>
		</div>
		<?php
	endif;
endwhile;
get_footer();
