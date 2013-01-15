<?php
session_start();
$page = $_GET['page'];
if (trim($page)!=''){
  if(file_exists($_SERVER['DOCUMENT_ROOT'].'/languages/english/admin/'.$page.'.php')){
	  include_once('languages/english/admin/'.$page.'.php'); 
	  echo PAGE_TITLE;
  }
} else {
	define('PAGE_TITLE',('My Account -  goo2o.com'));
	echo PAGE_TITLE;

	  //include_once('languages/english/myaccount/myaccount.php'); 
	//  echo PAGE_TITLE;

}
?>
