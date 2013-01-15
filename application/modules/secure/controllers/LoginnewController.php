<?php

/**
 * @author : Nagendra Yadavs
 * Used for Login Controller
 * func init setting layout files and appending script link and title
 * func indexAction
 * @var tabValue for redirection after login 1: cart default : home
 * @var $urlData : json data
 * @var $userData : decoded json data to php
 * @var $apiSessData : current session value as in case of API implementation
 * @var $usernameError : storing username error if any
 * @var $passwordError : storing password error if any
 * @var $usersid : userid of the profiled user
 * @obj $sessName : object for namespace Session
 * @obj $userName : object for namespace User
 * func registerUser : register a user
*/
require_once 'recaptchalib.php';
class Secure_LoginnewController extends Zend_Controller_Action {
    private $publickey;
    private $privatekey;
    private $userid;
    private $objTrigger;
    public function init() {  
       $uid= new Zend_Session_Namespace('USER');
       $vcodes=$uid->userDetails[0]['vcode'];
       $Inputs = $this->_request->getParams();

         if(isset($vcodes) && isset($_SESSION['USER']['userId']))
         {
         $mapper  = new Secure_Model_LoginMapper();
         $checkuserverification=$mapper->checkusrverification($vcodes);
         $checkcanceluser=$mapper->checkcanceleduser($vcodes);
		         $Inputs = $this->_request->getParams();

         if($checkcanceluser>0)
         {
            $this->_redirect(HTTP_SECURE.'/login/notmyaccount/passcode/'.$vcodes);
         }
         if(isset($_SESSION['USER']['userId']) && $checkuserverification==0){

            $this->_redirect(HTTP_SECURE.'/login/accountrestricted/passcode/'.$vcodes);
        }
		
		 if($Inputs['action']=='login')
		 {
         if(isset($_SESSION['USER']['userId'])){
				//if($Inputs['action']!='notmyaccount')
				//{
				$this->_redirect(HTTP_SERVER.'/myaccount');
				//}
            }
		
		}
     }
        $this->publickey = "6LfgNAoAAAAAAEb9m-vKAwvOtZulVJkoogKwC40v "; // recaptcha public key
        $this->privatekey = "6LfgNAoAAAAAAOrGvWEemmjTn62kqvYe-exXJbsC "; // recaptcha private key
        Zend_Layout::getMvcInstance()->setLayout('secure'); // setting the layout file to secure
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH . '/modules/secure/layouts');
        $this->view->headScript()->appendFile('/jscript/common/jquery-1.4.2.min.js');
        $this->view->headScript()->appendFile('/jscript/secure/login.js', 'text/javascript');
        $this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
        $this->view->headLink()->appendStylesheet('/css/secure/registration.css');
        $this->objTrigger = new Notification();

    }

    public function indexAction() {
        $this->view->headTitle('Sign in: Sign in to your Goo2o account - Goo2o.com');
        $this->view->headMeta()->setName('keywords', 'Login , GoO2o Technologies');
        $this->view->headMeta()->setName('description', 'Login , GoO2o Technologies');
        $this->view->captchview = 0;
        $this->view->controller = 'login';
        $this->view->publickey = "6LfgNAoAAAAAAEb9m-vKAwvOtZulVJkoogKwC40v "; // recaptcha public key
        $this->view->privatekey = "6LfgNAoAAAAAAOrGvWEemmjTn62kqvYe-exXJbsC "; // recaptcha private key
        $tabValue = $_REQUEST['tab'];
        $sapi = $_REQUEST['sapi'];
        $this->view->tabs = $tabValue; // tab valueset for redirection
        $this->view->userempty = 0;
        $this->view->lessthansix = 0;
        $this->view->passwordempty = 0;
        if ($_POST['action'] == 'process') { // when posted
            if ($_POST['customer'] == 1) { // when new customer registers
                $mapper = new Secure_Model_LoginMapper();
                $numusername = $mapper->checkUsernameExists($_POST['username']);
                if ($pos = strpos($_POST['username'], '@')) {
                    $url = 'user_email_address=' . $_POST['username'];
                    $email = $_POST['username'];
                    $numemail = $mapper->checkEmailIdExists($email);
                    if ($numemail > 0) {
                        $this->_redirect(HTTP_SECURE . '/registration/emailalreadyuse?' . $url);
                    }
                } else if ($numusername > 0) {
                    $url = 'username=' . $_POST['username'];
                    $this->_redirect(HTTP_SECURE . '/registration/emailalreadyuse?' . $url);
                } else {
                    $url = 'username=' . $_POST['username'];
                }

                if ($_POST['tab'] != '')
                    $addUrl = '&tab=' . $_POST['tab'];


                $this->_redirect(HTTP_SECURE . '/registration?' . $url . $addUrl);
            }
            $captchaErr = "";
            if (isset($_POST['remember'])) {
                $mapper = new Secure_Model_LoginMapper();
                $mapper->updateSessionlifetime();
                //setcookie("c_n", base64_encode($_POST['username']), time()+86400*7, "/");
                //setcookie("c_p", base64_encode($_POST['password']), time()+86400*7, "/");
                $this->view->captchapost = 1;
            } else {
                //setcookie("c_n", base64_encode($_POST['username']), time()-86400*7, "/");
                //setcookie("c_p",base64_encode($_POST['password']), time()-86400*7, "/");
                $this->view->captchapost = 0;
            }


            // fetching the tab value
            if ($_POST['captcha'] == 1 && $_POST['username'] != '') {
                $resp = recaptcha_check_answer($this->view->privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
                if (!$resp->is_valid) {
                    // What happens when the CAPTCHA was entered incorrectly
                    $this->view->captchaErr = 'The characters you entered didn\'t match the word verification. Please try again';
                    $captchaErr = 'The characters you entered didn\'t match the word verification. Please try again';
                } else {
                    // Your code here to handle a successful verification
                }
            }
            $this->view->usernamepost = $_POST['username'];
            $this->view->passwordpost = $_POST['password'];

            function curl($url) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
                curl_setopt($ch,CURLOPT_USERPWD, 'skywalker:dexter');
                return curl_exec($ch);
                curl_close($ch);
            }

            $class = new General();
            $feed = HTTP_SECURE . "/signin/json?username=" . $_POST['username'] . "&password=" . $_POST['password'];
            $urlData = curl($feed);
            $userData = Zend_Json::decode(trim($urlData)); // getting value as json, decoding it
            $apiKey = (string) $userData['apikey'];
            $apiSessData = (string) $userData['apisessid']; //session
            $usernameError = (string) $userData['username']; // if any error
            $allError = (string) $userData['all']; // if any error
            $passwordError = (string) $userData['password']; // if any error
            $captchaErr = (string) $userData['captchaappear']; // if any error
            $usersid = (string) $userData['userid']; // userid of the profiled user
            $this->view->userErr = $usernameError;
            $this->view->passErr = $passwordError;
            $mapper = new Secure_Model_LoginMapper();
            $valusername = $mapper->checkUsernameExists(trim($_POST['username']));
            if ($pos = strpos($_POST['username'], '@')) {
                $url = 'user_email_address=' . $_POST['username'];
                $email = $_POST['username'];
                $valusername = $mapper->checkEmailIdExists($email);
            }

            if (trim($_POST['username']) == '' && trim($_POST['password']) == '') {
                $this->view->userempty = 1;
                $this->view->passwordempty = 1;
            } else if ($valusername > 0 && trim($_POST['password']) == '') {
                $this->view->passwordempty = 1;
            } else if (trim($_POST['username']) == '' && trim($_POST['password']) != '') {
                $this->view->userempty = 1;
            }

            if ($valusername > 0 && strlen(trim($_POST['password'])) > 0 && strlen(trim($_POST['password'])) < 6 && trim($_POST['customer'] != 1)) {
                $this->view->lessthansix = 1;
            } else if ($valusername > 0 && trim($_POST['password']) != '') {
                $this->view->allError = $allError;
            }
            if ($userData['captchaappear'] == 1 || $_POST['captcha'] == 1)
                $this->view->captchview = 1;
            if ($_POST['username'] == '') {
                $this->view->captchview = 0;
            }
            if ($usernameError == "" && $passwordError == "" && ($captchaErr == "") && $allError == "") {
                $mapper = new Secure_Model_LoginMapper();

                if ($pos = strpos($_POST['username'], '@')) {

                    $email = $_POST['username'];
                    $vcodes = $mapper->getvcodesemail($email);
                } else {
                    $vcodes = $mapper->getvcodesusername($_POST['username']);
                }

                $usrvcode = $vcodes[0]['vcode'];
                $user_join_date = $vcodes[0]['user_join_date'];
                $checkcanceluser = $mapper->checkcanceleduser($usrvcode);
                if ($checkcanceluser > 0) {
                    $this->_redirect(HTTP_SECURE . '/login/notmyaccount/passcode/' . $usrvcode);
                }
                $mapper = new Secure_Model_LoginMapper();
                $checkuserverifications = $mapper->checkusrverificationinprocess($usrvcode, $user_join_date);

                if ($checkuserverifications == 0) {
                    $this->_redirect(HTTP_SECURE . '/login/accountrestricted/passcode/' . $usrvcode);
                }
                $sessName = new Zend_Session_Namespace('SESSION'); // namespace session
                $userName = new Zend_Session_Namespace('USER'); // session user
                $original = new Zend_Session_Namespace('original_login'); //
                $original->apikey = $apiKey;

                $sessName->thissessid = $apiSessData; // setting the session value
                $sessName->ApiKey = $apiKey;
                $userName->userId = $usersid; // settig the userid in the session user
                $userdet = $class->getLoggedUserDetails('id', $usersid);

                $getMallsById = $class->getApiDetails($userdet[0]['user_email_address']);
                $userdet['stores'] = $getMallsById;
                $original->user = $userdet;
                $original->userId = $usersid;
                $userName->userDetails = $userdet;

                $update_session = $class->updateSessionname($userdet, $usersid);

                $mapper = new Admin_Model_IndexMapper();
                $mapper->changeUserprofile('', $usersid);
                setcookie("asess", $apiKey, time() + 3600, "/", ".sketcheeze.com");
                //setcookie("asess", $apiKey);
                $restoerecontent = new Secure_Model_Cart();
                $restoerecontent->restoreContent();
               
                if ($_GET['id'] == 'popup') {
                    echo '<script type="text/javascript" language="javascript">self.close();opener.parent.location.reload()</script>';
                    exit;
                }


                switch ($tabValue) { // add more cases for different redirection , snapshot implementation
                    case 1 :

                        $this->_redirect(HTTP_SECURE . '/cart/selectshippingaddress');
                        break;
                    case 2 :
                        $this->_redirect(HTTP_SECURE . '/merchantenrollment/business-details');
                        break;
                    default:
                    
                        if ($_SESSION['mypage'] == '')
                            $this->_redirect(HTTP_SERVER . '/admin');
                        
                        else if($_SESSION['mypage'] == 'http://secure.o2o.com/demo/create-demo-store')
							$this->_redirect(HTTP_SECURE . '/demo/congratulation');
						else
                            $this->_redirect($_SESSION['mypage']);
                        break;
                } // end switch
            }// end if
        }// end if
    }

