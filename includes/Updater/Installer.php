<?php

namespace MobileSecurity\Updater;


use MobileSecurity\Constants\Constants;

class Installer
{

	/**
	 * @var Installer
	 */
	private static $_instance = null;

	/**
	 * Utils constructor.
	 */
	private function __construct()
	{

	}

	/**
	 * Helper function that generates a random string, used to generate a api key for the site
	 * @param int $strength
	 * @return string
	 */
	public function generate_string($strength = 16)
	{
		$permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$input_length = strlen($permitted_chars);
		$random_string = '';
		for ($i = 0; $i < $strength; $i++) {
			$random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
			$random_string .= $random_character;
		}

		return $random_string;
	}

	/**
	 * @return Installer
	 */
	public static function getInstance()
	{
		if (!Installer::$_instance)
			Installer::$_instance = new Installer();

		return Installer::$_instance;
	}

	/**
	 * Function that verifies the presence of updates in the db and generates the secret key if not present
	 */
	public function update_db_check()
	{
		$current_db_version = get_option(Constants::OPTION_DB_VERSION);
		if (Constants::DATABASE_VERSION != $current_db_version) {
			$this->install();
			update_option(Constants::OPTION_DB_VERSION, Constants::DATABASE_VERSION);
		}
	}

	public function install()
	{
		global $wpdb;

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$table_name = $wpdb->prefix . Constants::SQL_TABLE_IP_LOG;

		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE $table_name (
			`id` VARCHAR(36) NOT NULL,
			`ip` VARCHAR(100),
			`time` DATETIME,
			`useragent` VARCHAR(500),
			`url` VARCHAR(500),
			`referer` VARCHAR(500),
			`error` TEXT,
			`type` INT,
			`file` VARCHAR(500),
			`line` INT,
			`status` INT,
			`timing` DOUBLE,
			`memory` INT,
			`logged_as` VARCHAR(50),
			KEY `ip_index` (`ip`) USING BTREE,
			PRIMARY KEY (`id`)
		); $charset_collate;";

		dbDelta($sql);

		$table_name = $wpdb->prefix . Constants::SQL_TABLE_BAN_RULES;

		$sql = "CREATE TABLE $table_name (
			`id` VARCHAR(36) NOT NULL,
			`content` VARCHAR(700),
			`type` VARCHAR(20),
			`permanent` BOOLEAN,
			`whitelist` BOOLEAN,
      		`ban_from` DATETIME,
			`ban_up_to` DATETIME,
			`enable_from` DATETIME,
			`enable_up_to` DATETIME,
			KEY `content_index` (`content`) USING BTREE,
			PRIMARY KEY (`id`)
		); $charset_collate;";

		dbDelta($sql);

		$table_name = $wpdb->prefix . Constants::SQL_TABLE_DEVICES;

		$sql = "CREATE TABLE $table_name (
			`id` VARCHAR(100) NOT NULL,
			`token` VARCHAR(300) NOT NULL,
			`login_successful` TINYINT(1) DEFAULT 0,
			`login_unsuccessful` TINYINT(1) DEFAULT 0,
			`content_updated` TINYINT(1) DEFAULT 0,
			`content_created` TINYINT(1) DEFAULT 0,
			`logout` TINYINT(1) DEFAULT 0,
			`mail_failed` TINYINT(1) DEFAULT 0,
			`mail_sent` TINYINT(1) DEFAULT 0,
			`comment_added` TINYINT(1) DEFAULT 0,
			`comment_approved` TINYINT(1) DEFAULT 0,
			KEY `token_index` (`token`) USING BTREE,
			PRIMARY KEY (`id`)
		); $charset_collate;";

		dbDelta($sql);


		if (!wp_next_scheduled(Constants::CRON_FLUSH_LOG_TABLE)) {
			wp_schedule_event(time(), 'daily', Constants::CRON_FLUSH_LOG_TABLE);
		}
		add_action(Constants::CRON_FLUSH_LOG_TABLE, array($this, 'flush_log_table'));
	}

	public function flush_log_table()
	{
		global $wpdb;
		$table_name = Constants::SQL_TABLE_IP_LOG;
		$oneWeek = 60 * 60 * 24 * 7;
		// removes all logs older than 7 days
		$wpdb->query("DELETE FROM $table_name WHERE TIMESTAMPDIFF(SECOND,`time`,NOW()) > $oneWeek;");
	}

	public function uninstall()
	{
		wp_clear_scheduled_hook(Constants::CRON_FLUSH_LOG_TABLE);
	}

}
