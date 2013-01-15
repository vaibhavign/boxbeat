<?php
class Api_Model_Signin
{
    protected $_username;
    protected $_password;

    public function __construct(array $options = null)
    {	
        if (is_array($options)) {
            $this->setOptions($options);
			//print_r($this->setOptions($options));
        }
    }
    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid guestbook property');
        }
        $this->$method($value);
    }
    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid guestbook property');
        }
        return $this->$method();
    }
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
		//print_r($methods);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }
	
	public function setUsername($text)
    {
        $this->_username = (string) $text;
        return $this;
    }
    public function getUsername()
    {
        return $this->_username;
    }
	
	
	
    public function setPassword($text)
    {
        $this->_password = (string) $text;
        return $this;
    }
    public function getPassword()
    {
        return $this->_password;
    }
   
}

