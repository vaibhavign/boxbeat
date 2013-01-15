<?php
class Secure_Model_LoginMapper
{
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
   function checkUsernameExists($userName){
       $db = Zend_Db_Table::getDefaultAdapter();
       $query="select * from username where username='$userName'";
       $result = $db->fetchAll($query);
       return count($result); 
    }
     
   function checkEmailIdExists($userEmail){
       $db = Zend_Db_Table::getDefaultAdapter();
       $query="select * from username where name='$userEmail'";
       $result = $db->fetchAll($query);
       return count($result);
    }
    function checkusrverification($vcodes){
        $db = Zend_Db_Table::getDefaultAdapter();
        $uid= new Zend_Session_Namespace('USER');
        $usrmail=$uid->userDetails[0]['user_email_address'];
        $usrjoindate=$uid->userDetails[0]['user_join_date'];
        $date_diff= time() - strtotime(date('d-m-Y',strtotime($usrjoindate)));
        $datediff=floor($date_diff/(60 * 60 * 24));
        $query="select * from user where vcode='$vcodes' AND email_verification='0'";
        $result = $db->fetchAll($query);
        $verifyusernum=count($result);
        if(($verifyusernum>0) && $datediff>=5)
        {
            return '0';
        }
        else
        {
            return '1';
        }
  
    }
     function checkusrverificationinprocess($usrvcode,$doj){
          $db = Zend_Db_Table::getDefaultAdapter();
          $usrjoindate=$doj;
          $date_diff= time() - strtotime(date('d-m-Y',strtotime($usrjoindate)));
          $datediff=floor($date_diff/(60 * 60 * 24));
            $query="select * from user where vcode='$usrvcode' AND email_verification='0'";
            $result = $db->fetchAll($query);
            $verifyusernum=count($result);
            if(($verifyusernum>0) && $datediff>=5)
            {
                return '0';
            }
            else
            {
                return '1';
            }
    }

    function updateSessionlifetime()
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->query("update session set lifetime='".(86400*7)."' where session_id='".Zend_Session::getId()."'");
    }
    function removeemail($vcodes)
    {
       $db = Zend_Db_Table::getDefaultAdapter();
       $query="select id,user_email_address,username from user where vcode='$vcodes'";
       $result = $db->fetchRow($query); 
       $useremail= $result['user_email_address'];
       $userid= $result['id'];
       $username= $result['username'];
       if($userid == '')
       {
           $db->query("update user_emails set email_verification='2' where vcode='$vcodes'");
       }
       else{
           $db->query("update user_role set email_id='$username' where email_id='$useremail'");
           $db->query("update user set email_verification='2',notmyaccount_email='$useremail',user_email_address='deletedid-".$userid."@delete.com' where vcode='$vcodes'");
           $db->query("update username set notmyaccount_email='$useremail',name='deletedid-".$userid."@delete.com' where id='$userid'");
           //$db->query("update user_emails set email_verification='2' where user_email='$useremail'");
           $db->query("delete from session where session_id='".Zend_Session::getId()."'");
		$db->query("delete from session_loged_store where session_id='".Zend_Session::getId()."'");
		$user=new Zend_Session_Namespace('USER');
		$db->query("delete from session_loged_store where session_id='".$user->userId."'");
		Zend_Session::destroy();
       }
       
    }
    function checknotmyaccount($vcodes,$email,$usenamerid,$premail)
    {
       $db = Zend_Db_Table::getDefaultAdapter();
       $db->query("update user_role set email_id='$email' where email_id='$premail'");
       $db->query("update user set user_email_address='$email',email_verification='0' where vcode='$vcodes'");
       $db->query("update username set name='$email' where id='$usenamerid'");
       $db->query("update user_emails set user_email='$email' where user_email='$premail' and email_verification='3'");
     }
      function checkcanceleduser($vcodes)
    {
       $db = Zend_Db_Table::getDefaultAdapter();
       $query="select * from user where vcode='$vcodes' AND email_verification='2'";
       $result = $db->fetchAll($query);
       return count($result);
     }
    function getvcodesusername($username)
     {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query="select u.id,u.vcode,u.user_join_date,u.user_full_name,u.user_email_address,u.forgetpasscode,un.active from user as u inner join username as un on u.uid=un.id where u.username='$username'";
        $result = $db->fetchAll($query);
        return $result;
      }
 
  function getvcodesemail($email)
      {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query="select u.id,u.vcode,u.user_join_date,u.user_full_name,u.forgetpasscode,un.active from user as u inner join username as un on u.uid=un.id where un.name='$email'";
        $result = $db->fetchAll($query);  
        return $result; 
      }
    function getdetailpasscode($getuservcode)
      {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query="select forgetpasscode,uid,user_full_name,user_email_address from user where vcode='$getuservcode'";
        $result = $db->fetchRow($query);
        return $result;
      }
     function updateforgetpasstatus($getuservcode,$newemailpasscode)
      {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->query("update user set forgetpasscode='$newemailpasscode' where vcode='$getuservcode'");
      }
      function updateusenamepassword($usernameid,$newpassword,$code_str,$posteduservcode)
      {
        $db = Zend_Db_Table::getDefaultAdapter();
        $securepassword=md5($newpassword);
        $db->query("update username set password='$securepassword' where id='$usernameid'");
        $db->query("update user set forgetpasscode='$code_str' where vcode='$posteduservcode'");
      }  
      function checkUserfollowed($userapikey,$storeapikey)
      {
        $db = Zend_Db_Table::getDefaultAdapter();
      //  echo "select * from store_follow_custome where capikey='".$userapikey."' and uapikey='".$storeapikey."' and deleted_flag='0'";exit;
        $selectQuery=$db->query("select * from store_follow_customer where capikey='".$userapikey."' and sapikey='".$storeapikey."' and deleted_flag='0'");
        $data=$selectQuery->fetchAll();
        if(empty($data))
        {
         $db->query("insert into store_follow_customer set capikey='".$userapikey."',sapikey='".$storeapikey."',folowing='0',follow_time=".time()."");     
          return true; 
        }
        else {
                return false;
             }
        } 
        
        function getmalldetail($sapi)
      {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query="select title,mallurl from mall_detail where apikey='$sapi'";
        $result = $db->fetchRow($query);
        return $result;
      }
      function getexistcustomer($sapi,$capi)
      {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query="select * from store_follow_customer where sapikey='$sapi' AND capikey='$capi'";
        $result = $db->fetchAll($query);
        return count($result);
      }
      function addnewcustomer($sapi,$capi)
      {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->query("insert into store_follow_customer set capikey='".$capi."',sapikey='".$sapi."',folowing='1',follow_time=".time()."");     
      }
      function removeuseremails($vcode)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->query("update user_emails set email_verification='2' where vcode='$vcode'");       
    }
        
}

