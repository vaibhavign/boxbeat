<?php
interface Suggestion
{
	public function removeSuggestion($user_id);
	public function setPeopleList($start,$end);
}
interface Contacts
{
	public function importContacts($email,$password);
	public function sendInvitation($email_array,$text);
	public function sendRemainder($email_array,$text);
	public function unsubscribe($email);
	public function existedContacts();
	public function getImportedContacts();
}
class Default_Model_Classes_People extends Default_Model_Classes_UserProfile implements Suggestion,Contacts
{
	protected $contactsArray;
	protected $existedContactsArray;
	protected $importedContactsArray;
	protected $sentRemainderArray;
	protected $peopleArray;
	protected $browseArray;
	protected $deptListArray;
	protected $cityListArray;
	protected $searchListArray;
	public function __construct($user_id)
	{
		parent::__construct($user_id);
		$this->peopleArray = array();
		$this->existedContactsArray = array();
		$this->sentRemainderArray = array();
		$this->browseArray = array();
		$this->deptListArray = array();
		$this->cityListArray = array();
		$this->searchListArray = array();

	}
	public function __get($request)
	{
		return $this->$request;
	}
	
	public function importContacts($email,$password)
	{
		$mail_server = Utility::get_string_between($email,"@",".");
		$arr = explode("@",$email);
		$email = $arr[0];
		$server_mode = 0;
		switch($mail_server)
		{
			case "rediffmail":
								require_once APPLICATION_PATH.'/includes/imports/rediffmail.inc.php';
								$contacts=getContactList_rediff($email,$password);
								$server_mode = 2;
								break;
			case "gmail":		require_once APPLICATION_PATH.'/includes/imports/gmail.inc.php';
								$contacts=get_contacts($email, $password);
								$server_mode = 0;
								break;
			case "yahoo":		require_once APPLICATION_PATH.'/includes/imports/yahoo.inc.php';
								$contacts = yahoologin($email,$password);
								$server_mode = 1;
								break;
			case "hotmail":		require_once APPLICATION_PATH.'/includes/imports/hotmail.inc.php';
								$contacts=hotmail_login($email."@hotmail.com", $password);
								$server_mode = 2;
								break;
			default:			$contacts = "invalid";
								break;
		}
		if(is_array($contacts) && count($contacts) > 0)
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			foreach($contacts as $contact)
			{
				 $where = "email_address = '".trim(strtolower($contact['email']))."' and user_id = $this->user_id";
				$count = DataRender::getRecordCount(TABLE_IMPORT_CONTACTS,$where);
				if($count == 0)
				{
					$data = array('user_id'=>$this->user_id,'email_address'=>trim(strtolower($contact['email'])),'status'=>0,'import_type'=>$server_mode);
					$db->insert(TABLE_IMPORT_CONTACTS,$data);
				}
			}
		}
		$this->contactsArray = $contacts;
	}
	public function sendInvitation($email_array,$text)
	{
		$html = new Zend_View();
		$html->setScriptPath(APPLICATION_PATH . '/modules/default/views/emails/');
		$this->setUserInfo();
		$html->assign('user_name', $this->userArray['user_full_name']);
		$mail = new Zend_Mail('utf-8');
		$bodyText = $html->render('template.phtml');
		$mail->setSubject($this->userArray['user_name'].' wants to keep up with you on Go o2o');
		$mail->setFrom('support@goo2o.com','GOO2O');
		$mail->setBodyHtml($bodyText);
		$email_str = '';
		foreach($email_array as $email)
		{
			$mail->addTo($email);
			if($email_str == '')
				$email_str =  "'".$email."'";
			else
				$email_str .= ",'".$email."'";
		}
		$mail->send();
		$sql = "update ".TABLE_IMPORT_CONTACTS." set status = '1' where user_id = '$this->user_id' and email_address in ($email_str)";
		Zend_Db_Table::getDefaultAdapter()->query($sql);
		echo $bodyText;
		exit;
	}
	public function sendRemainder($email_array,$text)
	{
		//
		$db = Zend_Db_Table::getDefaultAdapter();
		$html = new Zend_View();
		$html->setScriptPath(APPLICATION_PATH . '/modules/default/views/emails/');
		$this->setUserInfo();
		$html->assign('user_name', $this->userArray['user_full_name']);
		$bodyText = $html->render('template.phtml');
		$remainder_sent_email_arr = array();

		foreach($email_array as $email_address)
		{
			$conditions = "((status='3' and DATEDIFF(CURDATE(),FROM_UNIXTIME(action_date)) >4) or (status='4' and DATEDIFF(CURDATE(),FROM_UNIXTIME(action_date)) >7)) and user_id = '$this->user_id' and email_address = '$email_address'";
			$count = DataRender::getRecordCount(TABLE_IMPORT_CONTACTS,$conditions);
			if($count > 0)
			{
				$status = DataRender::getFieldsVal(TABLE_IMPORT_CONTACTS,"status","user_id=$this->user_id and email_address = '$email_address'");
				$new_status = $status+1;
				$sql = "update ".TABLE_IMPORT_CONTACTS." set status = '$new_status',action_date=UNIX_TIMESTAMP(CURRENT_TIMESTAMP) where user_id = '$this->user_id' and email_address = '$email_address'";
				$mail = new Zend_Mail('utf-8');
				$mail->addTo($email_address);
				$mail->setSubject($this->userArray['user_name'].' wants to keep up with you on Go o2o');
				$mail->setFrom('support@goo2o.com','GOO2O');
				$mail->setBodyHtml($bodyText);
				$action_date = 
				$remainder_sent_email_arr[] = array('email_address'=>$email_address,'action_date'=>date('d/m/Y'));
				//echo $bodyText;exit;
				$mail->send();
			}
		}
		$this->sentRemainderArray = $remainder_sent_email_arr;
	}
	public function unsubscribe($email)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->delete(TABLE_IMPORT_CONTACTS,"user_id = $this->user_id and email_address = $email");
	}
	public function removeSuggestion($user_id)
	{
		//
		$db = Zend_Db_Table::getDefaultAdapter();
		$count = DataRender::getRecordCount(TABLE_SUGGESTIONS,"user_id = $this->user_id and suggested_id = $user_id");
		if($count == 0)
			$sql = "insert into ".TABLE_SUGGESTIONS."(user_id,suggested_id,suggestion_date) values('".$this->user_id."','".$user_id."',UNIX_TIMESTAMP(CURRENT_TIMESTAMP))";
		else
			$sql = "update ".TABLE_SUGGESTIONS." set user_id=".$this->user_id.",suggested_id=".$user_id.",suggestion_date = UNIX_TIMESTAMP(CURRENT_TIMESTAMP), suggestion_status = suggestion_status+1";
		$db->query($sql);
	}
	public function existedContacts()
	{
		error_reporting(0);
		$contacts = $this->contactsArray;
		$email_arr = array();
		foreach($contacts as $contact)
			$email_arr[] = "'".$contact['email']."'";
		$contacts_str = implode(",",$email_arr);
		$sql = "select id from ".TABLE_USER." where user_email_address in(".$contacts_str.") and id <> $this->user_id and id not in (select user_id from ".TABLE_FOLLOWERS." where followers = '".$this->user_id."')";
		$this->existedUserArray = DataRender::getQuery($sql);
		//exit;
	}
	public function getImportedContacts()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$status ="if(status='0','Not yet invited',if(status='6','Joined',if(status='3' and DATEDIFF(CURDATE(),FROM_UNIXTIME(action_date)) >4,
