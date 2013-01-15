<?php

class Default_Model_InboxMapper extends DML {

    private $buyerMapper;

    function __construct() {
        parent::__construct();
        $this->buyerMapper = new Myaccount_Model_BuyerMapper();
        //$this->db = Zend_Db_Table::getDefaultAdapter();
    }

    function getConversation($current='', $data='',$inboxtype='') {
        $userName = new Zend_Session_Namespace('USER');
        $db = Zend_Db_Table::getDefaultAdapter();
        $addSql = '';
        $addfilterSql='';
        //echo $inboxtype;exit;
        if($inboxtype!='')
        $addfilterSql=" and c.message_type IN (".$inboxtype.")";
        if($inboxtype=="0")
        {
          $addfilterSql=" and c.stared IN ('".$userName->userId."','-1')";
        }
	//echo "SELECT c.id,c.i_id,c.comment,c.message_type,c.request_text,c.order_item_id,c.user1 as user1id,c.user2 as user2id,userfir.user_full_name  as user1name,usersec.user_full_name  as user2name,c.guestname,c.id FROM chat AS c LEFT JOIN user AS userfir ON convert(c.user1,char)=convert(userfir.id,char) LEFT JOIN user AS usersec ON convert(c.user2,char)=convert(usersec.id,char) where (c.user1 =" . $userName->userId . " || c.user2 =" . $userName->userId . ")".  $addfilterSql;exit;
        $allChat = $db->query("SELECT c.readby,c.modified_date,c.stared,c.id,c.i_id,c.comment,c.message_type,c.request_text,c.request_status,c.alert_to,c.order_item_id,c.user1 as user1id,c.user2 as user2id,userfir.user_full_name  as user1name,usersec.user_full_name  as user2name,userfir.user_email_address  as user1email,usersec.user_email_address  as user2email,c.guestname,c.id FROM chat AS c LEFT JOIN user AS userfir ON convert(c.user1,char)=convert(userfir.id,char) LEFT JOIN user AS usersec ON convert(c.user2,char)=convert(usersec.id,char) where c.message_type='4' and (c.user1 =" . $userName->userId . " || c.user2 =" . $userName->userId . ")".  $addfilterSql);
        $results = $allChat->fetchAll();

        $chatData = array();
        if (!empty($results)) {
            $i = 0;
            foreach ($results as $chatIndx => $chatval) {
              // echo $current.'<br>';
                 //echo $chatval['modified_date'].'<br>';
                  //echo $current.'<br>';
               // exit;
                 if(($chatval['message_type']==0) || ($chatval['message_type']==1))
                {
                if ($current != '')
                    $addsql = " and sent_time>" . $current;
                //echo "select * from chat_message where chat_id=".$chatval['id']." and deleted!='".$userName->userId."' and deleted!=-1 ". $addsql;exit;
                $chatdetail = $db->query("select * from chat_message where chat_id=" . $chatval['id'] . " and deleted!='" . $userName->userId . "' and deleted!=-1 " . $addsql);
                $resultschat = $chatdetail->fetchAll();

               if (empty($resultschat) && (($chatval['message_type']==0) || ($chatval['message_type']==1)) )
                   continue;
                 
                

                $chatData[$i]['time'] = $this->getNewTimeFormat($resultschat[count($resultschat) - 1]['sent_time']);
                $chatData[$i]['times'] = $resultschat[count($resultschat) - 1]['sent_time'];
                }
                if(($chatval['message_type']==2))
                {
                if ($current != '')
                {     
                if($chatval['modified_date']< $current)
                {
                 continue;
                }
                }   
                    
                $commentDetail = $db->query("select message from request_messages where request_id=" . $chatval['i_id']." order by request_message_id desc limit 0,1");
                $resultschatrequest = $commentDetail->fetchAll();
                 $chatData[$i]['requestdetail'] = $resultschatrequest;
                 $chatData[$i]['time'] = $this->getNewTimeFormat($chatval['modified_date']);
                $chatData[$i]['times'] = $chatval['modified_date'];
                   
               }
                if(($chatval['message_type']==3))
               {
                if ($current != '')
                {     
                if($chatval['modified_date']< $current)
                {
                 continue;
                }
                }     
                $commentDetaildispute = $db->query("select message from dispute_messages where dispute_id=" . $chatval['i_id']." order by disputes_message_id  desc limit 0,1");
                $resultschatdispute = $commentDetaildispute->fetchAll();
                $chatData[$i]['disputedetail'] = $resultschatdispute;
                 $chatData[$i]['time'] = $this->getNewTimeFormat($chatval['modified_date']);
                $chatData[$i]['times'] = $chatval['modified_date'];

               }
                 if(($chatval['message_type']==4))
               {
			   		if($chatval['alert_to']==$userName->userId)
					continue;
                if ($current != '')
                {     
                if($chatval['modified_date']< $current)
                {
                 continue;
                }
                }   
                $chatData[$i]['time'] = $this->getNewTimeFormat($chatval['modified_date']);
                $chatData[$i]['times'] = $chatval['modified_date'];

               }
                //echo "select * from chat_message where `to`='".$userName->userId."' and chat_id=".$chatval['id']." and readed='0' and deleted!='".$userName->userId."' and deleted!=1";exit;
                $readunread = $db->query("select * from chat_message where `to`='" . $userName->userId . "' and chat_id=" . $chatval['id'] . " and readed='0' and deleted!='" . $userName->userId . "' and deleted!=-1");
                $resultsreaded = $readunread->fetchAll();
                //print_r()
               
                if (empty($resultsreaded)) {
                    $readunreadto = $db->query("select * from chat_message where  chat_id=" . $chatval['id'] . " and readed='0' and deleted!='" . $userName->userId . "' and deleted!=-1");
                    if (!empty($resultsreaded))
                        $chatData[$i]['readed'] = 0;
                }

                $chatData[$i]['chatdetail'] = $resultschat;
               	$genObj=new General();
                if ($chatval['user1name'] == '') {
                    $valuefirst = $chatval['guestname'];
                } else {
                    $valuefirst = $chatval['user1name'];
                }
                if ($chatval['user2name'] == '') {
                    $valuesec = $chatval['guestname'];
                } else {
                    $valuesec = $chatval['user2name'];
                }
                if ($userName->userId == $chatval['user1id']) {
			
                    $chatData[$i]['to'] = $valuesec;
					$emailForRole=$chatval['user2email'];
		    $chatData[$i]['image']= $genObj->getuserimageSrc($chatval['user2id'],36,36,'small',$title=0);	
		   	
                } else {
                    $chatData[$i]['to'] = $valuefirst;
		     $chatData[$i]['image']= $genObj->getuserimageSrc($chatval['user1id'],36,36,'small',$title=0);	
			 		$emailForRole=$chatval['user1email'];
                }
				$chatData[$i]['role'] = $this->getuserrole($emailForRole);
                $chatData[$i]['id'] = $chatval['id'];
                $chatData[$i]['messagetype'] = $chatval['message_type'];
		        $chatData[$i]['request_status'] = $chatval['request_status'];
                $chatData[$i]['request_text'] = $chatval['request_text'];
                $chatData[$i]['order_item_id'] = $chatval['order_item_id'];
                $chatData[$i]['comment'] = $chatval['comment'];
                $chatData[$i]['i_id'] = encryptLink($chatval['i_id']);
                $chatData[$i]['readby'] = $chatval['readby'];
                $chatData[$i]['stared'] = $chatval['stared'];



                $i++;
            }
        }
        //$chatData['time']=time();
        $Class=new General();
        $da = $Class->array_sort($chatData, 'times');
        //ksort($da);
        /* echo "<pre>";
          print_r($da);
          exit; */

        return $da;
    }
	
	

