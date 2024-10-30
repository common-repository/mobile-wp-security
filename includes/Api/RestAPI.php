<?php
/**
 * API.php
 * User: Daniele Callegaro <daniele.callegaro.90@gmail.com>
 * Created: 11/09/19
 */

namespace MobileSecurity\Api;

use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use MobileSecurity\Classes\BanRule;
use MobileSecurity\Constants\Constants;
use MobileSecurity\Repository\DataRepository;
use WP_REST_Request;

/**
 * Contains a list of Api Available
 * Class RestAPI
 * @package MobileSecurity\Api
 */
class RestAPI
{
	/**
	 * @var RestAPI
	 */
	private static $_instance = null;
	/**
	 * @var Serializer
	 */
	private $serializer;

	/**
	 * RestAPI constructor.
	 */
	private function __construct()
	{
		$this->serializer = SerializerBuilder::create()->build();
	}

	/**
	 * @return RestAPI
	 */
	public static function getInstance()
	{
		if (!RestAPI::$_instance)
			RestAPI::$_instance = new RestAPI();

		return RestAPI::$_instance;
	}

	/**
	 * Banns permanently the resource(ip, website or useragent) passed as parameter
	 * @param WP_REST_Request $request
	 * @return \StdClass|\WP_REST_Response
	 */
	public function addPermanentBan(WP_REST_Request $request)
	{
		if (!$this->validateSecret($request))
			return $this->NotFound404();

		$obj = $this->getJson($request);
		DataRepository::getInstance()->storeBanRule($obj->content, $obj->type, true, false);
		return $this->OK();
	}

	/**
	 * Removes the ban from the resource(ip, website or useragent) passed as parameter
	 * @param WP_REST_Request $request
	 * @return \StdClass|\WP_REST_Response
	 */
	public function removePermanentBan(WP_REST_Request $request)
	{
		if (!$this->validateSecret($request))
			return $this->NotFound404();

		$obj = $this->getJson($request);
		DataRepository::getInstance()->storeBanRule($obj->content, $obj->type, false, false);
		return $this->OK();
	}

	/**
	 * Temporarily banns the resource(ip, website or useragent) passed as a parameter for a duration equal to the value of the 'duration' field
	 * @param WP_REST_Request $request
	 * @return \StdClass|\WP_REST_Response
	 */
	public function addTemporaryBan(WP_REST_Request $request)
	{
		if (!$this->validateSecret($request))
			return $this->NotFound404();

		$obj = $this->getJson($request);
		DataRepository::getInstance()->storeBanRule($obj->content, $obj->type, false, false, $obj->duration);
		return $this->OK();
	}

	/**
	 * Removes the temporary ban from the resource(ip, website or useragent) passed as parameter
	 * @param WP_REST_Request $request
	 * @return \StdClass|\WP_REST_Response
	 */
	public function removeTemporaryBan(WP_REST_Request $request)
	{
		if (!$this->validateSecret($request))
			return $this->NotFound404();

		$obj = $this->getJson($request);
		DataRepository::getInstance()->storeBanRule($obj->content, $obj->type, false, false);
		return $this->OK();
	}

	/**
	 * Allows a banned resource(ip, website or useragent) to temporarily access the site for a duration equal to the duration field
	 * @param WP_REST_Request $request
	 * @return \StdClass|\WP_REST_Response
	 */
	public function enableTemporaryBan(WP_REST_Request $request)
	{
		if (!$this->validateSecret($request))
			return $this->NotFound404();

		$obj = $this->getJson($request);
		DataRepository::getInstance()->storeBanRule($obj->content, $obj->type, true, true, $obj->duration);
		return $this->OK();
	}

	/**
	 * Allows a banned resource(ip, website or useragent) to temporarily access the site for a duration equal to the duration field
	 * @param WP_REST_Request $request
	 * @return \StdClass|\WP_REST_Response
	 */
	public function getIpLog(WP_REST_Request $request)
	{
		if (!$this->validateSecret($request))
			return $this->NotFound404();

		$obj = $this->getJson($request);

		return json_decode($this->serializer->serialize(DataRepository::getInstance()->getIpAddressLog($obj->duration), 'json'));
	}

	/**
	 * Return the list of banned resources
	 * @param WP_REST_Request $request
	 * @return mixed|\WP_REST_Response
	 */
	public function getIpRules(WP_REST_Request $request)
	{
		if (!$this->validateSecret($request))
			return $this->NotFound404();

		return json_decode($this->serializer->serialize(DataRepository::getInstance()->getResourceRules(), 'json'));
	}

