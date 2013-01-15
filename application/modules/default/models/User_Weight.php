<?php
class Default_Model_User_Weight
{
	protected $_id;
	protected $_user_id;
	protected $_weight_type;
	protected $_weight;
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
	public function getWeightType()
	{
		return $this->_weight_type;
	}
	public function setWeightType($value)
	{
		$this->_weight_type = (string)$value;
		return $this;
	}
	public function getWeight()
	{
		return $this->_weight;
	}
	public function setWeight($value)
	{
		$this->_weight = $value;
		return $this;
	}
	/********************************************************************/
	
	
}