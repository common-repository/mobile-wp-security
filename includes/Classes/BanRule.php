<?php
/**
 * IpRule.php
 * User: Daniele Callegaro <daniele.callegaro.90@gmail.com>
 * Created: 04/02/20
 */

namespace MobileSecurity\Classes;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Type;

/**
 * Class IpRule
 * @ExclusionPolicy("all")
 */
class BanRule
{
	const IP = 'IP';
	const USERAGENT = 'USERAGENT';
	const URL = 'URL';

	/**
	 * @var string
	 * @Type("string")
	 * @Expose
	 */
	private $content;

	/**
	 * @var string
	 * @Type("string")
	 * @Expose
	 */
	private $type;

	/**
	 * @var bool
	 * @Type("bool")
	 * @Expose
	 */
	private $permanent;
	/**
	 * @var bool
	 * @Type("bool")
	 * @Expose
	 */
	private $whitelist;
	/**
	 * @var \DateTime
	 * @Type("DateTime")
	 * @Expose
	 */
	private $ban_from;
	/**
	 * @var \DateTime
	 * @Type("DateTime")
	 * @Expose
	 */
	private $ban_up_to;
	/**
	 * @var integer
	 * @Type("integer")
	 * @Expose
	 */
	private $ban_time_left;

	/**
	 * @var \DateTime
	 * @Type("DateTime")
	 * @Expose
	 */
	private $enable_from;
	/**
	 * @var \DateTime
	 * @Type("DateTime")
	 * @Expose
	 */
	private $enable_up_to;
	/**
	 * @var integer
	 * @Type("integer")
	 * @Expose
	 */
	private $enable_time_left;

	/**
	 * @return bool
	 */
	public function isPermanent()
	{
		return $this->permanent;
	}

	/**
	 * @param bool $permanent
	 */
	public function setPermanent($permanent)
	{
		$this->permanent = $permanent;
	}

	/**
	 * @return bool
	 */
	public function isWhitelist()
	{
		return $this->whitelist;
	}

	/**
	 * @param bool $whitelist
	 */
	public function setWhitelist($whitelist)
	{
		$this->whitelist = $whitelist;
	}

	/**
	 * @return \DateTime
	 */
	public function getBanFrom()
	{
		return $this->ban_from;
	}

	/**
	 * @param \DateTime $ban_from
	 */
	public function setBanFrom($ban_from)
	{
		$this->ban_from = $ban_from;
	}

	/**
	 * @return \DateTime
	 */
	public function getBanUpTo()
	{
		return $this->ban_up_to;
	}

	/**
	 * @param \DateTime $ban_up_to
	 */
	public function setBanUpTo($ban_up_to)
	{
		$this->ban_up_to = $ban_up_to;
	}

	/**
	 * @return int
	 */
	public function getBanTimeLeft()
	{
		return $this->ban_time_left;
	}

	/**
	 * @param int $ban_time_left
	 */
	public function setBanTimeLeft($ban_time_left)
	{
		$this->ban_time_left = $ban_time_left;
	}


	public function checkBan($ip, $useragent, $url)
	{
		if ($this->type == self::IP) {
			if ($this->content != $ip)
				return false;
		}
		if ($this->type == self::URL) {
			if (strpos($url, $this->content) === false)
				return false;
		}
		if ($this->type == self::USERAGENT) {
			if (strpos($useragent, $this->content) === false)
				return false;
		}
		if ($this->permanent) {
			if ($this->enable_up_to == null)
				return true;
			if ($this->enable_time_left < 0)
				return true;
		}
		if ($this->ban_time_left > 0)
			return true;
		return false;
	}

	/**
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * @param string $content
	 */
	public function setContent($content)
	{
		$this->content = $content;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/**
	 * @return \DateTime
	 */
	public function getEnableFrom()
	{
		return $this->enable_from;
	}

	/**
	 * @param \DateTime $enable_from
	 */
	public function setEnableFrom($enable_from)
	{
		$this->enable_from = $enable_from;
	}

	/**
	 * @return \DateTime
	 */
	public function getEnableUpTo()
	{
		return $this->enable_up_to;
	}

	/**
	 * @param \DateTime $enable_up_to
	 */
	public function setEnableUpTo($enable_up_to)
	{
		$this->enable_up_to = $enable_up_to;
	}

	/**
	 * @return int
	 */
	public function getEnableTimeLeft()
	{
		return $this->enable_time_left;
	}

	/**
	 * @param int $enable_time_left
	 */
	public function setEnableTimeLeft($enable_time_left)
	{
		$this->enable_time_left = $enable_time_left;
	}


}
