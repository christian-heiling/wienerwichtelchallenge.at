<?php

namespace app;

use app\posttypes\WishPostType;

class JiraHandler {

    const JIRA_FIELD_KEY = 'key';
    const JIRA_FIELD_PROJECT = 'project';
    const JIRA_FIELD_STATUS = 'status';
    const JIRA_FIELD_LINK_TO_JIRA = 'customfield_10109';
    const JIRA_FIELD_WICHTEL_ID = 'customfield_10116';
    const JIRA_FIELD_WICHTEL_NAME = 'customfield_10142';
    const JIRA_FIELD_WICHTEL_MAIL = 'customfield_10143';
    const JIRA_FIELD_PRICE = 'customfield_10117';
    const JIRA_FIELD_SUMMARY = 'summary';
    const JIRA_FIELD_DESCRIPTION = 'description';
    const JIRA_FIELD_REPORTER = 'reporter';
    const JIRA_FIELD_RECIPIENT = 'customfield_10120';
    const JIRA_FIELD_END_DATE = 'duedate';
    const JIRA_FIELD_LAST_WICHTEL_DELIVERY_DATE = 'customfield_10118';
    const JIRA_FIELD_FOUND_WICHTEL_DATE = 'customfield_10119';
    const JIRA_FIELD_COMPONENTS = 'components';
    const JIRA_FIELD_DELIVERY_TYPE = 'customfield_10603';
    const JIRA_FIELD_APPROVER = 'customfield_10107';

    private $domain;
    private $username;
    private $password;
    private $project;

    public function getWishesPerPartialImport() {
        return 250;
    }

    public function __construct() {
        $options = App::getInstance()->getOptions();

        /**
         * @todo Fix that the option is taken
         */
        $this->domain = $options->get('jira', 'domain');
        $this->username = $options->get('jira', 'username');
        $this->password = $options->get('jira', 'password');
        $this->project = $options->get('jira', 'project');
    }

    public function getProjects() {
        return $this->doGet('/rest/api/2/project')->values;
    }

    public function search($jql, $startAt = 0, $maxResults = 1000) {
        $post_params = array(
            'jql' => $jql,
            'startAt' => $startAt,
            'maxResults' => $maxResults,
            'fields' => array(
                self::JIRA_FIELD_PROJECT,
                self::JIRA_FIELD_STATUS,
                self::JIRA_FIELD_LINK_TO_JIRA,
                self::JIRA_FIELD_WICHTEL_ID,
                self::JIRA_FIELD_WICHTEL_NAME,
                self::JIRA_FIELD_WICHTEL_MAIL,
                self::JIRA_FIELD_PRICE,
                self::JIRA_FIELD_SUMMARY,
                self::JIRA_FIELD_DESCRIPTION,
                self::JIRA_FIELD_REPORTER,
                self::JIRA_FIELD_RECIPIENT,
                self::JIRA_FIELD_END_DATE,
                self::JIRA_FIELD_LAST_WICHTEL_DELIVERY_DATE,
                self::JIRA_FIELD_FOUND_WICHTEL_DATE,
                self::JIRA_FIELD_COMPONENTS,
                self::JIRA_FIELD_DELIVERY_TYPE,
                self::JIRA_FIELD_APPROVER
            )
        );

        return $this->doPost('/rest/api/2/search', json_encode($post_params));
    }

    public function doTransition($issue_id, $transition_id, $updateFields = [], $comment = '') {
        $postData = array(
            'transition' => array(
                'id' => $transition_id
            )
        );

        if (!empty($comment)) {
            $postData['update'] = array(
                'comment' => array(
                    array(
                        'add' => array(
                            'body' => $comment
                        )
                    )
                )
            );
        }

        if (!empty($updateFields)) {
            $postData['fields'] = $updateFields;
        }

        $postData = json_encode($postData);

        $this->doPost('/rest/api/2/issue/' . $issue_id . '/transitions', $postData);
        return $this->doImportSingleIssue($issue_id);
    }

