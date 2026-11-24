<?php
/**
 * Plugin Name:       Levanto Home FX
 * Description:       Motion layer for the Levanto Elementor home page: animated background patterns, marquee, product accordion, hover transitions, counters, sticky nav and mobile menu. Works on any page that uses the lv-* global classes.
 * Version:           1.2.2
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Levanto
 * License:           GPL-2.0-or-later
 * Text Domain:       levanto-home-fx
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LEVANTO_FX_VERSION', '1.2.2' );

add_action(
	'wp_enqueue_scripts',
	function () {
		$base = plugin_dir_url( __FILE__ ) . 'assets/';
		wp_enqueue_style( 'levanto-fx', $base . 'levanto-fx.css', array(), LEVANTO_FX_VERSION );
		wp_enqueue_script( 'levanto-fx', $base . 'levanto-fx.js', array(), LEVANTO_FX_VERSION, true );
	},
	20
);

// Inter is referenced through the lv-body font variable; make sure it is always available.
add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style( 'levanto-fx-inter', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap', array(), null );
	},
	5
);
