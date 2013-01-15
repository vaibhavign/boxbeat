<?php
ob_start();
class Secure_Bootstrap extends Zend_Application_Module_Bootstrap
{
	/**
	 * Allow configs to be obtained through the registry
	 */
	protected function _initAutoload()
	{
	
		 Zend_Session::start(array('cookie_domain' => '.goo2ostore.com'));
                Zend_Session::start();
                //$front = Zend_Controller_Front::getInstance();
               // $request->getModuleName();
               // $request->getControllerName();
			   
		$autoloader = new Zend_Application_Module_Autoloader(array(
					'namespace' => 'Secure',
					'basePath' => APPLICATION_PATH.'/modules/secure',
		));
		$browseroptions=$this->getBrowser();
		
		if($_COOKIE['bsupport']=='')
		{
		
			if(($browseroptions['name']=='Opera' && $browseroptions['version']<11) || ($browseroptions['name']=='Apple Safari' && $browseroptions['version']<5) || ($browseroptions['name']==' Internet Explorer' && $browseroptions['version']<7) || ($browseroptions['name']=='Mozilla Firefox' && $browseroptions['version']<11) || ($browseroptions['name']==' Google Chrome' && $browseroptions['version']<7))
			{
				//if($_SERVER['REQUEST_URI']!='/error/browsernotsupported' && $_SERVER['REQUEST_URI']!='error/browsernotsupported')
				//$_SESSION['redirectpage']=$_SERVER['HTTP_HOST'].(($_SERVER['REQUEST_URI']!='')?$_SERVER['REQUEST_URI']:'');
			//	//setcookie('bsupport','yes',time()+(86400*100),'/');
			//	header('Location: '.HTTP_SERVER.'/error/browsernotsupported');
				
			}
		}
		//echo 'hrer';exit;
		return $autoloader;
		
		//$view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");
	}
	function getBrowser() 
		{ 
		
			$u_agent = $_SERVER['HTTP_USER_AGENT']; 
			$bname = 'Unknown';
			$platform = 'Unknown';
			$version= "";
		
			//First get the platform?
			if (preg_match('/linux/i', $u_agent)) {
				$platform = 'linux';
			}
			elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
				$platform = 'mac';
			}
			elseif (preg_match('/windows|win32/i', $u_agent)) {
				$platform = 'windows';
			}
			
			// Next get the name of the useragent yes seperately and for good reason
			if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
			{ 
				$bname = 'Internet Explorer'; 
				$ub = "MSIE"; 
			} 
			elseif(preg_match('/Firefox/i',$u_agent)) 
			{ 
				$bname = 'Mozilla Firefox'; 
				$ub = "Firefox"; 
			} 
			elseif(preg_match('/Chrome/i',$u_agent)) 
			{ 
				$bname = 'Google Chrome'; 
				$ub = "Chrome"; 
			} 
			elseif(preg_match('/Safari/i',$u_agent)) 
			{ 
				$bname = 'Apple Safari'; 
				$ub = "Safari"; 
			} 
			elseif(preg_match('/Opera/i',$u_agent)) 
			{ 
				$bname = 'Opera'; 
				$ub = "Opera"; 
			} 
			elseif(preg_match('/Netscape/i',$u_agent)) 
			{ 
				$bname = 'Netscape'; 
				$ub = "Netscape"; 
			} 
			
			// finally get the correct version number
			$known = array('Version', $ub, 'other');
			$pattern = '#(?<browser>' . join('|', $known) .
			')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
			if (!preg_match_all($pattern, $u_agent, $matches)) {
				// we have no matching number just continue
			}
			// see how many we have
			$i = count($matches['browser']);
			if ($i != 1) {
				//we will have two since we are not using 'other' argument yet
				//see if version is before or after the name
				if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
					$version= $matches['version'][0];
				}
				else {
					$version= $matches['version'][1];
				}
			}
			else {
				$version= $matches['version'][0];
			}
			
			// check if we have a number
			if ($version==null || $version=="") {$version="?";}
			
			return array(
				'userAgent' => $u_agent,
				'name'      => $bname,
				'version'   => $version,
				'platform'  => $platform,
				'pattern'    => $pattern
			);
		}

        

}
