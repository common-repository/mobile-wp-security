<?php
/**
 * IpName.php
 * User: Daniele Callegaro <daniele.callegaro.90@gmail.com>
 * Created: 04/02/20
 */
namespace MobileSecurity\Classes;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Type;

/**
 * Class IpName
 * @ExclusionPolicy("all")
 */
class IpName
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
	private $name;

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
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}
}
