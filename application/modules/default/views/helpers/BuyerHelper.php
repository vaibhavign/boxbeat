<?php

/**
 * buyerHelper Function will be used to create the Remarks , Prime Actions and 
 * Available Action according to the paramenter passed. 
 * @param $purspose string {'order','shipment','dateformat'}
 * @param $type string {'default','tm','status','remarks','primeaction','availableaction'}
 */
class Zend_View_Helper_BuyerHelper extends Zend_View_Helper_Abstract {

    function buyerHelper($purpose, $type, $status, $itemId=NULL, $db=NULL) {

        // For Specific Date Format
        // Will return Date in Form of May 30, 2011
        if ($purpose == 'date' && $type == 'default') {
            return date('M j, Y', $status);
        }

        // For Specific Time Format
        // Will return Time in Form of 7:58:43 AM
        if ($purpose == 'date' && $type == 'time') {
            return date('g:i:s A', $status);
        }
        //order ststus
        if ($purpose == 'order' && $type == 'status') {
            return $this->returnOrderStatus($status);
        }
        if ($purpose == 'order' && $type == 'remarks') {
            return $this->returnOrderRemarks($status);
        }
        if ($purpose == 'order' && $type == 'prime-action') {
            return $this->returnOrderPrimeActions($status, $itemId);
        }
        if ($purpose == 'order' && $type == 'available-action') {
            return $this->returnOrderAvailableActions($status, $itemId);
        }

        //shipments
        //order ststus
        if ($purpose == 'shipment' && $type == 'status') {
            return $this->returnShipmentStatus($status);
        }
        if ($purpose == 'shipment' && $type == 'remarks') {
            return $this->returnShipmentRemarks($itemId, $status);
            //return $shipmentstatus;
        }
        if ($purpose == 'shipment' && $type == 'prime-action') {
            return $this->returnShipmentPrimeAction($itemId, $status);
        }
        if ($purpose == 'shipment' && $type == 'available-action') {
//            echo 'hello';
//            exit;
            return $this->returnShipmentAvailableAction($itemId, $status);
        }
        if ($purpose == 'shipments' && $type == 'short') {
            return $this->returnShortOrder($orderStatus);
        }
    }

    function returnOrderStatus($status) {
        if ($status == 0) {
            $status = 'Open';
        }
        if ($status == 1) {
            $status = 'Order in Progress';
        }
        if ($status == 2) {
            $status = 'Shipped';
        }
        if ($status == 3) {
            $status = 'Delivered';
        }
        if ($status == 4) {
            $status = 'Compelted';
        }
        if ($status == 5) {
            $status = 'cancelled';
        }
        return ucwords($status);
    }

    function returnOrderRemarks($orderStatus) {

        if ($orderStatus == 0) {
            $remarks = 'Awaiting payment';
        }

        if ($orderStatus == 1) {
            $remarks = 'payment received - waiting for seller to ship the item';
        }

        if ($orderStatus == 2) {
            $remarks = 'seller has shipped the items - please confirm delivery when you receive the items
';
        }
        if ($orderStatus == 3) {
            $remarks = 'you have received the items - please leave a feedback for the seller';
        }
        if ($orderStatus == 4) {
            $remarks = 'Thank you for shopping with us';
        }
        if ($orderStatus == 5) {
            $remarks = 'seller cancelled the order ';
        }
        return ucwords($remarks);
    }

    function returnOrderPrimeActions($orderStatus, $itemId) {
        $btnAction = '';
        // When Payment is Awaited ; Generate Cancel Order Button
        if ($orderStatus == 0) {
            $btnAction.='<form action="/buyer/remove-item/item/' . $itemId . '" method="POST">';
            $btnAction.='<input type="hidden" value="' . $itemId . '" title="Cancel item" name="itemid" />';
            $btnAction.='<input type="image" src="/images/default/buyer/cancel_item.gif" title="Cancel item" name="cancel" />';
            $btnAction.='</form>';
        }
        // When Payment is Received
        if ($orderStatus == 1) {
            $btnAction = '';
        }
        // When Order is shipped; Generate Confirm Delivery Button
        if ($orderStatus == 2) {
            $btnAction.='<form action="/buyer/confirm-delivery/item/' . $itemId . '" method="POST">';
            $btnAction.='<input type="hidden" value="' . $itemId . '" title="Confirm Delivery" name="itemid" />';
            $btnAction.='<input type="image" src="/images/default/buyer/confirm_delivery.gif" title="Confirm Delivery" name="confirm_delivery" />';
            $btnAction.='</form>';
        }
        if ($orderStatus == 3) {
            $btnAction = '';
        }
        if ($orderStatus == 4) {
            $btnAction = '';
        }
        if ($orderStatus == 5) {
            $btnAction = '';
        }

        return $btnAction;
    }

