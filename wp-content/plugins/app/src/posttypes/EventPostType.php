<?php

namespace app\posttypes;

class EventPostType extends AbstractPostType {
    
    public function getLabel() {
        return __( 'Events', 'app' );
    }

    public function getMenuIcon() {
        return 'dashicons-calendar-alt';
    }

    public function getPostType() {
        return 'event';
    }

    public function getSlug() {
        return __('events-slug', 'app');
    }

    public function getSortableColumns() {
        return array('start', 'end');
    }
    
    public function addMetaBox($meta_boxes) {       
        $meta_boxes[] = array(
            'title'  => __('Time'),
            'post_types' => array($this->getPostType()),
            'fields' => array(
                array(
                    'id'   => 'start',
                    'name' => __('Starts', 'app'),
                    'type' => 'datetime',
                    'options' => array(
                        'timestamp' => true
                    )
                ),
                array(
                    'id'   => 'end',
                    'name' => __('Ends', 'app'),
                    'type' => 'datetime',
                    'options' => array(
                        'timestamp' => true
                    )
                )
            ),
        );
        
        $meta_boxes[] = array(
            'title'  => __('Location'),
            'post_types' => array($this->getPostType()),
            'fields' => array(
                array(
                    'id'   => 'location_name',
                    'name' => __('Name', 'app'),
                    'type' => 'text'
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
                    //'std' => '48.20849,16.37208,13',
                    'address_field' => 'street,zip,city'
                ),
                array(
                    'id'   => 'reachable_via',
                    'name' => __('Public Reachable via', 'app'),
                    'type' => 'textarea'
                )
            )
        );
        
        $meta_boxes[] = array(
            'title'  => __('Description'),
            'post_types' => array($this->getPostType()),
            'fields' => array(
                array(
                    'id'   => 'category',
                    'name' => __('Category', 'app'),
                    'type' => 'text',
                    'options' => array(
                        'textarea_rows' => 4
                    )
                ),
                array(
                    'id'   => 'teaser',
                    'name' => __('Teaser', 'app'),
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 4
                    )
                ),
                array(
                    'id'   => 'description',
                    'name' => __('Press Text', 'app'),
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 8
                    )
                )
            ),
        );
        
        return $meta_boxes;
    }
    
    public function setColumnHead() {
        $head['cb'] = '<input type="checkbox" />';
        $head['title'] = __('Title', 'app');
        $head['location_name'] = __('Location', 'app');
        $head['start'] = __('Start at', 'app');
        $head['end'] = __('End at', 'app');
        $head['date'] = __('Date', 'app');
        return $head;
    }

    public function echoColumnBody($column_name, $post_ID) {
        if (in_array($column_name, ['location_name'])) {
            echo rwmb_meta($column_name, [], $post_ID);
        }
        
        if (in_array($column_name, ['start', 'end'])) {
            $date = new \Carbon\Carbon(rwmb_meta($column_name, [], $post_ID), get_option('timezone_string'));
            echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $date->timestamp)
                . ' (' . $date->diffForHumans() . ')';
        }
    }

    public function echoEntryMeta() {


        ?>
<div class="calender">
    <span class="month"></span>
    <span class="weekday"></span>
    <span class="day"></span>
</div>
        <?php

        $this->outputMetaBoxContentWithSpans(array(
            'location_name', 'zip'
        ));
    }

    public function echoEntryContent() {
        $this->outputMetaBoxContentWithHeadings(
            array(
                array(
                    'field_ids' => array(
                        'description'
                    )
                ),
                array(
                    'section_name' => __('Time', 'app'),
                    'field_ids' => array(
                        'start',
                        'end'
                    )
                ),
                array(
                    'section_name' => __('Location', 'app'),
                    'field_ids' => array(
                        'map',
                        'location_name',
                        'street',
                        'zip',
                        'city',
                        'reachable_via'
                    )
                )
            ), 
            array(
                'first_heading' => 2
            )
        );
    }
    
    public function echoExcerptMeta() {
        $this->outputMetaBoxContentWithSpans(array(
            'start', 'end', 'location_name', 'zip'
        ));
    }

    public function echoExcerptContent() {
        echo rwmb_meta('teaser');
    }

}