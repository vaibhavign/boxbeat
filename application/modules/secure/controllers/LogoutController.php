<?php

session_start();

class Secure_LogoutController extends Zend_Controller_Action {

    public function init() {
        if (isset($_SESSION['USER']['userId'])) {
            //$this->_redirect(HTTP_SERVER.'/login');
        }
        Zend_Layout::getMvcInstance()->setLayout('secure');

        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH . '/modules/secure/layouts');
        $this->view->headScript()->appendFile('/jscript/common/jquery-1.4.2.min.js', 'text/javascript');
        $this->view->headLink()->appendStylesheet('/css/superadmin/superadmin.css');
        $this->view->headLink()->appendStylesheet('/css/secure/login.css');
        // $this->view->headTitle('o2o- Login');
    }

    public function indexAction() {
		$userName = new Zend_Session_Namespace('USER');
        $db = Zend_Db_Table::getDefaultAdapter();
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
		
        $db->query("delete from session where session_id='" . Zend_Session::getId() . "'");
        Zend_Session::destroy();
		if($_REQUEST['self']!=1)
			{
				echo '<script>window.location.href="/login"</script>';
			}
		else
			{
			
				if($_GET['request']!='')
				{
					$form='';
					
						
							?>
								<noscript>
								<meta http-equiv="refresh" content="0;url=<?php echo $_REQUEST['request']?>?authId=<?php echo Zend_Session::getId()?>"> 
								</noscript>
								
								<?php
								$form='<form name="goo2oform" id="goo2oform" method="post" action="'.$_REQUEST['request'].'">';
								//$form.="<input type='hidden' name='authid' value=''>";
							$form.="<input type='hidden' name='unsetrequest' value='1'>";
								//$form.="<input type='hidden' name='sessionvalue'  value='". json_encode($_SESSION)."'>";
								$form.='</form>';
								echo $form;
								
								echo '<script>document.forms["goo2oform"].submit();</script>';
								
								exit;
							
						/*else
							{
								$form='<form name="goo2oformlogin" id="goo2oformlogin" method="post" action="http://secure.o2ocheckout.com/login">';
								$form.="<input type='hidden' name='authid' value='".Zend_Session::getId()."'>";
								$form.="<input type='hidden' name='from'  value='". $_REQUEST['request']."'>";
								$form.='</form>';
								echo $form;
								echo '<script>document.forms["goo2oformlogin"].submit();</script>';
								exit;
							
								//$form.="<input type='hidden' name='sessionvalue'  value='". json_encode(array())."'>";
							}*/
					
				}
			}
		
    }

// end function indexAction
}

// End class Secure_login Controller