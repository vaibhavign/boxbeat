<?php
class Secure_Model_CartMapper   
{
    protected $_dbTable;
    public function setDbTable($dbTable)
    {
	
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable('Api_Model_DbTable_Cart');
        }
        return $this->_dbTable;
    }
    public function find($id, Api_Model_Cart $cart)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            return;
        }
        $row = $result->current();
        $guestbook->setId($row->id)
                  ->setEmail($row->email)
                  ->setComment($row->comment)
                  ->setCreated($row->created);
    }
    public function fetchAll()
    {
        $result= $this->getDbTable()->fetchAll();
		$count = count($result);
		//foreach ($result as $row) {
		//echo $row->name;	
		
		//}
        return $entries;
    }
	 public function saveCartItem($data)
    {
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->insert('basket',$data);
        //$this->getDbTable()->insert($data);
    }
	public function updateCartItem($customerid,$apikey,$productid,$qty,$shipping='')
	{
	if($shipping!='')
		{
			$data = array('product_qty' => $qty,'address_book_id' => $shipping);
		}
	else
		{
			$data = array('product_qty' => $qty);
		}	
	
	
	
	$this->getDbTable()->update($data, array('customer_id = ?' => $customerid,'store_api_key = ?' => $apikey,'product_id = ?' => $productid,'deletedflag = ?' => '0'));
	}
	public function deleteItem($key,$id,$cid,$vcode)
	{
	$db = Zend_Db_Table::getDefaultAdapter();
	$where = $db->query("update basket set  deletedflag='1' where product_id = '" . $id . "' AND customer_id = '" . $cid . "' AND store_api_key = '" . $key . "' AND variationcode='".$vcode."'");
	//$this->getDbTable()->delete($where);
	
	}
	public function myAddresses($customerId)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		//$select = $db->select()->from('address_book')->where("customers_id='".$customerId."'");
		$sql = "select * from address_book as ab left join city as c on  ab.city=c.id left join state as s on s.id=ab.state where ab.customers_id=".$customerId." and ab.deletedflag='0' and ab.deletedflag='0' order by ab.address_book_id desc" ;
		//$sql = "select ab.*, c.cityname as cityname, s.state_name as state_name from address_book as ab, cities as c, state as s where ab.city=c.id and ab.state=s.id and customers_id='$customerId'";
		$result = $db->query($sql);
		$resultSet = $result->fetchAll();	
		return $resultSet;
	}
	public function myBillingAddresses($customerId)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = "select * from address_book where customers_id='$customerId' and deletedflag='0'";
		$result = $db->query($sql);
		$resultSet = $result->fetchAll();	
		/*$select = $db->select()->from('address_book')->where("customers_id='".$customerId."' AND billing_address='1'");
		$result = $select->query();
		$resultSet = $result->fetchAll();*/
		return $resultSet;
	
	}
	public function paymentModules()
	{		
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()->from('order_payment_modules')->where("pay_mod_status='1'");
			$result = $select->query();
			$resultSet = $result->fetchAll();	
			return $resultSet;
		}
	public function allCities($stateid)
	{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()->from('city')->where("stateid=".$stateid);
			$result = $select->query();
			$resultSet = $result->fetchAll();	
			return $resultSet;
		}
	public function allStates()
	{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()->from('state')->where("deleted='0'");
			$result = $select->query();
			$resultSet = $result->fetchAll();	
			return $resultSet;
	
	}
	public function insertMyAddress($data)
	{
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->insert('address_book',$data);
		echo $db->lastInsertRowid();exit;
	}
	
	public function myBillingAddressesDetail($id,$cid)
	{
	$db = Zend_Db_Table::getDefaultAdapter();
	if($id!='')
		{
			//$select = $db->select()->from('address_book')->where("customers_id='".$cid."' AND address_book_id=".$id);
			$selects = "select ab.*, c.cityname as cityname, s.state_name as state_name from address_book as ab, city as c, state as s where ab.city=c.id and ab.state=s.id and customers_id='$cid' and address_book_id='$id' and ab.deletedflag='0'";
			$result = $db->query($selects);
			$resultSet = $result->fetchAll();
			return $resultSet;
		}	
	}
