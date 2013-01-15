<?php
/**
 * @author : Nagendra yadav
 * Used for User registration actions
 * @var private $publickey : public captcha key
 * @var private $privatekey : private captcha key
 * func init
 * setting the values for $publickey and $privatekey
 * @obj $mapper : object of registermapper class
 * @var $returnedLocationArray : Location array
 * @var $cityList : City list array
 * @obj $selectBox : object of Zend_Form_Element_Select class
 */

class Secure_RevenueController extends Zend_Controller_Action
{
            private $publickey;
            private $privatekey;
	

    public function init()
    {
		$this->userName = new Zend_Session_Namespace('USER');
		$this->sessionName = new Zend_Session_Namespace('SESSION');
	
		$this->storeName = $this->userName->userDetails[0]['title'];
   }
    // main action for register process
    public function indexAction()
    {
			Zend_Layout::getMvcInstance()->setLayout('secure');
			Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');
                        $this->view->headTitle('Registration: Create your Goo2o Account - Goo2o.com');
			$this->view->headScript()->appendFile('/jscript/common/o2ojslib.js','text/javascript');
			$this->view->headScript()->appendFile('/jscript/secure/registration.js','text/javascript');
			$this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
			$this->view->publickey = $this->publickey;
			$this->view->privatekey = $this->privatekey;
             
			
        
		
    } // end function index action
	
   public function mytransactionsAction(){

	   define('PAGE_TITLE',('Transaction Listing - '.$this->userName->userDetails[0]['username'].PAGE_EXTENSION));
        Zend_Layout::getMvcInstance()->setLayout('secureadmin');
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH . '/modules/secure/layouts');
			$this->view->headScript()->appendFile('/jscript/common/o2ojslib.js','text/javascript');
			$this->view->headScript()->appendFile('/jscript/secure/registration.js','text/javascript');
			$this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
			$this->view->headLink()->appendStylesheet('/css/admin/admin.css');
			
			$mapper  = new Api_Model_RevenueMapper();
			$transactionListing = $mapper->transactionListing($this->sessionName->ApiKey);
			$this->view->transactionListingArray = $transactionListing; 
			
	}
   
    
}
?>
