<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


if ( bp_is_active( 'groups' ) ) :

class PP_Group_Events extends BP_Group_Extension {

    function __construct() {

		$setting = groups_get_groupmeta( $this->get_group_id(), 'pp-events-assignable'  );

		if( $setting == '1' )
			$show_tab = 'anyone';
		else
			$show_tab = 'noone';

        $args = array(
            'slug'              => 'group-events',
            'name'              =>  __( 'Events', 'bp-simple-events' ),
            'nav_item_position' => 200.5,
            'show_tab'          => $show_tab,
            'screens' => array(
                'edit' => array(
                    'name'      => __( 'Events', 'bp-simple-events' ),
                ),
                'create'        => array( 'position' => 100, ),
            ),
        );
        parent::init( $args );
    }

    function display( $group_id = NULL ) {
        $group_id = bp_get_group_id();

        $setting = groups_get_groupmeta( $this->group_id, 'pp-events-assignable'  );

		if( $setting == '1' )
			bp_get_template_part('groups/single/group-events-loop');
		else
			echo '<br/>' . __( 'Please go to Manage > Events and check the box so that all Group members can see the "Events" tab and Events can be displayed for this Group.', 'bp-simple-events' );

    }

    function settings_screen( $group_id = NULL ) {
        $setting = groups_get_groupmeta( $group_id, 'pp-events-assignable'  );
        ?>
		<h4><?php _e( 'Group Events', 'bp-simple-events' ); ?></h4>

		<div class="checkbox">
	        <input type="checkbox" name="pp-events-assignable" id="pp-events-assignable" value="1"<?php if ( $setting == '1' ) 	echo ' checked="checked"'; ?> />&nbsp;
	        <?php _e( 'Allow group members to assign Events to this group.', 'bp-simple-events' ); ?>
	 	</div>
		<br/>
		<hr />
        <?php
    }

    function settings_screen_save( $group_id = NULL ) {
        $setting = isset( $_POST['pp-events-assignable'] ) ? '1' : '0';
        groups_update_groupmeta( $group_id, 'pp-events-assignable', $setting );
    }

}
bp_register_group_extension( 'PP_Group_Events' );

endif;

