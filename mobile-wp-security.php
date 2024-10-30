<?php
/**
 * Plugin Name:     Mobile WP Security
 * Plugin URI:      https://wordpress.org/plugins/mobile-wp-security
 * Description:     This plugin allows you to check in realtime through the <strong>mobile App</strong> who is visiting your site and allows you to ban suspicious visitors.
 * Author:          Daniele Callegaro
 * Author URI:      mailto:daniele.callegaro.90@gmail.com
 * Text Domain:     mobile_wp_security
 * Domain Path:     /languages
 * Version:         1.2.0
 *
 * @package         Mobile_WP_Security
 */

use Doctrine\Common\Annotations\AnnotationRegistry;
use MobileSecurity\Constants\Constants;
use MobileSecurity\Hooks\Hooks;
use MobileSecurity\Updater\Installer;


$autoloader = require __DIR__ . "/vendor/autoload.php";
AnnotationRegistry::registerLoader(array($autoloader, "loadClass"));

\MobileSecurity\Repository\DataRepository::getInstance()->setStartTime(microtime(true));
register_activation_hook(__FILE__, 'mobile_wp_security_on_activate');
register_deactivation_hook(__FILE__, 'mobile_wp_security_on_deactivate');

Hooks::getInstance()->loadHook();

function mobile_wp_security_on_activate($network_wide)
{
	global $wpdb;
	if (is_multisite() && $network_wide) {
		// Get all blogs in the network and activate plugin on each one
		$blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
		foreach ($blog_ids as $blog_id) {
			switch_to_blog($blog_id);
			Installer::getInstance()->install();
			restore_current_blog();
		}
	} else {
		add_option(Constants::OPTION_ACTIVATION_REDIRECT, true);
		Installer::getInstance()->install();
	}
}

function mobile_wp_security_on_new_blog($blog_id) {

	if ( is_plugin_active_for_network( 'mobile-wp-security/mobile-wp-security.php' ) ) {
		switch_to_blog($blog_id);
		Installer::getInstance()->install();
		restore_current_blog();
	}
}

add_action('wpmu_new_blog', 'mobile_wp_security_on_new_blog');

function mobile_wp_security_on_deactivate($network_wide)
{
	global $wpdb;
	if (is_multisite() && $network_wide) {
		// Get all blogs in the network and activate plugin on each one
		$blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
		foreach ($blog_ids as $blog_id) {
			switch_to_blog($blog_id);
			Installer::getInstance()->uninstall();
			restore_current_blog();
		}
	} else {
		Installer::getInstance()->uninstall();
	}
}
