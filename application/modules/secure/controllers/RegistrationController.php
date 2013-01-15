<?php
/**
 * @author : Nagendra yadav
 * Used for User registration actions
 * @var private $publickey : public captcha key
 * @var private $privatekey : private captcha key
 * func init
 * setting the values for $publickey and $privatekey
 * @obj $mapper : object of registermapper class
 * @var $returnedLocationArray : Location array
 * @var $cityList : City list array
 * @obj $selectBox : object of Zend_Form_Element_Select class
 */
require_once 'recaptchalib.php'; // for recaptcha path root\library
class Secure_RegistrationController extends Zend_Controller_Action
{
    public function init()
    {
                        $this->view->controller = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
                        $this->publickey = "6LfgNAoAAAAAAEb9m-vKAwvOtZulVJkoogKwC40v"; // recaptcha public key
                        $this->privatekey = "6LfgNAoAAAAAAOrGvWEemmjTn62kqvYe-exXJbsC "; // recaptcha private key
                        $this->objTrigger = new Notification();
                        $this->view->headLink()->appendStylesheet('/css/secure/registration.css');
                        $this->RegObj  = new Secure_Model_RegistrationMapper(); // object for register mapper class
    }
    // main action for register process
    public function indexAction()
    {
                        $db = Zend_Db_Table::getDefaultAdapter();
			Zend_Layout::getMvcInstance()->setLayout('secure');                       
			Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');
                        $this->view->headTitle('Registration: Create your Goo2o Account - Goo2o.com');
			$this->view->headScript()->appendFile('/jscript/common/o2ojslib.js','text/javascript');
			$this->view->headScript()->appendFile('/jscript/secure/registration.js','text/javascript');
			$this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
			$this->view->publickey = $this->publickey;
			$this->view->privatekey = $this->privatekey;
                        $tabValue = $_REQUEST['tab'];
                        $this->view->tabs= $tabValue; // tab valueset for redirection
                        if(isset($_SESSION['USER']['userId'])){
                        $this->_redirect(HTTP_SERVER.'/myaccount');
                        }                      			
			$returnedStateArray = $this->RegObj->getStateList(); // getting Country list
			$this->view->states=$returnedStateArray;
			$this->view->totalattempt=0;
			//$this->view->city=$this->RegObj->getLocationList(0,'');
		 	if($_POST['action']=='process'){
                        $numusername=$this->RegObj->checkUsernamenumExists($_POST['username']);
                        $numemail=$this->RegObj->checkEmailIdnumExists($_POST['user_email_address']);
			$resp = recaptcha_check_answer ($this->view->privatekey,
			$_SERVER["REMOTE_ADDR"],
			$_POST["recaptcha_challenge_field"],
			$_POST["recaptcha_response_field"]);
			if (!$resp->is_valid) {
			$this->view->totalattempt=$_POST["attempt"]+1;
			$this->view->captchaErr='The characters you entered didn\'t match the word verification. Please try again';
			//$captchaErr='The characters you entered didn\'t match the word verification. Please try again';
			} else if($numusername==0 && $numemail==0 ) {
                                        $class=new General();
					$idUser=$this->RegObj->saveregistration($_POST);
                                        $userName = new Zend_Session_Namespace('USER');// session user
                                        $userName->userId = $idUser['lastinserted'];// settig the userid in the session user
                                        $userName->onceconfirmation=1;                                      
                                        $sessName = new Zend_Session_Namespace('SESSION');// namespace session
                                        $sessName->thissessid = '' ;// setting the session value
                                        $sessName->ApiKey = $idUser['detail']['apikey'];
					$original = new Zend_Session_Namespace('original_login');//
					$original->apikey=$idUser['detail']['apikey'];
                                        $userdet = $class->getLoggedUserDetails('id',$idUser['lastinserted']);
                                        $userName->userDetails = $userdet ;
                                        if($_POST['sapikey']!='')
					{								 	
$db->query("insert into store_follow_customer set capikey ='".$idUser['detail']['apikey']."',sapikey ='".$_POST['sapikey']."',folowing='1',follow_time=".time()."");		
					}
                                        $original->user=$userdet;
                                        $original->userId=$idUser['lastinserted'];
                                        $_SESSION['USER']['userDetails']['0']['vcode']=$vcodes;
                                        $update_session=$class->updateSessionname($userdet,$idUser['lastinserted']);
                                        setcookie("asess", $apiKey, time()+3600, "/", ".goo2ostore.com");

                                        $vcodes =  $class->makeUrl($idUser['lastinserted']);
                                        $_SESSION['USER']['userDetails']['0']['vcode']=$vcodes;
                                        $_SESSION['original_login']['user']['0']['vcode']=$vcodes;
                                        $vcode = HTTPS_SECURE.'/registration/mailconfirmation/passcode/'.$vcodes;
                                        $notmyaccountlink = HTTPS_SECURE.'/login/notyouraccount/passcode/'.$vcodes;
                           /*----@Trigger NO: 1 @created by : mrunal kanti roy @date : 12-11-2011 -------------*/
                                       $tData = array( 'account_verification_link'=>$vcode,
                                                       'not_my_account_link'=>$notmyaccountlink,
                                                       'to_id'=>$idUser['lastinserted'],
                                                       'to_mail'=>$idUser['user_email_address'],
                                                       'to_name'=>$idUser['user_full_name']);
                                        $this->objTrigger->triggerFire(1,$tData);															
                                                $tabValue=$_REQUEST['tab'];										
						$restoerecontent=new Secure_Model_Cart();
						$restoerecontent->restoreContent();	                                               
						switch($tabValue){ // add more cases for different redirection , snapshot implementation
							case 1 :
									$this->_redirect(HTTP_SECURE.'/cart/selectshippingaddress');
									break;
							case 2 :
								$this->_redirect(HTTP_SECURE.'/merchantenrollment/business-details');
									break; 
							default:
								if($_SESSION['mypage']=='')
									$this->_redirect(HTTP_SECURE."/registration/checkyourmail/passcode/".$vcodes);
								 else if($_SESSION['mypage'] == HTTP_SECURE.'/demo/create-my-free-demo-store')
									$this->_redirect(HTTP_SECURE . '/demo/store-created-successfully');
								else
									$this->_redirect($_SESSION['mypage']);
								break;
							} // end switch
                        }
			$returnedStateArray = $this->RegObj->getStateList(); // getting Country list
			$this->view->stateid=$_POST['state_name'];
			$this->view->states=$returnedStateArray;
			//$this->view->city=$this->RegObj->getLocationList($_POST['state_name'],$_POST['city_name']);                       
        }
		
    } // end function index action
	
