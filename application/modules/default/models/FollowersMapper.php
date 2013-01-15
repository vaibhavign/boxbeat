<?php
class Default_Model_FollowersMapper
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
			$this->setDbTable('Default_Model_DbTable_Followers');
		}
		return $this->_dbTable;
	}
	public function save(Default_Model_Followers $followers)
	{
		$data = array('user_id'=>$followers->getUserId(),'followers'=>$followers->getFollowers(),'follow_date'=>$followers->getFollowDate());
		if(null ===  ($id = $followers->getId())){
			unset($data['id']);
			$this->getDbTable()->insert($data);
		} else {
		$this->getDbTable()->update($data, array('id = ?' => $id));
 		}
	}
	public function find($id, Default_Model_Followers $followers)
	{
		 $result = $this->getDbTable()->find($id);
		 if (0 == count($result)) {
			 return;
		 }
		 $row = $result->current();
		 $followers->setId($row->id)
		 ->setUserId($row->user_id)
		 ->setFollowers($row->followers)
		 ->setFollowDate($row->follow_date);
	 }
	 public function fetchAll()
	 {
		 $resultSet = $this->getDbTable()->fetchAll();
		 $entries = array();
		 foreach ($resultSet as $row) {
		 $entry = new Default_Model_Followers();
		 $entry->setId($row->id)
		 	   ->setUserId($row->user_id)
			   ->setFollowers($row->followers)
			   ->setFollowDate($row->follow_date);
		 $entries[] = $entry;
	 	}
		 return $entries;
	 }
}