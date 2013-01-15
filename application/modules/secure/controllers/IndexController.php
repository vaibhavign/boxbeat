<?php

class Secure_IndexController extends Zend_Controller_Action
{

    public function init()
    {
	
        /* Initialize action controller here */
		$Inputs = $this->_request->getParams();
		 $this->view->controller=$Inputs['controller'];
		 echo '<script type="text/javascript" language="javascript">self.close();</script>';exit;
    }

    public function indexAction()
    {
		
        // action body
    }
	
	  public function twoAction()
    {
		
        // action body
    }
	
	
	 

}

