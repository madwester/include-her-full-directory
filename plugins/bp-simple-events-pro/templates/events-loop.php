<?php

/**
 * Template for displaying the Events Loop
 * You can copy this file to your-theme
 * and then edit the layout.
 */

get_header();

	
$search_event_ids = pp_events_search_ids(); 

$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

if ( empty( $search_event_ids ) &&  empty( $_POST['events-searching'] ) ) {

	$args = array(
		'post_type'      => 'event',
		'order'          => 'ASC',
		'orderby'		 => 'meta_value_num',
		'meta_key'		 => 'event-unix',
		'paged'          => $paged,
		'posts_per_page' => 10,
	
		'meta_query' => array(
			array(
				'key'		=> 'event-unix',
				'value'		=> current_time( 'timestamp' ),
				'compare'	=> '>=',
				'type' 		=> 'NUMERIC',
			),
		),
	
	);
}
elseif ( ! empty( $search_event_ids ) ) {
		
		$args = array(
			'post_type'      => 'event',
			'post__in'       => $search_event_ids,
			'order'          => 'ASC',
			'orderby'		 => 'meta_value_num',
			'meta_key'		 => 'event-unix',
			'paged'          => $paged,
			'posts_per_page' => -1,
		
			'meta_query' => array(
				array(
					'key'		=> 'event-unix',
					'value'		=> current_time( 'timestamp' ),
					'compare'	=> '>=',
					'type' 		=> 'NUMERIC',
				),
			),
		
		);	
}
else {
	
	$args = array(	'post_type'      => 'jamtart6goo-oxpi' );
	
}

$wp_query = new WP_Query( $args );
 
?>
<div class="feature featureEvents">
<div class="featureCaption">
        <h1>Did you know all our members can create own events?</h1>
		<button type="button" class="btn featureBtn blueBtn">Create an event</button>
    </div>