 function getuserrole($mymail)
        {
					if($mymail=='')
					return 0;
                    $db = Zend_Db_Table::getDefaultAdapter();
                    $selects = "SELECT store_apikey,role_name,title FROM user_role left join mall_detail on mall_detail.apikey=user_role.store_apikey where email_id='$mymail' AND status=1 AND deleted_flag=0";
                    $results = $db->fetchAll($selects);
					if(!empty($results))
					{
						foreach($results as $key => $value){
							if($value['title']!='')
                            $rname.=$value['role_name'].' at '.$value['title'].', ';
                        }
						return substr($rname,0,-2);	
					}
					else
					{
						return 0;	
					}
		   
        }
    function getConversationupdater($current='', $data='',$inboxtype='') {
        if($current=='undefined')
        $current=time();
       // echo $current;exit;
        $userName = new Zend_Session_Namespace('USER');
        $db = Zend_Db_Table::getDefaultAdapter();
        $addSql = '';
        $addfilterSql='';
        //echo $inboxtype;exit;
        if($inboxtype!='')
        $addfilterSql=" and c.message_type IN (".$inboxtype.")";
        if($inboxtype=="0")
        {
          $addfilterSql=" and c.stared IN ('".$userName->userId."','-1')";
        }
	//echo "SELECT c.id,c.i_id,c.comment,c.message_type,c.request_text,c.order_item_id,c.user1 as user1id,c.user2 as user2id,userfir.user_full_name  as user1name,usersec.user_full_name  as user2name,c.guestname,c.id FROM chat AS c LEFT JOIN user AS userfir ON convert(c.user1,char)=convert(userfir.id,char) LEFT JOIN user AS usersec ON convert(c.user2,char)=convert(usersec.id,char) where (c.user1 =" . $userName->userId . " || c.user2 =" . $userName->userId . ")".  $addfilterSql;exit;
        $allChat = $db->query("SELECT c.readby,c.modified_date,c.stared,c.id,c.i_id,c.comment,c.message_type,c.request_text,c.request_text,c.order_item_id,c.user1 as user1id,c.user2 as user2id,userfir.user_full_name  as user1name,usersec.user_full_name  as user2name,userfir.user_email_address  as user1email,usersec.user_email_address  as user2email,c.guestname,c.id FROM chat AS c LEFT JOIN user AS userfir ON convert(c.user1,char)=convert(userfir.id,char) LEFT JOIN user AS usersec ON convert(c.user2,char)=convert(usersec.id,char) where  c.message_type='4' and  (c.user1 =" . $userName->userId . " || c.user2 =" . $userName->userId . ")".  $addfilterSql);
      
        $results = $allChat->fetchAll();
        
        $chatData = array();
        if (!empty($results)) {
            $i = 0;
            foreach ($results as $chatIndx => $chatval) {
              // echo $current.'<br>';
                 //echo $chatval['modified_date'].'<br>';
                  //echo $current.'<br>';
               // exit;
                 if(($chatval['message_type']==0) || ($chatval['message_type']==1))
                {
                if ($current != '')
                    $addsql = " and sent_time>" . $current;
                
               
                //echo "select * from chat_message where chat_id=".$chatval['id']." and deleted!='".$userName->userId."' and deleted!=-1 ". $addsql;exit;
               // echo "select * from chat_message where  chat_id=" . $chatval['id'] . " and `to`=''"." . $userName->userId . "."' and deleted!='" . $userName->userId . "' and deleted!=-1 " . $addsql;exit;
                $chatdetail = $db->query("select * from chat_message where  chat_id=" . $chatval['id'] . " and `to`='".  $userName->userId ."' and deleted!='" . $userName->userId . "' and deleted!=-1 " . $addsql);
                $resultschat = $chatdetail->fetchAll();

               if (empty($resultschat) && (($chatval['message_type']==0) || ($chatval['message_type']==1)) )
                   continue;
                 
                

                $chatData[$i]['time'] = $this->getNewTimeFormat($resultschat[count($resultschat) - 1]['sent_time']);
                $chatData[$i]['times'] = $resultschat[count($resultschat) - 1]['sent_time'];
                }
                if(($chatval['message_type']==2))
                {
                if ($current != '')
                {     
                if($chatval['modified_date']< $current)
                {
                 continue;
                }
                }   
                    
                $commentDetail = $db->query("select message from request_messages where request_id=" . $chatval['i_id']." order by request_message_id desc limit 0,1");
                $resultschatrequest = $commentDetail->fetchAll();
                 $chatData[$i]['requestdetail'] = $resultschatrequest;
                 $chatData[$i]['time'] = $this->getNewTimeFormat($chatval['modified_date']);
                $chatData[$i]['times'] = $chatval['modified_date'];
                   
               }
                if(($chatval['message_type']==3))
               {
                if ($current != '')
                {     
                if($chatval['modified_date']< $current)
                {
                 continue;
                }
                }     
                $commentDetaildispute = $db->query("select message from dispute_messages where dispute_id=" . $chatval['i_id']." order by disputes_message_id  desc limit 0,1");
                $resultschatdispute = $commentDetaildispute->fetchAll();
                $chatData[$i]['disputedetail'] = $resultschatdispute;
                 $chatData[$i]['time'] = $this->getNewTimeFormat($chatval['modified_date']);
                $chatData[$i]['times'] = $chatval['modified_date'];

               }
                 if(($chatval['message_type']==4))
               {
                if ($current != '')
                {     
                if($chatval['modified_date']< $current)
                {
                 continue;
                }
                }   
                $chatData[$i]['time'] = $this->getNewTimeFormat($chatval['modified_date']);
                $chatData[$i]['times'] = $chatval['modified_date'];

               }
                //echo "select * from chat_message where `to`='".$userName->userId."' and chat_id=".$chatval['id']." and readed='0' and deleted!='".$userName->userId."' and deleted!=1";exit;
                $readunread = $db->query("select * from chat_message where `to`='" . $userName->userId . "' and chat_id=" . $chatval['id'] . " and readed='0' and deleted!='" . $userName->userId . "' and deleted!=-1");
                $resultsreaded = $readunread->fetchAll();
                //print_r()
               
                if (empty($resultsreaded)) {
                    $readunreadto = $db->query("select * from chat_message where  chat_id=" . $chatval['id'] . " and readed='0' and deleted!='" . $userName->userId . "' and deleted!=-1");
                    if (!empty($resultsreaded))
                        $chatData[$i]['readed'] = 0;
                }

                $chatData[$i]['chatdetail'] = $resultschat;
               
                if ($chatval['user1name'] == '') {
                    $valuefirst = $chatval['guestname'];
                } else {
                    $valuefirst = $chatval['user1name'];
                }
                if ($chatval['user2name'] == '') {
                    $valuesec = $chatval['guestname'];
                } else {
                    $valuesec = $chatval['user2name'];
                }
                if ($userName->userId == $chatval['user1id']) {
                    $chatData[$i]['to'] = $valuesec;
					$emailForRole=$chatval['user2email'];
		    $chatData[$i]['image']= $genObj->getuserimageSrc($chatval['user2id'],36,36,'small',$title=0);	
					
                } else {
                    $chatData[$i]['to'] = $valuefirst;
		     		$chatData[$i]['image']= $genObj->getuserimageSrc($chatval['user1id'],36,36,'small',$title=0);	
			 		$emailForRole=$chatval['user1email'];
                }
				$chatData[$i]['role'] = $this->getuserrole($emailForRole);
                $chatData[$i]['id'] = $chatval['id'];
                $chatData[$i]['messagetype'] = $chatval['message_type'];
                $chatData[$i]['request_text'] = $chatval['request_text'];
                $chatData[$i]['order_item_id'] = $chatval['order_item_id'];
                $chatData[$i]['comment'] = $chatval['comment'];
                $chatData[$i]['i_id'] = encryptLink($chatval['i_id']);
                $chatData[$i]['readby'] = $chatval['readby'];
                $chatData[$i]['stared'] = $chatval['stared'];



                $i++;
            }
        }
        //$chatData['time']=time();
        $Class=new General();
        $da =array();
        $da = $Class->array_sort($chatData, 'times');
        //ksort($da);
        /* echo "<pre>";
          print_r($da);
          exit; */

        return $da;
    }
    function suggestusername($term)
    {
        $userName = new Zend_Session_Namespace('USER');
        $db = Zend_Db_Table::getDefaultAdapter();
        //echo "select * from user where user_full_name like='%".$term."' order by user_full_name ASC limit 0,10";exit;
       // echo "select * from user where user_full_name like '".$term."%' and id not in(".$userName->userId.") order by user_full_name ASC limit 0,10";exit;
        $selectQuery=$db->query("select * from user where user_full_name like '".$term."%' and id not in(".$userName->userId.") order by user_full_name ASC limit 0,10");
        
        $data=$selectQuery->fetchAll();
        //print_r( $data);
        //exit;
         $autoSuggestion=array();
	$classobj=new General();
        if(!empty($data))
        {

            foreach($data as $key=>$val)
            { 
	//echo strstr($val['user_email_address'], '-', true);
              if(strpos($val['user_email_address'], '-', 1)>0)
		continue;
              $locations=explode(",",$val['user_location']);  
              $staeSql=$db->query("select state_name from state where id=".$locations[0]);
              $statequery= $staeSql->fetchAll();
              $statename= $statequery[0]['state_name'];
              // $citySql=$db->query("select cityname from cities where id=".$locations[0]);
              //$cityquery=  $citySql->fetchAll();
                $cityname= $locations[1];
		$image=$classobj->getuserimageSrc($val[id],26,26,'small',0,1);

		
              $autoSuggestion[]=array('id'=>$val['id'],'user_image'=>$image,'image'=>'<div class="floatLeft"><img src="'.$image.'"  height="26" weidth="26" style="padding:6px;"></div>','label'=>'<span class="floatLeft"><span class="fwBold">'.$val['user_full_name'].'</span></span>','location'=>'<br /><span class="popaddress">'.$cityname.', '.$statename.'</span>');  
			 //  $autoSuggestion[]=array('id'=>$val['id'],'image'=>'<span class=""><img src="/images/secure/user_image/'.$val['user_image'].'"  height="36" weidth="36"></span>','label'=>str_ireplace($term,'<span class="match">'.$term.'</span>',$val['user_full_name'])); 
            }
            return  $autoSuggestion;
        }
    }
    function getUnreadmessage()
    {

        $userName = new Zend_Session_Namespace('USER');
        $db = Zend_Db_Table::getDefaultAdapter();
      //  echo "select count(id) as total,message_type from chat where (user1=".$userName->userId." || user2=".$userName->userId.") and and readby!=-1 and readby!=".$userName->userId." group by message_type  ";exit;
        $sql=$db->query("select count(id) as total,message_type from chat where (user1=".$userName->userId." || user2=".$userName->userId.") and  readby!=-1 and readby!=".$userName->userId." and message_type='4' group by message_type");
        $results = $sql->fetchAll();
        $offline=0;
        $online=0;
        $dispute=0;
        $request=0;
        $alert=0;
        $j = 0;
        if(!empty( $results))
        {
            foreach($results as $key=>$val)
            {
              
                if($val['message_type']==0)
                $offline= $val['total'];
                if($val['message_type']==1)
                $online= $val['total'];
                if($val['message_type']==2)
                $request= $val['total'];
                if($val['message_type']==3)
                $dispute= $val['total'];
                if($val['message_type']==4)
                $alert= $val['total'];
            }
        }
        $unread=array();
        //echo "SELECT * from chat  as c where (c.user1 =" . $userName->userId . " || c.user2 =" . $userName->userId . ") and c.readby!-1 and c.readby!=" . $userName->userId . "";exit;
        $allChat = $db->query("SELECT * from chat  as c where (c.user1 =" . $userName->userId . " || c.user2 =" . $userName->userId . ") and c.readby!=-1 and c.readby!=" . $userName->userId . " and c.message_type IN (4)");
        $results = $allChat->fetchAll();
        
        if (!empty($results)) {
            $j = 0;
            foreach ($results as $chatIndx => $chatval) {
                $resultschat=array();
                //echo "select * from chat_message where chat_id=".$chatval['id']." and deleted!='".$userName->userId."' and deleted!=-1 ". $addsql;exit;
                $chatdetail = $db->query("select * from chat_message where chat_id=" . $chatval['id'] . " and deleted!='" . $userName->userId . "' and deleted!=-1 " . $addsql);
                $resultschat = $chatdetail->fetchAll();
        

               if ((empty($resultschat)) )
               {
                  //echo $j;
                   continue;
               }else{

            //echo $j;
               $j=$j+1;
               }
            }
        }
        $unread['chat']= $j;
        $unread['dispute']=$dispute;
        $unread['request']=$request;
        $unread['alert']=$alert;
        $unread['total']=$dispute+$request+$alert;
        return  $unread;
    }
    function updateBookMark($chatid,$update)
    {
         $db = Zend_Db_Table::getDefaultAdapter();
         $userName = new Zend_Session_Namespace('USER');
         $selectQuery=$db->query("select stared,user1,user2 from chat where id=".$chatid);
         $dataPrev=$selectQuery->fetchAll();
         if($dataPrev[0]['user1']==$userName->userid)
         $otheruser=$dataPrev[0]['user2'];
         else
         $otheruser=$dataPrev[0]['user1'];
         if($update==1)
         {
             if($dataPrev[0]['stared']==0)
             $update=$userName->userId;
             if($dataPrev[0]['stared']==-1)
             $update='-1';
             if($dataPrev[0]['stared']==$otheruser)
             $update= '-1';
             if($dataPrev[0]['stared']== $userName->userId)
             $update=$userName->userId;
         }
         if($update==0)
         {
             if($dataPrev[0]['stared']==0)
             $update=0;
             if($dataPrev[0]['stared']==-1)
             $update= $otheruser;
             if($dataPrev[0]['stared']==$otheruser)
             $update= $otheruser;
             if($dataPrev[0]['stared']== $userName->userId)
             $update=0;
         }
         $db->query("update chat set stared='".$update."' where id=".$chatid);
        
    }

