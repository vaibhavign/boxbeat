<?php
error_reporting(0);
//$_SERVER['REMOTE_ADDR']=182.71.165.53
// Define path to application directory
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
//set_include_path('E:\xampp\php');
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(LANGUAGE_PATH), get_include_path(),
)));
//		mail('mohantyt@gmail.com',"goo2ostore.com",get_include_path());

/*
require_once 'igbapplication.php';
require_once 'igbaction.php';
require_once 'igbapi.php';
*/
ini_set("date.timezone", "Asia/Calcutta");

require 'Zend/Application.php';

$igbApplication = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$igbApplication->bootstrap()
            ->run();
		
		
