<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package ialphabet
 */

get_header(); ?>

		<div class="container">
			<div class="row">
				<p class="homepage-quote" style="background:rgba(0,0,0,0.6); color:#fff; font-style: italic; font-size:1em; max-width:350px; position:absolute; padding:10px; margin:30px 0 0 20px;">
				Remember, remember always, that all of us, and you and I especially, are descended from immigrants and revolutionists.
				- Franklin D. Roosevelt
				</p>	
				<img src="/ialphabet/wp-content/themes/ialphabet/img/HomePagePhoto-1235x463.jpg"/>
			</div>
			
			<div class="row">
				<p style="text-align:center; padding:40px 0 30px 0; font-size:1.2em;">
				<b>Al-Bustan Seeds of Culture</b> presents a public art installation featuring the work of artist <b>Wendy Ewald</b> in collaboration with 
				<b>Northeast High School</b> students exploring their immigrant experiences, with a series of free participatory events from September through December 2017 in Philadelphia.
				</p>	
			</div>

			<div class="row" style="border-top:1px solid #ccc;">
			<br>
				<h4>View Alphabet</h4>
				<a href="http://www.albustanseeds.org/ialphabet/alphabet//">
				<img src="http://www.albustanseeds.org/ialphabet/wp-content/uploads/2017/10/alphabet-static-2.png" alt="iAlphabet" /></a>
			</div>
			<br>
			
			<div class="row" style="border-top:1px solid #ccc;">
			<div class="col-lg-6">
			<h4 style="text-align:center; padding:10px 0 40px 0; font-size:1.4em;">Upcoming Events</h4>

			<?php
			global $query_string; //Need this to make pagination work
			/*Setting up our custom query (In here we are setting it to show 12 posts per page and eliminate all sticky posts) */
			query_posts($query_string . '&cat=4');
			if(have_posts()) :	while(have_posts()) :  the_post(); 
			$url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
			?>
			
			
			
  	  	  <div style="padding-bottom:20px;">
				  
			<span style="background:#DD1E27; color:#fff; font-weight:bold; padding:3px 6px 3px 6px;">
				<?php echo get_post_meta($post->ID, "Date", true); ?>
			</span>
			<span style="color:#333; font-weight:bold; padding:2px 8px 2px 8px;">
				<?php echo get_post_meta($post->ID, "Time", true); ?>
			</span>	
			
				  
			<a href="<?php echo get_permalink(); ?>" class="info"><h4 style="font-weight:bold; color:#DD1E27;"><?php the_title(); ?></h4></a>
			
			<h6 style="color:#aaa; font-weight:bold;"><?php echo get_post_meta($post->ID, "Location", true); ?></h6>
  	  	  </div> 
  
			<?php
			endwhile;
			//Pagination can go here if you want it.
			endif;
			?>
			</div>

			<div class="col-lg-6">
			<h4 style="text-align:center; padding:10px 0 40px 0; font-size:1.4em;">News</h4>

			<?php
			global $query_string; //Need this to make pagination work
			/*Setting up our custom query (In here we are setting it to show 12 posts per page and eliminate all sticky posts) */
			query_posts($query_string . '&cat=2');
			if(have_posts()) :	while(have_posts()) :  the_post(); 
			$url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
			?>
			
			
			
  	  	  <div style="padding-bottom:20px;">  
			<a href="<?php echo get_permalink(); ?>" class="info"><h4 style="font-weight:bold; color:#DD1E27;"><?php the_title(); ?></h4></a>			
			<h6 style="color:#aaa; font-weight:bold;"><?php echo get_post_meta($post->ID, "Location", true); ?></h6>
  	  	  </div> 
  
			<?php
			endwhile;
			//Pagination can go here if you want it.
			endif;
			?>
			</div>

			<div class="col-lg-6">
				<div class="newsletter-button middle-page">
				<div class="newsletterLabel">
				<a href="#boxzilla-393">Stay INFORMED! Newsletter</a></div>
				</div>
			</div>


	  	  <h2>Instagram #iAlphabet</h2>
  	  	  <div>
  	  	  	<?php echo do_shortcode("[grace id='1']"); ?>
  	  	  	<br>
  	  	  </div>

			</div>	
			
		</div><!-- .container -->





<?php
get_footer();
