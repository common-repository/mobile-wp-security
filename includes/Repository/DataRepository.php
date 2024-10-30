<?php

namespace MobileSecurity\Repository;

use MobileSecurity\Classes\BanRule;
use MobileSecurity\Classes\IpLog;
use MobileSecurity\Classes\User;
use MobileSecurity\Constants\Constants;
use WP_User;

class DataRepository
{

	/**
	 * @var DataRepository
	 */
	private static $_instance = null;
	private $startTime;
	private $id;

	/**
	 * DataRepository constructor.
	 */
	private function __construct()
	{

	}

	/**
	 * @return DataRepository
	 */
	public static function getInstance()
	{
		if (!DataRepository::$_instance) {
			DataRepository::$_instance = new DataRepository();
		}

		return DataRepository::$_instance;
	}

	/**
	 * Disavle XmlRpc if flag is present
	 */
	public function onInit()
	{
		$rpc_disabled = get_option(Constants::OPTION_XML_RPC);
		if ($rpc_disabled && $rpc_disabled == "disabled") {
			add_filter('xmlrpc_enabled', '__return_false');
		}
	}

	/**
	 * Adds Request in log and check if a request should be banned
	 */
	public function addIpAddressInLog()
	{
		$data = $this->getRequestData();
		$this->storeIpAddressInLog($data['ip'], $data['user_agent'], $data['url']);
		$this->checkRequest($data['ip'], $data['user_agent'], $data['url']);
	}

	/**
	 * Get informations (Ip, UserAgent, Url) for the current request
	 * @return array
	 */
	private function getRequestData()
	{
		$userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "unknown";
		$ip = $_SERVER['REMOTE_ADDR'];
		$uri = $_SERVER['REQUEST_URI'];

		$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		return array('ip' => $ip, 'user_agent' => $userAgent, 'url' => $url);
	}


	/**
	 * Check and block a resource that should be blocked
	 * @param $ip
	 * @param $userAgent
	 * @param $url
	 */
	private function checkRequest($ip, $userAgent, $url)
	{
		$ipRules = $this->getResourceRules();

		if (strpos($url, Constants::API_NAMESPACE . "/" . Constants::API_VERSION) !== false)
			return;

		$current_user = wp_get_current_user();
		if ($current_user instanceof WP_User) {
			if ($current_user->user_login !== false)
				return;
		}

		foreach ($ipRules as $rule) {
			if ($rule->checkBan($ip, $userAgent, $url)) {
				$this->forbidden(__("You have been blocked from the website because of your suspicious activity.", Constants::PLUGIN_LANGUAGE_DOMAIN));
				return;
			}
		}

		if ($this->quotaReached($ip)) {
			$this->forbidden(__("You have been blocked from the website because of your suspicious activity.", Constants::PLUGIN_LANGUAGE_DOMAIN));
			return;
		}
	}

