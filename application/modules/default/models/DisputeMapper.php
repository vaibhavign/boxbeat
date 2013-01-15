<?php

class Default_Model_DisputeMapper extends DML {

    private $buyerMapper;
    public $genObj;

    function __construct() {
        parent::__construct();
        $this->buyerMapper = new Myaccount_Model_BuyerMapper();
        $this->genObj = new general();
        //$this->db = Zend_Db_Table::getDefaultAdapter();
    }

//DISPUTES
//@author : Harpreet Singh
// Used for Disputes regarding an Order
// Creation Date : 22-09-2011
// Created By : Harpreet Singh 

    public function validateOrderItem($orderItemId, $userId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $result = $db->query("Select * from order_item as oi join orders as o where oi.order_id=o.order_id and oi.order_item_id='" . $orderItemId . "'");
        $getRecord = $result->fetchAll();
        if ($getRecord[0]['order_item_owner'] == $userId || $getRecord[0]['customer_id'] == $userId) { //order item do belong to customer(Buyer) or order_item_owner(Seller)
            echo 'oid/' . encryptLink($orderItemId);
            exit;
        }//End if 
        else {
            echo '1'; //to display error
            exit;
        }//End else
    }

// End function
    //Get reasons and related examples
    public function getReasonsAndExample($itemStatus, $orderItemId, $userId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        // echo "SELECT * FROM dispute_master where order_status_id='" . $itemStatus . "'";exit;
        $result = $db->query("SELECT * FROM dispute_master where order_status_id='" . $itemStatus . "'");
        $getReasonAndExample = $result->fetchAll();

        $orderItemData = $this->select('*')
                ->from('order_item as oi')
                ->join('orders as o', 'o.order_id=oi.order_id', 'inner')
                ->where('oi.order_item_id', $orderItemId)
                ->get()
                ->rowArray();

        if ($userId == $orderItemData['customer_id']) {
            $getReasonAndExample = $this->select('*')
                    ->from('dispute_master')
                    ->where(array('order_status_id' => $itemStatus, 'user_type' => '3'))
                    ->get()
                    ->resultArray();
        }//End if 
        elseif ($userId == $orderItemData['order_item_owner']) {
            $getReasonAndExample = $this->select('*')
                    ->from('dispute_master')
                    ->where(array('order_status_id' => $itemStatus, 'user_type' => '2'))
                    ->get()
                    ->resultArray();
        }//End elseif
        //echo '<pre>'; print_r($getReasonAndExample); exit;
        $i = 0;
        foreach ($getReasonAndExample as $items) {
            if ($items['reason_example'] != '') {
                $pieces = explode('_', $items['reason_example']);
                //echo '<pre>';print_r($pieces); 
                $example_1 = $pieces[0];
                if ($pieces[1] != '') {
                    $example_2 = $pieces[1];
                }
                $getReasonAndExample[$i]['example_1'] = $example_1;
                $getReasonAndExample[$i]['example_2'] = $example_2;
            }

            $i++;
        }
//        for ($i = 0; $i < count($getReasonAndExample); $i++) {echo $i,'a';
//         $reason_example = $getReasonAndExample[$i]['reason_example'];
//            //if($getReasonAndExample[$i]['reason_example']==''){echo 'yea';exit;}
//            
//            if($reason_example!=''){ echo $i.'a';
//                if (preg_match('/_/', $reason_example, $matches)) { //In database, more then 2 examples are stored by seperating them by '_'. so explode them and get both examples.
//                    $pieces = explode('_', $reason_example);
//                    $reason_1 = $pieces[0];
//                    $reason_2 = $pieces[1];
//                    $getReasonAndExample[$i]['reason_example_1'] = $reason_1;
//                    $getReasonAndExample[$i]['reason_example_2'] = $reason_2;
//                }//End if 
//                else {
//                    $example = $getReasonAndExample[$i]['reason_example'];
//                    $getReasonAndExample[$i]['reason_example_1'] = $example;
//                }//End elseif
//            }
//            else{
//                echo $i.'b'; 
//                //continue;
//            }
//        }//End for

        return $getReasonAndExample;
    }

//End function

    public function getSubReasons($orderStatusId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $result = $db->query("SELECT * FROM dispute_master where parent_id = '" . $orderStatusId . "' and visible='1'");
        $getReasons = $result->fetchAll();
        $data = array();
        foreach ($getReasons as $items) {
            if ($items['reason_name'] != '') {
                array_push($data, $items);
            }
        }
        return $data;
    }

//End function

