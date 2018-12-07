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
$imageMobile = get_field('banner_mobile', $term);
$color = get_field('color', $term);
$categoryDescription = get_field('category_description', $term);
$termSlug = $term->slug;
?>
<div class="banner-wrap">
	<div class="banner banner--width-content <?php print $color; ?> <?php if($termSlug == 'sale'): ?>banner--sale<?php endif; ?>">
		<div class="banner__image">
			<span class="hidden-on-mobile desktop-img">
				<?php
					if($image) {
						echo wp_get_attachment_image( $image['ID'], 'full' );
					} else {
						echo '<img width="1366" height="487" src="'.get_template_directory_uri().'/assets/images/banner.jpg" alt="rise+shine image">';
					}
				?>
			</span>
			<span class="hidden-on-tablet mobile-img">
				<?php
					if($imageMobile) {
						echo wp_get_attachment_image( $imageMobile['ID'], 'full' );
					} else {
						if($image) {
							echo wp_get_attachment_image( $image['ID'], 'full' );
						} else {
							echo '<img width="1366" height="487" src="'.get_template_directory_uri().'/assets/images/banner_mobile.jpg" alt="rise+shine image">';
						}
					}
				?>
			</span>
			<?php if($termSlug == 'sale'): ?>
				<div class="scroll-element">
					<i class="icon-arrow-down js-scroll-down"></i>
				</div>
			<?php endif; ?>
		</div>
		<div class="banner__wrap">
			<div class="container">
				<div class="banner__body">
					<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
						<h1 class="banner__subtitle"><?php woocommerce_page_title(); ?></h1>
					<?php endif; ?>
					<?php if($categoryDescription): ?>
						<div class="banner__content">
							<div class="banner__description text--large">
								<?php echo $categoryDescription; ?>
							</div>
							<?php if($termSlug == 'bed-in-a-bag'): ?>
								<div class="banner__link">
		              <a href="/sale" class="btn" tabindex="0"><span>discover</span></a>
		            </div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<?php if($termSlug != 'sale' && $termSlug != 'bed-in-a-bag'): ?>
						<div class="best-advice hidden-on-mobile"><?php  _e( 'best advice. never beaten on price', 'ssvtheme' ); ?></div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<?php if($termSlug != 'bed-in-a-bag'): ?>
		<div class="scroll-element">
			<i class="icon-arrow-down js-scroll-down"></i>
		</div>
	<?php endif; ?>
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

	<div class="box-filter is-hidden">
		<div id="filter"></div>
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
								'exclude' => array( 15 ),
				      ));
				    ?>
				    <?php foreach ($product_categories as $key => $product_category): ?>
				    	<li class="<?php if($term->term_id == $product_category->term_id) print 'active'; ?>">
				    		<a href="<?php print get_term_link($product_category); ?>#filter">
				    			<?php
				    				$str_filter = $product_category->name;
				    				$arr_filter = explode(" ", $str_filter);
				    				$str_filter = implode("<br />", $arr_filter);
				    				echo $str_filter;
				    			?>
				    		</a>
				    	</li>
				    <?php endforeach ?>
				  </ul>
				</div>
				<div class="box-filter__bottom">
					<h5 class="box-filter__title">Filter</h5>
					<div class="box-filter__body">
						<span class="js-show btn"><span>more filters</span></span>
					  <div class="box-filter__aside show">
							<div class="box-filter__item">
	              <div class="container">
									<div class="form-wrap">
										<div class="form-list form-top">
	                    <div class="form-item">
	                      <span class="js-back come-back"><i class="icon-arrow-right"></i>back</span>
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
											<?php
								  			$sub_categories =  get_terms('product_cat', array(
									        'hide_empty' => 1,
									        'parent' => $term->term_id,
									      ));
								  		?>
											<div class="form-item <?php if (count($sub_categories) <= 1) print 'disabled'; ?>">
	                      <span class="btn js-show"><span>type</span></span>
	                      <div class="box-filter__child form-show show">
	                        <div class="container">
	                          <div class="form-list form-top">
	                            <div class="form-item">
	                              <span class="js-back come-back"> <i class="icon-arrow-right"></i>back</span>
	                              <input type="reset" name="" value="clear all">
	                            </div>
	                          </div>
	                          <div class="form-list form-caption">
	                            <div class="form-item">
	                              <span>type</span>
	                            </div>
	                          </div>
	                          <?php if (count($sub_categories) >= 2): ?>
		                          <div class="form-list">
												  			<?php $default_sub_categories = isset($_GET['types']) ? (array) $_GET['types'] : array(); ?>
																<?php foreach ($sub_categories as $key => $sub_category): ?>
													  			<div class="form-item form-type-checkbox">
													  				<input id="edit-type-<?php print $sub_category->slug; ?>" type="checkbox" name="types[]" value="<?php print $sub_category->slug; ?>" <?php checked(in_array($sub_category->slug, $default_sub_categories), 1); ?> />
													  				<label for="edit-type-<?php print $sub_category->slug; ?>" class="option"> <?php print $sub_category->name; ?></label>
													  			</div>
													  		<?php endforeach; ?>
		                          </div>
	                          <?php endif ?>
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
	                              <span class="js-back come-back"> <i class="icon-arrow-right"></i>back</span>
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
												  				<input id="edit-size-<?php print $attribute_size->slug; ?>" type="checkbox" name="sizes[]" value="<?php print $attribute_size->slug; ?>" <?php checked(in_array($attribute_size->slug, $default_attribute_sizes), 1); ?> />
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
	                              <span class="js-back come-back"> <i class="icon-arrow-right"></i>back</span>
	                              <input type="reset" name="" value="clear all">
	                            </div>
	                          </div>
	                          <div class="form-list form-caption">
	                            <div class="form-item">
	                              <span>comfort</span>
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
								  			<input type="number" name="_price_from" placeholder="<?php print __('$ min', 'riseandshine'); ?>" value="<?php if(isset($_GET['_price_from'])) print $_GET['_price_from']; ?>" />
								  		</div>
								  		<div class="form-item form-type-text">
								  			<input type="number" name="_price_to" placeholder="<?php print __('$ max', 'riseandshine'); ?>" value="<?php if(isset($_GET['_price_to'])) print $_GET['_price_to']; ?>" />
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
				$i = 0;
				while ( have_posts() ) {
					the_post();

					/**
					 * Hook: woocommerce_shop_loop.
					 *
					 * @hooked WC_Structured_Data::generate_product_data() - 10
					 */
					do_action( 'woocommerce_shop_loop' );

					wc_get_template_part( 'content', 'product' );
					$i++;
					if ($i == 5 || $i == 10) {
						include get_template_directory() . '/templates/content-promotion.php';
					}
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
