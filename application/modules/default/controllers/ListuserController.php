<?php 
		class ListuserController extends Zend_Controller_Action
	{
		public function indexAction()
		{
			$per_page = 10; //rows per page
			$params=array('host'=>'localhost','username'=>'root','password'=>'','dbname'=>'api');
	  		$DB = new Zend_Db_Adapter_Pdo_Mysql($params);	
     	 	$DB->setFetchMode(Zend_Db::FETCH_OBJ);  
			$sql = "SELECT * FROM user u inner join username un on u.id=un.id";  
   			$result = $DB->fetchAssoc($sql);  
   			$this->view->assign('title','Member List');  
    		$this->view->assign('description','Below, our members:');  
    		$this->view->assign('datas',$result);   
			$count = mysql_num_rows($result);
			$pages = ceil($count/$per_page)		
      	}
		
?>