public function myBillingAddressesDetailOrder($id,$cid)
	{
	$db = Zend_Db_Table::getDefaultAdapter();
	if($id!='')
		{
			//$select = $db->select()->from('address_book')->where("customers_id='".$cid."' AND address_book_id=".$id);
			 $selects = "select * from  order_addresses as ab where ab.customer_id=".$cid." and ab.order_address_id=".$id;
			$result = $db->query($selects);
			$resultSet = $result->fetchAll();
			return $resultSet;
		}	
	}
	public function updateMyAddress($data,$cid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->update('address_book',$data,'customers_id ='.$cid);
	}
	public function productExists($customerId,$apiKey,$productId)
	{
	/*echo $customerId;
	echo "<br />";
	echo $apiKey;
	echo "<br />";
	echo "==>".$productId;
	exit;*/
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()->from('basket')->where("customer_id='".$customerId."' AND store_api_key='".$apiKey."'  AND  product_id='".$productId."' and deletedflag='0'");
		$result = $select->query();
		$resultSet = $result->fetchAll();
		return $resultSet;
	}
   public function getCategorieDataSecure($p_id = 0){
   $db = Zend_Db_Table::getDefaultAdapter();
		if (!is_array($catarray)) $catarray = array();
		$sql = $db->select();
		$sql->from(array('addcategory'),array(cat_id))
			->where("cat_id != 1 and parent_id='".$p_id."'")
			->where("status='1'");
		$result = $db->fetchAll($sql);
		if(count($result)>0){
			foreach ($result as $key=>$val){
				$this->getCategorieDataSecure($val['cat_id']);
				$catarray[] =  $val;
			}
			if(!empty($catarray))
				{
					$catids='';
					foreach($catarray as $key=>$val)
						{
							$catids.=$val['cat_id'].",";
						}
				}
				//echo "select id from product where category_id in(".substr($catids,0,-1).")";
			$getProducys=$db->query("select id from product where category_id in(".substr($catids,0,-1).")");
			$getAllProduct=	$getProducys->fetchAll();
			return $getAllProduct;
		}
		
		
		
		//echo "<pre>#########<br/>";print_r($this->_cat_list_arr);//exit;
	}
	function updateAll($data,$userId,$totalQty,$storeApiKey,$productId)
	{
	
	$db = Zend_Db_Table::getDefaultAdapter();
	$sql = "Update basket set product_qty = '$totalQty' where customer_id = '$userId' and store_api_key='$storeApiKey' and product_id='$productId' and deletedflag='0'";
    $result = $db->query($sql);
    return $result;
	//$this->getDbTable()->update($data, array('customer_id = ?' => $userId,'store_api_key = ?' => $storeApiKey,'product_id = ?' => $productId,'product_qty = ?' => $totalQty));
	}
	public function fetchMycart($cId)
	{	
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()->from('basket')->where("customer_id=".$cId." and deletedflag='0'");
			$result = $select->query();
			$resultSet = $result->fetchAll();	
			
			return $resultSet;
		}
	public function updateaddress($data,$aid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->update('address_book',$data,"address_book_id =".$aid." and deletedflag='0'");
	}
	public function storetitle($storeurl)
    {
	
		$db = Zend_Db_Table::getDefaultAdapter();
		//echo "select * from username as u inner join mall_detail as md on md.user_id=u.id and m.apikey='".$storeurl."'";
		//echo 'dfgfdg';exit;
		$sql = "select *,md.id as mallid from mall_detail as md left join user_role as ur on md.apikey=ur.store_apikey and ur.role='2' join user as un on ur.email_id=un.user_email_address where ur.store_apikey='".$storeurl."'";
		$select =$db->query($sql);

		$resultSet = $select->fetchAll();
		return $resultSet;
    }
	public function totalproduct($pid,$variationcode)
	{
			$db = Zend_Db_Table::getDefaultAdapter();
			/*echo $selects = "select pv.variant_name from product_variation pv inner join product p on pv.product_id='$pid' and pv.variant_name=Stock and pv.default_flag='1' and p.id='".$pid."' and p.delete_flag='1'";exit;*/
			$selects = "select variant_value  from product_variation where product_id=".$pid." and variant_name='Stock' and variation_code=".$variationcode;

			$results = $db->fetchOne($selects);
			return $results;
			}
	public function checkuotdetail($product_id,$storeApiKey)
	{
			$db = Zend_Db_Table::getDefaultAdapter();
			$selects = "SELECT * FROM basket WHERE product_id ='".$product_id."' and store_api_key='".$storeApiKey."' and deletedflag='0'"; 
			$results = $db->fetchAll($selects);		
			return $results;
		}
	public function shipcondition($pid,$vcode)
	{
			$db = Zend_Db_Table::getDefaultAdapter();
		  	$selects = "SELECT variant_value FROM product_variation WHERE product_id='".$pid."' AND variation_code='".$vcode."' AND variant_name='Condition'" ; 
			$results = $db->fetchOne($selects);
			return $results; 	
		}
	public function shipvariation($pid,$vcode,$apikey)
	{
			$db = Zend_Db_Table::getDefaultAdapter();
		  	$selects = "SELECT variant_name,variant_value FROM product_variation WHERE product_id='".$pid."' AND variation_code='".$vcode."'" ;  
			$results = $db->fetchAll($selects);
				if(!empty($results))
					{
						$variationstring='';
						foreach($results as $key=>$val)
						{
						if($val['variant_name']=='SRP')
						{
							$pDetail[$pid]['srp']=$val['variant_value'];
						}
						if($val['variant_name']=='Condition')
						{
							$pDetail[$pid]['condition']=$val['variant_value'];
						}
						if($val['variant_name']=='Stock')
						{
						
							$pDetail[$pid]['stock']=$val['variant_value'];
							
						
						}
							
							
								if($val['variant_name']!='SRP' && $val['variant_name']!='Condition' &&  $val['variant_name']!='Description' && $val['variant_name']!='MRP' && $val['variant_name']!='Stock')
								{
									if($val['variant_value']!='')
									$variationstring.=$val['variant_name']."-".$val['variant_value'].",";
								}
							}
							$pDetail[$pid]['allvariation']= substr($variationstring,0,-1);
					
					}
					
			return $pDetail[$pid]['allvariation'];
		}
	function getprotname($pid,$apikey)
	{
	
			$db = Zend_Db_Table::getDefaultAdapter();
			$selects = "SELECT product_name FROM basket WHERE product_id='".$pid."' and store_api_key='".$apikey."' and deletedflag='0'";
			$results = $db->fetchOne($selects);	
			return $results; 
	}
	function shiprecords($pid,$vcode,$apikey,$qty)
	{
			$db = Zend_Db_Table::getDefaultAdapter();
			$session = new Zend_Session_Namespace('Api_Model_Cart');
			$userName = new Zend_Session_Namespace('USER');
			$data=$session->items;
			
			$selects = "SELECT variant_name,variant_value FROM product_variation WHERE product_id='".$pid."' AND variation_code='".$vcode."'" ; 
			$results = $db->fetchAll($selects);
				if(!empty($results))
					{
						$variationstring='';
						foreach($results as $key=>$val)
						{
						if($val['variant_name']=='SRP')
						{
							$pDetail[$pid]['srp']=$val['variant_value'];
						}
						if($val['variant_name']=='Condition')
						{
							$pDetail[$pid]['condition']=$val['variant_value'];
						}
						if($val['variant_name']=='Stock')
						{
						
							$pDetail[$pid]['stock']=$val['variant_value'];
							
							
							
						}
							
							
								if($val['variant_name']!='SRP' && $val['variant_name']!='Condition' &&  $val['variant_name']!='Description' && $val['variant_name']!='MRP' && $val['variant_name']!='Stock')
								{
									if($val['variant_value']!='')
									$variationstring.=$val['variant_name']."-".$val['variant_value'].",";
								}
							}
							$pDetail[$pid]['allvariation']= substr($variationstring,0,-1);
					
					}
					
			return $pDetail;
			}
	public function getLocationId($pid='')
	{
	//echo $pid;exit;
	$db = Zend_Db_Table::getDefaultAdapter();
	$userName = new Zend_Session_Namespace('USER');
	$ori = new Zend_Session_Namespace('original_login');
	$userid=$ori->userId;
	
	$sqlSellerApiKey=$db->query("select seller_id  from product where delete_flag='1' and id=".$pid);
	$userid=$sqlSellerApiKey->fetchAll();
	
	$sql = $db->query("select city from store_address where store_id= '".$userid[0]['seller_id']."' and visible='1'");
		$result = $sql->fetchAll();
		
		$r=array();
		 if(!empty($result))
		 {
		 	$i=0;
		 	foreach($result as $k=>$v)
			{
				$r[$i]=$v['city'];
			$i++;
			}
			
		 }	
	
		return  $r;
	}
	public function getcityIdsfromStateid($state_ids)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = "select id from city where stateid IN ($state_ids)";
		$result = $db->fetchAll($sql);
		
			if(!empty($result))
			{
				return $result;
			}
	}
	public function getExcludedcities($shippingId)
	{
	$db = Zend_Db_Table::getDefaultAdapter();
	$sql = "select REPLACE(sel.location_id,'^',',') as ids,location_type  from shipping_exclude_location as sel where sel.shipping_id  = '$shippingId'";
		$result = $db->fetchAll($sql);
		
	
		
			if(!empty($result))
			{
				$citids=array();
				
				foreach($result as $key=>$val)
				{
					if($val['ids']!='')
					{
					if($val['location_type']==1)
					{
						//echo $val['ids'];exit;
						$cityRows=$this->getcityIdsfromStateid($val['ids']);
						
						//print_r($cityRows);
					}
					if($val['location_type']==2)
					{
						$cityRows=explode(",", $val['ids']);
					}
					
					
					$citids= array_merge($citids,$cityRows);
					}
				}	//$mystr = str_replace("", ",", $result['location_id']);
				
			}
			
			return $citids;
	}
	public function getOrderDetailById($orderid)
	{
				$db = Zend_Db_Table::getDefaultAdapter();
				$userName =  new Zend_Session_Namespace('USER');
				$ori = new Zend_Session_Namespace('original_login');

				$orderdata=$db->query("select *,o.order_address_id as orederbillingaddress,md.id as mallid from order_item as oi   inner join  orders as o  on o.order_id=oi.order_id inner join mall_detail as md on md.user_id=oi.order_item_owner inner join order_product_detail as opd on opd.product_id 	=oi.order_product_detail_id 	  where o.order_id=".$orderid." and o.customer_id=".$ori->userId );
				$orderdata=$orderdata->fetchAll();
			$data=array();
			if(!empty($orderdata))
			{
				$i=0;
				foreach($orderdata as $key=>$val)
				{
					$data[$i]['orderid']=$val['order_id'];
					$data[$i]['orderitemid']=$val['order_item_id'];
					$data[$i]['gcdetail']=$val['gc_amount'];
					$data[$i]['orderdate']=$val['order_place_date'];
					$data[$i]['billingaddress']=$val['orederbillingaddress'];
					$data[$i]['shippingaddress']=$val['orederbillingaddress'];
					$data[$i]['payment_module']=$val['payment_module'];

					$data[$i]['productvariation']=$val['product_variation'];
					$data[$i]['product_variation_code']=$val['product_variation_code'];
					$data[$i]['product_condition']=$val['product_condition'];
					$data[$i]['product_name']=$val['product_name'];
					$data[$i]['product_id']=$val['product_id'];
					$data[$i]['pid']=$val['pid'];
					$data[$i]['product_shipping_price']=$val['product_shipping_price'];

					$data[$i]['order_item_total']=$val['order_item_total'];
					$data[$i]['product_mrp']=$val['product_mrp'];
					$data[$i]['title']=$val['title'];
					$data[$i]['storeid']=$val['mallid'];
				$i++;}
			}
		return $data;

	}
	public function productshippngcost($pid, $locid='')
	{	
		$db = Zend_Db_Table::getDefaultAdapter();
                //echo $pid;
                //echo "<br />";
                //echo $locid;exit;
		$errormaeesge=array();
		if($locid=='' || $locid=='0')
		{
                        //echo "hi";exit;
			$returnshipprice=-1;
			return $returnshipprice;
		}
           
		  $sql = "select  psp.shipping_id as shipping_id, sd.destination as ship_destination, sc.shipping_type as shipping_type, sc.shipping_pirce as shipping_pirce from product_shipping_policy as psp, shipping_cost as sc, shipping_method as sd where psp.shipping_id = sc.shipping_id and sd.shipping_id = psp.shipping_id and sd.delete_flag='0' and psp.product_id = ".$pid;
		$result = $db->fetchRow($sql);
		$getLocationAddressId=$db->query("select city from address_book where address_book_id=".$locid);
		$locid =$getLocationAddressId->fetchAll();
		$locid=$locid[0]['city'];
		$delhincrArray=array();
		
		$sql_delhiId="select value from config where `key`='DELHI_NCR_ID' and delete_flag='1' and status='1'";
			$result_delhincrId = $db->fetchRow($sql_delhiId);
			$sql_delhincr="select value from config where `key`='DELHI_NCR_CITY_ID' and delete_flag='1' and status='1'";
			$result_delhincr = $db->fetchRow($sql_delhincr);
			$delhincrArray=explode(",",$result_delhincr['value']);
			
		if($result['ship_destination']=='1')
		{
			
			
			$excludedLocation=$this->getExcludedcities($result['shipping_id']);
			$result_intersect = array_intersect($delhincrArray, $excludedLocation);
			if(count($result_intersect)==count($delhincrArray))
			{
			
				array_push($excludedLocation,$result_delhincrId['value']);
			}
		
			$mylocations=$this->getLocationId($pid);
				if(($locid==$result_delhincrId['value'] || in_array($locid,$delhincrArray)) && (in_array($result_delhincrId['value'],$mylocations)))
				{
				
			/*if($pid==19012)
		{
			echo "<pre>";
			print_r($mylocations);
			print_r($excludedLocation);
			echo $locid;
			print_r($delhincrArray);
			echo $result_delhincrId['value'];
		}*/
					$mylocations=array_merge($mylocations,$delhincrArray);
						array_unique($mylocations);
				}
                        //echo "reached";exit;
             
			/*if(!empty($delhincrArray))
			{
				$mylocations=array_merge($mylocations,$delhincrArray);
				array_unique($mylocations);
			}*/
			
			
			
			if(!empty($excludedLocation))
			$diff_array = array_diff($mylocations, $excludedLocation) ;
			else
			$diff_array = $mylocations;
			
    
			if($locid=='0')
				{
					$returnshipprice ='0';

				}
				else if(in_array($locid, $diff_array))
				{
					if($result['shipping_type']==1)
					{
					$returnshipprice= 0;
					}
						if($result['shipping_type']==2)
						{
						$returnshipprice= $result['shipping_pirce'];
						}
			}
			else
			{
                            $returnshipprice ='error';
			}
		}
		if($result['ship_destination']==2)
		{
		
		
		$excludedLocation=$this->getExcludedcities($result['shipping_id']);
		$result_intersect = array_intersect($delhincrArray, $excludedLocation);
			if(count($result_intersect)==count($delhincrArray))
			{
			
				array_push($excludedLocation,$result_delhincrId['value']);
			}
		
		$mylocations=$this->getLocationId($pid);
	
		 /*if($pid==19059)
					{
					echo $result['shipping_type'];
						echo 's dsfdsfds';
						echo "<pre>";
						print_r($excludedLocation);
						exit;
					}
					
					if(!empty($excludedLocation))
					$diff_array = array_diff($mylocations, $excludedLocation) ;
					else
					$diff_array = $mylocations;
				
					
					if($locid=='0')
                       			 {
                           			 $returnshipprice ='0';

                      			 }
		*/
	
			
			
				if($result['shipping_type']==1)
				{
					
					if(!empty($excludedLocation))
					{
						if(in_array($result_delhincrId['value'],$excludedLocation))
						{
							$excludedLocation=array_merge($excludedLocation,$delhincrArray);	
						}
						foreach($excludedLocation as $k=>$val)
						{
						
							if($val['id']==$locid || $val==$locid)
							{
								 return 'error';
								 break;
							}
						}
					}
					$returnshipprice= 0;
				}
				if($result['shipping_type']==2)
				{
					if(!empty($excludedLocation))
					{
						if(in_array($result_delhincrId['value'],$excludedLocation))
						{
							$excludedLocation=array_merge($excludedLocation,$delhincrArray);	
						}
					
						foreach($excludedLocation as $k=>$val)
						{     
						
							if($val['id']==$locid || $val==$locid)
							{
								 return 'error';
								 break;
							}
						}
					}
					$returnshipprice= $result['shipping_pirce'];
				}
				if($result['shipping_type']==3)
				{
				
				if(!empty($excludedLocation))
					{
					if(in_array($result_delhincrId['value'],$excludedLocation))
						{
							$excludedLocation=array_merge($excludedLocation,$delhincrArray);	
						}
						foreach($excludedLocation as $k=>$val)
						{
						
							if($val['id']==$locid || $val==$locid)
							{
								 return 'error';
								 break;
							}
						}
					}	
				
					$excludedLocation=$this->getExcludedcities($result['shipping_id']);
					$result_intersect = array_intersect($delhincrArray, $excludedLocation);
						if(count($result_intersect)==count($delhincrArray))
						{
							array_push($excludedLocation,$result_delhincrId['value']);
						}
		
					/*if($pid==19059)
					{
						echo "<pre>";
						print_r($excludedLocation);
						exit;
					}*/
					$mylocations=$this->getLocationId($pid);
					if(($locid==$result_delhincrId['value'] || in_array($locid,$delhincrArray)) && (in_array($result_delhincrId['value'],$mylocations)))
					{
					
					$mylocations=array_merge($mylocations,$delhincrArray);
						array_unique($mylocations);
					}
					
					if(!empty($excludedLocation))
					$diff_array = array_diff($mylocations, $excludedLocation) ;
					else
					$diff_array = $mylocations;
					
				/*	if($pid==37421)
					{
						echo "<pre>";
						print_r($mylocations);
						
					}*/
				
					
					if($locid=='0')
                       			 {
                           			 $returnshipprice ='0';

                      			 }
                     /*   else if(in_array($locid, $diff_array))
                        {*/
					$priceTocalculate= explode("^",$result['shipping_pirce']);
					if(in_array($locid, $diff_array))
					{
						$returnshipprice= $priceTocalculate[0];
					}
					else
					{
						$returnshipprice= $priceTocalculate[1];
					}
					}
						/*else
					   {
                            $returnshipprice ='error';
			           }*/
		
		//}
		}
              //echo $returnshipprice;exit;
			 if($pid=='19028')
	 {
	 	//echo $locid."_".$returnshipprice;
		//echo "<pre>";
		//print_r($diff_array);
		//exit;
	 }
		 return $returnshipprice;
	}
	public function totalUseCustomerById($couponId,$apikey)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		//echo "SELECT count(*) from coupon_used_by_user where coupon_code='".$couponId."' and buyer_api_key='".$apikey."'";exit;
		  $sql = $db->query("SELECT count(*) as total from coupon_used_by_user where coupon_id='".$couponId."' and buyer_api_key='".$apikey."'");
			$resultSet = $sql->fetchAll();	
			return $resultSet[0];
	}
	public function updatetotalUser($couponId,$apikey)
	{
	$db = Zend_Db_Table::getDefaultAdapter();
		//echo "insert into coupon_used_by_user set  coupon_id='".$couponId."',buyer_api_key='".$apikey."',date_applied=".time();
		//echo "update coupons_detail set redeemed=redeemed+1 where id=".$couponId;exit;
		//echo "insert into coupon_used_by_user set  coupon_id='".$couponId."',buyer_api_key='".$apikey."',date_applied=".time();exit;
		  $sql = $db->query("insert into coupon_used_by_user set  coupon_id='".$couponId."',buyer_api_key='".$apikey."',date_applied=".time());
		$db->query("update coupons_detail set redeemed=redeemed+1 where id=".$couponId);
			
	}	
	public function getGiftCertificateDetail($gccode,$apikey,$emailsession)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		//echo "SELECT * from gift_certificate_recipient as gcr inner join gift_certificate_purchase as gcp  on gcp.id=gcr.gift_purchase_id where gcr.gift_code ='".$gccode."' and recipient_email='".$emailsession."'";exit;
		//echo "SELECT gcr.id as rid,* from gift_certificate_recipient as gcr inner join gift_certificate_purchase as gcp  on gcp.id=gcr.gift_purchase_id where gcr.gift_code ='".$gccode."' and recipient_email='".$emailsession."'";exit;
		
		$sql = $db->query("SELECT *,gcr.id as rid from gift_certificate_recipient as gcr inner join gift_certificate_purchase as gcp  on gcp.id=gcr.gift_purchase_id where gcr.gift_code ='".$gccode."' and gcp.status='1'");
		$resultSet = $sql->fetchAll();	
			return $resultSet[0];
	}
	public function getGiftCertificateDetailCheck($gcid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		//echo "SELECT * from gift_certificate_recipient as gcr inner join gift_certificate_purchase as gcp  on gcp.id=gcr.gift_purchase_id where gcr.gift_code ='".$gccode."' and recipient_email='".$emailsession."'";exit;
		$sql = $db->query("SELECT *,gcr.id as rid from gift_certificate_recipient as gcr inner join gift_certificate_purchase as gcp  on gcp.id=gcr.gift_purchase_id where gcr.id='".$gcid."' " );
		$resultSet = $sql->fetchAll();	
			return $resultSet[0];
	}
	public function getCoupondetail($couponcode)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		    $sql = $db->query("SELECT * from coupons_detail where coupon_code='".$couponcode."' and delete_status='1' and coupon_status='1'");
			$resultSet = $sql->fetchAll();	
