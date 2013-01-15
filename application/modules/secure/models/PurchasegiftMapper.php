<?php
class Secure_Model_PurchasegiftMapper{
	protected $_user_api_key;
	protected $_gift_details_array;
	protected $_gift_duration_array;
	protected $_mall_detail_array;
	protected $_last_inserted_id;
	protected $_gift_coupon_code;
	protected $_last_receipint_id;
	
	private $db;
	public function __construct(){
		//$this->_user_api_key = $_user_api_key;
		$this->db = Zend_Db_Table::getDefaultAdapter();
	}
	public function __get($request){
		return $this->$request;
	}
	public function getGiftDetail($id,$gc_duration_id){
		$gift_detail = "SELECT gc.*,ogc.* FROM giftcert_details AS gc INNER JOIN order_gc_details AS ogc ON gc.id = ogc.main_id WHERE gc.id = '".$id."'";
		$details = $this->db->fetchRow($gift_detail);
		$this->_gift_details_array = $details;
		$gift_duration = "SELECT * FROM order_gc_create WHERE id =".$gc_duration_id;
		$duration_details = $this->db->fetchRow($gift_duration);
		$this->_gift_duration_array = $duration_details;
	}
	public function getMallDetails($id){
		$store_api_key = "SELECT store_api_key FROM order_gc_create WHERE id = '".$id."'";
		$store_api_key_detail = $this->db->fetchRow($store_api_key);
		$mall_detail_query = "SELECT * FROM mall_detail WHERE apikey ='".$store_api_key_detail['store_api_key']."'";
		$mall_details = $this->db->fetchRow($mall_detail_query);
		$this->_mall_detail_array = $mall_details;
	}
	public function addGCPurchaseData($gift_array,$code_val,$recipient_detail){
		$insert_detail = '';
		foreach($gift_array as $gift_key => $gift_value)
			$insert_detail .= $gift_key."='".$gift_value."',";
			
		$insert_gift_data = "INSERT INTO gift_certificate_purchase set ".substr($insert_detail,0,-1);
		$user_gift_added = $this->db->query($insert_gift_data);
		$gift_inserted_id = $this->db->lastInsertId();
		$this->_last_inserted_id = $gift_inserted_id;
		$insert_recipient = '';
		$counter = 0;
		foreach($recipient_detail['receipt_name'] as $receipt_key => $receipt_val){
			$md5_val = strtoupper(md5(time().$code_val.$receipt_val.$recipient_detail['receipt_email'][$receipt_key]));
			$code_char = substr($md5_val, 0, 8);
			$this->_gift_coupon_code[$counter] = $code_char;
			$check_code = "SELECT count(*) AS code_total FROM gift_certificate_recipient WHERE gift_code='".$code_char."'";
			$code_details = $this->db->fetchRow($check_code);
			$receopt_details = '';
			if($code_details['code_total'] < 1){
				$receopt_details .= "('$receipt_val','".strtolower($recipient_detail['receipt_email'][$receipt_key])."','$code_char','$gift_inserted_id','".$recipient_detail['gift_amount']."')".', ';
				$insert_receipt_val = "INSERT INTO gift_certificate_recipient (`recipient_name`,`recipient_email`,`gift_code`,`gift_purchase_id`,`gift_amount_remaining`) values ".substr($receopt_details,0,-2);
				$receopt_added = $this->db->query($insert_receipt_val);
				$receipt_inserted_id = $this->db->lastInsertId();
				$this->_last_receipint_id[$counter] = $receipt_inserted_id;
			}
			$counter++;
		}
	}
}