// end function indexAction

    public function forgetpasswordAction() {
        Zend_Layout::getMvcInstance()->setLayout('secure');
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH . '/modules/secure/layouts');
        $this->view->captchview = 0;
        $this->view->controller = 'forgetpassword';
        $this->view->publickey = "6LfgNAoAAAAAAEb9m-vKAwvOtZulVJkoogKwC40v "; // recaptcha public key
        $this->view->privatekey = "6LfgNAoAAAAAAOrGvWEemmjTn62kqvYe-exXJbsC "; // recaptcha private key
        Zend_Layout::getMvcInstance()->setLayout('secure');
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH . '/modules/secure/layouts');
        $this->view->headTitle('Forgot your Password - Goo2o.com');
        $this->view->headScript()->appendFile('/jscript/common/jquery-1.4.2.min.js');
        $this->view->headScript()->appendFile('/jscript/secure/forgetpassword.js');
        $this->view->headScript()->appendFile('/jscript/common/o2ojslib.js', 'text/javascript');
        $this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
        $mapper = new Secure_Model_LoginMapper();
        $valusername = $mapper->getvcodesusername(trim($_POST['changeduseremail']));
        if ($pos = strpos($_POST['changeduseremail'], '@')) {
            $valusername = $mapper->getvcodesemail(trim($_POST['changeduseremail']));
        }
        $newuseremail = $valusername[0]['user_email_address'];
        $this->view->useremailaddress = $newuseremail;
        $this->view->userfullname = $valusername[0]['user_full_name'];
        $this->view->vcodes = $valusername[0]['vcode'];
        $this->view->forgetpass = $valusername[0]['forgetpasscode'];
        $email = $_POST['changeduseremail'];
        $getuservcode = $valusername[0]['vcode'];
        $getuserid = $valusername[0]['uid'];
        $getuserfullusername = $valusername[0]['user_full_name'];
        $existpasscode = $valusername[0]['forgetpasscode'];
        $this->view->emailExist = 0;
        $pageaction = $_POST['forgetpasspage'];
        $valuseremail = count($valusername);
        $emailpasscode = rand(10000, 5000);
        $newemailpasscode = $existpasscode . ',' . $emailpasscode;
        $resp = recaptcha_check_answer($this->view->privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
        if (!$resp->is_valid && $valuseremail == 0 && $pageaction == 'forgetpasswordvalue') {
            $this->view->emailExist = 1;
            $this->view->useremailaddresses = $email;
            return false;
        } else if (!$resp->is_valid && $pageaction == 'forgetpasswordvalue') {
            $this->view->useremailaddresses = $email;
            return false;
        } else if ($pageaction == 'forgetpasswordvalue' && $valuseremail == 0) {
            $this->view->emailExist = 1;
            $this->view->useremailaddresses = $email;
            return false;
        } else if ($pageaction == 'forgetpasswordvalue' && $valuseremail > 0) {
            $mapper = new Secure_Model_LoginMapper();
            $vcode = HTTPS_SECURE . '/login/resetpassword/passcode/' . $getuservcode . '/emailpasscode/' . $emailpasscode;
//          $html = new Zend_View();
//          $html->setScriptPath(APPLICATION_PATH . '/modules/secure/views/emails/');
//          $html->assign('name',$getuserfullusername);
//          $fromemail= $email;
//          $from=$fullusername;
//          $html->assign('link',$vcode);
//          $mail = new Zend_Mail('utf-8');
//          $bodyText = $html->render('forgetpasswordinstructions.phtml');
//          $mail->addTo($email);
//          $mail->setSubject('Change Password');
//          $mail->setFrom($fromemail,$from);
//          $mail->setBodyHtml($bodyText);
//          //$mail->send();
            $mapper->updateforgetpasstatus($getuservcode, $newemailpasscode);

            /* ----@Trigger NO: 3 @created by : mrunal kanti roy @date : 13-11-2011 ------------- */
            $tData = array('password_reset_link' => $vcode,
                'to_id' => $valusername[0]['id'],
                'to_mail' => $email,
                'to_name' => $valusername[0]['user_full_name']);
            $this->objTrigger->triggerFire(3, $tData);

            $this->_redirect(HTTP_SECURE . '/login/forgetpasswordinstruction/passcode/' . $getuservcode);
        }
    }