</div>
<div id="primary" class="content-area">
	<div id="content" class="site-content" role="main">
		<div id='buddypress' class="containerEvents">
		<!-- Events Search -->
		<?php include("eventsMap.php");?>
		<div class="container">
		<div class="entry-content">
			<button class="defaultBtn pinkBtn full">Create an event <i class="fas fa-angle-double-right"></i></button>
			<!--<a href="?e=emap"><button class="defaultBtn">Events Map</button></a>-->
			<input id="pp-events-search-toggle" type="submit" value="<?php  _e( ' Events Search ', 'bp-simple-events' ); ?>">
			
			<!--<a class="generic button" href="?e=emap">Events Map</a>-->
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					
				
					$('#pp-events-search').hide();
					$('#pp-events-search-toggle').click(function(){
						$('#pp-events-search').toggle();
					});
					
					$('#event-search-date').datetimepicker({
						controlType: 'select',
						oneLine: true,
						timeFormat: 'h:mm tt',
						dateFormat: 'DD, MM d, yy'
					});

					$('#event-search-date-end').datetimepicker({
						controlType: 'select',
						oneLine: true,
						timeFormat: 'h:mm tt',
						dateFormat: 'DD, MM d, yy'		
					});
				});
			</script>
		  
			<div id="pp-events-search" style="display:none">
		
				<form action="" method="POST" id="pp_events_search_form" class="standard-form searchForm">
				
					<div>
						<!--<label><?php //echo __( 'Title / Description', 'bp-simple-events' ); ?></label>-->
						<input type="text" class="searchEventInput" name="event-search-text" id="event-search-text" placeholder="<?php echo __( 'Enter keywords..', 'bp-simple-events' ); ?>" value="">
						<input type="hidden" name="event-search-text-2" value="1">
					</div>				
				
					<div>
						<!--<label><?php //echo __( 'Date', 'bp-simple-events' ); ?></label>-->
						<input type="text" class="searchEventInput" id="event-search-date" name="event-search-date" placeholder="<?php echo __( 'Enter Start date ..', 'bp-simple-events' ); ?>" value="" />
						<input type="text" class="searchEventInput" id="event-search-date-end" name="event-search-date-end" placeholder="<?php echo __( 'Enter End date ..', 'bp-simple-events' ); ?>" value="" />
					</div>			
				
					<div>
						<!--<label><?php //echo __( 'Location', 'bp-simple-events' ); ?></label>-->
						<input type="text" class="searchEventInput" name="event-search-location" id="event-search-location" placeholder="<?php echo __( 'Enter City, State or Post Code..', 'bp-simple-events' ); ?>" value="">
					</div>
					
					<div>
						<!--<label><?php //echo __( 'Categories', 'bp-simple-events' ); ?></label>-->
						<select name="event-search-categories[]" id="event-search-categories">
							<option value="0"/><?php echo __( 'Select...', 'bp-simple-events' ); ?></option>;
							<?php
								$args = array(
									'type'                     => 'post',
									'child_of'                 => 0, 
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
						
									<?php
										foreach( $categories as $category ) 
											echo '&nbsp;&nbsp;<option value="' . $category->term_id . '"' . '/> ' . $category->name . '</option>';
									?>
								</p>
						
							<?php endif; ?>
						</select>
					</div>			
					
					<div class="submit">
						<input type="submit" class="searchEventBtn" value="<?php echo __( ' Search Events ', 'bp-simple-events' ); ?>">
					</div>
					
					<input type="hidden" name="events-searching" value="1">
					
				</form>
			</div>
		</div>
		<!-- Events Search - End -->	
	
		<?php if ( empty( $search_event_ids ) && ( isset( $_POST['events-searching'] ) && $_POST['events-searching'] == '1' ) ) : ?>
			
			<div class="entry-content"><br/>
				<?php _e( 'No Events were found.', 'bp-simple-events' ); ?>
			</div>
		
		<?php endif; ?>
		
		<?php if ( ! empty( $search_event_ids ) ) : ?>
			
			<div class="entry-content"><br/>
				<?php 
				if ( $wp_query->found_posts == 1 )
					_e('1 Event was found.', 'bp-simple-events'); 
				else
					printf( __('%s Events were found.', 'bp-simple-events'), $wp_query->found_posts ); 
				?>
			</div>
		
		<?php endif; ?>		
	
		<?php if ( $wp_query->have_posts() ) : ?>

			<div class="entry-content"><br/>
				<?php echo pp_events_pagination( $wp_query ); ?>
			</div>

			<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>

				<br/>
				<div class="entry-content rowItemEvent">
				<div class="imageItemEvent">
				<?php
					if ( has_post_thumbnail() ) {
						the_post_thumbnail( 'thumbnail' );
						echo '<br/>';
					}
					?>
				</div>
				<div class="descriptionItemEvent">
				<h2 class="entry-title">
						<?php// the_category(', ') ?>
						<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
						<?php the_title(); ?></a>
					</h2>
					<h6 class="subTitleEvent">
						<?php
						$author_id = get_the_author_meta('ID');
						$author_name = get_the_author_meta('display_name');
						?>

						<a href="<?php echo bp_core_get_user_domain( $author_id ); ?>">
						<?php ( array( 'item_id' => $author_id ) ); ?>
						Organizer: <?php echo $author_name; ?></a>
					</h6>
					<h6 class="subTitleEvent">		
					<?php
					$meta = get_post_meta($post->ID );
					if( ! empty( $meta['event-date'][0] ) )
						echo __( '', 'bp-simple-events' ) . '' . $meta['event-date'][0];
					?>
					</h6>
					<h6 class="subTitleEvent">
					<?php
					$meta = get_post_meta($post->ID );

					if( ! empty( $meta['event-time'][0] ) )
						echo '' . __( '', 'bp-simple-events' ) . '' . $meta['event-time'][0];

					if( ! empty( $meta['event-address'][0] ) )
						echo '' . __( '', 'bp-simple-events' ) . '' . $meta['event-address'][0];

					if( ! empty( $meta['event-url'][0] ) )
						echo '' . __( 'Url', 'bp-simple-events' ) . ':&nbsp;' . pp_event_convert_url( $meta['event-url'][0] );

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
					</h6>
				</div>					
					
					

					<?php //the_excerpt(); ?>

					

				</div><!-- .entry-content -->

		<?php endwhile; ?>

		<div class="entry-content"><br/>
			<?php echo pp_events_pagination( $wp_query ); ?>
		</div>

		<?php else : ?>

			<div class="entry-content"><br/><?php __( 'There are no upcoming Events.', 'bp-simple-events' ); ?></div>

		<?php endif; ?>


		<?php wp_reset_postdata(); ?>
		</div>
		

		</div><!-- buddypress -->
	</div><!-- #content -->
</div><!-- #primary -->
<?php get_footer(); ?>
