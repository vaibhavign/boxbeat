<?php
class Default_Model_Store
{
	protected $_user_api_key;
	protected $_product_detail;
	protected $_domain_api_key;
	public function __construct($user_api_key)
    {
		 $this->_user_api_key = $user_api_key;
    }
	public function __get($request)
	{
		return $this->$request;
	}
	
	/********************************************************************/
	public function loadProductFromId($product_id,$product_url){
		$this->db = Zend_Db_Table::getDefaultAdapter();
		$query = "select p.id,product_name as title,short_description as product_desc,image_name,v1.variant_value as mrp,v2.variant_value as srp from product as p
				 left join product_image as pi on p.id = pi.product_id left join product_variation as v1 on p.id = v1.product_id left join product_variation as v2 
				 on p.id = v2.product_id where image_type='1' and v1.default_flag = '1' and 
				 v1.variant_name='MRP' and v2.default_flag = '1' and v2.variant_name='SRP' and p.id = '".$product_id."' and product_url = '".$product_url."'" ;
	
		$data = $this->db->query($query);
		
		$this->_product_detail = $data->fetch();
		
	}
	public function getApiFromDomain($domain)
	{
		$domain = preg_replace("`(http://)?(www.)?(.*?)`is", "$3", $domain);
		$this->db = Zend_Db_Table::getDefaultAdapter();
		$query = "select apikey from mall_detail where mallurl like '%".$domain."' and active='1'";
		$data = $this->db->query($query);
		$mall = $data->fetch();
		$this->_domain_api_key = $mall['apikey'];
	}
	
}