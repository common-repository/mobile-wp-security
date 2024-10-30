<?php

namespace MobileSecurity\Constants;


class Constants
{
	const PLUGIN_VERSION = "1.2";
	const DATABASE_VERSION = "2.9";
	const PLUGIN_CODE = "mobile_wp_security";
	const PLUGIN_NAME = "mobile-wp-security";

	const PLUGIN_LANGUAGE_DOMAIN = "mobile_wp_security";

	const OPTION_DB_VERSION = self::PLUGIN_CODE . "_db_version";
	const OPTION_SECRET_KEY = self::PLUGIN_CODE . "_secret_key";
	const OPTION_ACTIVATION_REDIRECT = self::PLUGIN_CODE . "_activation_redirect";
    const OPTION_QUOTA = self::PLUGIN_CODE .  "_req_block_quota";
	const OPTION_XML_RPC = self::PLUGIN_CODE .  "_xml_rpc_option";

	const SQL_TABLE_IP_LOG = "mobile_security_ip_log";
	const SQL_TABLE_BAN_RULES = "mobile_security_ban_rules";
	const SQL_TABLE_DEVICES = "mobile_security_devices";

	const API_NAMESPACE = "mobile_wp_security";
	const API_VERSION = "v1";

	const CRON_FLUSH_LOG_TABLE = self::PLUGIN_CODE . "_cron_flush_table_log";

	const API_ROUTE_ADD_PERMANENT_BAN_IP = "add-permanent-ban";
	const API_ROUTE_REMOVE_PERMANENT_BAN_IP = "remove-permanent-ban";
	const API_FUNCTION_ADD_PERMANENT_BAN_IP = "addPermanentBan";
	const API_FUNCTION_REMOVE_PERMANENT_BAN_IP = "removePermanentBan";

	const API_ROUTE_ADD_TEMPORARY_BAN_IP = "add-temporary-ban";
	const API_ROUTE_REMOVE_TEMPORARY_BAN_IP = "remove-temporary-ban";
	const API_FUNCTION_ADD_TEMPORARY_BAN_IP = "addTemporaryBan";
	const API_FUNCTION_REMOVE_TEMPORARY_BAN_IP = "removeTemporaryBan";

	const API_ROUTE_ENABLE_BAN = "enable-ban";
	const API_FUNCTION_ENABLE_BAN = "enableTemporaryBan";

	const API_ROUTE_STORE_DEVICE_TOKEN = "store-device-token";
	const API_FUNCTION_STORE_DEVICE_TOKEN = "storeDeviceToken";

	const API_ROUTE_REMOVE_DEVICE_TOKEN = "remove-device-token";
	const API_FUNCTION_REMOVE_DEVICE_TOKEN = "removeDeviceToken";

	const API_ROUTE_WEBSITE_DATA = "website-data";
	const API_FUNCTION_WEBSITE_DATA = "websiteData";

	const API_ROUTE_GET_IP_LOG = "get-ip-log";
	const API_FUNCTION_GET_IP_LOG = "getIpLog";

	const API_ROUTE_GET_IP_RULES = "get-ip-rules";
	const API_FUNCTION_GET_IP_RULES = "getIpRules";

	const API_ROUTE_GET_IP_NAMES = "get-ip-names";
	const API_FUNCTION_GET_IP_NAMES = "getIpNames";

	const API_ROUTE_SET_IP_NAME = "set-ip-name";
	const API_FUNCTION_SET_IP_NAME = "setIpName";

	const API_ROUTE_REMOVE_IP_NAME = "remove-ip-name";
	const API_FUNCTION_REMOVE_IP_NAME = "removeIpName";

	const API_ROUTE_GET_USERS = "get-users";
	const API_FUNCTION_GET_USERS = "getUsers";

	const API_ROUTE_GET_ROLES = "get-roles";
	const API_FUNCTION_GET_ROLES = "getRoles";

	const API_ROUTE_CREATE_USER = "create-user";
	const API_FUNCTION_CREATE_USER = "createUser";

	const API_ROUTE_UPDATE_USER = "update-user";
	const API_FUNCTION_UPDATE_USER = "updateUser";

	const API_ROUTE_UPDATE_NOTIFICATION = "update-notification-pref";
	const API_FUNCTION_UPDATE_NOTIFICATION = "updateNotificationPreferences";

	const API_ROUTE_GET_NOTIFICATION_PREF = "get-notification-pref";
	const API_FUNCTION_GET_NOTIFICATION_PREF = "getNotificationPreferences";

	const API_ROUTE_GET_ERROR_LOGS = "get-error-logs";
	const API_FUNCTION_GET_ERROR_LOGS = "getErrorLogs";

	const API_ROUTE_CLEAR_ERROR_LOGS = "clear-error-logs";
	const API_FUNCTION_CLEAR_ERROR_LOGS = "clearErrorLogs";

	const API_ROUTE_SET_REQUEST_QUOTA = "set-request-quota";
	const API_FUNCTION_SET_REQUEST_QUOTA = "setRequestQuota";

	const API_ROUTE_ENABLE_XMLRPC = "enable-xmlrpc";
	const API_FUNCTION_ENABLE_XMLRPC = "enableXMLRPC";

	const API_ROUTE_DISABLE_XMLRPC = "disable-xmlrpc";
	const API_FUNCTION_DISABLE_XMLRPC = "disableXMLRPC";

	const API_ROUTE_EMERGENCY_LOGIN = "emergency-login";
	const API_FUNCTION_EMERGENCY_LOGIN  = "activateEmergencyLogin";
}
