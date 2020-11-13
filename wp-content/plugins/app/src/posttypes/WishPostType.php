<?php

namespace app\posttypes;

class WishPostType extends AbstractPostType {

    const STATE_OPEN = 'offen';
    const STATE_IN_PROGRESS = 'in_arbeit';
    const STATE_FULFILLED = 'erfuellt';
    const STATE_DONE = 'abgeschlossen';
    const TRANSITION_ASSIGN = 'vergeben';
    const TRANSITION_FULFILL = 'erfuellen';
    const TRANSITION_PUT_BACK = 'zuruecklegen';

    public static function getStates() {
        return array(
            self::STATE_OPEN,
            self::STATE_IN_PROGRESS,
            self::STATE_FULFILLED,
            self::STATE_DONE
        );
    }

    public static function getTransitionsByState() {
        return array(
            self::STATE_OPEN => array(
                self::TRANSITION_ASSIGN
            ),
            self::STATE_IN_PROGRESS => array(
                self::TRANSITION_FULFILL,
                self::TRANSITION_PUT_BACK
            )
        );
    }

    public function registerHooks() {
        parent::registerHooks();

        add_action('wp_enqueue_scripts', array($this, 'addAdditionalScripts'));
        add_action('the_post', array($this, 'addShortcodes'));

        add_action('wp', array($this, 'handleTransition'));
        add_action('wp', array($this, 'restrictAccess'), 5);
        add_filter('wp', array($this, 'redirectDuplicatedWishes'), 1);

        add_action('init', array($this, 'createRegionTaxonomy'), 0);
        add_filter('pre_get_posts', array($this, 'addWishToTaxonomyInQuery'), 0);
        add_filter('pre_get_posts', array($this, 'showCertainWishesInArchive'), 1);

        add_action('bp_core_install_emails', array($this, 'addMailTemplates'));

        add_action('pre_get_posts', array($this, 'limitQuery'));
    }

    function limitQuery($query) {
        if (!is_admin() && $query->is_main_query() && is_archive() && $query->get('post_type') == $this->getPostType()) {
            $query->set('posts_per_page', 30);
        }
    }

    /*
     * tokens related to wishes:
     */

    public function getMailTemplates() {

        $content = <<<MAILCONTENT
Folgende Kürzel können verwendet werden für diese E-Mail:
{{wish.url}}
{{wish.title}}
{{wish.key}}
{{wish.status_id}}
{{wish.status_name}}
{{wish.link_to_jira}}
{{wish.wichtel_id}}
{{wish.wichtel_name}}
{{wish.wichtel_mail}}
{{wish.price}}
{{wish.summary}}
{{wish.description}}
{{wish.reporter_mail}}
{{wish.recipient}}
{{wish.address}}
{{wish.zip}}
{{wish.end_date}}
{{wish.wichtel_end_date}}
{{wish.wichtel_end_date_delta_in_days}}
{{wish.last_wichtel_delivery_date}}
{{wish.found_wichtel_date}}
{{wish.social_organisation_id}}
{{wish.vergeben.postal.url}}
{{wish.vergeben.personal.url}}
{{wish.erfuellen.url}}
{{wish.zuruecklegen.url}}
{{organisation.url}}
{{organisation.title}}
{{organisation.carrier}}
{{organisation.field_of_action}}
{{organisation.street}}
{{organisation.zip}}
{{organisation.city}}
{{organisation.map}}
{{organisation.reachable_via}}
{{organisation.delivery_hours}}
{{organisation.contact}}
{{organisation.teaser}}
{{organisation.description}}
{{organisation.link}}
{{organisation.jira_user}}
{{organisation.logo}}
MAILCONTENT;

        return array(
            array(
                'post_title' => __('[{{{site.name}}}] Danke vorab, dass du den Wunsch {{wish.title}} erfüllen willst!', 'app'),
                'post_content' => $content,
                'post_excerpt' => $content,
                'action' => 'wishTaken.postal',
                'action_description' => __('Wichtel want to takeover a wish and send it later by post', 'app')
            ),
            array(
                'post_title' => __('[{{{site.name}}}] Danke vorab, dass du den Wunsch {{wish.title}} erfüllen willst!', 'app'),
                'post_content' => $content,
                'post_excerpt' => $content,
                'action' => 'wishTaken.personal',
                'action_description' => __('Wichtel want to takeover a wish and bring it later directly to the institution', 'app')
            ),
            array(
                'post_title' => __('[{{{site.name}}}] Nur mehr {{wish.wichtel_end_diff_in_days}} Tage den Wunsch {{wish.title}} zu erfüllen.', 'app'),
                'post_content' => $content,
                'post_excerpt' => $content,
                'action' => 'wishRemember',
                'action_description' => __('after 7 days: remember mail', 'app')
            ),
            array(
                'post_title' => __('[{{{site.name}}}] Hast du den Wunsch {{wish.title}} erfüllt?', 'app'),
                'post_content' => $content,
                'post_excerpt' => $content,
                'action' => 'wishLastDeliveryDate',
                'action_description' => __('am letzten Abgabedatum für den Wichtel: Wunsch erfüllt oder zurücklegen?', 'app')
            ),
            array(
                'post_title' => __('[{{{site.name}}}] Dein Wunsch wurde zurückgelegt, weil du dich leider nicht mehr bei uns gemeldet hast', 'app'),
                'post_content' => $content,
                'post_excerpt' => $content,
                'action' => 'wishLayedBack',
                'action_description' => __('Nach dem letzten Abgabedatum für den Wichtel: Info das der Wunsch zurückgegeben worden ist', 'app')
            ),
            array(
                'post_title' => __('[{{{site.name}}}] Danke für dein Geschenk {{wish.title}}', 'app'),
                'post_content' => $content,
                'post_excerpt' => $content,
                'action' => 'thankYou',
                'action_description' => __('Nachdem die Einrichtung die Geschenkannahme bestätigt hat', 'app')
            )
        );
    }

