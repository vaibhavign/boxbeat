<?php
/**
 * @author : Nagendra yadav
 * Used for User accountsetting actions
 */
class Secure_AccountsettingController extends Zend_Controller_Action
{
     public function init()
        {
		$this->view->headLink()->appendStylesheet('/css/secure/headersetting.css');
		$this->view->controller= $this->_request->getParam('controller');
		Zend_Layout::getMvcInstance()->setLayout('secureadmin');
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');
		$this->view->headLink()->appendStylesheet('/css/secure/accountsetting.css');
                $orilogin = new Zend_Session_Namespace('original_login');
                $this->view->orilogin=$orilogin;
                
        }
    public function indexAction()
    {
                $this->userName = new Zend_Session_Namespace('USER');
                $orilogin = new Zend_Session_Namespace('original_login');
                
                if ($this->userName->userId == '') {
                    $this->_redirect(HTTP_SECURE . '/login');
                }
                Zend_Layout::getMvcInstance()->setLayout('secureadmin');
                Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');
                $this->view->headLink()->appendStylesheet('/css/secure/accountsettings.css');
                $this->view->headTitle('Account setting - ' . $_SESSION['USER']['userDetails'][0]['username'].''.PAGE_EXTENSION);
                $this->view->headMeta()->setName('keywords', 'Account setting , GoO2o Technologies');
                $this->view->headMeta()->setName('description', 'Account setting , GoO2o Technologies');
                
                $userlocation=$orilogin->user['0']['user_location'];
                $userstate_city=explode(',',$userlocation);
                $mapper  = new Secure_Model_AccountsettingMapper();

                $userstate=$mapper->getuserstate($userstate_city[0]);
                //$usercity=$mapper->getusercity($userstate_city[1]);
                $this->view->user_state=$userstate;
                //$this->view->user_city=$usercity;
                $this->view->userimage=$orilogin->user['0']['user_image'];
                $this->view->passwordupdatedtime=$mapper->lastupdatedtime();
                $allemails=$mapper->getallemails();
                $this->view->alluseremail=$allemails;


    } // end function index action
     public function editmobileAction()
    {
                $this->view->headScript()->appendFile('/jscript/admin/popups.js','text/javascript');
                $this->orilogin = new Zend_Session_Namespace('original_login');
                if ($this->orilogin->userId == ''){
                    $this->_redirect(HTTP_SECURE . '/login');
                }

                Zend_Layout::getMvcInstance()->setLayout('secure');
                Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');
                $this->view->headLink()->appendStylesheet('/css/secure/editmobile.css');
                if($_SESSION['USER']['userDetails'][0]['user_mobile']=="")
                    {
                        $this->view->headTitle('Add mobile - '.$this->orilogin->user[0]['username'].PAGE_EXTENSION);
                    }
                else {
                        $this->view->headTitle('Edit mobile - '.$this->orilogin->user[0]['username'].PAGE_EXTENSION);
                }
                $this->view->headMeta()->setName('keywords', 'Account setting , GoO2o Technologies');
                $this->view->headMeta()->setName('description', 'Account setting , GoO2o Technologies');
                $this->view->headScript()->appendFile('/jscript/common/o2ojslib.js','text/javascript');
                $this->view->headScript()->appendFile('/jscript/secure/editmobile.js','text/javascript');
                $this->view->mobilephone=$this->orilogin->user[0]['user_mobile'];
                $pageaction=$_POST['pageaction'];
                $userphone=trim($_POST['user_phone']);
                if($pageaction=='editusercontact')
                {
                    $mapper  = new Secure_Model_AccountsettingMapper();
                    $updatecontact=$mapper->updateusercontact($userphone);
                    $this->_redirect(HTTP_SECURE . '/accountsetting');
                }   

    } 

 public function updatepasswordAction()
    {
            $this->userName = new Zend_Session_Namespace('original_login');
            if ($this->userName->userId == '') {
                $this->_redirect(HTTP_SECURE . '/login');
            }
            Zend_Layout::getMvcInstance()->setLayout('secure');
            Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');
            $this->view->headLink()->appendStylesheet('/css/secure/updatepassword.css');
            $this->view->headTitle('Update password - '. $_SESSION['USER']['userDetails'][0]['username'].PAGE_EXTENSION);
            $this->view->headMeta()->setName('keywords', 'Account setting , GoO2o Technologies');
            $this->view->headMeta()->setName('description', 'Account setting , GoO2o Technologies');
            $this->view->headScript()->appendFile('/jscript/common/o2ojslib.js');
            $this->view->headScript()->appendFile('/jscript/secure/updatepassword.js');
            $currentpass=$_POST['currentpass'];
            $newpassword=$_POST['newpassword'];
            $pageaction=$_POST['action'];
            if($pageaction=='updatepasswordaction'){
                $mapper  = new Secure_Model_AccountsettingMapper();
                $validateuserpass=$mapper->checkuserpass($currentpass,$newpassword);

            }
     }
        
