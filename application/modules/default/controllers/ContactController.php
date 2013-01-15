<?php

class ContactController extends Zend_Controller_Action
{

	private $form;

	public function init()
	{
	$this->form = new Default_Form_ContactForm();
	}
	
    function indexAction()
    {
			$this->view->pageTitle = "<b><b><u>Contact Us</u></b></b>";
			$this->view->bodyCopy = "<p><font color='red'>*Please fill out this form.</font></p>";
			if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            if($this->form->isValid($formData)) {
			$insert_model = new Default_Model_Insert();
			$data = array('title' => $this->_request->getParam('title'),'first_name' => $this->_request->getParam('firstName'),'last_name' => $this->_request->getParam('lastName'),'email' => $this->_request->getParam('email'),'message' => $this->_request->getParam('message'));
			$insert_model->insertcontact($data);             
            } else {
                $this->form->populate($formData);
            }
        }
        $this->view->form = $this->form;
    }
	
	 public function listAction()  
       {  
	   	
	        $DB = Zend_Db_Table::getDefaultAdapter();
     	 	$DB->setFetchMode(Zend_Db::FETCH_OBJ);  
			$sql = "SELECT * FROM users";  
   			$result = $DB->fetchAssoc($sql);  
   			$this->view->assign('title','Member Contact Message');  
    		$this->view->assign('description',' ');  
    		$this->view->assign('datas',$result);       
      } 
	   public function delAction()
	 {
	  		$DB = Zend_Db_Table::getDefaultAdapter();	
	  		$request=$this->getRequest();
	  		$DB->delete('users','id='.$request->getParam('id'));
	  		$this->view->assign('description','Deleting succes');              
	} 
	
}
?>