    public function addMailTemplates() {

        $mailTemplates = $this->getMailTemplates();

        foreach ($mailTemplates as $event => $mt) {
            $post_exists = post_exists($mt['post_title']);

            if ($post_exists != 0 && get_post_status($post_exists) == 'publish') {
                return;
            }

            $post = array(
                'post_title' => $mt['post_title'],
                'post_content' => $mt['post_content'],
                'post_excerpt' => $mt['post_excerpt'],
                'post_status' => 'publish',
                'post_type' => bp_get_email_post_type()
            );

            // Insert the email post into the database
            $post_id = wp_insert_post($post);

            if ($post_id) {
                $tt_ids = wp_set_object_terms($post_id, $mt['action'], bp_get_email_tax_type());
                foreach ($tt_ids as $tt_id) {
                    wp_update_term($tt_id, bp_get_email_tax_type(), array(
                        'description' => $mt['action_description'],
                    ));
                }
            }
        }
    }

    public function generateAmazonAffiliateLink($wish_id = null) {
        if (empty($wish_id)) {
            $wish_id = get_the_ID();
        }

        $o = \app\App::getInstance()->getOptions();
        $tag = $o->get('amazonde', 'tag');
        $camp = $o->get('amazonde', 'camp');
        $creative = $o->get('amazonde', 'creative');
        $link_text = $o->get('amazonde', 'link_text');

        if (empty($tag) || empty($camp) || empty($creative) || empty($link_text)) {
            return '';
        }

        $attributes = array(
            'ie' => 'UTF8',
            'tag' => $tag,
            'linkCode' => 'ur2',
            'linkId' => md5(rwmb_get_value('key', [], $wish_id)),
            'camp' => $camp,
            'creative' => $creative,
            'index' => 'aps',
            'keywords' => $this->improveAffiliateSearchTerms(rwmb_get_value('summary', [], $wish_id))
        );

        $linkParams = array();

        foreach ($attributes as $a => $v) {
            $linkParams[] = $a . '=' . $v;
        }

        $linkParams = implode('&', $linkParams);
        $link = 'https://www.amazon.de/gp/search?' . $linkParams;


        $html = '<div class="wp-block-button">';
        $html .= '<a class="wp-block-button__link" target="_blank" href="' . $link . '">' .
                $link_text .
                '</a>';
        $html .= '</div>';

        return $html;
    }