    public function submitDispute($data, $userId) {
        $db = Zend_Db_Table::getDefaultAdapter();


        $result = $this->select('*')
                ->from('order_item as oi')
                ->join('orders as o', 'oi.order_id=o.order_id', 'inner')
                ->where('oi.order_item_id', $data['orderItemId'])
                ->get()
                ->rowArray();

        if ($userId == $result['order_item_owner']) {
            $dispute_raised_by = $result['order_item_owner'];
            $dispute_raised_against = $result['customer_id'];
        }//End if
        elseif ($userId == $result['customer_id']) {
            $dispute_raised_by = $result['customer_id'];
            $dispute_raised_against = $result['order_item_owner'];
        }//End elseif

        $dispute = array('reason_id' => $data['reasonId'],
            'sub_reason_id' => $data['subReasonId'],
            'order_item_id' => $data['orderItemId'],
            'order_address_id' => $result['order_address_id'],
            'dispute_raised_by' => $dispute_raised_by,
            'dispute_raised_against' => $dispute_raised_against,
            'dispute_status' => '1',
            'description' => $data['description'],
            'created_on' => time(),
        );
        $insert_dispute = $this->insertRecord('dispute', $dispute);
        
        $this->genObj->createDisputeImageFolder($insert_dispute); //to create a folder in images to put dispute related files. It can be image,pdf etc.

        $main_dir = 'uploads/temp/' . $data['randdisputeid'] . '/';
        $fileData = array();

        if (is_dir($main_dir)) {
            $dirs = scandir($main_dir);
            $allFiles = explode(':', $data['fileToUpload']);
            for ($i = 0; $i < count($dirs); $i++) {
                if (in_array($dirs[$i], $allFiles)) {
                    $source = "uploads/temp/" . $data['randdisputeid'] . "/" . strip_tags($dirs[$i]);
                    $target = $this->genObj->getDisputeUploadedFilesPath($insert_dispute);
                    $target .= '/' . $dirs[$i];
                    copy($source, $target);
                    $pieces = explode('.', $dirs[$i]);
                    $fileData[$i]['file_name'] = $pieces[0];
                    $fileData[$i]['file_ext'] = $pieces[1];
                }//End inner if
            }//End for
        }//End outer if

        /* for ($i = 0; $i < count($dirs); $i++) {
          if ($dirs[$i] == '.' || $dirs[$i] == '..') {
          continue;
          } else {
          $source = "uploads/temp/" . $data['randdisputeid'] . "/" . strip_tags($dirs[$i]);
          $target = "images.goo2o.com/images/0/" . $_SESSION['SESSION']['ApiKey'] . "/disputes/" . $insert_dispute . "/" . $dirs[$i];
          copy($source, $target);
          $pieces = explode('.', $dirs[$i]);
          $fileData[$i]['file_name'] = $pieces[0];
          $fileData[$i]['file_ext'] = $pieces[1];
          }
          }
          } */

        foreach ($fileData as $filedata) { //insert all files
            $data = array('dispute_id' => $insert_dispute,
                'doc_name' => $filedata['file_name'],
                'doc_type' => $filedata['file_ext'],
                'uploaded_by' => $userId,
                'uploaded_on' => time());
            $this->insertRecord('dispute_doc', $data);
        }
        //Save the values of the order_item table when the dispute is raised
        if ($userId == $result['customer_id']) {

            $insert_dispute_order_item = $db->query("INSERT INTO dispute_order_item(order_item_id,order_id,
									product_coupon_id,
									order_address_id,
									order_item_total,
									order_shipment_done,
									order_product_detail_id,
									order_item_owner,
									order_item_status,
									order_sub_status_id,
									ocr_id,
									ocr_details,
									modified_on,
									buyer_substatus) 
					VALUES('" . $result['order_item_id'] . "',
						  '" . $result['order_id'] . "',
						  '" . $result['product_coupon_id'] . "',
						  '" . $result['order_address_id'] . "',
						  '" . $result['order_item_total'] . "',
						  '" . $result['order_shipment_done'] . "',
						  '" . $result['order_product_detail_id'] . "',
						  '" . $result['order_item_owner'] . "',
						  '" . $result['order_item_status'] . "',
						  '" . $result['order_sub_status_id'] . "',
						  '" . $result['ocr_id'] . "',
						  '" . $result['ocr_details'] . "',
						  '" . $result['modified_on'] . "',
						  '" . $result['buyer_substatus'] . "')");
        } else {
            $rs = $this->select('*,orr.return_id as rid')
                    ->from('order_returns as orr')
                    ->join('order_request as orq', 'orq.request_item_id=orr.order_item_id', 'inner')
                    ->join('order_item as oi', 'oi.order_item_id=orq.request_item_id', 'inner')
                    ->where(
                            array('orr.order_item_id' => $data['orderItemId'],
                                'orr.is_visible' => RETURN_VISIBILITY_STATUS_ACCEPTED,
                                'orq.request_status' => '1')
                    )
                    ->get()
                    ->rowArray();

            $insert_dispute_order_item = $db->query("INSERT INTO dispute_order_item(order_item_id,order_id,
									order_address_id,
									order_item_total,
									order_shipment_done,
									order_product_detail_id,
									order_item_owner,
									order_item_status,
									order_sub_status_id,
                                                                        modified_on,
									buyer_substatus) 
					VALUES('" . $rs['order_item_id'] . "',
						  '" . $rs['order_id'] . "',
                                                      '" . $rs['order_address_id'] . "',
						  '" . $rs['quantity'] . "',
						  '" . $rs['quantity_shipped'] . "',
						  '" . $rs['order_product_detail_id'] . "',
						  '" . $userId . "',
						  '" . $rs['return_status'] . "',
						  '" . $rs['seller_sub_status_id'] . "',
                                                  '" . $rs['modified_on'] . "',
						  '" . $rs['return_substatus'] . "')");
        }
        
        if ($insert_dispute) {
            $this->changeOrderItemStatus($data['orderItemId'], $data['subReasonId'], $data['reasonId']);
        }

        $select_reason = $this->select('*')
                ->from('dispute_master as dm')
                ->where('dm.id', $data['reasonId'])
                ->get()
                ->rowArray();
        $reason = addslashes($select_reason['reason_name']);
        //make an entry in chat table for this dispute
        $insert_chat = $db->query("INSERT INTO chat(order_item_id,
						user1,
						user2,
						deleted_flag,
						message_type,
						request_text,
						comment,
						request_status,
						i_id,
						readby
						) 
				  VALUES('" . $data['orderItemId'] . "',
						 '" . $dispute_raised_by . "',
						 '" . $dispute_raised_against . "',
						 '0',
						 '3',
						 '" . $reason . "',
						 '" . $data['description'] . "',
						 '0',
						'" . $insert_dispute . "',
						'0'
						 )");
        echo encryptLink($insert_dispute); //send last inserted dispute
    }

//End function

    public function getOrderDetailsForDispute($disputeId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $result = $this->select('*,dmreason.id as reasonid, dmreason.reason_name as reasonName,dmsubreason.id as subreasonid, dmsubreason.reason_name as subreason_name')
                ->from('dispute as dis')
                ->join('order_item as oi', 'dis.order_item_id = oi.order_item_id', 'inner')
                ->join('order_product_detail as opd', 'oi.order_product_detail_id=opd.product_id', 'left')
                ->join('orders as o', 'o.order_id=oi.order_id', 'inner')
                ->join('dispute_master as dmreason', "dis.reason_id = dmreason.id", 'left')
                ->join('dispute_master as dmsubreason', 'dis.sub_reason_id = dmsubreason.id', 'left')
                ->where('dis.dispute_id', $disputeId)
                ->get()
                ->rowArray();
        $dispute_raised_by_name = $this->select('*')
                ->from('user')
                ->where('id', $result['dispute_raised_by'])
                ->get()
                ->rowArray();
        $result['dispute_raised_by_name'] = $dispute_raised_by_name['user_full_name'];

        $dispute_raised_against_name = $this->select('*')
                ->from('user')
                ->where('id', $result['dispute_raised_against'])
                ->get()
                ->rowArray();
        $result['dispute_raised_against_name'] = $dispute_raised_against_name['user_full_name'];

        $status_name = $this->select('*')
                ->from('order_status')
                ->where('id', $result['order_item_status'])
                ->get()
                ->rowArray();
        $result['status_name'] = $status_name['status'];

        return $result;
    }

//End function

