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
?>
<section id="primary" class="content-area event-archive">
    <main id="main" class="site-main">
        <article class="hentry entry">

            <?php 
            $flash = \app\App::getInstance()->getFlash();
            if (!empty($flash)) {
                echo '<div class="entry-content"><div class="ad">' . $flash . '</div></div>';
            }
            ?>
            
            <div class="entry-content">
                <?php echo do_shortcode(app\App::getInstance()->getOptions()->get('wish_archive', 'teaser')); ?>
            </div>

            <div class="entry-content">
                <?php
                // create region menu
                global $wp_query;

                $c = \app\App::getInstance()->getWishController();
                $o = \app\App::getInstance()->getOptions();

                $terms = get_terms(array(
                    'taxonomy' => $c->getRegionTaxonomyName(),
                    'hide_empty' => true,
                    'orderby' => 'name'
                ));

                $class = '';
                $current_region = '';

                if (!is_tax()) {
                    $class = 'is-active';
                } else {
                    global $wp_query;
                    $current_region = $wp_query->get($c->getRegionTaxonomyName());
                }

                $links = array(
                    '<li class="' . $class . '"><p><a href="' . get_post_type_archive_link($c->getPostType()) . '">'
                    . $o->get('country') . '</a></p></li>'
                );

                foreach ($terms as $term) {
                    $class = '';
                    if (!empty($current_region) && $current_region == $term->slug) {
                        $class = 'is-active';
                    }

                    $links[] = '<li class="' . $class . '"><p><a href="' . get_term_link($term) . '">' . $term->name . '</a></p></li>';
                }

                echo '<ul class="wish-region-menu">' . implode('', $links) . '</ul>';
                ?>
            </div>
            <div class="entry-content">
                <?php get_template_part('template-parts/content/content-archive', 'my-wishes'); ?>
            </div>

            <div class="entry-content">
                <?php
                $wishListState = $o->get('wish_list_status');

                if ($wishListState == 'done') {
                    /**
                     * @todo translate it
                     */
                    echo '<h2>' . __('Fulfilled Wishes', 'app') . '</h2>';
                } else {
                    /**
                     * @todo translate it
                     */
                    echo '<h2>' . __('Open Wishes', 'app') . '</h2>';
                }
                ?>

                <?php if (have_posts()): ?>
                    <?php twentynineteen_the_posts_navigation(); ?>
                    <?php get_template_part('template-parts/content/content-archive', 'open-wishes'); ?> 
                    <?php twentynineteen_the_posts_navigation(); ?>
                <?php else: ?>

                    <?php
                    /**
                     * @todo translate it
                     */
                    ?>
                    <p><?php echo __('no open Wishes', 'app'); ?></p>
                <?php endif; ?>
            </div>
        </article>
    </main><!-- #main -->
</section><!-- #primary -->

<?php
get_footer();
