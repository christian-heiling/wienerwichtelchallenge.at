<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */

?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer">

            <div class="wp-block-columns">
                <div class="wp-block-column">
                    
                    <?php echo app\App::getInstance()->getOptions()->get('footer'); ?>
                    
                    <?php if ( has_nav_menu( 'social' ) ) : ?>
                    <nav class="social-navigation" aria-label="<?php esc_attr_e( 'Social Links Menu', 'twentynineteen' ); ?>">
                            <?php
                            wp_nav_menu(
                                    array(
                                            'theme_location' => 'social',
                                            'menu_class'     => 'social-links-menu',
                                            'link_before'    => '<span class="screen-reader-text">',
                                            'link_after'     => '</span>' . twentynineteen_get_icon_svg( 'link' ),
                                            'depth'          => 1,
                                    )
                            );
                            ?>
                    </nav><!-- .social-navigation -->
                    <?php endif; ?>
                </div>
            
                <div class="wp-block-column">
                    <h3><?php echo __('Our supporters', 'app'); ?></h3>

                    <ul>
                    <?php 
                    $controller = app\App::getInstance()->getSponsorController();
                    foreach($controller->getTypes() as $type) {
                        $controller->queryByType($type);
                        while ( have_posts() ) {
                            the_post();
                            echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
                        }
                    }
                    ?>
                    </ul>
                </div>

                <div class="wp-block-column">
                    <h3><?php echo __('We support', 'app'); ?></h3>

                    <ul>
                    <?php 
                    $controller = app\App::getInstance()->getSocialOrganisationController();
                    query_posts(array(
                            'post_type' => $controller->getPostType(),
                            'orderby' => 'title',
                            'posts_per_page' => -1,
                            'orderby' => 'title',
                            'order' => 'ASC'
                    ));

                    while ( have_posts() ) {
                        the_post();
                        echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
                    }
                    ?>
                    </ul>
                </div>
        </div>
            
	<div class="site-info">
            <?php $blog_info = get_bloginfo( 'name' ); ?>
            <?php if ( ! empty( $blog_info ) ) : ?>
                    <?php bloginfo( 'name' ); ?> © <?php echo app\App::getInstance()->getOptions()->get('copyright_year'); ?> - <?php echo date("Y"); ?>
            <?php endif; ?>
        </div>
        </footer>

</div><!-- #page -->

<?php wp_reset_query(); ?>
<?php wp_footer(); ?>

</body>
</html>
