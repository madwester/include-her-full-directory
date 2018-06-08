<?php

 /**
 * Template for displaying a single Event
 * You can copy this file to your-theme
 * and then edit the layout.
 */

do_action( 'pp_single_event_notification' );

do_action( 'pp_single_event_attending' );

$gapikey = get_site_option( 'pp_gapikey' );
					
if ( $gapikey != false ) {
		
	wp_register_script( 'google-maps-api', '//maps.googleapis.com/maps/api/js?key=' . $gapikey );
		
}

function pp_single_map_css() {
	echo '<style type="text/css"> .pp_map_canvas img { max-width: none; } </style>';
}
add_action( 'wp_head', 'pp_single_map_css' );

get_header();

?>

<div id="primary" class="content-area">

	<div id="content" class="site-content singleEventContent" role="main">
		<div id="buddypress">
		<?php while ( have_posts() ) : the_post(); ?>

			<div class="entry-content container eventSingleContainer">
				
				<h2 class="entry-title eventTitle">
					<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
					<?php the_title(); ?></a>
				</h2>
				<div class="row imageRowSingleEvent">
					<?php
					if ( has_post_thumbnail() ) {
						echo '<br/>';
						the_post_thumbnail();
						echo '<br/>';
					}
					?>
				</div>
				<?php
					$author_id = get_the_author_meta('ID');
					$author_name = get_the_author_meta('display_name');
					$user_link = bp_core_get_user_domain( $author_id );
					if( get_current_user_id() == $author_id )
						$is_author = true;
					else
						$is_author = false;

					$meta = get_post_meta($post->ID );      //var_dump( $meta );
					?>
				<div class="row">
					<div class="col-md-6 eventSingleItem">
					<?php

					if( ! empty( $meta['event-date'][0] ) )
						echo __( 'Start', 'bp-simple-events' ) . ':&nbsp;' . $meta['event-date'][0];

					if( ! empty( $meta['event-date-end'][0] ) )
						echo '<br/>' . __( 'End', 'bp-simple-events' ) . ':&nbsp;' . $meta['event-date-end'][0];

					if( ! empty( $meta['event-address'][0] ) )
						echo '<br/>' . __( 'Location', 'bp-simple-events' ) . ':&nbsp;' . $meta['event-address'][0];

					if( ! empty( $meta['event-url'][0] ) )
						echo '<br/>' . __( 'Url', 'bp-simple-events' ) . ':&nbsp;' . pp_event_convert_url( $meta['event-url'][0] );

					if( ! empty( $meta['event-groups'] ) ) {

						$groups_str = '';

						foreach( $meta['event-groups'] as $group_id ) {

							$group = groups_get_group( array( 'group_id' => $group_id, 'populate_extras'   => true ) );

							if( $group->status != 'public' && $group->is_member != 1 )
								continue;

							else
								$groups_str .= '<a href="' . bp_get_group_permalink( $group ) . '">' . $group->name  . '</a>, ';

						}

						if( !empty( $groups_str ) ) {

							$groups_str = substr( $groups_str, 0, -2 );
							echo '<br/>' . __( 'Groups', 'bp-simple-events' ) . ':&nbsp;' . $groups_str;

						}
					}
					?>
					<br>
					Category: <?php the_category(', ') ?>
					<br>
					<a href="<?php echo bp_core_get_user_domain( $author_id ); ?>">
						<?php //echo bp_core_fetch_avatar( array( 'item_id' => $author_id, 'type' => 'thumb' ) ); ?>
						Organizer: <?php echo $author_name; ?></a>
						</div>
					<div class="col-md-6 eventSingleItem">
						<?php the_content(); ?>
					</div>
				</div>
				<div class="row attendingRow">
				<div class="col-md-12">
				<?php
				// Show the list of attendees if 'show list of attendees' was selected when creating or editing Event
				$show_list = false;
				if( ! empty( $meta['event-attendees-list'][0] ) ) 
					$show_list = true;
						

				if( $is_author || is_super_admin() || $show_list ) {

					if( ! empty( $meta['event-attendees'][0] ) ) {

						$attendees = $meta['event-attendees'][0];  

						$attendees = maybe_unserialize( $attendees );

						if( ! empty( $attendees ) ) {

							echo '<br/>' . __( 'Attendees:', 'bp-simple-events' );
							
							$attendee_list_names = array();

							foreach( $attendees as $attendee ) {

								if( ! empty( $meta['event-attendees-list-avatars'][0] ) ) {
								?>
									&nbsp;<a href="<?php echo bp_core_get_user_domain( $attendee ); ?>" title="<?php echo bp_core_get_user_displayname( $attendee );?>"><?php echo bp_core_fetch_avatar( array( 'item_id' => $attendee ) ); ?></a>
								<?php
								}
								else {

									$attendee_list_names[] = '&nbsp;<a href="' . bp_core_get_user_domain( $attendee ) . '">' .bp_core_get_user_displayname( $attendee ) . '</a>';

								}
							}
							
							if( ! empty( $attendee_list_names ) ) 
								echo implode( ', ', $attendee_list_names );
							
							echo '<br/>';
						}
					}

				}
				?>
				</div>
				<div class="col-md-12">
					<?php
					// show the 'I want to attend' button if not viewing an Event you created
					//if( ! $is_author ) {
					
						// if 'add button' was selected when creating or editing Event
						if( ! empty( $meta['event-attend-button'][0] ) ) {
		
							if( $meta['event-attend-button'][0] == '1' ) {
								echo '<br/>';
								pp_event_attending_button( $post->ID, $author_id );
								echo '<br/>';
							}
						}
					//}
					?>
				</div>
				</div>
				<?php //if( ! empty( $meta['event-latlng'][0] ) ) : ?>
					<br/>
					<?php /*
					wp_print_scripts( 'google-maps-api' );
					$map_id = uniqid( 'pp_map_' ); 
					$settings_single = get_site_option( 'pp-events-map-single-settings' ); 
					extract($settings_single);
					?>
					
					<div class="pp_map_canvas" id="<?php echo esc_attr( $map_id ); ?>" style="height: <?php echo $map_height; ?>px; width: 100%;"></div>
					
				    <script type="text/javascript">
						var map_<?php echo $map_id; ?>;
						function pp_run_map_<?php echo $map_id ; ?>(){
							var location = new google.maps.LatLng(<?php echo $meta['event-latlng'][0]; ?>);
							var map_options = {
								zoom: <?php echo $map_zoom_level; ?>,
								center: location,
								mapTypeId: google.maps.MapTypeId.ROADMAP
							}
							map_<?php echo $map_id ; ?> = new google.maps.Map(document.getElementById("<?php echo $map_id ; ?>"), map_options);
							var marker = new google.maps.Marker({
							position: location,
							map: map_<?php echo $map_id ; ?>
							});
							
						}
						pp_run_map_<?php echo $map_id ; ?>();
					</script>
				<?php endif; */?>
			</div>
			<!--<div class="entry-content">
				<nav class="nav-single">
					<span class="nav-previous"><?php //previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'bp-simple_events' ) . '</span> %title' ); ?></span>
					&nbsp; &nbsp;
					<span class="nav-next"><?php //next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'bp-simple_events' ) . '</span>' ); ?></span>
				</nav>
			</div>-->
			<?php //comments_template( '', true ); ?>

		<?php endwhile; ?>
		<div class="row editDeleteRow">
					
					<?php
					if( $is_author || is_super_admin() ) :

						$edit_link = wp_nonce_url( $user_link . 'events/create?eid=' . $post->ID, 'editing', 'edn');

						$delLink = get_delete_post_link( $post->ID );

					?>

						<span class="edit"><a href="<?php echo $edit_link; ?>" title="Edit  Event">Edit</a></span>
						&nbsp; &nbsp;
						<span class="trash"><a onclick="return confirm('Are you sure you want to delete this Event?')" href="<?php echo $delLink; ?>" title="Delete Event" class="submit">Delete</a></span>

						<?php echo '<br/>'; ?>

					<?php endif; ?>
				</div>
		</div><!-- buddypress -->
	</div><!-- #content -->
</div><!-- #primary -->

<?php get_footer(); ?>
