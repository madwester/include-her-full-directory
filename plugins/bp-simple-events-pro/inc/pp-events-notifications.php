<?php

if ( ! defined( 'ABSPATH' ) ) exit;


function pp_events_screen_notification_settings() {
	global $current_user;

	if ( current_user_can('publish_events') ) { 
	?>
		<br/>
		<table class="notification-settings" id="events-notification-settings">
	
			<thead>
			<tr>
				<th class="icon"></th>
				<th class="title"><?php _e( 'Events', 'bp-simple-events' ) ?></th>
				<th class="yes"><?php _e( 'Yes', 'bp-simple-events' ) ?></th>
				<th class="no"><?php _e( 'No', 'bp-simple-events' )?></th>
			</tr>
			</thead>
	
			<tbody>
			<tr>
				<td></td>
				<td><?php _e( 'A member decides to attend or not attend one of your Events', 'bp-simple-events' ) ?></td>
				<td class="yes"><input type="radio" name="notifications[notification_events]" value="yes" <?php if ( !get_user_meta( $current_user->ID, 'notification_events', true ) || 'yes' == get_user_meta( $current_user->ID, 'notification_events', true ) ) { ?>checked="checked" <?php } ?>/></td>
				<td class="no"><input type="radio" name="notifications[notification_events]" value="no" <?php if ( get_user_meta( $current_user->ID, 'notification_events', true ) == 'no' ) { ?>checked="checked" <?php } ?>/></td>
			</tr>
	
			<?php do_action( 'pp_event_notification_settings' ); ?>
	
			</tbody>
		</table>
<?php
	}
}
add_action( 'bp_notification_settings', 'pp_events_screen_notification_settings' );



// Remove a single event notification(s) for a user when clicked on the notifications bar. 
// Hook is in event-single.php template
function pp_events_remove_single_screen_notification() {
	global $post; 
	
	bp_notifications_mark_notifications_by_item_id( bp_loggedin_user_id(), $post->ID, 'events', 'event_attender', $secondary_item_id = false, $is_new = false );
}
add_action( 'pp_single_event_notification', 'pp_events_remove_single_screen_notification' );




function pp_events_format_notifications( $action, $item_id, $secondary_item_id, $total_items, $format = 'string' ) {

	switch ( $action ) {
		case 'event_attender':

			$user_fullname = bp_core_get_user_displayname( $secondary_item_id );
			$user_url = get_permalink( $item_id );

			$going = false;
			$attendees = get_post_meta( $item_id, 'event-attendees', true );    //	var_dump( $attendees );
			if( ! empty( $attendees ) ) {
				if( in_array( $secondary_item_id, $attendees ) )
					$going = true;
			}
				

			/***
			 * We don't want a whole list of similar notifications in a users list, so we group them.
			 * If the user has more than one action from the same component, they are counted and the
			 * notification is rendered differently.
			 */
			if ( (int) $total_items > 1 ) {
				$user_url = trailingslashit( bp_loggedin_user_domain() . '/notifications' );
				$title = __( 'Multiple Event Attenders', 'bp-simple-events' );
				$text = sprintf( __( '%d new Event Attendees', 'bp-simple-events' ), (int) $total_items );
				$filter = 'pp_event_multiple_new_event_attender_notification';
			} else {
					if( $going )
						$text = sprintf( __( '%s is attending your Event', 'bp-simple-events' ), $user_fullname );
					else
						$text = sprintf( __( '%s is not attending your Event', 'bp-simple-events' ), $user_fullname );


				$filter = 'pp_event_single_new_event_attender_notification';
			}

		break;
	}

	if ( 'string' == $format ) {
			$return = apply_filters( $filter, '<a href="' . esc_url( $user_url ) . '">' . esc_html( $text ) . '</a>', $user_url, (int) $total_items, $item_id, $secondary_item_id, $text );
	} else {
		$return = apply_filters( $filter, array(
			'text' => $text,
			'link' => $user_url
		), $user_url, (int) $total_items, $item_id, $secondary_item_id );
	}

	do_action( 'pp_event_format_notifications', $action, $item_id, $secondary_item_id, $total_items );

	return $return;
}


// maybe send notification, maybe send email
function pp_events_send_attend_notification( $to_user_id = 0, $from_user_id = 0, $post_id = 0 ) {

	if ( empty( $to_user_id ) || empty( $from_user_id ) || empty( $post_id ) )
		return;

	$bp = buddypress();


	$send_notify = get_post_meta( $post_id, 'event-attend-notify', true );

	if ( ! empty( $send_notify ) ) {

		bp_notifications_add_notification( array(
			'user_id'           => $to_user_id,
			'item_id'           => $post_id,
			'secondary_item_id' => $from_user_id,
			'component_name'    => $bp->events->id,
			'component_action'  => 'event_attender'
		) );
	}


	// Check to see if the Event owner wants emails
	if( 'yes' == get_user_meta( (int)$to_user_id, 'notification_events', true ) ) {

		$sender_name = bp_core_get_user_displayname( $from_user_id, false );
		$receiver_name = bp_core_get_user_displayname( $to_user_id, false );
		$receiver_email = bp_core_get_user_email( $to_user_id );

		$sender_profile_link = trailingslashit( bp_core_get_user_domain( $from_user_id ) );
		$event_link = get_permalink( $post_id );

		$attendees = get_post_meta( $post_id, 'event-attendees', true );
		if( in_array( $from_user_id, $attendees ) )
			$going = __( 'is attending your Event', 'bp-simple-events' );
		else
			$going = __( 'is not attending your Event', 'bp-simple-events' );

		// Set up and send the message
		$to = $receiver_email;
		$subject = '[' . get_blog_option( 1, 'blogname' ) . '] ' . sprintf( __( '%s %s', 'bp-simple_events' ), stripslashes( $sender_name ), $going );


		$message = sprintf( __(
'%s %s

To see %s\'s profile: %s

Event: %s

---------------------
', 'bp-simple_events' ), $sender_name, $going, $sender_name, $sender_profile_link, $event_link );

		// Only add the link to email notifications settings if the component is active
		if ( bp_is_active( 'settings' ) ) {
			$receiver_settings_link = trailingslashit( bp_core_get_user_domain( $to_user_id ) . bp_get_settings_slug() . '/notifications' );
			$message .= sprintf( __( 'To disable these notifications please log in and go to: %s', 'bp-simple_events' ), $receiver_settings_link );
		}

		wp_mail( $to, $subject, $message );

	}
}
add_action( 'pp_event_notification', 'pp_events_send_attend_notification', 1, 2 );


// Remove a user's notification data. for  an "about to be deleted" user
function pp_events_remove_notifications_data( $user_id = 0 ) {
	if ( empty( $user_id ) )
		return false;

	// Arguments are the user_id being deleted, the component id, the component action
	bp_notifications_delete_notifications_from_user( $user_id, buddypress()->event->id, 'event_attender' );
}
add_action( 'wpmu_delete_user', 'pp_events_remove_notifications_data', 1 );
add_action( 'delete_user', 'pp_events_remove_notifications_data', 1 );
