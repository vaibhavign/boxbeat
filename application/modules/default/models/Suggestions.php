<?php
class Default_Model_Suggestions
{
	protected $_id;
	protected $_user_id;
	protected $_suggested_id;
	protected $_suggestion_date;
	protected $_suggestion_status;
	
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
	public function getSuggestedId()
	{
		return $this->_suggested_id;
	}
	public function setSuggestedId($value)
	{
		$this->_suggested_id = $value;
		return $this;
	}
	public function getSuggestionDate()
	{
		return $this->_suggestion_date;
	}
	public function setSuggestionDate($value)
	{
		$this->_suggestion_date = $value;
		return $this;
	}
	public function getSuggestionStatus()
	{
		return $this->_suggestion_status;
	}
	public function setSuggestionStatus($value)
	{
		$this->_suggestion_status = $value;
		return $this;
	}
	
}