<?php
class Test_Model_Department
{
	protected $_id;
	protected $_dept_name;
	protected $_image_class;
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
	public function getDeptName()
	{
		return $this->_dept_name;
	}
	public function setDeptName($value)
	{
		$this->_dept_name = (string)$value;
		return $this;
	}
	public function getImageClass()
	{
		return $this->_image_class;
	}
	public function setImageClass($value)
	{
		$this->_image_class = (string)$value;
		return $this;
	}
	/********************************************************************/
	
	
}