    public function getDisputeMessages($disputeId, $userId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $allmessage = $this->select('*')
                ->from('request_messages')
                ->where('request_id', $disputeId)
                ->where('request_type', '3')
                ->get()
                ->resultArray();

        $data = array();
        $i = 0;
        foreach ($allmessage as $messagekey => $messageval) {
            $data[$i]['message'] = $messageval['message'];
            $data[$i]['time'] = $messageval['time'];
            if ($messageval['message_by'] == $userId) {
                $data[$i]['name'] = 'me';
            }//End if
            else {
                $usersql = $db->query("select * from user where id='" . $messageval['message_by'] . "'");
                $userRecord = $usersql->fetchAll();
                $data[$i]['name'] = $userRecord[0]['user_full_name'];
            }//Enf else
            $i++;
        }//End for

        $result = $this->select('*')
                ->from('dispute')
                ->where('dispute_id', $disputeId)
                ->get()
                ->rowArray();


        $data['request_description'] = $result['description'];

        return $data;
    }

//End function

    /* public function getExtension($str) {
      $i = strrpos($str, ".");
      if (!$i) {
      return "";
      }
      $l = strlen($str) - $i;
      $ext = substr($str, $i + 1, $l);
      return $ext;
      } */

    public function getAllOrderItemRelatedToUser($userId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        /* $result = $this->select('*')
          ->from('order_item as oi')
          ->join('orders as o','o.order_id=oi.order_id','left')
          ->join('order_product_detail as opd','opd.product_id= oi.order_product_detail_id','inner')
          ->where('o.customer_id',$userId)
          ->get()
          ->resultArray(); */
        $current_time = time();
        $prev_time = time() - (15 * 24 * 60 * 60); //order item related to user since last 15 days

        $sql = $db->query("SELECT * FROM order_item as oi left join orders as o on o.order_id=oi.order_id inner join order_product_detail as opd on opd.product_id=oi.order_product_detail_id where (o.customer_id='" . $userId . "' OR oi.order_item_owner='" . $userId . "') and (o.order_place_date between " . $prev_time . " and " . $current_time . ")");
        $records = $sql->fetchAll();
        $data = '';

        $data = '<div class="outerBorder selectPopups">
  <div class="blackBorder">
    <div class="addChangeReq">
      <div class="lh6">&nbsp;</div>
      <div class="floatLeft">
        <div class="wid17">&nbsp;</div>
        <div class="floatLeft">
          <div class="clearBoth">Select an order item id to raise a dispute</div>
          <div class="lh4">&nbsp;</div>
          <div class="subHeadingText">To view order items from a different time period please use the drop-down menu below</div>
        </div>
      </div>
      <div class="floatRight">
        <div class="floatLeft marginTop cancelHyperlink">
          <input type="image" src="/images/default/close.gif" title="Close" alt="Close" />
        </div>
        <div class="wid12">&nbsp;</div>
      </div>
    </div>
    <div class="innerDiv">
      <div class="lh5">&nbsp;</div>
      <div class="clearBoth">
        <div class="wid145">&nbsp;</div>
        <div class="floatLeft">
          <div class="showMeText floatLeft"><b>Show me:</b></div>
          <div class="floatLeft">
            <select class="dropDowm214" id="changeDays" name="order_item_change">
              <option value="15">orders placed in the past 15 days</option>
              <option value="30">orders placed in the past 30 days</option>
              <option value="45">orders placed in the past 45 days</option>
              <option value="90">orders placed in the past 90 days</option>
            </select>
          </div>
        </div>
      </div>
      <div class="lh15">&nbsp;</div>
      <div class="clearBoth">
        <div class="headingBg">
          <div class="wid30">&nbsp;</div>
          <div class="wid144 tableHeading">Order item ID</div>
          <div class="wid255 tableHeading">Order items</div>
        </div>
		 <div class="tableContainer" id="append_item">
          <div class="lh12">&nbsp;</div>';

        for ($i = 0; $i < count($records); $i++) {

            $data .='<div class="clearBoth">';
            $data .= '<div class="wid22"><input type="radio" name="order_item_radio" value="OR_' . $records[$i]["payment_module"] . '_' . $records[$i]["order_id"] . '_' . $records[$i]["order_item_id"] . '"/></div>';
            $data.= '<div class="wid144 showMeText">OR_' . $records[$i]["payment_module"] . '_' . $records[$i]["order_id"] . '_' . $records[$i]["order_item_id"] . '</div>';
            $data.='<div class="wid255 showMeText">' . $records[$i]["product_name"] . '</div>';
            $data.='</div>';
        }

        $data.= '<div class="lh8">&nbsp;</div>
        </div>
		</div>
      <div class=" lh24">&nbsp;</div>
      <div class="bgLine">&nbsp;</div>
      <div class="lh6">&nbsp;</div>
      <div class="floatRight">
        <div class="floatLeft curpointer">
          <input type="image" src="/images/default/submit_btn.png" title="Submit" id="submitItem"/>
        </div>
        <div class="wid15">&nbsp;</div>
        <div class="floatLeft curpointer"><a class="cancelText curpointer" id="closeLink" title="Cancel">Cancel</a></div>
        <div class="wid15">&nbsp;</div>
      </div>
    </div>
    <div class="lh15">&nbsp;</div>
  </div>
</div>';
        echo $data;
    }

    public function getOrderItemForThirtyDays($userId) {
        $db = Zend_Db_Table::getDefaultAdapter();

        $current_time = time();
        $prev_time = time() - (30 * 24 * 60 * 60); //Get order item related to the user since last 30 days

        $sql = $db->query("SELECT * FROM order_item as oi left join orders as o on o.order_id=oi.order_id inner join order_product_detail as opd on opd.product_id=oi.order_product_detail_id where (o.customer_id='" . $userId . "' OR oi.order_item_owner='" . $userId . "') and (o.order_place_date between " . $prev_time . " and " . $current_time . ")");
        $records = $sql->fetchAll();
        $data = '';
        $data .= '<div class="lh12">&nbsp;</div>';

        for ($i = 0; $i < count($records); $i++) {

            $data .='<div class="clearBoth">';
            $data .= '<div class="wid22"><input type="radio" name="order_item_radio" value="OR_' . $records[$i]["payment_module"] . '_' . $records[$i]["order_id"] . '_' . $records[$i]["order_item_id"] . '"/></div>';
            $data.= '<div class="wid144 showMeText">OR_' . $records[$i]["payment_module"] . '_' . $records[$i]["order_id"] . '_' . $records[$i]["order_item_id"] . '</div>';
            $data.='<div class="wid255 showMeText">' . $records[$i]["product_name"] . '</div>';
            $data.='</div>';
        }//End for

        $data.= '<div class="lh8">&nbsp;</div>';

        echo $data;
    }

//End function

