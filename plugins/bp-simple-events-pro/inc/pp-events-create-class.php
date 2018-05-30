<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$image_support = get_option( 'events-img-support' );

if( $image_support == '1' )
	require( PP_EVENTS_DIR . '/inc/pp-image-editor-class.php' );


/**
 * create & edit events from member profile
 * file only required when loading the template:
 * templates\members\single\profile-events-create.php
 * in inc\pp-events-screen.php
 */

class PP_Simple_Events_Create {

	public $title = '';
	public $description = '';
	public $date = '';
	public $date_end = '';
	public $url = '';
	public $address = '';
	public $latlng = '';
	public $cats = '';
	public $cats_checked = array();
	public $groups = array();
	public $groups_checked = array();
	public $attend_button = 0;
	public $attendees_list = 0;
	public $attendees_public = 0;
	public $attendees_avatars = 0;
	public $attend_notify = 0;
	public $post_id = 0;
	public $editor = false;

	private $edit_permission = false;
	private $user_id = 0;
	private $img_error = false;
	private $errors = '';

    public function __construct() {

		if( ! bp_is_my_profile() && ! is_super_admin() )
			return;

		if( ! user_can( bp_displayed_user_id(), 'publish_events' ) )
			return;

		add_filter( 'bp_core_render_message_content', array( $this, 'message_format' ), 11, 2 );

		if( isset( $_GET['eid'] ) )
			$this->edit();

		$this->get_title();
		$this->get_description();
		$this->get_date();
		$this->get_date_end();
		$this->get_address();
		$this->get_url();
		$this->get_latlng();
		$this->get_cats_checked();
		$this->get_attend_button();
		$this->get_attendees_list();
		$this->get_attendees_public();
		$this->get_attendees_avatars();
		$this->get_attend_notify();

		if( PP_GROUPS ) {
			$this->get_groups();
			$this->get_groups_checked();
		}

		$this->save();

	}

	private function edit() {

		if( ! isset( $_GET['edn'] ) || ! wp_verify_nonce( $_GET['edn'], 'editing' ) )
			echo 'Security Fail';
		else {

			$this->edit_permission_check( $_GET['eid'] );

			if( ! $this->edit_permission )
				echo 'You cannot edit this Event.';
			else {
				$post_object = get_post( $this->post_id );
				$this->title = $post_object->post_title;
				$this->description = $post_object->post_content;
				$this->cats_checked = wp_get_post_categories( $this->post_id );
				$this->editor = true;
			}
		}
	}

	private function edit_permission_check( $post_id) {

		$post_author_id = get_post_field( 'post_author', $post_id );

		if( $post_author_id != bp_displayed_user_id() )
			$this->edit_permission = false;
		else {
			$this->edit_permission = true;
			$this->post_id = $post_id;
		}

	}

	function get_title() {

		if( isset( $_POST['event-title'] ) && ! empty( $_POST['event-title'] ) )
			$this->title = stripslashes( $_POST['event-title'] );

	}

	function get_description() {

		if( isset( $_POST['event-description'] ) && ! empty( $_POST['event-description'] ) )
			$this->description = stripslashes( $_POST['event-description'] );

	}

	function get_date() {

		if( isset( $_POST['event-date'] ) && ! empty( $_POST['event-date'] ) )
			$date = $_POST['event-date'];
		else
			$date = get_post_meta( $this->post_id, 'event-date', true );

		$this->date = ! empty( $date ) ? $date : '';  //current_time( 'l, F j, Y' );

	}

	function get_date_end() {

		if( isset( $_POST['event-date-end'] ) && ! empty( $_POST['event-date-end'] ) )
			$date = $_POST['event-date-end'];
		else
			$date = get_post_meta( $this->post_id, 'event-date-end', true );

		$this->date_end = ! empty( $date ) ? $date : '';  //current_time( 'l, F j, Y' );

	}

	function get_address() {

		if( isset( $_POST['event-address'] ) && ! empty( $_POST['event-address'] ) )
			$address = $_POST['event-address'];
		else
			$address = get_post_meta( $this->post_id, 'event-address', true );

		$this->address = ! empty( $address ) ? $address : '';

	}

