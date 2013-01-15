<?php

class InboxController extends Zend_Controller_Action {

    private $session;

    public function init() {
	
        //echo 		strtotime('20-10-2011');exit;
         
        Zend_Layout::getMvcInstance()->setLayout('secureadmin');
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH . '/modules/secure/layouts');

        $this->view->headLink()->appendStylesheet('/css/secure/admin.css');
        $this->view->headLink()->appendStylesheet('/css/default/popup.css');
        //$this->view->headLink()->appendStylesheet('/css/default/shippingaddresschange.css');
        $this->view->headScript()->appendFile('/jscript/common/o2olib.js', 'text/javascript');

        $this->view->headScript()->appendFile('/jscript/default/ajs.js', 'text/javascript');
		        $this->view->headScript()->appendFile('/jscript/default/jquery.popup.js', 'text/javascript');
		 $this->view->headScript()->appendFile('/jscript/common/inbox.js', 'text/javascript');
     //  $this->view->headScript()->appendFile('/jscript/default/inbox.js', 'text/javascript');
       

        //$this->view->headLink()->appendStylesheet('/css/default/shippingaddresschange.css');
        //	$this->view->headLink()->appendStylesheet('/css/default/commondispute.css');
        $this->session = new Zend_Session_Namespace('SESSION');

	

        $this->userName = new Zend_Session_Namespace('USER');
      $datatitle = $this->_request->getParams();

	if($datatitle['type']=='starred')
	//define('PAGE_TITLE','Inbox - '.$this->userName->userDetails[0]['username'].PAGE_EXTENSION);
            $this->view->headTitle(' Starred - '.$this->userName->userDetails[0]['username'].PAGE_EXTENSION);
        else if($datatitle['type']=='conversation')
	//define('PAGE_TITLE','Inbox - '.$this->userName->userDetails[0]['username'].PAGE_EXTENSION);
            $this->view->headTitle(' Conversation - '.$this->userName->userDetails[0]['username'].PAGE_EXTENSION);
        else if($datatitle['type']=='request')
	//define('PAGE_TITLE','Inbox - '.$this->userName->userDetails[0]['username'].PAGE_EXTENSION);
            $this->view->headTitle(' Request - '.$this->userName->userDetails[0]['username'].PAGE_EXTENSION);
        else if($datatitle['type']=='dispute')
	//define('PAGE_TITLE','Inbox - '.$this->userName->userDetails[0]['username'].PAGE_EXTENSION);
            $this->view->headTitle(' Dispute - '.$this->userName->userDetails[0]['username'].PAGE_EXTENSION);
         else if($datatitle['type']=='alert')
	//define('PAGE_TITLE','Inbox - '.$this->userName->userDetails[0]['username'].PAGE_EXTENSION);
            $this->view->headTitle(' Alert - '.$this->userName->userDetails[0]['username'].PAGE_EXTENSION);
	else
            $this->view->headTitle(' Inbox - '.$this->userName->userDetails[0]['username'].PAGE_EXTENSION);
       //define('PAGE_TITLE','Conversation - '.$this->userName->userDetails[0]['username'].PAGE_EXTENSION);
	//$this->userName->userId = '4';
        if ($this->userName->userId == '') {
            $this->_redirect(HTTP_SECURE . '/login');
        }

        $this->mapper = new Default_Model_InboxMapper();

