<?php
/**
 * Displays the post header
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */
$discussion = !is_page() && twentynineteen_can_show_post_thumbnail() ? twentynineteen_get_discussion_data() : null;

/**
 * @var app\posttypes\AbstractPostType $controller
 */
global $wp_query;
$controller = \app\App::getInstance()->getController($wp_query->get('post_type'));
?>

<?php
echo '<h1 class="entry-title">';

if (is_tax(\app\App::getInstance()->getWishController()->getRegionTaxonomyName())) {
    $tax_item_slug = $wp_query->get($controller->getRegionTaxonomyName());
    $term = get_term_by('slug', $tax_item_slug, $controller->getRegionTaxonomyName());
    echo sprintf(__('Wish list %s', 'app'), $term->name);
} elseif ($wp_query->get('post_type') == \app\App::getInstance()->getWishController()->getPostType()) {
    echo sprintf(__('Wish list %s', 'app'), \app\App::getInstance()->getOptions()->get('country'));
} else {
    echo post_type_archive_title();
}

echo '</h1>';
?>

<?php if (!is_page() && $wp_query->get('post_type') !== \app\App::getInstance()->getWishController()->getPostType()) : ?>
    <div class="entry-meta">
        <span>
            <?php echo \app\App::getInstance()->getOptions()->get($controller->getPostType(), 'archive_teaser'); ?>
        </span>
        <?php
        ?>
    </div><!-- .entry-meta -->
    <?php endif; ?>