	function get_latlng() {

		if( isset( $_POST['event-latlng'] ) && ! empty( $_POST['event-latlng'] ) )
			$latlng = $_POST['event-latlng'];
		else
			$latlng = get_post_meta( $this->post_id, 'event-latlng', true );

		$this->latlng = ! empty( $latlng ) ? $latlng : '';

	}

	function get_url() {

		if( isset( $_POST['event-url'] ) && ! empty( $_POST['event-url'] ) )
			$url = $_POST['event-url'];
		else
			$url = get_post_meta( $this->post_id, 'event-url', true );

		$this->url = ! empty( $url ) ? $url : '';

	}


	function get_cats_checked() {

		if( isset( $_POST['event-cats'] ) && ! empty( $_POST['event-cats'] ) )
			$this->cats_checked = $_POST['event-cats'];

	}


	function get_attend_button() {

		if( isset( $_POST['event-attend-button'] ) )
			$attend_button = 1;
		else
			$attend_button = get_post_meta( $this->post_id, 'event-attend-button', true );

		$this->attend_button = ! empty( $attend_button ) ? $attend_button : '';

	}

	function get_attendees_list() {

		if( isset( $_POST['event-attendees-list'] ) )
			$attendees_list = 1;
		else
			$attendees_list = get_post_meta( $this->post_id, 'event-attendees-list', true );

		$this->attendees_list = ! empty( $attendees_list ) ? $attendees_list : '';

	}

	function get_attendees_public() {

		if( isset( $_POST['event-attendees-list-public'] ) )
			$attendees_list_public = 1;
		else
			$attendees_list_public = get_post_meta( $this->post_id, 'event-attendees-list-public', true );

		$this->attendees_list_public = ! empty( $attendees_list_public ) ? $attendees_list_public : '';

	}

	function get_attendees_avatars() {

		if( isset( $_POST['event-attendees-list-avatars'] ) )
			$attendees_list_avatars = 1;
		else
			$attendees_list_avatars = get_post_meta( $this->post_id, 'event-attendees-list-avatars', true );

		$this->attendees_list_avatars = ! empty( $attendees_list_avatars ) ? $attendees_list_avatars : '';

	}

	function get_attend_notify() {

		if( isset( $_POST['event-attend-notify'] ) )
			$attend_notify = 1;
		else
			$attend_notify = get_post_meta( $this->post_id, 'event-attend-notify', true );

		$this->attend_notify = ! empty( $attend_notify ) ? $attend_notify : '';

	}


	/**
	 * Collect ids for all groups the member belongs to
	 * Check if any of those groups has enabled Event assignment
	 */
	function get_groups() {

		$user_groups_ids = groups_get_user_groups();

		foreach( $user_groups_ids['groups'] as $group_id ) {

			$assignable = groups_get_groupmeta( $group_id, 'pp-events-assignable'  );

			if( $assignable == '1' ) {

				$args = array( 'group_id'  => $group_id );
				$group_row = groups_get_group( $args );

				$this->groups[$group_id] = $group_row->name;

			}
        }

        if( ! empty( $this->groups ) )
			asort( $this->groups );

	}

	function get_groups_checked() {

		if( isset( $_POST['event-groups'] ) && ! empty( $_POST['event-groups'] ) )
			$groups_checked = $_POST['event-groups'];
		else
			$groups_checked = get_post_meta( $this->post_id, 'event-groups', false );

		$this->groups_checked = ! empty( $groups_checked ) ? $groups_checked : $this->groups_checked;
	}


