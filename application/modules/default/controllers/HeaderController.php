<?php
class HeaderController extends Zend_Controller_Action
{
	function indexAction()
		{
				Zend_Layout::getMvcInstance()->setLayout('eheader');
				Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');
				$this->view->headLink()->appendStylesheet('http://login.eshopbox.com/css/default/eheader.css');
				
			
		}
	
}

?>
