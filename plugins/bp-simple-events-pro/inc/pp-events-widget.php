<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class PP_Simple_Events_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array(
			'description' => __( 'BuddyPress Simple Events Widget', 'bp-simple-events' ),
			'classname' => 'widget_events_widget buddypress widget',
		);
		parent::__construct( false, $name = _x( "Events", 'widget name', 'bp-simple-events' ), $widget_ops );
	}

	function widget($args, $instance) {

		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		echo $before_title
		   . $title
		   . $after_title;

		$args = array(
			'post_type'      => 'event',
			'order'          => 'ASC',
			'orderby'		 => 'meta_value_num',
			'meta_key'		 => 'event-unix',
			'posts_per_page' => $instance['max_events'],
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

		<?php if ( $wp_query->have_posts() ) : ?>

			<ul>

			<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); 	?>

				<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>

			<?php endwhile; ?>

			</ul>

		<?php wp_reset_postdata(); ?>

		<?php else : ?>

			<div class="entry-content"><?php _e( 'There are no upcoming Events.', 'bp-simple-events' ); ?></div>

		<?php endif; ?>

		<?php echo $after_widget; ?>

	<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['max_events'] = strip_tags( $new_instance['max_events'] );

		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'title' => __( "Events", 'bp-simple-events' ),
			'max_events' => 5
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		$title = strip_tags( $instance['title'] );
		$max_events = strip_tags( $instance['max_events'] );
		?>

		<p><label for="bp-core-widget-title"><?php _e('Title:', 'bp-simple-events'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" style="width: 100%" /></label></p>

		<p><label for="bp-core-widget-events-max"><?php _e('Max Events to show:', 'bp-simple-events'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'max_events' ); ?>" name="<?php echo $this->get_field_name( 'max_events' ); ?>" type="text" value="<?php echo esc_attr( $max_events ); ?>" style="width: 30%" /></label></p>
	<?php
	}


} // class PP_Simple_Events_Widget


class PP_Simple_User_Events_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array(
			'description' => __( 'BuddyPress Simple Events Widget - Single User', 'bp-simple-events' ),
			'classname' => 'widget_events_widget buddypress widget',
		);
		parent::__construct( false, $name = _x( "Events - User", 'widget name', 'bp-simple-events' ), $widget_ops );
	}

	function widget($args, $instance) {
		global $wpdb; 
		 
		$attending_event_ids = array();
		 
		$event_attendance = $wpdb->get_results( "SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_key = 'event-attendees'" );
		
		if ( $event_attendance ) {
			
			foreach ( $event_attendance as $attendance ) {
			
				$attendees = maybe_unserialize( $attendance->meta_value );
				
				if ( in_array( bp_loggedin_user_id(), $attendees ) )
					$attending_event_ids[] = $attendance->post_id;
		
			}
			
		}	
	
		extract( $args );

		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		echo $before_title
		   . $title
		   . $after_title;

		if ( empty( $attending_event_ids ) ) {
			echo '<div class="entry-content">';
			_e( 'You are not attending any upcoming Events.', 'bp-simple-events' );
		    echo '</div>' . $after_widget;
		}
		else {	
		   
			$args = array(
				'post_type'      => 'event',
				'order'          => 'ASC',
				'orderby'		 => 'meta_value_num',
				'meta_key'		 => 'event-unix',
				'posts_per_page' => $instance['max_events'],
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
	
			<?php if ( $wp_query->have_posts() ) : ?>
	
				<ul>
	
				<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); 	?>
	
					<?php $event_date = get_post_meta( get_the_ID(), 'event-date', true ); ?>
					
					<li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><br/><?php echo $event_date; ?></li>
	
				<?php endwhile; ?>
	
				</ul>
	
			<?php wp_reset_postdata(); ?>
	
			<?php else : ?>
	
				<div class="entry-content"><?php _e( 'You are not attending any upcoming Events.', 'bp-simple-events' ); ?></div>
	
			<?php endif; ?>
	
			<?php echo $after_widget; ?>
	<?php
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['max_events'] = strip_tags( $new_instance['max_events'] );

		return $instance;
	}

	function form( $instance ) {
		$defaults = array(
			'title' => __( "User Events", 'bp-simple-events' ),
			'max_events' => 5
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		$title = strip_tags( $instance['title'] );
		$max_events = strip_tags( $instance['max_events'] );
		?>

		<p><label for="bp-core-widget-title"><?php _e('Title:', 'bp-simple-events'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" style="width: 100%" /></label></p>

		<p><label for="bp-core-widget-events-max"><?php _e('Max Events to show:', 'bp-simple-events'); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'max_events' ); ?>" name="<?php echo $this->get_field_name( 'max_events' ); ?>" type="text" value="<?php echo esc_attr( $max_events ); ?>" style="width: 30%" /></label></p>
	<?php
	}


} // class PP_Simple_User_Events_Widget


function pp_register_events_widgets() {
    register_widget( 'PP_Simple_Events_Widget' );
    register_widget( 'PP_Simple_User_Events_Widget' );
}
add_action( 'widgets_init', 'pp_register_events_widgets' );

