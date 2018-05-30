<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


// total events per member for Events profile tab
function pp_events_count_profile( $user_id = 0 ) {
	global $wpdb;

	if ( empty( $user_id ) )
		$user_id = bp_displayed_user_id();

	return $wpdb->get_var( "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_author = $user_id AND post_type = 'event' AND post_status = 'publish'" );

}


// pagination for Events loop page
function pp_events_pagination( $wp_query ) {

	$big = 999999999;

	$events_links = paginate_links( array(
		'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		'format' => '?paged=%#%',
		'current' => max( 1, get_query_var('paged') ),
		'total' => $wp_query->max_num_pages
	) );

	return apply_filters( 'pp_events_pagination', $events_links );
}

// pagination for profile Events loop page
function pp_events_profile_pagination( $wp_query ) {

	$events_profile_page_links = paginate_links( array(
		'base' => esc_url( add_query_arg( 'ep', '%#%' ) ),
		'format' => '',
		'total' => ceil( (int) $wp_query->found_posts / (int) get_query_var('posts_per_page') ),
		'current' => (int) get_query_var('paged'),
		//'prev_text' => '&larr;',
		//'next_text' => '&rarr;',
		//'mid_size' => 1
	) );

	return apply_filters( 'pp_events_profile_pagination', $events_profile_page_links );

}


// so event cpt is found on assigned cat archive page
function pp_event_query_post_type($query) {

	if( is_category() &&  $query->is_main_query() && empty( $query->query_vars['suppress_filters'] ) ) {
		$post_type = get_query_var('post_type');
		if($post_type)
			$post_type = $post_type;
		else
			$post_type = array( 'post', 'event', 'nav_menu_item');

		$query->set('post_type',$post_type);

		return $query;
	}

}
add_filter('pre_get_posts', 'pp_event_query_post_type');


// redirect when Event is trashed on front-end
function pp_event_trash_redirect(){
    if (is_404()){
        global $wp_query, $wpdb;
        $page_id = $wpdb->get_var( $wp_query->request );
        $post_status = get_post_status( $page_id );
        if($post_status == 'trash'){
            wp_redirect(site_url('/events/'), 301);
            die();
        }
    }
}
add_action('template_redirect', 'pp_event_trash_redirect');



// cleanup when Event is trashed
function pp_event_trash_cleanup( $postid ){

	BP_Activity_Activity::delete( array( 'secondary_item_id' => $postid ) );

	$user_id = get_post_field( 'post_author', $postid );
	$item_id = $postid;
	$component_name = 'events';
	$component_action = 'event_attender';
	
	bp_notifications_delete_notifications_by_item_id( $user_id, $item_id, $component_name, $component_action );

}
add_action( 'trash_event', 'pp_event_trash_cleanup' );



// turn Event > Url to a link
function pp_event_convert_url( $text, $scheme = 'http://' ) {

	$url = parse_url( $text, PHP_URL_SCHEME) === null ? $scheme . $text : $text;

	$disallowed = array('http://', 'https://');
	foreach( $disallowed as $d ) {
		if( strpos( $text, $d ) === 0 )
			$text = str_replace( $d, '', $text );
	}

	return apply_filters( 'pp_event_convert_url', '<a href="' . $url . '" rel="nofollow">' . $text . '</a>', $text );
}

// filter activity action string for Event
function pp_filter_activity_event_action( $action, $activity ) {

	if ( $activity->type == 'new_event' ) {
	
		$add_title = 'Event&nbsp;-&nbsp;' . get_the_title( $activity->secondary_item_id ) . '</a>';
		
		$action = str_replace( 'Event</a>', $add_title, $action);

	}
	
	return $action;
}
add_filter( 'bp_activity_custom_post_type_post_action', 'pp_filter_activity_event_action', 1, 2 );


