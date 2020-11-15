<?php

/**
 * @var $c app\posttypes\WishPostType
 */
global $wp_query;

$c = \app\App::getInstance()->getController($wp_query->get('post_type'));
$rows = 2;

$ads = \app\App::getInstance()->getOptions()->get('ad_banner');
$ads = unserialize($ads);


if (have_posts()) {

    // Start the Loop.
    $i = 0;

    while (have_posts()) {
        if ($i % $rows == 0) {
            echo '<div class="wp-block-columns">';
        }

        echo '<div class="wp-block-column">';
        $i++;

        the_post();

        /*
         * Include the Post-Format-specific template for the content.
         * If you want to override this in a child theme, then include a file
         * called content-___.php (where ___ is the Post Format name) and that will be used instead.
         */
        echo '<article id="post-' . get_the_ID() . '" ' . implode(' ', get_post_class()) . '>';
        get_template_part('template-parts/content/content', 'wish-excerpt');
        echo '</article>';

        // End the loop.

        echo '</div>';
        if ($i % $rows == 0) {
            echo '</div>';
        }

        // every 12th wish an add
        if ($i % 12 == 0) {
            if (empty($ads)) {
                continue;
            }

            //select add
            $ad = $ads[array_rand($ads)];

            // echo ad
            echo '<div class="ad">' . do_shortcode($ad) . '</div>';
        }
    }


    while ($i % $rows !== 0) {
        $i++;
        echo '<div class="wp-block-column">&nbsp;</div>';
        if ($i % $rows == 0) {
            echo '</div>';
        }
    }

    if ($i % $rows !== 0) {
        echo '</div>';
    }
}