<?php

namespace MobileSecurity\Notification;

use MobileSecurity\Repository\DataRepository;

class Events
{

	/**
	 * @var Events
	 */
	private static $_instance = null;

	/**
	 * Events constructor.
	 */
	private function __construct()
	{

	}

	/**
	 * @return Events
	 */
	public static function getInstance()
	{
		if (!Events::$_instance) {
			Events::$_instance = new Events();
		}

		return Events::$_instance;
	}

	public function onLoginFailed($username, $error = null)
	{
		$tokens = DataRepository::getInstance()->getDeviceTokens();
		$notificator = new PushNotificator($tokens);
		$notificator->sendNotification(Notificator::TYPE_LOGIN_UNSUCCESSFUL, "Login failed", "User $username tried to log into the system.", array("action" => "website"));
	}

	public function onLoginSuccess($username, $user = null)
	{
		$tokens = DataRepository::getInstance()->getDeviceTokens();
		$notificator = new PushNotificator($tokens);
		$notificator->sendNotification(Notificator::TYPE_LOGIN_SUCCESSFUL, "Login Success", "User $username has logged into the system", array("action" => "website"));
	}

	public function onPostSavedOrUpdated($post_ID, $post, $update)
	{
		$tokens = DataRepository::getInstance()->getDeviceTokens();
		$notificator = new PushNotificator($tokens);
		$author = get_the_author_meta('user_login', get_current_user_id());
		if ($update) {
			$title = "Content Updated - ". $post->post_type;
			$message = $post->post_title . " updated by " . $author;
			$type = Notificator::TYPE_CONTENT_UPDATED;
		} else {
			$title = "New Content Created - ". $post->post_type;
			$message = $post->post_title . " created by " . $author;
			$type = Notificator::TYPE_CONTENT_CREATED;
		}
		$notificator->sendNotification($type, $title, $message, array("action" => "website"));
	}

	public function onLogOut()
	{
		$tokens = DataRepository::getInstance()->getDeviceTokens();
		$notificator = new PushNotificator($tokens);
		$notificator->sendNotification(Notificator::TYPE_LOGOUT, "User logged out", "A user has logged out", array("action" => "website"));
	}

	public function onMailFailed(\WP_Error $wp_error)
	{
		$tokens = DataRepository::getInstance()->getDeviceTokens();
		$notificator = new PushNotificator($tokens);
		$recipient = $wp_error->error_data["to"];
		$notificator->sendNotification(Notificator::TYPE_MAIL_FAILED, "Email failed", "Sending the email to $recipient has failed", array("action" => "website"));
	}

	public function onCommentAdded($comment_ID, $comment_approved, $commentdata)
	{
		$tokens = DataRepository::getInstance()->getDeviceTokens();
		$notificator = new PushNotificator($tokens);

		$title = "A new comment has been added";
		$type = Notificator::TYPE_COMMENT_ADDED;
		if (1 === $comment_approved) {
			$title = "A new comment has been approved";
			$type = Notificator::TYPE_COMMENT_APPROVED;
		}
		$message = $commentdata["comment_content"];
		$notificator->sendNotification($type, $title, $message, array("action" => "website"));
	}
}
