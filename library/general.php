<?php

class General {

    private $all_permissions;
    private $all_sub_categories;

    function isAdmin() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $user = new Zend_Session_Namespace('USER');
        if ($user->userId == '')
            return false;
        $sql = $db->query("select * from user as u inner join mall_detail md on md.user_id=u.id and u.id=" . $user->userId);
        $data = $sql->fetchAll();
        if (empty($data))
            return false;
        else
            return true;
    }

    function array_sort($array, $on, $order='SORT_DESC') {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case 'SORT_ASC':
                    asort($sortable_array);
                    break;
                case 'SORT_DESC':
                    arsort($sortable_array);
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[] = $array[$k];
            }
        }
        return $new_array;
    }

    function updateSessionname($userdetail, $userid='') {
        $db = Zend_Db_Table::getDefaultAdapter();

        $db->query("update session set sessionname='" . $userdetail[0]['username'] . "',sessionemail='" . $userdetail[0]['user_email_address'] . "',user_id=" . $userid . " where session_id ='" . Zend_Session::getId() . "'");
        //echo "update session set sessionname='".$userdetail[0]['username']."',user_id=".$userid." where session_id ='".Zend_Session::getId()."'";exit;
    }

    function offlinestatus() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $user = new Zend_Session_Namespace('USER');
        $select = $db->query("select 	gooffline,playsound from user  where id='" . $user->userId . "'");
        $result = $select->fetchAll();
        return $result[0];
        //echo "update session set sessionname='".$userdetail[0]['username']."',user_id=".$userid." where session_id ='".Zend_Session::getId()."'";exit;
    }

    function getonlineuserlistofstore($storeid) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $user = new Zend_Session_Namespace('USER');
        if ($user->userId == '')
            return false;

        //if($user->userId==99)
        //$storeid=28;
        //else
        //$storeid=0;
//echo "select s.id,s.sessionname,s.sessionemail,s.session_id,s.user_id,s.modified,sls.session_id as uniqueid from session_loged_store as sls inner join session s on s.user_id=sls.session_id and s.user_id!=".$user->userId."  and s.sessionname is NOT NULL left join user as u on u.id=s.user_id  and sls.session_id!=''  and s.modified >=".(time()-86400)."  where sls.store_id=".$storeid." group by sls.session_id ";exit;
        $select = $db->query("select s.id,s.sessionname,s.sessionemail,s.session_id,s.user_id,s.modified,sls.session_id as uniqueid from session_loged_store as sls inner join session s on s.user_id=sls.session_id and s.user_id!=" . $user->userId . "  and s.sessionname is NOT NULL left join user as u on u.id=s.user_id  and sls.session_id!=''  and s.modified >=" . (time() - 86400) . "  where sls.store_id=" . $storeid . " group by sls.session_id");
        $result = $select->fetchAll();
        $i = 0;
        $data = array();
        if (!empty($result)) {
            foreach ($result as $key => $val) {
                $data[$i]['id'] = $val['id'];
                $data[$i]['sessionname'] = $val['sessionname'];
                $data[$i]['session_id'] = $val['session_id'];
                $data[$i]['user_id'] = $val['user_id'];
                $data[$i]['modified'] = $val['modified'];
                $data[$i]['uniqueid'] = $val['uniqueid'];
                $data[$i]['image'] = $this->getuserimageSrc($val['user_id'], 20, 20, 'small', $title = 0);

                $i++;
            }
        }
        return $data;
    }

    function openTabed($typeingupdater='') {
        $userName = new Zend_Session_Namespace('USER');
        $db = Zend_Db_Table::getDefaultAdapter();
//echo "select c.id,c.guestname,c.user1,c.user2,c.u1open,c.u2open,c.u1status,c.u2status,uother.username as first,u.username as second from chat as c left join user as u on convert(u.id,char)=convert(c.user2,char) left join user as uother on convert(uother.id,char)=convert(c.user1,char) where  (c.u1open=".$userName->userId." ||  c.u2open=".$userName->userId.")";exit;
        $select = $db->query("select c.id,c.guestname,c.user1,c.user2,c.u1open,c.u2open,c.u1status,c.u2status,uother.username as first,u.username as second from chat as c left join user as u on convert(u.id,char)=convert(c.user2,char) left join user as uother on convert(uother.id,char)=convert(c.user1,char) where  (c.u1open=" . $userName->userId . " ||  c.u2open=" . $userName->userId . ")");
        $result = $select->fetchAll();

        $data = array();
        if (!empty($result)) {
            $i = 0;
            foreach ($result as $key => $val) {
                if ($val['user1'] == $userName->userId) {
                    $f = 'u1typeing';
                    $checkSql = $db->query("select * from session where user_id='" . $val['user2'] . "'");
                } else {
                    $f = 'u2typeing';
                    $checkSql = $db->query("select * from session where user_id='" . $val['user1'] . "'");
                }

                $sessionExist = $checkSql->fetchAll();


                if ($typeingupdater != '') {
                    //echo "update chat set ".$f."='0' where id=".$val['id'];exit;
                    //$updateQuery=$db->query("update chat set ".$f."='0' where id=".$val['id']);
                }
                if ($val['u1status'] == '0' && $val['u2status'] == '0') {
                    //continue;
                }
                if ($val['u1open'] == $userName->userId || $val['u2open'] == $userName->userId) {

                    $openedtab = 0;
                    $firstfrommessage = $val['user1'];
                    if ($firstfrommessage == $userName->userId) {

                        $start = $val['user2'];
                        $username = $val['second'];
                        if ($val['second'] == '') {
                            $username = $val['guestname'];
                        }
                        if ($val['u1status'] != 0) {
                            $openedtab = $val['u1status'];
                        }
                    } else {
                        $start = $val['user1'];
                        $username = $val['first'];
                        if ($val['first'] == '') {
                            $username = $val['guestname'];
                        }
                        if ($val['u2status'] != 0) {
                            $openedtab = $val['u2status'];
                        }
                    }

                    $data[$i]['guestname'] = $username;
                    $data[$i]['from'] = $start;
                    $data[$i]['opentab'] = $openedtab;
                    if (empty($sessionExist)) {
                        $data[$i]['loged'] = 0;
                    } else {
                        $data[$i]['loged'] = 1;
                    }
                    $i++;
                }
            }
        }

        return $data;
    }

    function allChatofadmin() {
        $userName = new Zend_Session_Namespace('USER');
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = "select FROM_UNIXTIME(cm.sent_time,'%l%p %b %d %Y') as time,cm.message,cm.to,cm.from,c.guestname,u.username as first,uother.username as second from chat_message as cm  inner join chat as c on c.id=cm.chat_id left join user as u on convert(u.id,char)=convert(cm.from,char) left join user as uother on convert(uother.id,char)=convert(cm.to,char)  where  (cm.from=" . $userName->userId . " || cm.to=" . $userName->userId . ") order by cm.sent_time ASC";
        $result = $db->fetchAll($select);
        $data = array();
        if (!empty($result)) {
            //other is yo
            $i = 0;
            foreach ($result as $key => $val) {
                if ($val['from'] == $userName->userId) {
                    $start = $val['to'];
                    $username = $val['second'];
                    if ($val['second'] == '')
                        $username = $val['guestname'];
                }
                else {
                    $start = $val['to'];
                    $username = 'me';
                }
                if ($val['to'] == $userName->userId) {
                    $start = $val['from'];
                    $username = $val['first'];
                    if ($val['first'] == '')
                        $username = $val['guestname'];
                }
                else {
                    $username = 'me';
                    $start = $val['to'];
                }
                $data[$i]['username'] = $username;
                $data[$i]['time'] = $val['time'];
                $data[$i]['message'] = $val['message'];
                $data[$i]['from'] = $start;
                $i++;
            }
        }
        return $data;
    }

    function getLoggedUserDetails() {
        $params = func_get_args();
        $case = $params[0];
        $content = $params[1];
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = "select *,  md.id as mallid from user as u left join mall_detail as md on u.id=md.user_id where u.id=" . $content;

        $result = $db->fetchAll($select);
        return $result;
    }

    function makeUrl($customer_id) {

        $db = Zend_Db_Table::getDefaultAdapter();
        $length = 6;
        $string = "";
        $characters = 'abcdefghijkmnopqrstuvwxyz';
        for ($p = 0; $p < $length; $p++) {
            $string .= $characters[mt_rand(0, strlen($characters))];
        }

        $len = 4;
        $charactersarray = 'abcdefghijkmnopqrstuvwxyz';
        $stringval = "";
        for ($k = 0; $k < $length; $k++) {
            $stringval .= $charactersarray[mt_rand(0, strlen($charactersarray))];
        }

        $numeric = "023456789";
        $lennum = 6;
        $stringvalue = "";
        for ($i = 0; $i < $lennum; $i++) {
            $stringvalue .= $numeric[mt_rand(0, strlen($numeric))];
        }

        $sql = "update user set vcode ='" . $string . $customer_id . 'a' . $stringval . $stringvalue . "' where id=" . $customer_id;
        //echo $sql;exit;
        $db->query($sql);
        $vcode = $string . $customer_id . 'a' . $stringval . $stringvalue;
        return $vcode;
    }

    function UrlforNotMyAccount($customers_id) {
        global $db;
        $strinrarray[] = '';
        $customers_id = (string) $customers_id;
        $lengthUid = strlen($customers_id);
        $md5codeOfUserid = md5($customers_id);

        for ($k = 0; $k < $lengthUid; $k++) {
            $stringval = mt_rand(0, strlen($md5codeOfUserid) - 1); //take random values from md5 string

            if (!in_array($stringval, $strinrarray)) {
                $strinrarray[$k] = $stringval;  //store locations
            } else {
                $k = $k - 1;
            }
        }
        $postionsSeprated = implode(',', $strinrarray);

        for ($i = 0; $i < count($strinrarray); $i++) {
            $md5codeOfUserid[$strinrarray[$i]] = $customers_id[$i];  //create charcter replaced string
        }
        for ($j = 0; $j < 1; $j++) {
            $takeonechar = mt_rand(0, strlen($md5codeOfUserid));
        }
        $nextcharpostion = $postionsSeprated . ';' . $takeonechar;
        $nextChunkChar = $md5codeOfUserid[$takeonechar];
        $md5OfNextChar = md5($nextChunkChar);     //used to store next string
        for ($a = 0; $a < 1; $a++) {
            $takeonecharfromNext = mt_rand(0, strlen($md5OfNextChar));
        }
        $lastcharpostion = $nextcharpostion . ';' . $takeonecharfromNext;
        $lastChunkChar = $md5OfNextChar[$takeonecharfromNext];
        $md5OfLastChar = md5($lastChunkChar);    //used to store last string
        $mainString = $md5codeOfUserid . $md5OfNextChar . $md5OfLastChar;  //complete 96 bit string
    }

    function makeUrlForConfirmPassword($customer_id) {
        global $db;
        $length = 6;
        $string = "";
        $characters = 'abcdefghijkmnopqrstuvwxyz';
        for ($p = 0; $p < $length; $p++) {
            $string .= $characters[mt_rand(0, strlen($characters))];
        }
        $len = 4;
        $charactersarray = 'abcdefghijkmnopqrstuvwxyz';
        $stringval = "";
        for ($k = 0; $k < $length; $k++) {
            $stringval .= $charactersarray[mt_rand(0, strlen($charactersarray))];
        }
        $numeric = "023456789";
        $lennum = 6;
        $stringvalue = "";
        for ($i = 0; $i < $lennum; $i++) {
            $stringvalue .= $numeric[mt_rand(0, strlen($numeric))];
        }
        $vcode = $string . $customer_id . 'a' . $stringval . $stringvalue;
        return $vcode;
    }

    function checkSignupStepsCompleted() {
        $db = Zend_Db::factory('Pdo_Mysql', array(
                    'host' => HTTP_HOST,
                    'username' => HTTP_USERNAME,
                    'password' => HTTP_PASSWORD,
                    'dbname' => HTTP_DBNAME
                ));
        if (isset($_SESSION['USER']['userId'])) {
            $db1 = Zend_Db_Table::setDefaultAdapter($db);
            $table = 'user';
            $data = $db->select()->from(array('user'))
                    ->where('id =' . $_SESSION['USER']['userId']);
            $result = $db->fetchAll($data);
            echo $result[0]['signup_steps'];

            switch ($result[0]['signup_steps']) {

                case 1:

                    header("Location:http://secure.v5.com/o2oregister/setyourpreference");
                    break;

                case 2:
                    header("Location:http://secure.v5.com/o2oregister/activatemobile");
                    break;

                case 3:
                    header("Location:http://secure.v5.com/o2oregister/verifyyourself");
                    break;

                case 4:
                    header("Location:http://secure.v5.com/o2oregister/verifyyourself");
                    break;
            }
        }
    }

    function extract_numbers($string) {
        preg_match_all('/([\d]+)/', $string, $match);
        return $match[0];
    }

    function checkVcode($vcode) {
        global $db;
        //$numbers_array = extract_numbers($vcode);
        // $customer_id = $numbers_array[0];
        //$activationCode = $numbers_array[1];
        $db = Zend_Db_Table::getDefaultAdapter();
        $table = 'user';
        $data = $db->select()->from(array('user'))
                ->where("vcode='" . $vcode . "'");
        $result = $db->fetchAll($data);
        $count = count($result);

        if ($count > 0) {
            $sql = "update user set vcode ='' , email_verification = '1' where vcode='" . $vcode . "'";
            $db->query($sql);
        }
        if ($customer_id != '') {
            $data = $db->select()->from(array('user'))
                    ->where('vcode =' . $vcode);
            $result = $db->fetchAll($data);
            return $customer_email = $result[0]['user_email_address'];
        }
    }

    // This function will be used to get the analytic code saved in the database
    // with specific user id. As a user id will be passed into this function ; it
    // will return the feedback code if saved.
    // If the code is in proper format as google webmaster has generated it will
    //produce a feedback button on the left side of the webpage.


    /* function getAnalyticCode($userid) {

      if($userid!=''){
      $db = new Admin_Model_PromoteModel();
      $item=$db->getWhere('mall_detail',array('user_id'=>$userid))->rowArray();
      return $item['code_feedback'];
      }

      } */

    function checkproductstatus($productid) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()->from(array('product_variation'))
                ->where('seller_id="' . $_SESSION['SESSION']['ApiKey'] . '" and product_id=' . $productid);
        $result = $db->fetchAll($select);
        return count($result);
    }

    function individualProductImage($productid) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()->from(array('product_image'))
                ->where('product_id=' . $productid);
        $productimage = $db->fetchAll($select);
        $productfolder = round($productid / 1000);
        foreach ($productimage as $key => $value) {
            if ($value['image_type'] == 1) {
                $productimage['image'] = '/images/product/' . $productfolder . '/' . $productid . '/small/' . $value['image_name'];
                $productimage['imagetitle'] = $value['image_title'];
            }
        }

        return $productimage;
    }

    /**
      Creation Date :
      Created By : Rakesh kumar
      Reason :This function requestActionType($type) is used in open,inbox,approved,onhold,rejcted index.phtml page to display request type in display listing.
      In this function passed argument is feed type .
     */
    function requestActionType($type) {
        switch ($type) {
            case 1 :$action = 'Need';
                break;
            case 2 :$action = 'Deel';
                break;
            case 3 :$action = 'Conversation';
                break;
            case 4 :$action = 'Comment';
                break;

            case 6 :
            case 7 :
            case 8 :
            case 9 :$action = 'Edit ';
                break;
            case 10 :$action = 'Create';
                break;
            case 11 :$action = 'Report';
                break;
        }
        return $action;
    }

    /**
      Creation Date :
      Created By : Rakesh kumar
      Reason :This function shortActionType($req) is used in open,inbox,approved,onhold,rejcted index.phtml page to display request info in display listing that  sellers what type of action have taken on product .
     */
    function shortActionType($req) {
        switch ($req) {
            case 1 :
            case 10 :$action = 'Created ';
                break;
            case 11 :$action = 'Reported On ';
                break;
            case 6 :
            case 7 :
            case 8 :
            case 9 :$action = 'Edited ';
                break;
        }
        return $action;
    }

    /**
      Creation Date :
      Created By : Rakesh kumar
      Reason :This function productName($productid) is used in open,inbox,approved,onhold,rejcted index.phtml page to display request info in display listing that  sellers what type of action have taken on which product .
     */
    function productName($productid) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()->from(array('product'))
                ->where('id=' . $productid);
        $result = $db->fetchAll($select);
        return $result[0]['product_name'];
    }

    /**
      Creation Date :
      Created By : Rakesh kumar
      Reason :This function editProductName($value) is used in open,inbox,approved,onhold,rejcted index.phtml page to display request info in display listing that sellers what new suggested on which product part .
     */
    function editProductName($value) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()->from(array('task'))
                ->where('feed_type in(6,7,8,9)');
        $result = $db->fetchAll($select);
        return $result[0]['product_value'];
    }

    /**
      Creation Date :
      Created By : Rakesh kumar
      Reason :This function editon($req) is used in open,inbox,approved,onhold,rejcted index.phtml page to display request info in display listing that sellers what type of action have taken on which product part .
     */
    function editon($req) {
        switch ($req) {
            case 6 :$request = 'Name of';
                break;
            case 7 :$request = 'Long Description of';
                break;
            case 8: $request = 'Short Description of';
                break;
            case 9: $request = 'Image of';
                break;
        }
        return $request;
    }

    function moderatorLeftMenu($value) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()->from(array('product_moderation'))
                ->where('status=' . $value);
        $result = $db->fetchAll($select);
        return count($result);
    }

    /**
      Creation Date :
      Created By : Rakesh kumar
      Reason :This function getAgentId($taskid) is used to return particuler agent id on based task id .
     */
    function getAgentId($taskid) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()->from(array('task_assign'))
                ->where("task_id='" . $taskid . "'");
        $result = $db->fetchAll($select);
        return $result;
    }

    /**
      Creation Date :
      Created By : Rakesh kumar
      Reason :This function getAgentDetail($agentid) is used to return agent detail on based employee_id .
     */
    function getAgentDetail($agentid) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()->from(array('emp_detail'))
                ->where("employee_id='" . $agentid . "'");
        $result = $db->fetchAll($select);
        return $result;
    }

    /**
      Creation Date :
      Created By : Rakesh kumar
      Reason :This function getAgentAction($actionbyagent) is used in inbox of moderator what type of action has taken by moderator and agent.
     */
    function getAgentAction($actionbyagent) {
        switch ($actionbyagent) {
            case 'inbox' :$action = 'assigned to';
                break;
            case 'onhold' :$action = 'on hold by';
                break;
            case 'approved' :$action = 'approved by';
                break;
            case 'rejected' : $action = 'rejected by';
                break;
        }
        return $action;
    }

    /**
      Creation Date : 08/06/2011
      Created By : Rakesh kumar
      Reason :This function getDetailPageType($feedtype) is used to display detail page type on based argument feedtype.
     */
    function getDetailPageType($feedtype) {
        if ($feedtype == '10') {
            $actionname = 'create';
        } else if ($feedtype == '9') {
            $actionname = 'image';
        } else if ($feedtype == '11') {
            $actionname = 'report';
        } else {
            $actionname = 'name';
        }
        return $actionname;
    }

    /**
      Creation Date : 24/06/2011
      Created By : Rakesh kumar
      Reason :This function countBrand($brandid) is used in addcategoty controller in manageyourbrand phtml file to count brand used in product.
     */
    function countBrand($brandid) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()->from(array('product'), array("num" => "count(*)"))
                ->where('brand_id=' . $brandid);
        $result = $db->fetchAll($select);
        return $result[0]['num'];
    }

    function getinputType($inputtype) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()->from(array(TABLE_INPUT_TYPE), array('*'))
                ->where('type_id=' . $inputtype);
        $result = $db->fetchAll($select);
        return $result[0]['name'];
    }

    function couponcategoryTree() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $controllerName = $request->getControllerName();
        $actionName = $request->getActionName();
        if ($actionName == 'managecategory') {
            $type = 1;
        } else {
            $type = 0;
        }
        $select = $db->select()->from(array('addcategory'), array(cat_id, cat_name, parent_id))
                ->where("status='1' and apikey='" . $_SESSION[SESSION][ApiKey] . "' and cat_id!='1'");
        $result = $db->query($select);
        $parentArr = $result->fetchAll();
        ?>
        <script type="text/javascript">
            d = new dTree('d');
            d.add(0,-1,'','javascript://','','','','','',0,'<?php echo $type; ?>');
        </script>
        <?php
        //echo "<pre>";print_r($parentArr);
        foreach ($parentArr as $parent) {
            if ($parent['cat_id'] == 0) {
                $noofproduct = '';
            } else {
                //$noofproduct = $this->getCount($parent['cat_id']);
				 $noofproduct = '';
            }//echo $parent['cat_id'].$parent['cat_name'].'</br>';
            ?>
            <script type="text/javascript">
                d.add('<?php echo $parent['cat_id']; ?>','<?php echo $parent['parent_id']; ?>','<?php echo $parent['cat_name']; ?>','javascript://','','','','','','<?php echo $noofproduct; ?>','<?php echo $type; ?>');
            </script>

            <!--<div class="main-cat-bg-line">&nbsp;</div>-->
            <?php } ?>
        <script type="text/javascript">
            //alert(d);
            //$('#catree').html(String(d));
            document.write(d);
        </script>
    <?php
    }

