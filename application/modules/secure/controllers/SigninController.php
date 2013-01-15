<?php
// required class file for igbapi
require 'igbapi.php';
class Secure_SigninController extends Zend_Controller_Action
{
    public function init()
    {
        Zend_Layout::getMvcInstance()->setLayout('nolayout');
	Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');
    
	}

    public function indexAction()
    {	
    }
     public function xmlAction()
    {
	$abc = new igbapi($_GET);
    }
     public function jsonAction()
    {	

        $abc = new igbapi($_GET,'Get','json','signin');
		
    }
	
		 public function empjsonAction()
    {		
	        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
			$abc = new igbapi($_GET,'Get','json','empsignin');
		//$frontController->setParam(�noViewRendered�, true);
	}
}
?>