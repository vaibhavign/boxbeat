<?php  
class Zend_View_Helper_CssHelper extends Zend_View_Helper_Abstract 
{ 
    function cssHelper() {
        
        $request = Zend_Controller_Front::getInstance()->getRequest();
		
       // echo $request->getModuleName();
        //echo $request->getControllerName();
		$file_uri = 'css/'.$request->getModuleName()."/".$request->getControllerName(). '.css';
				
                $file_uri2 = 'css/'.$request->getModuleName()."/".$request->getActionName(). '.css';
				
				$file_uri3 = 'css/'.$request->getModuleName().'/'.$request->getModuleName().'.css';
				$file_uri4 = 'css/'.$request->getModuleName()."/".$request->getControllerName(). '_common.css';
				$file_uri5 = 'css/'.$request->getModuleName()."/".$request->getControllerName()."/".$request->getActionName().'.css';
				$file_uri6 = 'css/'.$request->getModuleName()."/".$request->getControllerName()."/".$request->getActionName().'.css';
				
        if($request->getActionName()=='index'){
		if (file_exists($file_uri)) { 
            $this->view->headLink()->appendStylesheet('/' . $file_uri); 
        }
		}
        if (file_exists($file_uri2)) {
            $this->view->headLink()->appendStylesheet('/' . $file_uri2);
        }
		
		if (file_exists($file_uri3)) {
            $this->view->headLink()->appendStylesheet('/' . $file_uri3);
        }
         if (file_exists($file_uri4)) {
            $this->view->headLink()->appendStylesheet('/' . $file_uri4);
        }
		if(file_exists($file_uri5)){
			$this->view->headLink()->appendStylesheet('/' . $file_uri5);
		}
		
		if(file_exists($file_uri6)){
			$this->view->headLink()->appendStylesheet('/' . $file_uri6);
		}
        return $this->view->headLink(); 
    } 
}