    public function doComment($issue_id, $comment) {
        $postData = array(
            'body' => $comment
        );

        $postData = json_encode($postData);

        $this->doPost('/rest/api/2/issue/' . $issue_id . '/comment', $postData);
    }

    public function doImportSingleIssue($issue_id) {
        wp_cache_flush();

        // request wish
        $i = $this->doGet('/rest/api/2/issue/' . $issue_id . '/');

        if (empty($i)) {
            return;
        }

        // delete old wish
        $query = new \WP_Query(array(
            'posts_per_page' => -1,
            'post_type' => App::getInstance()->getWishController()->getPostType(),
            'post_status' => 'any',
            'meta_query' => array(
                array(
                    'key' => 'key',
                    'value' => $issue_id
                )
            )
        ));

        $posts = $query->get_posts();

        foreach ($posts as $post) {
            wp_delete_post($post->ID, true);
        }

        // create wish
        $this->createWish($i);

        return $i;
    }

    private function getSearchWishQuery() {
        return 'issuetype = Wunsch AND status in (Offen, "In Arbeit", Erfüllt, Abgeschlossen) AND project="' . $this->project . '"';
    }

    public function getCountOfWishesForImport() {
        $res = $this->search($this->getSearchWishQuery(), 0, 1);
        if (empty($res)) {
            return null;
        }

        return $res->total;
    }

    public function doPartialImport($part, $debug = false) {
        wp_suspend_cache_addition();
        wp_cache_flush();
        $start = microtime(true);

        $options = App::getInstance()->getOptions();

        $responses = array();

        // query all issues
        $count = 0;

        do {
            $res = $this->search($this->getSearchWishQuery(), $part * $this->getWishesPerPartialImport(), $this->getWishesPerPartialImport());

            if (!empty($res)) {
                $responses[] = $res;
                $count += $res->maxResults;
            }
        } while (!empty($res) && $count < $this->getWishesPerPartialImport());

        if ($debug)
            echo 'Request JIRA done: ' . (microtime(true) - $start) . "\n";

        // if no issues returned, abort
        if (empty($res)) {
            return;
        }

        // calculate flat array of issues
        $issues = array();
        foreach ($responses as $res) {
            foreach ($res->issues as $i) {
                $issues[] = $i;
            }
        }

        if ($debug)
            echo 'Delete Posts done: ' . (microtime(true) - $start) . "\n";

        // insert new wishes
        foreach ($issues as $i) {
            $this->createWish($i);
        }

        if ($debug)
            echo 'Create new Wishes done: ' . (microtime(true) - $start) . "\n";
//
        if ($debug) {
            global $wpdb;
            echo "<pre>";
            foreach ($wpdb->queries as $q) {
                echo implode(", ", array_map(function($e) {
                            return '"' . $e . '"';
                        }, $q)) . "\n";
            }
            echo "</pre>";
            die();
        }
    }

    public function doFullImport($debug = false) {
        wp_suspend_cache_addition();
        wp_cache_flush();

        $start = microtime(true);

        $options = App::getInstance()->getOptions();

        $responses = array();

        // query all issues
        $startAt = 0;

        do {
            $res = $this->search($this->getSearchWishQuery(), $startAt);

            if (!empty($res)) {
                $responses[] = $res;
                $startAt += $res->maxResults;
            }
        } while (!empty($res) && $res->startAt + $res->maxResults < $res->total);

        if ($debug)
            echo 'Request JIRA done: ' . (microtime(true) - $start) . "\n";

        // if no issues returned, abort
        if (empty($res)) {
            return;
        }

        // calculate flat array of issues
        $issues = array();
        foreach ($responses as $res) {
            foreach ($res->issues as $i) {
                $issues[] = $i;
            }
        }

        // delete old wishes
        $this->clearAllWishes();

        if ($debug)
            echo 'Delete Posts done: ' . (microtime(true) - $start) . "\n";

        // insert new wishes
        foreach ($issues as $i) {
            $this->createWish($i);
        }

        if ($debug)
            echo 'Create new Wishes done: ' . (microtime(true) - $start) . "\n";
//
        if ($debug) {
            global $wpdb;
            echo "<pre>";
            foreach ($wpdb->queries as $q) {
                echo implode(", ", array_map(function($e) {
                            return '"' . $e . '"';
                        }, $q)) . "\n";
            }
            echo "</pre>";
            die();
        }
    }

