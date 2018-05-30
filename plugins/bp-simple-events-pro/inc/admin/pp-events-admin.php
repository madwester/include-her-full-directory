<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * CPT admin metaboxes
 * Groups admin metabox
 */


class PP_Simple_Events_Admin {

    public function __construct() {

		add_action( 'admin_enqueue_scripts',            array( $this, 'events_scripts' ), 1000 );
		add_action( 'add_meta_boxes',                   array( $this, 'custom_meta_boxes' ) );
		add_action( 'save_post_event',                  array( $this, 'save_meta_boxes' ) );
		add_filter( 'manage_edit-event_columns',        array( $this, 'custom_columns_head' ), 10 );
		add_action( 'manage_event_posts_custom_column', array( $this, 'custom_columns_content' ), 10, 2 );

		add_filter( 'wp_insert_post_data',              array( $this, 'change_post_date' ), 199, 2);

		if( PP_GROUPS ) {

			add_action( 'bp_groups_admin_meta_boxes',       array( $this, 'add_group_metabox' ) );
			add_action( 'bp_group_admin_edit_after',        array( $this, 'save_group_metabox' ), 11, 1 );

		}
	}

	// add scripts & styles
	function events_scripts() {
		global $post_type;

		if( 'event' != $post_type )
			return;

		wp_enqueue_script('jquery-ui-datepicker', array( 'jquery' ) );

		wp_enqueue_script('jquery-ui-timepicker-addon', plugins_url( 'js/jquery-ui-timepicker-addon.js', dirname( __FILE__ ) ), array('jquery-ui-core','jquery-ui-datepicker') );

		wp_enqueue_style( 'jquery-ui-datepicker', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css', true);

		wp_enqueue_style( 'jquery-ui-timepicker-addon', plugins_url( 'css/jquery-ui-timepicker-addon.css', dirname( __FILE__ ) ) );

		wp_enqueue_script('script', plugins_url( 'js/events.js', dirname( __FILE__ ) ), array('jquery') );

		$gapikey = get_site_option( 'pp_gapikey' );

		if ( $gapikey != false ) {

			wp_register_script( 'google-places-api', '//maps.googleapis.com/maps/api/js?key=' . $gapikey . '&libraries=places' );
			wp_print_scripts( 'google-places-api' );

		}
	}

	// add event meta boxes
	function custom_meta_boxes() {
		global $post_type;

		if( 'event' != $post_type )
			return;

		add_meta_box('event_start',  __('Event Start', 'bp-simple-events'), array( $this, 'event_start' ), 'event', 'normal', 'default');

		add_meta_box('event_stop',  __('Event Stop', 'bp-simple-events'), array( $this, 'event_stop' ), 'event', 'normal', 'default');

		add_meta_box('event_location',  __('Location', 'bp-simple-events'), array( $this, 'location_show' ), 'event', 'normal', 'default');

		add_meta_box('event_url',  __('URL', 'bp-simple-events'), array( $this, 'url_show' ), 'event', 'normal', 'default');

		add_meta_box('event_attending',  __('Attending Options', 'bp-simple-events'), array( $this, 'attending_show' ), 'event', 'normal', 'default');

		add_meta_box('event_groups',  __('Groups', 'bp-simple-events'), array( $this, 'groups_show' ), 'event', 'normal', 'default');
	}


	// start metabox
	function event_start( $post ) {

		wp_nonce_field( 'start_box', 'start_box_nonce' );

		$date = get_post_meta( $post->ID, 'event-date', true );

		$date = ! empty( $date ) ? $date : current_time( 'l, F j, Y' );
		?>

		<p>
			<label for="event-date"><?php echo __( 'Start:', 'bp-simple-events' ); ?></label>
			<input type="text" size="25" id="event-date" name="event-date" value="<?php echo $date; ?>" />
		</p>

		<?php
	}

	// stop metabox
	function event_stop( $post ) {

		wp_nonce_field( 'stop_box', 'stop_box_nonce' );

		$date = get_post_meta( $post->ID, 'event-date-end', true );

		$date = ! empty( $date ) ? $date : current_time( 'l, F j, Y' );
		?>

		<p>
			<label for="event-date-end"><?php echo __( 'End:', 'bp-simple-events' ); ?></label>
			<input type="text" size="25" id="event-date-end" name="event-date-end" value="<?php echo $date; ?>" />
		</p>

		<?php
	}


	// url metabox
	function url_show( $post ) {

		wp_nonce_field( 'url_box', 'url_box_nonce' );

		$url = get_post_meta( $post->ID, 'event-url', true );

		$url = ! empty( $url ) ? $url : '';
		?>

		<p>
			<label for="event-url"><?php echo __( 'Event Url:', 'bp-simple-events' ); ?></label>
			<input type="text" size="70" id="event-url" name="event-url" value="<?php echo $url; ?>" />
		</p>

		<?php
	}


	// location metabox
	function location_show( $post ) {

		wp_nonce_field( 'location_box', 'location_box_nonce' );

		$location = get_post_meta( $post->ID, 'event-address', true );
		$location = ! empty( $location ) ? $location : '';

		$latlng = get_post_meta( $post->ID, 'event-latlng', true );
		$latlng = ! empty( $latlng ) ? $latlng : '';

		if( ! empty( $location ) ) :
		?>
			<p>
				<label for="event-location"><?php echo __( 'Event Location:', 'bp-simple-events' ); ?></label>
				<input type="text" size="80" id="event-location" name="event-location" placeholder="<?php echo __( 'Start typing location name...', 'bp-simple-events' ); ?>" value="<?php echo $location; ?>" />
			</p>
		<?php else : ?>
			<p>
				<label for="event-location"><?php echo __( 'Event Location:', 'bp-simple-events' ); ?></label><br/>
				<input type="text" size="80" id="event-location" name="event-location" placeholder="Start typing location name..." />

		<?php endif; ?>

			<input type="hidden" id="event-address" name="event-address" value="<?php echo $location; ?>" />
			<input type="hidden" id="event-latlng" name="event-latlng"  value="<?php echo $latlng; ?>" />
		</p>

		<?php
	}

	// attending options
	function attending_show( $post ) {

		wp_nonce_field( 'attending_box', 'attending_box_nonce' );

		$attend_button = get_post_meta( $post->ID, 'event-attend-button', true );
		$attend_button = ! empty( $attend_button ) ? $attend_button : '';

		$attendees_list = get_post_meta( $post->ID, 'event-attendees-list', true );
		$attendees_list = ! empty( $attendees_list ) ? $attendees_list : '';

		$attend_notify = get_post_meta( $post->ID, 'event-attend-notify', true );
		$attend_notify = ! empty( $attend_notify ) ? $attend_notify : '';
		?>

		<p>
			<input type="checkbox" name="event-attend-button" value="1" <?php checked( $attend_button, 1 ); ?> /> <?php echo __( 'Add an "I want to attend" button on single event pages.', 'bp-simple-events' ); ?>
		</p>

		<p>
			<input type="checkbox" name="event-attendees-list" value="1" <?php checked( $attendees_list, 1 ); ?> /> <?php echo __( 'Show a list of attendees on single event pages. The list will only be visible to you.', 'bp-simple-events' ); ?>
		</p>

		<p>
			<input type="checkbox" name="event-attend-notify" value="1" <?php checked( $attend_notify, 1 ); ?> /> <?php echo __( 'Receive a Notification when a member decides to attend or not attend your event.', 'bp-simple-events' ); ?>
		</p>

		<?php
	}

	function groups_show( $post ) {

		wp_nonce_field( 'groups_box', 'groups_box_nonce' );

		$args = array(
			'orderby'           => 'name',
			'order'             => 'ASC',
			'per_page'          => NULL,
			'page'              => NULL,
			'show_hidden'       => true,
			'populate_extras'   => false,
			'update_meta_cache' => false
		);

		$groups = groups_get_groups( $args );
		$assignable_groups = array();

		foreach( $groups['groups'] as $group ) {

			$assignable = groups_get_groupmeta( $group->id, 'pp-events-assignable'  );

			if( $assignable == '1' )
				$assignable_groups[] = $group;

		}

		$groups_checked = get_post_meta( $post->ID, 'event-groups', false );
		?>

		<p>
			<?php
			if( ! empty( $assignable_groups ) ) {

				foreach( $assignable_groups as $group ) {

					$checked = '';
					if( in_array( $group->id, $groups_checked ) )
						$checked = ' checked';

					echo '<input type="checkbox" name="event-groups[]" value="' . $group->id . '"' . $checked . '/> ' . $group->name . '<br/>';
				}
			}
			else
				echo __( 'There are no Groups that have selected to allow Event assignment.', 'bp-simple-events' );

			?>
		</p>
		<?php
	}

	function change_post_date ( $data, $postarr ) {

        if ( 'event' == $data['post_type'] ) {

			if ( isset( $_POST['event-date'] ) ) {

		        $post_id = $postarr['ID'];

				// set post_date to event-date ( start date ) so that Calendars don't use 'creation' date
				$event_date = sanitize_text_field( $_POST['event-date'] );
				$event_date = date("Y-m-d H:i:s", strtotime($event_date));

				$data['post_date']	    = $event_date;
				$data['post_date_gmt']	= get_gmt_from_date( $event_date );
				$data['post_status']	= 'publish';

			}
	    }

	    return $data;
	}


	function save_meta_boxes( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;

		if ( ! current_user_can( 'manage_options', $post_id ) )
			return $post_id;

		$this->save_start( $post_id );
		$this->save_stop( $post_id );
		$this->save_url( $post_id );
		$this->save_location( $post_id );
		$this->save_attend( $post_id );
		$this->save_groups( $post_id );

	}


	private function save_start( $post_id ) {

		if ( ! isset( $_POST['start_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['start_box_nonce'];

		if ( ! wp_verify_nonce( $nonce, 'start_box' ) )
			return $post_id;

		$date = sanitize_text_field( $_POST['event-date'] );
		update_post_meta( $post_id, 'event-date', $date );

		$this->save_timestamp( $post_id, $date );

	}

	private function save_stop( $post_id ) {

		if ( ! isset( $_POST['stop_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['stop_box_nonce'];

		if ( ! wp_verify_nonce( $nonce, 'stop_box' ) )
			return $post_id;

		$date = sanitize_text_field( $_POST['event-date-end'] );
		update_post_meta( $post_id, 'event-date-end', $date );

	}


	/**
	 * A unix timestamp is needed for sorting based on Event start date
	 */
	private function save_timestamp( $post_id, $event_date ) {

		$date_flag = false;
		$date = date_parse( $event_date );

		if( $date["error_count"] == 0 && checkdate( $date["month"], $date["day"], $date["year"] ) )
			$date_flag = true;


		if( $date_flag ) {
			$timestamp = strtotime( $event_date );
		}
		else {

			$event_unix = get_post_meta( $post_id, 'event-unix', true );

			if( ! empty( $event_unix ) )
				$timestamp = $event_unix;
			else
				$timestamp = current_time( 'timestamp' );
		}

		update_post_meta( $post_id, 'event-unix', $timestamp );

	}

	private function save_url( $post_id ) {

		if ( ! isset( $_POST['url_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['url_box_nonce'];

		if ( ! wp_verify_nonce( $nonce, 'url_box' ) )
			return $post_id;

		$url = sanitize_text_field( $_POST['event-url'] );
		update_post_meta( $post_id, 'event-url', $url );

	}

	private function save_location( $post_id ) {

		if ( ! isset( $_POST['location_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['location_box_nonce'];

		if ( ! wp_verify_nonce( $nonce, 'location_box' ) )
			return $post_id;

		$address = sanitize_text_field( $_POST['event-address'] );
		update_post_meta( $post_id, 'event-address', $address );

		$latlng = sanitize_text_field( $_POST['event-latlng'] );
		update_post_meta( $post_id, 'event-latlng', $latlng );

	}

	private function save_attend( $post_id ) {

		if ( ! isset( $_POST['attending_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['attending_box_nonce'];

		if ( ! wp_verify_nonce( $nonce, 'attending_box' ) )
			return $post_id;

		if( isset( $_POST['event-attend-button'] ) )
			update_post_meta( $post_id, 'event-attend-button', $_POST['event-attend-button'] );
		else
			delete_post_meta( $post_id, 'event-attend-button' );

		if( isset( $_POST['event-attendees-list'] ) )
			update_post_meta( $post_id, 'event-attendees-list', $_POST['event-attendees-list'] );
		else
			delete_post_meta( $post_id, 'event-attend-list' );

		if( isset( $_POST['event-attend-notify'] ) )
			update_post_meta( $post_id, 'event-attend-notify', $_POST['event-attend-notify'] );
		else
			delete_post_meta( $post_id, 'event-attend-notify' );

	}

	private function save_groups( $post_id ) {

		if ( ! isset( $_POST['groups_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['groups_box_nonce'];

		if ( ! wp_verify_nonce( $nonce, 'groups_box' ) )
			return $post_id;


		if ( bp_is_active( 'activity' ) ) {

			$prior_groups = get_post_meta( $post_id, 'event-groups' );

			foreach( $prior_groups as $pgroup_id ) {

				$args_delete = array(
					'component'         => 'groups',
					'item_id'           => $pgroup_id,
					'secondary_item_id' => $post_id
				);

				BP_Activity_Activity::delete( $args_delete );
			}
		}

		delete_post_meta( $post_id, 'event-groups' );



		if ( isset( $_POST['event-groups'] ) && ! empty( $_POST['event-groups'] ) ) {

			foreach ( $_POST['event-groups'] as $key => $group_id ) {

				add_post_meta( $post_id, 'event-groups', $group_id, false );

				$group = groups_get_group( array( 'group_id' => $group_id ) );

				if( $group->status == 'public' )
					$hide_sitewide = false;
				else
					$hide_sitewide = true;

				$content = get_post_field('post_content', $post_id);
				$content = wp_strip_all_tags( wp_trim_words( $content, 30 ) );

				if ( bp_is_active( 'activity' ) ) {

					$args = array(
					//	'id'                => $existing_activity_id,
						'user_id'           => bp_loggedin_user_id(),
						'action'            => sprintf( __( '%1$s created a new <a href="%2$s">Event</a> in the group %3$s', 'bp-simple-events' ), bp_core_get_userlink( bp_loggedin_user_id() ), get_permalink( $post_id ), '<a href="' . bp_get_group_permalink( $group ) . '">' .  esc_attr( $group->name ) . '</a>' ),
						'content'           => $content,
						'primary_link'      => get_permalink( $post_id ),
						'component'         => 'groups',
						'type'              => 'new_event',
						'item_id'           => $group_id,
						'secondary_item_id' => $post_id,
						'hide_sitewide'     => $hide_sitewide
					);

					groups_record_activity( $args );
				}

			}

		}
	}



	// add custom columns
	function custom_columns_head( $defaults ) {

		unset( $defaults['date'] );

		$defaults['event_date'] = __( 'Date', 'bp-simple-events' );
		$defaults['event_location'] = __( 'Location', 'bp-simple-events' );

		return $defaults;
	}

	// add content to custom columns
	function custom_columns_content( $column_name, $post_id ) {

		if ( 'event_date' == $column_name ) {
			$date = get_post_meta( $post_id, 'event-date', true );
			$time = get_post_meta( $post_id, 'event-time', true );
			echo $date . '<br/>' . $time;

		}

		if ( 'event_location' == $column_name ) {
			$location = get_post_meta( $post_id, 'event-address', true );
			echo $location;
		}
	}



	/**
	 * Create & save metabox on single group screen
	 */

	function add_group_metabox() {

		add_meta_box( 'bp_group_events', _x( 'Group Events', 'group admin edit screen', 'bp-simple-events' ),  array( $this, 'show_group_metabox'), get_current_screen()->id, 'side' );

	}

	function show_group_metabox() {
		$group_id = isset( $_REQUEST['gid'] ) ? (int) $_REQUEST['gid'] : '';
	?>

		<div id="bp_groups_events" class="postbox">
			<div class="inside">
				<input type="checkbox" name="pp-events-assignable" id="pp-events-assignable" value="1"<?php $this->group_assignable_setting( $group_id ); ?> /> <?php _e( 'Allow group members to assign Events to this group.', 'bp-simple-events' ); ?>
			</div>
		</div>

	<?php
	}

	function save_group_metabox( $group_id ) {

		if ( ! empty( $_POST['pp-events-assignable'] ) )
			groups_update_groupmeta( $group_id, 'pp-events-assignable', '1' );
		else
			groups_delete_groupmeta( $group_id, 'pp-events-assignable' );

	}

	private function group_assignable_setting( $group_id ) {

		if ( groups_get_groupmeta( $group_id, 'pp-events-assignable' ) )
			echo ' checked="checked"';

	}

} // end of PP_Simple_Events_Admin class

$pp_se_admin_instance = new PP_Simple_Events_Admin();

