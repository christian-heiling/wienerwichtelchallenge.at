<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

add_action('widgets_init', function() {
    
    $sidebars = array(
        'page-sidebar' => __('Page Sidebar', 'app'),
        'post-sidebar' => __('Post Sidebar', 'app'),
        'event-sidebar' => __('Event Sidebar', 'app'),
        'social-organisation-sidebar' => __('Social Organisation Sidebar', 'app'),
        'sponsor-sidebar' => __('Sponsor Sidebar', 'app')
    );
    
    asort($sidebars);
          
    $defaults = array(
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget'  => '</li>',
	'before_title'  => '<h2 class="widgettitle">',
	'after_title'   => '</h2>'
    );
    
    foreach($sidebars as $id => $name) {
        register_sidebar($defaults + ['id' => $id, 'name' => $name]);
    }
    
});