	/**
	 * Activate Emergency Login for 5 minutes.
	 * @param WP_REST_Request $request
	 * @return mixed|\WP_REST_Response
	 */
	public function activateEmergencyLogin(WP_REST_Request $request)
	{
		DataRepository::getInstance()->storeBanRule("/wp-login.php", BanRule::URL , true, true, 5*60);
		return array("message" => "Safe login disabled for 5 minutes");
	}

	/**
	 * Stores the token of the device needed to send notifications
	 * @param WP_REST_Request $request
	 * @return \StdClass|\WP_REST_Response
	 */
	public function storeDeviceToken(WP_REST_Request $request)
	{
		if (!$this->validateSecret($request))
			return $this->NotFound404();

		$obj = $this->getJson($request);
		DataRepository::getInstance()->storeDeviceToken($obj->id, $obj->token);

		return $this->OK();
	}

	/**
	 * Removes the token of the device needed to send notifications
	 * @param WP_REST_Request $request
	 * @return \StdClass|\WP_REST_Response
	 */
	public function removeDeviceToken(WP_REST_Request $request)
	{
		if (!$this->validateSecret($request))
			return $this->NotFound404();

		$obj = $this->getJson($request);
		DataRepository::getInstance()->removeDeviceToken($obj->id);

		return $this->OK();
	}

	/**
	 * Return the list of users who can log in to the system
	 * @param WP_REST_Request $request
	 * @return \StdClass|\WP_REST_Response
	 */
	public function getUsers(WP_REST_Request $request)
	{
		if (!$this->validateSecret($request))
			return $this->NotFound404();

		return json_decode($this->serializer->serialize(DataRepository::getInstance()->getUsers(), 'json'));
	}

	/**
	 * Create a user
	 * @param WP_REST_Request $request
	 * @return \StdClass|\WP_REST_Response
	 */
	public function createUser(WP_REST_Request $request)
	{
		if (!$this->validateSecret($request))
			return $this->NotFound404();

		$obj = $this->getJson($request);
		$user = $this->serializer->deserialize(json_encode($obj->user), 'MobileSecurity\Classes\User', 'json');
		$response = DataRepository::getInstance()->createUser($user);

		if (is_wp_error($response))
			return $this->KO($response->get_error_message());

		return $this->OK();
	}

	/**
	 * Modify a user's information
	 * @param WP_REST_Request $request
	 * @return \StdClass|\WP_REST_Response
	 */
	public function updateUser(WP_REST_Request $request)
	{
		if (!$this->validateSecret($request))
			return $this->NotFound404();

		$obj = $this->getJson($request);
		$user = $this->serializer->deserialize(json_encode($obj->user), 'MobileSecurity\Classes\User', 'json');
		$response = DataRepository::getInstance()->updateUser($user);

		if (is_wp_error($response))
			return $this->KO($response->get_error_message());

		return $this->OK();
	}

	/**
	 * Function that returns the list of roles of the website
	 * @param WP_REST_Request $request
	 * @return array|\WP_REST_Response
	 */
	public function getRoles(WP_REST_Request $request)
	{
		if (!$this->validateSecret($request))
			return $this->NotFound404();
		$roles = wp_roles();
		$names = $roles->get_names();
		$ret = array();
		foreach ($names as $key => $value) {
			$ret[] = array("name" => $value, "role" => $key);
		}
		return $ret;
	}

	/**
	 * Function that returns some information on the website, such as wordpress version and plugin version
	 * @param WP_REST_Request $request
	 * @return \StdClass|\WP_REST_Response
	 */
	public function websiteData(WP_REST_Request $request)
	{
		global $wp_version;
		$allPlugins = get_plugins(); // associative array of all installed plugins
		$activePlugins = get_option('active_plugins'); // simple array of active plugins

		if (!$this->validateSecret($request))
			return $this->NotFound404();

		$ret = new \StdClass();
		$ret->wordpress_version = $wp_version;
		$ret->plugin_version = Constants::PLUGIN_VERSION;
		$ret->number_plugins = count($allPlugins);
		$ret->number_active_plugins = count($activePlugins);
		$ret->quota = get_option(Constants::OPTION_QUOTA) ? intval(get_option(Constants::OPTION_QUOTA)) : null;
		$ret->rpc_disabled = get_option(Constants::OPTION_XML_RPC) ? true : false;
		$ret->icon = get_site_icon_url();
		return $ret;
	}

