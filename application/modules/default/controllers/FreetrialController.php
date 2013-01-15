<?php
class FreetrialController extends Zend_Controller_Action {
    function init(){
    //	echo 'me'; exit;
        Zend_Layout::getMvcInstance()->setLayout('eshopbox'); // setting the layout file to secure
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH . '/modules/secure/layouts');

        $this->view->headScript()->appendFile('/jscript/common/jquery.min.js');

        $this->mapper=new Default_Model_IndexMapper();
        $this->freetrailmapper=new Default_Model_FreetrailMapper();
      //  $this->freetrailmapper->copyr('/home/eshopbox/public_html/gm','/home/eshopbox/public_html/cm16');
        $this->objTrigger=new notification();
       $this->domainapi = new o2oDomainClass();
     //  echo '<pre>';
     //  print_r($this->domainapi);
        //$domainapi->createSubdomain('mydomaintest');
    }
    function indexAction(){
    			
        $this->view->headLink()->appendStylesheet('/css/secure/elogin/freetrail.css');
        $this->view->headTitle('Sign up for a free 15-day trial - eshopbox.com');
        $userArr = "";
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
        if($_POST){
            $_POST['password']='admin';
            $this->mapper->saveRegistrationData($_POST);
           // echo 'me'; exit;
            //$userArr = array('username'=>$_POST['uremail'],'user_full_name'=>$_POST['urname'],'user_email_address'=>$_POST['uremail'],'user_image'=>'no_image.jpg','user_location'=>'8,Delhi','vcode'=>$vcode,'eamil_verification'=>'1');
        //   echo "<pre>";print_r($this->domainap); exit;
         //   $this->domainapi->createSubdomain($_POST['ururl']);
          header("Location: /freetrial/creatingstore");  exit;
           // $this->_redirect('/freetrial/creatingstore');
         //   exit;										

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
    		
    	function creatingstoreAction(){
    	//	echo 'me'; exit;
    		$this->view->headLink()->appendStylesheet('/css/secure/elogin/creatingstore.css');
    		 $this->view->headTitle('Creating store for a free 15-day trial - eshopbox.com');
    		}	
    		
    	function storereadyAction(){
    		$this->view->headLink()->appendStylesheet('/css/secure/elogin/storeready.css');
$this->view->headTitle('Store ready for a free 15-day trial - eshopbox.com');
    		$REGISSESSION2 = new Zend_Session_Namespace('REGISSESSION');
    		$this->view->sessDetails = $REGISSESSION2->USERDETAILS; 
    		//print_r($REGISSESSION2->USERDETAILS);
    		//	echo 'test'; exit;
    		}	
}
?>	
