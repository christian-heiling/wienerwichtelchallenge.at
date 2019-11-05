<?php

namespace app;

class JiraHandler {

    private $domain;
    private $username;
    private $password;
    private $project;

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

    public function search($jql, $startAt = 0) {
        return $this->doGet('/rest/api/2/search?jqp=' . urlencode($jql) . '&startAt=' . $startAt);
    }

    public function doFullImport() {
        $options = App::getInstance()->getOptions();

        $wish_post_type = App::getInstance()->getWishController()->getPostType();
        $responses = array();

        // query all issues
        $startAt = 0;

        do {
            $res = $this->search('project="' . $this->project . '"', $startAt);

            if (!empty($res)) {
                $responses[] = $res;
                $startAt += $res->maxResults;
            }
        } while (!empty($res) && $res->maxResults == $res->total);

        // if no issues returned, abort
        if (empty($responses)) {
            return;
        }

        // delete old wishes
        global $wpdb;
        $wpdb->query('DELETE FROM wp_posts WHERE post_type="' . $wish_post_type . '";');
        $wpdb->query('DELETE FROM wp_postmeta WHERE post_id NOT IN (SELECT id FROM wp_posts);');

        // insert new wishes
        foreach ($responses as $res) {
            foreach ($res->issues as $i) {

                $f = $i->fields;

                $metas = [
                    'key' => $i->key,
                    'status_id' => $f->status->id,
                    'status_name' => $f->status->name,
                    'link_to_jira' => $f->customfield_10109->_links->web,
                    'wichtel_id' => $f->customfield_10116,
                    'wichtel_name' => $f->customfield_10142,
                    'wichtel_mail' => $f->customfield_10143,
                    'price' => $f->customfield_10117->value,
                    'summary' => $f->summary,
                    'description' => $f->description,
                    'reporter_mail' => $f->reporter->name,
                    'recipient' => $f->customfield_10120,
                    'address' => $f->customfield_10121,
                    'zip' => $f->customfield_10122,
                    'end_date' => $f->duedate,
                    'last_wichtel_delivery_date' => $f->customfield_10118,
                    'found_wichtel_date' => $f->customfield_10119
                ];

                if (!empty($i->components)) {
                    $metas['region'] = array_pop($i->components)->name;
                }

                if (!empty($metas['reporter_mail'])) {

                    $query = "SELECT post_id FROM wp_postmeta WHERE meta_key = 'jira_user' AND meta_value LIKE '%" . $wpdb->esc_like($metas['reporter_mail']) . "%';";
                    $results = $wpdb->get_results($query);

                    if (!empty($results)) {
                        $metas['social_organisation_id'] = array_pop($results)->post_id;
                    }
                }
                
                if (in_array($metas['status_id'],
                array(
                    $options->get('jira_state', 'offen'),
                    $options->get('jira_state', 'in_arbeit'),
                    $options->get('jira_state', 'erfuellt'),
                    $options->get('jira_state', 'abgeschlossen'),
                ))) {
                    $post_state = 'publish';
                } else {
                    $post_state = 'draft';
                }

                $id = wp_insert_post(
                        [
                            'post_title' => $i->key,
                            'post_type' => $wish_post_type,
                            'post_status' => $post_state
                        ]
                );

                foreach ($metas as $key => $value) {
                    add_post_meta($id, $key, $value, true);
                }
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
        }

        curl_close($ch);
        return $body;
    }

}
