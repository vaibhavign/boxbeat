<?php 
class RequestController extends Zend_Controller_Action {
    private $notification;
    private $order_model;
    
	function init() {
		$this->request_model = new Default_Model_RequestMapper();
                $this->order_model = new Admin_Model_OrdersMapper();
                $this->notification = new Notification();
 
		//$this->mapper=new Admin_Model_ConversationMapper();
		$userName = new Zend_Session_Namespace('USER');
		Zend_Layout::getMvcInstance()->setLayout('secureadmin');
        	Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH . '/modules/secure/layouts');
		$this->view->headLink()->appendStylesheet('/css/secure/admin.css');
		$this->view->headScript()->appendFile('/jscript/common/o2olib.js', 'text/javascript');
	        $this->view->headScript()->appendFile('/jscript/common/white.js', 'text/javascript');
                $this->view->headScript()->appendFile('/jscript/default/changerequest.js', 'text/javascript');
//		$this->view->headLink()->appendStylesheet('/css/myaccount/request/request.css');
                $this->view->headScript()->appendFile('/jscript/common/pulse.js', 'text/javascript');

		$this->session = new Zend_Session_Namespace('SESSION');

		$this->userName = new Zend_Session_Namespace('USER');
		//$this->userName->userId = '4';
	        if ($this->userName->userId == '') {
	            $this->_redirect(HTTP_SECURE . '/login');
	        }

//		$rqid = $this->_request->getParam('rqid');
//		if($rqid!=''){
//			$requestType = $this->request_model->getRequestType($rqid);
//			if($requestType==0){
//				header("Location: /inbox/#requestforcancellationdetails/rqid/".encryptLink($requestType));
//			}elseif($requestType==1){
//				header("Location: /inbox/#shippingaddresschange/rqid/".encryptLink($requestType));
//			}elseif($requestType==2){
//				header("Location: /inbox/#respondtoreturnrequest/rqid/".encryptLink($requestType));
//			
//			}
//		}
	}

	public function indexAction(){
		$this->view->headScript()->appendFile('/jscript/common/bbq.js');
	}
	
	/*public function customerrequestlistingAction(){
		$userName = new Zend_Session_Namespace('USER');
		$this->userid = $userName->userId;
		$userid = $this->userid;
		//$this->_request->getParam('chat_id');
		$chat_id = '1';
		$requestListing = $this->request_model->requestListing($chat_id,$userid);
		
		 $paginator = Zend_Paginator::factory($requestListing);

        $currentPage = 1;
        //Check if the user is not on page 1
        $i = $url['p'];
        if (!empty($i)) { //Where i is the current page
            $currentPage = $url['p'];
        }

        //Set the properties for the pagination
        $paginator->setItemCountPerPage(5);

        $paginator->setPageRange(4);

        $paginator->setCurrentPageNumber($currentPage);
		$this->view->paginator = $paginator;
	}
	
	public function customerreturnrequestdetailsAction(){
		//$chatid = $this->_request->getParam('chatid');
		$userName = new Zend_Session_Namespace('USER');
		$chatid = '43';
		$messageData = $this->mapper->getConversationDetail($chatid);
		$this->view->messageData = $messageData;
		$this->view->userName=$userName->userDetails[0]['userName'];
		$this->view->userId=$userName->userId;
		$this->view->cid=$chatid;
		$this->view->email = $messageData[0]['guestemail'];
		$this->view->userfullname=$messageData[0]['guestname'];
		/*echo '<pre>';
		print_r($messageData);
		exit;
		if($messageData[0]['from']==$userName->userId)
		{
			$this->view->userfullname=$messageData[0]['userfrom'];
			$this->view->email=$userName->userDetails[0]['user_email_address'];
			if($messageData[0]['userfrom']=='')
			{
			$this->view->userfullname=$messageData[0]['guestname'];
			$this->view->location='';
			}
			else
			{
			$this->view->location=$this->mapper->getLocationNamefromId($userName->userDetails[0]['user_location']);
			}
		}
		else
		{
		
		}
	}*/

	public function requestdetailAction(){
		$this->_helper->layout->disableLayout();
                $param = $this->_request->getParams();
                $request_id = decryptLink($param['rqid']);
                //$requestType='2';
                //$request_id = '1';
		if($request_id!=''){
		$requestType = $this->request_model->getRequestType($request_id);
                   if($requestType=='0'){ //cancellation
                            //header("Location: /inbox/#requestforcancellationdetails/rqid/".encryptLink($requestType));
                        $userName = new Zend_Session_Namespace('USER');
                        //$request_id = decryptLink($this->_request->getParam('rqid'));
                        //$this->request_model->changeReadByInChatTable($userName->userId,$request_id);
                        $dropdown = $this->request_model->showDropDown($request_id,$userName->userId);
                        
                       //echo $this->mapper->lastQuery(); $this->view->dropDown
                        $cancellationData = $this->request_model->shippingAddressChangeData($request_id);
                         
                        $this->view->cancellationData = $cancellationData;

                        $cancellationMessageData = $this->request_model->requestMessagesData($request_id, $userName->userId);
                        $this->view->cancellationMessageData = $cancellationMessageData;

                        $this->view->userId = $userName->userId;
                        $this->view->order_model = new Admin_Model_OrdersMapper();
                        $this->view->requestType = $requestType;
                    }
                    
                    elseif($requestType=='1'){ //shipping address change
				//header("Location: /inbox/#shippingaddresschange/rqid/".encryptLink($requestType));
                            $userName = new Zend_Session_Namespace('USER');
                            //$request_id = decryptLink($this->_request->getParam('rqid'));

                            $orderData = $this->request_model->shippingAddressChangeData($request_id);
                            
                             $this->view->dropDown =$this->request_model->showDropDown($request_id,$userName->userId);
                            // $this->view->dropDown
                            
                           // echo $this->mapper->lastQuery();
                            $this->view->orderData = $orderData;
                            $messageData = $this->request_model->requestMessagesData($request_id, $userName->userId);
                            $this->view->messageData = $messageData;
                            $this->view->userId = $userName->userId;
                            $this->view->requestType = $requestType;
                            
                            
                    }
                    
                    elseif($requestType=='2'){ //return requested
				//header("Location: /inbox/#respondtoreturnrequest/rqid/".encryptLink($requestType));
                            
                            $userName = new Zend_Session_Namespace('USER');
                            //$this->request_model->changeReadByInChatTable($userName->userId,$request_id);
                            //$requestId = decryptLink($this->_request->getParam('rqid'));
                            $returnDetails = $this->request_model->getReturnDetailFromReturnId($request_id);
                            
                            $returnMessageData = $this->request_model->requestMessagesData($request_id, $userName->userId);
                            $this->view->returnMessageData = $returnMessageData;
                            $this->view->returnDetails = $returnDetails;

                            $this->view->userId = $this->userName->userId;
                            $this->view->requestType = $requestType;
			
                    }
		}
        }
        
   public function respondtoreturnrequestAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if ($_POST['id'] == 'acceptReturn') { //Return request accepted
            $data = $this->_request->getParams($_POST);
            $this->request_model->respondToReturnAccepted($data);
            
            //Email Notication - notify customer after store owner accepts a return request
            $orderData = $this->order_model->getEverythingAboutAnOrder($data['orderItemId']);
            $orderData['customer_request_detail_page'] = HTTP_SERVER.'/request/#requestdetail/rqid/'.encryptLink($data['request_id']);
            $this->notification->triggerFire(43, array('to_id' => $orderData[0]['buyer_id'], 'orderData' => $orderData));
            
            exit;
        }//End if
        elseif ($_POST['id'] == 'rejectReturn') { //Return request rejected
            $data = $this->_request->getParams($_POST);
            $this->request_model->respondToReturnRejected($data);
            
            //Email Notication - notify customer after store owner rejects a return request
            $orderData = $this->order_model->getEverythingAboutAnOrder($data['orderItemId']);
            $orderData['customer_detail_page_link'] = HTTP_SERVER.'/myaccount/buyer/#return-item-details/rid/'.encryptLink($data['request_id']);
            
            $this->notification->triggerFire(42, array('to_id' => $orderData[0]['buyer_id'], 'orderData' => $orderData));
            exit;
        }//end elseif
    }
    
    public function addresschangerequestedAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        if ($_POST['id'] == 'acceptRequest') {  //Seller accepted the change in shipping address request
            $addressbook_id = $_POST['addressbook_id'];
            $requestid = $_POST['requestid'];
            $orderItemId = $_POST['orderItemId'];
            $this->request_model->changeInShippingAddressAccepted($requestid, $addressbook_id, $orderItemId);
            
            //Email Notication - Notify customer when store owner approves an address change request
            $orderData = $this->order_model->getEverythingAboutAnOrder($orderItemId);
            $orderData['customer_detail_page_link'] = HTTP_SERVER.'/myaccount/buyer/#return-item-details/rid/'.encryptLink($data['request_id']);
            $cityname = $this->order_model->getCityName($orderData['city']);
            $statename = $this->order_model->getStateName($orderData['state']);
            $orderData['old_address'] = $orderData['address'].','.$cityname.','.$statename.','.$orderData['pincode'];
            $orderData['new_address'] = $this->buyer_model->getAddressInString($orderData['request_address_book_id']);
            
            //from Goo2o to buyer
            $this->notification->triggerFire(55, array('to_id' => $orderData[0]['buyer_id'], 'orderData' => $orderData));
            //from store owner to buyer
            $this->notification->triggerFire(56, array('to_id' => $orderData[0]['buyer_id'],'from_id'=>$orderData[0]['seller_id'],'orderData' => $orderData));
            //from Goo2o to seller
            $this->notification->triggerFire(57, array('to_id' => $orderData[0]['seller_id'], 'orderData' => $orderData));
            
            
            
            exit;
        }

        if ($_POST['id'] == 'rejectRequest') { //Seller rejected the change in shipping address request
            $requestid = $_POST['requestid'];
            $this->request_model->changeInShippingAddressRejected($requestid);
            
            //Email Notication - Notify customer when store owner rejects an address change request
            $orderData = $this->order_model->getEverythingAboutAnOrder($orderItemId);
            $orderData['customer_detail_page_link'] = HTTP_SERVER.'/myaccount/buyer/#return-item-details/rid/'.encryptLink($data['request_id']);
            $cityname = $this->order_model->getCityName($orderData['city']);
            $statename = $this->order_model->getStateName($orderData['state']);
            $orderData['old_address'] = $orderData['address'].','.$cityname.','.$statename.','.$orderData['pincode'];
            $orderData['new_address'] = $this->buyer_model->getAddressInString($orderData['request_address_book_id']);
            
            //from Goo2o to buyer
            $this->notification->triggerFire(53, array('to_id' => $orderData[0]['buyer_id'], 'orderData' => $orderData));
            //from seller to buyer
            $this->notification->triggerFire(54, array('to_id' => $orderData[0]['buyer_id'],'from_id'=>$orderData[0]['seller_id'], 'orderData' => $orderData));
            
            
            exit;
        }
        
    }
    
    public function cancellationrequestedAction(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        if ($_POST['id'] == 'acceptCancellation') { //Seller accepted the cancellation request
            $addressbook_id = $_POST['addressbook_id'];
            $requestid = $_POST['requestid'];
            $orderItemId = $_POST['orderItemId'];
            $this->request_model->requestForCancellationAccepted($requestid, $addressbook_id, $orderItemId);
            
            //Email Notication - notify customer after store owner accepts a cancellation request
            $orderData = $this->order_model->getEverythingAboutAnOrder($orderItemId);
            $orderData['customer_detail_page_link'] = HTTP_SERVER.'/myaccount/buyer/#return-item-details/rid/'.encryptLink($data['request_id']);
            //from Goo2o to buyer
            $this->notification->triggerFire(44, array('to_id' => $orderData[0]['buyer_id'], 'orderData' => $orderData));
            
            //from seller to buyer
            $this->notification->triggerFire(46, array('to_id' => $orderData[0]['buyer_id'],'from_id'=>$orderData[0]['seller_id'], 'orderData' => $orderData));
            exit;
        }
        

        if ($_POST['id'] == 'rejectCancellation') { //Seller rejected the cancellation request
            $requestid = $_POST['requestid'];
            $this->request_model->requestForCancellationRejected($requestid);
             //Email Notication - notify customer after store owner rejects a cancellation request
            $orderData = $this->order_model->getEverythingAboutAnOrder($_POST['orderItemId']);
            $orderData['customer_detail_page_link'] = HTTP_SERVER.'/myaccount/buyer/#return-item-details/rid/'.encryptLink($data['request_id']);
            $this->notification->triggerFire(45, array('to_id' => $orderData[0]['buyer_id'], 'orderData' => $orderData));
            
            
            exit;
        }
    }

}
?>