// Event search - gather event IDs - called in templates/events-loop.php
function pp_events_search_ids() {

	$search_event_ids = array();
	
	if( isset( $_POST['events-searching'] ) && $_POST['events-searching'] == '1' ){
		
		global $wpdb;
	
		$event_ids = array();
		$found = array();
	
		foreach( $_POST as $key => $value ) {
	
			if( !empty( $value ) ) {  
			
				$sql = '';	
		
				switch ( $key ) {
		
					case 'event-search-text':
	
						$event_text = trim( $_POST['event-search-text'] );
		
						$event_text_pieces = explode(" ", $event_text);
						
						$event_text_terms = array();
		
						foreach ( $event_text_pieces as $event_text_piece ) {
		
							$event_text_piece = esc_sql( $wpdb->esc_like( $event_text_piece ) );  
		
					        $event_title_terms[] = "post_title LIKE '%$event_text_piece%'";
					        $event_content_terms[] = "post_content LIKE '%$event_text_piece%'";					        
					    }
	
						$sql = "SELECT ID FROM $wpdb->posts WHERE ( " . implode(' OR ', $event_title_terms) . ") OR ( " . implode(' OR ', $event_content_terms) . ") AND post_type = 'event' AND post_status = 'publish' ";	

					    break;	
	
	
					case 'event-search-date':
					
						$event_start = strtotime( $_POST['event-search-date'] );
						$event_end = strtotime( $_POST['event-search-date-end'] );
	
						$sql = "
							SELECT $wpdb->posts.ID FROM $wpdb->posts 
							INNER JOIN $wpdb->postmeta ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id ) 
							INNER JOIN $wpdb->postmeta AS mt1 ON ( $wpdb->posts.ID = mt1.post_id ) WHERE 1=1 
							AND ( $wpdb->postmeta.meta_key = 'event-unix' AND ( ( mt1.meta_key = 'event-unix' AND CAST(mt1.meta_value AS SIGNED) >= $event_start ) ) ) 
							AND ( $wpdb->postmeta.meta_key = 'event-unix' AND ( ( mt1.meta_key = 'event-unix' AND CAST(mt1.meta_value AS SIGNED) <= $event_end ) ) ) 
							AND $wpdb->posts.post_type = 'event'
							AND $wpdb->posts.post_status = 'publish'
						";			
		
					    break;						
						
	
					case 'event-search-location':
					
						$event_location = trim( $_POST['event-search-location'] );
		
						$event_location_pieces = explode(" ", $event_location);
						
						$event_location_terms = array();
		
						foreach ( $event_location_pieces as $event_location_piece ) {
		
							$event_location_piece = esc_sql( $wpdb->esc_like( $event_location_piece ) );  
		
					        $event_location_terms[] = "mt1.meta_value LIKE '%$event_location_piece%'";
					    }
	
						$sql = "
							SELECT $wpdb->posts.ID FROM $wpdb->posts 
							INNER JOIN $wpdb->postmeta ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id ) 
							INNER JOIN $wpdb->postmeta AS mt1 ON ( $wpdb->posts.ID = mt1.post_id ) WHERE 1=1 
							AND ( $wpdb->postmeta.meta_key = 'event-address' AND  ( mt1.meta_key = 'event-address' AND ( " . implode(' OR ', $event_location_terms) . ")  ) ) 
							AND $wpdb->posts.post_type = 'event'
							AND $wpdb->posts.post_status = 'publish'
						";					
		
					    break;						
						
						
					case 'event-search-categories':
	
						$event_cats = $_POST['event-search-categories'];
		
						$event_cats_terms = array();
						
						foreach ( $event_cats as $event_cats_piece ) {
							
							$event_cats_piece = esc_sql( $wpdb->esc_like( $event_cats_piece ) );  
		
					        $event_cats_terms[] = "mt1.term_taxonomy_id = $event_cats_piece";
					    }
					    
						$sql = "
							SELECT $wpdb->posts.ID FROM $wpdb->posts 
							INNER JOIN $wpdb->term_relationships ON ( $wpdb->posts.ID = $wpdb->term_relationships.object_id ) 
							INNER JOIN $wpdb->term_relationships AS mt1 ON ( $wpdb->posts.ID = mt1.object_id ) WHERE 1=1 
							AND ( " . implode(' OR ', $event_cats_terms) . ")  
							AND $wpdb->posts.post_type = 'event'
							AND $wpdb->posts.post_status = 'publish'
						";
		
					    break;						
							
				}
	
	
				if( $sql != '' ) {
					
					$found = $wpdb->get_col ($sql);     //var_dump( $found );
					
					if ( empty( $event_ids ) )
						$event_ids = $found;
					else
						$event_ids = array_intersect( $event_ids, $found );
					
				}
			}
		}
		
		$search_event_ids = array_unique( $event_ids );
		$search_event_ids = array_values( $search_event_ids );

	}	
	
	return $search_event_ids;
}


function pp_events_load_dot() {
	return plugin_dir_url(__FILE__) . '/icons/red-dot.png';
}

function pp_events_load_cluster_icons() {
	return plugin_dir_url(__FILE__) . '/icons/m';
}


//  protect the custom meta boxes so that they do not appear in custom-fields support
function pp_events_hide_meta_fields( $protected, $meta_key ) {
	
	$event_meta_keys = array( 'event-attend-button', 'event-attend-notify', 'event-attendees', 'event-attendees-list',  'event-attendees-list-avatars', 'event-attendees-list-public', 'event-date', 'event-date-end',  'event-groups', 'event-latlng', 'event-start', 'event-stop', 'event-time', 'event-unix', 'event-url', 'event-address' );
	
	if ( in_array( $meta_key, $event_meta_keys ) ) 
		return true;
	
	return $protected;
	
}
add_filter( 'is_protected_meta', 'pp_events_hide_meta_fields', 10, 2 );