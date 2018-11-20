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
			$attribute_sizes =  get_terms('pa_size', array(
				'hide_empty' => 0,
			));
			?>
			<?php if($attribute_sizes): ?>
				<h5 class="grid-products__sub-title">
					<?php foreach ($attribute_sizes as $key => $attribute_size): print $attribute_size->name; endforeach; ?>
				</h5>
			<?php endif; ?>
		<?php
	}
}

add_filter( 'woocommerce_get_price_html', 'ssv_woocommerce_price_html', 100, 2 );
function ssv_woocommerce_price_html( $price, $product ){
    return 'was ' . str_replace( '<ins>', '  <ins>now ', $price );
}

/**
 * Trim zeros in price decimals
 **/
 add_filter( 'woocommerce_price_trim_zeros', '__return_true' );
