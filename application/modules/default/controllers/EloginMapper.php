<?php
class Secure_Model_EloginMapper
{
	function checkexistingUser($username,$password)
		{
			//echo $username."--".$password;
			 $adapter = $this->_getAuthAdapter();
			 $adapter->setIdentity($username); 
			 $adapter->setCredential($password);
			 $auth = Zend_Auth::getInstance();
			
			
             $result = $auth->authenticate($adapter);
             echo '<pre>';
             print_r($result);
 exit;         

				if ($result->isValid()) {

					$user = $adapter->getResultRowObject();

					$auth->getStorage()->write($user);

					return true;

				}

        return false;
		}
	protected function _getAuthAdapter()
		{

        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
			$authAdapter->setTableName('username')
			->setIdentityColumn('name')
			->setCredentialColumn('password')
			->setCredentialTreatment('md5');
			return $authAdapter;

       }
    function getLoggedUserDetails() {
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
        $query="select u.id,u.vcode,u.user_join_date,u.user_full_name,u.user_email_address,u.forgetpasscode,un.active from user as u inner join username as un  where un.name='$username'";
        $result = $db->fetchAll($query);
        return $result;
      }   
    
}

?>
