<?php  
class Zend_View_Helper_TempateHelper extends Zend_View_Helper_Abstract 
{ 
    function templateHelper() { 
        $request = Zend_Controller_Front::getInstance()->getRequest(); 
		$obj = new Admin_Model_StructureMapper($_SESSION['SESSION']['ApiKey']);
		$css_array = $obj->currentTemplate()->setTemplateCssArray()->__get('_template_css_array');
		foreach($css_array as $css)
			 $this->view->headLink()->appendStylesheet($css);
		return $this->view->headLink(); 
    } 
}