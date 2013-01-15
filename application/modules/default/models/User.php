<?php
class Default_Model_User
{
	protected $_id;
	protected $_username;
	protected $_user_full_name;
	protected $_user_email_address;
	protected $_user_gender;
	protected $_user_dob;
	protected $_user_image;
	protected $_user_location;
	protected $_user_telephone;
	protected $_user_bio;
	protected $_user_account_status;
	protected $_user_join_date;
	protected $_dept_id;
	public function __construct(array $options = null)
    {
		 if (is_array($options)) {
		 $this->setOptions($options);
		 }
    }
	public function setOptions(array $options)
	{
		$methods = get_class_methods($this);
	 	foreach ($options as $key => $value) {
			$method = 'set' . ucfirst($key);
			if (in_array($method, $methods)) {
				$this->$method($value);
			}
	 	 }
	 	 return $this;
	 }
	public function __set($name,$value)
	{
		$method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid property');
        }
        $this->$method($value);
	}
	public function __get($name)
	{
		$method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid property');
        }
        return $this->$method();
	}
	public function getId()
	{
		return $this->_id;
	}
	public function setId($value)
	{
		$this->_id = (int)$value;
		return $this;
	}
	public function getUsername()
	{
		return $this->_username;
	}
	public function setUsername($value)
	{
		$this->_username = (string)$value;
		return $this;
	}
	public function getFullName()
	{
		return $this->_user_full_name;
	}
	public function setFullName($value)
	{
		$this->_user_full_name = (string)$value;
		return $this;
	}
	public function getEmailAddress()
	{
		return $this->_user_email_address;
	}
	public function setEmailAddress($value)
	{
		$this->_user_email_address = (string)$value;
		return $this;
	}
	public function getGender()
	{
		return $this->_user_gender;
	}
	public function setGender($value)
	{
		$this->_user_gender = (string)$value;
		return $this;
	}
	public function getDob()
	{
		return $this->_user_dob;
	}
	public function setDob($value)
	{
		$this->_user_dob = (string)$value;
		return $this;	
	}
	public function getImage()
	{
		return $this->_user_image;
	}
	public function setImage($value)
	{
		$this->_user_image =(string)$value;
		return $this;
	}
	public function getLocation()
	{
		return $this->_user_location;
	}
	public function setLocation($value)
	{
		$this->_user_location = $value;
		return $this;
	}
	public function getTelephone()
	{
		return $this->_user_telephone;
	}
	public function setTelephone($value)
	{
		$this->_user_telephone = (string)$value;
		return $this;
	}
	public function getBio()
	{
		return $this->_user_bio;
	}
	public function setBio($value)
	{
		$this->_user_bio = (string)$value;
		return $this;
	}
	public function getAccountStatus()
	{
		return $this->_user_account_status;
	}
	public function setAccountStatus($value)
	{
		$this->_user_account_status = $value;
		return $this;
	}
	public function getJoinDate()
	{
		return $this->_user_join_date;
	}
	public function setJoinDate($value)
	{
		$this->_user_join_date = $value;
		return $this;
	}
	public function getDeptId()
	{
		return $this->_dept_id;
	}
	public function setDeptId($value)
	{
		$this->_dept_id = $value;
		return $this;
	}
	/********************************************************************/
	
	
}