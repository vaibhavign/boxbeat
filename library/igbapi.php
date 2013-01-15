<?php
// class for Api
class igbApi{
	
	private $data;
	private $method;
	private $returnType;
	private $page;
	private $error = false;
	
	function __construct($data,$method="Get",$returnType = "xml",$page="signin"){
		
		$this->data = $data;
		$this->method = $method;
		$this->returnType = $returnType;
		$this->page = $page;
		$this->processInput();
	}
	
	protected function getServerError(){
		// function to return set of server errors
		
	}
	
	private function processInput(){
		$page = $this->page;
		$userErrorArray = array();
		$passErrorArray = array();
		$emailErrorArray = array();
		$emailErrorArray = array();
		$capchaerror = array();
		//return $this->data;
                
			switch($page){
				case 'signin' :
				if(!empty($this->data)){
				//echo 'dfgdfgdfg';
									//print_r($this->data['username']);exit;
					$username = new Zend_Validate_NotEmpty();
					if($username->isValid($this->data['username'])){
						//$username = new Zend_Validate_EmailAddress();
						//$username->isValid($this->data['username']);
					}	
						foreach ($username->getMessages() as $message) {
						$userErrorArray = array('username'=>'It can\'t be left blank');
					}
					$password = new Zend_Validate_NotEmpty();
					if($username->isValid($this->data['username'])){
						//$username = new Zend_Validate_EmailAddress();
						//$username->isValid($this->data['username']);
					}
					$password->isValid($this->data['password']);
					foreach ($password->getMessages() as $message) {
						$passErrorArray = array('password'=>'It can\'t be left blank');
					}
					$password->isValid($this->data['recaptcha_response_field']);
					foreach ($password->getMessages() as $message) {
						$capchaerror = array('capchaerror'=>'Enter Above chaptcha value');
					}
					$arr = array_merge($userErrorArray,$passErrorArray);
                                       // print_r($arr);exit;

					if(empty($userErrorArray))
					{
					//echo 'gfhfghgfh';exit;
					  $comment = new Api_Model_Signin($this->data);
						$mapper  = new Api_Model_SigninMapper();
						$returnedArray = $mapper->save($comment);
						$captchaerror=array();
						if($returnedArray<=0){
						$captchaerror = array('captchaappear'=>(($returnedArray==0)?"":1));
						}
						  $arr = array_merge($arr,$captchaerror);
                                                  $all['all'] ='Wrong username/email and password combination';
                                                  $arr = array_merge($arr,$all);
                                                  if(is_array($returnedArray) )
                                                  {
                                                   $arr=array();
                                                      
                                                  }
						 
						
						
						}
					if(!empty($arr)){
						$this->error = true;
						$this->sendErrorResponse($arr);	
					} else {
						//$form    = new Api_Form_Sign();
						//$abc = $this->_request->getParams();
						//$apiKey = $abc['apikey'];
						//$abc = new igbapi($_GET);
						
						$comment = new Api_Model_Signin($this->data);
						$mapper  = new Api_Model_SigninMapper();
						$returnedArray = $mapper->save($comment);
						
						if($returnedArray<=0){
							$this->error = true;
							
						// call for send error response
							$customeErrorResponse = array('username'=>'','password'=>'','all'=>'Wrong username/email and password combination','captchaappear'=>(($returnedArray==0)?0:1));
							$this->sendErrorResponse($customeErrorResponse);
						} else {
						// call for return data response
						//print_r($returnedArray);
							$this->returnDataResponse($returnedArray,$page);
						}
						// check for processing here	
				}
			
			}
            break;
            
            	case 'register' :
				if(!empty($this->data)){
					//print_r($this->data);
					$emailaddress = new Zend_Validate_NotEmpty();
					if($emailaddress->isValid($this->data['emailaddress'])){
						$emailaddress = new Zend_Validate_EmailAddress();
						$emailaddress->isValid($this->data['emailaddress']);
					}	
					foreach ($emailaddress->getMessages() as $message) {
						$emailErrorArray = array('emailaddress'=>$message);
					}
					
					$password = new Zend_Validate_NotEmpty();
					$password->isValid($this->data['passwords']);
					foreach ($password->getMessages() as $message) {
						$passErrorArray = array('passwords'=>$message);
					}
					
					$username = new Zend_Validate_NotEmpty();
					$username->isValid($this->data['username']);
					foreach ($username->getMessages() as $message) {
						$userErrorArray = array('username'=>$message);
					}
					
					$fullname = new Zend_Validate_NotEmpty();
					$fullname->isValid($this->data['fullname']);
					foreach ($fullname->getMessages() as $message) {
						$fullnameErrorArray = array('fullname'=>$message);
					}
					
					
					$arr = array_merge($emailErrorArray,$passErrorArray,$userErrorArray,$fullnameErrorArray);
					if(!empty($arr)){
						$this->error = true;
						$this->sendErrorResponse($arr);	
					} else {
						$comment = new Api_Model_Register($this->data);
						$mapper  = new Api_Model_RegisterMapper();
						$returnedArray = $mapper->save($comment);
						if($returnedArray[0]['email']!='' || $returnedArray[1]['username']!='' ){
							$this->error = true;
						// call for send error response
							$customeErrorResponse = array('username'=>$returnedArray[0]['email'],'emailaddress'=>$returnedArray[1]['username']);
							$this->sendErrorResponse($customeErrorResponse);
						}
						 else {
						// call for return data response
							$this->returnDataResponse($returnedArray,$page);
						}
						// check for processing here	
				}
			
			}	
            break;
			
			case 'forgetpass' :
				if(!empty($this->data)){
					//print_r($this->data);
					$emailaddress = new Zend_Validate_NotEmpty();
					if($emailaddress->isValid($this->data['emailaddress'])){
						$emailaddress = new Zend_Validate_EmailAddress();
						$emailaddress->isValid($this->data['emailaddress']);
					}	
					foreach ($emailaddress->getMessages() as $message) {
						$emailErrorArray = array('emailaddress'=>$message);
					}
					
					$arr = $emailErrorArray;
					if(!empty($arr)){
						$this->error = true;
						$this->sendErrorResponse($arr);	
					} else {
						$comment = new Api_Model_Forgetpass($this->data);
						$mapper  = new Api_Model_ForgetpassMapper();
						$returnedArray = $mapper->save($comment);
						
						if(!empty($returnedArray)){
							$this->error = true;
							$this->sendErrorResponse($returnedArray);
						} else {
							$userDetailsArray = array('emailaddress' =>" Password successfully sent to : ".$this->data['emailaddress']);
							
							$this->returnDataResponse($userDetailsArray,$page);
						}
						
						// check for processing here	
				}
			
			}	
			
			break;

            case 'empsignin' :
				if(!empty($this->data)){
					$username = new Zend_Validate_NotEmpty();
					if($username->isValid($this->data['username'])){
						$username = new Zend_Validate_EmailAddress();
						$username->isValid($this->data['username']);
					}
					foreach ($username->getMessages() as $message) {
						$userErrorArray = array('username'=>$message);
					}
					$password = new Zend_Validate_NotEmpty();
					$password->isValid($this->data['password']);
					foreach ($password->getMessages() as $message) {
						$passErrorArray = array('password'=>$message);
					}
					$arr = array_merge($userErrorArray,$passErrorArray);
					if(!empty($arr)){
						$this->error = true;
						$this->sendErrorResponse($arr);
					} else {
						//$form    = new Api_Form_Sign();
						//$abc = $this->_request->getParams();
						//$apiKey = $abc['apikey'];
						//$abc = new igbapi($_GET);
						$comment = new Api_Model_Signin($this->data);
						$mapper  = new Api_Model_SigninMapper();
						$returnedArray = $mapper->emplogin($comment);

						if(empty($returnedArray)){
							$this->error = true;
						// call for send error response
							$customeErrorResponse = array('username'=>'Invalid Username','password'=>'Invalid Password');
							$this->sendErrorResponse($customeErrorResponse);
						} else {
						// call for return data response
						//print_r($returnedArray);
							$this->returnDataResponse($returnedArray,$page);
						}
						// check for processing here
				}

			}
            break;
            
            default:
            
            break;
		}
	}
	
	
	private function sendErrorResponse($responseArray){
		//print_r($responseArray);
		switch($this->returnType){
		case 'xml' :	
		if($this->error){ // if encounters any error
			// generate hash xml for error messages
			$output  = "<?xml version=\"1.0\"?>\n";
			$output .= "<hash>\n";
			$output .= "<errors>\n";
			foreach($responseArray as $key => $val){
				$output .= "<".$key.">".$val."</".$key."> \n";
			}
			$output .= "</errors> \n";
			$output .= "</hash>";
			echo $output;
		} 
		break;
		
		case 'json' :
		//$abc = new Igb_Controller_Action();
		//echo "'Content-Type','application/json'";
		echo Zend_Json::encode($responseArray);
		break;
		}
		
	}
	
