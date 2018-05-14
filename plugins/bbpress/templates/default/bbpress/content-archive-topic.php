<?php

/**
 * Archive Topic Content Part
 *
 * @package bbPress
 * @subpackage Theme
 */
//when user press on a topic
?>

<div id="bbpress-forums">
<div class="container-fluid">
	<div class="row">
		<div class="col-md-2 col-sm-2 col-xs-12">
			<a href="localhost:8888/forums/new-topic"><button class="createTopic">Create new topic</button></a>
			<?php get_sidebar(); ?>
		</div>
		<div class="col-md-10 col-sm-2 col-xs-12">
		<?php if ( bbp_allow_search() ) : ?>

		<div class="bbp-search-form">

			<?php bbp_get_template_part( 'form', 'search' ); ?>

		</div>

		<?php endif; ?>

		<?php bbp_breadcrumb(); ?>

		<?php if ( bbp_is_topic_tag() ) bbp_topic_tag_description(); ?>

		<?php do_action( 'bbp_template_before_topics_index' ); ?>

		<?php if ( bbp_has_topics() ) : ?>

		<?php bbp_get_template_part( 'pagination', 'topics'    ); ?>

		<?php bbp_get_template_part( 'loop',       'topics'    ); ?>

		<?php bbp_get_template_part( 'pagination', 'topics'    ); ?>

		<?php else : ?>

		<?php bbp_get_template_part( 'feedback',   'no-topics' ); ?>

		<?php endif; ?>
				</div>
			</div>
		</div>
	<?php do_action( 'bbp_template_after_topics_index' ); ?>
</div>
