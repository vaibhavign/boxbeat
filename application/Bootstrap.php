<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initAutoload ()
    { 

        		//code by saroj for store session in db

		//if($request->getModuleName()!='default')
		//{
		//echo "sdfsd".$request->getModuleName();exit;
		//$this->bootstrap('db'); 
		//$this->bootstrap('session');
		//end code by saroj
		
		Zend_Loader_Autoloader::getInstance()->registerNamespace('My_');
		Zend_Session::setOptions(array('cookie_domain' => '.eshopbox.com'));
		Zend_Session::start();
		
			
		//}
	
		
		$SESSION = new Zend_Session_Namespace('SESSION');
		$USER = new Zend_Session_Namespace('USER');
		$CART = new Zend_Session_Namespace('CART');
		$ori = new Zend_Session_Namespace('original_login');
		
						 $autoloader = Zend_Loader_Autoloader::getInstance();
		$autoloader->registerNamespace('Restriction_');
       $autoloader->suppressNotFoundWarnings(true);
	
		$autoloader = new Zend_Application_Module_Autoloader(
		array('namespace' => 'Default', 
		'basePath' => APPLICATION_PATH . '/modules/default'));
		
		
		$writer = new Zend_Log_Writer_Stream(APPLICATION_PATH . '/logs/admin-app.log');
	        $logger = new Zend_Log($writer);
        	Zend_Registry::set("log", $logger);
				
		
		

		$SESSION->sessionId = Zend_Session::getId();
		
		include_once ('loader.php');
		require_once ('Zend/Cache.php');
		require('constants.php');
		require('generic_functions.php');
		require('jsLibrary.php');
        require('DML.php');
require('class.domain.php');
		
		//print_r($_SESSION);
		//exit;
		// checkSignupStepsCompleted($_SESSION['USER']['userId']);
		$GENERAL_OBJ = new General();

		return $autoloader;
    }
	
}
