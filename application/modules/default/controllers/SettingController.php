<?php
//@author: Gaurav trivedi
//@creation date: 22 april 2011
require_once APPLICATION_PATH.'/includes/DataRender.php';
require_once APPLICATION_PATH.'/includes/Utility.php';

class SettingController extends Zend_Controller_Action
{
   protected $current_user;
    public function init(){
		$this->current_user = (int)$_SESSION['USER']['userId'];
		if($this->current_user==''){
			$this->_redirect('/secure/login');
		}
		$user = new Default_Model_Classes_Setting($this->current_user);
		$user->getDetailsFromUser($this->current_user);
		$selectedData = $user->__get('currentUserData');
		if($selectedData[0]['email_verification']=='0'){
			$verification = '... not verified';
			$emailflag=0;
		}else{
			$verification = '... verified';
			$emailflag=1;	
		}	
		$this->view->verification=$verification;
		$this->view->emailflag=$emailflag;
		$this->view->checkUserInfo = $selectedData;
		$this->view->headScript()->appendFile('/jscript/default/setting.js');
    }
  
  public function indexAction(){
	  	
        $this->view->headLink()->appendStylesheet('/css/default/header.css');
		$this->view->headLink()->appendStylesheet('/css/default/accountsettings.css');
        $this->view->headTitle('Settings - o2o');
		$this->current_user = (int)$_SESSION['USER']['userId'];
		$completeArray = $_POST;
		if(isset($_POST['username'])){
			$db = Zend_Db_Table::getDefaultAdapter();
			$sql = "update user set username = '".$completeArray['username']."',user_email_address ='".$completeArray['email']."',email_verification='0' where id = '$this->current_user'";
			$db->query($sql);
			$sqlanother = "update username set username = '".$completeArray['username']."',name ='".$completeArray['email']."' where id = '$this->current_user'";
			$db->query($sqlanother);
			$user = new Default_Model_Classes_Setting($this->current_user);
			$user->getDetailsFromUser($this->current_user);
			$selectedData = $user->__get('currentUserData');
			$previousEmailId = $completeArray['sessionEmail'];//previous mail id
			$currentEmailId = $completeArray['email']; //current mail id
			$this->view->checkUserInfo = $selectedData;
			
			//code for email send when email has been changed to old email id 
			$html = new Zend_View();
			$html->setScriptPath(APPLICATION_PATH . '/modules/default/views/emails/');
			$mail = new Zend_Mail('utf-8');
			$html->user_full_name = $selectedData[0]['user_full_name'];
			$html->username = $selectedData[0]['username'];
			$bodyText = $html->render('changeusernameandemailsetting.phtml');
			$mail->setSubject('Your Go o2o email has changed.');
			$mail->setFrom('support@goo2o.com','Go o2o');
			$mail->setBodyHtml($bodyText);
			$mail->addTo($previousEmailId);
			//$mail->send();
			//End code for email send
			
			// code for email send when email changed and verify link send at new email address 
			$html->usernewemail = $currentEmailId;
			$linkVal= makeUrl($this->current_user);
			$html->LINKVALUE=$linkVal;
			$mail = new Zend_Mail('utf-8');
			$mail->setSubject('Confirm your Go o2o contact email, '.$selectedData[0]['username']);
			$bodyTextNew = $html->render('confirmchangeiglobulemailtouser.phtml');
			$mail->setFrom('support@goo2o.com','Go o2o');
			$mail->setBodyHtml($bodyTextNew);
			$mail->addTo($currentEmailId);
			//$mail->send();
			//End code for email send
			$this->view->msg = "updated";
			}
		
		if(isset($_POST['old_password'])){
			$user = new Default_Model_Classes_Setting($this->current_user);
			if($completeArray['old_password']!=''){
				$user->getValidatePassword($this->current_user,$completeArray['old_password']);
				$userOfCorrectPassword = $user->__get('currentpasswordValidate');
			if($userOfCorrectPassword==1){
				$db = Zend_Db_Table::getDefaultAdapter();
				$sql = "update username set password = '".md5($completeArray['confirm_password'])."' where id = '$this->current_user'";
				$db->query($sql);
				$this->view->msg = "updated";
				}
			}	
		}
	 }
	 
	  public function usernamevalidAction(){
		  
		$user = new Default_Model_Classes_Setting($this->current_user);
		$username = $_POST['username']; 
		$user->checkExistUserName($username);
		$count = $user->__get('selectExistUser');
		 if($count > 0) echo "exist";
		 else echo "notexist";
		  	exit;	  
		 }
		 
