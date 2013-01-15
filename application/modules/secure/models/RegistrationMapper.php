<?php
/**
 * @author : Nagendra Yadav
 * Used for doing database related operation of registration process
 * func setDbTable : used to set the default table
 * @param string $dbTable : Name of the table (user) as defined in dbTable
 * func getDbTable : used to get the default table as set using setDbTable
 * func registerUser : register a user
 * @param Secure_Model_Register $registerObj : Object of the model register class
 * func getLocationList : get all location list
 * @return array response
 * func getDepartmentList : get department list
 * @return array response
 */
include('class.sms.php');
class Secure_Model_RegistrationMapper
{
    private $_apikey;
    protected $_dbTable;
    private $_adminId;
    public $_status;
    public  $_errors;
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
	function getStateList(){
        $this->setDbTable('Secure_Model_DbTable_State');
        $db = $this->getDbTable();
        $select = $db->select('id','state_name')->where('deleted = 0 ')
                                ->order('state_name ASC');
        $result = $db->fetchAll($select);
        return $result;
    }
	function getLocationList($stateid='',$slectedcity='',$ajax=''){
	$this->setDbTable('Secure_Model_DbTable_City');	
        $db = $this->getDbTable(); 
	$select = $db->select()->where('stateid='.$stateid)
                                ->order('sort_order DESC');
        $result = $db->fetchAll($select);//print_r($result);
		$city_names='';
		$city_names='<select  class="dropDown230" name="city_name" id="city_name" >';
		$city_names .= '<option value="0" >Select city</option>';
		if(!empty($result))
		{
			foreach($result as $key=>$state){
			$city_names .= '<option value="'.$state['id'].'" ' .(($slectedcity==$state['id'])?'selected="selected"' :'').'>'.$state["cityname"].'</option>';
			}
		}
		$city_names .= '</select>';
		if($ajax)
		echo $city_names;
		else
		return $city_names;
       
    }
	
	function checkEmailIdExists($userEmail){
        $db = Zend_Db_Table::getDefaultAdapter();
	$select="select count(*) as numrec from username where name='$userEmail'";
        $query="select count(*) as numrec from user where user_email_address='$userEmail'";
        $useremailsquery="select count(*) as numrec from user_emails where user_email='$userEmail' and email_verification != '2'";
        $usermailresult = $db->fetchAll($useremailsquery);
        $result = $db->fetchAll($select);
        $userresult = $db->fetchAll($query);
        $count = $result[0]['numrec'];
        $usercount = $userresult[0]['numrec'];
        $usermailcount = $usermailresult[0]['numrec'];
         if($count > 0 || $usercount>0 || $usermailcount>0){
             echo"exist";
         } else {
             echo"notexist";
         }
    }

    function checkUsernameExists($userName){
        $db = Zend_Db_Table::getDefaultAdapter();
	$select="select count(*) as numrec from username where username='$userName'";
        $query="select count(*) as numrec from user where username='$userName'";
        $result = $db->fetchAll($select);
        $userresult = $db->fetchAll($query);
        $count = $result[0]['numrec'];
        $usercount = $userresult[0]['numrec'];
         if($count > 0 || $usercount>0){
             echo"exist";
         } else {
             echo"notexist";
         }
    }
	 function registrationFormValidation(Secure_Model_Registration $registerObj){
        $name = new Zend_Validate_NotEmpty();
        if($name->isValid($registerObj->getName())){
            $name = new Zend_Validate_StringLength(array('min' => 2));
            if($name->isValid($registerObj->getName())){
                // do nothing
            } else{
               $error['name'] = "It can't be less than 2 characters";
            }
         }else {
               $error['name'] = "It can't be blank";
         }

         $password = new Zend_Validate_NotEmpty();
        if($password->isValid($registerObj->getPassword())){
            $password = new Zend_Validate_StringLength(array('min' => 6));
            if($password->isValid($registerObj->getPassword())){
                // do nothing
            } else{
               $error['password'] = "It can't be less than 6 characters";
            }
         }else {
               $error['password'] = "It can't be blank";
         }
         
         $name = new Zend_Validate_NotEmpty();
        if($name->isValid($registerObj->getUser_full_name())){
            $name = new Zend_Validate_Alpha(array('allowWhiteSpace' => true));
            if($name->isValid($registerObj->getUser_full_name())){
                // do nothing
            } else{
               $error['name'] = "Plz enter only characters";
            }
         }else {
               $error['name'] = "It can't be blank";
         }
      $email = new Zend_Validate_NotEmpty();
      if($email->isValid($registerObj->getUser_email_address())){
      $email = new Zend_Validate_EmailAddress();
      $email->setOptions(array('domain' => false));
            if($email->isValid($registerObj->getUser_email_address())){
                // do nothing
            } else{
               $error['email'] = "Plz enter valid email id";
            }
         }else {
               $error['email'] = "It can't be blank";
         }
		if($registerObj->getRecaptcha_response_field() != ""){
			if($registerObj->getRecaptcha_challenge_field() || $registerObj->getRecaptcha_response_field())
			  {
				$resp = recaptcha_check_answer ($registerObj->getPkey(),
				$_SERVER["REMOTE_ADDR"],
				$registerObj->getRecaptcha_challenge_field(),
				$registerObj->getRecaptcha_response_field()
				);
				 if ($resp->is_valid) {
					$captcha=1;
				 }
				 else{
					$errorRecaptcha = $resp->error;
					$captcha=2;
				 }

		     }
		}

	  if($captcha == '') {
            $error['captchamsg'] = "Please fill the captcha field";

	  }
	 if($captcha == 2){
            $error['captchamsg'] = "Please fill the correct captcha.";
	  }
         
         $this->_errors = $error;
     }
  