    public function getOrderItemForFortyfiveDays($userId) {
        $db = Zend_Db_Table::getDefaultAdapter();

        $current_time = time();
        $prev_time = time() - (45 * 24 * 60 * 60); //Get order item related to the user since last 45 days

        $sql = $db->query("SELECT * FROM order_item as oi left join orders as o on o.order_id=oi.order_id inner join order_product_detail as opd on opd.product_id=oi.order_product_detail_id where (o.customer_id='" . $userId . "' OR oi.order_item_owner='" . $userId . "') and (o.order_place_date between " . $prev_time . " and " . $current_time . ")");
        $records = $sql->fetchAll();
        $data = '';
        $data .= '<div class="lh12">&nbsp;</div>';

        for ($i = 0; $i < count($records); $i++) {

            $data .='<div class="clearBoth">';
            $data .= '<div class="wid22"><input type="radio" name="order_item_radio" value="OR_' . $records[$i]["payment_module"] . '_' . $records[$i]["order_id"] . '_' . $records[$i]["order_item_id"] . '"/></div>';
            $data.= '<div class="wid144 showMeText">OR_' . $records[$i]["payment_module"] . '_' . $records[$i]["order_id"] . '_' . $records[$i]["order_item_id"] . '</div>';
            $data.='<div class="wid255 showMeText">' . $records[$i]["product_name"] . '</div>';
            $data.='</div>';
        }//End for

        $data.= '<div class="lh8">&nbsp;</div>';

        echo $data;
    }

//End function

    public function getOrderItemForNinetyDays($userId) {
        $db = Zend_Db_Table::getDefaultAdapter();

        $current_time = time();
        $prev_time = time() - (90 * 24 * 60 * 60); ////Get order item related to the user since last 90 days

        $sql = $db->query("SELECT * FROM order_item as oi left join orders as o on o.order_id=oi.order_id inner join order_product_detail as opd on opd.product_id=oi.order_product_detail_id where (o.customer_id='" . $userId . "' OR oi.order_item_owner='" . $userId . "') and (o.order_place_date between " . $prev_time . " and " . $current_time . ")");
        $records = $sql->fetchAll();
        $data = '';
        $data .= '<div class="lh12">&nbsp;</div>';

        for ($i = 0; $i < count($records); $i++) {

            $data .='<div class="clearBoth">';
            $data .= '<div class="wid22"><input type="radio" name="order_item_radio" value="OR_' . $records[$i]["payment_module"] . '_' . $records[$i]["order_id"] . '_' . $records[$i]["order_item_id"] . '"/></div>';
            $data.= '<div class="wid144 showMeText">OR_' . $records[$i]["payment_module"] . '_' . $records[$i]["order_id"] . '_' . $records[$i]["order_item_id"] . '</div>';
            $data.='<div class="wid255 showMeText">' . $records[$i]["product_name"] . '</div>';
            $data.='</div>';
        }//End for

        $data.= '<div class="lh8">&nbsp;</div>';

        echo $data;
    }

//End function

