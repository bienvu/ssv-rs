<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.1
 */

defined( 'ABSPATH' ) || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}

global $product;

$post_thumbnail_id = $product->get_image_id();
$attachment_ids = $product->get_gallery_image_ids();
$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
$thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
$image = wp_get_attachment_image( $post_thumbnail_id, 'woocommerce_single' );
$imageThumbnail = wp_get_attachment_image( $post_thumbnail_id, $thumbnail_size );
$imageFull = wp_get_attachment_image_src( $post_thumbnail_id, 'woocommerce_product_thumbnails_large_size' );
?>

<div class="product__thumnail js-gallery-thumbnail">
	<?php
		if ( $attachment_ids && $product->get_image_id() ) {
			foreach ( $attachment_ids as $attachment_id ) {
				echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', ssv_get_gallery_image_html( $attachment_id ), $attachment_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
			}
		} else {
			echo '<div class="product__thumnail__item">'. $imageThumbnail .'</div>';
		}
	 ?>
</div>
<div class="product__images js-gallery">
	<?php
		if ( $attachment_ids && $product->get_image_id() ) {
			foreach ( $attachment_ids as $attachment_id ) {
				echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', ssv_get_gallery_image_product_html( $attachment_id ), $attachment_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
			}
		} else {
		?>
			<div class='product__image__item easyzoom easyzoom--overlay'><a href="<?php echo $imageFull[0] ; ?>"><?php echo $image; ?></a></div>;
		<?php } ?>
</div>
