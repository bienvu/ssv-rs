<?php
/**
 * WooCommerce Template Functions.
 *
 * @package ssvtheme
 */




/**
 * Hooked woocommerce_product_tabs alter product tabs.
 */
function riseandshine_default_product_tabs($tabs = array()) {
	global $post, $product;
  // We remove review and addition information tabs.
  unset($tabs['reviews']);
  unset($tabs['additional_information']);
  // We add 2 custom tabs: Shipping and specification.
  if ($product_specification = get_field('_product_specification', $post->ID)) {
    if (!empty($product_specification)) {
      $tabs['specification'] = array(
        'title' => __('features & specifications', 'riseandshine-theme'),
        'priority' => 40,
        'callback' => 'riseandshine_product_specification_tab',
      );
    }
  }
  $tabs['shipping'] = array(
    'title' => __('Delivery & Returns', 'riseandshine-theme'),
    'priority' => 50,
    'callback' => 'riseandshine_product_shipping_tab',
  );
  return $tabs;
}

/**
 * Render specification tab of product.
 */
function riseandshine_product_specification_tab() {
	global $post, $product;
	$product_specification = get_field('_product_specification', $post->ID);
	print $product_specification;
}

/**
 * Render shipping tab of product.
 */
function riseandshine_product_shipping_tab() {
	print '<p>Aenean aliquam diam quis arcu volutpat, et pulvinar mauris molestie. Nunc risus quam, facilisis in ullamcorper sed, vestibulum non augue. Curabitur vel arcu ante. Integer vehicula dapibus interdum. Nam lacus arcu, porta eu condimentum vitae, egestas vitae lectus. Aliquam lobortis, orci ut pulvinar aliquam, ipsum mi posuere lectus, vel pellentesque urna orci in eros. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>';
}

/**
 * Hooked
 */
function riseandshine_product_description_heading($heading) {
	return '';
}

/**
 * Hooked woocommerce_single_product_summary.
 * Render size information in single product summary.
 */
function riseandshine_single_product_size_information() {
	wc_get_template('single-product/size-information.php');
}

/**
 * Hooked woocommerce_share.
 */
function riseandshine_product_share_buttons_payment_brands() {
	wc_get_template('single-product/share-buttons-payment-brands.php');
}

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
		// print_r(woocommerce_get_product_thumbnail('product_list', 0, 0));
		echo "<div class='grid-products__image'>". woocommerce_get_product_thumbnail('product_list', 0, 0)."</div>"; // WPCS: XSS ok.
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
	$price = '<del>was ' . ( is_numeric( $regular_price ) ? wc_price( $regular_price ) : $regular_price ) . '</del> <ins>now ' . ( is_numeric( $sale_price ) ? wc_price( $sale_price ) : $sale_price ) . '</ins>';
	return $price;
}

/**
 * Trim zeros in price decimals
 **/
 add_filter( 'woocommerce_price_trim_zeros', '__return_true' );


 /**
  * Get HTML for a gallery image.
  *
  * Woocommerce_gallery_thumbnail_size, woocommerce_gallery_image_size and woocommerce_gallery_full_size accept name based image sizes, or an array of width/height values.
  *
  * @since 3.3.2
  * @param int  $attachment_id Attachment ID.
  * @param bool $main_image Is this the main image or a thumbnail?.
  * @return string
  */
 function ssv_get_gallery_image_product_html( $attachment_id, $main_image = false ) {
 	$flexslider        = (bool) apply_filters( 'woocommerce_single_product_flexslider_enabled', get_theme_support( 'wc-product-gallery-slider' ) );
 	$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
 	$thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
 	$image_size        = apply_filters( 'woocommerce_gallery_image_size', 'woocommerce_single');
 	$full_size         = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
 	$thumbnail_src     = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
 	$full_src          = wp_get_attachment_image_src( $attachment_id, $full_size );
 	$image             = wp_get_attachment_image( $attachment_id, $image_size, false, array(
 		'data-caption'            => get_post_field( 'post_excerpt', $attachment_id ),
 		'data-src'                => $full_src[0],
 		'data-large_image'        => $full_src[0],
 		'data-large_image_width'  => $full_src[1],
 		'data-large_image_height' => $full_src[2],
 		'class'                   => $main_image ? 'wp-post-image' : '',
 	) );

 	return '<div class="product__image__item easyzoom easyzoom--overlay"><a href="' . esc_url( $full_src[0] ) . '">' . $image . '</a></div>';
 }

 function ssv_get_gallery_image_html( $attachment_id, $main_image = false ) {
 	$flexslider        = (bool) apply_filters( 'woocommerce_single_product_flexslider_enabled', get_theme_support( 'wc-product-gallery-slider' ) );
 	$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
 	$thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
 	$image_size        = apply_filters( 'woocommerce_gallery_image_size', $flexslider || $main_image ? 'woocommerce_single' : $thumbnail_size );
 	$full_size         = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
 	$thumbnail_src     = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
 	$full_src          = wp_get_attachment_image_src( $attachment_id, $full_size );
 	$image             = wp_get_attachment_image( $attachment_id, $image_size, false, array(
 		'data-caption'            => get_post_field( 'post_excerpt', $attachment_id ),
 		'data-src'                => $full_src[0],
 		'data-large_image'        => $full_src[0],
 		'data-large_image_width'  => $full_src[1],
 		'data-large_image_height' => $full_src[2],
 		'class'                   => $main_image ? 'wp-post-image' : '',
 	) );

 	return '<div data-thumb="' . esc_url( $thumbnail_src[0] ) . '" class="product__thumnail__item">' . $image . '</div>';
 }

//Change the breadcrumb separator
function wcc_change_breadcrumb_delimiter( $defaults ) {
    // Change the breadcrumb delimeter from '/' to '>'
    $defaults['delimiter'] = '<span> &gt; </span>';
    return $defaults;
}

// Add filter
add_filter( 'woocommerce_placeholder_img_src', 'riseandshine_custom_woocommerce_placeholder', 10 );
/**
 * Function to return new placeholder image URL.
 */
function riseandshine_custom_woocommerce_placeholder( $image_url ) {
  $image_url = get_template_directory_uri() . '/assets/images/placeholder.jpg';  // change this to the URL to your custom placeholder
  return $image_url;
}
