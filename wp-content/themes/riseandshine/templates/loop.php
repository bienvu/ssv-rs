<?php if (have_posts()): while (have_posts()) : the_post(); ?>

	<!-- article -->
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<!-- post thumbnail -->
			<?php if ( has_post_thumbnail()) : // Check if thumbnail exists ?>
				<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
					<?php the_post_thumbnail(array(120,120)); // Declare pixel size you need inside the array ?>
				</a>
			<?php endif; ?>
			<!-- /post thumbnail -->

			<!-- post title -->
			<h3>
				<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
			</h3>
			<!-- /post title -->

			<!-- post details -->
			<span class="date"><?php the_time('F j, Y'); ?> <?php the_time('g:i a'); ?></span>
			<!-- <span class="author"><?php _e( 'Published by', 'sentiustheme' ); ?> <?php the_author_posts_link(); ?></span> -->
			<span class="comments"><?php if (comments_open( get_the_ID() ) ) comments_popup_link( __( 'Leave your thoughts', 'sentiustheme' ), __( '1 Comment', 'sentiustheme' ), __( '% Comments', 'sentiustheme' )); ?></span>
			<!-- /post details -->

			<?php sentiusvn_excerpt('sentiusvn_index'); // Build your custom callback length in functions.php ?>
	</article>
	<!-- /article -->

<?php endwhile; ?>

<?php else: ?>

	<!-- article -->
	<article>
		<h3><?php _e( 'Sorry, nothing to display.', 'sentiustheme' ); ?></h3>
	</article>
	<!-- /article -->

<?php endif; ?>