//function getfeaturename($featureid){
// 	$db = Zend_Db_Table::getDefaultAdapter();
//		$select = $db->select()->from(array(TABLE_PRODUCT_FEATURE),array('*'))
//				  ->where('product_feature_id='.$featureid);
//		$result = $db->fetchAll($select);
//		return $result[0]['feature_name'];
//
//}

    function categoryTree() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $controllerName = $request->getControllerName();
        $actionName = $request->getActionName();

        if ($actionName == 'managecategory') {
            $type = 1;
        } else {
            $type = 0;
        }
        $select = $db->select()->from(array('addcategory'), array(cat_id, cat_name, parent_id))
                ->where("status='1' and apikey='" . $_SESSION[SESSION][ApiKey] . "' and cat_id!='1'");
        $result = $db->query($select);
        $parentArr = $result->fetchAll();
        ?>
        <script type="text/javascript" src="/jscript/admin/dtree.js"></script>
        <script type="text/javascript">
            d = new dTree('d');
            d.add(0,-1,'','javascript://','','','','','',0,'<?php echo $type; ?>');
        </script>
        <?php
        //echo "<pre>";print_r($parentArr);exit;
        foreach ($parentArr as $parent) {
            if ($parent['cat_id'] == 0) {
                $noofproduct = '';
            } else {
                //$noofproduct = $this->getCount($parent['cat_id']);
				 $noofproduct = '';
            }//echo $parent['cat_id'].$parent['cat_name'].'</br>';
            ?>
            <script type="text/javascript">
                d.add('<?php echo $parent['cat_id']; ?>','<?php echo $parent['parent_id']; ?>','<?php echo addslashes($parent['cat_name']); ?>','javascript://','','','','','','<?php echo $noofproduct; ?>','<?php echo $type; ?>');
            </script>

            <!--<div class="main-cat-bg-line">&nbsp;</div>-->
            <?php } ?>
        <script type="text/javascript">
            //alert(d);
            $('#catree').html(String(d));
            //document.write(d);
        </script>
    <?php
    }

    function noOfProduct($catid) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()->from(array('product'), array("num" => "count(*)"))
                ->where('category_id=' . $catid . ' and delete_flag="1"');
        $result = $db->fetchAll($select);
        return $result[0]['num'];
    }

    function draganddrop() {//echo "<pre>";print_r($_SESSION);
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $controllerName = $request->getControllerName();
        $actionName = $request->getActionName();
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()->from(array('addcategory'), array(cat_id, cat_name, parent_id))
                ->where("status='1' and parent_id='0' and apikey='" . $_SESSION[SESSION][ApiKey] . "' and cat_id!='1'");
        $result = $db->query($select);
        $parentArr = $result->fetchAll();
        $width = 20;
        foreach ($parentArr as $row) {
            ?>
            <li id="node_<?php echo $row[cat_id]; ?>">
                <a href="javascript://" rel="<?php echo $row[cat_id]; ?>" class="changecss"><?php echo stripslashes($row['cat_name']); ?></a>
            <?php if (($controllerName == 'addcategory') && ($actionName == 'placecategory')) { ?>
                    <div style="float:right; display:none; margin-top:0px; *margin-top:-25px; height:5px;" id="tick_<?php echo $row[cat_id]; ?>" class="showhidetick" ><img src="/images/addcategory/tick.gif" width="12" height="9" border="0" /></div>
            <?php }if (($controllerName == 'addcategory') && ($actionName == 'managecategory')) { ?><div style="float:right; display:none; *margin-top:-27px;"  class="action type_<?php echo $row[cat_id]; ?>" ><div class="actionName" style="cursor:pointer;">Action</div> <div style="cursor:pointer" class="actionDownArrow"></div></div>
                    <div class="showhidedrag action_<?php echo $row[cat_id]; ?>" style="display:none;">&nbsp;</div>
            <?php } ?>
            <!--<div style="width:<?php echo $width; ?>px; float:left">&nbsp;</div>--><ul><?php categoryChild($row['cat_id'], $width); ?>
                </ul></li>
            <?php
        }
    }

    //echo "</ul>";
    function categoryChild($id, $width=0) {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $controllerName = $request->getControllerName();
        $actionName = $request->getActionName();
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()->from(array('addcategory'), array(cat_id, cat_name, parent_id))
                ->where("status='1' and apikey='" . $_SESSION[SESSION][ApiKey] . "' and cat_id!='1' and parent_id='" . $id . "'");
        $result = $db->query($select);
        $parentArr = $result->fetchAll();
        if (count($result) > 0) {
            $width+=20;
            foreach ($parentArr as $row) {
                ?>
                <li id="node_<?php echo $row['cat_id']; ?>">
                    <a href="javascript://" rel="<?php echo $row[cat_id]; ?>" class="changecss"><?php echo stripslashes($row['cat_name']); ?></a>
                <?php if (($controllerName == 'addcategory') && ($actionName == 'placecategory')) { ?><div style="float:right; display:none; margin-top:0px; *margin-top:-25px;" id="tick_<?php echo $row[cat_id]; ?>" class="showhidetick"><img src="/images/addcategory/tick.gif" width="12" height="9" border="0"/></div>
                <?php }if (($controllerName == 'addcategory') && ($actionName == 'managecategory')) { ?><div style="float:right; display:none; *margin-top:-27px;"  class="action type_<?php echo $row[cat_id]; ?>" ><div class="actionName" style="cursor:pointer;">Action</div><div style="cursor:pointer" class="actionDownArrow"></div></div>
                        <div class="showhidedrag action_<?php echo $row[cat_id]; ?>" style="display:none;">&nbsp;</div>
                <?php } ?>
                <!--<div style="width:<?php echo $width; ?>px; float:left">&nbsp;</div>--><ul><?php categoryChild($row['cat_id'], $width); ?>
                    </ul></li>

                <?php
            }
        }
    }

    function imagesize($imgname, $type) {
        list($width, $height) = getimagesize($imgname);
        if ($type == 1) {
            $maxwidth = 125;
            $maxheight = 100;
        } else if ($type == 2) {
            $maxwidth = 300;
            $maxheight = 300;
        } else if ($type == 3) {
            $maxwidth = 40;
            $maxheight = 40;
        }

        $xRatio = $maxwidth / $width;
        $yRatio = $maxheight / $height;
        if ($width < $maxwidth && $height < $maxheight) {
            $width = $width;
            $height = $height;
        } else if ($xRatio * $height < $maxheight) { // Resize the image based on width
            $height = ceil($xRatio * $height);
            $width = $maxwidth;
        } else { // Resize the image based on height
            $width = ceil($yRatio * $width);
            $height = $maxheight;
        }

//		if(($width>=$maxwidth) && ($height<=$maxheight)){
//			$width = $maxwidth;
//		}else if(($width<=$maxwidth) && ($height>=$maxheight)){
//			$height = $maxheight;
//		}else if(($width>=$maxwidth) && ($height>=$maxheight)){
//			$width = $maxwidth;
//			$height = $maxheight;
//		}
        return array($width, $height);
    }

    function getimagedimension($imagename, $moduleName, $requiredwidth, $requiredheight) {
        $dimension = array();
        list($width, $height) = getimagesize($imagename);
        $xRatio = $requiredwidth / $width;
        $yRatio = $requiredheight / $height;
        if ($width < $requiredwidth && $height < $requiredheight) {
            $dimension[0] = $width;
            $dimension[1] = $height;
        } else if ($xRatio * $height < $requiredheight) { // Resize the image based on width
            $dimension[1] = ceil($xRatio * $height);
            $dimension[0] = $requiredwidth;
        } else { // Resize the image based on height
            $dimension[0] = ceil($yRatio * $width);
            $dimension[1] = $requiredheight;
        }
        return $dimension;
    }

    function getHeaderButtonClass($controller, $action) {
        $pageArrays['manage'] = array('orders');
        //$pageArrays['manage'] = array('index','managefeatures','managecategory','managebrands','managevariations');
        $pageArrays['buzz'] = array('features1');
        $pageArrays['create'] = array('placecategory', 'featurebasicinfo', 'brandbasicinfo', 'basicinfo');
        $pageArrays['promote'] = array('features3');
        $pageArrays['design'] = array('features4');
        $pageArrays['buzz'] = array('features5');
        $pageArrays['stats'] = array('features5');
        $controllerArray = array('manage', 'buzz', 'create', 'promote', 'design', 'buzz', 'stats');
        foreach ($controllerArray as $key => $val) {
            //echo $pageArrays[$val]."<br>";
            if (in_array($controller, $pageArrays[$val])) {

                //$class[$val]= ucfirst($val).'TabActive';
                $class[$val] = '_tabActive';
            } else {

                //$class[$val]= ucfirst($val).'Tab';
                $class[$val] = 'Tab';
            }
        }
        return $class;
    }

    /**
     * @author : Ashis
     * @modifay : Mrunal
     * @var $cat_id : categorie id
     * Creation Date : 19-11-2011
     * Reason : get Product count
     */
    function getCount($cat_id,$tempcounter=0) {
        $db = Zend_Db_Table::getDefaultAdapter();
		$temCounter=0;
		$this->all_sub_categories[]=$cat_id;
        $sub_cat_arr = explode(',', $this->getSubCatStr($cat_id,'',$temCounter));
        $catquery = '';
        foreach ($sub_cat_arr as $key => $val) {
            //SELECT * ,CONCAT(',',`category_id`,',') as con  FROM `product` WHERE CONCAT(',',`category_id`,',') LIKE '%,262,%'
            if ($key != 0)
                $catquery .= " OR ";
            $catquery .= "(CONCAT(',',category_id,',') LIKE '%," . $val . ",%')";
        }
        $sql = $db->select()->from(array(TABLE_PRODUCT), array('count(*)'))
                ->where("seller_id = ?", $_SESSION['USER']['stores']['0']['store_apikey'])
                ->where(" delete_flag = '1'")
                ->where($catquery);
        return $db->fetchOne($sql);
    }

    /**
     * @author : Mrunal
     * @var $cat_id : categorie id
     * Creation Date : 19-11-2011
     * Reason : get sub-caregorie ids
     * return : sub-caregorie ids in a string
     */
    function getSubCatStr($cat_id, $str = '',$temCounter=0) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $str = ($str == '') ? $cat_id : $str;
        $sql = $db->select()->from(array(TABLE_ADDCATEGORY), array(cat_id))
                ->where("cat_id != 1 AND parent_id='" . $cat_id . "' AND (apikey='" . $_SESSION[SESSION][ApiKey] . "') AND (status='1')");
        $result = $db->fetchAll($sql);
        if (count($result) > 0) {
            foreach ($result as $key => $val) {
				if($_SESSION[SESSION][ApiKey]=='d1ef87fa0618c54d8c61d7a2c3486e53') {
					if(!in_array($val['cat_id'],$this->all_sub_categories))
						$this->all_sub_categories[]=$val['cat_id'];
					//if ($val['cat_id']==8980 || $cat_id==8980) continue;
				}
				$temCounter++;
                $str .= ',' . $this->getSubCatStr($val['cat_id'], $str,$temCounter);
            }
            return $str;
        } else {
            return $cat_id;
        }
    }

    /**
     * @author : Vaibhav Sharma
     * Used for updating the client session id when used API login
     * @var $clientsess : Client session key // define parameters for the functions
     * Creation Date : 12-07-2011
     * Created By : Author Name
     * // use comma separated for entering modification date
     * Modified Date :
     * Modified Date :
     * Reason :
     */
    function updateClientSession($clientsess) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->query("update session set clientsess ='" . $clientsess . "' where session_id ='" . Zend_Session::getId() . "'");
    }

    function categoryTreeList() {
        $db = Zend_Db_Table::getDefaultAdapter();
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $controllerName = $request->getControllerName();
        $actionName = $request->getActionName();
        if (($controllerName == 'addcategory') && ($actionName == 'placecategory')) {
            $setAction = 'place';
        } else if (($controllerName == 'addcategory') && ($actionName == 'managecategory')) {
            $setAction = 'manage';
        }
        $select = $db->select()->from(array('addcategory'), array(cat_id, cat_name, parent_id, cat_flag))
                ->where("status='1' and (parent_id='0') and (apikey='" . $_SESSION[SESSION][ApiKey] . "' or apikey='') ")
                ->order('cat_id asc');

        $result = $db->query($select);
        $parentArr = $result->fetchAll();
        $width = 20;
		$tempcountertest=1;
        foreach ($parentArr as $key => $row) {
            if ($row['cat_flag'] == 0) {
                $opacity = 'opacity:0.5;';
            } else {
                $opacity = '';
            }
            if ($key == 0) {
                $clickwidth = 615;
                $underline = '';
            } else {
                $clickwidth = 615 - $width;
                $underline = 'underline';
            }
			if($_SESSION[SESSION][ApiKey]=='d1ef87fa0618c54d8c61d7a2c3486e53'){
			//if($row['cat_id']==2225) continue;
			}
            ?>
            <li id="node_<?php echo $row['cat_id']; ?>" class="treeItem <?php echo $underline; ?>" rel="<?php echo $width; ?>"><div style="float:left; margin-left:2px; margin-right:5px; margin-top:8px; <?php echo $opacity; ?>"><img src="/images/addcategory/closed.gif" class="folderImage" id="node_<?php echo $row['cat_id']; ?>" /></div><span class="textHolder <?php echo $setAction; ?>" id="node_<?php echo $row['cat_id'] ?>" rel="<?php echo $row['cat_id']; ?>" style="<?php echo $opacity; ?>" title="<?php if ($row['cat_id'] != 1) {

                //echo stripslashes($row['cat_name']) . ' (' . $this->getCount($row['cat_id'],$tempcountertest) . ')';
				 echo stripslashes($row['cat_name']);
            } else {
                echo $row['cat_name'];
            } ?>"><?php if ($row['cat_id'] != 1) {
               // echo stripslashes($row['cat_name']) . ' (' . $this->getCount($row['cat_id']) . ')';
				 echo stripslashes($row['cat_name']);
            } else {
                echo $row['cat_name'];
            } ?></span>
            <?php 			$tempcountertest++;