    function saveregistration($_POST){
        $this->setDbTable('Default_Model_DbTable_Username'); // set dbtable as username
        $db = $this->getDbTable();
        //$db = Zend_Db_Table::getDefaultAdapter();
        $this->generateApiKey($lastInserted,$_POST['user_email_address'],$_POST['username']); //generate api key
        $dataUsername = array(
            'name'   => trim($_POST['user_email_address']),
            'password' => md5(trim($_POST['password'])),
            'apikey' => $this->_apikey['key'],
            'salt' => $this->_apikey['salt'],
            'username' => trim($_POST['username']));
       $db->insert($dataUsername);
		 // get the last inserted id
        $lastInserted = $db->getAdapter()->lastInsertId();
        $this->setDbTable('Secure_Model_DbTable_Register'); // set dbtable as register
        $db = $this->getDbTable();
	$location=$_POST['state_name'].','.$_POST['location'];
        $data1 = array(
            'username'   => trim($_POST['username']),
            'user_full_name' => trim($_POST['name']),
			'user_email_address' => trim($_POST['user_email_address']),
			'user_gender' => '',
			'user_dob' => '',
			'user_image' => '',
			'user_location' =>  $location,
			'user_telephone' =>'',
			'user_bio' => '',
                        'user_mobile' => trim($_POST['user_phone']),
			'email_verification' => '0',
			'uid' => $lastInserted
            );
        $db->insert($data1);
	$lastInsertedUser =$db->getAdapter()->lastInsertId();
  		$data1['lastinserted'] = $lastInsertedUser;
		$data1['detail'] =$dataUsername;
                $data1['usernameid'] =$lastInserted;
		return $data1;
    }
	
