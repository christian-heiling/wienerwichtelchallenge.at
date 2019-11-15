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
        return __('Wichtelchallenge Options', 'app');
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
            $this->options = get_post_custom($this->optionItem->ID);
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
        
        $shortcodes = array(
            '<code>[wichtel_end_date]</code>',
            '<code>[wichtel_end_date_delta_in_days]</code>',
        );
        
        $boxes = App::getInstance()->getWishController()->addMetaBox([]);
        $fields = array_pop($boxes)['fields'];
        foreach($fields as $field) {
            $shortcodes[] = '<code>[' . $field['id'] . ']</code>';
        }
        $shortcodes_description = sprintf(__('You can use following snippets to add content from the wish: %s', 'app'), implode(', ', $shortcodes));
        
        $meta_boxes[] = array(
            'title'  => __('General'),
            'post_types' => array($this->getPostType()),
            'fields' => array(
                array(
                    'id'   => 'country',
                    'name' => __('Country', 'app'),
                    'type' => 'text',
                ),
                array(
                    'id' => 'header_image',
                    'name' => __('Header Image', 'app'),
                    'type' => 'image_advanced',
                    'force_delete' => false,
                    'max_file_uploads' => 1,
                    'image_size' => 'medium'
                ),
                array(
                    'id'   => 'start_header',
                    'name' => __('Header of the Start Page', 'app'),
                    'type' => 'text',
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 8
                    )
                ),
                array(
                    'id'   => 'footer',
                    'name' => __('Footer', 'app'),
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 8
                    )
                ),
                array(
                    'id'   => 'copyright_year',
                    'name' => __('Start Year of Copyright', 'app'),
                    'type' => 'text',
                )
            ),
        );
        
        $meta_boxes[] = array(
            'title'  => __('JIRA Connection Settings', 'app'),
            'post_types' => array($this->getPostType()),
            'fields' => array(
                array(
                    'id' => 'jira_domain',
                    'name' => __('Domain', 'app'),
                    'type' => 'url'
                ),
                array(
                    'id' => 'jira_username',
                    'name' => __('User Name', 'app'),
                    'type' => 'text'
                ),
                array(
                    'id' => 'jira_password',
                    'name' => __('Password', 'app'),
                    'type' => 'text'
                ),
                array(
                    'id' => 'jira_project',
                    'name' => __('Project', 'app'),
                    'type' => 'text'
                )
        ));
        
        $meta_boxes[] = array(
            'title'  => __('JIRA State Ids', 'app'),
            'post_types' => array($this->getPostType()),
            'fields' => array(
                array(
                    'id' => 'jira_state_offen',
                    'name' => __('Offen', 'app'),
                    'type' => 'text' 
                ),
                array(
                    'id' => 'jira_state_in_arbeit',
                    'name' => __('In Arbeit', 'app'),
                    'type' => 'text'
                ),
                array(
                    'id' => 'jira_state_erfuellt',
                    'name' => __('Erfüllt', 'app'),
                    'type' => 'text' 
                ),
                array(
                    'id' => 'jira_state_abgeschlossen',
                    'name' => __('Abgeschlossen', 'app'),
                    'type' => 'text' 
                )
            )
        );
        
        $meta_boxes[] = array(
            'title'  => __('Before Wish Letter', 'app'),
            'post_types' => array($this->getPostType()),
            'fields' => array(
                array(
                    'id' => 'jira_state_pre_offen',
                    'name' => __('Offen', 'app'),
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 8
                    ),
                    'desc' => $shortcodes_description
                ),
                array(
                    'id' => 'jira_state_pre_in_arbeit',
                    'name' => __('In Arbeit', 'app'),
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 8
                    ),
                    'desc' => $shortcodes_description
                ),
                array(
                    'id' => 'jira_state_pre_erfuellt',
                    'name' => __('Erfüllt', 'app'),
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 8
                    ),
                    'desc' => $shortcodes_description
                ),
                array(
                    'id' => 'jira_state_pre_abgeschlossen',
                    'name' => __('Abgeschlossen', 'app'),
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 8
                    ),
                    'desc' => $shortcodes_description
                )
            )
        );
        
        $meta_boxes[] = array(
            'title'  => __('JIRA Transition Ids', 'app'),
            'post_types' => array($this->getPostType()),
            'fields' => array(
                array(
                    'id' => 'jira_transition_vergeben',
                    'name' => __('Vergeben: Offen => In Arbeit', 'app'),
                    'type' => 'text' 
                ),
                array(
                    'id' => 'jira_transition_erfuellen',
                    'name' => __('Erfüllen: In Arbeit => Erfüllt', 'app'),
                    'type' => 'text'
                ),
                array(
                    'id' => 'jira_transition_zuruecklegen',
                    'name' => __('Zurücklegen: In Arbeit => Offen', 'app'),
                    'type' => 'text' 
                )
            )
        );
        
        $meta_boxes[] = array(
            'title'  => __('Transition: Confirm Question', 'app'),
            'post_types' => array($this->getPostType()),
            'fields' => array(
                array(
                    'id' => 'jira_transition_question_vergeben',
                    'name' => __('Vergeben: Offen => In Arbeit', 'app'),
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 8
                    ),
                    'desc' => $shortcodes_description
                ),
                array(
                    'id' => 'jira_transition_question_erfuellen',
                    'name' => __('Erfüllen: In Arbeit => Erfüllt', 'app'),
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 8
                    ),
                    'desc' => $shortcodes_description
                ),
                array(
                    'id' => 'jira_transition_question_zuruecklegen',
                    'name' => __('Zurücklegen: In Arbeit => Offen', 'app'),
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 8
                    ),
                    'desc' => $shortcodes_description
                ),
                array(
                    'id' => 'jira_transition_question_not_logged_in',
                    'name' => __('Fallback: If user is not logged in', 'app'),
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 8
                    ),
                    'desc' => $shortcodes_description
                ),
            )
        );
        
        $meta_boxes[] = array(
            'title'  => __('Transition: Button Caption', 'app'),
            'post_types' => array($this->getPostType()),
            'fields' => array(
                array(
                    'id' => 'jira_transition_button_vergeben',
                    'name' => __('Vergeben: Offen => In Arbeit', 'app'),
                    'type' => 'text'
                ),
                array(
                    'id' => 'jira_transition_button_erfuellen',
                    'name' => __('Erfüllen: In Arbeit => Erfüllt', 'app'),
                    'type' => 'text'
                ),
                array(
                    'id' => 'jira_transition_button_zuruecklegen',
                    'name' => __('Zurücklegen: In Arbeit => Offen', 'app'),
                    'type' => 'text'
                )
            )
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
                )
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
                )
            ),
        );

        return $meta_boxes;
    }
    
    public function get($a, $b = null) {
        
        if (empty($this->options)) {
            $this->getOptionItem();
        }
        
        if (empty($b)) {
            $name = $a;
        } else {
            $name = $a . '_' . $b;
        }
              
        $value = $this->options[$name][0];
        return $value;
    }

    public function getPostId() {
        return $this->getOptionItem()->ID;
    }
}