//expiration_date>=".time()."
		$data=array();
		if(!empty($resultSet))
{
		if($resultSet[0]['$resultSet']==0 || $resultSet[0]['expiration_date']>=time())
			$data=$resultSet;
}
		

return $data;	
	}

public function getCoupondetailById($couponcode)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
	//echo "SELECT * from coupons_detail where id='".$couponcode."'";exit;
		    $sql = $db->query("SELECT * from coupons_detail where id='".$couponcode."'");
			$resultSet = $sql->fetchAll();	
			return $resultSet;
	}
	public function allCitiesSecure($stateid)
	{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()->from('city')->where("stateid=".$stateid);
			$result = $select->query();
			$resultSet = $result->fetchAll();	
			return $resultSet;
		}
	public function allStatesSecure()
	{
			
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()->from('state')->where("deleted='0'");
			$result = $select->query();
			$resultSet = $result->fetchAll();	
			return $resultSet;
	
	}
	public function allcitySecure($stateid)
	{
			
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()->from('city')->where('stateid='.$stateid);
			$result = $select->query();
			$resultSet = $result->fetchAll();	
			return $resultSet;
	
	}
	public function getDetailAddressByIdSecure($userId,$addressid)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$result = $db->query("select * from address_book as ab left join city as c on  ab.city=c.id left join state as s on s.id=ab.state where customers_id=".$userId." and ab.address_book_id=".$addressid." and ab.deletedflag='0'");
		$resultSet = $result->fetchAll();	
		return $resultSet[0];
	}	
	public function saveaddressSecure($data,$addressid='')
	{
	$db = Zend_Db_Table::getDefaultAdapter();
	if($addressid)
	{
		$result = $db->query("select * from address_book as ab left join city as c on  ab.city=c.id left join state as s on s.id=ab.state where  ab.address_book_id=".$addressid." and ab.deletedflag='0'");
		$resultSet = $result->fetchAll();	
		
	if($resultSet[0]['fullname']!=trim($data['fullname']) || $resultSet[0]['address']!=trim($data['address']) || $resultSet[0]['zipcode']!=trim($data['zipcode']) || $resultSet[0]['city']!=trim($data['city']) || $resultSet[0]['state']!=trim($data['state']) || $resultSet[0]['phone']!=trim($data['phone']) || $resultSet[0]['officeaddress']!=trim($data['officeaddress']))
	{
		$select = $db->insert('address_book',$data);
	}	
	}
	else
	{
		//print_r($data);exit;
		$select = $db->insert('address_book',$data);
	}
	
	}
	public function deleteSecure($addid,$customid)
	{
	$db = Zend_Db_Table::getDefaultAdapter();
	$result = $db->query("update address_book set  deletedflag='1' where address_book_id=".$addid." and customers_id=".$customid);
	}
	public function getShippingAddressDetail($address_id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		if($address_id!='')
		{
			/*$select = $db->select()->from('address_book')->where("address_book_id=".$address_id);
			$result = $select->query();
			$resultSet = $result->fetchAll();
			return $resultSet;*/
			$sql = "select ab.*, c.cityname as cityname, s.state_name as state_name from address_book as ab, city as c, state as s where ab.city=c.id and ab.state=s.id and address_book_id='$address_id' and ab.deletedflag='0'";
			$result = $db->query($sql);
			$resultSet = $result->fetchAll();
			return $resultSet;
		}
	}
	public function paymentMethod($paymentmethod_id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		if($paymentmethod_id!='')
		{
			$select = $db->select()->from('order_payment_modules')->where("pay_mod_id=".$paymentmethod_id);
			$result = $select->query();
			$resultSet = $result->fetchAll();
			return $resultSet;
		}
	}