if ($setAction == 'place') { ?><div style="float:right; display:none; margin-top:10px; *margin-top:-25px;" id="tick_<?php echo $row[cat_id]; ?>" class="showhidetick"><img src="/images/addcategory/tick.gif" width="12" height="9" border="0"/></div>
            <?php }if ($setAction == 'manage') { ?>
                    <div class="showhidedragedit actionhide_<?php echo $row['cat_id']; ?>" style="display:none;">&nbsp;</div>
            <?php } ?>

            <?php

$haschild = $this->checkChild($row['cat_id']);
            if ($haschild > 0) { ?>
                    <ul style="display: none;" class="showhidedrag action_<?php echo $row[cat_id]; ?>"><?php $this->categoryChildProduct($row['cat_id'], $width) ?></ul>
            <?php } ?>
            </li><?php
        }
    }

    function categoryChildProduct($id, $width=0) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $controllerName = $request->getControllerName();
        $actionName = $request->getActionName();
        if (($controllerName == 'addcategory') && ($actionName == 'placecategory')) {
            $setAction = 'place';
        } else if (($controllerName == 'addcategory') && ($actionName == 'managecategory')) {
            $setAction = 'manage';
        }
        $select = $db->select()->from(array('addcategory'), array(cat_id, cat_name, parent_id, cat_flag))
                ->where("status='1' and parent_id='" . $id . "' and (apikey='" . $_SESSION[SESSION][ApiKey] . "' or apikey='') ");
        $result = $db->query($select);
        $parentArr = $result->fetchAll();

        if (count($result) > 0) {
            $width+=20;
            foreach ($parentArr as $row) {
                $catCount = $this->subCategoryLevel($row['cat_id']);
                if ($catCount < 4) {
                    $levelReached = 1;
                } else {
                    $levelReached = 0;
                }
                if ($row['cat_flag'] == 0) {
                    $opacity = 'opacity:0.5;';
                } else {
                    $opacity = '';
                }
                ?><input type="hidden" name="hiddenwidth" id="hiddenwidth_<?php echo $row['cat_id']; ?>" value="<?php echo $width; ?>" />
                <li id="node_<?php echo $row['cat_id']; ?>" class="treeItem underline" rel="<?php echo $width; ?>"><div style="float:left; margin-left:2px; margin-right:5px; margin-top:8px; <?php echo $opacity; ?>"><img src="/images/addcategory/closed.gif" class="folderImage" id="node_<?php echo $row['cat_id']; ?>" /></div><span class="textHolder <?php echo $setAction . " " . $levelReached; ?>" id="node_<?php echo $row['cat_id'] ?>" rel="<?php echo $row['cat_id']; ?>" style="<?php echo $opacity; ?>" title="<?php echo stripslashes($row['cat_name']); ?>"><?php echo stripslashes($row['cat_name']); ?></span>
                <?php if (($controllerName == 'addcategory') && ($actionName == 'placecategory')) { ?><div style="float:right; display:none; margin-top:10px; *margin-top:-25px;" id="tick_<?php echo $row[cat_id]; ?>" class="showhidetick"><img src="/images/addcategory/tick.gif" width="12" height="9" border="0"/></div>
                <?php }if (($controllerName == 'addcategory') && ($actionName == 'managecategory')) { ?>
                        <div class="showhidedragedit actionhide_<?php echo $row[cat_id]; ?>" style="display:none;">&nbsp;</div>
                <?php } ?>
                <?php $haschild = $this->checkChild($row['cat_id']);
                if ($haschild > 0) { ?>
                        <ul style="display: none;" class="showhidedrag action_<?php echo $row[cat_id]; ?>"><?php $this->categoryChildProduct($row['cat_id'], $width) ?></ul>
                <?php } ?>
                </li>
                    <?php
                }
            }
            //echo "asdfasd".$tempvar;exit;
        }

        function subCategoryLevel($catid, $cnt=0) {
            $db = Zend_Db_Table::getDefaultAdapter();
            for ($i = 0; $i < 5; $i++) {
                $select = $db->select()->from(array(TABLE_ADDCATEGORY), array('parent_id'))
                        ->where("apikey='" . $_SESSION[SESSION][ApiKey] . "' and cat_id=" . $catid . " and status='1'");
                $result = $db->fetchRow($select);
                if (count($result) > 0 && $result['parent_id'] != 0) {
                    $cnt = $cnt + 1;
                    $catid = $result['parent_id'];
                }
            }
            return $cnt;
        }

        function checkChild($parentid) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()->from(array('addcategory'), array("num" => "count(*)"))
                    ->where('status="1" and parent_id=' . $parentid . ' and apikey="' . $_SESSION[SESSION][ApiKey] . '"');
            $result = $db->fetchAll($select);
            return $result[0]['num'];
        }

        /* 	function databaseconnection(){
          //require_once "index.php";
          return $db = Zend_Db::factory('Pdo_Mysql', array(
          'host'     => 'localhost',
          'username' => 'o2ostore_store',
          'password' => '11]]@~0qDsis',
          'dbname'   => 'o2ostore_o2ostore'
          ));
          function databaseconnection(){
          //require_once "index.php";
          return $db = Zend_Db::factory('Pdo_Mysql', array(
          'host'     => 'localhost',
          'username' => 'root',
          'password' => '',
          'dbname'   => 'o2oapi'
          )); */

        function databaseconnection() {
            //require_once "index.php";
            return $db = Zend_Db::factory('Pdo_Mysql', array(
                        'host' => 'localhost',
                        'username' => 'o2ostore_store',
                        'password' => '7var#usr5A',
                        'dbname' => 'o2ostore_o2ostore'
                    ));
        }

        function updateImage($id, $imagename, $type) {
            $db = $this->databaseconnection();
            if ($type == 1) {
                $select = $db->select()->from(array('temp_product'), array("*"))
                        ->where('id=' . $id);
                $result = $db->fetchAll($select);
                if (count($result) > 0) {
                    $productimage = $result['0']['image_name'];
                    $imagetype = $result['0']['image_type'];
                    $imagetag = $result['0']['image_alttag'];
                    $imagetitle = $result['0']['image_title'];
                    $imagedescription = $result['0']['image_description'];
                } else {
                    $productimage = '';
                    $imagetype = '';
                    $imagetag = '';
                    $imagetitle = '';
                    $imagedescription = '';
                }
                if ($productimage == '') {
                    $imagevalue = $imagename;
                    $imagetype = 1;
                    $imagetag = ":";
                    $imagetitle = ":";
                    $imagedescription = '';
                } else {
                    $imagevalue = $productimage . ":" . $imagename;
                    $imagetype = $imagetype . ":0";
                    $imagetag = $imagetag . ":";
                    $imagetitle = $imagetitle . ":";
                    $imagedescription = $imagedescription . ":";
                }
                $data_arr = array("image_name" => $imagevalue, "image_type" => $imagetype, "image_title" => $imagetitle, "image_description" => $imagedescription, "image_alttag" => $imagetag, "image_status" => '1');
                $where = "id=" . $id;
                $db->update('temp_product', $data_arr, $where);
            } else if ($type == 2 || $type == 3) {
                $data_arr = array("image_name" => $imagename, "imagemanagerstatus" => '1');
                $where = "cat_id=" . $id;
                $db->update('temp_addcategory', $data_arr, $where);
            }
        }

        function deleteImage($filename) {
            $db = $this->databaseconnection();
            $idoffile = explode("_", $filename);
            $select = $db->select()->from(array('temp_product'), array("*"))
                    ->where('id=' . $idoffile[1]);
            $result = $db->fetchAll($select);
            $productimage = $result['0']['image_name'];
            $indimage = explode(":", $productimage);
            for ($i = 0; $i < count($indimage); $i++) {
                if ($indimage[$i] != $filename) {
                    $imagename.=$indimage[$i] . ':';
                }
            }
            $imagevalue = substr($imagename, 0, -1);
            if (count($indimage) == 1) {
                $image = 2;
            } else {
                $image = 1;
            }
            $data_arr = array("image_name" => $imagevalue, "image_status" => $image);
            $where = "id=" . $idoffile[1];
            $db->update('temp_product', $data_arr, $where);
        }

        function displayMenu($CLASS) {
            //print_r($CLASS);
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()->from(array('menu2'))
                    ->where("parent_id = 0");
            $result = $db->fetchAll($select);
            $strt = '<ul class="topnav">';
            foreach ($result as $key) {
                $menuname = strtolower($key["name"]);
                //echo $CLASS[$menuname];
                $strt .= '<li class="' . $key["name"] . $CLASS[$menuname] . '">';
                $selectsubmenu = $db->select()->from(array('menu2'))
                        ->where("parent_id = " . $key[id]);
                $resultsubmenu = $db->fetchAll($selectsubmenu);
                if (count($resultsubmenu) > 0) {
                    $strt .='<ul class="subnav">';
                    foreach ($resultsubmenu as $keymenu) {
                        //$struct[$key['name']] = $resultsubmenu ;
                        $strt .= '<li><a href="' . $keymenu['link'] . '">' . $keymenu['name'] . '</a></li>';
                    }
                    $strt .='</ul>';
                    //print_r($resultsubmenu);
                }
                $strt .='</li>';
                //print_r($result);
                //$str[$key['name']] = $select;
            }
            $strt .='</ul>';
            return $strt;
        }

        function getpathheaderblock($edata='') {
            $request = Zend_Controller_Front::getInstance()->getRequest();
            $controllerName = $request->getControllerName();
            $actionName = $request->getActionName();
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/languages/htmlblocks/headers/' . $controllerName . '.php')) {
                include_once('htmlblocks/headers/' . $controllerName . ".php");
            }
        }

        function checkpagehash() {
            echo "<script>";
            echo " var pattern = '[#]';";
            echo "var docurl = window.location.href; ";
            echo "if(docurl.search(pattern)==-1){window.location.href='http://goo2ostore.com/admin/orders/#list-order';}";
            echo "</script>";
        }

        /*
         * @author : Rakesh
         * Used for User registration actions  // define purpose
         * @var private $publickey : No    // define parameters for the functions
         * @var private $privatekey : $db;
         * Creation Date :
         * Created By : Rakesh
         * Modified Date :09-Aug-2011
         * Reason :This function check date valid or then return for save.
         */

        function check_date($date) {
            if (strlen($date) == 10) {
                $pattern = '/\.|\/|-/i';    // . or / or -
                preg_match($pattern, $date, $char);

                $array = preg_split($pattern, $date, -1, PREG_SPLIT_NO_EMPTY);

                if (strlen($array[2]) == 4) {
                    // mm/dd/yyyy    # Common U.S. writing
                    if ($char[0] == "/") {
                        $month = $array[0];
                        $day = $array[1];
                        $year = $array[2];
                    }
                }
                if (checkdate($month, $day, $year)) {    //Validate Gregorian date
                    return TRUE;
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;    // more or less 10 chars
            }
        }

        function getFulfillerName($id) {
            switch ($id) {
                case 1:
                    return 'Goo2o';
                    break;
                case 2:
                    return 'You';
                    break;
            }
        }

        function countimagedetail($filename) {
            $db = $this->databaseconnection();
            $idoffile = explode("_", $filename);
            $select = $db->select()->from(array('temp_product'), array("*"))
                    ->where('id=' . $idoffile[1]);
            $result = $db->fetchAll($select);
            $productimage = $result['0']['image_name'];
            $indimage = explode(":", $productimage);
            return count($indimage);
        }

        function getExtension($str) {
            $i = strrpos($str, ".");
            if (!$i) {
                return "";
            }

            $l = strlen($str) - $i;
            $ext = substr($str, $i + 1, $l);
            return strtolower($ext);
        }

        function callMe($resultSet, $myIdString) {
            $obj = new Superadmin_Model_NotificationManagerMapper();
            if (count($resultSet) == 0)
                return $myIdString;
            else {
                foreach ($resultSet as $items) {
                    $ids.=$items['cat_id'] . ',';
                }
                $ids = substr($ids, 0, -1);
                $myIdString.= $ids . ',';

                $result = $obj->select('cat_id')
                        ->from('addcategory')
                        ->whereIn('parent_id', explode(',', $ids))
                        ->get()
                        ->resultArray();
                //echo $obj->lastQuery();
                return $this->callMe($result, $myIdString);
            }
        }

        function getsessionuser($sessionid) {       //echo "test"; exit;
            $db = $this->databaseconnection();
            //$user=new Zend_Session_Namespace('USER');
            $select = "select * from user as u left join session as s on u.id=s.user_id left join mall_detail as md on u.id=md.user_id  where s.session_id='" . $sessionid . "'";

            $result = $db->fetchAll($select);
            return $result;
        }

//Name:Nagendra Yadav
//Date:19-8-2011
//Reason:To get product name from product table for productreview
        function getproductnameofproduct($product_id) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = "select product_name from product where id='$product_id'";
            return $result = $db->fetchOne($select);
        }

        /*         * **********************Created By: Sumit, Used in Wallpaper module*******************************
          $actual_file = Actual file path i.e $_FILES[...]['tmp_name'];
          $uploaded_filename = Actual file path i.e $_FILES[...]['name'];
          $nwidth = new width;
          $nheight = new height;
          $spath = path to store small image;
          $lpath = path to store large image;
         */

        function imageManipulator($actual_file, $uploaded_filename, $nwidth, $nheight, $spath, $lpath) {
            ini_set('memory_limit', '-1');
            define("MAX_SIZE", "4000");
            $errors = 0;
            if ($actual_file) {
                $filename = stripslashes($uploaded_filename);
                $extension = $this->getExtension($filename);
                $extension = strtolower($extension);
                if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif")) {
                    echo ' Unknown Image extension ';
                    $errors = 1;
                } else {
                    $size = filesize($actual_file);
                    if ($size > MAX_SIZE * 1024) {
                        echo "You have exceeded the size limit";
                        $errors = 1;
                    }
                    if ($extension == "jpg" || $extension == "jpeg") {
                        $uploadedfile = $actual_file;
                        $src = imagecreatefromjpeg($uploadedfile);
                    } else if ($extension == "png") {
                        $uploadedfile = $actual_file;
                        $src = imagecreatefrompng($uploadedfile);
                    } else {
                        $uploadedfile = $actual_file;
                        $src = imagecreatefromgif($uploadedfile);
                    }
                    list($width, $height) = getimagesize($uploadedfile);
                    $newwidth = $nwidth;
                    $newheight = ($height / $width) * $newwidth;
                    $tmp = imagecreatetruecolor($newwidth, $newheight);
                    $newwidth1 = $nheight;
                    $newheight1 = ($height / $width) * $newwidth1;
                    $tmp1 = imagecreatetruecolor($width, $height);
                    imagecopyresampled($tmp, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                    imagecopyresampled($tmp1, $src, 0, 0, 0, 0, $width, $height, $width, $height);
                    $filename = $lpath . $uploaded_filename;
                    $filename1 = $small = $spath . $uploaded_filename;
                    imagejpeg($tmp1, $filename, 100);
                    imagejpeg($tmp, $filename1, 100);
                    imagedestroy($src);
                    imagedestroy($tmp);
                    imagedestroy($tmp1);
                }
            }
            //If no errors registred, print the success message
            if (isset($_POST['Submit']) && !$errors) {
                // mysql_query("update SQL statement ");
                echo "Image Uploaded Successfully!";
            }
        }

        function mkDirMainImage($moduleName, $mainId, $edit='') {
            $db = $this->databaseconnection();
            $imageNamePath = $_SERVER['DOCUMENT_ROOT'] . '/images/' . $moduleName;
            if ($moduleName == 'product') {
                $select = $db->select()->from(array('product_image'), array('*'))
                        ->where('product_id=' . $mainId);
                $result = $db->query($select)->fetchAll(); //echo "<pre>";print_r($result);exit;
            } else if ($moduleName == 'category') {
                $select = $db->select()->from(array('addcategory'), array('image_name'))
                        ->where('cat_id=' . $mainId);
                $result = $db->fetchRow($select);
                if ($edit == 'edit') {
                    $selectapikey = $db->select()->from(array('addcategory'), array('apikey'))
                            ->where('cat_id=' . $mainId);
                    $apikey = $db->fetchRow($selectapikey);
                    $_SESSION['SESSION']['ApiKey'] = $apikey['apikey'];
                    $selectuserid = $db->select()->from(array('username'), array('id'))
                            ->where("apikey='" . $apikey['apikey'] . "'");
                    $userid = $db->fetchrow($selectuserid);
                    $_SESSION['USER']['userId'] = $userid['id'];
                }
            } else if ($moduleName == 'brand') {
                $select = $db->select()->from(array('brand'), array('image_name' => 'brand_image'))
                        ->where('brand_id=' . $mainId);
                $result = $db->fetchRow($select);
                if ($edit == 'edit') {
                    $selectapikey = $db->select()->from(array('brand'), array('api_key'))
                            ->where('brand_id=' . $mainId);
                    $apikey = $db->fetchRow($selectapikey);
                    $_SESSION['SESSION']['ApiKey'] = $apikey['api_key'];
                    $selectuserid = $db->select()->from(array('username'), array('id'))
                            ->where("apikey='" . $apikey['api_key'] . "'");
                    $userid = $db->fetchrow($selectuserid);
                    $_SESSION['USER']['userId'] = $userid['id'];
                }
            }
            if (count($result) > 0) {
                $adminIdDir = floor($_SESSION['USER']['userId'] / 1000);
                $adminIdDirPath = getcwd() . IMAGE_SERVER_REAL . '/' . $adminIdDir;
                $apiKeyDirPath = $adminIdDirPath . '/' . $_SESSION['SESSION']['ApiKey'];
                $moduleDirPath = $apiKeyDirPath . '/' . $moduleName;
                $proIdDir = floor($mainId / 1000);
                $proIdDirPath = $moduleDirPath . '/' . $proIdDir;
                $productIdDirPath = $proIdDirPath . '/' . $mainId;
                $smallDir = $productIdDirPath . '/small';
                $mediumDir = $productIdDirPath . '/medium';
                $largeDir = $productIdDirPath . '/large';
                $enlargeDir = $productIdDirPath . '/enlarge';
                $cartDir = $productIdDirPath . '/cart';
                $thumbnailDir = $productIdDirPath . '/thumbnail';
                if (is_dir($adminIdDirPath)) {
                    if (is_dir($apiKeyDirPath)) {
                        if (is_dir($moduleDirPath)) {
                            if (is_dir($proIdDirPath)) {
                                if (is_dir($productIdDirPath)) {
                                    if (is_dir($smallDir)) {
                                        if (is_dir($mediumDir)) {
                                            if (is_dir($largeDir)) {
                                                if (is_dir($enlargeDir)) {
                                                    if (is_dir($cartDir)) {
                                                        if (is_dir($thumbnailDir)) {
                                                            //echo "Files already exists";
                                                        } else {
                                                            mkdir($thumbnailDir);
                                                        }
                                                    } else {
                                                        mkdir($cartDir);
                                                    }
                                                } else {
                                                    mkdir($enlargeDir);
                                                }
                                            } else {
                                                mkdir($largeDir);
                                            }
                                        } else {
                                            mkdir($mediumDir);
                                        }
                                    } else {
                                        mkdir($smallDir);
                                    }
                                } else {
                                    mkdir($productIdDirPath, 0777);
                                    mkdir($smallDir, 0777);
                                    mkdir($mediumDir, 0777);
                                    if ($moduleName == 'product') {
                                        mkdir($largeDir, 0777);
                                        mkdir($enlargeDir, 0777);
                                        mkdir($cartDir, 0777);
                                        mkdir($thumbnailDir, 0777);
                                    }
                                }
                            } else {
                                mkdir($proIdDirPath, 0777);
                                mkdir($productIdDirPath, 0777);
                                mkdir($smallDir, 0777);
                                mkdir($mediumDir, 0777);
                                if ($moduleName == 'product') {
                                    mkdir($largeDir, 0777);
                                    mkdir($enlargeDir, 0777);
                                    mkdir($cartDir, 0777);
                                    mkdir($thumbnailDir, 0777);
                                }
                            }
                        } else {
                            mkdir($moduleDirPath, 0777);
                            mkdir($proIdDirPath, 0777);
                            mkdir($productIdDirPath, 0777);
                            mkdir($smallDir, 0777);
                            mkdir($mediumDir, 0777);
                            if ($moduleName == 'product') {
                                mkdir($largeDir, 0777);
                                mkdir($enlargeDir, 0777);
                                mkdir($cartDir, 0777);
                                mkdir($thumbnailDir, 0777);
                            }
                        }
                    } else {
                        mkdir($apiKeyDirPath, 0777);
                        mkdir($moduleDirPath, 0777);
                        mkdir($proIdDirPath, 0777);
                        mkdir($productIdDirPath, 0777);
                        mkdir($smallDir, 0777);
                        mkdir($mediumDir, 0777);
                        if ($moduleName == 'product') {
                            mkdir($largeDir, 0777);
                            mkdir($enlargeDir, 0777);
                            mkdir($cartDir, 0777);
                            mkdir($thumbnailDir, 0777);
                        }
                    }
                } else {
                    mkdir($adminIdDirPath, 0777);
                    mkdir($apiKeyDirPath, 0777);
                    mkdir($moduleDirPath, 0777);
                    mkdir($proIdDirPath, 0777);
                    mkdir($productIdDirPath, 0777);
                    mkdir($smallDir, 0777);
                    mkdir($mediumDir, 0777);
                    if ($moduleName == 'product') {
                        mkdir($largeDir, 0777);
                        mkdir($enlargeDir, 0777);
                        mkdir($cartDir, 0777);
                        mkdir($thumbnailDir, 0777);
                    }
                }
                $mainpath = $productIdDirPath;
                if ($moduleName == 'product') {
                    foreach ($result as $key => $val) {
                        //foreach ($val as $ky => $vl) { //commented by tausif
                            $filename = $imageNamePath . '/' . $val['image_name'];
                            $extension = $this->getExtension($filename);
							$width=$val['image_width'];
							$height=$val['image_height'];
                            //list($width, $height) = getimagesize($filename);
                            $image_p = imagecreatetruecolor($width, $height);
                            if ($extension == "jpg" || $extension == "jpeg") {
                                $imageOuter = imagecreatefromjpeg($filename);
                            } else if ($extension == "png") {
                                $imageOuter = imagecreatefrompng($filename);
                            } else {
                                $imageOuter = imagecreatefromgif($filename);
                            }
                            //$imageOuter = imagecreatefromjpeg($filename);
                            $mainPathOuter = $mainpath . '/' . $val['image_name'];
                            imagecopyresampled($image_p, $imageOuter, 0, 0, 0, 0, $width, $height, $width, $height);
                            imagejpeg($image_p, $mainPathOuter, 100);
                            $this->setMainImageSize($val['image_name'], $mainpath, $filename, $moduleName, $imageOuter, $mainId);
                      //  }
                    }
                } else if (($moduleName == 'category') || ($moduleName == 'brand')) {
                    //foreach($result as $key=>$val){
                    $filename = $imageNamePath . '/' . $result['image_name'];
                    $extension = $this->getExtension($filename);
                    list($width, $height) = getimagesize($filename);
                    $image_p = imagecreatetruecolor($width, $height);
                    if ($extension == "jpg" || $extension == "jpeg") {
                        $imageOuter = imagecreatefromjpeg($filename);
                    } else if ($extension == "png") {
                        $imageOuter = imagecreatefrompng($filename);
                    } else {
                        $imageOuter = imagecreatefromgif($filename);
                    }
                    $mainPathOuter = $mainpath . '/' . $result['image_name'];
                    imagecopyresampled($image_p, $imageOuter, 0, 0, 0, 0, $width, $height, $width, $height);
                    imagejpeg($image_p, $mainPathOuter, 100);
                    $this->setMainImageSize($result['image_name'], $mainpath, $filename, $moduleName, $imageOuter);
                    //}
                }
            }
        }

        function setMainImageSize($imagename, $mainpath, $imagepathname, $moduleName, $imageOuter='', $mainId='') {
            $db = Zend_Db_Table::getDefaultAdapter();
            $imageLarge = $imageMedium = $imageSmall = $imageOuter;
            $filename = $imagepathname; //$_SERVER['DOCUMENT_ROOT'].'\images\product/'.$imagename;
            // Content type
            header('Content-Type: image/jpeg');
            // Get new dimensions
            list($width, $height) = getimagesize($filename);
            // Resample
            //$extension = $this->getExtension($filename);
            /* if($extension=="jpg" || $extension=="jpeg" ){
              $imageLarge = imagecreatefromjpeg($filename);
              $imageMedium = imagecreatefromjpeg($filename);
              $imageSmall = imagecreatefromjpeg($filename);
              }else if($extension=="png"){
              $imageLarge = imagecreatefrompng($filename);
              $imageMedium = imagecreatefrompng($filename);
              $imageSmall = imagecreatefrompng($filename);
              }else{
              $imageLarge = imagecreatefromgif($filename);
              $imageMedium = imagecreatefromgif($filename);
              $imageSmall = imagecreatefromgif($filename);
              } */
            $mainPathEnlarge = $mainpath . '/enlarge/' . $imagename;
            $mainPathLarge = $mainpath . '/large/' . $imagename;
            $mainPathMedium = $mainpath . '/medium/' . $imagename;
            $mainPathSmall = $mainpath . '/small/' . $imagename;
            $mainPathThumbnail = $mainpath . '/thumbnail/' . $imagename;
            $mainPathCart = $mainpath . '/cart/' . $imagename;

            if ($moduleName == 'product') {
                $imagesize = $this->getimagedimension($filename, $moduleName, '250', '250');
                $largeWidth = $imagesize[0];
                $largeHeight = $imagesize[1];
                $image_l = imagecreatetruecolor($largeWidth, $largeHeight);
                imagecopyresampled($image_l, $imageLarge, 0, 0, 0, 0, $largeWidth, $largeHeight, $width, $height);
                imagejpeg($image_l, $mainPathLarge, 80);

                $imagesize = $this->getimagedimension($filename, $moduleName, '60', '60');
                $smallWidth = $imagesize[0];
                $smallHeight = $imagesize[1];
                $image_s = imagecreatetruecolor($smallWidth, $smallHeight);
                imagecopyresampled($image_s, $imageMedium, 0, 0, 0, 0, $smallWidth, $smallHeight, $width, $height);
                imagejpeg($image_s, $mainPathSmall, 80);

                $imagesize = $this->getimagedimension($filename, $moduleName, '100', '100');
                $smallWidth = $imagesize[0];
                $smallHeight = $imagesize[1];
                $image_c = imagecreatetruecolor($smallWidth, $smallHeight);
                imagecopyresampled($image_c, $imageMedium, 0, 0, 0, 0, $smallWidth, $smallHeight, $width, $height);
                imagejpeg($image_c, $mainPathCart, 80);

                $imagesize = $this->getimagedimension($filename, $moduleName, '800', '800');
                $enlargeWidth = $imagesize[0];
                $enlargeHeight = $imagesize[1];
                $image_e = imagecreatetruecolor($enlargeWidth, $enlargeHeight);
                imagecopyresampled($image_e, $imageLarge, 0, 0, 0, 0, $enlargeWidth, $enlargeHeight, $width, $height);
                imagejpeg($image_e, $mainPathEnlarge, 80);

                $imagesize = $this->getimagedimension($filename, $moduleName, '32', '32');
                $verysmallWidth = $imagesize[0];
                $verysmallHeight = $imagesize[1];
                $image_t = imagecreatetruecolor($verysmallWidth, $verysmallHeight);
                imagecopyresampled($image_t, $imageSmall, 0, 0, 0, 0, $verysmallWidth, $verysmallHeight, $width, $height);
                imagejpeg($image_t, $mainPathThumbnail, 80);
            }

            if ($moduleName == 'brand' || $moduleName == 'product') {
                $imagesize = $this->getimagedimension($filename, $moduleName, '60', '60');
                $brandsmallWidth = $imagesize[0];
                $brandsmallHeight = $imagesize[1];
                $image_bs = imagecreatetruecolor($brandsmallWidth, $brandsmallHeight);
                imagecopyresampled($image_bs, $imageSmall, 0, 0, 0, 0, $brandsmallWidth, $brandsmallHeight, $width, $height);
                imagejpeg($image_bs, $mainPathSmall, 80);
            }
            $imagesize = $this->getimagedimension($filename, $moduleName, '132', '132');
            $mediumWidth = $imagesize[0];
            $mediumHeight = $imagesize[1];
            $image_m = imagecreatetruecolor($mediumWidth, $mediumHeight);
            imagecopyresampled($image_m, $imageMedium, 0, 0, 0, 0, $mediumWidth, $mediumHeight, $width, $height);
            imagejpeg($image_m, $mainPathMedium, 80);
            if ($moduleName == 'product') {
                $imagepath = $this->getImageLocationFromDir($mainId, 'product');
                $update = "update " . TABLE_PRODUCTS_IMAGE . " set image_location='" . $imagepath . "' where product_id=" . $mainId;
                $db->query($update);
                /* echo "<script type='text/jscript'> window.location ='/admin/product/#manageproduct' </script> return false;"; */
            }
        }

        function getUserDetailFromId($id, $type) {
            $returnArray = array();
            $db = Zend_Db_Table::getDefaultAdapter();
            if ($type == 'product') {
                $select = $db->select()->from(array('product'), array('apikey' => 'seller_id'))
                        ->where('id=' . $id);
            } else if ($type == 'category') {
                $select = $db->select()->from(array('addcategory'), array('apikey'))
                        ->where('cat_id=' . $id);
            } else if ($type == 'brand') {
                $select = $db->select()->from(array('brand'), array('apikey' => 'api_key'))
                        ->where('brand_id=' . $id);
            } else if ($type == 'user') {
                $select = $db->select()->from(array('username'), array('apikey'))
                        ->where('id=' . $id);
            }
            $result = $db->fetchRow($select);
            if (count($result) > 0) {
                $returnArray[0] = $result['apikey'];
                $select = $db->select()->from(array('username'), array('id'))
                        ->where("apikey='" . $result['apikey'] . "'");
                $resultSet = $db->fetchRow($select);
                //$returnArray[1] = $resultSet['id'];
				 $returnArray[1] = $_SESSION['USER']['userId'];
			}
            return $returnArray;
        }

        function getImageFromDir($id, $type, $imageType, $noOfImage='', $disputeid='') {
            $db = Zend_Db_Table::getDefaultAdapter();
            $imagePathArray = array();
            $uservalue = $this->getUserDetailFromId($id, $type);
            $adminIdDir = floor($uservalue[1] / 1000);
            $proIdDir = floor($id / 1000);
            $productIdDirPathCheck = getcwd() . IMAGE_SERVER_REAL . '/' . $adminIdDir . '/' . $uservalue[0] . '/' . $type . '/' . $proIdDir . '/' . $id;
            $productIdDirPath = IMAGE_SERVER . '/' . $adminIdDir . '/' . $uservalue[0] . '/' . $type . '/' . $proIdDir . '/' . $id;
            if ($type == 'product') {
                //if(!is_dir($productIdDirPathCheck)){
                //$defaultPath[] = IMAGE_SERVER.'/'.$imageType.'_product_img_default.gif';
                //return $defaultPath;
                //}
                $select = $db->select()->from(array('product_image'), array('*'))
                        ->where('product_id=' . $id);
                $result = $db->query($select)->fetchAll();
                if ($noOfImage == 1) {
                    foreach ($result as $key => $val) {
                        foreach ($val as $ky => $vl) {
                            if ($val['image_type'] == 1) {
                                $mainImageName = $val['image_name'];
                                if ($val['image_location'] != '') {
                                    $imagePathArray[] = $val['image_location'] . '/' . $imageType . '/' . $mainImageName;
                                    break;
                                } else if ($imageType != '') {
                                    $imagePathArray[] = $productIdDirPath . '/' . $imageType . '/' . $mainImageName;
                                    break;
                                } else {
                                    $imagePathArray[] = $productIdDirPath . '/' . $mainImageName;
                                    break;
                                }
                            }
                        }
                    }
                } else {
                    foreach ($result as $key => $val) {
                        if ($val['image_location'] != '') {
                            $imagePathArray[] = $val['image_location'] . '/' . $imageType . '/' . $val['image_name'];
                        } else if ($imageType != '') {
                            $imagePathArray[] = $productIdDirPath . '/' . $imageType . '/' . $val['image_name'];
                        } else {
                            $imagePathArray[] = $productIdDirPath . '/' . $val['image_name'];
                        }
                    }
                }
            } else if (($type == 'category') || ($type == 'brand') || ($type == 'user')) {
                if ($type == 'category') {
                    if (!is_dir($productIdDirPathCheck)) {
                        $defaultPath[] = IMAGE_SERVER . '/' . $imageType . '_category_img_default.gif';
                        return $defaultPath;
                    }
                    $tableName = 'addcategory';
                    $tableid = 'cat_id=' . $id;
                    $field = array('image_name');
                } else if ($type == 'brand') {
                    if (!is_dir($productIdDirPathCheck)) {
                        $defaultPath[] = IMAGE_SERVER . '/' . $imageType . '_brand_img_default.gif';
                        return $defaultPath;
                    }
                    $tableName = 'brand';
                    $tableid = 'brand_id=' . $id;
                    $field = array('image_name' => 'brand_image');
                } else if ($type == 'user') {
                    if (!is_dir($productIdDirPathCheck)) {
                        $defaultPath[] = IMAGE_SERVER . '/' . $imageType . '_user_img_default.gif';
                        return $defaultPath;
                    }
                    $tableName = 'user';
                    $tableid = 'id=' . $id;
                    $field = array('image_name' => 'user_image');
                }
                $select = $db->select()->from(array($tableName), $field)
                        ->where($tableid);
                $result = $db->fetchRow($select);
                if ($imageType != '') {
                    $imagePathArray[] = $productIdDirPath . '/' . $imageType . '/' . $result['image_name'];
                } else if ($imageType == '') {
                    $imagePathArray[] = $productIdDirPath . '/' . $result['image_name'];
                }
            } else if ($type == 'dispute') {

            }
            return $imagePathArray;
        }

        function getImageLocationFromDir($id, $type) {
            $db = Zend_Db_Table::getDefaultAdapter();
            //$imagePathArray = array();
            $uservalue = $this->getUserDetailFromId($id, $type);
            $adminIdDir = floor($uservalue[1] / 1000);
            $proIdDir = floor($id / 1000);
            $productIdDirPathCheck = getcwd() . IMAGE_SERVER_REAL . '/' . $adminIdDir . '/' . $uservalue[0] . '/' . $type . '/' . $proIdDir . '/' . $id;
            $productIdDirPath = CACHE_CDN . IMAGE_SERVER_REAL . '/' . $adminIdDir . '/' . $uservalue[0] . '/' . $type . '/' . $proIdDir . '/' . $id;
            return $productIdDirPath;
        }

        function unlinkImageFromDir($id, $typename, $imageName) {
            $adminIdDir = floor($_SESSION['USER']['userId'] / 1000);
            $adminIdDirPath = IMAGE_SERVER . '/' . $adminIdDir;
            $apiKeyDirPath = $adminIdDirPath . '/' . $_SESSION['SESSION']['ApiKey'];
            $moduleDirPath = $apiKeyDirPath . '/' . $typename;
            $proIdDir = floor($id / 1000);
            $proIdDirPath = $moduleDirPath . '/' . $proIdDir;
            $productIdDirPath = $proIdDirPath . '/' . $id;
            unlink($productIdDirPath . '/' . $imageName);
            unlink($productIdDirPath . '/enlarge/' . $imageName);
            unlink($productIdDirPath . '/large/' . $imageName);
            unlink($productIdDirPath . '/medium/' . $imageName);
            unlink($productIdDirPath . '/small/' . $imageName);
            unlink($productIdDirPath . '/cart/' . $imageName);
        }

        function displayerrorpage($link) {
            return $var = '<div class="entity_mainContainer"><div class="lh100">&nbsp;</div><div class="entity_mainHeading">Entity Not Found</div><div class="lh10">&nbsp;</div><div class="leftPanel"><div class="leftBox"><div class="leftBox_Heading">The page you are looking for appears to have been moved, deleted or does not exist.</div><div class="lh25">&nbsp;</div><div class="contentText">This is most likely due to:</div><div class="lh15">&nbsp;</div><div class="clearBoth"><ul><li>An outdated link</li><li>A typo in address/url</li></ul></div><div class="lh15">&nbsp;</div></div><div class="lh15">&nbsp;</div><div class="clearBoth"><div class="floatRight"><a href="' . $link . '" title="Back to list" class="hyperLink">Back to list</a></div></div></div></div>';
        }

