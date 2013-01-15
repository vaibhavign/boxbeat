<?php

class DisputeController extends Zend_Controller_Action{

    private $session;
    public $genObj;
    public function init() {
        //echo 		'with in 15'.strtotime('15-09-2011');exit;
        //echo encryptLink(6);
            $this->genObj = new General();
        Zend_Layout::getMvcInstance()->setLayout('secureadmin');
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH . '/modules/secure/layouts');

        $this->view->headLink()->appendStylesheet('/css/secure/admin.css');
        $this->view->headLink()->appendStylesheet('/css/admin/admin.css');
        //$this->view->headLink()->appendStylesheet('/css/default/shippingaddresschange.css');
        $this->view->headScript()->appendFile('/jscript/common/o2olib.js', 'text/javascript');
        $this->view->headScript()->appendFile('/jscript/common/white.js', 'text/javascript');
       // $this->view->headScript()->appendFile('/jscript/default/inbox.js', 'text/javascript');
        $this->view->headScript()->appendFile('/jscript/default/changerequest.js', 'text/javascript');
        $this->view->headScript()->appendFile('/jscript/common/ajaxfileupload.js', 'text/javascript');
	$this->view->headScript()->appendFile('/jscript/common/pulse.js', 'text/javascript');

        //$this->view->headLink()->appendStylesheet('/css/default/shippingaddresschange.css');
        //	$this->view->headLink()->appendStylesheet('/css/default/commondispute.css');
        $this->session = new Zend_Session_Namespace('SESSION');

	

        $this->userName = new Zend_Session_Namespace('USER');
	//
        //$this->userName->userId = '4';
        if ($this->userName->userId == '') {
            $this->_redirect(HTTP_SECURE . '/login');
        }
        $this->mapper = new Default_Model_DisputeMapper();
        $this->buyer_model = new Myaccount_Model_BuyerMapper();
        $this->order_model = new Admin_Model_OrdersMapper();
	$this->storeName = $this->userName->userDetails[0]['username'];
    }

    public function indexAction() {
	define('PAGE_TITLE',' Dispute - '.$this->storeName.PAGE_EXTENSION);
        $this->view->headScript()->appendFile('/jscript/common/bbq.js');
    }


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
        $error =  $this->mapper->validateOpenDispute($this->userName->userId, $orderItemId);//error handling
        
        if ($error != '') {
            $this->view->error = $error;
        }
        
        //$raisedBy = $this->mapper->checkDisputeRaisedByBuyerOrSeller($orderItemId,$this->userName->userId);
        $returnValue= $this->mapper->checkReturnTable($orderItemId);
        if($returnValue){  // empty
            $orderItemDetails = $this->mapper->getOrderDetailsByOrderItemId($orderItemId);
            //echo '<pre>';print_r($orderItemDetails);
            $this->view->returnRequested =false;
            
           $itemStatus = $orderItemDetails['order_item_status'];
          
            
        }else{ //Not empty
            $orderItemDetails = $this->mapper->getReturnItemDetails($orderItemId);
              $this->view->returnRequested =true;
              $itemStatus = $orderItemDetails['return_status'];
            //echo '<pre>';print_r($orderItemDetails);exit;
        }
        $itemDetails = $this->mapper->getOrderItemStatusFromOrderItemId($orderItemId);
        
        $itemStatusName = $itemDetails['status'];
              
        $reasonsAndExample = $this->mapper->getReasonsAndExample($itemDetails['id'], $orderItemId, $this->userName->userId);

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
        $getOID = $this->mapper->getOrderItemIdFromDisputeId($disputeId);
        
        $returnValue= $this->mapper->checkReturnTable($getOID['order_item_id']);
        if($returnValue){
            $disputeDetail = $this->mapper->getOrderDetailsForDispute($disputeId);
        }else{
            $disputeDetail = $this->mapper->getReturnDetailsForDispute($disputeId);
        }
        //$disputeDetail = $this->mapper->getOrderDetailsForDispute($disputeId);
        $disputeMessages = $this->mapper->getDisputeMessages($disputeId, $this->userName->userId);
        $dropDown = $this->mapper->createDropDownActions($disputeId, $this->userName->userId); //Drop down actions e.g.-escalate,close,solve etc
        $remark = $this->mapper->createRemark($disputeId, $this->userName->userId);    //Generate remark e.g.- A has raised a dispute against B
        $documents = $this->mapper->getAttachedDocuments($disputeId, $this->userName->userId);

        $this->view->remark = $remark;
        $this->view->dropDown = $dropDown;
        $this->view->disputeDetail = $disputeDetail;
        $this->view->disputeMessages = $disputeMessages;
        $this->view->documents = $documents;
	$this->view->userId = $this->userName->userId;
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