public function paymentMethodBN($paymentmethod_name)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		if($paymentmethod_name!='')
		{
			$select = $db->select()->from('order_payment_modules')->where("short_desc='".$paymentmethod_name."'");
			$result = $select->query();
			$resultSet = $result->fetchAll();
			return $resultSet[0];
		}
	}
	public function billingAddress($address_id, $fullname, $address, $zipcode, $city, $state, $phone, $officeaddress,$userid,$storeApikeyforrest)
	{
		/*$db = Zend_Db_Table::getDefaultAdapter();
		$sql = "select address_book_id from order_addresses where address_book_id='$address_id' and fullname='$fullname' and address='$address' and zipcode ='$zipcode' and city='$city' and state='$state' and phone='$phone' and officeaddress='$officeaddress' and customer_id='$userid'";
		$results = $db->fetchOne($sql);	
		return $results;*/
		$client = new Zend_Rest_Client('http://orders.o2ocheckout.com');
		$options['api_key']=$storeApikeyforrest;
		$options['method'] = 'exists';
		$options['table'] = 'order_addresses';
		$options['returnField']='order_address_id';
		$options['data']=array('address_book_id'=>$address_id,
					'fullname'=>$fullname,
					'address'=>$address,
					'zipcode' =>$zipcode,
                                        'city'=>$city,
					'state'=>$state,
					'phone'=>$phone,
					'officeaddress'=>$officeaddress,
					'customer_id'=>$userid);
$response = $client->restPost('/api/services/if-exists', $options);


	$d=$this->xml2array($response->getBody());


//exit;

		//echo $id=(string)$xml->response[0];exit;
			
		/*$sql = $db->insert('orders', $data);
		$sql = "select LAST_INSERT_ID() from orders";
		$results = $db->fetchOne($sql);*/
		if($d['exists']['status']=='success')
		return $d['insert']['response'];
		else return 0;
	}
