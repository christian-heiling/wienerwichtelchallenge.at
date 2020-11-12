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
use app\posttypes\WishPostType;

$c = \app\App::getInstance()->getController(get_post_type());
$o = \app\App::getInstance()->getOptions();

$institution = rwmb_get_value('social_organisation_id');
$institution = get_post($institution);
$institution_id = $institution->ID;

$is_open = rwmb_get_value('status_id') == $o->get('jira_state', WishPostType::STATE_OPEN);
$is_in_progress = rwmb_get_value('status_id') == $o->get('jira_state', WishPostType::STATE_IN_PROGRESS);

$blur_class = '';
$recipient = rwmb_get_value('recipient');
if ($is_open) {
    $blur_class = 'blur';
    $recipient = 'Versteckt';
}


$deliveryOptions = rwmb_get_value('delivery_options', [], $institution_id);

if (in_array('amazon', $deliveryOptions)) {
    $summary = rwmb_get_value('summary');
    $summary = strtolower($summary);

    $noAffiliateLinkWords = array(
        'wertkarte', 'guthaben', 'yesss', 'a1', 't-mobile', 'magenta',
        'gutschein',
        'zigarette', 'marlboro', 'john player', 'pall mall', 'gauloises', 'lucky strike', 'memphis', 'chesterfield', 'camel', 'meine sorte', 'newport', 'winston', 'parliament'
    );

    foreach ($noAffiliateLinkWords as $word) {
        if (strpos($summary, $word) !== false) {
            $key = array_search('amazon', $deliveryOptions);
            unset($deliveryOptions[$key]);
        }
    }
}

if (empty($deliveryOptions)) {
    $deliveryOptions = array('postal');
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="entry-content">
        <?php
        $c->echoPreLetter();
        $c->echoCtaButtons();
        ?>
        <br>
        <?php if ($is_in_progress || $is_open): ?>
            <p><?php echo __('You have following possibilities to deliver your present:', 'app'); ?></p>
            
            <div class="wp-block-columns has-<?php echo count($deliveryOptions); ?>-columns">
                <?php if (in_array('postal', $deliveryOptions) && in_array(rwmb_meta('delivery_type'), array('', 'postal'))) : ?>
                    <div class="wp-block-column">
                        <h3><?php echo __('Postal delivery', 'app'); ?></h3>
                        <p><?php echo __('If you want to stay at home.', 'app') ?></p>

                        <p><strong><?php echo __('Write following on your package:', 'app'); ?></strong></p>

                        <?php
                        $recipient = rwmb_meta('recipient');
                        if ($is_open) {
                            $recipient = '<span class="blur">John Doe</span>';
                        }
                        ?>

                        <p>
                            <span><?php echo str_replace('%recipient%', $recipient, __('Wichtel-Present for %recipient%', 'app')); ?></span><br>
                            <?php if (!$is_open): ?>
                                <span><?php echo rwmb_meta('postal_addressee', [], $institution_id); ?></span><br>
                                <span><?php echo rwmb_meta('postal_street', [], $institution_id); ?></span><br>
                            <?php else: ?>
                                <span class="blur">Topsecret Organisation</span><br>
                                <span class="blur">Geheime Adresse 147</span><br>
                            <?php endif; ?>
                            <span><?php echo rwmb_meta('postal_zip', [], $institution_id) . ' ' . rwmb_meta('postal_city', [], $institution_id); ?></span>
                        </p>

                        <?php if ($is_open): ?>
                            <p><em><?php echo __('All blured out data you will see after confirming that you want to fulfill this wish.', 'app'); ?></em></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (in_array('personal', $deliveryOptions) && in_array(rwmb_meta('delivery_type'), array('', 'personal'))): ?>
                    <div class="wp-block-column">
                        <h3><?php echo __('Personal delivery', 'app'); ?></h3>
                        <p><?php echo __('If you are living next to the social institiution and want to visit.', 'app') ?></p>

                        <?php
                        $recipient = rwmb_meta('recipient');
                        if ($is_open) {
                            $recipient = '<span class="blur">John Doe</span>';
                        }
                        ?>
                        
                        <p><strong><?php echo str_replace('%recipient%', $recipient, __('Please write "%recipient%" on your present to assure that the right person will get it.', 'app')); ?></strong></p>
                        
                        <p><strong><?php echo __('Be aware of following COVID-19 rules:', 'app'); ?></strong></p>
                        <p><?php echo strip_tags(rwmb_meta('covid19_regulations', [], $institution_id)); ?></p>

                        <p><strong><?php echo __('Drop-off-point', 'app'); ?></strong></p>

                        <p>
                            <?php if (!$is_open): ?>
                                <span><?php echo rwmb_meta('addressee', [], $institution_id); ?></span><br>
                                <span><?php echo rwmb_meta('street', [], $institution_id); ?></span><br>
                            <?php else: ?>
                                <span class="blur">Topsecret Organisation</span><br>
                                <span class="blur">Geheime Adresse 147</span><br>
                            <?php endif; ?>
                            <span><?php echo rwmb_meta('zip', [], $institution_id) . ' ' . rwmb_meta('city', [], $institution_id); ?></span>
                        </p>

                        <p><?php echo strip_tags(rwmb_meta('delivery_hours', [], $institution_id)); ?></p>

                        <?php if ($is_open): ?>
                            <p><em><?php echo __('All blured out data you will see after confirming that you want to fulfill this wish.', 'app'); ?></em></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>


            </div>
            <br>
        <?php endif; ?>


        <div class="wp-block-columns has-2-columns">
            <div class="wp-block-column">
                <?php
                get_template_part('template-parts/content/content', 'wish-excerpt');
                ?>
                <br>
            </div>

            <div class="wp-block-column">
                <?php
                /**
                 * @todo translate it
                 */
                ?>
                <h2><?php echo __('Contact for Quenstions', 'app') ?></h2>
                <?php
                $contact = rwmb_meta('contact', [], $institution_id);
                ?>
                <span><?php echo $contact; ?></span>
                <br>
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
