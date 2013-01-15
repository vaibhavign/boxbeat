<?php
class Default_Model_Followers
{
	protected $_id;
	protected $_user_id;
	protected $_followers;
	protected $_follow_date;
	
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
		$this->_user_id = (string)$value;
		return $this;
	}
	public function getFollowers()
	{
		return $this->_followers;
	}
	public function setFollowers($value)
	{
		$this->_followers = (string)$value;
		return $this;
	}
	public function getFollowDate()
	{
		return $this->_follow_date;
	}
	public function setFollowDate($value)
	{
		$this->_follow_date = (string)$value;
		return $this;
	}
	/********************************************************************/
	
	
}