       // $this->buyer_model = new Myaccount_Model_BuyerMapper();
       // $this->order_model = new Admin_Model_OrdersMapper();

    }

    public function indexAction() {

        //$unreadMessage=$this->mapper->getUnreadmessage();
	
        
    }

    public function listAction() { 

        //echo "<pre>";
        //print_r($_SESSION);
         //echo "</pre>";
        $dataget = $this->_request->getParams();
         $this->view->totalunread= $this->mapper->getUnreadmessage();

         //echo "<pre>";
        //print_r( $this->mapper->getUnreadmessage());
         //exit;
        //print_r($data);
        if($dataget['type']=='dispute')
        {
        $inboxtype="'3'";

        }
        else if($dataget['type']=='request')
        {
        $inboxtype="'2'";
        }
        else if($dataget['type']=='alert')
        {
        $inboxtype="'4'";
        }
        else if($dataget['type']=='starred')
        {
        $inboxtype="0";
        }
        else if($dataget['type']=='conversation')
        {
        $inboxtype="'0','1'";
        }
        else
        {
        $inboxtype='';
        }
     
       $this->view->filter=$dataget['type'];
        $this->view->currentid = $this->userName->userId;
        $recordperpage = 10;
 		

        $data = $this->mapper->getConversation('', $dataget,$inboxtype);
		
  // echo "<pre>";
   // print_r( $data );
        //echo "</pre>";
       // exit;
       
        $this->view->recordperpage = $recordperpage;
        $this->view->getdata = $dataget;
        $this->view->totalpage = ceil(count($data) / $recordperpage);
         $this->view->totalrecord = count($data);
        //$this->view->totalunread= $this->mapper->getcountunreadMessage();
        $this->view->data = $data;
    }
    public function bookunbookAction()
    {
             $this->_helper->layout->disableLayout();
             $data = $this->_request->getParams();
            
             $this->mapper->updateBookMark($data['cid'],$data['update']);
             exit;
    }

    public function ajaxlistAction() {
        $this->_helper->layout->disableLayout();
        $data = $this->mapper->getConversation();
        $this->view->data = $data;
        $data['data'] = $this->view->data;
        $data['time'] = time();
        echo json_encode($data);
        exit;
    }
    public function autosuggessionAction()
    {
        $data = $this->_request->getParams(); 
        $d=$this->mapper->suggestusername($data['term']);
        echo json_encode($d);
        exit;
    }
    public function closeuAction()
    {
     $_SESSION['totalnew']=0;   
     exit;   
    }
    public function savemessageAction()
    {
       $data = $this->_request->getParams(); 
        $d=$this->mapper->savenewmessage( $data);
       // echo json_encode($d);
        exit;
        
    }

    public function upAction() {
        $this->_helper->layout->disableLayout();
        $lTime = $this->_request->getParam('time');
       $userName = new Zend_Session_Namespace('USER');
        $data = $this->mapper->getConversation($lTime);
        $datas = array();
        //print_r($data);exit;
        $datas['data'] = $data;
        $datas['time'] = time();
        $datas['readunread'] =  $this->mapper->getUnreadmessage();
         $datas['currentid'] = $userName->userId;
        echo json_encode($datas);
        exit;
    }
 public function nmuAction() {
        $this->_helper->layout->disableLayout();
        $lTime = $this->_request->getParams();
       // echo $lTime ;exit;
       $userName = new Zend_Session_Namespace('USER');
       
       $data = $this->mapper->getConversationupdater($lTime['time']);
       //$msgCounter = new Zend_Session_Namespace('MessageCounter');
       $datas = array();
       $msgPrev=$lTime['b'];
       //echo 'dfgdfgf';exit;
       $upper =  $this->mapper->getUnreadmessage();
       $datas['totalupper']=$upper['total'];
       //exit;
       //if(!$msgCounter->totalnew)
      // $msgCounter->totalnew=0;   
       //print_r($data);exit;
       
       $msgCounter = new Zend_Session_Namespace('MessageCounter');
      // echo "<pre>";
      // print_r($msgCounter);
     // $totalM=$msgCounter->totalnew;
      $newArr= sizeof($data);
       $msgCounter->totalnew= 1 ;
       // $datas['data'] = $data;
         $datas['total'] =  $msgCounter->totalnew;
        $datas['time'] = time();
         $_SESSION['totalnew']='';
          $_SESSION['totalnew']= 1;
        //echo '<pre>';
       // print_r($_SESSION);
        //echo $_SESSION['totalnew'];
        echo json_encode($datas);
        exit;
    }
    public function detailAction() {
        
        $chatid = $this->_request->getParam('id');
        $userName = new Zend_Session_Namespace('USER');
        $this->mapper->readunreadaction($userName->userId, $chatid, 1);
        $message = $this->mapper->getConversationDetail($chatid);
//echo "<pre>";
//print_r();
if(  $chatid==1112)
{
//echo "<pre>";
//print_r( $message);

}
        $messageData=$message['data'];
        $this->view->otheruserdetail=$message['userdetail'];
        //echo "<pre>";
        //print_r($messageData);
        //exit;
        $this->view->messagedata = $messageData;
        $this->view->userName = $userName->userDetails[0]['userName'];
        $this->view->userId = $userName->userId;
        $this->view->cid = $chatid;
        //echo '<pre>';
        //print_r($messageData);
        //exit;
        //echo $userName->userDetails[0]['user_location'];exit;
        $this->view->email = $messageData[0]['guestemail'];
        $this->view->userfullname = $messageData[0]['guestname'];
        if ($messageData[0]['from'] == $userName->userId) {
            $this->view->userfullname = $messageData[0]['userfrom'];
            $this->view->email = $userName->userDetails[0]['user_email_address'];
            if ($messageData[0]['userfrom'] == '') {
                $this->view->userfullname = $messageData[0]['guestname'];
                $this->view->location = '';
            } else {
                
               // $this->view->location = $this->mapper->getLocationNamefromId($userName->userDetails[0]['user_location']);
            }
        } else {
            
        }



        //exit;
    }

    public function detailajaxAction() {
        $this->_helper->layout->disableLayout();
        $chatid = $this->_request->getParam('id');
        $userName = new Zend_Session_Namespace('USER');
        $messageData = $this->mapper->getConversationDetail($chatid);
        $this->view->messagedata = $messageData['data'];
        $this->view->userName = $userName->userDetails[0]['userName'];
        $this->view->userId = $userName->userId;
        $this->view->cid = $chatid;
        //echo '<pre>';
        //print_r($messageData);
        //exit;
        $this->view->email = $messageData[0]['guestemail'];
        $this->view->userfullname = $messageData[0]['guestname'];
        if ($messageData[0]['from'] == $userName->userId) {
            $this->view->userfullname = $messageData[0]['userfrom'];
            $this->view->email = $userName->userDetails[0]['user_email_address'];
            if ($messageData[0]['userfrom'] == '') {
                $this->view->userfullname = $messageData[0]['guestname'];
                $this->view->location = '';
            } else {
                $this->view->location = $this->mapper->getLocationNamefromId($userName->userDetails[0]['user_location']);
            }
        } else {
            
        }

        echo json_encode($this->view);
        exit;
        //exit;
    }

    public function replayAction() {
        $this->_helper->layout->disableLayout();
        $data = $this->_request->getParams();
        $userName = new Zend_Session_Namespace('USER');
        $toid = $this->mapper->setreplay($userName->userId, $data['chatid'], $data['message']);
        echo json_encode($toid);
        exit;
    }

    public function ruAction() {
        $this->_helper->layout->disableLayout();
        $data = $this->_request->getParams();
        $userName = new Zend_Session_Namespace('USER');
        $toid = $this->mapper->readunreadaction($userName->userId, $data['cid'], $data['a']);
        $total =  $this->view->totalunread= $this->mapper->getUnreadmessage();
        if($data['re']==1)
        {
        echo '<script>window.location.href="/inbox/list";</script>';
        exit;
        }
        else
        {
        echo json_encode($total);
        exit;
        }
    }

    public function dAction() {
        //return false;
        //$this->_helper->layout->disableLayout();
        $data = $this->_request->getParams();
        $userName = new Zend_Session_Namespace('USER');
        $toid = $this->mapper->deletechat($userName->userId, $data['id']);
        //echo 'dfgdf';
        echo '<script>window.location.href="/inbox/list";</script>';
        exit;
        //$this->_redirect('/admin/conversation/#list');
        exit;
    }

    //Request for shipping address change
    public function shippingaddresschangeAction() {
        $this->_helper->layout->disableLayout();
        $userName = new Zend_Session_Namespace('USER');
        $request_id = decryptLink($this->_request->getParam('rqid'));

        if ($_POST['id'] == 'acceptRequest') {  //Seller accepted the change in shipping address request
            $addressbook_id = $_POST['addressbook_id'];
            $requestid = $_POST['requestid'];
            $orderItemId = $_POST['orderItemId'];
            $this->mapper->changeInShippingAddressAccepted($requestid, $addressbook_id, $orderItemId);
            exit;
        }

        if ($_POST['id'] == 'rejectRequest') { //Seller rejected the change in shipping address request
            $requestid = $_POST['requestid'];
            $this->mapper->changeInShippingAddressRejected($requestid);
            exit;
        }

        $orderData = $this->mapper->shippingAddressChangeData($request_id);
           $this->view->dropDown =$this->mapper->showDropDown($request_id,$userName->userId);
       // echo $this->mapper->lastQuery();
        $this->view->orderData = $orderData;
        $messageData = $this->mapper->requestMessagesData($request_id, $userName->userId);
        $this->view->messageData = $messageData;
        $this->view->userId = $userName->userId;
    }

