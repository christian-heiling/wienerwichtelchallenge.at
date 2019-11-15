<?php

namespace app\posttypes;

class SocialOrganisationPostType extends AbstractPostType {

    function registerHooks() {
        parent::registerHooks();

        add_action('wp_enqueue_scripts', array($this, 'addScripts'));
    }

    function addScripts() {
        if (is_archive() && get_post_type() == $this->getPostType()) {
            wp_enqueue_style('leaflet', 'https://unpkg.com/leaflet@1.5.1/dist/leaflet.css', array(), '1.5.1');
            wp_enqueue_script('leaflet', 'https://unpkg.com/leaflet@1.5.1/dist/leaflet.js', array(), '1.5.1', true);
            wp_enqueue_script('rwmb-osm-frontend', RWMB_JS_URL . 'osm-frontend.js', array('jquery', 'leaflet'), RWMB_VER, true);
        }
    }

    function getSupports() {
        return array('title', 'thumbnail', 'revisions');
    }

    public function echoColumnBody($column_name, $post_ID) {
        if (in_array($column_name, ['carrier', 'field_of_action', 'city', 'zip'])) {
            echo rwmb_meta($column_name, [], $post_ID);
        }
    }

    public function getLabel() {
        return __('Social Organisations', 'app');
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
            'title' => __('Infos'),
            'post_types' => array($this->getPostType()),
            'fields' => array(
                array(
                    'id' => 'logo',
                    'name' => __('Logo', 'app'),
                    'type' => 'image'
                ),
                array(
                    'id' => 'carrier',
                    'name' => __('Carrier', 'app'),
                    'type' => 'text'
                ),
                array(
                    'id' => 'field_of_action',
                    'name' => __('Field of Action', 'app'),
                    'type' => 'select',
                    'flatten' => true,
                    'options' => $this->getFieldOfActionOptions()
                ),
                array(
                    'id' => 'street',
                    'name' => __('Street', 'app'),
                    'type' => 'text'
                ),
                array(
                    'id' => 'zip',
                    'name' => __('ZIP', 'app'),
                    'type' => 'text'
                ),
                array(
                    'id' => 'city',
                    'name' => __('City', 'app'),
                    'type' => 'post_type',
                ),
                array(
                    'id' => 'map',
                    'name' => __('Map', 'app'),
                    'type' => 'osm',
                    'std' => '48.20849,16.37208,13',
                    'address_field' => 'street,zip,city'
                ),
                array(
                    'id' => 'reachable_via',
                    'name' => __('Public Reachable via', 'app'),
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 4,
                        'media_buttons' => false,
                        'teeny' => true
                    )
                ),
                array(
                    'id' => 'delivery_hours',
                    'name' => __('Delivery hours', 'app'),
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 4,
                        'media_buttons' => false,
                        'teeny' => true
                    )
                ),
                array(
                    'id' => 'contact',
                    'name' => __('Contact for Questions', 'app'),
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 4,
                        'media_buttons' => false,
                        'teeny' => true
                    )
                ),
                array(
                    'id' => 'teaser',
                    'name' => __('Teaser', 'app'),
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 4,
                        'media_buttons' => false,
                        'teeny' => true
                    )
                ),
                array(
                    'id' => 'description',
                    'name' => __('About the Social Organisation', 'app'),
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 8,
                        'media_buttons' => false,
                        'teeny' => true
                    )
                ),
                array(
                    'id' => 'link',
                    'name' => __('Link', 'app'),
                    'type' => 'text'
                )
            )
        );

        $meta_boxes[] = array(
            'title' => __('JIRA'),
            'post_types' => array($this->getPostType()),
            'fields' => array(
                array(
                    'id' => 'jira_user',
                    'name' => __('Associated Jira Users', 'app'),
                    'type' => 'text',
                    'clone' => 'true'
                )
            )
        );


        return $meta_boxes;
    }

    public function getFieldOfActionOptions() {
        $field_of_actions = array(
            __('Children, Youth, Family', 'app'),
            __('Elderly People', 'app'),
            __('Health', 'app'),
            __('Children', 'app'),
            __('Deliquence', 'app'),
            __('Work and Education', 'app'),
            __('Migration and Integration', 'app'),
            __('Homeless People', 'app'),
            __('Material Security', 'app')
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
        $head['city'] = __('City', 'app');
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
        $this->outputMetaBoxContentWithSpans(array(
            'carrier', 'field_of_action', 'zip'
        ));
    }

    public function echoEntryContent() {

        $logo = array_pop(rwmb_meta('logo', array('limit' => 1)));

        echo '<div class="wp-block-columns">';
        echo '<div class="wp-block-column">';
        ?>
        <figure class="social-organisation-logo wp-block-image is-resized overflow">
            <img src="<?php echo $logo['full_url'] ?>"
                 alt=""
                 class="wp-image-<?php echo $logo['ID']; ?>"
                 srcset="<?php echo $logo['srcset']; ?>"
                 sizes="(max-width: 1920px) 100vw, 1920px"
                 width="1920"
                 height="516">
        </figure>
        <?php
        echo '<p>' . rwmb_meta('description') . '</p>';

        echo '</div>';
        echo '<div class="wp-block-column">';

        echo '<h2>' . __('Delivery Infos', 'app') . '</h2>';
        echo '<p>';
        echo rwmb_meta('street') . '<br>';
        echo rwmb_meta('zip') . ' ' . rwmb_meta('city');
        echo '</p>';

        $this->outputMetaBoxContentWithHeadings(
                array(
            array(
                'section_name' => '',
                'field_ids' => array(
                    'delivery_hours',
                    'contact',
                    'reachable_via',
                    'link'
                )
            )
                ), array(
            'first_heading' => '2'
                )
        );
        echo '</div>';
        echo '</div>';

        $query = new \WP_Query(array(
            'post_type' => \app\App::getInstance()->getWishController()->getPostType(),
            'meta_key' => 'social_organisation_id',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'social_organisation_id',
                    'value' => get_the_ID()
                ),
                array(
                    'key' => 'status_id',
                    'value' => \app\App::getInstance()->getOptions()->get('jira_state', 'offen')
                )
            ),
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'rand',
            'order' => 'ASC'
        ));

        echo '<h2>' . sprintf(__('Open Wishes', 'app')) . '</h2>';
        
        if ($query->post_count !== 0) {    
            global $wp_query;
            $wp_query = $query;

            get_template_part('template-parts/content/content-archive', 'open-wishes');
        } else {
            echo '<p>' . __('keine offenen Wünsche', 'app') . '</p>';
        }
    }

    public function echoExcerptMeta() {
        $logo = array_pop(rwmb_meta('logo', array('limit' => 1)));
        ?>
        <figure class="social-organisation-logo wp-block-image is-resized overflow">
            <img src="<?php echo $logo['full_url'] ?>"
                 alt=""
                 class="wp-image-<?php echo $logo['ID']; ?>"
                 srcset="<?php echo $logo['srcset']; ?>"
                 sizes="(max-width: 1920px) 100vw, 1920px"
                 width="1920"
                 height="516">
        </figure>
        <?php
        $this->outputMetaBoxContentWithSpans(array(
            'carrier', 'field_of_action', 'city', 'zip'
        ));
    }

    public function echoExcerptContent() {
        echo rwmb_meta('teaser');
    }

    public function generateRandomItem() {
        // do noting
    }

    public function getAll() {
        return get_posts(array(
            'post_type' => $this->getPostType(),
            'orderby' => 'title'
        ));
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
