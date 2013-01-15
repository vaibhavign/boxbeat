<?php 
class Zend_View_Helper_JavascriptHelper extends Zend_View_Helper_Abstract
{   
    function javascriptHelper() {
        $request = Zend_Controller_Front::getInstance()->getRequest();
		$fixedUrls = "/jscript/common/jquery-1.4.2.min.js";
		$this->view->headScript()->prependFile($fixedUrls);
        $file_uri_action = 'jscript/'.$request->getModuleName().'/'.$request->getActionName().'.js';
		$file_uri_controller = 'jscript/'.$request->getModuleName().'/'.$request->getControllerName().'.js';
        if (file_exists($file_uri_action)) {
		
           $this->view->headScript()->appendFile('/'.$file_uri_action);
        }
		else if (file_exists($file_uri_controller)) {
           $this->view->headScript()->appendFile('/'.$file_uri_controller);
        }  
		return $this->view->headScript();
    }
	
	
	
}