    public function clearAllWishes() {

        wp_suspend_cache_addition();
        wp_cache_flush();

        $wish_post_type = App::getInstance()->getWishController()->getPostType();

        global $wpdb;
        $wpdb->query('DELETE FROM wp_posts WHERE post_type="' . $wish_post_type . '";');
        $wpdb->query('DELETE FROM wp_postmeta WHERE post_id NOT IN (SELECT id FROM wp_posts);');
    }

    private function createWish($i, $debug = false) {
        global $wpdb;
        $options = App::getInstance()->getOptions();

        $wish_post_type = App::getInstance()->getWishController()->getPostType();
        $region_taxonomy = App::getInstance()->getWishController()->getRegionTaxonomyName();

        $f = $i->fields;

        if ($f->project->key !== $this->project) {
            return;
        }

        // create post
        $states = WishPostType::getStates();
        $state_ids = array();

        foreach ($states as $state) {
            $state_ids[] = $options->get('jira_state', $state);
        }

        // publish all known states
        // unknown states ignore e.g. requested wishes, faulty wishes
        if (in_array($f->{self::JIRA_FIELD_STATUS}->id, $state_ids)) {
            $post_state = 'publish';
        } else {
            return;
        }

        $id = wp_insert_post(
                [
                    'post_title' => $i->key,
                    'post_type' => $wish_post_type,
                    'post_status' => $post_state
                ]
        );

        // add metas 
        $metas = [
            'key' => $i->{self::JIRA_FIELD_KEY},
            'status_id' => $f->{self::JIRA_FIELD_STATUS}->id,
            'status_name' => $f->{self::JIRA_FIELD_STATUS}->name,
            'link_to_jira' => $f->{self::JIRA_FIELD_LINK_TO_JIRA}->_links->web,
            'wichtel_id' => $f->{self::JIRA_FIELD_WICHTEL_ID},
            'wichtel_name' => $f->{self::JIRA_FIELD_WICHTEL_NAME},
            'wichtel_mail' => $f->{self::JIRA_FIELD_WICHTEL_MAIL},
            'price' => $f->{self::JIRA_FIELD_PRICE}->value,
            'summary' => $f->{self::JIRA_FIELD_SUMMARY},
            'description' => $f->{self::JIRA_FIELD_DESCRIPTION},
            'reporter_mail' => $f->{self::JIRA_FIELD_REPORTER}->name,
            'recipient' => $f->{self::JIRA_FIELD_RECIPIENT},
            'end_date' => substr($f->{self::JIRA_FIELD_END_DATE}, 0, 10),
            'last_wichtel_delivery_date' => substr($f->{self::JIRA_FIELD_LAST_WICHTEL_DELIVERY_DATE}, 0, 10),
            'found_wichtel_date' => substr($f->{self::JIRA_FIELD_FOUND_WICHTEL_DATE}, 0, 10),
            'priority' => ceil(rand(0, 1000)),
            'delivery_type' => $f->{self::JIRA_FIELD_DELIVERY_TYPE},
            'approve_id' => $f->{self::JIRA_FIELD_APPROVER}[0]->id
        ];

        if (!empty($metas['reporter_mail'])) {
            $query = "SELECT post_id FROM wp_postmeta WHERE meta_key = 'jira_user' AND meta_value LIKE '%" . $wpdb->esc_like($metas['reporter_mail']) . "%';";
            $results = $wpdb->get_results($query);

            if (!empty($results)) {
                $metas['social_organisation_id'] = array_pop($results)->post_id;
            }
        }

        $values = '';
        foreach ($metas as $key => $value) {
            $key = wp_unslash($key);
            $value = wp_unslash($value);
        }

        $this->add_post_metas($id, $metas);

        // add region     
        if (!empty($f->components)) {
            $components = $f->components;

            foreach ($components as $c) {
                wp_set_object_terms($id, $c->name, $region_taxonomy, true);
            }
        }
    }

