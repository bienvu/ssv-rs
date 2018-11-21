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

if ( ! function_exists( 'ssv_woocommerce_template_loop_product_thumbnail' ) ) {

	/**
	 * Get the product thumbnail for the loop.
	 */
	function ssv_woocommerce_template_loop_product_thumbnail() {
		echo "<div class='grid-products__image'>". woocommerce_get_product_thumbnail()."</div>"; // WPCS: XSS ok.
	}
}

if ( ! function_exists( 'ssv_woocommerce_template_loop_product_title' ) ) {

	/**
	 * Show the product title in the product loop. By default this is an H2.
	 */
	function ssv_woocommerce_template_loop_product_title() {
		echo '<h4 class="grid-products__title">' . get_the_title() . '</h4>';
	}
}

if ( ! function_exists( 'ssv_woocommerce_product_size' ) ) {

	/**
	 * Show the product title in the product loop. By default this is an H2.
	 */
	function ssv_woocommerce_product_size() {
		?>
		<?php
			global $product;
			$sizes = $product->get_attribute('pa_size');
		?>
		<?php if(!empty($sizes)): ?>
			<h5 class="grid-products__sub-title">
				<?php print $sizes; ?>
			</h5>
		<?php endif; ?>
		<?php
	}
}

add_filter( 'woocommerce_format_sale_price', 'ssv_woocommerce_format_sale_price', 99, 3);
function ssv_woocommerce_format_sale_price($price, $regular_price, $sale_price){
	$price = '<del>was ' . ( is_numeric( $regular_price ) ? wc_price( $regular_price ) : $regular_price ) . '</del> <ins> now' . ( is_numeric( $sale_price ) ? wc_price( $sale_price ) : $sale_price ) . '</ins>';
	return $price;
}

/**
 * Trim zeros in price decimals
 **/
 add_filter( 'woocommerce_price_trim_zeros', '__return_true' );
