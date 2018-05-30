<?php

/**
 * Archive Forum Content Part
 *
 * @package bbPress
 * @subpackage Theme
 */
?>
<div id="bbpress-forums">
	<div class="container-fluid content">
		<div class="row">
			<div class="col-md-2">
				<a href="localhost:8888/forums/new-topic"><button class="createTopic">Create new topic</button></a>
				<?php get_sidebar();?>
			</div>
			<div class="col-md-10">
				<div class="row headingForumRow">
					<div class="col-md-6">
						<?php bbp_breadcrumb(); ?>
						<?php bbp_forum_subscription_link(); ?>
					</div>
					<div class="col-md-6 bbp-search-form">
						<?php if ( bbp_allow_search() ) : ?>
						<?php bbp_get_template_part( 'form', 'search' ); ?>
						<?php endif; ?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<?php do_action( 'bbp_template_before_forums_index' ); ?>
						<?php if ( bbp_has_forums() ) : ?>

						<?php bbp_get_template_part( 'loop',     'forums'    ); ?>

						<?php else : ?>

						<?php bbp_get_template_part( 'feedback', 'no-forums' ); ?>

						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
		<?php do_action( 'bbp_template_after_forums_index' ); ?>
</div>
