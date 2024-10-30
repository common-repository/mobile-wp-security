<?php

namespace MobileSecurity\Notification;

use MobileSecurity\Constants\Constants;

/**
 * Class that define a notify service for push notification
 * Class PushNotificator
 * @package MobileSecurity\Notification
 */
class PushNotificator extends Notificator
{
	private $devices;

	/**
	 * PushNotification constructor.
	 * @param $devices
	 */
	public function __construct($devices)
	{
		$this->devices = $devices;
	}

	/**
	 * Send a notification via google cloud notification(GCM)
	 * @param $type
	 * @param $title
	 * @param $message
	 * @param $data
	 * @param $website_url
	 */
	protected function send($type, $title, $message, $data, $website_url)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . Constants::SQL_TABLE_DEVICES;

		$where = "";
		switch ($type) {
			case Notificator::TYPE_LOGIN_SUCCESSFUL:
				$where = "`login_successful` = 1";
				break;
			case Notificator::TYPE_LOGIN_UNSUCCESSFUL:
				$where = "`login_unsuccessful` = 1";
				break;
			case Notificator::TYPE_CONTENT_CREATED:
				$where = "`content_created` = 1";
				break;
			case Notificator::TYPE_CONTENT_UPDATED:
				$where = "`content_updated` = 1";
				break;
			case Notificator::TYPE_LOGOUT:
				$where = "`logout` = 1";
				break;
			case Notificator::TYPE_COMMENT_ADDED:
				$where = "`comment_added` = 1";
				break;
			case Notificator::TYPE_COMMENT_APPROVED:
				$where = "`comment_approved` = 1";
				break;
			case Notificator::TYPE_MAIL_SENT:
				$where = "`mail_sent` = 1";
				break;
			case Notificator::TYPE_MAIL_FAILED:
				$where = "`mail_failed` = 1";
				break;
		}

		$query = "SELECT `token` FROM $table_name WHERE $where;";
		$rows = $wpdb->get_results($query,ARRAY_A);

		$data['tokens'] = array();

		foreach ($rows as $row)
			$data['tokens'][] = $row['token'];

		if(count($data['tokens'])) {
			$args = array(
				'body' => json_encode($data),
				'headers' => array(
					'Content-Type' => 'application/json'
				),
			);
			wp_remote_post('https://us-central1-wordpress-mobile-security.cloudfunctions.net/sendNotification', $args);
		}
	}
}