//Name:Nagendra Yadav
//Date:28-8-2011
//Reason:To get all published testimonials which is approved by admin
        function getpublishedstatus($reid, $storeid) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $query = "select * from published_review where apikey='" . $_SESSION['SESSION']['ApiKey'] . "' and review_id IN(SELECT DISTINCT pr.review_id FROM product_review pr,product p,username u Where pr.status='2' AND pr.type_id=p.id AND pr.review_id='$reid' AND pr.store_id='$storeid')";
            $result = $db->fetchAll($query);

            if (count($result) > 0) {
                $rec = 1;
                return $rec;
            } else {
                return 0;
            }
        }

//Name:Nagendra Yadav
//Date:29-8-2011
//Reason:To get average rating on a particular product
        function getaveragerating($productid, $storeid) {
            return 0;
            $db = Zend_Db_Table::getDefaultAdapter();
            $selects = "SELECT  avg(pr.review_marks) FROM product_review pr,product p,username u Where pr.status='2' AND pr.type_id='" . $productid . "' AND pr.store_id='" . $storeid . "' AND pr.type_id=p.id";
            $result = $db->fetchOne($selects);

            return round($result);
        }

//Name:Nagendra Yadav
//Date:30-8-2011
//Reason:To calculate total number of yes marks on a particular reviiew record
        function getyesrating($reid) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $selectsyes = "SELECT  count(*) as yesrate FROM published_review pr Where pr.yes_no_status='1' and pr.review_id='$reid'";
            $resultyes = $db->fetchOne($selectsyes);
            return $resultyes;
        }