    public function mailconfirmationAction(){
        $this->view->headTitle('Email Confirmation - Goo2o.com');
        $this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
        Zend_Layout::getMvcInstance()->setLayout('secure');
       	Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');
        $this->view->headScript()->appendFile('/jscript/common/o2ojslib.js','text/javascript');
        $Input = $this->_request->getParams();
        $this->userName = new Zend_Session_Namespace('USER');
        $this->view->userid=$this->userName->userId;
        $flag=$this->RegObj->resendEmailVerification($Input['passcode']);
        $flaguseremails=$this->RegObj->resendnewEmailVerification($Input['passcode']);
          if($flag<=0 && $flaguseremails<=0)
         {
              if($this->userName->userId=='')
              {
                  $this->_redirect(HTTP_SECURE.'/login');
              }
              else {
                  $this->_redirect(HTTP_SERVER.'/myaccount');
              }
         }

        else if($flag<=0)
        {
                 $uname =$this->RegObj->getuseremailsdetail($Input['passcode']);
                 $this->view->username =  $uname[0]['user_email'];
                 $this->RegObj->updateemailstatus($Input['passcode']);
           /*----@Trigger NO: 7 @created by : mrunal kanti roy @date : 15-11-2011 -------------*/
                   $tData = array( 'to_id'=>$uname[0]['uid'],
                                   'to_mail'=>$uname[0]['user_email'],
                                   'to_name'=>$uname[0]['user_full_name']);
                   $this->objTrigger->triggerFire(7,$tData);
        }  
        else
        {
                 $uname =$this->RegObj->getusername($Input['passcode']);
                 $this->view->username =  $uname[0]['user_email_address'];
                 $this->RegObj->updateemailstatus($Input['passcode']);
           /*----@Trigger NO: 7 @created by : mrunal kanti roy @date : 15-11-2011 -------------*/
                 $tData = array( 'to_id'=>$uname[0]['uid'],
                                   'to_mail'=>$uname[0]['user_email_address'],
                                   'to_name'=>$uname[0]['user_full_name']);
                 $this->objTrigger->triggerFire(7,$tData);
        }
         
 }// end of mailconfirmation action
	
	
     public function emailalreadyuseAction()
    { 
        Zend_Layout::getMvcInstance()->setLayout('secure');
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');
        $this->view->headTitle('Email Already in use - Goo2o.com');
        $this->view->headScript()->appendFile('/jscript/common/o2ojslib.js','text/javascript');
        $this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
       
	
    } 
     public function checkyourmailAction()
    {
        $userName = new Zend_Session_Namespace('USER');// session user
        Zend_Layout::getMvcInstance()->setLayout('secure');
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');
        $this->view->headTitle('Verify your Email – Goo2o.com');
        $this->view->headScript()->appendFile('/jscript/common/jquery-1.4.2.min.js');
        $this->view->headScript()->appendFile('/jscript/secure/checkyourmail.js');
        $this->view->headScript()->appendFile('/jscript/common/o2ojslib.js','text/javascript');
        $this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
        $Input = $this->_request->getParams();
        $uservcodes =  $Input['passcode'];
        $this->view->vcodes= $uservcodes;
	$returnusrnameArray = $this->RegObj->getusername($uservcodes);
        $newuseremail=$returnusrnameArray[0]['user_email_address'];
        $this->view->useremailaddress = $newuseremail;
        $this->view->useremailaddresses= $newuseremail;
        $this->view->userfullname=$returnusrnameArray[0]['user_full_name'];
        $newuserid=$returnusrnameArray[0]['uid'];
        $this->view->userid=$newuserid;
        $email=trim($_POST['newusrmail']);
        $getuservcode=$_POST['vcode'];
        $getuserid=$_POST['usernameid'];
        $fullusername=$_POST['user_full_name'];
        $this->view->emailExist=0;
        $returnusrecancelstatus = $this->RegObj->checkcanceleduser($uservcodes);
        $this->view->usercancelstatus=$returnusrecancelstatus;
        $valuseremail=$this->RegObj->checkEmailIdExistsforemail($email,$getuserid);       
        $pageaction=$_POST['formaction'];
        $this->view->display='none';
       //if($userName->onceconfirmation!=1)
       //{
       //  $this->_redirect(HTTP_SECURE.'/login');
      // }
       if($pageaction == 'checkyourmail' && $newuseremail == $email)
        {           
              $vcode = HTTPS_SECURE.'/registration/mailconfirmation/passcode/'.$getuservcode;
              $notmyaccountlink = HTTPS_SECURE.'/login/notyouraccount/passcode/'.$getuservcode;
   /*----@Trigger NO: 1 @created by : mrunal kanti roy @date : 12-11-2011 -------------*/
              $tData = array('account_verification_link'=>$vcode,
                             'not_my_account_link'=>$notmyaccountlink,
                             'to_id'=>$newuserid,
                             'to_mail'=>$email,
                             'to_name'=>$fullusername,
                             'no_alert_flag'=>'YES');
              $this->objTrigger->triggerFire(1,$tData);
				if($_SESSION['mypage']=='')
					$this->_redirect(HTTP_SERVER.'/myaccount');
				else
					$this->_redirect($_SESSION['mypage']);             
        }
      else if($pageaction == 'checkyourmail' && $valuseremail==0)
       { 
              $orilogin= new Zend_Session_Namespace('original_login');
              $vcode = HTTPS_SECURE.'/registration/mailconfirmation/passcode/'.$getuservcode;
              $notmyaccountlink = HTTPS_SECURE.'/login/notyouraccount/passcode/'.$getuservcode;
              $fromemail= $email;
              $from=$fullusername;
	      $tData = array('account_verification_link'=>$vcode,
                             'not_my_account_link'=>$notmyaccountlink,
                             'to_id'=>$newuserid,
                             'to_mail'=>$email,
                             'to_name'=>$fullusername,
                             'no_alert_flag'=>'YES');
              $this->objTrigger->triggerFire(1,$tData);
              $this->RegObj->updateuseremail($getuservcode,$email,$getuserid);
              $_SESSION['USER']['userDetails']['0']['user_email_address']=$email;
              $orilogin->user[0]['user_email_address']=$email;
              $this->_redirect(HTTP_SERVER.'/myaccount');
        } 
        else if($pageaction == 'checkyourmail' && $valuseremail>0)
        {
             $this->view->emailExist = 1;
             $this->view->useremailaddresses = $email;
             $this->view->display='block';   
             return false;
        }
        
    
  }// end of checkyourmail action
	
