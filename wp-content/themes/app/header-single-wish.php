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
    

<?php get_template_part('template-parts/header/event', 'countdown'); ?>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'twentynineteen' ); ?></a>
        
		<header id="masthead" class="site-header featured-image">                    
                    <div class="site-branding-container">
                        <?php get_template_part( 'template-parts/header/site', 'branding' ); ?>
                    </div><!-- .site-branding-container -->

                <div class="site-featured-image">

                    <figure class="post-thumbnail">
                        <?php
                        $imageId = \app\App::getInstance()->getOptions()->get('header_image');
                        echo wp_get_attachment_image( $imageId, 'full' , false, [] );
                        ?>
                    </figure>

                    <?php
                    twentynineteen_post_thumbnail();
                    $classes = 'entry-header';
                    ?>
                    <div class="<?php echo $classes; ?>">
                        <?php get_template_part( 'template-parts/header/single-wish', 'header' ); ?>
                    </div><!-- .entry-header -->
                    <?php rewind_posts(); ?>
                </div>

		</header><!-- #masthead -->

	<div id="content" class="site-content">
