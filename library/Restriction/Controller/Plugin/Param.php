<?php
class Restriction_Controller_Plugin_Param extends Zend_Controller_Plugin_Abstract
{
	

	
	
   public function postDispatch(Zend_Controller_Request_Abstract $request)
    {

		$order_id = $this->_request->getParams();
		//if($order_id['action']=='basicinfo'){echo "<pre>";print_r($order_id);exit;}
		$abc = new General();
		if($order_id['action']!='index'){
		if($order_id['module']=='admin'){
		 $checkDemoVal = $abc->checkDemoUserValidity($_SESSION['USER']['stores'][0]['apikey']);
		if($checkDemoVal == 0){
			echo "<script>window.location='".HTTP_SERVER . "/myaccount/demo/#trial-expire'</script>";
			exit;
		}
		if($order_id['controller']!='overview'){
		  $link = "/".$order_id['module']."/".$order_id['controller']."/#".$order_id['action'];

		$db = Zend_Db_Table::getDefaultAdapter();
		$orderSql = "select * from menu where trim(link)='$link'";
        $result = $db->query($orderSql);
        $resultSet = $result->fetchAll();
		//echo $_SESSION['USER']['stores'][0]['apikey'] ;
		//echo $resultSet[0]['module_id']."------".$resultSet[0]['action_id']; exit;
		if($_SESSION['USER']['stores'][0]['role']==4){
		if(sizeof($resultSet)==1){
		
		if($abc->checkUserModuleActionPermission($_SESSION['USER']['stores'][0]['apikey'],$_SESSION['USER']['userDetails'][0]['user_email_address'],$resultSet[0]['module_id'],$resultSet[0]['action_id']) || $order_id['productid']>0 || $order_id['catid']>0 || $order_id['brandid']>0 || $order_id['featureid']>0 || $order_id['groupid']>0 || $order_id['variationid']>0 ||$order_id['id']>0 || $order_id['coupon_id']>0){
		} else {
			//echo $_SESSION['USER']['stores'][0]['apikey']."------".$_SESSION['USER']['userDetails'][0]['user_email_address'].'----'.$resultSet[0]['module_id'].'-----'.$resultSet[0]['action_id']; exit;
			echo "<script> $('.bbq-content').html('');  window.location = '/admin/overview/#accessdenied'; </script>";
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
