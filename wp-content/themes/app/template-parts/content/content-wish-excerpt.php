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
$o = \app\App::getInstance()->getOptions();
?>

<div class="wish-letter-mini">
    <header class="wish-header">
        <span class="key">#<?php echo rwmb_meta('key'); ?></span>
        <span class="status"><?php echo rwmb_meta('status_name'); ?></span>
        <div style="clear: both;"></div>
        <img class="status-icon" src="<?php echo get_stylesheet_directory_uri() . '/images/' . $controller->getState() . '.png'; ?>" width="72" height="72" />
        
        <?php
            if ($o->get('wish_list_status') == 'done') {
                echo '<h1>' . rwmb_meta('summary') . '</h1>';
            } else {
                echo '<a href="' . get_permalink() . '"><h1><' . rwmb_meta('summary') . '</h1></a>';
            }
        ?>
        
        
    </header><!-- .entry-header -->

    <div class="wish-content">
        <p class="description"><?php echo rwmb_meta('description'); ?></p>
    </div><!-- .entry-content -->


    <?php
    $controller->echoCtaButtons();
    ?>

    
    <footer class="wish-footer">
        <p class="institution-info">
            <?php $so_id = rwmb_get_value('social_organisation_id'); ?>
            <?php echo get_the_title($so_id); ?>,
            <?php echo rwmb_meta('zip', [], $so_id); ?> <?php echo rwmb_meta('city', [], $so_id); ?>
        </p>
    </footer><!-- .entry-footer -->
</div>
