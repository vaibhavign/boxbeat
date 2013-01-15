<?php
require("o2ofuncs.php");
define('HTTP_SERVER','http://sketcheeze.com');
define('HTTPS_SECURE','http://secure.sketcheeze.com');
define('HTTP_SECURE','http://secure.sketcheeze.com');
define('HTTP_SECURE_GOO2O','http://sketcheze.com');

class Secure_CartController extends Zend_Controller_Action
{
	protected $_cartModel;
	private $cart_model;

    public function init()
    {
				

	//echo  $this->view->baseUrl();
	Zend_Layout::getMvcInstance()->setLayout('secure');
	Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');
	
	$this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
	$this->view->headLink()->appendStylesheet('/css/secure/checkout_common.css');
	$this->userName = new Zend_Session_Namespace('original_login');
	$data=$this->_request->getParams();
	$this->view->controller=$data['action'];
		//$this->view->headTitle('Select your shipping address – Goo2o.com checkout');
		
	if($this->userName->userId == '' && $this->view->controller!='pgtranssecure')
		{
			/*if($this->view->controller!='acknowledgement')
			{*/
			$this->_redirect(HTTP_SECURE.'/login?tab=1');
			/*}*/
		}
	
	
		$this->objTrigger = new Notification();	
	
		$this->_cartModel = new Secure_Model_Cart();
		
		$this->view->cart_model = new Secure_Model_Cart();

		
		

    }
    public function indexAction()
    {
	}
	public function addAction()
	{
		$cartInput = $this->_request->getParams();
		$error=$this->_cartModel->validateCartInput($cartInput);
			if(empty($error))
				{
				 	$this->_cartModel->addItem($cartInput);
					$mapper  = new Api_Model_CartMapper();
					$this->_helper->redirector('view', 'cart');
				}
				else
				{

				}
	}
	public function totalItemsIncart()
	{
		$session = new Zend_Session_Namespace('Api_Model_Cart');
		return $totalItems=count($session->items);
	}
	public function subtotal()
	{
		$session = new Zend_Session_Namespace('Api_Model_Cart');
		$total=0;
		if(!empty($session->_items))
			{
		    	foreach($session->_items as $productkey=>$productval)
					{
						$total += ($productval->productSrp * $productval->productQty);
					}
			}
		return  $total;
	}
	public function totalShipping()
	{
		$session = new Zend_Session_Namespace('Api_Model_Cart');
		$total=0;
		if(!empty($session->items))
			{
		    	foreach($session->items as $productkey=>$productval)
					{
						$total += $productval->productShipping * $productval->productQty;
					}
			}
		return  $total;
	}
	public function updateAction()
	{

		$request = $this->_request->getParams();
		if(!empty($request))
		{
			$this->_cartModel->updateItem($request,$this->totalItemsIncart());
		}
		$this->_helper->redirector('view', 'cart');
	}
	public function updatecheckoutAction()
	{
		$request = $this->_request->getParams();
		if($request['confirm_x']!='' || $request['confirm']!='')
		{
			$paymentType = new Zend_Session_Namespace('paymentType');
			$paymentType->paymentType = $request['payment'];
			$paymentType->message = $request['message'];
			for($i=1;$i<=$this->totalItemsIncart();$i++)
				{
					if($request["shippingBox_".$i]==0)
					{
						$this->view->errors='dfgd dgdfg dfgdfgdfgdfg';
						$this->_redirect('/cart/checkout/err/1');
					}
				}
			if(!isset($request['payment']))
				{
					$this->_redirect('/cart/checkout/err/2');
				}
				$this->_helper->redirector('review', 'secure');
		}


		if(!empty($request))
		{
			$this->_cartModel->updateItem($request,$this->totalItemsIncart(),'checkout');
		}


		$this->_helper->redirector('checkout', 'cart');
	}
	public function deleteAction()
	{
		$request = $this->_request->getParams();
		$this->_cartModel->removeItem($request['key'],$request['id'],$request['vcode']);
		$this->_redirect('/cart/checkoutaddress');
	}
	public function deletebnAction()
	{
		$request = $this->_request->getParams();
		$this->_cartModel->removeItembn($request['key'],$request['id'],$request['vcode']);
		$this->_redirect('/cart/checkoutaddressbn');
	}
	public function deletecheckoutAction()
	{
		$request = $this->_request->getParams();
		$this->_cartModel->removeItem($request['key'],$request['id']);
		$this->_helper->redirector('/cart/checkout');
	}
	public function addressAction()
	{
		$this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
		$this->view->headLink()->appendStylesheet('/css/secure/shipping.css');
		$this->view->headScript()->appendFile('/jscript/common/jquery-1.4.2.min.js','text/javascript');
		$this->view->headScript()->appendFile('/jscript/secure/shipping.js','text/javascript');
		$this->view->totalItems=$this->totalItemsIncart();
		$this->view->subtotalAllItem=number_format($this->subtotal(),2);
		$this->view->totalShipping=number_format($this->totalShipping(),2);
		$this->view->totalCart=number_format($this->totalShipping()+$this->subtotal(),2);
		$this->view->cities=$this->_cartModel->getAllcities(0);
		$this->view->states=$this->_cartModel->getAllstates();
	}
	public function selectshippingaddressAction()
	{
		
		$sessionDiscountAmount = new Zend_Session_Namespace('CartDIscountAmount');
		unset($sessionDiscountAmount->total);
		unset($_SESSION['Cart_Coupon']['coupons']['product']);
		unset($_SESSION['Cart_Coupon']['coupons']['order']);
		unset($_SESSION['Cart_Coupon']['coupons']['shipping']);
		unset($_SESSION['Cart_GiftCertificate']['giftcoupon']);	
		//echo 'Configure Gift Certificate - '. $_SESSION['USER']['userDetails'][0]['title'].PAGE_EXTENSION;exit;
		$this->view->headTitle('Select your shipping address - Goo2o.com checkout' );
		$this->view->headMeta()->setName('keywords', 'Select your shipping address , Goo2o Technologies');
		$this->view->headMeta()->setName('description', 'Select your shipping address , Goo2o Technologies');
		$this->view->headLink()->appendStylesheet('/css/secure/checkout_common.css');
		$userName =  new Zend_Session_Namespace('USER');
		$ori = new Zend_Session_Namespace('original_login');
		$this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
        $this->view->headLink()->appendStylesheet('/css/secure/manage.css');
		$this->view->headLink()->appendStylesheet('/css/secure/manageaddress.css');
		$this->view->headScript()->appendFile('/jscript/secure/addressbook.js','text/javascript');
		//$this->view->headTitle();
		

		$session = new Zend_Session_Namespace('Api_Model_Cart');
		$this->view->totalProductcart=count($session->items);
		$data = $this->_request->getParams();
				
		$cartMapper = new Secure_Model_CartMapper();
		$sessionItem = new Zend_Session_Namespace('Api_Model_Cart');
	
		//echo $userName->userId;exit;
		if($ori->userId=='')
		{
			$this->_redirect(HTTPS_SECURE.'/secure/login?tab=1');
		}
		$this->view->shippingaddresses=$this->_cartModel->getShippingAddresses();
		
		$this->view->states=$cartMapper->allStatesSecure();
		if($data['id']!='')
			{
			$this->view->addressdetail=$cartMapper->getDetailAddressByIdSecure($this->userName->userId,$data['id']);
			$this->view->cities=$cartMapper->allcitySecure($this->view->addressdetail['state']);
			}
			
	}
	public function selectshippingaddressbnAction()
	{
	
		$sessionDiscountAmount = new Zend_Session_Namespace('CartDIscountAmount');
		unset($sessionDiscountAmount->total);
		unset($_SESSION['Cart_Coupon']['coupons']['product']);
		unset($_SESSION['Cart_Coupon']['coupons']['order']);
		unset($_SESSION['Cart_Coupon']['coupons']['shipping']);
		unset($_SESSION['Cart_GiftCertificate']['giftcoupon']);	
		//echo 'Configure Gift Certificate - '. $_SESSION['USER']['userDetails'][0]['title'].PAGE_EXTENSION;exit;
		$this->view->headTitle('Select your shipping address - Goo2o.com checkout' );
		$this->view->headMeta()->setName('keywords', 'Select your shipping address , Goo2o Technologies');
		$this->view->headMeta()->setName('description', 'Select your shipping address , Goo2o Technologies');
		$this->view->headLink()->appendStylesheet('/css/secure/checkout_common.css');
		$userName =  new Zend_Session_Namespace('USER');
		$ori = new Zend_Session_Namespace('original_login');
		$this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
        $this->view->headLink()->appendStylesheet('/css/secure/manage.css');
		$this->view->headLink()->appendStylesheet('/css/secure/manageaddress.css');
		$this->view->headScript()->appendFile('/jscript/secure/addressbook.js','text/javascript');
		//$this->view->headTitle();
		

		$session = new Zend_Session_Namespace('Api_Model_Cart_Buynow');
		$this->view->totalProductcart=count($session->items);
		$data = $this->_request->getParams();
				
		$cartMapper = new Secure_Model_CartMapper();
		$sessionItem = new Zend_Session_Namespace('Api_Model_Cart_Buynow');
	
		//echo $userName->userId;exit;
		if($ori->userId=='')
		{
			$this->_redirect(HTTPS_SECURE.'/secure/login?tab=1');
		}
		$this->view->shippingaddresses=$this->_cartModel->getShippingAddresses();
		
		$this->view->states=$cartMapper->allStatesSecure();
		if($data['id']!='')
			{
			$this->view->addressdetail=$cartMapper->getDetailAddressByIdSecure($this->userName->userId,$data['id']);
			$this->view->cities=$cartMapper->allcitySecure($this->view->addressdetail['state']);
			}
	}
	public function deleteaddressAction()
	{
		$cartMapper = new Secure_Model_CartMapper();
		$data = $this->_request->getParams();
		$cartMapper->deleteSecure($data['addid'],$this->userName->userId);
		$this->_redirect('/cart/selectshippingaddress');
	}
	public function saveAction()
	{
		$data = $this->_request->getParams();
                if(isset($data['back_x']))
                {
                    //$this->_redirect($_SERVER['HTTP_REFERER']);
                }
		$cartMapper = new Secure_Model_CartMapper();
		if($data['action']=='save')
		{
			$addressdata=array('customers_id'=>$this->userName->userId,
								'fullname'=>$data['name'],
								'address'=>$data['address'],
								'zipcode'=>$data['zipcode'],
								'city'=>$data['cityname'],
								'state'=>$data['state'],
								'phone'=>$data['phone'],
								'officeaddress'=>$data['officeaddress'],
								);
		if($data['addressid']!='')
		{

		$cartMapper->saveaddressSecure($addressdata,$data['addressid']);
		}
		else
		{
		$cartMapper->saveaddressSecure($addressdata);
		}

		$this->_redirect('/cart/selectshippingaddress');
		}
	}
	public function manageaddressAction()
	{
		$this->view->headLink()->appendStylesheet('/css/secure/manageaddress.css');
		$this->view->headLink()->appendStylesheet('/css/secure/checkout_common.css');
		$this->view->headScript()->appendFile('/jscript/common/jquery-1.4.2.min.js','text/javascript');
		$this->view->headScript()->appendFile('/jscript/secure/addressbook.js','text/javascript');
		$userName =  new Zend_Session_Namespace('USER');
		$ori = new Zend_Session_Namespace('original_login');
		$this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
		$cartMapper = new Secure_Model_CartMapper();
		$sessionItem = new Zend_Session_Namespace('Api_Model_Cart');
		if($ori->userId=='')
		{
			$this->_redirect(HTTPS_SECURE.'/secure/login?tab=1');
		}

		$data = $this->_request->getParams();
		$this->view->states=$cartMapper->allStatesSecure();
		if($data['id']!='')
			{
			$this->view->addressdetail=$cartMapper->getDetailAddressByIdSecure($this->userName->userId,$data['id']);
			$this->view->cities=$cartMapper->allcitySecure($this->view->addressdetail['state']);
			}
                //echo "<pre>";
                //print_r($_SERVER);
	}
	public function updatemyaddressAction()
	{
	$request = $this->_request->getParams();
	//echo "<pre>";
	//print_r($request);exit;
	if($request['address']!='' || $request['address_x']!='')
	{
	if($request['update']!='')
	{
	$data=array('entry_gender'=>$request['gtitle'],
						'entry_firstname'=>$request['guestfname'],
						'entry_lastname'=>$request['guestlname'],
						'entry_street_address'=>$request['guestadd1'],
						'entry_suburb'=>$request['guestadd2'],
						'entry_postcode'=>$request['pzcode'],
						'entry_phone'=>$request['guestcontact'],
						'entry_city'=>$request['shippingcity'],
						'entry_state'=>$request['shippingstate'],
						'entry_country_id'=>99,
						);
			$this->_cartModel->updateAddress($data);
			if($request['billing']=='s')
			{
				$this->_redirect('/cart/selectshippingaddress');
			}
			else
			{
				$this->_redirect('/cart/selectshippingaddress');
			}
	}
	else
	{
		$data=array('entry_gender'=>$request['gtitle'],
						'entry_firstname'=>$request['guestfname'],
						'entry_lastname'=>$request['guestlname'],
						'entry_street_address'=>$request['guestadd1'],
						'entry_suburb'=>$request['guestadd2'],
						'entry_postcode'=>$request['pzcode'],
						'entry_phone'=>$request['guestcontact'],
						'entry_city'=>$request['shippingcity'],
						'entry_state'=>$request['shippingstate'],
						'entry_country_id'=>99,
						);
			$this->_cartModel->insertAddress($data);


	}

	}
	//echo "<pre>";
	//print_r($request);exit;

	}
	public function updateaddressAction()
	{
		$request = $this->_request->getParams();
		if($request['part']=='guestshippingdetail')
		{
			$data=array('entry_gender'=>$request['gtitle'],
						'entry_firstname'=>$request['guestfname'],
						'entry_lastname'=>$request['guestlname'],
						'entry_street_address'=>$request['guestadd1'],
						'entry_suburb'=>$request['guestadd2'],
						'entry_postcode'=>$request['pzcode'],
						'entry_phone'=>$request['guestcontact'],
						'entry_city'=>$request['shippingcity'],
						'entry_state'=>$request['shippingstate'],
						'entry_country_id'=>99,
						);
			$this->_cartModel->updatesingleAddress($data,$request['update']);
		}
		exit;

	}
	public function addaddressAction()
	{
		$request = $this->_request->getParams();
		if($request['part']=='guestshippingdetail')
		{
			$session =  new Zend_Session_Namespace('USER');
			$ori = new Zend_Session_Namespace('original_login');
			$dataShip=array('entry_gender'=>$request['gtitle'],
						'entry_firstname'=>$request['guestfname'],
						'entry_lastname'=>$request['guestlname'],
						'entry_street_address'=>$request['guestadd1'],
						'entry_suburb'=>$request['guestadd2'],
						'entry_postcode'=>$request['pzcode'],
						'entry_phone'=>$request['guestcontact'],
						'entry_city'=>$request['shippingcity'],
						'entry_state'=>$request['shippingstate'],
						'entry_country_id'=>99,
						'customers_id'=> $ori->userId

						);

			$this->_cartModel->insertAddress($dataShip);

			if(!$request['checkshow'])
			{
			$dataBill=array('entry_gender'=>$request['gtitle1'],
						'entry_firstname'=>$request['guestfname1'],
						'entry_lastname'=>$request['guestlname1'],
						'entry_street_address'=>$request['guestadd11'],
						'entry_suburb'=>$request['guestadd12'],
						'entry_postcode'=>$request['pzcode1'],
						'entry_phone'=>$request['guestcontact1'],
						'entry_city'=>$request['shippingcity1'],
						'entry_state'=>$request['shippingstate1'],
						'entry_country_id'=>99,
						'customers_id'=> $ori->userId,
						'billing_address'=>'1'
						);

			$this->_cartModel->insertAddress($dataBill);
			}

		}
		exit;
	}
	public function updatecityAction()
	{
		$request = $this->_request->getParams();
		$cId=$request['dId'];
		$cities=$this->_cartModel->getAllcities($request['stateid']);
		$selectbox='<option value="0">Select</option>';
					foreach($cities as $cityKey=>$cityVal)
					{
					$selectbox.='<option value='.$cityVal['id'].'>'.$cityVal['cityname'].'</option>';
					}
					echo $selectbox;
					exit;
	}
	public function checkoutAction()
	{
	$sessionReviewwelldone = new Zend_Session_Namespace('Api_Model_Review_Welldone');
	$sessionReviewwelldone->item=array();	//echo "<pre>";
                //print_r($_SESSION);exit;
		$this->view->headTitle('Select your payment mode - Goo2o.com checkout');
		$request = $this->_request->getParams();
		if(isset($request['error']))
		{
			$error = decryptLink($request['error']);
			$errormsg = explode(':', $error);
			$this->view->errormaeesge=$errormsg;
		}
		$userName =  new Zend_Session_Namespace('USER');
		$ori = new Zend_Session_Namespace('original_login');
		$this->view->currentUserId=$ori->userId;
		$this->view->headLink()->appendStylesheet('/css/secure/checkout_payment.css');
		$cartMapper = new Secure_Model_CartMapper();
		$sessionItem = new Zend_Session_Namespace('Api_Model_Review');
		$data=$sessionItem->items;
		
		
		/*echo "<pre>";
		print_r($data);
		exit;*/
/*		echo "<pre>";
		unset($_SESSION['Api_Model_Cart']['items']['_62']);
		unset($_SESSION['Api_Model_Cart']['items']['_']);
		print_r($_SESSION);exit;
*/
		if($ori->userId=='')
				{
				$this->_redirect(HTTPS_SECURE.'/secure/login?tab=1');
				}


			if(count($sessionItem)<=0)
				{
					$this->_redirect(HTTP_SERVER.'/cart/#list');
				}
			$this->view->shippingaddresses=$this->_cartModel->getShippingAddresses();

			$this->view->billing_address = $sessionItem->billingaddress;
			$net_amt = 0;
			$this->view->allfrommetrohat=1;
			foreach($data as $key=>$productrec)
			{
			
			
				if($productrec->store_api_key[0]['mallid']!=240)
					{
						$this->view->allfrommetrohat=0;
						break;
					}
			}
			foreach($data as $key=>$productrec)
			{
			
				 if($productrec->product_id=='')
			  {
					  	unset($_SESSION['Api_Model_Review']['items'][$key]);
						continue;
			  }   
				$proname=$this->_cartModel->getproductname($productrec->product_id,$productrec->storeApiKey);
				$sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->product_name=$proname;
				$variation= $this->_cartModel->getproductdetail($productrec->product_id,$productrec->variationcode,$productrec->storeApiKey,$productrec->product_qty);
				$shippingcost=$this->_cartModel->getproductshippngcost($productrec->product_id, $productrec->address_book_id);
				$sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->storeApiKey=$productrec->storeApiKey;
				$sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->store_api_key=$productrec->store_api_key;
				$sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->variations=$variation;
				$sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->shipcost=$shippingcost;
				$store_api_key = $sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode];
				$item_total = $this->_cartModel->getSubTotal($productrec->product_qty, $productrec->variations[$productrec->product_id][srp], $shippingcost);
				$sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->purchase_amt = $item_total;
				$net_amt += $item_total;
			}

			if(count($sessionItem->items) > 0)
			{
				$this->view->checkoutdetail=$sessionItem->items;
			}
			else
			{
				$this->_redirect(HTTP_SERVER.'/cart/#list');
			}
			$sessionItem->net_amt = number_format(($net_amt),2,'.','');
			$this->view->total_pamt=$sessionItem->net_amt;
			$this->view->paymentmode_id=$sessionItem->paymentmode_id;
			$this->view->payment_method = $this->_cartModel->getPaymentmethod();
			$this->view->store=$productrec->store_api_key[0]['title'];
			$sessionCoupons = new Zend_Session_Namespace('Cart_Coupon');
			$gcCoupon= new Zend_Session_Namespace('Cart_GiftCertificate');
			$this->view->coupons=$sessionCoupons->coupons;
			$this->view->gccoupons=$gcCoupon->giftcoupon;

