<?php
class Secure_Model_Cart implements SeekableIterator, Countable, ArrayAccess 
{
     /**
     * The cart item objects
     *
     * @var array
     */
    protected $_items ;
        
    /**
     * Total before shipping
     * @var decimal
     */
    protected $_subTotal = 0;
    
    /**
     * Total with shipping
     * @var decimal
     */
    protected $_total = 0;
    
    /**
     * The shipping cost
     * @var decimal
     */
    protected $_shipping = 0;
    
    /**
     * ZNS for Persistance
     * 
     * @var Zend_Session_Namespace
     */
	 
	private $cart_mapper;
    protected $_sessionNamespace;
	public function __construct()
	{
	 	$this->loadSession();
 		$this->cart_mapper = new Secure_Model_CartMapper(); 
	}
	/**
     * Vlidate the inputs of cart
     *
     * @param int $qty
     * @return return true if validated else return error
     */	
	public function validateCartInput($cartInput)
	{
		$errors=array();
		//validation for the api key registered
		
		
		//validate the quantity i.e greater than 0 and valide number
		
		//validate the mrp 
		
		//validate shipping price  
		
		//check the the image exist or assign no image of own
		
		return $errors;
		
		}
     /**
     * Adds or updates an item contained with the shopping cart
     *
     * @param Storefront_Resource_Product_Item_Interface $product
     * @param int $qty
     * @return Storefront_Resource_Cart_Item
     */
    public function addItem($cartInput)
    {
	 	$cartMapper = new Secure_Model_CartMapper(); 
		
		$item->product_id = (int) $cartInput['o2oProductId'];
	
		
        //$item->customer_id = $cartInput['ProductName'];
				$item->product_name = $cartInput['o2oProductName'];
				$item->product_qty = (int) $cartInput['o2oProductQty'];
				$item->product_maxqty = $cartInput['o2oProductmaxqty'];
				$item->product_mrp = $cartInput['o2oProductMrp'];
				$item->productImageSrc = $cartInput['o2oimagelocation'];
				$item->product_url = $cartInput['o2oProductlink'];
				$item->product_shipping	= $cartInput['o2oshippingtype'];
				//$item->product_instant_checkout=$v['o2oCheckouttype'];
				//$item->address_book_id=0;
				$item->storeApiKey   = $cartInput['o2oApikey'];
				$item->title= $cartMapper->storetitle($cartInput['store_api_key']);
				$this->_items[$item->storeApiKey."_".$item->product_id] =  $item;
				$item->variationcode=$cartInput['o2oVariationcode'];
				$item->ckeckouttype=$cartInput['o2oCheckouttype'];
				$item->currencytype=$cartInput['o2oCurrencytype'];
				$item->shippingtype=$cartInput['o2oshippingtype'];
				$item->shippingsubtype=$cartInput['o2oshippingsubtype'];
				$item->shippinglocation=$cartInput['o2oshippinglocation'];
				$item->excludedcity=$cartInput['o2oexcludedcity'];
				$item->addedtime=time();
				$this->_items[$item->storeApiKey."_".$item->product_id] =  $item;
				
		/*$item->productId           = (int) $cartInput['productId'];
        $item->productName         = $cartInput['productName'];
        $item->productMrp          = $cartInput['productMrp'];
		$item->productSrp          = $cartInput['productSrp'];
		$item->productMaxQty       = $cartInput['productMaxQty'];
		$item->productShipping     = $cartInput['productShipping'];
        $item->productQty          = (int) $cartInput['productQty'];
		$item->storeApiKey         = $cartInput['storeApiKey'];
		$item->productImageSrc     = $cartInput['productImageSrc'];
			$item->storetitle     = $cartMapper->storetitle($data);
		$item->product_instant_checkout=0;
		$this->_items[$item->storeApiKey."_".$item->productId] =  $item;
		*/
        $this->persist();
		$session =  new Zend_Session_Namespace('USER');
		$ori = new Zend_Session_Namespace('original_login');
		
		if($ori->userId)
			{
			$data = array(
				'product_id'   => $item->productId,
				'customer_id' => $ori->userId,
				'product_name' => $item->productName,
				'product_qty' => $item->productQty,
				'product_maxqty'=>$item->productMaxQty,
				'product_mrp' => $item->productMrp,
				'product_imagesrc'=>$item->productImageSrc,
				'product_url'=>$item->product_url,
				'product_shipping'=>$item->product_shipping,
				'product_dateadded'=>$item->addedtime,
				'product_datemodified'=>$item->addedtime,
				'address_book_id'=>'',
				'store_api_key'=>$item->storeApiKey,
				'variationcode'=>$item->variationcode,
				'ckeckouttype'=>$item->ckeckouttype,
				'currencytype'=>$item->currencytype,
				'shippingtype'=>$item->shippingtype,	
				'shippingsubtype'=>$item->shippingsubtype,	
				'shippinglocation'=>$item->shippinglocation,	
				'excluded_city'=>$item->excludedcity			
           );
		    
			 $cartMapper->saveCartItem($data);
			//$map->saveCartItem($data);
			}
	
    }
	public function updateItem($cartInput,$totalItems,$checkout='')
	{
	   $session =  new Zend_Session_Namespace('USER');
		$ori = new Zend_Session_Namespace('original_login');
	   $ns = new Zend_Session_Namespace('Api_Model_Cart');
	  
	   if(!empty($cartInput))
	   {
	   		for($i=1;$i<=$totalItems;$i++)
			{
				if(array_key_exists ($cartInput['storeapikey'.$i].'_'.$cartInput['productid'.$i],$ns->items))
				{
				     if($checkout)
					 {
					 	$ns->items[$cartInput['storeapikey'.$i].'_'.$cartInput['productid'.$i]]->addressBookId=$cartInput['shippingBox_'.$i];
					 }
					 $ns->items[$cartInput['storeapikey'.$i].'_'.$cartInput['productid'.$i]]->productQty=$cartInput['productQty'.$i];
					 if($ori->userId)
						{
							$cartMapper = new Secure_Model_CartMapper(); 
							$cartMapper->updateCartItem($ori->userId,$cartInput['storeapikey'.$i],$cartInput['productid'.$i],$cartInput['productQty'.$i],$cartInput['shippingBox_'.$i]);
							
						}
				}
			}
	   }
	   	

		return true;
    }
    /**
     * Remove an item for the shopping cart
     * 
     * @param int|Storefront_Resource_Product_Item_Interface $product
     */
    public function removeItem($key,$productid,$vcode)
    {
		//$ns = new Zend_Session_Namespace('Api_Model_Cart');
		//echo "<pre>";
		//print_r($ns);
		//exit;
		$session =  new Zend_Session_Namespace('USER');
		$ori = new Zend_Session_Namespace('original_login');
		if($ori->userId)
		{
		
			$cartMapper = new Secure_Model_CartMapper();
			
			$cartMapper->deleteItem($key,$productid,$ori->userId,$vcode);
			$ns = new Zend_Session_Namespace('Api_Model_Cart');
       		unset($ns->items[$key."_".$productid."_".$vcode]);
		}
       // $this->persist();
    }
	 public function removeItembn($key,$productid,$vcode)
    {
		//$ns = new Zend_Session_Namespace('Api_Model_Cart');
		//echo "<pre>";
		//print_r($ns);
		//exit;
		$session =  new Zend_Session_Namespace('USER');
		$ori = new Zend_Session_Namespace('original_login');
		if($ori->userId)
		{
		
			$cartMapper = new Secure_Model_CartMapper();
			
			//$cartMapper->deleteItem($key,$productid,$ori->userId,$vcode);
			$ns = new Zend_Session_Namespace('Api_Model_Cart_Buynow');
       		unset($ns->items[$key."_".$productid."_".$vcode]);
		}
       // $this->persist();
    }
	function paymentModules()
	{
		$cartMapper = new Secure_Model_CartMapper(); 
		$pModules=$cartMapper->paymentModules();
		return $pModules;
	}
	public function getPaymentmethod($paymentSyntax="")
	{
		$method=$this->cart_mapper->paymentModules();
		return $method;
	}
	function getShippingAddresses()
	{
		$session =  new Zend_Session_Namespace('USER');
		$ori = new Zend_Session_Namespace('original_login');
		$cartMapper = new Secure_Model_CartMapper(); 
		$shippingaddresses=$cartMapper->myAddresses($ori->userId);
		return $shippingaddresses;
	
	}
	function insertAddress($data)
	{
		$cartMapper = new Secure_Model_CartMapper(); 
		$shippingaddresses=$cartMapper->insertMyAddress($data);
	
	}
	function getBillingAddresses()
	{
		$session =  new Zend_Session_Namespace('USER');
		$ori = new Zend_Session_Namespace('original_login');
		$cartMapper = new Secure_Model_CartMapper(); 
		$billingaddresses=$cartMapper->myBillingAddresses($ori->userId);
		return $billingaddresses;
	}
	function getDetailAddress($id,$type="")
	{
		$session =  new Zend_Session_Namespace('USER');
		$ori = new Zend_Session_Namespace('original_login');
		$cartMapper = new Secure_Model_CartMapper(); 
		//$addresses=$cartMapper->myBillingAddressesDetail($id,$type,$session->userId);
		$addresses=$cartMapper->myBillingAddressesDetail($id,$ori->userId);
		return $addresses;
	}
	function getDetailAddressOrder($id,$type="")
	{
		$session =  new Zend_Session_Namespace('USER');
		$ori = new Zend_Session_Namespace('original_login');
		$cartMapper = new Secure_Model_CartMapper(); 
		//$addresses=$cartMapper->myBillingAddressesDetailOrder($id,$type,$session->userId);
		$addresses=$cartMapper->myBillingAddressesDetailOrder($id,$ori->userId);
		return $addresses;
	}
	function updateAddress($data)
	{
		$session =  new Zend_Session_Namespace('USER');
		$ori = new Zend_Session_Namespace('original_login');
		$cartMapper = new Secure_Model_CartMapper(); 
		$addresses=$cartMapper->updateMyAddress($data,$ori->userId);
	
	}
	function updatesingleAddress($data,$addressbookid)
	{
	    $session =  new Zend_Session_Namespace('USER');
		$cartMapper = new Secure_Model_CartMapper(); 
		$addresses=$cartMapper->updateaddress($data,$addressbookid);
	
	}
	function getAllcities($stateid='')
	{
		$cartMapper = new Secure_Model_CartMapper(); 
		return $cities=$cartMapper->allCities($stateid);
	}
	function getAllstates()
	{
		$cartMapper = new Secure_Model_CartMapper(); 
		return $states=$cartMapper->allStates();
	}
    /**
     * Setter for the session namespace
     * 
     * @param Zend_Session_Namespace $ns 
     */
    public function setSessionNs(Zend_Session_Namespace $ns)
    {
        $this->_sessionNamespace = $ns;
    }
    /**
     * Getter for session namespace
     * 
     * @return  Zend_Session_Namespace
     */
    public function getSessionNs()
    {
        if (null === $this->_sessionNamespace) {
            $this->setSessionNs(new Zend_Session_Namespace('Api_Model_Cart'));
        }
        return $this->_sessionNamespace;
    }
    /**
     * Persist the cart data in the session
     */
    public function persist()
    {
		  $this->getSessionNs()->items = $this->_items;
        //$this->getSessionNs()->cart = $this->_item;
		//$this->getSessionNs()->qty = $this->qty;
       // $this->getSessionNs()->shipping = $this->getShippingCost();
	    //$this->getSessionNs()->shipping =$this->shipping;
    }
    /**
     * Load any presisted data
     */
    public function loadSession()
    {
        if (isset($this->getSessionNs()->items)) {
            $this->_items = $this->getSessionNs()->items;
        }
        if (isset($this->getSessionNs()->shipping)) {
          
		    $this->setShippingCost($this->getSessionNs()->shipping);
        }
    }
    /**
     * Calculate the totals
     */
    public function CalculateTotals($qty, $srp, $cost)
    {
	   	$sub = ($qty * $srp) + ($qty * $cost);
        return $this->_subTotal = number_format($sub, 2, '.', '');
    }
    /**
     * Set the shipping cost
     *
     * @param float $cost
     */
    public function setShippingCost($cost)
    {
        $this->_shipping = $cost;
        $this->CalculateTotals();
        $this->persist();
    }
    /**
     * Get the shipping cost
     * 
     * @return float 
     */
    public function getShippingCost()
    {
        $this->CalculateTotals();
        return $this->_shipping;
    }
    /**
     * Get the sub total
     * 
     * @return float 
     */
    public function getSubTotal($qty="", $srp="", $cost="")
    {
        $this->CalculateTotals($qty, $srp, $cost);
        return $this->_subTotal;
    }
    /**
     * Get the basket total
     * 
     * @return float
     */
    public function getTotal()
    {
        $this->CalculateTotals();
        return $this->_total;
    }
    /**
     * Does the given offset exist?
     *
     * @param string|int $key key
     * @return boolean offset exists?
     */
    public function offsetExists($key)
    {
        return isset($this->_items[$key]);
    }
    /**
     * Returns the given offset.
     *
     * @param string|int $key key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->_items[$key];
    }
    /**
     * Sets the value for the given offset.
     *
     * @param string|int $key
     * @param mixed $value
     */
    public function offsetSet($key, $value)
    {
        return $this->_items[$key] = $value;
    }
    /**
     * Unset the given element.
     *
     * @param string|int $key
     */
    public function offsetUnset($key)
    {
        unset($this->_items[$key]);
    }
    /**
     * Returns the current row.
     *
     * @return array|boolean current row 
     */
    public function current()
    {
        return current($this->_items);
    }
    /**
     * Returns the current key.
     *
     * @return array|boolean current key
     */
    public function key()
    {
        return key($this->_items);
    }
    /**
     * Moves the internal pointer to the next item and
     * returns the new current item or false.
     *
     * @return array|boolean next item
     */
    public function next()
    {
        return next($this->_items);
    }
    /**
     * Reset to the first item and return.
     *
     * @return array|boolean first item or false
     */
    public function rewind()
    {
       // return reset($this->_items);
    }
    /**
     * Is the pointer set to a valid item?
     *
     * @return boolean valid item?
     */
    public function valid()
    {
       // return current($this->_items) !== false;
    }
    /**
     * Seek to the given index.
     *
     * @param int $index seek index
     */
    public function seek($index)
    {
        $this->rewind();
        $position = 0;

        while ($position < $index && $this->valid()) {
            $this->next();
            $position++;
        }

        if (!$this->valid()) {
            throw new SF_Model_Exception('Invalid seek position');
        }
    }
    /**
     * Count the cart items
     *
     * @return int row count
     */
    public function count()
    {
        return count($this->_items);
    }
	public function restoreContent() 
	{	
			$sessionitem = new Zend_Session_Namespace('Api_Model_Cart');
			$user = new Zend_Session_Namespace('USER');
			$ori = new Zend_Session_Namespace('original_login');
			if($ori->userId=='')
			{
				return false;
			}

			$cartMapper = new Secure_Model_CartMapper(); 
			if(!empty($sessionitem->items))
				{
				
				foreach($sessionitem->items as $key=>$val)
				{
					
					$productExists=$cartMapper->productExists($ori->userId,$val->storeApiKey,$val->product_id,$val->variationcode);
					
					$pCount=count($productExists);
					 if($pCount<=0)
					 {	
						$data = array(
						'product_id' => $val->product_id,
						'customer_id' => $ori->userId,
						'product_name' => $val->product_name,
						'product_qty' => $val->product_qty,
						'product_maxqty'=>$val->product_maxqty,
						'product_mrp' => $val->product_mrp,
						'product_imagesrc'=>$val->productImageSrc,
						'product_url' => $val->product_url,
						'product_shipping' => $val->product_shipping,
						'product_dateadded'=>$val->addedtime,
						'product_datemodified'=>time(),
						'address_book_id'=>'',
						'store_api_key'=>$val->storeApiKey,
						'variationcode'=>$val->variationcode,
						'ckeckouttype'=>$val->ckeckouttype,
						'currencytype'=>$val->currencytype,
						'shippingtype'=>$val->shippingtype,
						'shippingsubtype'=>$val->shippingsubtype,
						'shippinglocation'=>$val->shippinglocation,
						'excluded_city'=>$val->excludedcity);
					 $cartMapper->saveCartItem($data);
					 }
					 else
					 {
					 //echo "data already exist";exit;
					 $preQty=$productExists[0]['product_qty'];
					 $nowQty=$val->product_qty;
					 $totalQty= $preQty+$nowQty;
					 $total_product=$cartMapper->totalproduct($val->product_id,$val->variationcode);
					 if($totalQty>$total_product)
					 {
					 	$totalQty=$total_product;
					 }
					  $data = array(
						'product_name' => $val->product_name,
						'product_mrp' => $val->product_mrp,
						'product_shipping' => $val->product_shipping,
						'product_qty'=>$totalQty,
						'product_imagesrc'=>$val->productImageSrc,
						'product_datemodified'=>time()
           				);
						$cartMapper->updateAll($data,$ori->userId,$totalQty,$val->storeApiKey,$val->product_id);
					 }
					}
				}
			unset($sessionitem->items);
			$sessionitem = new Zend_Session_Namespace('Api_Model_Cart');
			$allProduct=$cartMapper->fetchMycart($ori->userId);
			$cartMapper = new Secure_Model_CartMapper();
			
			foreach($allProduct as $k=>$v)
			{
			$item='';
				$item->product_id = (int) $v['product_id'];
        		//$item->customer_id = $cartInput['ProductName'];
				$item->product_name = $v['product_name'];
				$item->product_qty = $v['product_qty'];
				$item->product_maxqty = $v['product_maxqty'];
				$item->product_mrp = (int) $v['product_mrp'];
				$item->productImageSrc = $v['product_imagesrc'];
				$item->product_url = $v['product_url'];
				$item->product_shipping	= $v['shippingtype'];
				$item->product_instant_checkout=$v['ckeckouttype'];	
				$item->address_book_id=$v['address_book_id'];;
				$item->storeApiKey=$v['store_api_key'];
				$item->store_api_key= $cartMapper->storetitle($v['store_api_key']);
				$item->variationcode=$v['variationcode'];
				$item->ckeckouttype=$v['ckeckouttype'];
				$item->currencytype=$v['currencytype'];
				$item->shippingtype=$v['shippingtype'];
				$item->shippingsubtype=$v['shippingsubtype'];
				$item->shippinglocation=$v['shippinglocation'];
				$item->excludedcity=$v['excluded_city'];
				$item->addedtime=$v['product_dateadded'];
				
			/*	$item->productId           = (int) $v['product_id'];
				$item->productName         = $v['product_id'];
				$item->productMrp          = $v['product_mrp'];
				$item->productSrp          = $v['product_srp'];
				$item->productMaxQty       = $v['product_maxqty'];
				$item->productShipping     = $v['product_shipping'];
				$item->productQty          = (int) $v['product_qty'];
				$item->storeApiKey         = $v['store_api_key'];
				$item->productImageSrc     = $v['product_imagesrc'];
				$item->storetitle     = $cartMapper->storetitle($v['store_api_key']);
				$item->product_instant_checkout=0;*/
				$this->_items[$v['store_api_key']."_".$v['product_id']."_".$v['variationcode']] =  $item;
				$this->persist();
				
				
			
			}
		

		}
	public function checkoutrecords($product_id,$storeApiKey)
	{
            $cartMapper = new Secure_Model_CartMapper();
            $records=$cartMapper->checkuotdetail($product_id,$storeApiKey);
            return $records;
	}
	public function getproductname($product_id,$storeApiKey)
	{
            $cartMapper = new Secure_Model_CartMapper();
            $records=$cartMapper->getprotname($product_id,$storeApiKey);
            return $records;
	}
	public function getshippingcondition($product_id,$vcode)
	{
            $cartMapper = new Secure_Model_CartMapper();
            return $record=$cartMapper->shipcondition($product_id,$vcode);
	}
	public function getshippingvariation($product_id,$variationcode,$storeApiKey)
	{
            $cartMapper = new Secure_Model_CartMapper();
            return $shipvariation=$cartMapper->shipvariation($product_id,$variationcode,$storeApiKey);
	}
	public function getproductdetail($pid,$vcode,$apikey,$qty)
	{
            $cartMapper = new Secure_Model_CartMapper();
            return $shipvariation=$cartMapper->shiprecords($pid,$vcode,$apikey,$qty);
	}
	public function getproductshippngcost($pid, $loc_id)
	{ 
	if($pid=='18192')
	{
	//echo $loc_id;exit;
	}
            $cartMapper = new Secure_Model_CartMapper();
            $productshippngcost=$cartMapper->productshippngcost($pid, $loc_id);
            return $productshippngcost;
	}
	public function getOrderDetail($orderId)
	{ 
            $cartMapper = new Secure_Model_CartMapper();
            $productshippngcost=$cartMapper->getOrderDetailById($orderId);
            return $productshippngcost;
	}
	public function getDoiscountCouponDetail($couponid)
	{
			$cartMapper = new Secure_Model_CartMapper();
            $cDetail=$cartMapper->getCoupondetail($couponid);
			
			return  $cDetail;
	}
public function getDoiscountCouponDetailById($couponid)
	{
			$cartMapper = new Secure_Model_CartMapper();
		
            $cDetail=$cartMapper->getCoupondetailById($couponid);
			
			return  $cDetail[0];
	}
	public function customerUseCouponTotal($couponid)
	{
			$session =  new Zend_Session_Namespace('original_login');
		//print_r($session);
		    $cartMapper = new Secure_Model_CartMapper();
			$total=$cartMapper->totalUseCustomerById($couponid,$session->apikey);
			return  $total;
	}
	public function updatecouponByUser($couponid)
	{
			$session =  new Zend_Session_Namespace('original_login');

		     $cartMapper = new Secure_Model_CartMapper();
			$total=$cartMapper->updatetotalUser($couponid,$session->apikey);
			return  $total;
	}
	public function getgiftDetail($gccode)
	{
		//echo "<pre>";
		//print_r($_SESSION);	
		     $userName =  new Zend_Session_Namespace('USER');
			$session =  new Zend_Session_Namespace('SESSION');	
		    $cartMapper = new Secure_Model_CartMapper();
			$gcDetail=$cartMapper->getGiftCertificateDetail($gccode,$session->ApiKey);
			return  $gcDetail;
	}
	public function getgiftDetailById($gccode)
	{
		//echo "<pre>";
		//print_r($_SESSION);	
		     $userName =  new Zend_Session_Namespace('USER');
			$session =  new Zend_Session_Namespace('SESSION');	
		    $cartMapper = new Secure_Model_CartMapper();
			$gcDetail=$cartMapper->getGiftCertificateDetailCheck($gccode);
			return  $gcDetail;
	}
		public function getProductByCatIds($catid)
		{
			$cartMapper = new Secure_Model_CartMapper();
			$pIds=$cartMapper->getCategorieDataSecure($catid);
			return  $pIds;
		}
	function getShippingAddressesSecure($id)
	{
            $session =  new Zend_Session_Namespace('USER');
            $cartMapper = new Secure_Model_CartMapper();
            $shippingaddresses=$cartMapper->getShippingAddressDetail($id);
            return $shippingaddresses;
	
	}	
	public function getPaymentMethodDetail($paymentmethod_id)
	{
            $method=$this->cart_mapper->paymentMethod($paymentmethod_id);
            return $method;
	}
public function getPaymentMethodDetailByName($paymentmethod_id)
	{
            $method=$this->cart_mapper->paymentMethodBN($paymentmethod_id);
            return $method;
	}
	public function getSmallImage($product_id)
	{
            $image = getImageFromDir($product_id,'product','small','1');
            return $image[0];
	}
	public function checkOrderAddress($address_id, $fullname, $address, $zipcode, $city, $state, $phone, $officeaddress,$userid,$storeApikeyforrest)
	{
            $billingaddress_id=$this->cart_mapper->billingAddress($address_id, $fullname, $address, $zipcode, $city, $state, $phone, $officeaddress,$userid,$storeApikeyforrest);
            return $billingaddress_id;
	}
	public function getOrderAddressesId($addressbook_id)
	{	
            $orderaddress_id=$this->cart_mapper->orderAddress($addressbook_id);
            return $orderaddress_id;
	}
	public function insertOrderAddresses($address_detail,$storeApikeyforrest)
	{	
            $orderaddress_id=$this->cart_mapper->insertOrderAdd($address_detail,$storeApikeyforrest);
            return $orderaddress_id;
	}
	public function insertOrder($customer_id, $orderaddresses_id, $paymentmode_type,$storeApikeyforrest)
	{
            $order_id=$this->cart_mapper->insertOrderMapper($customer_id, $orderaddresses_id, $paymentmode_type,$storeApikeyforrest);
            return $order_id;
	}
	public function insertOrderProductDetail($product_id, $product_variation, $product_condition, $product_name, $product_mrp, $product_shipping_price, $product_variation_code,$order_item_total,$c_detail,$formid,$storeApikeyforrest)
	{	
            $product_id=$this->cart_mapper->insertOrderProductDetailMapper($product_id, $product_variation, $product_condition, $product_name, $product_mrp, $product_shipping_price, $product_variation_code,$order_item_total,$c_detail,$formid,$storeApikeyforrest);
            return $product_id;
	}
	public function updatepersiable($product_id, $item_id,$storeApikeyforrest)
	{	
		
            $product_id=$this->cart_mapper->getProductShippingPolicyDetail($product_id, $item_id,$storeApikeyforrest);
            return $product_id;
	}
	public function insertOrderItem($orders_id, $orderaddresses_id, $order_item_owner, $order_item_total, $productid,$storeApikeyforrest)
	{
            
            $orderitem_id=$this->cart_mapper->insertOrderItemMapper($orders_id, $orderaddresses_id, $order_item_owner, $order_item_total, $productid,$storeApikeyforrest);
            return $orderitem_id;
	}
        public function UpdateBasketAddId($cust_id, $vcode, $api_key, $pid, $add_id)
        {
            $update_status=$this->cart_mapper->UpdateBasketAddId($cust_id, $vcode, $api_key, $pid, $add_id);
            return $update_status;
        }
        public function removeBasket($store_apikey, $product_id, $variationcode, $customer_id)
        {
            $delete_status=$this->cart_mapper->removeBasket($store_apikey, $product_id, $variationcode, $customer_id);
            return $delete_status;
        }
		public function getStoreLocationsByApikey($apikey)
		{
			  $r=$this->cart_mapper->getstorelocations($apikey);
            return $r;
		}
		public function getStorePhoneById($apikey)
		{
			  $r=$this->cart_mapper->getstorePhone($apikey);
            return $r;
		}
public function checkOrderAddressStorewise($fullname, $address, $zipcode, $city, $state, $phone,$email,$storeapikey)
	{
	
            $billingaddress_id=$this->cart_mapper->billingAddressStorewise($fullname, $address, $zipcode, $city, $state, $phone,$email,$storeapikey);
            return $billingaddress_id;
	}
public function insertOrderAddressesStorewise($address_detail)
	{	

            $orderaddress_id=$this->cart_mapper->insertOrderAddStorewise($address_detail);
            return $orderaddress_id;
	}
public function insertOrderStorewise($orderaddresses_id, $paymentmode_type,$email,$storeapikey)
	{
            $order_id=$this->cart_mapper->insertOrderMapperStorewise($orderaddresses_id, $paymentmode_type,$email,$storeapikey);
            return $order_id;
	}
public function insertOrderProductDetailStorewise($product_id, $product_variation, $product_condition, $product_name, $product_mrp, $product_shipping_price, $product_variation_code,$order_item_total,$c_detail,$formid)
	{	

            $product_id=$this->cart_mapper->insertOrderProductDetailMapperStorewise($product_id, $product_variation, $product_condition, $product_name, $product_mrp, $product_shipping_price, $product_variation_code,$order_item_total,$c_detail,$formid);
            return $product_id;
	}
public function insertOrderItemStorewise($orders_id, $orderaddresses_id,  $order_item_total, $productid)
	{
            
            $orderitem_id=$this->cart_mapper->insertOrderItemMapperStorewise($orders_id, $orderaddresses_id,  $order_item_total, $productid);
            return $orderitem_id;
	}
public function updatepersiableStorewise($product_id, $item_id)
	{	
		
            $product_id=$this->cart_mapper->getProductShippingPolicyDetailStorewise($product_id, $item_id);
            return $product_id;
	}
}

