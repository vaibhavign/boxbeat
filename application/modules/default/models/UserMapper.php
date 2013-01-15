<?php
class Default_Model_UserMapper
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
			$this->setDbTable('Default_Model_DbTable_User');
		}
		return $this->_dbTable;
	}
	public function save(Default_Model_User $user)
	{
		$data = array('username'=>$user->getUsername(),'user_full_name'=>$user->getFullName(),'user_email_address'=>$user->getEmailAddress(),'user_gender'=>$user->getGender,'user_dob'=>$user->getDob(),'user_image'=>$user->getImage(),'user_location'=>$user->getLocation(),'user_telephone'=>$user->getTelephone(),'user_bio'=>$user->getBio(),'user_account_status'=>$user->getAccountStatus(),'user_join_date'=>$user->getJoinDate(),'dept_id'=>$user->getDeptId());
		if(null ===  ($id = $user->getId())){
			unset($data['id']);
			$this->getDbTable()->insert($data);
		} else {
		$this->getDbTable()->update($data, array('id = ?' => $id));
 		}
	}
	public function find($id, Default_Model_User $user)
	{
		 $result = $this->getDbTable()->find($id);
		 if (0 == count($result)) {
			 return;
		 }
		 $row = $result->current();
		 $user->setId($row->id)
		 ->setUsername($row->username)
		 ->setFullName($row->user_full_name)
		 ->setEmailAddress($row->user_email_address)
		 ->setGender($row->user_gender)
		 ->setDob($row->user_dob)
		 ->setImage($row->user_image)
		 ->setLocation($row->user_location)
		 ->setTelephone($row->user_telephone)
		 ->setBio($row->user_bio)
		 ->setAccountStatus($user->user_account_status)
		 ->setJoinDate($user->user_join_date)
		 ->setDeptId($user->dept_id);
	 }
	 public function fetchAll()
	 {
		 $resultSet = $this->getDbTable()->fetchAll();
		 $entries = array();
		 foreach ($resultSet as $row) {
		 $entry = new Default_Model_User();
		 $entry->setId($row->id)
		 	   ->setUsername($row->username)
			   ->setFullName($row->user_full_name)
			   ->setEmailAddress($row->user_email_address)
			   ->setGender($row->user_gender)
			   ->setDob($row->user_dob)
			   ->setImage($row->user_image)
			   ->setLocation($row->user_location)
			   ->setTelephone($row->user_telephone)
			   ->setBio($row->user_bio)
			   ->setAccountStatus($user->user_account_status)
			   ->setJoinDate($user->user_join_date)
		   	   ->setDeptId($user->dept_id);
		 $entries[] = $entry;
	 	}
		 return $entries;
	 }
}