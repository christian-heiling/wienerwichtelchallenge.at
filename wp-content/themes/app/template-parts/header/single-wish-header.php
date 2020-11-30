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

<?php if ( ! is_page() ) : ?>
<div class="entry-meta">
        <?php if (!empty($controller)): ?>
            <?php $controller->echoEntryMeta(); ?>
        <?php else: ?>
	<?php twentynineteen_posted_by(); ?>
	<?php twentynineteen_posted_on(); ?>
        <?php endif; ?>
	<span class="comment-count">
		<?php
		if ( ! empty( $discussion ) ) {
			twentynineteen_discussion_avatars_list( $discussion->authors );
		}
		?>
		<?php twentynineteen_comment_count(); ?>
	</span>
</div><!-- .entry-meta -->
<?php endif; ?>
