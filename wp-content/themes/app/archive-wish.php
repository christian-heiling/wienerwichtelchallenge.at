<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */

get_header();

/**
 * @var app\posttypes\SponsorPostType $controller
 */
$controller = \app\App::getInstance()->getController(get_post_type());

	?>

	<section id="primary" class="content-area event-archive">
		<main id="main" class="site-main">

                        <?php get_template_part( 'template-parts/content/wish', 'filter' ); ?>
                        
			<?php if ( have_posts() ) : ?>

				<article class="hentry entry">
							
				<?php
				// Start the Loop.
				$i = 0;

				while ( have_posts() ) :
					$rows = 2;
					if ($i % $rows == 0) {
						echo '<div class="entry-content"><div class="wp-block-columns">';
					}
					echo '<div class="wp-block-column">';
					$i++;

					the_post();

					/*
					 * Include the Post-Format-specific template for the content.
					 * If you want to override this in a child theme, then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
					get_template_part( 'template-parts/content/content', 'excerpt' );

					// End the loop.

					echo '</div>';
					if ($i % $rows == 0) {
						echo '</div></div>';
					}
				endwhile;
			endif;
			?>
                    </article>                    
		</main><!-- #main -->
	</section><!-- #primary -->

	<?php

get_footer();
