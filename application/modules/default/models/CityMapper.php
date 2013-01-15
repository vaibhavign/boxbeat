<?php
class Default_Model_CityMapper
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
			$this->setDbTable('Default_Model_DbTable_Department');
		}
		return $this->_dbTable;
	}
	public function save(Default_Model_City $city)
	{
		$data = array('city_name'=>$city->getCityName(),'state_id'=>$city->getSteteId());
		if(null ===  ($id = $city->getId())){
			unset($data['id']);
			$this->getDbTable()->insert($data);
		} else {
		$this->getDbTable()->update($data, array('id = ?' => $id));
 		}
	}
	public function find($id, Default_Model_City $city)
	{
		 $result = $this->getDbTable()->find($id);
		 if (0 == count($result)) {
			 return;
		 }
		 $row = $result->current();
		 $city->setId($row->id)
		 ->setCityName($row->city_name)
		 ->setStateId($row->state_id);
	 }
	 public function fetchAll()
	 {
		 $resultSet = $this->getDbTable()->fetchAll();
		 $entries = array();
		 foreach ($resultSet as $row) {
		 $entry = new Default_Model_City();
		 $entry->setId($row->id)
		 	   ->setCityName($row->city_name)
			   ->setStateId($row->state_id);
		 $entries[] = $entry;
	 	}
		 return $entries;
	 }
}