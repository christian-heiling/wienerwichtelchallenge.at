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

require 'vendor/autoload.php';
require 'src/App.php';

if (!function_exists('array_key_first')) {
    function array_key_first(array $arr) {
        foreach($arr as $key => $unused) {
            return $key;
        }
        return NULL;
    }
}

$app = \app\App::getInstance();