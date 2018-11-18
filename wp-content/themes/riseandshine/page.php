<?php get_header(); ?>
	<main role="main" class="main">
		<?php if( have_rows('components') ): ?>
			<?php get_template_part('components'); ?>
		<?php else: ?>
		<div class="container">
			<h1><?php the_title(); ?></h1>

			<?php if (have_posts()): while (have_posts()) : the_post(); ?>

				<!-- article -->
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<?php the_content(); ?>

					<?php comments_template( '', true ); // Remove if you don't want comments ?>

					<br class="clear">

					<?php edit_post_link(); ?>

				</article>
				<!-- /article -->

			<?php endwhile; ?>

			<?php else: ?>

				<!-- article -->
				<article>
					<div class="container">
						<h2><?php _e( 'Sorry, nothing to display.', 'html5blank' ); ?></h2>
					</div>
				</article>
				<!-- /article -->

			<?php endif; ?>
		</div>
	<?php endif; ?>
	</main>
<?php get_footer(); ?>
