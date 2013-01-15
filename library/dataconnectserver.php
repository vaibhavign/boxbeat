<?php
include_once 'xml_lib.php';
define('HTTP_HOST_STOREFRONT','216.185.116.48');
define('HTTP_USERNAME_STOREFRONT','o2ocheck_sphinx');
define('HTTP_PASSWORD_STOREFRONT','sphinx@!0');
define('HTTP_DBNAME_STOREFRONT','o2ocheck_storefront');
class Connectdata{
    private $obj;
    function __construct(){
       	 $link = mysql_connect(HTTP_HOST_STOREFRONT, HTTP_USERNAME_STOREFRONT, HTTP_PASSWORD_STOREFRONT);
	if($link){
	    mysql_select_db(HTTP_DBNAME_STOREFRONT,$link);
	}else{//echo "not connected, check connection";exit;
	}
	$this->obj = new Xml();

	/* $link = mysql_connect("192.168.1.106", "Jagabandhu", "news@0071");
        if($link){
            mysql_select_db("storefronto2o",$link);
            mysql_query("SET NAMES 'utf8'");
            //echo "connected";exit;
        }else{
            //echo "not connected, check connection";exit;
        }*/
    }

      function BrandInsert($data, $type='') {
        header('Content-Type: text/html; charset=utf-8');
        $where = '';
         $branddata=$this->obj->array2xml(array('brand'=>$data));
        $status =$data['brand_flag']= $data['brand_flag'] == 0 ? 1 : 0;
        $deleteflag =$data['delete_status']= $data['delete_status'] == 0 ? 1 : 0;
        if ($type == 'update') {
            $where = ' where brandid=' . $data[brand_id];
        } else {
            $type = 'insert into ';
        }
       $value= $insertintobrand = mysql_query($type . " brand set apikey='" . $data[api_key] . "',brandid=$data[brand_id],brandname='" . addslashes(stripslashes(trim($data[brand_name]))) . "',branddata='" . addslashes(stripslashes(trim($branddata))) . "',brandurl='" . addslashes(stripslashes(trim($data[brand_url]))) . "',brandtitle='" . addslashes(stripslashes(trim($data[brand_page_title]))) . "',brandkeyword='" . addslashes(stripslashes(trim($data[brand_page_keyword]))) . "',branddescription='" . addslashes(stripslashes(trim($data[brand_page_description]))) . "',brandimagetitle='" . addslashes(stripslashes(trim($data[brand_title]))) . "',brandimagelocation='" . $data[image_location] . "',brandimagename='".$data['brand_image']."', brandstatus='" . $status . "',deleteflag='" . $deleteflag . "'$where");
          
    }

    function CategoryManage($data, $type = 'insert',$stat='') {
        header('Content-Type: text/html; charset=utf-8');
        $deleteflag =$data['status']= ($data['status'] == '0') ? 1 : 0;
        $status = $data['cat_flag']=($data['cat_flag'] == '0') ? 1 : 0;
        $XMLfile = $this->obj->array2xml(array('category'=>$data));
        $where = '';
        if ($type == "insert") {
            $sql = "insert into category set ";
        } else if ($type == "update") {
            $sql = "update category set ";
            $where = " where catid=" . $data['cat_id'];
        }
	if($stat=='deleteproduct'){
            mysql_query("update product set productstatus='1' where catid=".$data['cat_id']);
        }
       $value=mysql_query($sql . "apikey='" . $data['apikey'] . "', catid=" . $data['cat_id'] . ", parentid=" . $data['parent_id'] . ", catname='" . addslashes(stripslashes(trim($data['cat_name']))) . "', catdata='" . addslashes(stripslashes(trim($XMLfile))) . "', caturl='" . addslashes(stripslashes(trim($data['cat_url']))) . "', cattitle='" . addslashes(stripslashes(trim($data['cat_page_title']))) . "', catdescription='" . addslashes(stripslashes(trim($data['cat_page_description']))) . "', catkeyword='" . addslashes(stripslashes(trim($data['cat_page_keyword']))) . "', catimagetitle='" . addslashes(stripslashes(trim($data['image_title']))) . "', catimagelocation='" . addslashes(stripslashes(trim($data['image_location']))) . "', catstatus='" . $status . "',catimagename='".addslashes(stripslashes(trim($data['image_name'])))."', deleteflag='" . $deleteflag ."'". $where);
        
    }
    
