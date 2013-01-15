<?php

class ErrorController extends Zend_Controller_Action
{

    public function init()
    {
       
        Zend_Layout::getMvcInstance()->setLayout('secure');
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH . '/modules/secure/layouts');
	   $this->view->headLink()->appendStylesheet('/css/secure/admin.css');
	   	$this->userName = new Zend_Session_Namespace('USER');
		$this->storeName = $this->userName->userDetails[0]['username'];
		 $userName = new Zend_Session_Namespace('USER');// session user
                 if( $userName ->userId=='')
                 {
                   // $_SESSION['mypage']=HTTP_SERVER. '/myaccount';
                     //  $this->_redirect(HTTP_SECURE . '/login');
                     
                 }
        	if($userName->onceconfirmation==1)
        	{
            		$userName->onceconfirmation=0;
        	}
			$c = $this->_request->getParams();
			$this->view->controller=$c['controller'];
    }

    public function indexAction()
    {
		define('PAGE_TITLE',' My Account - '.$this->storeName.PAGE_EXTENSION);
        $this->view->headScript()->appendFile('/jscript/common/jquery.history.js');
        $this->view->headScript()->appendFile('/jscript/common/site.js');
        $this->view->headScript()->appendFile('/jscript/common/prettify.js');
		 $this->view->headTitle(' My Account - '.$this->storeName.PAGE_EXTENSION);
         $this->view->headMeta()->setName('keywords', 'My Account , Goo2o Technologies');
        $this->view->headMeta()->setName('description', 'My Account , Goo2o Technologies');
	//$this->_helper->layout->disableLayout();
	//echo "test";
	
	
    }
	public function enablejavascriptAction()
		{
			$this->view->headTitle('Enable javascript - Goo2o.com');
			$this->view->headLink()->appendStylesheet('/css/default/javascriptenable.css');
			
			//$this->_helper->layout->disableLayout();  

		}
	public function browsernotsupportedAction()
		{
	
		//	print_r($browser);
		print_r($browseroptions);
		$this->view->headLink()->appendStylesheet('/css/default/browser.css');
		$this->view->headTitle('Browser not supported - Goo2o.com');
			//$this->_helper->layout->disableLayout();  

		}	
  
}

