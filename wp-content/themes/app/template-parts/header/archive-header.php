<?php
/**
 * Displays the post header
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */

$discussion = ! is_page() && twentynineteen_can_show_post_thumbnail() ? twentynineteen_get_discussion_data() : null;

/**
 * @var app\posttypes\AbstractPostType $controller
 */
$controller = \app\App::getInstance()->getController(get_post_type());

?>

<?php
echo '<h1 class="entry-title">';
echo post_type_archive_title();
echo '</h1>';
?>

<?php if ( ! is_page() ) : ?>
<div class="entry-meta">
        <span>
            <?php echo \app\App::getInstance()->getOptions()->get($controller->getPostType(), 'archive_teaser'); ?>
        </span>
	<?php
	// Edit post link.
		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers. */
					__( 'Edit <span class="screen-reader-text">%s</span>', 'twentynineteen' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			),
			'<span class="edit-link">' . twentynineteen_get_icon_svg( 'edit', 16 ),
			'</span>',
            \app\App::getInstance()->getOptions()->getPostId()
		);
	?>
</div><!-- .entry-meta -->
<?php endif; ?>