// End shipping address change
    //Buyer requested for cancellation of order
    public function requestforcancellationdetailsAction() {
        $this->_helper->layout->disableLayout();
        $userName = new Zend_Session_Namespace('USER');

        if ($_POST['id'] == 'acceptCancellation') { //Seller accepted the cancellation request
            $addressbook_id = $_POST['addressbook_id'];
            $requestid = $_POST['requestid'];
            $orderItemId = $_POST['orderItemId'];
            $this->mapper->requestForCancellationAccepted($requestid, $addressbook_id, $orderItemId);
            exit;
        }

        if ($_POST['id'] == 'rejectCancellation') { //Seller rejected the cancellation request
            $requestid = $_POST['requestid'];
            $this->mapper->requestForCancellationRejected($requestid);
            exit;
        }

        $request_id = decryptLink($this->_request->getParam('rqid'));
        $this->view->dropDown =$this->mapper->showDropDown($request_id,$userName->userId);
       //echo $this->mapper->lastQuery();
        $cancellationData = $this->mapper->shippingAddressChangeData($request_id);
        $this->view->cancellationData = $cancellationData;

        $cancellationMessageData = $this->mapper->requestMessagesData($request_id, $userName->userId);
        $this->view->cancellationMessageData = $cancellationMessageData;

        $this->view->userId = $userName->userId;
        $this->view->order_model = new Admin_Model_OrdersMapper();
    }

