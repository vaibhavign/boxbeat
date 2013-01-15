<?php
class Default_Model_Classes_Setting{
	
	protected $currentUserData;
	protected $updateUserData;
	protected $currentpasswordValidate;
	protected $selectExistUser;
	protected $selectExistUserEmail;
	protected $selectExistOldPassword;
	protected $resendMailData;
	
	public function __construct($user_id)
	{
		$this->currentUserData = array();
		$this->currentpasswordValidate = array();
		$this->selectExistUser=array();
		$this->selectExistUserEmail=array();
		$this->selectExistOldPassword=array();
		$this->resendMailData=array();
		
	}
	
	public function __get($request)
	{
		return $this->$request;
	}
	
	public function getDetailsFromUser($userid)
	{
		$sql = "SELECT  u.*,un.* FROM user u INNER JOIN username un  ON (u.id=un.id) WHERE u.id='".$userid."'";
		$row = DataRender::getQuery($sql);
		$this->currentUserData = $row;
		}
		
	public function getValidatePassword($userid,$currentPassword)
	{
		$sql = "SELECT  count(*) as count FROM username un WHERE un.id='".$userid."' AND un.password='".md5($currentPassword)."' ";
		$row = DataRender::getQuery($sql);
		$this->currentpasswordValidate = $row[0]['count'];
		
		}
		
	public function checkExistUserName($username)
	{
		$sql = "select count(*) as total from user where username = '".$username."'";
		$row = DataRender::getQuery($sql);
		$this->selectExistUser = $row[0]['total'];
		}	
	
	public function checkExistUserEmail($useremail)
	{
		$sql = "select count(*) as total from user where user_email_address = '".$useremail."'";
		$row = DataRender::getQuery($sql);
		$this->selectExistUserEmail = $row[0]['total'];
		}
		
	public function checkOldPassword($customerId,$password)
	{
		$sql = "select password from username where id='".$customerId."'";
		$row = DataRender::getQuery($sql);

	if($row[0]['password'] == md5($password))
		$this->selectExistOldPassword = "valid";
	else
		$this->selectExistOldPassword = "invalid";
	
		}	
		
	public function resendMailSend($emailid,$customerid){
		
		$sql = "SELECT count(*) as recordcount FROM user WHERE user_email_address='$emailid' AND id!='$customerid'";
		$row = DataRender::getQuery($sql);
		$this->resendMailData = $row[0]['recordcount'];
		
		}
		
	}

?>