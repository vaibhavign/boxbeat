<?php
class Secure_Model_Register
{
    protected $_username;
    protected $_password;
    protected $_user_full_name;
    protected $_user_email_address;
    protected $_user_location;
    protected $_recaptcha_response_field;
    protected $_recaptcha_challenge_field;
    protected $_pkey;
    protected $_imgName;
    protected $_shortnote;

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

     public function setUser_full_name($text)
    {

        $this->_user_full_name = (string) $text;
        return $this;
    }
    public function getUser_full_name()
    {
        return $this->_user_full_name;
    }

    public function setUser_email_address($text)
    {
        $this->_user_email_address = (string) $text;
        return $this;
    }
    public function getUser_email_address()
    {
        return $this->_user_email_address;
    }

    public function setUser_location($text)
    {
        $this->_user_location = (string) $text;
        return $this;
    }
    public function getUser_location()
    {
        return $this->_user_location;
    }

    public function setRecaptcha_response_field($text)
    {
        $this->_recaptcha_response_field = (string) $text;
        return $this;
    }
    public function getRecaptcha_response_field()
    {
        return $this->_recaptcha_response_field;
    }
    
    public function setRecaptcha_challenge_field($text)
    {
        $this->_recaptcha_challenge_field = (string) $text;
        return $this;
    }
    public function getRecaptcha_challenge_field()
    {
        return $this->_recaptcha_challenge_field;
    }

    public function setPkey($text)
    {
        $this->_pkey = (string) $text;
        return $this;
    }
    public function getPkey()
    {
        return $this->_pkey;
    }

    public function setImgName($text)
    {
        $this->_imgName = (string) $text;
        return $this;
    }
    public function getImgName()
    {
        return $this->_imgName;
    }

    public function setShortnote($text)
    {
        $this->_shortnote = (string) $text;
        return $this;
    }
    public function getShortnote()
    {
        return $this->_shortnote;
    }
   
}

