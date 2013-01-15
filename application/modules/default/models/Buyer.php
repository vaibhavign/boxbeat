<?php

/**
 * A customer can purchase various items from different shop and can pack as a single Order . 
 * These Items can be purchased by different Shops whose shop owner Id will be saved with each
 * Order Item. 
 */
class Default_Model_Buyer extends DML {

    function __construct() {
        parent::__construct();
    }

    /**
     * function getPurchaseDetails() will be used to fetch the purchase details according to the paramenter passed. 
     *
     * @param integer $cust_id  Customer ID
     * @param array $search     The Search Elements in form of associative array
     * @param integer $shop_owner_id   If we pass Shop Owner Id as well, It will fetch the purchase details that is purchased from this shop only
     * @return array    The result set
     * 
     * Created By: Ankit Vishwakarma <ankitvishwakarma@sify.com>
     * Created On: May 8 , 2011
     */
    public function getPurchaseDetails($cust_id, $search=NULL, $shop_owner_id=NULL) {

        $where = array('o.customer_id' => $cust_id);
        if ($search != NULL) {
            if (!empty($search['search_text'])) {
                if ($search['criteria'] == 'order_id')
                    $where['o.order_id'] = $search['search_text'];
                if ($search['criteria'] == 'order_item_id')
                    $where['oi.order_item_id'] = $search['search_text'];
            }
            if ($search['search_status'] != 'all' && !empty($search['search_status']))
                $where['oi.order_item_status'] = $search['search_status'];
        }
        if ($shop_owner_id != NULL) {
            $where['oi.oder_item_owner'] = $shop_owner_id;
        }

        $this->select('*')
                ->from('orders as o')
                ->join('order_item as oi', 'o.order_id=oi.order_id', 'inner')
                ->join('order_product_detail as opd', 'opd.product_id=oi.order_product_detail_id', 'left')
                ->join('user as u', 'u.id=' . $cust_id, 'left')
                ->join('mall_detail as md', 'md.user_id=oi.oder_item_owner', 'left')
                ->join('order_payment_modules as pm', 'pm.short_desc=o.payment_module', 'left')
                ->join('order_addresses as oa', 'oa.order_address_id=o.order_address_id', 'left')
                ->where($where);
        if ($search != NULL) {
            if (!empty($search['search_text'])) {
                if ($search['criteria'] == 'seller_name')
                    $this->like('u.user_full_name', $search['search_text']);
            }
            if (!empty($search['search_text'])) {
                if ($search['criteria'] == 'product_name')
                    $this->like('opd.product_name', $search['search_text']);
            }
            if (!empty($search['from']) && !empty($search['to']))
                $this->between('o.order_place_date', $search['from'], $search['to']);
        }

        $list = $this->get()->resultArray();
        // echo $this->lastQuery();
        //exit;
        return $list;
    }

    /**
     *  function getShopOwnerIdByCustomerId() will be used to fetch the distinct Shop owner Id 
     *  with respect to Customer Id;
     *  
     * @param integer $cust_id
     * @return array The Result Set 
     */
    public function getShopOwnerIdByCustomerId($cust_id) {
        return $this->select('A.oder_item_owner')
                ->from('order_item as A')
                ->join('orders as B', 'A.order_id=B.order_id', 'left')
                ->where('B.customer_id', $cust_id)
                ->groupBy('A.oder_item_owner')
                ->get()
                ->resultArray();
    }

    public function getMallDetailByOwnerId($owner_id) {
        return $this->getWhere('mall_detail', array('user_id' => $owner_id))->rowArray();
    }

    public function getSavedShipmentIds($order_id, $customer_id) {
        return $this->select('shipment_id')
                ->from('order_shipment as a')
                ->join('order_item as b', "b.order_item_id=a.order_item_id and a.order_shipment_status ='2'", 'inner')
                ->join('orders as o', 'b.order_id=o.order_id', 'inner')
                ->whereAfter(array('b.order_id' => $order_id, 'o.customer_id' => $customer_id), '', false)
                ->get()
                ->resultArray();
    }

