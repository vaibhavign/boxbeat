<?php
class Secure_Model_CityMapper
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
            $this->setDbTable('Secure_Model_DbTable_City');
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
//foreach ($result as $row) {
//echo $row->name;	
//}
echo $count;
		// $this->getDbTable()->insert($data);
}

function getCityList(){

$db = $this->getDbTable();

$select = $db->select();
$result = $db->fetchAll($select);
return $result;
}

   /* public function find($id, Application_Model_Guestbook $guestbook)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $guestbook->setId($row->id)
                  ->setEmail($row->email)
                  ->setComment($row->comment)
                  ->setCreated($row->created);
    }*/
   /* public function fetchAll()
    {
        $resultSet = $this->getDbTable()->fetchAll();
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Application_Model_Guestbook();
            $entry->setId($row->id)
                  ->setEmail($row->email)
                  ->setComment($row->comment)
                  ->setCreated($row->created);
            $entries[] = $entry;
        }
        return $entries;
    }*/
}
