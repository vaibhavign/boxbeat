<?php 
class CartController extends Zend_Controller_Action
{
	public function init()
	{
		Zend_Layout::getMvcInstance()->setLayout('admin');
		Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH.'/modules/admin/layouts');
		$this->view->headScript()->appendFile('/jscript/common/o2olib.js','text/javascript');
		$this->view->headScript()->appendFile('/jscript/common/white.js','text/javascript');
		$this->view->headScript()->appendFile('/jscript/default/jquery.popup.js', 'text/javascript');
		$this->view->headLink()->appendStylesheet('/css/admin/admin.css');
		$this->view->headLink()->appendStylesheet('/css/default/cart.css');
		$this->view->headLink()->appendStylesheet('/css/default/wishlist_popup.css');
		$session = new Zend_Session_Namespace('USER');				
		$this->mapper=new Default_Model_CartMapper();
		if($session->userId=='')
		define('PAGE_TITLE','My Shopping Cart -'.PAGE_EXTENSION);


	}
	public function indexAction()
	{
	$session = new Zend_Session_Namespace('Api_Model_Cart');
         //echo "<pre>";
		// print_r($session->items);  
		// exit;
		$this->view->headScript()->appendFile('/jscript/common/bbq.js');
	}
	public function listAction()
	{ 	
		                      
		$request = $this->_request->getParams();
		if($request['list'] || $request['list_x'])
		{
			$this->_redirect(HTTPS_SECURE.'/cart/selectshippingaddress');
		}
		$this->_helper->layout->disableLayout();
		$userName =  new Zend_Session_Namespace('USER');
		$session = new Zend_Session_Namespace('Api_Model_Cart');
		//$wishlist = new Zend_Session_Namespace('Api_Model_Wishlist');
		$data=$session->items;
		$flag = 0;

		if($userName->userId!='')
		{
			$flag = 1;
		}
		
              // echo 'dfgdfg';
		$wishlist=$this->mapper->getWishlist($userName->userId);
          //  print_r($wishlist);
              // exit;
		$this->view->wishlist=$wishlist['data'];

		$cartdata=array();
		$errormaeesge=array();
                //print_r($wishlist['error']);
               
                if(!empty($wishlist['error']))
                {

                   $errormaeesge= array_merge($errormaeesge,$wishlist['error']);
                }
		$class=New General();
		
		
		if(!empty($data))
		{ 
	
			foreach($data as $key=>$productrec)
			{ 
			//echo "<pre>";
                      //  print_r($productrec);
			
		
				$proname=$this->mapper->getproductname($productrec->product_id,$productrec->storeApiKey,$productrec->variationcode);
					
				if($proname=='0')
				{
					array_push($errormaeesge, "We're sorry. The item <a href='".$productrec->product_url."' title='".$productrec->product_name."' target='_blank'>".$productrec->product_name."</a> is no longer available to buy on <strong><a href='".$productrec->store_api_key[0]['mallurl']."' title='".$productrec->store_api_key[0]['title']."' target='_blank'>".$productrec->store_api_key[0]['title']."</a></strong>. Hence we've removed from your cart.");
					unset($session->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]);
					continue;
				}
                                
				$session->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->product_name=$proname;
                                if($productrec->variations[$productrec->product_id]['srp']=='')
                                $productrec->variations[$productrec->product_id]['srp']=$productrec->product_mrp;
				$variation= $this->mapper->getproductdetail($productrec->product_id,$productrec->variationcode,$productrec->storeApiKey,$productrec->product_qty,$productrec->variations[$productrec->product_id]['srp']);
				//print_r($variation);exit;
                               
                                if($productrec->variations[$productrec->product_id]['srp']=='')
                                {
                                 $productrec->variations[$productrec->product_id]['srp']= number_format( $productrec->product_mrp,2);
                                    
                                }
				if($variation==0)
				{
					array_push($errormaeesge, "We're sorry. The item <a href='".$productrec->product_url."' title='".$productrec->product_name."' target='_blank'>".$productrec->product_name."</a> is no longer available to buy on <strong><a href='".$productrec->store_api_key[0]['mallurl']."' title='".$productrec->store_api_key[0]['title']."' target='_blank'>".$productrec->store_api_key[0]['title']."</a></strong>. Hence we've removed from your cart.");
					unset($session->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]);
					continue;
				}
				if($variation[$productrec->product_id]['error']=='-1')
				{
					array_push($errormaeesge, "Please note that the price of <a href='".$productrec->product_url."' title='".$productrec->product_name."' target='_blank'>".$productrec->product_name."</a> has increased to <b>Rs.".$variation[$productrec->product_id]['srp']."</b> from <b>Rs.".$productrec->variations[$productrec->product_id]['srp']."</b> since you placed it in your Shopping Cart.");
					
				}
				if($variation[$productrec->product_id]['error']=='-2')
				{
					array_push($errormaeesge, "Please note that the price of <a href='".$productrec->product_url."' title='".$productrec->product_name."' target='_blank'>".$productrec->product_name."</a> has decreased to <b>Rs.".$variation[$productrec->product_id]['srp']."</b> from <b>Rs.".$productrec->variations[$productrec->product_id]['srp']."</b> since you placed it in your Shopping Cart.");
					
				}
				

				$session->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->shippingbox=$this->mapper->shippingBoxDetail($productrec->product_id,$productrec->storeApiKey);	
				$session->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->storeApiKey=$productrec->storeApiKey;
				
				$session->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->store_api_key=$this->mapper->storetitleDefault($productrec->storeApiKey);
				//print_r($this->mapper->storetitleDefault($productrec->storeApiKey));
//exit;

				//$session->items[$productrec->storeApiKey."_".$productrec->product_id]->store_api_key=$productrec->store_api_key;
				$session->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->variations=$variation;
                               
                $img=$class->getImageFromDir($productrec->product_id,'product','cart','1');
			//	$session->items[$productrec->storeApiKey."_".$productrec->product_id]->product_url= $img=$class->getImageFromDir($productrec->product_id,'product','cart','1');
                                //print_r($img[0]);exit;
				$session->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]->productImageSrc=$img[0];
			}
		}

		//echo "<pre>";
		//print_r($data);
		//exit;
		
		$this->view->errormaeesge=$errormaeesge;
		$this->view->sessiondata=$session->items;
		$this->view->status=$flag;

		$this->view->totalprice=$this->mapper->totalprice();
                //echo "<pre>";
                //print_r($_SESSION);exit;
	}
	public function uqtyAction()
	{
		$this->_helper->layout->disableLayout();
		$request = $this->_request->getParams();
		$session = new Zend_Session_Namespace('Api_Model_Cart');
		$data=$session->items;
		$session->items[$request['api']."_".$request['pid']."_".$request['vcode']]->product_qty=$request['qty'];
		$datatotalprice['total']=number_format($this->mapper->totalprice(),2,'.','');
		$userName = new Zend_Session_Namespace('USER');
		if($userName->userid!='')
		{
			$this->mapper->updatequantity($request['pid'],$request['api'],$userName->userid,$request['qty']);
		}
		echo json_encode($datatotalprice);
		exit;
			
		}
	public function uqtywishlistAction()
	{
		$this->_helper->layout->disableLayout();
		$userName =  new Zend_Session_Namespace('USER');
		$request = $this->_request->getParams();
		$api_key = $request['api'];
		$pid = $request['pid'];
		$uid = $userName->userId;
		$vcode = $request['vcode'];
		$qty = $request['qty'];
		$wishlist=$this->mapper->updateWishlist($pid,$uid,$vcode,$qty,$api_key);
		exit;
		}
	public function deletelistAction()
	{
		$this->_helper->layout->disableLayout();
		$request = $this->_request->getParams();
		$session = new Zend_Session_Namespace('Api_Model_Cart');
		$data=$session->items;
		$id = $request['id'];
		$key = $request['key'];
		$vcode = $request['vcode'];
		//echo $request['key']."_".$request['id']."_".$vcode;exit;
		//if(($session->items[$request['key']."_".$request['id']]->variationcode)==$vcode)
		//{
			unset($session->items[$request['key']."_".$request['id']."_".$vcode]);
		//}
		
		$userName = new Zend_Session_Namespace('USER');
		if($userName->userId!='')
		{
			//echo 'fdgdfg df';exit;
			$this->mapper->deleteproduct($request['id'],$request['key'],$userName->userId,$vcode);
		}
		$datatotalprice['totalprice']=number_format($this->mapper->totalprice(),2,'.','');
		$datatotalprice['totalproduct']=count($session->items);
		$this->_redirect('/cart/#list');
		//echo json_encode($datatotalprice);
		exit;
		}
	public function wishlistAction()
	{       
		$this->_helper->layout->disableLayout();
		$request = $this->_request->getParams();
		$userName =  new Zend_Session_Namespace('USER');
		$session = new Zend_Session_Namespace('Api_Model_Cart');
		$data=$session->items;
		$vcode = $request['vcode'];
		if($userName->userId=='')
		{
			$_SESSION['mypage']=HTTP_SERVER.'/cart/#list';
			$this->_redirect(HTTPS_SECURE.'/login');
			
		}
		/*echo "<pre>";
		print_r($data);
		echo $request['val']."_".$vcode;exit;*/
		if($data[$request['val']."_".$vcode]->variationcode == $vcode)
		{       //echo "<pre>";
                        //print_r($data[$request['val']]);exit;
			$product_id = $data[$request['val']."_".$vcode]->product_id;
			$customer_id = $userName->userId;
			$product_name = $data[$request['val']."_".$vcode]->product_name;
			$product_qty = $data[$request['val']."_".$vcode]->product_qty;
			$product_maxqty = $data[$request['val']."_".$vcode]->product_maxqty;
			$product_mrp = $data[$request['val']."_".$vcode]->variations[$data[$request['val']."_".$vcode]->product_id]['srp'];
			$product_imagesrc = $data[$request['val']."_".$vcode]->productImageSrc;
			$product_url = $data[$request['val']."_".$vcode]->product_url;
			$product_shipping = $data[$request['val']."_".$vcode]->product_shipping;
			$product_dateadded = time();
			$product_datemodified = time();
			$address_book_id = $data[$request['val']."_".$vcode]->address_book_id;
			$store_api_key = $data[$request['val']."_".$vcode]->storeApiKey;
			$variationcode = $data[$request['val']."_".$vcode]->variationcode;
			$ckeckouttype = $data[$request['val']."_".$vcode]->ckeckouttype;
			$currencytype = $data[$request['val']."_".$vcode]->currencytype;
			$shippingtype = $data[$request['val']."_".$vcode]->shippingtype;
			$shippingsubtype = $data[$request['val']."_".$vcode]->shippingsubtype;
			$shippinglocation = $data[$request['val']."_".$vcode]->shippinglocation;
			$excluded_city = $data[$request['val']."_".$vcode]->excludedcity;
                        
			$keydata = $this->mapper->storetitleDefault($store_api_key);
                        
			$wishlist_data = array(
			'product_id'=>$product_id,
			'customer_id'=>$customer_id,
			'product_name'=>$product_name,
			'product_qty'=>$product_qty,
			'product_maxqty'=>$product_maxqty,
			'product_mrp'=>$product_mrp,
			'product_imagesrc'=>$product_imagesrc,
			'product_url'=>$product_url,
			'product_shipping'=>$product_shipping,
			'product_dateadded'=>$product_dateadded,
			'product_datemodified'=>$product_datemodified,
			'address_book_id'=>$address_book_id,
			//'storeApiKey'=>$store_api_key,
			'store_api_key'=>$keydata[0]['apikey'],
			'variationcode'=>$variationcode,
			'ckeckouttype'=>$ckeckouttype,
			'currencytype'=>$currencytype,
			'shippingtype'=>$shippingtype,
			'shippingsubtype'=>$shippingsubtype,
			'shippinglocation'=>$shippinglocation,
			'excluded_city'=>$excluded_city
			);
			foreach($data as $key=>$val)
			{	//echo "<pre>";print_r($data);exit;
			
				if($request['val']."_".$vcode == $key)
				{
                                        //echo $request['val'];exit;
                                        //echo "<pre>";
                                        //print_r($session->items[$request['val']]);exit;
					$wishlist=$this->mapper->insertWishlist($wishlist_data);
                                        
					if($wishlist)
					{
                                        unset($_SESSION['Api_Model_Cart']['items'][$request['val']."_".$vcode]);
					//unset($session->items[$request['val']]);
					}
				}
			}
		}
                //echo "<pre>";
                  // print_r($_SESSION);exit;
		$this->_redirect(HTTP_SERVER.'/cart/#list');
                    
	}	
	public function movetocartAction()
	{
		$this->_helper->layout->disableLayout();
		$request = $this->_request->getParams();
		$userName =  new Zend_Session_Namespace('USER');
		$session = new Zend_Session_Namespace('Api_Model_Cart');
                $db = Zend_Db_Table::getDefaultAdapter();
		$data=$session->items;
		$product_id = $request['id'];
		$storeapi_key = $request['key'];
		$customer_id = $userName->userId;
		$variation_code = $request['vcode'];
		$wishlist=$this->mapper->deleteWishlist($product_id,$storeapi_key,$customer_id,$variation_code);
		if(count($wishlist) > 0)
		{	
			$key = $storeapi_key.'_'.$product_id;
			$dvcode = $wishlist[0]['variationcode'];
			$svcode = $data[$key]->variationcode;
			$qty = $wishlist[0]['product_qty'];
			if($dvcode == $svcode)
			{
				$dqty = $wishlist[0]['product_qty'];
				$sqty = $data[$key]->product_qty;
				$smaxqty = $data[$key]->product_maxqty;
				$nqty = $dqty + $sqty;
				if($nqty > $smaxqty)
				{
					$qty = $smaxqty;
				}
				else
				{
					$qty = $nqty;;
				}
			}	
				
				$session->items[$key."_".$variation_code]->product_id=$wishlist[0]['product_id'];
				$session->items[$key."_".$variation_code]->product_name=$wishlist[0]['product_name'];
				$session->items[$key."_".$variation_code]->product_qty=$qty;
                                $session->items[$key."_".$variation_code]->addedtime=time();
				$session->items[$key."_".$variation_code]->product_maxqty=$wishlist[0]['product_maxqty'];
				$session->items[$key."_".$variation_code]->productImageSrc=$wishlist[0]['product_imagesrc'];
				$session->items[$key."_".$variation_code]->product_url=$wishlist[0]['product_url'];
				$session->items[$key."_".$variation_code]->product_shipping=$wishlist[0]['product_shipping'];
				$session->items[$key."_".$variation_code]->address_book_id=$wishlist[0]['address_book_id'];
				$session->items[$key."_".$variation_code]->storeApiKey=$wishlist[0]['store_api_key'];
				$session->items[$key."_".$variation_code]->store_api_key=$this->mapper->storetitleDefault($wishlist[0]['store_api_key']);
				$session->items[$key."_".$variation_code]->variationcode=$wishlist[0]['variationcode'];
				$session->items[$key."_".$variation_code]->ckeckouttype=$wishlist[0]['ckeckouttype'];
				$session->items[$key."_".$variation_code]->currencytype=$wishlist[0]['currencytype'];
				$session->items[$key."_".$variation_code]->shippingtype=$wishlist[0]['shippingtype'];
				$session->items[$key."_".$variation_code]->shippingsubtype=$wishlist[0]['shippingsubtype'];
				$session->items[$key."_".$variation_code]->shippinglocation=$wishlist[0]['shippinglocation'];						
				$session->items[$key."_".$variation_code]->excludedcity=$wishlist[0]['excluded_city'];						
				$session->items[$key."_".$variation_code]->variations[$wishlist[0]['product_id']][srp]=$wishlist[0]['product_mrp'];
                //echo "insert into basket set product_id=".$wishlist[0]['product_id'].",customer_id=".$customer_id.",product_name='".$wishlist[0]['product_name']."',product_qty=".$qty.",product_maxqty=".$wishlist[0]['product_maxqty'].",product_mrp=".$wishlist[0]['product_mrp'].",product_imagesrc='".$wishlist[0]['product_imagesrc']."'product_url='".$wishlist[0]['product_url']."',product_dateadded='".time()."',product_datemodified='".time()."',address_book_id='0',store_api_key='".$wishlist[0]['store_api_key']."',variationcode='".$wishlist[0]['variationcode']."',ckeckouttype='".$wishlist[0]['ckeckouttype']."',currencytype='".$wishlist[0]['currencytype']."',shippingtype='".$wishlist[0]['shippingtype']."',shippingsubtype='".$wishlist[0]['shippingsubtype']."',shippinglocation='".$wishlist[0]['shippinglocation']."',excluded_city='".$wishlist[0]['excluded_city']."'";exit;
						$db->query("delete from basket where customer_id=".$customer_id." and product_id =".$wishlist[0]['product_id']." and store_api_key='".$wishlist[0]['store_api_key']."' and variationcode=".$wishlist[0]['variationcode']."");	

                 $db->query("insert into basket set product_id=".$wishlist[0]['product_id'].",customer_id=".$customer_id.",product_name='".$wishlist[0]['product_name']."',product_qty=".$qty.",product_maxqty=".$wishlist[0]['product_maxqty'].",product_mrp=".$wishlist[0]['product_mrp'].",product_imagesrc='".$wishlist[0]['product_imagesrc']."',product_url='".$wishlist[0]['product_url']."',product_dateadded='".time()."',product_datemodified='".time()."',address_book_id='0',store_api_key='".$wishlist[0]['store_api_key']."',variationcode='".$wishlist[0]['variationcode']."',ckeckouttype='".$wishlist[0]['ckeckouttype']."',currencytype='".$wishlist[0]['currencytype']."',shippingtype='".$wishlist[0]['shippingtype']."',shippingsubtype='".$wishlist[0]['shippingsubtype']."',shippinglocation='".$wishlist[0]['shippinglocation']."',excluded_city='".$wishlist[0]['excluded_city']."'");
		}
		$this->_redirect('/cart/#list');
	}	
	public function deletewishlistAction()
	{
		$this->_helper->layout->disableLayout();
		$request = $this->_request->getParams();
		$userName =  new Zend_Session_Namespace('USER');
		$product_id = $request['id'];
		$storeapi_key = $request['key'];
		$customer_id = $userName->userId;
		$variation_code = $request['vcode'];
		$wishlist=$this->mapper->deleteWishlist($product_id,$storeapi_key,$customer_id,$variation_code);
		$this->_redirect('/cart/#list');
	}						
}
?>
