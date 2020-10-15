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
$is_in_arbeit = rwmb_get_value('status_id') == $o->get('jira_state', 'in_arbeit');

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
    $deliveryOptions = array('personal');
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="entry-content">
        <?php
        $c->echoPreLetter();
        $c->echoCtaButtons();
        ?>
        <br>
        <?php if ($is_in_arbeit || $is_open): ?>

            <h2><?php echo __('Delivery Infos', 'app'); ?></h2>
            <?php
            /**
             * @todo translate it
             */
            ?>
            <p><?php echo __('Du hast folgende Möglichkeiten, dein Geschenk abzugeben:', 'app'); ?></p>


            <div class="wp-block-columns has-<?php echo count($deliveryOptions); ?>-columns">
                <?php if (in_array('personal', $deliveryOptions)): ?>
                    <div class="wp-block-column">
                        <br>
                        <?php
                        /**
                         * @todo translate it
                         */
                        ?>
                        <p><strong><?php echo __('Persönliche Abgabe', 'app'); ?></strong></p>
                        <?php
                        /**
                         * @todo translate it
                         */
                        ?>
                        <p><?php echo __('Wenn du in der Nähe wohnst und die Einrichtung persönlich kennenlernen willst.', 'app') ?></p>

                        <?php if ($is_in_arbeit): ?>
                            <p>
                                <span><?php echo $institution->post_title; ?></span><br>
                                <span><?php echo rwmb_meta('street', [], $institution_id); ?></span><br>
                                <span><?php echo rwmb_meta('zip', [], $institution_id) . ' ' . rwmb_meta('city', [], $institution_id); ?></span>
                            </p>

                            <p><em><?php echo __('Abgabezeiten', 'app') ?></em></p>
                            <p><?php echo rwmb_meta('delivery_hours', [], $institution_id); ?></p>

                            <p><em><?php echo __('Public Reachable via', 'app') ?></em></p>
                            <p><?php echo rwmb_meta('reachable_via', [], $institution_id) ?></span></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (in_array('amazon', $deliveryOptions) && !empty($o->get('amazonde_tag'))): ?>
                    <div class="wp-block-column">
                        <br>
                        <?php
                        /**
                         * @todo translate it
                         */
                        ?>
                        <p><strong><?php echo __('Als Amazon.de-Geschenk', 'app'); ?></strong></p>
                        <?php
                        /**
                         * @todo translate it
                         */
                        ?>
                        <p><?php echo __('Wenn du nicht in der Nähe wohnst und kein Geschenk verpacken willst', 'app') ?></p>

                        <?php if ($is_in_arbeit): ?>
                            <p>Wähle das Produkt über unseren Link aus und sende es als Geschenk verpackt an die Einrichtung</p>

                            <?php echo $c->generateAmazonAffiliateLink(); ?>
                            <?php
                            /**
                             * @todo translate it
                             */
                            ?>
                            <?php
                            $info = '<p>Wenn du über diesen Link das Geschenk besorgst, erhaltet die Wichtelchallenge eine Vermittlungsprovision.</p><p>Diese wird verwendet für den Kauf von jenen Geschenken, die keinen Wichtel gefunden haben.</p>'
                            ?>
                            <a href="#" data-featherlight="<?php echo esc_attr($info) ?>"><small>ⓘ Infos zum Link</small></a>

                            <p><em>Gib dafür folgende Lieferadresse an</em></p>
                            <p>
                                Wichtelchallenge Geschenk für <span class="<?php $blur_class; ?>"><?php echo $recipient; ?></span><br>
                                <?php echo $institution->post_title; ?><br>
                                <?php echo rwmb_meta('street', [], $institution_id); ?><br>
                                <?php echo rwmb_meta('zip', [], $institution_id) . ' ' . rwmb_meta('city', [], $institution_id); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (in_array('postal', $deliveryOptions)): ?>
                    <div class="wp-block-column">
                        <br>
                        <p><strong><?php echo __('Mit der Post', 'app'); ?></strong></p>
                        <p><?php echo __('Wenn du nicht in der Nähe wohnst und dein Geschenk selbst liebevoll verpacken willst', 'app') ?></p>

                        <?php if ($is_in_arbeit): ?>
                            <p><?php echo __('Schreibe die Empfängerkennung auf das Geschenk und sende das Paket an:', 'app') ?></p>
                            <p>
                                <span><?php echo $institution->post_title; ?></span><br>
                                <span><?php echo rwmb_meta('street', [], $institution_id); ?></span><br>
                                <span><?php echo rwmb_meta('zip', [], $institution_id) . ' ' . rwmb_meta('city', [], $institution_id); ?></span>
                            </p>
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
                <h2><?php echo __('Empfängerkennung', 'app'); ?></h2>
                <p>
                    <span class="<?php echo $blur_class; ?>"><?php echo $recipient; ?></span>
                </p>

                <?php
                /**
                 * @todo translate it
                 */
                ?>
                <h2><?php echo __('Ort der Abgabe', 'app'); ?></h2>
                <p>
                    <?php if (!$is_open): ?>
                        <span><?php echo $institution->post_title; ?></span><br>
                        <span><?php echo rwmb_meta('street', [], $institution_id); ?></span><br>
                    <?php else: ?>
                        <span class="blur">Topsecret Organisation</span><br>
                        <span class="blur">Geheime Adresse 147</span><br>
                    <?php endif; ?>
                    <span><?php echo rwmb_meta('zip', [], $institution_id) . ' ' . rwmb_meta('city', [], $institution_id); ?></span>
                </p>
                <p><?php echo rwmb_meta('delivery_hours', [], $institution_id); ?></p>

                <h2><?php echo __('Contact for Quenstions', 'app') ?></h2>
                <?php
                $contact = rwmb_meta('contact', [], $institution_id);
                if ($is_open) {
                    $contact = '3sadf09834890<br>sdaflkjasd@sdaklfa.com';
                }
                ?>
                <span class="<?php echo $blur_class; ?>"><?php echo $contact; ?></span>
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
