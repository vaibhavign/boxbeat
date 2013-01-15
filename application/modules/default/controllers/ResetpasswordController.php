<?php
class ResetpasswordController extends Zend_Controller_Action {
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
				$data = $this->_request->getParams();
				$this->view->passcode=$data['passcode'];
				$this->view->vcode=$data['emailpasscode'];
				$newpassword = trim($_POST['password']);
				$getdetailpasscodes = $this->mapper->getdetailpasscode($data['passcode']);
				$uniquepasscode = explode(',', $getdetailpasscodes['forgetpasscode']);
				
				if (!(in_array($data['emailpasscode'], $uniquepasscode))) {
					header('Location:'.HTTP_ROOT);
				}
				if($newpassword=='')
					{
						$this->view->headTitle('Reset password - eshopbox.com');
						$this->view->headLink()->appendStylesheet('/css/secure/elogin/elogin.css');	
						$this->view->headScript()->appendFile('/jscript/secure/elogin/resetpassword.js');
					}
				else
					{
						$passcode=$_POST['vcode'];
						$vcode=$_POST['passcode'];

						$getdetailpasscodes = $this->mapper->getdetailpasscode($vcode);
						
						$useremail=$getdetailpasscodes['user_email_address'];
						$uniquepasscode = explode(',', $getdetailpasscodes['forgetpasscode']);
						
						
						unset($uniquepasscode[array_search($passcode, $uniquepasscode)]);
						$code_str = implode(',', $uniquepasscode);
						

						$this->mapper->updateusenamepassword($useremail, $newpassword, $code_str, $vcode);
							$tData = array('to_id' => $getdetailpasscodes['uid'],
							'to_mail' => $getdetailpasscodes['user_email_address'],
							'to_name' => $getdetailpasscodes['user_full_name']);
							//$this->objTrigger->triggerFire(4, $tData);
						echo json_encode(1);exit;
					

					}
		}
	
		function resetAction()
		{
			$this->_helper->layout()->disableLayout();
			
				/*$this->view->vcodes = $uservcodes;
				$useremailpasscode = $Input['emailpasscode'];
				$this->view->passemailcode = $useremailpasscode;
				$newpassword = trim($_POST['newpassword']);
				$posteduservcode = trim($_POST['newvcode']);
				$posteduserpasscode = $this->_getParam('emailpasscode'); //$_POST['hideforgetpasscde'];
				$postedpageaction = $_POST['hideresetpasscode'];
				$mapper = new Secure_Model_LoginMapper();
				$getdetailpasscodes = $mapper->getdetailpasscode($uservcodes);
				$uniquepasscode = explode(',', $getdetailpasscodes['forgetpasscode']);
				$usernameid = $getdetailpasscodes['uid'];
				if (!(in_array($posteduserpasscode, $uniquepasscode))) {
				$this->_redirect(HTTP_SECURE . '/login/forgetpassword');
				}
				if ($postedpageaction == 'hiddenpasswordvalue') {
				unset($uniquepasscode[array_search($posteduserpasscode, $uniquepasscode)]);
				$code_str = implode(',', $uniquepasscode);
				$mapper->updateusenamepassword($usernameid, $newpassword, $code_str, $posteduservcode);
				$tData = array('to_id' => $getdetailpasscodes['uid'],
				'to_mail' => $getdetailpasscodes['user_email_address'],
				'to_name' => $getdetailpasscodes['user_full_name']);
				$this->objTrigger->triggerFire(4, $tData);
				$this->_redirect(HTTP_SECURE . '/login/resetpasswordinstruction');
				}
				}*/
				echo json_encode(0);exit;
				exit;
		}		
	
	
	}
?>	