'Not yet joined',if(status='4' and DATEDIFF(CURDATE(),FROM_UNIXTIME(action_date)) >7,'Not yet joined','Remainder sent recently')))) as 
state";
		$emails_rs = $db->select()->from(TABLE_IMPORT_CONTACTS,array("email_address","action_date"))
									  ->where("user_id='?' and status = '0'",$this->user_id)
									  ->order('email_address');
		
		$imported_emails_arr = $db->query($emails_rs)->fetchAll();
		$sent_emails_rs = $db->select()->from(TABLE_IMPORT_CONTACTS,array("email_address","status","$status","action_date","DATE_FORMAT(FROM_UNIXTIME(action_date),'%d/%m/%Y') as invitation_date"))
									  ->where("user_id='?' and status <> '0'",$this->user_id)
									  ->order('email_address');
		$sent_emails_arr = $db->query($sent_emails_rs)->fetchAll();
		$this->importedContactsArray = array('imported_emails'=>$imported_emails_arr,'sent_emails'=>$sent_emails_arr);
	}
	public function userUnfollow(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = $db->quoteInto('user_id = ?', $this->user_id);
		$db->delete(TABLE_FOLLOWERS, $where);
	}
	function setPeopleList($start,$end)
	{
		$frontendOptions = array(
		   'lifetime' => 7200, // cache lifetime of 2 hours
		   'automatic_serialization' => true
		);
     
		$backendOptions = array(
			'cache_dir' => './tmp/' // Directory where to put the cache files
		);
     
    // getting a Zend_Cache_Core object
		$cache = Zend_Cache::factory('Core',
						'File',
						$frontendOptions,
						$backendOptions);
		if( ($this->peopleArray = $cache->load('myresult')) === false ) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql="select u1.id from ".TABLE_USER." as u1 inner join ".TABLE_USER." as u2 inner join (select distinct ud1.user_id  as user_id from ".TABLE_USER_DEPT." as ud1 
inner join ".TABLE_USER_DEPT." as ud2 where ud1.dept_id=ud2.dept_id and ud2.user_id=".$this->user_id.") as d on u1.id=d.user_id where u1.user_location = u2.user_location
and u2.id=".$this->user_id." and u1.id !=".$this->user_id." and u1.id NOT IN (SELECT SUGGESTED_ID FROM ".TABLE_SUGGESTIONS." AS S WHERE DATEDIFF(CURDATE(),FROM_UNIXTIME(SUGGESTION_DATE)) <5 AND S.USER_ID = ".$this->user_id." OR SUGGESTION_STATUS = 2) and u1.id not in(select user_id from ".TABLE_FOLLOWERS." where followers = '".$this->user_id."') limit $start,$end";
		$preference_result = Utility::getArray($db->query($sql)->fetchAll());
		if(count($preference_result) > 0)
		{
			$sql="SELECT DISTINCT T1.F1 FROM (SELECT B.FOLLOWERS AS F1 FROM ".TABLE_FOLLOWERS." AS A INNER JOIN ".TABLE_FOLLOWERS." AS B ON A.FOLLOWERS = B.USER_ID AND A.USER_ID = ".$this->user_id.")AS T1 LEFT JOIN	(SELECT C.FOLLOWERS AS F2 FROM ".TABLE_FOLLOWERS." AS C WHERE C.USER_ID = ".$this->user_id.") AS T2 ON T1.F1 = T2.F2 WHERE T2.F2 IS NULL AND T1.F1<>".$this->user_id." and T1.F1 NOT IN (SELECT SUGGESTED_ID FROM ".TABLE_SUGGESTIONS." AS S WHERE DATEDIFF(CURDATE(),FROM_UNIXTIME(SUGGESTION_DATE)) <5 AND S.USER_ID = ".$this->user_id." OR SUGGESTION_STATUS = 2)";
			$foaf_result = Utility::getArray($db->query($sql)->fetchAll());
			$arr1 = array();
			$arr1 = array_intersect($preference_result,$foaf_result);
			$arr2 = array_diff($preference_result,$arr1);
			$str = implode(",",$arr2);
			$select = $db->select()->from(TABLE_USER_WEIGHT,array('user_id'))
									->group('user_id')
									->having("user_id in ($str)")
									->order('sum(weight) DESC');
			$ordered_preference_result = $db->query($select)->fetchAll();
			$final_array = array_merge($arr1,$ordered_preference_result);
			$this->peopleArray = Utility::getArray($final_array);
		}	
		
			$cache->save($this->peopleArray, 'myresult');
		}
	
	}
	public function setBrowseList($dept,$location,$start,$end)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		switch($dept)
		{
			case 'all': 
						$sql = "select u1.id from ".TABLE_USER." as u1 inner join ".TABLE_USER." as u2  where u1.user_location = $location and u2.id=$this->user_id and u1.id !=$this->user_id and u1.id NOT IN (SELECT SUGGESTED_ID FROM ".TABLE_SUGGESTIONS." AS S WHERE DATEDIFF(CURDATE(),FROM_UNIXTIME(SUGGESTION_DATE)) <5 AND S.USER_ID = $this->user_id OR SUGGESTION_STATUS = 2) and u1.id not in(select user_id from ".TABLE_FOLLOWERS." where followers = '$this->user_id')";
						break;
			case 'subscribed':
						$sql = "select u1.id from ".TABLE_USER." as u1 inner join ".TABLE_USER." as u2 inner join (select distinct ud1.user_id  as user_id from ".TABLE_USER_DEPT." as ud1 inner join ".TABLE_USER_DEPT." as ud2 where ud1.dept_id=ud2.dept_id and ud2.user_id=".$this->user_id.") as d on u1.id=d.user_id where u1.user_location = $location
and u2.id=".$this->user_id." and u1.id !=".$this->user_id." and u1.id NOT IN (SELECT SUGGESTED_ID FROM ".TABLE_SUGGESTIONS." AS S WHERE DATEDIFF(CURDATE(),FROM_UNIXTIME(SUGGESTION_DATE)) <5 AND S.USER_ID = ".$this->user_id." OR SUGGESTION_STATUS = 2) and u1.id not in(select user_id from ".TABLE_FOLLOWERS." where followers = '".$this->user_id."')";
						break;
			default:	
						$sql = "select u1.id from ".TABLE_USER." as u1 inner join ".TABLE_USER." as u2 inner join (select distinct ud.user_id from ".TABLE_USER_DEPT." as ud where ud.dept_id = $dept) as d on u1.id=d.user_id where u1.user_location = $location and u2.id=".$this->user_id." and u1.id !=".$this->user_id." and u1.id NOT IN (SELECT SUGGESTED_ID FROM ".TABLE_SUGGESTIONS." AS S WHERE DATEDIFF(CURDATE(),FROM_UNIXTIME(SUGGESTION_DATE)) <5 AND S.USER_ID = ".$this->user_id." OR SUGGESTION_STATUS = 2) and u1.id not in(select user_id from ".TABLE_FOLLOWERS." where followers = '".$this->user_id."')";
						break;
		}
		$sql .= " limit $start,$end";
		$preference_result = Utility::getArray($db->query($sql)->fetchAll());
		if(count($preference_result) > 0)
		{
			$sql="SELECT DISTINCT T1.F1 FROM (SELECT B.FOLLOWERS AS F1 FROM ".TABLE_FOLLOWERS." AS A INNER JOIN ".TABLE_FOLLOWERS." AS B ON A.FOLLOWERS = B.USER_ID AND A.USER_ID = ".$this->user_id.")AS T1 LEFT JOIN	(SELECT C.FOLLOWERS AS F2 FROM ".TABLE_FOLLOWERS." AS C WHERE C.USER_ID = ".$this->user_id.") AS T2 ON T1.F1 = T2.F2 WHERE T2.F2 IS NULL AND T1.F1<>".$this->user_id." and T1.F1 NOT IN (SELECT SUGGESTED_ID FROM ".TABLE_SUGGESTIONS." AS S WHERE DATEDIFF(CURDATE(),FROM_UNIXTIME(SUGGESTION_DATE)) <5 AND S.USER_ID = ".$this->user_id." OR SUGGESTION_STATUS = 2)";
			$foaf_result = Utility::getArray($db->query($sql)->fetchAll());
			$arr1 = array();
			$arr1 = array_intersect($preference_result,$foaf_result);
			$arr2 = array_diff($preference_result,$arr1);
			$str = implode(",",$arr2);
			$select = $db->select()->from(TABLE_USER_WEIGHT,array('user_id'))
									->group('user_id')
									->having("user_id in ($str)")
									->order('sum(weight) DESC');
			$ordered_preference_result = $db->query($select)->fetchAll();
			$final_array = array_merge($arr1,$ordered_preference_result);
			$this->browseArray = Utility::getArray($final_array);
		}	
	}
	public function getDepartmentList(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$deptRecord = $db->select()->from(TABLE_DEPARTMENT,array('*'));
		$dept_array = DataRender::getQuery($deptRecord);
		$this->deptListArray = $dept_array;
	}
	public function getCityList(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$cityRecord = $db->select()->from(TABLE_CITIES,array('*'))
								   ->where('displaystatus = 1');
		$city_array = DataRender::getQuery($cityRecord);
		$this->cityListArray = $city_array;
	}
	public function setSearchList($text,$dept,$location,$start,$end)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql =  "SELECT distinct u.id FROM ".TABLE_USER." as u left join ".TABLE_USER_DEPT." as ud on ud.user_id  = u.id WHERE (u.user_full_name like '$text%' or
 u.user_full_name like '% $text%')";
 		if($dept > 0)
				$sql .= " and ud.dept_id = '$dept'";
		if($location > 0)
				$sql .= " and u.user_location = '$location'";
		$sql .= " limit $start,$end";
		$this->searchListArray = Utility::getArray($db->query($sql)->fetchAll());
	}
}