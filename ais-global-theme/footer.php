<?php
/**
 * Site footer. Renders the Elementor page chosen in
 * Appearance > Customize > AIS Theme Options, or a simple built-in footer.
 *
 * @package AIS_Global
 */

defined( 'ABSPATH' ) || exit;
?>
</div><!-- #content -->

<footer id="site-footer" class="ais-footer">
	<?php
	if ( ! ais_global_render_elementor_part( get_theme_mod( 'ais_footer_page', 0 ) ) ) :
		?>
		<div class="ais-footer__fallback">
			<p><?php echo esc_html( get_theme_mod( 'ais_copyright', '© Copyright 2026, All Rights Reserved by AIS Global Group' ) ); ?></p>
		</div>
	<?php endif; ?>
</footer>

<?php wp_footer(); ?>
</body>
</html>
