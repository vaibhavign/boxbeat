<?php
class FreetrialController extends Zend_Controller_Action {
    function init(){
    	
        Zend_Layout::getMvcInstance()->setLayout('eshopbox'); // setting the layout file to secure
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH . '/modules/secure/layouts');

        $this->view->headScript()->appendFile('/jscript/common/jquery.min.js');

        $this->mapper=new Default_Model_IndexMapper();
         $this->freetrailmapper=new Default_Model_FreetrailMapper();
        $this->objTrigger=new notification();
       // echo 'test2'; exit;
       // $domainapi = new o2oDomainClass();
        //$domainapi->createSubdomain('mydomaintest');
    }
    function indexAction(){
        $this->view->headLink()->appendStylesheet('/css/secure/elogin/freetrail.css');
        $this->view->headTitle('Sign up for a free 15-day trial - eshopbox.com');
        $userArr = "";
	//	echo 'me';
	$abc= array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
	for($i=0;$i<8;$i++){
				$rand = "";
				for($j=0;$j<5;$j++){
						$rand .=$abc[rand(0,25)];							
					}
		$randArray[$i] = $rand; 
		}
		$num= array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
	$randNum =	$num[rand(0,8)];
	$this->view->randToDisplay = $randArray[$randNum]; 	
	$this->view->totalRandArray = $randArray;
		//echo '<pre>';
		//print_r($randArray);			
//$abc= array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
//$num= array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
//echo $abc[rand(0,25)];
//echo $num[rand(0,9)];  
        if($_POST){
          //  $_POST['password']='admin';
            $this->mapper->saveRegistrationData($_POST);
        }
    }
    
    function domaincheckAction(){
    //	print_r($_GET);
    	//		echo 'me';
			//	exit;    	
    	//		print_r($this->freetrailmapper);
    	 $abc =   $this->freetrailmapper->domainAvailability($_GET['domain']);
  echo json_encode($abc);    		
    		exit;
    	}
    	
    	function checkemailAction(){
    	//	    	print_r($_GET);
    		$abc =   $this->freetrailmapper->emailAvailability($_GET['email']);
  			echo json_encode($abc);    		
    		exit;
    		} 
    
    
}
?>	
