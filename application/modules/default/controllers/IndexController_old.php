<?php
class IndexController extends Zend_Controller_Action
{

    public function init()
    {
	if($_SERVER['REMOTE_ADDR']=="182.71.165.53" && $_SERVER['HTTP_HOST']=='www.goo2ostore.com'){
		echo 'tusar';
		exit;
		}
	
	 $Inputs = $this->_request->getParams();
	

        /* Initialize action controller here */
      /* $uid= new Zend_Session_Namespace('USER');
         $vcodes=$uid->userDetails[0]['vcode'];
         if(isset($vcodes) && isset($_SESSION['USER']['userId']) && trim($vcodes)!='')
         {
			 $mapper  = new Secure_Model_LoginMapper();
			
			 $checkuserverification=$mapper->checkusrverification($vcodes);
			 $checkcanceluser=$mapper->checkcanceleduser($vcodes);
			 if($checkcanceluser>0)
			 {
				$this->_redirect(HTTP_SECURE.'/login/notmyaccount/passcode/'.$vcodes);
			 }
			 if(isset($_SESSION['USER']['userId']) && $checkuserverification==0){
	
				$this->_redirect(HTTP_SECURE.'/login/accountrestricted/passcode/'.$vcodes);
			}
			 if(isset($_SESSION['USER']['userId'])){
				$this->_redirect(HTTP_SERVER.'/myaccount');
			}
    	}*/
	}
     public function homeAction()
    {
	echo session_id();exit;
      
    }
    public function listAction()
    {
        //$this->view->myname = 'Jasobanta1';
		// action body
    }

   public function indexAction()
    {

		   $original = new Zend_Session_Namespace('original_login'); 
		 $this->view->name= $original;
		// action body
    }
	
	

}