//Name:Nagendra Yadav
//Date:31-8-2011
//Reason:To calculate total number of marks on a particular reviiew record
        function gettotalrating($reid) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $selectstotal = "SELECT  count(*) as totalrate FROM published_review pr Where pr.review_id='$reid'";
            $resulttotal = $db->fetchOne($selectstotal);
            return $resulttotal;
        }
		/*
        function displayMenuNew() {
            //print_r($CLASS);
            $storeurl = func_get_arg(0);
            $api_key = $_SESSION['USER']['stores'][0]['store_apikey'];
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()->from(array('menu'))
                    ->where("parent_id = 0 and mtype = 0 ")
                    ->order("id asc");
            $result = $db->fetchAll($select);
            $this->checkUserModuleActionPermission($_SESSION['SESSION']['ApiKey'], $_SESSION['USER']['userDetails'][0]['user_email_address'], 1);
            $all_permissions = $this->all_permissions;
            //echo '<pre>';print_r($all_permissions);
            $xyz = '<div class="HTabBlock5">';
            foreach ($result as $key) {
                $menuname = strtolower($key["name"]);
                $selectsubmenu = $db->select()->from(array('menu'))
                        ->where("parent_id = " . $key[id]);
                $resultsubmenu = $db->fetchAll($selectsubmenu);

                if (sizeof($resultsubmenu) > 0) {
                    $xyz .= '<div class="' . $key["name"] . 'Tab maintab" rel=' . strtolower($key["name"]) . '>';
                    $xyzTemp = '';
                    $submenuheight = 0;
                    foreach ($resultsubmenu as $keymenu) {
                        if ($menuname == "design") {
                            $page_map = array(21 => 'storedesign', 22 => 'pages', 41 => 'banners');
                            $keymenu['link'] = DESIGN_SERVER . '/' . $page_map[$keymenu['id']] . "?sessid=" . Zend_Session::getId() . "&key=" . $api_key;
                        }
						if($_SESSION['USER']['stores'][0]['role']==2){
                        //if(isset($all_permissions[$keymenu['module_id'].'_'.$keymenu['action_id']])){
                        $xyzTemp.= '<a class="aclass" href="' . $keymenu['link'] . '"><div>' . $keymenu['name'] . '</div></a>';
                        $submenuheight+=22;
                        //}
						} else {
							if(isset($all_permissions[$keymenu['module_id'].'_'.$keymenu['action_id']])){
                        $xyzTemp.= '<a class="aclass" href="' . $keymenu['link'] . '"><div>' . $keymenu['name'] . '</div></a>';
                        $submenuheight+=22;
                        }
						}
                    }
                    $xyz .= '<div id="' . $menuname . 'id" class="' . $menuname . 'Tabmenu submenu" rel=' . strtolower($menuname) . ' style="height:' . $submenuheight . 'px">';
                    $xyz .=$xyzTemp;
                    $xyz .= '</div>';

                    $xyz .= '</div>';
                    $xyz .= '<a style="cursor:pointer;" id="' . $menuname . '" ><div class="atab ' . $key["name"] . 'TabArrow">&nbsp;</div></a>';
                } else {
                    if ($key["name"] == 'Viewstore')
                        $xyz .='<a href="' . $storeurl . '" target="_blank" title="View Store"><div class="' . $key["name"] . 'Tab maintab Viewstore" title="View Store"></div></a>';
                    else
                        $xyz .='<a href="' . $key["link"] . '"><div class="' . $key["name"] . 'Tab maintab"></div></a>';
                }
            }
            $xyz .='<div class="o2ojsblock">&nbsp;</div></div>';
            return $xyz;
        }
*/
function displayMenuNew(){
		//print_r($CLASS);
		$storeurl=func_get_arg(0);
		$role=func_get_arg(1);	
		$url = '';
		$apikey=func_get_arg(2);	
		$useremailaddress=func_get_arg(3);	
		$db = Zend_Db_Table::getDefaultAdapter();

			$select = $db->select()->from(array('menu2'))
			->where("parent_id = 0 and mtype = 0 ")
			->order("id asc");
			$result = $db->fetchAll($select);
			if($role!=2){
                		unset($result[3]);
        		}

			$this->checkUserModuleActionPermission($apikey,$useremailaddress,1);
			$all_permissions=$this->all_permissions;
			$selectsubmenu = $db->select()->from(array('menu2'))
		->where("parent_id <> 0");
		$resultsubmenu = $db->fetchAll($selectsubmenu);
		//	echo '<pre>';print_r($all_permissions);exit;

			$xyz = '<div class="HTabBlock5">';
			foreach($result as $key){
			// if tab view permission exists or not
			// patched to implement the module action permission
			$menuname = strtolower($key["name"]);
							$selectsubmenu = $db->select()->from(array('menu2'))
					->where("parent_id = ".$key[id]);
					$resultsubmenu = $db->fetchAll($selectsubmenu);

					$checkactionExists = false;
					if($role==2){
						$checkactionExists = true;
						} else {
						if($storeurl=='http://www.buyer.mygoo2o.com')
						{
							//echo "<pre>";
						//	print_r($all_permissions);

						//	print_r($resultsubmenu);
								//echo $key[id];
						}
					foreach($resultsubmenu as $mykey){

						foreach($all_permissions as $per){
							if($per['action_id']==$mykey['action_id']){

							if(isset($all_permissions[$mykey['module_id'].'_'.$mykey['action_id']])){
								$checkactionExists = true;

							}	//echo $key[id];


							}

						}
					}
					}
					if(!$checkactionExists){
						// hate to do hardcode, but no option
						if($key["name"] == 'Overview' || $key["name"] == 'Viewstore'){
							$checkactionExists = true;
						}
						//$resultsubmenu = '';
						//echo $key["name"];
					}
					if($checkactionExists){

					if($menuname == "design")
					{
						$sessid = Zend_Session::getId();
						$api_key = $apikey;
						$url = "?sessid=$sessid&key=$api_key";
					}

					if(sizeof($resultsubmenu) > 0){
					if($storeurl=='http://www.buyer.mygoo2o.com')
						{
							//echo "<pre>";
							//print_r($resultsubmenu);
							//echo strtolower($key["name"]).'_'.sizeof($resultsubmenu);
						}
						//echo sizeof($resultsubmenu);
						$xyz .= '<div class="'.$key["name"].'Tab maintab" rel='.strtolower($key["name"]).'>';
						$xyzTemp='';
						$submenuheight=0;
						foreach($resultsubmenu as $keymenu){

if($keymenu['name']=='Products' || $keymenu['name']=='Store category' || $keymenu['name']=='Store brand' || $keymenu['name']=='Product Features' || $keymenu['name']=='Product Variations' || $keymenu['name']=='Reviews' || $keymenu['name']=='Testimonials' || $keymenu['name']=='Forms' || $keymenu['name']=='Discount coupons' || $keymenu['name']=='SEO suite' || $keymenu['name']=='Product Feature' || $keymenu['name']=='Product' || $keymenu['name']=='Category' || $keymenu['name']=='Brand' || $keymenu['name']=='Product Variation' || $keymenu['name']=='Testimonial' || $keymenu['name']=='Form')
			{
				$root='http://catalog.eshopbox.com';
			}
			else if($keymenu['name']=='Orders' || $keymenu['name']=='Returns' || $keymenu['name']=='Shipments' || $keymenu['name']=='Invoices' || $keymenu['name']=='Invoice' )
			{
				$root='http://orders.eshopbox.com';
			}
			else if($keymenu['name']=='Overview') 
			{
				$root='http://login.eshopbox.com';
			}
			else
			{
				$root='';
			}



							if($role==2){

						//	if(isset($all_permissions[$keymenu['module_id'].'_'.$keymenu['action_id']])){
			
                        $xyzTemp.=        '<a class="aclass" href="'.$root.trim($keymenu['link']).$url.'"><div>'.$keymenu['name'].'</div></a>';
						$submenuheight+=22;
						//}
						} else {

						if(isset($all_permissions[$keymenu['module_id'].'_'.$keymenu['action_id']])){
                        $xyzTemp.=        '<a class="aclass" href="'.$root.$keymenu['link'].$url.'"><div>'.$keymenu['name'].'</div></a>';
						$submenuheight+=22;
						}

							}
						}
                        $xyz .= '<div id="'.$menuname.'id" class="'.$menuname.'Tabmenu submenu" rel='.strtolower($menuname).' style="height:'.$submenuheight.'px">';
						$xyz .=$xyzTemp;
                        $xyz .=    		'</div>';

						$xyz .= '</div>';
                        $xyz .= '<a style="cursor:pointer;" id="'.$menuname.'" ><div class="atab '.$key["name"].'TabArrow">&nbsp;</div></a>';

 			} else {
				if($key["name"]=='Viewstore')
				$xyz .='<a href="'.$storeurl.'" target="_blank" title="View Store"><div class="'.$key["name"].'Tab maintab Viewstore" title="View Store"></div></a>';
				else
				$xyz .='<a href="'.$root.$key["link"].$url.'"><div class="'.$key["name"].'Tab maintab"></div></a>';
			}
					}
			}
$xyz .='<div class="o2ojsblock">&nbsp;</div></div>';
return $xyz;
	}

        function getProductVariationDetail($productid) {
            $db = Zend_Db_Table::getDefaultAdapter();
            //$productid=$productid+60;
            //$productid=61;
            $get_variant_data = "SELECT * FROM  product_variation WHERE product_id = '" . $productid . "' order by id";
            $variant_data = $db->query($get_variant_data)->fetchAll();
            //$reversedata=array_reverse($variant_data);
            //echo $reversedata[0]['variation_code'];
            $codevalue = 1;
            $j = 0;
            $separator = '';
            foreach ($variant_data as $key => $value) {
                if ($value['variation_code'] != $codevalue) {
                    $j = $j + 1;
                    $separator = '';
                }
                if ($value['variant_name'] == 'MRP' || $value['variant_name'] == 'SRP' || $value['variant_name'] == 'Stock' || $value['variant_name'] == 'Description' || $value['variant_name'] == 'Condition') {
                    $data[$j][$value['variant_name']] = $value['variant_value'];
                } else {
                    if ($data[$j]['value'] != '')
                        $separator = "-";
                    $data[$j]['value'] .= $separator . $value['variant_value'];
                }

                //$data[$j][$value['variant_name']]=$value['variant_value'];
                $codevalue = $value['variation_code'];
            }

            //echo "<pre>";
            //print_r($data);exit;
            return $data;
        }

        function displaySuperAdminMenu() {
            $args = func_get_args();
            $controllerName = $args[0];
            //print_r($CLASS);
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()->from(array('menu2'))
                    ->where("parent_id = 0 and mtype = 1 ")
                    ->order("id asc");
            $result = $db->fetchAll($select);


            $xyz = '<ul>';
            foreach ($result as $key) {
                $menuname = strtolower(str_replace(" ", "", $key['name']));
                if ($menuname == $controllerName) {
                    $xyz .='<li class="selected"><a href="' . $key['link'] . '">' . $key['name'] . '</a></li>';
                } else {
                    $xyz .='<li><a href="' . $key['link'] . '">' . $key['name'] . '</a></li>';
                }
            }
            $xyz .="</ul>";
            return $xyz;
        }

        function getUserBasicDetails() {
            $db = Zend_Db_Table::getDefaultAdapter();
            $args = func_get_args();
            $userId = $args[0];
            $select = "select * from user where id=" . $userId;

            $result = $db->fetchAll($select);
            //print_r($result);


            return $result;
        }

        function swapmessages() {
            $db = Zend_Db_Table::getDefaultAdapter();
            $args = func_get_args();
            $type = trim($args[0]); // type of message depends on the module
            $string = trim($args[1]);
            switch ($type) {
                case 'cancellation' :
                    switch ($string) {
                        case "I don't want this item anymore":
                            $returnString = "Customer refused to accept the product";
                            break;

                        case "I made a wrong choice of item":
                            $returnString = "Customer made a wrong choice of product";
                            break;

                        case "I will not be available on the mentioned shipping address for sometime":
                            $returnString = "Customer will not be available on his mentioned shipping address";
                            break;

                        case "I have a different reason to cancel":
                            $returnString = "Customer has some other reason for cancellation";
                            break;
                    }
                    break;
            }

            return $returnString;
        }

