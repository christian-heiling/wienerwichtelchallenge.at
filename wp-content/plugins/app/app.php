<?php 

/*
	Plugin Name: App
	Plugin URI: https://www.wienerwichtelchallenge.at
	Description: Add App Functionality
	Author: Christian Heiling
	Author URI: https://christian-heiling.com
	Requires PHP: 7.0
	Text Domain: app
	Domain Path: /languages
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require 'src/App.php';

if (!function_exists('array_key_first')) {
    function array_key_first(array $arr) {
        foreach($arr as $key => $unused) {
            return $key;
        }
        return NULL;
    }
}


function svd_deactivate() {
    wp_clear_scheduled_hook( 'app_daily_cron' );
}
 
add_action('init', function() {
    add_action( 'app_daily_cron', 'svd_run_cron' );
    register_deactivation_hook( __FILE__, 'svd_deactivate' );
 
    if (! wp_next_scheduled ( 'app_daily_cron' )) {
        wp_schedule_event( time(), 'daily', 'app_daily_cron' );
    }
});

function svd_run_cron() {
    \app\App::getInstance()->getJiraHandler()->shuffleWishes();
    \app\App::getInstance()->getJiraHandler()->setLastMinuteWishes();
}

$app = \app\App::getInstance();