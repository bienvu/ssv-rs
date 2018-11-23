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
global $wp_query;
defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

// Prepare some variable.
$term = get_queried_object();
$image = get_field('banner_image', $term);
$color = get_field('color', $term);
$termSlug = $term->slug;
?>

<div class="banner banner--width-content <?php print $color; ?> <?php if($termSlug == 'sale'): ?>banner--sale<?php endif; ?>">
	<div class="banner__image"><?php echo wp_get_attachment_image( $image['ID'], 'full' ); ?></div>
	<div class="banner__wrap">
		<div class="container">
			<div class="banner__body">
				<?php if($termSlug != 'sale'): ?>
					<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
						<h1 class="banner__subtitle"><?php woocommerce_page_title(); ?></h1>
					<?php endif; ?>
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
				<?php if($termSlug != 'sale'): ?>
					<div class="best-advice hidden-on-mobile"><?php  _e( 'best advice. never beaten on price', 'ssvtheme' ); ?></div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<?php
/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action('woocommerce_before_main_content');
?>
<?php if($term->slug != 'bed-in-a-bag'): ?>
	<div class="box-filter is-hidden">
		<div class="container">
			<form class="box-filter__wrap riseandshine-product-category-filter-form" action="" method="get">
				<div class="box-filter__top">
					<h5 class="box-filter__title">Category</h5>
				  <ul>
				    <?php
				      $product_categories = get_terms('product_cat', array(
				        'hide_empty' => 0,
				        'parent' => 0,
								'orderby' => 'term_id',
                'order'   => 'ASC',
								'exclude' => array( 275, 15 ),
				      ));
				    ?>
				    <?php foreach ($product_categories as $key => $product_category): ?>
				    	<li class="<?php if($term->term_id == $product_category->term_id) print 'active'; ?>">
				    		<a href="<?php print get_term_link($product_category); ?>">
				    			<?php print $product_category->name; ?>
				    		</a>
				    	</li>
				    <?php endforeach ?>
				  </ul>
				</div>
				<div class="box-filter__bottom">
					<h5 class="box-filter__title">Filters</h5>
					<div class="box-filter__body">
						<span class="js-show btn"><span>more filters</span></span>
					  <div class="box-filter__aside show">
							<div class="box-filter__item">
	              <div class="container">
									<div class="form-wrap">
										<div class="form-list form-top">
	                    <div class="form-item">
	                      <span class="js-back come-back"><i class="icon-arrow"></i>back</span>
	                      <input type="reset" name="" value="clear all">
	                    </div>
	                  </div>
										<div class="form-list">
	                    <div class="form-item">
	                      <input type="checkbox" name="" id="sale">
	                      <label class="reverse" for="sale">sale</label>
	                    </div>
	                  </div>
										<div class="form-list">
											<!-- Type -->
											<div class="form-item">
	                      <span class="btn js-show"><span>type</span></span>
	                      <div class="box-filter__child form-show show">
	                        <div class="container">
	                          <div class="form-list form-top">
	                            <div class="form-item">
	                              <span class="js-back come-back"> <i class="icon-arrow"></i>back</span>
	                              <input type="reset" name="" value="clear all">
	                            </div>
	                          </div>
	                          <div class="form-list form-caption">
	                            <div class="form-item">
	                              <span>type</span>
	                            </div>
	                          </div>
	                          <div class="form-list">
															<?php
												  			$sub_categories =  get_terms('product_cat', array(
													        'hide_empty' => 0,
													        'parent' => $term->term_id,
													      ));
												  		?>
												  		<?php $default_sub_categories = isset($_GET['types']) ? (array) $_GET['types'] : array(); ?>
															<?php foreach ($sub_categories as $key => $sub_category): ?>
												  			<div class="form-item form-type-checkbox">
												  				<input id="edit-type-<?php print $sub_category->slug; ?>" type="checkbox" name="types[]" value="<?php print $sub_category->slug; ?>" <?php checked(in_array($sub_category->slug, $default_sub_categories), 1); ?> />
												  				<label for="edit-type-<?php print $sub_category->slug; ?>" class="option"> <?php print $sub_category->name; ?></label>
												  			</div>
												  		<?php endforeach; ?>
	                          </div>
	                          <div class="form-list">
	                            <div class="form-item">
	                              <button type="submit" class=" btn btn--lost-icon"><span>go</span></button>
	                            </div>
	                          </div>
	                        </div>
	                      </div>
	                    </div>
											<!-- End type -->
											<!-- Size -->
	                    <div class="form-item">
	                      <span class="btn js-show"><span>size</span></span>
	                      <div class="box-filter__child form-show show">
	                        <div class="container">
	                          <div class="form-list form-top">
	                            <div class="form-item">
	                              <span class="js-back come-back"> <i class="icon-arrow"></i>back</span>
	                              <input type="reset" name="" value="clear all">
	                            </div>
	                          </div>
	                          <div class="form-list form-caption">
	                            <div class="form-item">
	                              <span>size</span>
	                            </div>
	                          </div>
	                          <div class="form-list">
															<?php
												  			$attribute_sizes =  get_terms('pa_size', array(
													        'hide_empty' => 0,
													      ));
												  		?>
												  		<?php $default_attribute_sizes = isset($_GET['sizes']) ? (array) $_GET['sizes'] : array(); ?>
															<?php foreach ($attribute_sizes as $key => $attribute_size): ?>
											  				<div class="form-item form-type-checkbox">
												  				<input id="edit-size-<?php print $attribute_size->slug; ?>" type="checkbox" name="sizes[]" value="<?php print $attribute_size->slug; ?>" <?php checked(in_array($attr_size->slug, $default_attribute_sizes), 1); ?> />
												  				<label for="edit-size-<?php print $attribute_size->slug; ?>" class="option">
												  					<?php print $attribute_size->name; ?>
												  				</label>
												  			</div>
											  			<?php endforeach; ?>
	                          </div>
	                          <div class="form-list">
	                            <div class="form-item">
	                              <button type="submit" class=" btn btn--lost-icon"><span>go</span></button>
	                            </div>
	                          </div>
	                        </div>
	                      </div>
	                    </div>
											<!-- End Size -->
											<!-- comfort -->
	                    <div class="form-item">
	                      <span class="btn js-show"><span>comfort</span></span>
												<div class="box-filter__child form-show show">
	                        <div class="container">
	                          <div class="form-list form-top">
	                            <div class="form-item">
	                              <span class="js-back come-back"> <i class="icon-arrow"></i>back</span>
	                              <input type="reset" name="" value="clear all">
	                            </div>
	                          </div>
	                          <div class="form-list form-caption">
	                            <div class="form-item">
	                              <span>size</span>
	                            </div>
	                          </div>
	                          <div class="form-list">
															<?php
												  			$attribute_comforts =  get_terms('pa_comfort', array(
													        'hide_empty' => 0,
													      ));
												  		?>
												  		<?php $default_attribute_comforts = isset($_GET['comforts']) ? (array) $_GET['comforts'] : array(); ?>
											  			<?php foreach ($attribute_comforts as $key => $attribute_comfort): ?>
											  				<div class="form-item form-type-checkbox">
												  				<input id="edit-comfort-<?php print $attribute_comfort->slug; ?>" type="checkbox" name="comforts[]" value="<?php print $attribute_comfort->slug; ?>" <?php checked(in_array($attribute_comfort->slug, $default_attribute_comforts), 1); ?> />
												  				<label for="edit-comfort-<?php print $attribute_comfort->slug; ?>" class="option">
												  					<?php print $attribute_comfort->name; ?>
												  				</label>
												  			</div>
											  			<?php endforeach; ?>
	                          </div>
	                          <div class="form-list">
	                            <div class="form-item">
	                              <button type="submit" class=" btn btn--lost-icon"><span>go</span></button>
	                            </div>
	                          </div>
	                        </div>
	                      </div>
	                    </div>
											<!-- End comfort -->
										</div>
								  	<div class="product-price--range form-list form-2col">
								  		<div class="form-item form-type-text">
								  			<input type="text" name="_price_from" placeholder="<?php print __('$ min', 'riseandshine'); ?>" value="<?php if(isset($_GET['_price_from'])) print $_GET['_price_from']; ?>" />
								  		</div>
								  		<div class="form-item form-type-text">
								  			<input type="text" name="_price_to" placeholder="<?php print __('$ max', 'riseandshine'); ?>" value="<?php if(isset($_GET['_price_to'])) print $_GET['_price_to']; ?>" />
								  		</div>
								  	</div>
										<div class="form-list">
									  	<div class="form-submit form-item">
									  		<input type="submit" name="submit-filter" value="Go" class="btn btn--lost-icon">
									  	</div>
									  </div>
								  </div>
							  </div>
						  </div>
					  </div>
						<!-- <div class="box-filter__sort">
	            <span class="js-show btn"><span>sort: recommended</span></span>

	            <ul class="show">
	              <li><a href="#">1</a></li>
	              <li><a href="#">2</a></li>
	              <li><a href="#">3</a></li>
	            </ul>
	          </div> -->
				  </div>
				</div>
			</form>
			<div class="box-filter__sort">
				<?php
				/**
				 * Hook: woocommerce_before_shop_loop.
				 *
				 * @hooked woocommerce_output_all_notices - 10
				 * @hooked woocommerce_result_count - 20
				 * @hooked woocommerce_catalog_ordering - 30
				 */
				do_action( 'woocommerce_before_shop_loop' );
				?>
			</div>
		</div>
	</div>

	<?php
		print '<div class="grid-products"><div class="container">';
		if ( woocommerce_product_loop() ) {

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
		print '</div></div>';
	?>
<?php endif; ?>
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
<?php
/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );
?>

<!-- Footer -->
<?php get_footer( 'shop' ); ?>