// end function index action

    public function forgetpasswordinstructionAction() {
        Zend_Layout::getMvcInstance()->setLayout('secure');
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH . '/modules/secure/layouts');
        $this->view->headScript()->appendFile('/jscript/common/o2ojslib.js', 'text/javascript');
        $this->view->headTitle('Forgot your Password - Goo2o.com');
        $this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
        $this->view->controller = 'forgetpasswordinstruction';
        $Input = $this->_request->getParams();
        $uservcodes = $Input['passcode'];
        $this->view->vcodes = $uservcodes;
        $mapper = new Secure_Model_RegistrationMapper(); // object for register mapper class
        $returnusrnameArray = $mapper->getusername($uservcodes);
        $newuseremail = $returnusrnameArray[0]['user_email_address'];
        $this->view->useremailaddress = $newuseremail;
    }

// end function index action

    public function forgetpasswordresendinstructionAction() {
        Zend_Layout::getMvcInstance()->setLayout('secure');
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH . '/modules/secure/layouts');
        $this->view->headScript()->appendFile('/jscript/common/o2ojslib.js', 'text/javascript');
        $this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
        $this->view->controller = 'forgetpasswordresendinstruction';
        $Input = $this->_request->getParams();
        $uservcodes = $Input['passcode'];
        $mapper = new Secure_Model_LoginMapper();
        $userdetailrow = $mapper->getdetailpasscode($uservcodes);
        $existpasscode = $userdetailrow['forgetpasscode'];
        $getuserfullusername = $userdetailrow['user_full_name'];
        $email = $userdetailrow['user_email_address'];
        $emailpasscode = rand(10000, 5000);
        $newemailpasscode = $existpasscode . ',' . $emailpasscode;
        $vcode = HTTPS_SECURE . '/login/resetpassword/passcode/' . $uservcodes . '/emailpasscode/' . $emailpasscode;

