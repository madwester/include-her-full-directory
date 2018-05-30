<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function pp_events_profile() {
	add_action( 'bp_template_content', 'pp_events_profile_screen' );
	bp_core_load_template( 'members/single/plugins' );
}


function pp_events_profile_screen() {
	bp_get_template_part('members/single/profile-events-loop');
}


function pp_events_profile_create() {
	require( PP_EVENTS_DIR . '/inc/pp-events-create-class.php' );
	add_action( 'bp_template_title', 'pp_events_profile_create_title' );
	add_action( 'bp_template_content', 'pp_events_profile_create_screen' );
	bp_core_load_template( 'members/single/plugins' );
}

function pp_events_profile_create_title() {

	if( isset( $_GET['eid'] ) )
	    echo __( 'Edit Event', 'bp-simple-events' );
	else
		echo __( 'Create an Event', 'bp-simple-events' );
}


function pp_events_profile_create_screen() {
	bp_get_template_part('members/single/profile-events-create');
}


function pp_events_profile_archive() {
	add_action( 'bp_template_content', 'pp_events_profile_archive_screen' );
	bp_core_load_template( 'members/single/plugins' );
}

function pp_events_profile_archive_screen() {
	bp_get_template_part('members/single/profile-events-archive');
}


function pp_events_profile_attending() {
	add_action( 'bp_template_content', 'pp_events_profile_attending_screen' );
	bp_core_load_template( 'members/single/plugins' );
}

function pp_events_profile_attending_screen() {
	bp_get_template_part('members/single/profile-events-attending');
}


function pp_events_enqueue() {

	if ( is_page( 'events' ) || ( ( bp_is_my_profile() || is_super_admin() ) && 'events' == bp_current_component() && 'create' == bp_current_action() ) ) {
	
		wp_enqueue_script('jquery-ui-datepicker', array( 'jquery' ) );
		//wp_enqueue_script('jquery-ui-slider', array( 'jquery' ) );		
		wp_enqueue_script('jquery-ui-timepicker-addon', plugin_dir_url(__FILE__) . '/js/jquery-ui-timepicker-addon.js', array('jquery-ui-core','jquery-ui-datepicker') );
		
		wp_enqueue_style( 'jquery-ui-datepicker', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css', true);

		wp_enqueue_style( 'jquery-ui-timepicker-addon', plugin_dir_url(__FILE__) . '/css/jquery-ui-timepicker-addon.css' );		
		
	}
	
	if ( ( bp_is_my_profile() || is_super_admin() ) && 'events' == bp_current_component() && 'create' == bp_current_action() ) {
		
		wp_enqueue_script('script', plugin_dir_url(__FILE__) . '/js/events.js', array('jquery') );

		$gapikey = get_site_option( 'pp_gapikey' );

		if ( $gapikey != false ) {
	
			wp_register_script( 'google-places-api', '//maps.googleapis.com/maps/api/js?key=' . $gapikey . '&libraries=places' );
			wp_print_scripts( 'google-places-api' );
			
		}	
		
	}	

}
add_action('wp_enqueue_scripts', 'pp_events_enqueue');


// loads scripts for All Events Map screen
function pp_events_load_map_scripts() {
	
	$gapikey = get_site_option( 'pp_gapikey' );
					
	if ( $gapikey != false ) {

		wp_register_script( 'google-maps-api', '//maps.googleapis.com/maps/api/js?key=' . $gapikey );
		wp_print_scripts( 'google-maps-api' );
		
		wp_enqueue_script('google-maps-api2', plugin_dir_url(__FILE__) . '/js/markerclusterer.min.js', array('jquery') );
		wp_print_scripts( 'google-maps-api2' );			
		
	}
	
}
add_action( 'pp_events_page_map_scripts', 'pp_events_load_map_scripts' );