		public function useremailvalidAction(){
		  
			$user = new Default_Model_Classes_Setting($this->current_user);
			$useremail = $_POST['usermail']; 
			$user->checkExistUserEmail($useremail);
			$count = $user->__get('selectExistUserEmail');
 			if($count > 0) echo 'exist';
 			else echo 'notexist';
				exit;  
			 }
			 
		public function oldpasswordAction(){
		  
			$user = new Default_Model_Classes_Setting($this->current_user);
			$customerId = $_POST['customer_id'];$password = $_POST['password'];
			$user->checkOldPassword($customerId,$password);
			$oldPasswordFlag = $user->__get('selectExistOldPassword');
			echo $oldPasswordFlag;
			exit;
		}
		
		public function resendmailAction(){
			
			$user = new Default_Model_Classes_Setting($this->current_user);
		    $email = $_POST['email'];
			$user->resendMailSend($email,$this->current_user);
			$fetchResultForMailSend = $user->__get('resendMailData');

			//code for resend mail link
			if($fetchResultForMailSend > 0){
				echo "Please enter email id";
			}else{
				$db = Zend_Db_Table::getDefaultAdapter();
				$sql = "UPDATE user set user_email_address='".$email."' where id='".$this->current_user."'";
				$db->query($sql);
				$sqlanother = "UPDATE username set name ='".$email."' where id = '".$this->current_user."'";
				$db->query($sqlanother);
				$user->getDetailsFromUser($this->current_user);
				$selectedDataForResend = $user->__get('currentUserData');
				$customerName=$selectedDataForResend[0]['user_full_name'];
				$linkVal= makeUrl($this->current_user);
				$html = new Zend_View();
				$html->setScriptPath(APPLICATION_PATH . '/modules/default/views/emails/');
				$mail = new Zend_Mail('utf-8');
				$html->LINKVALUE= '<a href="'.$linkVal.'" target="_blank">Click here to verify your account</a>';
				$html->USER_EMAIL= $email;	
				$html->USERNAME= $customerName;
				$notMyAccountUrl = UrlforNotMyAccount($this->current_user);
				$notmyaccount='<a href="'.$notMyAccountUrl.'"target="_blank" style="font-family:Trebuchet MS, Arial, helvetica; font-size:11px;">not my account</a>';
				$html->NOT_MY_ACCOUNT=$notmyaccount;
				
				if($customerName!=''){
					
					$mail->setSubject('Important: verify your email address!');
					$bodyTextNew = $html->render('verifyaccountaftersignuptocustomer.phtml');
					$mail->setFrom('support@goo2o.com','Go o2o');
					$mail->setBodyHtml($bodyTextNew);
					$mail->addTo($email);
					$mail->send();
					exit;
					}
				}
				//end code for resend mail link
			}
			
			public function forgetpasswordAction(){
				
				$user = new Default_Model_Classes_Setting($this->current_user);
				$user->getDetailsFromUser($this->current_user);
				$selectedDataForResend = $user->__get('currentUserData');
				$customerName=$selectedDataForResend[0]['user_full_name'];
				$encryptedmail=makeUrlForConfirmPassword($this->current_user);
				$html = new Zend_View();
				$html->setScriptPath(APPLICATION_PATH . '/modules/default/views/emails/');
				$html->EMAIL_ADDRESS = $selectedDataForResend[0]['user_email_address'];
				$html->EMAIL_CUSTOMER_NAME= $customerName;
				$html->LOGIN_LINK='<a href="'.HTTPS_SERVER_REDIRECTION_PORTAL.'index.php?main_page=login" target="_blank">Login</a>';
				$html->PASSWORD_LINK='<a href="'.$encryptedmail.'" target="_blank">'.$encryptedmail.'</a>';
				$html->ACCOUNT_SETTINGS='<a href="'.HTTP_SERVER_WITHOUT_HTTP.'/settings" target="_blank">account settings</a>';
				$bodyText = $html->render('resetpasswordmailtocustomer.phtml');
				$mail = new Zend_Mail('utf-8');
				$mail->setSubject("Reset your iglobul password!");
				$mail->setFrom('support@goo2o.com','Go o2o');
				$mail->setBodyHtml($bodyText);
				$mail->addTo($selectedDataForResend[0]['user_email_address']);
				$mail->send();
				$emailsent=true;
				exit;
				}
			
