<?php

namespace app\posttypes;

use Carbon\Carbon;

class EventPostType extends AbstractPostType {

    public function getLabel() {
        return __('Events', 'app');
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

    protected function registerHooks() {
        parent::registerHooks();

        add_shortcode('events', array($this, 'getEventsHtml'));
        add_action('pre_get_posts', array($this, 'limitQuery'));
    }
    
    function limitQuery($query) {
        if (!is_admin() && $query->is_main_query() && is_archive() && $query->get('post_type') == $this->getPostType()) {
            $query->set('posts_per_page', -1);
        }
    }

    public function getEventsHtml($atts) {
        $atts = shortcode_atts(array(
            'count' => 3
                ), $atts);

        $events = array_merge($this->getRunningEvents(), $this->getUpcomingEvents());

        $return = '<div class="wp-block-columns">';
        for ($i = 0; $i < $atts['count'] && $i < count($events); $i++) {

            $event = $events[$i];

            $return .= '<div class="wp-block-column">' . $this->getEventHtml($event) . '</div>';
        }
        $return .= '</div>';
        return $return;
    }

    public function getEventHtml($event) {
        $return = '';

        $return .= '<article class="event type-event hentry entry">';
        $return .= '<header class="entry-header">';
        $return .= '<h3 class="entry-header">' .
                '<a href="' . get_permalink($event) . '">' . $event->post_title . '</a>' .
                '</h3>';
        $return .= '<div class="entry-meta">';

        $image = rwmb_meta('image', array('limit' => 1), $event->ID);
        if (!empty($image)) {
            $image = array_pop($image);

            $return .= '<a href="' . get_permalink($event->ID) . '">';
            $return .= '<figure class="wp-block-image is-resized overflow">'
                    . '<img src="' . $image['full_url'] . '" '
                    . 'alt="' . get_the_title($event->ID) . '" '
                    . 'class="wp-image-' . $image['ID'] . '" '
                    . 'srcset="' . $logo['srcset'] . '" '
                    . 'sizes="(max-width: 1920px) 100vw, 1920px" '
                    . 'width="1920" '
                    . 'height="516"></figure>';
            $return .= '</a>';
        }

        $return .= '<span>' . $this->getStartAndEnddate($event->ID) . '</span>';
        $return .= '<span>' . __('Location', 'app') . ': ' . rwmb_meta('location_name', '', $event->ID) . '</span>';

        $zip = rwmb_meta('zip', [], $event->ID);
        if (!empty($zip)) {
            $return .= '<span>' . __('ZIP', 'app') . ': ' . rwmb_meta('zip', '', $event->ID) . '</span>';
        }

        $return .= '</div>';

        $return .= '<div class="entry-content">';
        $return .= rwmb_meta('teaser', [], $event->ID);
        $return .= '</div>';

        $return .= '</header>';
        $return .= '</article>';

        return $return;
    }

    public function addMetaBox($meta_boxes) {
        $meta_boxes[] = array(
            'title' => __('Time'),
            'post_types' => array($this->getPostType()),
            'fields' => array(
                array(
                    'id' => 'start',
                    'name' => __('Starts', 'app'),
                    'type' => 'datetime',
                    'timestamp' => true
                ),
                array(
                    'id' => 'end',
                    'name' => __('Ends', 'app'),
                    'type' => 'datetime',
                    'timestamp' => true
                )
            ),
        );

        $meta_boxes[] = array(
            'title' => __('Location Infos', 'app'),
            'post_types' => array($this->getPostType()),
            'fields' => array(
                array(
                    'id' => 'location_name',
                    'name' => __('Location', 'app'),
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
                    'id' => 'reachable_via',
                    'name' => __('Public Reachable via', 'app'),
                    'type' => 'textarea'
                )
            )
        );

        $meta_boxes[] = array(
            'title' => __('Description'),
            'post_types' => array($this->getPostType()),
            'fields' => array(
                array(
                    'id' => 'image',
                    'name' => __('Image', 'app'),
                    'type' => 'image_advanced',
                    'max_file_uploads' => 1
                ),
                array(
                    'id' => 'category',
                    'name' => __('Category', 'app'),
                    'type' => 'text',
                    'options' => array(
                        'textarea_rows' => 4
                    )
                ),
                array(
                    'id' => 'teaser',
                    'name' => __('Teaser', 'app'),
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 4
                    )
                ),
                array(
                    'id' => 'description',
                    'name' => __('Press Text', 'app'),
                    'type' => 'wysiwyg',
                    'options' => array(
                        'textarea_rows' => 8
                    )
                )
            ),
        );

        $meta_boxes[] = array(
            'title' => __('Link'),
            'post_types' => array($this->getPostType()),
            'fields' => array(
                array(
                    'id' => 'link_label',
                    'name' => __('Label', 'app'),
                    'desc' => __('e.g. Get Tickets here!'),
                    'type' => 'text'
                ),
                array(
                    'id' => 'link',
                    'name' => __('URL', 'app'),
                    'type' => 'url'
                )
            )
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
            $date = new \Carbon\Carbon('@' . rwmb_meta($column_name, [], $post_ID), get_option('timezone_string'));
            echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $date->timestamp)
            . ' (' . $date->diffForHumans() . ')';
        }
    }

    public function echoEntryMeta() {
        echo '<span>' . $this->getStartAndEnddate() . '</span>';

        $fields = ['location_name'];

        if (!empty(rwmb_meta('zip'))) {
            $fields[] = 'zip';
        }

        $this->outputMetaBoxContentWithSpans($fields);
    }

    public function echoEntryContent() {
        $image = rwmb_meta('image', array('limit' => 1));
        echo '<div class="wp-block-columns">';
        echo '<div class="wp-block-column">';
        if (!empty($image)) {
            $image = array_pop($image);
            ?>
            <figure class="wp-block-image is-resized overflow">
                <img src="<?php echo $image['full_url'] ?>"
                     alt="<?php the_title(); ?>"
                     class="wp-image-<?php echo $image['ID']; ?>"
                     srcset="<?php echo $logo['srcset']; ?>"
                     sizes="(max-width: 1920px) 100vw, 1920px"
                     width="1920"
                     height="516">
            </figure>
            <?php
        }
        echo '<p>' . do_shortcode(rwmb_meta('description')) . '</p>';
        echo '</div>';
        echo '<div class="wp-block-column">';
        echo '<h3>' . __("When and Where?", "app") . '</h3>';
        echo '<p>'
        . rwmb_meta('location_name') . '<br>';

        if (!empty(rwmb_meta('street')) && !empty(rwmb_meta('zip')) && !empty(rwmb_meta('city'))) {
            echo rwmb_meta('street') . '<br>';
            echo rwmb_meta('zip') . ' ' . rwmb_meta('city');
        }

        echo '</p>';
        echo '<p>' . $this->getStartAndEnddate() . '</p>';

        if (!empty(rwmb_meta('reachable_via'))) {
            echo '<h3>' . __('Public Reachable via', 'app') . '</h3>';
            echo '<p>' . rwmb_meta('reachable_via') . '</p>';
        }

        if (!empty(rwmb_meta('link')) && !empty(rwmb_meta('link_label'))) {
            echo '<div class="wp-block-button">';
            echo '<a class="wp-block-button__link" href="' . rwmb_meta('link') . '">' . rwmb_meta('link_label') . '</a>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';

        if (!empty(rwmb_meta('street')) && !empty(rwmb_meta('zip')) && !empty(rwmb_meta('city'))) {
            $this->outputMetaBoxContentWithHeadings(
                    array(
                array(
                    'section_name' => '',
                    'field_ids' => array(
                        'map'
                    )
                )
                    ), array(
                'first_heading' => 2
                    )
            );
        }
    }

    public function echoExcerptMeta() {
        $image = rwmb_meta('image', array('limit' => 1));
        if (!empty($image)) {
            $image = array_pop($image);
            ?>
            <a href="<?php the_permalink(); ?>">
            <figure class="wp-block-image is-resized overflow">
                <img src="<?php echo $image['full_url'] ?>"
                     alt="<?php the_title(); ?>"
                     class="wp-image-<?php echo $image['ID']; ?>"
                     srcset="<?php echo $logo['srcset']; ?>"
                     sizes="(max-width: 1920px) 100vw, 1920px"
                     width="1920"
                     height="516">
            </figure>
            </a>
            <?php
        }

        $this->echoEntryMeta();
    }

    public function getExcerptContent() {
        return rwmb_meta('teaser');
    }

    public function echoExcerptContent() {
        echo $this->getExcerptContent();
    }

    public function getNextUpcomingEvent() {
        return current($this->getUpcomingEvents());
    }

    private function getUpcomingEventsQuery() {
        return array(
            'post_type' => $this->getPostType(),
            'meta_key' => 'start',
            'order' => 'ASC',
            'orderby' => 'meta_value',
            'meta_query' => array(
                'key' => 'start',
                'value' => time(),
                'compare' => '>'
            )
        );
    }

    public function queryUpcomingEvents() {
        query_posts($this->getUpcomingEventsQuery());
    }

    public function getUpcomingEvents() {
        return get_posts($this->getUpcomingEventsQuery());
    }

    public function getRunningEventsQuery() {
        return array(
            'post_type' => $this->getPostType(),
            'meta_key' => 'end',
            'order' => 'ASC',
            'orderby' => 'meta_value',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'start',
                    'value' => time(),
                    'compare' => '<'
                ),
                array(
                    'key' => 'end',
                    'value' => time(),
                    'compare' => '>'
                )
            )
        );
    }

    public function queryRunningEvents() {
        query_posts($this->getRunningEventsQuery());
    }

    public function getRunningEvents() {
        return get_posts($this->getRunningEventsQuery());
    }

    public function queryPastEvents() {
        query_posts(array(
            'post_type' => $this->getPostType(),
            'meta_key' => 'end',
            'order' => 'ASC',
            'orderby' => 'meta_value',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'start',
                    'value' => time(),
                    'compare' => '<'
                ),
                array(
                    'key' => 'end',
                    'value' => time(),
                    'compare' => '<'
                )
            )
        ));
    }

    public function getStartAndEnddate($id = null) {
        $start = new \Carbon\Carbon('@' . rwmb_meta('start', [], $id), get_option('timezone_string'));
        $end = new \Carbon\Carbon('@' . rwmb_meta('end', [], $id), get_option('timezone_string'));
        
        $now = new \Carbon\Carbon('@' . \time(), get_option('timezone_string'));
        $return = '';

        // running events
        if ($start->lessThan($now) && $end->greaterThan($now)) {
            if ($end->hour == 0 && $end->minute == 0) {
                $return .= 'bis ' . date_i18n('D., j. M Y', $end->timestamp);
            } else {
                $return .= 'bis ' . date_i18n('D., j. M Y, H:i', $end->timestamp);
            }

            return $return;
        }
        // more than one day events
        elseif ($start->diffInDays($end) !== 0) {


            if ($start->hour == 0 && $start->minute == 0) {
                $return .= date_i18n('D., j. M Y', $start->timestamp);
            } else {
                $return .= date_i18n('D., j. M Y, H:i', $start->timestamp);
            }


            if ($end->hour == 0 && $end->minute == 0) {
                $return .= ' bis ' . date_i18n('D., j. M Y', $end->timestamp);
            } else {
                $return .= ' bis ' . date_i18n('D., j. M Y, H:i', $end->timestamp);
            }

            return $return;
        } else {
            return date_i18n('D., j. M Y, H:i', $start->timestamp)
                    . ' bis ' . date_i18n('H:i', $end->timestamp);
        }
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
            'start' => $start,
            'end' => $start + rand(30, 60 * 12 * 3) * 60,
            'location_name' => $faker->text(10),
            'street' => $faker->streetAddress,
            'zip' => '1' . sprintf("%02d'.02", rand(1, 21)) . '0',
            'city' => 'Wien',
            'map' => '48.1935651,16.3394902,12',
            'reachable_via' => 'U3, U6 Westbahnhof',
            'category' => $faker->text(10),
            'teaser' => $faker->text(120),
            'description' => $faker->text(500)
        ];

        foreach ($metas as $key => $value) {
            add_post_meta($id, $key, $value, true);
        }
    }

}
