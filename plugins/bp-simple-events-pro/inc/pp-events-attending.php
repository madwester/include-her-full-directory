<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


function pp_event_attending_button( $post_id, $author_id ) {
	global $post;

	$user_id = get_current_user_id();

	if ( $user_id == 0 )
		return;

	$attendees = get_post_meta( $post_id, 'event-attendees', true );     //var_dump( $attendees );

	if( $attendees == '')
		$attendees = array();

	if( ! in_array($user_id, $attendees) )
		$button_text = __( 'I want to attend', 'bp-simple-events' );

	else
		$button_text = __( 'Cancel my attendance', 'bp-simple-events' );

	?>
		<form action="" name="event_attend_form" id="event_attend_form" method="post">
			<?php wp_nonce_field('event-attend-form-action', 'event-attend-form-field'); ?>
			<input type="submit" name="event-attend-submit" id="event-attend-submit" value="<?php echo $button_text; ?>" />
		</form>
<?php
}

function pp_event_attend_update() {
	global $post;
	
	if( ! isset( $_POST['event-attend-form-field'] ) )
		return;

	if ( ! wp_verify_nonce($_POST['event-attend-form-field'], 'event-attend-form-action') ) die('Security Check');

	
	$attendees = get_post_meta( $post->ID, 'event-attendees', true);

	if( $attendees == '' )
		$attendees = array();

	if( ! in_array( bp_loggedin_user_id(), $attendees ) )
		$attendees[] = bp_loggedin_user_id();

	else {

		if( ( $key = array_search( bp_loggedin_user_id(), $attendees ) ) !== false )
		    unset( $attendees[$key] );

	}

	$saved = update_post_meta( $post->ID, 'event-attendees', $attendees );

	pp_events_send_attend_notification( $post->post_author, bp_loggedin_user_id(), $post->ID );

}
add_action('pp_single_event_attending', 'pp_event_attend_update' );