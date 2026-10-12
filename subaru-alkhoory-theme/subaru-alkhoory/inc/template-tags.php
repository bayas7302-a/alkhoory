<?php
/**
 * Template tags for blog / newsroom templates.
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Primary category of a post.
 *
 * @param int $post_id Post.
 * @return WP_Term|null
 */
function subaru_primary_category( $post_id ) {
	$cats = get_the_category( $post_id );
	if ( ! $cats ) {
		return null;
	}
	foreach ( $cats as $c ) {
		if ( 'uncategorized' !== $c->slug ) {
			return $c;
		}
	}
	return $cats[0];
}

/**
 * Estimated reading time in minutes.
 *
 * @param int $post_id Post.
 * @return int
 */
function subaru_reading_time( $post_id ) {
	$words = str_word_count( wp_strip_all_tags( strip_shortcodes( get_post_field( 'post_content', $post_id ) ) ) );
	return max( 1, (int) ceil( $words / 220 ) );
}

/**
 * Render a post card.
 *
 * @param WP_Post $post    Post.
 * @param string  $variant home|grid|list|featured.
 * @param int     $index   Index (stagger).
 */
function subaru_post_card( $post, $variant = 'grid', $index = 0 ) {
	$id     = $post->ID;
	$url    = get_permalink( $id );
	$cat    = subaru_primary_category( $id );
	$title  = get_the_title( $id );
	$date_f = 'home' === $variant ? 'F Y' : ( 'featured' === $variant ? 'F Y' : 'M Y' );
	$size   = 'featured' === $variant ? 'large' : 'subaru-card';
	$delay  = ( $index % 3 ) * 120;
	?>
	<article <?php post_class( 'sa-card sa-card--' . $variant, $id ); ?> data-sa-reveal style="--d:<?php echo (int) $delay; ?>ms">
		<a class="sa-card__media" href="<?php echo esc_url( $url ); ?>" tabindex="-1" aria-hidden="true">
			<?php
			if ( has_post_thumbnail( $id ) ) {
				echo get_the_post_thumbnail(
					$id,
					$size,
					array(
						'loading' => ( 'featured' === $variant ) ? 'eager' : 'lazy',
						'alt'     => '',
					)
				);
			} else {
				echo '<span class="sa-card__ph">';
				subaru_the_icon( 'car', 48 );
				echo '</span>';
			}
			?>
		</a>
		<div class="sa-card__body">
			<div class="sa-card__meta">
				<?php if ( 'featured' === $variant ) : ?>
					<span class="sa-badge sa-badge--solid"><?php esc_html_e( 'Featured', 'subaru-alkhoory' ); ?></span>
				<?php endif; ?>
				<?php if ( $cat ) : ?>
					<a class="<?php echo esc_attr( 'home' === $variant ? 'sa-badge' : 'sa-card__cat' ); ?>" href="<?php echo esc_url( get_category_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a>
				<?php endif; ?>
				<time class="sa-card__date" datetime="<?php echo esc_attr( get_the_date( 'c', $id ) ); ?>"><?php echo esc_html( get_the_date( $date_f, $id ) ); ?></time>
			</div>
			<?php if ( 'featured' === $variant ) : ?>
				<h2 class="sa-card__title"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a></h2>
			<?php else : ?>
				<h3 class="sa-card__title"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a></h3>
			<?php endif; ?>
			<p class="sa-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt( $id ), 'featured' === $variant ? 34 : 22 ) ); ?></p>
			<a class="sa-link sa-card__more" href="<?php echo esc_url( $url ); ?>">
				<?php esc_html_e( 'Read article', 'subaru-alkhoory' ); ?>
				<?php subaru_the_icon( 'arrow', 16 ); ?>
				<span class="screen-reader-text"><?php echo esc_html( ': ' . $title ); ?></span>
			</a>
		</div>
	</article>
	<?php
}

/**
 * Breadcrumbs (Home / Newsroom / …).
 *
 * @param array $trail Additional crumbs: array of [label, url|null].
 */