//          $html = new Zend_View();
//          $html->setScriptPath(APPLICATION_PATH . '/modules/secure/views/emails/');
//          $html->assign('name',$getuserfullusername);
//          $fromemail= $email;
//          $from=$fullusername;
//          $html->assign('link',$vcode);
//          $mail = new Zend_Mail('utf-8');
//          $bodyText = $html->render('forgetpasswordinstructions.phtml');
//          $mail->addTo($email);
//          $mail->setSubject('Change Password');
//          $mail->setFrom($fromemail,$from);
//          $mail->setBodyHtml($bodyText)
        //$mail->send();
        $mapper->updateforgetpasstatus($uservcodes, $newemailpasscode);

        /* ----@Trigger NO: 3 @created by : mrunal kanti roy @date : 24-11-2011 ------------- */
        $tData = array('password_reset_link' => $vcode,
            'to_id' => $userdetailrow['uid'],
            'to_mail' => $userdetailrow['user_email_address'],
            'to_name' => $userdetailrow['user_full_name']);
        $this->objTrigger->triggerFire(3, $tData);
        $this->_redirect(HTTP_SECURE . '/login/forgetpasswordinstruction/passcode/' . $uservcodes);
    }

// end function index action

    public function resetpasswordAction() {
        Zend_Layout::getMvcInstance()->setLayout('secure');
        $this->view->headTitle('Reset Password - Goo2o.com');
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH . '/modules/secure/layouts');
        $this->view->headScript()->appendFile('/jscript/common/jquery-1.4.2.min.js');
        $this->view->headScript()->appendFile('/jscript/secure/resetpassword.js');
        $this->view->headScript()->appendFile('/jscript/common/o2ojslib.js', 'text/javascript');
        $this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
        $this->view->controller = 'resetpassword';
        $Input = $this->_request->getParams();
        $uservcodes = $Input['passcode'];
        $this->view->vcodes = $uservcodes;
        $useremailpasscode = $Input['emailpasscode'];
        $this->view->passemailcode = $useremailpasscode;
        $newpassword = $_POST['newpassword'];
        $posteduservcode = $_POST['newvcode'];
        $posteduserpasscode = $this->_getParam('emailpasscode'); //$_POST['hideforgetpasscde'];
        $postedpageaction = $_POST['hideresetpasscode'];
        $mapper = new Secure_Model_LoginMapper();
        $getdetailpasscodes = $mapper->getdetailpasscode($uservcodes);
        $uniquepasscode = explode(',', $getdetailpasscodes['forgetpasscode']);
        $usernameid = $getdetailpasscodes['uid'];
        if (!(in_array($posteduserpasscode, $uniquepasscode))) {
            $this->_redirect(HTTP_SECURE . '/login/forgetpassword');
        }
        if ($postedpageaction == 'hiddenpasswordvalue') {
            unset($uniquepasscode[array_search($posteduserpasscode, $uniquepasscode)]);
            $code_str = implode(',', $uniquepasscode);
            $mapper->updateusenamepassword($usernameid, $newpassword, $code_str, $posteduservcode);
            /* ---- @Trigger NO: 4 @created by : mrunal kanti roy @date : 14-11-2011 ------------- */
            $tData = array('to_id' => $getdetailpasscodes['uid'],
                'to_mail' => $getdetailpasscodes['user_email_address'],
                'to_name' => $getdetailpasscodes['user_full_name']);
            $this->objTrigger->triggerFire(4, $tData);
            $this->_redirect(HTTP_SECURE . '/login/resetpasswordinstruction');
        }
    }