//End requestforcancellation

    /* public function customerreturnrequestdetailsAction(){
      $this->_helper->layout->disableLayout();
      //$request_id = $this->_request->getParam('request_id');
      $request_id = '2';
      $userName = new Zend_Session_Namespace('USER');
      $returnData = $this->mapper->shippingAddressChangeData($request_id);
      $this->view->returnData = $returnData;
      $returnMessageData = $this->mapper->requestMessagesData($request_id,$userName->userId);
      $this->view->returnMessageData = $returnMessageData;

      } */

    //DISPUTES ACTIONS
    //@author : Harpreet Singh
    // Used for Disputes regarding an Order
    // Creation Date : 22-09-2011
    // Created By : Harpreet Singh 

    public function openadisputeAction() {
        $this->_helper->layout->disableLayout();
        $userName = new Zend_Session_Namespace('USER');

        if ($_GET['method'] == 'open') {
            $pieces = explode('_', $_GET['orderItemId']);
            $orderItemId = $pieces[3];

            $validate = $this->mapper->validateOrderItem($orderItemId, $this->userName->userId);
            $this->view->validate = $validate;
            //$orderItemDetails = $this->buyer_model->getOrderDetailsByOrderItemId($orderItemId);
        }
        /* $data = $this->mapper->disputed();
          $this->view->mydata = $data; */
    }

// End openadispute

    public function customeropenadisputeAction() {
        $this->_helper->layout->disableLayout();

        $orderItemId = decryptLink($this->_request->getParam('oid'));
        $error = $this->mapper->validateOpenDispute($this->userName->userId, $orderItemId); //error handling
        if ($error != '') {
            $this->view->error = $error;
            exit;
        }
        $orderItemDetails = $this->buyer_model->getOrderDetailsByOrderItemId($orderItemId);
        $itemStatus = $orderItemDetails['order_item_status'];
        $itemStatusName = $this->order_model->getOrderStatus($itemStatus);
        $reasonsAndExample = $this->mapper->getReasonsAndExample($itemStatus, $orderItemId, $this->userName->userId);

        $this->view->orderItemDetails = $orderItemDetails;
        $this->view->itemStatusName = $itemStatusName;
        $this->view->reasonsAndExample = $reasonsAndExample;
    }

// End customeropenadispute

    public function customerdisputedetailsAction() {
        $this->_helper->layout->disableLayout();
        $disputeId = decryptLink($this->_request->getParam('did'));

        $error = $this->mapper->validateDisputeDetails($this->userName->userId, $disputeId); //error handling
        if ($error != '') {
            $this->view->error = $error;
            exit;
        }

        $disputeDetail = $this->mapper->getOrderDetailsForDispute($disputeId);
        $disputeMessages = $this->mapper->getDisputeMessages($disputeId, $this->userName->userId);
        $dropDown = $this->mapper->createDropDownActions($disputeId, $this->userName->userId); //Drop down actions e.g.-escalate,close,solve etc
        $remark = $this->mapper->createRemark($disputeId, $this->userName->userId);    //Generate remark e.g.- A has raised a dispute against B
        $documents = $this->mapper->getAttachedDocuments($disputeId, $this->userName->userId);

        $this->view->remark = $remark;
        $this->view->dropDown = $dropDown;
        $this->view->disputeDetail = $disputeDetail;
        $this->view->disputeMessages = $disputeMessages;
        $this->view->documents = $documents;
    }