	function save() {

		if( bp_is_my_profile() || is_super_admin() )
			$this->user_id = bp_displayed_user_id();
		else {
			bp_core_add_message( __( 'Please use your own profile to create or edit Events.', 'bp-simple-events' ), 'error' );
			return;
		}

		if( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) &&  $_POST['action'] == "event-action") {

			check_admin_referer( 'event-nonce' );

			$this->check_required_fields();

			if( ! empty( $this->errors ) ) {

				$this->errors = 'These fields are required: ' . $this->errors;

				bp_core_add_message( $this->errors, 'error' );

			}

			else {

				// set post_date to event-date ( start date ) so that Calendars don't use 'creation' date
				$event_date = sanitize_text_field( $_POST['event-date'] );
				$event_date = date("Y-m-d H:i:s", strtotime($event_date));
			
				$event = array(
					'post_title'	=>	wp_strip_all_tags( $_POST['event-title'] ),
					'post_content'	=>	$_POST['event-description'],
					'post_status'	=>	'publish',
					'post_type'		=>	'event',
					'post_author'   =>  $this->user_id,
					'post_date'     =>  $event_date,
					'post_date_gmt' =>  get_gmt_from_date( $event_date )
				);

				if( ! empty( $_POST['eid'] ) ) {

					$this->edit_permission_check( $_POST['eid'] );

					if( $this->edit_permission ) {

						$event['ID'] = $this->post_id;

						$this->post_id = wp_update_post( $event );

					}
				}
				else
					$this->post_id = wp_insert_post( $event );


				if( $this->post_id != 0 ) {

					// needed so that 'status' is not 'future' due to date being in the future
					wp_publish_post( $this->post_id );
					
					$this->save_event_meta();

					if( ! $this->img_error )
						bp_core_add_message( __( 'Event was saved.', 'bp-simple-events' ) );
					else
						bp_core_add_message( __( 'Event was saved. But your image was not a jpg and was not saved. Edit your event to add an image.', 'bp-simple-events' ) );

					bp_core_redirect( bp_core_get_user_domain( $this->user_id ) . '/events/' );

				}
			}
		}
	}


	private function check_required_fields() {

		if( $_POST['event-title'] == '' )
			$this->errors .= '# ' . __( 'Title', 'bp-simple-events' );

		if( $_POST['event-description'] == '' )
			$this->errors .= '# ' . __( 'Description', 'bp-simple-events' );

		$required_fields = get_option( 'pp_events_required' );

		if( empty( $_POST['event-date'] ) && in_array( 'date', $required_fields ) )
			$this->errors .= '# ' . __( 'Start', 'bp-simple-events' );

		if( empty( $_POST['event-date-end'] ) && in_array( 'date-end', $required_fields ) )
			$this->errors .= '# ' . __( 'End', 'bp-simple-events' );

		if( empty( $_POST['event-location'] ) && in_array( 'location', $required_fields ) )
			$this->errors .= '# ' . __( 'Location', 'bp-simple-events' );

		if( empty( $_POST['event-url'] ) && in_array( 'url', $required_fields ) )
			$this->errors .= '# ' . __( 'Url', 'bp-simple-events' );

		if( empty( $_POST['event-cats'] ) && in_array( 'categories', $required_fields ) )
			$this->errors .= '# ' . __( 'Categories', 'bp-simple-events' );

		if( PP_GROUPS )
			if( empty( $_POST['event-groups'] ) && in_array( 'groups', $required_fields ) )
				$this->errors .= '# ' . __( 'Groups', 'bp-simple-events' );

		if( empty( $_FILES['event-img']['name'] ) && in_array( 'image', $required_fields ) )
			$this->errors .= '# ' . __( 'Image', 'bp-simple-events' );

		if( ! $this->image_type_check() && in_array( 'image', $required_fields ) )
			$this->errors .= '# ' . __( 'Image not a jpg', 'bp-simple-events' );
	}


	function save_event_meta() {
	
		if( ! empty( $_POST['event-date'] ) ) {
			$this->date = sanitize_text_field( $_POST['event-date'] );
			update_post_meta( $this->post_id, 'event-date', $this->date );
		}

		if( ! empty( $_POST['event-date-end'] ) ) {
			$this->date_end = sanitize_text_field( $_POST['event-date-end'] );
			update_post_meta( $this->post_id, 'event-date-end', $this->date_end );
		}


		$this->save_timestamp();

		$this->save_location();

		$this->save_url();

		$this->save_cats();

		$this->save_groups();

		$this->save_attend_button();

		$this->save_attendees_list();

		$this->save_attendees_list_public();

		$this->save_attendees_list_avatars();

		$this->save_attend_notify();

		$this->process_img();

	}


	/**
	 * A unix timestamp is needed for sorting based on Event date + time
	 */
	private function save_timestamp() {

		$date_flag = false;
		$date = date_parse( $this->date );

		if( $date["error_count"] == 0 && checkdate( $date["month"], $date["day"], $date["year"] ) )
			$date_flag = true;


		if( $date_flag ) {
			$timestamp = strtotime( $this->date );
		}
		else {

			$event_unix = get_post_meta( $this->post_id, 'event-unix', true );

			if( ! empty( $event_unix ) )
				$timestamp = $event_unix;
			else
				$timestamp = current_time( 'timestamp' );
		}

		update_post_meta( $this->post_id, 'event-unix', $timestamp );

	}

	private function save_location() {

		if( ! empty( $_POST['event-location'] ) ) {

			if( ! empty( $_POST['event-address'] ) ) {

				$this->address = sanitize_text_field( $_POST['event-address'] );
				update_post_meta( $this->post_id, 'event-address', $this->address );

			}
			else
				delete_post_meta( $this->post_id, 'event-address' );

			if( ! empty( $_POST['event-latlng'] ) ) {

				$this->latlng = sanitize_text_field( $_POST['event-latlng'] );
				update_post_meta( $this->post_id, 'event-latlng', $this->latlng );

			}
			else
				delete_post_meta( $this->post_id, 'event-latlng' );

		}
		else {

			delete_post_meta( $this->post_id, 'event-address' );
			delete_post_meta( $this->post_id, 'event-latlng' );
		}

	}

	private function save_url() {

		if( ! empty( $_POST['event-url'] ) ) {

			$this->url = sanitize_text_field( $_POST['event-url'] );
			update_post_meta( $this->post_id, 'event-url', $this->url );

		}
		else
			delete_post_meta( $this->post_id, 'event-url' );
	}

	// save assigned categories
	private function save_cats() {

		if ( isset( $_POST['event-cats'] ) && ! empty( $_POST['event-cats'] ) ) {

			$cats = array();

			foreach ( $_POST['event-cats'] as $key => $value )
				$cats[] = $value;

			wp_set_post_terms($this->post_id, $cats, 'category');
		}

	}

	 /**
	  * save assigned groups for display in \inc\templates\groups\single\group-events-loop.php
	  * create activity entry for each assigned group
	  */

	private function save_groups() {

		$prior_groups = get_post_meta( $this->post_id, 'event-groups' );

		foreach( $prior_groups as $pgroup_id ) {

			$args_delete = array(
				'component'         => 'groups',
				'item_id'           => $pgroup_id,
				'secondary_item_id' => $this->post_id
			);

			BP_Activity_Activity::delete( $args_delete );
		}

		delete_post_meta( $this->post_id, 'event-groups' );



		if ( isset( $_POST['event-groups'] ) && ! empty( $_POST['event-groups'] ) ) {

			foreach ( $_POST['event-groups'] as $key => $group_id ) {

				add_post_meta( $this->post_id, 'event-groups', $group_id, false );

				$group = groups_get_group( array( 'group_id' => $group_id ) );

				if( $group->status == 'public' )
					$hide_sitewide = false;
				else
					$hide_sitewide = true;

				$content = wp_strip_all_tags( wp_trim_words( $_POST['event-description'], 30 ) );


				$args = array(
				//	'id'                => $existing_activity_id,
					'user_id'           => bp_loggedin_user_id(),
					'action'            => sprintf( __( '%1$s created a new <a href="%2$s">Event</a> in the group %3$s', 'bp-simple-events' ), bp_core_get_userlink( bp_loggedin_user_id() ), get_permalink( $this->post_id ), '<a href="' . bp_get_group_permalink( $group ) . '">' .  esc_attr( $group->name ) . '</a>' ),
					'content'           => $content,
					'primary_link'      => get_permalink( $this->post_id ),
					'component'         => 'groups',
					'type'              => 'new_event',
					'item_id'           => $group_id,
					'secondary_item_id' => $this->post_id,
					'hide_sitewide'     => $hide_sitewide
				);

				groups_record_activity( $args );

			}
		}
	}


	private function save_attend_button() {

		if( ! empty( $_POST['event-attend-button'] ) ) {

			$this->attend = sanitize_text_field( $_POST['event-attend-button'] );
			update_post_meta( $this->post_id, 'event-attend-button', $this->attend_button );

		}
		else
			delete_post_meta( $this->post_id, 'event-attend-button' );
	}

	private function save_attendees_list() {

		if( ! empty( $_POST['event-attendees-list'] ) ) {

			$this->attendees_list = sanitize_text_field( $_POST['event-attendees-list'] );
			update_post_meta( $this->post_id, 'event-attendees-list', $this->attendees_list );

		}
		else
			delete_post_meta( $this->post_id, 'event-attendees-list' );
	}

	private function save_attendees_list_public() {

		if( ! empty( $_POST['event-attendees-list-public'] ) ) {

			$this->attendees_list_public = sanitize_text_field( $_POST['event-attendees-list-public'] );
			update_post_meta( $this->post_id, 'event-attendees-list-public', $this->attendees_list_public );

		}
		else
			delete_post_meta( $this->post_id, 'event-attendees-list-public' );
	}	
	
	private function save_attendees_list_avatars() {

		if( ! empty( $_POST['event-attendees-list-avatars'] ) ) {

			$this->attendees_list_avatars = sanitize_text_field( $_POST['event-attendees-list-avatars'] );
			update_post_meta( $this->post_id, 'event-attendees-list-avatars', $this->attendees_list_avatars );

		}
		else
			delete_post_meta( $this->post_id, 'event-attendees-list-avatars' );
	}
	
	private function save_attend_notify() {

		if( ! empty( $_POST['event-attend-notify'] ) ) {

			$this->attendees_list = sanitize_text_field( $_POST['event-attend-notify'] );
			update_post_meta( $this->post_id, 'event-attend-notify', $this->attend_notify );

		}
		else
			delete_post_meta( $this->post_id, 'event-attend-notify' );
	}

	// check image upload
	function process_img() {

		if( isset( $_POST['event-img-delete'] ) )
			if(  $_POST['event-img-delete'] == '1' )
				delete_post_meta( $this->post_id, '_thumbnail_id' );

		if( ! empty( $_FILES['event-img']['name'] ) ) {

			$type = $this->image_type_check();

	        if( $type ) {

				if( $_FILES ) {
					foreach ( $_FILES as $file => $array ) {
						$newupload = $this->save_img( $file, true );
					}
				}
	        }
	        else
	            $this->img_error = true;

	    }
	}

	// only jpg file types are allowed
	private function image_type_check() {

		if( ! empty( $_FILES['event-img']['name'] ) ) {

	        $supported_types = array("image/jpg", "image/jpeg");
	        $arr_file_type = wp_check_filetype(basename($_FILES['event-img']['name']));
	        $uploaded_type = $arr_file_type['type'];

	        if( in_array( $uploaded_type, $supported_types ) )
				return true;
			else
				return false;
		}
		return true;
	}

	// image save
	function save_img( $file_handler, $setthumb='false' ) {

		if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();

		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		require_once(ABSPATH . "wp-admin" . '/includes/file.php');
		require_once(ABSPATH . "wp-admin" . '/includes/media.php');

		$attach_id = media_handle_upload( $file_handler, $this->post_id );

		if( $setthumb )
			update_post_meta( $this->post_id, '_thumbnail_id', $attach_id );

		return $attach_id;
	}

	function message_format( $content, $type ) {

		$content = str_replace('#', '<br/>', $content);

		return $content;

	}


}  // end of PP_Simple_Events_Create

/**
 * this global is only used for this template
 * inc\templates\members\single\profile-events-create.php
 */

global $pp_ec;
$pp_ec = new PP_Simple_Events_Create();