function subaru_breadcrumbs( $trail ) {
	echo '<nav class="sa-crumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'subaru-alkhoory' ) . '"><ol>';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'subaru-alkhoory' ) . '</a></li>';
	$last = count( $trail ) - 1;
	foreach ( $trail as $i => $crumb ) {
		if ( $i === $last || empty( $crumb[1] ) ) {
			echo '<li><span aria-current="page">' . esc_html( $crumb[0] ) . '</span></li>';
		} else {
			echo '<li><a href="' . esc_url( $crumb[1] ) . '">' . esc_html( $crumb[0] ) . '</a></li>';
		}
	}
	echo '</ol></nav>';
}

/**
 * Newsroom (posts page) title & URL.
 *
 * @return array [title, url]
 */
function subaru_blog_page() {
	$id = (int) get_option( 'page_for_posts' );
	if ( $id ) {
		return array( get_the_title( $id ), get_permalink( $id ) );
	}
	return array( __( 'Newsroom', 'subaru-alkhoory' ), home_url( '/' ) );
}

/**
 * Page hero for theme templates.
 *
 * @param array $args eyebrow, title, text, image (URL), crumbs, class, meta (html).
 */
function subaru_page_hero( $args ) {
	$args = wp_parse_args(
		$args,
		array(
			'eyebrow' => '',
			'title'   => '',
			'text'    => '',
			'image'   => '',
			'crumbs'  => array(),
			'class'   => '',
			'meta'    => '',
		)
	);
	$style = $args['image'] ? ' style="--sa-hero-img:url(' . esc_url( $args['image'] ) . ')"' : '';
	?>
	<header class="sa-phero <?php echo esc_attr( $args['class'] . ( $args['image'] ? ' has-image' : '' ) ); ?>"<?php echo $style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>>
		<div class="sa-phero__inner sa-wrap">
			<?php
			if ( $args['crumbs'] ) {
				subaru_breadcrumbs( $args['crumbs'] );
			}
			if ( $args['eyebrow'] ) {
				echo '<p class="sa-eyebrow sa-eyebrow--light">' . esc_html( $args['eyebrow'] ) . '</p>';
			}
			?>
			<h1 class="sa-phero__title"><?php echo esc_html( $args['title'] ); ?></h1>
			<?php
			if ( $args['text'] ) {
				echo '<p class="sa-phero__text">' . esc_html( $args['text'] ) . '</p>';
			}
			if ( $args['meta'] ) {
				echo wp_kses_post( $args['meta'] );
			}
			?>
		</div>
	</header>
	<?php
}

/**
 * Category filter pills + search (Newsroom).
 */
function subaru_blog_toolbar() {
	$cats    = get_categories(
		array(
			'hide_empty' => true,
			'exclude'    => array( (int) get_option( 'default_category' ) ),
			'orderby'    => 'count',
			'order'      => 'DESC',
			'number'     => 8,
		)
	);
	$current = is_category() ? get_queried_object_id() : 0;
	$blog    = subaru_blog_page();
	?>
	<div class="sa-toolbar">
		<nav class="sa-pills" aria-label="<?php esc_attr_e( 'Filter by category', 'subaru-alkhoory' ); ?>">
			<a class="sa-pill<?php echo $current ? '' : ' is-active'; ?>" href="<?php echo esc_url( $blog[1] ); ?>"<?php echo $current ? '' : ' aria-current="page"'; ?>><?php esc_html_e( 'All', 'subaru-alkhoory' ); ?></a>
			<?php foreach ( $cats as $c ) : ?>
				<a class="sa-pill<?php echo $current === $c->term_id ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_category_link( $c ) ); ?>"<?php echo $current === $c->term_id ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $c->name ); ?></a>
			<?php endforeach; ?>
		</nav>
		<form class="sa-search-inline" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="sa-blog-search"><?php esc_html_e( 'Search articles', 'subaru-alkhoory' ); ?></label>
			<?php subaru_the_icon( 'search', 16 ); ?>
			<input type="search" id="sa-blog-search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search articles', 'subaru-alkhoory' ); ?>">
			<input type="hidden" name="post_type" value="post">
		</form>
	</div>
	<?php
}

/**
 * Numbered pagination.
 *
 * @param WP_Query|null $query Query.
 */
