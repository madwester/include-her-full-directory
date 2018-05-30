=== BuddyPress Simple Events Pro ===
Contributors: shanebp
Donate link: http://www.philopress.com/donate/
Tags: buddypress, events
Author: PhiloPress
Author URI: http://philopress.com/contact/
Plugin URI: http://philopress.com/products/
Requires at least: 4.0 
Tested up to: 4.7
Stable tag: 2.4
Copyright (C) 2016-2017  shanebp, PhiloPress 

Pro Version of the BuddyPress Simple Events plugin for BuddyPress

== Description ==

This plugin:

* supports the basic creation and editing of Events
* uses the Google Places API for locations
* provides a tab on each members' profile for front-end creation, editing and deletion
* supports one jpg Image per event, auto-rotated of necessary
* supports assignment to any Group that allows assignment and that the member belongs to
* provides options for 'Attending' button, notifications and emails
* creates a custom post type called 'event'
* uses WP / BP templates that can be overloaded
* includes a widget
* includes Event search


This plugin does NOT have:

* ticketing
* calendars - BUT should work with any WP Calendar plugin that supports custom post types
* recurring events



== Installation ==

1. Upload the zip on the Plugins > Add screen in wp-admin

2. Activate the plugin through the 'Plugins' menu in WordPress

3. If you already have the free version - BuddyPress Simple Events - please deactivate it before continuing

4. Go to Settings -> BP Group Maps and enter your License Key AND Google Maps API Key. If you don't have a Key, see FAQ.  

5. Go to Settings -> BP Simple Events and select which user roles are allowed to create Events.
Admins are automatically given permission.  Other settings are also available.



== Frequently Asked Questions ==

= Do I need a Google Maps API Key?
Yes. If you need help, read this tutorial [Google Maps API Key](http://www.philopress.com/google-maps-api-key/ "Google Maps API Key")

= MultiSite support? =

Yes. Tested in the following configuration:

* WP.4.1.1 - Multisite
* BuddyPress 2.2.1 - Network Activated
* BuddyPress Simple Events - Network Activated

Roles can be assigned via the Network Admin > Settings > BP Simple Events screen.

But a member _must_ be a member of the main site in order to create Events.
If they are not a member of the main site, they will not see the Events tab.

= Calendar support? =

Yes - if the Calendar supports custom post types


= My Events are not showing on the Events page =

Your events are probably being archived.
Check the Events > Archive page on your profile.

This is due to a difference between English and European preference re date format.

The fix is simple.
Open this file in a text editor:
bp-simple-events-pro\inc\js\events.js

Find in 2 places: dateFormat: 'DD, MM d, yy'

Change it to:  dateFormat: 'dd-mm-yy'

Upload it and then change the date on an existing Event or create a new one.


== Changelog ==

= 2.4 =
* Save post_date as the Event Start date so that Calendar plugins can be used

= 2.3 =
* Allow showing of Attending Button for Event Creator

= 2.2 =
* Added License Key

= 2.0 =
* Added requirement for Google Maps API Key

= 1.6 =
* Added Search

= 1.5 =
* Added End date, updated language file

= 1.4 =
* Map display fix, Added options for public display of attendees & list as names or avatars

= 1.3 =
* Tested in WP 4.2 & BP 2.2.3.1, tweak subnav creation, replace template_redirect with template_include

= 1.2 =
* added check for BP active

= 1.1 =
* multisite support added, event trashing cleanup improved

= 1.0 =
* Initial release.

