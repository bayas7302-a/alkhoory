<?php
/**
 * Newsroom (posts page).
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$subaru_blog     = subaru_blog_page();
$subaru_page_id  = (int) get_option( 'page_for_posts' );
$subaru_intro    = $subaru_page_id && has_excerpt( $subaru_page_id ) ? get_the_excerpt( $subaru_page_id ) : __( 'Stay inspired with the latest Subaru UAE news, model spotlights, driving tips, technology insights and adventure guides.', 'subaru-alkhoory' );
$subaru_featured = ( ! is_paged() ) ? subaru_featured_post_id() : 0;
?>
<main id="content" class="sa-main sa-blog">
	<?php
	subaru_page_hero(
		array(
			'eyebrow' => __( 'News & Blogs', 'subaru-alkhoory' ),
			'title'   => $subaru_blog[0] . '.',
			'text'    => $subaru_intro,
			'crumbs'  => array( array( $subaru_blog[0], null ) ),
		)
	);
	get_template_part( 'template-parts/listing', null, array( 'featured' => $subaru_featured ) );
	subaru_cta_band();
	?>
</main>
<?php
get_footer();