    function getNewTimeFormat($getTime) {
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
                $sendTime = date("jS F, Y", $getTime);
            }
        } else {
            $sendTime = "Not Revealed";
        }
        
        return $sendTime;
    }

    function getConversationDetail($chatid) {
        $userName = new Zend_Session_Namespace('USER');
        $db = Zend_Db_Table::getDefaultAdapter();
        //$allMessage = $db->query("SELECT cm.chat_id, cm.message, cm.sent_time, c.guestname, c.guestemail FROM chat_message AS cm LEFT JOIN chat AS c on chat_id=".$chat_id."");
        //echo "SELECT c.id, cm.chat_id, cm.from, cm.to, cm.message, cm.sent_time, userfrom.id AS userid, userfrom.user_email_address FROM chat_message AS cm LEFT JOIN chat AS c ON c.id = cm.chat_id and cm.chat_id=".$chatid."  LEFT JOIN user AS userfrom ON userfrom.id = cm.from LEFT JOIN user AS userto ON userto.id = cm.to";exit;
        //echo "SELECT cm.chat_id, cm.from, cm.to, cm.message, cm.sent_time, u1.user_full_name as userfrom,u2.user_full_name as userto, c.guestname FROM chat as c  join chat_message as cm on cm.chat_id=c.id and cm.chat_id=".$chatid." left  join user as u1 on convert(u1.id,char)=convert(cm.from,char) left  join user as u2 on convert(u2.id,char)=convert(cm.to,char) and cm.chat_id=".$chatid."";exit;
        //$db->query("update chat_message set readed='1' where chat_id=" . $chatid . " and `to`=" . $userName->userId);
     //  echo "SELECT cm.chat_id, cm.from, cm.to, cm.message, cm.sent_time, u1.user_full_name AS userfrom, u2.user_full_name AS userto, c.guestname, c.guestemail,u1.user_email_address as useremail, u1.user_full_name as username, u1.user_location FROM chat AS c JOIN chat_message AS cm ON cm.chat_id = c.id AND cm.chat_id =" . $chatid . " and cm.deleted!='-1' and cm.deleted!=" . $userName->userId . " LEFT JOIN user AS u1 ON convert( u1.id, char ) = convert( cm.from, char ) LEFT JOIN user AS u2 ON convert( u2.id, char ) = convert( cm.to, char ) AND cm.chat_id =" . $chatid . "";exit;
        $allMessage = $db->query("SELECT cm.chat_id, cm.from, cm.to, cm.message, cm.sent_time, u1.user_full_name AS userfrom, u2.user_full_name AS userto, c.guestname, c.guestemail,u1.user_email_address as useremail, u1.user_full_name as username, u1.user_location FROM chat AS c JOIN chat_message AS cm ON cm.chat_id = c.id AND cm.chat_id =" . $chatid . " and cm.deleted!='-1' and cm.deleted!=" . $userName->userId . " LEFT JOIN user AS u1 ON convert( u1.id, char ) = convert( cm.from, char ) LEFT JOIN user AS u2 ON convert( u2.id, char ) = convert( cm.to, char ) AND cm.chat_id =" . $chatid . "");
        $results = $allMessage->fetchAll();
	$genObj=new General();	
        //echo '<pre>';
      // print_r($results);
       
        if( $results[0]['from']== $userName->userId)
        $otherUserid=$results[0]['to'];
        else
        $otherUserid=$results[0]['from'];
       // echo $otherUserid;exit;
       //echo "select * from user where id='".$otherUserid."'";exit;
        $checkUserAsRegistered=$db->query("select * from user where convert(id,char)='".$otherUserid."'");
        $userdetail=$checkUserAsRegistered->fetchAll();
       // print_r($userdetail);
       
        if(empty( $userdetail))
        {
            $othername=$results[0]['guestname'];
            $otheremail=$results[0]['guestemail'];
	    $image= $genObj->getuserimageSrc(0,36,36,'small',$title=0);	
            $otherlocation=0;
            $asguest='1';
        }
        else
        {
            $othername=$userdetail[0]['user_full_name'];
            $otheremail=$userdetail[0]['user_email_address'];
	    $image= $genObj->getuserimageSrc($userdetail[0]['id'],36,36,'small',$title=0);
	    $locatiion=explode(",",$userdetail[0]['user_location']);				
            $otherlocation= $locatiion[1];
            $asguest='0';
            
        }
        $userdetail['name']=$othername;
	$userdetail['image']=$image;
        $userdetail['email']=$otheremail;
        $userdetail['location']=$otherlocation;
        $userdetail['guest']=$asguest;
        if (!empty($results)) {
            $message = '';
            $create = '';
            $i = 0;
            $j = 1;
            $data = array();
	    
            foreach ($results as $key => $val) {
                if ($create == $val['from'] || $create == '') {
					$time = $val['sent_time'];
                    $message.= '<div class="maintxt">'.$val['message'] .'</div><div class="date_text"  style="float:left; width:110px; text-align:right; padding-bottom:15px;">'.$this->getNewTimeFormat($time).'</div>';
                    $from = $val['from'];
		    $image = $genObj->getuserimageSrc($val['from'],36,36,'small',$title=0);	
                    $userfrom = $val['userfrom'];
                    $userto = $val['userto'];
                    $guestname = $val['guestname'];
                    $guestemnail = $val['guestemail'];
                    $userfullname = $val['username'];
                    $email = $val['useremail'];
                    $location = $val['user_location'];
                    
                    $timeFormated = $this->getNewTimeFormat($time);
                    //$userto=$val['userto'];
                    /* $var = count($results);
                      echo $var;
                      echo '<pre>';
                      print_r($results);
                      exit; */
                    $username = $me; //value of username = lalit and guestname= abcd for id 8
                    if (count($results) == $j) {
                        $data[$i]['from'] = $from;
			           $data[$i]['image']=$image = $genObj->getuserimageSrc($from,36,36,'small',$title=0);		
                        $data[$i]['message'] = $message;
                        $data[$i]['userfrom'] = $userfrom;
                        $data[$i]['userto'] = $userto;
                        $data[$i]['guestname'] = $guestname;
                        $data[$i]['guestemail'] = $guestemnail;
                        $data[$i]['username'] = $userfullname;
                        $data[$i]['email'] = $email;
                        $data[$i]['location'] = $location;
                        $data[$i]['time'] = $timeFormated;
                        $message = '';
                    }
                } else {
                    $data[$i]['from'] = $from;
		    $data[$i]['image']=$image = $genObj->getuserimageSrc($from,36,36,'small',$title=0);			
                    $data[$i]['message'] = $message;
                    $data[$i]['userfrom'] = $userfrom;
                    $data[$i]['userto'] = $userto;
                    $data[$i]['guestname'] = $guestname;
                    $data[$i]['guestemail'] = $guestemnail;
                    $data[$i]['username'] = $userfullname;
                    $data[$i]['email'] = $email;
                    $data[$i]['location'] = $location;
                    $data[$i]['time'] = $timeFormated;
                    //$data[$i]['guestname']=$val['guestname'];
                    $message = '';
                    $message.='<div class="maintxt">'.$val['message'] .'</div><div class="date_text"  style="float:left; width:110px; text-align:right; padding-bottom:15px;">'. $timeFormated.'</div>';
                    $from = $val['from'];
		     $image = $genObj->getuserimageSrc($val['from'],36,36,'small',$title=0);		
                    $userfrom = $val['userfrom'];
                    $userto = $val['userto'];
                    $guestname = $val['guestname'];
                    $guestemnail = $val['guestemail'];
                    $userfullname = $val['username'];
                    $email = $val['useremail'];
                    $location = $val['user_location'];
                    $time = $val['sent_time'];
                    $timeFormated = $this->getNewTimeFormat($time);
                    //$username= $me;
                    //	$username= $val['guestname'];// here the $name is 2 and msg is fhgh for id 8
                    /* if($username1!=$val['guestname'])
                      {
                      $username=$val['guestname'];
                      } */

                    $i++;
                    if (count($results) == $j) {
                        $data[$i]['from'] = $from;
			            $data[$i]['image']=$image = $genObj->getuserimageSrc($from,36,36,'small',$title=0);			
                        $data[$i]['message'] = $message;
                        $data[$i]['userfrom'] = $userfrom;
                        $data[$i]['userto'] = $userto;
                        $data[$i]['guestname'] = $guestname;
                        $data[$i]['guestemail'] = $guestemnail;
                        $data[$i]['guestname'] = $guestname;
                        $data[$i]['username'] = $userfullname;
                        $data[$i]['email'] = $email;
                        $data[$i]['location'] = $location;
                        $data[$i]['time'] = $timeFormated;
                        $message = '';
                    }
                }
                $create = $val['from'];
                $j++;
            }
        }
        /* echo '<pre>';
          print_r($data);
          exit; */
        $returndata['data']=$data;
         $returndata['userdetail']=$userdetail;
        return $returndata;
    }

    function setreplay($userid, $chatid, $message) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $message=addslashes($message);
        $otherid = $db->query("select * from chat_message where  chat_id=" . $chatid . " limit 0,1");
        $data = $otherid->fetchAll();
        //print_r($data);
        if ($data[0]['from'] == $userid)
            $to = $data[0]['to'];
        else
            $to = $data[0]['from'];
        $db->query("update chat set readby=".$userid." where id=".$chatid);
       // echo "insert into chat_message set chat_id=" . $chatid . ",`from`='" . $userid . "',`to`='" . $to . "',sent_time=" . time() . ",message= '" . $message . "'";exit;
        $db->query("insert into chat_message set chat_id=" . $chatid . ",`from`='" . $userid . "',`to`='" . $to . "',sent_time=" . time() . ",message='" . $message . "'");
        return $to;
    }

    function getLocationNamefromId($id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $citiname = $db->query("select * from cities where id=" . $id)->fetchAll();
        return $citiname[0]['cityname'];
    }

    function readunreadaction($userid, $chatid, $action) {
        $db = Zend_Db_Table::getDefaultAdapter();
        //echo "update chat_message set readed='".$action."' where chat_id=".$chatid." and `to`=".$userid." limit 0,1";exit;
        $sql = $db->query("select * from chat where id=" . $chatid);
        $data = $sql->fetchAll();
        if ($data[0]['user1'] == $userid)
            $otherUser = $data[0]['user2'];
        else
            $otherUser = $data[0]['user1'];
        if ($action == 0) {

            if ($data[0]['readby'] == $userid)
                $toupdate = 0;
            if ($data[0]['readby'] == '-1')
                $toupdate = $otherUser;
            if ($data[0]['readby'] == $otherUser)
                $toupdate = $otherUser;
            if ($data[0]['readby'] == 0)
                $toupdate = 0;
        }
        if ($action == 1) {


            if ($data[0]['readby'] == 0)
                $toupdate = $userid;
            if ($data[0]['readby'] == $otherUser)
                $toupdate = '-1';
             if ($data[0]['readby'] == -1)
                $toupdate = '-1';
             if ($data[0]['readby'] == $userid)
                $toupdate = $userid;
        }
        $db->query("update chat set readby='" . $toupdate . "' where id=" . $chatid);
    }

    function deletechat($userid, $chatid) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $selctUserchat = $db->query("select * from chat_message where chat_id=" . $chatid . "");
        $chatuser = $selctUserchat->fetchAll();
        //print_r($chatuser);
        //exit;
        if (!empty($chatuser)) {
            foreach ($chatuser as $key => $val) {
                if ($val['deleted'] == 0) {
                    //echo "update chat_message set deleted='".$userid."' where id=".$val['id'];exit;
                    $db->query("update chat_message set deleted='" . $userid . "' where id=" . $val['id']);
                } else if ($val['deleted'] == $userid) {
                    
                } else {
                    $db->query("update chat_message set deleted='-1' where id=" . $val['id']);
                }
                //
            }
        }
        return true;
    }
    public function savenewmessage($data)
    {
         $db = Zend_Db_Table::getDefaultAdapter();
          $userName = new Zend_Session_Namespace('USER');
          //echo "insert into chat set user1=". $userName->userId .",user2=".$data['sentto'].",created_date=".time().",modified_date=".time().",readby=". $userName->userid ."";exit;
          $db->query("insert into chat set user1=". $userName->userId .",user2=".$data['sentto'].",created_date=".time().",modified_date=".time().",readby=". $userName->userId ."");
          $lastid=$db->lastInsertId();
        // echo "insert into chat_message set chat_id=". $lastid.",message='".$data['messagebox']."',from=".$userName->userId.",to=".$data['sentto'].",sent_time=".time()."";exit;
          $db->query("insert into chat_message set chat_id=". $lastid.",message='".$data['messagebox']."',`from`=".$userName->userId.",`to`=".$data['sentto'].",sent_time=".time()."");
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
       // echo $this->lastQuery();exit;
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

//End function

    public function requestMessagesData($request_id, $userId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $messagesql = $db->query("select * from request_messages where request_id='" . $request_id . "'");
        $allmessage = $messagesql->fetchAll();

        $data = array();
        $i = 0;
        foreach ($allmessage as $messagekey => $messageval) {
            $data[$i]['message'] = $messageval['message'];
            $data[$i]['time'] = $messageval['time'];

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

//End function
    public function getPrevStatusFromOrderItemTable($orderItemId) {
        $result = $this->select('order_item_status as order_prev_status,order_sub_status_id as order_prev_seller_substatus,buyer_substatus as order_prev_buyer_substatus')
                ->from('order_item')
                ->where('order_item_id', $orderItemId)
                ->get()
                ->rowArray();
        return $result;
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
            switch ($prev['order_prev_status']) {
                case ORDER_STATUS_OPEN:
                    switch ($prev['order_prev_seller_substatus']) {
                        case 1:  // neither requested for shipping address change nor for cancellation
                        case 55:  // cancellation requested
                        case 100:  // cancellation rejected
                        case 7:     // shipping address change request declined 

                            $updatedata = array('order_sub_status_id' => 102,
                                'buyer_substatus' => 103);

                            break;
                    }//End inner switch
                    break;
                case ORDER_STATUS_PAYMENT_RECEIVED:
                    switch ($prev['order_prev_seller_substatus']) {
                        case 3:  // shipment not created yet and shipping address change requested
                        case 114: // shipment not created and cancellation requested
                        case 108:  // shipment  not created and cancellation request rejected
                        case 118:// shipment address change request accepted
                            $updatedata = array('order_sub_status_id' => 110,
                                'buyer_substatus' => 111);
                            break;
                        case 4 :  // shipment created for  all the quantity and shipping address change requested
                        case 124: // shipment created for  all the quantity and cancellation requested
                        case 116: // shipment created for  all the quantity and cancellation request rejected
                            $updatedata = array('order_sub_status_id' => 120,
                                'buyer_substatus' => 121);
                            break;
                        case 5:  // shipment created for x numbers of items and shipping address change requested
                        case 128: // shipment created for x numbers of items and cancellation requested
                        case 130: // shipment created for x numbers of items and cancellation request rejected
                            $updatedata = array('order_sub_status_id' => 134,
                                'buyer_substatus' => 135);
                            break;
                    }//End inner switch

                    break;
            }//End outer switch


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
            $this->updateRecord('order_item', $updatedata, array('order_item_id' => $orderItemId));
        }//End if request status==0
    }

//End function

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
        $this->buyerMapper->updateOrderItemWhenAddressChangeRequestDeclined($rs1['request_item_id'], $updateStatus['order_prev_status'], $updateStatus['order_prev_seller_substatus']);

       
    }

//End function

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

//End function

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

//End function

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
        $this->buyerMapper->updateOrderItemWhenCancellationRequestDeclined($rs1['request_item_id'], $updateStatus['order_prev_status'], $updateStatus['order_prev_seller_substatus']);
    }

