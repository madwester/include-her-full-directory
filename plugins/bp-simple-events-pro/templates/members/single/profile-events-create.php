<?php

/**
 * Template for creating or editing Events on a member profile page
 * You can copy this file to your-theme/buddypress/members/single
 * and then edit the layout.
 */

global $pp_ec;  // access to BP_Simple_Events_Create singleton
$required_fields = get_option( 'pp_events_required' );
?>

<form id="profile-event-form" name="profile-event-form" method="post" action="" class="standard-form" enctype="multipart/form-data">

	<p>
		<label for="event-title"><?php echo __( 'Title', 'bp-simple-events' ); ?>: *</label>
		<input type="text" id="event-title" name="event-title" value="<?php echo $pp_ec->title; ?>" />
	</p>

	<p>
		<label for="event-description"><?php echo __( 'Description', 'bp-simple-events' ); ?>: *</label>
		<textarea id="event-description" name="event-description" ><?php echo $pp_ec->description; ?></textarea>
	</p>

	<p>
		<label for="event-date"><?php echo __( 'Start', 'bp-simple-events' ); ?>: <?php if( in_array('date', $required_fields) ) echo __( '*', 'bp-simple-events' ); ?></label>
		<input type="text" id="event-date" name="event-date" placeholder="<?php echo __( 'Click to add Start Date...', 'bp-simple-events' ); ?>" value="<?php echo $pp_ec->date; ?>" />
	</p>

	<p>
		<label for="event-date-end"><?php echo __( 'End', 'bp-simple-events' ); ?>: <?php if( in_array('date-end', $required_fields) ) echo __( '*', 'bp-simple-events' ); ?></label>
		<input type="text" id="event-date-end" name="event-date-end" placeholder="<?php echo __( 'Click to add End Date...', 'bp-simple-events' ); ?>" value="<?php echo $pp_ec->date_end; ?>" />
	</p>

	<p>
		<label for="event-location"><?php echo __( 'Location', 'bp-simple-events' ); ?>: <?php if( in_array('location', $required_fields) ) echo __( '*', 'bp-simple-events' ); ?></label>
		<input type="text" id="event-location" name="event-location" placeholder="<?php echo __( 'Start typing location name...', 'bp-simple-events' ); ?>" value="<?php echo $pp_ec->address; ?>" />
	</p>

	<p>
		<label for="event-url"><?php echo __( 'Url', 'bp-simple-events' ); ?>: <?php if( in_array('url', $required_fields) ) echo __( '*', 'bp-simple-events' ); ?></label>
		<input type="text" size="80" id="event-url" name="event-url" placeholder="<?php echo __( 'Add an Event-related Url...', 'bp-simple-events' ); ?>" value="<?php echo $pp_ec->url; ?>" />
	</p>

	<?php
		$args = array(
			'type'                     => 'post',
			'child_of'                 => 0, //get_cat_ID( 'Events' ),
			'parent'                   => '',
			'orderby'                  => 'name',
			'order'                    => 'ASC',
			'hide_empty'               => 0,
			'hierarchical'             => 1,
			'exclude'                  => '',
			'include'                  => '',
			'number'                   => '',
			'taxonomy'                 => 'category',
			'pad_counts'               => false
		);

		$categories = get_categories( $args );
	?>

	<?php if( ! empty( $categories ) ) : ?>

		<p>
			<label for="event-cats"><?php echo __( 'Categories', 'bp-simple-events' ); ?>: <?php if( in_array('categories', $required_fields) ) echo __( '*', 'bp-simple-events' ); ?></label>
			<?php
				foreach( $categories as $category ) {

					$checked = '';
					if( in_array( $category->term_id, $pp_ec->cats_checked ) )
						$checked = ' checked';

					echo '&nbsp;&nbsp;<input type="checkbox" name="event-cats[]" value="' . $category->term_id . '"' . $checked . '/> ' . $category->name . '<br/>';
				}
			?>
		</p>

	<?php endif; ?>

	<p>
	
		<label for="event-attend"><?php echo __( 'Attending Options', 'bp-simple-events' ); ?>:</label>
		&nbsp;&nbsp;<input type="checkbox" id="event-attend-button" name="event-attend-button" value="1" <?php checked( $pp_ec->attend_button, 1 ); ?> /> <?php echo __( 'Add an "I want to attend" button on single event pages.', 'bp-simple-events' ); ?>

		<div id="event-attend-options" name="event-attend-options" style="margin-left: 10px;">

			<input type="checkbox" name="event-attend-notify" value="1" <?php checked( $pp_ec->attend_notify, 1 ); ?> /> <?php echo __( 'Receive a Notification when a member decides to attend or not attend your event.', 'bp-simple-events' ); ?>
			<br/>

			<input type="checkbox" id="event-attendees-list" name="event-attendees-list" value="1" <?php checked( $pp_ec->attendees_list, 1 ); ?> /> <?php echo __( 'Show a list of attendees on single event pages.', 'bp-simple-events' ); ?>
			
			<div id="event-attendees-display-options" name="event-attendees-display-options" style="margin-left: 10px;">

				<input type="checkbox" id="event-attendees-list-public" name="event-attendees-list-public" value="1" <?php checked( $pp_ec->attendees_list_public, 1 ); ?> /> <?php echo __( 'Make the attendees list public. Otherwise it will only be visible to you.', 'bp-simple-events' ); ?>
				<br/>

				<input type="checkbox" id="event-attendees-list-avatars" name="event-attendees-list-avatars" value="1" <?php checked( $pp_ec->attendees_list_avatars, 1 ); ?> /> <?php echo __( 'Show attendees as Avatars. Otherwise their display names with be shown.', 'bp-simple-events' ); ?>

			</div>

		</div>

	</p>


	<?php
	 /**
	  * If site admin has enabled Group support...
	  * And if this member belongs to a Group that has selected to allow Events to be assigned to a Group
	  * then those Groups will appear here as checkboxes
	  * If this member selects a Group, this Event will appear under the Events tab for that Group
	  * See see function get_groups() in bp-simple-events\inc\pp-events-create-class.php
	  */
	?>
	<?php $groups = $pp_ec->groups; ?>
	<?php if( ! empty( $groups ) ) : ?>

		<p>
			<label for="event-groups"><?php echo __( 'Groups', 'bp-simple-events' ); ?>: <?php if( in_array('groups', $required_fields) ) echo __( '*', 'bp-simple-events' ); ?></label>
			<?php
				foreach( $groups as $key => $value ) {

					$checked = '';
					if( in_array( $key, $pp_ec->groups_checked ) )
						$checked = ' checked';

					echo '&nbsp;&nbsp;<input type="checkbox" name="event-groups[]" value="' . $key . '"' . $checked . '/> ' . $value . '<br/>';
				}
			?>
		</p>

	<?php endif; ?>


	<?php if( ! $pp_ec->editor ) : ?>

		<p>
			<label for="event-img"><?php echo __( 'Image ( jpg only  )', 'bp-simple-events' ); ?>: <?php if( in_array('image', $required_fields) ) echo __( '*', 'bp-simple-events' ); ?></label>
			<input type="file" id="event-img" class="addImageBtn" name="event-img" value="">
			&nbsp;&nbsp;<input onclick="clearFileInput('event-img')" type="button" value="Remove" />
		</p>

	<?php else : ?>

		<p>
			<label for="event-img"><?php echo __( 'Image: ( jpg only  )', 'bp-simple-events' ); ?></label>

			<?php if ( has_post_thumbnail( $pp_ec->post_id ) ) : ?>

				<?php echo __( 'Current Image:', 'bp-simple-events' ); ?>
				<br/>
				<?php echo get_the_post_thumbnail( $pp_ec->post_id, 'thumbnail' ); ?>
				<br/>
				<?php echo __( 'Delete the Image?', 'bp-simple-events' ); ?>
				&nbsp;&nbsp;<input type="checkbox" name="event-img-delete" id="event-img-delete" value="1">
				<br/>&nbsp;<br/>

			<?php endif; ?>

			<input type="file" id="event-img" name="event-img" value="">
			&nbsp;&nbsp;<input onclick="clearFileInput('event-img')" type="button" value="Remove" />
		</p>

	<?php endif; ?>

	<input type="hidden" id="event-address" name="event-address" value="<?php echo $pp_ec->address; ?>" />
	<input type="hidden" id="event-latlng" name="event-latlng"  value="<?php echo $pp_ec->latlng; ?>" />
	<input type="hidden" name="action" value="event-action" />
	<input type="hidden" name="eid" value="<?php echo $pp_ec->post_id; ?>" />
	<?php wp_nonce_field( 'event-nonce' ); ?>

	<input type="submit" name="submit" class="button button-primary" value="<?php echo __(' SAVE ', 'bp-simple-events'); ?>"/>

</form>