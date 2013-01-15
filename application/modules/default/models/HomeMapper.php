<?php
class Default_Model_HomeMapper  
{
 	/*public function DepWithcat()
      {
              $db = Zend_Db_Table::getDefaultAdapter();
			
			 $masterquery =$db->query("Select DISTINCT d.id,d.dept_name From categories inner join categories_description on categories.categories_id = categories_description.categories_id inner join department d on categories.department_id= d.id where categories.parent_id=0");
              $resultSet = $masterquery->fetchAll(); 
      
              foreach ($resultSet as $key => $result){
                               $catetorySql = $db->query('select * from categories INNER JOIN  categories_description on categories.categories_id = categories_description.categories_id where department_id='.$result['id']);
                               $resultSet[$key][categories] = $catetorySql->fetchAll(); 
                       }

               return $resultSet;
 }*/
 
 public function DepWithcat()
      {
	      $db = Zend_Db_Table::getDefaultAdapter();
		  $catetorySql = $db->query('SELECT DISTINCT d.id as did, d.dept_name as dname, c.categories_id as cid, cd.categories_name as catname FROM department d LEFT JOIN (categories c INNER JOIN categories_description cd ON cd.categories_id = c.categories_id) ON d.id = c.department_id');
          $resultSet = $catetorySql->fetchAll(); 
          foreach ($resultSet as $key => $result){
			  $r[$result['did']][$result['cid']] = $result['catname']; 
    	      $r[$result['did']]['name'] = $result['dname']; 
          }
         return $r;
 }
   public function Location()
      {
             $db = Zend_Db_Table::getDefaultAdapter();
			 $masterquery =$db->query("SELECT * FROM `cities` where displaystatus = 1 order by cityname");
             $resultSet = $masterquery->fetchAll(); 
             return $resultSet;
      }
	  
	public function saveneed($data)
      {
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->insert('need',$data);
		$lastinsertedid = $db->lastInsertId();
	
		$sqlTask = $db->query("INSERT INTO `task` (`feed_id` ,`feed_type` ,`date_added` ,`date_modified` ,`feed_ownerid` ,`feed_title` ,`feed_comment` ,`feed_city` ,`feed_category` ,`feed_reposted` ,`feed_view` ,`feed_topic` ,`topic_id` ,`feed_flag` ,`feed_status` ,`moderation_status` ,`deletedflag`)VALUES (".$lastinsertedid.", 1, ".$data['n_cdate'].", '', ".$data['n_ownerid'].", '".$data['n_title']."', '', ".$data['n_location'].", ".$data['n_category'].", '0', '0', '0', '0', '0', '0', '0', '0')");
		
      }
	public function getUserDetailById($userid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()->from('user')->where('id='.$userid);
		$result = $select->query();
		$resultSet = $result->fetchAll();	
		return $resultSet[0];
	
	}
	public function getNeedDetailByUser($userid='',$nid=''){
	    if($nid){
		$where = "need.n_id='$nid'";
		}elseif($userid){
		$where = "user.id='$userid'";
		}
		//echo 'SELECT * FROM need INNER JOIN user ON need.n_ownerid=user.id WHERE '.$where.'  order by need.n_cdate desc';exit ;
        $db = Zend_Db_Table::getDefaultAdapter();
		
		$needSql = $db->query('SELECT * FROM need INNER JOIN user ON need.n_ownerid=user.id WHERE '.$where.'  order by need.n_cdate desc');
        $resultSet = $needSql->fetchAll();
	
		foreach ($resultSet as $key => $result){
			$locname  = $this->getlocationByNeed($result['n_location']);
			$username = $this->getUserDetailById($result['n_ownerid']);
			$catname = $this->getCatDetailById($result['n_category']);
			$resultSet[$key]['username'] = $username['user_full_name'];
			$resultSet[$key]['loc_name'] = $locname['cityname']; 
			$resultSet[$key]['catname'] = $catname['categories_name']; 
			          
          }	
		  
		return $resultSet;
	
	}
	
	public function getCatDetailById($catid){
        $db = Zend_Db_Table::getDefaultAdapter();
		$catetorySql = $db->query('SELECT * FROM categories c INNER JOIN categories_description cd ON cd.categories_id = c.categories_id WHERE c.categories_id='.$catid);
          $resultSet = $catetorySql->fetchAll(); 
		return $resultSet[0];
	}
	public function getlocationByNeed($locid){
        $db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()->from('cities')->where('id='.$locid);
		$result = $select->query();
		$resultSet = $result->fetchAll();	
		return $resultSet[0];
	}

}