// end function index action

    public function resetpasswordinstructionAction() {
        Zend_Layout::getMvcInstance()->setLayout('secure');
        $this->view->headTitle('Reset Password - Goo2o.com');
        $this->view->controller = 'resetpasswordinstruction';
        $Input = $this->_request->getParams();
        $this->userName = new Zend_Session_Namespace('USER');
        $this->view->userid=$this->userName->userId;
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH . '/modules/secure/layouts');
        $this->view->headScript()->appendFile('/jscript/common/o2ojslib.js', 'text/javascript');
        $this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
    }

// end function index action

    public function notmyaccountAction() {
        Zend_Layout::getMvcInstance()->setLayout('secure');
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH . '/modules/secure/layouts');
        $this->view->headTitle('Not my Account - Goo2o.com');
        $this->view->headScript()->appendFile('/jscript/common/jquery-1.4.2.min.js');
        $this->view->headScript()->appendFile('/jscript/secure/notmyaccount.js');
        $this->view->headScript()->appendFile('/jscript/common/o2ojslib.js', 'text/javascript');
        $this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
        $this->view->controller = 'notmyaccount';
        $Input = $this->_request->getParams();
        $uservcodes = $Input['passcode'];
        $this->view->vcodes = $uservcodes;
        $mapper = new Secure_Model_RegistrationMapper(); // object for register mapper class
        $returnusrnameArray = $mapper->getusername($uservcodes);
        $newuseremail = $returnusrnameArray[0]['notmyaccount_email'];
        $this->view->useremailaddress = $newuseremail;
        $this->view->userfullname = $returnusrnameArray[0]['user_full_name'];
        $this->view->username = $returnusrnameArray[0]['username'];
        $newuserid = $returnusrnameArray[0]['uid'];
        $this->view->userid = $newuserid;
        $email = trim($_POST['changeduseremail']);
        $getuservcode = $_POST['vcode'];
        $getuserid = $_POST['usernameid'];
        $fullusername = $_POST['user_full_name'];
        $username = $_POST['username'];
        $this->view->emailExist = 0;
        $this->view->sameemailExist = 0;
        $valuseremail = $mapper->checkEmailforcanceluser($email);
        $pageaction = trim($_POST['formaction']);
        if ($pageaction == 'notmyaccounts' && $newuseremail == $email) {
            $this->view->sameemailExist = 1;
            $this->view->useremailaddresses = $email;
            return false;
        } else if ($pageaction == 'notmyaccounts' && $valuseremail > 0) {
            $this->view->emailExist = 1;
            $this->view->useremailaddresses = $email;
            return false;
        } else if ($pageaction == 'notmyaccounts' && $valuseremail == 0) {
            $mapper = new Secure_Model_LoginMapper();
            $mapper->checknotmyaccount($_POST['vcode'], $_POST['changeduseremail'], $getuserid,$username);
            $vcode = HTTPS_SECURE . '/registration/mailconfirmation/passcode/' . $getuservcode;
            $notmyaccountlink = HTTPS_SECURE . '/login/notyouraccount/passcode/' . $getuservcode;
           /* $html = new Zend_View();
            $html->setScriptPath(APPLICATION_PATH . '/modules/secure/views/emails/');
            $html->assign('name', $fullusername);
            $fromemail = $email;
            $from = $fullusername;
            $html->assign('link', $vcode);
            $html->assign('notmyaccountlinks', $notmyaccountlink);
            $mail = new Zend_Mail('utf-8');
            $bodyText = $html->render('registertocustomer.phtml');
            $mail->addTo($email);
            $mail->setSubject('Email verifiction Mail');
            $mail->setFrom($fromemail, $from);
            $mail->setBodyHtml($bodyText);
            //$mail->send();
              */
            $tData = array( 'account_verification_link'=>$vcode,
                                                       'not_my_account_link'=>$notmyaccountlink,
                                                       'to_id'=>$newuserid,
                                                       'to_mail'=>$email,
                                                       'to_name'=>$fullusername);
                                        $this->objTrigger->triggerFire(1,$tData);
                                         
            $this->_redirect(HTTP_SECURE . '/accountsetting/logout');
        }
    }

