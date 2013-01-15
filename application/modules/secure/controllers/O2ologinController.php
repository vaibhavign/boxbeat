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
class Secure_O2ologinController extends Zend_Controller_Action
{
		private $publickey;
		private $privatekey;
		private $userid;
                private $objTrigger;
    public function init()
    {  
	if(isset($_GET['test']))
	{
	echo '<script type="text/javascript">
        window.opener.location.reload(true);
 </script>';
exit;
}
	 //echo '<pre>';
        //print_r($_SESSION);exit;
	$Input = $this->_request->getParams();
	if($Input['type']!=5){
         $vcodes=$_SESSION['USER']['vcode'];
         if(isset($vcodes) && isset($_SESSION['USER']['userId']))
         {
         $mapper  = new Secure_Model_LoginMapper();
        
         $checkuserverification=$mapper->checkusrverification($vcodes);
         $checkcanceluser=$mapper->checkcanceleduser($vcodes);
         if($checkcanceluser>0)
         {
           // $this->_redirect(HTTP_SECURE.'/login/notmyaccount/passcode/'.$vcodes);
         }
         if(isset($_SESSION['USER']['userId']) && $checkuserverification==0){

            //$this->_redirect(HTTP_SECURE.'/login/accountrestricted/passcode/'.$vcodes);
        }
         if(isset($_SESSION['USER']['userId']) && $checkuserverification==1){
           echo '<script type="text/javascript" language="javascript">self.close();opener.location.href="'.HTTP_SERVER.'/admin/overview"</script>';
	exit;
        }
		
     }
	 if(isset($_SESSION['USER']['userId']) && $Input['action']=='index'){
			
           echo '<script type="text/javascript" language="javascript">self.close();opener.location.href="'.$_GET['id'].'"</script>';
		exit;
	}
        }
       $this->publickey = "6LfgNAoAAAAAAEb9m-vKAwvOtZulVJkoogKwC40v "; // recaptcha public key
       $this->privatekey = "6LfgNAoAAAAAAOrGvWEemmjTn62kqvYe-exXJbsC "; // recaptcha private key
       Zend_Layout::getMvcInstance()->setLayout('secure'); // setting the layout file to secure
       Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');
       $this->view->headScript()->appendFile('/jscript/common/jquery-1.4.2.min.js');
       $this->view->headScript()->appendFile('/jscript/secure/login.js','text/javascript');
       $this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
       $this->view->headLink()->appendStylesheet('/css/secure/loginpopup.css');
       $this->objTrigger = new Notification();
    }