// 1 : dispute raised
// 2 : claim
// 3  : solved
// 4 : closed


        function getDisputeStatus() {
            $args = func_get_args();
            $disputeStatus = $args[0];

            switch ($disputeStatus) {
                case 1 :
                    $status = "Dispute Raised";
                    break;

                case 2 :
                    $status = "Claim Raised";
                    break;

                case 3 :
                    $status = "Solved";
                    break;

                case 4 :
                    $status = "Closed";
                    break;
            }

            return $status;
        }

        function createDisputeImageFolder() {
            $args = func_get_args();
            $disputeId = $args[0];
            $adminIdDir = floor($_SESSION['USER']['userId'] / 1000);
            $adminIdDirPath = IMAGE_SERVER . '/' . $adminIdDir;
            $apiKeyDirPath = $adminIdDirPath . '/' . $_SESSION['SESSION']['ApiKey'];
            $moduleDirPath = $apiKeyDirPath . '/disputes';
            if (is_dir($moduleDirPath)) {
                $dispuetdir = $moduleDirPath . '/' . $disputeId;
                if (is_dir($dispuetdir)) {
                    // do nothing
                } else {
                    mkdir($dispuetdir, 0777);
                }
            } else {
                mkdir($moduleDirPath, 0777);
                $dispuetdir = $moduleDirPath . '/' . $disputeId;
                mkdir($dispuetdir, 0777);
            }
        }

        function getDisputeUploadedFilesPath() {
            // assuming file exists
            $args = func_get_args();
            $disputeId = $args[0];
            $adminIdDir = floor($_SESSION['USER']['userId'] / 1000);
            $adminIdDirPath = IMAGE_SERVER . '/' . $adminIdDir;
            $apiKeyDirPath = $adminIdDirPath . '/' . $_SESSION['SESSION']['ApiKey'];
            $moduleDirPath = $apiKeyDirPath . '/disputes';
            if (is_dir($moduleDirPath)) {
                $dispuetdir = $moduleDirPath . '/' . $disputeId . '/';
                return $dispuetdir;
            }
        }

        function displayError($errorHeading, $errorContent, $home) {
            $backHome = ' <a href="' . $home . '">Go Home</a>';
            $error = '<div class="error_page"><div class="error_heading">' . $errorHeading . '</div><div class="error_description">' . $errorContent . $backHome . '</div></div>';
            return $error;
        }

        function getIndividualPattern($valueid, $valuename, $pageinfoflag, $page) {
            switch ($page) {
                case 'category':
                    $valueflag = 1;
                    break;
                case 'brands':
                    $valueflag = 2;
                    break;
                case 'product':
                    $valueflag = 3;
                    break;
                case 'mypages':
                    $valueflag = 4;
                    break;
            }
            $db = Zend_Db_Table::getDefaultAdapter();
            $sql = "select * from " . TABLE_SEO_INDIVIDUAL_PATTERN . " where apikey='" . $_SESSION[SESSION][ApiKey] . "' and value_flag='" . $valueflag . "' and value_id='" . $valueid . "' and page_info_flag='" . $pageinfoflag . "' and delete_flag='1'";
            $result = $db->query($sql)->fetchAll();
            return $result;
        }

        function displayDynamicContentForLandingPages() {
            $args = func_get_args();
            $case = $args[0];
            $hyperlink = $args[1];
            $capitalcase = $args[2];
            $api_key = $_SESSION['USER']['stores'][0]['store_apikey'];
            $dir = "admin";
            switch ($case) {
                case "design" :
                    $type = 5;
                    break;

                case "promote":
                    $type = 6;
                    break;

                case "create":
                    $type = 4;
                    break;

                case "manage":
                    $type = 2;
                    break;

                case "myaccount" :
                    $type = 2;
                    $dir = "myaccount";
                    break;

                default :
                    $type = 5;
                    break;
            }

            $db = Zend_Db_Table::getDefaultAdapter();

            if ($case == "myaccount") {

                $select = $db->select()->from(array('menu2'))
                        ->where(" mtype = 2 ")
                        ->order("priority asc");
            } else {
                $select = $db->select()->from(array('menu2'))
                        ->where("parent_id = " . $type . " and mtype = 0 ")
                        ->order("priority asc");
            }

            $result = $db->fetchAll($select);

            //print_r($result);
            $numRecords = sizeof($result);
            if ($numRecords > 0) { // there is slight possibility for this condition to fail but who knows
                $i = 1;
                foreach ($result as $key => $value) {
                    if ($capitalcase) {
                        $headingText = strtoupper($value['name']);
                    } else {
                        $headingText = strtolower($value['name']);
                    }
                    if($headingText=='orders' || $headingText=='returns' || $headingText=='shipments' || $headingText=='invoices' || $headingText=='invoice'){
                        $mainvalue='http://orders.eshopbox.com';
                    }else{
                        $mainvalue='http://catalog.eshopbox.com';
                    }
                    $filename = $case . '_' . strtolower(str_replace(' ', '_', $value['name']));
                    //echo $_SERVER['DOCUMENT_ROOT']."/images/admin/".$case."/admin_".$filename;
                    //echo HTTP_SERVER."/images/admin/".$filename.".jpg";
                    //echo $_SERVER['DOCUMENT_ROOT']."/images/".$dir."/".$case."/".$filename.".jpg";
                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/images/" . $dir . "/" . $case . "/" . $filename . ".jpg")) {
                        //echo "m2";
                    } else {
                        $filename = "design_overveiw_img1";
                    }
                    $abc .='
				<div class="wid312">
                <div class="landing_topborder"></div>
                <div class="lh20">&nbsp;</div>
                ';
                    if ($hyperlink) {
                        if ($case == 'design') {
                            $page_map = array(21 => 'storedesign', 22 => 'pages', 41 => 'banners');
                            $value['link'] = DESIGN_SERVER . '/' . $page_map[$value['id']] . "?sessid=" . Zend_Session::getId() . "&key=" . $api_key;
                        }
                        $abc .='<div class="clearBoth" style="cursor:pointer;"><a href="' .$mainvalue.$value['link'] . '" title="' . $headingText . '"><img src="/images/' . $dir . '/' . $case . '/' . $filename . '.jpg" alt="' . $headingText . '" /></a></div>';
                    } else {
                        $abc .='<div class="clearBoth" ><img src="/images/' . $dir . '/' . $case . '/' . $filename . '.jpg" alt="' . $headingText . '" /></div>';
                    }
                    $abc .='<div class="lh20">&nbsp;</div>
                <div class="clearBoth" style="cursor:pointer;"><a href="' .$mainvalue. $value['link'] . '" class="landingpage_link" title="' . $headingText . '">' . $headingText . '</a></div>
                <div class="lh12"></div>
                <div class="img_textDescription">' . $value['description'] . '</div>

            </div>';
                    if ($i % 3 == 0) {
                        $abc .='<div class="lh38">&nbsp;</div>';
                    } else {
                        $abc .='<div class="wid32">&nbsp;</div>';
                    } $i++;
                }
            }
            return $abc;
        }