// end function index action

    public function youremailremovedAction() {
        Zend_Layout::getMvcInstance()->setLayout('secure');
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH . '/modules/secure/layouts');
        $this->view->headTitle('Email Removed - Goo2o.com');
        $this->view->controller = 'youremailremoved';
        $this->view->headScript()->appendFile('/jscript/common/o2ojslib.js', 'text/javascript');
        $this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
        $Input = $this->_request->getParams();
        $vcodes = $Input['passcode'];
        $this->view->vcodes = $vcodes;
        $mapper = new Secure_Model_RegistrationMapper(); // object for register mapper class
        $returnusrnameArray = $mapper->getusername($vcodes);
        $newuseremail = $returnusrnameArray[0]['user_email_address'];
        $this->view->useremailaddress = $newuseremail;
        $mapper = new Secure_Model_LoginMapper(); // object for register mapper class
        $mapper->removeemail($vcodes);      
    }

// end function index action

    public function notyouraccountAction() {
        Zend_Layout::getMvcInstance()->setLayout('secure');
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH . '/modules/secure/layouts');
        $this->view->headScript()->appendFile('/jscript/common/o2ojslib.js', 'text/javascript');
        $this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
        $this->view->headTitle('Not my Account - Goo2o.com');
        $this->view->controller = 'notyouraccount';
        $Input = $this->_request->getParams();
        $this->userName = new Zend_Session_Namespace('USER');
        $this->view->userid=$this->userName->userId;
        $vcodes = $Input['passcode'];
        $this->view->vcodes = $vcodes;
        $uservcode = $_POST['vcode'];
        $pageaction = $_POST['formaction'];
        $mapper = new Secure_Model_RegistrationMapper(); // object for register mapper class
        $returnusrnameArray = $mapper->getusername($vcodes);
        $newuserid = $returnusrnameArray[0]['uid'];
        $this->view->usernameid = $newuserid;
        $newuseremail = $returnusrnameArray[0]['user_email_address'];
        $this->view->useremailaddress = $newuseremail;
        if ($pageaction == 'notmyaccount') {
            $this->_redirect(HTTP_SECURE . "/login/youremailremoved/passcode/" . $uservcode);
        }
    }

