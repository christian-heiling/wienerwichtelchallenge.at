<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="profile" href="https://gmpg.org/xfn/11" />
        <link href="https://fonts.googleapis.com/css?family=Montserrat&display=swap" rel="stylesheet"> 
	<?php wp_head(); ?>
</head>

<?php
/**
 * @var app\posttypes\AbstractPostType $controller
 */
$controller = \app\App::getInstance()->getController(get_post_type());

?>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
    
<div class="event-countdown-container">
    <?php get_template_part('template-parts/header/event', 'countdown'); ?>
</div>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'twentynineteen' ); ?></a>
        
		<header id="masthead" class="<?php echo ((is_singular() && twentynineteen_can_show_post_thumbnail()) || (!empty($controller))) ? 'site-header featured-image' : 'site-header'; ?>">                    
                    <div class="site-branding-container">
                            <?php get_template_part( 'template-parts/header/site', 'branding' ); ?>
                    </div><!-- .site-branding-container -->

            <?php if (!empty($controller)) : ?>
                <div class="site-featured-image">

                    <figure class="post-thumbnail">
                        <?php
                        $imageId = array_key_first(\app\App::getInstance()->getOptions()->get($controller->getPostType(), 'header_image'));
                        echo wp_get_attachment_image( $imageId, 'full', false, [] );
                        ?>
                    </figure>

                    <?php
                    twentynineteen_post_thumbnail();
                    $classes = 'entry-header';
                    ?>
                    <div class="<?php echo $classes; ?>">
                        <?php if (is_archive()): ?>
                            <?php get_template_part( 'template-parts/header/archive', 'header' ); ?>
                        <?php else: ?>
                            <?php get_template_part( 'template-parts/header/entry', 'header' ); ?>
                        <?php endif; ?>
                    </div><!-- .entry-header -->
                    <?php rewind_posts(); ?>
                </div>
            <?php elseif ( is_singular() && twentynineteen_can_show_post_thumbnail() ) : ?>
				<div class="site-featured-image">
					<?php
						twentynineteen_post_thumbnail();
						the_post();
						$discussion = ! is_page() && twentynineteen_can_show_post_thumbnail() ? twentynineteen_get_discussion_data() : null;

						$classes = 'entry-header';
					if ( ! empty( $discussion ) && absint( $discussion->responses ) > 0 ) {
						$classes = 'entry-header has-discussion';
					}
					?>
					<div class="<?php echo $classes; ?>">
						<?php get_template_part( 'template-parts/header/front', 'page' ); ?>
					</div><!-- .entry-header -->
					<?php rewind_posts(); ?>
				</div>
			<?php endif; ?>
		</header><!-- #masthead -->

	<div id="content" class="site-content">