	public function returnDataResponse($dataArray,$type){
		switch($this->returnType){
		case 'xml' :
		if(!$this->error){
                   // print_r($dataArray);
                   //  $output = $this->toXml($dataArray);
                   //  ob_start();
                   //   $output = ob_get_clean();

                  // echo $output;
                      
                  	$output  = "<?xml version=\"1.0\" encoding='ISO-8859-1' ?>\n";
			$output .= "<hash>\n";
			$output .= "<errors>\n";
			foreach($dataArray as $key => $val){
				$output .= "<".$key.">".$val."</".$key."> \n";
			}
			$output .= "</errors> \n";
			$output .= "</hash>";
                        ob_start();
                        ob_get_clean();
                  
			echo $output;
                }
                   // print_r($dataArray);
                  // $output = $this->toXml($dataArray);
                  // echo $output;

               
		break;
		case 'json' :
		//$abc = new Igb_Controller_Action();
		//echo "'Content-Type','application/json'";
		echo Zend_Json::encode($dataArray);
		break;
		
	}
	}

        public  function toXml($data, $rootNodeName = 'data', $xml=null)
	{
		// turn off compatibility mode as simple xml throws a wobbly if you don't.
		if (ini_get('zend.ze1_compatibility_mode') == 1)
		{
			ini_set ('zend.ze1_compatibility_mode', 0);
		}

		if ($xml == null)
		{
			$xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
		}

		// loop through the data passed in.
		foreach($data as $key => $value)
		{
			// no numeric keys in our xml please!
			if (is_numeric($key))
			{
				// make string key...
				$key = "unknownNode_". (string) $key;
			}

			// replace anything not alpha numeric
			$key = preg_replace('/[^a-z]/i', '', $key);

			// if there is another array found recrusively call this function
			if (is_array($value))
			{
				$node = $xml->addChild($key);
				// recrusive call.
				ArrayToXML::toXml($value, $rootNodeName, $node);
			}
			else
			{
				// add single node.
                                $value = htmlentities($value);
				$xml->addChild($key,$value);
			}

		}
		// pass back as string. or simple xml object if you want!
		return $xml->asXML();
	}




}
?>