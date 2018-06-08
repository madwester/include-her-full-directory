<?php

/**
 * Template for displaying the Events Map
 * You can copy this file to your-theme
 * and then edit the layout.
 */

$args = array(
	'post_type'      => 'event',
	'posts_per_page' => -1,
	'meta_query' => array(
		array(
			'key'		=> 'event-unix',
			'value'		=> current_time( 'timestamp' ),
			'compare'	=> '>=',
			'type' 		=> 'NUMERIC',
		),
        array(
            'key'     => 'event-latlng'
        ),
	),
);

$events_query = new WP_Query( $args );

$geo_locations = array();
$geo_names = array();
$geo_content = array();


if ( $events_query->have_posts() ) {

	while ( $events_query->have_posts() ) {
	
		$events_query->the_post();

		$meta = get_post_meta( get_the_ID() ); 	//var_dump( $meta );

		$title = '<a href="' . get_the_permalink() . '" target="maptab">' . get_the_title() . '</a>';

		$geo_locations[] = explode(",", $meta['event-latlng'][0]);
		$geo_names[] = get_the_title();
		$geo_content[] =  '<div>' . $title . '<p>' . $meta['event-date'][0] . '</div>';

	}

}

wp_reset_postdata();

?>
			<?php do_action( 'bp_before_events_map' ); ?>
				<div class="entry-content">
					<?php if ( $events_query->post_count > 0 ) : ?>

						<?php

						do_action( 'pp_events_page_map_scripts' );
						
						$settings_all = get_site_option( 'pp-events-map-all-settings' ); 
						extract($settings_all);

						$map_id = uniqid( 'events_' );

						?>

						<div id="<?php echo esc_attr( $map_id ); ?>" style="height: <?php echo $map_height_all; ?>px; width: 100%;"></div>

					    <script type="text/javascript">

							var map_<?php echo $map_id; ?>;

							var latLongMap = new Object();
							var styledMapType = new google.maps.StyledMapType(

//YH
//Fade with yellow    
//shift worker    
[
    {
        "stylers": [
            {
                "saturation": -100
            },
            {
                "gamma": 1
            }
        ]
    },
    {
        "elementType": "labels.text.stroke",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "poi.business",
        "elementType": "labels.text",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "poi.business",
        "elementType": "labels.icon",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "poi.place_of_worship",
        "elementType": "labels.text",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "poi.place_of_worship",
        "elementType": "labels.icon",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "road",
        "elementType": "geometry",
        "stylers": [
            {
                "visibility": "simplified"
            }
        ]
    },
    {
        "featureType": "water",
        "stylers": [
            {
                "visibility": "on"
            },
            {
                "saturation": 50
            },
            {
                "gamma": 0
            },
            {
                "hue": "#50a5d1"
            }
        ]
    },
    {
        "featureType": "administrative.neighborhood",
        "elementType": "labels.text.fill",
        "stylers": [
            {
                "color": "#333333"
            }
        ]
    },
    {
        "featureType": "road.local",
        "elementType": "labels.text",
        "stylers": [
            {
                "weight": 0.5
            },
            {
                "color": "#333333"
            }
        ]
    },
    {
        "featureType": "transit.station",
        "elementType": "labels.icon",
        "stylers": [
            {
                "gamma": 1
            },
            {
                "saturation": 50
            }
        ]
    }
],
            {name: 'Styled Map'});

							function readLatLongMap( key ) {
								return latLongMap[key];
							}

							function jiggleMarkers( locations ) {

								var currentLat;
								var currentLong;

								for ( var i = 0; i < locations.length; i++) {

									currentLat = +(locations[i][0]);
									currentLong = +(locations[i][1]);
									if( Math.abs(readLatLongMap( currentLat ) - currentLong) < .0005 ) {
										var longChange = +(2*( Math.random() - 0.5) * .002);
										var latChange = +(2*( Math.random() - 0.5) * .001);
										latLongMap[ (currentLat + latChange) ] = currentLong + longChange;
										locations[i][0] = currentLat + latChange;
										locations[i][1] = currentLong + longChange;

									} else {
										latLongMap[ currentLat ] = currentLong;
									}
								}
							}

							function pp_run_map_<?php echo $map_id ; ?>(){

								var locations = <?php echo json_encode( $geo_locations ); ?>;
								var titles = <?php echo json_encode( $geo_names ); ?>;
								var markers_content = <?php echo json_encode( $geo_content ); ?>;
								var center = new google.maps.LatLng(<?php echo $event_latlng ?>);
								var infoWindow = new google.maps.InfoWindow( { maxWidth: 200 });

								jiggleMarkers( locations );

								var map_options = {
									zoom: <?php echo $map_zoom_level_all; ?>,
									center:  center,
									mapTypeId: google.maps.MapTypeId.ROADMAP
								}
								map_<?php echo $map_id ; ?> = new google.maps.Map(document.getElementById("<?php echo $map_id ; ?>"), map_options);

								var markers = [];
								for(var i=0;i<locations.length;i++){
									var data = markers_content[i];
									var lat = locations[i][0];
									var lng = locations[i][1];
									var location = new google.maps.LatLng(lat,lng);
									var icon = "<?php echo pp_events_load_dot(); ?>";
									var marker = new google.maps.Marker({
										position: location,
										title: decode_title( titles[i] ),
										map: map_<?php echo $map_id ; ?>,
										icon:  new google.maps.MarkerImage(icon)
									});

						            (function (marker, data) {
						                google.maps.event.addListener(marker, "click", function (e) {
						                    infoWindow.setContent(data);
						                    infoWindow.open(map_<?php echo $map_id ; ?>, marker);
						                });
						            })(marker, data);

									markers.push(marker);
								}
								
								var markerCluster = new MarkerClusterer(map_<?php echo $map_id; ?>, markers, { 
								    imagePath: '<?php echo pp_events_load_cluster_icons(); ?>' 
								});

							}

							function decode_title(txt){
								var sp = document.createElement('span');
								sp.innerHTML = txt;
								return sp.innerHTML;
							}


							google.maps.event.addDomListener(window, "resize", function() {
								var map = map_<?php echo $map_id; ?>;
								var center = map.getCenter();
								google.maps.event.trigger(map, "resize");
								map.setCenter(center);
								//Associate the styled map with the MapTypeId and set it to display.
								map.mapTypes.set('styled_map', styledMapType);
								map.setMapTypeId('styled_map');
							});


							pp_run_map_<?php echo $map_id ; ?>();

						</script>

					<?php else : ?>
						<?php _e( 'No Events with valid locations were found.', 'bp-simple-events' ); ?>
						
					<?php endif; ?>
					
					<br/>&nbsp;<br/>
				</div>
