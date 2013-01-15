<?php
class PhoneNumber extends Zend_Validate_Abstract
{
const PHONE_BAD_CHARS = 'phoneBadChars';
const PHONE_BAD_LENGTH = 'phoneBadLength';

private $_allowedCharacters = array('1','2','3','4','5','6','7','8','9','0');
private $_separators = array('-','/','.');

protected $_messageTemplates = array(
self::PHONE_BAD_CHARS => 'Phone numbers can contain digits 0-9 and characters ". / -"',
self::PHONE_BAD_LENGTH => "'%value%' is not a valid North American phone number",
);

public function isValid($value)
{
$valueString = (string) $value;
$this->_setValue($valueString);
$valArray = str_split($valueString);

foreach($valArray as $char)
{
if(!in_array($char, $this->_allowedCharacters) && !in_array($char, $this->_separators))
{
$this->_error(self::PHONE_BAD_CHARS);
return false;
}
}
$countStr = str_replace($this->_separators, '', $valueString);
$len = strlen($countStr);
if($len != 10 && $len != 11)
{
$this->_error(self::PHONE_BAD_LENGTH);
return false;
}
return true;
}
}

?> 