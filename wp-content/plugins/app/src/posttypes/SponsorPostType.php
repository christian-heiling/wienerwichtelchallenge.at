<?php

namespace app\posttypes;

class SponsorPostType extends AbstractPostType {
    
    public function getLabel() {
        return __( 'Sponsors', 'app' );
    }

    public function getMenuIcon() {
        return 'dashicons-heart';
    }

    public function getSlug() {
        return __('sponsors-slug', 'app');
    }

    public function getPostType() {
        return 'sponsor';
    }

    public function getSortableColumns() {
        return [];
    }
    
    public function addMetaBox($meta_boxes) {
        
        $meta_boxes[] = array(
            'title'  => __('Infos'),
            'post_types' => array($this->getPostType()),
            'fields' => array(
                array(
                    'id'   => 'logo',
                    'name' => __('Logo', 'app'),
                    'type' => 'image'
                ),
                array(
                    'id'   => 'teaser',
                    'name' => __('Teaser', 'app'),
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 8
                    )
                ),
                array(
                    'id'   => 'description',
                    'name' => __('Infos', 'app'),
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 8
                    )
                ),
                array(
                    'id'  => 'link',
                    'name' => __('Link', 'app'),
                    'type' => 'text',
                )
            )
        );
        return $meta_boxes;
    }
    
    public function setColumnHead() {
        $head['cb'] = '<input type="checkbox" />';
        $head['title'] = __("Title");
        $head['date'] = __("Date");
        
        return $head;
    }
    
    public function echoColumnBody($column_name, $post_ID) {
        
    }

    public function echoEntryMeta() {
        $this->outputMetaBoxContentWithSpans(array(
            'carrier', 'field_of_action', 'zip'
        ));
    }

    public function echoEntryContent() {
        $this->outputMetaBoxContentWithHeadings(
            array(
                array(
                    'field_ids' => array(
                        'description',
                        'link'
                    )
                )
            ),
            array(
               'first_heading' => '2' 
            )
        );
    }
    
    public function echoExcerptMeta() {
        // silence is gold
    }

    public function echoExcerptContent() {
        echo rwmb_meta('teaser');
    }


}
