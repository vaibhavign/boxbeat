<?php
class Secure_RegisterController extends Zend_Controller_Action
{
    public function init()
    {

    }

    public function indexAction()
    {	

	}
	
	
	 public function xmlAction()
    {

		$xmlGetData = new igbapi($_GET,'Get','xml','register');
		//$frontController->setParam(’noViewRendered’, true);
		}
	
	 public function jsonAction()
    {		
			$jsonGetData = new igbapi($_GET,'Get','json','register');
		//$frontController->setParam(’noViewRendered’, true);
		}

	
}
?>