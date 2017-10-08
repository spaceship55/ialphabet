<?php
/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package ialphabet
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="container">
			<h3 style="text-align:center; margin:30px 0 50px 0;">Events</h3>
		<div class="row">	
		<?php
			$counter = 1; //start counter
			global $query_string; //Need this to make pagination work
			/*Setting up our custom query (In here we are setting it to show 12 posts per page and eliminate all sticky posts) */
			query_posts($query_string . '&cat=1');
			if(have_posts()) :	while(have_posts()) :  the_post(); 
			$url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
			?>
			
			
			
  	  	  <div class="col-sm-6 col-md-6 col-xs-12" style="padding:20px; min-height:350px; border:1px solid #ccc;">

			<div class="col-lg-5">	  
			<span style="background:#DD1E27; color:#fff; font-weight:bold; padding:3px 6px 3px 6px;">
				<?php echo get_post_meta($post->ID, "Date", true); ?>
			</span><br>
			<span style="color:#333; font-weight:bold; padding:2px 8px 2px 8px;">
				<?php echo get_post_meta($post->ID, "Time", true); ?>
			</span>	
			
				  
			<a href="<?php echo get_permalink(); ?>" class="info"><h4 style="font-weight:bold; color:#DD1E27;"><?php the_title(); ?></h4></a>
			
			<h6 style="color:#aaa; font-weight:bold;"><?php echo get_post_meta($post->ID, "Location", true); ?></h6>
			<?php the_excerpt(); ?>
			</div>

			<div class="col-lg-7">
			<a href="<?php echo get_permalink(); ?>"><img src="<?php echo $url ?>" /></a>
			</div>
  	  	  </div> 
  
			<?php
			$counter++;
			endwhile;
			//Pagination can go here if you want it.
			endif;
			?>

			</div>
			<br><br>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
