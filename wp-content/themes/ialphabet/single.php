<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package ialphabet
 */

get_header(); ?>

	<div id="primary" class="container">
		<main id="main" class="site-main">
		
		<?php
		while ( have_posts() ) : the_post();
		?>

			<div class="row" style="padding:0; margin:0;">
				<div class="col-lg-5">
					
					<img src="<?php echo wp_get_attachment_url( get_post_thumbnail_id($post->ID) ); ?>" />
				</div>
				<div class="col-lg-7">
				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

				<?php if (in_category( '1' )) : ?>

	
				<span style="background:#DD1E27; color:#fff; font-size:2em; font-weight:bold; padding:3px 6px 3px 6px;">
				<?php echo get_post_meta($post->ID, "Date", true); ?>
			</span>
			<br>
			<span style="color:#333; font-weight:bold; font-size:2em; padding:2px 8px 2px 8px;">
				<?php echo get_post_meta($post->ID, "Time", true); ?>
			</span>
			<h3 style="color:#aaa; font-weight:bold;"><?php echo get_post_meta($post->ID, "Location", true); ?></h3>
			<?php endif;?>
				</div>
			</div>
			
			<div class="row" style="padding:0; margin:20px 0 50px 0;">
			<?php
			the_content( sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'ialphabet' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			) );

			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'ialphabet' ),
				'after'  => '</div>',
			) );
			?>
			</div>


		<?php			

			the_post_navigation();

		endwhile; // End of the loop.
		?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