//End function
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
        $result = $db->query("SELECT * FROM dispute_master where order_status_id='" . $itemStatus . "'");
        $getReasonAndExample = $result->fetchAll();

        $orderItemData = $this->select('*')
                ->from('order_item as oi')
		->join('orders as o','o.order_id=oi.order_id','inner')
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

        for ($i = 0; $i < count($getReasonAndExample); $i++) {
            $reason_example = $getReasonAndExample[$i]['reason_example'];
            if (preg_match('/_/', $reason_example, $matches)) { //In database, more then 2 examples are stored by seperating them by '_'. so explode them and get both examples.
                $pieces = explode('_', $reason_example);
                $reason_1 = $pieces[0];
                $reason_2 = $pieces[1];
                $getReasonAndExample[$i]['reason_example_1'] = $reason_1;
                $getReasonAndExample[$i]['reason_example_2'] = $reason_2;
            }//End if 
            else {
                $example = $getReasonAndExample[$i]['reason_example'];
                $getReasonAndExample[$i]['reason_example_1'] = $example;
            }//End elseif
        }//End for
        return $getReasonAndExample;
    }

//End function

    public function getSubReasons($orderStatusId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $result = $db->query("SELECT * FROM dispute_master where parent_id = '" . $orderStatusId . "'");
        $getReasons = $result->fetchAll();
        return $getReasons;
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
            'created_on' => time()
        );
        $insert_dispute = $this->insertRecord('dispute', $dispute);

        createDisputeImageFolder($insert_dispute); //to create a folder in images to put dispute related files. It can be image,pdf etc.

        $main_dir = 'uploads/temp/' . $data['randdisputeid'] . '/';
        $fileData = array();

        if (is_dir($main_dir)) {
            $dirs = scandir($main_dir);
            $allFiles = explode(':', $data['fileToUpload']);
            for ($i = 0; $i < count($dirs); $i++) {
                if (in_array($dirs[$i], $allFiles)) {
                    $source = "uploads/temp/" . $data['randdisputeid'] . "/" . strip_tags($dirs[$i]);
                    $target = getDisputeUploadedFilesPath($insert_dispute);
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
						request_status
						) 
				  VALUES('" . $data['orderItemId'] . "',
						 '" . $dispute_raised_by . "',
						 '" . $dispute_raised_against . "',
						 '0',
						 '3',
						 '" . $reason . "',
						 '" . $data['description'] . "',
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
                ->join('order_product_detail as opd', 'oi.order_product_detail_id=opd.product_id', 'inner')
                ->join('orders as o', 'o.order_id=oi.order_id', 'inner')
                ->join('dispute_master as dmreason', "dis.reason_id = dmreason.id", 'inner')
                ->join('dispute_master as dmsubreason', 'dis.sub_reason_id = dmsubreason.id', 'inner')
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
                ->from('dispute_messages')
                ->where('dispute_id', $disputeId)
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
            $data .= '<div class="wid22"><input type="radio" name="order_item_radio" value="OR_'.$records[$i]["payment_module"].'_'.$records[$i]["order_id"].'_'.$records[$i]["order_item_id"].'"/></div>';
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
        <div class="floatLeft">
          <input type="image" src="/images/default/submit_btn.png" title="Submit" id="submitItem"/>
        </div>
        <div class="wid15">&nbsp;</div>
        <div class="floatLeft"><a class="cancelText curpointer" id="closeLink" title="Cancel">Cancel</a></div>
        <div class="wid15">&nbsp;</div>
      </div>
    </div>
    <div class="lh15">&nbsp;</div>
  </div>
</div>';
        echo $data;
    }

//End function

    public function getReturnDetailFromReturnId($requestId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $result = $this->select('*')
                ->from('order_request as orq')
                ->join('order_returns as orn', 'orn.order_item_id= orq.request_item_id', 'inner')
                ->join('orders as o', 'o.order_id=orn.order_id', 'inner')
                ->where('orq.request_id', $requestId)
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

//End function

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
            
            //when return for replacement is accepted: new order_item_id generated;
       //echo_pre($acceptedRequestForReplacement);
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

//End function

    public function respondToReturnRejected($data) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $updaterequest = $db->query("UPDATE order_request SET request_status='2' where request_id='" . $data['requestId'] . "'");

        $feedback = $this->select('*')
                ->from('order_feedback')
                ->where('order_item_id', $data['orderItemId'])
                ->get()
                ->rowArray();

        if (!empty($feedback)) {
            $result = $this->select('order_prev_status')
                    ->from('order_request')
                    ->where('request_id', $requestId)
                    ->get()
                    ->rowArray();

            $updateOrderItem = $db->query("UPDATE order_item SET order_item_status='" . $result['order_prev_status'] . "',order_sub_status_id='146',buyer_substatus='147' where order_item_id='" . $data['orderItemId'] . "'");
            exit;
        }//End if 
        else {
            $result = $this->select('order_prev_status')
                    ->from('order_request')
                    ->where('request_id', $requestId)
                    ->get()
                    ->rowArray();

            $updateOrderItem = $db->query("UPDATE order_item SET order_item_status='" . $result['order_prev_status'] . "',order_sub_status_id='150',buyer_substatus='151' where order_item_id='" . $data['orderItemId'] . "'");
            exit;
        }//End else
    }

//End function

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
            $data .= '<div class="wid22"><input type="radio" name="order_item_radio" value="' . encryptLink($records[$i]["order_item_id"]) . '"/></div>';
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
            $data .= '<div class="wid22"><input type="radio" name="order_item_radio" value="' . encryptLink($records[$i]["order_item_id"]) . '"/></div>';
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
            $data .= '<div class="wid22"><input type="radio" name="order_item_radio" value="' . encryptLink($records[$i]["order_item_id"]) . '"/></div>';
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
            $data .= '<div class="wid22"><input type="radio" name="order_item_radio" value="' . encryptLink($records[$i]["order_item_id"]) . '"/></div>';
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
        $dropDown .= '<div class="floatLeft"><a class="actionbuttondefault curpointer" id="actionbutton"></a></div>
         <div class="floatLeft"><a href="/inbox/#openadispute" class="back_link" title="Back to disputes"><span class="backtoarrow"></span>Back to disputes</a></div>	
         </div></div>
		<div id="actiondropdown" class="curpointer"> <ul> ';

        switch ($disputeData['dispute_status']) {
            case 1:
                //claim raised
                if ($userId == $disputeData['dispute_raised_by']) {
                    $dropDown .= '<li><a id="solved_raised" class="curpointer">Resolved</a></li>';
                    $dropDown .= '<li><a id="attachDocuments_raised" class="curpointer">Attach Documents</a></li>';
                    $dropDown .= '<li><a id="escalatetoclaim_raised_by" class="curpointer">Request a Mediation from Goo2o</a></li>';
                }//End if
                elseif ($userId == $disputeData['dispute_raised_against']) {
                    $dropDown .= '<li><a id="attachDocuments_raised" class="curpointer">Attach Documents</a></li>';
                    $dropDown .= '<li><a id="escalatetoclaim_raised_against" class="curpointer">Request a Mediation from Goo2o</a></li>';
                }//End else if
                break;
            case 2:
                //escalate to claim
                if ($userId == $disputeData['dispute_raised_by']) {
                    $dropDown .= '<li><a id="attachDocuments_raised" class="curpointer">Attach Documents</a></li>';
                }//End if
                elseif ($userId == $disputeData['dispute_raised_against']) {
                    $dropDown .= '<li><a id="attachDocuments_raised" class="curpointer">Attach Documents</a></li>';
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
                    break;
                case '3':
                    //solved
                    /* Dispute has been solved */
                    $remark = 'Dispute has been solved';
                    break;
                case '4':
                    //closed
                    /* Dispute is closed due to time lapse */
                    $remark = 'Dispute is closed due to time lapse';
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
                    $remark = 'Dispute is closed due to time lapse';
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
            $error = displayError('Order Item Not Found', 'Order Item is empty.Please navigate through proper channels.', '/inbox/#openadispute');
            echo $error;
            exit;
        }//End if

        $statusvalidate = $this->select('*')
                ->from('order_item')
                ->where('order_item_id', $orderItemId)
                ->get()
                ->rowArray();
        if ($statusvalidate['order_item_status'] == '1' || $statusvalidate['order_item_status'] == '5' || $statusvalidate['order_item_status'] == '6') { //if order item status is not pending, shipped, delievered.
            $error = displayError('Order Item status not valid', 'You can\'t raise dispute for this item', '/inbox/#openadispute');
            echo $error;
            exit;
        }//End if
        if ($statusvalidate['order_item_status'] != '4' && $statusvalidate['order_item_owner'] == $userId) { //if order item status is delievered and order_item_owner(Seller) is logged in
            $error = displayError('The Order Item is not Delievered.', 'You can raise dispute when this item is delievered.', '/inbox/#openadispute');
            echo $error;
            exit;
        }//End if

        $result = $this->select('*')
                ->from('dispute')
                ->where('order_item_id', $orderItemId)
                ->get()
                ->rowArray();

        if ($result != '') {
            $error = displayError('Dispute Already Raised', 'You have already raised a dispute for this Order Item.', '/inbox/#openadispute');
            echo $error;
            exit;
        }//End if
    }

//End function

    public function validateDisputeDetails($userId, $disputeId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($disputeId == '') {
            $error = displayError('Dispute Not Found', 'Please navigate through proper channels.', '/inbox/#openadispute');
            echo $error;
            exit;
        }//End if

        $result = $db->query("select * from dispute where dispute_id = '" . $disputeId . "' and (dispute_raised_by='" . $userId . "' OR dispute_raised_against = '" . $userId . "')");
        $getRecord = $result->fetchAll();

        if ($result == '') {
            $error = displayError('Already raised a dispute', 'Please navigate through proper links', '/inbox/#openadispute');
            echo $error;
            exit;
        }//End if
    }

    public function showDropDown($requestId, $userId) {
        return $this->select('*')
                ->from('order_request')
                ->where(array('request_id'=>$requestId,'request_seller_id'=>$userId))
                ->where('expire','FALSE','request_status','0')
                ->get()
                ->rowArray();
                
      //  return $this->getWhere('order_request', array('request_id' => $requestId, 'expire' => 'FALSE', 'request_seller_id' => $userId))->rowArray();
    }

//End function
}

//End Class

