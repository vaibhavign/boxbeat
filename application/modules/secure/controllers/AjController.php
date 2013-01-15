<?php
class Secure_AjController extends Zend_Controller_Action
{
	private $sessName;

    public function init()
    {
        Zend_Layout::getMvcInstance()->setLayout('nolayout');
	Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');
    }

    public function indexAction()
    {
		if($_GET['actionw']=='process'){
	
			$urlData =  file_get_contents("http://v5.com/secure/signin/json?username=".$_GET['username']."&password=".$_GET['password']."&clientsess=".$_GET['clientsess']);
		
                    echo $urlData;
			//print_r($urlData);
/*			$xmlData = new SimpleXMLElement($urlData);
			foreach($xmlData as $userData){
				$apiSessData = (string) $userData->apisessid;
				$usernameError = (string) $userData->username;
				$passwordError = (string) $userData->password;
			}
			$this->view->userErr=$usernameError;	
			$this->view->passErr=$passwordError;*/	
			
		}
	
		}
		
	public function getdataAction(){
		$abc[] = array('name'=>"test successfull");
		$qwe = json_encode($abc);
		echo $_GET['jsoncallback']. '('.$qwe.')';	
	}
	
	public function getJsondataAction(){
		echo "testtest";	
		
	}
	
   
}