	/**
	 * Function that changes the preferences for notifications. You can decide which notifications to receive for a given device
	 * @param WP_REST_Request $request
	 * @return \StdClass|\WP_REST_Response
	 */
	public function updateNotificationPreferences(WP_REST_Request $request)
	{
		if (!$this->validateSecret($request))
			return $this->NotFound404();

		$array = $this->getJson($request, true);
		$id = $array["id"];
		$data = $array["data"];
		DataRepository::getInstance()->updateNotificationPreferences($id, $data);
		return $this->OK();
	}


	/**
	 * Function that returns the list of notifications that a given device receives
	 * @param WP_REST_Request $request
	 * @return array|object|void|\WP_REST_Response|null
	 */
	public function getNotificationPreferences(WP_REST_Request $request)
	{
		if (!$this->validateSecret($request))
			return $this->NotFound404();

		$obj = $this->getJson($request);

		return DataRepository::getInstance()->getNotificationPreferences($obj->id);
	}

	/**
	 * Function that returns the list of errors read from the log file in reverse order. If the log file does not exist or is not accessible an error message is returned
	 * @param WP_REST_Request $request
	 * @return array|\WP_REST_Response
	 */
	public function getErrorLogs(WP_REST_Request $request)
	{
		if (!$this->validateSecret($request))
			return $this->NotFound404();

		$logs = DataRepository::getInstance()->getErrorLogs();

		if ($logs) {
			$lines = explode("\n", $logs);
			$lines = array_reverse($lines);
			$lines = join("\n\n", $lines);
			return array("logs" => $lines, "found" => true);
		} else
			return array("logs" => "Log file not found or not accessible.", "found" => false);
	}

	/**
	 * Function that empties the log file
	 * @param WP_REST_Request $request
	 * @return \StdClass|\WP_REST_Response
	 */
	public function clearErrorLogs(WP_REST_Request $request)
	{
		if (!$this->validateSecret($request))
			return $this->NotFound404();

		DataRepository::getInstance()->clearErrorLogs();
		return $this->OK();
	}


	/**
	 * Function that sets a maximum number of requests for a given ip address within one minute. If the limit is reached, the ip address is temporary banned.
	 * @param WP_REST_Request $request
	 * @return \StdClass|\WP_REST_Response
	 */
	public function setRequestQuota(WP_REST_Request $request)
	{
		if (!$this->validateSecret($request))
			return $this->NotFound404();

		$obj = $this->getJson($request);
		if ($obj->quota != null) {
			update_option(Constants::OPTION_QUOTA, $obj->quota);
		} else {
			delete_option(Constants::OPTION_QUOTA);
		}

		return $this->OK();
	}


	/**
	 * Function that disables xmlrpc
	 * @param WP_REST_Request $request
	 * @return \StdClass|\WP_REST_Response
	 */
	public function disableXMLRPC(WP_REST_Request $request)
	{
		if (!$this->validateSecret($request))
			return $this->NotFound404();

		update_option(Constants::OPTION_XML_RPC, "disabled");

		return $this->OK();
	}

	/**
	 * Function that enables xmlrpc
	 * @param WP_REST_Request $request
	 * @return \StdClass|\WP_REST_Response
	 */
	public function enableXMLRPC(WP_REST_Request $request)
	{
		if (!$this->validateSecret($request))
			return $this->NotFound404();

		delete_option(Constants::OPTION_XML_RPC);

		return $this->OK();
	}


	/**
	 * Function that returns a successful response(200)
	 * @return \StdClass
	 */
	private function OK()
	{
		$ret = new \StdClass();
		$ret->status = "OK";
		return $ret;
	}

	/**
	 * Function that returns an unsuccessful response(500)
	 * @return \WP_REST_Response
	 */
	private function KO($message = "")
	{
		$ret = new \StdClass();
		$ret->status = "KO";
		$ret->message = $message;
		return new \WP_REST_Response($ret, 500);
	}

	/**
	 * Function that returns Not Found(404)
	 * @return \WP_REST_Response
	 */
	private function NotFound404()
	{
		return new \WP_REST_Response(null, 404);
	}

	/**
	 * Function that processes the invated json in the body of the response
	 * @param WP_REST_Request $request
	 * @param bool $associative
	 * @return mixed
	 */
	private function getJson(WP_REST_Request $request, $associative = false)
	{
		$json = $request->get_body();
		return json_decode($json, $associative);
	}

	/**
	 * Function that verifies that the secret site key sent in each request is valid.
	 * @param WP_REST_Request $request
	 * @return bool
	 */
	private function validateSecret(WP_REST_Request $request)
	{
		$obj = $this->getJson($request);
		return $obj->secret == get_option(Constants::OPTION_SECRET_KEY);
	}
}