    public function indexAction()
    {
      
	  		if($_GET['type']!=5)
			{
            $this->view->headTitle('Sign in: Sign in to your Goo2o account - Goo2o.com');
			}else
			{
            $this->view->headTitle('Check out: Demo store');
			}
            $this->view->headMeta()->setName('keywords', 'Login , GoO2o Technologies');
            $this->view->headMeta()->setName('description', 'Login , GoO2o Technologies');
            $this->view->captchview=0;
            $this->view->controller='o2ologin';
            $this->view->publickey = "6LfgNAoAAAAAAEb9m-vKAwvOtZulVJkoogKwC40v "; // recaptcha public key
            $this->view->privatekey = "6LfgNAoAAAAAAOrGvWEemmjTn62kqvYe-exXJbsC "; // recaptcha private key
            $tabValue = $_REQUEST['tab'];
            $this->view->tabs= $tabValue; // tab valueset for redirection
            $this->view->userempty=0;
            $this->view->lessthansix=0;
            $this->view->passwordempty=0;
            
        if($_POST['action']=='process'){ // when posted
                    if($_POST['customer']==1) // when new customer registers
                    {
                        $mapper  = new Secure_Model_LoginMapper();
                        $numusername=$mapper->checkUsernameExists($_POST['username']);
                        if($pos = strpos($_POST['username'],'@'))
                        {
                                $url='user_email_address='.$_POST['username'];
                                $email = $_POST['username'];
                                $numemail=$mapper->checkEmailIdExists($email);
                                if($numemail>0)
                                  {
                                    echo '<script type="text/javascript" language="javascript">self.close();opener.location.href="'.HTTP_SECURE.'/registration/emailalreadyuse?'.$url.'"</script>';
					exit;
                                   
                                  }
                        }
                        else if($numusername>0)
                          {
                                 $url='username='.$_POST['username'];
                                 echo '<script type="text/javascript" language="javascript">self.close();opener.location.href="'.HTTP_SECURE.'/registration/emailalreadyuse?'.$url.'"</script>';
					exit;
                               
                          }
                        else
                          {
                               $url='username='.$_POST['username'];
                          }
                        $url='username='.$_POST['username'];
                               echo '<script type="text/javascript" language="javascript">self.close();opener.location.href="'.HTTP_SECURE.'/registration?'.$url.'"</script>';
                             exit;
                    }
                        $captchaErr="";
                        if(isset($_POST['remember'])){
                                $mapper  = new Secure_Model_LoginMapper();
                                $mapper->updateSessionlifetime();
                                //setcookie("c_n", base64_encode($_POST['username']), time()+86400*7, "/");
                                //setcookie("c_p", base64_encode($_POST['password']), time()+86400*7, "/");
                                $this->view->captchapost=1;
   			}
			else
			{
				//setcookie("c_n", base64_encode($_POST['username']), time()-86400*7, "/");
                                //setcookie("c_p",base64_encode($_POST['password']), time()-86400*7, "/");
				$this->view->captchapost=0;
			}


             // fetching the tab value
			if($_POST['captcha']==1 && $_POST['username']!='')
			{
                                $resp = recaptcha_check_answer ($this->view->privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);
                                if (!$resp->is_valid) {
                                // What happens when the CAPTCHA was entered incorrectly
                                $this->view->captchaErr='The characters you entered didn\'t match the word verification. Please try again';
                                $captchaErr='The characters you entered didn\'t match the word verification. Please try again';
                                } else {
                                // Your code here to handle a successful verification
                                }
			}
			$this->view->usernamepost=$_POST['username'];
			$this->view->passwordpost=$_POST['password'];
				function curl($url){
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
				curl_setopt($ch,CURLOPT_USERPWD, 'skywalker:dexter');
				return curl_exec($ch);
				curl_close($ch);
       		 }
            $class=new General();
            $feed = HTTPS_SECURE."/signin/json?username=".$_POST['username']."&password=".$_POST['password'];
            $urlData = curl($feed);
            $userData = Zend_Json::decode(trim($urlData));// getting value as json, decoding it
            $apiKey = (string) $userData['apikey'];
            $apiSessData = (string) $userData['apisessid'];//session
            $usernameError = (string)$userData['username'];// if any error
            $allError = (string)$userData['all'];// if any error
            $passwordError = (string)$userData['password'];// if any error
	        $captchaErr = (string)$userData['captchaappear'];// if any error
            $usersid = (string)$userData['userid'];// userid of the profiled user
            $this->view->userErr=$usernameError;
            $this->view->passErr=$passwordError;
            $mapper  = new Secure_Model_LoginMapper();
            $valusername=$mapper->checkUsernameExists(trim($_POST['username']));
            if($pos = strpos($_POST['username'],'@'))
              {
                    $url='user_email_address='.$_POST['username'];
                    $email = $_POST['username'];
                    $valusername=$mapper->checkEmailIdExists($email);
              }
            
            if(trim($_POST['username'])=='' && trim($_POST['password'])=='')
              {
                    $this->view->userempty=1;
                    $this->view->passwordempty=1;
              }
            else if($valusername>0 && trim($_POST['password'])=='')
              {
                    $this->view->passwordempty=1;
              }
            else if(trim($_POST['username'])=='' && trim($_POST['password'])!='')
              {
                    $this->view->userempty=1;
              }

           if($valusername>0 &&  strlen(trim($_POST['password'])) > 0 && strlen(trim($_POST['password']))< 6 && trim($_POST['customer'] != 1))
            {
                $this->view->lessthansix=1;
            }
            else if($valusername>0 && trim($_POST['password'])!='')
              {
                    $this->view->allError=$allError;
              }
            if($userData['captchaappear']==1 || $_POST['captcha']==1)
                    $this->view->captchview=1;
            if($_POST['username']=='')
             {
                    $this->view->captchview=0;
             }
           if($usernameError=="" && $passwordError=="" && ($captchaErr=="") && $allError==""){
              $mapper  = new Secure_Model_LoginMapper();
              $vcodes=$mapper->getvcodesusername($_POST['username']);
              if($pos = strpos($_POST['username'],'@'))
                {
                    $email = $_POST['username'];
                    $vcodes=$mapper->getvcodesemail($email);
                }
             $usrvcode = $vcodes[0]['vcode'];
             $user_join_date = $vcodes[0]['user_join_date'];
             $checkcanceluser=$mapper->checkcanceleduser($usrvcode);
             if($checkcanceluser>0)
             {
                 echo '<script type="text/javascript" language="javascript">self.close();window.opener.location="'.HTTP_SECURE.'/login/notmyaccount/passcode/'.$usrvcode.'"</script>';
                             exit;
               
             }
             $mapper = new Secure_Model_LoginMapper();
             $checkuserverifications=$mapper->checkusrverificationinprocess($usrvcode,$user_join_date);
            if($checkuserverifications==0){
                echo '<script type="text/javascript" language="javascript">self.close();window.opener.location="'.HTTP_SECURE.'/login/accountrestricted/passcode/'.$usrvcode.'";</script>';
                             exit;
            
            }
                $sessName = new Zend_Session_Namespace('SESSION');// namespace session
                $userName = new Zend_Session_Namespace('USER');// session user
				$original = new Zend_Session_Namespace('original_login');//
				$original->apikey=$apiKey ;
				
                $sessName->thissessid = $apiSessData ;// setting the session value
				$sessName->ApiKey = $apiKey ;
                $userName->userId = $usersid ;// settig the userid in the session user
                $userdet = $class->getLoggedUserDetails('id',$usersid);
				$getMallsById=$class->getApiDetails($userdet[0]['user_email_address']);
				$userdet['stores']=$getMallsById;
				$original->user=$userdet;
				$original->userId=$usersid ;
				$userName->userDetails = $userdet ;
				$update_session=$class->updateSessionname($userdet, $usersid);

				$mapper=new Admin_Model_IndexMapper();
		        	$mapper->changeUserprofile('',$usersid);

                setcookie("asess", $apiKey, time()+3600, "/", ".goo2ostore.com");
                //setcookie("asess", $apiKey);
		$restoerecontent=new Secure_Model_Cart();
               $restoerecontent->restoreContent();		
$mappers=new Secure_Model_LoginMapper();
                 $followed = $mappers->checkUserfollowed($apiKey,$_GET['spk']);
                 if($followed)
                 {
                     
                     //echo '<script type="text/javascript" language="javascript"> window.location="'.HTTP_SECURE.'/o2ologin/follow?shop='.$_GET['store'].'&skey='.$_GET['spk'].'"</script>';
			//echo 'goo2o';exit;
  echo '<script type="text/javascript" language="javascript">opener.location.href="'.$_GET['id'].'"</script>';
			$this->_redirect(HTTP_SECURE.'/o2ologin/follow?shop='.$_GET['store'].'&skey='.$_GET['spk']);		
                     exit;  
                     
                 }
                 else
                 {
                 
                     echo '<script type="text/javascript" language="javascript">self.close();opener.location.href="'.$_GET['id'].'"</script>'; 
					exit; 
                 }
                 
                 
                switch($tabValue){ // add more cases for different redirection , snapshot implementation
                    case 1 :
                                
                                echo '<script type="text/javascript" language="javascript">self.close();opener.location.href="'.$_GET['id'].'"</script>';
					exit;
                               
                                break;
                    default:
                                echo '<script type="text/javascript" language="javascript">self.close();opener.location.href="'.$_GET['id'].'"</script>';
					exit;
                } // end switch
       }// end if

     }// end if
   }// end function indexAction
     public function followAction()
    {
       //echo 'dfgdfg';exit; 
           
        $this->view->controller='o2ologin';
       $this->view->detail= $userName = new Zend_Session_Namespace('USER');
         
        
       
    } // end function index action
     public function denyAction()
    {
          $db = Zend_Db_Table::getDefaultAdapter();
        $Input = $this->_request->getParams();

        if($Input['skey']==''){
             echo '<script type="text/javascript" language="javascript">self.close();"</script>';
					exit;
        }
        $sessionItem = new Zend_Session_Namespace('SESSION');
       // echo "<pre>";
       // print_r($_SESSION);
       // echo "update store_follow_customer set folowing='1',follow_time=".time()." where capikey='".$sessionItem->ApiKey."' and sapikey='".$Input['skey']."' and deleted_flag='0'";exit;
//echo "update store_follow_customer set folowing='1',follow_time=".time()." where capikey='".$sessionItem->ApiKey."' and sapikey='".$Input['skey']."' and deleted_flag='0'";
//print_r( $Input);
//exit;

        $db->query("update store_follow_customer set folowing='1',follow_time=".time()." where capikey='".$sessionItem->ApiKey."' and sapikey='".$Input['skey']."' and deleted_flag='0'");
            
            echo '<script type="text/javascript" language="javascript">self.close()</script>';
		exit; 
        $this->view->controller='o2ologin';
       $this->view->detail= $userName = new Zend_Session_Namespace('USER');
         
        
       
    } // end function index action
  
 } // End class Secure_login Controller
?>
