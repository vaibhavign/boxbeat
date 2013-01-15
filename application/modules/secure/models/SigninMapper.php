<?php
class Api_Model_SigninMapper
{
    protected $_dbTable;
    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Api_Model_DbTable_Signin');
        }
		
        return $this->_dbTable;
    }
    public function save(Api_Model_Signin $guestbook)
    {
		//echo $guestbook->getUsername();
		

        $data = array(
            'name'   => $guestbook->getUsername(),
            'password' => $guestbook->getPassword()
        );
	
$db = $this->getDbTable();

$select = $db->select()->where('name = ? ', $data['name'])
						->where('password = ? ', $data['password']);
$result = $db->fetchAll($select);
$count = count($result);
echo $count;
		
}




}