//for no content message
        function nocontentMessage($value, $url='', $status='') {
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()->from(array('nocontent_message'))
                    ->where('value = "' . $value . '"');
            $result = $db->fetchRow($select);
            $value = '<div class="nocontent_box">
	<div class="floatLeft">
    	<div class="nocontent_icon"><img src="/images/admin/' . $value . '_nocontent.png" /></div>
        <div class="floatLeft">
        	<div class="floatLeft nocontent_content">' . $result['nocontent_message'] . '</div>
            <div class="lh20">&nbsp;</div>';
            if ($result['sub_message'] != '') {
                $value.='<div class="floatLeft nocontent_content2">' . $result['sub_message'] . '</div>
                 <div class="lh20">&nbsp;</div>';
            }
            if ($status == 'yes')
                $value.='<div class="floatLeft"><a href="' . $url . '" class="nocontent_continue_btn" title="Continue"></a></div>
        </div>
    </div>
</div>';
            return $value;
        }

//Name:Nagendra Yadav
//Date:29-10-2011
//Reason:To check user email verification globly .it will work on each admin header page
      function checkuserverification($vcodes){
        $orilogin = new Zend_Session_Namespace('original_login');
        $db = Zend_Db_Table::getDefaultAdapter();

        $query="select * from user where vcode='".$vcodes."' AND email_verification='0'";
         $result = $db->fetchAll($query);
       $usrmail=$result['0']['user_email_address'];
        $usrjoindate=$result[0]['user_join_date'];
        $date_diff= time() - strtotime(date('d-m-Y',strtotime($usrjoindate)));
        $datediff=floor($date_diff/(60 * 60 * 24));

        $verifyusernum=count($result);
        if(($verifyusernum>0) && $datediff<=5)
        {
            return '1';
        }
        else
        {
            return '0';
        }

    }//end of function


