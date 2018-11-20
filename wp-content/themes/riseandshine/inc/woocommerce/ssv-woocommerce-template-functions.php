<?php
/**
 * WooCommerce Template Functions.
 *
 * @package ssvtheme
 */

if ( ! function_exists( 'ssv_before_content' ) ) {
	/**
	 * Before Content
	 * Wraps all WooCommerce content in wrappers which match the theme markup
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	function ssv_before_content() {
		?>
			<main id="main" class="site-main" role="main">
		<?php
	}
}

if ( ! function_exists( 'ssv_after_content' ) ) {
	/**
	 * After Content
	 * Closes the wrapping divs
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	function ssv_after_content() {
		?>

			</main><!-- #main -->
		<?php
	}
}

if ( ! function_exists( 'ssv_content_middle' ) ) {
	/**
	 * Before Content
	 * Wraps all WooCommerce content in wrappers which match the theme markup
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	function ssv_content_middle() {
		?>
		<?php
			$contentBottom = get_field('content_top','option');
			if($contentBottom) {
				print do_shortcode($contentBottom);
			}
		?>
		<?php
	}
}
if ( ! function_exists( 'ssv_content_bottom' ) ) {
	/**
	 * Before Content
	 * Wraps all WooCommerce content in wrappers which match the theme markup
	 *
	 * @since   1.0.0
	 * @return  void
	 */
	function ssv_content_bottom() {
		?>
		<?php
			$contentBottom = get_field('content_bottom','option');
			if($contentBottom) {
				print do_shortcode($contentBottom);
			}
		?>
		<?php
	}
}
