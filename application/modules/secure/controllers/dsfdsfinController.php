<?php
class Secure_LoginController extends Zend_Controller_Action
{
	private $sessName;

    public function init()
    {
		//echo 'here';
	//print_r($_SESSION);
		// $bootstrap = $this->getInvokeArg('bootstrap');
		//	$this->sessName = $bootstrap->getResource('USER');
		//	echo "<pre>";
		//	print_r( $bootstrap);
		/* Initialize action controller here */
    }

    public function indexAction()
    {
		
<<<<<<< .mine
			//$echo1 =  file_get_contents("http://v5.com/secure/signin/xml?username=".$_GET['username']."&password=".$_GET['password']);
=======
		//print_r($_SESSION);
		if($_GET['action']=='process'){
>>>>>>> .r60
			
			$echo1 =  file_get_contents("http://v5.com/secure/signin/xml?username=".$_GET['username']."&password=".$_GET['password']);
			$xmlData = new SimpleXMLElement($echo1);
			foreach($xmlData as $userData){

				$apiSessData = (string) $userData->apisessid;
				$usernameError = (string) $userData->username;
				$passwordError = (string) $userData->password;
			}
			$this->view->usererr=$usernameError;	
			$this->view->passerr=$passwordError;	
			if($apiSessData!=''){
			$sessName = new Zend_Session_Namespace('SESSION');
			$sessName->thissessid = $apiSessData ;
			$this->_redirect('/home');
			}
		}
		}
   
}