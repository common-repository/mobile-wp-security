<?php

namespace MobileSecurity\Hooks;


use MobileSecurity\Api\RestAPI;
use MobileSecurity\Constants\Constants;
use MobileSecurity\Notification\Events;
use MobileSecurity\Repository\DataRepository;
use MobileSecurity\Settings\PluginSettings;
use MobileSecurity\Updater\Installer;

class Hooks
{

	/**
	 * @var Hooks
	 */
	private static $_instance = null;

	/**
	 * Utils constructor.
	 */
	private function __construct()
	{

	}

	/**
	 * @return Hooks
	 */
	public static function getInstance()
	{
		if (!Hooks::$_instance)
			Hooks::$_instance = new Hooks();

		return Hooks::$_instance;
	}

	public function loadHook()
	{
		add_action('plugins_loaded', array(Installer::getInstance(), 'update_db_check'));

		register_shutdown_function(array(DataRepository::getInstance(), 'onShutdown'));

		add_action('init', array(DataRepository::getInstance(), 'addIpAddressInLog'));
		add_action('init', array(DataRepository::getInstance(), 'onInit'));

		add_action('admin_init', array(PluginSettings::getInstance(), 'register_settings'));
		add_action('admin_init', array(PluginSettings::getInstance(), 'redirect_after_activation'));
		add_action('admin_menu', array(PluginSettings::getInstance(), 'register_options_page'));
		add_filter('plugin_action_links_' . Constants::PLUGIN_NAME . "/" . Constants::PLUGIN_NAME . ".php", array(PluginSettings::getInstance(), 'register_settings_link'));

		/** EVENTS */
		add_action('wp_login_failed', array(Events::getInstance(), 'onLoginFailed'), 10, 2);
		add_action('wp_login', array(Events::getInstance(), 'onLoginSuccess'), 10, 2);
		add_action('save_post', array(Events::getInstance(), 'onPostSavedOrUpdated'), 10, 3);
		add_action('wp_logout', array(Events::getInstance(), 'onLogOut'), 10, 0);
		add_action('wp_mail_failed', array(Events::getInstance(), 'onMailFailed'), 10, 1);
		add_action( 'comment_post',  array(Events::getInstance(), 'onCommentAdded'), 10, 3);


		add_action('rest_api_init', function () {

			/************* IP Hooks ************/

			register_rest_route(
				Constants::API_NAMESPACE . "/" . Constants::API_VERSION,
				'/' . Constants::API_ROUTE_ADD_PERMANENT_BAN_IP,
				array(
					'methods' => 'POST',
					'callback' => array(RestAPI::getInstance(), Constants::API_FUNCTION_ADD_PERMANENT_BAN_IP),
				));
			register_rest_route(
				Constants::API_NAMESPACE . "/" . Constants::API_VERSION,
				'/' . Constants::API_ROUTE_REMOVE_PERMANENT_BAN_IP,
				array(
					'methods' => 'POST',
					'callback' => array(RestAPI::getInstance(), Constants::API_FUNCTION_REMOVE_PERMANENT_BAN_IP),
				));
			register_rest_route(
				Constants::API_NAMESPACE . "/" . Constants::API_VERSION,
				'/' . Constants::API_ROUTE_ADD_TEMPORARY_BAN_IP,
				array(
					'methods' => 'POST',
					'callback' => array(RestAPI::getInstance(), Constants::API_FUNCTION_ADD_TEMPORARY_BAN_IP),
				));
			register_rest_route(
				Constants::API_NAMESPACE . "/" . Constants::API_VERSION,
				'/' . Constants::API_ROUTE_REMOVE_TEMPORARY_BAN_IP,
				array(
					'methods' => 'POST',
					'callback' => array(RestAPI::getInstance(), Constants::API_FUNCTION_REMOVE_TEMPORARY_BAN_IP),
				));
			register_rest_route(
				Constants::API_NAMESPACE . "/" . Constants::API_VERSION,
				'/' . Constants::API_ROUTE_ENABLE_BAN,
				array(
					'methods' => 'POST',
					'callback' => array(RestAPI::getInstance(), Constants::API_FUNCTION_ENABLE_BAN),
				));

			register_rest_route(
				Constants::API_NAMESPACE . "/" . Constants::API_VERSION,
				'/' . Constants::API_ROUTE_GET_IP_LOG,
				array(
					'methods' => 'POST',
					'callback' => array(RestAPI::getInstance(), Constants::API_FUNCTION_GET_IP_LOG),
				));

			register_rest_route(
				Constants::API_NAMESPACE . "/" . Constants::API_VERSION,
				'/' . Constants::API_ROUTE_WEBSITE_DATA,
				array(
					'methods' => 'POST',
					'callback' => array(RestAPI::getInstance(), Constants::API_FUNCTION_WEBSITE_DATA),
				));

			register_rest_route(
				Constants::API_NAMESPACE . "/" . Constants::API_VERSION,
				'/' . Constants::API_ROUTE_GET_IP_RULES,
				array(
					'methods' => 'POST',
					'callback' => array(RestAPI::getInstance(), Constants::API_FUNCTION_GET_IP_RULES),
				));

			register_rest_route(
				Constants::API_NAMESPACE . "/" . Constants::API_VERSION,
				'/' . Constants::API_ROUTE_GET_IP_NAMES,
				array(
					'methods' => 'POST',
					'callback' => array(RestAPI::getInstance(), Constants::API_FUNCTION_GET_IP_NAMES),
				));

			register_rest_route(
				Constants::API_NAMESPACE . "/" . Constants::API_VERSION,
				'/' . Constants::API_ROUTE_SET_IP_NAME,
				array(
					'methods' => 'POST',
					'callback' => array(RestAPI::getInstance(), Constants::API_FUNCTION_SET_IP_NAME),
				));

			register_rest_route(
				Constants::API_NAMESPACE . "/" . Constants::API_VERSION,
				'/' . Constants::API_ROUTE_REMOVE_IP_NAME,
				array(
					'methods' => 'POST',
					'callback' => array(RestAPI::getInstance(), Constants::API_FUNCTION_REMOVE_IP_NAME),
				));

			register_rest_route(
				Constants::API_NAMESPACE . "/" . Constants::API_VERSION,
				'/' . Constants::API_ROUTE_GET_USERS,
				array(
					'methods' => 'POST',
					'callback' => array(RestAPI::getInstance(), Constants::API_FUNCTION_GET_USERS),
				));

			register_rest_route(
				Constants::API_NAMESPACE . "/" . Constants::API_VERSION,
				'/' . Constants::API_ROUTE_GET_ROLES,
				array(
					'methods' => 'POST',
					'callback' => array(RestAPI::getInstance(), Constants::API_FUNCTION_GET_ROLES),
				));

			register_rest_route(
				Constants::API_NAMESPACE . "/" . Constants::API_VERSION,
				'/' . Constants::API_ROUTE_CREATE_USER,
				array(
					'methods' => 'POST',
					'callback' => array(RestAPI::getInstance(), Constants::API_FUNCTION_CREATE_USER),
				));

			register_rest_route(
				Constants::API_NAMESPACE . "/" . Constants::API_VERSION,
				'/' . Constants::API_ROUTE_UPDATE_USER,
				array(
					'methods' => 'POST',
					'callback' => array(RestAPI::getInstance(), Constants::API_FUNCTION_UPDATE_USER),
				));

			register_rest_route(
				Constants::API_NAMESPACE . "/" . Constants::API_VERSION,
				'/' . Constants::API_ROUTE_STORE_DEVICE_TOKEN,
				array(
					'methods' => 'POST',
					'callback' => array(RestAPI::getInstance(), Constants::API_FUNCTION_STORE_DEVICE_TOKEN),
				));

			register_rest_route(
				Constants::API_NAMESPACE . "/" . Constants::API_VERSION,
				'/' . Constants::API_ROUTE_REMOVE_DEVICE_TOKEN,
				array(
					'methods' => 'POST',
					'callback' => array(RestAPI::getInstance(), Constants::API_FUNCTION_REMOVE_DEVICE_TOKEN),
				));

			register_rest_route(
				Constants::API_NAMESPACE . "/" . Constants::API_VERSION,
				'/' . Constants::API_ROUTE_UPDATE_NOTIFICATION,
				array(
					'methods' => 'POST',
					'callback' => array(RestAPI::getInstance(), Constants::API_FUNCTION_UPDATE_NOTIFICATION),
				));

			register_rest_route(
				Constants::API_NAMESPACE . "/" . Constants::API_VERSION,
				'/' . Constants::API_ROUTE_GET_NOTIFICATION_PREF,
				array(
					'methods' => 'POST',
					'callback' => array(RestAPI::getInstance(), Constants::API_FUNCTION_GET_NOTIFICATION_PREF),
				));

			register_rest_route(
				Constants::API_NAMESPACE . "/" . Constants::API_VERSION,
				'/' . Constants::API_ROUTE_GET_ERROR_LOGS,
				array(
					'methods' => 'POST',
					'callback' => array(RestAPI::getInstance(), Constants::API_FUNCTION_GET_ERROR_LOGS),
				));

			register_rest_route(
				Constants::API_NAMESPACE . "/" . Constants::API_VERSION,
				'/' . Constants::API_ROUTE_CLEAR_ERROR_LOGS,
				array(
					'methods' => 'POST',
					'callback' => array(RestAPI::getInstance(), Constants::API_FUNCTION_CLEAR_ERROR_LOGS),
				));

			register_rest_route(
				Constants::API_NAMESPACE . "/" . Constants::API_VERSION,
				'/' . Constants::API_ROUTE_SET_REQUEST_QUOTA,
				array(
					'methods' => 'POST',
					'callback' => array(RestAPI::getInstance(), Constants::API_FUNCTION_SET_REQUEST_QUOTA),
				));

			register_rest_route(
				Constants::API_NAMESPACE . "/" . Constants::API_VERSION,
				'/' . Constants::API_ROUTE_ENABLE_XMLRPC,
				array(
					'methods' => 'POST',
					'callback' => array(RestAPI::getInstance(), Constants::API_FUNCTION_ENABLE_XMLRPC),
				));

			register_rest_route(
				Constants::API_NAMESPACE . "/" . Constants::API_VERSION,
				'/' . Constants::API_ROUTE_DISABLE_XMLRPC,
				array(
					'methods' => 'POST',
					'callback' => array(RestAPI::getInstance(), Constants::API_FUNCTION_DISABLE_XMLRPC),
				));

			register_rest_route(
				Constants::API_NAMESPACE . "/" . Constants::API_VERSION,
				'/' . Constants::API_ROUTE_EMERGENCY_LOGIN,
				array(
					'methods' => 'GET',
					'callback' => array(RestAPI::getInstance(), Constants::API_FUNCTION_EMERGENCY_LOGIN),
				));
		});
	}

}