function xml2array($contents, $get_attributes=1, $priority = 'tag') {
 if (!$contents)
        return array();

    if (!function_exists('xml_parser_create')) {
        //print "'xml_parser_create()' function not found!";
        return array();
    }

    //Get the XML parser of PHP - PHP must have this module for the parser to work
    $parser = xml_parser_create('');
    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, trim($contents), $xml_values);
    xml_parser_free($parser);

    if (!$xml_values)
        return; //Hmm...       
//Initializations
    $xml_array = array();
    $parents = array();
    $opened_tags = array();
    $arr = array();

    $current = &$xml_array; //Refference
    //Go through the tags.
    $repeated_tag_index = array(); //Multiple tags with same name will be turned into an array
    foreach ($xml_values as $data) {
        unset($attributes, $value); //Remove existing values, or there will be trouble
        //This command will extract these variables into the foreach scope
        // tag(string), type(string), level(int), attributes(array).
        extract($data); //We could use the array by itself, but this cooler.

        $result = array();
        $attributes_data = array();

        if (isset($value)) {
            if ($priority == 'tag')
                $result = $value;
            else
                $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
        }

        //Set the attributes too.
        if (isset($attributes) and $get_attributes) {
            foreach ($attributes as $attr => $val) {
                if ($priority == 'tag')
                    $attributes_data[$attr] = $val;
                else
                    $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
            }
        }

        //See tag status and do the needed.
        if ($type == "open") {//The starting of the tag '<tag>'
            $parent[$level - 1] = &$current;
            if (!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                $current[$tag] = $result;
                if ($attributes_data)
                    $current[$tag . '_attr'] = $attributes_data;
                $repeated_tag_index[$tag . '_' . $level] = 1;

                $current = &$current[$tag];
            } else { //There was another element with the same tag name
                if (isset($current[$tag][0])) {//If there is a 0th element it is already an array
                    $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                    $repeated_tag_index[$tag . '_' . $level]++;
                } else {//This section will make the value an array if multiple tags with the same name appear together
                    $current[$tag] = array($current[$tag], $result); //This will combine the existing item and the new item together to make an array
                    $repeated_tag_index[$tag . '_' . $level] = 2;

                    if (isset($current[$tag . '_attr'])) { //The attribute of the last(0th) tag must be moved as well
                        $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                        unset($current[$tag . '_attr']);
                    }
                }
                $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
                $current = &$current[$tag][$last_item_index];
            }
        } elseif ($type == "complete") { //Tags that ends in 1 line '<tag />'
            //See if the key is already taken.
            if (!isset($current[$tag])) { //New Key
                $current[$tag] = $result;
                $repeated_tag_index[$tag . '_' . $level] = 1;
                if ($priority == 'tag' and $attributes_data)
                    $current[$tag . '_attr'] = $attributes_data;
            } else { //If taken, put all things inside a list(array)
                if (isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...
                    // ...push the new element into that array.
                    $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;

                    if ($priority == 'tag' and $get_attributes and $attributes_data) {
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                    }
                    $repeated_tag_index[$tag . '_' . $level]++;
                } else { //If it is not an array...
                    $current[$tag] = array($current[$tag], $result); //...Make it an array using using the existing value and the new value
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    if ($priority == 'tag' and $get_attributes) {
                        if (isset($current[$tag . '_attr'])) { //The attribute of the last(0th) tag must be moved as well
                            $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                            unset($current[$tag . '_attr']);
                        }

                        if ($attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                        }
                    }
                    $repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
                }
            }
        } elseif ($type == 'close') { //End of tag '</tag>'
            $current = &$parent[$level - 1];
        }
    }

    return($xml_array);
}

	public function orderAddress($address_id)
	{	
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = "select order_address_id from order_addresses where address_book_id='$address_id'";
		$results = $db->fetchOne($sql);
		return $results;
	}
	public function insertOrderAdd($address_detail,$storeApikeyforrest)
	{	
		$db = Zend_Db_Table::getDefaultAdapter();
//print_r($address_detail);
//exit;

		unset($address_detail['deletedflag']);
	

		$sql = $db->insert('order_addresses', $address_detail);
		$sql = "select LAST_INSERT_ID() from order_addresses";
		$results = $db->fetchOne($sql);

		$client = new Zend_Rest_Client('http://orders.o2ocheckout.com');
		$options['api_key']=$storeApikeyforrest;
		$options['method'] = 'insert';
		$options['table'] = 'order_addresses';
		$options['returnField']='order_address_id';
		$options['data']=$address_detail;
		$response = $client->restPost('/api/services/add-record', $options);

		$d=$this->xml2array($response->getBody());




		/*$sql = $db->insert('order_addresses', $address_detail);
		$sql = "select LAST_INSERT_ID() from order_addresses";
		$results = $db->fetchOne($sql);
		return $results;*/
		if($d['insert']['status']=='success')
		return $d['insert']['response'];
		else return 0;
		//return $results;
	}
	public function insertOrderMapper($customer_id, $orderaddresses_id, $paymentmode_type,$storeApikeyforrest)
	{	
		$db = Zend_Db_Table::getDefaultAdapter();
		$order_place_date = strtotime(date('d-m-Y'));
		$client_ip_address = $_SERVER["REMOTE_ADDR"];
		$data=array(
		'order_coupon_id'=>'0',
		'customer_id'=>$customer_id,
		'order_address_id'=>$orderaddresses_id,
		'ip_addresses'=>"$client_ip_address",
		'payment_status'=>'-1',
		'payment_module'=>$paymentmode_type,
		'order_place_date'=>$order_place_date,
		'payment_recieve_date'=>'0'
		);
		$sql = $db->insert('orders', $data);
		$sql = "select LAST_INSERT_ID() from orders";
		$results = $db->fetchOne($sql);
		$client = new Zend_Rest_Client('http://orders.o2ocheckout.com');
		$options['api_key']=$storeApikeyforrest;
		$options['method'] = 'insert';
		$options['returnField']='order_id';
		$options['table'] = 'orders';
		$options['data']=$data; 
		$response = $client->restPost('/api/services/add-record', $options);
		$d=$this->xml2array($response->getBody());

		if($d['insert']['status']=='success')
		return $d['insert']['response'];
		else return 0;
		return $results;
	}
	public function insertOrderProductDetailMapper($product_id, $prod_variation, $prod_condition, $prod_name, $prod_mrp, $prod_shipping_price, $prod_variation_code,$order_item_total,$cdetail,$formid,$storeApikeyforrest)
	{	
		/*echo $product_id;
		echo "<br />".$prod_condition;
		echo "<br />".$prod_variation;
		echo "<br />".$prod_name;
		echo "<br />".$prod_mrp;
		echo "<br />".$prod_shipping_price;
		echo "<br />".$prod_variation_code;
		exit;*/
		$db = Zend_Db_Table::getDefaultAdapter();
		$sqlForPersible="select policy_type,shipping_id from product_shipping_policy where product_id=".$product_id;
		$fetchPersiable=$db->fetchAll($sqlForPersible);
		if($fetchPersiable[0]['policy_type']==1)
		$persiable='YES';
		else
		$persiable='NO';
		
		$sqlForPersible_id="select handling_time from shipping_method where shipping_id=".$fetchPersiable[0]['shipping_id']." and delete_flag='0'";
		$fetchPersiable_id=$db->fetchOne($sqlForPersible_id);
		
		
		
		
		$sqlFeature="select * from product_feature where product_id=".$product_id;
		$featureProductdata=$db->fetchAll($sqlFeature);
		$featureString=array();
		if(!empty($featureProductdata))
			{
				foreach($featureProductdata as $k=>$v)
					{
						$featureString[$v['feature_name']]=$v['feature_value'];
					}
					
			}
		$fJson=json_encode($featureString);	
		
		$sqlDescriptions="select long_description,short_description from product where id=".$product_id;
		$descriptions=$db->fetchAll($sqlDescriptions);
		
		
		$productMrp=$db->query("select variant_value from product_variation where product_id=".$product_id." and variation_code=".$prod_variation_code."  and variant_name='MRP'");  
		$mrpProduct= $productMrp->fetchAll();
		
		$checkProductVariations=$db->query("select variant_value,variant_name from product_variation where product_id=".$product_id." and variation_code=".$prod_variation_code.""); 
		$productvariationsAll= $checkProductVariations->fetchAll();
		$variationString=array();
		if(!empty($productvariationsAll))
			{
				foreach($productvariationsAll as $ke=>$va)
					{
						$variationString[$va['variant_name']]=$va['variant_value'];
					}
					
			}
			$vJson=json_encode($variationString);	
			
			
			
       $selectBlockTemplate = "select * from  product_image where product_id=" . $product_id . " and image_type='1'";
            $blockTemplateArray = $db->fetchAll($selectBlockTemplate);
            $imagePath = str_replace('http://cache.goo2ostore.com','http://goo2ostore.com', $blockTemplateArray[0]['image_location'] . '/large/' . $blockTemplateArray[0]['image_name']);
	
	$img[]= $imagePath;
	
	
	//echo $imagePath;exit;
	
	//echo $imagePath;
foreach($img as $i){
	$this->save_image($i);
	if(getimagesize(basename($i))){
	 $imagename=basename($i);
		//echo '<h3 style="color: green;">Image ' . basename($i) . ' Downloaded OK</h3>';
	}else{
 $imagename=basename($i);
		//echo '<h3 style="color: red;">Image ' . basename($i) . ' Download Failed</h3>';
	}
}

	
    
		$cdetails=explode("-~^-",$cdetail);
		
		$data=array(
		'pid'=>$product_id,
		'product_variation'=>$prod_variation,
		'product_condition'=>$prod_condition,
		'product_name'=>$prod_name,
		'product_shiped_owner'=>'0',
		'product_mrp'=>$prod_mrp,
		'is_perishable'=>$persiable,
		'product_shipping_price'=>$prod_shipping_price/$order_item_total,
		'product_variation_code'=>$prod_variation_code,
		'customizefields'=>$cdetails[0],
		'product_feature'=>$fJson,
		'product_longdes'=>$descriptions[0]['long_description'],
		'product_shortdes'=>$descriptions[0]['short_description'],
		'original_mrp'=>$mrpProduct[0]['variant_value'],
		'product_variations'=>$vJson,
		'product_image'=>$imagename,
		'formid'=>$formid,
		'date_added'=>time(),
		'handling_time'=>$fetchPersiable_id
		);
		
		
		
     //   echo 'df gdfgdf';exit; 
//echo "select variant_value from product_variation where product_id=".$product_id." and variation_code=".$prod_variation_code."   and variant_value='Stock'";  
		$checkProductQty=$db->query("select variant_value from product_variation where product_id=".$product_id." and variation_code=".$prod_variation_code."   and variant_name='Stock'");  
		$qtyResult=  $checkProductQty->fetchAll();
		$currentQty= $qtyResult[0]['variant_value'];
		
		if($order_item_total<=$currentQty)
		{
		//echo "update  product_variation set variant_value =".($currentQty-$order_item_total)." where product_id=".$product_id." and variation_code=".$prod_variation_code."   and variant_name='Stock'";exit;
			$db->query("update  product_variation set variant_value =".($currentQty-$order_item_total)." where product_id=".$product_id." and variation_code=".$prod_variation_code."   and variant_name='Stock'");  	
		}
else
{
		
			$db->query("update  product_variation set variant_value =0 where product_id=".$product_id." and variation_code=".$prod_variation_code."   and variant_name='Stock'");  	
}



		$sql = $db->insert('order_product_detail', $data);
/*if($product_id==19057)
		
		{
		echo $imagePath ;
			echo "<pre>";
			print_r($data);
			exit;
		}*/
	
               	$client = new Zend_Rest_Client('http://orders.o2ocheckout.com');
		$options['api_key']=$storeApikeyforrest;
		$options['method'] = 'insert';
		$options['table'] = 'order_product_detail';
		$options['returnField']='product_id';
		$options['data']=$data;
		$response = $client->restPost('/api/services/add-record', $options);


		$d=$this->xml2array($response->getBody());

		/*$sql = $db->insert('order_addresses', $address_detail);
		$sql = "select LAST_INSERT_ID() from order_addresses";
		$results = $db->fetchOne($sql);
		return $results;*/
		if($d['insert']['status']=='success')
		return $d['insert']['response'];
		else return 0;
		return $db->lastInsertId();
	}
	public function  save_image($img,$fullpath='basename'){
	if($fullpath=='basename'){
		$fullpath = basename($img);
	}
	$ch = curl_init ($img);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
$fullpath='/home/sketchee/public_html/secure/cart/images/order_images/'.$fullpath;
	$rawdata=curl_exec($ch);
	curl_close ($ch);
	if(file_exists($fullpath)){
		unlink($fullpath);
	}
	$fp = fopen($fullpath,'w');
	
	fwrite($fp, $rawdata);
	

	fclose($fp);
}
	public function insertOrderItemMapper($orders_id, $orderaddresses_id, $order_item_owner, $order_item_total, $productid,$storeApikeyforrest)
	{
		/*echo $orders_id;
		echo "<br />".$orderaddresses_id;
		echo "<br />".$order_item_owner;
		echo "<br />".$order_item_total;
		echo "<br />".$productid;
		exit;*/
                
		$db = Zend_Db_Table::getDefaultAdapter();

		$data=array(
		'order_id'=>$orders_id,
		'product_coupon_id'=>'0',
		'order_address_id'=>$orderaddresses_id,
		'order_item_total'=>$order_item_total,
		'order_shipment_done'=>'0',
		'order_product_detail_id'=>$productid,
		'order_item_owner'=>$order_item_owner,
		'order_item_status'=>'1',
		'order_sub_status_id'=>'1',
		'ocr_id'=>'0',				
		'ocr_details'=>'',				
		'buyer_substatus'=>'28'				
		);
		

		
		$sql = $db->insert('order_item', $data);

		$client = new Zend_Rest_Client('http://orders.o2ocheckout.com');
		$options['api_key']=$storeApikeyforrest;
		$options['method'] = 'insert';
		$options['table'] = 'order_item';
		$options['returnField']='order_item_id';
		$options['data']=$data;
		$response = $client->restPost('/api/services/add-record', $options);


		$d=$this->xml2array($response->getBody());

		/*$sql = $db->insert('order_addresses', $address_detail);
		$sql = "select LAST_INSERT_ID() from order_addresses";
		$results = $db->fetchOne($sql);
		return $results;*/
		if($d['insert']['status']=='success')
		return $d['insert']['response'];
		else return 0;
	}
        public function getDCByNumber($dc_number, $store_api_key)
        {   	
		$store_apikey = implode('\',\'', $store_api_key);
		$db = Zend_Db_Table::getDefaultAdapter();
		$coupon_code = array();
		$sql2 = "SELECT coupon_code from coupons_detail where api_key IN ('$store_apikey') and delete_status = '1' and coupon_status = '1'";
		$result2 = $db->query($sql2);
		$resultSet2 = $result2->fetchAll();
		foreach($resultSet2 as $key=>$val)
		{
			array_push($coupon_code, $val['coupon_code']);
		}
		
		if(in_array($dc_number, $coupon_code))
		{
		$sql = "SELECT * from coupons_detail where coupon_code='$dc_number'";
		$result = $db->query($sql);
		$resultSet = $result->fetchAll();
		$error_msg = array();
		$dc_detail = array();	
		foreach($resultSet as $key=>$val)
			{				
				$flag = 0;
				$expiry_dt = $val['expiration_date'];
				$curr_dt = strtotime(date('d-m-Y'));
				if($curr_dt <= $expiry_dt )
				{
					$flag= 1;
				}
				else
				{
					$flag= 0;
					$error_msg = "Coupon no.:'$dc_number' has expired.";
					continue;
				}

				if($val['redeemed'] < $val['usage_number_val'])
				{
					$flag= 1;
				}
				else
				{
					$flag= 0;	
					$error_msg = "Coupon no.:'$dc_number' has crossed maximum usage limit.";
					continue;
				}
				$api_key = $val['api_key'];
				$dc_amt = $val['discount_amt'];
				$coupon_type = $val['discount_type_id'];
				$dc_detail[$dc_number]=  array('type'=>$coupon_type, 'amt'=>$dc_amt, 'apikey'=>$api_key);
			}
			return $dc_detail;
		}
		else
		{
			$error_msg = "Coupon no.:'$dc_number' does not belong to our store.";
		}
		
	}
        public function UpdateBasketAddId($cust_id, $vcode, $api_key, $pid, $add_id)
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $sql = "update basket set address_book_id = '$add_id' where customer_id='$cust_id' and variationcode='$vcode' and store_api_key ='$api_key' and product_id='$pid' and deletedflag='0'";
            $result = $db->query($sql);
        }

        public function removeBasket($store_apikey, $product_id, $variationcode, $customer_id)
        {
            $db = Zend_Db_Table::getDefaultAdapter();
            $sql = "update basket set deletedflag='1' where customer_id='$customer_id' and variationcode='$variationcode' and store_api_key='$store_apikey' and product_id='$product_id'";
            $result = $db->query($sql);
        }
		 public function getProductShippingPolicyDetail($product_id, $order_item,$storeApikeyforrest) //$options['api_key']=$storeApikeyforrest;
        {   
            $db = Zend_Db_Table::getDefaultAdapter();
            $sql = "select * from product_shipping_policy where product_id='$product_id'";
            $result = $db->query($sql);
            $resultSet = $result->fetchAll();
			
            $resultSet[0]['order_item_id'] =  $order_item;
            $policy_type = $resultSet[0]['policy_type'];
            $default_policy_id = $resultSet[0]['default_policy_id'];
			//echo $product_id;exit;
			/*if($product_id==37429)
			{
				echo $order_item;
				echo "<pre>";
				print_r($resultSet[0]);
				exit;
			}*/
            if($default_policy_id == '0')
            {
			
                $sql = $db->insert('order_shipping_policy', $resultSet[0]);
                $last_id = $db->lastInsertId();
				
                $client = new Zend_Rest_Client('http://orders.o2ocheckout.com');
		$options['api_key']=$storeApikeyforrest;
		$options['method'] = 'insert';
		$options['table'] = 'order_shipping_policy';
		$options['returnField']='id';
		$options['data']=$resultSet[0];
		$response = $client->restPost('/api/services/add-record', $options);


		$d=$this->xml2array($response->getBody());

		
		if($d['insert']['status']=='success')
		return $d['insert']['response'];
		else return 0;
            }
            else if($default_policy_id != '0')
            {
                if($policy_type == '1')
                {
                    $last_id = $this->getPolicyPerishable($default_policy_id, $order_item,$storeApikeyforrest);
                    return $last_id;
                }
                else if($policy_type == '2')
                {
                    $last_id = $this->getPolicyNonperishable($default_policy_id, $order_item,$storeApikeyforrest);
                    return $last_id;
                }
            }
        }

        public function getPolicyPerishable($default_policy_id, $order_item,$storeApikeyforrest)
        {   
            /*echo "perishable";
            echo $default_policy_id;
            echo "<br />";
            echo $order_item;
            exit;*/
            
            $db = Zend_Db_Table::getDefaultAdapter();
            $sql = "select * from policy_perishable where policy_id='$default_policy_id'";
            $result = $db->query($sql);
            $resultSet = $result->fetchAll();
            $resultSet[0]['order_item_id'] =  $order_item;
            $sql = $db->insert('order_shipping_policy', $resultSet[0]);
            $client = new Zend_Rest_Client('http://orders.o2ocheckout.com');
		$options['api_key']=$storeApikeyforrest;
		$options['method'] = 'insert';
		$options['table'] = 'order_shipping_policy';
		$options['returnField']='id';
		$options['data']=$resultSet[0];
		$response = $client->restPost('/api/services/add-record', $options);


		$d=$this->xml2array($response->getBody());

		
		if($d['insert']['status']=='success')
		return $d['insert']['response'];
		else return 0;
        }
        public function getPolicyNonperishable($default_policy_id, $order_item,$storeApikeyforrest)
        {
            /*echo "non perishable";
            echo $default_policy_id;
            echo "<br />";
            echo $order_item;
            exit;*/

            $db = Zend_Db_Table::getDefaultAdapter();
            $sql = "select * from policy_nonperishable where policy_id='$default_policy_id'";
            $result = $db->query($sql);
            $resultSet = $result->fetchAll();
            $resultSet[0]['order_item_id'] =  $order_item;
            $sql = $db->insert('order_shipping_policy', $resultSet[0]);
            $client = new Zend_Rest_Client('http://orders.o2ocheckout.com');
		$options['api_key']=$storeApikeyforrest;
		$options['method'] = 'insert';
		$options['table'] = 'order_shipping_policy';
		$options['returnField']='id';
		$options['data']=$resultSet[0];
		$response = $client->restPost('/api/services/add-record', $options);


		$d=$this->xml2array($response->getBody());

		
		if($d['insert']['status']=='success')
		return $d['insert']['response'];
		else return 0;	
        	}
		public function getstorelocations($apikey)
		{
			
            $db = Zend_Db_Table::getDefaultAdapter();
            $sql = "select 	user_email  from user_emails where support_selected='1' and email_verification='3' and uid=".$apikey."";
            $result = $db->query($sql);
			if(!empty( $result))
				{
					foreach($result as $key=>$val)
					{
						$emails.=$val['user_email'].", ";
					}
				}
			if($emails!='')
			return substr($emails,0,-2);
			else
			return '';	
		}
		public function getstorePhone($apikey)
		{
			
            $db = Zend_Db_Table::getDefaultAdapter();
            $sql = "select 	contact_number  from settings_support where store_id='".$apikey."'";
            $result = $db->query($sql);
			if(!empty( $result))
				{
					foreach($result as $key=>$val)
					{
						$emails.=$val['contact_number'].",";
					}
				}
			if($emails!='')
			return substr($emails,0,-1);
			else
			return '';	
		}