    private function improveAffiliateSearchTerms($keywords) {

        $keywords = strtolower($keywords);

        // remove non alphabetical signs
        $nonAlphabeticalSigns = array(
            "[", "]", "(", ")", "-", ",", ":", "&", "!", "\"", "'", "/"
        );

        $keywords = str_replace($nonAlphabeticalSigns, ' ', $keywords);

        // remove top 100 frequent words
        $frequentGermanTerms = array(
            "die", "der", "und", "in", "zu", "den", "das", "nicht", "von", "sie",
            "ist", "des", "sich", "mit", "dem", "dass", "er", "es", "ein", "ich", "auf",
            "so", "eine", "auch", "als", "an", "nach", "wie", "im", "für", "man", "aber",
            "aus", "durch", "wenn", "nur", "war", "noch", "werden", "bei", "hat", "wir",
            "was", "wird", "sein", "einen", "welche", "sind", "oder", "zur", "um", "haben",
            "einer", "mir", "über", "ihm", "diese", "einem", "ihr", "uns", "da",
            "zum", "kann", "doch", "vor", "dieser", "mich", "ihn", "du", "hatte", "seine",
            "mehr", "am", "denn", "nun", "unter", "sehr", "selbst", "schon", "hier", "bis",
            "habe", "ihre", "dann", "ihnen", "seiner", "alle", "wieder", "meine", "Zeit",
            "gegen", "vom", "ganz", "einzelnen", "wo", "muss", "ohne", "eines", "können", "sei",
            "gebraucht", "gebrauchtes", "über"
        );

        foreach ($frequentGermanTerms as &$word) {
            $word = '/\b' . preg_quote($word, '/') . '\b/';
        }

        $keywords = preg_replace($frequentGermanTerms, '', $keywords);

        // remove mulitple whitespace signs with one space
        $keywords = preg_replace('!\s+!', ' ', $keywords);

        // remove leading and ending white spaces
        $keywords = trim($keywords);

        return $keywords;
    }

    public function getCurrentWichtelLastDeliveryDate($wish_id = null) {
        if (empty($wish_id)) {
            $wish_id = get_the_ID();
        }
        
        $found_wichtel = rwmb_get_value('found_wichtel_date', [], $wish_id);
        
        $lastWichtelDeliveryDate = new \DateTime(rwmb_get_value('last_wichtel_delivery_date', [], $wish_id));
        
        if (!empty($found_wichtel)) {
            $currentLastWichtelDeliveryDate = new \DateTime($found_wichtel);
            $currentLastWichtelDeliveryDate->add(new \DateInterval('P14D'));
            
            if ($currentLastWichtelDeliveryDate->getTimestamp() > $lastWichtelDeliveryDate->getTimestamp()) {
                $currentLastWichtelDeliveryDate = $lastWichtelDeliveryDate;
            }
            return $currentLastWichtelDeliveryDate;
        } else {
            $currentLastWichtelDeliveryDate = new \DateTime($found_wichtel);
            $currentLastWichtelDeliveryDate->add(new \DateInterval('P14D'));
            
            if ($currentLastWichtelDeliveryDate->getTimestamp() > $lastWichtelDeliveryDate->getTimestamp()) {
                $currentLastWichtelDeliveryDate = $lastWichtelDeliveryDate;
            }
            return $currentLastWichtelDeliveryDate;
        }
    }

    public function getCurrentWichtelLastDeliveryDateDeltaInDays($wish_id = null) {
        $d = $this->getCurrentWichtelLastDeliveryDate($wish_id);

        if (empty($d)) {
            return '';
        }
        $now = new \DateTime(date('Y-m-d'));

        $delta = $now->diff($d, false);

        if ($delta->invert) {
            return $delta->d * -1;
        } else {
            return $delta->d;
        }
    }

    /**
     * 
     * @param \WP_Query $query
     */
    public function addWishToTaxonomyInQuery($query) {
        $region = $query->get($this->getRegionTaxonomyName());

        if (empty($region)) {
            return $query;
        }

        $query->set('post_type', $this->getPostType());
        return $query;
    }

    /**
     * 
     * @param \WP_Query $query
     */
    public function showCertainWishesInArchive($query) {

        $o = \app\App::getInstance()->getOptions();

        // is it the right query?
        if (is_admin() || !$query->is_main_query() || !is_archive()) {
            return;
        }

        if ($query->get('post_type') !== $this->getPostType()) {
            return;
        }

        // ... Yes, then change it to only show open wishes 
        // and respect the last_wichtel_delivery_date

        $wishListState = $o->get('wish_list_status');

        if ($wishListState == 'done') {
            $validStates = array(
                $o->get('jira_state', self::STATE_FULFILLED),
                $o->get('jira_state', self::STATE_DONE)
            );

            $metaQuery = array(
                'relation' => 'AND',
                array(
                    'key' => 'status_id',
                    'value' => $validStates,
                    'compare' => 'IN'
                )
            );
        } else {
            $validStates = array(
                $o->get('jira_state', self::STATE_OPEN)
            );

            $metaQuery = array(
                'relation' => 'AND',
                array(
                    'key' => 'status_id',
                    'value' => $validStates,
                    'compare' => 'IN'
                ),
                array(
                    'key' => 'last_wichtel_delivery_date',
                    'value' => date('Y-m-d'),
                    'compare' => '>=',
                    'type' => 'DATE'
                )
            );
        }

        $query->set('meta_key', 'priority');
        $query->set('orderby', 'meta_value');
        $query->set('meta_query', $metaQuery);


        return $query;
    }

