<?php
class Secure_Model_AccountsettingMapper
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
    function getuserstate($getuserstatecode)
      {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query="select state_name from state where id='$getuserstatecode'";
        $result = $db->fetchOne($query);
        return $result;
      }
      function updateusercontact($newcontact)
      {
        $uid= new Zend_Session_Namespace('USER');
        $uOri= new Zend_Session_Namespace('original_login');
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->query("update user set user_mobile='$newcontact' where id='$uOri->userId'");
        $uid->userDetails[0]['user_mobile']=$newcontact;
        $uOri->user[0]['user_mobile']=$newcontact;
      }
      function checkuserpass($currentpass,$newpassword){
        $uid= new Zend_Session_Namespace('original_login');
        $db = Zend_Db_Table::getDefaultAdapter();
        $getpassword=md5($currentpass);
        $getnewpassword=md5($newpassword);
	$select="select * from username where id='$uid->userId' and password='$getpassword'";
        $userresult = $db->fetchAll($select);
        $count = count($userresult);
             if($count > 0){
             $currenttime=time();
             $db->query("update username set password='$getnewpassword' where id=".$uid->user[0]['uid']);
             $db->query("update user set updated_date='$currenttime' where uid='$uid->userId'");
             $uid->user[0]['updated_date']=$currenttime;
             echo 0;
             exit;
         } else {
             echo 1;
             exit;
         }
    }
    function updateusername($currentpass,$username){
        $uid= new Zend_Session_Namespace('USER');
        $uOri= new Zend_Session_Namespace('original_login');
        $db = Zend_Db_Table::getDefaultAdapter();
        $select="select * from username where username='$username'";
        $query="select * from user where username='$username'";
        $result = $db->fetchAll($select);
        $userresult = $db->fetchAll($query);
        $usernamecount = count($result);
        $usercount = count($userresult);
        $getpassword=md5($currentpass);
        $newusename=$username;
	$select="select * from username where id='".$uOri->user[0]['uid']."' and password='$getpassword'";
        $userresult = $db->fetchAll($select);
        $count = count($userresult);
        if($usernamecount>0 || $usercount>0)
        {
                    echo 2;
                     exit;
        }
       
              else if($count > 0){                
                     $currenttime=time();                   
                     $db->query("update username set username='$username' where id='".$uOri->user[0]['uid']."'");
                     $db->query("update user set updatedusername_time='$currenttime' , username='$username' where id='$uOri->userId'");                     
                     $uid->userDetails[0]['username']=$username;
                     $uid->userDetails[0]['updatedusername_time']=$currenttime;
                     $uOri->user[0]['username']=$username;
                     $uOri->user[0]['updatedusername_time']=$currenttime;
                     echo 0;
                     exit;
                   }
              
                else {
                    echo 1;
                    exit;
                  }
    }
    function checkUsernameExists($userName){
       $db = Zend_Db_Table::getDefaultAdapter();
	$select="select * from username where username='$userName'";
        $query="select * from user where username='$userName'";
        $result = $db->fetchAll($select);
        $userresult = $db->fetchAll($query);
        $count = count($result);
        $usercount = count($userresult);
         if($count > 0 || $usercount>0){
             echo 1;
             exit;
         } else {
             echo 0;
             exit;
         }
    }
    function checkUseremailExists($userEmail){
        $db = Zend_Db_Table::getDefaultAdapter();
	$select="select * from username where name='$userEmail'"; 
        $query="select * from user where user_email_address='$userEmail'";
  
        $useremailsquery="select * from user_emails where user_email='$useremail' and (email_verification!='2' and email_verification!='3')";
        $result = $db->fetchAll($select);
        $userresult = $db->fetchAll($query);
        $useremails = $db->fetchAll($useremailsquery);
        $count = count($result);
        $usercount = count($userresult);
        $useremailscount = count($useremails);
         if($count > 0 || $usercount>0 || $useremailscount>0){
             echo 1;
             exit;
         } else {
             echo 0;
             exit;
         }
  
    }
    function updateuserinfo($fullusername,$newlocation,$fileimage)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $uid= new Zend_Session_Namespace('USER');
        $uOri= new Zend_Session_Namespace('original_login');
        $newfileimage=$fileimage;
        $db->query("update user set user_full_name='$fullusername',user_location='$newlocation',user_image='$newfileimage' where id='$uOri->userId'");
        if($newfileimage != 'no_image.jpg')
        {
                $Genobj=new General();
                $fixedsizeimgurl=$Genobj->getuserimage($uOri->userId,100,100);
                move_uploaded_file($fixedsizeimgurl,"images/secure/user_image/".$uOri->userId.'/'.$uOri->userId.'_'.$newfileimage);
        }
        $uOri->user[0]['user_full_name']=$fullusername;
        $uid->userDetails[0]['user_full_name']=$fullusername;
        $uOri->user[0]['user_location']=$newlocation;
        $uid->userDetails[0]['user_location']=$newlocation;
        $uOri->user[0]['user_image']=$newfileimage;
        $uid->userDetails[0]['user_image']=$newfileimage;
        //echo "<pre>";
       // print_r($_SESSION);
       // exit;
        
    }
     function removeuserimage()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $uid= new Zend_Session_Namespace('original_login');
        $newfileimage='no_image.jpg';
        $db->query("update user set user_image='$newfileimage' where uid='$uid->userId'");
        $uid->user[0]['user_image']=$newfileimage;
    }
    function addnewemail($currentpass,$useremail){
        $class=new General();
        $this->objTrigger=new Notification();
        $uOri= new Zend_Session_Namespace('original_login');
        $db = Zend_Db_Table::getDefaultAdapter();
        $getpassword=md5($currentpass);
	$select="select * from username where id='$uOri->userId' and password='$getpassword'";
        $userresult = $db->fetchAll($select);
        $countrecord = count($userresult);
        $vcodes =  $this->makenewUrl($uOri->userId);
        $currenttime=time();
        $select="select * from username where name='$useremail'"; 
        $query="select * from user where user_email_address='$useremail'";
        $useremailsquery="select * from user_emails where user_email='$useremail' and (email_verification!='2' and email_verification!='3')";
        $result = $db->fetchAll($select);
        $userresult = $db->fetchAll($query);
        $useremails = $db->fetchAll($useremailsquery);
        $count = count($result);
        $usercount = count($userresult);
        $useremailscount = count($useremails);
         if($count > 0 || $usercount>0 || $useremailscount>0){
             echo 2;
             exit;
         } 
             if($countrecord > 0){
             
             $data = array(
            'uid'   => $uOri->userId,
            'user_email' => $useremail,
            'vcode'=>$vcodes,
            'email_verification'=>0,
            'new_email_date'=>$currenttime);
            $db->insert(user_emails,$data);
                    $getuservcode=$vcodes;
                    $email=$useremail;
                    $fullusername=$uOri->user[0]['user_full_name'];;
                    $vcode = HTTPS_SECURE.'/registration/newmailconfirmation/passcode/'.$getuservcode;
                    $notmyaccountlink = HTTPS_SECURE.'/login/notyournewaccount/passcode/'.$getuservcode;
                    mail('nagendra.lnct@gmail.com','verifymail',"$vcode"."$notmyaccountlink");
                              $tData = array('link'=>$vcode,
                              'notmyaccountlinks'=> $notmyaccountlink,
                              'name'=>$fullusername,
                              'to_id'=>$email);                           
                               $this->objTrigger->triggerFire(37,$tData);
                    echo 0;exit;
                   }
            else {
                    echo 1;
                    exit;
                  }
    }
    function getallemails()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $orilogin = new Zend_Session_Namespace('original_login');
 	$select="select * from user_emails where uid='$orilogin->userId' and email_verification!='2' and email_verification!='3'";        
        $useremails = $db->fetchAll($select);;
        return $useremails;
    }
    function removeuseremails($removeuseremailid)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->query("update user_emails set email_verification='2' where id='$removeuseremailid'");       
    }
        function getuseremailsdetail($useremailid)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $uid= new Zend_Session_Namespace('original_login');
 	$select="select * from user_emails where id='$useremailid'";
        $useremailsdetail = $db->fetchRow($select);;
        return $useremailsdetail;
    }
    function makeuseremailprimary($useremailid)
    {
        //echo $useremailid;exit;
        $db = Zend_Db_Table::getDefaultAdapter();
        $uid= new Zend_Session_Namespace('USER');
        $uOri= new Zend_Session_Namespace('original_login');
        $useremaildetais=$this->getuseremailsdetail($useremailid);//details of emailid to be primary
        $previousemail=$uOri->user[0]['user_email_address'];
        $previousemail_verification=$uOri->user[0]['email_verification'];
        $previousvcode=$uOri->user[0]['vcode'];
        $newuseremail=$useremaildetais['user_email'];
        $email_verification=$useremaildetais['email_verification'];
        $useremailvcode=$useremaildetais['vcode'];
        $userid=$uOri->userId;
        $emailid=$useremaildetais['id'];
        mail('nagendra.lnct@gmail.com','testquery', "update user_emails set user_email='$previousemail',email_verification='$previousemail_verification',vcode='$previousvcode' where id='$emailid'".'</n>'."update user_role  set email_id='$newuseremail' where email_id='$previousemail'".'</n>'."update user set user_email_address='$newuseremail',email_verification='1',vcode='$useremailvcode' where id='$userid'".'</n>'."update username set name='$newuseremail' where id='$userid'");
        $db->query("update user_emails set user_email='$previousemail',email_verification='$previousemail_verification',vcode='$previousvcode' where id='$emailid'");
        $db->query("update user_role  set email_id='$newuseremail' where email_id='$previousemail'");
        $db->query("update user set user_email_address='$newuseremail',email_verification='1',vcode='$useremailvcode' where id='$userid'");
        $db->query("update username set name='$newuseremail' where id='$userid'");
        return 1;
    }
     function lastupdatedtime()
    {
       
       $orilogin = new Zend_Session_Namespace('original_login');
       $timestamp=$orilogin->user[0]['updated_date'];
		$sec= time()-$timestamp;
		$min=round($sec/60);
		$hr=round($sec/3600);
		$day=round($hr/24);
                $month=round($day/30);
                $year=round($month/12);
                if($timestamp==0){
                $result="Password never been updated";
                }
		elseif($sec<60){
			$result='Updated few second ago';
		}
                elseif($sec==60){
			$result='Updated a minute ago';
		}
		elseif (($sec > 60) && ($min < 60)){
			$result='Updated few minute ago';
		}
                 elseif($min==60){
			$result='Updated an hour ago';
		}
		elseif(($min>60) && ($hr < 24)){
			$result='Updated few hours ago';
		}
		elseif($hr >= 24 && ($day < 30)){
			$result='Updated few days ago';
		}
                elseif($day >= 30 && ($month < 12)){
			$result='Updated few months ago';
		}
                else
                {
                    $result='Updated few years ago';
                }
		return $result;
	}
        function getverifiedemail()
        {
             $db = Zend_Db_Table::getDefaultAdapter();
             $uid= new Zend_Session_Namespace('original_login');
             $select="select * from user_emails where uid='".$uid->userId."' and email_verification='0'";
             $useremails = $db->fetchAll($select);
             return count($useremails);
        }
       
    function makenewUrl($customer_id){
	
	$db = Zend_Db_Table::getDefaultAdapter();
	$length = 6;
	$string = "";
	$characters = 'abcdefghijkmnopqrstuvwxyz';
	for ($p = 0; $p < $length; $p++) {
		$string .= $characters[mt_rand(0, strlen($characters))];
	}	
	$len= 4;
	$charactersarray = 'abcdefghijkmnopqrstuvwxyz';
	$stringval = "";
	for ($k = 0; $k < $length; $k++) {
		$stringval .= $charactersarray[mt_rand(0, strlen($charactersarray))];
	}	
	$numeric = "023456789";
	$lennum = 6;
	$stringvalue = "";
	for ($i = 0; $i <$lennum; $i++) {
		$stringvalue .= $numeric[mt_rand(0, strlen($numeric))];
	}
	$vcode = $string.$customer_id.'a'.$stringval.$stringvalue;
	return $vcode;
  }
  function getStateList(){
        $this->setDbTable('Secure_Model_DbTable_State');
        $db = $this->getDbTable();
        $select = $db->select()->where('deleted = 0 ')
                                ->order('state_name ASC');
        $result = $db->fetchAll($select);
        return $result;
    } 
}

