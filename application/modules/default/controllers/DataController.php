<?php
include_once 'cssparser.php';
include_once('simple_html_dom.php');
include_once("Utility.php");
include_once ('smarty-config.php');
class DataController extends Zend_Controller_Action
{	
	protected $_user_api_key;
    public function init()
    {
		
		
    }

    public function homeAction()
    {
		
		$this->_user_api_key =$_POST['apikey'];
		if($this->_user_api_key == "")
		{
			echo "Please Contact to the Administrator for mapping this domain.";
			exit;
		}
		$user_api_key = $this->_user_api_key;
		//Get and Assigned Current Active Template Details
		$template_object = new Admin_Model_StructureMapper($user_api_key);
		
		$template_object->currentTemplate()->setTemplatePath()->setTemplateCssArray()->setTemplateJsArray();
		$path = $template_object->__get('_template_path');
		$xml_data_populator = new Admin_Model_XmlDataPopulator($path ,$this->_user_api_key);
		$mall_url = $xml_data_populator->__get('_mall_url');

		 $writer = new Zend_Log_Writer_Stream(APPLICATION_PATH . '/logs/'.basename($mall_url).'.log');
         $logger = new Zend_Log($writer);
         Zend_Registry::set("log", $logger);
		 $mall_title = $xml_data_populator->__get('_mall_title');

		$template_object->setStoreIndexHtml();//generate html file for home page
		$html = $template_object->__get('_html');
		
	
		if($html != '')
			file_put_contents($path.'/index.html',$html);
		$template_object->setFeedbackScript();
		$feedback_code = $template_object->__get('_feedback_script');
		
		$css_files = $template_object->__get('_template_css_array');
		//assign css list 	
		$html = new Zend_View();
        $generalObj = new General();
		
		
		$html->setScriptPath(APPLICATION_PATH . '/modules/admin/views/scripts/design/');
		
		foreach($css_files as $css_file)
			$html->headLink()->appendStylesheet(DESIGN_CDN_SERVER.$css_file);
			$html->headLink()->appendStylesheet(CDN_SERVER.'/css/admin/design/bx-slider.css');
			
		$js_files = $template_object->__get('_template_js_array');
		$script = "";
		
		///generate java script
        foreach($js_files as $js_file){
    		//$this->view->headScript()->appendFile($js_file);
    		if($js_file != $template_object->__get('_template_public_path').'/../js/jquery.bxSlider.min.js'){
			Zend_Registry::get("log")->debug("DesignController.file name = ".$js_file);
			if(strstr($js_file,'custom-block-'))
				//if(substr($js_file,0,7) == 'custom-')
    				$script = $script.file_get_contents(getcwd().$js_file)."\n";
    		}
        }
		$html->page_title = $generalObj->seoInformation('','home','title',$this->_user_api_key,$mall_url,$mall_title);
		$html->page_description = $generalObj->seoInformation('','home','description',$this->_user_api_key,$mall_url,$mall_title);
		$html->page_keyword = $generalObj->seoInformation('','home','keyword',$this->_user_api_key,$mall_url,$mall_title);
		$html->headScript()->appendFile(CDN_SERVER.'/jscript/common/jquery-1.5.1.js');
		$html->headScript()->appendFile(CDN_SERVER.'/jscript/admin/jquery.bxSlider.min.js');
		$html->headScript()->appendFile('/view.js');
		$myscript = 'var Apikey=[];
Apikey.push(["'.$user_api_key.'"]);						
(function(){
var o2oScripts=document.createElement("script");
o2oScripts.type="text/javascript";
o2oScripts.src="'.DESIGN_CDN_SERVER.'/bar/jscript/o2ojslib.js";
var o2oTags=document.getElementsByTagName("script")[0];
o2oTags.parentNode.insertBefore(o2oScripts,o2oTags);
})
();';
		$script = $myscript . "\n".'jQuery(document).ready(function() {'.$script.'});';
		$html->headScript()->appendScript($script);
		$html->feedback_code = stripslashes($feedback_code);
		$html->html = file_get_contents($path."/index.html");
		$contents = $html->render('view.phtml');
		echo $contents; 
		exit;
	
	}

