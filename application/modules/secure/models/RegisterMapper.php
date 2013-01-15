<?php
/**
 * @author : vaibhav sharma
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
class Secure_Model_RegisterMapper
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
    

    function getLocationList(){
        $this->setDbTable('Secure_Model_DbTable_Cities');
        $db = $this->getDbTable();
        $select = $db->select()->where('displaystatus = 1 ')
                                ->order('sort_order DESC');
        $result = $db->fetchAll($select);
        return $result;
    }

    function getDepartmentList(){
        $this->setDbTable('Secure_Model_DbTable_Department');
        $db = $this->getDbTable();
        $select = $db->select();
        $result = $db->fetchAll($select);
        return $result;
    }

    // core function to register a user
    function registerUser(Secure_Model_Register $registerObj){
        $this->setDbTable('Secure_Model_DbTable_Register'); // set dbtable as register
        $db = $this->getDbTable();
        $data1 = array(
            'username'   => $registerObj->getUsername(),
            'user_full_name' => $registerObj->getUser_full_name(),
            'user_email_address' => $registerObj->getUser_email_address(),
            'user_location' => $registerObj->getUser_location(),);
        $db->insert($data1);
        // get the last inserted id
        $lastInserted = $db->getAdapter()->lastInsertId();
        $this->generateApiKey($lastInserted); //generate api key

        // insert into table username

        $this->setDbTable('Default_Model_DbTable_Username'); // set dbtable as username
        $db = $this->getDbTable();
        $data = array(
            'name'   => $registerObj->getUser_email_address(),
            'password' => md5($registerObj->getPassword()),
            'apikey' => $this->_apikey,
            'username' => $registerObj->getUsername(),);

        $db->insert($data);
        $data1['lastinserted'] = $lastInserted;
        return $data1;
    }

    function checkEmailIdExists($userEmail){
        $this->setDbTable('Default_Model_DbTable_Username');
        $db = $this->getDbTable();
        $select = $db->select()->where('name = ? ',trim($userEmail));
        $result = $db->fetchAll($select);
        $count = count($result);
         if($count > 0){
             echo"exist";
         } else {
             echo"notexist";
         }
    }
    
    function checkUsernameExists($userName){
        
        $this->setDbTable('Default_Model_DbTable_Username');
        $db = $this->getDbTable();
        $select = $db->select()->where('username = ? ',$userName);
        $result = $db->fetchAll($select);
        $count = count($result);
         if($count > 0){
             echo"exist";
         } else {
             echo"notexist";
         }
    }


    function generateApiKey($admin_id){
	$admin_id = (int)$admin_id;
	$strinrarray[] ='';
	$cubeOfId = ($admin_id) * ($admin_id) *($admin_id);
	$randomData = mt_rand(100000,1000000);
	$addBothData =(int)$cubeOfId + (int)$randomData;
	$md5SecuredData = md5((int)$addBothData);
	for ($k = 0; $k <5; $k++) {
	 $stringval = mt_rand(0, 31);
	if(!in_array($stringval,$strinrarray)){

		$dataArray.=$md5SecuredData[$stringval];
		$strinrarray[$k] = $stringval;
		}else{
			$k = $k-1;
		}
	}
	$md5OfSelectedData = md5($dataArray);
	$postionsSeprated = implode(',',$strinrarray);
	$getSingleRandom = mt_rand(1,18); //store in admin_key table for single random character
	$getSubstrOfData = substr($md5OfSelectedData,0,$getSingleRandom);
	$md5OfSubString = md5($getSubstrOfData); //admin_key
	$this->_apikey = $md5OfSubString;
	}

     function registrationFormValidation(Secure_Model_Register $registerObj){
        $username = new Zend_Validate_NotEmpty();
        if($username->isValid($registerObj->getUsername())){
            $username = new Zend_Validate_StringLength(array('min' => 3));
            if($username->isValid($registerObj->getUsername())){
                // do nothing
            } else{
               $error['username'] = "It can't be less than 3 characters";
            }
         }else {
               $error['username'] = "It can't be blank";
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

       //	$registerObj->getRecaptcha_response_field();
      //  $registerObj->getRecaptcha_challenge_field();
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

     function insertProfileData(Secure_Model_Register $registerObj){
        $userId = $_SESSION['USER']['userId'];
        $this->setDbTable('Secure_Model_DbTable_Register'); // set dbtable as register
        $db = $this->getDbTable();
        $userImage = $registerObj->getImgName();
        $userBio = $registerObj->getShortnote();
        $data2 = array('user_image'=>$userImage,'user_bio'=>$userBio);
        $db->update($data2,'id = '.$userId);

     }
    function subscribeUnsubs(){
        $userId = $_SESSION['USER']['userId'];
        $this->setDbTable('Default_Model_DbTable_Userdept'); // set dbtable as register
        $db = $this->getDbTable();
     if($_POST['subscribe']>0){
        $deptId = $_POST['subscribe'];
        $data = array(
            'user_id'   => $userId,
            'dept_id' => $deptId ,);

        $db->insert($data);
         
     } else {
        $deptId = $_POST['unsubscribedata'];
         $db->delete('user_id = '.$userId." and dept_id = ".$deptId);
      
         }
     }

    function selectUserDepartment($userid){
        $this->setDbTable('Default_Model_DbTable_Userdept'); // set dbtable as register
        $db = $this->getDbTable();
        $select = $db->select()->where('user_id = '.$userid);
        $result = $db->fetchAll($select);
        return $result;
    }

    function sendVerificationSms($data){
        $userId = $_SESSION['USER']['userId'];
        $mobileno = trim($_POST['mobile']);
	$verification_code = rand(111111,999999);
        $this->setDbTable('Default_Model_DbTable_Mobverification'); // set dbtable as register
        
        $db = $this->getDbTable();
       	$message_text = "Hi , Your iglobul mobile service activation code is ".$verification_code.". Confirm this to activate.";
        // check if any records exists in mobile verificatikn if true then update the correspondinf record
        $select = $db->select()->where('customer_id = '.$userId);
        $result = $db->fetchAll($select);
        $count = count($result); // count number of record
        $mobile_verification_data = array(
            'customer_id'=>$userId,
            'message'=>$message_text,
            'messageId'=>'',
            'messagestatus'=>'',
            'sent_date'=>mktime(),
            'verification_code'=>$verification_code,
            'mobile_no'	=>$mobileno,);
        if($count==0){ // if zero i.e no records found
            // insert into the table mobile verification
           
           $db->insert($mobile_verification_data);
        } else {
            // update the mobile number and message
           $db->update($mobile_verification_data,'customer_id = '.$userId);
        }
        $this->setDbTable('Default_Model_DbTable_Sms');
        $db = $this->getDbTable();
        $insertmessage = array('mobile_no' => $mobileno,
                                'message' => $message_text,
                                'a_c_id' => $userId,
                                'a_c_type' => '2',
                                'message_time' => time(),
                                'status' => '1',
                                'priority_sms' => '0',);
        $db->insert($insertmessage);
        $lastInserted = $db->getAdapter()->lastInsertId();
        $message = new sms();
        $message->msisdn = $mobileno;
        $message->message = urlencode($message_text);
		$message->sendSms();
        $updateMessage = array('status' => '2');
        $db->update($updateMessage,'id = '.$lastInserted);
     }
     // velidate the verification code
    function verifySmsVerificationCode($data){

        $userId = $_SESSION['USER']['userId'];
        $confirmcode=$data['confirmcode'];
        $this->setDbTable('Default_Model_DbTable_Mobverification'); // set dbtable as register
        $db = $this->getDbTable();

        // check if any records exists in mobile verificatikn if true then update the correspondinf record
        $select = $db->select()->where('customer_id = '.$userId)
                                ->where('verification_code = '.$confirmcode);
        $result = $db->fetchAll($select);
    
        foreach($result as $ass){
            $databaseconfirmcode=array('vode'=>$ass->verification_code,
                                       'mobile'=>$ass->mobile_no);
        }

        $error=0;
        if($databaseconfirmcode['vode']!=$confirmcode){
            $error=1;
            echo "1";
        }else{
            echo "2";
        }

        if($error==0){
         
            $updateMobileVerification = array('messagestatus' => 'verified');
            $db->update($updateMobileVerification,'customer_id = '.$userId);
            $this->setDbTable('Default_Model_DbTable_User'); // set dbtable as register
            $db = $this->getDbTable();
            //update mobile number of the user
            $updateUserMobile = array('user_mobile' => $databaseconfirmcode['mobile']);
            $db->update($updateUserMobile,'id = '.$userId);
            $this->setDbTable('Default_Model_DbTable_Sms');
            $db = $this->getDbTable();
            $message_text = 'Congrats! Your mobile is activated for iglobul updates.';
            $insertConfirmMessage = array('mobile_no' => $databaseconfirmcode['mobile'],
                                            'message' => $message_text,
                                            'a_c_id' => $userId,
                                            'a_c_type' => '2',
                                            'message_time' => time(),
                                            'status' => '1'
                                    );
            $lastInserted = $db->getAdapter()->lastInsertId();
            $message = new sms();
            $message->msisdn = $databaseconfirmcode['mobile'];
            $message->message = urlencode($message_text);
			$message->sendSms();
            $updateMessage = array('status' => '2');
            $db->update($updateMessage,'id = '.$lastInserted);
        }
    }

    function resendEmailVerification($data,$user){
       $userDetails = getLoggedUserDetails('id',$user);
       
       if($data['email']==$userDetails[0]['user_email_address']){
           // do nothing
           // send user email
           //echo "doing nothing";
       } else {
           // update user table
           // send user email
            $this->setDbTable('Default_Model_DbTable_User'); // set dbtable as register
            $db = $this->getDbTable();
            $updateUserEmail = array('user_email_address'=>$data['email']);
            $db->update($updateUserEmail,'id = '.$user);
         
       }
    }
    
}
