<?php
class Default_Model_CartMapper
{
	function getproductname($pid,$apikey,$vcode)
	{
	
			$db = Zend_Db_Table::getDefaultAdapter();
			$selects = "SELECT product_name FROM product WHERE id='".$pid."' and delete_flag='1' and status='1'";
			$results = $db->fetchOne($selects);	
			$userName = new Zend_Session_Namespace('USER');
			$ori = new Zend_Session_Namespace('original_login');
                        
			if($results!='')
			{
			if($ori->userId != '')
			{
			//echo "update basket set product_name='".$results."' where customer_id ='".$userName->userId."' and store_api_key ='".$apikey."' and product_id=".$pid." and variationcode=".$vcode;exit;
			$db->query("update basket set product_name='".$results."' where customer_id ='".$ori->userId."' and store_api_key ='".$apikey."' and product_id=".$pid." and variationcode=".$vcode." and deletedflag='0'");
			}
			}
			else
			{
				if($ori->userId != '')
				{
				//echo "delete from  basket where customer_id ='".$userName->userId."' and store_api_key ='".$apikey."' and product_id=".$pid." and variationcode=".$vcode;exit;
				$db->query("update basket set deletedflag='1' where customer_id ='".$ori->userId."' and store_api_key ='".$apikey."' and product_id=".$pid." and variationcode=".$vcode);
				}
				return '0';
				exit;
			}
			
			return $results;
	}
	public function isformassigned($productid,$formid)
	{
			$db = Zend_Db_Table::getDefaultAdapter();
			$sql="select * from form_assign as fa inner join form_detail as fd on  fa.type_form='1' and fa.type_value=".$productid." and deleted_flag='0' and fa.form_id=".$formid." and fd.status='1' and fd.delete_flag='0' where fa.form_id=fd.form_id";
			$results = $db->fetchAll($sql);	
			if(empty($results))
			return true;
			else
			return false;
	}
	public function isformassignedwishlist($productid,$formid,$vid,$apikey)
	{
			$db = Zend_Db_Table::getDefaultAdapter();
			$ori = new Zend_Session_Namespace('original_login');
			$sql="select * from form_assign as fa inner join form_detail as fd on  fa.type_form='1' and fa.type_value=".$productid." and deleted_flag='0' and fa.form_id=".$formid." and fd.status='1' and fd.delete_flag='0' where fa.form_id=fd.form_id";
			$results = $db->fetchAll($sql);	
			if(empty($results))
			{
			//echo "update wishlist set deletedflag='1' where customer_id ='".$ori->userId."' and store_api_key ='".$apikey."' and product_id=".$productid." and variationcode=".$vid;exit;
			$db->query("update wishlist set deletedflag='1' where customer_id ='".$ori->userId."' and store_api_key ='".$apikey."' and product_id=".$productid." and variationcode=".$vid);
			return true;
			}
			else
			{
			return false;
			
			}
	}
function getproductnamewishlist($pid,$apikey,$vcode)
	{
	
			$db = Zend_Db_Table::getDefaultAdapter();
			$selects = "SELECT product_name FROM product WHERE id='".$pid."' and delete_flag='1' and status='1'";
			$results = $db->fetchOne($selects);	
			$userName = new Zend_Session_Namespace('USER');
			$ori = new Zend_Session_Namespace('original_login');
                        
			if($results!='')
			{
			if($ori->userId != '')
			{
			//echo "update basket set product_name='".$results."' where customer_id ='".$userName->userId."' and store_api_key ='".$apikey."' and product_id=".$pid." and variationcode=".$vcode;exit;
			$db->query("update wishlist set product_name='".$results."' where customer_id ='".$ori->userId."' and store_api_key ='".$apikey."' and product_id=".$pid." and variationcode=".$vcode);
			}
			}
			else
			{
				if($ori->userId != '')
				{
				//echo "delete from  basket where customer_id ='".$userName->userId."' and store_api_key ='".$apikey."' and product_id=".$pid." and variationcode=".$vcode;exit;
				$db->query("update wishlist set deletedflag='1' where customer_id ='".$ori->userId."' and store_api_key ='".$apikey."' and product_id=".$pid." and variationcode=".$vcode);
				}
				return '0';
				exit;
			}
			
			return $results;
	}
	function getproductdetail($pid,$vcode,$apikey,$qty,$srp='')
	{
			//if($qty=='')
                           // return false;
			$db = Zend_Db_Table::getDefaultAdapter();
			$session = new Zend_Session_Namespace('Api_Model_Cart');
			$userName = new Zend_Session_Namespace('USER');
			$ori = new Zend_Session_Namespace('original_login');
			$data=$session->items;
			$selects = "SELECT variant_name,variant_value FROM product_variation WHERE product_id='".$pid."' AND variation_code='".$vcode."'" ;
			$results = $db->fetchAll($selects);
				if(!empty($results))
					{
						$variationstring='';
						foreach($results as $key=>$val)
						{
						
						if($val['variant_name']=='Condition')
						{
							
							$pDetail[$pid]['condition']=$val['variant_value'];
						}
						if($val['variant_name']=='Stock')
						{
							$pDetail[$pid]['stock']=$val['variant_value'];
							
								//$session->items[$apikey."_".$pid]->product_qty=$val['variant_value'];
								//echo $qty."_".$val['variant_value'];exit;
								$session->items[$apikey."_".$pid."_".$vcode]->product_maxqty=$val['variant_value'];
							if($qty>$val['variant_value'] && $val['variant_value']>0)
							{
							
								$session->items[$apikey."_".$pid."_".$vcode]->product_qty=$val['variant_value'];
								$session->items[$apikey."_".$pid."_".$vcode]->product_maxqty=$val['variant_value'];
								if($ori->userId != '')
								{
							$db->query("update  basket set product_qty=".$val['variant_value'].",product_qty=".$val['product_maxqty']." where   customer_id ='".$ori->userId."' and product_id ='".$pid."' and variationcode ='".$vcode."' and store_api_key ='".$apikey."' and  deletedflag='0'");
							}
								
							}
							if($val['variant_value']<=0)
							{
								if($ori->userId != '')
								{
							
								$db->query("update  basket set deletedflag='1' where   customer_id ='".$ori->userId."' and product_id ='".$pid."' and variationcode ='".$vcode."' and store_api_key ='".$apikey."'");
								}
							return '0';
							exit;
							}
						}
						if($val['variant_name']=='SRP')
						{
                                                   // echo $val['variant_value']." --".$srp;exit;
                                                  
							if($srp<$val['variant_value'])
							{
								$pDetail[$pid]['srp']=$val['variant_value'];
								$pDetail[$pid]['error']='-1';	
							}
							else if($srp>$val['variant_value'])
							{
								$pDetail[$pid]['srp']=$val['variant_value'];
								$pDetail[$pid]['error']='-2';	
							}
							$pDetail[$pid]['srp']=$val['variant_value'];
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
        function getproductdetailwishlist($pid,$vcode,$apikey,$qty,$srp='')
	{
			//if($qty=='')
                           // return false;
			$db = Zend_Db_Table::getDefaultAdapter();
			$session = new Zend_Session_Namespace('Api_Model_Cart');
			$userName = new Zend_Session_Namespace('USER');
			$ori = new Zend_Session_Namespace('original_login');
			//$data=$session->items;
			$selects = "SELECT variant_name,variant_value FROM product_variation WHERE product_id='".$pid."' AND variation_code='".$vcode."'" ;
			$results = $db->fetchAll($selects);

				if(!empty($results))
					{
						$variationstring='';
						foreach($results as $key=>$val)
						{

						if($val['variant_name']=='Condition')
						{

							$pDetail[$pid]['condition']=$val['variant_value'];
						}
						if($val['variant_name']=='Stock')
						{

							$pDetail[$pid]['stock']=$val['variant_value'];

							if($qty>$val['variant_value'] && $val['variant_value']>0)
							{

								//$session->items[$apikey."_".$pid]->product_qty=$val['variant_value'];
								//$session->items[$apikey."_".$pid]->product_maxqty=$val['variant_value'];
								if($ori->userId != '')
								{
			if($_GET['test']==1)
{
echo "update  wishlist set product_qty=".$val['variant_value'].",product_qty=".$val['product_maxqty']." where   customer_id ='".$ori->userId."' and product_id ='".$pid."' and variationcode ='".$vcode."' and store_api_key ='".$apikey."'";
echo $ori->userId;
echo "<pre>";
	print_r($results);
 echo 'dfgdfg';exit;
}

							$db->query("update  wishlist set product_qty=".$val['variant_value'].",product_qty=".$val['product_maxqty']." where   customer_id ='".$ori->userId."' and product_id ='".$pid."' and variationcode ='".$vcode."' and store_api_key ='".$apikey."' and deletedflag='0'");
	
							}

							}
							if($val['variant_value']<=0)
							{
							if($ori->userId != '')
							{
							$db->query("update  wishlist set deletedflag='1'  where   customer_id ='".$ori->userId."' and product_id ='".$pid."' and variationcode ='".$vcode."' and store_api_key ='".$apikey."'");
							}
							return '0';
							exit;
							}
						}
						if($val['variant_name']=='SRP')
						{
                                                   // echo "kk".$srp;
                                                   // echo "mm".$val['variant_value'];exit;

							if($srp<$val['variant_value'])
							{
                                                           
								$pDetail[$pid]['srp']=$val['variant_value'];
								$pDetail[$pid]['error']='-1';
							}
							else if($srp>$val['variant_value'])
							{
								$pDetail[$pid]['srp']=$val['variant_value'];
								$pDetail[$pid]['error']='-2';
							}
                                                        $db->query("update  wishlist set product_mrp=".$val['variant_value']."  where   customer_id ='".$ori->userId."' and product_id ='".$pid."' and variationcode ='".$vcode."' and store_api_key ='".$apikey."' and deletedflag='0'");
							$pDetail[$pid]['srp']=$val['variant_value'];
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
	public function updateCartItem($data,$cid,$pId,$apiKey)
	{
			$db = Zend_Db_Table::getDefaultAdapter();
			$ori = new Zend_Session_Namespace('original_login');
			
			if($ori->userId != '')
			{
		   $db->query("update basket set product_name='".$data['product_name']."',product_qty=".$data['product_qty'].",product_maxqty=".$data['product_maxqty'].",product_mrp=".$data['product_mrp'].",product_imagesrc='".$data['product_imagesrc']."',product_shipping=".$data['product_shipping'].",product_url='".$data['product_url']."',product_datemodified=".$data['product_datemodified']." where customer_id =".$cid." AND product_id=".$pId." AND store_api_key='".$apiKey."' and deletedflag='0'");
		   }
	}
	public function shippingBoxDetail($pid,$storeapikey='')
	{
	$db = Zend_Db_Table::getDefaultAdapter();
		$sql=$db->query("SELECT sc.shipping_type,sm.destination,sel.location_id as states,selc.location_id as cities FROM `product_shipping_policy` as psp inner join shipping_cost sc on psp.product_id=".$pid." and sc.`shipping_id`=psp.`shipping_id` inner join  shipping_method as sm on sm.shipping_id=sc.`shipping_id` and sm.delete_flag='0' inner join product as p on p.id=psp.product_id left join shipping_exclude_location as sel on sel.shipping_id=sc.`shipping_id` and sel.location_type='1' left join shipping_exclude_location as selc on selc.shipping_id=sc.`shipping_id` and selc.location_type='2'");
		
		$data=$sql->fetchAll();	
	
	
		$shippingdata=array();
	//echo "select city from store_address where store_id='".$storeapikey."'";
	if($storeapikey){
	  $sellerCitiesSql=$db->query("select city from store_address where store_id='".$storeapikey."' and visible='1'");
	 $sellerCities=$sellerCitiesSql->fetchAll();	
	   }
		if($data[0]['states']!='')
		{
			$explodelocations= explode("^",$data[0]['states']);
			
			if(!empty($explodelocations))
						{
							$statename='';
							foreach($explodelocations as $key=>$val)
								{
									//$explodecity= explode(",",$val);
									
									$statename.=$this->getCityNameFromId($val,'state','state_name').",";
								}
								$statename=substr($statename,0,-1);
							//$shippingdata['excludestates']=substr($statename,0,-1);	
						}
			
		}
		if($data[0]['cities']!='')
		{
			$explodelocationc= explode("^",$data[0]['cities']);
			if(!empty($explodelocationc))
						{
							$citynameexlu='';
							foreach($explodelocationc as $key=>$val)
								{
									$citynameexlu.=$this->getCityNameFromId($val,'city','cityname').", ";
								}
								$citynameexlu=substr($citynameexlu,0,-1);
							//$shippingdata['excludecities']=substr($citynameexlu,0,-1);	
						}
			
		}
		/*if($pid==37421)
		{
			echo "<pre>";
			print_r($sellerCities);
		}*/
		if($data[0]['destination']==1)
			{

				
					if(!empty($sellerCities))
						{
							$cityname='';
							foreach($sellerCities as $key=>$val)
								{
		
								
									$cityname.=$this->getCityNameFromId($val['city'],'city','cityname').", ";
								}
								if($citynameexlu!='' && $statename!='')
									$exlu=$statename.",".$citynameexlu;
									if($citynameexlu!='' && $statename=='')
									$exlu=$citynameexlu;
									if($citynameexlu=='' && $statename!='')
									$exlu=$statename;
									
									$shippingdata['exlu']=$exlu;
							$shippingdata['text']='Shipping within '.substr($cityname,0,-1).' only';
						}
			}
			if($data[0]['destination']==2)
			{
						$shippingdata['text']='Shipping available across india';
						
						if($citynameexlu!='' && $statename!='')
						$exlu=$statename.",".$citynameexlu;
						if($citynameexlu!='' && $statename=='')
						$exlu=$citynameexlu;
						if($citynameexlu=='' && $statename!='')
						$exlu=$statename;
						
						$shippingdata['exlu']=$exlu;	
			}
			return $shippingdata;
		
	
	}
	public function  getCityNameFromId($id,$tablename,$fieldname)
	{
			$db = Zend_Db_Table::getDefaultAdapter();
			$citiname=$db->query("select ".$fieldname." from  ".$tablename." where id=".$id);
			$data=$citiname->fetchAll();
			return $data[0][$fieldname];	
		}
	public function  totalprice()
	{
		$session = new Zend_Session_Namespace('Api_Model_Cart');
		$data=$session->items;
		//echo "<pre>";
		//print_r($data);
		//exit;
		if(!empty($data))
		{
			$price=0;
			foreach($data as $key=>$val)
			{
				//$price = "100";
				$price+=($val->variations[$val->product_id]['srp']*$val->product_qty);
			}
		}

		return $price;		
	}
	public function updatequantity($pid,$apikey,$uid,$qty,$vcode)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = "update basket set product_qty=".$qty." where product_id=".$pid." and store_api_key ='".$apikey."' and customer_id=".$uid." and variationcode=".$vcode." and deletedflag='0'";
		$db->query($sql);
	}
	public function deleteproduct($pid,$apikey,$uid,$vcode)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		//echo "delete from basket  where product_id=".$pid." and store_api_key ='".$apikey."' and customer_id=".$uid." and variationcode=".$vcode;exit;
		$db->query("update basket set  deletedflag='1' where product_id=".$pid." and store_api_key ='".$apikey."' and customer_id=".$uid." and variationcode=".$vcode);
	}
	public function insertWishlist($data)
	{	//echo "reached";exit;
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->query("update basket set  deletedflag='1' where customer_id=".$data['customer_id']." and product_id =".$data['product_id']." and store_api_key='".$data['store_api_key']."' and variationcode=".$data['variationcode']."");	
		$key = $data['store_api_key'];
		$pid = $data['product_id'];
		$cid = $data['customer_id'];
		$vcode = $data['variationcode'];
		$updatedqty = $data['product_qty'];
		$maxqty = $data['product_maxqty'];
		$sql = "select id from wishlist where store_api_key='$key' and product_id='$pid' and customer_id='$cid' and variationcode='$vcode' and deletedflag='0'";
		$status = $db->fetchall($sql);
		if(count($status) >0)
		{
			$sql = "select product_qty from wishlist where store_api_key='$key' and product_id='$pid' and customer_id='$cid' and variationcode='$vcode' and deletedflag='0'";
			$qty = $db->fetchone($sql);
			$newqty = $qty+$updatedqty;
				if($newqty >$maxqty)
				{
					$qty = $maxqty;
				}
				else
				{
					$qty = $newqty;
				}
				$customizefields= $data['customizefields'];
			$sql = "update wishlist set product_qty='$qty',customizefields='".$customizefields."' where store_api_key='$key' and product_id='$pid' and customer_id='$cid' and variationcode='$vcode' and deletedflag='0'";
			$status = $db->query($sql);	
		}
		else
		{
                        
			$status = $db->insert('wishlist',$data);
                        
		}
                //echo $sql = "delete from basket where store_api_key='".$key."' and product_id=".$pid." and customer_id=".$cid." and variationcode=".$vcode."";exit;
                //$db->query($sql);
		return $status;
	}
	public function getWishlist($user_id)
	{
		$db = Zend_Db_Table::getDefaultAdapter();

		$sql = $db->select()->from('wishlist')->where("customer_id='$user_id' and deletedflag='0'");
		$result = $sql->query();
		$resultSet = $result->fetchAll();	
		
                $class=New General();


		if(!empty($resultSet))
		{ 
                    
			$i=0;
                        $errormaeesge=array();
			foreach($resultSet as $key=>$productrec)
			{ 	//echo "<pre>";print_r($productrec);exit;
			
				$proname=$this->getproductnamewishlist($productrec['product_id'],$productrec['store_api_key'],$productrec['variationcode']);
				
				$proid=$productrec['product_id'];
				$proqty=$productrec['product_qty'];
				$promaxqty = $productrec['product_maxqty'];
				$atoreapikey=$productrec['store_api_key'];
				$productrec['store_api_key']=$this->storetitleDefault($productrec['store_api_key']);
				
				if($proname=='0')
				{
				$apikey=$productrec['store_api_key'][0]['title'];
					array_push($errormaeesge, "We're sorry. The item <a href='".$productrec['product_url']."' title='".$productrec['product_name']."' target='_blank'>".$productrec['product_name']."</a> is no longer available to buy on <strong><a href='".$productrec['store_api_key'][0]['mallurl']."' title='".$apikey."' target='_blank'>".$productrec['store_api_key'][0]['title']."</a></strong>. Hence we've removed from your cart.");
					//unset($session->items[$productrec->storeApiKey."_".$productrec->product_id]);
					continue;
				}
				
				if($productrec['customizefields']!='')
					{
						$custodetail=explode("-~^-",$productrec['customizefields']);
							
						
		                if($custodetail[1]!='')
						{
						$checksameform=$this->isformassignedwishlist($productrec['product_id'],$custodetail[1],$productrec['variationcode'],$atoreapikey);
					    }
						if($checksameform)
							{
							 
						$apikey=$productrec['store_api_key'][0]['title'];
						
								array_push($errormaeesge, "Please note that <strong><a href='".$productrec['store_api_key'][0]['mallurl']."' title='".$apikey."' target='_blank'>".$productrec['store_api_key'][0]['title']."</a></strong> has removed the customization option for <a href='".$productrec['product_url']."' title='".$productrec['product_name']."' target='_blank'>".$productrec['product_name']."</a>. Hence we've removed from your cart.");
				//	unset($session->items[$productrec->storeApiKey."_".$productrec->product_id."_".$productrec->variationcode]);
					continue;
							}
							
							
					}
					
				$data[$i]['product_name']=$proname;
                $data[$i]['product_dateadded']=$productrec['product_dateadded'];
				$data[$i]['product_id']=$proid;
				$data[$i]['product_qty']=$proqty;
				$data[$i]['product_maxqty']=$promaxqty;
				$data[$i]['variationcode']=$productrec['variationcode'];

				$variation= $this->getproductdetailwishlist($productrec['product_id'],$productrec['variationcode'],$atoreapikey,$productrec['product_qty'],$productrec['product_mrp']);

				//$variation= $this->mapper->getproductdetail($productrec->product_id,$productrec->variationcode,$productrec->storeApiKey,$productrec->product_qty,$productrec->variations[$productrec->product_id]['srp']);
				//print_r($variation);exit;
				if($variation==0)
				{
					$apikey=$productrec['store_api_key'][0]['title'];
					array_push($errormaeesge, "We're sorry. The item <a href='".$productrec['product_url']."' title='".$productrec['product_name']."' target='_blank'>".$productrec['product_name']."</a> is no longer available to buy on <strong><a href='".$productrec['store_api_key'][0]['mallurl']."' title='".$apikey."' target='_blank'>".$productrec['store_api_key'][0]['title']."</a></strong>. Hence we've removed from your cart.");
					
					
				}
				if($variation[$productrec['product_id']]['error']=='-1')
				{
					array_push($errormaeesge, "Please note that the price of <a href='".$productrec['product_url']."' title='".$productrec['product_name']."' target='_blank'>".$productrec['product_name']."</a> has increased to <b>Rs.".$variation[$productrec['product_id']]['srp']."</b> from <b>Rs.".$productrec['product_mrp']."</b> since you placed it in your Shopping Cart.");

				}
				if($variation[$productrec['product_id']]['error']=='-2')
				{
					array_push($errormaeesge, "Please note that the price of <a href='".$productrec['product_url']."' title='".$productrec['product_name']."' target='_blank'>".$productrec['product_name']."</a> has decreased to <b>Rs.".$variation[$productrec['product_id']]['srp']."</b> from <b>Rs.".$productrec['product_mrp']."</b> since you placed it in your Shopping Cart.");

				}

				$data[$i]['shippingbox']=$this->shippingBoxDetail($productrec['product_id'],$atoreapikey);	
				
				$data[$i]['product_url']=$productrec['product_url'];
				$data[$i]['storeApiKey']=$productrec['store_api_key'];

				$data[$i]['store_api_key']=$this->storetitleDefault($atoreapikey);
				$data[$i]['variations']=$variation;

				$image=$class->getImageFromDir($productrec['product_id'],'product','cart','1');

				$data[$i]['productImageSrc']=$image[0];
				$data[$i]['customizefields']=$productrec['customizefields'];
			$i++;
			}
			
		}
                
                $returnarray['data']=$data;
                $returnarray['error']=$errormaeesge;
		return $returnarray;
	}
	public function deleteWishlist($product_id,$storeapi_key,$customer_id,$variation_code)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$wishlist = $this->getSingleWishlist($product_id,$storeapi_key,$customer_id,$variation_code);
		if(!empty($wishlist))
		{
			$sql = "update wishlist set deletedflag='1' where store_api_key = '$storeapi_key' and product_id='$product_id' and customer_id='$customer_id' and variationcode='$variation_code'";
			$result = $db->query($sql);
		}
		return $wishlist;
	}
	public function getSingleWishlist($product_id,$storeapi_key,$customer_id,$variation_code)
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		$selects = "SELECT * FROM wishlist where store_api_key = '$storeapi_key' and product_id='$product_id' and customer_id='$customer_id' and variationcode='$variation_code' and deletedflag='0'";
		$results = $db->fetchAll($selects);	
		return $results;
	}
	public function storetitleDefault($storeurl)
    {
	
		$db = Zend_Db_Table::getDefaultAdapter();
		//echo "select *  from mall_detail as md inner join  username as u  on md.user_id=u.id and m.apikey='".$storeurl."'";
		//echo 'dfgfdg';exit;
		$sql = "select *,md.id as mallid from mall_detail as md left join user_role as ur on md.apikey=ur.store_apikey and ur.role='2' join user as un on ur.email_id=un.user_email_address where ur.store_apikey='".$storeurl."'";
		$select =$db->query($sql);
		
		$resultSet = $select->fetchAll();
//echo "<pre>";
//print_r($resultSet);
		//exit;	
		return $resultSet;
    }
	public function updateWishlist($pid,$uid,$vcode,$qty,$api_key)
	{	
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = "update wishlist set product_qty='$qty' where product_id='$pid' and store_api_key ='$api_key' and variationcode='$vcode' and customer_id='$uid' and deletedflag='0'";
		$db->query($sql);
	}
}