    private function getDefaultCurlOptions() {
        return array(
            CURLOPT_USERAGENT => 'Wichtel Challenge Bot',
            CURLOPT_USERPWD => $this->username . ':' . $this->password,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_VERBOSE => 1,
            CURLOPT_HEADER => 1
        );
    }

    private function doGet($path, $debug = false) {
        $ch = curl_init($this->domain . $path);

        curl_setopt_array($ch, $this->getDefaultCurlOptions());
        $response = curl_exec($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        $body = json_decode($body);

        if ($debug) {
            echo '<h1>GET ' . $path . '</h1>';
            echo '<h2>Header</h2>';
            echo '<p>' . $header . '</p>';
            echo '<h2>Body</h2>';
            echo '<p>' . print_r($body, true) . '</p>';
            echo '<hr>';
            exit;
        }

        curl_close($ch);
        return $body;
    }

    private function doPost($path, $postData, $debug = false) {
        $ch = curl_init($this->domain . $path);

        $curl_options = $this->getDefaultCurlOptions();
        $curl_options[CURLOPT_POST] = 1;
        $curl_options[CURLOPT_POSTFIELDS] = $postData;
        $curl_options[CURLOPT_HTTPHEADER] = array('Content-Type:application/json');

        curl_setopt_array($ch, $curl_options);
        $response = curl_exec($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        $body = json_decode($body);

        if ($debug) {
            echo '<h1>POST ' . $path . '</h1>';
            echo 'Postdata: ' . $postData;
            echo '<h2>Header</h2>';
            echo '<p>' . $header . '</p>';
            echo '<h2>Body</h2>';
            echo '<p>' . print_r($body, true) . '</p>';
            echo '<hr>';
            die();
        }

        curl_close($ch);
        return $body;
    }

    // increase performance
    private function add_post_metas($id, $metas) {

        $values = array();
        foreach ($metas as $key => $value) {
            $key = esc_sql($key);
            $value = esc_sql($value);

            $values[] = '(' . $id . ', "' . $key . '", "' . $value . '")';
        }

        $query = 'INSERT INTO `wp_postmeta` (`post_id`, `meta_key`, `meta_value`) VALUES ' . implode(', ', $values) . ';';

        global $wpdb;
        $wpdb->get_results($query);
    }

    public function shuffleWishes() {
        $wish_type = App::getInstance()->getWishController()->getPostType();

        $wishes = get_posts(array(
            'post_type' => $wish_type,
            'limit' => -1,
            'posts_per_page' => -1
        ));

        foreach ($wishes as $wish) {
            update_post_meta($wish->ID, 'priority', ceil(rand(0, 1000)));
        }
    }

    public function setLastMinuteWishes() {
        $options = App::getInstance()->getOptions();
        $wish_controller = App::getInstance()->getWishController();

        $wishes = get_posts(array(
            'post_type' => $wish_controller->getPostType(),
            'limit' => -1,
            'posts_per_page' => -1
        ));

        foreach ($wishes as $wish) {
            $is_open = rwmb_meta('status_id', array(), $wish->ID) == $options->get('jira_state', WishPostType::STATE_OPEN);

            if ($is_open) {
                $delta_in_days = $wish_controller->getCurrentWichtelLastDeliveryDateDeltaInDays($wish->ID);
                $last_minute_threshold = $options->get('last_minute_threshold');
                
                if ($delta_in_days <= $last_minute_threshold) {
                    wp_set_object_terms($wish->ID, intval($options->get('last_minute_tag_id')), $wish_controller->getRegionTaxonomyName(), true);
                } else {
                    wp_remove_object_terms($wish->ID, intval($options->get('last_minute_tag_id')), $wish_controller->getRegionTaxonomyName());
                }
            }
        }
    }

}
