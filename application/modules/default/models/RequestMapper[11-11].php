<?php

class Default_Model_RequestMapper extends DML {

    public $buyerMapper ;
    function __construct() {
        parent::__construct();
        $this->db = Zend_Db_Table::getDefaultAdapter();
        $this->buyerMapper = new Myaccount_Model_BuyerMapper();
    }

    public function requestListing($chat_id, $userid) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = "select * from chat where message_type='2' and user1='" . $userid . "'";
        $getAllRecords = $db->query($sql);
        $userdata = $getAllRecords->fetchAll();

        for ($i = 0; $i < count($userdata); $i++) {
            $order_item_id = $userdata[$i]['order_item_id'];
            $orderidsql = $db->query("SELECT order_id from order_item where order_item_id='" . $order_item_id . "'");
            $getorderid = $orderidsql->fetchAll();
            $userdata[$i]['order_id'] = $getorderid[0]['order_id'];

            $orderid = $getorderid[0]['order_id'];
            $ordersql = $db->query("select * from orders where order_id='" . $orderid . "'");
            $orderDetail = $ordersql->fetchAll();
            $userdata[$i]['payment_module'] = $orderDetail[0]['payment_module'];

            $userid = $userdata[0]['user1'];
            $username = $db->query("SELECT * FROM user where id='" . $userid . "'");
            $getusername = $username->fetchAll();
            $userdata[$i]['username'] = $getusername[0]['user_full_name'];


            $userdata[$i]['time'] = $this->getNewTimeFormat($userdata[$i]['created_date']);
        }
        return $userdata;
    }

    public function getNewTimeFormat($getTime) {
        $timeGone = time() - $getTime;
        if ($getTime != '') {
            if ($timeGone == 0) {
                $sendTime = 'Just now';
            }
            if ($timeGone <= 60 && $timeGone != 0) {
                $sendTime = $timeGone . ' second' . ($timeGone > 1 ? 's' : '') . ' ago';
            } elseif ($timeGone <= 3600 && $timeGone > 60) {
                $sendTime = ceil($timeGone / 60) . ' minute' . (ceil($timeGone / 60) > 1 ? 's' : '') . ' ago';
            } elseif ($timeGone <= 86400 && $timeGone > 3600) {
                $sendTime = ceil($timeGone / 86400) . ' hour' . (ceil($timeGone / 86400) > 1 ? 's' : '') . ' ago';
            } elseif ($timeGone <= 1296000 && $timeGone > 86400) {
                $sendTime = ceil($timeGone / 86400) . ' day' . (((ceil($timeGone / 86400)) > 1) ? 's' : '') . ' ago';
            } elseif ($timeGone > 1296000) {
                $sendTime = date("jS F, Y", $selectAllFeedsFromDeal->fields['date_modified']);
            }
        } else {
            $sendTime = "Not Revealed";
        }
        return $sendTime;
    }

    public function getRequestType($rqid) {
        $result = $this->select('*')
                ->from('order_request')
                ->where(array('request_id' => $rqid))
                ->get()
                ->rowArray();
        return $result['request_type'];
    }

    public function requestForCancellationAccepted($requestid, $addressbook_id, $orderItemId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $shipment = $db->query("delete from order_shipment where order_item_id = '" . $orderItemId . "'");
        /* Change the request_type in order_request to 1 */
        $selectsql = $db->query("select * from order_request where request_id='" . $requestid . "'");
        $getrecords = $selectsql->fetchAll();

        if ($getrecords[0]['request_status'] == 0) {
            $requesttype_change = $db->query("UPDATE order_request SET request_status='1' where request_id='" . $requestid . "'");

            $updateStatus = array('order_item_status' => ORDER_STATUS_CANCELLED,
                'order_sub_status_id' => SELLER_SUBSTATUS_ORDER_CANCELLATION_ACCEPTED,
                'buyer_substatus' => BUYER_SUBSTATUS_ORDER_CANCELLATION_ACCEPTED);
            $this->updateRecord('order_item', $updateStatus, array('order_item_id' => $orderItemId));

            // $updateOrder_item = $db->query("UPDATE order_item SET order_address_id='" . $addressbook_id . "',order_item_status='2',order_sub_status_id='20',buyer_substatus = '43' where order_item_id='" . $orderItemId . "'");
        }//End if

        exit;
    }

    public function requestForCancellationRejected($requestid) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $rs1 = $this->select('request_item_id')
                ->from('order_request')
                ->where('request_id', $requestid)
                ->get()
                ->rowArray();
        $updateStatus = $this->getPrevStatusFromOrderItemTable($rs1['request_item_id']);
        $updateStatus['request_status'] = 2;
        $updateStatus['expire'] = 'TRUE';

        $this->updateRecord('order_request', $updateStatus, array('request_id' => $requestid));
         
        $data = $this->buyerMapper->updateTableOrderItemWhenRequestCancellationDeclined($rs1['request_item_id'], 2);
      
    }

    /* public function updateOrderItemWhenAddressChangeRequestDeclined($orderItemId, $orderPrevStatus, $orderPrevSellerSubStatus, $userType=2) {
      switch ($orderPrevStatus) {
      case ORDER_STATUS_OPEN:
      switch ($orderPrevSellerSubStatus) {
      case 1:  // neither requested for shipping address change nor for cancellation
      case 55:  // cancellation requested
      case 100:  // cancellation rejected
      case 104:  // shipping address change request rejected
      case 7:
      $updatedata = array('order_sub_status_id' => 104,
      'buyer_substatus' => 105);

      break;
      }//End inner switch
      break;
      case ORDER_STATUS_PAYMENT_RECEIVED:
      switch ($orderPrevSellerSubStatus) {
      case 3:  // shipment not created yet and shipping address change requested
      case 114: // shipment not created and cancellation requested
      case 108:  // shipment not created and cancellation request rejected
      case 118:  // shipment not created and shipping address change requested declinced once
      $updatedata = array('order_sub_status_id' => 112);
      if ($userType == 2) {
      $updatedata['buyer_substatus'] = 113; // when seller cancelled the request for change in shipping address
      } else {
      $updatedata['buyer_substatus'] = 153; // when buyer cancelled the request for change in shipping address.
      }

      break;
      case 4 :  // shipment created for  all the quantity and shipping address change requested
      case 124: // shipment created for  all the quantity and cancellation requested
      case 116: // shipment created for  all the quantity and cancellation request rejected
      $updatedata = array('order_sub_status_id' => 122,
      'buyer_substatus' => 123);
      break;
      case 5:  // shipment created for x numbers of items and shipping address change requested
      case 128: // shipment created for x numbers of items and cancellation requested
      case 130: // shipment created for x numbers of items and cancellation request rejected
      $updatedata = array('order_sub_status_id' => 136,
      'buyer_substatus' => 137);
      break;
      }//End inner switch

      break;
      }//End outer switch

      $this->updateRecord('order_item', $updatedata, array('order_item_id' => $orderItemId));
      }
     */

    public function showDropDown($requestId, $userId) {
        return $this->select('*')
                        ->from('order_request')
                        ->where(array('request_id' => $requestId, 'request_seller_id' => $userId))
                        ->where('expire', 'FALSE', 'request_status', '0')
                        ->get()
                        ->rowArray();
        //  return $this->getWhere('order_request', array('request_id' => $requestId, 'expire' => 'FALSE', 'request_seller_id' => $userId))->rowArray();
    }

    public function shippingAddressChangeData($request_id) {

        $db = Zend_Db_Table::getDefaultAdapter();
        $getRequest = $this->select('*')
                ->from('order_request as ore')
                ->join('order_item as oi', 'ore.request_item_id=oi.order_item_id', 'inner')
                ->join('orders as o', 'o.order_id=oi.order_id', 'inner')
                ->join('address_book as ab', 'ore.address_book_id=ab.address_book_id', 'left')
                ->where(array('ore.request_id' => $request_id))
                ->get()
                ->resultArray();

        // get seller fullname
        $sellersql = $db->query("select * from user where id='" . $getRequest[0]['request_seller_id'] . "'");
        $getname = $sellersql->fetchAll();
        $seller_name = $getname[0]['user_full_name'];
        $getRequest[0]['seller_name'] = $seller_name;

        //get state name from statid 
        $state = $db->query("select * from state where id='" . $getRequest[0]['state'] . "'");
        $getstate = $state->fetchAll();
        $state_name = $getstate[0]['state_name'];
        $getRequest[0]['state_name'] = $state_name;

        // get city name from cityid
        $city = $db->query("select * from cities where id='" . $getRequest[0]['city'] . "'");
        $getcity = $city->fetchAll();
        $city_name = $getcity[0]['cityname'];
        $getRequest[0]['cityname'] = $city_name;

        return $getRequest;
    }

    public function requestMessagesData($request_id, $userId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $messagesql = $db->query("select * from request_messages where request_id='" . $request_id . "'");
        $allmessage = $messagesql->fetchAll();
        
        $data = array();
        $i = 0;
        foreach ($allmessage as $messagekey => $messageval) {
            $data[$i]['message'] = $messageval['message'];
            $data[$i]['time'] = $messageval['time'];
               $data[$i]['message_by'] = $messageval['message_by'];

            if ($messageval['message_by'] == $userId) {
                $data[$i]['name'] = 'me';
            } else {
                $usersql = $db->query("select * from user where id='" . $messageval['message_by'] . "'");
                $userRecord = $usersql->fetchAll();
                $data[$i]['name'] = $userRecord[0]['user_full_name'];
            }
            $i++;
        }

        $result = $this->select('*')
                ->from('order_request')
                ->where('request_id', $request_id)
                ->get()
                ->rowArray();

        $data['request_description'] = $result['request_description'];
        return $data;
    }

    public function changeInShippingAddressAccepted($requestid, $addressbook_id, $orderItemId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $updateStatus = $this->getPrevStatusFromOrderItemTable($orderItemId);
        $this->updateRecord('order_request', $updateStatus, array('request_id' => $requestid));



        $selectsql = $db->query("select * from order_request where request_id='" . $requestid . "'");
        $getrecords = $selectsql->fetchAll();

        if ($getrecords[0]['request_status'] == 0) {

            $requesttype_change = $db->query("UPDATE order_request SET request_status='1' where request_id='" . $requestid . "'"); // Change the request_type in order_request to 1

            $getRow = $db->query("SELECT * FROM address_book where address_book_id='" . $addressbook_id . "'"); //get details from address book
            $getAll = $getRow->fetchAll();

            $cityid = $getAll[0]['city'];
            $cityname = $db->query("Select cityname from cities where id = '" . $cityid . "'");
            $getCityName = $cityname->fetchAll();
            $getAll[0]['cityname'] = $getCityName[0]['cityname'];


            $stateid = $getAll[0]['state'];
            $sql = "SELECT state_name FROM state where id = '" . $stateid . "'";
            $statename = $db->query($sql);
            $getStateName = $statename->fetchAll();
            $getAll[0]['statename'] = $getStateName[0]['state_name'];

            $getAll[0]['description'] = $description;


            $allRecords = $db->query("SELECT * FROM order_addresses where address_book_id='" . $addressbook_id . "'"); // check whether addressbook_id exits in order_addresses. if it does exits then do not insert because it will duplicate the data
            $fetchRecords = $allRecords->fetchAll();

            $prev = $this->select('order_prev_status,order_prev_seller_substatus')
                    ->from('order_request')
                    ->where(array('request_id' => $requestid, 'expire' => 'FALSE'))
                    ->get()
                    ->rowArray();
            $updatedata  = $this->buyerMapper->updateTableOrderItemWhenShippingAddressChangeRequestAccepted($getrecords[0]['request_item_id']);
           


            if (empty($fetchRecords)) { //Insert in order address if empty
                $orderAddressInsert = $db->query("INSERT INTO order_addresses(fullname,address_book_id,address,zipcode,city,state,customer_id,phone,officeaddress,reason_change) VALUES('" . $getAll[0]['fullname'] . "','" . $addressbook_id . "','" . $getAll[0]['address'] . "','" . $getAll[0]['zipcode'] . "','" . $getAll[0]['cityname'] . "','" . $getAll[0]['statename'] . "','" . $getAll[0]['customers_id'] . "','" . $getAll[0]['phone'] . "','" . $getAll[0]['officeaddress'] . "','" . $getAll[0]['description'] . "')");

                $id = $db->query("SELECT LAST_INSERT_ID() FROM order_addresses");
                $getid = $id->fetchAll();
                $totalOrderItem = $this->getTotalOrderItem($orderItemId);

                $updatedata['order_address_id'] = $getid[0]['LAST_INSERT_ID()'];
            }//End if 
            else {
                $totalOrderItem = $this->getTotalOrderItem($orderItemId);
                //$orderid = $fetchRecords[0]['order_address_id'];
                $updatedata['order_address_id'] = $fetchRecords[0]['order_address_id'];
            }//End else
            
            $this->updateRecord('order_request', array('expire' => 'TRUE'), array('request_id' => $requestid));
            //  echo $this->lastQuery();exit;
        }//End if request status==0
    }

    public function getPrevStatusFromOrderItemTable($orderItemId) {
        $result = $this->select('order_item_status as order_prev_status,order_sub_status_id as order_prev_seller_substatus,buyer_substatus as order_prev_buyer_substatus')
                ->from('order_item')
                ->where('order_item_id', $orderItemId)
                ->get()
                ->rowArray();
        return $result;
    }

    public function getTotalOrderItem($orderItemId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $results = $this->select('*')
                ->from('order_shipment as os')
                ->where('order_item_id', $orderItemId)
                ->get()
                ->resultArray();

        for ($i = 0; $i < count($results); $i++) {
            $shipmentTotal = $results[$i]['order_shipment_total'];
            $data = $this->select('*')
                    ->from('order_item')
                    ->where('order_item_id', $results[$i]['order_item_id'])
                    ->get()
                    ->resultArray();

            $shipmentLeft = $data[0]['order_shipment_done'] - $shipmentTotal;

            $update = $db->query("UPDATE order_item SET order_shipment_done='" . $shipmentLeft . "' where order_item_id='" . $orderItemId . "'");
        }//End for
        $shipment = $db->query("delete from order_shipment where order_item_id = '" . $orderItemId . "' ");
        return $newOrderShipmentDone;
    }

    public function changeInShippingAddressRejected($requestid) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $rs1 = $this->select('request_item_id')
                ->from('order_request')
                ->where('request_id', $requestid)
                ->get()
                ->rowArray();
        $updateStatus = $this->getPrevStatusFromOrderItemTable($rs1['request_item_id']);
        $updateStatus['request_status'] = 2;
        $updateStatus['expire'] = 'TRUE';

        $this->updateRecord('order_request', $updateStatus, array('request_id' => $requestid));
        $this->buyerMapper->updateTableOrderItemWhenRequestChangeAddressDeclined($rs1['request_item_id'],2);
    }

    public function getReturnDetailFromReturnId($requestId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $result = $this->select('*')
                ->from('order_request as orq')
                ->join('order_returns as orn', 'orn.order_item_id= orq.request_item_id', 'inner')
                ->join('orders as o', 'o.order_id=orn.order_id', 'inner')
                ->where(array('orq.request_id' => $requestId))
                ->get()
                ->rowArray();
        $statusId = $result['result_status'];
        $data = $this->select('status')
                ->from('order_status')
                ->where('id', $statusId)
                ->get()
                ->rowArray();
        $result['status_name'] = $data['status'];

        $buyer_name = $this->select('*')
                ->from('user')
                ->where('id', $result['request_buyer_id'])
                ->get()
                ->rowArray();
        $result['buyer_name'] = $buyer_name['user_full_name'];

        $seller_name = $this->select('*')
                ->from('user')
                ->where('id', $result['request_seller_id'])
                ->get()
                ->rowArray();
        $result['seller_name'] = $seller_name['user_full_name'];
        return $result;
    }

    public function respondToReturnAccepted($data) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $updaterequest = $db->query("UPDATE order_request SET request_status='1' where request_id='" . $data['requestId'] . "'");
        $db->query("UPDATE order_returns SET is_visible='1' where order_item_id='" . $data['orderItemId'] . "'");
        // If the return type is replacement; we need to create a new order with the same order id. 
        $acceptedRequestForReplacement = $this->select('oi.*,orr.return_id,orr.quantity')
                ->from('order_item as oi')
                ->join('order_returns as orr', 'oi.order_item_id=orr.order_item_id', 'inner')
                ->where(array('orr.is_visible' => RETURN_VISIBILITY_STATUS_ACCEPTED, 'orr.return_type' => RETURN_TYPE_REPLACEMENT))
                ->get()
                ->rowArray();
        //echo $this->lastQuery();
        if (!empty($acceptedRequestForReplacement)) {
            // echo 'ankit';
            $acceptedRequestForReplacement['order_item_status'] = ORDER_STATUS_PAYMENT_RECEIVED;
            $acceptedRequestForReplacement['order_sub_status_id'] = ORDER_SUBSTATUS_PAYMENT_RECEIVED;
            $acceptedRequestForReplacement['buyer_substatus'] = BUYER_SUBSTATUS_ORDER_SHIPMENT_CREATED;
            $acceptedRequestForReplacement['order_item_total'] = $acceptedRequestForReplacement['quantity'];
            $acceptedRequestForReplacement['order_shipment_done'] = 0;
            unset($acceptedRequestForReplacement['quantity'], $acceptedRequestForReplacement['order_item_id'], $acceptedRequestForReplacement['modified_on'], $acceptedRequestForReplacement['ocr_id'], $acceptedRequestForReplacement['ocr_details']);
            $lastInserted = $this->insertRecord('order_item', $acceptedRequestForReplacement);
            $newResult = $this->select('*')
                    ->from('order_shipping_policy as osp')
                    ->where('osp.order_item_id', $acceptedRequestForReplacement['order_item_id'])
                    ->get()
                    ->rowArray();
            unset($newResult['id'], $newResult['order_item_id']);
            $newResult['order_item_id'] = $lastInserted;
            $this->insertRecord('order_shipping_policy', $newResult);
        }


        $feedback = $this->select('*')
                ->from('order_feedback')
                ->where('order_item_id', $data['orderItemId'])
                ->get()
                ->rowArray();

        if (!empty($feedback)) {
            //echo "UPDATE order_item SET order_item_status='4',order_sub_status_id='144',buyer_substatus='145' where order_item_id='".$data['orderItemId']."'";exit;
            $result = $this->select('order_prev_status')
                    ->from('order_request')
                    ->where('request_id', $requestId)
                    ->get()
                    ->rowArray();

            $updateOrderItem = $db->query("UPDATE order_item SET order_item_status='" . $result['order_prev_status'] . "',order_sub_status_id='144',buyer_substatus='145' where order_item_id='" . $data['orderItemId'] . "'");
            exit;
        } //Enf id
        else {
            $result = $this->select('order_prev_status')
                    ->from('order_request')
                    ->where('request_id', $requestId)
                    ->get()
                    ->rowArray();

            $updateOrderItem = $db->query("UPDATE order_item SET order_item_status='" . $result['order_prev_status'] . "',order_sub_status_id='148',buyer_substatus='149' where order_item_id='" . $data['orderItemId'] . "'");
            exit;
        }//End else
    }

    public function respondToReturnRejected($data) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $updaterequest = $db->query("UPDATE order_request SET request_status='2' where request_id='" . $data['requestId'] . "'");

        $feedback = $this->select('*')
                ->from('order_feedback')
                ->where('order_item_id', $data['orderItemId'])
                ->get()
                ->rowArray();
        $this->buyerMapper->updateTableOrderItemWhenReturnRequestDeclined($data['orderItemId'], 2);
       

    }
    
    public function changeReadByInChatTable($userId,$reuestId){
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql =$db->query("select * from chat where i_id='".$reuestId."'");
        $chatResult = $sql->fetchAll();
        
        if($chatResult[0]['readby']==0){ //page viewed by none of them
            $data = array('readby'=>$userId);
            $this->updateRecord('chat', $data, 'i_id',$requestId);
        }elseif($chatResult[0]['readby']==-1){  //page viewed by both seller and buyer
            //Do Nothing
        }else{          //page viewed by one and that user's id is there
            if($userId==$chatResult[0]['readby']){
                //Do Nothing
            }elseif($userId != $chatResult[0]['readby']){
                $data1 = array('readby'=>-1);
                $this->updateRecord('chat', $data1, 'i_id',$requestId);
             }
        }
    }

}

?>
