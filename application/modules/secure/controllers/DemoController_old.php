<?php
class Secure_DemoController extends Zend_Controller_Action{
	protected $_user_api_key;
	protected $_store_api_key;
	protected $_user_name;
	private $objTrigger;
	
	private $db;
    public function init(){
		$_SESSION['mypage']=HTTP_SECURE . '/demo/create-demo-store';
        Zend_Layout::getMvcInstance()->setLayout('secure');
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');
		$this->view->headLink()->appendStylesheet('/css/secure/secure.css');
		$this->view->controller='demo';
		$this->_user_name = new Zend_Session_Namespace('original_login');
        $current_store = new Zend_Session_Namespace('USER');
        $this->_store_api_key=$current_store->stores['0']['store_apikey'];
		$this->_user_api_key = $_SESSION['original_login']['apikey'];
		$this->objTrigger = new Notification();
		if($this->_user_name->userId == ''){
			$this->_redirect(HTTP_SECURE . '/login');
		}
		$this->view->headScript()->appendFile('/jscript/common/jquery-1.4.2.min.js');
    }

    public function indexAction(){
		$this->_redirect(HTTP_SECURE . '/demo/create-demo-store');
	}
	public function createDemoStoreAction(){
		$this->view->headTitle('Welcome : create a  GMS demo store - goo2o.com');
		$user_type = array('1'=>'New to e-commerce','2'=>'Experienced in e commerce','3'=>'Web designer/developer','4'=>'Switching from competitor','5'=>'Other');
		$primary_indus = array('1'=>'Animals and Pets','2'=>'Apparel and Clothing','3'=>'Arts and Craft, Business','4'=>'Computer and Software','5'=>'Education, Electronics','6'=>'Food and Beverages','7'=>'Furniture, Gifts, Office and Stationary','8'=>'Jewellery and Accessories','9'=>'Home and Garden','10'=>'Health and Beauty','11'=>'Other');
		$source_area = array('1'=>'Blog Post','2'=>'Web search','3'=>'Web advertisement','4'=>'Company Referral','5'=>'Other');
		$flag = 1;
		$this->view->user_type_array = $user_type;
		$this->view->primary_indus_array = $primary_indus;
		$this->view->source_area_array = $source_area;

		$merchant_mapper  = new Secure_Model_MerchantenrollmentMapper($this->_user_api_key); // object for Business mapper class
		$merchant_mapper->checkUserMall();
		$get_count_id = $merchant_mapper->__get('_mall_count_value');
		
		if($get_count_id < 1){	
			if($_POST['action'] == 'process'){ // condition for save the data and check session data on back button
			/*if($_POST['store_name']!=''){ // condition for check store name exist
				$merchant_mapper->checkStoreName(trim($_POST['store_name']));
				$get_total_count = $merchant_mapper->__get('_storename_count_value');
				if($get_total_count > 0){
					$this->view->error_msg = 'Storename already exist.';
					$this->view->display = 'display:block;';
					$flag = 0;
				}
			} condition  end for check store name exist */
			if($_POST['domain_name']!=''){ // condition for check store url exist
				$merchant_mapper->checkStoreUrl(trim($_POST['domain_name']));
				$get_total_count = $merchant_mapper->__get('_storeurl_count_value');
				if($get_total_count > 0){
					$this->view->url_error_msg = 'This domain name is already in use. Please enter some other name.';
					$this->view->url_msg_display = 'display:block;';
					$flag = 0;
				}
			}// condition end for check store url exist
			$this->view->store_name = ($_POST['store_name'])?$_POST['store_name']:'';
			$this->view->domain_name = ($_POST['domain_name'])?$_POST['domain_name']:'';
			$this->view->contact_no = ($_POST['contact_no'])?$_POST['contact_no']:'';
			//Optional Field Value
			$this->view->business_name = ($_POST['business_name'])?$_POST['business_name']:'';
			if($_POST['user_type']=='5'){
				$user_type = $_POST['user_text_val'];
				$this->view->user_type_display = 'display:block;';
				$this->view->user_type = ($_POST['user_type'])?$_POST['user_type']:'';
				$this->view->user_text_val = ($_POST['user_text_val'])?$_POST['user_text_val']:'';	
			}else{
				$this->view->user_type_display = 'display:none;';
				$user_type = $_POST['user_type'];
				$this->view->user_type = ($_POST['user_type'])?$_POST['user_type']:'';
			}
			if($_POST['primary_industry']=='11'){
				$primary_industry = $_POST['primary_text_val'];
				$this->view->primary_industry_display = 'display:block;';
				$this->view->primary_industry = ($_POST['primary_industry'])?$_POST['primary_industry']:'';
				$this->view->primary_text_val = ($_POST['primary_text_val'])?$_POST['primary_text_val']:'';	
			}else{
				$this->view->primary_industry_display = 'display:none;';
				$primary_industry = $_POST['primary_industry'];
				$this->view->primary_industry = ($_POST['primary_industry'])?$_POST['primary_industry']:'';
			}
			if($_POST['source_area']=='5'){
				$source_area = $_POST['source_text_val'];
				$this->view->source_area_display = 'display:block;';
				$this->view->source_area = ($_POST['source_area'])?$_POST['source_area']:'';
				$this->view->source_text_val = ($_POST['source_text_val'])?$_POST['source_text_val']:'';	
			}else{
				$this->view->source_area_display = 'display:none;';
				$source_area = $_POST['source_area'];
				$this->view->source_area = ($_POST['source_area'])?$_POST['source_area']:'';
			}			
			//End Here
			//preg_match('@^(?:http://www.)?([^.*]+)@i',$users_details['mallurl'], $mallurl);
			//$this->view->store_url = ($_POST['store_url'])?$_POST['store_url']:$mallurl[1];
			
			$mall_url = 'http://www.'.strtolower(trim($this->view->domain_name)).'.mygoo2o.com';
			$user_id = $_SESSION['original_login']['userId'];
			$insert_data_array = array('business_name'=>trim($this->view->business_name),'business_type'=>$user_type,'business_address'=>'Gurgaon, Haryana','city'=>'551','state'=>'11','pincode'=>'122016','title'=>trim($this->view->store_name),'mallurl'=>$mall_url,'user_id'=>$user_id,'primary_industry'=>trim($primary_industry),'source_area'=>$source_area,'active'=>'1','parent_store'=>'0','store_owner_type'=>'1');
			if($flag !=0){
				$merchant_mapper->addBusinessDetails($insert_data_array);
				$_SESSION['DEMO_STORE_DATA'] = $insert_data_array;
				$_SESSION['USER']['userDetails'][0]['title'] = $_SESSION['DEMO_STORE_DATA']['title'];
				$_SESSION['USER']['userDetails'][0]['mallurl'] = $_SESSION['DEMO_STORE_DATA']['mallurl'];
				$_SESSION['DEMO_FLAG'] = 2;
				$_SESSION['DEMO_MAGANE_FLAG'] == 3;
				$this->_redirect(HTTP_SECURE.'/demo/manage-demo-store');
			}
		}// condition end for save the data 
		}else{
			$this->_redirect(HTTP_SECURE . '/demo/store-exists');
		}//End if condition for check user allreadu exist or not

	}
	public function manageDemoStoreAction(){

		$this->view->headTitle('Your Store is Being Created - goo2o.com');
//		$this->view->headScript()->appendFile('/jscript/common/jquery-1.4.2.min.js');
//		$this->view->headScript()->appendFile('/jscript/secure/manage-demo-store.js');
		$url = $this->_request->getParams();
		if(isset($url['redirect'])){
				echo '<div style="clear:both;"><div style="border-bottom: 1px solid #D8D8D8;clear: both;height: 75px;margin: auto;width: 1000px;"><div style="clear: both;font-size: 0;height: 18px;line-height: 18px;">&nbsp;</div><div style="float:left;"><a href="javascript:void(0)"><img title="Logo" alt="Logo" src="http://goo2ostore.com/images/secure/merchantenrollment/logo_blue.gif"></a></div><div style="color: #0083CB;float: left;font-family: Verdana,Arial,Helvetica,sans-serif;font-weight: bold;line-height: 25px;padding-left: 16px;">Goo2o merchant solution</div></div></div>';
				echo '<div style="color:#0083CB;font-size:22px; margin-top:50px; text-align:center;">Your Store is Being Created, Please Wait...</div>
<div style="color:#0083CB;font-size:14px;margin-top:10px; text-align:center;">Thanks for choosing goo2o. We\'re creating your store now.</div>
<div style="margin-top:70px;text-align:center;" align="center"><img src="http://goo2ostore.com/bar/images/goo2oloading.gif"></div><div style="color:#0083CB;font-size:22px; margin-top:50px; text-align:center;">Your page will redirect in 5 seconds. Problems with the redirect, please press "F5" or Ctrl+R .</div>';
			$structure_mapper  = new Admin_Model_StructureMapper($this->_user_api_key); // object for Business mapper class
			$structure_mapper->buildStore();
			$_SESSION['DEMO_MAGANE_FLAG'] = '';
			$this->_redirect(HTTP_SECURE.'/demo/congratulation');
			exit;
		} else {
			$db = mysql_connect("localhost","sketchee_store","var#usr");
			mysql_select_db("sketchee_o2ostore",$db);
			$default_data_apikey = '9247a0bbbb97f01b30cafe0cd0584169';
			$new_data_apikey = $this->_user_api_key;
			if (trim($default_data_apikey) == '')
				$default_data_apikey = 'fc06cf4dc58e46ceb71d02bf3de6a317';
			//if (trim($new_data_apikey) == '')
			//	$new_data_apikey = 'fa4c9a69f2e4b01fdb495566788adc1a'; 
			if (trim($new_data_apikey) == '')
				$new_data_apikey = $_SESSION['USER']['STORES']['0']['store_apikey'];
			if (trim($new_data_apikey) == '' || trim($default_data_apikey) == '') {
				echo '<span style="color:#990000;font-size:20px;">Failed to copy Sample Data<br>Security Issue - Contact Administrator</span>';
				exit;
			}
			$result = mysql_query("select id from username where apikey='$new_data_apikey'");
			$result = mysql_fetch_array($result);
			$userid = $result['0'];
			
			/* ---migrate 3 table of feature (created by : mrunal)---- */
			
			$sql_features = mysql_query("SELECT * FROM features where api_key = '".$default_data_apikey."'");
			$old_new_fid = array();
			while($features = mysql_fetch_assoc($sql_features)){
				mysql_query("INSERT INTO `features`(`feature_name`, `input_type`, `mandatory_feature`, `api_key`, `add_date`, `modify_date`, `value_delete`) VALUES ('".addslashes(stripslashes($features['feature_name']))."','".$features['input_type']."','".$features['input_type']."','".$new_data_apikey."', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(),'".$features['value_delete']."')");
				$old_new_fid[$features['product_feature_id']] = mysql_insert_id();
			}
			
			$sql_fgroup = mysql_query("SELECT * FROM `feature_group` WHERE `api_key` = '" . $default_data_apikey . "'");
			$old_new_fgid = array();
			while($fGroup = mysql_fetch_assoc($sql_fgroup)){
				mysql_query("INSERT INTO `feature_group`( `feature_group_name`, `api_key`, `add_date`, `modify_date`, `group_delete`) VALUES ('".addslashes(stripslashes($fGroup['feature_group_name']))."','".$new_data_apikey."',UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), '".$fGroup['group_delete']."')");
				$old_new_fgid[$fGroup['group_id']] = mysql_insert_id();
			}
			
			$fgv_arr = array();
			$sql_fgvalue = mysql_query("SELECT * FROM `feature_group_value` WHERE `group_id` IN (" . implode(',', array_keys($old_new_fgid)) . ")");
			while ($fg_value = mysql_fetch_assoc($sql_fgvalue)){
				$fgv_arr[] = " (" . $old_new_fgid[$fg_value['group_id']] . ",".$old_new_fid[$fg_value['pf_id']].")";
			}
			if(count($fgv_arr)>0){
				mysql_query("INSERT INTO `feature_group_value` (`group_id`, `pf_id`) VALUES ".implode(',', $fgv_arr));//error handling
			}
			/* ---migrate 4 table of variation (created by : mrunal)---- */
			
			$sql_vn = mysql_query("select * from variation_name where api_key='$default_data_apikey'");
			$old_new_vn_id = array();
			while ($vn = mysql_fetch_assoc($sql_vn)) {
				mysql_query("INSERT INTO variation_name (variation_name, variation_code, api_key, date_added, date_modified, delete_flag_variation) VALUES ('" . addslashes(stripslashes($vn['variation_name'])) . "','" . addslashes(stripslashes($vn['variation_code'])) . "','" . $new_data_apikey . "', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(),'" . $vn['delete_flag_variation'] . "')");
				$old_new_vn_id[$vn['variation_name_id']] = mysql_insert_id();
			}
			
			$sql_vv = mysql_query("select * from variation_value where variation_name_id IN ( " . implode(',', array_keys($old_new_vn_id)). ")");
			$insert_vv_value = array();
			while ($vv = mysql_fetch_assoc($sql_vv)) {
				$insert_vv_value[] = "(" . $old_new_vn_id[$vv['variation_name_id']] . ",'" . addslashes(stripslashes($vv['value'])) . "')";
			}
			if(count($insert_vv_value)>0){
				mysql_query("INSERT INTO variation_value ( variation_name_id, value) VALUES ".implode(',', $insert_vv_value));//error handling
			}
			$old_new_vg_id = array();
			$sql_vg = mysql_query("select * from variation_group where api_key='$default_data_apikey'");
			while ($vg = mysql_fetch_assoc($sql_vg)) {
				mysql_query("INSERT INTO variation_group (name, api_key, date_added, date_modified, delete_flag) VALUES ('" . addslashes(stripslashes($vg['name'])) . "','" . $new_data_apikey . "', UNIX_TIMESTAMP(), UNIX_TIMESTAMP(),'" . $vg['delete_flag'] . "')");
				$old_new_vg_id[$vg['variation_group_id']] = mysql_insert_id();
			}
			
			$sql_vgv = mysql_query("select * from variation_group_value where variation_group_id IN( " . implode(',', array_keys($old_new_vg_id)) . " )");
			while ($vgv = mysql_fetch_assoc($sql_vgv)) {
				$var_vgv_arr[] = "(".$old_new_vg_id[$vgv['variation_group_id']].",".$old_new_vn_id[$vgv['variation_name_id']].",UNIX_TIMESTAMP())";
			}
			if(count($var_vgv_arr)>0){
				mysql_query("INSERT INTO variation_group_value (`variation_group_id`, `variation_name_id`, `date_added`) VALUES " . implode(',', $var_vgv_arr));//error handling
			}
			
			$result = mysql_query("select ac.*,bd.*,p.* from addcategory as ac,brand as bd,product as p where ac.cat_id=p.category_id and bd.brand_id=p.brand_id and p.seller_id ='$default_data_apikey' AND p.`status` = '1' AND p.`delete_flag` = '1' order by p.id");
			$product_feature = mysql_query("select * from product_feature where product_id in (select id from product where seller_id='$default_data_apikey' AND `status` = '1' AND `delete_flag` = '1' order by id)");
			$product_image = mysql_query("select * from product_image where product_id in (select id from product where seller_id='$default_data_apikey' AND `status` = '1' AND `delete_flag` = '1' order by id)");
			$product_shipping_policy = mysql_query("select * from product_shipping_policy where product_id in (select id from product where seller_id='$default_data_apikey' AND `status` = '1' AND `delete_flag` = '1' order by id)");
			$product_variation = mysql_query("select * from product_variation where product_id in (select id from product where seller_id='$default_data_apikey' AND `status` = '1' AND `delete_flag` = '1' order by id) order by id");
			$shipping_method = mysql_query("select * from shipping_method where api_key='" . $default_data_apikey . "' and destination='1'");
			
			while ($resproduct_feature = mysql_fetch_assoc($product_feature)) {
				$respf[$resproduct_feature['product_id']][$resproduct_feature['feature_name']] = $resproduct_feature['feature_value'];
			}
			while ($resproduct_image = mysql_fetch_assoc($product_image)) {
				$respi[$resproduct_image['product_id']][$resproduct_image['id']] = $resproduct_image;
			}
			
			while ($resproduct_shipping_policy = mysql_fetch_assoc($product_shipping_policy)) {
				$respsp[$resproduct_shipping_policy['product_id']] = $resproduct_shipping_policy;
			}
			
			while ($resproduct_variation = mysql_fetch_assoc($product_variation)) {
				$respv[$resproduct_variation['product_id']][$resproduct_variation['variation_code']][$resproduct_variation['variant_name']] = $resproduct_variation;
			}
			
			$ressm = mysql_fetch_assoc($shipping_method);
			$shipping_cost = mysql_query("select * from shipping_cost where shipping_id=" . $ressm['shipping_id']);
			$ressc = mysql_fetch_assoc($shipping_cost);
			
			$newcategories = array('nocategory');
			$newbrands = array('nobrand');
			$newcategories1 = '';
			$newbrands1 = '';
			$newproductids = '';
			$newproductidsstring = '';
			$newparentid = '';
			$categories_arr = '';
			$categories_qry = mysql_query("select * from addcategory where apikey='$default_data_apikey'");
			while ($categories_res = mysql_fetch_assoc($categories_qry)) {
				$categories_arr[$categories_res['cat_id']] = $categories_res['cat_name'];
				$parentCatid = $categories_res['parent_id'];
				$sql1 = mysql_query("INSERT INTO addcategory(`apikey`,`cat_name`,`cat_desc`,`parent_id`,`cat_url`,`cat_page_title`,`cat_page_description`,`cat_page_keyword`,`cat_flag`, `image_name`,`image_title`,`image_tag`,`image_description`,`date_added`,`date_modified`,`status`,`image_location`)values('" . $new_data_apikey . "','" . addslashes(stripslashes($categories_res['cat_name'])) . "','" . addslashes(stripslashes($categories_res['cat_desc'])) . "','" . $parentCatid . "','" . $categories_res['cat_url'] . "','" . addslashes(stripslashes($categories_res['cat_page_title'])) . "','" . addslashes(stripslashes($categories_res['cat_page_description'])) . "','" . addslashes(stripslashes($categories_res['cat_page_keyword'])) . "','" . $categories_res['cat_flag'] . "','" . $categories_res['image_name'] . "','" . addslashes(stripslashes($categories_res['image_title'])) . "','" . addslashes(stripslashes($categories_res['image_tag'])) . "','" . addslashes(stripslashes($categories_res['image_description'])) . "','" . $categories_res['date_added'] . "','" . $categories_res['date_modified'] . "','1','http://images.goo2ostore.com/0/" . $default_data_apikey . "/category/" . floor($categories_res['cat_id'] / 1000) . "/" . $categories_res['cat_id'] . "')");
				$last_categoryid = mysql_insert_id();
				$sql1 = mysql_query("INSERT INTO temp_addcategory(`api_key`,`cat_name`,`cat_desc`,`parent_id`,`cat_url`,`cat_page_title`,`cat_page_description`,`cat_page_keyword`,`cat_flag`, `image_name`,`image_title`,`image_tag`,`image_description`,`basicinfostatus`,`imagemanagerstatus`,`optimizestatus`,`type`,`main_cat_id`)values('" . $new_data_apikey . "','" . addslashes(stripslashes($categories_res['cat_name'])) . "','" . addslashes(stripslashes($categories_res['cat_desc'])) . "','" . $parentCatid . "','" . $categories_res['cat_url'] . "','" . addslashes(stripslashes($categories_res['cat_page_title'])) . "','" . addslashes(stripslashes($categories_res['cat_page_description'])) . "','" . addslashes(stripslashes($categories_res['cat_page_keyword'])) . "','" . $categories_res['cat_flag'] . "','" . $categories_res['image_name'] . "','" . addslashes(stripslashes($categories_res['image_title'])) . "','" . addslashes(stripslashes($categories_res['image_tag'])) . "','" . addslashes(stripslashes($categories_res['image_description'])) . "','1','1','1','0'," . $last_categoryid . ")");
				$newcategories[] = $categories_res['cat_name'];
				$newcategories1[$categories_res['cat_name']] = $last_categoryid;
				$newparentid[$last_categoryid] = $parentCatid;
			}
			foreach ($newcategories1 as $newcatKey => $newcatVal) {
				if ($newcatVal != 0) {
					$addcat = mysql_query("update addcategory set parent_id=" . $newcategories1[$categories_arr[$newparentid[$newcatVal]]] . " where cat_id=" . $newcatVal);
				}
			}
			
			while ($res = mysql_fetch_assoc($result)) {
				$last_categoryid = '';
				if (!in_array($res['brand_name'], $newbrands)) {
					$sql1 = mysql_query("insert into  brand(`brand_name`,`brand_description`,`brand_page_title`,`brand_page_description`,`brand_page_keyword`,`brand_flag`,`brand_image`,`brand_title`,`brand_tag`,`image_description`,`date_added`,`date_modified`,`api_key`,`brand_url`,`brand_status`,`delete_status`,`image_location`) values('" . addslashes(stripslashes($res['brand_name'])) . "','" . addslashes(stripslashes($res['brand_description'])) . "','" . addslashes(stripslashes($res['brand_page_title'])) . "','" . addslashes(stripslashes($res['brand_page_description'])) . "','" . addslashes(stripslashes($res['brand_page_keyword'])) . "','1','" . $res['brand_image'] . "','" . addslashes(stripslashes($res['brand_title'])) . "','" . addslashes(stripslashes($res['brand_tag'])) . "','" . addslashes(stripslashes($res['image_description'])) . "','" . $res['date_added'] . "','" . $res['date_modified'] . "','" . $new_data_apikey . "','" . $res['brand_url'] . "','1','1','http://images.goo2ostore.com/0/" . $default_data_apikey . "/brand/" . floor($res['brand_id'] / 1000) . "/" . $res['brand_id'] . "')");
			
					$lastbrand_id = mysql_insert_id();
					
					$sql1 = mysql_query("INSERT INTO temp_addcategory(`api_key`,`cat_name`,`cat_desc`,`parent_id`,`cat_url`,`cat_page_title`,`cat_page_description`,`cat_page_keyword`,`cat_flag`, `image_name`,`image_title`,`image_tag`,`image_description`,`basicinfostatus`,`imagemanagerstatus`,`optimizestatus`,`type`,`main_cat_id`)values('" . $new_data_apikey . "','" . addslashes(stripslashes($res['brand_name'])) . "','" . addslashes(stripslashes($res['brand_description'])) . "','0','" . $res['brand_url'] . "','" . addslashes(stripslashes($res['brand_page_title'])) . "','" . addslashes(stripslashes($res['brand_page_description'])) . "','" . addslashes(stripslashes($res['brand_page_keyword'])) . "','" . $categories_res['cat_flag'] . "','" . $categories_res['image_name'] . "','" . addslashes(stripslashes($res['brand_title'])) . "','" . addslashes(stripslashes($res['brand_tag'])) . "','" . addslashes(stripslashes($res['image_description'])) . "','1','1','1','1'," . $lastbrand_id . ")");
					$newbrands[] = $res['brand_name'];
					$newbrands1[$res['brand_name']] = $lastbrand_id; //last inserted id
				}
				$newprod = mysql_query("insert into product(`product_name`,`category_id`,`brand_id`,`seller_id`,`long_description`,`short_description`,`product_url`,`page_title`, `page_description`,`page_keyword`,`meta_field`,`create_date`,`modified_date`,`display_market_place`,`seller_productid`)values('" . $res['product_name'] . "','" . $newcategories1[$res['cat_name']] . "','" . $newbrands1[$res['brand_name']] . "','" . $new_data_apikey . "','" . addslashes(stripslashes($res['long_description'])) . "','" . addslashes(stripslashes($res['short_description'])) . "','" . $res['product_url'] . "','" . addslashes(stripslashes($res['page_title'])) . "','" . addslashes(stripslashes($res['page_description'])) . "','" . addslashes(stripslashes($res['page_keyword'])) . "','" . addslashes(stripslashes($res['meta_field'])) . "'," . mktime() . "," . mktime() . ",'0','SKU-1-2')");
				$newproductid = mysql_insert_id();
				if ($newproductid > 0) {
					$newproductids[] = $newproductid;
					$newproductidsstring.=$newproductid . ',';
				}
			//mysql_query("update product set seller_productid='SKU-$userid-$newproductid' where id=$newproductid");
			//print_r($respsp);
				$featurestring = '';
				if (isset($respf[$res['id']])) {
					foreach ($respf[$res['id']] as $key => $value)
						$featurestring.="('" . $newproductid . "','" . addslashes(stripcslashes($key)) . "','" . addslashes(stripcslashes($value)) . "'),";
					$newprodfea = mysql_query("insert into product_feature(`product_id`,`feature_name`,`feature_value`)values " . substr($featurestring, 0, -1));
				}
				if (isset($respi[$res['id']])) {
					$newimages = '';
					foreach ($respi[$res['id']] as $key => $value)
						$newimages.="('" . $newproductid . "','" . addslashes(stripcslashes($value['image_title'])) . "','" . addslashes(stripcslashes($value['image_tag'])) . "','" . addslashes(stripcslashes($value['image_description'])) . "','" . $value['image_name'] . "','http://images.goo2ostore.com/0/" . $default_data_apikey . "/product/" . floor($res['id'] / 1000) . "/" . $res['id'] . "','" . $value['image_type'] . "'),";
					$newprodimg = mysql_query("insert into product_image(`product_id`,`image_title`,`image_tag`,`image_description`,`image_name`,`image_location`,`image_type`)values " . substr($newimages, 0, -1));
				}
				if (isset($respv[$res['id']])) {
					$variationstring = '';
					foreach ($respv[$res['id']] as $key => $value1)
						foreach ($value1 as $key => $value)
							$variationstring.="('" . $newproductid . "','" . addslashes(stripcslashes($value['variant_name'])) . "','" . addslashes(stripcslashes($value['variant_value'])) . "','" . $value['variation_code'] . "','" . $value['modify_date'] . "','" . $value['default_flag'] . "'),";
					$newprodvar = mysql_query("insert into product_variation(`product_id`,`variant_name`,`variant_value`,`variation_code`,`modify_date`,`default_flag`)values " . substr($variationstring, 0, -1));
				}
				$allpsp = '';
				if (isset($respsp[$res['id']])) {
					$value = $respsp[$res['id']];
					$allpsp.="('" . $newproductid . "','" . addslashes(stripcslashes($value['weight'])) . "','" . $value['shipping_id'] . "','1','" . $value['default_policy_id'] . "','no','" . $value['buyer_absent_cost'] . "','" . $value['buyer_refuse'] . "','" . $value['buyer_refuse_cost'] . "','" . $value['delay_delivary'] . "','" . $value['delay_delivery_cost'] . "','" . $value['delay_delivery_day'] . "','" . $value['wrong_choice'] . "','" . $value['wrong_choice_cost'] . "','" . $value['wrong_choice_day'] . "','7','7','7','" . $value['restocking_wrong_choice'] . "','" . $value['restocking_wrong_choice_cost'] . "','no','" . $value['restocking_damage_item_cost'] . "','no','no'),";
					$newprodsp = mysql_query("insert into product_shipping_policy(`product_id`,`weight`,`shipping_id`,`policy_type`,`default_policy_id`,`buyer_absent`,`buyer_absent_cost`,`buyer_refuse`,`buyer_refuse_cost`,`delay_delivary`,`delay_delivery_cost`, `delay_delivery_day`, `wrong_choice`, `wrong_choice_cost`, `wrong_choice_day`,`diff_item_day`,`damage_item_day`,`defective_item_day`,`restocking_wrong_choice`,`restocking_wrong_choice_cost`,`restocking_damage_item`,`restocking_damage_item_cost`,`reimburse_delay_delivery`,`reimburse_wrong_choice`)values " . substr($allpsp, 0, -1));
				}
			}
			$newprodsm = mysql_query("insert into shipping_method(`destination`,`handling_time`,`shipping_name`,`api_key`,`delete_flag`,`date_added`,`date_modified`)values('1','" . $ressm['handling_time'] . "','" . $ressm['shipping_name'] . "','" . $ressm['shipping_name'] . "','" . $new_data_apikey . "','1','" . $ressm['date_added'] . "','" . $ressm['date_added'] . "')");
			$last_shipping_id = mysql_insert_id();
			$newprodsc = mysql_query("insert into shipping_cost(`price_id`,`shipping_id`,`shipping_type`,`shipping_pirce`)values('" . $ressc['price_id'] . "','" . $last_shipping_id . "','" . $ressc['shipping_type'] . "','" . $ressc['shipping_pirce'] . "')");
			$updtsellerprid = mysql_query("update product set seller_productid=concat('SKU-" . $userid . "-',id) where id in (" . substr($newproductidsstring, 0, -1) . ")");
			//create store
			//End here
			//$this->_redirect(HTTP_SERVER.'/admin/design/createuserstore');
			$this->_redirect(HTTP_SECURE.'/demo/manage-demo-store/redirect/true');
			exit;
			
			
		}
	}
	public function congratulationAction(){
		$this->view->headTitle('Congratulations: your GMS demo store is created - goo2o.com');
		$merchant_mapper  = new Secure_Model_MerchantenrollmentMapper($this->_user_api_key); // object for Business mapper class
		if($_SESSION['DEMO_FLAG'] == 2){
			//Insert In User_role ANd user_permission Table
			$get_last_id  = $_SESSION['original_login']['userId'];
			$merchant_mapper->mallOwnerRolePermission($this->_user_api_key,$_SESSION['original_login']['user'][0]['user_email_address']);
			$mapper=new Admin_Model_IndexMapper();
			$mapper->changeUserprofile($this->_user_api_key,$get_last_id);
			$this->view->mallurl = $_SESSION['USER']['stores'][0]['mallurl'];
			$_SESSION['DEMO_STORE_DATA'] = '';
			$_SESSION['DEMO_FLAG'] = '';
			/*----@Trigger NO: 14 @created by : Mukesh Bisht @date : 12-11-2011 -------------*/
				  $tData = array(  'control_panel_url'=>'http://goo2ostore.com/admin/overview/#page',
								   'store_url'=>$_SESSION['USER']['stores'][0]['mallurl'],
								   'to_id'=>$_SESSION['USER']['userId'],
								   'to_mail'=>$_SESSION['USER']['stores'][0]['email_id'],
								   'to_name'=>$_SESSION['original_login']['user'][0]['user_full_name']);
				  $this->objTrigger->triggerFire(14,$tData);
			/*---------------End HERE----------------------------*/
			/*----@Trigger NO: 13 @created by : Mukesh Bisht @date : 12-11-2011 -------------*/
				  $tData = array(  'to_id'=>$_SESSION['USER']['userId'],
								   'to_mail'=>$_SESSION['USER']['stores'][0]['email_id'],
								   'to_name'=>$_SESSION['original_login']['user'][0]['user_full_name']);
				  $this->objTrigger->triggerFire(13,$tData);
			/*---------------End HERE----------------------------*/		
		}else{
			 $this->_redirect(HTTP_SECURE.'/demo/create-demo-store');
		}
    }
	public function storeExistsAction(){
		$merchant_mapper  = new Secure_Model_MerchantenrollmentMapper($this->_user_api_key); // object for Business mapper class
		$merchant_mapper->userDetails();
		$this->view->mall_detail = $merchant_mapper->__get('_user_details_array');
		if($this->view->mall_detail[0]['store_owner_type']==1)
			$this->view->headTitle('Sorry: your demo store is active-goo2o.com');
		else
			$this->view->headTitle('Sorry: you have a regular store-goo2o.com');
	}
}
?>