    public function createRegionTaxonomy() {
        register_taxonomy($this->getRegionTaxonomyName(), $this->getPostType(), array(
            'label' => __('Wish Regions', 'app'),
            'public' => true,
            'public_queryable' => true,
            'show_ui' => true,
            'hierarchical' => false,
            'rewrite' => array(
                'slug' => __('wish-region-slug', 'app'),
                'with_front' => true
            ),
            'has_archive' => true,
            'show_admin_column' => true,
            'has_archive' => true
        ));
    }

    public function restrictAccess() {
   
        $options = \app\App::getInstance()->getOptions();

        // is user logged in and fast login flag is set
        // then login
        if (get_current_user_id() == 0 && isset($_GET['fl'])) {
            $mail = \app\App::getInstance()->decrypt($_GET['fl']);
            $user = get_user_by('email', $mail);

            // no user found -> send it to login
            if (empty($user)) {
                header('Location: ' . wp_login_url(get_permalink() . '?' . $_SERVER['QUERY_STRING']));
                exit;
            }

            // do fast login
            wp_set_current_user($user->ID, $user->data->user_login);
            wp_set_auth_cookie($user->ID);
            do_action('wp_login', $user->data->user_login);
            wp_safe_redirect($_SERVER["REQUEST_URI"]);
            exit;
        }

        // if wish is displayed ...
        if (get_post_type() == $this->getPostType() && is_single()) {

            // ... is not open
            // and not related to the user and not admin or editor
            if (rwmb_meta('status_id') !== $options->get('jira_state', self::STATE_OPEN)) {
                // 302 redirect it to the wish overview page

                if (get_current_user_id() !== intval(rwmb_get_value('wichtel_id')) && !(current_user_can('editor') || current_user_can('administrator'))) {
                    header('Location: ' . home_url('/' . $this->getSlug() . '/'));
                    exit;
                }
            }
        }
    }

    // curresponding to a bug this function is necessary
    // we have had duplicated wishes
    // we want only show the orign wish
    public function redirectDuplicatedWishes() {
        $request = $_SERVER['REQUEST_URI'];

        $request_parts = array_values(array_filter(explode('/', $request)));

        if (empty($request_parts) || $request_parts[0] != $this->getSlug() || count($request_parts) !== 2) {
            return;
        }

        $splittedWishId = explode('-', $request_parts[1]);
        if (count($splittedWishId) <= 2) {
            return;
        }

        // its a duplicated wish
        // now redirect to the origin wish
        $redirectUrl = home_url('/' . $this->getSlug() . '/' . $splittedWishId[0] . '-' . $splittedWishId[1]);
        if (!empty($_SERVER['QUERY_STRING'])) {
            $redirectUrl .= '?' . $_SERVER['QUERY_STRING'];
        }
        header('Location: ' . $redirectUrl);
        exit;
    }

    public function getState() {
        $o = \app\App::getInstance()->getOptions();
        foreach ($this->getStates() as $s) {
            if ($o->get('jira_state', $s) == rwmb_get_value('status_id')) {
                return $s;
            }
        }

        return null;
    }

    public function registerPostType() {

        $args = array(
            'label' => $this->getLabel(),
            'description' => 'Hallo',
            'supports' => $this->getSupports(),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 25,
            'menu_icon' => $this->getMenuIcon(),
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'page',
            'show_in_rest' => true,
            'rewrite' => array(
                'slug' => $this->getSlug()
            ),
            'taxonomies' => array($this->getRegionTaxonomyName()),
            'rest_base' => $this->slugify($this->getPostType()),
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        );
        register_post_type($this->getPostType(), $args);
    }

    public function getRegionTaxonomyName() {
        return 'wish-region';
    }

