<?php
class Secure_MerchantenrollmentController extends Zend_Controller_Action{
	protected $_user_api_key;
	protected $_store_api_key;
	protected $_user_name;
	private $objTrigger;
    public function init(){
        Zend_Layout::getMvcInstance()->setLayout('secure');
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');
		$this->view->headLink()->appendStylesheet('/css/secure/secure.css');
		$this->view->controller='merchantenrollment';
		$this->_user_name = new Zend_Session_Namespace('original_login');
        $current_store = new Zend_Session_Namespace('USER');
        $this->_store_api_key=$current_store->stores['0']['store_apikey'];
		$this->_user_api_key = $_SESSION['original_login']['apikey'];
		$this->objTrigger = new Notification();
		if($this->_user_name->userId == ''){
			$this->_redirect(HTTP_SECURE . '/login');
		}
		$this->view->headScript()->appendFile('/jscript/common/jquery-1.4.2.min.js');
    }

    public function indexAction(){
		$this->_redirect(HTTP_SECURE . '/merchantenrollment/business-details');
    }
    public function businessDetailsAction(){
		$this->view->headScript()->appendFile('/jscript/secure/business-details.js','text/javascript');
		$this->view->headTitle('Business Details: Fill your business information - Goo2o.com');
		$this->view->action_class = 'bussinessDetailsNavImg';
		$business_array = array('1'=>'Partnership','2'=>'Sole Proprietorship','3'=>'Private limited','4'=>'Public limited');
		$this->view->business_type_array = $business_array;
		$mapper  = new Secure_Model_RegistrationMapper(); // object for register mapper class
		$returnedStateArray = $mapper->getStateList(); // getting State list
		$this->view->states=$returnedStateArray; 
		$this->view->city=$mapper->getLocationList(0,'');
		if($_SESSION['original_login']['user'][0]['store_owner_type']=='1'){
			$this->view->business_name = stripslashes($_SESSION['original_login']['user'][0]['business_name']);
			$this->view->business_type = $_SESSION['original_login']['user'][0]['business_type'];
			$this->view->business_address = $_SESSION['original_login']['user'][0]['business_address'];
			$this->view->select_state = $_SESSION['original_login']['user'][0]['state'];
			$state_id = $_SESSION['original_login']['user'][0]['state'];
			$city_id = $_SESSION['original_login']['user'][0]['city'];
			$this->view->city = $mapper->getLocationList($state_id,$city_id);
			$this->view->pincode = $_SESSION['original_login']['user'][0]['pincode'];;
			$this->view->store_name = $_SESSION['original_login']['user'][0]['title'];;
			preg_match('@^(?:http://www.)?([^.*]+)@i',$_SESSION['original_login']['user'][0]['mallurl'], $mallurl);
			$this->view->store_url = $mallurl[1];
			$this->view->readonly = 'readonly="readonly"';
		}
		$merchant_mapper  = new Secure_Model_MerchantenrollmentMapper($this->_user_api_key); // object for Business mapper class
		$flag_value = $this->_request->getParam('flag');
		$err_flag = 1;
		if($flag_value > 0){
			$_SESSION['CHECKFLAG']['flag'] = $flag_value;
			$this->_redirect(HTTP_SECURE . '/merchantenrollment/business-details');
		}
		if($_POST['action'] == 'process' || $_SESSION['CHECKFLAG']['flag'] == 1){ // condition for save the data and check session data on back button
			$users_details = array();
			if($_SESSION['CHECKFLAG']['flag'] == 1){
				$users_details = $_SESSION['ENROLLMENT'];
			}
			if($_POST['store_name']!=''){ // condition for check store name exist
				$merchant_mapper->checkStoreName(trim($_POST['store_name']));
				$get_total_count = $merchant_mapper->__get('_storename_count_value');
				if($get_total_count > 0 && $_SESSION['original_login']['user'][0]['store_owner_type'] =='0'){
					$this->view->error_msg = 'Storename already exist.';
					$this->view->display = 'display:block;';
					$err_flag = 0;
				}
			}// condition  end for check store name exist
			if($_POST['store_url']!=''){ // condition for check store url exist
				$merchant_mapper->checkStoreUrl(trim($_POST['store_url']));
				$get_total_count = $merchant_mapper->__get('_storeurl_count_value');
				if($get_total_count > 0 && $_SESSION['original_login']['user'][0]['store_owner_type'] =='0'){
					$this->view->url_error_msg = 'Url already exist.';
					$this->view->url_msg_display = 'display:block;';
					$err_flag = 0;
				}
			}// condition end for check store url exist
			$this->view->business_name = ($_POST['business_name'])?$_POST['business_name']:$users_details['business_name'];
			$this->view->business_type = ($_POST['business_type'])?$_POST['business_type']:$users_details['business_type'];
			$this->view->business_address = ($_POST['business_address'])?$_POST['business_address']:$users_details['business_address'];
			$this->view->select_state = ($_POST['state'])?$_POST['state']:$users_details['state'];
			$state_id = ($_POST['state'])?$_POST['state']:$users_details['state'];
			$city_id = ($_POST['city_name'])?$_POST['city_name']:$users_details['city'];
			$this->view->city = $mapper->getLocationList($state_id,$city_id);
			$this->view->pincode = ($_POST['pincode'])?$_POST['pincode']:$users_details['pincode'];
			$this->view->store_name = ($_POST['store_name'])?$_POST['store_name']:$users_details['title'];
			preg_match('@^(?:http://www.)?([^.*]+)@i',$users_details['mallurl'], $mallurl);
			$this->view->store_url = ($_POST['store_url'])?$_POST['store_url']:$mallurl[1];
			
			$mall_url = 'http://www.'.strtolower(trim($this->view->store_url)).'.mygoo2o.com';
			$user_id = $_SESSION['original_login']['userId'];
			$insert_data_array = array('business_name'=>trim(addslashes(stripslashes($this->view->business_name))),'business_type'=>$this->view->business_type,'business_address'=>trim(addslashes($this->view->business_address)),'city'=>$city_id,'state'=>$state_id,'pincode'=>trim($this->view->pincode),'title'=>trim(addslashes(stripslashes($this->view->store_name))),'mallurl'=>$mall_url,'user_id'=>$user_id,'create_date'=>time());
			$_SESSION['ENROLLMENT'] = $insert_data_array;
			if($err_flag != 0){
				$_SESSION['CHECKFLAG']['flag'] = 2;
				$this->_redirect(HTTP_SECURE . '/merchantenrollment/payment-details');
			}
		}// condition end for save the data

	}
    public function paymentDetailsAction(){
		$this->view->headScript()->appendFile('/jscript/secure/payment-details.js','text/javascript');
		$this->view->headTitle('Payment Details: Choose your payment plan - Goo2o.com');
		//$this->view->software_lincense_amount = SOFTWARE_LICENSE_AMOUNT;
		$this->view->action_class = 'paymentDetailsNavImg';
		$this->view->yearly_payment_plan = 'checked="checked"';
		//$this->view->yearly_amount = YEARLY_PLAN_AMOUNT;
		$this->view->total_amount = $this->view->software_lincense_amount;//TOTAL_YEARLY_PLAN_AMOUNT;
		$this->view->checkbox_class = 'checkbox_off';
		$this->view->activation_code_display = 'display:block;';//'display:none;';
		$this->view->payment_option_display = 'display:none;';
		$this->view->card_payment_option = 'checked="checked"';
		$merchant_mapper  = new Secure_Model_MerchantenrollmentMapper($this->_user_api_key); // object for Business mapper class
		$flag_value = $this->_request->getParam('flag');
		if($flag_value == 0 && $flag_value !=''){
			$_SESSION['CHECKFLAG']['flag'] = $flag_value;
			$this->_redirect(HTTP_SECURE . '/merchantenrollment/payment-details');
		}
		if($_POST['continue_payment_btn'] == 'continue' || $_SESSION['CHECKFLAG']['flag'] == 0){
			$users_details=array();
			if($_SESSION['ENROLLMENT']['flag'] == 0){
				$users_details = $_SESSION['ENROLLMENT'];
				$payment_flag = 1;
			}
			$payment_plan = 2;
			$payment_mode = 3;
			$allready_used = 2;
			//$plan_payment = ($_POST['payment_plan'])?$_POST['payment_plan']:$users_details['payment_plan'];
			$checkbox_input = ($_POST['checkbox_input'])?$_POST['checkbox_input']:$users_details['allready_used'];
			//$mode_payment = ($_POST['payment_mode'])?$_POST['payment_mode']:$users_details['payment_mode'];
			
			/*if($_POST['payment_plan']=='with_yearly_plan' || $plan_payment == 1){
				$this->view->yearly_payment_plan = 'checked="checked"';	
				$this->view->noyearly_payment_plan = '';
				$this->view->yearly_amount = YEARLY_PLAN_AMOUNT;
				$this->view->total_amount = TOTAL_YEARLY_PLAN_AMOUNT;
				$payment_plan = 1;
			}
			elseif($_POST['payment_plan']=='without_yearly_plan' || $plan_payment == 2){
				$this->view->noyearly_payment_plan = 'checked="checked"';
				$this->view->yearly_payment_plan = '';
				$this->view->yearly_amount = NO_YEARLY_PLAN_AMOUNT;
				$this->view->total_amount = SOFTWARE_LICENSE_AMOUNT;
				$payment_plan = 2;
			}*/
			
			if($_POST['checkbox_input']=='checkbox_off' || $checkbox_input == 1){
				$this->view->checkbox_class = 'checkbox_off';	
				$this->view->activation_code_display = 'display:none;';
				$this->view->payment_option_display = 'display:block;';
				$this->view->activation_code_value = '';
				$payment_mode = '';
				$allready_used = 1;
			}
			else if($_POST['checkbox_input']=='checkbox_on' || $checkbox_input == 2){
				$this->view->checkbox_class = 'checkbox_on';
				$this->view->activation_code_display = 'display:block;';
				$this->view->payment_option_display = 'display:none;';
				$this->view->activation_code_value = ($_SESSION['ACTIVATION_CODE']['activation_code'])?$_SESSION['ACTIVATION_CODE']['activation_code']:$_POST['activation_code'];
				//$payment_mode = 3;
				//$allready_used = 2;
			}
			/*if($_POST['checkbox_input']=='checkbox_off' || $checkbox_input == 1){
				if($_POST['payment_mode']=='credit_card' || $mode_payment == 1){
					$this->view->card_payment_option = 'checked="checked"';
					$this->view->net_payment_option = '';
					$payment_mode = 1 ;
				}elseif($_POST['payment_mode']=='net_payment' || $mode_payment == 2){
					$this->view->card_payment_option = '';
					$this->view->net_payment_option = 'checked="checked"';
					$payment_mode = 2 ;
				}
			}*/
			if($_POST['action']=='payment'){
				$payment_flag = 0;
				if($_POST['activation_code']!=''){
					$merchant_mapper->checkActivationCode(trim($_POST['activation_code']));
					$get_record_count = $merchant_mapper->__get('_activationcode_count_value');
					if($get_record_count < 1){
						$this->view->code_error_msg = 'Enter a valid activation code';
						$this->view->code_error_display = 'display:block;';
						$payment_flag = 1;
					}
				}			
			}
			if($payment_flag==0){
				$update_data_array = array('payment_plan'=>$payment_plan,'allready_used'=>$allready_used,'payment_mode'=>$payment_mode);
				$_SESSION['ENROLLMENT'] = array_merge($_SESSION['ENROLLMENT'],$update_data_array);
				$_SESSION['ACTIVATION_CODE']['activation_code'] = $_POST['activation_code'];
				$_SESSION['CHECKFLAG']['flag'] = 3;
				$this->_redirect(HTTP_SECURE . '/merchantenrollment/review-details');
			}
		}
		if($_SESSION['CHECKFLAG']['flag'] == 3){
			$this->_redirect(HTTP_SECURE . '/merchantenrollment/business-details');
		}
	}
    public function reviewDetailsAction(){
		$this->view->headTitle('Review & Confirm: Review your enrollment details - Goo2o.com');
		$this->view->action_class = 'reviewConfirmDetailsNavImg';
		$merchant_mapper  = new Secure_Model_MerchantenrollmentMapper($this->_user_api_key); // object for Business mapper class
		if($_SESSION['CHECKFLAG']['flag'] != 3){
			$this->_redirect(HTTP_SECURE . '/merchantenrollment/business-details');
		}
		$users_details = $_SESSION['ENROLLMENT'];
		if($users_details['payment_mode']!=''){
			$this->view->user_info = $users_details;
			$this->view->city_name = $merchant_mapper->getCityName($users_details['city']);
			$this->view->state_name = $merchant_mapper->getStateName($users_details['state']);
		}else{
			 $this->_redirect(HTTP_SECURE.'/merchantenrollment/payment-details');
		}
	}
    public function congratulationsAction(){
		$this->view->headTitle('Congratulations: Setup your Goo2o Store - Goo2o.com');
		$this->view->action_class = 'congratulationNavImg';
		$merchant_mapper  = new Secure_Model_MerchantenrollmentMapper($this->_user_api_key); // object for Business mapper class
		$users_details = $_SESSION['ENROLLMENT'];
		if($users_details['payment_mode'] == 3 || $users_details['transaction_id']!= ''){
			$_SESSION['ENROLLMENT']['active'] = 1;
			if($_SESSION['ACTIVATION_CODE']['activation_code']!=''){
				$insert_data_array = $_SESSION['ENROLLMENT'];
				$merchant_mapper->checkUserMall();
				$get_count_id = $merchant_mapper->__get('_mall_count_value');
				if($get_count_id < 1){
					$mailFlag = 1;
					$insert_data_array['parent_store']=0;
					$store_address_array = array('store_id'=>$this->_user_api_key,'address_line_one'=>addslashes($_SESSION['ENROLLMENT']['business_address']),'city'=>$_SESSION['ENROLLMENT']['city'],'state'=>$_SESSION['ENROLLMENT']['state'],'pincode'=>$_SESSION['ENROLLMENT']['pincode'],'set_default'=>'TRUE','support_check'=>'0','created_on'=>time());
					$merchant_mapper->addBusinessDetails($insert_data_array);
					//Insert In User_role ANd user_permission TAble
					$merchant_mapper->mallOwnerRolePermission($this->_user_api_key,$_SESSION['original_login']['user'][0]['user_email_address']);
					$_SESSION['USER']['userDetails'][0]['title'] = $_SESSION['ENROLLMENT']['title'];
					$_SESSION['USER']['userDetails'][0]['mallurl'] = $_SESSION['ENROLLMENT']['mallurl'];
					$get_last_id  = $_SESSION['original_login']['userId'];
					$mapper=new Admin_Model_IndexMapper();
					$mapper->changeUserprofile($this->_user_api_key,$get_last_id);
					$merchant_mapper->addBussinessDetailInStoreAddress($store_address_array);
				}else{
					if($_SESSION['original_login']['user'][0]['store_owner_type']=='0'){
						$mailFlag = 1;
						$user_session_val = $_SESSION['original_login']['user'][0];
						$user_seesion_value = array('username'=>$user_session_val['username'].'-xyz','user_full_name'=>$user_session_val['user_full_name'],'user_email_address'=>$user_session_val['user_email_address'].'-xyz','user_gender'=>$user_session_val['user_gender'],'user_dob'=>$user_session_val['user_dob'],'user_image'=>$user_session_val['user_image'],'user_location'=>$user_session_val['user_location'],'user_telephone'=>$user_session_val['user_telephone'],'user_bio'=>$user_session_val['user_bio'],'user_account_status'=>$user_session_val['user_account_status'],'user_join_date'=>$user_session_val['user_join_date'],'dept_id'=>$user_session_val['dept_id'],'user_mobile'=>$user_session_val['user_mobile'],'user_login_status'=>$user_session_val['user_login_status'],'signup_steps'=>$user_session_val['signup_steps'],'vcode'=>$user_session_val['vcode'].'-xyz','email_verification'=>$user_session_val['email_verification'],'forgetpasscode'=>$user_session_val['forgetpasscode'],'gooffline'=>$user_session_val['gooffline'],'playsound'=>$user_session_val['playsound'],'email_verification'=>'1');
						$merchant_mapper->addSameUserWithNewApiKey($user_seesion_value,$user_session_val['user_email_address'],$user_session_val['username']);
						$get_last_id = $merchant_mapper->__get('_last_user_inserted_id');
						$get_new_api_key = $merchant_mapper->__get('_apikey');
						$this->_store_api_key=$get_new_api_key['key'];
						$username_array = array('name'=> $user_session_val['user_email_address'].'-'.$get_last_id,'password' => md5($user_session_val['username'].'-'.$get_last_id),'username' => $user_session_val['username'].'-'.$get_last_id,'apikey'=>$get_new_api_key['key'],'salt'=>$get_new_api_key['salt']);
						$merchant_mapper->updateSameUserWithNewApiKey($get_last_id,$username_array);
						$insert_data_array['parent_store']=$insert_data_array['user_id'];
						$insert_data_array['user_id']=$get_last_id;
						$merchant_mapper->addBusinessDetails($insert_data_array,$get_new_api_key['key']);//data insert into mall_detail table
						
						$update_user_array = array('username'=>$user_session_val['username'].'-'.$get_last_id,'user_email_address'=>$user_session_val['user_email_address'].'-'.$get_last_id,'vcode'=>$user_session_val['vcode'].'-'.$get_last_id);
						$merchant_mapper->updateNewUserDetail($get_last_id,$update_user_array);
						//Insert In User_role ANd user_permission TAble
						$merchant_mapper->mallOwnerRolePermission($get_new_api_key['key'],$_SESSION['original_login']['user'][0]['user_email_address']);
						$store_address_array = array('store_id'=>$this->_store_api_key,'address_line_one'=>addslashes($_SESSION['ENROLLMENT']['business_address']),'city'=>$_SESSION['ENROLLMENT']['city'],'state'=>$_SESSION['ENROLLMENT']['state'],'pincode'=>$_SESSION['ENROLLMENT']['pincode'],'set_default'=>'TRUE','support_check'=>'0','created_on'=>time());
						$merchant_mapper->addBussinessDetailInStoreAddress($store_address_array);
					} else {
						$mailFlag = 0;
						$insert_data_array['parent_store']=0;
						$insert_data_array['store_owner_type']=0;
						$merchant_mapper->updateDemoMallDetails($insert_data_array,$_SESSION['original_login']['user'][0]['id']);
						$store_address_array = array('store_id'=>$this->_store_api_key,'address_line_one'=>addslashes($_SESSION['ENROLLMENT']['business_address']),'city'=>$_SESSION['ENROLLMENT']['city'],'state'=>$_SESSION['ENROLLMENT']['state'],'pincode'=>$_SESSION['ENROLLMENT']['pincode'],'set_default'=>'TRUE','support_check'=>'0','created_on'=>time());
						$merchant_mapper->addBussinessDetailInStoreAddress($store_address_array);
						$_SESSION['original_login']['user'][0]['store_owner_type'] = 0;
						/*----@Trigger NO: 28 @created by : Mukesh Bisht @date : 12-11-2011 -------------*/
						$tData = array('to_id'=>$_SESSION['USER']['userId'],
									   'to_mail'=>$_SESSION['USER']['stores'][0]['email_id'],
									   'to_name'=>$_SESSION['original_login']['user'][0]['user_full_name']);
						$this->objTrigger->triggerFire(28,$tData);
						/*---------------End HERE----------------------------*/
					}
		}
				$merchant_mapper->updateActivationCode($_SESSION['ACTIVATION_CODE']['activation_code']);
				//Activation Mail Send here
				$mapper=new Admin_Model_IndexMapper();
				$mapper->changeUserprofile($this->_store_api_key,$get_last_id);
			}
			/*if($users_details['transaction_id']!=''){
				//payment Mail Send here
				$merchant_mapper->updateMallUrlStatus();
			}*/
			$this->view->user_info = $users_details;
			$this->view->city_name = $merchant_mapper->getCityName($users_details['city']);
			$this->view->state_name = $merchant_mapper->getStateName($users_details['state']);
			$this->view->email_address = $_SESSION['original_login']['user'][0]['user_email_address'];
			$this->view->user_full_name = $_SESSION['original_login']['user'][0]['user_full_name'];

			$_SESSION['ACTIVATION_CODE'] = '';
			$_SESSION['ENROLLMENT'] = '';
			$_SESSION['CHECKFLAG'] = '';
			
			if($mailFlag == '1'){
			/*----@Trigger NO: 8 @created by : Mukesh Bisht @date : 12-11-2011 -------------*/
				   $tData = array( 'store_subdomain_link'=>$_SESSION['USER']['stores'][0]['mallurl'],
								   'to_id'=>$_SESSION['USER']['userId'],
								   'to_mail'=>$_SESSION['USER']['stores'][0]['email_id'],
								   'to_name'=>$_SESSION['original_login']['user'][0]['user_full_name']);
					$this->objTrigger->triggerFire(8,$tData);
			/*---------------End HERE----------------------------*/
			}
		}else{
			 $this->_redirect(HTTP_SECURE.'/merchantenrollment/review-details');
		}
    }
	 public function getcitiesAction(){
		$mapper  = new Secure_Model_RegistrationMapper(); // object for register mapper class
		$returnedLocationArray = $mapper->getLocationList($_POST['stateid'],'','1'); // getting location list
		exit;
	}
    /*public function buildstoreAction(){
		$this->view->headTitle('Build Store - Goo2o.com');
		$structure_mapper  = new Admin_Model_StructureMapper($this->_store_api_key); // object for Business mapper class
		$structure_mapper->buildStore();
		exit;
		$structure_mapper->updateIndexHtmlInDb();
		$this->_redirect(HTTP_SERVER.'/admin/Overview/#page');
	}*/

}
?>
