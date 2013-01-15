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
			$this->view->select_state = $_SESSION['USER']['stores'][0]['state'];
			$state_id = $_SESSION['USER']['stores'][0]['state'];
			$city_id = $_SESSION['USER']['stores'][0]['city'];
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
			
			// data upload start here
			
			$db = mysql_connect("localhost","sketchee_store","var#usr");
			mysql_select_db("sketchee_o2ostore",$db);
			$default_data_apikey = '9247a0bbbb97f01b30cafe0cd0584169';
			$new_data_apikey = $this->_user_api_key;
			if (trim($default_data_apikey) == '')
				$default_data_apikey = 'fc06cf4dc58e46ceb71d02bf3de6a317';
			//if (trim($new_data_apikey) == '')
			//	$new_data_apikey = 'fa4c9a69f2e4b01fdb495566788adc1a'; 
			if (trim($new_data_apikey) == '')
				$new_data_apikey = $_SESSION['USER']['STORES']['0']['store_apikey'];
			if (trim($new_data_apikey) == '' || trim($default_data_apikey) == '') {
				echo '<span style="color:#990000;font-size:20px;">Failed to copy Sample Data<br>Security Issue - Contact Administrator</span>';
				exit;
			}
			$result = mysql_query("select id from username where apikey='$new_data_apikey'");
			$result = mysql_fetch_array($result);
			$userid = $result['0'];
			
			/* ---migrate 3 table of feature (created by : mrunal)---- */
			
			$sql_features = mysql_query("SELECT * FROM features where api_key = '".$default_data_apikey."'");
			$old_new_fid = $replace_old_fid = $replace_new_fid = array();
			while($features = mysql_fetch_assoc($sql_features)){
				mysql_query("INSERT INTO `features`(`feature_name`, `input_type`, `mandatory_feature`, `api_key`, `add_date`, `modify_date`, `value_delete`) VALUES ('".addslashes(stripslashes($features['feature_name']))."','".$features['input_type']."','".$features['input_type']."','".$new_data_apikey."', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(),'".$features['value_delete']."')");
				$old_new_fid[$features['product_feature_id']] = mysql_insert_id();
				$replace_old_fid[] = '"'.$features['product_feature_id'].'"]';
                $replace_new_fid[] = '"'.mysql_insert_id().'"]';
			}
			
			$sql_fgroup = mysql_query("SELECT * FROM `feature_group` WHERE `api_key` = '" . $default_data_apikey . "'");
			$old_new_fgid = array();
			while($fGroup = mysql_fetch_assoc($sql_fgroup)){
				mysql_query("INSERT INTO `feature_group`( `feature_group_name`, `api_key`, `add_date`, `modify_date`, `group_delete`) VALUES ('".addslashes(stripslashes($fGroup['feature_group_name']))."','".$new_data_apikey."',UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), '".$fGroup['group_delete']."')");
				$old_new_fgid[$fGroup['group_id']] = mysql_insert_id();
			}
			
			$fgv_arr = array();
			$sql_fgvalue = mysql_query("SELECT * FROM `feature_group_value` WHERE `group_id` IN (" . implode(',', array_keys($old_new_fgid)) . ")");
			while ($fg_value = mysql_fetch_assoc($sql_fgvalue)){
				$fgv_arr[] = " (" . $old_new_fgid[$fg_value['group_id']] . ",".$old_new_fid[$fg_value['pf_id']].")";
			}
			if(count($fgv_arr)>0){
				mysql_query("INSERT INTO `feature_group_value` (`group_id`, `pf_id`) VALUES ".implode(',', $fgv_arr));//error handling
			}
			/* ---migrate 4 table of variation (created by : mrunal)---- */
			
			$sql_vn = mysql_query("select * from variation_name where api_key='$default_data_apikey'");
			$old_new_vn_id = $replace_new_vid = $replace_old_vid = array();
			while ($vn = mysql_fetch_assoc($sql_vn)) {
				mysql_query("INSERT INTO variation_name (variation_name, variation_code, api_key, date_added, date_modified, delete_flag_variation) VALUES ('" . addslashes(stripslashes($vn['variation_name'])) . "','" . addslashes(stripslashes($vn['variation_code'])) . "','" . $new_data_apikey . "', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(),'" . $vn['delete_flag_variation'] . "')");
				$old_new_vn_id[$vn['variation_name_id']] = mysql_insert_id();
				$replace_old_vid[] = '"'.$vn['variation_name_id'].'":';
				$replace_new_vid[] = '"'.mysql_insert_id().'":';
			}
			
			$sql_vv = mysql_query("select * from variation_value where variation_name_id IN ( " . implode(',', array_keys($old_new_vn_id)). ")");
			$insert_vv_value = array();
			while ($vv = mysql_fetch_assoc($sql_vv)) {
				$insert_vv_value[] = "(" . $old_new_vn_id[$vv['variation_name_id']] . ",'" . addslashes(stripslashes($vv['value'])) . "')";
			}
			if(count($insert_vv_value)>0){
				mysql_query("INSERT INTO variation_value ( variation_name_id, value) VALUES ".implode(',', $insert_vv_value));//error handling
			}
			$old_new_vg_id = array();
			$sql_vg = mysql_query("select * from variation_group where api_key='$default_data_apikey'");
			while ($vg = mysql_fetch_assoc($sql_vg)) {
				mysql_query("INSERT INTO variation_group (name, api_key, date_added, date_modified, delete_flag) VALUES ('" . addslashes(stripslashes($vg['name'])) . "','" . $new_data_apikey . "', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(),'" . $vg['delete_flag'] . "')");
				$old_new_vg_id[$vg['variation_group_id']] = mysql_insert_id();
			}
			
			$sql_vgv = mysql_query("select * from variation_group_value where variation_group_id IN( " . implode(',', array_keys($old_new_vg_id)) . " )");
			while ($vgv = mysql_fetch_assoc($sql_vgv)) {
				$var_vgv_arr[] = "(".$old_new_vg_id[$vgv['variation_group_id']].",".$old_new_vn_id[$vgv['variation_name_id']].",UNIX_TIMESTAMP())";
			}
			if(count($var_vgv_arr)>0){
				mysql_query("INSERT INTO variation_group_value (`variation_group_id`, `variation_name_id`, `date_added`) VALUES " . implode(',', $var_vgv_arr));//error handling
			}
			//Add new code
			/* ---migrate 3 table of shipping (created by : mrunal)---- */
			$sql_sm = mysql_query("select * from shipping_method where api_key='$default_data_apikey'");
			$old_new_sm_id = array();
			while ($sm = mysql_fetch_assoc($sql_sm)) {
				mysql_query("INSERT INTO `shipping_method`(`destination`, `handling_time`, `shipping_name`, `api_key`, `delete_flag`, `date_added`, `date_modified`) VALUES ('".$sm['destination']."',".$sm['handling_time'].",'".$sm['shipping_name']."','".$new_data_apikey."','".$sm['delete_flag']."',UNIX_TIMESTAMP(),UNIX_TIMESTAMP())");
				$old_new_sm_id[$sm['shipping_id']] = mysql_insert_id();
			}
                        $sql_sc = mysql_query("SELECT * FROM  `shipping_cost` WHERE shipping_id IN (". implode(",", array_keys($old_new_sm_id)).")");
                        while($sc = mysql_fetch_assoc($sql_sc)){
                            mysql_query("INSERT INTO `shipping_cost`( `shipping_id`, `shipping_type`, `shipping_pirce`) VALUES (".$old_new_sm_id[$sc['shipping_id']].",'".$sc['shipping_type']."','".$sc['shipping_pirce']."')");
                        }
                        
                        $sql_sel = mysql_query("select * from shipping_exclude_location where shipping_id IN (". implode(",", array_keys($old_new_sm_id)).")");
                        while ($sel = mysql_fetch_assoc($sql_sel)){
                            mysql_query("INSERT INTO `shipping_exclude_location`( `shipping_id`, `location_type`, `location_id`) VALUES (".$old_new_sm_id[$sel['shipping_id']].",'".$sel['location_type']."','".$sel['location_id']."')");
                        }
			
			/* ---migrate 3 table of policy (created by : mrunal)---- */
			
                        $sql_rp = mysql_fetch_assoc(mysql_query("select * from return_policy where api_key = '".$default_data_apikey."'"));
                        if($sql_rp){
                            mysql_query("INSERT INTO `return_policy`( `policy_type`, `api_key`) VALUES ('".$sql_rp['policy_type']."','".$new_data_apikey."')");
                            $old_new_rp = array($sql_rp['policy_id'] => mysql_insert_id());
                            $sql_pp = mysql_fetch_assoc(mysql_query("select * from policy_perishable where policy_id = ".$sql_rp['policy_id']));
                            if($sql_pp){
                                mysql_query("INSERT INTO policy_perishable (policy_id,buyer_absent_flag,delay_delivery_flag,buyer_wrong_choice_flag,absent_cost,delay_cost,wrong_choice_cost) value (".$old_new_rp[$sql_pp['policy_id']].",'".$sql_pp['buyer_absent_flag']."','".$sql_pp['delay_delivery_flag']."','".$sql_pp['buyer_wrong_choice_flag']."','".$sql_pp['absent_cost']."','".$sql_pp['delay_cost']."','".$sql_pp['wrong_choice_cost']."')");
                            }
                            $sql_pn = mysql_fetch_assoc(mysql_query("select * from policy_nonperishable where policy_id = ".$sql_rp['policy_id']));
                            if($sql_pn){
                                mysql_query("INSERT INTO policy_nonperishable (policy_id,buyer_absent_flag,buyer_reject_flag,absent_cost,reject_cost,delay_delivery_flag,wrong_choice_flag,return_days_different_item,return_days_damaged_item,return_days_defective_item,return_days_delay_delivery,return_days_wrong_choice,reimburse_long_delay_flag,reimburse_wrong_choice_flag,restocking_cost_wrong_choice_flag,restocking_cost_damaged_flag,restocking_charge_wrong_choice,restocking_charge_damaged) value (".$old_new_rp[$sql_pn['policy_id']].",'".$sql_pn['buyer_absent_flag']."','".$sql_pn['buyer_reject_flag']."','".$sql_pn['absent_cost']."','".$sql_pn['reject_cost']."','".$sql_pn['delay_delivery_flag']."','".$sql_pn['wrong_choice_flag']."','".$sql_pn['return_days_different_item']."','".$sql_pn['return_days_damaged_item']."','".$sql_pn['return_days_defective_item']."','".$sql_pn['return_days_delay_delivery']."','".$sql_pn['return_days_wrong_choice']."','".$sql_pn['reimburse_long_delay_flag']."','".$sql_pn['reimburse_wrong_choice_flag']."','".$sql_pn['restocking_cost_wrong_choice_flag']."','".$sql_pn['restocking_cost_damaged_flag']."','".$sql_pn['restocking_charge_wrong_choice']."','".$sql_pn['restocking_charge_damaged']."')");
                            }
                        }
			//End HEre
			
			$result = mysql_query("select ac.*,bd.*,p.* from addcategory as ac,brand as bd,product as p where ac.cat_id=p.category_id and bd.brand_id=p.brand_id and p.seller_id ='$default_data_apikey' AND p.`status` = '1' AND p.`delete_flag` = '1' order by p.id limit 0,50");
			$product_feature = mysql_query("select * from product_feature where product_id in (select id from product where seller_id='$default_data_apikey' AND `status` = '1' AND `delete_flag` = '1' order by id)");
			$product_image = mysql_query("select * from product_image where product_id in (select id from product where seller_id='$default_data_apikey' AND `status` = '1' AND `delete_flag` = '1' order by id)");
			$product_shipping_policy = mysql_query("select * from product_shipping_policy where product_id in (select id from product where seller_id='$default_data_apikey' AND `status` = '1' AND `delete_flag` = '1' order by id)");
			$product_variation = mysql_query("select * from product_variation where product_id in (select id from product where seller_id='$default_data_apikey' AND `status` = '1' AND `delete_flag` = '1' order by id) order by id");
			$shipping_method = mysql_query("select * from shipping_method where api_key='" . $default_data_apikey . "' and destination='1'");
			
			while ($resproduct_feature = mysql_fetch_assoc($product_feature)) {
				$respf[$resproduct_feature['product_id']][$resproduct_feature['feature_name']] = $resproduct_feature['feature_value'];
			}
			while ($resproduct_image = mysql_fetch_assoc($product_image)) {
				$respi[$resproduct_image['product_id']][$resproduct_image['id']] = $resproduct_image;
			}
			
			while ($resproduct_shipping_policy = mysql_fetch_assoc($product_shipping_policy)) {
				$respsp[$resproduct_shipping_policy['product_id']] = $resproduct_shipping_policy;
			}
			
			while ($resproduct_variation = mysql_fetch_assoc($product_variation)) {
				$respv[$resproduct_variation['product_id']][$resproduct_variation['variation_code']][$resproduct_variation['variant_name']] = $resproduct_variation;
			}
			
			$ressm = mysql_fetch_assoc($shipping_method);
			$shipping_cost = mysql_query("select * from shipping_cost where shipping_id=" . $ressm['shipping_id']);
			$ressc = mysql_fetch_assoc($shipping_cost);
			
			$newcategories = array('nocategory');
			$newbrands = array('nobrand');
			$newcategories1 = '';
			$newbrands1 = '';
			$newproductids = '';
			$newproductidsstring = '';
			$newparentid = '';
			$categories_arr = '';
			$categories_qry = mysql_query("select * from addcategory where apikey='$default_data_apikey'");
			while ($categories_res = mysql_fetch_assoc($categories_qry)) {
				$categories_arr[$categories_res['cat_id']] = $categories_res['cat_name'];
				$parentCatid = $categories_res['parent_id'];
				$sql1 = mysql_query("INSERT INTO addcategory(`apikey`,`cat_name`,`cat_desc`,`parent_id`,`cat_url`,`cat_page_title`,`cat_page_description`,`cat_page_keyword`,`cat_flag`, `image_name`,`image_title`,`image_tag`,`image_description`,`date_added`,`date_modified`,`status`,`image_location`)values('" . $new_data_apikey . "','" . addslashes(stripslashes($categories_res['cat_name'])) . "','" . addslashes(stripslashes($categories_res['cat_desc'])) . "','" . $parentCatid . "','" . $categories_res['cat_url'] . "','" . addslashes(stripslashes($categories_res['cat_page_title'])) . "','" . addslashes(stripslashes($categories_res['cat_page_description'])) . "','" . addslashes(stripslashes($categories_res['cat_page_keyword'])) . "','" . $categories_res['cat_flag'] . "','" . $categories_res['image_name'] . "','" . addslashes(stripslashes($categories_res['image_title'])) . "','" . addslashes(stripslashes($categories_res['image_tag'])) . "','" . addslashes(stripslashes($categories_res['image_description'])) . "','" . $categories_res['date_added'] . "','" . $categories_res['date_modified'] . "','1','http://images.goo2ostore.com/0/" . $default_data_apikey . "/category/" . floor($categories_res['cat_id'] / 1000) . "/" . $categories_res['cat_id'] . "')");
				$last_categoryid = mysql_insert_id();
				$sql1 = mysql_query("INSERT INTO temp_addcategory(`api_key`,`cat_name`,`cat_desc`,`parent_id`,`cat_url`,`cat_page_title`,`cat_page_description`,`cat_page_keyword`,`cat_flag`, `image_name`,`image_title`,`image_tag`,`image_description`,`basicinfostatus`,`imagemanagerstatus`,`optimizestatus`,`type`,`main_cat_id`)values('" . $new_data_apikey . "','" . addslashes(stripslashes($categories_res['cat_name'])) . "','" . addslashes(stripslashes($categories_res['cat_desc'])) . "','" . $parentCatid . "','" . $categories_res['cat_url'] . "','" . addslashes(stripslashes($categories_res['cat_page_title'])) . "','" . addslashes(stripslashes($categories_res['cat_page_description'])) . "','" . addslashes(stripslashes($categories_res['cat_page_keyword'])) . "','" . $categories_res['cat_flag'] . "','" . $categories_res['image_name'] . "','" . addslashes(stripslashes($categories_res['image_title'])) . "','" . addslashes(stripslashes($categories_res['image_tag'])) . "','" . addslashes(stripslashes($categories_res['image_description'])) . "','1','1','1','0'," . $last_categoryid . ")");
				$newcategories[] = $categories_res['cat_name'];
				$newcategories1[$categories_res['cat_name']] = $last_categoryid;
				$newparentid[$last_categoryid] = $parentCatid;
			}
			foreach ($newcategories1 as $newcatKey => $newcatVal) {
				if ($newcatVal != 0) {
					$addcat = mysql_query("update addcategory set parent_id=" . $newcategories1[$categories_arr[$newparentid[$newcatVal]]] . " where cat_id=" . $newcatVal);
				}
			}
			
			while ($res = mysql_fetch_assoc($result)) {
				$last_categoryid = '';
				if (!in_array($res['brand_name'], $newbrands)) {
					$sql1 = mysql_query("insert into  brand(`brand_name`,`brand_description`,`brand_page_title`,`brand_page_description`,`brand_page_keyword`,`brand_flag`,`brand_image`,`brand_title`,`brand_tag`,`image_description`,`date_added`,`date_modified`,`api_key`,`brand_url`,`brand_status`,`delete_status`,`image_location`) values('" . addslashes(stripslashes($res['brand_name'])) . "','" . addslashes(stripslashes($res['brand_description'])) . "','" . addslashes(stripslashes($res['brand_page_title'])) . "','" . addslashes(stripslashes($res['brand_page_description'])) . "','" . addslashes(stripslashes($res['brand_page_keyword'])) . "','1','" . $res['brand_image'] . "','" . addslashes(stripslashes($res['brand_title'])) . "','" . addslashes(stripslashes($res['brand_tag'])) . "','" . addslashes(stripslashes($res['image_description'])) . "','" . $res['date_added'] . "','" . $res['date_modified'] . "','" . $new_data_apikey . "','" . $res['brand_url'] . "','1','1','http://images.goo2ostore.com/0/" . $default_data_apikey . "/brand/" . floor($res['brand_id'] / 1000) . "/" . $res['brand_id'] . "')");
			
					$lastbrand_id = mysql_insert_id();
					
					$sql1 = mysql_query("INSERT INTO temp_addcategory(`api_key`,`cat_name`,`cat_desc`,`parent_id`,`cat_url`,`cat_page_title`,`cat_page_description`,`cat_page_keyword`,`cat_flag`, `image_name`,`image_title`,`image_tag`,`image_description`,`basicinfostatus`,`imagemanagerstatus`,`optimizestatus`,`type`,`main_cat_id`)values('" . $new_data_apikey . "','" . addslashes(stripslashes($res['brand_name'])) . "','" . addslashes(stripslashes($res['brand_description'])) . "','0','" . $res['brand_url'] . "','" . addslashes(stripslashes($res['brand_page_title'])) . "','" . addslashes(stripslashes($res['brand_page_description'])) . "','" . addslashes(stripslashes($res['brand_page_keyword'])) . "','" . $categories_res['cat_flag'] . "','" . $categories_res['image_name'] . "','" . addslashes(stripslashes($res['brand_title'])) . "','" . addslashes(stripslashes($res['brand_tag'])) . "','" . addslashes(stripslashes($res['image_description'])) . "','1','1','1','1'," . $lastbrand_id . ")");
					$newbrands[] = $res['brand_name'];
					$newbrands1[$res['brand_name']] = $lastbrand_id; //last inserted id
				}
				$newprod = mysql_query("insert into product(`product_name`,`category_id`,`brand_id`,`seller_id`,`long_description`,`short_description`,`product_url`,`page_title`, `page_description`,`page_keyword`,`meta_field`,`create_date`,`modified_date`,`display_market_place`,`seller_productid`)values('" . $res['product_name'] . "','" . $newcategories1[$res['cat_name']] . "','" . $newbrands1[$res['brand_name']] . "','" . $new_data_apikey . "','" . addslashes(stripslashes($res['long_description'])) . "','" . addslashes(stripslashes($res['short_description'])) . "','" . $res['product_url'] . "','" . addslashes(stripslashes($res['page_title'])) . "','" . addslashes(stripslashes($res['page_description'])) . "','" . addslashes(stripslashes($res['page_keyword'])) . "','" . addslashes(stripslashes($res['meta_field'])) . "'," . mktime() . "," . mktime() . ",'0','SKU-1-2')");
				$newproductid = mysql_insert_id();
				if ($newproductid > 0) {
					$newproductids[] = $newproductid;
					$newproductidsstring.=$newproductid . ',';
				}
			//mysql_query("update product set seller_productid='SKU-$userid-$newproductid' where id=$newproductid");
			//print_r($respsp);
				$featurestring = '';
				if (isset($respf[$res['id']])) {
					foreach ($respf[$res['id']] as $key => $value)
						$featurestring.="('" . $newproductid . "','" . addslashes(stripcslashes($key)) . "','" . addslashes(stripcslashes($value)) . "'),";
					$newprodfea = mysql_query("insert into product_feature(`product_id`,`feature_name`,`feature_value`)values " . substr($featurestring, 0, -1));
				}
				if (isset($respi[$res['id']])) {
					$image_title = $image_alttag = $image_description = $image_type = $image_name = array();
					$newimages = '';
					foreach ($respi[$res['id']] as $key => $value){
						$newimages.="('" . $newproductid . "','" . addslashes(stripcslashes($value['image_title'])) . "','" . addslashes(stripcslashes($value['image_tag'])) . "','" . addslashes(stripcslashes($value['image_description'])) . "','" . $value['image_name'] . "','http://images.goo2ostore.com/0/" . $default_data_apikey . "/product/" . floor($res['id'] / 1000) . "/" . $res['id'] . "','" . $value['image_type'] . "'),";
						$image_title[] = addslashes(stripcslashes($value['image_title']));
						$image_alttag[] = addslashes(stripcslashes($value['image_tag']));
						$image_description[] = addslashes(stripcslashes($value['image_description']));
						$image_type[] = addslashes(stripcslashes($value['image_type']));
						$image_name[] = $value['image_name'];
					}
					$newprodimg = mysql_query("insert into product_image(`product_id`,`image_title`,`image_tag`,`image_description`,`image_name`,`image_location`,`image_type`)values " . substr($newimages, 0, -1));
				}
				if (isset($respv[$res['id']])) {
					$variationstring = '';
					foreach ($respv[$res['id']] as $key => $value1)

						foreach ($value1 as $key => $value)
							$variationstring.="('" . $newproductid . "','" . addslashes(stripcslashes($value['variant_name'])) . "','" . addslashes(stripcslashes($value['variant_value'])) . "','" . $value['variation_code'] . "','" . $value['modify_date'] . "','" . $value['default_flag'] . "'),";
					$newprodvar = mysql_query("insert into product_variation(`product_id`,`variant_name`,`variant_value`,`variation_code`,`modify_date`,`default_flag`)values " . substr($variationstring, 0, -1));
				}
				$allpsp = '';
				if (isset($respsp[$res['id']])) {
					$value = $respsp[$res['id']];
					$allpsp.="('" . $newproductid . "','" . addslashes(stripcslashes($value['weight'])) . "','" . $value['shipping_id'] . "','1','" . $value['default_policy_id'] . "','no','" . $value['buyer_absent_cost'] . "','" . $value['buyer_refuse'] . "','" . $value['buyer_refuse_cost'] . "','" . $value['delay_delivary'] . "','" . $value['delay_delivery_cost'] . "','" . $value['delay_delivery_day'] . "','" . $value['wrong_choice'] . "','" . $value['wrong_choice_cost'] . "','" . $value['wrong_choice_day'] . "','7','7','7','" . $value['restocking_wrong_choice'] . "','" . $value['restocking_wrong_choice_cost'] . "','no','" . $value['restocking_damage_item_cost'] . "','no','no'),";
					$newprodsp = mysql_query("insert into product_shipping_policy(`product_id`,`weight`,`shipping_id`,`policy_type`,`default_policy_id`,`buyer_absent`,`buyer_absent_cost`,`buyer_refuse`,`buyer_refuse_cost`,`delay_delivary`,`delay_delivery_cost`, `delay_delivery_day`, `wrong_choice`, `wrong_choice_cost`, `wrong_choice_day`,`diff_item_day`,`damage_item_day`,`defective_item_day`,`restocking_wrong_choice`,`restocking_wrong_choice_cost`,`restocking_damage_item`,`restocking_damage_item_cost`,`reimburse_delay_delivery`,`reimburse_wrong_choice`)values " . substr($allpsp, 0, -1));
				}
				//Add Temp Prodcut Query
					/*------insert into 'temp_product'------*/
					$new_product_arr = mysql_fetch_assoc(mysql_query("select * from product where id = " . $newproductid));
					$old_temp_product = mysql_fetch_assoc(mysql_query("select * from temp_product where product_id = " . $res['id']));
					$f_val = str_replace($replace_old_fid,$replace_new_fid,$old_temp_product['feature_value']);
					$v_val = str_replace($replace_old_vid,$replace_new_vid,$old_temp_product['variant_value_id']);
					if(strlen($old_temp_product['variant_group_id'])>0){
						$var = explode("_", $old_temp_product['variant_group_id']);
						$vn_id = array();
						foreach (explode(",", $var[1]) as $value) {
							$vn_id[] = $old_new_vn_id[$value];
						}
						$vg_val = $old_new_vg_id[$var[0]].'_'.implode(",",$vn_id);
					}
                    mysql_query("insert into temp_product (product_name,product_long_desc,product_short_desc,category_id,brand_id,datecreated,image_title,image_alttag,image_description,image_type,image_name,feature_id,feature_value,feature_group_id,variant_group_id,variant_value_id,product_weight,shipping_id,policy_type,policy_info,product_url,page_title,page_description,page_keywords,meta_field,basicinfo_status,image_status,feature_status,optimize_status,variation_status,shipping_status,seller_id,visible_flag,product_id) value ('".addslashes(stripcslashes($new_product_arr['product_name']))."','".addslashes(stripcslashes($new_product_arr['long_description']))."','".addslashes(stripcslashes($new_product_arr['short_description']))."','".addslashes(stripcslashes($new_product_arr['category_id']))."','".addslashes(stripcslashes($new_product_arr['brand_id']))."',UNIX_TIMESTAMP(),'".implode(":", $image_type)."','". addslashes(stripcslashes(implode(':', $image_alttag)))."','".addslashes(stripcslashes(implode(":", $image_description)))."','".addslashes(stripcslashes(implode(":", $image_type)))."','".addslashes(stripcslashes(implode(":", $image_name)))."','0','".addslashes(stripcslashes($f_val))."',".$old_new_fgid[$old_temp_product['feature_group_id']].",'".$vg_val."','".addslashes(stripcslashes($v_val))."',".$old_temp_product['product_weight'].",".$old_new_sm_id[$old_temp_product['shipping_id']].",'".$old_temp_product['policy_type']."','".addslashes(stripcslashes($old_temp_product['policy_info']))."','".addslashes(stripcslashes($new_product_arr['product_url']))."','".addslashes(stripcslashes($new_product_arr['page_title']))."','".addslashes(stripcslashes($old_temp_product['page_description']))."','".addslashes(stripcslashes($new_product_arr['page_keyword']))."','".addslashes(stripcslashes($new_product_arr['meta_field']))."','1','1','1','1','1','1','".$new_data_apikey."','1',".$newproductid.")");
				//End Here
			}
			$updtsellerprid = mysql_query("update product set seller_productid=concat('SKU-" . $userid . "-',id) where id in (" . substr($newproductidsstring, 0, -1) . ")");
			
			echo '<div style="clear:both;"><div style="border-bottom: 1px solid #D8D8D8;clear: both;height: 75px;margin: auto;width: 1000px;"><div style="clear: both;font-size: 0;height: 18px;line-height: 18px;">&nbsp;</div><div style="float:left;"><a href="javascript:void(0)"><img title="Logo" alt="Logo" src="http://goo2ostore.com/images/secure/merchantenrollment/logo_blue.gif"></a></div><div style="color: #0083CB;float: left;font-family: Verdana,Arial,Helvetica,sans-serif;font-weight: bold;line-height: 25px;padding-left: 16px;">Goo2o merchant solution</div></div></div>';
			echo '<div style="color:#0083CB;font-size:22px; margin-top:50px; text-align:center;">Your Store is Being Created, Please Wait...</div>
<div style="color:#0083CB;font-size:14px;margin-top:10px; text-align:center;">Thanks for choosing goo2o. We\'re creating your store now.</div>
<div style="margin-top:70px;text-align:center;" align="center"><img src="http://goo2ostore.com/bar/images/goo2oloading.gif"></div><div style="color:#0083CB;font-size:22px; margin-top:50px; text-align:center;">You will be redirected in 5 seconds. Problems with the redirect, please press "F5" or Ctrl+R .</div>';
			$structure_mapper  = new Admin_Model_StructureMapper($this->_user_api_key); // object for Business mapper class
			$structure_mapper->buildStore();
			$this->_redirect(HTTP_SERVER.'/admin/overview/#page');
			exit;
			//data upload end
		}else{
			$this->_redirect(HTTP_SERVER.'/admin/overview/#page');// $this->_redirect(HTTP_SECURE.'/merchantenrollment/review-details');
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