	/**
	 * Checks whether the maximum number of requests per minute per ip address has been reached or not
	 * @param $ip
	 * @return bool
	 */
	private function quotaReached($ip)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . Constants::SQL_TABLE_IP_LOG;
		$quota = intval(get_option(Constants::OPTION_QUOTA));
		if ($quota) {
			$row = $wpdb->get_row("SELECT count(*) as count FROM $table_name WHERE `ip` = '$ip' AND TIMESTAMPDIFF(SECOND,`time`,NOW()) < 60;", ARRAY_A);
			if ($row) {
				if (intval($row["count"]) > $quota) {
					$this->storeBanRule($ip, BanRule::IP, false, false, 60 * 15);
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Returns a forbidden page(403) using wp_die
	 * @param string $msg
	 */
	private function forbidden($msg = "")
	{
		wp_die($msg, __("Forbidden", Constants::PLUGIN_LANGUAGE_DOMAIN), array("response" => 403));
	}

	/**
	 * Updates the status code for the current request in the database
	 * @param $status
	 */
	private function updateStatusCode($status)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . Constants::SQL_TABLE_IP_LOG;
		$wpdb->update(
			$table_name,
			array(
				'status' => $status
			), array('id' => $this->id));
	}

	/**
	 * Updates the statistics of the current request (memory, time, errors) in the database
	 */
	private function updateRequestStat()
	{
		global $wpdb;

		if ($this->id == null) {
			$data = $this->getRequestData();
			$this->storeIpAddressInLog($data['ip'], $data['user_agent'], $data['url']);
		}

		$timing = microtime(true) - $this->startTime;
		$table_name = $wpdb->prefix . Constants::SQL_TABLE_IP_LOG;

		$error = error_get_last();
		$set = array(
			'timing' => $timing,
			'memory' => memory_get_peak_usage(),
		);
		if ($error) {
			$set["type"] = $error["type"];
			$set["error"] = $error["message"];
			$set["file"] = $error["file"];
			$set["line"] = $error["line"];
		}
		$set["status"] = http_response_code();

		$wpdb->update($table_name, $set, array('id' => $this->id));
		$wpdb->query('COMMIT');
	}

	/**
	 * Function called before the end of the request
	 */
	public function onShutdown()
	{
		$this->updateRequestStat();
	}

	/**
	 * Function that returns the list of requests made from now until the 'duration' parameter
	 * @param $duration
	 * @return array
	 */
	public function getIpAddressLog($duration)
	{
		global $wpdb;

		$table_name = $wpdb->prefix . Constants::SQL_TABLE_IP_LOG;

		$rows = $wpdb->get_results("SELECT `ip`, `time`, TIMESTAMPDIFF(SECOND,`time`,NOW()) as timeago, `useragent`, `url`, `timing`, `status`, `memory`, `referer`, `logged_as`, `type`, `error`, `line`,`file` FROM $table_name WHERE `time` BETWEEN DATE_SUB(NOW(), INTERVAL $duration SECOND) AND NOW() ORDER BY `time` DESC", ARRAY_A);

		return array_map(function ($row) {
			$ret = new IpLog();
			$ret->setIp($row["ip"]);
			$ret->setUseragent($row["useragent"]);
			$ret->setTime(new \DateTime($row["time"]));
			$ret->setTimeago($row["timeago"]);
			$ret->setUrl($row["url"]);
			$ret->setTiming($row["timing"]);
			$ret->setStatus($row["status"]);
			$ret->setMemory($row["memory"]);
			$ret->setReferer($row["referer"]);
			$ret->setLoggedAs($row["logged_as"]);
			$ret->setType($row["type"]);
			$ret->setError($row["error"]);
			$ret->setLine($row["line"]);
			$ret->setFile($row["file"]);
			return $ret;
		}, $rows);
	}

	/**
	 * Function that returns the list users
	 * @param $duration
	 * @return array
	 */
	public function getUsers()
	{
		$users = get_users();
		$ret = array();
		foreach ($users as $user) {
			$obj = new User();
			$obj->setId($user->data->ID);
			$obj->setLogin($user->data->user_login);
			$obj->setEmail($user->data->user_email);
			$obj->setDisplayName($user->data->display_name);
			$obj->setNickname($user->nickname);

			if (count($user->roles)) {
				$roles = array_values($user->roles);
				$obj->setRole($roles[0]);
			}
			$ret[] = $obj;
		}
		return $ret;
	}

	/**
	 * Function that creates a User
	 * @param $user
	 * @return int|\WP_Error
	 */
	public function createUser($user)
	{
		$userdata = array(
			'user_pass' => $user->getPassword(),
			'user_login' => $user->getLogin(),
			'user_email' => $user->getEmail(),
			'display_name' => $user->getDisplayName(),
			'nickname' => $user->getNickname(),
			'role' => $user->getRole()

		);
		return wp_insert_user($userdata);
	}

	/**
	 * Function that updates User data
	 * @param $user
	 * @return int|\WP_Error
	 */
	public function updateUser($user)
	{
		$userdata = array(
			'ID' => $user->getId()
		);
		if ($user->getPassword())
			$userdata["user_pass"] = $user->getPassword();
		if ($user->getRole())
			$userdata["role"] = $user->getRole();
		if ($user->getDisplayName())
			$userdata["display_name"] = $user->getDisplayName();
		if ($user->getNickname())
			$userdata["user_nicename"] = $user->getNickname();
		return wp_update_user($userdata);
	}

	/**
	 * Function that returns a list of banned resources
	 * @return array
	 */
	public function getResourceRules()
	{
		global $wpdb;

		$table_name = $wpdb->prefix . Constants::SQL_TABLE_BAN_RULES;

		$rows = $wpdb->get_results(
			"SELECT
					`content`,
					`type`,
					`permanent`,
					`whitelist`,
					`ban_from`,
					`ban_up_to`,
					`enable_from`,
					`enable_up_to`,
					TIMESTAMPDIFF(SECOND, NOW(), ban_up_to) as `ban_time_left`,
					TIMESTAMPDIFF(SECOND, NOW(), enable_up_to) as `enable_time_left`
					FROM $table_name WHERE 1", ARRAY_A);

		return array_map(function ($row) {
			$ret = new BanRule();
			$ret->setType($row["type"]);
			$ret->setContent($row["content"]);
			$ret->setPermanent($row["permanent"]);
			$ret->setWhitelist($row["whitelist"]);
			$ret->setBanFrom($row["ban_from"] ? new \DateTime($row["ban_from"]) : null);
			$ret->setBanUpTo($row["ban_up_to"] ? new \DateTime($row["ban_up_to"]) : null);
			$ret->setEnableFrom($row["enable_from"] ? new \DateTime($row["enable_from"]) : null);
			$ret->setEnableUpTo($row["enable_up_to"] ? new \DateTime($row["enable_up_to"]) : null);
			$ret->setBanTimeLeft($row["ban_time_left"]);
			$ret->setEnableTimeLeft($row["enable_time_left"]);
			return $ret;
		}, $rows);
	}


	/**
	 * Function that stores the current request to Database
	 * @param $ip
	 * @param $user_agent
	 * @param $url
	 */
	public function storeIpAddressInLog($ip, $user_agent, $url)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . Constants::SQL_TABLE_IP_LOG;
		$row = $wpdb->get_row('SELECT now() as now');
		$this->id = wp_generate_uuid4();

		if (!function_exists('wp_get_current_user')) {
			include(ABSPATH . "wp-includes/pluggable.php");
		}

		$current_user = wp_get_current_user();
		if (!($current_user instanceof WP_User)) {
			$current_user = null;
		} else {
			$current_user = $current_user->user_login;
			if ($current_user !== null && $current_user == "")
				$current_user = null;
		}

		if (strpos($url, Constants::API_NAMESPACE . "/" . Constants::API_VERSION) === FALSE) {
			$wpdb->query('START TRANSACTION');
			$wpdb->insert(
				$table_name,
				array(
					'id' => $this->id,
					'ip' => $ip,
					'time' => $row->now,
					'url' => $url,
					'referer' => isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : null,
					'status' => 200,
					'useragent' => $user_agent !== null && $user_agent !== "" ? $user_agent : "unknown",
					'logged_as' => $current_user,
				)
			);
		}
	}

	/**
	 * Function that store a Ban Rule to database
	 * @param $content
	 * @param $type
	 * @param $permanent
	 * @param $enable
	 * @param null $duration
	 * @throws \Exception
	 */
	public function storeBanRule($content, $type, $permanent, $enable, $duration = null)
	{
		global $wpdb;

		$table_name = $wpdb->prefix . Constants::SQL_TABLE_BAN_RULES;
		$row = $wpdb->get_row('SELECT now() as now');
		$now = $row->now;
		$ban_up_to = null;
		$enable_up_to = null;
		$enable_from = null;
		$ban_from = null;

		if ($enable) {
			if ($duration) {
				$enable_up_to = date_create_from_format("Y-m-d H:i:s", $now);
				$enable_up_to->add(new \DateInterval('PT' . $duration . 'S'));
				$enable_up_to = date("Y-m-d H:i:s", $enable_up_to->getTimestamp());
				$enable_from = $now;
			}
		} else {
			if ($duration) {
				$ban_up_to = date_create_from_format("Y-m-d H:i:s", $now);
				$ban_up_to->add(new \DateInterval('PT' . $duration . 'S'));
				$ban_up_to = date("Y-m-d H:i:s", $ban_up_to->getTimestamp());
				$ban_from = $now;
			}
		}

		$row = $wpdb->get_row("SELECT id from $table_name WHERE content = '$content'", ARRAY_A);

		if (isset($row) && array_key_exists("id", $row)) {
			$id = $row["id"];

			if ($permanent == 0 && $duration == null) {
				$wpdb->delete($table_name, array('id' => $id));
			} else {
				$wpdb->update($table_name,
					array(
						'permanent' => $permanent,
						'whitelist' => false,
						'ban_from' => $ban_from,
						'ban_up_to' => $ban_up_to,
						'enable_from' => $enable_from,
						'enable_up_to' => $enable_up_to,
					), array('id' => $id));
			}
		} else {
			$wpdb->insert(
				$table_name,
				array(
					'id' => wp_generate_uuid4(),
					'content' => $content,
					'permanent' => $permanent,
					'type' => $type,
					'whitelist' => false,
					'ban_from' => $ban_from,
					'ban_up_to' => $ban_up_to,
					'enable_from' => $enable_from,
					'enable_up_to' => $enable_up_to,
				)
			);
		}
	}

	/**
	 * Function that stores the device token in the device tokens lists
	 * @param $id
	 * @param $token
	 */
	public function storeDeviceToken($id, $token)
	{
		global $wpdb;

		$table_name = $wpdb->prefix . Constants::SQL_TABLE_DEVICES;

		$row = $wpdb->get_row("SELECT id from $table_name WHERE id = '$id'", ARRAY_A);

		if (isset($row) && array_key_exists("id", $row)) {
			$wpdb->update($table_name,
				array(
					'token' => $token
				), array('id' => $id));
		} else {
			$wpdb->insert($table_name,
				array(
					'id' => $id,
					'token' => $token
				));
		}
	}

	/**
	 * Function that returns a list of device token stored in database
	 * @return array|object|null
	 */
	public function getDeviceTokens()
	{
		global $wpdb;

		$table_name = $wpdb->prefix . Constants::SQL_TABLE_DEVICES;
		$results = $wpdb->get_results("
			SELECT
			`token`,
			`login_successful`,
			`login_unsuccessful`,
			`content_updated`,
			`content_created`,
			`logout`,
			`mail_failed`,
			`mail_sent`,
			`comment_added`,
			`comment_approved`
			from $table_name WHERE 1", ARRAY_A);
		return $results;
	}

	/**
	 * Function that returns a list of notification preferences for a device
	 * @param $id
	 * @return array|object|void|null
	 */
	public function getNotificationPreferences($id)
	{
		global $wpdb;

		$table_name = $wpdb->prefix . Constants::SQL_TABLE_DEVICES;
		$results = $wpdb->get_row("
			SELECT
			`login_successful`,
			`login_unsuccessful`,
			`content_updated`,
			`content_created`,
			`logout`,
			`mail_failed`,
			`mail_sent`,
			`comment_added`,
			`comment_approved`
			from $table_name WHERE `id` = '$id'", ARRAY_A);

		foreach ($results as $key => $value)
			$results[$key] = $value == "1";

		return $results;
	}

	/**
	 * Function that removes device token and it's preferences from database
	 * @param $id
	 */
	public function removeDeviceToken($id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . Constants::SQL_TABLE_DEVICES;

		$wpdb->delete($table_name, array('id' => $id));
	}

	/**
	 * Function that updates notification preferences for a device
	 * @param $id
	 * @param $array
	 */
	public function updateNotificationPreferences($id, $array)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . Constants::SQL_TABLE_DEVICES;

		$wpdb->update($table_name, $array, array('id' => $id));
	}

	/**
	 * Sets start time
	 * @param mixed $startTime
	 */
	public function setStartTime($startTime)
	{
		$this->startTime = $startTime;
	}

	/**
	 * Return the content of error log file
	 * @return false|string
	 */
	public function getErrorLogs()
	{
		$adaptive = true;
		$lines = 500;
		$errorPath = WP_CONTENT_DIR . "/debug.log";

		// Open file
		$f = @fopen($errorPath, "rb");
		if ($f === false) return false;

		// Sets buffer size, according to the number of lines to retrieve.
		// This gives a performance boost when reading a few lines from the file.
		if (!$adaptive) $buffer = 4096;
		else $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));

		// Jump to last character
		fseek($f, -1, SEEK_END);

		// Read it and adjust line number if necessary
		// (Otherwise the result would be wrong if file doesn't end with a blank line)
		if (fread($f, 1) != "\n") $lines -= 1;

		// Start reading
		$output = '';
		$chunk = '';

		// While we would like more
		while (ftell($f) > 0 && $lines >= 0) {

			// Figure out how far back we should jump
			$seek = min(ftell($f), $buffer);

			// Do the jump (backwards, relative to where we are)
			fseek($f, -$seek, SEEK_CUR);

			// Read a chunk and prepend it to our output
			$output = ($chunk = fread($f, $seek)) . $output;

			// Jump back to where we started reading
			fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);

			// Decrease our line counter
			$lines -= substr_count($chunk, "\n");

		}

		// While we have too many lines
		// (Because of buffer size we might have read too many)
		while ($lines++ < 0) {

			// Find first newline and remove all text before that
			$output = substr($output, strpos($output, "\n") + 1);

		}

		fclose($f);
		return trim($output);
	}

	/**
	 * Clear the content of error log file
	 */
	public function clearErrorLogs()
	{
		$errorPath = WP_CONTENT_DIR . "/debug.log";
		if (file_exists($errorPath)) {
			file_put_contents($errorPath, "**** Start Logs ****\n");
		}
	}

}