// end function index action

   public function accountrestrictedAction() {
        Zend_Layout::getMvcInstance()->setLayout('secure');
        $this->view->headTitle('Account Restricted – Goo2o.com');
        $this->view->controller = 'accountrestricted';
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH . '/modules/secure/layouts');
        $this->view->headScript()->appendFile('/jscript/common/jquery-1.4.2.min.js');
        $this->view->headScript()->appendFile('/jscript/secure/accountrestricted.js');
        $this->view->headScript()->appendFile('/jscript/common/o2ojslib.js', 'text/javascript');
        $this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
        $Input = $this->_request->getParams();
        $uservcodes = $Input['passcode'];
        $this->view->vcodes = $uservcodes;
        $mapper = new Secure_Model_RegistrationMapper(); // object for register mapper class
        $returnusrnameArray = $mapper->getusername($uservcodes);
        $newuseremail = $returnusrnameArray[0]['user_email_address'];
        $this->view->useremailaddress = $newuseremail;
        $this->view->useremailaddresses = $newuseremail;
        $this->view->userfullname = $returnusrnameArray[0]['user_full_name'];
        $newuserid = $returnusrnameArray[0]['uid'];
        $this->view->userid = $newuserid;
        $email = trim($_POST['changeduseremail']);
        $getuservcode = $_POST['vcode'];
        $getuserid = $_POST['usernameid'];
        $fullusername = $_POST['user_full_name'];
        $this->view->emailExist = 0;
        $valuseremail = $mapper->checkEmailIdExistsforemail($email, $getuserid);
        $pageaction = $_POST['restictedpage'];
        if ($pageaction == 'accountcheckprocess' && $newuseremail == $email) {
            $mapper = new Secure_Model_LoginMapper();
            $vcode = HTTPS_SECURE . '/registration/mailconfirmation/passcode/' . $getuservcode;
            $notmyaccountlink = HTTPS_SECURE . '/login/notyouraccount/passcode/' . $getuservcode;
            /*$html = new Zend_View();
            $html->setScriptPath(APPLICATION_PATH . '/modules/secure/views/emails/');
            $html->assign('name', $fullusername);
            $fromemail = $email;
            $from = $fullusername;
            $html->assign('link', $vcode);
            $html->assign('notmyaccountlinks', $notmyaccountlink);
            $mail = new Zend_Mail('utf-8');
            $bodyText = $html->render('registertocustomer.phtml');
            $mail->addTo($email);
            $mail->setSubject('Email verifiction Mail');
            $mail->setFrom($fromemail, $from);
            $mail->setBodyHtml($bodyText);*/
            //$mail->send();
	$tData = array( 'account_verification_link'=>$vcode,
                                                       'not_my_account_link'=>$notmyaccountlink,
                                                       'to_id'=>$newuserid,
                                                       'to_mail'=>$email,
                                                       'to_name'=>$fullusername);
                                        $this->objTrigger->triggerFire(1,$tData);
            
            $this->_redirect(HTTP_SECURE . '/accountsetting/logout');
           
        } else if ($pageaction == 'accountcheckprocess' && $valuseremail == 0) {
            $mapper = new Secure_Model_LoginMapper();
            $vcode = HTTPS_SECURE . '/registration/mailconfirmation/passcode/' . $getuservcode;
            $notmyaccountlink = HTTPS_SECURE . '/login/notyouraccount/passcode/' . $getuservcode;
           /* $html = new Zend_View();
            $html->setScriptPath(APPLICATION_PATH . '/modules/secure/views/emails/');
            $html->assign('name', $fullusername);
            $fromemail = $email;
            $from = $fullusername;
            $html->assign('link', $vcode);
            $html->assign('notmyaccountlinks', $notmyaccountlink);
            $mail = new Zend_Mail('utf-8');
            $bodyText = $html->render('registertocustomer.phtml');
            $mail->addTo($email);
            $mail->setSubject('Email verifiction Mail');
            $mail->setFrom($fromemail, $from);
            $mail->setBodyHtml($bodyText);*/
            //$mail->send();
	$tData = array( 'account_verification_link'=>$vcode,
                                                       'not_my_account_link'=>$notmyaccountlink,
                                                       'to_id'=>$newuserid,
                                                       'to_mail'=>$email,
                                                       'to_name'=>$fullusername);
                                        $this->objTrigger->triggerFire(1,$tData);
            $mapper->checknotmyaccount($getuservcode, $email, $getuserid);
            $this->_redirect(HTTP_SECURE . '/accountsetting/logout');
        } // end function index action
        else if ($pageaction == 'accountcheckprocess' && $valuseremail > 0) {
            $this->view->emailExist = 1;
            $this->view->useremailaddresses = $email;
            return false;
        }
    }