public function billingAddressStorewise($fullname, $address, $zipcode, $city, $state, $phone,$email,$storeapikey)
	{
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = "select address_book_id from order_addresses where address_book_id='$address_id' and fullname='$fullname' and address='$address' and zipcode ='$zipcode' and city='$city' and state='$state' and phone='$phone' and email='$email' and storeapikey='$storeapikey'";
		$results = $db->fetchOne($sql);	
		return $results;
		

	}
public function insertOrderAddStorewise($address_detail)
	{	
		$db = Zend_Db_Table::getDefaultAdapter();

		


		$sql = $db->insert('order_addresses', $address_detail);
		$sql = "select LAST_INSERT_ID() from order_addresses";
		$results = $db->fetchOne($sql);
		return $results;
	}
public function insertOrderMapperStorewise($orderaddresses_id, $paymentmode_type,$email,$storeapikey)
	{	
		$db = Zend_Db_Table::getDefaultAdapter();
		$order_place_date = strtotime(date('d-m-Y'));
		$client_ip_address = $_SERVER["REMOTE_ADDR"];
		$data=array(
		'order_coupon_id'=>'0',
		'storeapikey'=>$storeapikey,
		'email'=>$email,
		'order_address_id'=>$orderaddresses_id,
		'ip_addresses'=>"$client_ip_address",
		'payment_status'=>'-1',
		'payment_module'=>$paymentmode_type,
		'order_place_date'=>$order_place_date,
		'payment_recieve_date'=>'0'
		);
		$sql = $db->insert('orders', $data);
		$sql = "select LAST_INSERT_ID() from orders";
		$results = $db->fetchOne($sql);
		return $results;
	}

