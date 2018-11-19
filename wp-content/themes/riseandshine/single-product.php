<?php get_header(); ?>
	<main role="main">
		<div class="container">
				<?php if (have_posts()): while (have_posts()) : the_post(); ?>

				<!-- article -->
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<!-- post thumbnail -->
					<?php if ( has_post_thumbnail()) : // Check if Thumbnail exists ?>
						<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
							<?php the_post_thumbnail(); // Fullsize image for the single post ?>
						</a>
					<?php endif; ?>
					<!-- /post thumbnail -->

					<!-- post title -->
					<h1><?php the_title(); ?></h1>
					<!-- /post title -->

					<?php the_content(); // Dynamic Content ?>
				</article>
				<!-- /article -->

			<?php endwhile; ?>

			<?php else: ?>

				<!-- article -->
				<article>

					<h1><?php _e( 'Sorry, nothing to display.', 'ssvtheme' ); ?></h1>

				</article>
				<!-- /article -->
			<?php endif; ?>
		</div>
	</main>


<?php get_footer(); ?>
