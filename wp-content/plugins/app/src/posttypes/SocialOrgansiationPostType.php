<?php

namespace app\posttypes;

class SocialOrganisationPostType extends AbstractPostType {
    
    public function echoColumnBody($column_name, $post_ID) {
        if (in_array($column_name, ['carrier', 'field_of_action', 'zip'])) {
            echo rwmb_meta($column_name, [], $post_ID);
        }
    }

    public function getLabel() {
       return __( 'Social Organisations', 'app' ); 
    }

    public function getMenuIcon() {
        return 'dashicons-admin-home';
    }

    public function getPostType() {
        return 'social_organisation';
    }

    public function getSlug() {
        return __('organisations-slug', 'app');
    }

    public function getSortableColumns() {
        return array('carrier', 'field_of_action', 'zip');
    }
    
    public function addMetaBox($meta_boxes) {
        
        $meta_boxes[] = array(
            'title'  => __('Infos'),
            'post_types' => array($this->getPostType()),
            'fields' => array(
                array(
                    'id'   => 'carrier',
                    'name' => __('Carrier', 'app'),
                    'type' => 'text'
                ),
                array(
                    'id'   => 'field_of_action',
                    'name' => __('Field of Action', 'app'),
                    'type' => 'select',
                    'flatten' => true,
                    'options' => $this->getFieldOfActionOptions()
                ),
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
                    'std' => '48.20849,16.37208,13',
                    'address_field' => 'street,zip,city'
                ),
                array(
                    'id'   => 'reachable_via',
                    'name' => __('Public Reachable via', 'app'),
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 4,
                        'media_buttons' => false,
                        'teeny' => true
                    )
                ),
                array(
                    'id'   => 'delivery_hours',
                    'name' => __('Delivery hours', 'app'),
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 4,
                        'media_buttons' => false,
                        'teeny' => true
                    )
                ),
                array(
                    'id'   => 'contact',
                    'name' => __('Contact', 'app'),
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 4,
                        'media_buttons' => false,
                        'teeny' => true
                    )
                ),
                array(
                    'id'   => 'teaser',
                    'name' => __('Teaser', 'app'),
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 4,
                        'media_buttons' => false,
                        'teeny' => true
                    )
                ),
                array(
                    'id'   => 'description',
                    'name' => __('About the Social Organisation', 'app'),
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 8,
                        'media_buttons' => false,
                        'teeny' => true
                    )
                ),
                array(
                    'id'   => 'link',
                    'name' => __('Link', 'app'),
                    'type' => 'text'
                )
            )
        );
        
        return $meta_boxes;
    }
    
    public function getFieldOfActionOptions() {
        $field_of_actions = array(
            __('Homeless Assistance', 'app'),
            __('Elderly People', 'app'),
            __('Health Care', 'app'),
            __('Children Welfare', 'app'),
            __('Migration and Asylum', 'app'),
            __('People with Disabilities', 'app'),
            __('Addiction', 'app')
        );
        
        $field_of_actions = array_combine($field_of_actions, $field_of_actions);
        asort($field_of_actions);
        
        return $field_of_actions;
    }
    
    public function setColumnHead() {
        $head['cb'] = '<input type="checkbox" />';
        $head['title'] = __('Social Organisation', 'app');
        $head['carrier'] = __('Carrier', 'app');
        $head['field_of_action'] = __('Field of Action', 'app');
        $head['zip'] = __('ZIP', 'app');
        $head['date'] = __('Date');
        
        return $head;
    }
    
    
    /**
     * 
     * @param \WP_Query $query
     */
    public function sortColumns($query) {
        
        if ($query->is_main_query && ( $orderby = $query->get('orderby'))) {
            if (in_array($orderby, $this->getSortableColumns())) {
                $query->set('meta_key', $orderby);
                $query->set('orderby', 'meta_value');
            }
        }
    }

    public function echoEntryMeta() {
        echo '<span>' . rwmb_meta('description') . '</span>';
        $this->outputMetaBoxContentWithSpans(array(
            'carrier', 'field_of_action', 'zip'
        ));
    }

    public function echoEntryContent() {
        $this->outputMetaBoxContentWithHeadings(
            array(
                array(
                    'section_name' => __('Delivery Infos', 'app'),
                    'field_ids' => array(
                        'delivery_hours',
                        'contact'
                    )
                ),
                array(
                    'section_name' => __('Location', 'app'),
                    'field_ids' => array(
                        'map',
                        'street',
                        'zip',
                        'city',
                        'reachable_via'
                    )
                ),
                array(
                    'field_ids' => array(
                        'link'
                    )
                ),
            ),
            array(
               'first_heading' => '2' 
            )
        );
    }
    
    public function echoExcerptMeta() {
        $this->outputMetaBoxContentWithSpans(array(
            'carrier', 'field_of_action', 'zip'
        ));
    }

    public function echoExcerptContent() {
        echo rwmb_meta('teaser');
    }

}
