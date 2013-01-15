<?php
include_once("xmlapi.php"); // xml api class 
class o2oDomainClass{
public $xmlapi;
	public function __construct(){
		  $this->ip = "119.81.0.54";        // should be WHM ip address
		  $this->account = "eshopbox";  
		 // cpanel user account name
		 // $this->passwd ="Moh@75%^78";       // cpanel user password
		 // $this->passwd ="AP@2011!";
			 $this->passwd ="news@0071"; 	
     $this->port =2082;                // cpanel secure authentication port unsecure port# 2082
		  $this->email_domain = 'eshopbox.com'; // email domain (usually same as cPanel domain)
		  $this->xmlapi = new xmlapi($this->ip);
		  $this->xmlapi->set_port($this->port);  //set port number. cpanel client class allow you to access WHM as well using WHM port.
		  $this->xmlapi->password_auth($this->account, $this->passwd);
	}
   public function createSubdomain(){
   	echo $this->account;
			$args = func_get_args();
			$subDomainName = $args[0];   	
   	$this->xmlapi->api2_query($this->account, 'SubDomain','addsubdomain', array(dir=>"public_html/".$subDomainName, domain=>$subDomainName, rootdomain=>"eshopbox.com") );
			//echo 'abc123';   	
   	}
} // end class
?>