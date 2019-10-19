<?php

namespace app;

use app\posttypes\EventPostType;
use app\posttypes\CityPostType;

class App {
    
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
        //setup carbon
        \Carbon\Carbon::setLocale(substr(get_locale(), 0, 2));
        
        $this->initPostTypes();
        $this->initOptionHandler();
        
        $this->registerHooks();
    }
    
    private function initPostTypes() {
        $this->controllers = [];
        
        $event = new posttypes\EventPostType();
        $socialOrganisation = new posttypes\SocialOrganisationPostType();
        $sponsor = new posttypes\SponsorPostType();
        
        $this->controllers[$event->getPostType()] = $event;
        $this->controllers[$socialOrganisation->getPostType()] = $socialOrganisation;
        $this->controllers[$sponsor->getPostType()] = $sponsor;
    }
    
    private function initOptionHandler() {
        $this->options = new OptionHandler();
    }
    
    private function registerHooks() {
        add_action( 'plugins_loaded', array($this, 'loadTextdomain') );
        add_action( 'wp_enqueue_scripts', array($this, 'addDashiconsToFrontend') );

        // generate Items
	add_action('plugins_loaded', array($this, 'generateTestItems'));
        
        // do not limit query
        add_action('pre_get_posts', array($this, 'unlimitTheQuery'));
        
    }
    
    public function loadTextdomain() {
        load_plugin_textdomain( 'app', FALSE, basename( dirname(dirname( __FILE__ )) ) . '/languages/' );
    }
    
    public function addDashiconsToFrontend() {
        wp_enqueue_style( 'dashicons' );
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
     * @return OptionHandler
     */
    public function getOptions() {
        return $this->options;
    }

    public function generateTestItems() {

		//for($i = 0; $i < 20, $i++)
	    //$this->getEventController()->generateRandomItem();
	    //$this->getController('social_organisation')->generateRandomItem();
	    //$this->getController('sponsor')->generateRandomItem();

    }
    
    public function unlimitTheQuery($query) {
        $query->set('posts_per_page', 1000);
    }
}