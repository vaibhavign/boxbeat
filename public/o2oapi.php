<?php

error_reporting(0);
define('IMAGE_SERVER', 'http://images.goo2ostore.com');
define('IMAGE_SERVER_REAL', '/images/images');
define('IMAGE_SERVER', 'http://images.goo2ostore.com');
define('TABLE_SEO_INDIVIDUAL_PATTERN','seo_individual_pattern');
define('TABLE_SEO_DEFAULT_PATTERN','seo_default_pattern');
define('TABLE_SEO_DEFAULT_CUSTOM_PATTERN','seo_default_custom_pattern');
define('TABLE_SEO_INDIVIDUAL_PATTERN','seo_individual_pattern');
define('HTTP_SECURE', 'http://secure.goo2ostore.com');
define('HTTPS_SECURE', 'http://secure.goo2ostore.com');
define('TABLE_ADDCATEGORY','addcategory');
ini_set("date.timezone", "Asia/Calcutta");

defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
define('LANGUAGE_PATH',dirname(__FILE__)."/languages");
// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library/Doctrine'),
    get_include_path(),
)));
//echo  get_include_path();exit;
//set_include_path('/usr/share/php/libzend-framework-php');
set_include_path(implode(PATH_SEPARATOR, array( 
    realpath(LANGUAGE_PATH), get_include_path(), 
)));

require 'Zend/Application.php';

//echo  APPLICATION_PATH;
$igbApplication = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

include_once 'Zend/Session/SaveHandler/DbTable.php';

	
	$dbs = Zend_Db::factory('Pdo_Mysql', array( 
    'host'        =>'localhost',
    'username'    => 'iglobulh_central',
    'password'    => '!@#$%^',
    'dbname'    => 'iglobulh_goo2ocentral'
));


	if($_REQUEST['type']=='addCart')
	{
		
		
		if($_REQUEST['userid']!='')
		{
			//insert into basket	
		}
		else 
			{
					if($_REQUEST['cart_id']=='')
						{
							$dbs->query("insert into cart set date_added=".time().",date_modified=".time());
							$lastcartid=$dbs->lastInsertId();
							
							$dbs->query("insert into cart_mapper set cart_id=".$lastcartid.",product_id=".$_REQUEST['o2oProductId'].",
							product_name='".$_REQUEST['o2oProductName']."',product_condition='".$_REQUEST['o2oProductCondition']."',
							product_qty='".$_REQUEST['o2oProductQty']."',product_maxqty='".$_REQUEST['o2oProductmaxqty']."',
							product_mrp='".$_REQUEST['o2oProductMrp']."',product_imagesrc='".$_REQUEST['o2oimagelocation']."',
							product_url='".$_REQUEST['o2oProductlink']."',product_dateadded='".time()."',
							product_datemodified='".time()."',store_api_key='".$_REQUEST['o2oApikey']."',
							variationcode='".$_REQUEST['o2oVariationcode']."',shippingtype='".$_REQUEST['o2oshippingtype']."',
							shippingsubtype='".$_REQUEST['o2oshippingsubtype']."',product_variationdetail='".$_REQUEST['o2oVariationdetail']."',
							shipping_id='".$_REQUEST['o2oshippingid']."',shipping_location_id='".$_REQUEST['o2oshippinglocationid']."',
							shipping_location_name='".$_REQUEST['o2oshippinglocation']."',excluded_city_name='".$_REQUEST['o2oexcludedcityname']."',
							excluded_state_name='".$_REQUEST['o2oexcludedstatename']."',excluded_city_id='".$_REQUEST['o2oexcludedcityid']."',
							excluded_state_id='".$_REQUEST['o2oexcludedstateid']."',shipping_price_first='".$_REQUEST['o2oshippingprice']."',
							shipping_price_second='".$_REQUEST['o2oshippingpricerestofindia']."'");
							echo $lastcartid;
							exit;
							
						}
						else
						{
							
						}
			}
		
	}
