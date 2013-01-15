<?php
class ForgotpasswordController extends Zend_Controller_Action {
	function init()
		{
				
				Zend_Layout::getMvcInstance()->setLayout('eshopbox'); // setting the layout file to secure
				Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH . '/modules/secure/layouts');
				
				
				$this->view->headScript()->appendFile('/jscript/common/jquery.min.js');
				
				$this->mapper=new Default_Model_IndexMapper();

				$this->objTrigger=new notification();
		}
	function indexAction()
		{
				$email=$_POST['email'];	
				if($email=='')
					{
					$this->view->headTitle('Forgot password - eshopbox.com');
					$this->view->headScript()->appendFile('/jscript/secure/elogin/forgotpassword.js');
					$this->view->headLink()->appendStylesheet('/css/secure/elogin/elogin.css');

					}
				else
					{
					$this->_helper->layout()->disableLayout();

					$valusername = $this->mapper->getvcodesusername(trim($_POST['email']));
					
			
				if(empty($valusername))
					{
						echo json_encode(0);exit;
					}
					else
					{
						$getuservcode = $valusername[0]['vcode'];
						$getuserid = $valusername[0]['uid'];
						$getuserfullusername = $valusername[0]['user_full_name'];
						$existpasscode = $valusername[0]['forgetpasscode'];
						$valuseremail = count($valusername);
						$emailpasscode = rand(10000, 5000);
						$newemailpasscode = $existpasscode . ',' . $emailpasscode;
						$this->mapper->updateforgetpasstatus($getuservcode,$newemailpasscode);	
						$vcode = HTTP_ROOT . '/resetpassword/index/passcode/' . $getuservcode . '/emailpasscode/' . $emailpasscode;
						$tData = array('password_reset_link' => $vcode,
						'to_id' => $valusername[0]['id'],
						'to_mail' => $email,
						'to_name' => $valusername[0]['user_full_name']);
						echo '<pre>';
						print_r($tData);exit;
						
						$this->objTrigger->triggerFire(3, $tData);
						//mail to be send;
						
						//$this->_redirect(HTTP_SECURE . '/login/forgetpasswordinstruction/passcode/' . $getuservcode);
					}
					exit;

					}

				
				
	
				
		}
	
		
			
		
	
	}
?>	