public function insertOrderProductDetailMapperStorewise($product_id, $prod_variation, $prod_condition, $prod_name, $prod_mrp, $prod_shipping_price, $prod_variation_code,$order_item_total,$cdetail,$formid)
	{	
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$sqlForPersible="select policy_type,shipping_id from product_shipping_policy where product_id=".$product_id;
		$fetchPersiable=$db->fetchAll($sqlForPersible);
		if($fetchPersiable[0]['policy_type']==1)
		$persiable='YES';
		else
		$persiable='NO';


		
		$sqlForPersible_id="select handling_time from shipping_method where shipping_id=".$fetchPersiable[0]['shipping_id']." and delete_flag='0'";
		$fetchPersiable_id=$db->fetchOne($sqlForPersible_id);
		
		
		
		
		$sqlFeature="select * from product_feature where product_id=".$product_id;
		$featureProductdata=$db->fetchAll($sqlFeature);
		$featureString=array();
		if(!empty($featureProductdata))
			{
				foreach($featureProductdata as $k=>$v)
					{
						$featureString[$v['feature_name']]=$v['feature_value'];
					}
					
			}
		$fJson=json_encode($featureString);	
		
		$sqlDescriptions="select long_description,short_description from product where id=".$product_id;
		$descriptions=$db->fetchAll($sqlDescriptions);
		
		
		$productMrp=$db->query("select variant_value from product_variation where product_id=".$product_id." and variation_code=".$prod_variation_code."  and variant_name='MRP'");  
		$mrpProduct= $productMrp->fetchAll();
		
		$checkProductVariations=$db->query("select variant_value,variant_name from product_variation where product_id=".$product_id." and variation_code=".$prod_variation_code.""); 
		$productvariationsAll= $checkProductVariations->fetchAll();
		$variationString=array();
		if(!empty($productvariationsAll))
			{
				foreach($productvariationsAll as $ke=>$va)
					{
						$variationString[$va['variant_name']]=$va['variant_value'];
					}
					
			}
			$vJson=json_encode($variationString);	
			
			
			
       $selectBlockTemplate = "select * from  product_image where product_id=" . $product_id . " and image_type='1'";
            $blockTemplateArray = $db->fetchAll($selectBlockTemplate);
            $imagePath = str_replace('http://cache.goo2ostore.com','http://goo2ostore.com', $blockTemplateArray[0]['image_location'] . '/large/' . $blockTemplateArray[0]['image_name']);
	
	$img[]= $imagePath;
	
	

foreach($img as $i){
	$this->save_image($i);
	if(getimagesize(basename($i))){
	 $imagename=basename($i);
		//echo '<h3 style="color: green;">Image ' . basename($i) . ' Downloaded OK</h3>';
	}else{
 $imagename=basename($i);
		//echo '<h3 style="color: red;">Image ' . basename($i) . ' Download Failed</h3>';
	}
}

	

		$cdetails=explode("-~^-",$cdetail);
		
		$data=array(
		'pid'=>$product_id,
		'product_variation'=>$prod_variation,
		'product_condition'=>$prod_condition,
		'product_name'=>$prod_name,
		'product_shiped_owner'=>'0',
		'product_mrp'=>$prod_mrp,
		'is_perishable'=>$persiable,
		'product_shipping_price'=>$prod_shipping_price/$order_item_total,
		'product_variation_code'=>$prod_variation_code,
		'customizefields'=>$cdetails[0],
		'product_feature'=>$fJson,
		'product_longdes'=>$descriptions[0]['long_description'],
		'product_shortdes'=>$descriptions[0]['short_description'],
		'original_mrp'=>$mrpProduct[0]['variant_value'],
		'product_variations'=>$vJson,
		'product_image'=>$imagename,
		'formid'=>$formid,
		'date_added'=>time(),
		'handling_time'=>$fetchPersiable_id
		);
		
		
		
     //   echo 'df gdfgdf';exit; 
//echo "select variant_value from product_variation where product_id=".$product_id." and variation_code=".$prod_variation_code."   and variant_value='Stock'";  
		$checkProductQty=$db->query("select variant_value from product_variation where product_id=".$product_id." and variation_code=".$prod_variation_code."   and variant_name='Stock'");  
		$qtyResult=  $checkProductQty->fetchAll();
		$currentQty= $qtyResult[0]['variant_value'];
		
		if($order_item_total<=$currentQty)
		{
		//echo "update  product_variation set variant_value =".($currentQty-$order_item_total)." where product_id=".$product_id." and variation_code=".$prod_variation_code."   and variant_name='Stock'";exit;
			$db->query("update  product_variation set variant_value =".($currentQty-$order_item_total)." where product_id=".$product_id." and variation_code=".$prod_variation_code."   and variant_name='Stock'");  	
		}
else
{
		
			$db->query("update  product_variation set variant_value =0 where product_id=".$product_id." and variation_code=".$prod_variation_code."   and variant_name='Stock'");  	
}


		$sql = $db->insert('order_product_detail', $data);

               	
		return $db->lastInsertId();
	}
