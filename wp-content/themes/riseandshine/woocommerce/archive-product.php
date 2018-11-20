<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );
$term = get_queried_object();
$image = get_field('banner_image', $term);
$color = get_field('color', $term);
?>
<div class="banner banner--width-content <?php print $color; ?>">
	<div class="banner__image"><?php echo wp_get_attachment_image( $image['ID'], 'full' ); ?></div>

	<div class="banner__wrap">
		<div class="container">
			<div class="banner__body">
				<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
					<h1 class="banner__subtitle"><?php woocommerce_page_title(); ?></h1>
				<?php endif; ?>


				<div class="banner__content">
					<div class="banner__description text--large">
						<?php
						/**
						 * Hook: woocommerce_archive_description.
						 *
						 * @hooked woocommerce_taxonomy_archive_description - 10
						 * @hooked woocommerce_product_archive_description - 10
						 */
						do_action( 'woocommerce_archive_description' );
						?>
					</div>
				</div>

				<div class="best-advice hidden-on-mobile"><?php  _e( 'best advice. never beaten on price: ', 'ssvtheme' ); ?></div>
			</div>
		</div>
	</div>
</div>
<main class="main">

	<?php
	if(($term->slug != "bed-in-a-bag") || ($term->term_id != 157) ) {
		print('<div class="container">');
		if ( woocommerce_product_loop() ) {

			/**
			 * Hook: woocommerce_before_shop_loop.
			 *
			 * @hooked woocommerce_output_all_notices - 10
			 * @hooked woocommerce_result_count - 20
			 * @hooked woocommerce_catalog_ordering - 30
			 */
			do_action( 'woocommerce_before_shop_loop' );

			woocommerce_product_loop_start();

			if ( wc_get_loop_prop( 'total' ) ) {
				while ( have_posts() ) {
					the_post();

					/**
					 * Hook: woocommerce_shop_loop.
					 *
					 * @hooked WC_Structured_Data::generate_product_data() - 10
					 */
					do_action( 'woocommerce_shop_loop' );

					wc_get_template_part( 'content', 'product' );
				}
			}

			woocommerce_product_loop_end();

			/**
			 * Hook: woocommerce_after_shop_loop.
			 *
			 * @hooked woocommerce_pagination - 10
			 */
			do_action( 'woocommerce_after_shop_loop' );
		} else {
			/**
			 * Hook: woocommerce_no_products_found.
			 *
			 * @hooked wc_no_products_found - 10
			 */
			do_action( 'woocommerce_no_products_found' );
		}

		/**
		 * Hook: woocommerce_after_main_content.
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );
		print('</div>');
	}
	?>

	<!-- Get component data -->
	<?php
	$term = get_queried_object();
		if( have_rows('components', $term) ):
		  // loop through the rows of data
		  while ( have_rows('components', $term) ) : the_row();
				wc_get_template( '../templates/components.php' );
			endwhile;
		endif;
	?>
</main>

<!-- Footer -->
<?php get_footer( 'shop' ); ?>