// end function index action

    public function notyournewaccountAction() {
        Zend_Layout::getMvcInstance()->setLayout('secure');
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH . '/modules/secure/layouts');
        $this->view->headScript()->appendFile('/jscript/common/o2ojslib.js', 'text/javascript');
        $this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
        $this->view->headTitle('Not my Account - Goo2o.com');
        $this->view->controller = 'notyournewaccount';
        $Input = $this->_request->getParams();
        $vcodes = $Input['passcode'];
        $this->view->vcodes = $vcodes;
        $uservcode = $_POST['vcode'];
        $pageaction = $_POST['formaction'];
        $this->userName = new Zend_Session_Namespace('USER');
        $this->view->userid=$this->userName->userId;
        $mapper = new Secure_Model_RegistrationMapper();
        $returnusrnameArray = $mapper->getuseremailname($Input['passcode']);
        $newuserid = $returnusrnameArray[0]['uid'];
        $this->view->usernameid = $newuserid;
        $newuseremail = $returnusrnameArray[0]['user_email'];
        $this->view->useremailaddress = $newuseremail;
        if ($pageaction == 'notmyaccount') {
            $this->_redirect(HTTP_SECURE . "/login/yournewemailremoved/passcode/" . $uservcode);
        }
    }

// end function index action

    public function yournewemailremovedAction() {
        Zend_Layout::getMvcInstance()->setLayout('secure');
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH . '/modules/secure/layouts');
        $this->view->headTitle('Email Removed - Goo2o.com');
        $this->view->controller = 'yournewemailremoved';
        $this->view->headScript()->appendFile('/jscript/common/o2ojslib.js', 'text/javascript');
        $this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
        $Input = $this->_request->getParams();
        $vcodes = $Input['passcode'];
        $this->view->vcodes = $vcodes;
        $mapper = new Secure_Model_RegistrationMapper();
        $returnusrnameArray = $mapper->getuseremailname($Input['passcode']);
        $newuseremail = $returnusrnameArray[0]['user_email'];
        $this->view->useremailaddress = $newuseremail;
        $mapper = new Secure_Model_LoginMapper(); // object for register mapper class
        $mapper->removeuseremails($vcodes);
    }

// end function index action

    public function customerverificationAction() {
        Zend_Layout::getMvcInstance()->setLayout('secure');
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH . '/modules/secure/layouts');
        $this->view->headTitle('Customer verification - Goo2o.com');
        $this->view->controller = 'customerverification';
        $this->view->headScript()->appendFile('/jscript/common/o2ojslib.js', 'text/javascript');
        $this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
        $this->view->headLink()->appendStylesheet('/css/admin/customers/customer_common.css');
        $this->userName = new Zend_Session_Namespace('USER');
        $this->userApi = new Zend_Session_Namespace('SESSION');
        $capi = $this->userApi->ApiKey;
        $Input = $this->_request->getParams();
        $sapi = base64_decode($Input['sapi']);
	if($this->userName->userId == '')
		{
                        //$_SESSION['spk']=$sapi;
                        $_SESSION['mypage']='/login/customerverification/sapi/'.$sapi;
			$this->_redirect(HTTP_SECURE.'/login');
			exit;
		}
        $mapper = new Secure_Model_LoginMapper();
        $returnusmallDetail = $mapper->getmalldetail($sapi);
        $checkexist = $mapper->getexistcustomer($sapi, $capi);
        $this->view->userfullname = $this->userName->userDetails[0]['user_full_name'];
        $this->view->useremail = $this->userName->userDetails[0]['user_email_address'];
        $this->view->mallurl = $returnusmallDetail['mallurl'];
        $this->view->malltitle = $returnusmallDetail['title'];
        $this->view->userimage = $this->userName->userDetails[0]['user_image'];
        if ($checkexist == 0) {
            $mapper->addnewcustomer($sapi, $capi);
        } else {
            $this->_redirect(HTTP_SERVER . '/myaccount');
        }
    }

// end function index action
}

// End class Secure_login Controller
?>
