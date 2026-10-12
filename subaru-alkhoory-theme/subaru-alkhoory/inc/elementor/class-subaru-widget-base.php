<?php
/**
 * Shared base for Subaru widgets.
 *
 * @package SubaruAlKhoory
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

/**
 * Base widget.
 */
abstract class Subaru_Widget_Base extends Widget_Base {

	/**
	 * Category.
	 *
	 * @return array
	 */
	public function get_categories(): array {
		return array( 'subaru' );
	}

	/**
	 * Styles.
	 *
	 * @return array
	 */
	public function get_style_depends(): array {
		return array( 'subaru-widgets' );
	}

	/**
	 * Scripts.
	 *
	 * @return array
	 */
	public function get_script_depends(): array {
		return array( 'subaru-widgets' );
	}

	/**
	 * Keywords.
	 *
	 * @return array
	 */
	public function get_keywords(): array {
		return array( 'subaru', 'car', 'model' );
	}

	/**
	 * Render a link-bearing button from a URL control value.
	 *
	 * @param string $text  Label.
	 * @param array  $link  URL control value.
	 * @param string $class Classes.
	 */
	protected function button( $text, $link, $class ) {
		if ( '' === trim( (string) $text ) ) {
			return;
		}
		$url    = ! empty( $link['url'] ) ? $link['url'] : '#';
		$target = ! empty( $link['is_external'] ) ? ' target="_blank"' : '';
		$rel    = array();
		if ( ! empty( $link['is_external'] ) ) {
			$rel[] = 'noopener';
		}
		if ( ! empty( $link['nofollow'] ) ) {
			$rel[] = 'nofollow';
		}
		echo '<a class="' . esc_attr( $class ) . '" href="' . esc_url( $url ) . '"' . $target . ( $rel ? ' rel="' . esc_attr( implode( ' ', $rel ) ) . '"' : '' ) . '>' . esc_html( $text ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static strings.
	}

	/**
	 * Common "theme" (light/dark) control.
	 */
	protected function add_theme_control() {
		$this->add_control(
			'sa_theme',
			array(
				'label'        => __( 'Colour scheme', 'subaru-alkhoory' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'light',
				'options'      => array(
					'light' => __( 'Light', 'subaru-alkhoory' ),
					'dark'  => __( 'Dark (navy)', 'subaru-alkhoory' ),
				),
				'prefix_class' => 'sa-w-theme-',
			)
		);
	}

	/**
	 * Image URL from a media control value.
	 *
	 * @param array  $media Media.
	 * @param string $size  Size.
	 * @return string
	 */
	protected function media_url( $media, $size = 'large' ) {
		if ( ! empty( $media['id'] ) ) {
			$url = wp_get_attachment_image_url( (int) $media['id'], $size );
			if ( $url ) {
				return $url;
			}
		}
		return ! empty( $media['url'] ) ? $media['url'] : '';
	}

	/**
	 * Alt text for a media control value.
	 *
	 * @param array  $media    Media.
	 * @param string $fallback Fallback.
	 * @return string
	 */
	protected function media_alt( $media, $fallback = '' ) {
		if ( ! empty( $media['id'] ) ) {
			$alt = get_post_meta( (int) $media['id'], '_wp_attachment_image_alt', true );
			if ( $alt ) {
				return $alt;
			}
		}
		return $fallback;
	}
}
