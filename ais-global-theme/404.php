<?php
/**
 * 404 page.
 *
 * @package AIS_Global
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main class="ais-container ais-main ais-404">
	<h1 class="ais-page-title"><?php esc_html_e( 'Page not found', 'ais-global' ); ?></h1>
	<p><?php esc_html_e( 'Sorry, the page you are looking for does not exist or has moved.', 'ais-global' ); ?></p>
	<p><a class="ais-btn" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to home', 'ais-global' ); ?></a></p>
</main>
<?php
get_footer();
