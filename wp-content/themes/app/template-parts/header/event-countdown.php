<?php

$app = \app\App::getInstance();

$event = $app->getEventController()->getNextUpcomingEvent();
$event_start = rwmb_meta('start', [], $event->ID);
?>

<?php if (!empty($event)): ?>

<div class="event-countdown">
    <span class="event-countdown-heading"><?php echo $event->post_title . ' in '; ?></span>
    <span id="event-countdown-content" class="event-countdown-content"></span>
    <a href="<?php echo get_permalink($event); ?>"><?php echo __("Read more", "app"); ?></a>
</div>

    <?php
    add_action('wp_footer', function() use ($event_start) {
        ?>
        <script>
            var deadline = new Date(<?php echo $event_start; ?>000).getTime();

            var countdown = function() {

                var now = new Date().getTime();
                var t = deadline - now;
                var days = Math.floor(t / (1000 * 60 * 60 * 24));
                var hours = Math.floor((t % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((t % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((t % (1000 * 60)) / 1000);

                var output = "";

                if (days == 1) {
                    output = days + ' <?php echo __("Day", "app"); ?> ';
                } else {
                    output = days + ' <?php echo __("Days", "app"); ?> ';
                }

                if (hours == 1) {
                    output += '| ' + hours + ' <?php echo __("Hour", "app"); ?> ';
                } else {
                    output += '| ' + hours + ' <?php echo __("Hours", "app"); ?> ';
                }

                if (minutes == 1) {
                    output += '| ' + minutes + ' <?php echo __("Minute", "app"); ?> ';
                } else {
                    output += '| ' + minutes + ' <?php echo __("Minutes", "app"); ?> ';
                }

                if (seconds == 1) {
                    output += '| ' + seconds + ' <?php echo __("Second", "app"); ?>';
                } else {
                    output += '| ' + seconds + ' <?php echo __("Seconds", "app"); ?>';
                }

                document.getElementById("event-countdown-content").innerHTML = output;
                if (t < 0) {
                    clearInterval(x);
                    document.getElementById("event-countdown-content").innerHTML = 'läuft gerade';
                }
            };

            countdown();
            var x = setInterval(countdown, 1000);
        </script>
        <?php
    }); ?>

<?php endif; ?>