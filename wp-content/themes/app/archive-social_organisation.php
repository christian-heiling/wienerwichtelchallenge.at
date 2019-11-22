<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */

get_header();

/**
 * @var app\posttypes\SocialOrganisationPostType $controller
 */
$controller = \app\App::getInstance()->getController(get_post_type());

?>

<?php
$map = app\App::getInstance()->getOptions()->get('map');

$map = explode(',', $map);
?>
<script>
jQuery(document).ready(function() {
    var mymap = L.map('mapid').setView<?php echo '([' . $map[0] . ', ' . $map[1] . '], ' . $map[2] . ');'; ?>

    L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
            zoom: 2,
            scrollWheelZoom: false,
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
                    '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
                    'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
            id: 'mapbox.streets'
    }).addTo(mymap);

    <?php 
    foreach($controller->getAll() as $organisation){
        $map = rwmb_get_value('map', [], $organisation->ID);
        
        $popupText = '<strong>' . $organisation->post_title . '</strong><br>' 
            . 'Handlungsfeld: ' . rwmb_get_value('field_of_action', [], $organisation->ID) . '<br>'
            . '<a href=\\"' . get_permalink($organisation) . '\\">Mehr erfahren</a>';
        
        echo 'L.marker([' . $map['latitude'] . ', ' . $map['longitude'] . ']).addTo(mymap).bindPopup("' . $popupText . '");';
    } 
    ?>
});
</script>

<section id="primary" class="content-area event-archive">
    <main id="main" class="site-main">
        <article class="hentry entry">
            <header class="entry-header">
                <h2 class="entry-title">Karte</h2>
            </header>

            <div class="entry-content">
                <div id="mapid"></div>
            </div>
        </article>
    </main>
</section>
        
<?php
        
foreach($controller->getFieldOfActionOptions() as $fieldOfAction):
?>

	<section id="primary" class="content-area event-archive">
		<main id="main" class="site-main">

            <?php $controller->queryByFieldOfAction($fieldOfAction) ?>
			<?php if ( have_posts() ) : ?>

                <article class="hentry entry">
                    <header class="entry-header">
                        <h2 class="entry-title"><?php echo $fieldOfAction; ?></h2>
                    </header>
                

				<?php
				// Start the Loop.
                $i = 0;

				while ( have_posts() ) :
				    $rows = 2;
                    if ($i % $rows == 0) {
                        echo '<div class="entry-content"><div class="wp-block-columns">';
                    }
                    echo '<div class="wp-block-column">';
					$i++;

					the_post();

					/*
					 * Include the Post-Format-specific template for the content.
					 * If you want to override this in a child theme, then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
					get_template_part( 'template-parts/content/content', 'excerpt' );

					// End the loop.

                    echo '</div>';
                    if ($i % $rows == 0) {
                        echo '</div></div>';
                    }
				endwhile;
			endif;
			?>
                </article>
		</main><!-- #main -->
	</section><!-- #primary -->

<?php
endforeach;
?>
        
<?php
get_footer();