    public function handleTransition() {
        $o = \app\App::getInstance()->getOptions();

        // is a transition?
        if (!isset($_GET['transition'])) {
            return;
        }

        // is user not logged in 
        // then abort
        if (get_current_user_id() == 0) {
            return;
        }

        // check if a transition is valid ...
        // if not vergeben then wichtel_id must be equal current user id
        if ($_GET['transition'] !== self::TRANSITION_ASSIGN && rwmb_get_value('wichtel_id') != get_current_user_id()) {
            return;
        }

        // depending on the state only some transitions are valid
        $status = '';
        foreach ($this->getStates() as $state_name) {
            if ($o->get('jira_state', $state_name) == rwmb_get_value('status_id')) {
                $status = $state_name;
                break;
            }
        }

        if (!in_array($_GET['transition'], $this->getTransitionsByState()[$status])) {
            return;
        }

        // ... now we should be ready to do the transition
        // get transition id
        $transition_id = $o->get('jira_transition', $_GET['transition']);
        $single_wish_link = get_permalink();

        // calculated related fields
        global $current_user;
        $wishKey = rwmb_get_value('key');
        $wish = $this->getWishByKey($wishKey);

        $fields = array();

        if ($_GET['transition'] == self::TRANSITION_ASSIGN) {
            $fields = array(
                \app\JiraHandler::JIRA_FIELD_WICHTEL_ID => $current_user->data->ID,
                \app\JiraHandler::JIRA_FIELD_WICHTEL_NAME => $current_user->data->display_name,
                \app\JiraHandler::JIRA_FIELD_WICHTEL_MAIL => $current_user->data->user_email,
                \app\JiraHandler::JIRA_FIELD_DELIVERY_TYPE => $_GET['delivery_type']
            );
        }

        // do transition and wait for reimport

        $i = \app\App::getInstance()->getJiraHandler()->doTransition(
                $wishKey, $transition_id, $fields
        );

        // requery wish
        $wish = $this->getWishByKey($wishKey);

        $replace_pattern = array(
            'wichtel_name' => $current_user->data->display_name,
            'wish_title' => $i->fields->{\app\JiraHandler::JIRA_FIELD_SUMMARY},
            'wish_key' => $i->{\app\JiraHandler::JIRA_FIELD_KEY},
            'wish_recipient' => $i->fields->{\app\JiraHandler::JIRA_FIELD_RECIPIENT},
            'approve_link' => $this->calculateApproveLink($wishKey, $i->fields->{\app\JiraHandler::JIRA_FIELD_APPROVER}[0]->id)
        );

        // prepare comments
        $search = array();
        $replace = array();
        $comment = '';

        foreach ($replace_pattern as $key => $value) {
            $search[] = '%' . $key . '%';
            $replace[] = $value;
        }

        if ($_GET['transition'] == self::TRANSITION_ASSIGN) {
            $comment = __("*Found a Wichtel!*\n\nWichtel %wichtel_name% want to fulfill the wish %wish_title% (%wish_key%).\nThe present is for %wish_recipient%.", 'app');
        } elseif ($_GET['transition'] == self::TRANSITION_FULFILL) {
            $delivery_type = $i->fields->{\app\JiraHandler::JIRA_FIELD_DELIVERY_TYPE};

            if ($delivery_type == 'personal') {
                $comment = __("*Present delivered directly to the institution!*\n\nWichtel %wichtel_name% has indicated that he/she brought the present %wish_title% (%wish_key%) directly to the institution.\nThe present is for %wish_recipient%\n\n(!) Please confirm.\n\n%approve_link%", 'app');
            } elseif ($delivery_type == 'postal') {
                $comment = __("*Present brought to the post office!*\n\nWichtel %wichtel_name% has indicated that he/she brought the present %wish_title% (%wish_key%) to the post office. It should arrive in the next few days.\nThe present is for %wish_recipient%\n\n(!) Please confirm when the present arrive.\n\n%approve_link%", 'app');
            } else {
                $comment = __("*Present delivered!*\n\nWichtel %wichtel_name% has indicated that he/she the present %wish_title% (%wish_key%).\nThe present is for %wish_recipient%\n\n(!) Please confirm.\n\n%approve_link%", 'app');
            }
        } elseif ($_GET['transition'] == self::TRANSITION_PUT_BACK) {
            $comment = __(":( *Wichtel %wichtel_name% cannot fulfill wish %wish_title% (%wish_key%)*\n\n It was for %wish_recipient%.\nTherefore we are looking for a new Wichtel.", 'app');
        }

        $comment = str_replace($search, $replace, $comment);

        // send comment
        \app\App::getInstance()->getJiraHandler()->doComment($wishKey, $comment);

        // afterwards redirect to the single wish page
        header('Location: ' . $single_wish_link);
        exit;
    }

    public function addShortcodes() {


        if (get_post_type() == $this->getPostType()) {
            $boxes = $this->addMetaBox([]);
            $fields = array_pop($boxes)['fields'];

            foreach ($fields as $field) {
                $field_id = $field['id'];

                add_shortcode($field_id, function() use ($field_id) {
                    return rwmb_get_value($field_id);
                });
            }

            add_shortcode('wichtel_end_date_delta_in_days', function() {
                return $this->getCurrentWichtelLastDeliveryDateDeltaInDays();
            });

            add_shortcode('wichtel_end_date', function() {
                return date_i18n(get_option('date_format'), $this->getCurrentWichtelLastDeliveryDate()->getTimestamp());
            });

            $boxes = \app\App::getInstance()->getSocialOrganisationController()->addMetaBox([]);

            foreach ($boxes as $box) {
                $fields = $box['fields'];
                foreach ($fields as $field) {
                    $field_id = $field['id'];

                    add_shortcode('social_organisation_' . $field_id, function() use ($field_id) {
                        return rwmb_get_value($field_id);
                    });
                }
            }
        }
    }

