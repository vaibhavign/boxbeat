<?php
/**
 * @author : vaibhav sharma
 * Used for User registration actions
 * @var private $publickey : public captcha key
 * @var private $privatekey : private captcha key
 * func init
 * setting the values for $publickey and $privatekey
 * @obj $mapper : object of registermapper class
 * @var $returnedLocationArray : Location array
 * @var $cityList : City list array
 * @obj $selectBox : object of Zend_Form_Element_Select class
 * Creation Date : 09-04-2011
 * Created By : Vaibhav Sharma
 * // use comma separated for entering modification date
 * Modified Date :
 * Modified Date :
 * Reason :
 */
require_once 'recaptchalib.php'; // for recaptcha path root\library
class Secure_O2oregisterController extends Zend_Controller_Action
{
	private $publickey;
        private $privatekey;
        private $userid;

    public function init()
    {
        $this->userid = $_SESSION['USER']['userId'];
        $this->publickey = "6LfgNAoAAAAAAEb9m-vKAwvOtZulVJkoogKwC40v "; // recaptcha public key
	$this->privatekey = "6LfgNAoAAAAAAOrGvWEemmjTn62kqvYe-exXJbsC "; // recaptcha private key
        $mapper  = new Secure_Model_RegisterMapper(); // object for register mapper class
        $returnedLocationArray = $mapper->getLocationList(); // getting location list
        foreach($returnedLocationArray as $location){
            $cityList[$location->id] =  $location->cityname;
        }
        $selectBox = new Zend_Form_Element_Select('user_location'); // creating zend select box
        $selectBox->setLabel('Location')
                ->setMultiOptions($cityList)
                ->setOptions(array('class' => 'select_wid180'));
        $selectBox->removeDecorator('label');
        $selectBox->removeDecorator('HtmlTag');
        $this->view->country = $selectBox;
    } // End Init function

    // main action for register process
    public function indexAction()
    {
        Zend_Layout::getMvcInstance()->setLayout('secure');
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');
        $this->view->headScript()->appendFile('/jscript/common/o2ojslib.js','text/javascript');
        $this->view->headScript()->appendFile('/jscript/secure/o2oregister.js','text/javascript');
        $this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
        $this->view->headTitle('SignUp of o2o Account - o2o');
        $this->view->publickey = $this->publickey;
        $this->view->privatekey = $this->privatekey;

        // when form is posted
        if($_POST['action']=='process'){
   
            $map = new Secure_Model_Register($_POST);
            $mapper  = new Secure_Model_RegisterMapper();
            $mapper->registrationFormValidation($map);
            if(empty($mapper->_errors)){
                $idUser = $mapper->registerUser($map);

               // $sessName = new Zend_Session_Namespace('SESSION');// namespace session
                $userName = new Zend_Session_Namespace('USER');// session user
                //$sessName->thissessid = $apiSessData ;// setting the session value
                $userName->userId = $idUser['lastinserted'] ;// settig the userid in the session user
                $vcode =  makeUrl($idUser['lastinserted']);
                $html = new Zend_View();
                $html->setScriptPath(APPLICATION_PATH . '/modules/secure/views/emails/');
                $html->assign('name', $idUser['user_full_name']);
                $html->assign('link', $vcode);

                // create mail object
                $mail = new Zend_Mail('utf-8');



                // render view
                $bodyText = $html->render('registertocustomer.phtml');

                // configure base stuff
                $mail->addTo('vaibhavign@gmail.com');
                $mail->setSubject('Welcome to Limespace.de');
                $mail->setFrom('tets@goo2o.com','test');
                $mail->setBodyHtml($bodyText);
                $mail->send();

                $this->_redirect('/o2oregister/addprofileinfo');
            } else {
            // display server side errors
                $this->view->errors = $mapper->_errors;
            }
        }
    } // end function index action

    // add profile info action
    public function addprofileinfoAction(){
       
        Zend_Layout::getMvcInstance()->setLayout('secure');
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');
        $this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
        $this->view->headScript()->appendFile('/jscript/common/o2ojslib.js','text/javascript');
        $this->view->headScript()->appendFile('/jscript/secure/addprofileinfo.js','text/javascript');
        $this->view->headTitle('Add profile Info - o2o');
        $this->view->publickey = $this->publickey;
        $this->view->privatekey = $this->privatekey;
        //echo "m2d";
        // coding of insertion of user image and bio goes here
        
    }

