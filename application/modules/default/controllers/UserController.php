	<?php 
		class UserController extends Zend_Controller_Action
	{
		public function indexAction()
		{
	  		$this->view->assign('action',"process");
	  		$this->view->assign('title','Member Registration');
	  		$this->view->assign('label_fname','First Name');
	  		$this->view->assign('label_lname','Last Name');  
	  		$this->view->assign('label_uname','User Name');  
	  		$this->view->assign('label_pass','Password');
	  		$this->view->assign('label_submit','Register');      
      	}
		public function processAction()
		{
			$DB = Zend_Db_Table::getDefaultAdapter();
			$request = $this->getRequest();
	        $sql="INSERT INTO user(first_name,last_name,user_name,password) VALUES ('".$request->getParam('first_name')."', '".$request->getParam('last_name')."', '".			            $request->getParam('user_name')."', MD5('".$request->getParam('password')."'))";  
			$DB->query($sql);  
			$this->view->assign('title','Registration Process');  
			$this->view->assign('description','Registration succes');       
		} 

	
		 public function listAction()  
       {    $DB = Zend_Db_Table::getDefaultAdapter();
     	 	$DB->setFetchMode(Zend_Db::FETCH_OBJ);  
			$sql = "SELECT * FROM user ORDER BY user_name ASC";  
   			$result = $DB->fetchAssoc($sql);  
   			$this->view->assign('title','Member List');  
    		$this->view->assign('description','Below, our members:');  
    		$this->view->assign('datas',$result);       
      } 
	  public function updateAction()
	  {
	  		$DB = Zend_Db_Table::getDefaultAdapter();
			$request=$this->getRequest();
			$id=$request->getParam("id");
			$sql="select * from user where id='".$id."'";
			$result=$DB->fetchRow($sql);
			$this->view->assign('data',$result);
			$this->view->assign('action',$request->getBaseURL()."/user/processedit");
			$this->view->assign('title','Edit Member Registration');
			$this->view->assign('user_id','User Id');
	  		$this->view->assign('label_fname','First Name');
	  		$this->view->assign('label_lname','Last Name');  
	  		$this->view->assign('label_uname','User Name');  
	  		$this->view->assign('label_pass','Password');
	  		$this->view->assign('label_submit','Edit');
			$this->view->assign('description','Please Update This Form Completely');
	  }
	 public function processeditAction()  

 	 {  
			$DB = Zend_Db_Table::getDefaultAdapter();
			 $request = $this->getRequest();  
			 $data = array('first_name' => $request->getParam('first_name'),'last_name' => $request->getParam('last_name'),'user_name' => $request->getParam('user_name'),'password' => md5($request->getParam('password')));  
			 $DB->update('user', $data,'id = '.$request->getParam('id'));    
			 $this->view->assign('title','Editing Process');  
			 $this->view->assign('description','Editing succes');        

	 }
	 public function delAction()
	 {
	  		$params=array('host'=>'localhost','username'=>'root','password'=>'','dbname'=>'user_records');
	  		$DB = new Zend_Db_Adapter_Pdo_Mysql($params);	
	  		$request=$this->getRequest();
	  		$DB->delete('user','id='.$request->getParam('id'));
	  		$this->view->assign('description','Deleting succes');             
      		$this->view->assign('list',$request->getBaseURL()."/user/list");    
	} 
} 
?>