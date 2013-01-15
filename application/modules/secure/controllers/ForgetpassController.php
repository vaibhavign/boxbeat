<?php
class Secure_ForgetpassController extends Igb_Controller_Action
{
    public function init()
    {
	
    }

    public function indexAction()
    {	
    

	}
	
	
	 public function xmlAction()
    {
		
		$abc = new igbapi($_GET,'Get','xml','forgetpass');
		//$frontController->setParam(’noViewRendered’, true);
		}
	
	 public function jsonAction()
    {		
			$abc = new igbapi($_GET,'Get','json','signin');
		//$frontController->setParam(’noViewRendered’, true);
		}

	
}
?>