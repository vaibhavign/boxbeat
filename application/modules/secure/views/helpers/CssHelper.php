<?php  
class Zend_View_Helper_CssHelper extends Zend_View_Helper_Abstract 
{ 
    function cssHelper() {
        
        $request = Zend_Controller_Front::getInstance()->getRequest();
       // echo $request->getModuleName();
        //echo $request->getControllerName();
		$file_uri = 'css/'.$request->getModuleName()."/".$request->getControllerName(). '.css';
                $file_uri2 = 'css/'.$request->getModuleName()."/".$request->getActionName(). '.css';
				$file_uri3 = "css/secure/".$request->getModuleName().".css";
        if (file_exists($file_uri)) { 
            $this->view->headLink()->appendStylesheet('/' . $file_uri); 
        }

        if (file_exists($file_uri2)) {
            $this->view->headLink()->appendStylesheet('/' . $file_uri2);
        }
         
		  $this->view->headLink()->appendStylesheet('/' . $file_uri3); 
        return $this->view->headLink(); 
    } 
	
	
	
	
}