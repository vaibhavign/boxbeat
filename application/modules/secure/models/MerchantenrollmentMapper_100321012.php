<?php
class Secure_Model_MerchantenrollmentMapper{
	protected $_user_api_key;
	protected $_business_detail_msg;
	protected $_user_details_array;
	protected $_storename_count_value;
	protected $_storeurl_count_value;
	protected $_activationcode_count_value;
	protected $_mall_activation_value;
	protected $_mall_count_value;
	protected $_last_user_inserted_id;
	private $_apikey; // newly generate apikey
	protected $_new_api_key;
	protected $_demoapikey;
	
	private $db;
	public function __construct($_user_api_key){
		$this->_user_api_key = $_user_api_key;
		$this->db = Zend_Db_Table::getDefaultAdapter();
	}
	public function __get($request){
		return $this->$request;
	}
	public function checkUserMall(){
		$check_mall_detail = "SELECT count(*) AS mall_record , id from mall_detail WHERE assigned = '".$this->_user_api_key."' GROUP BY id";
		$total_record = $this->db->query($check_mall_detail)->fetchAll();
		$this->_mall_count_value = $total_record[0]['mall_record'];
	}
	public function userDetails(){
		$user_detail = "SELECT * FROM mall_detail WHERE assigned = '".$this->_user_api_key."'";
		$details = $this->db->query($user_detail)->fetchAll();
		$this->_user_details_array = $details;
	}
    public function getCityName($cityid=NULL) {
        if ($cityid != NULL) {
          	$city_details = $this->db->query("SELECT * FROM cities WHERE id = '".$cityid."'")->fetchAll();
            return $city_details[0]['cityname'];
        }
    }
    public function getStateName($stateid=NULL) {
        if ($stateid != NULL) {
			$state_details = $this->db->query("SELECT * FROM state WHERE id = '".$stateid."'")->fetchAll();
            return $state_details[0]['state_name'];
        }
    }
    public function checkStoreName($store_name) {
		$check_storename_query = "SELECT count(*) As total_value FROM mall_detail WHERE title LIKE '%".$store_name."%'";
		$store_count = $this->db->query($check_storename_query)->fetchAll();
        $this->_storename_count_value = $store_count[0]['total_value'];
    }
    public function checkStoreUrl($store_url) {
		$check_storeurl_query = "SELECT count(*) As total_value FROM mall_detail WHERE mallurl LIKE '%".$store_url."%'";
		$store_url_count = $this->db->query($check_storeurl_query)->fetchAll();
        $this->_storeurl_count_value = $store_url_count[0]['total_value'];
    }
    public function checkActivationCode($code) {
		$check_activationcode_query = "SELECT count(*) As check_record FROM enrollment_activation_code WHERE activation_code LIKE '%".$code."%' AND status = '1'";
		$activation_code_count = $this->db->query($check_activationcode_query)->fetchAll();
        $this->_activationcode_count_value = $activation_code_count[0]['check_record'];
    }
	public function updateActivationCode($activation_code){
		$update_code_query = "UPDATE enrollment_activation_code set status = '2', apikey = '".$this->_user_api_key."' WHERE activation_code = '".$activation_code."'";
		$code_updated = $this->db->query($update_code_query);
	}
    public function checkMallActivate() {
		$check_mall_detail = "SELECT count(*) AS mall_record from mall_detail WHERE assigned = '".$this->_user_api_key."'";
		$total_record = $this->db->query($check_mall_detail)->fetchAll();
		if(count($total_record[0]['mall_record']) > 0){
			$check_mall_activation_query = "SELECT active FROM mall_detail WHERE active = '1' AND assigned = '".$this->_user_api_key."' GROUP BY active";
			$mall_count = $this->db->query($check_mall_activation_query)->fetchAll();
       		$this->_mall_activation_value = $mall_count[0]['active'];
		}else{
			$this->_mall_activation_value = $total_record[0]['mall_record'];
		}
    }
	public function updateMallUrlStatus(){
		$update_status_query = "UPDATE mall_detail set active = '1' WHERE assigned = '".$this->_user_api_key."'";
		$status_updated = $this->db->query($update_status_query);
	}
	public function addSameUserWithNewApiKey($userdetails,$email_address,$username){
		foreach($userdetails as $key => $val)
			$insert_data .= $key."='".$val."',";
		$insert_user_data = "INSERT INTO user set ".substr($insert_data,0,-1);
		$user_added = $this->db->query($insert_user_data);
		$this->_last_user_inserted_id = $this->db->lastInsertId();
		$registrationObject = new Secure_Model_RegistrationMapper();
		$this->_apikey = $registrationObject->generateApiKey($this->_last_user_inserted_id,$email_address,$username);
	}
	public function updateSameUserWithNewApiKey($inserted_id,$usernamedetails){
		foreach($usernamedetails as $key => $val)
			$insert_data .= $key."='".$val."',";
		$insert_user_data = "INSERT INTO username set ".substr($insert_data,0,-1);
		$username_added = $this->db->query($insert_user_data);
		$last_user_inserted_id = $this->db->lastInsertId();
		mail('softmb1@gmail.com','Check Api Key',$insert_user_data.'=='.$last_user_inserted_id.'=='.$inserted_id);
	}
	public function addBusinessDetails($details,$newapikey=''){
		$insert_data = '';
		$apiKey=($newapikey!='')?$newapikey:$this->_user_api_key;
		foreach($details as $key => $val)
			$insert_data .= $key."='".$val."',";
		$insert_query = "INSERT INTO mall_detail set ".$insert_data." apikey = '".$apiKey."'";
		$details_added = $this->db->query($insert_query);
		$this->_business_detail_msg = $this->db->lastInsertId();
	}
	public function updateNewUserDetail($id,$details){
		$update_data = '';
		foreach($details as $key => $val)
			$update_data .= $key."='".$val."',";
		$update_query = "UPDATE user set ".substr($update_data,0,-1)." WHERE id = '".$id."'";
		$details_updated = $this->db->query($update_query);
	}
	public function mallOwnerRolePermission($apiKey,$email_address){
		$insert_user_role = "INSERT INTO user_role SET email_id = '".$email_address."', store_apikey = '".$apiKey."' , role = 2 , role_name = 'Admin' , status = 1";
		$user_role_entry = $this->db->query($insert_user_role);
		$user_role_id = $this->db->lastInsertId();
		$get_user_permission = "SELECT mam.* FROM module_action_mapping AS mam INNER JOIN module AS m ON mam.mod_id = m.id INNER JOIN module_action AS ma ON mam.action_id = ma.id
WHERE m.status = 1 AND m.deleted = 0 AND ma.status = 1 AND ma.deleted = 0";
		$user_permission = $this->db->query($get_user_permission)->fetchAll();
		foreach($user_permission as $permission_key => $permission_value)
			$permission_details .= "($user_role_id, $permission_value[id])".', ';
		$insert_user_permission = "INSERT INTO user_permission (`pid`,`mod_action_id`) values ".substr($permission_details,0,-2);
		$user_permission_update = $this->db->query($insert_user_permission);
	}
	public function addBussinessDetailInStoreAddress($user_address){
		$insert_store_data = '';
		//$apiKey=($newapikey!='')?$newapikey:$this->_user_api_key;
		foreach($user_address as $address_key => $address_value)
			$insert_store_data .= $address_key."='".$address_value."',";
		$insert_user_store_address = "INSERT INTO store_address set ".substr($insert_store_data,0,-1)."";
		$user_store_address_added = $this->db->query($insert_user_store_address);
	}
	public function updateDemoMallDetails($details,$id){
		$update_data = '';
		foreach($details as $key => $val)
			$update_data .= $key."='".$val."',";
		$update_query = "UPDATE mall_detail set ".substr($update_data,0,-1)." WHERE id = '".$id."'";
		$details_updated = $this->db->query($update_query);
	}
	public function getAndUpdateDemoMallDetail($apikey,$detail_array,$email_id){
		$demo_selection = "SELECT * FROM mall_detail WHERE assigned = 'NULL' ORDER BY id ASC LIMIT 0,1";
		$user_apikey = $this->db->query($demo_selection)->fetchAll();
		$this->_demoapikey = $user_apikey[0]['apikey'];
		
		$demo_updation = "UPDATE mall_detail SET assigned = '".$apikey."' WHERE assigned = 'NULL' AND apikey = '".$user_apikey[0]['apikey']."' LIMIT 1";
		$details_updated = $this->db->query($demo_updation);
		foreach($detail_array as $detailkey => $detailval)
			$update_data .= $detailkey."='".$detailval."',";
		$update_detail_query = "UPDATE mall_detail set ".substr($update_data,0,-1)." WHERE apikey = '".$user_apikey[0]['apikey']."'";
		$details_updated = $this->db->query($update_detail_query);
		$update_role_emailaddress = "UPDATE user_role set email_id = '".$email_id."' WHERE store_apikey = '".$user_apikey[0]['apikey']."'";
		$role_updated = $this->db->query($update_role_emailaddress);
	}
}