    function returnOrderAvailableActions($status, $itemId) {

        $dropdown = '<div class="floatLeft" style="height:4px;">
            <img src="/images/default/buyer/drp_down.gif" border="0"/ width="162">
            </div>
            <div class="dropBg">
            <ul> ';
        switch ($status) {
            case 0:   // payment Awaited
                $dropdown.='<li><a href="/buyer/remove-item/item/' . $itemId . '" title="Cancel order">Cancel Item</a></li>';
                $dropdown.='<li><a href="/buyer/change-address-request/item/' . $itemId . '" title="Change Address Request">Change Shipping Address</a></li>';
                break;
            case 1: // order in progress ; payment received
                $dropdown.='<li><a href="/buyer/request-cancellation/item/' . $itemId . '" title="Request Cancellation">Request Cancellation</a></li>';
                $dropdown.='<li><a href="/buyer/change-address-request/item/' . $itemId . '" title="Change Address Request">Change Shipping Address</a></li>';


                break;


            case 2:
                // order shipped
                $dropdown.='<li><a href="/buyer/confirm-delivery/item/' . $itemId . '" title="Confirm Delivery">Confirm Delivery</a></li>';
                $dropdown.='<li><a href="/buyer/return-item/item/' . $itemId . '" title="Return Item">Return Item</a></li>';
                $dropdown.='<li><a href="/buyer/leave-feedback/item/' . $itemId . '" title="Leave A Feedback">Leave A Feedback</a></li>';


                break;

            case 3:
                //order delivered
                $dropdown.='<li><a href="/buyer/leave-feedback/item/' . $itemId . '" title="Leave A Feedback">Leave A Feedback</a></li>';
                $dropdown.='<li><a href="/buyer/return-item/item/' . $itemId . '" title="Return Item">Return Item</a></li>';
                break;
            case 4:
            case 5:
                $dropdown = '';
                break;
        }
        $dropdown.='</ul></div>';
        return $dropdown;
    }

    function returnShipmentStatus($status) {
        if ($status == 0) {
            $status = '';
        }
        if ($status == 1) {
            $status = '';
        }
        if ($status == 2) {
            $status = 'Shipped';
        }
        if ($status == 3) {
            $status = 'Delivered';
        }

        return ucwords($status);
    }

    public function returnShipmentRemarks($shipmentid, $status) {
//        echo 'shipmentid'. $shipmentid;
//        exit;
        if ($status == 2) {
            return 'seller has shipped ' . $this->totalitemshipment($shipmentid) . ' item(s) - please confirm delivery when you receive the items';
        }
        return '';
    }

    function returnShipmentPrimeAction($ship_id, $ship_status) {
        $btnAction='';
        if (($ship_status == 2)) {
            //	echo $itemShipmentDone;exit;
            $btnAction.= '<form action="/buyer/confirm-shipment/ship_id/' . $ship_id . '" method="POST" >';
            $btnAction.= '<input type="hidden" value=' . $ship_id . ' title="ship_id" name="shid" />';
            $btnAction.= '<input type="image" src="/images/default/buyer/confirm_shipment.gif" title="Confirm shipment" name="cshipment" />';
            return $btnAction.= '</form>';
        }
        return $btnAction;
    }

    public function returnShipmentAvailableAction($itemId, $status) {
        
        $dropdown = '<div class="floatLeft" style="height:4px;">
            <img src="/images/default/buyer/drp_down.gif" border="0"/ width="162">
            </div>
            <div class="dropBg">
            <ul> ';
        switch ($status) {
            case 0:   // payment Awaited
            case 1: // order in progress ; payment received

                break;


            case 2:
                // order shipped
                
                $dropdown.='<li><a href="/buyer/confirm-delivery/item/' . $itemId . '" title="Confirm Delivery">Confirm Delivery</a></li>';
                $dropdown.='<li><a href="/buyer/leave-feedback/item/' . $itemId . '" title="Leave A Feedback">Leave A Feedback</a></li>';
                break;

            case 3:
                $dropdown.='<li><a href="/buyer/leave-feedback/item/' . $itemId . '" title="Leave A Feedback">Leave A Feedback</a></li>';
                break;
            case 4:
            case 5:
                $dropdown = '';
                break;
        }
        $dropdown.='</ul></div>';
        return $dropdown;
    }

