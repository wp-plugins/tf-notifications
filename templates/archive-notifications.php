<?php
get_header(); ?>

	<section id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<?php if ( have_posts() ) : ?>

			<?php
					// Start the Loop.
					while ( have_posts() ) :
						the_post();
						$class = $c == 0 ? "even" : "odd";
						$return_string = "";
						?>
						<div class="tf-notification-item <?php echo $class;?>">
							<h3><?php the_title();?></h3>
							<?php
							if(get_post_meta($post->ID, 'tf_start_date', true ))
								$return_string .= '<div class="tf-notification-date">'.date("D j M, Y - g:ia", strtotime(get_post_meta($post->ID, 'tf_start_date', true )));
							
							if(get_post_meta($post->ID, 'tf_start_date', true ) && get_post_meta($post->ID, 'tf_end_date', true ))
								$return_string .= " to ".date("D j M, Y - g:ia", strtotime(get_post_meta($post->ID, 'tf_end_date', true )));
							
							if(get_post_meta($post->ID, 'tf_start_date', true ))
								$return_string .= "</div>";
										
							echo $return_string;
							
							the_excerpt();
							?>
							<p><a href="<?php the_permalink();?>">Read more...</a></p>
						</div>
						<?php 
						$c = $c == 0 ? 1 : 0;
					endwhile;
					// Previous/next page navigation.
					twentyfourteen_paging_nav();

				else :
					// If no content, include the "No posts found" template.
					get_template_part( 'content', 'none' );

				endif;
			?>
		</div><!-- #content -->
	</section><!-- #primary -->

<?php
get_sidebar( 'content' );
get_sidebar();
get_footer();
