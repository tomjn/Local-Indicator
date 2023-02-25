=== Local Indicator ===
Contributors: TJNowell
Donate link: https://www.tomjn.com
Tags: admin, development, maintenance
Requires at least: 3.3
Tested up to: 6.1.1
Stable tag: 1.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A fast way of seeing which machine a WordPress install is running on.

== Description ==

An easy way of telling apart different installs of WordPress ( e.g. live/production/local ) via the admin bar. Super admins see a colour coded machine name in the admin bar, with an IP submenu. Mobiles will see a colour coded square dot

== Installation ==

1. Upload `local-indicator.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Upgrade Notice ==

= 1.7 =

 - Fixed PHP 8.1 deprecations
 - Coding standards improvements

= 1.6 =

 - Switched to add_node
 - Added PHP version to submenu
 - Added the hostname to the submenu for when in mobile view
 - Added a LOCALINDICATOR_ALWAYS_SHOWING define option
 - Added a LOCALINDICATOR_TEXT define option ( filters take precedence )

= 1.5 =

 - Fixed responsive view
 - Flattened and removed text shadows to fit in with MP6
 - Strict warning fixes