    public function getShipmentDetails($buyer_id, $data=NULL) {
        // print_r($data);
        $where = array();
        if ($data['search_status'] != 'all' && $data != NULL) {
            $where['A.order_shipment_status'] = $data['search_status'];
        }

        $this->select('*')
                ->from('order_shipment as A');

        $this->join('order_item as B', "A.order_item_id=B.order_item_id and A.order_shipment_status >='2'", 'inner');
        $this->join('order_product_detail as C', 'C.product_id=B.order_product_detail_id', 'inner');

        //$this->join('order_addresses as D', "D.order_address_id=B.order_address_id and D.customer_id='" . $buyer_id . "'", 'inner');
        $this->join('order_addresses as D', "D.order_address_id=B.order_address_id", 'inner');

        $this->join('order_ship_carrier_detail as E', 'E.order_shipment_id =A.shipment_id', 'left');


        if ($data != NULL) {
            if ($data['search_text'] != '') {
                if ($data['criteria'] == 'shipment_id')
                    $this->like('A.shipment_id', $data['search_text']);
                if ($data['criteria'] == 'tracking_id') {
                    $where['E.tracking_id'] = $data['search_text'];
                }
                if ($data['criteria'] == 'shipped_to')
                    $this->like('D.fullname', $data['search_text']);
                if ($data['criteria'] == 'city')
                    $this->like('D.city', $data['search_text']);
                if ($data['criteria'] == 'state')
                    $this->like('D.state', $data['search_text']);
            }
            if ($data['from'] != '' && $data['to'] != '')
                $this->between('A.order_shipment_date_created', $data['from'], $data['to']);
        }
        if (!empty($where))
            $this->whereAfter($where, '', false);

        $this->groupBy('A.shipment_id');
        $this->orderBy('A.order_shipment_date_created', 'DESC');
        $items = $this->get()->resultArray();
        //echo $this->lastQuery();
        return $items;
    }

    public function getBuyerShipmentDetails($user_id, $search_data, $shipment_id) {
        $data['shipment_details'] = $this->getShipmentDetails($user_id, $search_data, $shipment_id);

        $data['summary'] = $this->getShipmentCounter($shipment_id);
        return $data;
    }

    public function getMallDetailsBySellerId($seller_id) {
        return $this->select('*')
                ->from('mall_detail')
                ->join('user', 'mall_detail.user_id=user.id', 'inner')
                ->where('user_id', $seller_id)
                ->get()
                ->rowArray();
    }

    public function getFulfilmentDetails($shipment_id) {
        $items = $this->select('os.shipment_owner,oa.*,oscd.*,os.order_shipment_status,md.title')
                ->from('order_ship_carrier_detail as oscd')
                ->join('order_shipment as os', "os.shipment_id=oscd.order_shipment_id and os.shipment_id='" . $shipment_id . "' and os.order_shipment_status='2'", 'inner')
                ->join('order_item as oi', 'oi.order_item_id=os.order_item_id', 'inner')
                ->join('order_addresses as oa', 'oa.order_address_id=oi.order_address_id', 'inner')
                ->join('mall_detail as md ', 'md.user_id=oi.oder_item_owner', 'inner')
                ->get()
                ->resultArray();
        //echo $this->lastQuery();
        return $items;
    }

    public function getOrderTotal($orderId) {
        $order_count = $this->getWhere('order_item', array('order_id' => $orderId))->resultArray();

        $data['order_count'] = count($order_count);

        // Counting Total Amount
        $items = $this->select('SUM(c.product_mrp*a.order_item_total) as total_charge')
                ->from('order_item as a')
                ->join('order_product_detail as c', 'a.order_product_detail_id= c.product_id ', 'left')
                ->where('a.order_id', $orderId)
                ->get()
                ->rowArray();
        $data['charge'] = $items['total_charge'];
        return $data;
    }

    public function getBillingDetail($orderid) {

        $address = $this->select('*')
                ->from('orders as o')
                ->join('order_addresses as oa', 'o.order_address_id =oa.order_address_id', 'inner')
                ->join('order_payment_modules as opm', 'opm.short_desc=o.payment_module', 'inner')
                ->join('user as u', 'u.id=o.customer_id', 'inner')
                ->where('o.order_id', $orderid)
                ->get()
                ->rowArray();
        return $address;
    }

    public function getShippingDetails($ship_id) {

        $shipping_details = $this->select('oa.*')
                ->from('order_shipment as os')
                ->join('order_item as oi', "oi.order_item_id=os.order_item_id and os.shipment_id='" . $ship_id . "' and  os.order_shipment_status='2'", 'inner')
                ->join('order_addresses as oa', 'oa.order_address_id=oi.order_address_id', 'inner')
                ->get()
                ->rowArray();
        return $shipping_details;
    }