    public function calculateApproveLink($key, $approveId) {
        $link_pattern = '[%button_name%|https://wichtelchallenge.collabri.at/servicedesk/customer/user/approval-action/%key%/%approve_id%/%action%/]';

        $approveLink = str_replace(array('%button_name%', '%key%', '%approve_id%', '%action%'), array(__('I confirm that the present is arrived', 'app'), $key, $approveId, 'approve'), $link_pattern);

        return $approveLink;
    }

    public function addAdditionalScripts() {
        wp_register_style('featherlight', plugin_dir_url(dirname(dirname(__FILE__))) . 'featherlight/featherlight.css');
        wp_enqueue_style('featherlight');

        wp_register_script(
                'featherlight', plugin_dir_url(dirname(dirname(__FILE__))) . 'featherlight/featherlight.js', 'jquery', '1.17.1', true
        );
        wp_enqueue_script('featherlight');
    }

    public function getLabel() {
        return __('Wishes', 'app');
    }

    public function getMenuIcon() {
        return 'dashicons-buddicons-community';
    }

    public function getPostType() {
        return 'wish';
    }

    public function getSlug() {
        return __('wish-slugs', 'app');
    }

    public function getStatusOpen() {
        return 'Offen';
    }

    public function addMetaBox($meta_boxes) {

        $meta_boxes[] = array(
            'title' => __('JIRA'),
            'post_types' => array($this->getPostType()),
            'fields' => array(
                [
                    'id' => 'key',
                    'name' => 'key',
                    'type' => 'text'
                ],
                [
                    'id' => 'status_id',
                    'name' => 'status_id',
                    'type' => 'text'
                ],
                [
                    'id' => 'status_name',
                    'name' => 'status_name',
                    'type' => 'text'
                ],
                [
                    'id' => 'link_to_jira',
                    'name' => 'link_to_jira',
                    'type' => 'text'
                ],
                [
                    'id' => 'wichtel_id',
                    'name' => 'wichtel_id',
                    'type' => 'text'
                ],
                [
                    'id' => 'wichtel_name',
                    'name' => 'wichtel_name',
                    'type' => 'text'
                ],
                [
                    'id' => 'wichtel_mail',
                    'name' => 'wichtel_mail',
                    'type' => 'text'
                ],
                [
                    'id' => 'price',
                    'name' => 'price',
                    'type' => 'text'
                ],
                [
                    'id' => 'summary',
                    'name' => 'summary',
                    'type' => 'text'
                ],
                [
                    'id' => 'description',
                    'name' => 'description',
                    'type' => 'text'
                ],
                [
                    'id' => 'reporter_mail',
                    'name' => 'reporter_mail',
                    'type' => 'text'
                ],
                [
                    'id' => 'recipient',
                    'name' => 'recipient',
                    'type' => 'text'
                ],
                [
                    'id' => 'address',
                    'name' => 'address',
                    'type' => 'text'
                ],
                [
                    'id' => 'zip',
                    'name' => 'zip',
                    'type' => 'text'
                ],
                [
                    'id' => 'end_date',
                    'name' => 'end_date',
                    'type' => 'text'
                ],
                [
                    'id' => 'last_wichtel_delivery_date',
                    'name' => 'last_wichtel_delivery_date',
                    'type' => 'text'
                ],
                [
                    'id' => 'found_wichtel_date',
                    'name' => 'found_wichtel_date',
                    'type' => 'text'
                ],
                [
                    'id' => 'social_organisation_id',
                    'name' => 'social_organisation_id',
                    'type' => 'post',
                    'post_type' => \app\App::getInstance()->getSocialOrganisationController()->getPostType(),
                    'query_args' => array(
                        'post_status' => 'publish',
                        'posts_per_page' => - 1,
                    ),
                ],
                [
                    'id' => 'priority',
                    'name' => 'priority',
                    'type' => 'text'
                ],
                [
                    'id' => 'delivery_type',
                    'name' => 'delivery_type',
                    'type' => 'text'
                ],
                [
                    'id' => 'approve_id',
                    'name' => 'approve_id',
                    'type' => 'text'
                ]
        ));
        return $meta_boxes;
    }

