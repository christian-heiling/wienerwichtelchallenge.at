<?php

namespace app;

require 'JiraHandler.php';
require 'OptionHandler.php';
require 'posttypes/AbstractPostType.php';
require 'posttypes/EventPostType.php';
require 'posttypes/SocialOrganisationPostType.php';
require 'posttypes/SponsorPostType.php';
require 'posttypes/WichtelTypePostType.php';
require 'posttypes/WishPostType.php';

use app\posttypes\EventPostType;

class App {

    /**
     * use a synchron cipher method
     * see https://www.php.net/manual/de/function.openssl-get-cipher-methods.php
     */
    const ENCRYPTION_CIPHER = 'AES-128-CBC';

    protected static $instance;
    private $controllers;
    private $options;

    /**
     * 
     * @return App
     */
    public static function getInstance() {
        if (empty(self::$instance)) {
            self::$instance = new App();
            return self::$instance;
        } else {
            return self::$instance;
        }
    }

    private function __construct() {
        $this->initPostTypes();
        $this->initOptionHandler();

        $this->registerHooks();
    }

    private function initPostTypes() {
        $this->controllers = [];

        $event = new posttypes\EventPostType();
        $socialOrganisation = new posttypes\SocialOrganisationPostType();
        $sponsor = new posttypes\SponsorPostType();

        require_once 'posttypes/WishPostType.php';
        $wish = new posttypes\WishPostType();

        $this->controllers[$event->getPostType()] = $event;
        $this->controllers[$socialOrganisation->getPostType()] = $socialOrganisation;
        $this->controllers[$sponsor->getPostType()] = $sponsor;
        $this->controllers[$wish->getPostType()] = $wish;
    }

    private function initOptionHandler() {
        $this->options = new OptionHandler();
    }

    private function registerHooks() {
        add_action('plugins_loaded', array($this, 'loadTextdomain'));
        add_action('wp_enqueue_scripts', array($this, 'addDashiconsToFrontend'));

        // do not show admin bar if subscriber
        add_action('wp', array($this, 'removeAdminBarForSubscribers'));

        // add import to admin bar
        add_action('admin_bar_menu', array($this, 'addFullImportToAdminBar'), 80);

        // add single import at get call
        add_action('wp', array($this, 'handleJiraRequests'), 1);

        add_action('init', array($this, 'doFullImport'));
        add_action('init', array($this, 'handleAjaxRequest'));
        add_action('init', array($this, 'afterInit'));

        add_action('bp_email_use_wp_mail', function() {
            return true;
        });
        //maybe with this hook we can activate html mails
        //add_filter('wp_mail_content_type', function() { return 'text/html'; });
    }

    public function handleJiraRequests() {
        if (isset($_GET['action'])) {
            $action = $_GET['action'];
        } else {
            return;
        }

        if (isset($_GET['key'])) {
            $key = $_GET['key'];

            $wishes = get_posts(array(
                'post_type' => $this->getWishController()->getPostType(),
                'title' => $key
            ));

            if (empty($wishes)) {
                return;
            }

            $wish = array_pop($wishes);

            if ($action == 'issueUpdate') {
                $this->getJiraHandler()->doImportSingleIssue($key);
                exit;
            } elseif ($action == 'sendMail') {
                $type = $_GET['type'];

                $types = array_map(
                        function($e) {
                    return $e['action'];
                }, $this->getWishController()->getMailTemplates()
                );

                if (in_array($type, $types)) {
                    bp_send_email($type, rwmb_get_value('wichtel_mail', [], $wish->ID), array('tokens' => $this->getWishController()->getMailTokens($wish->ID)));
                    exit;
                }
            }
        } else {
            return;
        }
    }

    public function removeAdminBarForSubscribers() {
        global $current_user;
        if (user_can($current_user, "subscriber")) {
            add_filter('show_admin_bar', '__return_false');
        }
    }

    public function loadTextdomain() {
        load_plugin_textdomain('app', FALSE, basename(dirname(dirname(__FILE__))) . '/languages/');
    }

    public function addDashiconsToFrontend() {
        wp_enqueue_style('dashicons');
    }

    public function getController($postType) {
        if ($postType == false) {
            return null;
        }

        if (array_key_exists($postType, $this->controllers)) {
            return $this->controllers[$postType];
        } else {
            return null;
        }
    }

    /**
     * @return EventPostType
     */
    public function getEventController() {
        return $this->controllers['event'];
    }

    /**
     * @return SponsorPostType
     */
    public function getSponsorController() {
        return $this->controllers['sponsor'];
    }

    /**
     * @return SocialOrganisationPostType
     */
    public function getSocialOrganisationController() {
        return $this->controllers['social_organisation'];
    }

    /**
     * 
     * @return posttypes\WishPostType
     */
    public function getWishController() {
        return $this->controllers['wish'];
    }

    /**
     * @return OptionHandler
     */
    public function getOptions() {
        return $this->options;
    }

    public function afterInit() {
    }

    /**
     * 
     * @return \app\JiraHandler
     */
    public function getJiraHandler() {
        require_once 'JiraHandler.php';
        return new JiraHandler();
    }

    public function addFullImportToAdminBar($adminBar) {
        global $current_user;

        if (user_can($current_user, 'administrator')) {
            $args = array(
                'id' => 'jira_full_import',
                'title' => __('Full Import', 'app'),
                'href' => '?doFullImport=1',
                'parent' => false
            );

            $adminBar->add_node($args);
        }
    }

    public function doFullImport() {
        global $current_user;
        if (user_can($current_user, 'administrator') && array_key_exists('doFullImport', $_GET) && $_GET['doFullImport'] == 1) {
            $this->getJiraHandler()->doFullImport();

            // afterwards redirect to wish list in admin
            header('Location: ' . get_home_url() . '/wp-admin/edit.php?post_type=' . $this->getWishController()->getPostType());
            exit;
        }
    }

    public function handleAjaxRequest() {
        global $current_user;

        if (user_can($current_user, 'administrator') && array_key_exists('ajax-action', $_GET)) {
            $action = $_GET['ajax-action'];

            if ($action == 'clearAllWishes') {
                $this->getJiraHandler()->clearAllWishes();
            } elseif ($action == 'doPartialImport') {
                if (!array_key_exists('part', $_GET)) {
                    http_response_code(404);
                    exit;
                }
                $this->getJiraHandler()->doPartialImport($_GET['part']);
            } elseif ($action == 'doFullImport') {
                $this->getJiraHandler()->doFullImport();
            } elseif ($action == 'shuffleWishes') {
                $this->getJiraHandler()->shuffleWishes();
            } else {
                http_response_code(404);
            }

            exit;
        }
    }

    public function encrypt($plaintext) {
        $cipher = self::ENCRYPTION_CIPHER;
        $key = SECURE_AUTH_KEY;
        $salt = SECURE_AUTH_SALT;

        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = substr($salt, 0, $ivlen);
        $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
        $ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);

        return $ciphertext;
    }

    public function decrypt($ciphertext) {
        $cipher = self::ENCRYPTION_CIPHER;
        $key = SECURE_AUTH_KEY;
        $salt = SECURE_AUTH_SALT;
        
        $c = base64_decode($ciphertext);
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = substr($salt, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len = 32);
        $ciphertext_raw = substr($c, $ivlen + $sha2len);
        $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
        if (hash_equals($hmac, $calcmac)) { // PHP 5.6+ Rechenzeitangriff-sicherer Vergleich
            return $original_plaintext;
        }
    }

}
