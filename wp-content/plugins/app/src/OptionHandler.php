<?php

namespace app;

class OptionHandler {
    
    private $options;
    private $optionItem;
    
    public function __construct() {
        $this->registerHooks();
    }
    
    function getPostType() {
        return 'app_options';
    }
    
    function getSlug() {
        return 'app_options';
    }
    
    function getLabel() {
        return __('Wichtel Challenge Options', 'app');
    }
    
    function getSupports() {
        return ['revisions'];
    }

    protected function registerHooks() {
        add_action( 'init', array($this, 'registerPostType'), 0 );
        add_action( 'admin_head', array($this, 'addOptionItemIfNotExist'));
        add_action( 'admin_menu', array($this, 'addMenuItem'));
        
        add_filter( 'rwmb_meta_boxes', array($this, 'addMetaBox') );
    }
    
    public function registerPostType() {
        $args = array(
                'label'                 => $this->getLabel(),
                'supports'              => $this->getSupports(),
                'hierarchical'          => false,
                'public'                => true,
                'show_ui'               => true,
                'show_in_menu'          => false,
                'show_in_admin_bar'     => false,
                'show_in_nav_menus'     => false,
                'can_export'            => true,
                'has_archive'           => false,
                'exclude_from_search'   => true,
                'publicly_queryable'    => false,
                'capability_type'       => 'page',
                'show_in_rest'          => false
        );
        register_post_type( $this->getPostType(), $args );
    }
    
    public function addOptionItemIfNotExist() {
        $item = $this->getOptionItem();
        
        if (empty($item)) {
            // create item
            wp_insert_post([
                'post_type' => $this->getPostType(),
                'post_status' => 'publish'
            ]);
        }
    }
    
    private function getOptionItem() {
        
        if (!empty($this->optionItem)) {
            return $this->optionItem;
        }
        
        $items = get_posts(array(
            'post_type' => $this->getPostType(),
            'numberposts' => 1
        ));
        
        if (empty($items)) {
            return null;
        } else {
            $this->optionItem = array_pop($items);
            return $this->optionItem;
        }
    }
    
    public function addMenuItem() {
        add_menu_page(
            $this->getLabel(), 
            $this->getLabel(), 
            'publish_pages', 
            $this->getSlug(), 
            function() {
                echo '<script>';
                echo 'window.location.replace("/wp-admin/post.php?post=' . $this->getOptionItem()->ID . '&action=edit");';
                echo '</script>';
            }, 
            'dashicons-admin-generic',
            30
        );
    }
    
    public function addMetaBox($meta_boxes) {       
        $meta_boxes[] = array(
            'title'  => __('General'),
            'post_types' => array($this->getPostType()),
            'fields' => array(
                array(
                    'id'   => 'link_tac',
                    'name' => __('Link to Terms & Conditions', 'app'),
                    'type' => 'text',
                ),
                array(
                    'id'   => 'copyright_year',
                    'name' => __('Start Year of Copyright', 'app'),
                    'type' => 'text',
                ),
                array(
                    'id'   => 'start_header',
                    'name' => __('Header of the Start Page', 'app'),
                    'type' => 'text',
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 8
                    )
                )
            ),
        );
        
        $meta_boxes[] = array(
            'title'  => __('Map'),
            'post_types' => array($this->getSlug()),
            'fields' => array(
                array(
                    'id'   => 'street',
                    'name' => __('Street', 'app'),
                    'type' => 'text'
                ),
                array(
                    'id'   => 'zip',
                    'name' => __('ZIP', 'app'),
                    'type' => 'text'
                ),
                array(
                    'id'   => 'city',
                    'name' => __('City', 'app'),
                    'type' => 'text'
                ),
                array(
                    'id' => 'map',
                    'name' => __('Map', 'app'),
                    'type' => 'osm',
                    'address_field' => 'street,zip,city'
                ),
            )
        );
        
        $meta_boxes[] = array(
            'title'  => __('Event Options'),
            'post_types' => array($this->getPostType()),
            'fields' => array(
                array(
                    'id'   => 'event_archive_teaser',
                    'name' => __('Archive Teaser', 'app'),
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 8
                    )
                ),
                array(
                    'id' => 'event_header_image',
                    'name' => __('Header Image', 'app'),
                    'type' => 'image_advanced',
                    'force_delete' => false,
                    'max_file_uploads' => 1,
                    'image_size' => 'medium'
                )
            ),
        );

        $meta_boxes[] = array(
            'title'  => __('Social Organisation Options'),
            'post_types' => array($this->getPostType()),
            'fields' => array(
                array(
                    'id'   => 'social_organisation_archive_teaser',
                    'name' => __('Archive Teaser', 'app'),
                    'type' => 'text',
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 8
                    )
                ),
                array(
                    'id' => 'social_organisation_header_image',
                    'name' => __('Header Image', 'app'),
                    'type' => 'image_advanced',
                    'force_delete' => false,
                    'max_file_uploads' => 1,
                    'image_size' => 'medium'
                ),
            ),
        );

        $meta_boxes[] = array(
            'title'  => __('Sponsor Options'),
            'post_types' => array($this->getPostType()),
            'fields' => array(
                array(
                    'id'   => 'sponsor_archive_teaser',
                    'name' => __('Archive Teaser', 'app'),
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 8
                    )
                ),
                array(
                    'id' => 'sponsor_header_image',
                    'name' => __('Header Image', 'app'),
                    'type' => 'image_advanced',
                    'force_delete' => false,
                    'max_file_uploads' => 1,
                    'image_size' => 'medium'
                )
            ),
        );

        $meta_boxes[] = array(
            'title'  => __('Wichtel Type Options'),
            'post_types' => array($this->getPostType()),
            'fields' => array(
                array(
                    'id'   => 'wichtel_type_archive_teaser',
                    'name' => __('Archive Teaser', 'app'),
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 8
                    )
                ),
                array(
                    'id' => 'wichtel_type_header_image',
                    'name' => __('Header Image', 'app'),
                    'type' => 'image_advanced',
                    'force_delete' => false,
                    'max_file_uploads' => 1,
                    'image_size' => 'medium'
                )
            ),
        );

        return $meta_boxes;
    }
    
    public function get($a, $b = null) {
        
        if (empty($b)) {
            $name = $a;
        } else {
            $name = $a . '_' . $b;
        }
        
        return rwmb_get_value($name, [], $this->getPostId());
    }

    public function getPostId() {
        return $this->getOptionItem()->ID;
    }
}
