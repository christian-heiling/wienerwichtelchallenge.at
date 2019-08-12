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
		            'id'   => 'type',
		            'name' => __('Type', 'app'),
		            'type' => 'select',
		            'flatten' => true,
		            'options' => $this->getTypes()
	            ),
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
        $head['type'] = __("Type", "app");
        $head['date'] = __("Date");
        
        return $head;
    }
    
    public function echoColumnBody($column_name, $post_ID) {
        if (in_array($column_name, array('type'))) {
	        echo rwmb_meta($column_name, [], $post_ID);
        }
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
                        'link',
	                    'logo'
                    )
                )
            ),
            array(
               'first_heading' => '2' 
            )
        );
    }
    
    public function echoExcerptMeta() {
	    $logo = array_pop(rwmb_meta( 'logo', array( 'limit' => 1 ) ));

		?>
	    <figure class="wp-block-image is-resized overflow">
		    <img src="<?php echo $logo['full_url'] ?>"
		         alt=""
		         class="wp-image-<?php echo $logo['ID']; ?>"
		         srcset="<?php echo $logo['srcset']; ?>"
		         sizes="(max-width: 1920px) 100vw, 1920px"
		         width="1920"
		         height="516">
	    </figure>
		<?php
    }

    public function echoExcerptContent() {
        echo rwmb_meta('teaser');
    }

	public function generateRandomItem() {
		$faker = \Faker\Factory::create();

		$id = wp_insert_post(
			[
				'post_title' => $faker->company,
				'post_type' => $this->getPostType(),
				'post_status' => 'publish'
			]
		);

		$start = $faker->dateTimeBetween('-10 days', '+20 days')->getTimestamp();

		$metas = [
			'type' => $faker->randomElement($this->getTypes()),
			'logo' => 99,
			'teaser' => $faker->text(120),
			'description' => $faker->text(500),
			'link' => $faker->url
		];

		foreach($metas as $key => $value) {
			add_post_meta($id, $key, $value, true);
		}
	}

	public function getTypes() {
		$types = array(
			__('Gönnerwichtel', 'app'),
			__('Dagobertwichtel', 'app'),
			__('Sponsorwichtel', 'app')
		);

		$types = array_combine($types, $types);

		return $types;
	}

	public function queryByType($type) {
		query_posts(array(
			'post_type' => $this->getPostType(),
			'meta_key' => 'type',
			'order' => 'ASC',
			'orderby' => 'title',
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => 'type',
					'value' => $type
				)
			)
		));
	}
}