			$sessiondsserr = new Zend_Session_Namespace('dicounterrors');	
			$this->view->dmessages=$sessiondsserr->dmessages;
			$sessiondscheckbox = new Zend_Session_Namespace('discountcheck');	
			$this->view->checkbox=$sessiondscheckbox->checked;
			unset($sessiondsserr->dmessages);
			//echo "<pre>"; print_r($_SESSION);exit;
			/*$restoerecontent=new Secure_Model_Cart();
			$restoerecontent->restoreContent();*/
			/*echo "<pre>";
			print_r($_SESSION);*/
			}
	public function checkoutbnAction()
	{
	$sessionReviewwelldone = new Zend_Session_Namespace('Api_Model_Review_Welldone');
	$sessionReviewwelldone->item=array();	//echo "<pre>";
                //print_r($_SESSION);exit;
		$this->view->headTitle('Select your payment mode - Goo2o.com checkout');
		$request = $this->_request->getParams();
		if(isset($request['error']))
		{
			$error = decryptLink($request['error']);
			$errormsg = explode(':', $error);
			$this->view->errormaeesge=$errormsg;
		}
		$userName =  new Zend_Session_Namespace('USER');
		$ori = new Zend_Session_Namespace('original_login');
		$this->view->currentUserId=$ori->userId;
		$this->view->headLink()->appendStylesheet('/css/secure/checkout_payment.css');
		$cartMapper = new Secure_Model_CartMapper();
		$sessionItem = new Zend_Session_Namespace('Api_Model_Review_Buynow');
		$data=$sessionItem->items;
		
		
		/*echo "<pre>";
		print_r($data);
		exit;*/
/*		echo "<pre>";
		unset($_SESSION['Api_Model_Cart']['items']['_62']);
		unset($_SESSION['Api_Model_Cart']['items']['_']);
		print_r($_SESSION);exit;
*/
			if($ori->userId=='')
				{
				$this->_redirect(HTTPS_SECURE.'/secure/login?tab=1');
				}


			if(count($sessionItem)<=0)
				{
					$this->_redirect(HTTP_SERVER.'/cart/#list');
				}
			$this->view->shippingaddresses=$this->_cartModel->getShippingAddresses();

			$this->view->billing_address = $sessionItem->billingaddress;
			$net_amt = 0;
			foreach($data as $key=>$productrec)
			{
			
				 if($productrec->product_id=='')
			  {
					  	unset($_SESSION['Api_Model_Review_Buynow']['items'][$key]);
						continue;
			  }   
				$proname=$this->_cartModel->getproductname($productrec->product_id,$productrec->storeApiKey);
				$sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->product_name=$proname;
				$variation= $this->_cartModel->getproductdetail($productrec->product_id,$productrec->variationcode,$productrec->storeApiKey,$productrec->product_qty);
				$shippingcost=$this->_cartModel->getproductshippngcost($productrec->product_id, $productrec->address_book_id);
				$sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->storeApiKey=$productrec->storeApiKey;
				$sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->store_api_key=$productrec->store_api_key;
				$sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->variations=$variation;
				$sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->shipcost=$shippingcost;
				$store_api_key = $sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode];
				$item_total = $this->_cartModel->getSubTotal($productrec->product_qty, $productrec->variations[$productrec->product_id][srp], $shippingcost);
				$sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->purchase_amt = $item_total;
				$net_amt += $item_total;
			}

			if(count($sessionItem->items) > 0)
			{
				$this->view->checkoutdetail=$sessionItem->items;
			}
			else
			{
				$this->_redirect(HTTP_SERVER.'/cart/#list');
			}
			$sessionItem->net_amt = number_format(($net_amt),2,'.','');
			$this->view->total_pamt=$sessionItem->net_amt;
			$this->view->paymentmode_id=$sessionItem->paymentmode_id;
			$this->view->payment_method = $this->_cartModel->getPaymentmethod();
			$this->view->store=$productrec->store_api_key[0]['title'];
			$sessionCoupons = new Zend_Session_Namespace('Cart_Coupon');
			$gcCoupon= new Zend_Session_Namespace('Cart_GiftCertificate');
			$this->view->coupons=$sessionCoupons->coupons;
			$this->view->gccoupons=$gcCoupon->giftcoupon;

			$sessiondsserr = new Zend_Session_Namespace('dicounterrors');	
			$this->view->dmessages=$sessiondsserr->dmessages;
			$sessiondscheckbox = new Zend_Session_Namespace('discountcheck');	
			$this->view->checkbox=$sessiondscheckbox->checked;
			unset($sessiondsserr->dmessages);
			//echo "<pre>"; print_r($_SESSION);exit;
			/*$restoerecontent=new Secure_Model_Cart();
			$restoerecontent->restoreContent();*/
			/*echo "<pre>";
			print_r($_SESSION);*/
			}		
	public function checkoutaddressAction()
	{
$sessionDiscountAmount = new Zend_Session_Namespace('CartDIscountAmount');
unset($sessionDiscountAmount->total);
unset($_SESSION['Cart_Coupon']['coupons']['product']);
unset($_SESSION['Cart_Coupon']['coupons']['order']);
unset($_SESSION['Cart_Coupon']['coupons']['shipping']);
unset($_SESSION['Cart_GiftCertificate']['giftcoupon']);	
	$sessionReviewwelldone = new Zend_Session_Namespace('Api_Model_Review_Welldone');
	$sessionReviewwelldone->item=array();
				$this->view->headTitle('Select your shipping address - Goo2o.com checkout');

		$request = $this->_request->getParams();
		$this->view->updateBtnClicked=$request['err'];
		if(isset($request['error']))
		{
			$error = decryptLink($request['error']);
			$errormsg = explode(':', $error);
			$this->view->errormaeesge=$errormsg;
		}
		$userName =  new Zend_Session_Namespace('USER');
		$ori = new Zend_Session_Namespace('original_login');
		$this->view->headLink()->appendStylesheet('/css/secure/checkout_payment.css');
		$this->view->headLink()->appendStylesheet('/css/secure/ship_to_multiple_add.css');
		$cartMapper = new Secure_Model_CartMapper();
		$sessionItem = new Zend_Session_Namespace('Api_Model_Cart');
		$data=$sessionItem->items;
		
		//echo "<pre>";
		//print_r($data);
		//exit;
/*		echo "<pre>";
		unset($_SESSION['Api_Model_Cart']['items']['_62']);
		unset($_SESSION['Api_Model_Cart']['items']['_']);
		print_r($_SESSION);exit;
*/
			$db = Zend_Db_Table::getDefaultAdapter();
			if($ori->userId=='')
				{
				$this->_redirect(HTTPS_SECURE.'/secure/login?tab=1');
				}


			if(count($sessionItem)<=0)
				{
					$this->_redirect(HTTP_SERVER.'/cart/#list');
				}
			$this->view->shippingaddresses=$this->_cartModel->getShippingAddresses();

			$this->view->billing_address = $sessionItem->billingaddress;
			$net_amt = 0;
			
			
			foreach($data as $key=>$productrec)
			{
			
			  if($productrec->product_id=='')
			  {
					  	unset($_SESSION['Api_Model_Cart']['items'][$key]);
						continue;
			  }
			$selects = "SELECT variant_name,variant_value FROM product_variation WHERE product_id='".$productrec->product_id."' AND variation_code='".$productrec->variationcode."' and variant_name='SRP'" ;
				$results = $db->fetchAll($selects);
				$proname=$this->_cartModel->getproductname($productrec->product_id,$productrec->storeApiKey);
				
				
				$sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->product_name=$proname;
				$sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->product_mrp=$results[0]['variant_value'];
				$variation= $this->_cartModel->getproductdetail($productrec->product_id,$productrec->variationcode,$productrec->storeApiKey,$productrec->product_qty);
				
				$shippingcost=$this->_cartModel->getproductshippngcost($productrec->product_id, $productrec->address_book_id);

				$sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->storeApiKey=$productrec->storeApiKey;
				$sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->store_api_key=$productrec->store_api_key;
				$sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->variations=$variation;
				$sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->shipcost=$shippingcost;
				$store_api_key = $sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode];
				$item_total = $this->_cartModel->getSubTotal($productrec->product_qty, $productrec->variations[$productrec->product_id][srp], $shippingcost);
				
				$sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->purchase_amt = $item_total;
				$net_amt += $item_total;
			}

			if(count($sessionItem->items) > 0)
			{
				$this->view->checkoutdetail=$sessionItem->items;
			}
			else
			{
				$this->_redirect(HTTP_SERVER.'/cart/#list');
			}
			$sessionItem->net_amt = number_format(($net_amt),2,'.','');
			$this->view->total_pamt=$sessionItem->net_amt;
			$this->view->paymentmode_id=$sessionItem->paymentmode_id;
				
			$this->view->payment_method = $this->_cartModel->getPaymentmethod();
			$this->view->store=$productrec->store_api_key[0]['title'];
										

			//echo "<pre>"; print_r($_SESSION);exit;
			/*$restoerecontent=new Secure_Model_Cart();
			$restoerecontent->restoreContent();*/
			/*echo "<pre>";
			print_r($_SESSION);*/
			}
	public function checkoutaddressbnAction()
	{
$sessionDiscountAmount = new Zend_Session_Namespace('CartDIscountAmount');
unset($sessionDiscountAmount->total);
unset($_SESSION['Cart_Coupon']['coupons']['product']);
unset($_SESSION['Cart_Coupon']['coupons']['order']);
unset($_SESSION['Cart_Coupon']['coupons']['shipping']);
unset($_SESSION['Cart_GiftCertificate']['giftcoupon']);	
	$sessionReviewwelldone = new Zend_Session_Namespace('Api_Model_Review_Welldone');
	$sessionReviewwelldone->item=array();
				$this->view->headTitle('Select your shipping address - Goo2o.com checkout');

		$request = $this->_request->getParams();
		$this->view->updateBtnClicked=$request['err'];
		if(isset($request['error']))
		{
			$error = decryptLink($request['error']);
			$errormsg = explode(':', $error);
			$this->view->errormaeesge=$errormsg;
		}
		$userName =  new Zend_Session_Namespace('USER');
		$ori = new Zend_Session_Namespace('original_login');
		$this->view->headLink()->appendStylesheet('/css/secure/checkout_payment.css');
		$this->view->headLink()->appendStylesheet('/css/secure/ship_to_multiple_add.css');
		$cartMapper = new Secure_Model_CartMapper();
		$sessionItem = new Zend_Session_Namespace('Api_Model_Cart');
		$datas=$sessionItem->items;
		
		$sessionItem = new Zend_Session_Namespace('Api_Model_Cart_Buynow');
		$sessionItem->items=$datas;
		//echo "<pre>";
		//print_r($data);
		//exit;
/*		echo "<pre>";
		unset($_SESSION['Api_Model_Cart']['items']['_62']);
		unset($_SESSION['Api_Model_Cart']['items']['_']);
		print_r($_SESSION);exit;
*/

			if($ori->userId=='')
				{
				$this->_redirect(HTTPS_SECURE.'/secure/login?tab=1');
				}


			if(count($sessionItem)<=0)
				{
					$this->_redirect(HTTP_SERVER.'/cart/#list');
				}
			$this->view->shippingaddresses=$this->_cartModel->getShippingAddresses();

			$this->view->billing_address = $sessionItem->billingaddress;
			$net_amt = 0;
			
			
			foreach($data as $key=>$productrec)
			{
			  if($productrec->product_id=='')
			  {
					  	unset($_SESSION['Api_Model_Cart']['items'][$key]);
						continue;
			  }
			
				$proname=$this->_cartModel->getproductname($productrec->product_id,$productrec->storeApiKey);
				
				$sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->product_name=$proname;
				$variation= $this->_cartModel->getproductdetail($productrec->product_id,$productrec->variationcode,$productrec->storeApiKey,$productrec->product_qty);
				
				$shippingcost=$this->_cartModel->getproductshippngcost($productrec->product_id, $productrec->address_book_id);

				$sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->storeApiKey=$productrec->storeApiKey;
				$sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->store_api_key=$productrec->store_api_key;
				$sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->variations=$variation;
				$sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->shipcost=$shippingcost;
				$store_api_key = $sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode];
				$item_total = $this->_cartModel->getSubTotal($productrec->product_qty, $productrec->variations[$productrec->product_id][srp], $shippingcost);
				
				$sessionItem->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->purchase_amt = $item_total;
				$net_amt += $item_total;
			}

			if(count($sessionItem->items) > 0)
			{
				$this->view->checkoutdetail=$sessionItem->items;
			}
			else
			{
				$this->_redirect(HTTP_SERVER.'/cart/#list');
			}
			$sessionItem->net_amt = number_format(($net_amt),2,'.','');
			$this->view->total_pamt=$sessionItem->net_amt;
			$this->view->paymentmode_id=$sessionItem->paymentmode_id;
				
			$this->view->payment_method = $this->_cartModel->getPaymentmethod();
			$this->view->store=$productrec->store_api_key[0]['title'];
										

			//echo "<pre>"; print_r($_SESSION);exit;
			/*$restoerecontent=new Secure_Model_Cart();
			$restoerecontent->restoreContent();*/
			/*echo "<pre>";
			print_r($_SESSION);*/
			}		
	public function removecouponAction()
	{
		$request = $this->_request->getParams();
		$sessionCoupons = new Zend_Session_Namespace('Cart_Coupon');
		$gcCoupon= new Zend_Session_Namespace('Cart_GiftCertificate');
		$coupons=$sessionCoupons->coupons;
		$gccoupons=$gcCoupon->giftcoupon;
		
		if($request['t']=='g')
		{		
			foreach($gccoupons as $key=>$val)
			{
					
				if($val['ccode']==$request['code'])
				
				unset($_SESSION['Cart_GiftCertificate']['giftcoupon'][$key]);
			}
		}
		if($request['t']=='d')
		{
				
			foreach($coupons as $key=>$val)
				{
			   foreach($val as $k=>$v)
					{ 
					foreach($v as $lk=>$lv)
						{
					
							if($lv['ccode']==$request['code'])
							{
							if($key=='product' && $request['key']==$lk)
							{
							unset($_SESSION['Cart_Coupon']['coupons'][$key][$k][$lk]);
							}
							else if($key!='product')
							{
							unset($_SESSION['Cart_Coupon']['coupons'][$key][$k]);
							}
							}
						}
					}
			}
		}	
	$this->_redirect('/cart/checkout');

	}			
	public function updatecartAction()
	{

        $request = $this->_request->getParams();
		$cartMapper = new Secure_Model_CartMapper();
		$userName = new Zend_Session_Namespace('USER');
		$sessionItem = new Zend_Session_Namespace('Api_Model_Cart');
		$errormaeesge=array();
		$errormaeesges=array();
		$error_flag = 0;
		
		
		
		if(!empty($sessionItem->items))
		
			{
			
			$apikeys=array();
			$apiproducts=array();
			$i=0;
			$p=0;
			
			
				foreach($sessionItem->items as $key=>$val)
				{
				 if($val->product_id=='')
			  {
					  	unset($_SESSION['Api_Model_Cart']['items'][$key]);
						continue;
			  }
				    if(!in_array($val->storeApiKey,$apikeys))
					{
					$apikeys[$i]=$val->storeApiKey;
				    $i++;
					}
					 if(!in_array($val->product_id,$apiproducts))
					{
					$apiproducts[$p]=$val->product_id;
				    $p++;
					}
				}
			}
		if($request['dc_code'])
		{
			$discountCouponCode=trim($request['dc_code']);
			if($discountCouponCode=='')
				{
				//redirect with error discpunt coupon
				}
			else
				{
			
					$discountcouponDetail= $this->_cartModel->getDoiscountCouponDetail($discountCouponCode);
					
					
				
						if(empty($discountcouponDetail))
						{
							//redirect with error discpunt coupon
						}
						else
						{
							
							$couponDetail=$discountcouponDetail[0];
							$couponId=$couponDetail['id'];
							$couponType=$couponDetail['discount_type_id'];
							$couponapikey=$couponDetail['api_key'];
							$couponapplied_type=$couponDetail['applied_type'];
							$couponapplied_value=$couponDetail['applied_value'];
							$couponusage_number=$couponDetail['usage_number'];
							$couponusage_number_val=$couponDetail['usage_number_val'];
							$couponusage_user=$couponDetail['usage_user'];
							$couponusage_user_per=$couponDetail['usage_user_per'];
							$couponredeemed=$couponDetail['redeemed'];
							if(!in_array($couponapikey,$apikeys))
							{
								//redirect to error
							}
							
							if($couponusage_number==1)
							{
								if($couponusage_number_val<=$couponredeemed)
								{
									//redirect to error
								}
							
							}
							if($couponusage_user==1)
							{
								$getCustomerRedeemedCoupon=$this->_cartModel->customerUseCouponTotal($couponId);
									if($couponusage_user_per>=$getCustomerRedeemedCoupon['total'])
										{
											//redirect to error
										}
							
							}
							if($couponapplied_type==0) //check coupon applied products
									{
										$categoryIds=explode(",",$couponapplied_value);
										if(!empty($categoryIds))
										{
										$productid=array();
											foreach($categoryIds as $key=>$val)
												{
													if($val!='')
														{
																array_push($productid,$this->_cartModel->getProductByCatIds($val));
														}	
												}
										}		
												if(!empty($productid))
													{
														foreach($productid as $key=>$val)
															{
																if($val['id']!='')
																	{
																		$productids[]=$val['id'];  
																	}
															}
													}
									}	
								else
									{
										$productids = explode(",",$couponapplied_value);
									}
							
							if($couponType==2 || $couponType==4)
							{
											//check the product id exists for coupon
											$couponVailid=false;
											foreach($apiproducts as $key=>$val)
												{
													if(in_array($val,$productids))
													{
														$couponVailid=true;
													}
												}
								if($couponVailid)
								{
									
								
								}					
							}	
							if($couponType==5 || $couponType==6 || $couponType==7)
							{
							
							}
							if($couponType==1 || $couponType==3)
							{
							
							}
								
									
							
							
							
							
						}
						
				
				}	
				
		
		}
		if($request['gc_code'])
		{
			$gcCouponCode=trim($request['gc_code']);
			if($gcCouponCode=='')
				{
					//redirect with error gift csetificate
				}
			else
				{
					$giftCertificateDetail= $this->_cartModel->getgiftDetail($gcCouponCode);
						if(empty($discountcouponDetail))
						{
							//redirect with error gift certificate
						}
						else
						{
								$storeapiky=$giftCertificateDetail['store_apikey'];
								if(!in_array($storeapiky,$apikeys))
									{
										//redirect to error giftcertificate
										exit;
									}
									
						}
				
				}
		}
		
		
		foreach($request as $key=>$val)
		{
		
            $store_api_key;
			$prod = explode('_',$key);
			
			if($prod[0]=='qty')
			{
                $store_api_key = $prod[1];
				$sessionItem->items[$prod[1]."_".$prod[2]."_".$prod[3]]->product_qty=$val;
			}
			if($prod[0]=='addresss')
			{    
			//echo $prod[2]."_".$val;  exit; 
                 $shippingcost = $cartMapper->productshippngcost($prod[2],$val);
				// echo    $shippingcost;exit;
				$sessionItem->items[$prod[1]."_".$prod[2]."_".$prod[3]]->shipcost=$shippingcost;
				$sessionItem->items[$prod[1]."_".$prod[2]."_".$prod[3]]->address_book_id=$val;
				if($val === '0')
				{
                                   array_push($errormaeesge, "Please select shipping address.");
				}
                                if($shippingcost==='error')
				{
                                    
                                    array_push($errormaeesges, "Please select billing/ shipping address.");
				}
                                
			}
			if($prod[0]=='billing')
			{
				if($val == 0)
				{
					//array_push($errormaeesge, "Please select billing/ shipping address.");
				}
				else
				{
					$sessionItem->billingaddress=$val;
				}

			}
			if(isset($request['payment']))
			{
                                $value = explode('_',$val);
                                if($value[0] == 'payment')
                                {
                                    //echo '<pre>';
                                    //print_r($value);exit;
                                    $sessionItem->paymentmode_id=$value[1];
                                    $sessionItem->paymentmode_type=$value[2];
                                }

			}
                        else
                        {
                          // array_push($errormaeesge, "Please select payment mode.");
                        }
                        if($prod[0]=='applydc')
			{
                            //echo $store_api_key;exit;
                            $dc_number = $request['dc_code'];
                            $dcdetails = $cartMapper->getDCByNumber($dc_number, $store_api_key);
			}
		}


		$session_item = new Zend_Session_Namespace('Api_Model_Cart');
		$sessionReview = new Zend_Session_Namespace('Api_Model_Review');
		$sessionReview->items=$session_item->items;
		$sessionReview->billingaddress=$session_item->billingaddress;
		$sessionReview->net_amt=$session_item->net_amt;
		$sessionReview->paymentmode_id=$session_item->paymentmode_id;
		$sessionReview->paymentmode_type=$session_item->paymentmode_type;

		if($request['continue_x'] || $request['continue'])
		{
                        if(count($errormaeesge) > 0 || count($errormaeesges)>0)
		{
                        $message = array_unique($errormaeesge);
			$msg = encryptLink(implode(':', $message));
			$this->_redirect("/cart/checkoutaddress/error/$msg");
                        exit;
		}
			$this->_redirect('/cart/checkout');
		}
		if($request['update_x'] || $request['update'] )
		{
			$this->veiw->updateBtnClicked=1;

			$this->_redirect('/cart/checkoutaddress/err/1');
		}


	}
	public function updatecartbnAction()
	{

        $request = $this->_request->getParams();
		$cartMapper = new Secure_Model_CartMapper();
		$userName = new Zend_Session_Namespace('USER');
		$sessionItem = new Zend_Session_Namespace('Api_Model_Cart_Buynow');
		$errormaeesge=array();
		$errormaeesges=array();
		$error_flag = 0;
		
		
		
		if(!empty($sessionItem->items))
		
			{
			
			$apikeys=array();
			$apiproducts=array();
			$i=0;
			$p=0;
			
			
				foreach($sessionItem->items as $key=>$val)
				{
				 if($val->product_id=='')
			  {
					  	unset($_SESSION['Api_Model_Cart_Buynow']['items'][$key]);
						continue;
			  }
				    if(!in_array($val->storeApiKey,$apikeys))
					{
					$apikeys[$i]=$val->storeApiKey;
				    $i++;
					}
					 if(!in_array($val->product_id,$apiproducts))
					{
					$apiproducts[$p]=$val->product_id;
				    $p++;
					}
				}
			}
		if($request['dc_code'])
		{
			$discountCouponCode=trim($request['dc_code']);
			if($discountCouponCode=='')
				{
				//redirect with error discpunt coupon
				}
			else
				{
			
					$discountcouponDetail= $this->_cartModel->getDoiscountCouponDetail($discountCouponCode);
					
					
				
						if(empty($discountcouponDetail))
						{
							//redirect with error discpunt coupon
						}
						else
						{
							
							$couponDetail=$discountcouponDetail[0];
							$couponId=$couponDetail['id'];
							$couponType=$couponDetail['discount_type_id'];
							$couponapikey=$couponDetail['api_key'];
							$couponapplied_type=$couponDetail['applied_type'];
							$couponapplied_value=$couponDetail['applied_value'];
							$couponusage_number=$couponDetail['usage_number'];
							$couponusage_number_val=$couponDetail['usage_number_val'];
							$couponusage_user=$couponDetail['usage_user'];
							$couponusage_user_per=$couponDetail['usage_user_per'];
							$couponredeemed=$couponDetail['redeemed'];
							if(!in_array($couponapikey,$apikeys))
							{
								//redirect to error
							}
							
							if($couponusage_number==1)
							{
								if($couponusage_number_val<=$couponredeemed)
								{
									//redirect to error
								}
							
							}
							if($couponusage_user==1)
							{
								$getCustomerRedeemedCoupon=$this->_cartModel->customerUseCouponTotal($couponId);
									if($couponusage_user_per>=$getCustomerRedeemedCoupon['total'])
										{
											//redirect to error
										}
							
							}
							if($couponapplied_type==0) //check coupon applied products
									{
										$categoryIds=explode(",",$couponapplied_value);
										if(!empty($categoryIds))
										{
										$productid=array();
											foreach($categoryIds as $key=>$val)
												{
													if($val!='')
														{
																array_push($productid,$this->_cartModel->getProductByCatIds($val));
														}	
												}
										}		
												if(!empty($productid))
													{
														foreach($productid as $key=>$val)
															{
																if($val['id']!='')
																	{
																		$productids[]=$val['id'];  
																	}
															}
													}
									}	
								else
									{
										$productids = explode(",",$couponapplied_value);
									}
							
							if($couponType==2 || $couponType==4)
							{
											//check the product id exists for coupon
											$couponVailid=false;
											foreach($apiproducts as $key=>$val)
												{
													if(in_array($val,$productids))
													{
														$couponVailid=true;
													}
												}
								if($couponVailid)
								{
									
								
								}					
							}	
							if($couponType==5 || $couponType==6 || $couponType==7)
							{
							
							}
							if($couponType==1 || $couponType==3)
							{
							
							}
								
									
							
							
							
							
						}
						
				
				}	
				
		
		}
		if($request['gc_code'])
		{
			$gcCouponCode=trim($request['gc_code']);
			if($gcCouponCode=='')
				{
					//redirect with error gift csetificate
				}
			else
				{
					$giftCertificateDetail= $this->_cartModel->getgiftDetail($gcCouponCode);
						if(empty($discountcouponDetail))
						{
							//redirect with error gift certificate
						}
						else
						{
								$storeapiky=$giftCertificateDetail['store_apikey'];
								if(!in_array($storeapiky,$apikeys))
									{
										//redirect to error giftcertificate
										exit;
									}
									
						}
				
				}
		}
		
		
		foreach($request as $key=>$val)
		{
		
            $store_api_key;
			$prod = explode('_',$key);
			
			if($prod[0]=='qty')
			{
                $store_api_key = $prod[1];
				$sessionItem->items[$prod[1]."_".$prod[2]."_".$prod[3]]->product_qty=$val;
			}
			if($prod[0]=='addresss')
			{    
			//echo $prod[2]."_".$val;  exit; 
                 $shippingcost = $cartMapper->productshippngcost($prod[2],$val);
				// echo    $shippingcost;exit;
				$sessionItem->items[$prod[1]."_".$prod[2]."_".$prod[3]]->shipcost=$shippingcost;
				$sessionItem->items[$prod[1]."_".$prod[2]."_".$prod[3]]->address_book_id=$val;
				if($val === '0')
				{
                                   array_push($errormaeesge, "Please select shipping address.");
				}
                                if($shippingcost==='error')
				{
                                    
                                    array_push($errormaeesges, "Please select billing/ shipping address.");
				}
                                
			}
			if($prod[0]=='billing')
			{
				if($val == 0)
				{
					//array_push($errormaeesge, "Please select billing/ shipping address.");
				}
				else
				{
					$sessionItem->billingaddress=$val;
				}

			}
			if(isset($request['payment']))
			{
                                $value = explode('_',$val);
                                if($value[0] == 'payment')
                                {
                                    //echo '<pre>';
                                    //print_r($value);exit;
                                    $sessionItem->paymentmode_id=$value[1];
                                    $sessionItem->paymentmode_type=$value[2];
                                }

			}
                        else
                        {
                          // array_push($errormaeesge, "Please select payment mode.");
                        }
                        if($prod[0]=='applydc')
			{
                            //echo $store_api_key;exit;
                            $dc_number = $request['dc_code'];
                            $dcdetails = $cartMapper->getDCByNumber($dc_number, $store_api_key);
			}
		}


		$session_item = new Zend_Session_Namespace('Api_Model_Cart_Buynow');
		$sessionReview = new Zend_Session_Namespace('Api_Model_Review_Buynow');
		$sessionReview->items=$session_item->items;
		$sessionReview->billingaddress=$session_item->billingaddress;
		$sessionReview->net_amt=$session_item->net_amt;
		$sessionReview->paymentmode_id=$session_item->paymentmode_id;
		$sessionReview->paymentmode_type=$session_item->paymentmode_type;

		if($request['continue_x'] || $request['continue'])
		{
                        if(count($errormaeesge) > 0 || count($errormaeesges)>0)
		{
                        $message = array_unique($errormaeesge);
			$msg = encryptLink(implode(':', $message));
			$this->_redirect("/cart/checkoutaddressbn/error/$msg");
                        exit;
		}
			$this->_redirect('/cart/checkoutbn');
		}
		if($request['update_x'] || $request['update'] )
		{
			$this->veiw->updateBtnClicked=1;

			$this->_redirect('/cart/checkoutaddressbn/err/1');
		}


	}
	public function updatecartcheckoutAction()
	{
				

        $request = $this->_request->getParams();
//print_r($request);exit;
		$cartMapper = new Secure_Model_CartMapper();
		$userName = new Zend_Session_Namespace('USER');
		$sessionItem = new Zend_Session_Namespace('Api_Model_Review');
		$errormaeesge=array();
		$errormaeesges=array();
		$error_flag = 0;
		$sessiondsserr = new Zend_Session_Namespace('dicounterrors');	
		unset($sessiondsserr->dmessages);
		$sessiondscheckbox = new Zend_Session_Namespace('discountcheck');	
		if($request['couponcheck']==1)
		$sessiondscheckbox->checked=1;
		else
		$sessiondscheckbox->checked=0;
		
		
		if(!empty($sessionItem->items))
		
			{
			
			$apikeys=array();
			$apiproducts=array();
			$i=0;
			$p=0;
			
			
				foreach($sessionItem->items as $key=>$val)
				{
				 if($val->product_id=='')
			  {
					  	unset($_SESSION['Api_Model_Cart']['items'][$key]);
						continue;
			  }
				    if(!in_array($val->storeApiKey,$apikeys))
					{
					$apikeys[$i]=$val->storeApiKey;
				    $i++;
					}
					 if(!in_array($val->product_id,$apiproducts))
					{
					$apiproducts[$p]=$val->product_id;
				    $p++;
					}
				}
			}
			$this->view->discounterror="";

			$dicountMessages=array();
		$capplied=0;
		if($request['dc_code'])
		{
			$capplied=1;
						$this->view->discounterror='Please enter a valid coupon code!';

			$discountCouponCode=trim($request['dc_code']);
			
			if($discountCouponCode=='')
				{
					$dicountMessages['error'][]='Please enter a valid coupon code!';
				}
			else
				{
			
					$discountcouponDetail= $this->_cartModel->getDoiscountCouponDetail($discountCouponCode);
					
					
				
						if(empty($discountcouponDetail))
						{
							//redirect with error discpunt coupon
							$dicountMessages['error'][]='Please enter a valid coupon code!';
						}
						else
						{

							$error=0;
							
							$couponDetail=$discountcouponDetail[0];
							$couponId=$couponDetail['id'];
							$couponType=$couponDetail['discount_type_id'];
							$couponapikey=$couponDetail['api_key'];
							$couponapplied_type=$couponDetail['applied_type'];
							$couponapplied_value=$couponDetail['applied_value'];
							$couponusage_number=$couponDetail['usage_number'];
							$couponusage_number_val=$couponDetail['usage_number_val'];
							$couponusage_user=$couponDetail['usage_user'];
							$couponusage_user_per=$couponDetail['usage_user_per'];
							$couponredeemed=$couponDetail['redeemed'];
							$coupondiscount_amt=$couponDetail['discount_amt'];
							$coupondpurchase_amt=$couponDetail['purchase_amt'];
						
							if(!in_array($couponapikey,$apikeys))
							{
							
							$error=1;
							$dicountMessages['error'][]='The discount coupon '.$request['dc_code'].' is not valid on the product or the quantity selected by you!';
								
								//redirect to error
							}
							
							if($couponusage_number==1)
							{
								if($couponusage_number_val<=$couponredeemed)
								{
								$error=1;	
									$dicountMessages['error'][]='The discount coupon '.$request['dc_code'].' cannot be applied, as the usage limit has exceeded!';
								}
							
							}
							
							if($couponusage_user==1)
							{
								$getCustomerRedeemedCoupon=$this->_cartModel->customerUseCouponTotal($couponId);
								//print_r($getCustomerRedeemedCoupon);exit;
									if($couponusage_user_per<=$getCustomerRedeemedCoupon['total'])
										{
										$error=1;		
											//redirect to error
										$dicountMessages['error'][]='The discount coupon '.$request['dc_code'].' cannot be applied, as the usage limit has exceeded!';
										}
							
							}
						if($couponType==2 || $couponType==4)
							{
							if($couponapplied_type==0) //check coupon applied products
									{
			
										$categoryIds=explode(",",$couponapplied_value);
										//print_r($categoryIds);
										if(!empty($categoryIds))
										{
										$productid=array();
											foreach($categoryIds as $key=>$val)
												{
												
													if($val!='')
														{
														//print_r($this->_cartModel->getProductByCatIds($val));
														$this->_cartModel->getProductByCatIds($val);
														//echo 'goo2o';exit;
															array_push($productid,$this->_cartModel->getProductByCatIds($val));
														}	
												}
										}	
										
										
												if(!empty($productid))
													{
														
														foreach($productid as $key=>$val)
															{
																if(!empty($val))
																{
																   $i=0;
																	foreach($val as $k=>$v)
																	{
																		
																		if($v['id']!='')
																			{
																				$productids[$i]=$v['id']; 
																				$i++; 
																			}
																	}	
																}	
															}
													}
									}	
								else
									{
		
										$productids = explode(",",$couponapplied_value);
									}
}
		//print_r($productids);exit;	exit;	
				//print_r($productids);exit;
							$sessionCoupons = new Zend_Session_Namespace('Cart_Coupon');
//unset($_SESSION['Cart_Coupon']);
//print_r($productids);exit;


							if($couponType==2 || $couponType==4 && $error==0)
							{
											//check the product id exists for coupon
											$couponVailid=false;
											foreach($sessionItem->items as $key=>$val)
												{
//echo "<pre>";
								//echo $val->product_id;// print_r($val); 
//echo $val->product_qty;

if(in_array($val->product_id,$productids) && $coupondpurchase_amt<=$val->product_qty)
{
unset($_SESSION['Cart_GiftCertificate']['giftcoupon'][$val->storeApiKey]);
//echo $val->product_id;
unset($_SESSION['Cart_Coupon']['coupons']['shipping'][$val->storeApiKey]);
unset($_SESSION['Cart_Coupon']['coupons']['order'][$val->storeApiKey]);
$dicountMessages['sucess'][]='Discount coupon '.$request['dc_code'].' redeemed successfully, on '.$val->product_name.'!';

if(!array_key_exists($val->product_id."_".$val->variationcode,$sessionCoupons->coupons['products'][$val->storeApiKey]))
	{
//echo $val->product_id."_".$val->variationcode;
$data['product'][$val->storeApiKey][$val->product_id."_".$val->variationcode]['fromstore']=$val->store_api_key[0]['title'];
$data['product'][$val->storeApiKey][$val->product_id."_".$val->variationcode]['couponid']=$couponId;

$data['product'][$val->storeApiKey][$val->product_id."_".$val->variationcode]['mall_url']=$val->store_api_key[0]['mallurl'];
$data['product'][$val->storeApiKey][$val->product_id."_".$val->variationcode]['ccode']=$discountCouponCode;

			if($couponType==2)
				{
			if($coupondiscount_amt>=$val->product_mrp)
			{
			$coupondiscount_amt=$val->product_mrp;	
                        }
			$less=$coupondiscount_amt* $val->product_qty;

				}
			else
				{
			$less=($val->product_mrp/100)*($coupondiscount_amt*$val->product_qty);
				}
				if($val->product_mrp<=$less)
								{
									$less=$val->product_mrp;
								}

$data['product'][$val->storeApiKey][$val->product_id."_".$val->variationcode]['less']=round($less);	
$sessionCoupons->coupons=$data;
}

	
	else
	{
$sessionCoupons->coupons['product'][$val->storeApiKey][$val->product_id."_".$val->variationcode]['fromstore']=$val->store_api_key[0]['title'];

$sessionCoupons->coupons['product'][$val->storeApiKey][$val->product_id."_".$val->variationcode]['mall_url']=$val->store_api_key[0]['mallurl'];
$sessionCoupons->coupons['product'][$val->storeApiKey][$val->product_id."_".$val->variationcode]['ccode']=$discountCouponCode;
$sessionCoupons->coupons['product'][$val->storeApiKey][$val->product_id."_".$val->variationcode]['couponid']=$couponId;

			if($couponType==2)
				{
			if($coupondiscount_amt>$val->product_mrp)
			{
			$coupondiscount_amt=$val->product_mrp;	
                        }
			$less=$coupondiscount_amt* $val->product_qty;

				}
			else
				{
			$less=($val->product_mrp/100)*($coupondiscount_amt*$val->product_qty);
				}
				if($val->product_mrp<=$less)
								{
									$less=$val->product_mrp;
								}

$sessionCoupons->coupons['product'][$val->storeApiKey][$val->product_id."_".$val->variationcode]['less']=round($less);

	}

	}	
	else
	{
	
	$dicountMessages['error'][]='The discount coupon '.$request['dc_code'].' is not valid on the product or the quantity selected by you!';
	}											
	} //exit;		
													
	}
	//echo "<pre>";
	//print_r($sessionCoupons->coupons);	exit;
							if(($couponType==5 || $couponType==6 || $couponType==7) && $error==0)
							{

							

								$totalshipping=0;
								$totalprice=0;
								$allproductPrice=0;
								unset($_SESSION['Cart_GiftCertificate']['giftcoupon'][$couponapikey]);	
								foreach($sessionItem->items as $key=>$val)
								{
									$allproductPrice=($val->product_mrp * $val->product_qty)+($val->shipcost * $val->product_qty);
									if($val->storeApiKey==$couponapikey)
									{
									
									$totalshipping+=($val->shipcost * $val->product_qty);
									$totalprice+=($val->product_mrp * $val->product_qty);
									$mallName=$val->store_api_key[0]['title'];
									$mallUrl=$val->store_api_key[0]['mallurl'];
									$storeapi=$val->storeApiKey;	
									}	

								}
							//echo $totalshipping."_";exit;	
						//	echo $coupondiscount_amt;exit;
							if($coupondpurchase_amt>0 && $coupondpurchase_amt>=$allproductPrice &&  $coupondpurchase_amt!='')
							{
								{

						$dicountMessages['error'][]='Discount coupon '.$request['dc_code'].' not applied, as a minimum purchase of Rs '.$coupondpurchase_amt.' is required to avail this discount coupon!';	
								}
							}
							else
							{
							
								if($couponType==5)
								$lessamount=$coupondiscount_amt;
								if($couponType==6)
								$lessamount=round(($totalshipping/100)*$coupondiscount_amt);
								if($couponType==7)
								$lessamount=$totalshipping;
								
								if($lessamount>$totalshipping)
								{
									$lessamount=$totalshipping;
								}
							
							if($couponType==5 || $couponType==6)
							{
							$dicountMessages['sucess'][]="Discount coupon ".$request['dc_code']." redeemed successfully. You now pay only Rs ".(($totalshipping-$lessamount>0)?$totalshipping-$lessamount:0)." as shipping charges!";
							}
							if($couponType==7)
							{
							$dicountMessages['sucess'][]="Discount coupon ".$request['dc_code']." redeemed successfully. Your order will now be shipped for free!
";
							}



	//Discount coupon '.$request['dc_code'].' redeemed successfully. You can now get your product home in just 
	//echo 'Discount coupon '.$request['dc_code'].' redeemed successfully. You can get your product home in just '.$allproductPrice-$lessamount.' rupees.';exit;
//echo "Discount coupon ".$request['dc_code']." redeemed succcessfully. You can get your product home in just ".($allproductPrice-$lessamount)." rupees.";exit;
unset($_SESSION['Cart_Coupon']['coupons']['product'][$storeapi]);
unset($_SESSION['Cart_Coupon']['coupons']['order'][$storeapi]);
unset($_SESSION['Cart_Coupon']['coupons']['shipping'][$storeapi]);
$data['shipping'][$val->storeApiKey][0]['couponid']=$couponId;
$data['shipping'][$val->storeApiKey][0]['fromstore']=$mallName;
$data['shipping'][$val->storeApiKey][0]['mall_url']=$mallUrl;
$data['shipping'][$val->storeApiKey][0]['ccode']=$discountCouponCode;
$data['shipping'][$val->storeApiKey][0]['less']=round($lessamount);
$sessionCoupons->coupons=$data;
							}
						
							
								

							
							}
							if(($couponType==1 || $couponType==3) && $error==0)
							{

							unset($_SESSION['Cart_GiftCertificate']['giftcoupon'][$couponapikey]);
							$totalshipping=0;
								$totalprice=0;
								$allproductPrice=0;
								foreach($sessionItem->items as $key=>$val)
								{
									$allproductPrice=($val->product_mrp * $val->product_qty)+($val->shipcost * $val->product_qty);
									if($val->storeApiKey==$couponapikey)
									{
									
									$totalshipping+=($val->shipcost * $val->product_qty);
									$totalprice+=($val->product_mrp * $val->product_qty)+($val->shipcost * $val->product_qty);
									$mallName=$val->store_api_key[0]['title'];
									$mallUrl=$val->store_api_key[0]['mallurl'];
									$storeapi=$val->storeApiKey;	
									}	

								}
							if($totalprice>=$coupondpurchase_amt)
							{
								if($couponType==1)
								$lessamount=$coupondiscount_amt;
								if($couponType==3)
								$lessamount=round(($totalprice/100)*$coupondiscount_amt);
                                if($totalprice<=$coupondiscount_amt)
								{
									$lessamount=$totalprice;
								}

$dicountMessages['sucess'][]='Discount coupon '.$request['dc_code'].' redeemed successfully. You now have to pay '.($allproductPrice-$lessamount).' rupees after getting a discount of '.$lessamount.' rupees.';
								unset($_SESSION['Cart_Coupon']['coupons']['shipping'][$storeapi]);
								unset($_SESSION['Cart_Coupon']['coupons']['product'][$storeapi]);
								unset($_SESSION['Cart_Coupon']['coupons']['order'][$storeapi]);
								$data['order'][$val->storeApiKey][0]['fromstore']=$mallName;
								$data['order'][$val->storeApiKey][0]['mall_url']=$mallUrl;
								$data['order'][$val->storeApiKey][0]['ccode']=$discountCouponCode;
								$data['order'][$val->storeApiKey][0]['less']=round($lessamount);
						    	$data['order'][$val->storeApiKey][0]['couponid']=$couponId;								
								$sessionCoupons->coupons=$data;
							
							}else
							{

							$dicountMessages['error'][]='Discount coupon '.$request['dc_code'].' not applied, as a minimum purchase of Rs '.$coupondpurchase_amt.' is required to avail this discount coupon!';							
							}
							}
								
									
							
							
							
							
						}
						
				
				}	
				
		
		}
	if(count($dicountMessages['sucess'])>0)
	{
		unset($dicountMessages['error']);
	}
		if($request['gc_code'])
		{
		$capplied=1;
			$this->view->discounterror='Please enter a valid gift certificate!';

			$gcCouponCode=trim($request['gc_code']);
			if($gcCouponCode=='')
				{
					
					$dicountMessages['error'][]='Please enter a valid gift certificate!';
				}
			else
				{
				
					$giftCertificateDetail= $this->_cartModel->getgiftDetail($gcCouponCode);
					//echo "<pre>";

					
						if(empty($giftCertificateDetail))
						{

							$dicountMessages['error'][0]='Please enter a valid gift certificate!';
						}
						
						else
						{
								$gerror=0;
							
								$gcid=$giftCertificateDetail['id'];
								$grid=$giftCertificateDetail['rid'];

								$gccouponcode=$giftCertificateDetail['gift_code'];
								$gcpurchaseid=$giftCertificateDetail['gift_purchase_id'];
								$gcamountremaining=$giftCertificateDetail['gift_amount_remaining'];
								$gcgiftamount=$giftCertificateDetail['gift_amount'];


								$gcsendingdate=$giftCertificateDetail['sending_date'];



								$gcexpirydate=$giftCertificateDetail['expiry_date'];



				
								$storeapiky=$giftCertificateDetail['store_apikey'];
		
								if($gcsendingdate!=$gcsendingdate && $gcexpirydate<=time())

								{
									$gerror=1;	
									$dicountMessages['error'][0]='Please enter a valid gift certificate!';
								}
								
								if($gcamountremaining<=0)
								{
									$gerror=1;
									$dicountMessages['error'][0]='Gift certificate '.$gcCouponCode.' cannot be applied as it has expired!';
									
								}

					if(!in_array($storeapiky,$apikeys))
						{
							$gerror=1;
	
							$dicountMessages['error'][0]='Please enter a valid gift certificate!';
						}
						else if($gerror==0)
						{
						$gcCoupon= new Zend_Session_Namespace('Cart_GiftCertificate');
						$sessionCoupons = new Zend_Session_Namespace('Cart_Coupon');
						//total price of order
						$totalprice=0;
						foreach($sessionItem->items as $key=>$val)
						{
							if($val->storeApiKey==$storeapiky)
							{
							$totalprice+=($val->product_mrp * $val->product_qty)+($val->shipcost * $val->product_qty);
							$mallName=$val->store_api_key[0]['title'];
							$mallUrl=$val->store_api_key[0]['mallurl'];
							$storeapi=$val->storeApiKey;	
							}	

						}//end of foreach
						
					$dicountcouponapplied=0;
					//print_r($sessionCoupons->coupons);exit;
					if(!empty($sessionCoupons->coupons)) {  
						foreach($sessionCoupons->coupons as $key=>$val)
						{ foreach($val as $k=>$v)
							{       if($k==$storeapiky)
								{
								foreach($v as $lk=>$lv)
								{
									$dicountcouponapplied+=$lv['less'];
								}
								}
							}
						}
					$totalOrder=$totalprice-$dicountcouponapplied; 
					}
					if($gcamountremaining>$totalOrder)
					{
						$giftvalue=$totalOrder;
					}
					else
					{
						$giftvalue=$gcamountremaining;
					}
					$dicountMessages['sucess'][]='Gift certificate '.$gcCouponCode.' for '.$mallName.' redeemed successfully.
';
					
					$gcCoupon->giftcoupon[$storeapiky]['less']=$gcgiftamount;
					$gcCoupon->giftcoupon[$storeapiky]['fromstore']=$mallName;
					$gcCoupon->giftcoupon[$storeapiky]['mall_url']=$mallUrl;
					$gcCoupon->giftcoupon[$storeapiky]['ccode']=$gccouponcode;
					$gcCoupon->giftcoupon[$storeapiky]['gcid']=$grid;
								
					
								


						}
		
								
									
						}
				
				}
		}
		$sessiondsserr = new Zend_Session_Namespace('dicounterrors');	
		$sessiondsserr->dmessages=$dicountMessages;

		foreach($request as $key=>$val)
		{
                        $store_api_key;
			$prod = explode('_',$key);
			if($prod[0]=='qty')
			{
                                $store_api_key = $prod[1];
				$sessionItem->items[$prod[1]."_".$prod[2]."_".$prod[3]]->product_qty=$val;
			}
			if($prod[0]=='addresss')
			{       //echo "hi";exit;
                                $shippingcost = $cartMapper->productshippngcost($prod[2],$val);
				$sessionItem->items[$prod[1]."_".$prod[2]."_".$prod[3]]->shipcost=$shippingcost;
				$sessionItem->items[$prod[1]."_".$prod[2]."_".$prod[3]]->address_book_id=$val;
                                
				if($val === '0')
				{
                                   //array_push($errormaeesge, "Please select billing/ shipping address.");
				}
                                if($shippingcost === 'error')
				{
                                    
                                   // array_push($errormaeesges, "Please select billing/ shipping address.");
				}
                                
			}
			if($prod[0]=='billing')
			{
				if($val == 0)
				{
					//array_push($errormaeesge, "Please select billing/ shipping address.");
				}
				else
				{
					$sessionItem->billingaddress=$val;
				}

			}
			if(isset($request['payment']))
			{
                                $value = explode('_',$val);
                                if($value[0] == 'payment')
                                {
                                    //echo '<pre>';
                                    //print_r($value);exit;
                                    $sessionItem->paymentmode_id=$value[1];
                                    $sessionItem->paymentmode_type=$value[2];
                                }

			}
                        else
                        {
                          array_push($errormaeesge, "Please select payment mode.");
                        }
                        if($prod[0]=='applydc')
			{
                            //echo $store_api_key;exit;
                            $dc_number = $request['dc_code'];
                            $dcdetails = $cartMapper->getDCByNumber($dc_number, $store_api_key);
			}
		}


		$session_item = new Zend_Session_Namespace('Api_Model_Review');
		$sessionReview = new Zend_Session_Namespace('Api_Model_Review');
		$sessionReview->items=$session_item->items;
		$sessionReview->billingaddress=$session_item->billingaddress;
		$sessionReview->net_amt=$session_item->net_amt;
		$sessionReview->paymentmode_id=$session_item->paymentmode_id;
		$sessionReview->paymentmode_type=$session_item->paymentmode_type;

		if($request['continue_x'] || $request['continue'])
		{
                        if(count($errormaeesge) > 0 || count($errormaeesges)>0)
		{
                        $message = array_unique($errormaeesge);
			$msg = encryptLink(implode(':', $message));
			$this->_redirect("/cart/checkout/error/$msg");
                        exit;
		}
			$this->_redirect('/cart/review');
		}
		$addUrl='';
		if($capplied)
		$addUrl='#dap';

		if($request['update_x'] || $request['update'] || isset($request['applydc_x']) ||  isset($request['applydc']) || isset($request['applygc_x']) ||  isset($request['applygc']))
		{
//echo 'dsfsdf';exit;
			$this->_redirect('/cart/checkout'.$addUrl);
		}



	}
	public function updatecartcheckoutbnAction()
	{
				

        $request = $this->_request->getParams();
//print_r($request);exit;
		$cartMapper = new Secure_Model_CartMapper();
		$userName = new Zend_Session_Namespace('USER');
		$sessionItem = new Zend_Session_Namespace('Api_Model_Review_Buynow');
		$errormaeesge=array();
		$errormaeesges=array();
		$error_flag = 0;
		$sessiondsserr = new Zend_Session_Namespace('dicounterrors');	
		unset($sessiondsserr->dmessages);
		$sessiondscheckbox = new Zend_Session_Namespace('discountcheck');	
		if($request['couponcheck']==1)
		$sessiondscheckbox->checked=1;
		else
		$sessiondscheckbox->checked=0;
		
		
		if(!empty($sessionItem->items))
		
			{
			
			$apikeys=array();
			$apiproducts=array();
			$i=0;
			$p=0;
			
			
				foreach($sessionItem->items as $key=>$val)
				{
				 if($val->product_id=='')
			  {
					  	unset($_SESSION['Api_Model_Cart_Buynow']['items'][$key]);
						continue;
			  }
				    if(!in_array($val->storeApiKey,$apikeys))
					{
					$apikeys[$i]=$val->storeApiKey;
				    $i++;
					}
					 if(!in_array($val->product_id,$apiproducts))
					{
					$apiproducts[$p]=$val->product_id;
				    $p++;
					}
				}
			}
			$this->view->discounterror="";

			$dicountMessages=array();
		$capplied=0;
		if($request['dc_code'])
		{
			$capplied=1;
						$this->view->discounterror='Please enter a valid coupon code!';

			$discountCouponCode=trim($request['dc_code']);
			
			if($discountCouponCode=='')
				{
					$dicountMessages['error'][]='Please enter a valid coupon code!';
				}
			else
				{
			
					$discountcouponDetail= $this->_cartModel->getDoiscountCouponDetail($discountCouponCode);
					
					
				
						if(empty($discountcouponDetail))
						{
							//redirect with error discpunt coupon
							$dicountMessages['error'][]='Please enter a valid coupon code!';
						}
						else
						{

							$error=0;
							
							$couponDetail=$discountcouponDetail[0];
							$couponId=$couponDetail['id'];
							$couponType=$couponDetail['discount_type_id'];
							$couponapikey=$couponDetail['api_key'];
							$couponapplied_type=$couponDetail['applied_type'];
							$couponapplied_value=$couponDetail['applied_value'];
							$couponusage_number=$couponDetail['usage_number'];
							$couponusage_number_val=$couponDetail['usage_number_val'];
							$couponusage_user=$couponDetail['usage_user'];
							$couponusage_user_per=$couponDetail['usage_user_per'];
							$couponredeemed=$couponDetail['redeemed'];
							$coupondiscount_amt=$couponDetail['discount_amt'];
							$coupondpurchase_amt=$couponDetail['purchase_amt'];
						
							if(!in_array($couponapikey,$apikeys))
							{
							
							$error=1;
							$dicountMessages['error'][]='The discount coupon '.$request['dc_code'].' is not valid on the product or the quantity selected by you!';
								
								//redirect to error
							}
							
							if($couponusage_number==1)
							{
								if($couponusage_number_val<=$couponredeemed)
								{
								$error=1;	
									$dicountMessages['error'][]='The discount coupon '.$request['dc_code'].' cannot be applied, as the usage limit has exceeded!';
								}
							
							}
							
							if($couponusage_user==1)
							{
								$getCustomerRedeemedCoupon=$this->_cartModel->customerUseCouponTotal($couponId);
								//print_r($getCustomerRedeemedCoupon);exit;
									if($couponusage_user_per<=$getCustomerRedeemedCoupon['total'])
										{
										$error=1;		
											//redirect to error
										$dicountMessages['error'][]='The discount coupon '.$request['dc_code'].' cannot be applied, as the usage limit has exceeded!';
										}
							
							}
						if($couponType==2 || $couponType==4)
							{
							if($couponapplied_type==0) //check coupon applied products
									{
			
										$categoryIds=explode(",",$couponapplied_value);
										//print_r($categoryIds);
										if(!empty($categoryIds))
										{
										$productid=array();
											foreach($categoryIds as $key=>$val)
												{
												
													if($val!='')
														{
														//print_r($this->_cartModel->getProductByCatIds($val));
														$this->_cartModel->getProductByCatIds($val);
														//echo 'goo2o';exit;
															array_push($productid,$this->_cartModel->getProductByCatIds($val));
														}	
												}
										}	
										
										
												if(!empty($productid))
													{
														
														foreach($productid as $key=>$val)
															{
																if(!empty($val))
																{
																   $i=0;
																	foreach($val as $k=>$v)
																	{
																		
																		if($v['id']!='')
																			{
																				$productids[$i]=$v['id']; 
																				$i++; 
																			}
																	}	
																}	
															}
													}
									}	
								else
									{
		
										$productids = explode(",",$couponapplied_value);
									}
}
		//print_r($productids);exit;	exit;	
				//print_r($productids);exit;
							$sessionCoupons = new Zend_Session_Namespace('Cart_Coupon');
//unset($_SESSION['Cart_Coupon']);
//print_r($productids);exit;


							if($couponType==2 || $couponType==4 && $error==0)
							{
											//check the product id exists for coupon
											$couponVailid=false;
											foreach($sessionItem->items as $key=>$val)
												{
//echo "<pre>";
								//echo $val->product_id;// print_r($val); 
//echo $val->product_qty;

if(in_array($val->product_id,$productids) && $coupondpurchase_amt<=$val->product_qty)
{
unset($_SESSION['Cart_GiftCertificate']['giftcoupon'][$val->storeApiKey]);
//echo $val->product_id;
unset($_SESSION['Cart_Coupon']['coupons']['shipping'][$val->storeApiKey]);
unset($_SESSION['Cart_Coupon']['coupons']['order'][$val->storeApiKey]);
$dicountMessages['sucess'][]='Discount coupon '.$request['dc_code'].' redeemed successfully, on '.$val->product_name.'!';

if(!array_key_exists($val->product_id."_".$val->variationcode,$sessionCoupons->coupons['products'][$val->storeApiKey]))
	{
//echo $val->product_id."_".$val->variationcode;
$data['product'][$val->storeApiKey][$val->product_id."_".$val->variationcode]['fromstore']=$val->store_api_key[0]['title'];
$data['product'][$val->storeApiKey][$val->product_id."_".$val->variationcode]['couponid']=$couponId;

$data['product'][$val->storeApiKey][$val->product_id."_".$val->variationcode]['mall_url']=$val->store_api_key[0]['mallurl'];
$data['product'][$val->storeApiKey][$val->product_id."_".$val->variationcode]['ccode']=$discountCouponCode;

			if($couponType==2)
				{
			if($coupondiscount_amt>=$val->product_mrp)
			{
			$coupondiscount_amt=$val->product_mrp;	
                        }
			$less=$coupondiscount_amt* $val->product_qty;

				}
			else
				{
			$less=($val->product_mrp/100)*($coupondiscount_amt*$val->product_qty);
				}
				if($val->product_mrp<=$less)
								{
									$less=$val->product_mrp;
								}

$data['product'][$val->storeApiKey][$val->product_id."_".$val->variationcode]['less']=round($less);	
$sessionCoupons->coupons=$data;
}

	
	else
	{
$sessionCoupons->coupons['product'][$val->storeApiKey][$val->product_id."_".$val->variationcode]['fromstore']=$val->store_api_key[0]['title'];

$sessionCoupons->coupons['product'][$val->storeApiKey][$val->product_id."_".$val->variationcode]['mall_url']=$val->store_api_key[0]['mallurl'];
$sessionCoupons->coupons['product'][$val->storeApiKey][$val->product_id."_".$val->variationcode]['ccode']=$discountCouponCode;
$sessionCoupons->coupons['product'][$val->storeApiKey][$val->product_id."_".$val->variationcode]['couponid']=$couponId;

			if($couponType==2)
				{
			if($coupondiscount_amt>$val->product_mrp)
			{
			$coupondiscount_amt=$val->product_mrp;	
                        }
			$less=$coupondiscount_amt* $val->product_qty;

				}
			else
				{
			$less=($val->product_mrp/100)*($coupondiscount_amt*$val->product_qty);
				}
				if($val->product_mrp<=$less)
								{
									$less=$val->product_mrp;
								}

$sessionCoupons->coupons['product'][$val->storeApiKey][$val->product_id."_".$val->variationcode]['less']=round($less);

	}

	}	
	else
	{
	
	$dicountMessages['error'][]='The discount coupon '.$request['dc_code'].' is not valid on the product or the quantity selected by you!';
	}											
	} //exit;		
													
	}
	//echo "<pre>";
	//print_r($sessionCoupons->coupons);	exit;
							if(($couponType==5 || $couponType==6 || $couponType==7) && $error==0)
							{

							

								$totalshipping=0;
								$totalprice=0;
								$allproductPrice=0;
								unset($_SESSION['Cart_GiftCertificate']['giftcoupon'][$couponapikey]);	
								foreach($sessionItem->items as $key=>$val)
								{
									$allproductPrice=($val->product_mrp * $val->product_qty)+($val->shipcost * $val->product_qty);
									if($val->storeApiKey==$couponapikey)
									{
									
									$totalshipping+=($val->shipcost * $val->product_qty);
									$totalprice+=($val->product_mrp * $val->product_qty);
									$mallName=$val->store_api_key[0]['title'];
									$mallUrl=$val->store_api_key[0]['mallurl'];
									$storeapi=$val->storeApiKey;	
									}	

								}
							//echo $totalshipping."_";exit;	
						//	echo $coupondiscount_amt;exit;
							if($coupondpurchase_amt>0 && $coupondpurchase_amt>=$allproductPrice &&  $coupondpurchase_amt!='')
							{
								{

						$dicountMessages['error'][]='Discount coupon '.$request['dc_code'].' not applied, as a minimum purchase of Rs '.$coupondpurchase_amt.' is required to avail this discount coupon!';	
								}
							}
							else
							{
							
								if($couponType==5)
								$lessamount=$coupondiscount_amt;
								if($couponType==6)
								$lessamount=round(($totalshipping/100)*$coupondiscount_amt);
								if($couponType==7)
								$lessamount=$totalshipping;
								
								if($lessamount>$totalshipping)
								{
									$lessamount=$totalshipping;
								}
							
							if($couponType==5 || $couponType==6)
							{
							$dicountMessages['sucess'][]="Discount coupon ".$request['dc_code']." redeemed successfully. You now pay only Rs ".(($totalshipping-$lessamount>0)?$totalshipping-$lessamount:0)." as shipping charges!";
							}
							if($couponType==7)
							{
							$dicountMessages['sucess'][]="Discount coupon ".$request['dc_code']." redeemed successfully. Your order will now be shipped for free!
";
							}



	//Discount coupon '.$request['dc_code'].' redeemed successfully. You can now get your product home in just 
	//echo 'Discount coupon '.$request['dc_code'].' redeemed successfully. You can get your product home in just '.$allproductPrice-$lessamount.' rupees.';exit;
//echo "Discount coupon ".$request['dc_code']." redeemed succcessfully. You can get your product home in just ".($allproductPrice-$lessamount)." rupees.";exit;
unset($_SESSION['Cart_Coupon']['coupons']['product'][$storeapi]);
unset($_SESSION['Cart_Coupon']['coupons']['order'][$storeapi]);
unset($_SESSION['Cart_Coupon']['coupons']['shipping'][$storeapi]);
$data['shipping'][$val->storeApiKey][0]['couponid']=$couponId;
$data['shipping'][$val->storeApiKey][0]['fromstore']=$mallName;
$data['shipping'][$val->storeApiKey][0]['mall_url']=$mallUrl;
$data['shipping'][$val->storeApiKey][0]['ccode']=$discountCouponCode;
$data['shipping'][$val->storeApiKey][0]['less']=round($lessamount);
$sessionCoupons->coupons=$data;
							}
						
							
								

							
							}
							if(($couponType==1 || $couponType==3) && $error==0)
							{

							unset($_SESSION['Cart_GiftCertificate']['giftcoupon'][$couponapikey]);
							$totalshipping=0;
								$totalprice=0;
								$allproductPrice=0;
								foreach($sessionItem->items as $key=>$val)
								{
									$allproductPrice=($val->product_mrp * $val->product_qty)+($val->shipcost * $val->product_qty);
									if($val->storeApiKey==$couponapikey)
									{
									
									$totalshipping+=($val->shipcost * $val->product_qty);
									$totalprice+=($val->product_mrp * $val->product_qty)+($val->shipcost * $val->product_qty);
									$mallName=$val->store_api_key[0]['title'];
									$mallUrl=$val->store_api_key[0]['mallurl'];
									$storeapi=$val->storeApiKey;	
									}	

								}
							if($totalprice>=$coupondpurchase_amt)
							{
								if($couponType==1)
								$lessamount=$coupondiscount_amt;
								if($couponType==3)
								$lessamount=round(($totalprice/100)*$coupondiscount_amt);
                                if($totalprice<=$coupondiscount_amt)
								{
									$lessamount=$totalprice;
								}

$dicountMessages['sucess'][]='Discount coupon '.$request['dc_code'].' redeemed successfully. You now have to pay '.($allproductPrice-$lessamount).' rupees after getting a discount of '.$lessamount.' rupees.';
								unset($_SESSION['Cart_Coupon']['coupons']['shipping'][$storeapi]);
								unset($_SESSION['Cart_Coupon']['coupons']['product'][$storeapi]);
								unset($_SESSION['Cart_Coupon']['coupons']['order'][$storeapi]);
								$data['order'][$val->storeApiKey][0]['fromstore']=$mallName;
								$data['order'][$val->storeApiKey][0]['mall_url']=$mallUrl;
								$data['order'][$val->storeApiKey][0]['ccode']=$discountCouponCode;
								$data['order'][$val->storeApiKey][0]['less']=round($lessamount);
						    	$data['order'][$val->storeApiKey][0]['couponid']=$couponId;								
								$sessionCoupons->coupons=$data;
							
							}else
							{

							$dicountMessages['error'][]='Discount coupon '.$request['dc_code'].' not applied, as a minimum purchase of Rs '.$coupondpurchase_amt.' is required to avail this discount coupon!';							
							}
							}
								
									
							
							
							
							
						}
						
				
				}	
				
		
		}
	if(count($dicountMessages['sucess'])>0)
	{
		unset($dicountMessages['error']);
	}
		if($request['gc_code'])
		{
		$capplied=1;
			$this->view->discounterror='Please enter a valid gift certificate!';

			$gcCouponCode=trim($request['gc_code']);
			if($gcCouponCode=='')
				{
					$dicountMessages['error'][]='Please enter a valid gift certificate!';
				}
			else
				{
					$giftCertificateDetail= $this->_cartModel->getgiftDetail($gcCouponCode);
					//echo "<pre>";

					
						if(empty($giftCertificateDetail))
						{

							$dicountMessages['error'][0]='Please enter a valid gift certificate!';
						}
						
						else
						{
								$gerror=0;
							
								$gcid=$giftCertificateDetail['id'];
								$grid=$giftCertificateDetail['rid'];

								$gccouponcode=$giftCertificateDetail['gift_code'];
								$gcpurchaseid=$giftCertificateDetail['gift_purchase_id'];
								$gcamountremaining=$giftCertificateDetail['gift_amount_remaining'];
								$gcgiftamount=$giftCertificateDetail['gift_amount'];


								$gcsendingdate=$giftCertificateDetail['sending_date'];



								$gcexpirydate=$giftCertificateDetail['expiry_date'];



				
								$storeapiky=$giftCertificateDetail['store_apikey'];
		
								if($gcsendingdate!=$gcsendingdate && $gcexpirydate<=time())

								{
									$gerror=1;	
									$dicountMessages['error'][0]='Please enter a valid gift certificate!';
								}
								
								if($gcamountremaining<=0)
								{
									$gerror=1;
									$dicountMessages['error'][0]='Gift certificate '.$gcCouponCode.' cannot be applied as it has expired!';
									
								}

					if(!in_array($storeapiky,$apikeys))
						{
							$gerror=1;
	
							$dicountMessages['error'][0]='Please enter a valid gift certificate!';
						}
						else if($gerror==0)
						{
						$gcCoupon= new Zend_Session_Namespace('Cart_GiftCertificate');
						$sessionCoupons = new Zend_Session_Namespace('Cart_Coupon');
						//total price of order
						$totalprice=0;
						foreach($sessionItem->items as $key=>$val)
						{
							if($val->storeApiKey==$storeapiky)
							{
							$totalprice+=($val->product_mrp * $val->product_qty)+($val->shipcost * $val->product_qty);
							$mallName=$val->store_api_key[0]['title'];
							$mallUrl=$val->store_api_key[0]['mallurl'];
							$storeapi=$val->storeApiKey;	
							}	

						}//end of foreach
						
					$dicountcouponapplied=0;
					//print_r($sessionCoupons->coupons);exit;
					if(!empty($sessionCoupons->coupons)) {  
						foreach($sessionCoupons->coupons as $key=>$val)
						{ foreach($val as $k=>$v)
							{       if($k==$storeapiky)
								{
								foreach($v as $lk=>$lv)
								{
									$dicountcouponapplied+=$lv['less'];
								}
								}
							}
						}
					$totalOrder=$totalprice-$dicountcouponapplied; 
					}
					if($gcamountremaining>$totalOrder)
					{
						$giftvalue=$totalOrder;
					}
					else
					{
						$giftvalue=$gcamountremaining;
					}
					$dicountMessages['sucess'][]='Gift certificate '.$gcCouponCode.' for '.$mallName.' redeemed successfully.
';
					
					$gcCoupon->giftcoupon[$storeapiky]['less']=$gcgiftamount;
					$gcCoupon->giftcoupon[$storeapiky]['fromstore']=$mallName;
					$gcCoupon->giftcoupon[$storeapiky]['mall_url']=$mallUrl;
					$gcCoupon->giftcoupon[$storeapiky]['ccode']=$gccouponcode;
					$gcCoupon->giftcoupon[$storeapiky]['gcid']=$grid;
								
					
								


						}
		
								
									
						}
				
				}
		}
		$sessiondsserr = new Zend_Session_Namespace('dicounterrors');	
		$sessiondsserr->dmessages=$dicountMessages;

		foreach($request as $key=>$val)
		{
                        $store_api_key;
			$prod = explode('_',$key);
			if($prod[0]=='qty')
			{
                                $store_api_key = $prod[1];
				$sessionItem->items[$prod[1]."_".$prod[2]."_".$prod[3]]->product_qty=$val;
			}
			if($prod[0]=='addresss')
			{       //echo "hi";exit;
                                $shippingcost = $cartMapper->productshippngcost($prod[2],$val);
				$sessionItem->items[$prod[1]."_".$prod[2]."_".$prod[3]]->shipcost=$shippingcost;
				$sessionItem->items[$prod[1]."_".$prod[2]."_".$prod[3]]->address_book_id=$val;
                                
				if($val === '0')
				{
                                   //array_push($errormaeesge, "Please select billing/ shipping address.");
				}
                                if($shippingcost === 'error')
				{
                                    
                                   // array_push($errormaeesges, "Please select billing/ shipping address.");
				}
                                
			}
			if($prod[0]=='billing')
			{
				if($val == 0)
				{
					//array_push($errormaeesge, "Please select billing/ shipping address.");
				}
				else
				{
					$sessionItem->billingaddress=$val;
				}

			}
			if(isset($request['payment']))
			{
                                $value = explode('_',$val);
                                if($value[0] == 'payment')
                                {
                                    //echo '<pre>';
                                    //print_r($value);exit;
                                    $sessionItem->paymentmode_id=$value[1];
                                    $sessionItem->paymentmode_type=$value[2];
                                }

			}
                        else
                        {
                          array_push($errormaeesge, "Please select payment mode.");
                        }
                        if($prod[0]=='applydc')
			{
                            //echo $store_api_key;exit;
                            $dc_number = $request['dc_code'];
                            $dcdetails = $cartMapper->getDCByNumber($dc_number, $store_api_key);
			}
		}


		$session_item = new Zend_Session_Namespace('Api_Model_Review_Buynow');
		$sessionReview = new Zend_Session_Namespace('Api_Model_Review_Buynow');
		$sessionReview->items=$session_item->items;
		$sessionReview->billingaddress=$session_item->billingaddress;
		$sessionReview->net_amt=$session_item->net_amt;
		$sessionReview->paymentmode_id=$session_item->paymentmode_id;
		$sessionReview->paymentmode_type=$session_item->paymentmode_type;

		if($request['continue_x'] || $request['continue'])
		{
                        if(count($errormaeesge) > 0 || count($errormaeesges)>0)
		{
                        $message = array_unique($errormaeesge);
			$msg = encryptLink(implode(':', $message));
			$this->_redirect("/cart/checkoutbn/error/$msg");
                        exit;
		}
			$this->_redirect('/cart/reviewbn');
		}
		$addUrl='';
		if($capplied)
		$addUrl='#dap';

		if($request['update_x'] || $request['update'] || isset($request['applydc_x']) ||  isset($request['applydc']) || isset($request['applygc_x']) ||  isset($request['applygc']))
		{
//echo 'dsfsdf';exit;
			$this->_redirect('/cart/checkoutbn'.$addUrl);
		}



	}
	public function reviewAction()
	{
		$this->view->headTitle('Review and place your order - Goo2o.com checkout');
	        $r= $this->_request->getParams();
                //echo "<pre>";
                //print_r($_SERVER);exit;
		 $str1 = $_SERVER['HTTP_REFERER'];
		 $str2 = $_SERVER['SERVER_NAME'];
		$str2_len = strlen($str2) + strripos($str1,$str2);
		$chkt_pgurl = substr($str1, $str2_len);
		$userName =  new Zend_Session_Namespace('USER');
		$ori = new Zend_Session_Namespace('original_login');
		$sessionReview = new Zend_Session_Namespace('Api_Model_Review');
		$sessionReviewPg = new Zend_Session_Namespace('Api_Model_Review_PG');
		$explodeUrl=explode("/",$str1);
//echo substr($chkt_pgurl, 0, 14);
		//echo "<pre>";
           // print_r($sessionReview->items);
			//exit;
                //echo count($sessionReview);exit;
				
                if(count($sessionReview->items)<=0)
				{
		$this->_redirect(HTTP_SERVER.'/cart/#list');
				}
		if($ori->userId=='')
		{
			$this->_redirect(HTTPS_SECURE.'/secure/login?tab=1');
		}
		
		if($r['er']=='')
		{
		if($explodeUrl[4] != 'checkout'  &&  $explodeUrl[4]!= 'pgtrans' &&  $explodeUrl[4]!= 'review')
		{
			$this->_redirect(HTTP_SERVER.'/cart/#list');
		}
		}else

		{
		$this->view->e=$r['er'];
		}
		$data=$sessionReview->items;
		$this->view->totaldata =count($data);
		$address_id=$sessionReview->billingaddress;
		$this->view->headLink()->appendStylesheet('/css/secure/review.css');
		
		if(!empty($data))
			{
			$totalamount=0;
				$reviewData=array();
				foreach($data as $key=>$val)
					{
					$totalamount+=$val->purchase_amt;

						if(array_key_exists($val->storeApiKey,$reviewData))
						{
							array_push($reviewData[$val->storeApiKey],$val);
						}
						else
						{
							$reviewData[$val->storeApiKey][0]=$val;
						}
					
					}
			}
			//echo "<pre>";
		//print_r($reviewData);

		
		$sessionReviewPg->item=$reviewData;
		
		$sessionReviewPg->billingaddress=$sessionReview->billingaddress;
		$sessionReviewPg->net_amt=$totalamount;

		$sessionReviewPg->paymentmode_id=$sessionReview->paymentmode_id;
		$sessionReviewPg->paymentmode_type=$sessionReview->paymentmode_type;
		$this->view->reviewdata = $reviewData;
		$this->view->billingdata = $this->_cartModel->getDetailAddress($address_id);
		$this->view->total_pamt=$totalamount;
		$paymentmethod_id = $sessionReview->paymentmode_id;
		$this->view->paymentmethod = $this->_cartModel->getPaymentMethodDetail($paymentmethod_id);
		$sessionReviewBack = new Zend_Session_Namespace('Api_Model_Review_Back');
		if(count($sessionReviewBack->item)>0)
		{
		
			$this->view->recentpaidOrder=$sessionReviewBack->item;
			//echo "<pre>";
			//print_r($sessionReviewBack->item);
//exit;
			unset($sessionReviewBack->item);
		}
			$sessionCoupons = new Zend_Session_Namespace('Cart_Coupon');
			$gcCoupon= new Zend_Session_Namespace('Cart_GiftCertificate');
			$this->view->coupons=$sessionCoupons->coupons;
			$this->view->gccoupons=$gcCoupon->giftcoupon;	
			if(!empty($sessionCoupons->coupons))
			 {  
				$disamount=0;
				foreach($sessionCoupons->coupons as $key=>$val)
				{   
					foreach($val as $k=>$v)
						{
						
				 			
				 			foreach($v as $lk=>$lv)
								{ 
									$disamount+= $lv['less'];
						
								 }
				 		}
						 
				}
		 } 
		if(!empty($gcCoupon->giftcoupon))
			{
			 $disamountgc=0;
				foreach($gcCoupon->giftcoupon as $keygc=>$valgc)
					{

                                      
                                         $disamountgc+=$valgc['less'];
                                   
				 } 
				}
		
		$this->view->totaldiscount=  $disamountgc+$disamount;
		
	}
	public function reviewbnAction()
	{
		$this->view->headTitle('Review and place your order - Goo2o.com checkout');
	        $r= $this->_request->getParams();
                //echo "<pre>";
                //print_r($_SERVER);exit;
		 $str1 = $_SERVER['HTTP_REFERER'];
		 $str2 = $_SERVER['SERVER_NAME'];
		$str2_len = strlen($str2) + strripos($str1,$str2);
		$chkt_pgurl = substr($str1, $str2_len);
		$userName =  new Zend_Session_Namespace('USER');
		$ori = new Zend_Session_Namespace('original_login');
		$sessionReview = new Zend_Session_Namespace('Api_Model_Review_Buynow');
		$sessionReviewPg = new Zend_Session_Namespace('Api_Model_Review_PG_Buynow');
		$explodeUrl=explode("/",$str1);
//echo substr($chkt_pgurl, 0, 14);
		//echo "<pre>";
           // print_r($sessionReview->items);
			//exit;
                //echo count($sessionReview->items);exit;
				
                if(count($sessionReview->items)<=0)
				{
		$this->_redirect(HTTP_SERVER.'/cart/#list');
				}
		if($ori->userId=='')
		{
			$this->_redirect(HTTPS_SECURE.'/secure/login?tab=1');
		}
		if($r['er']=='')
		{
		if($explodeUrl[4] != 'checkoutbn'  &&  $explodeUrl[4]!= 'pgtransbn' &&  $explodeUrl[4]!= 'reviewbn')
		{
			$this->_redirect(HTTP_SERVER.'/cart/#list');
		}
		}else

		{
		$this->view->e=$r['er'];
		}
		$data=$sessionReview->items;
		$this->view->totaldata =count($data);
		$address_id=$sessionReview->billingaddress;
		$this->view->headLink()->appendStylesheet('/css/secure/review.css');
		
		if(!empty($data))
			{
			$totalamount=0;
				$reviewData=array();
				foreach($data as $key=>$val)
					{
					$totalamount+=$val->purchase_amt;

						if(array_key_exists($val->storeApiKey,$reviewData))
						{
							array_push($reviewData[$val->storeApiKey],$val);
						}
						else
						{
							$reviewData[$val->storeApiKey][0]=$val;
						}
					
					}
			}
			//echo "<pre>";
		//print_r($reviewData);

		
		$sessionReviewPg->item=$reviewData;
		
		$sessionReviewPg->billingaddress=$sessionReview->billingaddress;
		$sessionReviewPg->net_amt=$totalamount;
		$sessionReviewPg->paymentmode_id=$sessionReview->paymentmode_id;
		$sessionReviewPg->paymentmode_type=$sessionReview->paymentmode_type;
		$this->view->reviewdata = $reviewData;
		$this->view->billingdata = $this->_cartModel->getDetailAddress($address_id);
		$this->view->total_pamt=$totalamount;
		$paymentmethod_id = $sessionReview->paymentmode_id;
		$this->view->paymentmethod = $this->_cartModel->getPaymentMethodDetail($paymentmethod_id);
		$sessionReviewBack = new Zend_Session_Namespace('Api_Model_Review_Back_Buynow');
		if(count($sessionReviewBack->item)>0)
		{
		
			$this->view->recentpaidOrder=$sessionReviewBack->item;
			//echo "<pre>";
			//print_r($sessionReviewBack->item);
//exit;
			unset($sessionReviewBack->item);
		}
			$sessionCoupons = new Zend_Session_Namespace('Cart_Coupon');
			$gcCoupon= new Zend_Session_Namespace('Cart_GiftCertificate');
			$this->view->coupons=$sessionCoupons->coupons;
			$this->view->gccoupons=$gcCoupon->giftcoupon;	
			if(!empty($sessionCoupons->coupons))
			 {  
				$disamount=0;
				foreach($sessionCoupons->coupons as $key=>$val)
				{   
					foreach($val as $k=>$v)
						{
						
				 			
				 			foreach($v as $lk=>$lv)
								{ 
									$disamount+= $lv['less'];
						
								 }
				 		}
						 
				}
		 } 
		if(!empty($gcCoupon->giftcoupon))
			{
			 $disamountgc=0;
				foreach($gcCoupon->giftcoupon as $keygc=>$valgc)
					{

                                      
                                         $disamountgc+=$valgc['less'];
                                   
				 } 
				}
		
		$this->view->totaldiscount=  $disamountgc+$disamount;
		
	}
	public function updatereviewAction()
	{
		$request = $this->_request->getParams();
		/*echo "<pre>";
		print_r($request);exit;*/
		if($request['edit_x'] || $request['edit_y'])
		{
			$this->_redirect('/cart/checkout');
		}
		if($request['confirm_x'] || $request['confirm_y'])
		{
			$this->_redirect(HTTP_SECURE_GOO2O.'/cart/success');
		}

	}
	public function acknowledgementAction()
	{
	
		$request = $this->_request->getParams();
		
		$orderId=$request['oid'];
		$userName =  new Zend_Session_Namespace('USER');
		
		
		$orderDetail=$this->_cartModel->getOrderDetail($orderId);
		
		if(empty($orderDetail))
		{
			$this->_redirect(HTTP_SERVER.'/cart/#list');

		}
		$this->view->headTitle('Acknowledgement receipt - '.$orderDetail[0]['title']);
		$ac=array();
		if(!empty($orderDetail))
			{
				$total=0;
				$i=0;
				foreach($orderDetail as $keys=>$vals)
				{
				

					$ac[$i]['orderid']=$vals['orderid'];
					$ac[$i]['item_id']=$vals['orderitemid'];
					$ac[$i]['storename']=$vals['title'];
					$ac[$i]['datepalced']=$vals['orderdate'];
	

					$ac[$i]['billing'] = $this->_cartModel->getDetailAddressOrder($vals['billingaddress']);
					$ac[$i]['storeid']=$vals['storeid'];
		
					$ac[$i]['paymentmode']= $this->_cartModel->getPaymentMethodDetailByName($vals['payment_module']);	
					$ac[$i]['shipping']=$this->_cartModel->getDetailAddressOrder($vals['shippingaddress']);
					$ac[$i]['productname']=$vals['product_name'];
					$ac[$i]['productshipping']=$vals['product_shipping_price'];
					$ac[$i]['product_mrp']=$vals['product_mrp'];
					$ac[$i]['product_variation']=$vals['product_variation'];
					$ac[$i]['product_condition']=$vals['product_condition'];
					$ac[$i]['product_id']=$vals['product_id'];
					$ac[$i]['pid']=$vals['pid'];

					$ac[$i]['order_item_total']=$vals['order_item_total'];
					$ac[$i]['order_item_total']=$vals['order_item_total'];
					$total+=($vals['product_mrp']*$vals['order_item_total'])+($vals['product_shipping_price']*$vals['order_item_total']);
				$i++;}
			}
      	$gcdetail= explode("-",$orderDetail[0]['gcdetail']);
		
	$discountdetail=array();
	$totaltominus=0;
	foreach($gcdetail as $key=>$val)
	{
		
		if($key==0)
		continue;
		
		$explodeValue=explode(":",$val);
		if($key==1)
		{
			if($explodeValue[0]!=0)
			{
		
				$discountdetail['giftcertificatevalue']=$explodeValue[1];
				
				$giftCertificateDetail= $this->_cartModel->getgiftDetailById($explodeValue[0]);
				$totaltominus+=$explodeValue[1];
				$discountdetail['giftcertificatecouponcode']=$giftCertificateDetail['gift_code'];
			}
		}
		else if($key==2)
		{
			if($explodeValue[0]!=0)
			{
				$discountdetail['ordervalue']=$explodeValue[1];
				$totaltominus+=$explodeValue[1];
				$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($explodeValue[0]);
				$discountdetail['ordershippingcode']=$giftCertificateDetail['coupon_code'];
			}
		}
		else if($key==3)
		{
			if($explodeValue[0]!=0)
			{
				$discountdetail['shippingvalue']=$explodeValue[1];
				$totaltominus+=$explodeValue[1];
				$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($explodeValue[0]);
				$discountdetail['shippingcode']=$giftCertificateDetail['coupon_code'];
			}
		}
		
			else if($explodeValue[0]!=0)
			{
				$discountdetail[$explodeValue[2]]['value']=$explodeValue[1];
				$totaltominus+=$explodeValue[1];
				$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($explodeValue[0]);
				$discountdetail[$explodeValue[2]]['code']=$giftCertificateDetail['coupon_code'];
			
		}
		
	}
	
	
	
	$this->view->detail=$ac;
	$this->view->discountdetail=$discountdetail;
	$this->view->totaltominus=$totaltominus;	
	

	$this->view->useremail=	$userName->userDetails[0]['user_email_address'];
	$this->view->totalpaymentTotal=	$total;
	}
	public function pgtransAction()
	{

		$db = Zend_Db_Table::getDefaultAdapter();
		$this->view->headTitle('Order is being processed - Goo2o.com checkout');
		$this->_helper->layout->disableLayout();
		$this->view->headLink()->appendStylesheet('/css/secure/checkout_common.css');
		$request = $this->_request->getParams();

		$userName =  new Zend_Session_Namespace('USER');
		$ori = new Zend_Session_Namespace('original_login');
		$sessionReview = new Zend_Session_Namespace('Api_Model_Review_PG');
if($ori->userId==865 || $ori->userId==865)
			{

				//exit;
			}
		
		//print_r($request);exit;
		/*echo "<pre>";
		print_r($_SESSION);
		print_r($_REQUEST);
		print_r($request);
		exit;*/
	
		
		$sessionReviewwelldone = new Zend_Session_Namespace('Api_Model_Review_Welldone');

		if($sessionReview->item[$request['transapikey']][0]->store_api_key[0]['trade_activation']=='0' || $sessionReview->item[$request['transapikey']][0]->store_api_key[0]['trade_activation']==2){
		$data=$sessionReview->item[$request['transapikey']];
		unset($_SESSION['Cart_Coupon']['coupons']['product'][$request['transapikey']]);
		unset($_SESSION['Cart_Coupon']['coupons']['order'][$request['transapikey']]);
		unset($_SESSION['Cart_Coupon']['coupons']['shipping'][$request['transapikey']]);
		unset($_SESSION['Cart_GiftCertificate']['giftcoupon'][$request['transapikey']]);
		//unset($_SESSION['Cart_GiftCertificate'][$request['transapikey']]);
                $sessionReviewBack = new Zend_Session_Namespace('Api_Model_Review_Back');
		$sessionReviewBack->item=$data;
                $sessionReviewwelldone->item[$request['transapikey']]=$data;
		if(!empty($_SESSION['Api_Model_Review_PG']['item'][$request['transapikey']]))
					{
						foreach($_SESSION['Api_Model_Review_PG']['item'][$request['transapikey']] as $kry=>$val)
							{
							
							unset($_SESSION['Api_Model_Cart']['items'][$val->storeApiKey."_".$val->product_id."_".$val->variationcode]);
							unset($_SESSION['Api_Model_Review']['items'][$val->storeApiKey."_".$val->product_id."_".$val->variationcode]); 
									
							}
					}
			unset($_SESSION['Api_Model_Review_PG']['item'][$typePayment['1']]);
				
				if(!empty($_SESSION['Api_Model_Cart']['items']))
				 $this->_redirect('/cart/review');
				else
				$this->_redirect(HTTP_SECURE_GOO2O.'/cart/success');
		}

		if($request['Merchant_Id']!='')
		{
		
			$Merchant_Param= $request['Merchant_Param'];
			$typePayment=explode("-",$Merchant_Param);
			$WorkingKey = "5lev0req5ugwcnm87u"; //put in the 32 bit working key in the quotes provided here
			$Merchant_Id= $request['Merchant_Id'];
			$Amount= $request['Amount'];
			$Order_Id= $typePayment[2];
			
			$Checksum= $request['Checksum'];
			$AuthDesc=$request['AuthDesc'];
			//echo $Merchant_Id."-".$Order_Id."-".$Amount."-".$AuthDesc."-".$Checksum."-".$WorkingKey;
			 $Checksum = verifyChecksum($Merchant_Id, $Order_Id , $Amount,$AuthDesc,$Checksum,$WorkingKey);
			/*echo $AuthDesc;
			echo "<pre>";
			print_r($request);
			exit;*/
			//print_r($request);
			//echo "update orders set response_data='".$request."',transaction_amount='".$request['Amount']."' where order_id=".$request['Order_Id'];exit;
			//echo "update orders set response_data='".serialize($request)."',transaction_amount='".$request['Amount']."',transaction_id='".$request['nb_order_no']."' where  order_id=".$request['Order_Id'];exit;
			$updateQuery=$db->query("update orders set response_data='".serialize($request)."',transaction_amount='".$request['Amount']."',transaction_id='".$request['nb_order_no']."',payment_status='0' where  order_id=".$Order_Id);
			if($Checksum=="true" && $AuthDesc=="Y")
			{
			/*if($Order_Id==1187)
			{
				echo "<pre>";
				print_r($_REQUEST);
				echo $request['Notes'];
				exit;
			}*/
				
			$tData=$_SESSION['carttriggerdata'];
			if($request['Notes']!='')
			$tData['billing_cust_notes']='<b>Special instructions :</b> '.$request['Notes'];
			else
			$tData['billing_cust_notes']='';
			
             $this->objTrigger->triggerFire(64,$tData);	
			$this->objTrigger->triggerFire(65,$tData);
			
			unset($_SESSION['carttriggerdata']);		
			unset($_SESSION['Cart_Coupon']['coupons']['product'][$typePayment['1']]);
			unset($_SESSION['Cart_Coupon']['coupons']['order'][$typePayment['1']]);
			unset($_SESSION['Cart_Coupon']['coupons']['shipping'][$typePayment['1']]);
			unset($_SESSION['Cart_GiftCertificate']['giftcoupon'][$typePayment['1']]);
			$purchase_data = $_SESSION['Api_Model_Review_PG']['item'][$typePayment['1']];
			 if(!empty($_SESSION['Api_Model_Review_PG']['item'][$typePayment['1']]))
					{
						foreach($_SESSION['Api_Model_Review_PG']['item'][$typePayment['1']] as $kry=>$val)
							{
					
										unset($_SESSION['Api_Model_Cart']['items'][$val->storeApiKey."_".$val->product_id."_".$val->variationcode]);
										unset($_SESSION['Api_Model_Review']['items'][$val->storeApiKey."_".$val->product_id."_".$val->variationcode]); 
									
							}

					}
		//unset($_SESSION['Cart_GiftCertificate'][$request['transapikey']]);
                $purchase_data = $_SESSION['Api_Model_Review_PG']['item'][$typePayment['1']];
		 unset($_SESSION['Api_Model_Review_PG'][$typePayment['1']]);
				
				if(!empty($_SESSION['Api_Model_Cart']['items']))
				 $this->_redirect('/cart/review/er/1');
				else
				$this->_redirect(HTTP_SECURE_GOO2O.'/cart/success');
			
				//echo "<br>Thank you for shopping with us. Your credit card has been charged and your transaction is successful. We will be shipping your order to you soon.";
	
				//Here you need to put in the routines for a successful 
				//transaction such as sending an email to customer,
				//setting database status, informing logistics etc etc
			}
			else if($Checksum=="true" && $AuthDesc=="B")
			{
				
			$tData=$_SESSION['carttriggerdata'];
                        //$this->objTrigger->triggerFire(64,$tData);
			if($request['Notes']!='')
			$tData['billing_cust_notes']='<b>Special instructions :</b> '.$request['Notes'];
			else
			$tData['billing_cust_notes']='';				
			$this->objTrigger->triggerFire(63,$tData);
			$this->objTrigger->triggerFire(77,$tData);
			
			unset($_SESSION['carttriggerdata']);
			unset($_SESSION['Cart_Coupon']['coupons']['order'][$typePayment['1']]);
			unset($_SESSION['Cart_Coupon']['coupons']['shipping'][$typePayment['1']]);
			unset($_SESSION['Cart_GiftCertificate']['giftcoupon'][$typePayment['1']]);
			$purchase_data = $_SESSION['Api_Model_Review_PG']['item'][$typePayment['1']];
			 if(!empty($_SESSION['Api_Model_Review_PG']['item'][$typePayment['1']]))
					{
						foreach($_SESSION['Api_Model_Review_PG']['item'][$typePayment['1']] as $kry=>$val)
							{
					
										unset($_SESSION['Api_Model_Cart']['items'][$val->storeApiKey."_".$val->product_id."_".$val->variationcode]);
										unset($_SESSION['Api_Model_Review']['items'][$val->storeApiKey."_".$val->product_id."_".$val->variationcode]); 
									
							}

					}
		//unset($_SESSION['Cart_GiftCertificate'][$request['transapikey']]);
                $purchase_data = $_SESSION['Api_Model_Review_PG']['item'][$typePayment['1']];
		 unset($_SESSION['Api_Model_Review_PG']['item'][$typePayment['1']]);
				
				if(!empty($_SESSION['Api_Model_Cart']['items']))
				 $this->_redirect('/cart/review/er/1');
				else
				$this->_redirect('/cart/success');
				//echo "<br>Thank you for shopping with us.We will keep you posted regarding the status of your order through e-mail";
	
				//Here you need to put in the routines/e-mail for a  "Batch Processing" order
				//This is only if payment for this transaction has been made by an American Express Card
				//since American Express authorisation status is available only after 5-6 hours by mail from ccavenue and at the "View Pending Orders"
			}
			else if($Checksum=="true" && $AuthDesc=="N")
			{
			$tData=$_SESSION['carttriggerdata'];
                        //$this->objTrigger->triggerFire(64,$tData);	
			//$this->objTrigger->triggerFire(63,$tData);
			//$tData['billing_cust_notes']=$request['billing_cust_notes'];
			$this->objTrigger->triggerFire(80,$tData);
			$this->objTrigger->triggerFire(81,$tData);	
			unset($_SESSION['carttriggerdata']);
			unset($_SESSION['Api_Model_Review_Back']);
			//echo "update orders payment_status='2' where order_id=".$request['Order_Id'];
			//echo "update order_item set order_item_status='6',order_sub_status_id='21',buyer_substatus='47' where  order_id=".$request['Order_Id'];exit;
			$updateQuery=$db->query("update orders set payment_status='2' where order_id=".$Order_Id);
		$updateQuery=$db->query("update order_item set order_item_status='6',order_sub_status_id='21',buyer_substatus='47' where  order_id=".$Order_Id);
			$orderDetail=$this->_cartModel->getOrderDetail($typePayment['2']);
		//print_r($orderDetail);exit;
	
		$ac=array();
		if(!empty($orderDetail))
			{
				$total=0;
				$i=0;
				foreach($orderDetail as $keys=>$vals)
				{
				//echo "update product_variation set variant_value=(variant_value+".$vals['order_item_total'].") where variant_name='Stock' and product_id=".$vals['product_id']." and variation_code=".$vals['product_variation_code'];exit;	
					$db->query("update product_variation set variant_value=(variant_value+".$vals['order_item_total'].") where variant_name='Stock' and product_id=".$vals['product_id']." and variation_code=".$vals['product_variation_code']);
			}
			}
   	$gcdetail= explode("-",$orderDetail[0]['gcdetail']);
	$discountdetail=array();
	$totaltominus=0;
	foreach($gcdetail as $key=>$val)
	{
		
		if($key==0)
		continue;
		
		$explodeValue=explode(":",$val);
		if($key==1)
		{
			if($explodeValue[0]!=0)
			{
				$giftCertificateDetail= $this->_cartModel->getgiftDetailById($explodeValue[0]);
				$db->query("update gift_amount_remaining set gift_amount_remaining=(gift_amount_remaining+".$explodeValue[1].") where gift_code='".$giftCertificateDetail['gift_code']."'" );
				//$discountdetail['giftcertificatevalue']=$explodeValue[1];
				
				
				//$totaltominus+=$explodeValue[1];
				//$discountdetail['giftcertificatecouponcode']=$giftCertificateDetail['gift_code'];
			}
		}
		if($key==2)
		{
			/*if($explodeValue[0]!=0)
			{
				$discountdetail['ordervalue']=$explodeValue[1];
				$totaltominus+=$explodeValue[1];
				$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($explodeValue[0]);
				$discountdetail['ordershippingcode']=$giftCertificateDetail['coupon_code'];
			}*/
		}
		if($key==3)
		{
			/*if($explodeValue[0]!=0)
			{
				$discountdetail['shippingvalue']=$explodeValue[1];
				$totaltominus+=$explodeValue[1];
				$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($explodeValue[0]);
				$discountdetail['shippingcode']=$giftCertificateDetail['coupon_code'];
			}*/
		}
		
			/*if($explodeValue[0]!=0)
			{
				$discountdetail[$explodeValue[2]]['value']=$explodeValue[1];
				$totaltominus+=$explodeValue[1];
				$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($explodeValue[0]);
				$discountdetail[$explodeValue[2]]['code']=$giftCertificateDetail['coupon_code'];
			
		}*/
		
	}
	
				
				//echo "<br>Thank you for shopping with us.However,the transaction has been declined.";
	
				//Here you need to put in the routines for a failed
				//transaction such as sending an email to customer
				//setting database status etc etc   /**/
			 $this->_redirect('/cart/review/er/'.$typePayment[1]);
			}
			else
			{
			unset($_SESSION['Api_Model_Review_Back']);
					$orderDetail=$this->_cartModel->getOrderDetail($typePayment['2']);
		
	
		$ac=array();
		if(!empty($orderDetail))
			{
				$total=0;
				$i=0;
				foreach($orderDetail as $keys=>$vals)
				{
					
					$db->query("update product_variation set variant_value=(variant_value+".$vals['order_item_total'].") where variant_name='Stock' and product_id=".$vals['product_id']." and variation_code=".$vals['product_variation_code']);
			}
			}
   	$gcdetail= explode("-",$orderDetail[0]['gcdetail']);
	$discountdetail=array();
	$totaltominus=0;
	foreach($gcdetail as $key=>$val)
	{
		
		if($key==0)
		continue;
		
		$explodeValue=explode(":",$val);
		if($key==1)
		{
			if($explodeValue[0]!=0)
			{
				$giftCertificateDetail= $this->_cartModel->getgiftDetailById($explodeValue[0]);
				$db->query("update gift_amount_remaining set gift_amount_remaining=(gift_amount_remaining+".$explodeValue[1].") where gift_code='".$giftCertificateDetail['gift_code']."'" );
				//$discountdetail['giftcertificatevalue']=$explodeValue[1];
				
				
				//$totaltominus+=$explodeValue[1];
				//$discountdetail['giftcertificatecouponcode']=$giftCertificateDetail['gift_code'];
			}
		}
		if($key==2)
		{
			/*if($explodeValue[0]!=0)
			{
				$discountdetail['ordervalue']=$explodeValue[1];
				$totaltominus+=$explodeValue[1];
				$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($explodeValue[0]);
				$discountdetail['ordershippingcode']=$giftCertificateDetail['coupon_code'];
			}*/
		}
		if($key==3)
		{
			/*if($explodeValue[0]!=0)
			{
				$discountdetail['shippingvalue']=$explodeValue[1];
				$totaltominus+=$explodeValue[1];
				$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($explodeValue[0]);
				$discountdetail['shippingcode']=$giftCertificateDetail['coupon_code'];
			}*/
		}
		
			/*if($explodeValue[0]!=0)
			{
				$discountdetail[$explodeValue[2]]['value']=$explodeValue[1];
				$totaltominus+=$explodeValue[1];
				$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($explodeValue[0]);
				$discountdetail[$explodeValue[2]]['code']=$giftCertificateDetail['coupon_code'];
			
		}*/
		
	}
				 $this->_redirect('/cart/review/er/'.$typePayment[1]);
				
				//echo "<br>Thank you for shopping with us.However,the transaction has been declined.";
	
				//Here you need to put in the routines for a failed
				//transaction such as sending an email to customer
				//setting database status etc etc   /**/
				//echo "<br>Security Error. Illegal access detected";
	
				//Here you need to simply ignore this and dont need
				//to perform any operation in this condition
			}
			
		
		
		$generalobject=new General();
						//if($userName->userId==865)
		$generalobject->inserttofronttableproduct($productdetail->product_id);
		}
		//echo "<pre>";
		//print_r($_SESSION);
		//print_r($sessionReview->paymentmode_type);exit;
             ;
        if(count($sessionReview->item)<=0)
		{
		//$this->_redirect(HTTP_SERVER.'/cart/#list');
		}
		if($ori->userId=='')
		{
			$this->_redirect(HTTPS_SECURE.'/secure/login?tab=1');
		}
		/*if(count($sessionReview)<=0)
		{
				$this->_redirect(HTTP_SERVER.'/cart/#list');
		}*/
		//echo "<pre>";
		//echo $request['transapikey'];
		//print_r($_SESSION);
		//print_r($_SESSION['Api_Model_Cart']);
//exit;
	
		$data=$sessionReview->item[$request['transapikey']];
		$sessionCoupons = new Zend_Session_Namespace('Cart_Coupon');
		$gcCoupon= new Zend_Session_Namespace('Cart_GiftCertificate');
		$dccoupons=$sessionCoupons->coupons;
		$gccoupons=$gcCoupon->giftcoupon;
		/*echo "<pre>";
		print_r($data);
		print_r($dccoupons);
		print_r($gccoupons);*/
		$sessionCoupons = new Zend_Session_Namespace('Cart_Coupon');
		$gcCoupon= new Zend_Session_Namespace('Cart_GiftCertificate');
		$dccoupons=$sessionCoupons->coupons;
		$gccoupons=$gcCoupon->giftcoupon;
		$dcTextMail=0;	
               //echo "<pre>";
		//print_r($_SESSION);
//exit;
		
		 if(!empty($gccoupons))
		{
			foreach($gccoupons as $keygc=>$valgc)
			{
			//print_r($valgc);exit;
				if($keygc==$request['transapikey'])
					{
						$giftCertificateDetail= $this->_cartModel->getgiftDetailById($valgc['gcid']);
						//print_r($giftCertificateDetail);exit;
						if($giftCertificateDetail['gift_amount_remaining']!=$valgc['less'])
						{
							unset($_SESSION['Cart_GiftCertificate']['giftcoupon'][$request['transapikey']]);
							$this->_redirect('/cart/checkout/derror/1');	
						}
						else
						{
							$db = Zend_Db_Table::getDefaultAdapter();
							//echo "update gift_certificate_recipient set gift_amount_remaining=".($giftCertificateDetail['gift_amount_remaining']-$valgc['less'])." where id=".$giftCertificateDetail['rid'];exit;
							$db->query("update gift_certificate_recipient set gift_amount_remaining=".($giftCertificateDetail['gift_amount_remaining']-$valgc['less'])." where id=".$giftCertificateDetail['rid']);
						
						}
						$gcamountString.=$data[0]->store_api_key[0]['id']."-".$valgc['gcid'].":".$valgc['less'];
						//update balance in gift certificate table

						
					}
					
			}
		 }
		else
		{
			$gcamountString.=$data[0]->store_api_key[0]['id']."-0:0";
		}
		
		
			if(!empty($dccoupons['order'][$request['transapikey']][0]))
			{ 
				$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($dccoupons['order'][$request['transapikey']][0]['couponid']);
				
				$getCustomerRed=$this->_cartModel->customerUseCouponTotal($dccoupons['order'][$request['transapikey']][0]['couponid']);


			if($giftCertificateDetail['usage_user']==1 && $giftCertificateDetail['usage_user_per']<$getCustomerRed['total'] && $giftCertificateDetail['usage_user_per']!=0)
				{
				

							unset($_SESSION['Cart_Coupon']['coupons']['order'][$request['transapikey']]);
							$this->_redirect('/cart/checkout/derror/1');
						
				}	
				else if( $giftCertificateDetail['delete_status']==0)
				{

							unset($_SESSION['Cart_Coupon']['coupons']['order'][$request['transapikey']]);

							$this->_redirect('/cart/checkout/derror/1');
				}
				else
				{/**/
				
			$dcTextMail+=$dccoupons['order'][$request['transapikey']][0]['less'];
				$gcamountString.="-".$dccoupons['order'][$request['transapikey']][0]['couponid'].":".$dccoupons['order'][$request['transapikey']][0]['less'];
				$this->_cartModel->updatecouponByUser($dccoupons['order'][$request['transapikey']][0]['couponid']);
				}
				//update coupon reedemed by user
			}
			else
			{
				$gcamountString.="-0:0";	
			}
			

			
if(!empty($dccoupons['shipping'][$request['transapikey']][0]))
			{ 
				$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($dccoupons['shipping'][$request['transapikey']][0]['couponid']);
				$getCustomerRed=$this->_cartModel->customerUseCouponTotal($dccoupons['shipping'][$request['transapikey']][0]['couponid']);
			if($giftCertificateDetail['usage_user']==1 && $giftCertificateDetail['usage_user_per']<$getCustomerRed['total'])
				{	
					
							unset($_SESSION['Cart_Coupon']['coupons']['shipping'][$request['transapikey']]);
							$this->_redirect('/cart/checkout/derror/1');
					
				}	
				else if($giftCertificateDetail['delete_status']==0)
				{
							unset($_SESSION['Cart_Coupon']['coupons']['order'][$request['transapikey']]);
							$this->_redirect('/cart/checkout/derror/1');
				}
				else
				{ 
				//echo 'df gdfsgdfs';exit;
				$dcTextMail+=$dccoupons['shipping'][$request['transapikey']][0]['less'];
				$gcamountString.="-".$dccoupons['shipping'][$request['transapikey']][0]['couponid'].":".$dccoupons['shipping'][$request['transapikey']][0]['less'];
				$this->_cartModel->updatecouponByUser($dccoupons['shipping'][$request['transapikey']][0]['couponid']);
				//update coupon reedemed by user
				}			
			}
			else
			{
				$gcamountString.="-0:0";
			} 


			 foreach($sessionReview->item[$request['transapikey']] as $key=>$productdetail)
				{
				
					if(!empty($dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$productdetail->variationcode]))
					{  
	//echo $dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$productdetail->variationcode]['couponid'];exit;				
				 	$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$productdetail->variationcode]['couponid']);
			//print_r($giftCertificateDetail);exit;
				$getCustomerRed=$this->_cartModel->customerUseCouponTotal($dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$productdetail->variationcode]['couponid']);
			//print_r($getCustomerRed);exit;
			if($giftCertificateDetail['usage_user']==1 && $giftCertificateDetail['usage_user_per']<$getCustomerRed['total'])
				{	
					
		
							unset($_SESSION['Cart_Coupon']['coupons']['product'][$request['transapikey']]);
							$this->_redirect('/cart/checkout/derror/1');
						
				}	
				else if($giftCertificateDetail['delete_status']==0)
				{
					
							unset($_SESSION['Cart_Coupon']['coupons']['product'][$request['transapikey']]);
							$this->_redirect('/cart/checkout/derror/1');
				}
				else
				{ 
				//echo $dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$productdetail->variationcode]['couponid'];exit;
				
				$this->_cartModel->updatecouponByUser($dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$productdetail->variationcode]['couponid']);
				//update coupon reedemed by user
				}		
				
				}

				}	
				
		
		$address_id=$sessionReview->billingaddress;
		$total_pamt=$sessionReview->net_amt;
		$paymentmode_id=$sessionReview->paymentmode_id;
		$paymentmode_type=$sessionReview->paymentmode_type;
		$this->view->data=$data;
		$this->view->address_id=$address_id;
		$this->view->total_pamt=$total_pamt;
		$this->view->paymentmode_id=$paymentmode_id;
		$this->view->paymentmode_type=$paymentmode_type;
                $billingaddress_detail = $this->_cartModel->getDetailAddress($address_id);
			
                $fullname = $billingaddress_detail[0]['fullname'];
                $address = $billingaddress_detail[0]['address'];
                $zipcode = $billingaddress_detail[0]['zipcode'];
                $city = $billingaddress_detail[0]['cityname'];
                $state = $billingaddress_detail[0]['state_name'];
                $phone = $billingaddress_detail[0]['phone'];
                $officeaddress = $billingaddress_detail[0]['officeaddress'];
                // code to be place in payment module
		foreach($sessionReview->item[$request['transapikey']] as $key=>$productdetail)
		{


			$storeApikeyforrest=$productdetail->storeApiKey;
		}
	
		$addressid=$this->_cartModel->checkOrderAddress($address_id, $fullname, $address, $zipcode, $city, $state, $phone, $officeaddress,$ori->userId,$storeApikeyforrest);