    public function updateusernameAction()
    {
        
        $orilogin = new Zend_Session_Namespace('original_login');
        if ($orilogin->userId == '') {
            $this->_redirect(HTTP_SECURE . '/login');
        }
        else if($orilogin->user[0]['updatedusername_time'] != 0){
             $this->_redirect(HTTP_SECURE . '/accountsetting/useralreadychanged');
        }
	Zend_Layout::getMvcInstance()->setLayout('secure');
	Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');
        $this->view->headLink()->appendStylesheet('/css/secure/updateusername.css');
        $this->view->headTitle('Edit username - '. $_SESSION['USER']['userDetails'][0]['username'].PAGE_EXTENSION);
        $this->view->headMeta()->setName('keywords', 'Account setting , GoO2o Technologies');
        $this->view->headMeta()->setName('description', 'Account setting , GoO2o Technologies');
        $this->view->headScript()->appendFile('/jscript/common/o2ojslib.js','text/javascript');
        $currentpass=$_POST['currentpass'];
        $username=$_POST['username'];
        $pageaction=$_POST['action'];
        $this->view->usernameprev=$orilogin->user[0]['username'];
        if($pageaction=='updateusenameaction'){
            $mapper  = new Secure_Model_AccountsettingMapper();
            $validateuserpass=$mapper->updateusername($currentpass,$username);
            exit;
        }
    } 
      public function editemailAction()
    {
        $this->userName = new Zend_Session_Namespace('original_login');
        $this->view->headLink()->appendStylesheet('/css/default/popup.css');
        //$this->view->headScript()->appendFile('/jscript/default/jquery.popup.js', 'text/javascript');
	//$this->userName->userId = '4';
        if ($this->userName->userId == '') {
            $this->_redirect(HTTP_SECURE . '/login');
        }
	Zend_Layout::getMvcInstance()->setLayout('secure');
	Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');
        $this->view->headLink()->appendStylesheet('/css/secure/editemail.css');
        $this->view->headTitle('Edit Email - '. $_SESSION['USER']['userDetails'][0]['username'].PAGE_EXTENSION);
        $this->view->headMeta()->setName('keywords', 'Account setting , GoO2o Technologies');
        $this->view->headMeta()->setName('description', 'Account setting , GoO2o Technologies');
        $this->view->headScript()->appendFile('/jscript/common/o2ojslib.js','text/javascript');
        $this->view->headScript()->appendFile('/jscript/secure/editemail.js','text/javascript');
        $this->view->useremail=$this->userName->user[0]['user_email_address'];
        $mapper  = new Secure_Model_AccountsettingMapper();
        $allemails=$mapper->getallemails();
        $this->view->alluseremail=$allemails;        
        $this->view->allverified=$mapper->getverifiedemail();
    } 
    public function editbasicinfoAction()
    {            
        $this->view->headLink()->appendStylesheet('/css/default/popup.css');
        $this->userName = new Zend_Session_Namespace('original_login');
        $this->view->userName=$this->userName;
        if ($this->userName->userId == '') {
            $this->_redirect(HTTP_SECURE . '/login');
        }
        $class=new General();
	Zend_Layout::getMvcInstance()->setLayout('secure');
	Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');
        $this->view->headLink()->appendStylesheet('/css/secure/editbasicinfo.css');
        $this->view->headTitle('Edit Basic Info - '. $_SESSION['USER']['userDetails'][0]['username'].PAGE_EXTENSION);
        $this->view->headMeta()->setName('keywords', 'Account setting , GoO2o Technologies');
        $this->view->headMeta()->setName('description', 'Account setting , GoO2o Technologies');
        $this->view->headScript()->appendFile('/jscript/common/o2ojslib.js','text/javascript');
        $this->view->headScript()->appendFile('/jscript/secure/basicinfo.js','text/javascript');
        $this->view->headScript()->appendFile('/jscript/secure/ajaxupload.js','text/javascript');
        //$this->view->headScript()->appendFile('/jscript/admin/popups.js');
        $this->view->userimage=$this->userName->user[0]['user_image'];
        $this->view->userfullname=$this->userName->user[0]['user_full_name'];
        $regmapper  = new Secure_Model_AccountsettingMapper(); // object for register mapper class
	$returnedStateArray = $regmapper->getStateList(); // getting Country list
	$this->view->states=$returnedStateArray;
        $returnedStateArray = $regmapper->getStateList(); // getting Country list
	$this->view->stateid=$_POST['state_name'];
	$this->view->states=$returnedStateArray;
        $userlocation=$this->userName->user[0]['user_location'];
        $userstate_city=explode(',',$userlocation);
        $mapper  = new Secure_Model_AccountsettingMapper();
        $userstate=$mapper->getuserstate($userstate_city[0]);
        //$usercity=$mapper->getusercity($userstate_city[1]);
        $this->view->user_cityname=$userstate_city['1'];
        $this->view->user_stateid=$userstate_city[0];
        $user_cityid=$userstate_city[1];
        //$this->view->city=$regmapper->getLocationList($userstate_city[0],$user_cityid);
                if ((($_FILES["name"]["type"] == "image/gif")
                || ($_FILES["name"]["type"] == "image/jpeg")
                || ($_FILES["name"]["type"] == "image/pjpeg")
                || ($_FILES["name"]["type"] == "image/png"))
                && ($_FILES["name"]["size"] <= 700*1024)&& $_POST['pageaction']=='edituserinformation')
                {
                    if (!(file_exists("images/secure/user_image/".$this->userName->userId))) {
                    $rs = mkdir("images/secure/user_image/".$this->userName->userId, 0777 );    
                 } 
                    
                   
                    move_uploaded_file($_FILES["name"]["tmp_name"],"images/secure/user_image/".$this->userName->userId.'/'. $_FILES["name"]["name"]);
                     //  echo 'fdfd';exit;
                    
                        if($this->userName->user[0]['user_image'] != 'no_image.jpg')
                        {
                           
                            unlink("images/secure/user_image/".$this->userName->userId.'/'. $this->userName->user[0]['user_image']);
                            unlink("images/secure/user_image/".$this->userName->userId.'/'.$this->userName->userId.'_'.$this->userName->user[0]['user_image']);
                        }
                        $mapper  = new Secure_Model_AccountsettingMapper();
                        $newstateid=trim($_POST['state_name']);
                        $newcityid=trim($_POST['location']);
                        $newlocation=$newstateid.','.$newcityid;
                        $newimage=trim($_FILES["name"]["name"]);
                        if($newimage=='')
                        {
                            $newimage='no_image.jpg';
                        }
                        $mapper->updateuserinfo(trim($_POST['userfullname']),$newlocation,$newimage);
                        $this->_redirect(HTTP_SECURE . '/accountsetting');
                }
                else if($_POST['pageaction']=='edituserinformation' && trim($_FILES["name"]["name"])=='')
                {
                        $mapper  = new Secure_Model_AccountsettingMapper();
                        $newstateid=trim($_POST['state_name']);
                        $newcityid=trim($_POST['location']);
                        $newlocation=$newstateid.','.$newcityid;
                        $newimage=$this->userName->user[0]['user_image'];
                        $mapper->updateuserinfo(trim($_POST['userfullname']),$newlocation,$newimage);
                        $this->_redirect(HTTP_SECURE . '/accountsetting');
                }
                    
               
     
    } 
    
