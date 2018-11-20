<?php get_header(); ?>
	<main role="main" class="main-content main-space page-search">
		<!-- section -->
		<section class="container">

			<h1><?php echo sprintf( __( '%s Search Results for ', 'ssvtheme' ), $wp_query->found_posts ); echo get_search_query(); ?></h1>
			<div class="search-wrap">
				<?php get_template_part('templates/searchform'); ?>
			</div>
			<?php get_template_part('templates/loop'); ?>
			<?php get_template_part('templates/pagination'); ?>
		</section>
		<!-- /section -->
	</main>
<?php get_footer(); ?>
