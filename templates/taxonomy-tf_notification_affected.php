<?php
get_header(); ?>

<div id="main-content" class="main-content">
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

		<?php
			if ( have_posts() ) :
				// Start the Loop.
				while ( have_posts() ) :
					the_post();
					//
					?>
					<h1><?php the_title();?></h1>
					<?php
					the_excerpt();
					?>
					<p><a href="<?php the_permalink();?>">Read more...</a></p>
					<?php 
					//
				endwhile; // end while

			else :
				// If no content, include the "No posts found" template.
				get_template_part( 'content', 'none' );

			endif;
		?>

		</div><!-- #content -->
	</div><!-- #primary -->
	<?php get_sidebar( 'content' ); ?>
</div><!-- #main-content -->

<?php
get_sidebar();
get_footer();
