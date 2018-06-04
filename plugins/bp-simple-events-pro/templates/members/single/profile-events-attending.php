<?php

/**
 * Template for looping through all the upcoming Events that a member will be attending, on a member profile page
 * You can copy this file to your-theme/buddypress/members/single
 * and then edit the layout.
 */


global $wpdb; 
 
$attending_event_ids = array();
 
$event_attendance = $wpdb->get_results( "SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_key = 'event-attendees'" );

if ( $event_attendance ) {
	
	foreach ( $event_attendance as $attendance ) {
	
		$attendees = maybe_unserialize( $attendance->meta_value );
		
		if ( in_array( bp_displayed_user_id(), $attendees ) )
			$attending_event_ids[] = $attendance->post_id;

	}
	
}

?>

<?php if ( empty( $attending_event_ids ) ) : ?> 

	<div class="entry-content"><br/><?php _e( 'This member is not attending any upcoming Events.', 'bp-simple-events' ); ?></div>
	
<?php else : ?> 

	<?php
	
	$paged = ( isset( $_GET['ep'] ) ) ? $_GET['ep'] : 1;
	
	$args = array(
		'post_type'      => 'event',
		'order'          => 'ASC',
		'orderby'		 => 'meta_value_num',
		'meta_key'		 => 'event-unix',
		'paged'          => $paged,
		'posts_per_page' => 10,
		'post__in'       => $attending_event_ids,
	
		'meta_query' => array(
			array(
				'key'		=> 'event-unix',
				'value'		=> current_time( 'timestamp' ),
				'compare'	=> '>=',
				'type' 		=> 'NUMERIC',
			),
		),
	
	);
	
	$wp_query = new WP_Query( $args );
	
	
	?>
	
	<?php// _e( 'This member is attending these upcoming Events:', 'bp-simple-events' ); ?>
	
	<?php if ( $wp_query->have_posts() ) : ?>
	
		<div class="entry-content">
			<?php echo pp_events_profile_pagination( $wp_query ); ?>
		</div>
	
		<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); 	?>
	
			<div class="entry-content eventItemList">
				<h2 class="entry-title eventTitleList">
					<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
					<?php the_title(); ?></a>
				</h2>
	
	
				<?php //the_excerpt(); ?>
	
				<?php
				/*if ( has_post_thumbnail() ) {
					the_post_thumbnail( 'thumbnail' );
					echo '<br/>';
				}*/
				?>
	
				<?php
				$meta = get_post_meta($post->ID );
	
				if( ! empty( $meta['event-date'][0] ) )
					echo __( '', 'bp-simple-events' ) . '' . $meta['event-date'][0];
	
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
	
				<br/>
				<?php the_category(', ') ?>
	
	
			</div><!-- .entry-content -->
	
		<?php endwhile; ?>
	
		<div class="entry-content"><br/>
			<?php echo pp_events_profile_pagination( $wp_query ); ?>
		</div>
	
	
		<?php wp_reset_query(); ?>

	<?php else : ?>

	<div class="entry-content"><br/><?php __( 'This member is not attending any upcoming Events.', 'bp-simple-events' ); ?></div>

	<?php endif; ?>
	
<?php endif; ?>