$a_bliiing=$address_id;
		$a_fullname=$fullname;
		$a_address=$address;
		$a_zipcode=$zipcode;
		$a_city=$city;
		$a_stae=$state;
		$a_phone=$phone;
		$a_off=$officeaddress;
		$a_uid=$ori->userId;
		
		if($addressid && $addressid!=0)
		{
                	$orderaddresses_id = $addressid;//$orderaddresses_id = $this->_cartModel->getOrderAddressesId($addressid);
        	}
		else
		{
			$billingaddress_detail = $this->_cartModel->getDetailAddress($address_id);
			unset($billingaddress_detail[0]['city']);
			unset($billingaddress_detail[0]['state']);
			unset($billingaddress_detail[0]['deletedflag']);
			$city_name = $billingaddress_detail[0]['cityname'];
			$state_name = $billingaddress_detail[0]['state_name'];
			$customer_id = $billingaddress_detail[0]['customers_id'];
			$billingaddress_detail[0]['city']=$city_name;
			$billingaddress_detail[0]['state'] = $state_name;
			$billingaddress_detail[0]['customer_id'] = $customer_id;
			unset($billingaddress_detail[0]['cityname']);
			unset($billingaddress_detail[0]['state_name']);
			unset($billingaddress_detail[0]['customers_id']);

			$orderaddresses_id = $this->_cartModel->insertOrderAddresses($billingaddress_detail[0],$storeApikeyforrest);
			if($orderaddresses_id==0)
			{
				echo 'order address id not found';exit;
			}

		}
		
		$ori = new Zend_Session_Namespace('original_login');
		$customer_id = $ori->userId;

		$paymentmode_type=$sessionReview->paymentmode_type;
		$orders_id = $this->_cartModel->insertOrder($customer_id, $orderaddresses_id, $paymentmode_type,$storeApikeyforrest);
		if($orders_id==0)
		{
			echo 'order id not found';exit;
		}
                if($orders_id)
                {
                    $this->view->order_no = $orders_id;
                }
		

		$dateMail=date("m/d/Y");
		$shippingpriceMail=0;
                foreach($sessionReview->item[$request['transapikey']] as $key=>$productdetail)
		{
	//echo "<pre>";
	
	//print_r($productdetail);
	//exit;
	
			$productnameMail.=$productdetail->product_name.(($productdetail->variations[$productdetail->product_id]['allvariation'])?" - ".$productdetail->variations[$productdetail->product_id]['allvariation']:'').' x '.$productdetail->product_qty.' = Rs. '.number_format(($productdetail->product_mrp*$productdetail->product_qty)+($productdetail->shipcost*$productdetail->product_qty),2).'<br />';
			$shippingpriceMail=shippingpriceMail+($productdetail->shipcost*$productdetail->product_qty);
			$storenameMail=$productdetail->store_api_key[0]['title'];
			$storeApiKeyMail=$productdetail->storeApiKey;
			$followapikey=$productdetail->store_api_key[0]['apikey'];
			$storeuseridMail=$productdetail->store_api_key[0]['user_id'];
			$storeidMail=$productdetail->store_api_key[0]['mallid'];
			$streownernameMail=$productdetail->store_api_key[0]['user_full_name'];
			$mallNameMail=$productdetail->store_api_key[0]['title'];
			$mallurlMail=$productdetail->store_api_key[0]['mallurl'];
			$mallapikeyMail=$productdetail->store_api_key[0]['apikey'];
			$user_email_addressMail=$productdetail->store_api_key[0]['user_email_address'];
		
                        $product_variation = $productdetail->variations[$productdetail->product_id]['allvariation'];
			$product_condition = $productdetail->variations[$productdetail->product_id]['condition'];
			$product_name = $productdetail->product_name;
			$product_mrp = $productdetail->variations[$productdetail->product_id]['srp'];
			$product_shipping_price = ($productdetail->shipcost)*($productdetail->product_qty);
			$product_variation_code = $productdetail->variationcode;
			$product_id = $productdetail->product_id;
			//echo $productdetail->product_qty;exit; 	
			$customizationdata='';
			$formid='0';
			//unset($productdetail->customizeddata);

		
							if(trim($productdetail->customizeddata)!='')
							{
								$customizationdata.='Customization details of '.$productdetail->product_name.(($productdetail->variations[$productdetail->product_id]['allvariation'])?" - ".$productdetail->variations[$productdetail->product_id]['allvariation']:'').'<br />';
								$customizationdata.='<table cellpadding="0" cellspaceing="0" border="0">';
								$explodecustomization=explode("-~^-",$productdetail->customizeddata);
								$formid=$explodecustomization[1];
								$exdata=explode("~~~~",$explodecustomization[0]);
								
									if(!empty($exdata))
									{
										foreach($exdata as $k=>$v)
										{
											$explodeval=explode("^^^^",$v);
											
											if(trim($explodeval[1])!='')
											{
												 $key=$explodeval[1];
											}
											else
											{
												$key='--';
											}
											if($key==='')
											$key='';				
					
										if(trim($explodeval[0])!='' && trim($explodeval[0])!='')
											{
												$index=$explodeval[0];
											}
											else
											{
												$index='';
											}
											
											$customizationdata.='<tr><td><b>'.$index.'</b></td><td> : '.stripslashes(stripslashes($key)).'</td></tr>';
										}
										$customizationdata.='</table><br />';
									}
							}
	
				mail('saroj.dkl4u@gmail.com', 'My Subject', $customizationdata);				
						
                        $productid = $this->_cartModel->insertOrderProductDetail($product_id, $product_variation, $product_condition, $product_name, $product_mrp, $product_shipping_price, $product_variation_code,$productdetail->product_qty,$productdetail->customizeddata,$formid,$storeApikeyforrest);
			if( $productid==0)
			{
				echo 'product id not found';exit;
			}
						
			
						//echo 'd gdfg dfdfgdfg df';exit;
			$shippint_add = $productdetail->address_book_id;
			$order_item_total = $productdetail->product_qty;
			$order_item_owner = $productdetail->store_api_key[0]['user_id'];
                        $shippingaddress_detail = $this->_cartModel->getDetailAddress($shippint_add);
                        $fullnames = $shippingaddress_detail[0]['fullname'];
                        $addresss = $shippingaddress_detail[0]['address'];
                        $zipcodes = $shippingaddress_detail[0]['zipcode'];
                        $citys = $shippingaddress_detail[0]['cityname'];
                        $states = $shippingaddress_detail[0]['state_name'];
                        $phones = $shippingaddress_detail[0]['phone'];
                        $officeaddresss = $shippingaddress_detail[0]['officeaddress'];
			$ori = new Zend_Session_Namespace('original_login');
						
                        $addressids=$this->_cartModel->checkOrderAddress($shippint_add, $fullnames, $addresss, $zipcodes, $citys, $states, $phones, $officeaddresss,$ori->userId,$storeApikeyforrest);
						
                       if($addressids && $addressids!=0)
                        {
						
                            $ordershipping = $addressids;//$this->_cartModel->getOrderAddressesId($shippint_add);
							
                        }
                        else
                        {
							

                            $billingaddress_detail = $this->_cartModel->getDetailAddress($shippint_add);
                            unset($billingaddress_detail[0]['city']);
                            unset($billingaddress_detail[0]['state']);
                            $city_name = $billingaddress_detail[0]['cityname'];
                            $state_name = $billingaddress_detail[0]['state_name'];
                            $customer_id = $billingaddress_detail[0]['customers_id'];
                            $billingaddress_detail[0]['city']=$city_name;
                            $billingaddress_detail[0]['state'] = $state_name;
                            $billingaddress_detail[0]['customer_id'] = $customer_id;
                            unset($billingaddress_detail[0]['cityname']);
                            unset($billingaddress_detail[0]['state_name']);
                            unset($billingaddress_detail[0]['customers_id']);
							/*if($product_id==28774)
	{
	echo "<pre>";
	print_r($billingaddress_detail[0]);
	echo $sqlFeature="select * from product_feature where product_id=".$product_id;
	echo 'here';
	echo 'ok ankush';exit;
	}	*/
                            $ordershipping = $this->_cartModel->insertOrderAddresses($billingaddress_detail[0],$storeApikeyforrest);
							
                        }
						
                        $order_item = $this->_cartModel->insertOrderItem($orders_id, $ordershipping, $order_item_owner, $order_item_total, $productid,$storeApikeyforrest);
			if($order_item==0)
			{
				echo 'order item id missing';exit;
			}
				

					
						$this->_cartModel->updatepersiable($productdetail->product_id, $order_item,$storeApikeyforrest);

						
						$generalobject=new General();
					//	if($userName->userId==865)
							//$generalobject->inserttofronttableproduct($productdetail->product_id);
							//echo 'here';exit;	

			$orderItemMail.='OR-'.$productdetail->store_api_key[0]['mallid'].'-'.$orders_id.'-'.$order_item.' : '.$productdetail->product_name.(($productdetail->variations[$productdetail->product_id]['allvariation'])?" - ".$productdetail->variations[$productdetail->product_id]['allvariation']:'').' x '.$productdetail->product_qty.' = Rs. '.number_format(($productdetail->product_mrp*$productdetail->product_qty)+($productdetail->shipcost*$productdetail->product_qty),2).'<br />';
			//echo $productdetail->product_id."_".$product_variation_code;
			//print_r($dccoupons['product'][$request['transapikey']]);exit;
			if(!empty($dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$product_variation_code]))
			{  
			 $dcTextMail+=$dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$product_variation_code]['less'];	
				 $gcamountString.="-".$dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$product_variation_code]['couponid'].":".$dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$product_variation_code]['less'].":".$order_item;
				//update coupon reedemed by user
					$this->_cartModel->updatecouponByUser($dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$product_variation_code]['couponid']);
			}
			else
			{
				$gcamountString.="-0:0:".$order_item;
			} 
			
		}
		//echo $gcamountString;exit;
		
                $db->query("update orders set gc_amount='".$gcamountString."' where order_id=".$orders_id);
		$client = new Zend_Rest_Client('http://orders.o2ocheckout.com');
		$options['api_key']=$storeApikeyforrest;
		$options['method'] = 'update';
		$options['table'] = 'orders';
		$options['data']=array('gc_amount'=>$gcamountString);
		$options['where']=array('order_id'=>$orders_id);
		$response = $client->restPost('/api/services/update-record', $options);
		if($sessionReview->paymentmode_type=='DB' ||  $request['totalpay']<=0 ||  $sessionReview->paymentmode_type=='PCD'){
		unset($_SESSION['Cart_Coupon']['coupons']['product'][$request['transapikey']]);
		unset($_SESSION['Cart_Coupon']['coupons']['order'][$request['transapikey']]);
		unset($_SESSION['Cart_Coupon']['coupons']['shipping'][$request['transapikey']]);
		unset($_SESSION['Cart_GiftCertificate']['giftcoupon'][$request['transapikey']]);
		//unset($_SESSION['Cart_GiftCertificate'][$request['transapikey']]);
                $purchase_data = $_SESSION['Api_Model_Review_PG']['item'][$request['transapikey']];
		}
				
                foreach($purchase_data as $key=>$val)
                {
                    $store_apikey = $val->store_api_key[0][apikey];
                    $product_id = $val->product_id;
                    $variationcode = $val->variationcode;
			//if($sessionReview->paymentmode_type=='DB'){			
                    $delete_status = $this->_cartModel->removeBasket($store_apikey, $product_id, $variationcode, $customer_id);
			//}
					
                  //  $order_shipping_detail_id = $this->_cartModel->getProductShippingPolicyDetail($product_id, $order_item);
					//echo 'df gdfg dfgsd fsdsdf sdfsd';exit;
                }
				if( $orders_id )
					{
						$data['orderid'] = $orders_id ;
						$sessionReviewBack = new Zend_Session_Namespace('Api_Model_Review_Back');
						$sessionReviewBack->item=$data;

					}
				
				
				$sessionReviewwelldone->item[$request['transapikey']]=$data;
				
			 
			 if($sessionReview->paymentmode_type=='DB' ||  $request['totalpay']<=0 ||  $sessionReview->paymentmode_type=='PCD'){
			    if(!empty($_SESSION['Api_Model_Review_PG']['item'][$request['transapikey']]))
					{
						foreach($_SESSION['Api_Model_Review_PG']['item'][$request['transapikey']] as $kry=>$val)
							{
							
										unset($_SESSION['Api_Model_Cart']['items'][$val->storeApiKey."_".$val->product_id."_".$val->variationcode]);
										unset($_SESSION['Api_Model_Review']['items'][$val->storeApiKey."_".$val->product_id."_".$val->variationcode]); 
									
							}
					}
			}
			 $sessionDiscountAmount = new Zend_Session_Namespace('CartDIscountAmount');
			 $sessionDiscountAmount->total= $sessionDiscountAmount->total+$request['discountvalue'];