     public function useralreadychangedAction()
    {
            $this->userName = new Zend_Session_Namespace('original_login');
            if ($this->userName->userId == '') {
                $this->_redirect(HTTP_SECURE . '/login');
            }

            Zend_Layout::getMvcInstance()->setLayout('secure');
            Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');
            $this->view->headLink()->appendStylesheet('/css/secure/useralreadychanged.css');
            $this->view->headTitle('Edit username - '. $_SESSION['USER']['userDetails'][0]['username'].PAGE_EXTENSION);
            $this->view->headMeta()->setName('keywords', 'Account setting , GoO2o Technologies');
            $this->view->headMeta()->setName('description', 'Account setting , GoO2o Technologies');
            $this->view->headScript()->appendFile('/jscript/common/o2ojslib.js','text/javascript');
            $lastuserupdatedtime=date('jS F, Y',$this->userName->user[0]['updatedusername_time']);
            $this->view->userupdatedtime=$lastuserupdatedtime;
    } 
    public function checkusernameAction(){
             $this->_helper->layout->disableLayout();
             $this->_helper->viewRenderer->setNoRender(true);
             $mapper  = new Secure_Model_AccountsettingMapper();
             $mapper->checkUsernameExists($_POST['username']);
             
    }
     public function checkuseremailAction(){//print_r($_POST);exit;
             $this->_helper->layout->disableLayout();
             $this->_helper->viewRenderer->setNoRender(true);
             $mapper  = new Secure_Model_AccountsettingMapper();
             $mapper->checkUseremailExists($_POST['useremail']);
//             exit;
    }
       public function newupdateusernameAction(){
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $currentpass=$_POST['currentpass'];
            $username=$_POST['username'];
            $pageaction=$_POST['action'];
            if($pageaction=='updateusenameaction'){
             $mapper  = new Secure_Model_AccountsettingMapper();
             $validateuserpass=$mapper->updateusername($currentpass,$username);          
           }
    }
     public function addnewuseremailAction(){
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $currentpass=$_POST['currentpass'];
            $useremail=$_POST['useremail'];
            $pageaction=$_POST['action'];                    
                    $this->userName = new Zend_Session_Namespace('USER');
                    if ($this->userName->userId == '') {
                    $this->_redirect(HTTP_SECURE . '/login');
                   }
            if($pageaction=='addemailaction'){
             
                    $mapper  = new Secure_Model_AccountsettingMapper();
                    $mapper->addnewemail($currentpass,$useremail);
                   
                                
           }
    }
             public function removeuserimageAction(){
                 $this->userName = new Zend_Session_Namespace('original_login');
                 if($this->userName->user[0]['user_image'] != 'no_image.jpg')
                        {
                             unlink("images/secure/user_image/".$this->userName->userId.'/'. $this->userName->user[0]['user_image']);
                             unlink("images/secure/user_image/".$this->userName->userId.'/'.$this->userName->userId.'_'.$this->userName->user[0]['user_image']);
                        }
             $mapper  = new Secure_Model_AccountsettingMapper(); // object for register mapper class
             $mapper->removeuserimage(); // getting location list
             $this->_redirect(HTTP_SECURE . '/accountsetting');
		}
             public function removeuseremailAction(){                 
                 $this->_helper->layout->disableLayout();
                 $this->_helper->viewRenderer->setNoRender(true);
                 $mapper  = new Secure_Model_AccountsettingMapper(); // object for register mapper class
                 $mapper->removeuseremails($_POST['useremailid']); // getting location list
		}
             public function resendemailAction(){
                $this->objTrigger=new Notification();
                $this->_helper->layout->disableLayout();
                $this->_helper->viewRenderer->setNoRender(true);
                $mapper  = new Secure_Model_AccountsettingMapper();
                $userdetail=$mapper->getuseremailsdetail($_POST['useremailid']);
                $this->userName = new Zend_Session_Namespace('original_login');
                if ($this->userName->userId == '') {
                    $this->_redirect(HTTP_SECURE . '/login');
               }
                $getuservcode=$userdetail['vcode'];
                $email=$userdetail['user_email'];
                $fullusername=$this->userName->user[0]['user_full_name'];
                $vcode = HTTPS_SECURE.'/registration/newmailconfirmation/passcode/'.$getuservcode;
                $notmyaccountlink = HTTPS_SECURE.'/login/notyournewaccount/passcode/'.$getuservcode;
                           $tData = array('link'=>$vcode,
                          'notmyaccountlinks'=> $notmyaccountlink,
                          'name'=>$fullusername,
                          'to_id'=>$email);                           
                           $this->objTrigger->triggerFire(37,$tData);
               }
            public function makeemailprimaryAction(){
                 $this->_helper->layout->disableLayout();
                 $this->_helper->viewRenderer->setNoRender(true);
                 $mapper  = new Secure_Model_AccountsettingMapper(); // object for register mapper class
                 $check=$mapper->makeuseremailprimary($_POST['useremailid']); // getting location list
                 if($check==1)
                 {
                     echo '/secure/accountsetting/logout';
                     exit;
                 }
		}
             public function logoutAction(){
                $db = Zend_Db_Table::getDefaultAdapter();
                Zend_Session::start();
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$db->query("delete from session where session_id='".Zend_Session::getId()."'");
		$db->query("delete from session_loged_store where session_id='".Zend_Session::getId()."'");
		$user=new Zend_Session_Namespace('USER');
		$db->query("delete from session_loged_store where session_id='".$user->userId."'");
		Zend_Session::destroy();
		$this->_redirect(HTTP_SECURE.'/login');	
	 }

}
?>
