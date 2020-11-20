<?php
/**
 * @var $c app\posttypes\WishPostType
 */
global $wp_query;

$c = \app\App::getInstance()->getController($wp_query->get('post_type'));
$rows = 2;
?>

<?php
$query_options = array(
    'post_type' => $c->getPostType(),
    'meta_key' => 'status_id',
    'meta_query' => array(
        array(
            'key' => 'wichtel_id',
            'value' => get_current_user_id()
        )
    ),
    'limit' => -1,
    'posts_per_page' => -1
);

$wishTaxonomy = $wp_query->get($c->getRegionTaxonomyName());
if (!empty($wishTaxonomy)) {
    $query_options[$c->getRegionTaxonomyName()] = $wishTaxonomy;
}

$query = new WP_Query($query_options);


if ($query->have_posts()) {
    ?>
    <h2><?php echo __('My Wishes', 'app'); ?></h2>
    <?php
}



// Start the Loop.
$i = 0;

while ($query->have_posts()) :
    if ($i % $rows == 0) {
        echo '<div class="wp-block-columns">';
    }
    echo '<div class="wp-block-column">';
    $i++;

    $query->the_post();

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
endwhile;

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
