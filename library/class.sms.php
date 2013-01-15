<?php
/*
1. to send sms
eg. http://125.19.68.27:8084/messaging/bulksender.push?msisdn=9219495828&username=iglobul&password=55472159&apicode=106&cc=91&sendercli=iglobul&message=Hi
2. To check status:
http://125.19.68.27:8084/messaging/apireport.push?username=iglobul&password=55472159&messageid=65162736

usages
$message = new sms();
$message->msisdn = '9717729264';
$message->message = urlencode('Test message from class'.date('d-m-Y H:i:s'));

$message->sendSms(); // this will return an array of message along with status report.

*/

class sms {
/*
private $sendSmsParms = array ();
contains the required param for send sms api
 */
private $sendSmsParms = array (
'api' =>'http://125.19.68.27:8084/messaging/bulksender.push?',
'username'=>'iglobul',
'password'=>'55472159',
'apicode'=>'106',
'cc'=>'91',
'sendercli'=>'iglobul'
);

/*
private $statusParms
contains the required param for delievery status of sms sent just now
*/

private $statusParms = array (
'api' =>'http://125.19.68.27:8084/messaging/apireport.push?',
'username'=>'iglobul',
'password'=>'55472159',
);

/*
private $balanceParms
contains the required param for balance sms count from vendor
*/

private $balanceParms = array (
'api'=>'http://125.19.68.27:8084/messaging/checkstatus.push?',
'username'=>'iglobul',
'password'=>'55472159',
);

private  $url = '';
private $returnMessageFromapi = '';
public $messageId = '';
public $messageStatusText = '';
public $messageStatus = '';
public $message = '';
public $msisdn = '';

private function prepare($what='send'){
	if ($what == 'send') {
		$arrOfData = array_merge($this->sendSmsParms,array('msisdn'=>$this->msisdn,'message'=>$this->message));
		$this->url = $arrOfData['api'];
		foreach ($arrOfData as $key=>$value) {
			if ($key!='api'){
				$this->url .= $key.'='.$value.'&';
			}
		}
		$this->url = substr($this->url,0,-1);
	}

	if ($what == 'status') {
		$arrOfData = array_merge($this->statusParms,array('messageid'=>$this->messageId));
		$this->url = $arrOfData['api'];
		foreach ($arrOfData as $key=>$value) {
			if ($key!='api'){
				$this->url .= $key.'='.$value.'&';
			}
		}
		$this->url = substr($this->url,0,-1);
	}
}
private function saveSmsData(){
	/* 66168221#Message sent successfully to : 9717729264 */
	$arrSmsData = array();
	$status = explode('#', $this->returnMessageFromapi);
	$this->messageId = $status[0];
	$this->messageStatusText = $status[1];
	// prepare Data To save in to Data base;
	$arrSmsData = array(
	'to'=>$this->msisdn,
	'message'=>$this->message,
	'sendtime'=>date('Y-m-d H:i:s', time()),
	'messageId'=>$this->messageId,
	'messagestatustext'=>$this->messageStatusText,
	//'messagestatus'=>$this->delieverStatusReport(),
	'customer_id'=>($_SESSION['customer_id'])?$_SESSION['customer_id']:'0'
	);
	return $arrSmsData;
}

public function delieverStatusReport(){
	$this->prepare('status');
	$content = file_get_contents("$this->url");
	$contents = explode('#',$content);
	return $contents[1];
}


public function sendSms(){
	$this->prepare('send');
	$this->returnMessageFromapi = $contents = file_get_contents("$this->url");
	return $this->saveSmsData();
}

}
?>