	public function productdetailsAction()
	{
	
		$user_api_key = $this->_user_api_key;// '8151c25153f1c01fa7bc06a3caddd875';//$_POST['api_key'];
		//$user_api_key = '05dae78a743a8455ba0f5a1039542fba';//$_POST['api_key'];
		$general_obj = new General();
		$_SESSION['recent_products'] = array(); //-- defining session array--
		$product_id = $_POST['product_id'];
		$product_url = $_POST['product_url'];
		$url = $_SERVER['HTTP_HOST'].$product_id.'/'.$product_url;
		$obj = new Default_Model_Store($user_api_key);
		$obj->loadProductFromId($product_id,$product_url);
		$product_details = $obj->__get('_product_detail');
		if($product_details)
		{
			$_SESSION['recent_products'] = $product_id; //-- assign product id into recent product session array
			$html = new Zend_View();
			$html->setScriptPath(APPLICATION_PATH . '/modules/admin/views/scripts/design/');
			$html->products = $product_details;
			$image_arr = $general_obj->getImageFromDir($product_id,'product','medium','1',$disputeid='');
			$html->generalObj = new General();
			$html->products['image'] = $image_arr[0];
			$html->products['api_key'] = $user_api_key;
			$html->products['url'] = $url;
			$html->product_details = $html->render('products.phtml');
			$html->product_right_body_blocks = $html->render('products-right-body-block.phtml');
			
			
			$template_object = new Admin_Model_StructureMapper($user_api_key);
		
			$template_object->currentTemplate()->setTemplatePath()->setTemplateCssArray()->setTemplateJsArray();
			$html->header_html = file_get_contents($template_object->__get('_template_path')."/header.html");
			$html->footer_html = file_get_contents($template_object->__get('_template_path')."/footer.html");
			
			$css_files = $template_object->__get('_template_css_array');
			//assign css list 	
						
			foreach($css_files as $css_file)
				$html->headLink()->appendStylesheet(HTTP_SERVER.$css_file);
			$html->headLink()->appendStylesheet(HTTP_SERVER.'/css/store/product.css');
			$js_files = $template_object->__get('_template_js_array');
			$script = "";
			//generate java script
			foreach($js_files as $js_file){
				//$this->headScript()->appendFile($js_file);
				if($js_file != $template_object->__get('_template_public_path').'/../js/jquery.bxSlider.min.js'){
					$script = $script.file_get_contents(getcwd().$js_file)."\n";
				}
			}
			$html->headScript()->appendFile(HTTP_SERVER.'jscript/common/jquery-1.5.1.js');
			$html->headScript()->appendFile(HTTP_SERVER.'jscript/admin/jquery.bxSlider.min.js');
			$html->headScript()->appendFile(HTTP_SERVER.'jscript/admin/design/jsr_class.js');
			$html->headScript()->appendFile(HTTP_SERVER.'jscript/admin/design/view.js');
			$html->view->api_key = $user_api_key;
			if($script != ''){
			$script = 'jQuery(document).ready(function() {'.$script.'});';
			$html->headScript()->appendScript($script);}
			$html->html=$html->render('product-details.phtml');
			echo $html->render('view.phtml');
			}
			else
				echo "Page Not Found.";
			exit;
	}
	//--- Function is being call for "add to compare product" from frontent view page--
	public function compareproductAction(){	
		$obj = new Admin_Model_StructureMapper($this->_user_api_key);
		$obj->currentTemplate()->setTemplatePath();    	
		$template_path = $obj->__get('_template_path');
		$user_api_key = $this->_user_api_key;
		
		$xml_data_populator = new Admin_Model_XmlDataPopulator($template_path, $user_api_key);
		$xml_data_populator->populateCompareProduct();
		$xml_data_populator->__get('_compare_product_display_count');
		$totalCountProducts = $xml_data_populator->_compare_product_display_count;				

		if(($totalCountProducts!='all') && (sizeof($_SESSION['compare_products'])>=$totalCountProducts)){
		   echo "0";		  
 		   exit;
		}		

		if(!isset($_SESSION['compare_products'])) $_SESSION['compare_products'] = array();			
		if($_REQUEST['pid']!='' && !in_array($_REQUEST['pid'],$_SESSION['compare_products'])){							
			$_SESSION['compare_products'][] = $_REQUEST['pid'];								
		}

		$template_object = new Admin_Model_StructureMapper($this->_user_api_key);		
		$template_object->currentTemplate()->setTemplatePath();
		$compareProductDetails = $template_object->getCompareProductDetails();						
		echo $compareProductDetails;
		exit;		
	}

	public function removeproductAction(){		
	if($_REQUEST['sessionId']!=''){
		$pcount = $_REQUEST['sessionId'];

		if($pcount=="all")
		{
		   unset($_SESSION['compare_products']);
		}else{
			foreach($_SESSION['compare_products'] as $sesskey=>$sessval)	
			{
				//echo $sessval.'-------'.$pcount;

				if($sessval == $pcount)
				{
					unset($_SESSION['compare_products'][$sesskey]);						
				}
			}
		 
		}
		$template_object = new Admin_Model_StructureMapper($this->_user_api_key);		
		$template_object->currentTemplate()->setTemplatePath();
		$compareProductDetails = $template_object->getCompareProductDetails();						
		echo $compareProductDetails;
		exit;		

	}
	}
	public function xmlAction()
	{
		
	}
	public function pagesAction()
	{
		$page_url = $_POST['page'];
		$api = $_POST['api_key'];
		if($page_url)
		{
				echo "page found";
		}
		else
			echo "No Page Found";
		exit;
			
	}

 //-- Function returns language.xml file data --
	public function xmldataAction()
	{
	        $user_api_key = $_POST['apikey'];
		if($user_api_key)
		{	
			$obj = new Admin_Model_StructureMapper($user_api_key);
		    	$obj->currentTemplate()->setTemplatePath();
		    	$directory_path_prefix = $obj->__get('_template_path')."/../language.xml";
			$xml_file_contents = file_get_contents($directory_path_prefix);			
			echo $xml_file_contents;
			exit;
		}
		else
			echo "No Page Found";
		exit;
	}

  //-- Function returns tpl file data --	
	public function tpldataAction()
	{
		$tpl_file_name = $_POST['tpl'];
		if($this->_user_api_key)
		{	
			$obj = new Admin_Model_StructureMapper($this->_user_api_key);
		    	$obj->currentTemplate()->setTemplatePath();
		    	$tpl_path_prefix = $obj->__get('_template_path')."/blocks/".$tpl_file_name;
			if(!file_exists($tpl_path_prefix))
				$tpl_path_prefix = APPLICATION_PATH."/modules/default/views/scripts/templates/default/blocks/".$tpl_file_name;
			$tpl_file_contents = file_get_contents($tpl_path_prefix);			
			echo $tpl_file_contents;
			exit;
		}
		
		exit;
	}
	public function sessionAction(){
		if(!isset($_SESSION['USER']['userDetails']))
		{echo 0;exit;}
		if($_SESSION['USER']['userDetails'][0]['apikey'] != $_POST['apikey'])
		{echo $_SESSION['USER']['userDetails'][0]['apikey'];exit;}
		else
			{echo 1;exit;}
	}


}
?>
