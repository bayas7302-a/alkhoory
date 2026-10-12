<?php
/**
 * Front-end assets.
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Asset version: file modification time in development, theme version otherwise.
 *
 * @param string $rel Relative path.
 * @return string
 */
function subaru_asset_ver( $rel ) {
	$file = SUBARU_DIR . '/' . $rel;
	return file_exists( $file ) ? SUBARU_VERSION . '.' . filemtime( $file ) : SUBARU_VERSION;
}

/**
 * Enqueue fonts, styles and scripts.
 */
function subaru_enqueue_assets() {
	// Hello's header/footer stylesheet is not needed (the theme ships its own header/footer).
	wp_dequeue_style( 'hello-elementor-header-footer' );

	wp_enqueue_style(
		'subaru-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Michroma&display=swap',
		array(),
		null // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	);
	wp_enqueue_style( 'subaru-theme', SUBARU_URI . '/assets/css/theme.css', array( 'subaru-fonts' ), subaru_asset_ver( 'assets/css/theme.css' ) );
	wp_enqueue_style( 'subaru-forms', SUBARU_URI . '/assets/css/forms.css', array( 'subaru-theme' ), subaru_asset_ver( 'assets/css/forms.css' ) );

	if ( is_home() || is_archive() || is_search() || is_singular( 'post' ) || is_404() || ( is_page() && ! subaru_is_elementor_page() ) ) {
		wp_enqueue_style( 'subaru-blog', SUBARU_URI . '/assets/css/blog.css', array( 'subaru-theme' ), subaru_asset_ver( 'assets/css/blog.css' ) );
	}

	wp_enqueue_script( 'subaru-theme', SUBARU_URI . '/assets/js/theme.js', array(), subaru_asset_ver( 'assets/js/theme.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_enqueue_script( 'subaru-forms', SUBARU_URI . '/assets/js/forms.js', array(), subaru_asset_ver( 'assets/js/forms.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );

	wp_localize_script(
		'subaru-forms',
		'SubaruForms',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'i18n'    => array(
				'required' => __( 'This field is required.', 'subaru-alkhoory' ),
				'email'    => __( 'Please enter a valid email address.', 'subaru-alkhoory' ),
				'phone'    => __( 'Please enter a valid phone number.', 'subaru-alkhoory' ),
				'date'     => __( 'Please choose a date from tomorrow onwards.', 'subaru-alkhoory' ),
				'consent'  => __( 'Please accept to continue.', 'subaru-alkhoory' ),
				'sending'  => __( 'Sending…', 'subaru-alkhoory' ),
				'error'    => __( 'Something went wrong. Please try again or contact us directly.', 'subaru-alkhoory' ),
				'fix'      => __( 'Please check the highlighted fields.', 'subaru-alkhoory' ),
			),
		)
	);

	wp_localize_script(
		'subaru-theme',
		'SubaruTheme',
		array(
			'i18n' => array(
				'close'    => __( 'Close', 'subaru-alkhoory' ),
				'prev'     => __( 'Previous image', 'subaru-alkhoory' ),
				'next'     => __( 'Next image', 'subaru-alkhoory' ),
				'copied'   => __( 'Link copied', 'subaru-alkhoory' ),
				'colour'   => __( 'Colour', 'subaru-alkhoory' ),
				'openImg'  => __( 'Open image', 'subaru-alkhoory' ),
				'gallery'  => __( 'Image gallery', 'subaru-alkhoory' ),
			),
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'subaru_enqueue_assets', 20 );

/**
 * Preconnect to Google Fonts.
 *
 * @param array  $urls          URLs.
 * @param string $relation_type Relation.
 * @return array
 */
function subaru_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'subaru_resource_hints', 10, 2 );

/**
 * Early inline script: marks JS availability, reduced motion and the once-per-session intro
 * before first paint (prevents flashes).
 */
function subaru_head_script() {
	$intro = subaru_setting( 'page_intro' ) ? 1 : 0;
	if ( is_admin() || isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$intro = 0;
	}
	?>
<script id="subaru-head-js">
(function(d){var h=d.documentElement;h.classList.remove('no-js');h.classList.add('sa-js');
try{var rm=window.matchMedia('(prefers-reduced-motion: reduce)').matches;if(rm){h.classList.add('sa-rm');}
if(<?php echo (int) $intro; ?>&&!rm&&!sessionStorage.getItem('saIntro')){h.classList.add('sa-intro');sessionStorage.setItem('saIntro','1');}}catch(e){}})(document);
</script>
	<?php
}
add_action( 'wp_head', 'subaru_head_script', 1 );

/**
 * Add no-js class to <html> (replaced by sa-js inline).
 *
 * @param string $output Attributes.
 * @return string
 */
function subaru_language_attributes( $output ) {
	if ( is_admin() ) {
		return $output;
	}
	return $output . ' class="no-js"';
}
add_filter( 'language_attributes', 'subaru_language_attributes' );

/**
 * Editor: load theme CSS in the Elementor editor preview as well (wp_enqueue_scripts already covers it),
 * and give the block editor the theme fonts.
 */
function subaru_block_editor_assets() {
	wp_enqueue_style( 'subaru-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Michroma&display=swap', array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
}
add_action( 'enqueue_block_editor_assets', 'subaru_block_editor_assets' );
