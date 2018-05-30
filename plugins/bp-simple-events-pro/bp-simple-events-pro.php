<?php
/*
Plugin Name: BuddyPress Simple Events Pro
Description: An Events plugin for BuddyPress 
Version: 2.4
Author: PhiloPress
Author URI: http://philopress.com/
Requires at least: 4.0.0
Tested up to: 4.7
Text Domain: bp-simple-events
Domain Path: /languages
Copyright (C) 2016-2017 shanebp, PhiloPress 
*/

if ( !defined( 'ABSPATH' ) ) exit;

define( 'PP_EVENTS_STORE_URL', 'http://www.philopress.com/' );
define( 'PP_SIMPLE_EVENTS_PRO', 'BuddyPress Simple Events Pro' );


function pp_events_bp_check() {
	if ( !class_exists('BuddyPress') ) {
		add_action( 'admin_notices', 'pp_events_install_buddypress_notice' );
	}
}
add_action('plugins_loaded', 'pp_events_bp_check', 999);

function pp_events_install_buddypress_notice() {
	echo '<div id="message" class="error fade"><p style="line-height: 150%">';
	_e('BuddyPress Simple Events Pro requires the BuddyPress plugin. Please install BuddyPress or deactivate BuddyPress Simple Events Pro.', 'bp-simple-events');
	echo '</p></div>';
}

function pp_events_init() {

	$vcheck = pp_events_version_check();

	if( $vcheck ) {

		if( get_option( 'pp_events_groups' ) == '1' )
			define( 'PP_GROUPS', true );
		else
			define( 'PP_GROUPS', false );

		define( 'PP_EVENTS_DIR', dirname( __FILE__ ) );

		load_plugin_textdomain( 'bp-simple-events', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		require( dirname( __FILE__ ) . '/inc/pp-events-core.php' );

	}

}
add_action( 'bp_include', 'pp_events_init' );



function pp_events_activation() {

	$vcheck = pp_events_version_check();

	if( $vcheck ) {

		pp_add_event_caps();

		pp_create_post_type_event();

		pp_create_events_page();

		pp_create_events_options();

		pp_img_events_support();

		flush_rewrite_rules();
	}
}
register_activation_hook(__FILE__, 'pp_events_activation');


function pp_events_deactivation () {
	pp_remove_event_caps();

}
register_deactivation_hook(__FILE__, 'pp_events_deactivation');


function pp_events_uninstall () {
	delete_option( 'pp_events_tab_position' );
	delete_option( 'events-img-support' );
	delete_option( 'pp_events_groups' );
	delete_option( 'pp_events_required' );
}
register_uninstall_hook( __FILE__, 'pp_events_uninstall');


function pp_events_version_check() {

	if ( ! defined( 'BP_VERSION' ) )
		return false;

	if( version_compare( BP_VERSION, '2.2', '>=' ) )
		return true;
	else {
		echo '<div id="message" class="error">';
		_e('BuddyPress Simple Events requires at least version 2.2 of BuddyPress.', 'bp-simple-events');
		echo '</div>';
		return false;
	}
}


function pp_create_events_options() {

	// tab position on profile pages
	add_option( 'pp_events_tab_position', '201', '', 'no' );

	// Give Groups the option to assign Events
	add_option( 'pp_events_groups', '0', '', 'no' );

	//default required fields
	add_option( 'pp_events_required', array(), '', 'no' );
	
	if ( ! get_option( 'pp-events-map-single-settings' ) ) {
	
		$settings_single = array();

		$settings_single["map_zoom_level"] = 10;
		$settings_single["map_height"] = 200;
	
		add_site_option( 'pp-events-map-single-settings', $settings_single );
	
	}
	
	if ( ! get_option( 'pp-events-map-all-settings' ) ) {
	
		$settings_all = array();
	
		$settings_all["event_address"] = "Chicago, IL, USA";    
		$settings_all["event_latlng"] = "41.88,-87.623"; 
		$settings_all["map_zoom_level_all"] = 4;
		$settings_all["map_height_all"] = 400;
	
		add_site_option( 'pp-events-map-all-settings', $settings_all );
		
	}
	
	if ( ! get_option( 'pp_events_license_key' ) )
		add_option( 'pp_events_license_key', '' );

	if ( ! get_option( 'pp_events_license_status' ) )
		add_option( 'pp_events_license_status', '' );
	
	
}


function pp_create_events_page() {

    $page = get_page_by_path('events');

    if( ! $page ){
		$events_page = array(
		  'post_title'    => 'Events',
		  'post_name'     => 'events',
		  'post_status'   => 'publish',
		  'post_author'   => get_current_user_id(),
		  'post_type'     => 'page'
		);

		$post_id = wp_insert_post( $events_page, true );
    }

}


function pp_img_events_support() {

	$args = array(

		'mime_type' => 'image/jpeg',

		'methods' => array( 'rotate', 'resize',	'save')
	);

	$img_editor_test = wp_image_editor_supports( $args );

	if ( $img_editor_test !== false ) {
		add_option( 'events-img-support', '1', '', 'no' );
	}
	 else {
		$notice = __( 'Your server does not include the GD or Imagick extensions. Event Image Methods Not Supported. If you want Events images, please contact your hosting support.', 'bp-simple-events' );
		update_option('events-img-support-notice', $notice);
	}

}

function pp_activate_events_notice() {

	$notice = get_option( 'events-img-support-notice' );

	if( $notice ) {

		echo '<div class="update-nag"><p>' . $notice . '</p></div>';

		delete_option( 'events-img-support-notice' );
	}
}
add_action('admin_notices', 'pp_activate_events_notice');


function pp_create_post_type_event() {

	if ( ! defined( 'BP_VERSION' ) )
		return;

	register_post_type( 'event',
		array(
		  'labels' => array(
			'name' => __( 'Events' ),
			'singular_name' => __( 'Event' ),
			'add_new' => __( 'Add New' ),
			'add_new_item' => __( 'Add New Event' ),
			'edit' => __( 'Edit' ),
			'edit_item' => __( 'Edit Event' ),
			'new_item' => __( 'New Event' ),
			'view' => __( 'View Events' ),
			'view_item' => __( 'View Event' ),
			'search_items' => __( 'Search Events' ),
			'not_found' => __( 'No Events found' ),
			'not_found_in_trash' => __( 'No Events found in Trash' ),
            'bp_activity_admin_filter' => __( 'Events', 'bp-simple-events' ),
            'bp_activity_front_filter' => __( 'Events', 'bp-simple-events' ),
            'bp_activity_new_post'     => __( '%1$s created a new <a href="%2$s">Event</a>', 'bp-simple-events' ),
            'bp_activity_new_post_ms'  => __( '%1$s created a new <a href="%2$s">Event</a>, on the site %3$s', 'bp-simple-events' ),
			'bp_activity_comments_admin_filter' => __( 'Comments about Events', 'bp-simple-events' ), 
			'bp_activity_comments_front_filter' => __( 'Event Comments', 'bp-simple-events' ), 
			'bp_activity_new_comment'           => __( '%1$s commented on the <a href="%2$s">Event</a>', 'bp-simple-events' ),
			'bp_activity_new_comment_ms'        => __( '%1$s commented on the <a href="%2$s">Event</a>, on the site %3$s', 'bp-simple-events' )
			),
		'public' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'event' ),
		'capability_type' => array('event', 'events'),
		'exclude_from_search' => false,
		'has_archive' => true,
		'map_meta_cap' => true,
		'hierarchical' => false,
		"supports"	=> array("title", "editor", "thumbnail", "author", "comments", "trackbacks", "buddypress-activity"),
        'bp_activity' => array(
            'action_id'             => 'new_event',
            'contexts'              => array( 'activity', 'member', 'groups', 'member-groups' ),
            'comment_action_id'     => 'new_event_comment', // The activity type for comments
            'position'              => 70,
           ),
            // Note:  if you don't see 'Reply' links on post comments or SWA - make sure that wp-admin > Settings > Discussion > nested comments is checked and set to a high number
		'taxonomies' => array('category'),
		)
	);
	register_taxonomy_for_object_type('category', 'event');

}
add_action( 'init', 'pp_create_post_type_event' );