function subaru_pagination( $query = null ) {
	global $wp_query;
	$query = $query ? $query : $wp_query;
	$links = paginate_links(
		array(
			'total'     => $query->max_num_pages,
			'current'   => max( 1, get_query_var( 'paged' ) ),
			'type'      => 'array',
			'mid_size'  => 1,
			'end_size'  => 1,
			'prev_text' => esc_html__( 'Previous', 'subaru-alkhoory' ),
			'next_text' => esc_html__( 'Next', 'subaru-alkhoory' ),
		)
	);
	if ( ! $links ) {
		return;
	}
	echo '<nav class="sa-pagination" aria-label="' . esc_attr__( 'Posts pagination', 'subaru-alkhoory' ) . '"><ul>';
	foreach ( $links as $l ) {
		echo '<li>' . wp_kses_post( $l ) . '</li>';
	}
	echo '</ul></nav>';
}

/**
 * Share buttons.
 */
function subaru_share_buttons() {
	$url   = get_permalink();
	$title = wp_strip_all_tags( get_the_title() );
	$nets  = array(
		'whatsapp' => array( 'WhatsApp', 'https://wa.me/?text=' . rawurlencode( $title . ' ' . $url ) ),
		'x'        => array( 'X', 'https://x.com/intent/post?text=' . rawurlencode( $title ) . '&url=' . rawurlencode( $url ) ),
		'linkedin' => array( 'LinkedIn', 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode( $url ) ),
		'facebook' => array( 'Facebook', 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode( $url ) ),
	);
	echo '<div class="sa-share"><span class="sa-share__label">' . esc_html__( 'Share', 'subaru-alkhoory' ) . '</span><ul>';
	echo '<li><button type="button" class="sa-share__btn" data-sa-copy="' . esc_url( $url ) . '">';
	subaru_the_icon( 'link', 18 );
	echo '<span class="screen-reader-text">' . esc_html__( 'Copy link', 'subaru-alkhoory' ) . '</span><span class="sa-share__tip" aria-live="polite"></span></button></li>';
	foreach ( $nets as $key => $n ) {
		echo '<li><a class="sa-share__btn" href="' . esc_url( $n[1] ) . '" target="_blank" rel="noopener nofollow">';
		subaru_the_icon( $key, 18 );
		/* translators: %s: network */
		echo '<span class="screen-reader-text">' . esc_html( sprintf( __( 'Share on %s', 'subaru-alkhoory' ), $n[0] ) ) . '</span></a></li>';
	}
	echo '</ul></div>';
}

/**
 * "Let's start your Subaru journey" CTA band (theme templates).
 */
function subaru_cta_band() {
	$img = get_theme_mod( 'subaru_cta_image' );
	if ( ! $img ) {
		$found = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'name'           => '1c84b',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		$img = $found ? wp_get_attachment_image_url( $found[0], 'large' ) : '';
	}
	get_template_part( 'template-parts/cta', null, array( 'image' => $img ) );
}

/**
 * Comment callback.
 *
 * @param WP_Comment $comment Comment.
 * @param array      $args    Args.
 * @param int        $depth   Depth.
 */
function subaru_comment( $comment, $args, $depth ) {
	$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
	?>
	<<?php echo esc_html( $tag ); ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? 'sa-comment' : 'sa-comment parent', $comment ); ?>>
		<article class="sa-comment__body">
			<div class="sa-comment__avatar"><?php echo get_avatar( $comment, 48 ); ?></div>
			<div class="sa-comment__main">
				<header class="sa-comment__head">
					<strong class="sa-comment__author"><?php echo esc_html( get_comment_author( $comment ) ); ?></strong>
					<time datetime="<?php echo esc_attr( get_comment_time( 'c' ) ); ?>"><?php echo esc_html( get_comment_date( '', $comment ) ); ?></time>
				</header>
				<?php if ( '0' === $comment->comment_approved ) : ?>
					<p class="sa-comment__pending"><?php esc_html_e( 'Your comment is awaiting moderation.', 'subaru-alkhoory' ); ?></p>
				<?php endif; ?>
				<div class="sa-comment__text"><?php comment_text(); ?></div>
				<?php
				comment_reply_link(
					array_merge(
						$args,
						array(
							'depth'     => $depth,
							'max_depth' => $args['max_depth'],
							'before'    => '<div class="sa-comment__reply">',
							'after'     => '</div>',
						)
					)
				);
				?>
			</div>
		</article>
	<?php
}