$billingaddress_detail = $this->_cartModel->getDetailAddress($address_id);
			$totalpayMail==$request['totalpay'];
//foreach($billingaddress_detail as $key=>$val)
							//{
								//$billingAddressMail=ucwords(strtolower($val['fullname'])).", <br /> ".stripslashes($val['address']).", <br />".$val['cityname'].",  <br />".$val['state_name']." <br />".$val['zipcode']." <br /> Phone: ".$val['phone'];
$billingAddressMail=ucwords(strtolower($a_fullname)).", <br /> ".stripslashes($a_address).", <br />".$a_city.",  <br />".$a_stae." <br />".$a_zipcode." <br /> Phone: ".$a_phone;
							//}
$ori = new Zend_Session_Namespace('original_login');	
 $locationsStore=$this->_cartModel->getStoreLocationsByApikey($storeuseridMail);
  $storephone=$this->_cartModel->getStorePhoneById($mallapikeyMail);
if($dcTextMail>0)
{
		$discountmessage='The total amount of discount coupon used for this order is Rs '.number_format($dcTextMail,2);
}
else
{
	$discountmessage='';
}
$sq=$db->query("update orders set transaction_amount=".$request['totalpay']." where order_id=".$orders_id);
$client = new Zend_Rest_Client('http://orders.o2ocheckout.com');
		$options['api_key']=$storeApikeyforrest;
		$options['method'] = 'update';
		$options['table'] = 'orders';
		$options['data']=array('transaction_amount'=>$request['totalpay']);
		$options['where']=array('order_id'=>$orders_id);
		$response = $client->restPost('/api/services/update-record', $options);


	$sql=$db->query("select * from store_follow_customer where capikey='".$ori->apikey."' and sapikey='".$followapikey."' and deleted_flag='0'");
	$followdata=$sql->fetchAll();
	
	if(empty($followdata))
	{
		$db->query("insert into store_follow_customer set capikey='".$ori->apikey."',sapikey='".$followapikey."',folowing='1',follow_time=".time()."");
	}
	