//End customerdisputedetails
    //Get sub reasons using Json
    public function getsubreasonsAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $orderStatusId = $_POST['orderStatusId'];
        $subReasons = $this->mapper->getSubReasons($orderStatusId);
        echo json_encode($subReasons);
        exit;
    }

//End getSubreasons
    //Submit dispute
    public function submitdisputeAction() {
        $apikey = $_SESSION['SESSION']['ApiKey'];
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $mydata = $_POST;
        $this->mapper->submitDispute($mydata, $this->userName->userId, $apikey);
        exit;
    }

    public function orderitemdataAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->mapper->getAllOrderItemRelatedToUser($this->userName->userId);
    }

//End orderitemdata

    public function respondToRequestAction() {
        $this->_helper->layout->disableLayout();
        $userName = new Zend_Session_Namespace('USER');
        $requestId = decryptLink($this->_request->getParam('rqid'));
        $returnDetails = $this->mapper->getReturnDetailFromReturnId($requestId);
        $returnMessageData = $this->mapper->requestMessagesData($requestId, $userName->userId);

        $this->view->returnMessageData = $returnMessageData;
        $this->view->returnDetails = $returnDetails;

        $this->view->userId = $this->userName->userId;
    }

//End respondToRequest
    // Respond to return request
    public function respondtoreturnrequestAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($_POST['id'] == 'acceptReturn') { //Return request accepted
            $data = $this->_request->getParams($_POST);
            $this->mapper->respondToReturnAccepted($data);
            exit;
        }//End if
        elseif ($_POST['id'] == 'rejectReturn') { //Return request rejected
            $data = $this->_request->getParams($_POST);
            $this->mapper->respondToReturnRejected($data);
            exit;
        }//end elseif
    }

//End respondtoreturnrequest
    //File Upload is done using a plugin called AjaxFileUpload. For customeropenadisputeAction.
    public function doajaxfileuploadAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $userApiKey = $_SESSION['SESSION']['ApiKey'];
        $filename = $_POST['datatext']; //file name
        $dirnamess = $_POST['randname']; //Temp directory name in $dir
        $dir = "uploads/temp/" . $dirnamess;
	//if(is_dir($dir)){echo 'exits';exit;}else{echo 'not';exit;}
        //print_r($_SESSION);
        if (!is_dir($dir) && strlen($dir) > 0) {
            $rights = 0777;
            mkdir($dir, $rights);
        }
	//if(is_dir($dir)){echo 'dere';exit;}else{echo 'no';exit;}
        $error = "";
        $msg = "";
        $fileElementName = 'fileToUpload';
        if (!empty($_FILES[$fileElementName]['error'])) {
            switch ($_FILES[$fileElementName]['error']) {

                case '1':
                    $error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
                    break;
                case '2':
                    $error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
                    break;
                case '3':
                    $error = 'The uploaded file was only partially uploaded';
                    break;
                case '4':
                    $error = 'No file was uploaded.';
                    break;

                case '6':
                    $error = 'Missing a temporary folder';
                    break;
                case '7':
                    $error = 'Failed to write file to disk';
                    break;
                case '8':
                    $error = 'File upload stopped by extension';
                    break;
                case '999':
                default:
                    $error = 'No error code avaiable';
            }//End switch
        }//End if
        elseif (empty($_FILES['fileToUpload']['tmp_name']) || $_FILES['fileToUpload']['tmp_name'] == 'none') {
            $error = 'No file was uploaded..';
        }//End elseif
        else {
            $exp = explode(".", $_FILES['fileToUpload']['name']);
            $msg .= $dir . "/" . $filename . "." . $exp[1] . ":" . filesize($_FILES['fileToUpload']['tmp_name']) . ":" . $filename . "." . $exp[1];
            move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $dir . "/" . $filename . "." . $exp[1]);

            //$msg .= " File Name: " . $_FILES['fileToUpload']['name'] . ", ";
            //$msg .= " File Size: " . @filesize($_FILES['fileToUpload']['tmp_name']); 
            //for security reason, we force to remove all uploaded file
            //@unlink($_FILES['fileToUpload']);		
        }//End else		
        echo "{";
        echo "error: '" . $error . "',\n";
        echo "msg: '" . $msg . "'\n";
        echo "}";
    }

