<?php

namespace MobileSecurity\Notification;

/**
 * Abstract class that identifies a generic notifier service
 * Class Notificator
 * @package MobileSecurity\Notification
 */
abstract class Notificator
{
	const TYPE_LOGIN_SUCCESSFUL = 0;
	const TYPE_LOGIN_UNSUCCESSFUL = 1;
	const TYPE_CONTENT_CREATED = 2;
	const TYPE_CONTENT_UPDATED = 3;
	const TYPE_LOGOUT = 4;
	const TYPE_COMMENT_ADDED = 5;
	const TYPE_COMMENT_APPROVED = 6;
	const TYPE_MAIL_SENT = 7;
	const TYPE_MAIL_FAILED = 8;

	public function sendNotification($type, $title, $message, $data = array())
	{
		$title = $_SERVER['SERVER_NAME'] . " - " . $title;
		$data["website"] = $_SERVER['SERVER_NAME'];
		$data["title"] = $title;
		$data["message"] = $message;
		$data["action"] = "website";

		$this->send($type, $title, $message, $data, get_site_url());
	}

	protected abstract function send($type, $title, $message, $data, $website_url);
}
