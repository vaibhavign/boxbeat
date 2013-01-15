<?php
class indexController extends Zend_Controller_Action {
	function init()
		{

				Zend_Layout::getMvcInstance()->setLayout('eshopbox'); // setting the layout file to secure
				Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH . '/modules/secure/layouts');
				$this->userName = new Zend_Session_Namespace('USER');
				
				if($_REQUEST['request']!='' && $_REQUEST['self']!=1)
				{
					$form='';

						if($userName->userId!='')
							{
							?>
								<noscript>
								<meta http-equiv="refresh" content="0;url=<?php echo $_REQUEST['request']?>?authId=<?php echo Zend_Session::getId()?>">
								</noscript>
								<?php
								$form='<form name="goo2oform" id="goo2oform" method="post" action="'.$_REQUEST['request'].'">';
								$form.="<input type='hidden' name='authid' value='".Zend_Session::getId()."'>";
								$form.="<input type='hidden' name='sessionvalue'  value='". json_encode($_SESSION)."'>";
								$form.='</form>';
								echo $form;
								echo '<script>document.forms["goo2oform"].submit();</script>';
								exit;
							}
						}
						else if($_REQUEST['request']!='' && $_REQUEST['self']==1)
								{
								$form='<form name="goo2oform" id="goo2oform" method="post" action="'.$_REQUEST['request'].'">';
								$form.="<input type='hidden' name='authid' value='".Zend_Session::getId()."'>";
								$form.="<input type='hidden' name='sessionvalue'  value='". json_encode($_SESSION)."'>";
								$form.='</form>';
								echo $form;
								echo '<script>document.forms["goo2oform"].submit();</script>';
								exit;

								}
				if($this->userName->userId!='' && $_GET['tab']=='')
				{
					 $this->_redirect('/admin/overview/page');
				}
				if($this->userName->userId!='' && $_GET['tab']=='support')
				{
						$account_key = 'eshopboxhelp';
$api_key     = '83729440b226012f31ee12313e008572';

$salted = $api_key . $account_key;
$hash = hash('sha1',$salted,true);
$saltedHash = substr($hash,0,16);
$iv = "OpenSSL for Ruby";

// Build json data
$user_data = array(
	'uid' => $this->userName->userId,
	'customer_email' => $this->userName->userDetails[0]['user_email_address'],
	'customer_name' => $this->userName->userDetails[0]['user_full_name'],
	'expires' => date("c", strtotime("+10 minutes"))
);
$data = json_encode($user_data);

// XOR first block of data with IV
for ($i = 0; $i < 16; $i++) {
	$data[$i] = $data[$i] ^ $iv[$i];
}

// pad using standard PKCS#5 padding with block size of 16 bytes
$pad = 16 - (strlen($data) % 16);
$data = $data . str_repeat(chr($pad), $pad);

// encrypt data using AES128-cbc
$cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128,'','cbc','');
mcrypt_generic_init($cipher, $saltedHash, $iv);
$multipass = mcrypt_generic($cipher,$data);
mcrypt_generic_deinit($cipher);

// Base64 encode the encrypted data
$multipass = base64_encode($multipass);

// Convert encoded data to the URL safe variant
$multipass = preg_replace('/\=$/', '', $multipass);
$multipass = preg_replace('/\n/', '', $multipass);
$multipass = preg_replace('/\+/', '-', $multipass);
$multipass = preg_replace('/\//', '_', $multipass);

// Build an HMAC-SHA1 signature using the multipass string and your API key
$signature = hash_hmac("sha1", $multipass, $api_key, true);
// Base64 encode the signature
$signature = base64_encode($signature);

// Finally, URL encode the multipass and signature
$multipass = urlencode($multipass);
$signature = urlencode($signature);
//echo "http://eshopboxhelp.desk.com/customer/authentication/multipass/callback?multipass=".$multipass."&signature=".$signature; exit;

header("Location:http://eshopboxhelp.desk.com/customer/authentication/multipass/callback?multipass=".$multipass."&signature=".$signature);
				}
				$this->view->headScript()->appendFile('/jscript/common/jquery.min.js');
				
				$this->mapper=new Default_Model_IndexMapper();
				$this->objTrigger=new notification();
		}
	function indexAction()
		{

			
			 $Inputs = $this->_request->getParams();
				$username=$_POST['username'];
				$password=$_POST['password1'];

				 $this->view->redirecturl=$_REQUEST['request'];
				$this->view->tab=$_GET['tab'];
				if($password=='')
					{
						$this->view->headTitle('Sign in: Sign in to your eshopbox account - eshopbox.com');
						$this->view->headLink()->appendStylesheet('/css/secure/elogin/elogin.css');	
						$this->view->headScript()->appendFile('/jscript/secure/elogin/elogin.js');
					}
				else
					{
						
						$this->_helper->layout()->disableLayout();
						$username=$_POST['username'];
						$password=$_POST['password1'];
						if(filter_var($username, FILTER_VALIDATE_EMAIL)) {
							$email=1;
						    }
						    else {
							$email=0;
						    }


						$verifiedUser=$this->mapper->checkexistingUser($username,$password,$email);
					


				if($verifiedUser)
				{


						  $usersid=$_SESSION['Zend_Auth']['storage']->id;
						 $apiKey=$_SESSION['Zend_Auth']['storage']->apikey;
						  $email=$_SESSION['Zend_Auth']['storage']->name;
						$sessName = new Zend_Session_Namespace('SESSION'); // namespace session
						$userName = new Zend_Session_Namespace('USER'); // session user
						$original = new Zend_Session_Namespace('original_login'); //
						$original->apikey = $apiKey;
						$original->sessid = session_id();
						$sessName->thissessid = $apiSessData; // setting the session value
						$sessName->ApiKey = $apiKey;
						$userName->userId = $usersid; // settig the userid in the session user
                                                $userdet=array();    
						$userdet = $this->mapper->getLoggedUserDetails('id', $usersid);   
                                             


						$getMallsById = $this->mapper->getApiDetails($userdet[0]['user_email_address']);
						$userdet['stores'] = $getMallsById;
						$original->user = $userdet;
						$original->userId = $usersid;
						$userName->userDetails = $userdet;
                                                 $this->mapper->changeUserprofile('', $usersid);
						$datalogin=array();
						
						$datalogin['sessionid']=Zend_Session::getId();	
						$datalogin['sessiondata']=json_encode($_SESSION);
						$datalogin['r']=1;
						if($_POST['tab']=='support')
						{
							$account_key = 'eshopboxhelp';
$api_key     = '83729440b226012f31ee12313e008572';

$salted = $api_key . $account_key;
$hash = hash('sha1',$salted,true);
$saltedHash = substr($hash,0,16);
$iv = "OpenSSL for Ruby";

// Build json data
$user_data = array(
	'uid' => $usersid,
	'customer_email' => $userdet[0]['user_email_address'],
	'customer_name' => $userdet[0]['user_full_name'],
	'expires' => date("c", strtotime("+10 minutes"))
);
$data = json_encode($user_data);

// XOR first block of data with IV
for ($i = 0; $i < 16; $i++) {
	$data[$i] = $data[$i] ^ $iv[$i];
}

// pad using standard PKCS#5 padding with block size of 16 bytes
$pad = 16 - (strlen($data) % 16);
$data = $data . str_repeat(chr($pad), $pad);

// encrypt data using AES128-cbc
$cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128,'','cbc','');
mcrypt_generic_init($cipher, $saltedHash, $iv);
$multipass = mcrypt_generic($cipher,$data);
mcrypt_generic_deinit($cipher);

// Base64 encode the encrypted data
$multipass = base64_encode($multipass);

// Convert encoded data to the URL safe variant
$multipass = preg_replace('/\=$/', '', $multipass);
$multipass = preg_replace('/\n/', '', $multipass);
$multipass = preg_replace('/\+/', '-', $multipass);
$multipass = preg_replace('/\//', '_', $multipass);

// Build an HMAC-SHA1 signature using the multipass string and your API key
$signature = hash_hmac("sha1", $multipass, $api_key, true);
// Base64 encode the signature
$signature = base64_encode($signature);

// Finally, URL encode the multipass and signature
$multipass = urlencode($multipass);
$signature = urlencode($signature);
//echo "http://eshopboxhelp.desk.com/customer/authentication/multipass/callback?multipass=".$multipass."&signature=".$signature; 

$datalogin['url']="http://eshopboxhelp.desk.com/customer/authentication/multipass/callback?multipass=".$multipass."&signature=".$signature;

						}
                                               
						echo json_encode($datalogin);exit;
               
					
				}
				else
				{
						$datalogin=array();
						
						$datalogin['r']=0;
						echo json_encode($datalogin);exit;
						
				}
		

					}
		}
		
	
	
	}
?>	
