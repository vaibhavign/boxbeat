<?php
class Default_Model_Shares
{
	protected $_id;
	protected $_user_id;
	protected $_connection_id;
	protected $_connection_type;
	protected $_connection_name;
	protected $_access_token;
	protected $_secret_token;
	
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
	public function getConnectionId()
	{
		return $this->connection_id;
	}
	public function setConnectionId($value)
	{
		$this->connection_id = $value;
		return $this;
	}
	public function getConnectionType()
	{
		return $this->_connection_type;
	}
	public function setConnectionType($value)
	{
		$this->_connection_type = $value;
		return $this;
	}
	public function getConnectionName()
	{
		return $this->_connection_name;
	}
	public function setConnectionName($value)
	{
		$this->_connection_name = (string)$value;
		return $this;
	}
	public function getAccessToken()
	{
		return $this->_access_token;
	}
	public function setAccessToken($value)
	{
		$this->_access_token = (string)$value;
		return $this;
	}
	public function getSecretToken()
	{
		return $this->_secret_token;
	}
	public function setSecretToken($value)
	{
		$this->_secret_token = (string)$value;
		return $this;
	}
}