//
$sessionReviewPg = new Zend_Session_Namespace('Api_Model_Review_PG');
		 
		 $sessionReviewPg->paymentmode_type=$sessionReview->paymentmode_type;
if($sessionReviewPg->paymentmode_type=='DB')
{
  $pmode='Direct bank diposit';
}else if($sessionReviewPg->paymentmode_type=='CC')
{
	$pmode='Pay online';
}else if($sessionReviewPg->paymentmode_type=='PCD')
{
	$pmode='Pay cash on delivery';
}
 
$tData = array('to_id'=>$ori->userId,
                         'from_id'=>$storeuseridMail,
			'from_name'=>$mallNameMail,
			'from_mail'=>$user_email_addressMail,
			'to_id'=>$ori->userId,
			'to_mail'=>$userName->userDetails[0]['user_email_address'],
			'to_name'=>$userName->userDetails[0]['user_full_name'],
			'storeapikey'=>	$storeApiKeyMail,
			 'order_date'=>$dateMail,
			 'store_name'=>$storenameMail,
			 'customer_name'=>$_SESSION['USER']['userDetails'][0]['user_full_name'],
			'discount_coupon'=>$discountmessage,
			'gift_certificate'=>'',	
			'product_name'=>$orderItemMail,
			'total_amount'=>number_format($request['totalpay'],2),
			'total_shipping'=>number_format($shippingpriceMail,2),
			'billing_address'=>$billingAddressMail,
			'link'=>'secure.sketcheeze.com/cart/acknowledgement/oid/'.$orders_id,
			'store_owner_name'=>$streownernameMail,
			'order_item_detail'=>$orderItemMail,
			'order_id'=>'OR-'.$storeidMail.'-'.$orders_id,
			'store_url'=>$mallurlMail,
			'store_emails'=> $locationsStore,
			'store_phone'=>  $storephone,
			'customizationdata' =>$customizationdata,
			'paymentmode'=>$pmode,
			);
			$_SESSION['carttriggerdata']= $tData;
	
