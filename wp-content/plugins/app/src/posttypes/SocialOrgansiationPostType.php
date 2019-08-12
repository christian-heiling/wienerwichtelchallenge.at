<?php

namespace app\posttypes;

class SocialOrganisationPostType extends AbstractPostType {

	function getSupports() {
		return array( 'title', 'thumbnail', 'revisions' );
	}

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
	                'id'   => 'logo',
	                'name' => __('Logo', 'app'),
	                'type' => 'image'
                ),
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
                        'link',
	                    'logo'
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

	public function generateRandomItem() {
		$faker = \Faker\Factory::create();

		$id = wp_insert_post(
			[
				'post_title' => $faker->text(80),
				'post_type' => $this->getPostType(),
				'post_status' => 'publish'
			]
		);

		$start = $faker->dateTimeBetween('-10 days', '+20 days')->getTimestamp();

		$metas = [
			'carrier' => $faker->text(10),
			'field_of_action' => $faker->randomElement($this->getFieldOfActionOptions()),
			'street' => $faker->streetAddress,
			'zip' => '1020',
			'city' => 'Wien',
			'map' => '48.1935651,16.3394902,12',
			'reachable_via' => 'U3, U6 Westbahnhof',
			'delivery_hours' => 'Mo-Fr von 10 - 17 Uhr',
			'contact' => $faker->text(120),
			'teaser' => $faker->text(120),
			'description' => $faker->text(500),
			'link' => $faker->url
		];

		foreach($metas as $key => $value) {
			add_post_meta($id, $key, $value, true);
		}
	}

	public function queryByFieldOfAction($fieldOfAction) {
		query_posts(array(
			'post_type' => $this->getPostType(),
			'meta_key' => 'field_of_action',
			'order' => 'ASC',
			'orderby' => 'title',
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => 'field_of_action',
					'value' => $fieldOfAction
				)
			)
		));
	}

}
