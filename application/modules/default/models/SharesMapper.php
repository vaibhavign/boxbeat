<?php
class Default_Model_SharesMapper
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
			$this->setDbTable('Default_Model_Shares');
		}
		return $this->_dbTable;
	}
	public function save(Default_Model_Shares $obj)
	{
		$data = array('user_id'=>$obj->getUserId(),'connection_id'=>$obj->getConnectionId(),'connection_type'=>$obj->getConnectionType(),'connection_name'=>getConnectionName(),'access_token'=>getAccessToken(),'secret_token'=>getSecretToken());
		if(null ===  ($id = $obj->getId())){
			unset($data['id']);
			$this->getDbTable()->insert($data);
		} else {
		$this->getDbTable()->update($data, array('id = ?' => $id));
 		}
	}
	public function find($id, Default_Model_Shares $obj)
	{
		 $result = $this->getDbTable()->find($id);
		 if (0 == count($result)) {
			 return;
		 }
		 $row = $result->current();
		 $obj->setId($row->id)
		 ->setUserId($row->user_id)
		 ->setConnectionId($row->connection_id)
		 ->setConnectionName($row->connection_name)
		 ->setAccessToken($row->access_token)
		 ->setSecretToken($row->secret_token);
	 }
	 public function fetchAll()
	 {
		 $resultSet = $this->getDbTable()->fetchAll();
		 $entries = array();
		 foreach ($resultSet as $row) {
		 $entry = new Default_Model_Shares();
		 $entry->setId($row->id)
		 	   ->setUserId($row->user_id)
		   	   ->setConnectionId($row->connection_id)
			   ->setConnectionName($row->connection_name)
			   ->setAccessToken($row->access_token)
			   ->setSecretToken($row->secret_token);
		 $entries[] = $entry;
	 	}
		 return $entries;
	 }
}