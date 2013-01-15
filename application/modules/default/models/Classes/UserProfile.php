<?php
class Default_Model_Classes_UserProfile
{
	protected $user_id;
	protected $userArray;
	protected $foafArray;
	protected $searchArray;
	protected $followingArray;
	protected $userListArray;
	protected $userPreferenceArray;
	protected $userfullDetailArr;
	protected $checkfollowingArray;
	protected $getDeptNameArray;
	protected $getCityNameArray;
	function __construct($user_id)
	{
		$this->user_id = $user_id;
		$this->userArray =array();
		$this->foafArray =array();
		$this->searchArray =array();
		$this->followingArray =array();
		$this->userPreferenceArray = array();
		$this->userfullDetailArr = array();
		$this->checkfollowingArray = array();
		$this->getDeptNameArray = array();
		$this->getCityNameArray = array();
	}
	function setUserInfo()
	{
		$this->userArray = DataRender::getRecord(TABLE_USER,"*","id=$this->user_id");
	}
	function setUserListInfo($user_arr)
	{
		$user_str = implode(",",$user_arr);
		if(empty($user_str))
			$user_str = "0";
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()->from(array('u'=>TABLE_USER),array('u.id as user_id','user_full_name','user_image','user_bio',"concat(cityname,', ',state_name) as location,(select count(f.user_id) from ".TABLE_FOLLOWERS." as f where f.followers = $this->user_id and f.user_id = u.id) as followed"))
							   ->join(array('c'=>TABLE_CITIES),'u.user_location = c.id',array())
							   ->join(array('s'=>TABLE_STATE),'c.stateid = s.id',array())
							   ->where("u.id in ($user_str)")
							   ->where("u.user_account_status = '1'");
		$result = $db->query($select);
		$this->userListArray =$result->fetchAll();	
	}
	function setUserPreference($limit=0)
	{
		//get the array for the users belongs to departmetn and location
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql="select u1.id from ".TABLE_USER." as u1 inner join ".TABLE_USER." as u2 inner join (select distinct ud1.user_id  as user_id from ".TABLE_USER_DEPT." as ud1 
inner join ".TABLE_USER_DEPT." as ud2 where ud1.dept_id=ud2.dept_id and ud2.user_id=".$this->user_id.") as d on u1.id=d.user_id where u1.user_location = u2.user_location
and u2.id=".$this->user_id." and u1.id !=".$this->user_id." and u1.id NOT IN (SELECT SUGGESTED_ID FROM ".TABLE_SUGGESTIONS." AS S WHERE DATEDIFF(CURDATE(),FROM_UNIXTIME(SUGGESTION_DATE)) <5 AND S.USER_ID = ".$this->user_id." OR SUGGESTION_STATUS = 2) and u1.id not in(select user_id from ".TABLE_FOLLOWERS." where followers = '".$this->user_id."')";
		$result = Utility::getArray($db->query($sql)->fetchAll());
		
		if(count($result)>=$limit)
		{
			$result = array_rand($result,$limit);
			$this->userPreferenceArray = $result;
		}
		else
		{
			//fetch the array of users which belongs by location
			 $sql = "select u1.id from ".TABLE_USER." as u1 inner join ".TABLE_USER." as u2 where u1.user_location = u2.user_location and u2.id =".$this->user_id." and u1.id != ".$this->user_id."  and u1.id NOT IN (SELECT SUGGESTED_ID FROM ".TABLE_SUGGESTIONS." AS S WHERE DATEDIFF(CURDATE(),FROM_UNIXTIME(SUGGESTION_DATE)) <5 AND S.USER_ID = ".$this->user_id." OR SUGGESTION_STATUS = 2) and u1.id not in(select user_id from ".TABLE_FOLLOWERS." where followers = '".$this->user_id."')";

			$result1 = Utility::getArray($db->query($sql)->fetchAll());
			
			if(count($result)+count($result1) >= $limit)
			{
				$result1 = array_rand($result1,$limit-count($result));
				$this->userPreferenceArray = array_merge($result,$result1);
			}
			else
			{
				//fetch the array of users which belongs by category
				$sql = "select distinct u1.user_id from ".TABLE_USER_DEPT." as u1 inner join ".TABLE_USER_DEPT." as u2 where u1.dept_id=u2.dept_id and u2.user_id=".$this->user_id." and u1.user_id !=".$this->user_id." and u1.user_id NOT IN (SELECT SUGGESTED_ID FROM ".TABLE_SUGGESTIONS." AS S WHERE DATEDIFF(CURDATE(),FROM_UNIXTIME(SUGGESTION_DATE)) <5 AND S.USER_ID = ".$this->user_id." OR SUGGESTION_STATUS = 2) and u1.user_id not in (select user_id from ".TABLE_FOLLOWERS." where followers = '".$this->user_id."')";
		
				$result2 = Utility::getArray($db->query($sql)->fetchAll());
				if(count($result2) > ($limit - count($result) - count($result1)))
					$result2 = array_rand($result2,$limit - count($result) - count($result1));
				$this->userPreferenceArray = array_unique(array_merge($result,$result1,$result2));
				
			}
		}
		
	}
	function setFOAF($limit=0)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$result=array();
		$sql="SELECT DISTINCT T1.F1 FROM (SELECT B.FOLLOWERS AS F1 FROM ".TABLE_FOLLOWERS." AS A INNER JOIN ".TABLE_FOLLOWERS." AS B ON A.FOLLOWERS = B.USER_ID AND A.USER_ID = ".$this->user_id.")