//echo "<pre>";
//print_r($tData);exit;
if($sessionReview->paymentmode_type=='DB' || $request['totalpay']<=0 || $sessionReview->paymentmode_type=='PCD'){
		/*echo "<pre>";
		print_r($_SESSION);
		exit;*/
			//print_r($tData);exit;
			if($ori->userId==865)
			{
				//echo "<pre>";
				//print_r($_SESSION['Api_Model_Cart']['items']);
				//exit;
			}
			$updateQuery=$db->query("update orders set payment_status='0' where  order_id=".$orders_id);
$client = new Zend_Rest_Client('http://orders.o2ocheckout.com');
		$options['api_key']=$storeApikeyforrest;
		$options['method'] = 'update';
		$options['table'] = 'orders';
		$options['data']=array('payment_status'=>0);
		$options['where']=array('order_id'=>$orders_id);
		$response = $client->restPost('/api/services/update-record', $options);
		
                                     $this->objTrigger->triggerFire(64,$tData);	
				     $this->objTrigger->triggerFire(65,$tData);	
			    unset($_SESSION['Api_Model_Review_PG'][$request['transapikey']]);
				

				if(!empty($_SESSION['Api_Model_Cart']['items']))
				 $this->_redirect('/cart/review/er/1');
				else
				$this->_redirect('/cart/success');
}
				
				// unset($_SESSION['Api_Model_Cart']);
               // unset($_SESSION['Api_Model_Review']);
	
	
		$this->view->headScript()->appendFile('/jscript/common/jquery-1.5.1.js','text/javascript');
			$this->view->useremail=	$userName->userDetails[0]['user_email_address'];
  			$address_id=$sessionReview->billingaddress;
		$total_pamt=$sessionReview->net_amt;
		$paymentmode_id=$sessionReview->paymentmode_id;
		$paymentmode_type=$sessionReview->paymentmode_type;
		$this->view->data=$data;
		$this->view->address_id=$address_id;
		$this->view->total_pamt=$total_pamt;
		$this->view->paymentmode_id=$paymentmode_id;
		$this->view->paymentmode_type=$paymentmode_type;
		$this->view->storeId=$storeidMail;
		$this->view->userIdebs=$ori->userId;
                
	$this->view->billingdetail=$billingaddress_detail[0];
		$this->view->totalpay=$request['totalpay'];
		$this->view->oid=$orders_id;
		$this->view->ak=$request['transapikey'];
		
	
	}
public function pgtranssecureAction()
	{

		$db = Zend_Db_Table::getDefaultAdapter();
		$this->view->headTitle('Order is being processed - Goo2o.com checkout');
		$this->_helper->layout->disableLayout();
		$this->view->headLink()->appendStylesheet('/css/secure/checkout_common.css');
		$request = $this->_request->getParams();
		

		

		
		
		//check trade activation
		$tradeactivation=1;
		
		

		if($tradeactivation==1){
		
		
				
		}

		if($request['Merchant_Id']!='')
		{
		
			$Merchant_Param= $request['Merchant_Param'];
			$typePayment=explode("-",$Merchant_Param);
			$WorkingKey = "5lev0req5ugwcnm87u"; //put in the 32 bit working key in the quotes provided here
			$Merchant_Id= $request['Merchant_Id'];
			$Amount= $request['Amount'];
			$Order_Id= $typePayment[2];
			
			$Checksum= $request['Checksum'];
			$AuthDesc=$request['AuthDesc'];
			
			 $Checksum = verifyChecksum($Merchant_Id, $Order_Id , $Amount,$AuthDesc,$Checksum,$WorkingKey);
			
			$updateQuery=$db->query("update orders set response_data='".serialize($request)."',transaction_amount='".$request['Amount']."',transaction_id='".$request['nb_order_no']."',payment_status='0' where  order_id=".$Order_Id);
			if($Checksum=="true" && $AuthDesc=="Y")
			{
			
				
			$tData=$_SESSION['carttriggerdata'];
			if($request['Notes']!='')
			$tData['billing_cust_notes']='<b>Special instructions :</b> '.$request['Notes'];
			else
			$tData['billing_cust_notes']='';
			
                        $this->objTrigger->triggerFire(64,$tData);	
			$this->objTrigger->triggerFire(65,$tData);
			
			unset($_SESSION['carttriggerdata']);		
			unset($_SESSION['Cart_Coupon']['coupons']['product'][$typePayment['1']]);
			unset($_SESSION['Cart_Coupon']['coupons']['order'][$typePayment['1']]);
			unset($_SESSION['Cart_Coupon']['coupons']['shipping'][$typePayment['1']]);
			unset($_SESSION['Cart_GiftCertificate']['giftcoupon'][$typePayment['1']]);
			$purchase_data = $_SESSION['Api_Model_Review_PG']['item'][$typePayment['1']];
			 if(!empty($_SESSION['Api_Model_Review_PG']['item'][$typePayment['1']]))
					{
						foreach($_SESSION['Api_Model_Review_PG']['item'][$typePayment['1']] as $kry=>$val)
							{
					
										unset($_SESSION['Api_Model_Cart']['items'][$val->storeApiKey."_".$val->product_id."_".$val->variationcode]);
										unset($_SESSION['Api_Model_Review']['items'][$val->storeApiKey."_".$val->product_id."_".$val->variationcode]); 
									
							}

					}
		
                $purchase_data = $_SESSION['Api_Model_Review_PG']['item'][$typePayment['1']];
		 unset($_SESSION['Api_Model_Review_PG'][$typePayment['1']]);
				
				if(!empty($_SESSION['Api_Model_Cart']['items']))
				 $this->_redirect('/cart/review/er/1');
				else
				$this->_redirect(HTTP_SECURE_GOO2O.'/cart/success');
			
				//echo "<br>Thank you for shopping with us. Your credit card has been charged and your transaction is successful. We will be shipping your order to you soon.";
	
				//Here you need to put in the routines for a successful 
				//transaction such as sending an email to customer,
				//setting database status, informing logistics etc etc
			}
			else if($Checksum=="true" && $AuthDesc=="B")
			{
				
			$tData=$_SESSION['carttriggerdata'];
                        //$this->objTrigger->triggerFire(64,$tData);
			if($request['Notes']!='')
			$tData['billing_cust_notes']='<b>Special instructions :</b> '.$request['Notes'];
			else
			$tData['billing_cust_notes']='';				
			$this->objTrigger->triggerFire(63,$tData);
			$this->objTrigger->triggerFire(77,$tData);
			
			unset($_SESSION['carttriggerdata']);
			unset($_SESSION['Cart_Coupon']['coupons']['order'][$typePayment['1']]);
			unset($_SESSION['Cart_Coupon']['coupons']['shipping'][$typePayment['1']]);
			unset($_SESSION['Cart_GiftCertificate']['giftcoupon'][$typePayment['1']]);
			$purchase_data = $_SESSION['Api_Model_Review_PG']['item'][$typePayment['1']];
			 if(!empty($_SESSION['Api_Model_Review_PG']['item'][$typePayment['1']]))
					{
						foreach($_SESSION['Api_Model_Review_PG']['item'][$typePayment['1']] as $kry=>$val)
							{
					
										unset($_SESSION['Api_Model_Cart']['items'][$val->storeApiKey."_".$val->product_id."_".$val->variationcode]);
										unset($_SESSION['Api_Model_Review']['items'][$val->storeApiKey."_".$val->product_id."_".$val->variationcode]); 
									
							}

					}
		//unset($_SESSION['Cart_GiftCertificate'][$request['transapikey']]);
                $purchase_data = $_SESSION['Api_Model_Review_PG']['item'][$typePayment['1']];
		 unset($_SESSION['Api_Model_Review_PG']['item'][$typePayment['1']]);
				
				if(!empty($_SESSION['Api_Model_Cart']['items']))
				 $this->_redirect('/cart/review/er/1');
				else
				$this->_redirect('/cart/success');
				//echo "<br>Thank you for shopping with us.We will keep you posted regarding the status of your order through e-mail";
	
				//Here you need to put in the routines/e-mail for a  "Batch Processing" order
				//This is only if payment for this transaction has been made by an American Express Card
				//since American Express authorisation status is available only after 5-6 hours by mail from ccavenue and at the "View Pending Orders"
			}
			else if($Checksum=="true" && $AuthDesc=="N")
			{
			$tData=$_SESSION['carttriggerdata'];
                        //$this->objTrigger->triggerFire(64,$tData);	
			//$this->objTrigger->triggerFire(63,$tData);
			//$tData['billing_cust_notes']=$request['billing_cust_notes'];
			$this->objTrigger->triggerFire(80,$tData);
			$this->objTrigger->triggerFire(81,$tData);	
			unset($_SESSION['carttriggerdata']);
			unset($_SESSION['Api_Model_Review_Back']);
			//echo "update orders payment_status='2' where order_id=".$request['Order_Id'];
			//echo "update order_item set order_item_status='6',order_sub_status_id='21',buyer_substatus='47' where  order_id=".$request['Order_Id'];exit;
			$updateQuery=$db->query("update orders set payment_status='2' where order_id=".$Order_Id);
		$updateQuery=$db->query("update order_item set order_item_status='6',order_sub_status_id='21',buyer_substatus='47' where  order_id=".$Order_Id);
			$orderDetail=$this->_cartModel->getOrderDetail($typePayment['2']);
		//print_r($orderDetail);exit;
	
		$ac=array();
		if(!empty($orderDetail))
			{
				$total=0;
				$i=0;
				foreach($orderDetail as $keys=>$vals)
				{
				//echo "update product_variation set variant_value=(variant_value+".$vals['order_item_total'].") where variant_name='Stock' and product_id=".$vals['product_id']." and variation_code=".$vals['product_variation_code'];exit;	
					$db->query("update product_variation set variant_value=(variant_value+".$vals['order_item_total'].") where variant_name='Stock' and product_id=".$vals['product_id']." and variation_code=".$vals['product_variation_code']);
			}
			}
   	$gcdetail= explode("-",$orderDetail[0]['gcdetail']);
	$discountdetail=array();
	$totaltominus=0;
	foreach($gcdetail as $key=>$val)
	{
		
		if($key==0)
		continue;
		
		$explodeValue=explode(":",$val);
		if($key==1)
		{
			if($explodeValue[0]!=0)
			{
				$giftCertificateDetail= $this->_cartModel->getgiftDetailById($explodeValue[0]);
				$db->query("update gift_amount_remaining set gift_amount_remaining=(gift_amount_remaining+".$explodeValue[1].") where gift_code='".$giftCertificateDetail['gift_code']."'" );
				//$discountdetail['giftcertificatevalue']=$explodeValue[1];
				
				
				//$totaltominus+=$explodeValue[1];
				//$discountdetail['giftcertificatecouponcode']=$giftCertificateDetail['gift_code'];
			}
		}
		if($key==2)
		{
			/*if($explodeValue[0]!=0)
			{
				$discountdetail['ordervalue']=$explodeValue[1];
				$totaltominus+=$explodeValue[1];
				$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($explodeValue[0]);
				$discountdetail['ordershippingcode']=$giftCertificateDetail['coupon_code'];
			}*/
		}
		if($key==3)
		{
			/*if($explodeValue[0]!=0)
			{
				$discountdetail['shippingvalue']=$explodeValue[1];
				$totaltominus+=$explodeValue[1];
				$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($explodeValue[0]);
				$discountdetail['shippingcode']=$giftCertificateDetail['coupon_code'];
			}*/
		}
		
			/*if($explodeValue[0]!=0)
			{
				$discountdetail[$explodeValue[2]]['value']=$explodeValue[1];
				$totaltominus+=$explodeValue[1];
				$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($explodeValue[0]);
				$discountdetail[$explodeValue[2]]['code']=$giftCertificateDetail['coupon_code'];
			
		}*/
		
	}
	
				
				//echo "<br>Thank you for shopping with us.However,the transaction has been declined.";
	
				//Here you need to put in the routines for a failed
				//transaction such as sending an email to customer
				//setting database status etc etc   /**/
			 $this->_redirect('/cart/review/er/'.$typePayment[1]);
			}
			else
			{
			unset($_SESSION['Api_Model_Review_Back']);
					$orderDetail=$this->_cartModel->getOrderDetail($typePayment['2']);
		
	
		$ac=array();
		if(!empty($orderDetail))
			{
				$total=0;
				$i=0;
				foreach($orderDetail as $keys=>$vals)
				{
					
					$db->query("update product_variation set variant_value=(variant_value+".$vals['order_item_total'].") where variant_name='Stock' and product_id=".$vals['product_id']." and variation_code=".$vals['product_variation_code']);
			}
			}
   	$gcdetail= explode("-",$orderDetail[0]['gcdetail']);
	$discountdetail=array();
	$totaltominus=0;
	foreach($gcdetail as $key=>$val)
	{
		
		if($key==0)
		continue;
		
		$explodeValue=explode(":",$val);
		if($key==1)
		{
			if($explodeValue[0]!=0)
			{
				$giftCertificateDetail= $this->_cartModel->getgiftDetailById($explodeValue[0]);
				$db->query("update gift_amount_remaining set gift_amount_remaining=(gift_amount_remaining+".$explodeValue[1].") where gift_code='".$giftCertificateDetail['gift_code']."'" );
				//$discountdetail['giftcertificatevalue']=$explodeValue[1];
				
				
				//$totaltominus+=$explodeValue[1];
				//$discountdetail['giftcertificatecouponcode']=$giftCertificateDetail['gift_code'];
			}
		}
		if($key==2)
		{
			/*if($explodeValue[0]!=0)
			{
				$discountdetail['ordervalue']=$explodeValue[1];
				$totaltominus+=$explodeValue[1];
				$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($explodeValue[0]);
				$discountdetail['ordershippingcode']=$giftCertificateDetail['coupon_code'];
			}*/
		}
		if($key==3)
		{
			/*if($explodeValue[0]!=0)
			{
				$discountdetail['shippingvalue']=$explodeValue[1];
				$totaltominus+=$explodeValue[1];
				$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($explodeValue[0]);
				$discountdetail['shippingcode']=$giftCertificateDetail['coupon_code'];
			}*/
		}
		
			/*if($explodeValue[0]!=0)
			{
				$discountdetail[$explodeValue[2]]['value']=$explodeValue[1];
				$totaltominus+=$explodeValue[1];
				$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($explodeValue[0]);
				$discountdetail[$explodeValue[2]]['code']=$giftCertificateDetail['coupon_code'];
			
		}*/
		
	}
				 $this->_redirect('/cart/review/er/'.$typePayment[1]);
				
				//echo "<br>Thank you for shopping with us.However,the transaction has been declined.";
	
				//Here you need to put in the routines for a failed
				//transaction such as sending an email to customer
				//setting database status etc etc   /**/
				//echo "<br>Security Error. Illegal access detected";
	
				//Here you need to simply ignore this and dont need
				//to perform any operation in this condition
			}
			
		
		
		$generalobject=new General();
						//if($userName->userId==865)
		$generalobject->inserttofronttableproduct($productdetail->product_id);
		}
		
            
	
		$data=$sessionReview->item[$request['transapikey']];
		$sessionCoupons = new Zend_Session_Namespace('Cart_Coupon');
		$gcCoupon= new Zend_Session_Namespace('Cart_GiftCertificate');
		$dccoupons=$sessionCoupons->coupons;
		$gccoupons=$gcCoupon->giftcoupon;
		
		$sessionCoupons = new Zend_Session_Namespace('Cart_Coupon');
		$gcCoupon= new Zend_Session_Namespace('Cart_GiftCertificate');
		$dccoupons=$sessionCoupons->coupons;
		$gccoupons=$gcCoupon->giftcoupon;
		$dcTextMail=0;	
               
		
		 if(!empty($gccoupons))
		{
			foreach($gccoupons as $keygc=>$valgc)
			{
			//print_r($valgc);exit;
				if($keygc==$request['transapikey'])
					{
						$giftCertificateDetail= $this->_cartModel->getgiftDetailById($valgc['gcid']);
						//print_r($giftCertificateDetail);exit;
						if($giftCertificateDetail['gift_amount_remaining']!=$valgc['less'])
						{
							unset($_SESSION['Cart_GiftCertificate']['giftcoupon'][$request['transapikey']]);
							$this->_redirect('/cart/checkout/derror/1');	
						}
						else
						{
							$db = Zend_Db_Table::getDefaultAdapter();
							//echo "update gift_certificate_recipient set gift_amount_remaining=".($giftCertificateDetail['gift_amount_remaining']-$valgc['less'])." where id=".$giftCertificateDetail['rid'];exit;
							$db->query("update gift_certificate_recipient set gift_amount_remaining=".($giftCertificateDetail['gift_amount_remaining']-$valgc['less'])." where id=".$giftCertificateDetail['rid']);
						
						}
						$gcamountString.=$data[0]->store_api_key[0]['id']."-".$valgc['gcid'].":".$valgc['less'];
						//update balance in gift certificate table

						
					}
					
			}
		 }
		else
		{
			$gcamountString.=$data[0]->store_api_key[0]['id']."-0:0";
		}
		
		
			if(!empty($dccoupons['order'][$request['transapikey']][0]))
			{ 
				$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($dccoupons['order'][$request['transapikey']][0]['couponid']);
				
				$getCustomerRed=$this->_cartModel->customerUseCouponTotal($dccoupons['order'][$request['transapikey']][0]['couponid']);


			if($giftCertificateDetail['usage_user']==1 && $giftCertificateDetail['usage_user_per']<$getCustomerRed['total'] && $giftCertificateDetail['usage_user_per']!=0)
				{
				

							unset($_SESSION['Cart_Coupon']['coupons']['order'][$request['transapikey']]);
							$this->_redirect('/cart/checkout/derror/1');
						
				}	
				else if( $giftCertificateDetail['delete_status']==0)
				{

							unset($_SESSION['Cart_Coupon']['coupons']['order'][$request['transapikey']]);

							$this->_redirect('/cart/checkout/derror/1');
				}
				else
				{/**/
				
			$dcTextMail+=$dccoupons['order'][$request['transapikey']][0]['less'];
				$gcamountString.="-".$dccoupons['order'][$request['transapikey']][0]['couponid'].":".$dccoupons['order'][$request['transapikey']][0]['less'];
				$this->_cartModel->updatecouponByUser($dccoupons['order'][$request['transapikey']][0]['couponid']);
				}
				//update coupon reedemed by user
			}
			else
			{
				$gcamountString.="-0:0";	
			}
			

			
if(!empty($dccoupons['shipping'][$request['transapikey']][0]))
			{ 
				$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($dccoupons['shipping'][$request['transapikey']][0]['couponid']);
				$getCustomerRed=$this->_cartModel->customerUseCouponTotal($dccoupons['shipping'][$request['transapikey']][0]['couponid']);
			if($giftCertificateDetail['usage_user']==1 && $giftCertificateDetail['usage_user_per']<$getCustomerRed['total'])
				{	
					
							unset($_SESSION['Cart_Coupon']['coupons']['shipping'][$request['transapikey']]);
							$this->_redirect('/cart/checkout/derror/1');
					
				}	
				else if($giftCertificateDetail['delete_status']==0)
				{
							unset($_SESSION['Cart_Coupon']['coupons']['order'][$request['transapikey']]);
							$this->_redirect('/cart/checkout/derror/1');
				}
				else
				{ 
				//echo 'df gdfsgdfs';exit;
				$dcTextMail+=$dccoupons['shipping'][$request['transapikey']][0]['less'];
				$gcamountString.="-".$dccoupons['shipping'][$request['transapikey']][0]['couponid'].":".$dccoupons['shipping'][$request['transapikey']][0]['less'];
				$this->_cartModel->updatecouponByUser($dccoupons['shipping'][$request['transapikey']][0]['couponid']);
				//update coupon reedemed by user
				}			
			}
			else
			{
				$gcamountString.="-0:0";
			} 


			 foreach($sessionReview->item[$request['transapikey']] as $key=>$productdetail)
				{
				
					if(!empty($dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$productdetail->variationcode]))
					{  
	//echo $dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$productdetail->variationcode]['couponid'];exit;				
				 	$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$productdetail->variationcode]['couponid']);
			//print_r($giftCertificateDetail);exit;
				$getCustomerRed=$this->_cartModel->customerUseCouponTotal($dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$productdetail->variationcode]['couponid']);
			//print_r($getCustomerRed);exit;
			if($giftCertificateDetail['usage_user']==1 && $giftCertificateDetail['usage_user_per']<$getCustomerRed['total'])
				{	
					
		
							unset($_SESSION['Cart_Coupon']['coupons']['product'][$request['transapikey']]);
							$this->_redirect('/cart/checkout/derror/1');
						
				}	
				else if($giftCertificateDetail['delete_status']==0)
				{
					
							unset($_SESSION['Cart_Coupon']['coupons']['product'][$request['transapikey']]);
							$this->_redirect('/cart/checkout/derror/1');
				}
				else
				{ 
				//echo $dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$productdetail->variationcode]['couponid'];exit;
				
				$this->_cartModel->updatecouponByUser($dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$productdetail->variationcode]['couponid']);
				//update coupon reedemed by user
				}		
				
				}

				}	
				
		
		$billingdetail=json_decode(stripslashes($_REQUEST['billingdetail']));
		$shippingdetail=json_decode(stripslashes($_REQUEST['shippingdetail']));
		$productDetail=json_decode(stripslashes($_REQUEST['cart']));
		echo '<pre>';
		print_r($productDetail);
exit;
		
		$total_pamt=100;//total to set
		if($_REQUEST['payonline']=='on')
		{
			$paymentid=1;
		}

		$billingaddress_detail['fullname']=$billingdetail->fullname;
		$billingaddress_detail['address']=$billingdetail->address;
		$billingaddress_detail['zipcode']=$billingdetail->zipcode;
		$billingaddress_detail['city']=$billingdetail->city_name;
		$billingaddress_detail['state']=$billingdetail->state_name;
		$billingaddress_detail['phone']=$billingdetail->phone;
			

		$shippingaddress_detail['fullname']=$shippingdetail->fullname;
		$shippingaddress_detail['address']=$shippingdetail->address;
		$shippingaddress_detail['zipcode']=$shippingdetail->zipcode;
		$shippingaddress_detail['city']=$shippingdetail->city_name;
		$shippingaddress_detail['state']=$shippingdetail->state_name;
		$shippingaddress_detail['phone']=$shippingdetail->phone;

		
	
                
			
                $fullname = $billingaddress_detail['fullname'];
                $address = $billingaddress_detail['address'];
                $zipcode = $billingaddress_detail['zipcode'];
                $city = $billingaddress_detail['cityname'];
                $state = $billingaddress_detail['state_name'];
                $phone = $billingaddress_detail['phone'];
               	$email='saroj.dkl4u@gmail.com';//fetch the email
		$storeapikey='23421342314lhjbhjbhj';//store api key
		$paymentmode_type='payonline';
		$storenameMail='dhoomgifts';
		$storeownername='saroj';
		$mallurl='dhoomgifts.com';
               //ALTER TABLE `order_addresses` CHANGE `address_book_id` `address_book_id` INT( 11 ) NOT NULL DEFAULT '0'
		//ALTER TABLE `order_addresses` ADD `email` VARCHAR( 100 ) NOT NULL DEFAULT '0',ADD `storeapikey` VARCHAR( 100 ) NOT NULL DEFAULT '0'
		
		$addressid=$this->_cartModel->checkOrderAddressStorewise($fullname, $address, $zipcode, $city, $state, $phone,$email,$storeapikey);
		$a_bliiing=$address_id;
		$a_fullname=$fullname;
		$a_address=$address;
		$a_zipcode=$zipcode;
		$a_city=$city;
		$a_stae=$state;
		$a_phone=$phone;
		$a_off=$officeaddress;
		
		
		$shippingaddressid=$this->_cartModel->checkOrderAddressStorewise($shippingdetail->fullname, $shippingdetail->address, $shippingdetail->zipcode, $shippingdetail->city_name, $shippingdetail->state_name, $phone,$email,$storeapikey);
		

		if($shippingaddressid)
		{
                	$ship_id = $addressid;
        	}
		else
		{
			
			
			$shippingaddress_detail['email']=$email;
			$shippingaddress_detail['storeapikey']=$storeapikey;
	
			$ship_id = $this->_cartModel->insertOrderAddressesStorewise($shippingaddress_detail);

			

		}

		if($addressid)
		{
                	$orderaddresses_id = $addressid;
        	}
		else
		{
			
			
			$billingaddress_detail['email']=$email;
			$billingaddress_detail['storeapikey']=$storeapikey;

			$orderaddresses_id = $this->_cartModel->insertOrderAddressesStorewise($billingaddress_detail);
			

		}
		
		
		
		//ALTER TABLE `orders` CHANGE `customer_id` `customer_id` INT( 11 ) NOT NULL DEFAULT '0'
		//ALTER TABLE `orders` ADD `email` VARCHAR( 100 ) NOT NULL DEFAULT '0',ADD `storeapikey` VARCHAR( 100 ) NOT NULL DEFAULT '0'
		
		$orders_id = $this->_cartModel->insertOrderStorewise($orderaddresses_id, $paymentmode_type,$email,$storeapikey);
		
                if($orders_id)
                {
                    $this->view->order_no = $orders_id;
                }
		

		$dateMail=date("m/d/Y");
		$shippingpriceMail=0;
                foreach($productDetail as $key=>$productdetail)
		{
	//echo "<pre>";
	
	
	
			$productnameMail.=$productdetail->product_name.(($productdetail->variations[$productdetail->product_id]['allvariation'])?" - ".$productdetail->variations[$productdetail->product_id]['allvariation']:'').' x '.$productdetail->product_qty.' = Rs. '.number_format(($productdetail->product_mrp*$productdetail->product_qty)+($productdetail->shipcost*$productdetail->product_qty),2).'<br />';
			$shippingpriceMail=$shippingpriceMail+($productdetail->shipcost*$productdetail->product_qty);
			$storenameMail=$storenameMail;
			$storeApiKeyMail=$storeapikey;
			
			
			
			$streownernameMail=$storeownername;
			$mallNameMail=$storeownername;
			$mallurlMail=$mallurl;
			$mallapikeyMail=$storeapikey;
			$user_email_addressMail=$email;
		
                        $product_variation = $productdetail->variations[$productdetail->product_id]['allvariation'];
			$product_condition = $productdetail->variations[$productdetail->product_id]['condition'];
			$product_name = $productdetail->product_name;
			$product_mrp = $productdetail->product_mrp;
			$product_shipping_price = ($productdetail->shipcost)*($productdetail->product_qty);
			$product_variation_code = $productdetail->variation_id;
			$order_item_total = $productdetail->product_qty;
			$product_id = $productdetail->product_id;
			//echo $productdetail->product_qty;exit; 	
			$customizationdata='';
			$formid='0';
			//unset($productdetail->customizeddata);

		
							
							if(trim($productdetail->customizeddata)!='')
							{
								$customizationdata.='Customization details of '.$productdetail->product_name.(($productdetail->variations[$productdetail->product_id]['allvariation'])?" - ".$productdetail->variations[$productdetail->product_id]['allvariation']:'').'<br />';
								$customizationdata.='<table cellpadding="0" cellspaceing="0" border="0">';
								$explodecustomization=explode("-~^-",$productdetail->customizeddata);
								$formid=$explodecustomization[1];
								$exdata=explode("~~~~",$explodecustomization[0]);
								
									if(!empty($exdata))
									{
										foreach($exdata as $k=>$v)
										{
											$explodeval=explode("^^^^",$v);
											
											if(trim($explodeval[1])!='')
											{
												 $key=$explodeval[1];
											}
											else
											{
												$key='--';
											}
											if($key==='')
											$key='';				
					
										if(trim($explodeval[0])!='' && trim($explodeval[0])!='')
											{
												$index=$explodeval[0];
											}
											else
											{
												$index='';
											}
											
											$customizationdata.='<tr><td><b>'.$index.'</b></td><td> : '.stripslashes(stripslashes($key)).'</td></tr>';
										}
										$customizationdata.='</table><br />';
									}
							}
	
	
				mail('saroj.dkl4u@gmail.com', 'My Subject', $customizationdata);				
				
                        $productid = $this->_cartModel->insertOrderProductDetailStorewise($product_id=10660, $product_variation, $product_condition, $product_name, $product_mrp, $product_shipping_price, $product_variation_code,$productdetail->product_qty,$productdetail->customizeddata,$formid);
		        $ordershipping = $ship_id;
							
                       //ALTER TABLE `order_item` CHANGE `order_item_owner` `order_item_owner` INT( 11 ) NOT NULL DEFAULT '0'
				
                          $order_item = $this->_cartModel->insertOrderItemStorewise($orders_id, $ordershipping,  $order_item_total, $productid);
			
				

					
			$this->_cartModel->updatepersiableStorewise($productdetail->product_id, $order_item);

						
						$generalobject=new General();
					
							//$generalobject->inserttofronttableproduct($productdetail['product_id']);
						

			//$orderItemMail.='OR-'.$productdetail->store_api_key[0]['mallid'].'-'.$orders_id.'-'.$order_item.' : '.$productdetail->product_name.(($productdetail->variations[$productdetail->product_id]['allvariation'])?" - ".$productdetail->variations[$productdetail->product_id]['allvariation']:'').' x '.$productdetail->product_qty.' = Rs. '.number_format(($productdetail->product_mrp*$productdetail->product_qty)+($productdetail->shipcost*$productdetail->product_qty),2).'<br />';
			
			/*if(!empty($dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$product_variation_code]))
			{  
			 $dcTextMail+=$dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$product_variation_code]['less'];	
				 $gcamountString.="-".$dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$product_variation_code]['couponid'].":".$dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$product_variation_code]['less'].":".$order_item;
				//update coupon reedemed by user
					$this->_cartModel->updatecouponByUser($dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$product_variation_code]['couponid']);
			}
			else
			{
				$gcamountString.="-0:0:".$order_item;
			} */
			
		}
		
		
                $db->query("update orders set gc_amount='".$gcamountString."' where order_id=".$orders_id);
	
		 if($sessionReview->paymentmode_type=='DB' ||  $request['totalpay']<=0 ||  $sessionReview->paymentmode_type=='PCD'){

			    if(!empty($_SESSION['Api_Model_Review_PG']['item'][$request['transapikey']]))
					{
						foreach($_SESSION['Api_Model_Review_PG']['item'][$request['transapikey']] as $kry=>$val)
							{
							
										unset($_SESSION['Api_Model_Cart']['items'][$val->storeApiKey."_".$val->product_id."_".$val->variationcode]);
										unset($_SESSION['Api_Model_Review']['items'][$val->storeApiKey."_".$val->product_id."_".$val->variationcode]); 
									
							}
					}
			}

			//$sq=$db->query("update orders set transaction_amount=".$request['totalpay']." where order_id=".$orders_id);// to be check
		
		//echo 'sdfsfdsfdsff';exit;
 
	//|| $request['totalpay']<=0 

