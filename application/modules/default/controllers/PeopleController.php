<?php
/**
 * @author : Mukesh Bisht/Nadeem Akhtar
 * Used for people suggetion for follow action
 * @var private $publickey : public captcha key
 * @var private $privatekey : private captcha key
 * func init
 * setting the values for $publickey and $privatekey
 * Creation Date : 22-03-2011
 * Created By : Mukesh Bisht
 * // use comma separated for entering modification date
 * Modified Date :
 * Modified Date :
 * Reason :
 */
require_once APPLICATION_PATH.'/includes/DataRender.php';
require_once APPLICATION_PATH.'/includes/Utility.php';
require_once APPLICATION_PATH.'/includes/imports/curl.inc.php';

class PeopleController extends Zend_Controller_Action
{
	protected $current_user;
    public function init()
    {
		define('PAGE_LIMIT',20);
		define('PAGE_START_LIMIT',0);
		$this->current_user = (int)$_SESSION['USER']['userId'];
		if($this->current_user == 0)
			$this->_redirect('/secure/login');
		Zend_Layout::getMvcInstance()->setLayout('default');
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/default/layouts');
		 $this->view->headLink()->appendStylesheet('/css/default/header.css');
		$this->view->headScript()->appendFile('/jscript/common/jquery-1.4.2.min.js');
		$utility_js = "/jscript/common/utility.js";
		$this->view->headScript()->appendFile($utility_js);
    }
	// main action for suggestion process
   	public function indexAction()
    {
		$html = new Zend_View();
		$html->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/people/');
		$this->view->headLink()->appendStylesheet('/css/default/popups.css'); 
		$this->view->headScript()->appendFile('/jscript/default/people.js');
		$user = new Default_Model_Classes_People($this->current_user);
		$user->setPeopleList(0,PAGE_LIMIT);
		$peopleListArr = $user->__get('peopleArray');
		$user->setUserListInfo($peopleListArr);
		$getUserArr = $user->__get('userListArray');
		$html->userDetails = $getUserArr;
		$this->view->showMoreFlag = count($getUserArr) ==  PAGE_LIMIT ? 1 : 0;
		$this->view->lastCount =  count($getUserArr);
		$this->view->bodyText = $html->render('people_list.phtml');
		$user->getDepartmentList();
		$deptListArr = $user->__get('deptListArray');
		$this->view->deptArr = $deptListArr;
		$user->getCityList();
		$cityListArr = $user->__get('cityListArray');
		$this->view->cityArr = $cityListArr;
		$user->setUserInfo();
		$this->view->activeUsers = $user->__get("userArray");
		$user->getDeptName('subscribed');
		$deptName = $user->__get('getDeptNameArray');
		$this->view->departmentName = $deptName;
		$user->getCityName();
		$cityName = $user->__get('getCityNameArray');
		$this->view->getCityName = $cityName;
    }
	  // Search People action
	public function searchAction()
	{
		$html = new Zend_View();
		$html->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/people/');
		$this->view->headScript()->appendFile('/jscript/default/search.js');
		$searchUser = new Default_Model_Classes_People($this->current_user);
		$searchUser->setSearchPeople($_POST['searchBox'],0,PAGE_LIMIT);
		$getSearchResult = $searchUser->__get('searchArray');
		$searchUser->setUserListInfo($getSearchResult);
		$searchUserArr = $searchUser->__get('userListArray');
		$html->searchResult = $searchUserArr;
		$this->view->showMoreFlag = count($searchUserArr) ==  PAGE_LIMIT ? 1 : 0;
		$this->view->lastCount =  count($searchUserArr);
		$html->current_user = $this->current_user;
		$this->view->bodyText = $html->render('search_result.phtml');
		$searchUser->getDepartmentList();
		$deptListArr = $searchUser->__get('deptListArray');
		$this->view->deptArr = $deptListArr;
		$searchUser->getCityList();
		$cityListArr = $searchUser->__get('cityListArray');
		$this->view->cityArr = $cityListArr;
	}
	// follow,unfollow,remove People action
	public function followAction(){
		$db = Zend_Db_Table::getDefaultAdapter();
		// follow People condition
		if($_POST['part']=='followUser'){
			$count = DataRender::getRecordCount(TABLE_FOLLOWERS,"user_id='".$_POST['user_id']."' and followers = '$this->current_user'");
			if($count == 0)
			{
				$sql = "INSERT INTO ".TABLE_FOLLOWERS." (`user_id` , `followers` ,`follow_date`) VALUES ('".$_POST['user_id']."', '$this->current_user', UNIX_TIMESTAMP())";
				$db->query($sql);
			}
			exit;
		}	
		// remove People from suggestion list condition
		if($_POST['part']=='removeUser'){
			$count = DataRender::getRecordCount(TABLE_SUGGESTIONS,"suggested_id='".$_POST['user_id']."' and user_id = '$this->current_user'");
			if($count == 0)
			{
				$sql = "INSERT INTO ".TABLE_SUGGESTIONS." (user_id , suggested_id ,suggestion_date,suggestion_status) VALUES ('$this->current_user','".$_POST['user_id']."', UNIX_TIMESTAMP(),'1')";
				$db->query($sql);
			}
			exit;
		}
		if($_POST['part']=='showUser'){
			$user = new Default_Model_Classes_People($this->current_user);
			$user->userfullDetail($_POST['user_id']);
			$suggestedArr = call_user_func_array('array_merge',$user->__get('userfullDetailArr'));
			$new_arr = array();
			if(isset($_SESSION['recent_users']))
				$new_arr = $_SESSION['recent_users'];
			array_push($new_arr,$_POST['user_id']);
			$_SESSION['recent_users'] = $new_arr;
			
			echo json_encode($suggestedArr);
			exit;
		}
		// unfollow following People condition
		if($_POST['part']=='unfollowUser'){
			$user = new Default_Model_Classes_People($_POST['user_id']);
			$user->userUnfollow();
			$followingsCount = DataRender::getRecordCount(TABLE_FOLLOWERS,"followers=$this->current_user");
			$userFollowersCount = DataRender::getRecordCount(TABLE_FOLLOWERS,"user_id=".$_POST['user_id']);
			echo Zend_Json::encode(array('followingsCount'=>$followingsCount,'userFollowersCount'=>$userFollowersCount));
			exit;
		}
	}
	// send invitaion Action
	public function sendinvitationAction()
	{
		extract($_POST);
		if($action == "invitation")
		{
			$user = new Default_Model_Classes_People($this->current_user);
			$email_arr = array_unique(explode(",",$emails));
			$user->sendInvitation($email_arr,$text);
			exit;
		}
	}
	// Ajax Action for delete invited,send reminder actions
	public function ajaxAction()
	{
		$user = new Default_Model_Classes_People($this->current_user);
		extract($_POST);
		switch($action)
		{
			case 'delete_invited':	$sql = "delete from ".TABLE_IMPORT_CONTACTS." where user_id = '$this->current_user' and email_address in (".stripslashes($emails).")";
									try
									{
										Zend_Db_Table::getDefaultAdapter()->query($sql);
									}catch(Exception $e){die('error');}
									$user->getImportedContacts();
									$imported_array = $user->__get("importedContactsArray");
									$emails_array = Zend_Json::encode($imported_array['sent_emails']);
									echo $emails_array;exit;
									break;
			case 'delete_not_invited':$sql = "delete from ".TABLE_IMPORT_CONTACTS." where user_id = '$this->current_user' and email_address in (".stripslashes($emails).")";
									try
									{
										Zend_Db_Table::getDefaultAdapter()->query($sql);
									}catch(Exception $e){die('error');}
									$user->getImportedContacts();
									$imported_array = $user->__get("importedContactsArray");
									$emails_array = Zend_Json::encode($imported_array['imported_emails']);
									echo $emails_array;exit;
									break;
			case 'send_remainder':	$e_arr = explode(",",stripslashes($emails));
									$user->sendRemainder($e_arr,"");
									$remainder_sent_array = Zend_Json::encode($user->__get("sentRemainderArray"));
									echo $remainder_sent_array;exit;
									break;
		}
	}
	// show more Action
	public function showmoreAction()
	{
		$user = new Default_Model_Classes_People($this->current_user);
		$last_count = $_POST['last_count'];
		$dept = $_POST['dept'];
		$location = $_POST['location'];
		$user->setBrowseList($dept,$location,$last_count,PAGE_LIMIT);
		$data_arr = $user->__get('browseArray');
		$user->setUserListInfo($data_arr);
		$getUserArr = $user->__get('userListArray');
		$html = new Zend_View();
		$html->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/people/');
		$html->userDetails = $getUserArr;
		$html->showMoreFlag = count($getUserArr) ==  PAGE_LIMIT ? 1 : 0;
		$html->lastCount = $last_count + count($getUserArr);
		$bodyText = $html->render('people_list.phtml');
		echo $bodyText;
		exit;
	}
	// people show by dept and city Action
	public function deptcityAction(){
		$user = new Default_Model_Classes_People($this->current_user);
		$html = new Zend_View();
		$html->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/people/');
		$html->userDetails = $getUserArr;
		$user->setBrowseList($_POST['dept_id'],$_POST['locationId'],0,PAGE_LIMIT);
		$getRecord = $user->__get('browseArray');
		$user->setUserListInfo($getRecord);
		$getUserArr = $user->__get('userListArray');
		$html->userDetails = $getUserArr;
		$html->showMoreFlag = count($getUserArr) ==  PAGE_LIMIT ? 1 : 0;
		$html->lastCount = $last_count + count($getUserArr);
		$bodyText = $html->render('people_list.phtml');
		echo $bodyText;
		exit;
	}
	// preview people Action
	public function previewAction()
	{
		$user = new Default_Model_Classes_People($this->current_user);
		Zend_Layout::getMvcInstance()->disableLayout();
		$this->view->user_name = DataRender::getFieldsVal("user","user_full_name","id=$this->current_user");
		$this->render("preview");
	}
	// import people Action
	public function importAction()
	{
		$user = new Default_Model_Classes_People($this->current_user);
		$this->view->headScript()->appendFile('/jscript/default/import.js');
		$html = new Zend_View();
		$html->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/people/');
		$css_file1 = "/css/default/popups.css";
		$css_file2 = "/css/default/sendinvites.css";
		$this->view->headLink()->appendStylesheet($css_file1); 
		$this->view->headLink()->appendStylesheet($css_file2); 
		$user->setUserInfo();
		$this->view->user = $user->__get("userArray");
		$html->user = $user->__get("userArray");
		$this->view->inviteHtml = $html->render("invite.phtml");
		if(isset($_POST['email']))
		{
			$this->view->mode = 1;
			$this->view->email = $_POST['email'];
			$this->view->pass = $_POST['pass'];
		}
	}
	// import emails Action
	public function importemailsAction()
	{
		$user = new Default_Model_Classes_People($this->current_user);
		$user->importContacts($_POST['email'],$_POST['pass']);
		$imported_contacts = $user->__get("contactsArray");
		if($imported_contacts == "invalid" || count($imported_contacts) == 0 )
		{
			echo "invalid";
			exit;
		}
		$user->existedContacts();
		$user_arr = $user->__get("existedUserArray");
		if(count($user_arr)>0)
			$user->setUserListInfo(Utility::getArray($user_arr));
		$userListArray = $user->__get("userListArray");
		$html = new Zend_View();
		$html->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/people/');
		$html->imported_contacts = $imported_contacts;
		$html->userDetails = $userListArray;
		$html->peopleListHtml = $html->render('people_list.phtml');
		$bodyText = $html->render('sendinvites.phtml');
		echo $bodyText;
		exit;
	}
	// check existed people Action
	public function existedpeopleAction()
	{
		$user = new Default_Model_Classes_People($this->current_user);
		$html = new Zend_View();
		$html->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/people/');
		$user->existedContacts();
		$user_arr = $user->__get("existedUserArray");
		$html->existedUserArray  = $user_arr;
		if(count($user_arr)>0)
			$user->setUserListInfo(Utility::getArray($user_arr));
		$userListArray = $user->__get("userListArray");
		$html->userExistedList = $userListArray;
		$bodyText = $html->render('follow.phtml');
		echo $bodyText;
		exit;
	}
	// manage people Action
	public function manageAction()
	{
		$user = new Default_Model_Classes_People($this->current_user);
		$this->view->headScript()->appendFile('/jscript/default/manage.js');
		$html = new Zend_View();
		$html->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/people/');
		$user->getImportedContacts();
		$this->view->imported_array = $user->__get("importedContactsArray");
		$user->setUserInfo();
		$emails_array = Zend_Json::encode($this->view->imported_array['sent_emails']);
		$this->view->headScript()->appendScript("var sort_array = $emails_array;");
		$this->view->user = $user->__get("userArray");
		$html->user = $user->__get("userArray");
		$this->view->inviteHtml = $html->render("invite.phtml");
	}
	// search people by dept,city,name Action
	public function searchresultAction(){
		$user = new Default_Model_Classes_People($this->current_user);
		$html = new Zend_View();
		$html->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/people/');
		$user->setSearchList($_POST['nameText'],$_POST['dept_id'],$_POST['locationId'],0,PAGE_LIMIT);
		$getSearchRecord = $user->__get('searchListArray');
		$user->setUserListInfo($getSearchRecord);
		$getUserArr = $user->__get('userListArray');
		$html->searchResult = $getUserArr;
		$this->view->showMoreFlag = count($getUserArr) ==  PAGE_LIMIT ? 1 : 0;
		$this->view->lastCount = $last_count + count($getUserArr);
		$html->current_user = $this->current_user;
		$bodyText = $html->render('search_result.phtml');
		echo $bodyText;
		exit;
	}
	// show more people Action on search
	public function searchshowmoreAction(){
		$user = new Default_Model_Classes_People($this->current_user);
		$html = new Zend_View();
		$html->setScriptPath(APPLICATION_PATH . '/modules/default/views/scripts/people/');
		$user->setSearchList($_POST['nameText'],$_POST['dept_id'],$_POST['locationId'],$_POST['last_count'],PAGE_LIMIT);
		$getSearchRecord = $user->__get('searchListArray');
		$user->setUserListInfo($getSearchRecord);
		$getUserArr = $user->__get('userListArray');
		$html->searchResult = $getUserArr;
		$html->showMoreFlag = count($getUserArr) ==  PAGE_LIMIT ? 1 : 0;
		$html->lastCount = $_POST['last_count'] + count($getUserArr);
		$html->current_user = $this->current_user;
		$bodyText = $html->render('search_result.phtml');
		echo $bodyText;
		exit;

	}
}