    // set preference action
    public function setyourpreferenceAction(){
        Zend_Layout::getMvcInstance()->setLayout('secure');
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');
        $this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
        $this->view->headScript()->appendFile('/jscript/common/o2ojslib.js','text/javascript');
        $this->view->headScript()->appendFile('/jscript/secure/setyourpreference.js','text/javascript');
        $this->view->headTitle('Set Your Preference - o2o');
        $this->view->publickey = $this->publickey;
        $this->view->privatekey = $this->privatekey;
        $mapper  = new Secure_Model_RegisterMapper();
        $returnedArray = $mapper->getDepartmentList();
        foreach($returnedArray as $ass){
         if($counter%2==0){
			$firstdept[]=array('department_id'=>$ass->id,
                                          'dept_name'=>$ass->dept_name );
			$classnamefirst=explode(' ',strtolower($ass->dept_name));
			$firstclass[]=$classnamefirst[0].$ass->id;
		}else{
			$seconddept[]=array('department_id'=>$ass->id,
                                          'dept_name'=>$ass->dept_name );
			$classnamesecond=explode(' ',strtolower($ass->dept_name));
			$secondclass[]=$classnamesecond[0].$ass->id;
		}
                $counter++;
        }
       $selectedDept =  $mapper->selectUserDepartment($this->userid);
       $k = 0;
       foreach($selectedDept as $seldep){
            $arraySelDept[$k] = $seldep->dept_id;
            $k++;
       }
       
        $this->view->firstdept = $firstdept;
        $this->view->seconddept = $seconddept;
        $this->view->firstclass = $firstclass;
        $this->view->secondclass = $secondclass;
        $this->view->selecteddip = $arraySelDept;
    }

    // activate mobile action
    public function activatemobileAction(){
        Zend_Layout::getMvcInstance()->setLayout('secure');
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');
        $this->view->headTitle('Activate your mobile - o2o');
                $this->view->headScript()->appendFile('/jscript/common/o2ojslib.js','text/javascript');
        $this->view->headScript()->appendFile('/jscript/secure/activatemobile.js','text/javascript');

    }

    // verify yourself action
    public function verifyyourselfAction(){
        Zend_Layout::getMvcInstance()->setLayout('secure');
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');
        $this->view->headTitle('Verify Yourself - o2o');
        $this->view->headScript()->appendFile('/jscript/common/o2ojslib.js','text/javascript');
        $this->view->headScript()->appendFile('/jscript/secure/verifyyourself.js','text/javascript');
        $this->view->loggedUserDetail = getLoggedUserDetails('id',$this->userid);
        // for ajax control
        if($_POST['action']=='checkemail'){
            $this->_helper->layout->disableLayout(); // disable layout
            $this->_helper->viewRenderer->setNoRender(true); // no rendering
            $mapper  = new Secure_Model_RegisterMapper();
            $returnedArray = $mapper->resendEmailVerification($_POST,$this->userid);
        }
    }

     public function checkusernameAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $mapper  = new Secure_Model_RegisterMapper();
        $mapper->checkUsernameExists($_POST['username']);
    }

     public function checkemailAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $email = $_POST['email'];
        $mapper  = new Secure_Model_RegisterMapper();
        $mapper->checkEmailIdExists($email);
    }

    public function insertprofileAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        if($_POST['checkposted']=='true'){
           // echo "m2";
           $map = new Secure_Model_Register($_POST);
           $mapper  = new Secure_Model_RegisterMapper();
           $mapper->insertProfileData($map);

            // end coding of insertion of user image and bio
        }
    }

    public function setdepartmentpreferenceAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $mapper  = new Secure_Model_RegisterMapper();
      
        $mapper->subscribeUnsubs($_POST);
        
    }
    
    public function verifymobileAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if($_POST['activatemobile']=='true'){
            $mapper  = new Secure_Model_RegisterMapper();

        $mapper->sendVerificationSms($_POST);
        }

        if($_POST['action']=='checkconfirmcode'){
            $mapper  = new Secure_Model_RegisterMapper();

        $mapper->verifySmsVerificationCode($_POST);
        }


    }
}