    function returnDropdownActions($orderStatus, $itemTotal, $itemShipmentDone, $itemId, $orderid) {
        $btnAction = '<div class="floatLeft" style="height:6px;">
			<img src="/images/default/buyer/orders/drp_down.gif" border="0"/>
			</div> 
			<div class="dropBg"> <ul> ';
        if ($orderStatus == 0) {
            $btnAction.='
					<li><a href="/admin/orders/cancelitem/oii/' . $itemId . '" title="Cancel order">Cancel order</a></li>
					';
        }

        if ($orderStatus == 1 && ($itemShipmentDone == 0)) {
            $btnAction.='
					<li><a href="/admin/orders/createshipments/cs/' . $itemId . '" title="Confirm order">Create shipment</a></li>
                    <li><a href="/admin/orders/cancelitem/itemid/' . $itemId . '" title="Cancel order">Cancel order</a></li>
                    <li><a href="#" title="Print packaging slip">Print packaging slip</a></li>';
        }

        if (($orderStatus == 1) && ($itemTotal != $itemShipmentDone) && ($itemShipmentDone != 0)) {

            $itemshipped = $this->shipmentDetailOfid($itemId);
            $addEditShipment = '';
            if (count($itemshipped) == 1) {
                $addEditShipment = '<li><a href="/admin/orders/createshipments/shid/' . $itemshipped[0]['shipment_id'] . '" title="Edit shipment">Edit shipment</a></li>';
            } else if (count($itemshipped) > 1) {

                $addEditShipment = '<li><a href="/admin/orders/orderdetail/id/' . $orderid . '" title="Edit shipment">Edit shipment</a></li>';
            }
            $btnAction.='<li><a href="/admin/orders/createshipments/cs/' . $itemId . '" title="Create shipment">Create shipment</a></li>
					' . $addEditShipment . '
                    <li><a href="/admin/orders/cancelitem/itemid/' . $itemId . '" title="Cancel order">Cancel order</a></li>
                    <li><a href="#" title="Print packaging slip">Print packaging slip</a></li>';
        }
        if (($orderStatus == 1) && ($itemTotal == $itemShipmentDone)) {
            $addEditShipment = '';
            $itemshipped = $this->shipmentDetailOfid($itemId);
            //echo count($itemshipped);exit;
            if (count($itemshipped) == 1) {
                $addEditShipment = '<li><a href="/admin/orders/createshipments/shid/' . $itemshipped[0]['shipment_id'] . '" title="Edit shipment">Edit shipment</a></li>';
            } else if (count($itemshipped) > 1) {

                $addEditShipment = '<li><a href="/admin/orders/orderdetail/shid/' . $orderid . '" title="Edit shipment">Edit shipment</a></li>';
            }
            $btnAction.='
				
					' . $addEditShipment . '
                    <li><a href="/admin/orders/cancelitem/itemid/' . $itemId . '" title="Cancel order">Cancel order</a></li>
                    <li><a href="#" title="Print packaging slip">Print packaging slip</a></li>';
        }
        if ($orderStatus == 2) {
            $itemshipped = $this->shipmentDetailOfid($itemId);
            $btnAction.='<li><a href="/admin/orders/createshipments/shid/' . $itemshipped[0]['shipment_id'] . '" title="Confirm order">Edit shipment</a></li>';
        }
        if ($orderStatus == 4) {
            $btnAction = '';
        }
        if ($orderStatus == 5) {
            $btnAction = '';
        }
        $btnAction.='</ul></div>';
        return $btnAction;
    }

    public function shipmentDetailOfid($itemid) {
        $shipments = array();
        $db = Zend_Db_Table::getDefaultAdapter();
        $sqlShipmenCreated = "select *  from order_shipment where order_item_id=" . $itemid . " and order_shipment_status>='1' group by shipment_id ";
        $result = $db->query($sqlShipmenCreated);
        $resultSet = $result->fetchAll();
        $shipments = $resultSet;
        return $shipments;
    }

    public function totalitemshipment($shipmentid) {
        $db = Zend_Db_Table::getDefaultAdapter();
        //echo "SELECT sum(order_shipment_total) as total from order_shipment where shipment_id=".$shipmentid;exit;
        $sqlShipmenCreated = "SELECT sum(order_shipment_total) as total from order_shipment where shipment_id=" . $shipmentid;
        $result = $db->query($sqlShipmenCreated);
        $resultSet = $result->fetchAll();
        $shipments = $resultSet[0]['total'];
        return $shipments;
    }

    public function returnRemarksOrderShipment($shipmentid, $shipmentstatus) {
        //echo $shipmentstatus;exit;
        if ($shipmentstatus == 1) {

            return $this->totalitemshipment($shipmentid) . ' items packed - confirm shipment - ';
        }

        if (($shipmentstatus == 2)) {
            return 'Items shipped, Awaiting delivery -';
        }
    }

}