<?php
/**
 * AB Productions child theme.
 *
 * @package abp
 */

defined( 'ABSPATH' ) || exit;

define( 'ABP_VERSION', '1.0.0' );
define( 'ABP_DIR', get_stylesheet_directory() );
define( 'ABP_URI', get_stylesheet_directory_uri() );

require_once ABP_DIR . '/inc/helpers.php';
require_once ABP_DIR . '/inc/setup.php';
require_once ABP_DIR . '/inc/customizer.php';
require_once ABP_DIR . '/inc/fonts.php';
require_once ABP_DIR . '/inc/projects.php';
require_once ABP_DIR . '/inc/shortcodes.php';
require_once ABP_DIR . '/inc/elementor.php';
require_once ABP_DIR . '/inc/cf7.php';