    public function getOrderItemForFifteenDays($userId) {
        $db = Zend_Db_Table::getDefaultAdapter();

        $current_time = time();
        $prev_time = time() - (15 * 24 * 60 * 60); ////Get order item related to the user since last 15 days

        $sql = $db->query("SELECT * FROM order_item as oi left join orders as o on o.order_id=oi.order_id inner join order_product_detail as opd on opd.product_id=oi.order_product_detail_id where (o.customer_id='" . $userId . "' OR oi.order_item_owner='" . $userId . "') and (o.order_place_date between " . $prev_time . " and " . $current_time . ")");
        $records = $sql->fetchAll();
        $data = '';
        $data .= '<div class="lh12">&nbsp;</div>';

        for ($i = 0; $i < count($records); $i++) {

            $data .='<div class="clearBoth">';
            $data .= '<div class="wid22"><input type="radio" name="order_item_radio" value="OR_' . $records[$i]["payment_module"] . '_' . $records[$i]["order_id"] . '_' . $records[$i]["order_item_id"] . '"/></div>';
            $data.= '<div class="wid144 showMeText">OR_' . $records[$i]["payment_module"] . '_' . $records[$i]["order_id"] . '_' . $records[$i]["order_item_id"] . '</div>';
            $data.='<div class="wid255 showMeText">' . $records[$i]["product_name"] . '</div>';
            $data.='</div>';
        }//End for

        $data.= '<div class="lh8">&nbsp;</div>';

        echo $data;
    }

//End function
    //create drop down actions for the dispute details page
    public function createDropDownActions($disputeId, $userId) {

        $db = Zend_Db_Table::getDefaultAdapter();
        $disputeData = $this->select('*')
                ->from('dispute')
                ->where('dispute_id', $disputeId)
                ->get()
                ->rowArray();
        $dropDown = '';
        $dropDown .= '<div class="floatLeft"><a class="actionbuttondefault curpointer actionbutton" id="actionbutton" title="Actions"></a></div>
         <div class="floatLeft"><a href="/inbox/#list" class="back_link" title="Back to disputes"><span class="backtoarrow"></span>Back to disputes</a></div>	
         </div></div>
		<div id="actiondropdown" class="curpointer"> <ul> ';

        switch ($disputeData['dispute_status']) {
            case 1:
                //claim raised
                if ($userId == $disputeData['dispute_raised_by']) {
                    $dropDown .= '<li><a id="solved_raised" class="curpointer" title="Resolved">Resolved</a></li>';
                    $dropDown .= '<li><a id="attachDocuments_raised" class="curpointer" title="Attach Documents">Attach Documents</a></li>';
                    $dropDown .= '<li><a id="escalatetoclaim_raised_by" class="curpointer" title="Request a Mediation from Goo2o">Request a Mediation from Goo2o</a></li>';
                }//End if
                elseif ($userId == $disputeData['dispute_raised_against']) {
                    $dropDown .= '<li><a id="attachDocuments_raised" class="curpointer" title="Attach Documents">Attach Documents</a></li>';
                    $dropDown .= '<li><a id="escalatetoclaim_raised_against" class="curpointer" title="Request a Mediation from Goo2o">Request a Mediation from Goo2o</a></li>';
                }//End else if
                break;
            case 2:
                //escalate to claim
                if ($userId == $disputeData['dispute_raised_by']) {
                    $dropDown .= '<li><a id="attachDocuments_raised" class="curpointer" title="Attach Documents">Attach Documents</a></li>';
                }//End if
                elseif ($userId == $disputeData['dispute_raised_against']) {
                    $dropDown .= '<li><a id="attachDocuments_raised" class="curpointer" title="Attach Documents">Attach Documents</a></li>';
                }//End elseif
                break;
            case 3:
                //solved
                if ($userId == $disputeData['dispute_raised_by']) {
                    
                }//End if
                elseif ($userId == $disputeData['dispute_raised_against']) {
                    
                }//End elseif
                break;
            case 4:
                //closed
                if ($userId == $disputeData['dispute_raised_by']) {
                    
                }//End if 
                elseif ($userId == $disputeData['dispute_raised_against']) {
                    
                }//End elseif
                break;
        }//End switch
        $dropDown .= '</ul></div>';
        return $dropDown;
        //exit;
    }

//End function
    //Create remark e.g.-A raised a dispute against B
    public function createRemark($disputeId, $userId) {
        //echo $userId;
        $disputeData = $this->select('*')
                ->from('dispute')
                ->where('dispute_id', $disputeId)
                ->get()
                ->rowArray();

        $remark = '';
        if ($userId == $disputeData['dispute_raised_by']) {
            switch ($disputeData['dispute_status']) {
                case '1':
                    //raised
                    /* get against name------You have raised a dispute against NAME */

                    $against_name = $this->select('*')
                            ->from('user')
                            ->where('id', $disputeData['dispute_raised_against'])
                            ->get()
                            ->rowArray();
                    $remark = 'You have raised dispute against ' . $against_name['user_full_name'];

                    break;
                case '2':
                    //escalate
                    /* You have raised a claim against NAME */
                    if ($disputeData['superadmin_action_on_dispute'] == '1') {
                        $remark = 'Goo2o has raised a claim.';
                    } else {
                        if ($disputeData['claim_escalated_by'] == $userId) {
                            $against_name = $this->select('*')
                                    ->from('user')
                                    ->where('id', $disputeData['dispute_raised_against'])
                                    ->get()
                                    ->rowArray();
                            $remark = 'You have raised a claim against ' . $against_name['user_full_name'];
                        }//End if
                        elseif ($disputeData['claim_escalated_by'] != $userId) {
                            $by_name = $this->select('*')
                                    ->from('user')
                                    ->where('id', $disputeData['dispute_raised_against'])
                                    ->get()
                                    ->rowArray();
                            $remark = 'Claim had been raised by ' . $by_name['user_full_name'] . ' against you';
                        }//End else if
                    }
                    break;
                case '3':
                    //solved
                    /* Dispute has been solved */
                    $remark = 'Dispute has been solved';
                    break;
                case '4':
                    //closed
                    /* Dispute is closed due to time lapse */
                    $result = $this->select('*')
                            ->from('dispute')
                            ->where('dispute_id', $disputeId)
                            ->get()
                            ->rowArray();

                    if ($result['superadmin_action_on_dispute'] == 1) {
                        $dispute_raised_by = $this->select('*')
                                ->from('user')
                                ->where('id', $disputeData['dispute_raised_by'])
                                ->get()
                                ->rowArray();
                        $remark = 'Dispute closed by Goo2o due to chargeback raised by ' . $dispute_raised_by['user_full_name'];
                    } elseif ($result['superadmin_action_on_dispute'] == 2) {
                        $dispute_raised_by = $this->select('*')
                                ->from('user')
                                ->where('id', $disputeData['dispute_raised_by'])
                                ->get()
                                ->rowArray();
                        $remark = 'Claim closed by Goo2o due to chargeback raised by ' . $dispute_raised_by['user_full_name'];
                    }
                    break;
            }//End switch
            return $remark;
        }//End if
        elseif ($userId == $disputeData['dispute_raised_against']) {
            switch ($disputeData['dispute_status']) {
                case 1:
                    //raised
                    /* get against name------Dispute has been raised by NAME against you */
                    $by_name = $this->select('*')
                            ->from('user')
                            ->where('id', $disputeData['dispute_raised_by'])
                            ->get()
                            ->rowArray();
                    $remark = 'Dispute has been raised by ' . $by_name['user_full_name'] . ' against you';
                    break;
                case 2:
                    //escalate
                    /* Claim has been raised by NAME against you */
                    if ($disputeData['superadmin_action_on_dispute'] == 1) {
                        $dispute_raised_by = $this->select('*')
                                ->from('user')
                                ->where('id', $disputeData['dispute_raised_by'])
                                ->get()
                                ->rowArray();
                        $remark = 'Goo2o has raised a claim on behalf of ' . $dispute_raised_by['user_full_name'];
                    } else {
                        if ($disputeData['claim_escalated_by'] == $userId) {
                            $against_name = $this->select('*')
                                    ->from('user')
                                    ->where('id', $disputeData['dispute_raised_by'])
                                    ->get()
                                    ->rowArray();
                            $remark = 'You have raised a claim against ' . $against_name['user_full_name'];
                        }//End if
                        elseif ($disputeData['claim_escalated_by'] != $userId) {
                            $by_name = $this->select('*')
                                    ->from('user')
                                    ->where('id', $disputeData['dispute_raised_by'])
                                    ->get()
                                    ->rowArray();
                            $remark = 'Claim had been raised by ' . $by_name['user_full_name'] . ' against you';
                        }//End else if
                    }
                    break;
                //Now the claim is escalated so only view documents will be visible.
                //Now only superadmin can click on solve option so when he clicks on it $remark must change to CLAIM HAS BEEN SOLVED
                case 3:
                    //solved
                    /* Dispute has been solved */
                    $remark = 'Dispute has been solved';
                    break;
                case 4:
                    //closed
                    /* Dispute is closed due to time lapse */
                    $result = $this->select('*')
                            ->from('dispute')
                            ->where('dispute_id', $disputeId)
                            ->get()
                            ->rowArray();

                    if ($result['superadmin_action_on_dispute'] == 1) {
                        $dispute_raised_by = $this->select('*')
                                ->from('user')
                                ->where('id', $disputeData['dispute_raised_by'])
                                ->get()
                                ->rowArray();
                        $remark = 'Dispute closed by Goo2o due to chargeback raised by ' . $dispute_raised_by['user_full_name'];
                    } elseif ($result['superadmin_action_on_dispute'] == 2) {
                        $dispute_raised_by = $this->select('*')
                                ->from('user')
                                ->where('id', $disputeData['dispute_raised_by'])
                                ->get()
                                ->rowArray();
                        $remark = 'Claim closed by Goo2o due to chargeback raised by ' . $dispute_raised_by['user_full_name'];
                    }

                    break;
            }//End switch 
            return $remark;
        }// End elseif
    }

//End function

