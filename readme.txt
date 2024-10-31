=== OpenX Wordpress Widget ===
Contributors: hwde
Donate link: http://www.xclose.de/
Tags: openx, openads, phpadsnew, advertising, banner
Requires at least: 2.5
Tested up to: 4.9.0
Stable tag: 1.3.1


== Description ==

OpenX Wordpress Widget is a plugin for wordpress websites and provides a simple, straightforward way to place ads from a Revive (formerly OpenX) AdServer on a website.

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the zip file to the /wp-content/plugins/ directory and unzip. 
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to your Options Panel and open the "OpenX-WP" submenu.
4. Enter the URL of your OpenX Adserver.
5. Add the widget to a sidebar and setup the AdServers zoneIds to show inside the sidebar
6. Or: simply use zone-macros within your posts like {openx:123}, which will be replaced by the plugin with the adcode to show zoneID 123 from your OpenX Adserver.


== Changelog ==

Version 1.3.1
- fix php 54 strict error

Version 1.3.0
- test / confirm works with WP4.5.*
- add support for revive adserver async. javascript tag

Version 1.2.9
- test / confirm works with WP4.0

Version 1.2.6

- make use of plugin translation, provide
  a german translation
- move some admin stuff to a different file

Version 1.2.5

- Tested plugin with WP 3.0 RC2
- allow to add the category of a post into
  the adrequest (thanks to Nick, edditor)
- allow to add the "tags" into the adrequest
- don't add the title to sidebar when empty
- optionally add a <br /> after a sidebar banner


Version 1.2.4

- added global config option to block duplicate ads
  on the same webpage


Version 1.2.3

- fixed missing adserver url in settings


Version 1.2.2

- Add posibility to test adserver url in admin settings.

- Allow to pass custom key/values of current posts
  into the ad-request which can be targeted in
  openx adserver (i.e. with Site:source or
  Site:variable)


== Frequently Asked Questions ==

1. How can I find my OpenX delivery URL for the settings ?

If you are running OpenX 2.8: login, switch to Administrator and look in: Configuration -> Global Settings -> Banner Delivery Settings -> Delivery Engine URL.

If you are running OpenX 2.6: login, switch to Administrator and look in: My Account -> Global Settings -> Banner Delivery Settings -> Delivery Engine URL.

If you are running OpenX 2.4: login as admin and check in: Settings -> Main Settings -> Delivery Settings -> Delivery Engine URL.

If you are using the hosted version of OpenX, use "d1.openx.org".


2. How can I show ads from my template, i.e. header or footer. The magic macro {opens:nun} doesn't work here?

Correct, the template area isn't under the control of the plugin. You have to edit the template yourself and paste either the plain opens invocation code at the place where you want to show the banner, or paste the following function call:

<?php echo get_openx_zone($zoneID); ?>

(replace $zoneID with the real zoneID. This does the same by invoke the plugin to print the standard opens ad request.
