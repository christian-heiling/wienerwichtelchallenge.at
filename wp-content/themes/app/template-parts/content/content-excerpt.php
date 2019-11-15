<?php
/**
 * Template part for displaying post archives and search results
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */

/**
 * @var app\posttypes\AbstractPostType $controller
 */
$controller = \app\App::getInstance()->getController(get_post_type());
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php
		if ( is_sticky() && is_home() && ! is_paged() ) {
			printf( '<span class="sticky-post">%s</span>', _x( 'Featured', 'post', 'twentynineteen' ) );
		}
		the_title( sprintf( '<h3 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h3>' );
                
                if (!empty($controller)) {
                    echo '<div class="entry-meta">';
                    $controller->echoExcerptMeta();
                    echo '</div>';
                } else {
                    twentynineteen_post_thumbnail();
                }
                
		?>
	</header><!-- .entry-header -->

        <?php
        if (empty($controller)) {
            twentynineteen_post_thumbnail();
        }
        ?>

	<div class="entry-content">
            <?php
                if (!empty($controller)) {
                    $controller->echoExcerptContent();
                } else {
                    the_excerpt();
                }
                ?>
	</div><!-- .entry-content -->
</article><!-- #post-<?php the_ID(); ?> -->