    public function getMailTokens($id = null) {
        if ($id == null) {
            $id = get_the_ID();
        }

        // get wish data
        $w = get_post($id);
        $wMeta = get_post_meta($id);

        // get organisation data
        $oId = rwmb_get_value('social_organisation_id', [], $id);
        $oMeta = get_post_meta($oId);
        $o = get_post($oId);

        $tokens = array();
        $tokens['wish.url'] = get_permalink($w);
        $tokens['wish.title'] = $w->post_title;
        $tokens['wish.wichtel_end_date'] = date_i18n(get_option('date_format'), $this->getCurrentWichtelLastDeliveryDate($id)->getTimestamp());
        $tokens['wish.wichtel_end_date_delta_in_days'] = $this->getCurrentWichtelLastDeliveryDateDeltaInDays($id);
        foreach ($wMeta as $key => $value) {
            if (substr($key, 0, 1) == '_') {
                continue;
            }

            $value = $value[0];

            if (substr($key, -5) == '_date') {
                if (empty($value)) {
                    $tokens['wish.' . $key] = '';
                    continue;
                }

                $cDate = new \DateTime($value);
                $tokens['wish.' . $key] = date_i18n(get_option('date_format'), $cDate->getTimestamp());
            } else {
                $tokens['wish.' . $key] = trim(strip_tags($value));
            }
        }
        foreach ($this->getTransitionsByState() as $s) {
            foreach ($s as $trans_name) {
                // for the beginning do not use fast logins
                $url = get_permalink($w) . '?transition=' . $trans_name;
                //$url = get_permalink($w) . '?transition=' . $trans_name . '&fl=' . urlencode(\app\App::getInstance()->encrypt(array_pop($wMeta['wichtel_mail'])));
                if ($trans_name == self::TRANSITION_ASSIGN) {
                    $tokens['wish.' . $trans_name . '.postal.url'] = $url . '&delivery_type=postal';
                    $tokens['wish.' . $trans_name . '.personal.url'] = $url . '&delivery_type=personal';
                } else {
                    $tokens['wish.' . $trans_name . '.url'] = $url;
                }
            }
        }

        $tokens['organisation.url'] = get_permalink($o);
        $tokens['organisation.title'] = $o->post_title;
        foreach ($oMeta as $key => $value) {
            if (substr($key, 0, 1) == '_') {
                continue;
            }
            $tokens['organisation.' . $key] = trim(strip_tags($value[0]));
        }

        // rename organisation.covid19
        $tokens['organisation.covid19'] = $tokens['organisation.covid19_regulations'];
        unset($tokens['organisation.covid19_regulations']);
        
        // deliver always addressee, street, zip, city depending on the delivery type
        if (!empty($tokens['wish.delivery_type']) && $tokens['wish.delivery_type'] == 'postal') {
            $tokens['organisation.addressee'] = $tokens['organisation.postal_addressee'];
            $tokens['organisation.street'] = $tokens['organisation.postal_street'];
            $tokens['organisation.zip'] = $tokens['organisation.postal_zip'];
            $tokens['organisation.city'] = $tokens['organisation.postal_city'];          
        }
        
        unset($tokens['organisation.postal_addressee']);
        unset($tokens['organisation.postal_street']);
        unset($tokens['organisation.postal_zip']);
        unset($tokens['organisation.postal_city']);

        return $tokens;
    }

    public function setColumnHead() {
        $head['cb'] = '<input type="checkbox" />';
        $head['title'] = __("JIRA ID", 'app');
        $head['summary'] = __('Title', 'app');
        $head['description'] = __('Beschreibung', 'app');
        $head['social_organisation'] = __('Beschreibung', 'app');
        $head['region'] = __('Beschreibung', 'app');
        $head['status_name'] = __('Status', 'app');

        return $head;
    }

    public function echoColumnBody($column_name, $post_ID) {
        if (in_array($column_name, ['description', 'summary', 'status_name', 'wichtel_mail'])) {
            echo rwmb_meta($column_name, [], $post_ID);
        } elseif ($column_name == 'social_organisation') {
            $terms = get_terms(array(
                'taxonomy' => $this->getRegionTaxonomyName(),
                'hide_empty' => false
            ));

            $terms = array_map(function($e) {
                return $e->name;
            }, $terms);

            echo implode(', ', $terms);
        } elseif ($column_name = 'social_organsiation') {
            echo get_the_title(rwmb_get_value('social_organisation_id'));
        }
    }

    public function echoEntryMeta() {
        // do nothing
    }

    public function echoEntryContent() {
        // see template-parts/content-single-wish.php
    }

