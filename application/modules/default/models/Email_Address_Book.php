<?php
class Default_Model_Email_Address_Book
{
	protected $_id;
	protected $_user_id;
	protected $_email_address;
	protected $_status;
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
	public function getUserId()
	{
		return $this->_user_id;
	}
	public function setUserId($value)
	{
		$this->_user_id = $value;
		return $this;
	}
	public function getEmailAddress()
	{
		return $this->_email_address;
	}
	public function setEmailAddress($value)
	{
		$this->_email_address = (string)$value;
		return $this;
	}
	public function getStatus()
	{
		return $this->_status;
	}
	public function setStatus($value)
	{
		$this->_status = $value;
		return $this;
	}
	/********************************************************************/
	
	
}