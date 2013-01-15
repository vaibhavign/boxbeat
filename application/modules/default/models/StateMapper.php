<?php
class Default_Model_StateMapper
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
			$this->setDbTable('Default_Model_DbTable_State');
		}
		return $this->_dbTable;
	}
	public function save(Default_Model_State $state)
	{
		$data = array('state_name'=>$state->getStateName(),'country_id'=>$state->getCountryId());
		if(null ===  ($id = $state->getId())){
			unset($data['id']);
			$this->getDbTable()->insert($data);
		} else {
		$this->getDbTable()->update($data, array('id = ?' => $id));
 		}
	}
	public function find($id, Default_Model_State $state)
	{
		 $result = $this->getDbTable()->find($id);
		 if (0 == count($result)) {
			 return;
		 }
		 $row = $result->current();
		 $state->setId($row->id)
		 ->setStateName($row->state_name)
		 ->setCountryId($row->country_id);
	}
	 public function fetchAll()
	 {
		 $resultSet = $this->getDbTable()->fetchAll();
		 $entries = array();
		 foreach ($resultSet as $row) {
		 $entry = new Default_Model_State();
		 $entry->setId($row->id)
		 	   ->setStateName($row->state_name)
			   ->setCountryId($row->country_id);
		 $entries[] = $entry;
	 	}
		 return $entries;
	 }
}