    /*public function getcitiesAction(){
             $returnedLocationArray = $this->RegObj->getLocationList($_POST['stateid'],'','1'); // getting location list
             exit;
    }*/
    public function checkusernameAction(){
             $this->_helper->layout->disableLayout();
             $this->_helper->viewRenderer->setNoRender(true);
             $this->RegObj->checkUsernameExists($_POST['username']);
             exit;
    }
     public function checkemailAction(){
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $email = $_POST['email'];
            $this->RegObj->checkEmailIdExists($email);
            exit;
    }
    public function resendconfirmationAction(){                  
            $Input = $this->_request->getParams();
            $uservcodes =  $Input['passcode'];
            $this->view->vcodes= $uservcodes;
            $returnusrnameArray = $this->RegObj->getusername($uservcodes);
            $email=$returnusrnameArray[0]['user_email_address'];
            $userfullname=$returnusrnameArray[0]['user_full_name'];
            $vcode = HTTPS_SECURE.'/registration/mailconfirmation/passcode/'.$uservcodes;
            $notmyaccountlink = HTTPS_SECURE.'/login/notyouraccount/passcode/'.$uservcodes;
            /*----@Trigger NO: 1 @created by : mrunal kanti roy @date : 24-11-2011 -------------*/
            $tData = array('account_verification_link'=>$vcode,
                 'not_my_account_link'=>$notmyaccountlink,
                 'to_id'=>$returnusrnameArray[0]['uid'],
                 'to_mail'=>$returnusrnameArray[0]['user_email_address'],
                 'to_name'=>$returnusrnameArray[0]['user_full_name'],
                 'no_alert_flag'=>'YES');
            $this->objTrigger->triggerFire(1,$tData);
            if($Input['return']!='')                         
            $this->_redirect($Input['return']);
    }// end of resendconfirmation action
    public function newmailconfirmationAction(){
            $this->view->headTitle('Email Confirmation - Goo2o.com');
            $this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
            Zend_Layout::getMvcInstance()->setLayout('secure');
            Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');
            $this->view->headScript()->appendFile('/jscript/common/o2ojslib.js','text/javascript');
            $Input = $this->_request->getParams();
            $this->userName = new Zend_Session_Namespace('USER');
            $this->view->userid=$this->userName->userId;
            $uname =$this->RegObj->getuseremailname($Input['passcode']);
            $this->view->username =  $uname[0]['user_email'];
            $flag=$this->RegObj->resendnewEmailVerification($Input['passcode']);
            if($flag <= 0)
            {
                  if($this->userName->userId=='')
              {
                  $this->_redirect(HTTP_SECURE.'/login');
              }
              else {
                  $this->_redirect(HTTP_SERVER.'/myaccount');
              }
            }
            else
            {
                 $this->RegObj->updatenewemailstatus($Input['passcode']);
            }
      }// end of newmailconfirmation action   
}
?>
