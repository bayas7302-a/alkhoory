<?php
/**
 * Archives (category, tag, author, date) in the Newsroom layout.
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$subaru_blog = subaru_blog_page();
$subaru_desc = wp_strip_all_tags( get_the_archive_description() );
?>
<main id="content" class="sa-main sa-blog">
	<?php
	subaru_page_hero(
		array(
			'eyebrow' => is_category() ? __( 'Category', 'subaru-alkhoory' ) : __( 'Archive', 'subaru-alkhoory' ),
			'title'   => wp_strip_all_tags( get_the_archive_title() ),
			'text'    => $subaru_desc ? $subaru_desc : __( 'Browse articles from the Subaru UAE Newsroom.', 'subaru-alkhoory' ),
			'crumbs'  => array(
				array( $subaru_blog[0], $subaru_blog[1] ),
				array( wp_strip_all_tags( get_the_archive_title() ), null ),
			),
		)
	);
	get_template_part( 'template-parts/listing', null, array( 'featured' => 0 ) );
	subaru_cta_band();
	?>
</main>
<?php
get_footer();
