<?php
/**
 * Comments.
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="sa-comments">
	<?php if ( have_comments() ) : ?>
		<h2 class="sa-comments__title">
			<?php
			/* translators: %s: number of comments */
			echo esc_html( sprintf( _n( '%s comment', '%s comments', get_comments_number(), 'subaru-alkhoory' ), number_format_i18n( get_comments_number() ) ) );
			?>
		</h2>
		<ol class="sa-comments__list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 48,
					'callback'    => 'subaru_comment',
				)
			);
			?>
		</ol>
		<?php
		the_comments_pagination(
			array(
				'prev_text' => esc_html__( 'Older', 'subaru-alkhoory' ),
				'next_text' => esc_html__( 'Newer', 'subaru-alkhoory' ),
			)
		);
		?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="sa-comments__closed"><?php esc_html_e( 'Comments are closed.', 'subaru-alkhoory' ); ?></p>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'class_form'         => 'sa-comment-form',
			'title_reply'        => esc_html__( 'Leave a comment', 'subaru-alkhoory' ),
			'title_reply_before' => '<h2 id="reply-title" class="sa-comments__title">',
			'title_reply_after'  => '</h2>',
			'class_submit'       => 'sa-btn',
		)
	);
	?>
</section>