//File Upload is done using a plugin called AjaxFileUpload. For customeropenadisputeAction.
    public function doajaxfileuploadAction() {
        $limit = 8000;
        define("MAX_FILE_SIZE", $limit);
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
            $error = 'File size exceeds 8M.';
        }//End elseif
        else {
            //$file_size = fileize($_FILES['filesToUpload']['tmp_name']);
            if($file_size>=$limit){
                $error ='File size exceeds 8M.';
            }
            $exp = explode(".",$_FILES['fileToUpload']['name']);
			
            if($exp[1]=='jpg'||$exp[1]=='jpeg'||$exp[1]=='pdf'||$exp[1]=='doc'||$exp[1]=='docx'||$exp[1]=='png'||$exp[1]=='bmp'||$exp[1]=='gif'){
				$abc = $exp[1];
            }else{
                $error = 'Please upload a file of valid format.';
            }
            $msg .= $dir . "/" . $filename . "." . $exp[1] . ":" . filesize($_FILES['fileToUpload']['tmp_name']) . ":" . $filename . "." . $exp[1];
            move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $dir . "/" . $filename . "." . $exp[1]);

            //$msg .= " File Name: " . $_FILES['fileToUpload']['name'] . ", ";
            //$msg .= " File Size: " . @filesize($_FILES['fileToUpload']['tmp_name']); 
            //for security reason, we force to remove all uploaded file
            //@unlink($_FILES['fileToUpload']);		
        }//End else		
        echo "{";
        echo "error: '" . $error . "',\n";
        echo "msg: '" .$msg. "'\n";
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
        $limit = 8000;
        define("MAX_FILE_SIZE", $limit);
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $userApiKey = $_SESSION['SESSION']['ApiKey'];
        $filename = $_POST['datatext'];  //file name
        $dirnamess = $_POST['randname']; //Temp directory name in $dir
        $dir = $this->genObj->getDisputeUploadedFilesPath($dirnamess);
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
            $error = 'File size exceeds 8M.';
        }//End elseif
        else {
            if($file_size>=$limit){
                $error ='File size exceeds 8M.';
            }
            $exp = explode(".", $_FILES['fileToUpload']['name']);
            if($exp[1]=='jpg'||$exp[1]=='jpeg'||$exp[1]=='pdf'||$exp[1]=='doc'||$exp[1]=='docx'||$exp[1]=='png'||$exp[1]=='bmp'||$exp[1]=='gif'){
            }else{
                $error = 'Please upload a file of valid format.';
            }
            $exp = explode(".", $_FILES['fileToUpload']['name']);
            $this->mapper->insertFiles($filename, $dirnamess, $this->userName->userId, $exp[1]);
            $msg .= $dir . "/" . $filename . "." . $exp[1] . ":" . filesize($_FILES['fileToUpload']['tmp_name']) . ":" . $filename . "." . $exp[1];
            move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $dir . "/" . $filename . "." . $exp[1]);
			//$data = '<li><a title="'.$filename.'" href="">'.$filename.'</a></li>';
			//$this->view->fileData = $data;
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

	public function commentAction(){
		$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->mapper->insertCommentAndReturnStructure($_POST,$this->userName->userId);
	}

		public function getallcommentAction(){
		$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->mapper->getAllcomments($_POST,$this->userName->userId);
	}
	
	public function getuserimageAction(){
		$this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
		//echo $this->userName->userId;
        $this->mapper->getuserImage($this->userName->userId);
		
	}

}

?>
