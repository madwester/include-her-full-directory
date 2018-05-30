<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Settings Page class
 */


class PP_Simple_Events_Admin_Settings {

	private $roles_message = '';
	private $settings_message = '';
	private $license_message = '';
	
    public function __construct() {

		add_action( 'admin_enqueue_scripts',  array( $this, 'maps_scripts' ), 1000 );

		add_action( 'admin_init', array( $this, 'pp_events_save_license') );
		add_action( 'admin_init', array( $this, 'pp_events_activate_license') );
		add_action( 'admin_init', array( $this, 'pp_events_deactivate_license') );		
		
		if ( is_multisite() ) {

			if ( ! function_exists( 'is_plugin_active_for_network' ) )
			    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

        }

        if ( is_multisite() && is_plugin_active_for_network( 'bp-simple-events-pro/loader.php' ) )
			add_action('network_admin_menu', array( $this, 'multisite_admin_menu' ) );
		else
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );

	}


	function admin_menu() {
		add_options_page(  __( 'BP Simple Events', 'bp-simple-events'), __( 'BP Simple Events', 'bp-simple-events' ), 'manage_options', 'bp-simple-events', array( $this, 'settings_admin_screen' ) );
	}

	function multisite_admin_menu() {
		add_submenu_page( 'settings.php', __( 'BP Simple Events', 'bp-simple-events'), __( 'BP Simple Events', 'bp-simple-events' ), 'manage_options', 'bp-simple-events', array( $this, 'settings_admin_screen' ) );
	}

	// add scripts
	function maps_scripts( $hook ) {

        if ( 'settings_page_bp-simple-events' != $hook )
           return;

		$gapikey = get_site_option( 'pp_gapikey' );
						
		if ( $gapikey != false ) {
	
			wp_register_script( 'google-places-api', '//maps.googleapis.com/maps/api/js?key=' . $gapikey . '&libraries=places' );
			wp_print_scripts( 'google-places-api' );
			
		}	   

		?>
		<script type = 'text/javascript'>
			function initialize() {
				var input = document.getElementById('event-location');
				var autocomplete = new google.maps.places.Autocomplete(input);
				google.maps.event.addListener(autocomplete, 'place_changed', function () {
					var place = autocomplete.getPlace();
					//console.log(place);
					var lat = place.geometry.location.lat();
					var lng = place.geometry.location.lng();
					var latlng = lat + ',' + lng;
					//document.getElementById('event-place').value = JSON.stringify(place);
					if( place.formatted_address.indexOf( place.name ) > -1 )
						document.getElementById('event-address').value = place.formatted_address;
					else
						document.getElementById('event-address').value = place.name + ', ' + place.formatted_address;
					document.getElementById('event-latlng').value = latlng;
				});
			}
			google.maps.event.addDomListener(window, 'load', initialize);
		</script>
		<?php
	}


	function pp_events_save_license() {

		if ( ! empty( $_POST["pp-events-lic-save"] ) ) {

		 	if( ! check_admin_referer( 'pp_events_lic_save_nonce', 'pp_events_lic_save_nonce' ) )
				return;

			$old = get_option( 'pp_events_license_key' );
			$new = trim( $_POST["pp_events_license_key"] );

			if( $old && $old !=  $new ) {
				delete_option( 'pp_events_license_status' ); // new license has been entered, so must reactivate
			}

			update_option( 'pp_events_license_key', $new );
			
			$this->license_message .=
					"<div class='updated below-h2'>" .  __('License Key has been saved.', 'bp-simple-events') . "</div>";

		}
	}



	function pp_events_activate_license() {

		if( isset( $_POST['pp_events_license_activate'] ) ) {

		 	if( ! check_admin_referer( 'pp_events_lic_nonce', 'pp_events_lic_nonce' ) )
				return;

			$license = trim( get_option( 'pp_events_license_key' ) );

			$api_params = array(
				'edd_action'=> 'activate_license',
				'license' 	=> $license,
				'item_name' => urlencode( PP_SIMPLE_EVENTS_PRO ), 
				'url'       => home_url()
			);

			$response = wp_remote_post( PP_EVENTS_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

			if ( is_wp_error( $response ) ) {
				//var_dump( $response );
				return false;
			}

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			// $license_data->license will be either "valid" or "invalid"

			update_option( 'pp_events_license_status', $license_data->license );
			
			$this->license_message .=
					"<div class='updated below-h2'>" .  __('License has been activated.', 'bp-simple-events') . "</div>";

		}
	}


	function pp_events_deactivate_license() {

		if( isset( $_POST['pp_events_license_deactivate'] ) ) {

		 	if( ! check_admin_referer( 'pp_events_lic_nonce', 'pp_events_lic_nonce' ) )
				return;

			$license = trim( get_option( 'pp_events_license_key' ) );

			$api_params = array(
				'edd_action'=> 'deactivate_license',
				'license' 	=> $license,
				'item_name' => urlencode( PP_SIMPLE_EVENTS_PRO ), // the name of our product in EDD
				'url'       => home_url()
			);

			$response = wp_remote_post( PP_EVENTS_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

			if ( is_wp_error( $response ) )
				return false;

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			// $license_data->license will be either "deactivated" or "failed"
			if( $license_data->license == 'deactivated' ) {
				delete_option( 'pp_events_license_status' );
				$this->license_message .=
					"<div class='updated below-h2'>" .  __('License has been deactivated.', 'bp-simple-events') . "</div>";
			}
			else
				$this->license_message .=
					"<div class='error below-h2'>" .  __('License has NOT been deactivated.', 'bp-simple-events') . "</div>";
		}
	}	
	
	
	function settings_admin_screen(){
		global $wp_roles;

		if( !is_super_admin() )
			return;

		$this->roles_update();
		$this->settings_update();

		$all_roles = $wp_roles->roles;
		
		$settings_single = get_site_option( 'pp-events-map-single-settings' ); 
		extract($settings_single);

		$settings_all = get_site_option( 'pp-events-map-all-settings' ); 
		extract($settings_all);
		
		$gapikey = get_site_option( 'pp_gapikey' );
		if ( ! $gapikey )
			$gapikey = '';		
		
		$license 	= get_option( 'pp_events_license_key' );
		$status 	= get_option( 'pp_events_license_status' );
		
		?>

		<h3>BuddyPress Simple Events Pro Settings</h3>
		
		<table border="0" cellspacing="10" cellpadding="10">
		<tr>
		<td style="vertical-align:top; border: 1px solid #ccc;" >

			<h3><?php echo __('Assign User Roles', 'bp-simple-events'); ?></h3>
			<?php echo $this->roles_message; ?>
			<em><?php echo __('Which roles can create Events?', 'bp-simple-events'); ?></em><br/>
			<form action="" name="access-form" id="access-form"  method="post" class="standard-form">

			<?php wp_nonce_field('allowedroles-action', 'allowedroles-field'); ?>

			<ul id="pp-user_roles">

			<?php foreach(  $all_roles as $key => $value ){

				if( $key == 'administrator' ) :
				?>

					<li><label><input type="checkbox" id="admin-preset-role" name="admin-preset" checked="checked" disabled /> <?php echo ucfirst($key); ?></label></li>

				<?php else:

					if( array_key_exists('publish_events', $value["capabilities"]) )
						$checked = ' checked="checked"';
					else
						$checked = '';

				?>

					<li><label for="allow-roles-<?php echo $key ?>"><input id="allow-roles-<?php echo $key ?>" type="checkbox" name="allow-roles[]" value="<?php echo $key ?>" <?php echo  $checked ; ?> /> <?php echo ucfirst($key); ?></label></li>

				<?php endif;

			}?>

			</ul>

			<input type="hidden" name="role-access" value="1"/>
			<input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save Roles', 'bp-simple-events'); ?>"/>
			</form>

		</td>

		<td style="vertical-align:top; border: 1px solid #ccc;" >
		
			<div class="wrap">
			<h3><?php _e('License Options'); ?></h3>
			
			<?php echo $this->license_message; ?>
			
			<form method="post" action="">

				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row" valign="top">
								<?php _e('License Key', 'bp-simple-events'); ?>
							</th>
							<td>
								<input id="pp_events_license_key" name="pp_events_license_key" type="text" class="regular-text" placeholder="Paste Your License Key Here" value="<?php esc_attr_e( $license ); ?>" />
								<label class="description" for="pp_events_license_key"><em><?php _e('Enter your license key', 'bp-simple-events'); ?></em></label>
							</td>
						</tr>

						<?php if( false !== $license ) { ?>
							<tr valign="top">
								<th scope="row" valign="top">
									<?php _e('Activate License'); ?>
								</th>
								<td>
									<?php if( $status !== false && $status == 'valid' ) { ?>
										<span style="color:#32cd32;"><?php _e('Your License is Active', 'bp-simple-events' ); ?></span>
										<?php wp_nonce_field( 'pp_events_lic_nonce', 'pp_events_lic_nonce' ); ?>
										&nbsp;&nbsp;<input type="submit" class="button-secondary" name="pp_events_license_deactivate" value="<?php _e('Deactivate License', 'bp-simple-events'); ?>"/>
									<?php } else {
										wp_nonce_field( 'pp_events_lic_nonce', 'pp_events_lic_nonce' ); ?>
										<input type="submit" class="button-secondary" name="pp_events_license_activate" value="<?php _e('Activate License', 'bp-simple-events'); ?>"/>
									<?php } ?>
								</td>
							</tr>
						<?php } ?>

						<tr valign="top">
							<td>
								<?php wp_nonce_field( 'pp_events_lic_save_nonce', 'pp_events_lic_save_nonce' ); ?>
								<input type="submit" class="button button-primary" name="pp-events-lic-save" value="<?php _e("Save License Key", "bp-simple-events");?>" />
							</td>
							<td>&nbsp;<em><?php _e("You must Save your Key before you can Activate your License", "bp-simple-events");?></em></td>
						</tr>
					</tbody>
				</table>

			</form>
			<hr>
		</div>		
		
		
			<h3><?php echo __('Settings', 'bp-simple-events'); ?></h3>
			<?php echo $this->settings_message; ?>
			<form action="" name="settings-form" id="settings-form"  method="post" class="standard-form">

				<?php wp_nonce_field('settings-action', 'settings-field'); ?>

				<h4><?php _e( "Google Maps API Key", "bp-simple-events"); ?></h4>		
				<?php _e("Your Key", "bp-member-maps");?> &nbsp; 
				<input type="text" size="45" name="gapikey" placeholder="Paste Your Google Maps API Key Here" value="<?php echo $gapikey; ?>" /> 
				<br/><?php _e("A Key is required. If you do not have one, follow these instructions:", "bp-simple-events");?>
				<br/><a href="http://www.philopress.com/google-maps-api-key/" target="_blank">Get a Google Maps API Key</a>
					
				
				<h4><?php echo __('Profile', 'bp-simple-events'); ?></h4>
				<?php $tab_position = get_option( 'pp_events_tab_position' ); ?>
				<input type="text" size="5" id="pp-tab-position" name="pp-tab-position" value="<?php echo $tab_position; ?>" />
				<label for="pp-tab-position"><?php echo __( 'Tab Position <em>Numbers only.</em>', 'bp-simple-events' ); ?></label>
				<hr/>


				<h4><?php echo __('Groups', 'bp-simple-events'); ?></h4>
				<?php $groups = get_option( 'pp_events_groups' ); ?>
				<input type="checkbox" name="pp-groups" id="pp-groups" value="1"<?php if ( $groups == '1' ) echo ' checked="checked"'; ?> />
				<label for="pp-groups"><?php echo __( 'Give Groups the option to assign Events', 'bp-simple-events' ); ?></label>
				<br/>
				<?php echo __('If selected, each group can decide if members can assign an Event to that group.', 'bp-simple-events'); ?>
				<hr/>


				<h4><?php echo __('Required Fields', 'bp-simple-events'); ?></h4>
				<?php echo __('Select fields to be required when creating an Event.', 'bp-simple-events'); ?>
				<br/>

				<ul id="pp-fielders">

					<li><label><input type="checkbox" name="event-dummy[]" checked="checked" disabled /> Title</label></li>

					<li><label><input type="checkbox" name="event-dummy[]" checked="checked" disabled /> Description</label></li>

					<?php
					$required_fields = get_option( 'pp_events_required' );
					$checked = ' checked';
					?>

					<li><label for="required-date"><input id="required-date" type="checkbox" name="pp-required[]" value="date" <?php if( in_array( 'date', $required_fields ) ) echo $checked ; ?> /> <?php echo __( 'Start', 'bp-simple-events' ); ?></label></li>

					<li><label for="required-date-end"><input id="required-date-end" type="checkbox" name="pp-required[]" value="date-end" <?php if( in_array( 'date-end', $required_fields ) ) echo $checked ; ?> /> <?php echo __( 'End', 'bp-simple-events' ); ?></label></li>

					<li><label for="required-location"><input id="required-location" type="checkbox" name="pp-required[]" value="location" <?php if( in_array( 'location', $required_fields ) ) echo $checked ; ?> /> <?php echo __( 'Location', 'bp-simple-events' ); ?></label></li>

					<li><label for="required-url"><input id="required-url" type="checkbox" name="pp-required[]" value="url" <?php if( in_array( 'url', $required_fields ) ) echo $checked ; ?> /> <?php echo __( 'Url', 'bp-simple-events' ); ?></label></li>

					<li><label for="required-url"><input id="required-categories" type="checkbox" name="pp-required[]" value="categories" <?php if( in_array( 'categories', $required_fields ) ) echo $checked ; ?> /> <?php echo __( 'Categories', 'bp-simple-events' ); ?></label></li>

					<?php //if( PP_GROUPS ) : ?>
					<?php if( get_option( 'pp_events_groups') ==  '1' ) : ?>
						<li><label for="required-groups"><input id="required-groups" type="checkbox" name="pp-required[]" value="groups" <?php if( in_array( 'groups', $required_fields ) ) echo $checked ; ?> /> <?php echo __( 'Groups', 'bp-simple-events' ); ?></label></li>
					<?php endif; ?>

					<li><label for="required-image"><input id="required-image" type="checkbox" name="pp-required[]" value="image" <?php if( in_array( 'image', $required_fields ) ) echo $checked ; ?> /> <?php echo __( 'Image', 'bp-simple-events' ); ?></label></li>

				</ul>

				<hr/>

				<table cellspacing="5" cellpadding="5">

					<tr><td colspan="2"><h4><?php _e( "Single Event Map", "bp-simple-events"); ?></h4></td></tr>

					<tr>
						<td><?php _e("Zoom Level", "bp-simple-events");?></td>

						<td>
							<select name="map-zoom-level">
								<?php for ($i=1;$i<=15;$i++):?>
									<option value="<?php echo $i;?>" <?php if($map_zoom_level==$i) echo "selected=selected";?>><?php echo $i;?></option>
								<?php endfor;?>
							</select>
						</td>
					</tr>

					<tr>
						<td><?php _e("Map Width", "bp-simple-events");?></td>

						<td>100%&nbsp;<em><?php _e("Required so that map is responsive", "bp-simple-events");?></em></td>
					</tr>

					<tr>
						<td><?php _e("Map Height", "bp-simple-events");?></td>

						<td>
							<input type="text" size="3" name="map-height" value="<?php echo $map_height; ?>" />px  &nbsp;<em><?php _e("Cannot be less than 50 or greater than 640", "bp-simple-events");?></em>

						</td>
					</tr>


					<tr><td colspan="2"><hr></td></tr>



					<tr><td colspan="2"><h4><?php _e( "All Events Map", "bp-simple-events"); ?></h4></td></tr>

					<tr>
						<td><?php _e("Map Center", "bp-simple-events");?></td>

						<td>
							<input type="text" size="40" id="event-location" name="event-location" placeholder="<?php echo __( 'Start typing an address...', 'bp-simple-events' ); ?>" value="<?php echo stripslashes($event_address); ?>" />
			
							<input type="hidden" id="event-address" name="event-address" value="<?php echo stripslashes($event_address); ?>" />
							<input type="hidden" id="event-latlng" name="event-latlng"  value="<?php echo $event_latlng; ?>" />

						</td>
					</tr>

					<tr>
						<td><?php _e("Zoom Level", "bp-simple-events");?></td>

						<td>
							<select name="map-zoom-level-all">
								<?php for ($i=1;$i<=15;$i++):?>
									<option value="<?php echo $i;?>" <?php if($map_zoom_level_all == $i) echo "selected=selected";?>><?php echo $i;?></option>
								<?php endfor;?>
							</select>
						</td>
					</tr>

					<tr>
						<td><?php _e("Map Width", "bp-simple-events");?></td>

						<td>100%&nbsp;<em><?php _e("Required so that map is responsive", "bp-simple-events");?></em></td>
					</tr>

					<tr>
						<td><?php _e("Map Height", "bp-simple-events");?></td>

						<td>
							<input type="text" size="3" name="map-height-all" value="<?php echo $map_height_all;?>" />px  &nbsp;<em><?php _e("Cannot be less than 50 or greater than 640", "bp-simple-events");?></em>

						</td>
					</tr>
				</table>

				<hr/>
				<br/>
				<input type="hidden" name="settings-access" value="1"/>
				<input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save Settings', 'bp-simple-events'); ?>"/>
			</form>

		</td></tr></table>
	<?php
	}


	//  save any changes to role access options
	private function roles_update() {
		global $wp_roles;

		if( isset( $_POST['role-access'] ) ) {

			if( !wp_verify_nonce($_POST['allowedroles-field'],'allowedroles-action') )
				die('Security check');

			if( !is_super_admin() )
				return;

			$updated = false;

			$all_roles = $wp_roles->roles;

			foreach(  $all_roles as $key => $value ){

				if( 'administrator' != $key ) {

					$role = get_role( $key );

					$role->remove_cap( 'delete_published_events' );
					$role->remove_cap( 'delete_events' );
					$role->remove_cap( 'edit_published_events' );
					$role->remove_cap( 'edit_events' );
					$role->remove_cap( 'publish_events' );

					$updated = true;
				}
			}


			if( isset( $_POST['allow-roles'] ) ) {

				foreach( $_POST['allow-roles'] as $key => $value ){

					if( array_key_exists($value, $all_roles ) ) {

						if( 'administrator' != $value ) {

							$role = get_role( $value );
							$role->add_cap( 'delete_published_events' );
							$role->add_cap( 'delete_events' );
							$role->add_cap( 'edit_published_events' );
							$role->add_cap( 'edit_events' );
							$role->add_cap( 'publish_events' );

						}
					}
				}

			}

			if( $updated )
				$this->roles_message .=
					"<div class='updated below-h2'>" .
					__('User Roles have been updated.', 'bp-simple-events') .
					"</div>";
			else
				$this->roles_message .=
					"<div class='updated below-h2' style='color: red'>" .
					__('No changes were detected re User Roles.', 'bp-simple-events') .
					"</div>";
		}
	}

	//  save any changes to settings options
	private function settings_update() {

		if( isset( $_POST['settings-access'] ) ) {

			if( !wp_verify_nonce($_POST['settings-field'],'settings-action') )
				die('Security check');

			if( !is_super_admin() )
				return;

			if ( $_POST["gapikey"] != ''  ) 
				update_site_option( "pp_gapikey", $_POST["gapikey"] );				
			
			if( ! empty( $_POST['pp-tab-position'] ) ) {

				 if( is_numeric( $_POST['pp-tab-position'] ) )
				    $tab_value = $_POST['pp-tab-position'];
				else
					$tab_value = 52;
			}
			else
				$tab_value = 52;

			update_option( 'pp_events_tab_position', $tab_value );


			if ( ! empty( $_POST['pp-groups'] ) )
				update_option( 'pp_events_groups', '1' );
			else
				update_option( 'pp_events_groups', '0' );


			delete_option( 'pp_events_required' );
			$required_fields = array();
			if( ! empty( $_POST['pp-required'] ) ) {
				foreach ( $_POST['pp-required'] as $value )
					$required_fields[] = $value;
			}
			update_option( 'pp_events_required', $required_fields );

			
			$valid_map_zooms = array( '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15' );
			
			// update settings for single group map

			$settings_single = array();

			$map_zoom_level = $_POST["map-zoom-level"];
			if ( $map_zoom_level && in_array( $map_zoom_level, $valid_map_zooms ) )
				$settings_single["map_zoom_level"] = $map_zoom_level;
			else
				$settings_single["map_zoom_level"] = 10;

			$map_height = sanitize_text_field( $_POST["map-height"] );
			if ( $map_height && ( $map_height >= 50 && $map_height <= 640 ) )
				$settings_single["map_height"] = $map_height;
			else
				$settings_single["map_height"] = 200;

			
			update_site_option("pp-events-map-single-settings", $settings_single);


			// update settings for all groups map

			$settings_all = array();

			$map_address = sanitize_text_field( $_POST["event-address"] );
			$settings_all["event_address"] = $map_address;
			
			$map_latlng = sanitize_text_field( $_POST["event-latlng"] );
			$settings_all["event_latlng"] = $map_latlng;			
			
			$map_zoom_level = $_POST["map-zoom-level-all"];
			if ( $map_zoom_level && in_array( $map_zoom_level, $valid_map_zooms ) )
				$settings_all["map_zoom_level_all"] = $map_zoom_level;
			else
				$settings_all["map_zoom_level_all"] = 4;

			$map_height = sanitize_text_field( $_POST["map-height-all"] );
			if ( $map_height && ( $map_height >= 50 && $map_height <= 640 ) )
				$settings_all["map_height_all"] = $map_height;
			else
				$settings_all["map_height_all"] = 500;
			
			
			update_site_option("pp-events-map-all-settings", $settings_all);
			
			

			$this->settings_message .=
				"<div class='updated below-h2'>" .
				__('Settings have been updated.', 'bp-simple-events') .
				"</div>";
		}
	}

} // end of PP_Simple_Events_Admin_Settings class

$pp_se_admin_settings_instance = new PP_Simple_Events_Admin_Settings();