	function generateApiKey($admin_id,$email_address,$username){
	$admin_id = (int)$admin_id;
	$charactersarray = 'abcdefghijkmnopqrstuvwxyz';
	$salt1=$charactersarray[rand(0,31)];
	$md5adminid = md5(md5($admin_id).$salt1);
	$salt2=$charactersarray[rand(0,31)];
	$md5adminid = md5(md5($md5adminid.$email_address).$salt2);
	$salt3=$charactersarray[rand(0,31)];
	$md5adminid = md5(md5($md5adminid.$username).$salt3);
	$this->_apikey ['key']= $md5adminid;
	$this->_apikey ['salt']= $salt1.":".$salt2.":".$salt3;
	}
	  function insertVcode($vcode){
		$userName = new Zend_Session_Namespace('USER');
       // echo $userId = $userName->userId;exit;
        $this->setDbTable('Secure_Model_DbTable_Register'); // set dbtable as register
        $db = $this->getDbTable();
        $data2 = array('vcode'=>$vcode);
        $db->update($data2,'id = '.$userId);
    }
    function resendEmailVerification($vcode){
        $db = Zend_Db_Table::getDefaultAdapter();
        $query="select count(*) as numrec from user where vcode='$vcode' AND email_verification='0'";
        $resultuser = $db->fetchAll($query);
        return $resultuser[0]['numrec'];
       }
    function resendnewEmailVerification($vcode){
        $db = Zend_Db_Table::getDefaultAdapter();
        $query="select count(*) as numrec from user_emails where vcode='$vcode' AND email_verification='0'";
        $result = $db->fetchAll($query);
        return $result[0]['numrec'];
    }
    function updateemailstatus($vcode){
        $db = Zend_Db_Table::getDefaultAdapter();
        $uid= new Zend_Session_Namespace('USER');
        $orilogin= new Zend_Session_Namespace('original_login');
        $queryuser_email="select * from user_emails where vcode='$vcode'";
        $resultuseremails = $db->fetchAll($queryuser_email);
        $numresults=count($resultuseremails);
        if($numresults>0)
        {
            $query="update user_emails set email_verification='1' where vcode='$vcode'";
            $db->query($query);
        }
        else{
            $query="update user set email_verification='1' where vcode='$vcode'";
            $db->query($query);
            $uid->userDetails[0]['email_verification']=1;
            $orilogin->user[0]['email_verification']=1;
        }
    }
    function updatenewemailstatus($vcode){
        $db = Zend_Db_Table::getDefaultAdapter();
        $query="update user_emails set email_verification='1' where vcode='$vcode'";
        $db->query($query);
    }
    function checkEmailIdExistsforemail($email,$checkuserid){
        $db = Zend_Db_Table::getDefaultAdapter();
        $query="select count(*) as numrec from username where name='$email' AND id!='$checkuserid'";
        $result = $db->fetchAll($query);
        return $result[0]['numrec'];
        
    }
    function checkEmailforcanceluser($email){
        $db = Zend_Db_Table::getDefaultAdapter();
        $query="select * from user where user_email_address='$email'";
        $result = $db->fetchAll($query);
        $usernamequery="select * from username where name='$email'";
        $result = $db->fetchAll($usernamequery);
        return count($result);
    }
    function getusername($vcode){
        $db = Zend_Db_Table::getDefaultAdapter();
        $query="select username,user_full_name,uid,user_email_address,notmyaccount_email from user where vcode='$vcode'";
        $result = $db->fetchAll($query);
        return $result;
    }
    function getuseremailname($vcode){
        $db = Zend_Db_Table::getDefaultAdapter();
        $query="select ue.user_email from user_emails as ue where ue.vcode='$vcode'";
        $result = $db->fetchAll($query);
        return $result;
    }   
    function getuseremailsdetail($vcode){
        $db = Zend_Db_Table::getDefaultAdapter();
        $query="select ue.uid,ue.user_email,u.user_full_name from user_emails as ue inner join user as u on u.uid=ue.uid where ue.vcode='$vcode'";
        $result = $db->fetchAll($query);
        return $result;
    }
    function updateuseremail($uservcode,$usermail,$newuserid)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query="update user set user_email_address='$usermail' where vcode='$uservcode'";
        $db->query($query);
        $queryusername="update username set name='$usermail' where id='$newuserid'";
        $db->query($queryusername);
     }
     function checkcanceleduser($vcodes)
    {
       $db = Zend_Db_Table::getDefaultAdapter();
       $query="select count(*) as numrec from user where vcode='$vcodes' AND email_verification='2'";
       $result = $db->fetchAll($query);
       return $result[0]['numrec'];
     }   
    function accessusername($newuserid){
        $db = Zend_Db_Table::getDefaultAdapter();
        $query="select username from user where uid='$newuserid'";
        $result = $db->fetchOne($query);
        return $result;
    }
     function checkUsernamenumExists($userName){
       $db = Zend_Db_Table::getDefaultAdapter();
       $query="select count(*) as numrec from username where username='$userName'";
       $result = $db->fetchAll($query);
       return $result[0]['numrec']; 
    }
     
   function checkEmailIdnumExists($userEmail){
       $db = Zend_Db_Table::getDefaultAdapter();
       $query="select count(*) as numrec from username where name='$userEmail'";
       $result = $db->fetchAll($query);
       return $result[0]['numrec'];
    }

}
