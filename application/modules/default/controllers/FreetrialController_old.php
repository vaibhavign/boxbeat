<?php
class FreetrialController extends Zend_Controller_Action {
	function init()
		{
				Zend_Layout::getMvcInstance()->setLayout('eshopbox'); // setting the layout file to secure
				Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH . '/modules/secure/layouts');
				
				
				$this->view->headScript()->appendFile('/jscript/common/jquery.min.js');
				
				$this->mapper=new Default_Model_IndexMapper();
				$this->objTrigger=new notification();
		}
	function indexAction()
		{
				$this->view->headLink()->appendStylesheet('/css/secure/elogin/freetrail.css');
				$this->view->headTitle('Sign up for a free 15-day trial - eshopbox.com');
		}
	
	}
?>	