		public function preferencesAction(){
				
			$this->view->headScript()->appendFile('/jscript/default/preferencesetting.js');
			$this->view->headLink()->appendStylesheet('/css/default/header.css');
			$this->view->headLink()->appendStylesheet('/css/default/preferencesetting.css');
			$this->view->headTitle('Settings - o2o');
			$this->current_user = (int)$_SESSION['USER']['userId'];
			$user = new Default_Model_Classes_Setting($this->current_user);
			$user->completeArrayOfCategories();
			$selectedArrayOfCategories = $user->__get('completeArrayOfCategories');
			for($i=0;$i<count($selectedArrayOfCategories);$i++){
				
				$selectcat[$selectedArrayOfCategories[$i]['id']][] = $selectedArrayOfCategories[$i];//all categories listing according to department 2D array
				
				}

				$this->view->selectcat = $selectcat; //send to html
				$user->listingOfCategories($this->current_user);
				$selectedCustomerCategories = $user->__get('selectedCustomerCategories');//this is the categories store in user_dept
				
				for($i=0;$i<count($selectedCustomerCategories);$i++){
					
					$categoriesid.= rtrim($selectedCustomerCategories[$i]['cat_id'],','); //string of customer preferenced categories concatenated by comma
					}
					
				
				if($categoriesid!=''){
					
					$user->selectDepartmentOfCategories($categoriesid);
					$selectdeptid = $user->__get('selectdeptid');//department of selected categories
					}
				
				for($i=0;$i<count($selectdeptid);$i++){
					
					$totaldeptid[$selectdeptid[$i]['department_id']] = $selectdeptid[$i]; //selected department wid indexing department id
					}
					
					
					
				
				$this->view->totaldeptid=$totaldeptid;   //send to html
				for($i=1;$i<=count($selectcat);$i++){
					
					$departmentid = $selectcat[$i][0]['id'];
					$user->selectCategoriesByDepartment($departmentid);
					$indcat[] = $user->__get('getCategoriesByDepartment');
					$catid = explode(',',$categoriesid);
					}

					
				for($i=0;$i<count($indcat);$i++){
					
					for($k=0;$k<=count($catid);$k++){
						
						if($catid[$k]==$indcat[$i][$k]['categories_id']){
							
							$selectclass[$indcat[$i][$k]['categories_id']]='shoppingActive';//make a class for enabling text
							
						}
					}
				}
		}
		
		public function subscribeAction(){
			
			$deptid = $_POST['deptid'];
			$user = new Default_Model_Classes_Setting($this->current_user);
			
			if($_POST['value']=='subscribe'){
			
				$user->selectCategoriesByDepartment($deptid);
				$arrayOfsubscribeCategories = $user->__get('getCategoriesByDepartment');
				for($i=0;$i<count($arrayOfsubscribeCategories);$i++){
					$categoriesid.=$arrayOfsubscribeCategories[$i]['categories_id'].',';
					}
				$categoriesid = substr($categoriesid, 0, -1); 
				$db = Zend_Db_Table::getDefaultAdapter();
				$sql = "INSERT INTO user_dept SET user_id ='".$this->current_user."',dept_id='".$deptid."',cat_id ='".$categoriesid."'";
				$db->query($sql);	
				exit;
			}
			
			if($_POST['value']=='unsubscribe'){
				
				$db = Zend_Db_Table::getDefaultAdapter();
				$sql = "DELETE FROM user_dept WHERE user_id='".$this->current_user."' AND dept_id='".$deptid."'";
				$db->query($sql);
				exit;
				}	
			}
			
			public function savecategoryonebyoneAction(){
				
				if($_POST['checkbox']=='checked'){
					$selectedcatid = $_POST['catid'];
					
					if($selectedcatid!=''){
					
						$dept_catid = explode('_',$selectedcatid);
						$dept_id = $dept_catid[0];
						$catid = $dept_catid[1];				
						$customer_id = $this->current_user;
						$user = new Default_Model_Classes_Setting($this->current_user);
						$user->listingOfCategoriesWithDepartment($this->current_user,$dept_id);
						$categorylisting = $user->__get('selectedCustomerCategoriesWithDepartment');
						for($i=0;$i<count($categorylisting);$i++){
							$categoriesid.= rtrim($categorylisting[$i]['cat_id'],','); //string of customer preferenced categories concatenated by comma
							}
							
							if($categoriesid!=''){
								$categoriesid = $categoriesid.','.$catid;
							}else{
								$categoriesid = $catid;
								}
						
						if(count($categorylisting)==1){
						
							$db = Zend_Db_Table::getDefaultAdapter();
							$sql = "update user_dept SET cat_id='".$categoriesid."'WHERE user_id='".$this->current_user."'AND dept_id='".$dept_id."'";
							$db->query($sql);
							
						}else if(count($categorylisting)==0){
							
							$db = Zend_Db_Table::getDefaultAdapter();
							$sql = "insert into user_dept SET cat_id='".$categoriesid."',user_id='".$this->current_user."',dept_id='".$dept_id."'";
							$db->query($sql);
							
							}
					}
					exit;
				
				}
				
				if($_POST['checkbox']=='unchecked'){
					
					
                    
					exit;
					}
				
			}
	}
?>