AS T1 LEFT JOIN	(SELECT C.FOLLOWERS AS F2 FROM ".TABLE_FOLLOWERS." AS C WHERE C.USER_ID = ".$this->user_id.") AS T2 ON T1.F1 = T2.F2 WHERE T2.F2 IS NULL AND T1.F1<>".$this->user_id." and T1.F1 NOT IN (SELECT SUGGESTED_ID FROM ".TABLE_SUGGESTIONS." AS S WHERE DATEDIFF(CURDATE(),FROM_UNIXTIME(SUGGESTION_DATE)) <5 AND S.USER_ID = ".$this->user_id." OR SUGGESTION_STATUS = 2)";



		$result = Utility::getArray($db->query($sql)->fetchAll());
		if(count($result)>$limit)
			$this->foafArray = array_rand($result[0],$limit);
		else
			$this->foafArray = $result;

	}
	function follow($id)
	{
			$data = array('user_id'=>$id,'followers'=>$this->user_id);
			$user_follower = new Default_Model_Followers();
			$user_follower->setUserId($id)
						  ->setFollowers($this->user_id)
						  ->setFollowDate(time());
			$user_mapper = new Default_Model_FollowersMapper();
			$user_mapper->save($user_follower);
	}
	function setSearchPeople($text,$limitStart,$limitEnd)
	{
		//
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()->from(array(TABLE_USER),array('id'))
							 ->where("user_full_name like '$text%' or user_full_name like '% $text%'")
							 ->limit($limitEnd,$limitStart);
		$result = $db->query($select)->fetchAll();
		$this->searchArray = Utility::getArray($result);
	}
	function setFollowings()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()->from(array(TABLE_FOLLOWERS),array('user_id'))
							 ->where('followers = ?',$this->user_id);
		$result = $db->query($select);
		$this->followingArray = call_user_func_array('array_merge',$result->fetchAll());
	}
	function userfullDetail($userId){
		$main_array = array();
		$user_array = DataRender::getQuery("SELECT u.id,u.user_full_name,u.user_image,u.user_bio,concat(c.cityname,', ',s.state_name) as location,
											(SELECT count(*) from ".TABLE_FOLLOWERS." where followers = '".$this->user_id."' and user_id ='".$userId."') as followed FROM ".TABLE_USER." as u 
											INNER JOIN ".TABLE_CITIES." as c ON u.user_location = c.id 
											INNER join ".TABLE_STATE." as s ON c.stateid = s.id WHERE u.id = '".$userId."'");
		
		$followingsCount = DataRender::getRecordCount(TABLE_FOLLOWERS,"followers=$this->user_id");
		$foafCount = DataRender::getRecordCount(TABLE_FOLLOWERS,"user_id=$this->user_id");
		$main_array[] = array("user_info"=>$user_array,"followingsCount"=>$followingsCount,"foafCount"=>$foafCount);
		$this->userfullDetailArr = $main_array;
	}
	function checkFollowers($userid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$checkFollow = $db->select()->from(array(TABLE_FOLLOWERS),array('user_id'))
							 ->where('followers = ?',$userid)
							 ->where('user_id = ?',$this->user_id);
		$result = $db->query($checkFollow);
		$this->checkfollowingArray = $result->fetchAll();
	}
	function getDeptName($deptId){
		$db = Zend_Db_Table::getDefaultAdapter();
		if($deptId=='subscribed'){
			$getRecords = 'My Subscribed';
		}elseif($deptId=='all'){
			$getRecords = 'All';
		}else{
			$getRecords =  DataRender::getFieldsVal(TABLE_DEPARTMENT,"dept_name","id=$deptId");
		}
		$this->getDeptNameArray = $getRecords;
	}
	function getCityName(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$getCityRecords = $db->query("SELECT c.cityname FROM ".TABLE_CITIES." AS c INNER JOIN ".TABLE_USER." as u ON u.user_location = c.id WHERE u.id =$this->user_id")->fetchAll();
		$this->getCityNameArray = $getCityRecords;
		
	}
}