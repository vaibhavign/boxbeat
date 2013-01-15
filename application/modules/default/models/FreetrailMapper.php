<?php
class Default_Model_FreetrailMapper
{
	
	 public function domainAvailability(){
	 	
	 		$db = Zend_Db_Table::getDefaultAdapter();
	 		$args =  func_get_args();
	 		$domainToCheck = $args[0];
	  	$select_store = "SELECT * from mall_detail where domain='".$domainToCheck."'";
//exit;     	
     	$result_store = $db->fetchAll($select_store);
			if(sizeof($result_store)==1){
						$result = array('result'=> "fail");
				} else {
						$result = array('result'=> "success");
					}     	
	 		return $result;		
	 }	
	 
	 	 public function emailAvailability(){
	 	
	 		$db = Zend_Db_Table::getDefaultAdapter();
	 		$args =  func_get_args();
	 		$emailToCheck = $args[0];
	  	$select_email = "SELECT * from user where user_email_address='".$emailToCheck."'";
//exit;     	
     	$result_email = $db->fetchAll($select_email);
			if(sizeof($result_email)==1){
						$result = array('result'=> "fail");
				} else {
						$result = array('result'=> "success");
					}     	
	 		return $result;		
	 }
	 
	 
    function copyr($source, $dest){
    	echo "cp -r ".$source." ".$dest;
    	exec("cp -r ".$source." ".$dest);
    	exec("chmod 0777 -R ".$dest);
    
    }
                
    
}

?>
