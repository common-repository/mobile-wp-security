<?php
/**
 * IpLog.php
 * User: Daniele Callegaro <daniele.callegaro.90@gmail.com>
 * Created: 04/02/20
 */

namespace MobileSecurity\Classes;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Type;

/**
 * Class IpLog
 * @ExclusionPolicy("all")
 */
class IpLog
{
	/**
	 * @var string
	 * @Type("string")
	 * @Expose
	 */
	private $ip;
	/**
	 * @var string
	 * @Type("string")
	 * @Expose
	 */
	private $useragent;
	/**
	 * @var \DateTime
	 * @Type("DateTime")
	 * @Expose
	 */
	private $time;
	/**
	 * @var integer
	 * @Type("integer")
	 * @Expose
	 */
	private $timeago;
	/**
	 * @var string
	 * @Type("string")
	 * @Expose
	 */
	private $url;

	/**
	 * @var float
	 * @Type("float")
	 * @Expose
	 */
	private $timing;

	/**
	 * @var integer
	 * @Type("integer")
	 * @Expose
	 */
	private $status;

	/**
	 * @var integer
	 * @Type("integer")
	 * @Expose
	 */
	private $memory;

	/**
	 * @var string
	 * @Type("string")
	 * @Expose
	 */
	private $referer;


	/**
	 * @var string
	 * @Type("string")
	 * @Expose
	 */
	private $loggedAs;

	/**
	 * @var string
	 * @Type("string")
	 * @Expose
	 */
	private $error;

	/**
	 * @var string
	 * @Type("string")
	 * @Expose
	 */
	private $file;

	/**
	 * @var integer
	 * @Type("integer")
	 * @Expose
	 */
	private $line;

	/**
	 * @var integer
	 * @Type("integer")
	 * @Expose
	 */
	private $type;

	/**
	 * @return string
	 */
	public function getIp()
	{
		return $this->ip;
	}

	/**
	 * @param string $ip
	 */
	public function setIp($ip)
	{
		$this->ip = $ip;
	}

	/**
	 * @return string
	 */
	public function getUseragent()
	{
		return $this->useragent;
	}

	/**
	 * @param string $useragent
	 */
	public function setUseragent($useragent)
	{
		$this->useragent = $useragent;
	}

	/**
	 * @return \DateTime
	 */
	public function getTime()
	{
		return $this->time;
	}

	/**
	 * @param \DateTime $time
	 */
	public function setTime($time)
	{
		$this->time = $time;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @param string $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}

	/**
	 * @return int
	 */
	public function getTimeago()
	{
		return $this->timeago;
	}

	/**
	 * @param int $timeago
	 */
	public function setTimeago($timeago)
	{
		$this->timeago = $timeago;
	}

	/**
	 * @return float
	 */
	public function getTiming()
	{
		return $this->timing;
	}

	/**
	 * @param float $timing
	 */
	public function setTiming($timing)
	{
		$this->timing = $timing;
	}

	/**
	 * @return int
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @param int $status
	 */
	public function setStatus($status)
	{
		$this->status = $status;
	}

	/**
	 * @return int
	 */
	public function getMemory()
	{
		return $this->memory;
	}

	/**
	 * @param int $memory
	 */
	public function setMemory($memory)
	{
		$this->memory = $memory;
	}

	/**
	 * @return string
	 */
	public function getReferer()
	{
		return $this->referer;
	}

	/**
	 * @param string $referer
	 */
	public function setReferer($referer)
	{
		$this->referer = $referer;
	}

	/**
	 * @return string
	 */
	public function getLoggedAs()
	{
		return $this->loggedAs;
	}

	/**
	 * @param string $loggedAs
	 */
	public function setLoggedAs($loggedAs)
	{
		$this->loggedAs = $loggedAs;
	}

	/**
	 * @return string
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * @param string $error
	 */
	public function setError($error)
	{
		$this->error = $error;
	}

	/**
	 * @return string
	 */
	public function getFile()
	{
		return $this->file;
	}

	/**
	 * @param string $file
	 */
	public function setFile($file)
	{
		$this->file = $file;
	}

	/**
	 * @return int
	 */
	public function getLine()
	{
		return $this->line;
	}

	/**
	 * @param int $line
	 */
	public function setLine($line)
	{
		$this->line = $line;
	}

	/**
	 * @return int
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param int $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}
}
