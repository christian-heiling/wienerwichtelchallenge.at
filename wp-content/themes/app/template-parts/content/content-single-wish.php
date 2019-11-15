<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */

/**
 * @var app\posttypes\WishPostType Wish Type Controller
 */
$c = \app\App::getInstance()->getController(get_post_type());
$o = \app\App::getInstance()->getOptions();

$institution = rwmb_get_value('social_organisation_id');
$institution = get_post($institution);
$institution_id = $institution->ID;

$is_open = rwmb_get_value('status_id') == $o->get('jira_state', 'offen');
$blur_class = '';
if ($is_open) {
    $blur_class = 'blur';
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="entry-content">
        <?php
        $c->echoPreLetter();
        
        ?>

        <div class="wp-block-columns has-2-columns">
            <div class="wp-block-column">
                <?php 
                get_template_part('template-parts/content/content', 'wish-excerpt');
                ?>
            </div>
            
            <div class="wp-block-column">
                <h2><?php echo __('Delivery Infos', 'app'); ?></h2>

                <p>
                    z.H. <span class="<?php echo $blur_class; ?>"><?php echo rwmb_meta('recipient'); ?></span><br>
                    <span class="<?php echo $blur_class; ?>"><?php echo $institution->post_title; ?></span><br>
                    <span class="<?php echo $blur_class; ?>"><?php echo rwmb_meta('street', [], $institution_id); ?></span><br>
                    <span><?php echo rwmb_meta('zip', [], $institution_id) . ' ' . rwmb_meta('city', [], $institution_id); ?></span>
                </p>

                <p><?php echo rwmb_meta('delivery_hours', [], $institution_id); ?></p>

                <h2><?php echo __('Public Reachable via', 'app') ?></h2>
                <p><span class="<?php echo $blur_class; ?>"><?php echo rwmb_meta('reachable_via', [], $institution_id) ?></span></p>

                <h2><?php echo __('Contact for Quenstions', 'app') ?></h2>
                <span class="<?php echo $blur_class; ?>"><?php echo rwmb_meta('contact', [], $institution_id) ?></span>
            </div>

            
        </div>

        <?php if (!empty($institution_id)): ?>
            <h2><?php echo sprintf(__('About %s', 'app'), get_the_title($institution_id)); ?></h2>
            <p><?php echo rwmb_meta('teaser', [], $institution_id); ?></p>
            <p><a href="<?php echo get_permalink($institution_id); ?>">
                    Mehr Infos über <?php echo $institution->post_title; ?></a>
            </p>
        <?php endif; ?>
    </div><!-- .entry-content -->

</article><!-- #post-<?php the_ID(); ?> -->
