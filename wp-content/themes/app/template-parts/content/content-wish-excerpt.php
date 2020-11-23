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

        <?php $so_id = rwmb_get_value('social_organisation_id'); ?>
        <p class="institution-info">
            <a href="<?php echo get_permalink($so_id); ?>#wishes">
            
            <?php echo get_the_title($so_id); ?>,
            <?php
            if (empty(rwmb_meta('zip', [], $so_id))) {
                echo rwmb_meta('postal_zip', [], $so_id) . ' ' . rwmb_meta('postal_city', [], $so_id);
            } else {
                echo rwmb_meta('zip', [], $so_id) . ' ' . rwmb_meta('city', [], $so_id);
            }
            ?>
            </a>
            <br>
            <?php
            $delivery_options = rwmb_get_value('delivery_options', [], $so_id);

            $delivery_option_string = [];

            if (is_array($delivery_options) && in_array('postal', $delivery_options)) {
                $delivery_option_string[] = __('Postal delivery', 'app');
            }

            if (is_array($delivery_options) && in_array('personal', $delivery_options)) {
                $delivery_option_string[] = __('Personal delivery', 'app');
            }

            $delivery_option_string = implode(', ', $delivery_option_string);

            echo __('Delivery Options', 'app') . ': ' . $delivery_option_string;
            ?>
        </p>

        <img class="status-icon" src="<?php echo get_stylesheet_directory_uri() . '/images/' . $controller->getState() . '.png'; ?>" width="72" height="72" />

        <?php
        if ($o->get('wish_list_status') == 'done') {
            echo '<h1>' . rwmb_meta('summary') . '</h1>';
        } else {
            echo '<a href="' . get_permalink() . '"><h1>' . rwmb_meta('summary') . '</h1></a>';
        }
        ?>


    </header><!-- .entry-header -->

    <div class="wish-content">
        <p class="description"><?php echo $controller->getTextWithLinks(rwmb_meta('description')); ?></p>
    </div><!-- .entry-content -->


    <?php
    $controller->echoCtaButtons();
    ?>


    <footer class="wish-footer">
        <span class="key">#<?php echo rwmb_meta('key'); ?></span>
    </footer><!-- .entry-footer -->
</div>