public function insertOrderItemMapperStorewise($orders_id, $orderaddresses_id, $order_item_total, $productid)
	{
		
                
		$db = Zend_Db_Table::getDefaultAdapter();

		$data=array(
		'order_id'=>$orders_id,
		'product_coupon_id'=>'0',
		'order_address_id'=>$orderaddresses_id,
		'order_item_total'=>$order_item_total,
		'order_shipment_done'=>'0',
		'order_product_detail_id'=>$productid,
		
		'order_item_status'=>'1',
		'order_sub_status_id'=>'1',
		'ocr_id'=>'0',				
		'ocr_details'=>'',				
		'buyer_substatus'=>'28'				
		);
		

		
		$sql = $db->insert('order_item', $data);

		

		
		$sql = "select LAST_INSERT_ID() from order_item";
		$results = $db->fetchOne($sql);
		return $results;
	}	
 public function getProductShippingPolicyDetailStorewise($product_id, $order_item) //$options['api_key']=$storeApikeyforrest;
        {   
            $db = Zend_Db_Table::getDefaultAdapter();
            $sql = "select * from product_shipping_policy where product_id='$product_id'";
            $result = $db->query($sql);
            $resultSet = $result->fetchAll();
			
            $resultSet[0]['order_item_id'] =  $order_item;
            $policy_type = $resultSet[0]['policy_type'];
            $default_policy_id = $resultSet[0]['default_policy_id'];
			
            if($default_policy_id == '0')
            {
			
                $sql = $db->insert('order_shipping_policy', $resultSet[0]);
                return $last_id = $db->lastInsertId();
				
               
            }
            else if($default_policy_id != '0')
            {
                if($policy_type == '1')
                {
                    $last_id = $this->getPolicyPerishableStorewise($default_policy_id, $order_item);
                    return $last_id;
                }
                else if($policy_type == '2')
                {
                    $last_id = $this->getPolicyNonperishableStorewise($default_policy_id, $order_item);
                    return $last_id;
                }
            }
        }

        public function getPolicyPerishableStorewise($default_policy_id, $order_item)
        {   
            
            
            $db = Zend_Db_Table::getDefaultAdapter();
            $sql = "select * from policy_perishable where policy_id='$default_policy_id'";
            $result = $db->query($sql);
            $resultSet = $result->fetchAll();
            $resultSet[0]['order_item_id'] =  $order_item;
            return $sql = $db->insert('order_shipping_policy', $resultSet[0]);
            
        }
        public function getPolicyNonperishableStorewise($default_policy_id, $order_item)
        {
            

            $db = Zend_Db_Table::getDefaultAdapter();
            $sql = "select * from policy_nonperishable where policy_id='$default_policy_id'";
            $result = $db->query($sql);
            $resultSet = $result->fetchAll();
            $resultSet[0]['order_item_id'] =  $order_item;
            return $sql = $db->insert('order_shipping_policy', $resultSet[0]);
            	
        	}
		
		
}