    public function getPurchaseInfo($orderId) {
        return $this->select('*')
                ->from('orders as o')
                ->join('user as u', 'o.customer_id=u.id', 'left')
                ->join('order_payment_modules as opm', 'opm.short_desc=o.payment_module', 'left')
                ->where(array('o.order_id' => $orderId))
                ->get()
                ->rowArray();
    }

    /**
     * 
     * @param integer $ship_id 
     */
    public function getMallInfoByShipmentId($ship_id) {
        $mall_info = $this->select('user_id,title')
                ->from('order_shipment as os')
                ->join('order_item as oi', "oi.order_item_id=os.order_item_id and os.shipment_id='" . $ship_id . "'", 'inner')
                ->join('mall_detail as md', 'md.user_id=oi.oder_item_owner', 'inner')
                ->groupBy('md.user_id')
                ->get()
                ->resultArray();
        //echo $this->lastQuery();
        return $mall_info;
    }

    public function getShippingTotal($ship_id) {
        $ship_count = $this->getWhere('order_shipment', array('shipment_id' => $ship_id))->resultArray();

        $data['total_items'] = count($ship_count);

        //counting Total Quantity 
        $quantity = $this->select('SUM(oi.order_item_total) as total_quantity')
                ->from('order_shipment as os')
                ->join('order_item as oi', "oi.order_item_id=os.order_item_id and os.shipment_id='" . $ship_id . "' and os.order_shipment_status='2'", 'inner')
                ->get()
                ->rowArray();
        // echo $this->lastQuery();
        $data['total_quantity'] = $quantity['total_quantity'];
        // Counting Total Amount
        $amount = $this->select('SUM(opd.product_mrp*oi.order_item_total) as total_cost')
                ->from('order_shipment as os')
                ->join('order_item as oi', "oi.order_item_id=os.order_item_id and os.shipment_id='" . $ship_id . "'", 'inner')
                ->join('order_product_detail as opd', 'oi.order_product_detail_id= opd.product_id ', 'inner')
                ->get()
                ->rowArray();
        $data['total_cost'] = $amount['total_cost'];
        return $data;
    }

    public function getShipmentItemDetails($ship_id, $seller_id=NULL) {
        $this->select('os.shipment_id,os.order_item_id,oi.order_id,oi.order_item_total,opd.*')
                ->from('order_shipment as os');
        if ($seller_id != NULL)
            $this->join('order_item as oi', "os.order_item_id=oi.order_item_id and os.shipment_id='" . $ship_id . "' and oi.oder_item_owner='" . $seller_id . "' and os.order_shipment_status='2'", 'inner');
        else
            $this->join('order_item as oi', "os.order_item_id=oi.order_item_id and os.shipment_id='" . $ship_id . "'", 'inner');
        $items = $this->join('order_product_detail as opd', 'oi.order_product_detail_id=opd.product_id', 'inner')
                ->get()
                ->resultArray();
        //echo $this->lastQuery();
        return $items;
    }

    public function getPaymentModuleByOrderItemId($order_item_id) {
        $module = $this->select('payment_module')
                ->from('orders as o')
                ->join('order_item as oi', "o.order_id=oi.order_id and oi.order_item_id='" . $order_item_id . "'", 'inner')
                ->get()
                ->rowArray();
        return $module['payment_module'];
    }

    public function getShipmentIDByOrderId($orderId) {

        $ship_details = $this->select('shipment_id')
                ->from('order_shipment as a')
                ->join('order_item as b', 'a.order_item_id=b.order_item_id', 'inner')
                ->where(array('b.order_id' => $orderId, 'a.order_shipment_status!' => 0))
                ->groupBy('shipment_id')
                ->orderBy('a.order_shipment_date_created', 'DESC')
                // ->whereIn('order_item_id', $oids)
                ->get()
                ->resultArray();
        // echo $this->lastQuery();
        // exit;
        return $ship_details;
    }

    function getShipmentSummary($shipment_id, $seller_id=NULL) {
        $data['shipping_address'] = $this->getShippingDetails($shipment_id);


        $data['purchase_info'] = $this->getPurchaseInfo($shipment_id);
        $data['shipment_total'] = $this->getShippingTotal($shipment_id);
        $data['shipment_item_detail'] = $this->getShipmentItemDetails($shipment_id, $seller_id);
//        $data['ship_details'] = $this->getShipmentIDByOrderId($orderId);
        $data['fulfilment_details'] = $this->getFulfilmentDetails($shipment_id);
        return $data;
    }

}

?>