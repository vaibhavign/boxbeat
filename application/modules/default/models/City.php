<?php
class Test_Model_City
{
	protected $_id;
	protected $_city_name;
	protected $_state_id;
	
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
	public function getCityName()
	{
		return $this->_city_name;
	}
	public function setCityName($value)
	{
		$this->_city_name = (string)$value;
		return $this;
	}
	public function getStateId()
	{
		return $this->_state_id;
	}
	public function setStateId($value)
	{
		$this->_state_id = (string)$value;
		return $this;
	}
	/********************************************************************/
	
	
}