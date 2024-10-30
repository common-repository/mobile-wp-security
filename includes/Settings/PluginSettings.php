<?php

namespace MobileSecurity\Settings;


use MobileSecurity\Constants\Constants;
use MobileSecurity\Updater\Installer;

class PluginSettings
{

	/**
	 * @var PluginSettings
	 */
	private static $_instance = null;


	/**
	 * DataRepository constructor.
	 */
	private function __construct()
	{

	}

	/**
	 * @return PluginSettings
	 */
	public static function getInstance()
	{
		if (!PluginSettings::$_instance) {
			PluginSettings::$_instance = new PluginSettings();
		}

		return PluginSettings::$_instance;
	}

	function redirect_after_activation()
	{
		if (get_option(Constants::OPTION_ACTIVATION_REDIRECT, false)) {
			delete_option(Constants::OPTION_ACTIVATION_REDIRECT);
			$option_url = admin_url('options-general.php?page=mobile_wp_security');
			wp_redirect($option_url);
		}
	}

	public function register_settings()
	{
		add_option(Constants::PLUGIN_CODE . "_settings", __("Plugin Options", Constants::PLUGIN_LANGUAGE_DOMAIN));
		register_setting(Constants::PLUGIN_CODE . "_options_group", Constants::PLUGIN_CODE . "_settings");
	}

	public function register_options_page()
	{
		add_options_page(__("Mobile Security Options", Constants::PLUGIN_LANGUAGE_DOMAIN), 'Mobile Security', 'manage_options', Constants::PLUGIN_CODE, array($this, 'option_page'));
	}

	public function register_settings_link($links)
	{
		$option_url = admin_url('options-general.php?page=mobile_wp_security');
		$settings_link = "<a href='$option_url'>Settings</a>";
		array_unshift($links, $settings_link);
		return $links;
	}

	/**
	 * Function that shows the setting page. Displays the QR code to be framed with the smartphone.
	 * It also allows you to generate a new secret code that will be replaced by the previous one.
	 */
	public function option_page()
	{
		$url = get_site_url();

		if (isset($_GET["renew"])) {
			global $wpdb;
			$table_name = $wpdb->prefix . Constants::SQL_TABLE_DEVICES;
			update_option(Constants::OPTION_SECRET_KEY, Installer::getInstance()->generate_string());
			//Removes all device token
			$wpdb->query("DELETE FROM $table_name WHERE 1;");
		}

		$secret = get_option(Constants::OPTION_SECRET_KEY);
		if (!$secret)
			update_option(Constants::OPTION_SECRET_KEY, Installer::getInstance()->generate_string());

		$obj = new \StdClass();
		$obj->url = $url;
		$obj->secret = $secret;

		echo "<div style='display: flex;width: 100%;flex-direction: column;align-items: center;'>";
		echo "<h1 style='margin-top:40px'>" . __("Mobile WP Security", Constants::PLUGIN_LANGUAGE_DOMAIN) . "</h1>";
		echo "<p style='font-size: 16px;margin: 40px 0;'>" . __("Frame the QR code with your smartphone to add this site to the \"Mobile Security for Wordpress\" App.", Constants::PLUGIN_LANGUAGE_DOMAIN) . "</p>";
		echo "<img src='https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=" . json_encode($obj) . "&choe=UTF-8'>";
		echo "<div style='margin-top:30px'><a href='" . admin_url('options-general.php?page=mobile_wp_security&renew=true') . "'>" . __("Replace API Key With New One", Constants::PLUGIN_LANGUAGE_DOMAIN) . "</a></div>";
		echo "</div>";
	}

}
