<?php
/**
 * User.php
 * User: Daniele Callegaro <daniele.callegaro.90@gmail.com>
 * Created: 04/02/20
 */
namespace MobileSecurity\Classes;

use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Type;

/**
 * Class User
 * @ExclusionPolicy("all")
 */
class User
{
	/**
	 * @var integer
	 * @Type("integer")
	 * @Expose
	 */
	private $id;
	/**
	 * @var string
	 * @Type("string")
	 * @Expose
	 */
	private $login;
	/**
	 * @var string
	 * @Type("string")
	 * @Expose
	 */
	private $password;
	/**
	 * @var string
	 * @Type("string")
	 * @Expose
	 */
	private $email;
	/**
	 * @var string
	 * @Type("string")
	 * @Expose
	 */
	private $displayName;
	/**
	 * @var string
	 * @Type("string")
	 * @Expose
	 */
	private $role;
	/**
	 * @var string
	 * @Type("string")
	 * @Expose
	 */
	private $nickname;

	/**
	 * @return mixed
	 */
	public function getLogin()
	{
		return $this->login;
	}

	/**
	 * @param mixed $login
	 */
	public function setLogin($login)
	{
		$this->login = $login;
	}

	/**
	 * @return mixed
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @param mixed $password
	 */
	public function setPassword($password)
	{
		$this->password = $password;
	}

	/**
	 * @return mixed
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * @param mixed $email
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}

	/**
	 * @return mixed
	 */
	public function getDisplayName()
	{
		return $this->displayName;
	}

	/**
	 * @param mixed $displayName
	 */
	public function setDisplayName($displayName)
	{
		$this->displayName = $displayName;
	}

	/**
	 * @return mixed
	 */
	public function getRole()
	{
		return $this->role;
	}

	/**
	 * @param mixed $role
	 */
	public function setRole($role)
	{
		$this->role = $role;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getNickname()
	{
		return $this->nickname;
	}

	/**
	 * @param string $nickname
	 */
	public function setNickname($nickname)
	{
		$this->nickname = $nickname;
	}




}