    function ShippingManage($status, $data=array()){
        header('Content-Type: text/html; charset=utf-8');
        if ($status == "insert") {
            $XMLfile = $this->obj->array2xml(array('shipping'=>$data));
            $sql = "insert into shipping set shippingid = ".$data['shipping_id'].", apikey = '".$data['api_key']."', shippingdata = '".addslashes(stripslashes(trim($XMLfile)))."', deleteflag = '0'";
        } else if ($status == "update") {
            $XMLfile = $this->obj->array2xml(array('shipping'=>$data));
            $sql = "update shipping set apikey = '".$data['api_key']."', shippingdata = '".addslashes(stripslashes(trim($XMLfile)))."' where shippingid=" . $data['shipping_id'];
        } else if ($status == "delete") {
            $sql = "update shipping set deleteflag = '1' where shippingid=" . $data['shipping_id'];
			$p_sql = "update product as p set p.productstatus = '1' where p.shippingid = " . $data['shipping_id'];
            mysql_query($p_sql);
        }
        if($sql) mysql_query($sql);
        
    }
    
    
    function ProductInsert($data,$type='',$productid='',$status=''){
	header('Content-Type: text/html; charset=utf-8' );     	
        $where='';
	$status = $status==0 ? 1 : 0;
	if($type=='delete'){
		mysql_query("update product set deleteflag='1' where productid=".$productid);
		return;
	}else if($type=='statusupdate'){
		mysql_query("update product set productstatus='".$status."' where productid=".$productid);
		return;
	}
        $productdata=$this->obj->array2xml(array('product'=>$data));
        $status=$data['status']=$data['status']==0 ? 1 : 0;
        $deleteflag=$data['delete_flag']=$data['delete_flag']==0 ? 1 : 0;
        if($type=='update'){
            $where=' where productid='.$data['id'];            
        }else{
            $type='insert into ';
        }    	
        $insertintobrand=mysql_query($type." product set apikey='".$data[seller_id]."',productid=$data[id],productname='".addslashes(stripslashes(trim($data[product_name])))."',productdata='".addslashes(stripslashes(trim($productdata)))."',producturl='".addslashes(stripslashes(trim($data[product_url])))."',producttitle='".addslashes(stripslashes(trim($data[page_title])))."',productpagekeyword='".addslashes(stripslashes(trim($data[page_keyword])))."',productpagedescription='".addslashes(stripslashes(trim($data[page_description])))."',productshortdescription='".addslashes(stripslashes(trim($data[short_description])))."',productimagetitle='".addslashes(stripslashes(trim($data[image_title])))."',productimagelocation='".$data[image_location]."',productimagename='".$data['image_name']."',image_height='".$data['image_height']."',image_width='".$data['image_width']."',rating='".$data['product_rating']."',catid='".$data['category_id']."',brandid='".$data['brand_id']."',shippingid='".$data['shippingid']."',returnpolicyid='".$data['returnpolicyid']."',productmrp='".$data['mrp']."',productsrp='".$data['srp']."',createdate='".$data[create_date]."',modifydate='".$data[modified_date]."',productquantity='".$data['stock']."', productstatus='".$status."',deleteflag='".$deleteflag."'$where");
        
    }

    function returnPolicyManage($policy_id = '', $type = '', $api, $data = array()) {
        header('Content-Type: text/html; charset=utf-8');
        $XMLfile = $this->obj->array2xml(array('returnpolicy'=>$data));
        if ($policy_id) {
            $sql = "update returnpolicy set  apikey = '".$api."', policydata = '".addslashes(stripslashes(trim($XMLfile)))."', policytype = '".$type."' where returnpolicyid = ".$policy_id;
        } else {
            $sql = "insert into returnpolicy set returnpolicyid = ".$data['return_policy']['policy_id'].", apikey = '".$api."', policydata = '".addslashes(stripslashes(trim($XMLfile)))."', policytype = '".$type."'";
        }
        if($sql) mysql_query($sql);
        
    }
	function formdataupdate($data,$type){		
		mysql_query(" INSERT INTO form_assign set form_id=".$data['form_id'].",type_form=".$data['type_form'].",type_value=".$data['type_value']);
	}
	function formdataupdatedata($formid){		
		mysql_query("update form_assign set deleted_flag='1' where form_id =".$formid);
	}
	function formdetailupdate($data){				
		mysql_query(" INSERT INTO form_detail set apikey='".$data['apikey']."',form_url='".$data['form_url']."',form_name='".$data['form_name']."',create_date='".$data['create_date']."',form_value='".addslashes(stripslashes(trim($data['form_value'])))."',status='".$data['status']."'");
	}
	function formdetailupdatedelete($formid){		
		mysql_query("update form_detail set delete_flag='1' where form_id =".$formid);
	}
	function statusupdate($formid,$status){		
		mysql_query("update form_detail set status='".$status."' where form_id =".$formid);
	}
	
	/**
     * Created By : Mrunal
     * Creation Date : 23-02-2012
     * Reason : connect to store front database. And access the table of store front database
     * Return : ZendDb object.
     * */
    public static function storeFrontDb() {
        $db = Zend_Db::factory('Pdo_Mysql', array(
                    'host' => HTTP_HOST_STOREFRONT,
                    'username' => HTTP_USERNAME_STOREFRONT,
                    'password' => HTTP_PASSWORD_STOREFRONT,
                    'dbname' => HTTP_DBNAME_STOREFRONT
                ));
        return $db;
    }
}
?>