=== Mobile WP Security ===
Contributors: dacalleg
Tags: mobile, smartphone, security
Requires at least: 4.5
Tested up to: 5.2.1
Stable tag: 1.2.0
Requires PHP: 5.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin exposes the rest APIs to be able to control some aspects of wordpress security via the mobile
app ["Mobile Security for Wordpress"](https://play.google.com/store/apps/details?id=com.dacalleg.wordpressmobilesecurity)

== Description ==

This plugin allows you to interface wordpress with the ["Mobile Security for Wordpress"](https://play.google.com/store/apps/details?id=com.dacalleg.wordpressmobilesecurity) app.
After installing the plugin you will see a qr-code to frame with the app.

The plugin will periodically collect data on:

- Requests
- Status codes
- Errors
- Ip Addresses
- User-Agent
- Miscellaneous Statistics

In addition to this information from the app will be possible:

- View the version of Wordpress
- View requests in real time
- View requests grouped by IP
- Assign a name to an IP address
- Lock IP addresses permanently
- Lock IP addresses temporarily
- Enable IP addresses
- Disable XMLRPC
- Disable login
- Enable login
- View statistics
- View how many Bots or Humans visit the site
- Enable maximum request quotas per ip address (in case the quota will be reached the ip address will be blocked for 15 minutes)
- View users
- Modify users
- View errors and where they occur
- Manage notifications
- Filter requests

All communication between the app and the site is done in a secure way, to access the API exposed by the plugin you need a secret key that is
generated when the plugin is installed and communicated to the app via QR Code. You can also replace the secret key by generating a new one from the plugin preferences.

The Safe Login feature allows you to disable the login (showing a 403 screen). Very useful to avoid Brute Force attacks.
Login can be reactivated temporarily or permanently via the app.

== Installation ==

1. Upload `mobile-wp-security` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Scan the Qr Code with Mobile Security for Wordpress App

== Frequently Asked Questions ==

= I enabled safe login on a site and uninstalled the app. I can no longer access the login page. How can I do this? =

To deactivate the safe login in an emergency, use the browser at this URL: /wp-json/mobile_wp_security/v1/emergency-login.
The safe login will be disabled for 5 minutes and you can login to the site to re-associate the site to the app or to disable the plugin.

== Screenshots ==

1. Plugin Settings and Qr-Code

== Changelog ==

= 1.2 =
* Bug fixing

= 1.1 =
* Added Multisite Support

= 1.0 =
* First Version

