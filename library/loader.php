<?php
// General function to be included here
error_reporting(0);
include_once('general.php');
include_once('send_mail.php');
include_once('dbname/dbname.php');
include_once('english.php');
//include_once('application.php');
$uri =  $_SERVER['REQUEST_URI'];
$uriexp = explode('/',$uri);
$countUriExp = count($uriexp);
$servername = $_SERVER['SERVER_NAME'];
$explodeServerName = explode('.',$servername);
$countServerName =  count($explodeServerName);

if($countServerName == 3){ // currently running on subdomain
	if($uriexp[1]==''){
		if (file_exists('languages/english/'.$explodeServerName[0].'/'.$explodeServerName[0].'.php')) { 
			include_once('languages/english/'.$explodeServerName[0].'/'.$explodeServerName[0].'.php');
		}
	} else {
		if($uriexp[2]==''){
			if (file_exists('languages/english/'.$explodeServerName[0].'/'.$uriexp[1].'.php')) { 
				include_once('languages/english/'.$explodeServerName[0].'/'.$uriexp[1].'.php');
			} else {
				if (file_exists('languages/english/'.$uriexp[1].'/'.$uriexp[3].'.php')) { 
					include_once('languages/english/'.$uriexp[1].'/'.$uriexp[2].'.php');
				}	
			}
		} else {
				if (file_exists('languages/english/'.$explodeServerName[0].'/'.$uriexp[2].'.php')) { 
					include_once('languages/english/'.$explodeServerName[0].'/'.$uriexp[2].'.php');
				}
		}
	}
}// end currently running on subdomain  

else if($countServerName == 2){ // currently running on direct domain

if($uriexp[3]==''){
	if (file_exists('languages/english/'.$uriexp[1].'/'.$uriexp[2].'.php')) { 
		include_once('languages/english/'.$uriexp[1].'/'.$uriexp[2].'.php');
		//$this->view->headLink()->appendStylesheet('/' . $file_uri); 
	} else {
		if (file_exists('languages/english/'.$uriexp[1].'/'.$uriexp[3].'.php')) { 
			include_once('languages/english/'.$uriexp[1].'/'.$uriexp[2].'.php');
			//$this->view->headLink()->appendStylesheet('/' . $file_uri); 
		}	
	}
	} else {
		if (file_exists('languages/english/'.$uriexp[1].'/'.$uriexp[3].'.php')) { 
			include_once('languages/english/'.$uriexp[1].'/'.$uriexp[3].'.php');
		}


}



}
?>