    public function solveDispute($disputeId) {
        $data = array('dispute_status' => '3');
        $this->updateRecord('dispute', $data, array('dispute_id' => $disputeId));
    }

//End function	

    public function escalateDisputeBy($disputeId, $userId) {
        $data = array('dispute_status' => '2', 'claim_escalated_by' => $userId);
        $this->updateRecord('dispute', $data, array('dispute_id' => $disputeId));
    }

//End function

    public function escalateDisputeAgainst($disputeId, $userId) {
        $data = array('dispute_status' => '2', 'claim_escalated_by' => $userId);
        $this->updateRecord('dispute', $data, array('dispute_id' => $disputeId));
    }

//End function

    public function getAttachedDocuments($disputeId, $userId) {
        $docs = $this->select('*')
                ->from('dispute_doc')
                ->where(array('dispute_id' => $disputeId))
                ->get()
                ->resultArray();

        $documents = array();
        $j = 0;
        $k = 0;
        for ($i = 0; $i < count($docs); $i++) {
            if ($docs[$i]['uploaded_by'] == $userId) {
                $documents['uploaded'][$j] = $docs[$i]['doc_name'] . '.' . $docs[$i]['doc_type'];
                $j++;
            }//End if
            else {
                $documents['downloaded'][$k] = $docs[$i]['doc_name'] . '.' . $docs[$i]['doc_type'];
                $k++;
            }//End else if
        }//End for
        return $documents;
    }

//End function

    public function insertFiles($filename, $disputeId, $userId, $type) {

        $data = array('dispute_id' => $disputeId,
            'doc_name' => $filename,
            'doc_type' => $type,
            'uploaded_by' => $userId,
            'uploaded_on' => time()
        );
        $this->insertRecord('dispute_doc', $data);
    }

//End function

    public function validateOpenDispute($userId, $orderItemId) {
        if ($orderItemId == '') {
            $error = $this->genObj->displayError('Order Item Not Found', 'Order Item is empty.Please navigate through proper channels.', '/dispute/#openadispute');
            return $error;
        }//End if

        $statusvalidate = $this->select('*')
                ->from('order_item')
                ->where('order_item_id', $orderItemId)
                ->get()
                ->rowArray();
        if ($statusvalidate['order_item_status'] == '1' || $statusvalidate['order_item_status'] == '5' || $statusvalidate['order_item_status'] == '6') { //if order item status is not pending, shipped, delievered.
            $error = $this->genObj->displayError('status is not valid.', 'You can\'t raise dispute for this item', '/dispute/#openadispute');
            return $error;
        }//End if

        $result = $this->select('*')
                ->from('dispute')
                ->where('order_item_id', $orderItemId)
                ->get()
                ->rowArray();
        if($result['dispute_status']!='3'){
            $error = $this->genObj->displayError('Dispute Already Raised', 'You have already raised a dispute for this Order Item.', '/dispute/#openadispute');
            return $error;
        }
//        if ($result != '') {
//            $error = $this->genObj->displayError('Dispute Already Raised', 'You have already raised a dispute for this Order Item.', '/dispute/#openadispute');
//            return $error;
//        }//End if

        $checkReturn = $this->select('*')
                ->from('order_returns as orr')
                //->join('order_item as oi','orr.order_item_id=oi.order_item_id','inner')
                ->where('orr.order_item_id', $orderItemId)
                ->get()
                ->rowArray();
        //echo '<pre>';print_r($checkReturn);exit;
        if ($checkReturn != '') {
            if ($checkReturn['owner_id'] == $userId) {
                if ($checkReturn['return_status'] != '3' && $checkReturn['return_status'] != '4') {
                    $error = $this->genObj->displayError('No previlages', 'Return is not shipped yet.', '/dispute/#openadispute');
                    return $error;
                }
            }
        } elseif ($checkReturn == '') {
            if ($statusvalidate['order_item_owner'] == $userId) {
                $error = $this->genObj->displayError('No previlages', 'Since the return is not genereated corresponding to this order item, you cannot raise a dispute.', '/dispute/#openadispute');
                return $error;
            }
        }
    }

//End function

    public function validateDisputeDetails($userId, $disputeId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($disputeId == '') {
            $error = $this->genObj->displayError('Dispute Not Found', 'Please navigate through proper channels.', '/dispute/#openadispute');
            echo $error;
            exit;
        }//End if

        $result = $this->select('*')
                ->from('dispute')
                ->where('dispute_raised_by', $userId, 'dispute_raised_against', $userId)
                ->where('dispute_id', $disputeId)
                ->get()
                ->rowArray();
        //echo $this->lastQuery();
        if ($result == '') {
            $error = $this->genObj->displayError('This dispute doesn\'t belong to you.', 'Please navigate through proper links', '/dispute/#openadispute');
            echo $error;
            exit;
        }//End if
    }

    public function showDropDown($requestId, $userId) {
        return $this->getWhere('order_request', array('request_id' => $requestId, 'expire' => 'FALSE', 'request_seller_id' => $userId))->rowArray();
    }

    public function getOrderDetailsByOrderItemId($orderItemId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $items = $this->select('*')
                ->from('order_item as a')
                ->join('order_product_detail as b', 'a.order_product_detail_id=b.product_id', 'inner')
                ->join('order_addresses as c', 'a.order_address_id=c.order_address_id', 'left')
                ->join('orders as d', 'a.order_id=d.order_id', 'inner')
                //->join('order_payment_modules as e', 'e.short_desc=d.payment_module', 'inner')
                ->join('mall_detail as md', 'a.order_item_owner=md.user_id', 'left')
                ->where(array('a.order_item_id' => $orderItemId))
                ->get()
                ->rowArray();
        return $items;
    }

//End function

