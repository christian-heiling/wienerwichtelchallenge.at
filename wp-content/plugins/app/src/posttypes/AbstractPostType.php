<?php

namespace app\posttypes;

abstract class AbstractPostType {

    public function __construct() {
        $this->registerHooks();
    }

    abstract public function getPostType();

    abstract function getSlug();

    abstract function getSortableColumns();

    abstract function getLabel();

    abstract function getMenuIcon();

    abstract function addMetaBox($meta_boxes);

    abstract function setColumnHead();

    abstract function echoColumnBody($column_name, $post_ID);

    // for frontend
    abstract function echoEntryMeta();

    abstract function echoEntryContent();

    abstract function echoExcerptContent();

    abstract function echoExcerptMeta();

    // for testing
    abstract function generateRandomItem();

    function getSupports() {
        return array('title', 'thumbnail', 'revisions');
    }

    protected function registerHooks() {
        add_action('init', array($this, 'registerPostType'), 0);
        add_filter('rwmb_meta_boxes', array($this, 'addMetaBox'));

        add_filter('manage_' . $this->getPostType() . '_posts_columns', array($this, 'setColumnHead'));
        add_action('manage_' . $this->getPostType() . '_posts_custom_column', array($this, 'echoColumnBody'), 10, 2);

        add_filter('manage_edit-' . $this->getPostType() . '_sortable_columns', array($this, 'setSortableColumns'));
        add_action('pre_get_post', array($this, 'sortColumns'));

        add_action('rest_api_init', array($this, 'addMetaboxFields'));
    }

    public function registerPostType() {
        $args = array(
            'label' => $this->getLabel(),
            'description' => 'Hallo',
            'supports' => $this->getSupports(),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 25,
            'menu_icon' => $this->getMenuIcon(),
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'page',
            'show_in_rest' => true,
            'rewrite' => array(
                'slug' => $this->getSlug()
            ),
            'rest_base' => $this->slugify($this->getPostType()),
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        );
        register_post_type($this->getPostType(), $args);
    }

    protected function slugify($text) {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    public function setSortableColumns($sortable_columns) {
        foreach ($this->getSortableColumns() as $c) {
            $sortable_columns[$c] = $c;
        }
        return $sortable_columns;
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

    public function addMetaboxFields() {
        // retrieve added metaboxes
        $metaboxes = $this->addMetaBox([]);

        $fields = array();

        foreach ($metaboxes as $box) {
            foreach ($box['fields'] as $field) {
                $fields[] = $field['id'];
            }
        }

        foreach ($fields as $field) {
            $defaults = array(
                'get_callback' => array($this, 'getRestValue'),
                'update_callback' => null,
                'schema' => null,
            );

            register_rest_field($this->getPostType(), $field, $defaults);
        }
    }

    public function getRestValue($object, $field_name, $request) {
        return get_post_meta($object['id'], $field_name)[0];
    }

    public function echoArchiveTeaser() {
        echo \app\App::getInstance()->getOptions()->get($this->getPostType(), 'archive_teaser');
    }

    public function outputMetaBoxContentWithHeadings($displayConfig, $options) {
        $firstHeading = $options['first_heading'];

        $metaboxes = $this->addMetaBox([]);
        $fields = array();

        foreach ($metaboxes as $box) {
            foreach ($box['fields'] as $field) {
                $id = $field['id'];
                $name = array_key_exists('name', $field) ? $field['name'] : null;
                $type = $field['type'];

                $fields[$id] = array('name' => $name, 'type' => $type);
            }
        }

        foreach ($displayConfig as $sectionConfig) {
            if (isset($sectionConfig['section_name'])) {
                $sectionName = $sectionConfig['section_name'];
                echo '<h' . $firstHeading . '>' . $sectionName . '</h' . $firstHeading . '>';
            }

            foreach ($sectionConfig['field_ids'] as $id) {
                $name = $fields[$id]['name'];
                $type = $fields[$id]['type'];

                if (!empty($name)) {
                    echo '<h' . ($firstHeading + 1) . '>' . $name . '</h' . ($firstHeading + 1) . '>';
                }
                $this->outputField($id, $type);
            }
        }
    }

    /**
     * Example
     * 
     * $displayConfig = array(
     *      'start',
     *      'end'
     *      'location_name',
     *      'zip'
     * );
     * @param type $displayConfig
     * @param type $options
     */
    public function outputMetaBoxContentWithSpans($displayConfig) {
        $metaboxes = $this->addMetaBox([]);
        $fields = array();

        foreach ($metaboxes as $box) {
            foreach ($box['fields'] as $field) {
                $id = $field['id'];
                $name = array_key_exists('name', $field) ? $field['name'] : null;
                $type = $field['type'];

                $fields[$id] = array('name' => $name, 'type' => $type);
            }
        }

        foreach ($displayConfig as $id) {
            $name = $fields[$id]['name'];
            $type = $fields[$id]['type'];

            if (!empty($name)) {
                echo '<span>' . $name . ': ';
                $this->outputField($id, $type);
                echo '</span>';
            }
        }
    }

    private function outputField($id, $type) {
        if ($type == 'datetime') {
            $date = new \Carbon\Carbon('@' . rwmb_meta($id), get_option('timezone_string'));
            echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $date->timestamp)
            . ' (' . $date->diffForHumans() . ')';
        } elseif ($type == 'text' && substr(rwmb_meta($id), 0, 4) == 'http') {
            echo '<a href="' . rwmb_meta($id) . '" target="_blank" rel="nofollow">' . rwmb_meta($id) . '</a>';
        } elseif ($type == 'image') {
            $logo = array_pop(rwmb_meta('logo', array('limit' => 1)));
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
        } elseif ($type == 'map') {
            echo rwmb_meta(
                    $id, array(
                'height' => '200px',
                'js_options' => array(
                    'scrollWheelZoom' => false,
                    'tap' => false
                )
                    )
            );
        } else {
            echo rwmb_meta($id);
        }
    }

    function generateMapField() {
        $x = rand();
        $y = rand();
    }

}
