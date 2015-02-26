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
						if(get_post_meta($post->ID, 'tf_start_date', true ))
							$return_string .= '<div class="tf-notification-date">'.date("D j M, Y - g:ia", strtotime(get_post_meta($post->ID, 'tf_start_date', true )));
						
						if(get_post_meta($post->ID, 'tf_start_date', true ) && get_post_meta($post->ID, 'tf_end_date', true ))
							$return_string .= " to ".date("D j M, Y - g:ia", strtotime(get_post_meta($post->ID, 'tf_end_date', true )));
						
						if(get_post_meta($post->ID, 'tf_start_date', true ))
							$return_string .= "</div>";
									
						echo $return_string;
					the_content();
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
