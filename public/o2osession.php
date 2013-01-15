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


	$db = Zend_Db::factory('Pdo_Mysql', array( 
    'host'        =>'localhost',
    'username'    => 'sketchee_store',
    'password'    => 'var#usr',
    'dbname'    => 'sketchee_o2ostore'
));

 $val = strlen(trim($_COOKIE['PHPSESSID']));

if($val<=0)
	{
		$config = array(
		'name'           => 'session',
		'primary'        => 'session_id',
		'modifiedColumn' => 'modified',
		'dataColumn'     => 'data',
		'lifetimeColumn' => 'lifetime'
	);
		Zend_Db_Table_Abstract::setDefaultAdapter($dbs);
		Zend_Session::setOptions(array('cookie_domain' => '.goo2ostore.com'));
		Zend_Session::setSaveHandler(new Zend_Session_SaveHandler_DbTable($config));
		Zend_Session::start();
		$currentId=Zend_Session::getId();
		//setcookie("PHPSESSID", Zend_Session::getId(), 86400,'', ".goo2ostore.com");
		//setcookie("rrr",'tttttttttttt','');
}
else
{
 $currentId=$_COOKIE['PHPSESSID'];
$db->query("update session set modified=".time()." where session_id='".$currentId."'");
//setcookie("rrr",'ttttttttttttttttt', '');
}



	$storeid=$storeConfigured[0]['id'];
	// echo 'dfgdfgs';exit;
	$sessionSql="select data,sessionname,sessionemail,user_id,session_id from session where session_id ='".$currentId."'";
	 $sessionQuery = $db->query($sessionSql);
	$sessionresultSet= $sessionQuery->fetchAll();
	$vars=preg_split('/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff^|]*)\|/',
	$sessionresultSet[0]['data'],-1,PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	for($i=0; $vars[$i]; $i++) $result[$vars[$i++]]=unserialize($vars[$i]);
	$d=$sessionresultSet[0]['data'];
	
	$sessionData=$result;
	$sdata['session']=$sessionData['USER'];
	$sdata['session_id']=$currentId;
	
	
	//echo '<pre>';
	//print_r($sessionData);
	echo $_GET['jsoncallback']."(".json_encode($sdata).")";
	exit;
	
	
	
