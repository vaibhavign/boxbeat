<?php
class Default_Model_Import_ContactsMapper
{
    protected $_dbTable;
	public function __construct(array $options = null)
    {
    }
    public function setDbTable($dbTable)
	{
		if(is_string($dbTable)){
			$dbTable = new $dbTable();
		}
		if(!$dbTable instanceof Zend_Db_Table_Abstract){
			throw new Exception("Invalid Table Gateway Provided.");
		}
		$this->_dbTable = $dbTable;
		return $this;
	}
	public function getDbTable()
	{
		if(null === $this->_dbTable){
			$this->setDbTable('Default_Model_Import_Contacts');
		}
		return $this->_dbTable;
	}
	public function save(Default_Model_Import_Contacts $obj)
	{
		$data = array('user_id'=>$obj->getUserId(),'email_address'=>$obj->getEmailAddress(),'status'=>$obj->getStatus());
		if(null ===  ($id = $obj->getId())){
			unset($data['id']);
			$this->getDbTable()->insert($data);
		} else {
		$this->getDbTable()->update($data, array('id = ?' => $id));
 		}
	}
	public function find($id, Default_Model_Import_Contacts $obj)
	{
		 $result = $this->getDbTable()->find($id);
		 if (0 == count($result)) {
			 return;
		 }
		 $row = $result->current();
		 $obj->setId($row->id)
		 ->setUserId($row->user_id)
		 ->setEmailAddress($row->email_address)
		 ->setStatus($row->status);
	 }
	 public function fetchAll()
	 {
		 $resultSet = $this->getDbTable()->fetchAll();
		 $entries = array();
		 foreach ($resultSet as $row) {
		 $entry = new Default_Model_Import_Contacts();
		 $entry->setId($row->id)
		 	   ->setUserId($row->user_id)
			   ->setEmailAddress($row->email_address)
		       ->setStatus($row->status);
		 $entries[] = $entry;
	 	}
		 return $entries;
	 }
}