if($sessionReview->paymentmode_type!='payonline' ){
		$updateQuery=$db->query("update orders set payment_status='0' where  order_id=".$orders_id);
  		//redirect to merchant
}
				
			
	
		$this->view->headScript()->appendFile('/jscript/common/jquery-1.5.1.js','text/javascript');
		$this->view->useremail=	$userName->userDetails[0]['user_email_address'];
  		$address_id=$sessionReview->billingaddress;
		$total_pamt=$sessionReview->net_amt;
		$paymentmode_id=$sessionReview->paymentmode_id;
		$paymentmode_type=$sessionReview->paymentmode_type;
		$this->view->data=$data;
		$this->view->address_id=$address_id;
		$this->view->total_pamt=$total_pamt;
		$this->view->paymentmode_id=$paymentmode_id;
		$this->view->paymentmode_type=$paymentmode_type;
		$this->view->storeId=$storeidMail;
		$this->view->userIdebs=$ori->userId;
                
	        $this->view->billingdetail=$billingaddress_detail;
		$this->view->totalpay=$request['totalpay'];
		$this->view->oid=$orders_id;
		$this->view->ak=$request['transapikey'];
		
	
	}
	
	public function pgtransbnAction()
	{

		$db = Zend_Db_Table::getDefaultAdapter();
		$this->view->headTitle('Order is being processed - Goo2o.com checkout');
		$this->_helper->layout->disableLayout();
		$this->view->headLink()->appendStylesheet('/css/secure/checkout_common.css');
		$request = $this->_request->getParams();
		$userName =  new Zend_Session_Namespace('USER');
		$ori = new Zend_Session_Namespace('original_login');
		$sessionReview = new Zend_Session_Namespace('Api_Model_Review_PG_Buynow');
		//print_r($request);exit;
		/*echo "<pre>";
		print_r($_SESSION);
		print_r($_REQUEST);
		print_r($request);
		exit;*/
		$sessionReviewwelldone = new Zend_Session_Namespace('Api_Model_Review_Welldone_Buynow');
		if($sessionReview->item[$request['transapikey']][0]->store_api_key[0]['trade_activation']=='0' || $sessionReview->item[$request['transapikey']][0]->store_api_key[0]['trade_activation']==2){
		$data=$sessionReview->item[$request['transapikey']];
		unset($_SESSION['Cart_Coupon']['coupons']['product'][$request['transapikey']]);
		unset($_SESSION['Cart_Coupon']['coupons']['order'][$request['transapikey']]);
		unset($_SESSION['Cart_Coupon']['coupons']['shipping'][$request['transapikey']]);
		unset($_SESSION['Cart_GiftCertificate']['giftcoupon'][$request['transapikey']]);
		//unset($_SESSION['Cart_GiftCertificate'][$request['transapikey']]);
                $sessionReviewBack = new Zend_Session_Namespace('Api_Model_Review_Back_Buynow');
		$sessionReviewBack->item=$data;
                $sessionReviewwelldone->item[$request['transapikey']]=$data;
		if(!empty($_SESSION['Api_Model_Review_PG_Buynow']['item'][$request['transapikey']]))
					{
						foreach($_SESSION['Api_Model_Review_PG_Buynow']['item'][$request['transapikey']] as $kry=>$val)
							{
							
							unset($_SESSION['Api_Model_Cart_Buynow']['items'][$val->storeApiKey."_".$val->product_id."_".$val->variationcode]);
							unset($_SESSION['Api_Model_Review_Buynow']['items'][$val->storeApiKey."_".$val->product_id."_".$val->variationcode]); 
									
							}
					}
			unset($_SESSION['Api_Model_Review_PG_Buynow']['item'][$typePayment['1']]);
				
				if(!empty($_SESSION['Api_Model_Cart_Buynow']['items']))
				 $this->_redirect('/cart/reviewbn');
				else
				$this->_redirect(HTTP_SECURE_GOO2O.'/cart/successbn');
		}

		if($request['Merchant_Id']!='')
		{
			$Merchant_Param= $request['Merchant_Param'];
			$typePayment=explode("-",$Merchant_Param);
			$WorkingKey = "5lev0req5ugwcnm87u"; //put in the 32 bit working key in the quotes provided here
			$Merchant_Id= $request['Merchant_Id'];
			$Amount= $request['Amount'];
			$Order_Id= $typePayment[2];
			
			$Checksum= $request['Checksum'];
			$AuthDesc=$request['AuthDesc'];
			//echo $Merchant_Id."-".$Order_Id."-".$Amount."-".$AuthDesc."-".$Checksum."-".$WorkingKey;
			 $Checksum = verifyChecksum($Merchant_Id, $Order_Id , $Amount,$AuthDesc,$Checksum,$WorkingKey);
			/*echo $AuthDesc;
			echo "<pre>";
			print_r($request);
			exit;*/
			//print_r($request);
			//echo "update orders set response_data='".$request."',transaction_amount='".$request['Amount']."' where order_id=".$request['Order_Id'];exit;
			//echo "update orders set response_data='".serialize($request)."',transaction_amount='".$request['Amount']."',transaction_id='".$request['nb_order_no']."' where  order_id=".$request['Order_Id'];exit;
			$updateQuery=$db->query("update orders set response_data='".serialize($request)."',transaction_amount='".$request['Amount']."',transaction_id='".$request['nb_order_no']."',payment_status='0' where  order_id=".$Order_Id);
			if($Checksum=="true" && $AuthDesc=="Y")
			{
				
			$tData=$_SESSION['carttriggerdata'];
                        $this->objTrigger->triggerFire(64,$tData);	
			$this->objTrigger->triggerFire(65,$tData);
			
			unset($_SESSION['carttriggerdata']);		
			unset($_SESSION['Cart_Coupon']['coupons']['product'][$typePayment['1']]);
			unset($_SESSION['Cart_Coupon']['coupons']['order'][$typePayment['1']]);
			unset($_SESSION['Cart_Coupon']['coupons']['shipping'][$typePayment['1']]);
			unset($_SESSION['Cart_GiftCertificate']['giftcoupon'][$typePayment['1']]);
			$purchase_data = $_SESSION['Api_Model_Review_PG_Buynow']['item'][$typePayment['1']];
			 if(!empty($_SESSION['Api_Model_Review_PG_Buynow']['item'][$typePayment['1']]))
					{
						foreach($_SESSION['Api_Model_Review_PG_Buynow']['item'][$typePayment['1']] as $kry=>$val)
							{
					
										unset($_SESSION['Api_Model_Cart_Buynow']['items'][$val->storeApiKey."_".$val->product_id."_".$val->variationcode]);
										unset($_SESSION['Api_Model_Review_Buynow']['items'][$val->storeApiKey."_".$val->product_id."_".$val->variationcode]); 
									
							}

					}
		//unset($_SESSION['Cart_GiftCertificate'][$request['transapikey']]);
                $purchase_data = $_SESSION['Api_Model_Review_PG_Buynow']['item'][$typePayment['1']];
		 unset($_SESSION['Api_Model_Review_PG_Buynow'][$typePayment['1']]);
				
				if(!empty($_SESSION['Api_Model_Cart_Buynow']['items']))
				 $this->_redirect('/cart/reviewbn/er/1');
				else
				$this->_redirect(HTTP_SECURE_GOO2O.'/cart/successbn');
			
				//echo "<br>Thank you for shopping with us. Your credit card has been charged and your transaction is successful. We will be shipping your order to you soon.";
	
				//Here you need to put in the routines for a successful 
				//transaction such as sending an email to customer,
				//setting database status, informing logistics etc etc
			}
			else if($Checksum=="true" && $AuthDesc=="B")
			{
				
			$tData=$_SESSION['carttriggerdata'];
                        //$this->objTrigger->triggerFire(64,$tData);	
			$this->objTrigger->triggerFire(63,$tData);
			$this->objTrigger->triggerFire(77,$tData);
			
			unset($_SESSION['carttriggerdata']);
			unset($_SESSION['Cart_Coupon']['coupons']['order'][$typePayment['1']]);
			unset($_SESSION['Cart_Coupon']['coupons']['shipping'][$typePayment['1']]);
			unset($_SESSION['Cart_GiftCertificate']['giftcoupon'][$typePayment['1']]);
			$purchase_data = $_SESSION['Api_Model_Review_PG_Buynow']['item'][$typePayment['1']];
			 if(!empty($_SESSION['Api_Model_Review_PG_Buynow']['item'][$typePayment['1']]))
					{
						foreach($_SESSION['Api_Model_Review_PG_Buynow']['item'][$typePayment['1']] as $kry=>$val)
							{
					
										unset($_SESSION['Api_Model_Cart_Buynow']['items'][$val->storeApiKey."_".$val->product_id."_".$val->variationcode]);
										unset($_SESSION['Api_Model_Review_Buynow']['items'][$val->storeApiKey."_".$val->product_id."_".$val->variationcode]); 
									
							}

					}
		//unset($_SESSION['Cart_GiftCertificate'][$request['transapikey']]);
                $purchase_data = $_SESSION['Api_Model_Review_PG_Buynow']['item'][$typePayment['1']];
		 unset($_SESSION['Api_Model_Review_PG_Buynow']['item'][$typePayment['1']]);
				
				if(!empty($_SESSION['Api_Model_Cart_Buynow']['items']))
				 $this->_redirect('/cart/reviewbn/er/1');
				else
				$this->_redirect('/cart/successbn');
				//echo "<br>Thank you for shopping with us.We will keep you posted regarding the status of your order through e-mail";
	
				//Here you need to put in the routines/e-mail for a  "Batch Processing" order
				//This is only if payment for this transaction has been made by an American Express Card
				//since American Express authorisation status is available only after 5-6 hours by mail from ccavenue and at the "View Pending Orders"
			}
			else if($Checksum=="true" && $AuthDesc=="N")
			{
			$tData=$_SESSION['carttriggerdata'];
                        //$this->objTrigger->triggerFire(64,$tData);	
			//$this->objTrigger->triggerFire(63,$tData);
			$this->objTrigger->triggerFire(80,$tData);
			$this->objTrigger->triggerFire(81,$tData);	
			unset($_SESSION['carttriggerdata']);
			unset($_SESSION['Api_Model_Review_Back_Buynow']);
			//echo "update orders payment_status='2' where order_id=".$request['Order_Id'];
			//echo "update order_item set order_item_status='6',order_sub_status_id='21',buyer_substatus='47' where  order_id=".$request['Order_Id'];exit;
			$updateQuery=$db->query("update orders set payment_status='2' where order_id=".$Order_Id);
		$updateQuery=$db->query("update order_item set order_item_status='6',order_sub_status_id='21',buyer_substatus='47' where  order_id=".$Order_Id);
			$orderDetail=$this->_cartModel->getOrderDetail($typePayment['2']);
		//print_r($orderDetail);exit;
	
		$ac=array();
		if(!empty($orderDetail))
			{
				$total=0;
				$i=0;
				foreach($orderDetail as $keys=>$vals)
				{
				//echo "update product_variation set variant_value=(variant_value+".$vals['order_item_total'].") where variant_name='Stock' and product_id=".$vals['product_id']." and variation_code=".$vals['product_variation_code'];exit;	
					$db->query("update product_variation set variant_value=(variant_value+".$vals['order_item_total'].") where variant_name='Stock' and product_id=".$vals['product_id']." and variation_code=".$vals['product_variation_code']);
			}
			}
   	$gcdetail= explode("-",$orderDetail[0]['gcdetail']);
	$discountdetail=array();
	$totaltominus=0;
	foreach($gcdetail as $key=>$val)
	{
		
		if($key==0)
		continue;
		
		$explodeValue=explode(":",$val);
		if($key==1)
		{
			if($explodeValue[0]!=0)
			{
				$giftCertificateDetail= $this->_cartModel->getgiftDetailById($explodeValue[0]);
				$db->query("update gift_amount_remaining set gift_amount_remaining=(gift_amount_remaining+".$explodeValue[1].") where gift_code='".$giftCertificateDetail['gift_code']."'" );
				//$discountdetail['giftcertificatevalue']=$explodeValue[1];
				
				
				//$totaltominus+=$explodeValue[1];
				//$discountdetail['giftcertificatecouponcode']=$giftCertificateDetail['gift_code'];
			}
		}
		if($key==2)
		{
			/*if($explodeValue[0]!=0)
			{
				$discountdetail['ordervalue']=$explodeValue[1];
				$totaltominus+=$explodeValue[1];
				$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($explodeValue[0]);
				$discountdetail['ordershippingcode']=$giftCertificateDetail['coupon_code'];
			}*/
		}
		if($key==3)
		{
			/*if($explodeValue[0]!=0)
			{
				$discountdetail['shippingvalue']=$explodeValue[1];
				$totaltominus+=$explodeValue[1];
				$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($explodeValue[0]);
				$discountdetail['shippingcode']=$giftCertificateDetail['coupon_code'];
			}*/
		}
		
			/*if($explodeValue[0]!=0)
			{
				$discountdetail[$explodeValue[2]]['value']=$explodeValue[1];
				$totaltominus+=$explodeValue[1];
				$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($explodeValue[0]);
				$discountdetail[$explodeValue[2]]['code']=$giftCertificateDetail['coupon_code'];
			
		}*/
		
	}
	
				
				//echo "<br>Thank you for shopping with us.However,the transaction has been declined.";
	
				//Here you need to put in the routines for a failed
				//transaction such as sending an email to customer
				//setting database status etc etc   /**/
			 $this->_redirect('/cart/reviewbn/er/'.$typePayment[1]);
			}
			else
			{
			unset($_SESSION['Api_Model_Review_Back_Buynow']);
					$orderDetail=$this->_cartModel->getOrderDetail($typePayment['2']);
		
	
		$ac=array();
		if(!empty($orderDetail))
			{
				$total=0;
				$i=0;
				foreach($orderDetail as $keys=>$vals)
				{
					
					$db->query("update product_variation set variant_value=(variant_value+".$vals['order_item_total'].") where variant_name='Stock' and product_id=".$vals['product_id']." and variation_code=".$vals['product_variation_code']);
			}
			}
   	$gcdetail= explode("-",$orderDetail[0]['gcdetail']);
	$discountdetail=array();
	$totaltominus=0;
	foreach($gcdetail as $key=>$val)
	{
		
		if($key==0)
		continue;
		
		$explodeValue=explode(":",$val);
		if($key==1)
		{
			if($explodeValue[0]!=0)
			{
				$giftCertificateDetail= $this->_cartModel->getgiftDetailById($explodeValue[0]);
				$db->query("update gift_amount_remaining set gift_amount_remaining=(gift_amount_remaining+".$explodeValue[1].") where gift_code='".$giftCertificateDetail['gift_code']."'" );
				//$discountdetail['giftcertificatevalue']=$explodeValue[1];
				
				
				//$totaltominus+=$explodeValue[1];
				//$discountdetail['giftcertificatecouponcode']=$giftCertificateDetail['gift_code'];
			}
		}
		if($key==2)
		{
			/*if($explodeValue[0]!=0)
			{
				$discountdetail['ordervalue']=$explodeValue[1];
				$totaltominus+=$explodeValue[1];
				$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($explodeValue[0]);
				$discountdetail['ordershippingcode']=$giftCertificateDetail['coupon_code'];
			}*/
		}
		if($key==3)
		{
			/*if($explodeValue[0]!=0)
			{
				$discountdetail['shippingvalue']=$explodeValue[1];
				$totaltominus+=$explodeValue[1];
				$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($explodeValue[0]);
				$discountdetail['shippingcode']=$giftCertificateDetail['coupon_code'];
			}*/
		}
		
			/*if($explodeValue[0]!=0)
			{
				$discountdetail[$explodeValue[2]]['value']=$explodeValue[1];
				$totaltominus+=$explodeValue[1];
				$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($explodeValue[0]);
				$discountdetail[$explodeValue[2]]['code']=$giftCertificateDetail['coupon_code'];
			
		}*/
		
	}
				 $this->_redirect('/cart/reviewbn/er/'.$typePayment[1]);
				
				//echo "<br>Thank you for shopping with us.However,the transaction has been declined.";
	
				//Here you need to put in the routines for a failed
				//transaction such as sending an email to customer
				//setting database status etc etc   /**/
				//echo "<br>Security Error. Illegal access detected";
	
				//Here you need to simply ignore this and dont need
				//to perform any operation in this condition
			}
			
		
		
		$generalobject=new General();
						//if($userName->userId==865)
		$generalobject->inserttofronttableproduct($productdetail->product_id);
		}
		//echo "<pre>";
		//print_r($_SESSION);
		//print_r($sessionReview->paymentmode_type);exit;
              
        if(count($sessionReview->item)<=0)
		{
		//$this->_redirect(HTTP_SERVER.'/cart/#list');
		}
		if($ori->userId=='')
		{
			$this->_redirect(HTTPS_SECURE.'/secure/login?tab=1');
		}
		/*if(count($sessionReview)<=0)
		{
				$this->_redirect(HTTP_SERVER.'/cart/#list');
		}*/
		//echo "<pre>";
		//echo $request['transapikey'];
		//print_r($_SESSION);
		//print_r($_SESSION['Api_Model_Cart']);
//exit;
	
		$data=$sessionReview->item[$request['transapikey']];
		$sessionCoupons = new Zend_Session_Namespace('Cart_Coupon');
		$gcCoupon= new Zend_Session_Namespace('Cart_GiftCertificate');
		$dccoupons=$sessionCoupons->coupons;
		$gccoupons=$gcCoupon->giftcoupon;
		/*echo "<pre>";
		print_r($data);
		print_r($dccoupons);
		print_r($gccoupons);*/
		$sessionCoupons = new Zend_Session_Namespace('Cart_Coupon');
		$gcCoupon= new Zend_Session_Namespace('Cart_GiftCertificate');
		$dccoupons=$sessionCoupons->coupons;
		$gccoupons=$gcCoupon->giftcoupon;
		$dcTextMail=0;	
		
		 if(!empty($gccoupons))
		{
			foreach($gccoupons as $keygc=>$valgc)
			{
			//print_r($valgc);exit;
				if($keygc==$request['transapikey'])
					{
						$giftCertificateDetail= $this->_cartModel->getgiftDetailById($valgc['gcid']);
						//print_r($giftCertificateDetail);exit;
						if($giftCertificateDetail['gift_amount_remaining']!=$valgc['less'])
						{
							unset($_SESSION['Cart_GiftCertificate']['giftcoupon'][$request['transapikey']]);
							$this->_redirect('/cart/checkoutbn/derror/1');	
						}
						else
						{
							$db = Zend_Db_Table::getDefaultAdapter();
							//echo "update gift_certificate_recipient set gift_amount_remaining=".($giftCertificateDetail['gift_amount_remaining']-$valgc['less'])." where id=".$giftCertificateDetail['rid'];exit;
							$db->query("update gift_certificate_recipient set gift_amount_remaining=".($giftCertificateDetail['gift_amount_remaining']-$valgc['less'])." where id=".$giftCertificateDetail['rid']);
						
						}
						$gcamountString.=$data[0]->store_api_key[0]['id']."-".$valgc['gcid'].":".$valgc['less'];
						//update balance in gift certificate table

						
					}
					
			}
		 }
		else
		{
			$gcamountString.=$data[0]->store_api_key[0]['id']."-0:0";
		}
		
		
			if(!empty($dccoupons['order'][$request['transapikey']][0]))
			{ 
				$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($dccoupons['order'][$request['transapikey']][0]['couponid']);
				
				$getCustomerRed=$this->_cartModel->customerUseCouponTotal($dccoupons['order'][$request['transapikey']][0]['couponid']);


			if($giftCertificateDetail['usage_user']==1 && $giftCertificateDetail['usage_user_per']<$getCustomerRed['total'] && $giftCertificateDetail['usage_user_per']!=0)
				{
				

							unset($_SESSION['Cart_Coupon']['coupons']['order'][$request['transapikey']]);
							$this->_redirect('/cart/checkoutbn/derror/1');
						
				}	
				else if( $giftCertificateDetail['delete_status']==0)
				{

							unset($_SESSION['Cart_Coupon']['coupons']['order'][$request['transapikey']]);

							$this->_redirect('/cart/checkoutbn/derror/1');
				}
				else
				{/**/
				
			$dcTextMail+=$dccoupons['order'][$request['transapikey']][0]['less'];
				$gcamountString.="-".$dccoupons['order'][$request['transapikey']][0]['couponid'].":".$dccoupons['order'][$request['transapikey']][0]['less'];
				$this->_cartModel->updatecouponByUser($dccoupons['order'][$request['transapikey']][0]['couponid']);
				}
				//update coupon reedemed by user
			}
			else
			{
				$gcamountString.="-0:0";	
			}
			

			
if(!empty($dccoupons['shipping'][$request['transapikey']][0]))
			{ 
				$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($dccoupons['shipping'][$request['transapikey']][0]['couponid']);
				$getCustomerRed=$this->_cartModel->customerUseCouponTotal($dccoupons['shipping'][$request['transapikey']][0]['couponid']);
			if($giftCertificateDetail['usage_user']==1 && $giftCertificateDetail['usage_user_per']<$getCustomerRed['total'])
				{	
					
							unset($_SESSION['Cart_Coupon']['coupons']['shipping'][$request['transapikey']]);
							$this->_redirect('/cart/checkoutbn/derror/1');
					
				}	
				else if($giftCertificateDetail['delete_status']==0)
				{
							unset($_SESSION['Cart_Coupon']['coupons']['order'][$request['transapikey']]);
							$this->_redirect('/cart/checkoutbn/derror/1');
				}
				else
				{ 
				//echo 'df gdfsgdfs';exit;
				$dcTextMail+=$dccoupons['shipping'][$request['transapikey']][0]['less'];
				$gcamountString.="-".$dccoupons['shipping'][$request['transapikey']][0]['couponid'].":".$dccoupons['shipping'][$request['transapikey']][0]['less'];
				$this->_cartModel->updatecouponByUser($dccoupons['shipping'][$request['transapikey']][0]['couponid']);
				//update coupon reedemed by user
				}			
			}
			else
			{
				$gcamountString.="-0:0";
			} 

			 foreach($sessionReview->item[$request['transapikey']] as $key=>$productdetail)
				{
				
					if(!empty($dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$productdetail->variationcode]))
					{  
	//echo $dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$productdetail->variationcode]['couponid'];exit;				
				 	$giftCertificateDetail=  $this->_cartModel->getDoiscountCouponDetailById($dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$productdetail->variationcode]['couponid']);
			//print_r($giftCertificateDetail);exit;
				$getCustomerRed=$this->_cartModel->customerUseCouponTotal($dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$productdetail->variationcode]['couponid']);
			//print_r($getCustomerRed);exit;
			if($giftCertificateDetail['usage_user']==1 && $giftCertificateDetail['usage_user_per']<$getCustomerRed['total'])
				{	
					
		
							unset($_SESSION['Cart_Coupon']['coupons']['product'][$request['transapikey']]);
							$this->_redirect('/cart/checkoutbn/derror/1');
						
				}	
				else if($giftCertificateDetail['delete_status']==0)
				{
					
							unset($_SESSION['Cart_Coupon']['coupons']['product'][$request['transapikey']]);
							$this->_redirect('/cart/checkoutbn/derror/1');
				}
				else
				{ 
				//echo $dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$productdetail->variationcode]['couponid'];exit;
				
				$this->_cartModel->updatecouponByUser($dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$productdetail->variationcode]['couponid']);
				//update coupon reedemed by user
				}		
				
				}

				}	
				
		
		$address_id=$sessionReview->billingaddress;
		$total_pamt=$sessionReview->net_amt;
		$paymentmode_id=$sessionReview->paymentmode_id;
		$paymentmode_type=$sessionReview->paymentmode_type;
		$this->view->data=$data;
		$this->view->address_id=$address_id;
		$this->view->total_pamt=$total_pamt;
		$this->view->paymentmode_id=$paymentmode_id;
		$this->view->paymentmode_type=$paymentmode_type;
                $billingaddress_detail = $this->_cartModel->getDetailAddress($address_id);
			
                $fullname = $billingaddress_detail[0]['fullname'];
                $address = $billingaddress_detail[0]['address'];
                $zipcode = $billingaddress_detail[0]['zipcode'];
                $city = $billingaddress_detail[0]['cityname'];
                $state = $billingaddress_detail[0]['state_name'];
                $phone = $billingaddress_detail[0]['phone'];
                $officeaddress = $billingaddress_detail[0]['officeaddress'];
                // code to be place in payment module
			
	
		$addressid=$this->_cartModel->checkOrderAddress($address_id, $fullname, $address, $zipcode, $city, $state, $phone, $officeaddress,$ori->userId);
		
		if($addressid)
		{
                	$orderaddresses_id = $this->_cartModel->getOrderAddressesId($addressid);
        }
		else
		{
			$billingaddress_detail = $this->_cartModel->getDetailAddress($address_id);
			unset($billingaddress_detail[0]['city']);
			unset($billingaddress_detail[0]['state']);
			unset($billingaddress_detail[0]['deletedflag']);
			$city_name = $billingaddress_detail[0]['cityname'];
			$state_name = $billingaddress_detail[0]['state_name'];
			$customer_id = $billingaddress_detail[0]['customers_id'];
			$billingaddress_detail[0]['city']=$city_name;
			$billingaddress_detail[0]['state'] = $state_name;
			$billingaddress_detail[0]['customer_id'] = $customer_id;
			unset($billingaddress_detail[0]['cityname']);
			unset($billingaddress_detail[0]['state_name']);
			unset($billingaddress_detail[0]['customers_id']);

			$orderaddresses_id = $this->_cartModel->insertOrderAddresses($billingaddress_detail[0]);

		}
		
		$ori = new Zend_Session_Namespace('original_login');
		$customer_id = $ori->userId;

		$paymentmode_type=$sessionReview->paymentmode_type;
		$orders_id = $this->_cartModel->insertOrder($customer_id, $orderaddresses_id, $paymentmode_type);
                if($orders_id)
                {
                    $this->view->order_no = $orders_id;
                }
		

		$dateMail=date("m/d/Y");
		$shippingpriceMail=0;
                foreach($sessionReview->item[$request['transapikey']] as $key=>$productdetail)
		{
	//echo "<pre>";
	
	//print_r($productdetail);
	//exit;
			$productnameMail.=$productdetail->product_name.(($productdetail->variations[$productdetail->product_id]['allvariation'])?" - ".$productdetail->variations[$productdetail->product_id]['allvariation']:'').' x '.$productdetail->product_qty.' = Rs. '.number_format(($productdetail->product_mrp*$productdetail->product_qty)+($productdetail->shipcost*$productdetail->product_qty),2).'<br />';
			$shippingpriceMail=shippingpriceMail+($productdetail->shipcost*$productdetail->product_qty);
			$storenameMail=$productdetail->store_api_key[0]['title'];
			$storeApiKeyMail=$productdetail->storeApiKey;
			$storeuseridMail=$productdetail->store_api_key[0]['user_id'];
			$storeidMail=$productdetail->store_api_key[0]['mallid'];
			$streownernameMail=$productdetail->store_api_key[0]['user_full_name'];
			$mallNameMail=$productdetail->store_api_key[0]['title'];
			$mallurlMail=$productdetail->store_api_key[0]['mallurl'];
			$mallapikeyMail=$productdetail->store_api_key[0]['apikey'];
			$user_email_addressMail=$productdetail->store_api_key[0]['user_email_address'];
		
                        $product_variation = $productdetail->variations[$productdetail->product_id]['allvariation'];
			$product_condition = $productdetail->variations[$productdetail->product_id]['condition'];
			$product_name = $productdetail->product_name;
			$product_mrp = $productdetail->variations[$productdetail->product_id]['srp'];
			$product_shipping_price = ($productdetail->shipcost)*($productdetail->product_qty);
			$product_variation_code = $productdetail->variationcode;
			$product_id = $productdetail->product_id;
			//echo $productdetail->product_qty;exit; 		
                        $productid = $this->_cartModel->insertOrderProductDetail($product_id, $product_variation, $product_condition, $product_name, $product_mrp, $product_shipping_price, $product_variation_code,$productdetail->product_qty);
						//echo 'd gdfg dfdfgdfg df';exit;
			$shippint_add = $productdetail->address_book_id;
			$order_item_total = $productdetail->product_qty;
			$order_item_owner = $productdetail->store_api_key[0]['user_id'];
                        $shippingaddress_detail = $this->_cartModel->getDetailAddress($shippint_add);
                        $fullnames = $shippingaddress_detail[0]['fullname'];
                        $addresss = $shippingaddress_detail[0]['address'];
                        $zipcodes = $shippingaddress_detail[0]['zipcode'];
                        $citys = $shippingaddress_detail[0]['cityname'];
                        $states = $shippingaddress_detail[0]['state_name'];
                        $phones = $shippingaddress_detail[0]['phone'];
                        $officeaddresss = $shippingaddress_detail[0]['officeaddress'];
			$ori = new Zend_Session_Namespace('original_login');			
                        $addressids=$this->_cartModel->checkOrderAddress($shippint_add, $fullnames, $addresss, $zipcodes, $citys, $states, $phones, $officeaddresss,$ori->userId);
						
                        if($addressids)
                        {
						
                            $ordershipping = $this->_cartModel->getOrderAddressesId($shippint_add);
							
                        }
                        else
                        {
							

                            $billingaddress_detail = $this->_cartModel->getDetailAddress($shippint_add);
                            unset($billingaddress_detail[0]['city']);
                            unset($billingaddress_detail[0]['state']);
                            $city_name = $billingaddress_detail[0]['cityname'];
                            $state_name = $billingaddress_detail[0]['state_name'];
                            $customer_id = $billingaddress_detail[0]['customers_id'];
                            $billingaddress_detail[0]['city']=$city_name;
                            $billingaddress_detail[0]['state'] = $state_name;
                            $billingaddress_detail[0]['customer_id'] = $customer_id;
                            unset($billingaddress_detail[0]['cityname']);
                            unset($billingaddress_detail[0]['state_name']);
                            unset($billingaddress_detail[0]['customers_id']);
							
                            $ordershipping = $this->_cartModel->insertOrderAddresses($billingaddress_detail[0]);
							
                        }
						
                        $order_item = $this->_cartModel->insertOrderItem($orders_id, $ordershipping, $order_item_owner, $order_item_total, $productid);
			
						$this->_cartModel->updatepersiable($productdetail->product_id, $order_item);
						$generalobject=new General();
						//if($userName->userId==865)
							$generalobject->inserttofronttableproduct($productdetail->product_id);

			$orderItemMail.='OR-'.$productdetail->store_api_key[0]['mallid'].'-'.$orders_id.'-'.$order_item.' : '.$productdetail->product_name.(($productdetail->variations[$productdetail->product_id]['allvariation'])?" - ".$productdetail->variations[$productdetail->product_id]['allvariation']:'').' x '.$productdetail->product_qty.' = Rs. '.number_format(($productdetail->product_mrp*$productdetail->product_qty)+($productdetail->shipcost*$productdetail->product_qty),2).'<br />';
			//echo $productdetail->product_id."_".$product_variation_code;
			//print_r($dccoupons['product'][$request['transapikey']]);exit;
			if(!empty($dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$product_variation_code]))
			{  
			 $dcTextMail+=$dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$product_variation_code]['less'];	
				 $gcamountString.="-".$dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$product_variation_code]['couponid'].":".$dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$product_variation_code]['less'].":".$order_item;
				//update coupon reedemed by user
					$this->_cartModel->updatecouponByUser($dccoupons['product'][$request['transapikey']][$productdetail->product_id."_".$product_variation_code]['couponid']);
			}
			else
			{
				$gcamountString.="-0:0:".$order_item;
			} 
		}
		//echo $gcamountString;exit;
		
                $db->query("update orders set gc_amount='".$gcamountString."' where order_id=".$orders_id);
		if($sessionReview->paymentmode_type=='DB' ||  $request['totalpay']<=0 ||  $sessionReview->paymentmode_type=='PCD'){
		unset($_SESSION['Cart_Coupon']['coupons']['product'][$request['transapikey']]);
		unset($_SESSION['Cart_Coupon']['coupons']['order'][$request['transapikey']]);
		unset($_SESSION['Cart_Coupon']['coupons']['shipping'][$request['transapikey']]);
		unset($_SESSION['Cart_GiftCertificate']['giftcoupon'][$request['transapikey']]);
		//unset($_SESSION['Cart_GiftCertificate'][$request['transapikey']]);
                $purchase_data = $_SESSION['Api_Model_Review_PG']['item'][$request['transapikey']];
		}
				
                foreach($purchase_data as $key=>$val)
                {
                    $store_apikey = $val->store_api_key[0][apikey];
                    $product_id = $val->product_id;
                    $variationcode = $val->variationcode;
			//if($sessionReview->paymentmode_type=='DB'){			
                    $delete_status = $this->_cartModel->removeBasket($store_apikey, $product_id, $variationcode, $customer_id);
			//}
					
                  //  $order_shipping_detail_id = $this->_cartModel->getProductShippingPolicyDetail($product_id, $order_item);
					//echo 'df gdfg dfgsd fsdsdf sdfsd';exit;
                }
				if( $orders_id )
					{
						$data['orderid'] = $orders_id ;
						$sessionReviewBack = new Zend_Session_Namespace('Api_Model_Review_Back');
						$sessionReviewBack->item=$data;

					}
				
				
				$sessionReviewwelldone->item[$request['transapikey']]=$data;
				
			 
			 if($sessionReview->paymentmode_type=='DB' ||  $request['totalpay']<=0 ||  $sessionReview->paymentmode_type=='PCD'){
			    if(!empty($_SESSION['Api_Model_Review_PG_Buynow']['item'][$request['transapikey']]))
					{
						foreach($_SESSION['Api_Model_Review_PG_Buynow']['item'][$request['transapikey']] as $kry=>$val)
							{
							
										unset($_SESSION['Api_Model_Cart_Buynow']['items'][$val->storeApiKey."_".$val->product_id."_".$val->variationcode]);
										unset($_SESSION['Api_Model_Review_Buynow']['items'][$val->storeApiKey."_".$val->product_id."_".$val->variationcode]); 
									
							}
					}
			}
			 $sessionDiscountAmount = new Zend_Session_Namespace('CartDIscountAmount');
			 $sessionDiscountAmount->total= $sessionDiscountAmount->total+$request['discountvalue'];
