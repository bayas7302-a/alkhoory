<?php
/**
 * Search results.
 */

defined( 'ABSPATH' ) || exit;

get_header();
global $wp_query;
$found = (int) $wp_query->found_posts;
?>
<section class="yt-search-hero">
	<div class="yt-search-hero__inner">
		<p class="yt-search-hero__crumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'yutong-uae' ); ?></a><span aria-hidden="true">›</span><?php esc_html_e( 'Search', 'yutong-uae' ); ?></p>
		<h1 class="yt-search-hero__title">
			<?php
			if ( get_search_query() ) {
				/* translators: %s: search terms */
				printf( esc_html__( 'Results for “%s”', 'yutong-uae' ), '<span>' . esc_html( get_search_query() ) . '</span>' );
			} else {
				esc_html_e( 'Search the site', 'yutong-uae' );
			}
			?>
		</h1>
		<?php yutong_uae_search_form( 'page' ); ?>
		<?php if ( get_search_query() ) : ?>
			<p class="yt-search-hero__count">
				<?php
				/* translators: %d: number of results */
				printf( esc_html( _n( '%d result', '%d results', $found, 'yutong-uae' ) ), $found );
				?>
			</p>
		<?php endif; ?>
	</div>
</section>

<section class="yt-search-results">
	<?php if ( have_posts() ) : ?>
		<ol class="yt-search-results__list">
			<?php
			while ( have_posts() ) :
				the_post();
				$post = get_post();
				?>
				<li class="yt-result">
					<a class="yt-result__link" href="<?php the_permalink(); ?>">
						<?php if ( has_post_thumbnail() ) : ?>
							<span class="yt-result__thumb"><?php the_post_thumbnail( 'medium' ); ?></span>
						<?php endif; ?>
						<span class="yt-result__body">
							<span class="yt-result__label"><?php echo esc_html( yutong_uae_result_label( $post ) ); ?></span>
							<span class="yt-result__title"><?php the_title(); ?></span>
							<span class="yt-result__snippet"><?php echo wp_kses( yutong_uae_search_snippet( $post ), array( 'mark' => array() ) ); ?></span>
							<span class="yt-result__more"><?php esc_html_e( 'View page', 'yutong-uae' ); ?><?php echo yutong_uae_icon( 'arrow', 16 ); ?></span>
						</span>
					</a>
				</li>
			<?php endwhile; ?>
		</ol>
		<?php
		the_posts_pagination(
			array(
				'mid_size'  => 1,
				'prev_text' => '‹',
				'next_text' => '›',
				'class'     => 'yt-pagination',
			)
		);
		?>
	<?php else : ?>
		<div class="yt-search-empty">
			<p class="yt-search-empty__title"><?php esc_html_e( 'No matching pages found.', 'yutong-uae' ); ?></p>
			<p><?php esc_html_e( 'Try a model name such as T12E or U12, or browse:', 'yutong-uae' ); ?></p>
			<?php yutong_uae_menu( 'yutong-primary', 'yt-search-empty__links' ); ?>
		</div>
	<?php endif; ?>
</section>
<?php
get_footer();
