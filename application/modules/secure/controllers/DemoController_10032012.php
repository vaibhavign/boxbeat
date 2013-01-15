<?php
class Secure_DemoController extends Zend_Controller_Action{
	protected $_user_api_key;
	protected $_store_api_key;
	protected $_user_name;
	private $objTrigger;
	
	private $db;
    public function init(){
		$_SESSION['mypage']=HTTP_SECURE . '/demo/create-demo-store';
        Zend_Layout::getMvcInstance()->setLayout('secure');
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');
		$this->view->headLink()->appendStylesheet('/css/secure/secure.css');
		$this->view->controller='demo';
		$this->_user_name = new Zend_Session_Namespace('original_login');
        $current_store = new Zend_Session_Namespace('USER');
        $this->_store_api_key=$current_store->stores['0']['store_apikey'];
		$this->_user_api_key = $_SESSION['original_login']['apikey'];
		$this->objTrigger = new Notification();
		$this->view->headScript()->appendFile('/jscript/common/jquery-1.4.2.min.js');
    }

    public function indexAction(){
		//$this->_redirect(HTTP_SECURE . '/demo/create-demo-store');
	}
	public function createDemoStoreAction(){
		$this->view->headTitle('Welcome : create a  GMS demo store - goo2o.com');
		$flag = 1;
		$merchant_mapper  = new Secure_Model_MerchantenrollmentMapper($this->_user_api_key); // object for Business mapper class
		if($_SESSION['original_login']['userId']!=''){
			$merchant_mapper->checkUserMall();
			$get_count_id = $merchant_mapper->__get('_mall_count_value');
		}else{
			$get_count_id = 0;
		}
		if($get_count_id < 1){
			if($_POST['action'] == 'process'){ // condition for save the data and check session data on back button
				if($_POST['domain_name']!=''){ // condition for check store url exist
					$merchant_mapper->checkStoreUrl(trim($_POST['domain_name']));
					$get_total_count = $merchant_mapper->__get('_storeurl_count_value');
					if($get_total_count > 0){
						$this->view->url_error_msg = 'Domain name already exist.';
						$this->view->url_msg_display = 'display:block;';
						$flag = 0;
					}
				}// condition end for check store url exist
				$this->view->store_name = ($_POST['store_name'])?$_POST['store_name']:'';
				$this->view->domain_name = ($_POST['domain_name'])?$_POST['domain_name']:'';
				$this->view->contact_no = ($_POST['contact_no'])?$_POST['contact_no']:'';
				$mall_url = 'http://www.'.strtolower(trim($this->view->domain_name)).'.mygoo2o.com';
				$insert_data_array = array('business_name'=>'','business_type'=>'','business_address'=>'','city'=>'','state'=>'','pincode'=>'','title'=>trim(addslashes($this->view->store_name)),'mallurl'=>$mall_url,'user_id'=>'','primary_industry'=>'','source_area'=>'','active'=>'1','parent_store'=>'0','store_owner_type'=>'1','create_date'=>time());
				if($flag !=0){
					$_SESSION['DEMO_STORE_DATA'] = $insert_data_array;
					$_SESSION['DEMO_FLAG'] = 2;
					$_SESSION['DEMO_MAGANE_FLAG'] == 3;
					$this->_redirect(HTTP_SECURE.'/demo/congratulation');
				}
			}// condition end for save the data 
		}else{
				$this->_redirect(HTTP_SECURE . '/demo/store-exists');
		}//End if condition for check user already exist or not	
	}
	public function congratulationAction(){
		if($this->_user_name->userId == ''){
			$this->_redirect(HTTP_SECURE . '/login');
		}
		$this->view->headTitle('Congratulations: your GMS demo store is created - goo2o.com');
		$merchant_mapper  = new Secure_Model_MerchantenrollmentMapper($this->_user_api_key); // object for Business mapper class
		$merchant_mapper->checkUserMall();
		$get_count_id = $merchant_mapper->__get('_mall_count_value');
		if($get_count_id < 1){
			if($_SESSION['DEMO_FLAG'] == 2){
				$_SESSION['DEMO_STORE_DATA']['user_id'] = $_SESSION['original_login']['userId'];
				$merchant_mapper->getAndUpdateDemoMallDetail($this->_user_api_key,$_SESSION['DEMO_STORE_DATA'],$_SESSION['original_login']['user'][0]['user_email_address']);
				$demoapikey = $merchant_mapper->__get('_demoapikey');
				$_SESSION['DEMOAPIKEY'] = $demoapikey;
				$_SESSION['USER']['userDetails'][0]['title'] = stripslashes($_SESSION['DEMO_STORE_DATA']['title']);
				$_SESSION['USER']['userDetails'][0]['mallurl'] = $_SESSION['DEMO_STORE_DATA']['mallurl'];
				//Insert In User_role ANd user_permission Table
				$get_last_id  = $_SESSION['original_login']['userId'];
				$merchant_mapper->mallOwnerRolePermission($this->_user_api_key,$_SESSION['original_login']['user'][0]['user_email_address']);
				$mapper=new Admin_Model_IndexMapper();
				$mapper->changeUserprofile($_SESSION['DEMOAPIKEY'],$get_last_id);
				$this->view->mallurl = $_SESSION['USER']['stores'][0]['mallurl'];
				$_SESSION['original_login']['user'][0]['store_owner_type'] = 1;
				$_SESSION['DEMO_STORE_DATA'] = '';
				$_SESSION['DEMO_FLAG'] = '';
				$_SESSION['DEMOAPIKEY'] = '';
				/*----@Trigger NO: 14 @created by : Mukesh Bisht @date : 12-11-2011 -------------*/
					  $tData = array(  'control_panel_url'=>'http://goo2ostore.com/admin/overview/#page',
									   'store_url'=>$_SESSION['USER']['stores'][0]['mallurl'],
									   'to_id'=>$_SESSION['USER']['userId'],
									   'to_mail'=>$_SESSION['USER']['stores'][0]['email_id'],
									   'to_name'=>$_SESSION['original_login']['user'][0]['user_full_name']);
					  $this->objTrigger->triggerFire(14,$tData);
				/*---------------End HERE----------------------------*/
				/*----@Trigger NO: 13 @created by : Mukesh Bisht @date : 12-11-2011 -------------*/
					  $tData = array(  'to_id'=>$_SESSION['USER']['userId'],
									   'to_mail'=>$_SESSION['USER']['stores'][0]['email_id'],
									   'to_name'=>$_SESSION['original_login']['user'][0]['user_full_name']);
					  $this->objTrigger->triggerFire(13,$tData);
				/*---------------End HERE----------------------------*/		
			}else{
				 $this->_redirect(HTTP_SECURE.'/demo/create-demo-store');
			}//End if condition for check demo flag session exist or not
		}else{
				$this->_redirect(HTTP_SECURE . '/demo/store-exists');
		}//End if condition for check user already exist or not

    }
	public function storeExistsAction(){
		if($this->_user_name->userId == ''){
			$this->_redirect(HTTP_SECURE . '/login');
		}
		$merchant_mapper  = new Secure_Model_MerchantenrollmentMapper($this->_user_api_key); // object for Business mapper class
		$merchant_mapper->userDetails();
		$this->view->mall_detail = $merchant_mapper->__get('_user_details_array');
		if($this->view->mall_detail[0]['store_owner_type']==1)
			$this->view->headTitle('Sorry: your demo store is active-goo2o.com');
		else
			$this->view->headTitle('Sorry: you have a regular store-goo2o.com');
			
		$_SESSION['DEMO_STORE_DATA'] = '';
		$_SESSION['DEMO_FLAG'] = '';
		$_SESSION['DEMOAPIKEY'] = '';
	}
}
?>