$billingaddress_detail = $this->_cartModel->getDetailAddress($address_id);
			$totalpayMail==$request['totalpay'];
foreach($billingaddress_detail as $key=>$val)
							{
								$billingAddressMail=ucwords(strtolower($val['fullname'])).", <br /> ".stripslashes($val['address']).", <br />".$val['cityname'].",  <br />".$val['state_name'].", <br /> Phone: ".$val['phone'];
							}
$ori = new Zend_Session_Namespace('original_login');	
 $locationsStore=$this->_cartModel->getStoreLocationsByApikey($storeuseridMail);
  $storephone=$this->_cartModel->getStorePhoneById($mallapikeyMail);
if($dcTextMail>0)
{
		$discountmessage='The total amount of discount coupon used for this order is Rs '.number_format($dcTextMail,2);
}
else
{
	$discountmessage='';
}
$tData = array('to_id'=>$ori->userId,
                         'from_id'=>$storeuseridMail,
			'from_name'=>$mallNameMail,
			'from_mail'=>$user_email_addressMail,
			'to_id'=>$ori->userId,
			'to_mail'=>$userName->userDetails[0]['user_email_address'],
			'to_name'=>$userName->userDetails[0]['user_full_name'],
			'storeapikey'=>	$storeApiKeyMail,
			 'order_date'=>$dateMail,
			 'store_name'=>$storenameMail,
			 'customer_name'=>$_SESSION['USER']['userDetails'][0]['user_full_name'],
			'discount_coupon'=>$discountmessage,
			'gift_certificate'=>'',	
			'product_name'=>$orderItemMail,
			'total_amount'=>number_format($request['totalpay'],2),
			'total_shipping'=>number_format($shippingpriceMail,2),
			'billing_address'=>$billingAddressMail,
			'link'=>'goo2o.com/cart/acknowledgement/oid/'.$orders_id,
			'store_owner_name'=>$streownernameMail,
			'order_item_detail'=>$orderItemMail,
			'order_id'=>'OR-'.$storeidMail.'-'.$orders_id,
			'store_url'=>$mallurlMail,
			'store_emails'=> $locationsStore,
			'store_phone'=>  $storephone
			);


			$_SESSION['carttriggerdata']= $tData;
//echo "<pre>";
//print_r($tData);exit;
if($sessionReview->paymentmode_type=='DB' || $request['totalpay']<=0 || $sessionReview->paymentmode_type=='PCD'){
		/*echo "<pre>";
		print_r($_SESSION);
		exit;*/
			//print_r($tData);exit;
			if($ori->userId==865)
			{
				//echo "<pre>";
				//print_r($_SESSION['Api_Model_Cart']['items']);
				//exit;
			}
			$updateQuery=$db->query("update orders set payment_status='0' where  order_id=".$orders_id);
		
                                     $this->objTrigger->triggerFire(64,$tData);	
				     $this->objTrigger->triggerFire(65,$tData);	
			    unset($_SESSION['Api_Model_Review_PG_Buynow'][$request['transapikey']]);
				

				if(!empty($_SESSION['Api_Model_Cart_Buynow']['items']))
				 $this->_redirect('/cart/reviewbn/er/1');
				else
				$this->_redirect(HTTP_SECURE_GOO2O.'/cart/successbn');
}
				
				// unset($_SESSION['Api_Model_Cart']);
               // unset($_SESSION['Api_Model_Review']);
	
	
		$this->view->headScript()->appendFile('/jscript/common/jquery-1.5.1.js','text/javascript');
			$this->view->useremail=	$userName->userDetails[0]['user_email_address'];
  			$address_id=$sessionReview->billingaddress;
		$total_pamt=$sessionReview->net_amt;
		$paymentmode_id=$sessionReview->paymentmode_id;
		$paymentmode_type=$sessionReview->paymentmode_type;
		$this->view->data=$data;
		$this->view->address_id=$address_id;
		$this->view->total_pamt=$total_pamt;
		$this->view->paymentmode_id=$paymentmode_id;
		$this->view->paymentmode_type=$paymentmode_type;
                
	$this->view->billingdetail=$billingaddress_detail[0];
		$this->view->totalpay=$request['totalpay'];
		$this->view->oid=$orders_id;
		$this->view->ak=$request['transapikey'];
		
	
	}
	public function successAction()
	{	
	$this->_helper->layout->enableLayout();
	$this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
	$this->view->headLink()->appendStylesheet('/css/secure/checkout_common.css');
		$this->view->headLink()->appendStylesheet('/css/secure/success.css');
		$this->view->headTitle('Order placed - Goo2o.com checkout');


		$userName =  new Zend_Session_Namespace('USER');
		$ori = new Zend_Session_Namespace('original_login');
		$sessionReview = new Zend_Session_Namespace('Api_Model_Review');
		$sessionCart= new Zend_Session_Namespace('Api_Model_CART');
		//Zend_Layout::getMvcInstance()->setLayout('secure');
	//Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');

                //echo "<pre>";
                //print_r($sessionReview);exit;
			$sessionReviewBack = new Zend_Session_Namespace('Api_Model_Review_Back');
        if(count($sessionReviewBack->item)<=0)
		{
		$this->_redirect(HTTP_SERVER.'/cart/#list');
		}
		if($ori->userId=='')
		{
			$this->_redirect(HTTPS_SECURE.'/secure/login?tab=1');
		}
		$sessionReviewwelldone = new Zend_Session_Namespace('Api_Model_Review_Welldone');
		$this->view->welldonedata=$sessionReviewwelldone->item;
		if(!empty($sessionReviewwelldone->item))
			{
				$totalamount=0;
				foreach($sessionReviewwelldone->item as $key=>$val)
					{
					
						$totalamount+=($val[0]->product_mrp*$val[0]->product_qty)+($val[0]->shipcost*$val[0]->product_qty);
					}
			}
		$this->view->totalamount=$totalamount;
		$this->view->totalitem=count($sessionReviewwelldone->item);
			$sessionReviewBack = new Zend_Session_Namespace('Api_Model_Review_Back');
			$this->view->recentpaidOrder=$sessionReviewBack->item;
			$sessionDiscountAmount = new Zend_Session_Namespace('CartDIscountAmount');
			$this->view->minusDisCount= $sessionDiscountAmount->total;
			unset($sessionDiscountAmount->total);
			unset($sessionReviewBack->item);
			unset($sessionReview->items);
			unset($sessionCart->items);
				unset($sessionReview->item);
			unset($sessionCart->item);	
	/*	echo "<pre>";
		print_r($sessionReviewwelldone->item); 
		exit;*/
		
	}
	public function successbnAction()
	{	
	$this->_helper->layout->enableLayout();
	$this->view->headLink()->appendStylesheet('/css/secure/stylesheet_header.css');
	$this->view->headLink()->appendStylesheet('/css/secure/checkout_common.css');
		$this->view->headLink()->appendStylesheet('/css/secure/success.css');
		$this->view->headTitle('Order placed - Goo2o.com checkout');


		$userName =  new Zend_Session_Namespace('USER');
		$ori = new Zend_Session_Namespace('original_login');
		$sessionReview = new Zend_Session_Namespace('Api_Model_Review_Buynow');
		$sessionCart= new Zend_Session_Namespace('Api_Model_CART_Buynow');
		//Zend_Layout::getMvcInstance()->setLayout('secure');
	//Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/secure/layouts');

                //echo "<pre>";
                //print_r($sessionReview);exit;
			$sessionReviewBack = new Zend_Session_Namespace('Api_Model_Review_Back_Buynow');
        if(count($sessionReviewBack->item)<=0)
		{
		$this->_redirect(HTTP_SERVER.'/cart/#list');
		}
		if($ori->userId=='')
		{
			$this->_redirect(HTTPS_SECURE.'/secure/login?tab=1');
		}
		$sessionReviewwelldone = new Zend_Session_Namespace('Api_Model_Review_Welldone_Buynow');
		$this->view->welldonedata=$sessionReviewwelldone->item;
		if(!empty($sessionReviewwelldone->item))
			{
				$totalamount=0;
				foreach($sessionReviewwelldone->item as $key=>$val)
					{
					
						$totalamount+=($val[0]->product_mrp*$val[0]->product_qty)+($val[0]->shipcost*$val[0]->product_qty);
					}
			}
		$this->view->totalamount=$totalamount;
		$this->view->totalitem=count($sessionReviewwelldone->item);
			$sessionReviewBack = new Zend_Session_Namespace('Api_Model_Review_Back_Buynow');
			$this->view->recentpaidOrder=$sessionReviewBack->item;
			$sessionDiscountAmount = new Zend_Session_Namespace('CartDIscountAmount');
			$this->view->minusDisCount= $sessionDiscountAmount->total;
			unset($sessionDiscountAmount->total);
			unset($sessionReviewBack->item);
			unset($sessionReview->items);
			unset($sessionCart->items);
				unset($sessionReview->item);
			unset($sessionCart->item);	
	/*	echo "<pre>";
		print_r($sessionReviewwelldone->item); 
		exit;*/
		
	}

        public function setshippingaddAction()
        {
		
            $request = $this->_request->getParams();
            $id = $request['id'];
            $userId = new Zend_Session_Namespace('USER');
	$ori = new Zend_Session_Namespace('original_login');	
            $sessionReview = new Zend_Session_Namespace('Api_Model_Review');
            $sessionItems = new Zend_Session_Namespace('Api_Model_Cart');
            $sesstionItm = $sessionItems->items;
            $ReviewItm = $sessionReview->items;
            $totalItems = count($sesstionItm);
            $totalReview = count($ReviewItm);
           
            if(isset($request['id']))
            {
               foreach($sesstionItm as $key=>$val)
               {
			   
                   $val->address_book_id = $id;
                   $cust_id = $ori->userId;
                   $vcode = $val->variationcode;
                   $api_key = $val->store_api_key[0][apikey];
                   $pid = $val->product_id;
                   $add_id = $val->address_book_id;
				   $shippingcost=$this->_cartModel->getproductshippngcost($val->product_id, $val->address_book_id);
				   
                   $this->_cartModel->UpdateBasketAddId($cust_id, $vcode, $api_key, $pid, $add_id);
				   
				   
               }
               foreach($ReviewItm as $key=>$val)
               {
                   $val->address_book_id = $id;
               }

            }
			
         		
            $this->_redirect('/cart/checkoutaddress');
		
			
			
        }
		 public function setshippingaddbnAction()
        {
		
            $request = $this->_request->getParams();
            $id = $request['id'];
            $userId = new Zend_Session_Namespace('USER');
			$ori = new Zend_Session_Namespace('original_login');	
            $sessionItems = new Zend_Session_Namespace('Api_Model_Cart_Buynow');
            $sesstionItm = $sessionItems->items;
            $ReviewItm = $sessionReview->items;
            $totalItems = count($sesstionItm);
            $totalReview = count($ReviewItm);
           
            if(isset($request['id']))
            {
               foreach($sesstionItm as $key=>$val)
               {
			   
                   $val->address_book_id = $id;
                   $cust_id = $ori->userId;
                   $vcode = $val->variationcode;
                   $api_key = $val->store_api_key[0][apikey];
                   $pid = $val->product_id;
                   $add_id = $val->address_book_id;
				   
                   $this->_cartModel->UpdateBasketAddId($cust_id, $vcode, $api_key, $pid, $add_id);
				   
               }
               foreach($ReviewItm as $key=>$val)
               {
                   $val->address_book_id = $id;
               }

            }
			
         		
            $this->_redirect('/cart/checkoutaddressbn');
		
			
			
        }
	public function setGiftConfigureAction(){
		$this->view->headTitle('Configure Gift Certificate - '. $_SESSION['USER']['userDetails'][0]['title'].PAGE_EXTENSION);
		$this->view->headMeta()->setName('keywords', 'Configure Gift Certificate , Goo2o Technologies');
		$this->view->headMeta()->setName('description', 'Configure Gift Certificate , Goo2o Technologies');
		$this->view->headLink()->appendStylesheet('/css/secure/set-gift-configure.css');

		$this->view->headScript()->appendFile('/jscript/common/jquery-1.5.1.js','text/javascript');
        $this->view->headLink()->appendStylesheet('/jquery-ui/themes/base/jquery.ui.all.css');
		$this->view->headScript()->appendFile('/jquery-ui/ui/jquery.ui.core.js');
		$this->view->headScript()->appendFile('/jquery-ui/ui/jquery.ui.widget.js');
		$this->view->headScript()->appendFile('/jquery-ui/ui/jquery.ui.datepicker.js');
		$this->view->headLink()->appendStylesheet('/jquery-ui/demos/demos.css');
       // $this->view->headScript()->appendFile('/o2ocheckout/jscript/range_date_picker.js');
		$this->view->headScript()->appendFile('/jscript/secure/set-gift-configure.js','text/javascript');
		$this->view->request =  $this->_request->getParams();	
		$gift_id = explode('_',$this->view->request['gc']);
		if($this->view->request['flag']==2){
			$this->_redirect(HTTP_SECURE . '/cart/set-gift-configure/gc/'.$_SESSION['GCPURCHASE']['gc_id']);
		}
		$gift_mapper = new Secure_Model_PurchasegiftMapper();
		$gift_mapper->getGiftDetail($gift_id[1],$gift_id[0]);
		$this->view->order_gc_create_id = $gift_id[0].'_'.$gift_id[1];
		$gift_details = $gift_mapper->__get('_gift_details_array');
		$this->view->gift_data = $gift_details;
		$gift_duration = $gift_mapper->__get('_gift_duration_array');

		$this->view->gift_duration = $gift_duration;
		$arr_total_detail = Zend_Json::encode($gift_duration);
		$this->view->headScript()->appendScript("var gift_duration_detail = $arr_total_detail");
		$total_receipt = count($_SESSION['GCPURCHASE']['receipt_name']);
		$counter_val = ($_SESSION['GCPURCHASE'])?$total_receipt:1;
		$this->view->headScript()->appendScript("var counter_val = $counter_val");
		$this->view->counter_value = $counter_val;
		if($_POST['continue_gc_detail_x']!=''){
			$_SESSION['GCPURCHASE'] = $_POST;//echo '<pre>';print_r($_SESSION['GCPURCHASE']);
			if($gift_duration['expiry_set']!=''){
				$_SESSION['GCPURCHASE']['store_api_key'] = $gift_duration['store_api_key'];
				$_SESSION['GCPURCHASE']['expiry_duration'] = $gift_duration['expiry_duration'];
				$_SESSION['GCPURCHASE']['expiry_duration_type'] = $gift_duration['expiry_duration_type'];
				$_SESSION['GCPURCHASE']['gift_template_name'] = $gift_details['name'];
			}
			$this->_redirect(HTTP_SECURE . '/cart/review-and-confirm-gift/');
		}
	}
	public function reviewAndConfirmGiftAction(){
		$this->view->headTitle('Review & Confirm Gift Certificate - '.$_SESSION['USER']['userDetails'][0]['title'].PAGE_EXTENSION);
		$this->view->headMeta()->setName('keywords', 'Review & Confirm Gift Certificate , Goo2o Technologies');
		$this->view->headMeta()->setName('description', 'Review & Confirm Gift Certificate , Goo2o Technologies');
		$this->view->headLink()->appendStylesheet('/css/secure/review-and-confirm-gift.css');
		if(isset($_SESSION['GCPURCHASE'])){
			$this->view->request =  $this->_request->getParams();
			$gift_mapper = new Secure_Model_PurchasegiftMapper();
			$this->view->gcpurchase = $_SESSION['GCPURCHASE'];
			$gift_id = explode('_',$_SESSION['GCPURCHASE']['gc_id']);
			$gift_mapper->getMallDetails($gift_id[0]);
			$mall_details = $gift_mapper->__get('_mall_detail_array');
			$this->view->mall_detail = $mall_details;
			if($this->view->gcpurchase['sending_date']=='Now')
				$date_timestamp = time();
			else
				$date_timestamp = strtotime($this->view->gcpurchase['sending_date']);
			
			$this->view->date_timestamp = date("M j, Y",$date_timestamp);
		}else{
			$this->_redirect(HTTP_SECURE.'/login?tab=1');
		}
	}
	public function wellDoneAction(){
		$this->view->headTitle('Well Done - '.$_SESSION['USER']['userDetails'][0]['title'].PAGE_EXTENSION);
		$this->view->headMeta()->setName('keywords', 'Well Done , Goo2o Technologies');
		$this->view->headMeta()->setName('description', 'Well Done , Goo2o Technologies');
		if($_SESSION['GCPURCHASE']){
			$this->view->request =  $this->_request->getParams();
			$this->view->final_gcpurchase = $_SESSION['GCPURCHASE'];
			$gift_id = explode('_',$_SESSION['GCPURCHASE']['gc_id']);
			$gift_mapper = new Secure_Model_PurchasegiftMapper();
			$gift_mapper->getGiftDetail($gift_id[1],$gift_id[0]);
			$gift_details = $gift_mapper->__get('_gift_details_array');
			$this->view->gift_purchanse_date = $gift_details;	
			if($this->view->final_gcpurchase['sending_date']=='Now')
				$send_date = time();	
			else
				$send_date = strtotime($this->view->final_gcpurchase['sending_date']);
			
			$this->view->date_sending = date("M j, Y",$send_date);
		
			$code_val = $_SESSION['GCPURCHASE']['store_api_key'].'-'.$_SESSION['SESSION']['ApiKey'].'-'.$_SESSION['GCPURCHASE']['gc_id'];
			//$sending_date = strtotime($_SESSION['GCPURCHASE']['sending_date']);
			$transactionid = 'FKSDF45SDF';
			if($_SESSION['GCPURCHASE']['expiry_duration_type']=='days'){
				$exp_date = $send_date + ($_SESSION['GCPURCHASE']['expiry_duration']* 86400);
			}else if($_SESSION['GCPURCHASE']['expiry_duration_type']=='months'){
				$exp_date = strtotime("+".$_SESSION['GCPURCHASE']['expiry_duration']."months") ;
			}else if($_SESSION['GCPURCHASE']['expiry_duration_type']=='years'){
				$exp_date = strtotime("+".$_SESSION['GCPURCHASE']['expiry_duration']."years") ;
			}
			$payment_mode = 1;
			
			$purchase_array = array('gift_amount'=>$_SESSION['GCPURCHASE']['gift_amount'],'sender_name'=>$_SESSION['GCPURCHASE']['sender_name'],'sender_email'=>strtolower($_SESSION['GCPURCHASE']['sender_email']),'gift_msg'=>$_SESSION['GCPURCHASE']['msg_textarea'],'buyer_apikey'=>$_SESSION['SESSION']['ApiKey'],'sending_date'=>$send_date,'payment_mode'=>$payment_mode,'transaction_id'=>$transactionid,'giftid_tempid'=>$_SESSION['GCPURCHASE']['gc_id'],'expiry_date'=>$exp_date,'store_apikey'=>$_SESSION['GCPURCHASE']['store_api_key'],'created_date'=>time(),'gift_template_name'=>$_SESSION['GCPURCHASE']['gift_template_name']);
			$gift_mapper->addGCPurchaseData($purchase_array,$code_val,$_SESSION['GCPURCHASE']);
			$this->view->gift_inserted_details = $gift_mapper->__get('_last_inserted_id');
			$_SESSION['GCPURCHASE'] = '';
			
		}else{
			 $this->_redirect(HTTP_SERVER.'/myaccount/buyer/#buyer-purchase-list');
		}
		
	} 
	public function processAction()
	{
		$generalobject=new General();
						//if($userName->userId==865)
		for($i=0;$i<1;$i++)
		{	
		
		$generalobject->inserttofronttableproduct(19028);
		
		}
		exit;
	}
}
?>
