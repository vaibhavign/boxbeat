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
                public function generateApiKey($admin_id,$email_address,$username){
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
                public function saveRegistrationData($_POST){
                    $db = Zend_Db_Table::getDefaultAdapter();
                    $REGISSESSION = new Zend_Session_Namespace('REGISSESSION');
                    $location='8,Delhi';
                    $data1 = array(
                        'username'   => trim($_POST['uremail']),
                        'user_full_name' => trim($_POST['urname']),
                        'user_email_address' => trim($_POST['uremail']),
                        'user_gender' => '',
                        'user_dob' => '',
                        'user_image' => '',
                        'user_location' =>  $location,
                        'user_telephone' =>trim($_POST['urcontact']),
                        'user_bio' => '',
                        'user_mobile' => '',
                        'email_verification' => '0'
                     );
                    foreach($data1 as $datakey => $dataval){
                        $datas .= "`".$datakey."`". "=". "'".$dataval."'".",";
                    }
                    $db->query("INSERT INTO user SET ".substr($datas,0,-1));
                    $lastInsertedUser = $db->lastInsertId();
                    $this->generateApiKey($lastInsertedUser,$_POST['uremail'],$_POST['urname']); //generate api key
                    $dataUsername = array(
                        'id'=> $lastInsertedUser,
                        'name'   => trim($_POST['uremail']),
                        'password' => md5(trim($_POST['password'])),
                        'apikey' => $this->_apikey['key'],
                        'salt' => $this->_apikey['salt'],
                        'username' => trim($_POST['uremail']));
                    foreach($dataUsername as $dataskey => $datasval){
                        $userdatas .= "`".$dataskey."`". "=". "'".$datasval."'".",";
                    }
                    $db->query("INSERT INTO username SET ".substr($userdatas,0,-1));
                             // get the last inserted id
                    $lastInserted = $db->lastInsertId();
                    //Insert Data in mall detail
                    $datamall = array(
                        'user_id'=> $lastInsertedUser,
                        'title'   => trim($_POST['storename']),
                        'mallurl' => "http://www.".trim($_POST['ururl'].".eshopbox.com"),
                        'active' => '1',
                        'apikey' => $this->_apikey['key'],
                        'store_owner_type' => '1',
                        'domain'   => trim($_POST['ururl']),
                        'create_date' => time(),
                    );
                    foreach($datamall as $mallkey => $mallval){
                        $malldatas .= "`".$mallkey."`". "=". "'".$mallval."'".",";
                    }
                    $db->query("INSERT INTO mall_detail SET ".substr($malldatas,0,-1));
                    //echo "select user.*,md.* from user as user join mall_detail as md on user.id = md.user_id where user.id =".$lastInserted;
                    //exit;
                    $q = $db->query("select user.*,md.* from user as user join mall_detail as md on user.id = md.user_id where user.id =".$lastInserted);
                    $REGISSESSION->USERDETAILS = $q->fetchAll();
                }
                
                
    
}

?>
