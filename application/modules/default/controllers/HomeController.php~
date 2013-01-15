<?php
class HomeController extends Zend_Controller_Action
{

    public function init()
    {

	$userName = new Zend_Session_Namespace('USER');
	if($userName->userId=='')
	{
		$this->_redirect('/secure/login');
	}
	$this->view->headLink()->appendStylesheet('/css/default/home.css');
	$this->view->headScript()->appendFile('/jscript/default/home.js','text/javascript');
	$this->CategoryMapper=new Default_Model_HomeMapper();
	//echo $userName->userId;
        //checkSignupStepsCompleted($userName->userId);
        /* Initialize action controller here */
		//echo "varun";exit;
		
    }

     public function homeAction()
    {
	
        	
		// action body
    }
    public function listAction()
    {
		$userName = new Zend_Session_Namespace('USER');
		$reponseNeeddata=$this->CategoryMapper->getNeedDetailByUser($userName->userId);
		/*echo "<pre>";
		print_r($reponseNeeddata);*/
		echo json_encode($reponseNeeddata);
		exit;
		// action body
		
    }
	public function detailneedAction()
    {
		$request = $this->_request->getParams();
		$reponseNeeddata=$this->CategoryMapper->getNeedDetailByUser('',$request['nid']);
		/*echo "<pre>";
		print_r($reponseNeeddata);*/
		echo json_encode($reponseNeeddata);
		exit;
		// action body
		
    }

   public function indexAction()
    {

	//$this->view->headLink()->appendStylesheet('/css/default/home.css');
	//$this->view->headScript()->appendFile('/jscript/default/home.js','text/javascript');
	//$departmentdata=$this->categoryAction();
	//$this->view->headScript()->appendScript("jQuery(function(){var department=$departmentdata;});");
	//$this->_redirect('/home/newsfeed');
		// action body
    }
	public function categoryAction()
    {
	echo $category= json_encode($this->CategoryMapper->DepWithcat());
//echo "<pre>";
	//print_r($this->CategoryMapper->DepWithcat());
	exit;
	}
	public function locationAction()
    {
	echo $location= json_encode($this->CategoryMapper->Location());
	exit;
	}
	public function saveAction()
	{
		$request = $this->_request->getParams();
		
	
		 $userName = new Zend_Session_Namespace('USER');
	$data = array(
		'n_title'      => $request['needtitle'],
		'n_category'      => $request['catid'],
		'n_cdate'      => time(),
		'n_location'      => $request['locationid'],
		'n_ownerid'      => $userName->userId
	);
		$this->CategoryMapper->saveneed($data);
		
		$userdetail=$this->CategoryMapper->getUserDetailById($userName->userId);
	
		$reponsedata= array(
		'userfullname'      => $userdetail['user_full_name'],
		'needtitle'      => $request['needtitle'],
		'needtime'      => time(),
		'userimage'      => '/images/default/newsfeed_thumb.gif',
	);
		echo json_encode($reponsedata);
	exit;
	
	}
   /*public function newsfeedAction()
    {
		//$this->_redirect('/home/newsfeed');
		// action body
    }*/
}