    public function insertCommentAndReturnStructure() {
        $db = Zend_Db_Table::getDefaultAdapter();

        //print_r($db);
        $args = func_get_args();

        $this->changeReadByInChatTable($args[0]['whateverid'], $args[1]);

        $comment = $args[0]['comment'];
        $commentType = $args[0]['commentType'];
        $whateverid = $args[0]['whateverid'];
        $userid = $args[1];

        $data = array('request_id' => $whateverid,
            'request_type' => $commentType,
            'message' => nl2br($comment),
            'time' => time(),
            'visibility' => 0,
            'message_by' => $userid
        );
        $lastInsertedId = $this->insertRecord('request_messages', $data);
        // echo 'select * from request_messages where request_message_id='.$lastInsertedId.' and request_type='.$commentType;
        $result = $this->db->query('select * from request_messages where request_message_id=' . $lastInsertedId . ' and request_type="' . $commentType . '"');
        $resultSet = $result->fetchAll();
        foreach ($resultSet as $key) {
          //  $imgs = $this->genObj->getImageFromDir($key["message_by"], "user", "small", " ");
		 
			$imgs = $this->genObj->getuserimageSrc($key["message_by"],"40","40","small","",0);
            $ran = rand(44474, 999999);
            //print_r($key);
            echo $abc = '<ul>
						<li class = "dotted_border selected">
							<div class="conversation_liContainer">
								<div class="imagecontainer">'.$imgs.'</div>
								<div class="floatLeft">
									<div class="clearBoth">
										<div class="content_block">
											<div class="clearBoth"><a href="#" class="user_link" title="me">me</a></div>
											<div class="clearBoth">
												<div class="wid920">
												<div class="wid815">
												<div class="clearBoth">' . $key["message"] . '</div>
											</div>
												<div class="floatRight">
											<div class="date_text">' . date("d/m/y   h:i A", $key["time"]) . '</div>
										</div>
											</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</li>
					</ul>';
        }
    }

    public function getAllcomments() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $args = func_get_args();
        $commentType = $args[0]['commentType'];
        $whateverid = $args[0]['whateverid'];
        $userid = $args[1];

