<?php
class Restriction_Controller_Plugin_Param extends Zend_Controller_Plugin_Abstract
{
   public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
		$order_id = $this->_request->getParams();
		$abc = new General();
		if($order_id['action']!='index'){
		if($order_id['module']=='admin'){
		if($order_id['controller']!='overview'){
		  $link = "/".$order_id['module']."/".$order_id['controller']."/#".$order_id['action'];
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$orderSql = "select * from menu where trim(link)='$link'";
        $result = $db->query($orderSql);
        $resultSet = $result->fetchAll();
		//echo $resultSet[0]['module_id']."------".$resultSet[0]['action_id']; exit;
		//echo $_SESSION['USER']['stores'][0]['apikey'] ;
		//echo $_SESSION['USER']['stores'][0]['apikey'] ;
		if($_SESSION['USER']['stores'][0]['apikey'] =='a229eb0209ba474500023fc377879d40'){
		if(sizeof($resultSet)==1){
		if($abc->checkUserModuleActionPermission($_SESSION['USER']['stores'][0]['apikey'],$_SESSION['USER']['userDetails'][0]['user_email_address'],$resultSet[0]['module_id'],$resultSet[0]['action_id'])){
		} else {
			//echo $_SESSION['USER']['stores'][0]['apikey']."------".$_SESSION['USER']['userDetails'][0]['user_email_address'].'----'.$resultSet[0]['module_id'].'-----'.$resultSet[0]['action_id']; exit;
			echo "<script> $('.bbq-content').html('');  window.location = '/admin/overview/#page'; </script>";
			return false;
			
		}
		}
		}
		}
		}
		}
		
    }
 
}

?>