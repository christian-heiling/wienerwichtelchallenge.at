<?php

namespace app\posttypes;

class WichtelTypePostType extends AbstractPostType {
    
    public function getLabel() {
        return __( 'Wichtel Types', 'app' );
    }

    public function getMenuIcon() {
        return 'dashicons-buddicons-activity';
    }

    public function getPostType() {
        return 'wichtel_type';
    }

    public function getSlug() {
        return __('wichtel-type-slugs', 'app');
    }

    public function getSortableColumns() {
        return [];
    }
    
    public function addMetaBox($meta_boxes) {
        
        $meta_boxes[] = array(
            'title'  => __('Infos'),
            'post_types' => array($this->getSlug()),
            'fields' => array(
                array(
                    'id'   => 'description',
                    'type' => 'datetime',
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 4
                    )
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
        
    }

    public function echoEntryContent() {
        
    }

    public function echoExcerptContent() {
        
    }

    public function echoExcerptMeta() {
        
    }

	public function generateRandomItem() {

	}

}
