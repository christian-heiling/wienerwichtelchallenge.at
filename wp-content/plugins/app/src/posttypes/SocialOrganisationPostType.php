<?php

namespace app\posttypes;

class SocialOrganisationPostType extends AbstractPostType {

    function registerHooks() {
        parent::registerHooks();

        add_action('wp_enqueue_scripts', array($this, 'addScripts'));

        add_action('pre_get_posts', array($this, 'limitQuery'));
    }

    function limitQuery($query) {
        if (!is_admin() && $query->is_main_query() && is_archive() && $query->get('post_type') == $this->getPostType()) {
            $query->set('posts_per_page', -1);
            $query->set('orderby', 'title');
            $query->set('order', 'ASC');
        }
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
                    'id' => 'link',
                    'name' => __('Link', 'app'),
                    'type' => 'text'
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
                    'id' => 'delivery_options',
                    'name' => __('Delivery Options', 'app'),
                    'type' => 'checkbox_list',
                    'options' => array(
                        'personal' => __('Personal', 'app'),
                        'postal' => __('Postal', 'app'),
                        'amazon' => __('via Amazon as Present', 'app')
                    ),
                    'select_all_none' => true
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
                )
            )
        );

        $meta_boxes[] = array(
            'title' => __('Personal Delivery', 'app'),
            'post_types' => array($this->getPostType()),
            'fields' => array(
                array(
                    'id' => 'addressee',
                    'name' => __('Addressee', 'app'),
                    'type' => 'text'
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
                    'id' => 'covid19_regulations',
                    'name' => __('COVID-19 Regulations', 'app'),
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
                )
            )
        );

        $meta_boxes[] = array(
            'title' => __('Postal Delivery', 'app'),
            'post_types' => array($this->getPostType()),
            'fields' => array(
                array(
                    'id' => 'postal_addressee',
                    'name' => __('Addressee', 'app'),
                    'type' => 'text'
                ),
                array(
                    'id' => 'postal_street',
                    'name' => __('Street', 'app'),
                    'type' => 'text'
                ),
                array(
                    'id' => 'postal_zip',
                    'name' => __('ZIP', 'app'),
                    'type' => 'text'
                ),
                array(
                    'id' => 'postal_city',
                    'name' => __('City', 'app'),
                    'type' => 'post_type',
                )
            )
        );

        $meta_boxes[] = array(
            'title' => __('JIRA', 'app'),
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
            array('value' => 'Asly und Migration', 'label' => __('Asylum and Migration', 'app')),
            array('value' => 'Gesundheit', 'label' => __('Health', 'app')),
            array('value' => 'Kinder- und Jugendhilfe', 'label' => __('Childrens', 'app')),
            array('value' => 'Menschen mit Behinderungen', 'label' => __('People with Disablities', 'app')),
            array('value' => 'Nachbarschaftshilfe', 'label' => __('Neighborhood Aid', 'app')),
            array('value' => 'Suchthilfe', 'label' => __('Addicted People', 'app')),
            array('value' => 'Wohnungslosenhilfe', 'label' => __('Homeless People', 'app'))
        );

        usort($field_of_actions, function($a, $b) {
            return strcasecmp($a['label'], $b['label']);
        });

        return $field_of_actions;
    }

    public function getFieldOfActionLabelByValue($value) {
        $options = $this->getFieldOfActionOptions();

        foreach ($options as $o) {
            if ($o['value'] == $value) {
                return $o['label'];
            }
        }

        return '';
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
        echo '<span>';
        if (!empty(trim(rwmb_meta('zip')))) {
            echo rwmb_meta('zip') . ' ' . rwmb_meta('city');
        } else {
            echo rwmb_meta('postal_zip') . ' ' . rwmb_meta('postal_city');
        }
        echo '</span>';

        echo '<span>' . __('Field of Action', 'app') . ': ';
        echo $this->getFieldOfActionLabelByValue(rwmb_meta('field_of_action'));
        echo '</span>';

        echo '<span>' . __('Carrier', 'app') . ': ';
        $this->outputField('carrier', 'text');
        echo '</span>';
    }

    public function echoEntryContent() {

        $name = get_the_title();
        $logos = rwmb_meta('logo', array('limit' => 1));

        echo '<div class="wp-block-columns">';
        echo '<div class="wp-block-column">';

        if (!empty($logos)) {
            $logo = array_pop($logos);
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
        }

        echo '<p>' . strip_tags(rwmb_meta('description'), '<br><ol><li><ul><p><strong><em>') . '</p>';

        echo '</div>';
        echo '<div class="wp-block-column">';

        $this->outputMetaBoxContentWithHeadings(
                array(
            array(
                'section_name' => '',
                'field_ids' => array(
                    'contact'
                )
            )
                ), array(
            'first_heading' => '2'
                )
        );

        echo '<a href="' . rwmb_meta('link') . '">' . rwmb_meta('link') . '</a>';

        if (in_array('postal', rwmb_meta('delivery_options'))) {
            echo '<h2>' . __('Postal Delivery', 'app') . '</h2>';
            echo '<p>';
            echo rwmb_meta('postal_addressee') . '<br>';
            echo rwmb_meta('postal_street') . '<br>';
            echo rwmb_meta('postal_zip') . ' ' . rwmb_meta('postal_city');
            echo '</p>';
        }

        if (in_array('personal', rwmb_meta('delivery_options'))) {
            echo '<h2>' . __('Personal Delivery', 'app') . '</h2>';

            echo '<h4>' . __('COVID19 Regulations', 'app') . '</h4>';
            echo '<p>' . strip_tags(rwmb_meta('covid19_regulations'), '<br><ol><li><ul><p><strong><em>') . '</p>';

            $this->outputMetaBoxContentWithHeadings(
                    array(
                array(
                    'section_name' => '',
                    'field_ids' => array(
                        'delivery_hours'
                    )
                )
                    ), array(
                'first_heading' => '3'
                    )
            );

            echo '<h4>' . __('Drop-off Point', 'app') . '</h4>';
            echo '<p>';
            echo rwmb_meta('addressee') . '<br>';
            echo rwmb_meta('street') . '<br>';
            echo rwmb_meta('zip') . ' ' . rwmb_meta('city');
            echo '</p>';

        }

        echo '</div>';
        echo '</div>';

        $o = \app\App::getInstance()->getOptions();
        $wishListState = $o->get('wish_list_status');

        if ($wishListState == 'done') {
            echo '<h2>' . sprintf(__('Fulfilled Wishes from $name', 'app')) . '</h2>';
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
                        'value' => array(
                            $o->get('jira_state', 'erfuellt'),
                            $o->get('jira_state', 'abgeschlossen')
                        ),
                        'compare' => 'IN'
                    )
                ),
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'orderby' => 'rand',
                'order' => 'ASC'
            ));
        } else {
            echo '<h2 id="wishes">' . str_replace('%name%', $name, __('Open Wishes from %name%', 'app')) . '</h2>';

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
                    ),
                    array(
                        'key' => 'last_wichtel_delivery_date',
                        'value' => date('Y-m-d'),
                        'compare' => '>=',
                        'type' => 'DATE'
                    )
                ),
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'orderby' => 'rand',
                'order' => 'ASC'
            ));
        }

        if ($query->post_count !== 0) {
            global $wp_query;
            $wp_query = $query;

            get_template_part('template-parts/content/content-archive', 'open-wishes');
        } else {
            echo '<p>' . __('no wishes found', 'app') . '</p>';
        }
    }

    public function echoExcerptMeta() {

        $logo = rwmb_meta('logo', array('limit' => 1));
        $logo = array_pop($logo);
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
        echo '<span>';
        if (!empty(trim(rwmb_meta('zip')))) {
            echo rwmb_meta('zip') . ' ' . rwmb_meta('city');
        } else {
            echo rwmb_meta('postal_zip') . ' ' . rwmb_meta('postal_city');
        }
        echo '</span>';

        echo '<span>' . __('Carrier', 'app') . ': ';
        $this->outputField('carrier', 'text');
        echo '</span>';
    }

    public function echoExcerptContent() {
        echo strip_tags(rwmb_meta('teaser'));
    }

    public function getAll() {
        return get_posts(array(
            'post_type' => $this->getPostType(),
            'orderby' => 'title',
            'posts_per_page' => -1
        ));
    }

    public function queryByFieldOfAction($fieldOfAction) {
        query_posts(array(
            'post_type' => $this->getPostType(),
            'meta_key' => 'field_of_action',
            'order' => 'ASC',
            'orderby' => 'title',
            'limit' => -1,
            'posts_per_page' => -1,
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
