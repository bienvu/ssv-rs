<?php get_header(); ?>
<?php
	$termId = get_queried_object()->term_id;
	$childrenData = get_term_children($termId, 'category');
	$term = get_queried_object();
	$image = get_field('category_banner', $term);
	print_r($term);
?>
bien
	<main role="main" class="<?php if($childrenData):?>sub-category-page<?php else: ?>category-page<?php endif; ?>">
		<div class="banner banner--width-content">
		  <div class="banner__image"><?php echo wp_get_attachment_image( $image['ID'], 'full' ); ?></div>

		  <div class="banner__wrap">
		    <div class="container">
		      <div class="banner__body">
		        <h2 class="banner__subtitle bg--dark-red">mattresses</h2>

		        <div class="banner__content">
		          <div class="banner__description text--large">
		            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla cillum dolore eu pariatur.</p>

		            <p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium.</p>
		          </div>
		        </div>

		        <div class="best-advice hidden-on-mobile">
		          <a href="#">best advice. never beaten on price</a>
		        </div>
		      </div>
		    </div>
		  </div>
		</div>


	</main>
<?php get_footer(); ?>