    public function echoPreLetter() {
        $o = \app\App::getInstance()->getOptions();

        $text = '';
        foreach ($this->getStates() as $state_name) {
            if ($o->get('jira_state', $state_name) == rwmb_get_value('status_id')) {
                if ($state_name == 'in_arbeit') {
                    $text = $o->get('jira_state_pre', $state_name . '_' . rwmb_meta('delivery_type'));
                } else {
                    $text = $o->get('jira_state_pre', $state_name);
                }

                break;
            }
        }

        echo do_shortcode($text);
    }

    public function echoCtaButtons() {

        // figure out which status
        $o = \app\App::getInstance()->getOptions();



        $status = '';
        foreach ($this->getStates() as $state_name) {
            if ($o->get('jira_state', $state_name) == rwmb_get_value('status_id')) {
                $status = $state_name;
                break;
            }
        }

        if (empty($status)) {
            return;
        }

        // figure out which transitions for this status
        if (!array_key_exists($status, $this->getTransitionsByState())) {
            return;
        }

        $transitions = $this->getTransitionsByState()[$status];

        if (is_archive() && $status == self::STATE_OPEN) {
            echo '<div class="wish-buttons">';
            echo '<div class="wp-block-button wish-button-primary">';
            echo '<a class="wp-block-button__link" href="' . get_permalink() . '">'
            . __('Read more', 'app')
            . '</a>';
            echo '</div>';
            echo '</div>';
            return;
        }

        echo '<div class="wish-buttons">';
        foreach ($transitions as $key => $trans_name) {
            $button_caption = $o->get('jira_transition_button', $trans_name);

            if (!is_user_logged_in()) {
                $question = $o->get('jira_transition_question', 'not_logged_in');

                $popup_html = do_shortcode($question);
                $popup_html .= '<div class="wish-buttons">';

                $popup_html .= '<div class="wp-block-button wish-button-secondary">';
                $popup_html .= '<a class="wp-block-button__link" href="' . wp_login_url(get_permalink()) . '">';

                $popup_html .= __('Log in', 'app');
                $popup_html .= '</a>';
                $popup_html .= '</div>';

                $popup_html .= '</div>';
            } else {
                $question = $o->get('jira_transition_question', $trans_name);
                $trans_id = $o->get('jira_transition', $trans_name);

                $popup_html = do_shortcode($question);
                $popup_html .= '<div class="wish-buttons">';

                $popup_html .= '<div class="wp-block-button wish-button-primary">';

                if ($trans_name == 'vergeben') {
                    $popup_html .= '<a class="wp-block-button__link" href="' . get_permalink() . '?transition=' . $trans_name . '&delivery_type=postal">';
                    $popup_html .= __('I will send the present by post', 'app');
                    $popup_html .= '</a>&nbsp;';
                    $popup_html .= '<a class="wp-block-button__link" href="' . get_permalink() . '?transition=' . $trans_name . '&delivery_type=personal">';
                    $popup_html .= __('I will bring the present to the institution', 'app');
                    $popup_html .= '</a>';
                } else {
                    $popup_html .= '<a class="wp-block-button__link" href="' . get_permalink() . '?transition=' . $trans_name . '">';
                    $popup_html .= __('Confirm', 'app');
                    $popup_html .= '</a>';
                }

                $popup_html .= '</div>';
            }

            $popup_html = str_replace('"', "'", $popup_html);

            if ($key == 0) {
                $class = 'wish-button-primary';
            } else {
                $class = 'wish-button-secondary';
            }

            echo '<div class="wp-block-button ' . $class . '">';
            echo '<a class="wp-block-button__link" href="#" data-featherlight="' . $popup_html . '">' . $button_caption . '</a>';
            echo '</div>';
        }


        if (is_archive() && $status !== self::STATE_OPEN) {
            echo '<a class="wish-detail-link" href="' . get_permalink() . '">'
            . __('alle Details sehen', 'app')
            . '</a>';
            return;
        }
        echo '</div>';
    }

    private function echoCtaButton($popup_html, $button_caption) {
        
    }

    public function echoExcerptContent() {
        
    }

    public function echoExcerptMeta() {
        
    }

    public function getSortableColumns() {
        return [];
    }

    public function getWishByKey($key) {
        $query = new \WP_Query(array(
            'posts_per_page' => -1,
            'post_type' => \app\App::getInstance()->getWishController()->getPostType(),
            'post_status' => 'any',
            'meta_query' => array(
                array(
                    'key' => 'key',
                    'value' => $key
                )
            )
        ));

        $posts = $query->get_posts();
        return array_pop($posts);
    }

}
