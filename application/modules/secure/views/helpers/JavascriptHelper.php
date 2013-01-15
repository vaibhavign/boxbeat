<?php 
class Zend_View_Helper_JavascriptHelper extends Zend_View_Helper_Abstract
{   
    function javascriptHelper() {
        $request = Zend_Controller_Front::getInstance()->getRequest();
		$fixedUrls = "/jscript/common/jquery-1.4.2.min.js";
		$fixedUrlNew = "/jscript/common/jquery.js";
		$fixedUrlNew2 = "/jscript/common/pulse.js";
		$this->view->headScript()->prependFile($fixedUrls);
		$this->view->headScript()->appendFile($fixedUrlNew);
        $file_uri = 'jscript/'.$request->getModuleName().'/'.$request->getActionName().'.js';
        if (file_exists($file_uri)) {
		$file_uri = '/jscript/'.$request->getModuleName().'/'.$request->getActionName().'.js';
          $this->view->headScript()->appendFile($file_uri);
        }  
		$this->view->headScript()->appendFile($fixedUrlNew2);
		return $this->view->headScript();
    }
	
	
	
}
