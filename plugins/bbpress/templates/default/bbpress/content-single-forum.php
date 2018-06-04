<?php

/**
 * Single Forum Content Part
 *
 * @package bbPress
 * @subpackage Theme
 */

 //forum -> coding issues

?>

<div id="bbpress-forums" class="bbpress-forumsMargin">
	<div class="container-fluid content">
		<div class="row">
			<div class="col-md-2 col-sm-12 col-xs-12">
				<a href="http://localhost:8888/forums/new-topic/"><button class="createTopic">Create new topic</button></a>
				<?php get_sidebar(); ?>
			</div>
			<div class="col-md-10 col-sm-12 col-xs-12">
				<?php bbp_breadcrumb(); ?>

				<?php bbp_forum_subscription_link(); ?>

				<?php do_action( 'bbp_template_before_single_forum' ); ?>

				<?php if ( post_password_required() ) : ?>

					<?php bbp_get_template_part( 'form', 'protected' ); ?>

				<?php else : ?>

					<?php bbp_single_forum_description(); ?>

					<?php if ( bbp_has_forums() ) : ?>

						<?php bbp_get_template_part( 'loop', 'forums' ); ?>

					<?php endif; ?>

					<?php if ( !bbp_is_forum_category() && bbp_has_topics() ) : ?>

						<?php bbp_get_template_part( 'pagination', 'topics'    ); ?>

						<?php bbp_get_template_part( 'loop',       'topics'    ); ?>

						<?php bbp_get_template_part( 'pagination', 'topics'    ); ?>

						<?php bbp_get_template_part( 'form',       'topic'     ); ?>

					<?php elseif ( !bbp_is_forum_category() ) : ?>

						<?php bbp_get_template_part( 'feedback',   'no-topics' ); ?>

						<?php bbp_get_template_part( 'form',       'topic'     ); ?>

					<?php endif; ?>

				<?php endif; ?>
			</div>
		</div>
	</div>


	<?php do_action( 'bbp_template_after_single_forum' ); ?>

</div>