function pp_add_event_caps() {

	$role = get_role( 'administrator' );
	$role->add_cap( 'delete_published_events' );
	$role->add_cap( 'delete_others_events' );
	$role->add_cap( 'delete_events' );
	$role->add_cap( 'edit_others_events' );
	$role->add_cap( 'edit_published_events' );
	$role->add_cap( 'edit_events' );
	$role->add_cap( 'publish_events' );

}

function pp_remove_event_caps() {
	global $wp_roles;

	$all_roles = $wp_roles->roles;

	foreach( $all_roles as $key => $value ){

		$role = get_role( $key );

		$role->remove_cap( 'delete_published_events' );
		$role->remove_cap( 'delete_others_events' );
		$role->remove_cap( 'delete_events' );
		$role->remove_cap( 'edit_others_events' );
		$role->remove_cap( 'edit_published_events' );
		$role->remove_cap( 'edit_events' );
		$role->remove_cap( 'publish_events' );

	}
}


function pp_events_add_settings_link( $links ) {
	$link = array( '<a href="' . admin_url( 'options-general.php?page=bp-simple-events' ) . '">Settings</a>', );
	return array_merge( $links, $link );
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'pp_events_add_settings_link' );


function pp_events_plugin_updater() {

	if( !class_exists( 'PP_Events_Pro_Plugin_Updater' ) )
		include( dirname( __FILE__ ) . '/inc/admin/PP_Events_Pro_Plugin_Updater.php' );

	$license_key = trim( get_option( 'pp_events_license_key' ) );

	$edd_updater = new PP_Events_Pro_Plugin_Updater( PP_EVENTS_STORE_URL, __FILE__, array(
			'version' 	=> '2.4', 				
			'license' 	=> $license_key, 		
			'item_name' => PP_SIMPLE_EVENTS_PRO, 
			'author' 	=> 'PhiloPress'
		)
	);

}
add_action( 'admin_init', 'pp_events_plugin_updater', 0 );