        $result = $this->db->query('select * from request_messages where request_id=' . $whateverid . ' and request_type="' . $commentType . '" order by time asc');
        $resultSet = $result->fetchAll();
        foreach ($resultSet as $key) {
           // $imgs = $this->genObj->getImageFromDir($key["message_by"], "user", "small", " ");
		   $imgs = $this->genObj->getuserimageSrc($key["message_by"],"40","40","small","",0);
            if ($key["message_by"] == $userid) {
                $nameText = "me";
                $dottedBorder = "dotted_border selected";
            } else {
                $userDetails = $this->genObj->getUserBasicDetails($key["message_by"]);
                $nameText = $userDetails[0]['username'];
                $dottedBorder = "dotted_border";
            }
            $ran = rand(44474, 999999);
            //print_r($key);
            $abc .= '<ul>
						<li class = "' . $dottedBorder . '">
							<div class="conversation_liContainer">
								<div class="imagecontainer">'.$imgs.'</div>
								<div class="floatLeft">
									<div class="clearBoth">
										<div class="content_block">
											<div class="clearBoth"><a href="#" class="user_link" title="me">' . $nameText . '</a></div>
											<div class="clearBoth">
												<div class="wid920">
												<div class="wid815">
												<div class="clearBoth">' . $key["message"] . '</div>
											</div>
												<div class="floatRight">
											<div class="date_text">' . date("d/m/y   h:i A", $key["time"]) . '</div>
										</div>
											</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</li>
					</ul>';
        }
       // echo $abc .='<div class="lh45">&nbsp;</div>';
	    echo $abc ;
    }

    public function changeReadByInChatTable($requestId, $userId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = $db->query("select * from chat where i_id='" . $requestId . "'");
        $chatResult = $sql->fetchAll();
        //echo_pre($chatResult);exit;

        if ($chatResult[0]['readby'] == 0) { //page viewed by none of them
            $update = $db->query("Update chat set readby='" . $userId . "',i_id='" . $requestId . "'");

//$data = array('readby'=>$userId);
            //$this->updateRecord('chat', $data, 'i_id',$requestId);
            echo $this->lastQuery();
            exit;
        } elseif ($chatResult[0]['readby'] == -1) {  //page viewed by both seller and buyer
            $update = $db->query("Update chat set readby='" . $userId . "',i_id='" . $requestId . "'");
        } else {          //page viewed by one and that user's id is there
            if ($userId == $chatResult[0]['readby']) {
                //Do Nothing
            } elseif ($userId != $chatResult[0]['readby']) {
                $update = $db->query("Update chat set readby='-1',i_id='" . $requestId . "'");
            }
        }
    }

    public function changeOrderItemStatus($orderItemId, $subReasonId, $reasonId=NULL) {
        $data['order_item_status'] = ORDER_STATUS_CONFLICT;
        $returnData['return_status'] = RETURN_STATUS_CANCELLED;
        switch ($subReasonId) {
            case 38:
            case 5:
            case 8:
                $data['order_sub_status_id'] = 160;
                $data['buyer_substatus'] = 161;
                break;
            case 37:
            case 6:
            case 9:
                $data['order_sub_status_id'] = 162;
                $data['buyer_substatus'] = 163;
                break;
            case 4:
            case 7:
            case 10:
                $data['order_sub_status_id'] = 164;
                $data['buyer_substatus'] = 165;
                break;

            case 13:
            case 41:
                $data['order_sub_status_id'] = 174;
                $data['buyer_substatus'] = 175;
                break;
            case 14:
            case 42:
                $data['order_sub_status_id'] = 176;
                $data['buyer_substatus'] = 177;
                break;
            case 15:
            case 43:
                $data['order_sub_status_id'] = 178;
                $data['buyer_substatus'] = 179;
                break;
            case 16:
            case 44:
                $data['order_sub_status_id'] = 180;
                $data['buyer_substatus'] = 181;
                break;
            case 17:
            case 45:
                $data['order_sub_status_id'] = 182;
                $data['buyer_substatus'] = 183;
                break;
            case 19:
            case 47:
                $data['order_sub_status_id'] = 184;
                $data['buyer_substatus'] = 185;
                break;
            case 20:
            case 45:
                $data['order_sub_status_id'] = 186;
                $data['buyer_substatus'] = 187;
                break;
            case 21:
            case 49:
                $data['order_sub_status_id'] = 188;
                $data['buyer_substatus'] = 189;
                break;
            case 22:
            case 58:
                $data['order_sub_status_id'] = 190;
                $data['buyer_substatus'] = 191;
                break;
            case 24:
            case 52:
//                $data['order_sub_status_id'] = 192;
//                $data['buyer_substatus'] = 193;

                $returnData['return_substatus'] = 193;
                $returnData['seller_sub_status_id'] = 192;
                break;
            case 25:
            case 53:
//                $data['order_sub_status_id'] = 194;
//                $data['buyer_substatus'] = 195;

                $returnData['return_substatus'] = 195;
                $returnData['seller_sub_status_id'] = 194;
                break;
            case 27:
            case 55:
//                $data['order_sub_status_id'] = 196;
//                $data['buyer_substatus'] = 197;

                $returnData['return_substatus'] = 197;
                $returnData['seller_sub_status_id'] = 196;
                break;
            case 29:
            case 57:
//                $data['order_sub_status_id'] = 198;
//                $data['buyer_substatus'] = 199;

                $returnData['return_substatus'] = 199;
                $returnData['seller_sub_status_id'] = 198;
                break;
            case 30:
            case 58:
//                $data['order_sub_status_id'] = 200;
//                $data['buyer_substatus'] = 201;

                $returnData['return_substatus'] = 201;
                $returnData['seller_sub_status_id'] = 200;
                break;
            case 31:
            case 59:
//                $data['order_sub_status_id'] = 202;
//                $data['buyer_substatus'] = 203;

                $returnData['return_substatus'] = 203;
                $returnData['seller_sub_status_id'] = 202;
                break;
            case 32:
            case 60:
//                $data['order_sub_status_id'] = 204;
//                $data['buyer_substatus'] = 205;

                $returnData['return_substatus'] = 205;
                $returnData['seller_sub_status_id'] = 204;
                break;
            case 33:
            case 61:
//                $data['order_sub_status_id'] = 206;
//                $data['buyer_substatus'] = 207;

                $returnData['return_substatus'] = 207;
                $returnData['seller_sub_status_id'] = 206;
                break;
            default:
                if ($subReasonId == 0) {

                    $data['order_sub_status_id'] = 172;
                    $data['buyer_substatus'] = 173;
                }

                break;
        }
        if ($returnData != '') {
            $this->updateRecord('order_returns', $returnData, array('order_item_id' => $orderItemId, 'is_visible' => '1'));
        }
        if ($data != '') {
            $this->updateRecord('order_item', $data, array('order_item_id' => $orderItemId));
        }
    }

    public function checkDisputeRaisedByBuyerOrSeller($orderItemId, $userId) {
        $orderItemData = $this->select('*')
                ->from('order_item as oi')
                ->join('orders as o', 'oi.order_id=o.order_id', inner)
                ->where('oi.order_item_id', $orderItemId)
                ->get()
                ->rowArray();
        if ($orderItemData['customer_id'] == $userId) {
            return '2';
        } elseif ($orderItemData['order_item_owner'] == $userId) {
            return '3';
        }
    }

    public function getReturnItemDetails($oid) {
        $rs = $this->select('*,orr.return_id as rid')
                ->from('order_returns as orr')
                ->join('orders as o', 'o.order_id=orr.order_id', 'inner')
                ->join('order_item as oi', 'oi.order_item_id=orr.order_item_id', 'inner')
                ->join('order_addresses as oa', 'oa.order_address_id=oi.order_address_id', 'inner')
                ->join('order_product_detail as opd', 'oi.order_product_detail_id=opd.product_id', 'inner')
                ->join('user as u', "u.id=orr.owner_id", 'inner')
                ->join('mall_detail', 'u.id=orr.owner_id', 'left')
                ->where('orr.order_item_id', $oid)
                ->get()
                ->rowArray();
        // echo $this->lastQuery();
        return $rs;
    }

    public function checkReturnTable($orderItemId) {
        $returnData = $this->select('*')
                ->from('order_returns')
                ->where('order_item_id', $orderItemId)
                ->where('is_visible', 1)
                ->get()
                ->rowArray();


        if (empty($returnData)) {
            return true;
        } else {
            return false;
        }
    }

    public function getOrderItemIdFromDisputeId($disputeId) {
        return $this->select('order_item_id')
                        ->from('dispute')
                        ->where(array('dispute_id' => $disputeId))
                        ->get()
                        ->rowArray();
    }

    public function getReturnDetailsForDispute($disputeId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $result = $this->select('*,dmreason.id as reasonid, dmreason.reason_name as reasonName,dmsubreason.id as subreasonid, dmsubreason.reason_name as subreason_name,oi.order_product_detail_id as product_id')
                ->from('dispute as dis')
                ->join('order_returns as orr', 'dis.order_item_id = orr.order_item_id', 'inner')
                ->join('order_item as oi', 'oi.order_item_id=orr.order_item_id', 'inner')
                ->join('order_product_detail as opd', 'oi.order_item_id=opd.product_id', 'left')
                ->join('orders as o', 'o.order_id=orr.order_id', 'inner')
                ->join('dispute_master as dmreason', "dis.reason_id = dmreason.id", 'left')
                ->join('dispute_master as dmsubreason', 'dis.sub_reason_id = dmsubreason.id', 'left')
                ->where('dis.dispute_id', $disputeId)
                ->get()
                ->rowArray();
        //echo $this->lastQuery();exit;
        //echo_pre($result);exit;
        $dispute_raised_by_name = $this->select('*')
                ->from('user')
                ->where('id', $result['dispute_raised_by'])
                ->get()
                ->rowArray();
        $result['dispute_raised_by_name'] = $dispute_raised_by_name['user_full_name'];

        $dispute_raised_against_name = $this->select('*')
                ->from('user')
                ->where('id', $result['dispute_raised_against'])
                ->get()
                ->rowArray();
        $result['dispute_raised_against_name'] = $dispute_raised_against_name['user_full_name'];

        $status_name = $this->select('*')
                ->from('order_status')
                ->where('id', $result['return_status'])
                ->get()
                ->rowArray();
        $result['status_name'] = $status_name['status'];
        //echo_pre($result);exit;
        return $result;
    }

    public function getOrderItemStatusFromOrderItemId($oid) {
        $status = $this->select('*')
                ->from('order_item')
                ->where('order_item_id', $oid)
                ->get()
                ->rowArray();
        $mydata = $this->select('status,id')
                ->from('order_status')
                ->where(array('id' => $status['order_item_status']))
                ->get()
                ->rowArray();
        return $mydata;
    }

	public function getuserImage(){
		$args =  func_get_args();
		$userId =  $args[0];
		echo $this->genObj->getuserimageSrc($userId,"40","40","large","",0);		
	}

}

//End function
?>