//end of function

        function getCategoryName($mainId) {
            $db = $this->databaseconnection();
            $select = $db->select()->from(array('addcategory'), array('cat_name'))
                    ->where('cat_id=' . $mainId);
            $result = $db->fetchRow($select);
            return $result['cat_name'];
        }

        function getApiDetails($email_id) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $select_store = "SELECT ur.*,md.* FROM user_role AS ur INNER JOIN mall_detail AS md ON ur.store_apikey = md.apikey WHERE ur.deleted_flag='0' and ur.status='1' and  email_id = '" . $email_id . "' ORDER BY ur.role";
            $result_store = $db->fetchAll($select_store);
            return $result_store;
        }

        function getuserimage($userid, $imgheight, $imgwidth) {
            $db = Zend_Db_Table::getDefaultAdapter();
            //$this->userName = new Zend_Session_Namespace('USER');
            $args = func_get_args();
            //$userId = $this->userName->userId;
            $mediumHeight = $imgheight;
            $mediumWidth = $imgwidth;
            //echo $imgwidth;exit;
            $select = "select user_image from user where id=".$userid;
            $result = $db->fetchRow($select);

            $filename = HTTP_SERVER . '/images/secure/user_image/' .$userid. '/' . $result['user_image'];
            list($width, $height) = getimagesize($filename);
            $extension = $this->getExtension($result['user_image']);
            if ($extension == "jpg" || $extension == "jpeg") {
                $imageMedium = imagecreatefromjpeg($filename);
            } else if ($extension == "png") {
                $imageMedium = imagecreatefrompng($filename);
            } else {
                $imageMedium = imagecreatefromgif($filename);
                //echo $imageMedium;exit;
            }

            $image_m = imagecreatetruecolor($mediumWidth, $mediumHeight);
            imagecopyresampled($image_m, $imageMedium, 0, 0, 0, 0, $mediumWidth, $mediumHeight, $width, $height);
            $mainPathMedium = "images/secure/user_image/" . $userid . '/';
            $mainPathMedium = $mainPathMedium .$userid. '_' .$result['user_image'];
            imagejpeg($image_m, $mainPathMedium, 100);
            return $mainPathMedium;
        }


        function getuserimageSrc($userid, $imgheight, $imgwidth, $size, $title=0, $src=0) {

            $db = Zend_Db_Table::getDefaultAdapter();
            $userId = $userid;
            $mediumHeight = $imgheight;
            $mediumWidth = $imgwidth;
            //echo $imgwidth;exit;
            $select = "select user_image,user_full_name from user where id='" . $userid . "'";
            $result = $db->fetchRow($select);

            //$imgheight=$imgheight.'px';
            //$imgwidth=$imgwidth.'px';

            if (size == 'large')
                $imageName = $userid . "/" . $result['user_image'];
            else
                $imageName = $userid . "/" . $userid . '_' . $result['user_image'];
            if ($title == 0)
                $title = '';
            else
                $title = $result['user_full_name'];
            if ($result['user_image'] == 'no_image.jpg')
                $imageName = $result['user_image'];
            if (empty($result) || $result['user_image'] == '' || $result['user_image'] == '0') {
                $imageName = 'no_image.jpg';
            }
            if (!file_exists("images/secure/user_image/" . $imageName)) {
                $imageName = 'no_image.jpg';
            }
            if ($src == 0)
                return "<img src='" . HTTP_USER_IMAGE .  $imageName . "' alt='" . $title . "' title='" . $title . "' height='" . $imgheight . "' width='" . $imgwidth . "'>";
            else
                return HTTP_USER_IMAGE . $imageName;
        }

        function getUserApiKey() {
            $args = func_get_args();
            $userId = $args[0];
            $db = Zend_Db_Table::getDefaultAdapter();
            $select_store = "SELECT * FROM username WHERE id = " . $userId;
            $result_store = $db->fetchAll($select_store);
            return $result_store[0]['apikey'];
        }

        function checkUserModuleActionPermission($store_apikey, $user_email_id, $mod_id, $action_id='') {
            $db = Zend_Db_Table::getDefaultAdapter();
            $get_user_id = "SELECT id FROM user_role WHERE deleted_flag='0' and status='1' and email_id = '" . $user_email_id . "' AND store_apikey = '" . $store_apikey . "'";
            $result_user_id = $db->fetchRow($get_user_id);
            if ($result_user_id == NULL)
                return false;
            $all_user_permission_id = "SELECT up.*,mam.* from user_permission as up inner join module_action_mapping as mam on up.mod_action_id=mam.id where up.pid='" . $result_user_id['id'] . "' and up.deleted_flag='0'";
            $user_setting_mapper = new Settings_Model_UsersettingMapper($store_apikey);
            $all_permissions = $user_setting_mapper->fetchList($all_user_permission_id, 'mod_id', 'action_id');
            $this->all_permissions = $all_permissions;
            //Now get Module ID
            $action_condition = '';
            if ($action_id != '') {
                $action_condition = ' AND mam.action_id=' . $action_id;
            }
            $get_module_id = "SELECT up.*,mam.* from user_permission as up inner join module_action_mapping as mam on up.mod_action_id=mam.id where up.pid='" . $result_user_id['id'] . "' and up.deleted_flag='0' and mam.mod_id='" . $mod_id . "'" . $action_condition;
            $result_module_permission = $db->fetchRow($get_module_id);
            if ($result_module_permission)
                return true;
            else
                return false;
        }

        function checkModulePermission($store_apikey, $user_email_address, $moduleid) {
            $check_product_view = $this->checkUserModuleActionPermission($store_apikey, $user_email_address, $moduleid);
            if (!$check_product_view) {
                header('Location:' . NO_PERMISSION_URL);
            }
        }

        function checkemailverification($emailid) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = "select email_verification from user_emails where id='$emailid'";
            return $result = $db->fetchOne($select);
        }

        function getSeodetailOfStore($user_api_key) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $select = $db->select()->from(array(TABLE_SEO_SEARCH_ENGINE), array('*'))
                    ->where('api_key="' . $user_api_key . '"');

            return $result = $db->fetchRow($select);
            //print_r($result);exit;
        }

        function displaySettingMenuNew() {
            //print_r($CLASS);
            $args = func_get_args();
            $controllerName = $args[0];
            $actionName = $args[1];
            $request = Zend_Controller_Front::getInstance()->getRequest();
            $moduleName = $request->getModuleName();
            $userAdmin = $_SESSION['USER']['stores'][0]['role'];

            $xyz = '<div class="businessDetailsLinks">';
            //$xyz .='<div id="testdata">'.$controllerName.'-'.$moduleName.'--'.$actionName.'</div>';
            if ($controllerName == 'accountsetting') {
                $xyz .='<div class="mainMenuselected">Account Settings</div>';
            } else {
                $xyz .='<div class="mainMenu"><a href="http://login.eshopbox.com/secure/accountsetting/" title="Account Setting">Account Settings</a></div>';
            }
            if ($controllerName == 'mypayment') {
                $xyz .='<div class="mainMenuselected">My Payment</div>';
            } else {
                $xyz .='<div class="mainMenu"><a href="http://login.eshopbox.com/secure/mypayment" title="My Payment">My Payment</a></div>';
            }
			$checkValidity = $this->checkDemoUserValidity($_SESSION['USER']['stores'][0]['apikey']);
			if($checkValidity == 1){
            	if ($userAdmin == 2) {
                if (($controllerName == 'general') || ($controllerName == 'location') || ($controllerName == 'support') || ($controllerName == 'shipping') || ($controllerName == 'policy') || ($controllerName == 'user-setting') || ($controllerName == 'verification') || ($controllerName=='checkout-settings')) {
                    $xyz .='<div class="mainMenuselected">Store</div>';
                } else {
                    $xyz .='<div class="mainMenu"><a href="http://login.eshopbox.com/settings/general/storedetail" title="Store">Store</a></div>';
                }
				if($_SERVER['REMOTE_ADDR']=='182.71.165.53'){
					 if ($controllerName == 'checkout-settings') {
                    $xyz .='<div class="subMenuselected">Payments</div>';
                } else {
                    $xyz .='<div class="subMenu"><a href="http://login.eshopbox.com/secure/checkout-settings" title="Payments">Payments</a></div>';
                }
				}
                if ($actionName == 'storedetail') {
                    $xyz .='<div class="subMenuselected">General</div>';
                } else {
                    $xyz .='<div class="subMenu"><a href="http://login.eshopbox.com/settings/general/storedetail" title="General">General</a></div>';
                }
                if ($actionName == 'storelocations') {
                    $xyz .='<div class="subMenuselected">Store Location</div>';
                } else {
                    $xyz .='<div class="subMenu"><a href="http://login.eshopbox.com/settings/location/storelocations" title="Store Location">Store Location</a></div>';
                }
                if (($controllerName == 'support') && ($actionName == 'index')) {
                    $xyz .='<div class="subMenuselected">Support</div>';
                } else {
                    $xyz .='<div class="subMenu"><a href="http://login.eshopbox.com/settings/support" title="Support">Support</a></div>';
                }
                if ($actionName == 'manageshipping') {
                    $xyz .='<div class="subMenuselected">Shipping Policy</div>';
                } else {
                    $xyz .='<div class="subMenu"><a href="http://catalog.eshopbox.com/settings/shipping/manageshipping" title="Shipping Policy">Shipping Policy</a></div>';
                }
                if ($actionName == 'terms-policies') {
                    $xyz .='<div class="subMenuselected">Returns & Cancellation policy</div>';
                } else {
                    $xyz .='<div class="subMenu"><a href="http://catalog.eshopbox.com/settings/policy/terms-policies" title="Returns & Cancellation policy">Returns & Cancellation policy</a></div>';
                }

                if(($actionName=='user-listing') || ($actionName=='edit-user')){
                  $xyz .='<div class="subMenuselected">Users</div>';
                  } else {
                  $xyz .='<div class="subMenu"><a href="http://login.eshopbox.com/settings/user-setting/user-listing" title="Users">Users</a></div>';
                  }
/*
                  if($actionName==''){
                  $xyz .='<div class="subMenuselected">Users</div>';
                  } else {
                  $xyz .='<div class="subMenu"><a href="#">Billing</a></div>';
                  } */
                if (($controllerName == 'verification') && ($actionName == 'index')) {
                    $xyz .='<div class="subMenuselected">Verification</div>';
                } else {
                    $xyz .='<div class="subMenu"><a href="http://login.eshopbox.com/settings/verification" title="Verification">Verification</a></div>';
                }
            }
			}
            $xyz .= '</div>';
            return $xyz;
        }

        public function getMallUserDetailByApikey($owner_apikey) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $select_user_store_detail = "SELECT md.*,u.* FROM mall_detail AS md INNER JOIN user AS u ON md.user_id = u.id WHERE md.apikey = '" . $owner_apikey . "'";
            $result_store_user_details = $db->fetchAll($select_user_store_detail);
            return $result_store_user_details;
        }

        public function countCatForRedirection() {
            $db = Zend_Db_Table::getDefaultAdapter();
            $sql = "select * from " . TABLE_ADDCATEGORY . " where apikey='" . $_SESSION[SESSION][ApiKey] . "' and status='1' and cat_id!='1'";
            $result = $db->query($sql)->fetchAll();
            if (count($result) > 0) {
                return 0;
            } else {
                $data = array('api_key' => $_SESSION[SESSION][ApiKey], 'parent_id' => 0, 'type' => '0');
                $db->insert(TABLE_TEMP_ADDCATEGORY, $data);
                $lastId = $db->lastInsertId();
                $_SESSION['CATEGORY']['catId'] = $lastId;
                $_SESSION['CATEGORY']['parentId'] = 0;
                return $lastId;
            }
        }

        public function seoInformation($typeid, $type, $seoType, $storeapikey, $storedomain, $storename) {
            //$storedomain = 'mygoo2o.com';
            $storedomain = $this->getDomainName($storedomain);
            switch ($type) {
                case 'home':
                    switch ($seoType) {
                        case 'title':
                            $pageinfoflag = 1;
                            $defaultformat = 'Welcome to ' . $storename . ' – ' . $storedomain;
                            break;
                        case 'description':
                            $pageinfoflag = 2;
                            $defaultformat = 'Welcome to online shopping store ' . $storename . ' to buy new and branded products online at ' . $storename . ' – ' . $storedomain;
                            break;
                        case 'keyword':
                            $pageinfoflag = 3;
                            $defaultformat = '';
                            break;
                    }
                    break;
                case 'category':
                    $tableName = TABLE_ADDCATEGORY;
                    $id = 'cat_id';
                    $name = 'cat_name';
                    $apikey = 'apikey';
                    $valueflag = 1;
                    $typeflag = 2;
                    $deleteflag = " and status='1'";
                    switch ($seoType) {
                        case 'title':
                            $pageinfoflag = 1;
                            $defaultformat = '{category_name} | ' . $storename . ' – ' . $storedomain;
                            break;
                        case 'description':
                            $pageinfoflag = 2;
                            $defaultformat = '{category_name}: Buy new and branded {category_name} products online at ' . $storename . ' – ' . $storedomain;
                            break;
                        case 'keyword':
                            $pageinfoflag = 3;
                            $defaultformat = '';
                            break;
                        case 'url':
                            $pageinfoflag = 4;
                            $defaultformat = '{category_name}';
                            break;
                    }
                    break;
                case 'brand':
                    $tableName = TABLE_BRAND;
                    $id = 'brand_id';
                    $apikey = 'api_key';
                    $name = 'brand_name';
                    $valueflag = 2;
                    $typeflag = 3;
                    $deleteflag = " and delete_status='1'";
                    switch ($seoType) {
                        case 'title':
                            $pageinfoflag = 1;
                            $defaultformat = '{brand_name} | ' . $storename . ' – ' . $storedomain;
                            break;
                        case 'description':
                            $pageinfoflag = 2;
                            $defaultformat = 'Buy genuine {brand_name} products online at ' . $storename . ': Discount Price for Latest {brand_name} products with express delivery in India along with manufacturer warranty – ' . $storedomain;
                            break;
                        case 'keyword':
                            $pageinfoflag = 3;
                            $defaultformat = '';
                            break;
                        case 'url':
                            $pageinfoflag = 4;
                            $defaultformat = '{brand_name}';
                            break;
                    }
                    break;
                case 'product':
                    $tableName = TABLE_PRODUCT;
                    $id = 'id';
                    $apikey = 'seller_id';
                    $name = 'product_name';
                    $valueflag = 3;
                    $typeflag = 4;
                    $deleteflag = " and delete_flag='1'";
                    $catName = $this->getCategoryNameFromProduct($typeid, $storeapikey);
                    switch ($seoType) {
                        case 'title':
                            $pageinfoflag = 1;
                            $defaultformat = '{product_name} | ' . $storename . ' – ' . $storedomain;
                            break;
                        case 'description':
                            $pageinfoflag = 2;
                            $defaultformat = 'Shop {product_name} in India. Buy {product_name} online at ' . $storename . ' View ' . $catName . ' products price, reviews and features – ' . $storedomain;
                            break;
                        case 'keyword':
                            $pageinfoflag = 3;
                            $defaultformat = '';
                            break;
                        case 'url':
                            $pageinfoflag = 4;
                            $defaultformat = '{product_name}';
                            break;
                    }
                    break;
                case 'mypage':
                    $tableName = 'custom_page';
                    $id = 'id';
                    $apikey = 'user_api_key';
                    $name = 'page_name';
                    $valueflag = 4;
                    $typeflag = 5;
                    $deleteflag = " and deleted_flag='0'";
                    switch ($seoType) {
                        case 'title':
                            $pageinfoflag = 1;
                            $defaultformat = '{page_name} | ' . $storename . ' – ' . $storedomain;
                            break;
                        case 'description':
                            $pageinfoflag = 2;
                            $defaultformat = 'Read & explore {page_name} at ' . $storename . ' – ' . $storedomain;
                            break;
                        case 'keyword':
                            $pageinfoflag = 3;
                            $defaultformat = '';
                            break;
                        case 'url':
                            $pageinfoflag = 4;
                            $defaultformat = '{page_name}';
                            break;
                    }
                    break;
            }
            $db = Zend_Db_Table::getDefaultAdapter();
            if ($type != 'home') {
                $sql = "select value_pattern from " . TABLE_SEO_INDIVIDUAL_PATTERN . " where value_flag='" . $valueflag . "' and value_id='" . $typeid . "' and page_info_flag='" . $pageinfoflag . "' and apikey='" . $storeapikey . "' and delete_flag='1'";
                $result = $db->query($sql)->fetchAll();
                if (count($result) > 0) {
                    return $result['0']['value_pattern'];
                } else {
                    $sql = "select custom_pattern from " . TABLE_SEO_DEFAULT_CUSTOM_PATTERN . " where apikey='" . $storeapikey . "' and page_info_flag='" . $pageinfoflag . "' and type_flag='" . $typeflag . "'";
                    $result = $db->query($sql)->fetchAll();
                    if ($result['0']['custom_pattern'] != '') {
                        return $result['0']['custom_pattern'];
                    } else {
                        $select = "select " . $name . " from " . $tableName . " where " . $apikey . "='" . $storeapikey . "' and " . $id . "=" . $typeid . $deleteflag;
                        $mainArr = $db->query($select)->fetchAll();
                        if (count($mainArr) > 0) {
                            $toReplace = '{' . $name . '}';
                            $pattern = str_replace($toReplace, $mainArr[0][$name], $defaultformat);
                            return $pattern;
                        }
                    }
                }
            } else {
                $sql = "select custom_pattern from " . TABLE_SEO_DEFAULT_CUSTOM_PATTERN . " where apikey='" . $storeapikey . "' and page_info_flag='" . $pageinfoflag . "' and type_flag='1'";
                $result = $db->query($sql)->fetchAll();
                if ((count($result) > 0) && ($result[0]['custom_pattern'] != '')) {
                    $pattern = str_replace('{store_name}', $storename, $result[0]['custom_pattern']);
                    $pattern = str_replace('{store_domain}', $storedomain, $pattern);
                    return $pattern;
                } else {
                    return $defaultformat;
                }
            }
        }

        public function getCategoryNameFromProduct($productid, $storeapikey) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $sql = "select category_id from " . TABLE_PRODUCT . " where seller_id='" . $storeapikey . "' and id=" . $productid . " and delete_flag='1'";
            $result = $db->query($sql)->fetchAll();
            $catid = $result[0]['category_id'];
            if (strlen($catid) > 0) {
                $this->catName = array();
                $arr = explode(",", $catid); //print_r($arr);exit;
                foreach ($arr as $catId) {
                    $selectcategory = "SELECT * FROM " . TABLE_ADDCATEGORY . " where cat_id=" . $catId;
                    //$resultSet=$this->db->query($selectcategory);
                    $getcatname = $db->fetchRow($selectcategory);
                    //  echo "<pre>";print_r($getcatname);exit;
                    $catName.= $getcatname['cat_name'] . ',';
                }
                $catName = substr($catName, 0, -1);
                return $catName;
            }
        }

        function getcustomerfullname($id) {
            // $Sapikey=$_SESSION['SESSION']['ApiKey'];
            $db = $this->databaseconnection();

            $selects = "SELECT u.user_full_name FROM `store_follow_customer` as sfc inner join username as un on un.apikey=sfc.capikey and sfc.deleted_flag='0' inner join user as u on u.id=un.id where sfc.id='$id'";

            $results = $db->query($selects)->fetchAll();
            return $results;
        }

        /**
         * @author : Irshad Hussain
         * @var $url : Current URL
         * Creation Date : 5-12-2011
         * Reason : get fetch domail name and store name from URL
         * return : domail name or store name
         */
        function getDomainName($url) {
            $arr = explode('.', $url);
            $domain = $arr[count($arr) - 2] . '.' . $arr[count($arr) - 1];
            if ($domain == 'co.in')
                return ($arr[count($arr) - 3] . '.' . $domain);
            else
                return $domain;
        }

        public function checkMallRemaingDays($user_api_key) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $mall_query = "SELECT (15 - floor((UNIX_TIMESTAMP()-mall_detail.create_date)/86400)) AS date_diff FROM mall_detail WHERE `store_owner_type` = '1' AND apikey = '" . $user_api_key . "'";
            $date_difference = $db->fetchrow($mall_query);
            return $date_difference['date_diff'];
        }

        function returnModuleAction() {
            $db = Zend_Db_Table::getDefaultAdapter();
            $args = func_get_args();
            $permissionArray = $args[0];
            for ($i = 0; $i < sizeof($permissionArray); $i++) {
                $arr[$i] = $permissionArray[$i]['action_id'];
            }
            $arrangedData = implode(',', $arr);
            $actions = "select * from  module_action where id IN (" . $arrangedData . ") and status = 1";
            $actionArray = $db->fetchAll($actions);

            for ($i = 0; $i < sizeof($actionArray); $i++) {
                $actionName[$i] = strtolower(str_replace(" ", "", strtolower($actionArray[$i]['name'])));
                //$actionName[$i] = $actionArray[$i]['name'];
            }
		$actionName[]='respondtorequest';
		$actionName[]='contactbuyer';

//print_r($actionName);
            return $actionName;
        }

        function manageRolesandRestriction() {
            $front = Zend_Controller_Front::getInstance()->getRequest();
            //$abc = $this->_request->getParams();
            //echo $front->getModuleName();
//	echo "<pre>";
//	print_r($_SERVER);
            echo "me";
        }

        function checkUserPermissions($store_apikey, $user_email_id, $module) {
            $db = Zend_Db_Table::getDefaultAdapter();
            $get_user_id = "SELECT id FROM user_role WHERE deleted_flag='0' and status='1' and email_id = '" . $user_email_id . "' AND store_apikey = '" . $store_apikey . "'";
            $result_user_id = $db->fetchRow($get_user_id);
            if ($result_user_id == NULL)
                return false;
            $all_user_permission_id = "SELECT up.*,mam.* from user_permission as up inner join module_action_mapping as mam on up.mod_action_id=mam.id where up.pid='" . $result_user_id['id'] . "' and up.deleted_flag='0' and mam.mod_id =" . $module;
            $permission = $db->fetchAll($all_user_permission_id);
//	echo "<pre>";
            //print_r($permission);
            return $permission;
        }

        function getStructureBlock() {
            $db = Zend_Db_Table::getDefaultAdapter();
            $args = func_get_args();
            $tempId = $args[0];
            $type = $args[1];
            $selectBlockTemplate = "select * from  structure_template where id=" . $tempId . " and type=" . $type . ' and visible = 1';
            $blockTemplateArray = $db->fetchAll($selectBlockTemplate);
            return $blockTemplateArray[0]['html'];
        }

        function getProdcutImage($productId, $size='small') {
//	$args = func_get_args();
//	$productId = $args[0];
//                      $size =$args[]

            switch ($size) {
                case 'cart':
                case 0:
                    $size = 'cart';
                    break;
                case 'medium':
                case 1:
                    $size = 'medium';
                    break;
                case 'large':
                case 2:
                    $size = 'large';
                    break;
                default:
                    $size = 'small';
                    break;
            }

            $db = Zend_Db_Table::getDefaultAdapter();
            $selectBlockTemplate = "select * from  product_image where product_id=" . $productId . " and image_type=1";
            $blockTemplateArray = $db->fetchAll($selectBlockTemplate);
            $imagePath = $blockTemplateArray[0]['image_location'] . '/' . $size . '/' . $blockTemplateArray[0]['image_name'];

            if(!@fopen($imagePath,'r')){
            $imagePath =  $blockTemplateArray[0]['image_location'] . '/small/' . $blockTemplateArray[0]['image_name'];
            }

            return $imagePath;
            //return $blockTemplateArray[0]['html'];
        }

		        function databaseconnectiondesignserver() {
            //require_once "index.php";
            /*
              return $db = Zend_Db::factory('Pdo_Mysql', array(
              'host'     => '111.118.182.208',
              'username' => 'o2ocheck_store',
              'password' => 'var#usr',
              'dbname'   => 'o2ocheck_o2ostore'
              ));
             */

            return $db = Zend_Db::factory('Pdo_Mysql', array(
                        'host' => '216.185.116.48',
                        'username' => 'o2ocheck_sphinx',
                        'password' => 'sphinx@!0',
                        'dbname' => 'o2ocheck_storefront'
                    ));
        }
	public function checkDemoUserValidity($user_apikey) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $get_days = "SELECT mail_days FROM user_mail_setting WHERE apikey = '" . $user_apikey . "' AND mail_trigger = '001'";
        $get_result = $db->fetchRow($get_days);
    	$get_mall_days = "SELECT id,(floor((UNIX_TIMESTAMP()-mall_detail.create_date)/86400)) AS date_diff, user_id,title FROM mall_detail WHERE `store_owner_type` = 1 AND apikey = '" . $user_apikey . "'";
        $get_mall_result = $db->fetchRow($get_mall_days);
        $mail_days = (empty($get_result['mail_days'])) ? '15' : $get_result['mail_days'];

        $days_result = $mail_days - $get_mall_result['date_diff'];
		//echo $get_mall_result['date_diff']. .count($get_mall_result);
		//if(isset($get_mall_result['date_diff'])){
			if ($days_result < 1)
				return false;
			else
				return true;
		/*}else{
			return false;
		}*/
    }


    }
    ?>
