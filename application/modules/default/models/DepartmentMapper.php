<?php
class Default_Model_DepartmentMapper
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
	public function save(Default_Model_Department $department)
	{
		$data = array('dept_name'=>$department->getDeptName(),'image_class'=>$department->getImageClass());
		if(null ===  ($id = $department->getId())){
			unset($data['id']);
			$this->getDbTable()->insert($data);
		} else {
		$this->getDbTable()->update($data, array('id = ?' => $id));
 		}
	}
	public function find($id, Default_Model_Department $department)
	{
		 $result = $this->getDbTable()->find($id);
		 if (0 == count($result)) {
			 return;
		 }
		 $row = $result->current();
		 $department->setId($row->id)
		 ->setDeptName($row->dept_name);
		 ->setImageClass($row->image_class);
	 }
	 public function fetchAll()
	 {
		 $resultSet = $this->getDbTable()->fetchAll();
		 $entries = array();
		 foreach ($resultSet as $row) {
		 $entry = new Default_Model_Department();
		 $entry->setId($row->id)
		 	   ->setDeptName($row->dept_name)
			   ->setImageClass($row->image_class);
		 $entries[] = $entry;
	 	}
		 return $entries;
	 }
}