//End doajaxfileupload
    //Find order item popup. Get data for different days
    public function getorderitemaccordingtodaysAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $userName = new Zend_Session_Namespace('USER');

        if ($_POST['numOfDays'] == '15') {
            $this->mapper->getOrderItemForFifteenDays($this->userName->userId);
        }//End if
        elseif ($_POST['numOfDays'] == '30') {
            $this->mapper->getOrderItemForThirtyDays($this->userName->userId);
        }//End elseif
        elseif ($_POST['numOfDays'] == '45') {
            $this->mapper->getOrderItemForFortyfiveDays($this->userName->userId);
            exit;
        }//End elseif
        elseif ($_POST['numOfDays'] == '90') {
            $this->mapper->getOrderItemForNinetyDays($this->userName->userId);
            exit;
        }//End elseif
    }

//End getorderitemaccordingtoDays
    //Dispute Solved
    public function solvedisputeAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $disputeId = $_POST['disputeId'];
        $this->mapper->solveDispute($disputeId);
        exit;
    }

//End disptesolved
    //Dispute escalated by through JS
    public function escalatedisputebyAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $disputeId = $_POST['disputeId'];
        $this->mapper->escalateDisputeBy($disputeId, $this->userName->userId);
        exit;
    }

//End escalatedisputeby
    //Dispute escalated against through JS
    public function escalatedisputeagainstAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $disputeId = $_POST['disputeId'];
        $this->mapper->escalateDisputeAgainst($disputeId, $this->userName->userId);
        exit;
    }

    //File Upload is done using a plugin called AjaxFileUpload. For customerdisputedetail.
    public function doajaxfileuploadfordisputeAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $userApiKey = $_SESSION['SESSION']['ApiKey'];
        $filename = $_POST['datatext'];  //file name
        $dirnamess = $_POST['randname']; //Temp directory name in $dir
        $dir = getDisputeUploadedFilesPath($dirnamess);
        if (!is_dir($dir) && strlen($dir) > 0) {
            echo 'exits';
            $rights = 0777;
            mkdir($dir, $rights);
        }
        $error = "";
        $msg = "";
        $fileElementName = 'fileToUpload';
        if (!empty($_FILES[$fileElementName]['error'])) {
            switch ($_FILES[$fileElementName]['error']) {

                case '1':
                    $error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
                    break;
                case '2':
                    $error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
                    break;
                case '3':
                    $error = 'The uploaded file was only partially uploaded';
                    break;
                case '4':
                    $error = 'No file was uploaded.';
                    break;

                case '6':
                    $error = 'Missing a temporary folder';
                    break;
                case '7':
                    $error = 'Failed to write file to disk';
                    break;
                case '8':
                    $error = 'File upload stopped by extension';
                    break;
                case '999':
                default:
                    $error = 'No error code avaiable';
            }//End switch
        }//ENd if
        elseif (empty($_FILES['fileToUpload']['tmp_name']) || $_FILES['fileToUpload']['tmp_name'] == 'none') {
            $error = 'No file was uploaded..';
        }//End elseif
        else {
            $exp = explode(".", $_FILES['fileToUpload']['name']);
            $this->mapper->insertFiles($filename, $dirnamess, $this->userName->userId, $exp[1]);
            $msg .= $dir . "/" . $filename . "." . $exp[1] . ":" . filesize($_FILES['fileToUpload']['tmp_name']) . ":" . $filename . "." . $exp[1];
            move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $dir . "/" . $filename . "." . $exp[1]);

            //$msg .= " File Name: " . $_FILES['fileToUpload']['name'] . ", ";
            //$msg .= " File Size: " . @filesize($_FILES['fileToUpload']['tmp_name']); 
            // $var = $this->mapper->copyFilesForDispute($_POST['randname'],$userApiKey);
            //for security reason, we force to remove all uploaded file
            //@unlink($_FILES['fileToUpload']);	
        }//End else		
        echo "{";
        echo "error: '" . $error . "',\n";
        echo "msg: '" . $msg . "'\n";
        echo "}";
    }

//End doajaxfileuploadfordispute
}

//End Class
