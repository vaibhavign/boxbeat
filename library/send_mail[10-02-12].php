<?php

/**
 * @author : Mrunal Kanti Roy
 * Creation Date : 5-11-2011	* Modified Date :
 * Reason : manage trigger notification
 */
class Notification {

    private $db;
    private $user;
    private $tid = 0;
    private $ph = array();
    private $mail = array();
    private $to_info = array();
    private $from_info = array();
    private $result_arr = array();
    private $trigger_info = array();

    public function __construct() {
        require_once 'Zend/Mail.php';
        require_once 'Zend/Mail/Transport/Smtp.php';

        $this->db = Zend_Db_Table::getDefaultAdapter();
        //$this->user = new Zend_Session_Namespace('SESSION');
    }

    public function test($data_arr = 'test') {
        return $data_arr;
    }

    /**
     * Created By : Mrunal Kanti Roy
     * @var $id : Trigger Id to be fire
     * @var $data_arr : input data in the trigger
     * Creation Date : 5-11-2011	* Modified Date :
     * Reason : 
     * */
    public function triggerFire($tId, $data_arr = array()) {
        $this->ph = array();
        $this->mail = array();
        $this->to_info = array();
        $this->from_info = array();
        $this->result_arr = array();
        $this->trigger_info = array();

        $this->tid = $tId;
        try {
            $sql = $this->db->select()->from(array(TABLE_NOTIFICATION_TRIGGERS), array('*'))
                    ->where('trigger_id = ?', $this->tid)
                    ->where('trash = ?', 'NO');
            $this->trigger_info = $this->db->fetchRow($sql);

            /* ------ set placeholder if Trigger Active ---- */
            if ($this->trigger_info['trigger_status'] == '1' && (($this->trigger_info['trigger_email_flag'] == 'ON') || ($this->trigger_info['trigger_update_flag'] == 'ON'))) {
                if ($data_arr['no_alert_flag'] == 'YES') {
                    $this->trigger_info['trigger_update_flag'] = 'OFF';
                }
                $this->setPlaceholder($this->trigger_info['trigger_id'], $data_arr);
                /* -----Set from mail info--(SELLER , O2O)--- */
                if ($this->mail['from_mail'] == '') {
                    if ($this->trigger_info['trigger_ownership'] == 'SELLER') {
                        $this->fromMailInfo('USER-ID', $data_arr['from_id']);
                    } else {
                        $this->fromMailInfo('DO-NOT-REPLY');
                    }
                }

                /* -----Set to mail info---------- (0 = Store Owner, 1 = Product owner, 2 = Customer) ------------- */
                if ($this->mail['to_mail'] == '') {
                    if (is_numeric($data_arr['to_id']))
                        $this->toMailInfo('USER-ID', $data_arr['to_id']);
                    else
                        $this->toMailInfo('EMAIL-ID', $data_arr['to_id']);
                }


                /* ------ send Mail if Mail Active ---- */
                if ($this->trigger_info['trigger_email_flag'] == 'ON') {
                    $this->manageMail();
                }

                /* ------ alert fire if alert Active ---- */
                if ($this->trigger_info['trigger_update_flag'] == 'ON') {
                    $this->manageAlert();
                }
            } else {
                return 'Invalid trigger (497.8)';
            }
        } catch (Exception $e) {
            echo 'Exception caught: ', $e->getMessage(), "\n";
        }
    }

    /**
     * Created By : Mrunal Kanti Roy
     * Creation Date : 7-11-2011	* Modified Date :
     * Reason : set 'mail_body' and 'mail_subject' then send_mail
     * */
    public function manageMail() {

        $sql = $this->db->select()->from(array('nda' => TABLE_NOTIFICATION_DETAILS_ADMIN), array('*'))
                ->joinLeft(array('nsf' => TABLE_NOTIFICATION_SUPPORT_FOOTER), 'nsf.footer_id = nda.support_footer_id', array('sf_body' => 'support_footer_body'))
                ->joinLeft(array('ns' => TABLE_NOTIFICATION_SIGNATURE), 'ns.signature_id = nda.signature_id', array('s_body' => 'signature_body'))
                ->joinLeft(array('ndf' => TABLE_NOTIFICATION_DEFAULT_FOOTER), 'ndf.default_footer_id = nda.default_footer_id', array('df_body' => 'df_body'))
                ->where('trigger_id = ?', $this->tid);
        $admin_mail = $this->db->fetchRow($sql);

//        $supportFooter = $this->getSupportFooter($admin_mail['support_footer_id']);
//        $signature = $this->getSignature($admin_mail['signature_id']);
//        $default_footer = $this->getDefaultFooter($admin_mail['default_footer_id']);

        /* -------Assign placeholder in support footer------- */
        $ph_sf = array();
        foreach ($ph_sf as $key => $val) {
            $admin_mail['sf_body'] = str_replace('{' . $key . '}', $val, $admin_mail['sf_body']);
        }

        /* -------Assign placeholder in signature------------ */
        $ph_s = array();
        foreach ($ph_s as $key => $val) {
            $admin_mail['s_body'] = str_replace('{' . $key . '}', $val, $admin_mail['s_body']);
        }

        /* -------Assign placeholder in default footer------------ */
        $ph_df = array('PH_NOT_MY_ACCOUNT' => $this->ph_not_my_account, 'PH_ACCOUNT_SETTINGS' => HTTP_SECURE . "/accountsetting");
        foreach ($ph_df as $key => $val) {
            $admin_mail['df_body'] = str_replace('{' . $key . '}', $val, $admin_mail['df_body']);
        }

        $this->mail['body'] = $admin_mail['mail_body'] . '<br/><br/>' . $admin_mail['sf_body'] . '<br/><br/>' . $admin_mail['s_body'] . '<br/><br/>' . $admin_mail['df_body'];
        /* -------Assign placeholder in mail body ------------ */
        foreach ($this->ph as $key => $val) {
            $this->mail['body'] = str_replace('{' . $key . '}', $val, $this->mail['body']);
            $this->mail['body'] = str_replace('%7B' . $key . '%7D', $val, $this->mail['body']);
            $admin_mail['mail_subject'] = str_replace('{' . $key . '}', $val, $admin_mail['mail_subject']);
        }

        $this->mail['subject'] = $admin_mail['mail_subject'];

        $this->mailFire();
    }

    /**
     * Created By : Mrunal Kanti Roy
     * Creation Date : 7-11-2011	* Modified Date :
     * Reason : 
     * */
    public function manageAlert() {
        $sql = $this->db->select()->from(array(TABLE_NOTIFICATION_UPDATE_DATA), array('update_body'))
                ->where('trigger_id = ?', $this->tid)
                ->where('api_key = ?', 0);

        $msg_text = $this->db->fetchOne($sql);

        foreach ($this->ph as $key => $val) {
            $msg_text = str_replace('{' . $key . '}', $val, $msg_text);
            $msg_text = str_replace('%7B' . $key . '%7D', $val, $msg_text);
        }

        $insert_data = array('user1' => $this->mail['from_id'],
            'user2' => $this->mail['to_id'],
            'created_date' => time(),
            'modified_date' => time(),
            'message_type' => '4',
            'request_text' => $msg_text,
            'ipaddress' => $_SERVER['REMOTE_ADDR'],
            'readby' => $this->mail['from_id']);
        $this->db->insert(TABLE_CHAT, $insert_data);
        $this->result_arr['alert'] = is_numeric($this->db->lastInsertId()) ? 'INSERTED' : 'ERROR';
    }

    /**
     * Created By : Mrunal Kanti Roy
     * @var 
     * Creation Date : 7-11-2011	* Modified Date :
     * Reason : 
     * */
//    public function getSupportFooter($sfId){
//        $CURRENT_USER_NAME_FOOTER = 'Ashis';
//        $CURRENT_USER_EMAIL = 'aaa@bbb.com';
//        $HELPLINE = '100';
//        
//        $sql = $this->db->select()->from(array(TABLE_NOTIFICATION_SUPPORT_FOOTER),array('support_footer'=>'support_footer_body'))
//                            ->where('footer_id = ?', $sfId)
//                            ->where('trash = "NO"');
//        
//        $supportFooter = $this->db->fetchOne($sql);
//        $supportFooter = str_replace('{CURRENT_USER_NAME_FOOTER}', $CURRENT_USER_NAME_FOOTER, $supportFooter);
//        $supportFooter = str_replace('{CURRENT_USER_EMAIL}', $CURRENT_USER_EMAIL, $supportFooter);
//        $supportFooter = str_replace('{HELPLINE}', $HELPLINE, $supportFooter);
//        return $supportFooter;
//    }

    /**
     * Created By : Mrunal Kanti Roy
     * @var $id : update/alert fire
     * Creation Date : 5-11-2011	* Modified Date :
     * Reason : 
     * */
//    public function getSignature($sId) {
//        
//        $sql = $this->db->select()->from(array(TABLE_NOTIFICATION_SIGNATURE),array('signature'=>'signature_body'))
//                            ->where('signature_id = ?', $sId)
//                            ->where('trash = "NO"');
//        return $this->db->fetchOne($sql);
//        
//    }
//    
    /**
     * Created By : Mrunal Kanti Roy
     * @var $id : update/alert fire
     * Creation Date : 7-11-2011	* Modified Date :
     * Reason : 
     * */
//    public function getDefaultFooter($dfId){
//        
//        $sql = $this->db->select()->from(array(TABLE_NOTIFICATION_DEFAULT_FOOTER), array('df_body'=>'df_body'))
//                                  ->where('default_footer_id = ?', $dfId)
//                                  ->where('trash = "NO"');
//        return $this->db->fetchOne($sql);
//        
//    }

    /**
     * Created By : Mrunal Kanti Roy
     * @var $mail : mail data array()
     *              $mail{to_mail]  =>  'email_address'
     *              $mail[to_name]  =>  'to_name'
     *              $mail[from_mail]=>  'from mail'
     *              $mail[from_name]=>  'from name'
     *              $mail[body]     =>  'email_body'
     *              $mail[subject]  =>  'email_subject'
     *              
     * Creation Date : 5-11-2011	* Modified Date :
     * Reason : 
     * */
    public function mailFire($mail = array()) {
        if (count($mail) > 0) {
            $this->mail = $mail;
        }
        if (count($this->mail) > 0) {
            $mail = new Zend_Mail();
            $mail->setFrom($this->mail['from_mail'], $this->mail['from_name']);
            $mail->setBodyHtml($this->mail['body']);
            $mail->addTo($this->mail['to_mail'], $this->mail['to_name']);
            $mail->addBcc('ashislubumohanty@gmail.com', 'Ashis');
            $mail->addBcc('saroj.dkl4u@gmail.com', 'Saroj');
           // $mail->addBcc('mrunal.roy@live.com', 'Mrunal');
            //$mail->addBcc('vaibhavign@gmail.com', 'vaibhav');
            //$mail->addBcc('harpreets.singh888@gmail.com', 'harpreet');
            $mail->addBcc('ankush.goo2o@gmail.com', 'ankush');
            $mail->setSubject($this->mail['subject']);
            $mail->send();
        } else {
            return 'ERROR';
        }
    }

    /**
     * Created By : Mrunal Kanti Roy
     * @var $id : update/alert fire
     * Creation Date : 7-11-2011	* Modified Date :
     * Reason : 
     * */
    public function fromMailInfo($case, $data = '') {
        switch ($case) {
            case 'DO-NOT-REPLY':
                $this->from_info = $this->db->fetchRow("SELECT *  FROM " . TABLE_USER . " WHERE user_email_address = 'do-not-reply@goo2o.com'");
                $this->mail['from_id'] = $this->from_info['id'];
                $this->mail['from_mail'] = $this->from_info['user_email_address'];
                $this->mail['from_name'] = $this->from_info['user_full_name'];
                break;

            case 'USER-ID':
                $this->from_info = $this->db->fetchRow('select * from ' . TABLE_USER . ' where id = ' . $data);
                $this->mail['from_id'] = $this->from_info['id'];
                $this->mail['from_mail'] = $this->from_info['user_email_address'];
                $this->mail['from_name'] = $this->from_info['user_full_name'];
                break;
            case 'API-KEY':
                $this->from_info = $this->db->fetchRow("select * from mall_detail as md left join user_role as ur on md.apikey=ur.store_apikey join username as un on ur.store_apikey=un.apikey where   un.apikey='" . $data . "'");

                $this->mail['from_id'] = '639';
                $this->mail['from_mail'] = 'saroj.dkl4u@gmail.com';
                $this->mail['from_name'] = 'hello';
                break;

            case 'SET-INFO':
                $this->from_info = $data;
                $this->mail['from_id'] = $data['from_id'];
                $this->mail['from_mail'] = $data['from_mail'];
                $this->mail['from_name'] = $data['from_name'];
                break;

            default:
                break;
        }
    }

    /**
     * Created By : Mrunal Kanti Roy
     * @var $data : required data_array 
     * Creation Date : 7-11-2011	* Modified Date :
     * Reason : get details of user
     * */
    public function toMailInfo($case, $data = '') {
        switch ($case) {

            case 'USER-ID':
                $this->to_info = $this->db->fetchRow('select * from ' . TABLE_USER . ' where id = ' . $data);
                $this->mail['to_id'] = $this->to_info['id'];
                $this->mail['to_mail'] = $this->to_info['user_email_address'];
                $this->mail['to_name'] = $this->to_info['user_full_name'];
                $this->ph_not_my_account = HTTPS_SECURE . '/login/notyouraccount/passcode/' . $this->to_info['vcode'];
                break;

            case 'SET-INFO':
                $this->to_info = $data;
                $this->mail['to_id'] = $data['to_id'];
                $this->mail['to_mail'] = $data['to_mail'];
                $this->mail['to_name'] = $data['to_name'];
                break;

            case 'EMAIL-ID':
                $this->to_info = array('to_id' => 0, 'to_mail' => $data, 'to_name' => $data);
                $this->mail['to_id'] = $this->to_info['to_id'];
                $this->mail['to_mail'] = $this->to_info['to_mail'];
                $this->mail['to_name'] = $this->to_info['to_name'];
                $vcode = $this->db->fetchOne("select vcode from user_emails where user_email = '$data'");
                $this->ph_not_my_account = HTTPS_SECURE . '/login/notyournewaccount/passcode/' . $vcode;
                break;

            case 'TEST':     //for test
                $this->to_info = array('id' => 0, 'email' => 'mrunal.roy@goo2o.com', 'name' => 'Mrunal');
                $this->mail['to_id'] = $this->to_info['id'];
                $this->mail['to_mail'] = $this->to_info['email'];
                $this->mail['to_name'] = $this->to_info['name'];
                break;

            default:
                break;
        }
    }

    /**
     * Created By : Mrunal Kanti Roy
     * @var 
     * Creation Date : 7-11-2011	* Modified Date :
     * Reason : 
     * */
    public function setPlaceholder($tId, $data_arr = array()) {//if($tId==37){echo "<pre>";print_r($data_arr);exit;}
        $id = $tId;
        switch ($id) {

            case '6':       //for test 
                $this->toMailInfo('SET-INFO', array('to_mail' => 'harpreets.singh888@gmail.com', 'to_name' => 'Mrunal Kanti Roy', 'to_id' => 0));
                $this->fromMailInfo('DO-NOT-REPLY');
                $data_str = 'Data Array()<br/>&nbsp;&nbsp;';


                //$all = array_merge( $data_arr, array('---- to---mail ------'), $this->to_info, array('------- from--mail --------'), $this->from_info);

                foreach ($data_arr as $key => $val) {
                    if(is_array($val))
                        foreach ($val as $k2=>$val2) {
                        if(is_array($val))
                            foreach ($val2 as $k3=>$val3) {
                            $data_str .= '&nbsp;&nbsp;&nbsp;&nbsp;'.$k3 . '&nbsp;&nbsp;##&nbsp;&nbsp;' . $val3 . '<br/>&nbsp;&nbsp;';
                            }
                        $data_str .= '&nbsp;&nbsp;'.$k2 . '&nbsp;&nbsp;##&nbsp;&nbsp;' . $val2 . '<br/>&nbsp;&nbsp;';
                        }
                    $data_str .= $key . '&nbsp;&nbsp;##&nbsp;&nbsp;' . $val . '<br/>&nbsp;&nbsp;';
                }
                $this->ph = array('DATA' => $data_str);
echo "Test WWWWWWWWWW";
print_r($data_arr);exit;
                break;

            case '1': /* ---------(When a new user register to Goo2o, email verification to user)---------- */
                $this->ph = array('USER_FULL_NAME' => $data_arr['to_name'],
                    'ACCOUNT_VERIFICATION_LINK' => $data_arr['account_verification_link'],
                    'EDIT_BASIC_INFO_LINK' => HTTPS_SECURE . '/accountsetting/editbasicinfo',
                    'PRIMARY_EMAIL_ID' => $data_arr['to_mail'],
                    'MY_PAYMENTS_LINK' => HTTPS_SECURE . '/mypayment');
                break;

            case '3': /* ----------(Reset password notification mail to user)--------- */
                $this->ph = array('USER_FULL_NAME' => $data_arr['to_name'], 'PASSWORD_RESET_LINK' => $data_arr['password_reset_link']);
                break;

            case '4': /* ------(Notification mail to user for password successfully changed)------- */
                $this->ph = array('USER_FULL_NAME' => $data_arr['to_name']);
                break;

            case '7': /* ------------(When the user visit dashboard home first time, welcome note to user)------------- */
                $this->ph = array('USER_FULL_NAME' => $data_arr['to_name']);
                break;

            case '8': /* -------------(Notify merchants for store live on subdomain and will later map with domain)----------------- */
                $this->ph = array('STORE_OWNER_NAME' => $data_arr['to_name'], 'STORE_SUBDOMAIN_LINK' => $data_arr['store_subdomain_link']);
                break;

            case '14': /* -------------(Notify merchants for store live on subdomain and will later map with domain)----------------- */
                $this->ph = array('STORE_OWNER_NAME' => $data_arr['to_name'], 'STORE_URL' => $data_arr['store_url'], 'CONTROL_PANEL_URL' => $data_arr['control_panel_url'], 'STORE_OWNER_EMAIL_ID' => $data_arr['to_mail']);
                break;

            case '13': /* -------------(Notify merchants for store live on subdomain and will later map with domain)----------------- */
                $this->ph = array('STORE_OWNER_NAME' => $data_arr['to_name']);
                break;
            case '58':      //confirm shipment to seller
                $this->ph = array(
                    'STORE_OWNER_SENDING_EMAIL' => 'harpreetankush@gmail.com',
                    'STORE_NAME' => $data_arr['orderData'][0]['title'],
                    'STORE_EMAIL_ID' => $data_arr['orderData'][0]['seller_email'],
                    'STORE_CONTACT_NUMBER' => $data_arr['orderData'][0]['seller_contact_number'],
                    'CUSTOMER_NAME' => $data_arr['orderData'][0]['buyer_name'],
                    'CUSTOMER_EMAIL_ID' => $data_arr['orderData'][0]['buyer_email'],
                    'SHIPMENT_ID' => $data_arr['orderData']['shipment_id'],
                    'SHIPPING_DATE' => date('d/m/y', $data_arr['orderData']['order_ship_date']),
                    'EXPECTED_TRANSIT_TIME' => $data_arr['orderData']['order_delivery_date'],
                    'COURIER_NAME' => $data_arr['orderData']['order_carrier_name'],
                    'DOCKET_NUMBER' => $data_arr['orderData']['tracking_id'],
                    'SHIPPING_ADDRESS' => $data_arr['orderData']['ossa_address'],
                    'SHIPMENT_ITEMS_LIST' => $data_arr['orderData']['shipment_list'],
                    'CUSTOMER_SHIPMENT_DETAIL_PAGE_LINK' => $data_arr['orderData']['shipment_detail_page_customer'],
                    'STORE_URL' => $data_arr['orderData'][0]['seller_mallurl']
                );
                break;
            case '25': /* -------------(Notify merchants for store live on subdomain and will later map with domain)----------------- */
                $this->ph = array('STORE_OWNER_NAME' => $data_arr['to_name'], 'DEMO_END_DAYS' => $data_arr['demo_end_days'], 'STORE_NAME' => $data_arr['store_name'], 'STORE_OWNER_EMAIL_ID' => $data_arr['to_mail']);
                break;
            case '29': /* -------------(Notify merchants for store live on subdomain and will later map with domain)----------------- */
                $this->ph = array('STORE_OWNER_NAME' => $data_arr['to_name'], 'STORE_OWNER_EMAIL_ID' => $data_arr['to_mail']);
                break;
            case '30': /* -------------(Notify merchants for store live on subdomain and will later map with domain)----------------- */
                $this->ph = array('STORE_OWNER_NAME' => $data_arr['to_name'], 'STORE_OWNER_EMAIL_ID' => $data_arr['to_mail']);
                break;
            case '28': /* -------------(Notify merchants for store live on subdomain and will later map with domain)----------------- */
                $this->ph = array('STORE_OWNER_NAME' => $data_arr['to_name'], 'STORE_OWNER_EMAIL_ID' => $data_arr['to_mail']);
                break;

            case '36':/* -------------(Notify merchants for store live on subdomain and will later map with domain)----------------- */
                $this->ph = array('CUSTOMER_VERIFICATION_CONFIRMATION_LINK' => $data_arr['link'],
                    'INVITE_CUSTOMER_EMAIL_ID' => $data_arr['useremail'],
                    'STORE_URL' => $data_arr['storeurl'],
                    'STORE_NAME' => $data_arr['storename'],
                    'STORE_EMAIL_ID' => $data_arr['storeemail'],
                    'STORE_CONTACT_NUMBER' => $data_arr['storecontacts']
                );
                break;
            case '37':/* -------------(Notify to add a secondary email )----------------- */
                $this->ph = array('ACCOUNT_VERIFICATION_LINK' => $data_arr['link'],
                    'PH_NOT_MY_MAIL' => $data_arr['notmyaccountlinks'],
                    'USER_FULL_NAME' => $data_arr['name']
                );
                break;
            case '22':          //shipped cancelled
                $this->ph = array(
                    'ORDER_ITEM_ID' => $data_arr['orderData']['orderItemId'],
                    'STORE_NAME' => $data_arr['orderData'][0]['title'],
                    'STORE_URL' => $data_arr['orderData'][0]['seller_mallurl'],
                    'CUSTOMER_EMAIL_ID' => $data_arr['orderData'][0]['buyer_email'],
                    'CUSTOMER_NAME' => $data_arr['orderData'][0]['buyer_name'],
                    'REASON_CANCELLATION' => $data_arr['orderData']['reasonCancellation'],
                    'ITEM_CANCELLED' => $data_arr['orderData']['orderItemPattern'],
                    'SHIPMENT_ID' => $data_arr['orderData']['shipment_id'],
                    'STORE_CONTACT_NUMBER' => $data_arr['orderData'][0]['seller_contact_number'],
                    'STORE_EMAIL_ID' => 'harpreetankush@gmail.com',
                    'STORE_OWNER_SENDING_EMAIL' => 'harpreetankush@gmail.com',
                    'REFUND_AMOUNT' => 'Rs.000'
                );
                break;

            case '21':          //pending cancelled
                $this->ph = array(
                    'ORDER_ITEM_ID' => $data_arr['orderData']['orderItemId'],
                     'CUSTOMER_NAME' => $data_arr['orderData'][0]['buyer_name'],
                    'STORE_NAME' => $data_arr['orderData'][0]['title'],
                    'STORE_URL' => $data_arr['orderData'][0]['seller_mallurl'],
                    'REASON_CANCELLATION' => $data_arr['orderData']['reasonCancellation'],
                    'ITEM_CANCELLED' => $data_arr['orderData']['orderItemPattern'],
                    'REFUND_AMOUNT' => 'Rs.000',
                    'STORE_CONTACT_NUMBER' => $data_arr['orderData'][0]['seller_contact_number'],
                    'STORE_EMAIL_ID' => $data_arr['orderData'][0]['seller_email'],
                    'STORE_OWNER_SENDING_EMAIL' => $data_arr['orderData'][0]['seller_email']
                );
                break;

            case '26':      //confirm delievery
                $this->ph = array('SHIPMENT_ID' => $data_arr['orderData']['shipment_id'],
                    'CUSTOMER_NAME' => $data_arr['orderData'][0]['buyer_name'],
                    'STORE_OWNER_EMAIL_ID' => 'harpreetankush@gmail.com',
                    'STORE_OWNER_NAME' => $data_arr['orderData'][0]['seller_name'],
                    'EXPECTED_TRANSIT_TIME' => $data_arr['orderData']['order_ship_date'],
                    'COURIER_NAME' => $data_arr['orderData']['order_carrier_name'],
                    'DOCKET_NUMBER' => $data_arr['orderData']['tracking_id'],
                    'STORE_OWNER_SHIPMENT_DETAIL_PAGE_LINK' => $data_arr['orderData']['shipment_detail_page'],
                    'SHIPMENT_ITEMS_LIST' => $data_arr['orderData']['shipment_list']
                );
                break;

            case '23':      //confirm shipment to buyer
                $this->ph = array(
                    'SHIPMENT_ID' => $data_arr['orderData']['shipment_id'],
                    'CUSTOMER_NAME' => $data_arr['orderData'][0]['buyer_name'],
                    'CUSTOMER_EMAIL_ID' => $data_arr['orderData'][0]['buyer_email'],
                    'SHIPPING_DATE' => date('d/m/y', $data_arr['orderData']['order_shipment_date_created']),
                    'EXPECTED_TRANSIT_TIME' => $data_arr['orderData']['order_ship_date'],
                    'COURIER_NAME' => $data_arr['orderData']['order_carrier_name'],
                    'DOCKET_NUMBER' => $data_arr['orderData']['tracking_id'],
                    'CUSTOMER_SHIPMENT_DETAIL_PAGE_LINK' => $data_arr['orderData']['shipment_detail_page_customer'],
                    'STORE_NAME' => $data_arr['orderData'][0]['title'],
                    'STORE_URL' => $data_arr['orderData'][0]['seller_mallurl'],
                    'SHIPMENT_ITEMS_LIST' => $data_arr['orderData']['shipment_list']
                );
                break;

            case '32':      //confirm return shipment from goo2o to buyer
                $returnType = ($data_arr['orderData']['return_type'] == '1') ? 'Refund' : 'Replacement';
                $this->ph = array(
                    'ORDER_ITEM_ID' => $data_arr['orderData'][0]['orderItemId'],
                    'STORE_NAME' => $data_arr['orderData'][0]['title'],
                    'STORE_URL' => $data_arr['orderData'][0]['seller_mallurl'],
                    'SHIPPING_DATE' => date('d/m/y', $data_arr['orderData']['order_shipment_date_created']),
                    'EXPECTED_TRANSIT_TIME' => $data_arr['orderData']['order_ship_date'],
                    'COURIER_NAME' => $data_arr['orderData']['order_carrier_name'],
                    'DOCKET_NUMBER' => $data_arr['orderData']['tracking_id'],
                    'SHIPPING_ADDRESS' => $data_arr['orderData']['address_line_one'],
                    'CUSTOMER_EMAIL_ID' => $data_arr['orderData'][0]['buyer_email'],
                    'CUSTOMER_NAME' => $data_arr['orderData'][0]['buyer_name'],
                    'RETURN_ID' => $data_arr['orderData']['return_id'],
                    'SHIPMENT_ITEMS_LIST' => $data_arr['orderData']['shipment_list'],
                    'RETURN_TYPE' => $returnType,
                    'CUSTOMER_SHIPMENT_DETAIL_PAGE_LINK' => $data_arr['orderData']['shipment_detail_page_customer'],
                    'SHIPMENT_ID' => $data_arr['orderData']['shipment_id'],
                    'PURCHASE_DETAIL_PAGE_LINK' => $data_arr['orderData']['purchase_link'],
                    'STORE_EMAIL_ID' => 'harpreetankush@gmail.com',
                    'STORE_CONTACT_NUMBER' => $data_arr['orderData'][0]['seller_contact_number'],
                    'REASON_RETURN' => $data_arr['orderData']['return_reason']
                );
                break;

            case '38':      //request for cancellation
                $this->ph = array(
                    'CUSTOMER_NAME' => $data_arr['orderData'][0]['buyer_name'],
                    'STORE_OWNER_EMAIL_ID' => 'harpreetankush@gmail.com',
                    'STORE_OWNER_NAME' => $data_arr['orderData'][0]['seller_name'],
                    'REQUEST_DATE' => date('d/m/y', $data_arr['orderData']['request_date']),
                    'REASON_CANCELLATION' => stripslashes($data_arr['orderData']['cancellation_reason']),
                    'STORE_NAME' => $data_arr['orderData'][0]['title'],
                    'STORE_OWNER_REQUEST_DETAILS_PAGE_LINK' => $data_arr['orderData']['request_detail_link_seller'],
                    'ORDER_ITEM_ID' => $data_arr['orderData']['orderItemId']
                );
                break;

            case '41':      //request for return of an item
                $returnType = ($data_arr['orderData']['return_type'] == '1') ? 'Refund' : 'Replacement';
                $this->ph = array(
                    'CUSTOMER_NAME' => $data_arr['orderData'][0]['buyer_name'],
                    'ORDER_ITEM_ID' => $data_arr['orderData']['orderItemId'],
                    'STORE_OWNER_NAME' => $data_arr['orderData'][0]['seller_name'],
                    'STORE_OWNER_EMAIL_ID' => 'harpreetankush@gmail.com',
                    'REQUEST_DATE' => date('d/m/y', $data_arr['orderData']['request_date']),
                    'RETURN_REASON' => $data_arr['orderData']['return_reason'],
                    'RETURN_TYPE' => $returnType,
                    'STORE_NAME' => $data_arr['orderData'][0]['title'],
                    'STORE_OWNER_REQUEST_DETAILS_PAGE_LINK' => $data_arr['orderData']['request_detail_link_seller']
                );
                break;

            case '42':      //request for return rejected
                $this->ph = array(
                    'ORDER_ITEM_ID' => $data_arr['orderData']['orderItemId'],
                    'CUSTOMER_EMAIL_ID' => $data_arr['orderData'][0]['buyer_email'],
                    'CUSTOMER_NAME' => $data_arr['orderData'][0]['buyer_name'],
                    'STORE_NAME' => $data_arr['orderData'][0]['title'],
                    'STORE_URL' => $data_arr['orderData'][0]['seller_mallurl'],
                    'REQUEST_DATE' => date('d/m/y', $data_arr['orderData']['request_date']),
                    'CUSTOMER_REQUEST_DETAILS_PAGE_LINK' => $data_arr['orderData']['customer_detail_page_link']
                );
                break;

            case '43':      //request for return accepted
                $returnType = ($data_arr['orderData']['return_type'] == '1') ? 'Refund' : 'Replacement';
                $this->ph = array(
                    'ORDER_ITEM_ID' => $data_arr['orderData']['orderItemId'],
                    'CUSTOMER_EMAIL_ID' => $data_arr['orderData'][0]['buyer_email'],
                    'CUSTOMER_NAME' => $data_arr['orderData'][0]['buyer_name'],
                    'STORE_NAME' => $data_arr['orderData'][0]['title'],
                    'STORE_URL' => $data_arr['orderData'][0]['seller_mallurl'],
                    'REQUEST_DATE' => date('d/m/y', $data_arr['orderData']['request_date']),
                    'RETURN_TYPE' => $returnType,
                    'RETURN_QUANTITY' => $data_arr['orderData']['quantity'],
                    'EXPECTED_SHIPPING_PRICE' => $data_arr['orderData']['ship_cost'],
                    'CUSTOMER_REQUEST_DETAILS_PAGE_LINK' => $data_arr['orderData']['customer_request_detail_page'],
                    'RETURN_ID' => $data_arr['orderData']['return_id']
                );
                break;

            case '44':      //request for cancellation accepted from goo2o to buyer
                $this->ph = array(
                    'ORDER_ITEM_ID' => $data_arr['orderData']['orderItemId'],
                    'CUSTOMER_EMAIL_ID' => $data_arr['orderData'][0]['buyer_email'],
                    'CUSTOMER_NAME' => $data_arr['orderData'][0]['buyer_name'],
                    'STORE_NAME' => $data_arr['orderData'][0]['title'],
                    'STORE_URL' => $data_arr['orderData'][0]['seller_mallurl'],
                    'REQUEST_DATE' => date('d/m/y', $data_arr['orderData']['request_date']),
                    'CUSTOMER_REQUEST_DETAILS_PAGE_LINK' => $data_arr['orderData']['customer_detail_page_link'],
                    'REASON_CANCELLATION' => stripslashes($data_arr['orderData']['request_reason'])
                );
                break;

            case '45':      //request for cancellation rejected from goo2o to buyer
                $this->ph = array(
                    'ORDER_ITEM_ID' => $data_arr['orderData']['orderItemId'],
                    'CUSTOMER_EMAIL_ID' => $data_arr['orderData'][0]['buyer_email'],
                    'CUSTOMER_NAME' => $data_arr['orderData'][0]['buyer_name'],
                    'STORE_NAME' => $data_arr['orderData'][0]['title'],
                    'STORE_URL' => $data_arr['orderData'][0]['seller_mallurl'],
                    'REQUEST_DATE' => date('d/m/y', $data_arr['orderData']['request_date']),
                    'CUSTOMER_REQUEST_DETAILS_PAGE_LINK' => $data_arr['orderData']['customer_detail_page_linkss'],
                    'REASON_CANCELLATION' => stripslashes($data_arr['orderData']['request_reason'])
                );
                break;

            case '46':      //request for cancellation accepted from seller to buyer
                $this->ph = array(
                    'ORDER_ITEM_ID' => $data_arr['orderData']['orderItemId'],
                    'CUSTOMER_EMAIL_ID' => $data_arr['orderData'][0]['buyer_email'],
                    'CUSTOMER_NAME' => $data_arr['orderData'][0]['buyer_name'],
                    'STORE_NAME' => $data_arr['orderData'][0]['title'],
                    'STORE_URL' => $data_arr['orderData'][0]['seller_mallurl'],
                    'REQUEST_DATE' => date('d/m/y', $data_arr['orderData']['request_date']),
                    'CUSTOMER_REQUEST_DETAILS_PAGE_LINK' => $data_arr['orderData']['customer_detail_page_link'],
                    'REASON_CANCELLATION' => $data_arr['orderData']['request_reason'],
                    'STORE_OWNER_SENDING_EMAIL' => 'harpreetankush@gmail.com'
                );
                break;

            case '47':      //request for cancellation rejected from seller to buyer
                $this->ph = array(
                    'ORDER_ITEM_ID' => $data_arr['orderData']['orderItemId'],
                    'CUSTOMER_EMAIL_ID' => $data_arr['orderData'][0]['buyer_email'],
                    'CUSTOMER_NAME' => $data_arr['orderData'][0]['buyer_name'],
                    'STORE_NAME' => $data_arr['orderData'][0]['title'],
                    'STORE_URL' => $data_arr['orderData'][0]['seller_mallurl'],
                    'REQUEST_DATE' => date('d/m/y', $data_arr['orderData']['request_date']),
                    'CUSTOMER_REQUEST_DETAILS_PAGE_LINK' => $data_arr['orderData']['customer_detail_page_link'],
                    'STORE_OWNER_SENDING_EMAIL' => 'harpreetankush@gmail.com',
                    'REASON_CANCELLATION' => $data_arr['orderData']['request_reason']
                );
                break;


            case '48':      //request for shipping address made,from goo2o to seller
                $this->ph = array(
                    'CUSTOMER_NAME' => $data_arr['orderData'][0]['buyer_name'],
                    'ORDER_ITEM_ID' => $data_arr['orderData']['orderItemId'],
                    'STORE_OWNER_NAME' => $data_arr['orderData'][0]['seller_name'],
                    'STORE_OWNER_EMAIL_ID' => 'harpreetankush@gmail.com',
                    'REQUEST_DATE' => date('d/m/y', $data_arr['orderData']['request_date']),
                    'STORE_NAME' => $data_arr['orderData'][0]['title'],
                    'OLD_ADDRESS' => $data_arr['orderData']['old_address'],
                    'NEW_ADDRESS' => $data_arr['orderData']['new_address'],
                    'STORE_OWNER_REQUEST_DETAILS_PAGE_LINK' => $data_arr['orderData']['customer_detail_page_link']
                );
                break;

            case '49':      //request for shipping address made,from goo2o to buyer
                $this->ph = array(
                    'ORDER_ITEM_ID' => $data_arr['orderData']['orderItemId'],
                    'CUSTOMER_NAME' => $data_arr['orderData'][0]['buyer_name'],
                    'CUSTOMER_EMAIL_ID' => $data_arr['orderData'][0]['buyer_email'],
                    'REQUEST_DATE' => date('d/m/y', $data_arr['orderData']['request_date']),
                    'STORE_NAME' => $data_arr['orderData'][0]['title'],
                    'STORE_URL' => $data_arr['orderData'][0]['seller_mallurl'],
                    'OLD_ADDRESS' => $data_arr['orderData']['old_address'],
                    'NEW_ADDRESS' => $data_arr['orderData']['new_address'],
                    'CUSTOMER_REQUEST_DETAILS_PAGE_LINK' => $data_arr['orderData']['customer_detail_page_link']
                );
                break;

            case '55':      //request for shipping address accepted,from goo2o to buyer
                $this->ph = array(
                    'ORDER_ITEM_ID' => $data_arr['orderData']['orderItemId'],
                    'CUSTOMER_NAME' => $data_arr['orderData'][0]['buyer_name'],
                    'CUSTOMER_EMAIL_ID' => $data_arr['orderData'][0]['buyer_email'],
                    'REQUEST_DATE' => date('d/m/y', $data_arr['orderData']['request_date']),
                    'STORE_NAME' => $data_arr['orderData'][0]['title'],
                    'STORE_URL' => $data_arr['orderData'][0]['seller_mallurl'],
                    'NEW_ADDRESS' => $data_arr['orderData']['new_address'],
                    'CUSTOMER_REQUEST_DETAILS_PAGE_LINK' => $data_arr['orderData']['customer_detail_page_link']
                );
                break;

            case '56':      //request for shipping address accepted,from seller to buyer
                $this->ph = array(
                    'ORDER_ITEM_ID' => $data_arr['orderData']['orderItemId'],
                    'CUSTOMER_NAME' => $data_arr['orderData'][0]['buyer_name'],
                    'CUSTOMER_EMAIL_ID' => $data_arr['orderData'][0]['buyer_email'],
                    'REQUEST_DATE' => date('d/m/y', $data_arr['orderData']['request_date']),
                    'STORE_NAME' => $data_arr['orderData'][0]['title'],
                    'STORE_URL' => $data_arr['orderData'][0]['seller_mallurl'],
                    'NEW_ADDRESS' => $data_arr['orderData']['new_address'],
                    'CUSTOMER_REQUEST_DETAILS_PAGE_LINK' => $data_arr['orderData']['customer_detail_page_link'],
                    'STORE_OWNER_SENDING_EMAIL' => 'harpreetankush@gmail.com'
                );
                break;

            case '57':      //request for shipping address accepted,from Goo2o to seller
                $this->ph = array(
                    'ORDER_ITEM_ID' => $data_arr['orderData']['orderItemId'],
					'CUSTOMER_NAME' => $data_arr['orderData'][0]['buyer_name'],
                    'STORE_OWNER_EMAIL_ID' => 'harpreetankush@gmail.com',
                    'STORE_OWNER_NAME' => $data_arr['orderData'][0]['seller_name'],
                    'REQUEST_DATE' => date('d/m/y', $data_arr['orderData']['request_date']),
                    'STORE_NAME' => $data_arr['orderData'][0]['title'],
                    'NEW_ADDRESS' => $data_arr['orderData']['new_address'],
                    'STORE_OWNER_REQUEST_DETAILS_PAGE_LINK' => $data_arr['orderData']['customer_detail_page_link'],
                    'STORE_URL' => $data_arr['orderData'][0]['seller_mallurl']
                );
                break;

            case '53':      //request for shipping address rejected,from Goo2o to buyer
                $this->ph = array(
                    'ORDER_ITEM_ID' => $data_arr['orderData']['orderItemId'],
                    'CUSTOMER_NAME' => $data_arr['orderData'][0]['buyer_name'],
                    'CUSTOMER_EMAIL_ID' => $data_arr['orderData'][0]['buyer_email'],
                    'REQUEST_DATE' => date('d/m/y', $data_arr['orderData']['request_date']),
                    'STORE_NAME' => $data_arr['orderData'][0]['title'],
                    'CUSTOMER_REQUEST_DETAILS_PAGE_LINK' => $data_arr['orderData']['customer_detail_page_link'],
                    'STORE_URL' => $data_arr['orderData'][0]['seller_mallurl']
                );
                break;

            case '54':      //request for shipping address rejected,from seller to buyer
                $this->ph = array(
                    'ORDER_ITEM_ID' => $data_arr['orderData']['orderItemId'],
                    'CUSTOMER_NAME' => $data_arr['orderData'][0]['buyer_name'],
                    'CUSTOMER_EMAIL_ID' => $data_arr['orderData'][0]['buyer_email'],
                    'REQUEST_DATE' => date('d/m/y', $data_arr['orderData']['request_date']),
                    'STORE_NAME' => $data_arr['orderData'][0]['title'],
                    'CUSTOMER_REQUEST_DETAILS_PAGE_LINK' => $data_arr['orderData']['customer_detail_page_link'],
                    'STORE_URL' => $data_arr['orderData'][0]['seller_mallurl'],
                    'STORE_OWNER_SENDING_EMAIL' => 'harpreetankush@gmail.com'
                );
                break;
            case '64':
                $this->toMailInfo('SET-INFO', array('to_id' => $data_arr['to_id'], 'to_mail' => $data_arr['to_mail'], 'to_name' => $data_arr['to_name']));
                $this->fromMailInfo('SET-INFO', array('from_id' => $data_arr['from_id'], 'from_mail' => $data_arr['from_mail'], 'from_name' => $data_arr['from_name']));
                $this->ph = array(
                    'STORE_NAME' => $data_arr['store_name'],
                    'CUSTOMER_NAME' => $data_arr['customer_name'],
                    'PRODUCT_NAME' => $data_arr['product_name'],
                    'ORDER_ID' => $data_arr['order_id'],
                    'GIFT_CERTIFICATE_APPLIED' =>  $data_arr['gift_certificate'],
                    'ORDER_DATE' => $data_arr['order_date'],
                    'TOTAL_SHIPPING_AMOUNT' => number_format($data_arr['total_shipping'], 2),
                    'DISCOUNT_COUPON_APPLIED' =>$data_arr['discount_coupon'],
                    'TOTAL_AMOUNT' => number_format($data_arr['total_amount'], 2),
                    'BILLING_ADDRESS' => $data_arr['billing_address'],
                    'ACKNOWLEDGEMENT_RECEIPT_LINK' => $data_arr['link'],
					'STORE_URL'=> $data_arr['store_url'],
					'STORE_CONTACT_NUMBER'=>$data_arr['store_phone'],
					'STORE_EMAIL_ID'=>$data_arr['store_emails']
                );
                break;
            case '65':

                //echo 'df gdfgdfgf';exit;
                $this->toMailInfo('SET-INFO', array('to_id' => $data_arr['from_id'], 'to_mail' => $data_arr['from_mail'], 'to_name' => $data_arr['from_name']));
                $this->ph = array(
                    'STORE_NAME' => $data_arr['store_name'],
                    'CUSTOMER_NAME' => $data_arr['customer_name'],
                    'BILLING_ADDRESS' => $data_arr['billing_address'],
                    'ORDER_ID' => $data_arr['order_id'],
                    'TOTAL_AMOUNT' => $data_arr['total_amount'],
                    'ORDER_DATE' => $data_arr['order_date'],
                    'TOTAL_SHIPPING_AMOUNT' => $data_arr['total_shipping'],
                    'GIFT_CERTIFICATE_APPLIED' => $data_arr['gift_certificate'],
                    'DISCOUNT_COUPON_APPLIED' => $data_arr['discount_coupon'],
                    'ORDER_ITEM_ID' => $data_arr['order_item_detail'],
                    'STORE_OWNER_NAME' => $data_arr['store_owner_name'],
                );
                break;
            case '77':
                $this->toMailInfo('SET-INFO', array('to_id' => $data_arr['from_id'], 'to_mail' => $data_arr['from_mail'], 'to_name' => $data_arr['from_name']));
                //echo 'df gdfgdfgf';exit;
                $this->ph = array(
                    'STORE_NAME' => $data_arr['store_name'],
                    'CUSTOMER_NAME' => $data_arr['customer_name'],
                    'BILLING_ADDRESS' => $data_arr['billing_address'],
                    'PRODUCT_NAME' => $data_arr['order_item_detail'],
                    'ORDER_ID' => $data_arr['order_id'],
                    'TOTAL_AMOUNT' => $data_arr['total_amount'],
                    'ORDER_DATE' => $data_arr['order_date'],
                    'ORDER_ITEM_ID' => $data_arr['order_item_detail'],
                    'STORE_OWNER_NAME' => $data_arr['store_owner_name'],
                );
                break;
            case '80':
                $this->toMailInfo('SET-INFO', array('to_id' => $data_arr['from_id'], 'to_mail' => $data_arr['from_mail'], 'to_name' => $data_arr['from_name']));
                //echo 'df gdfgdfgf';exit;
                $this->ph = array(
                    'STORE_NAME' => $data_arr['store_name'],
                    'CUSTOMER_NAME' => $data_arr['customer_name'],
                    'BILLING_ADDRESS' => $data_arr['billing_address'],
                    'ORDER_ITEM_ID' => $data_arr['order_item_detail'],
                    'GIFT_CERTIFICATE_APPLIED' =>  $data_arr['gift_certificate'],
                    'ORDER_DATE' => $data_arr['order_date'],
                    'DISCOUNT_COUPON_APPLIED' =>$data_arr['discount_coupon'],
                    'ORDER_ID' => $data_arr['order_id'],
                    'TOTAL_AMOUNT' => $data_arr['total_amount'],
                    'ORDER_DATE' => $data_arr['order_date'],
                    'STORE_OWNER_NAME' => $data_arr['store_owner_name'],
					'STORE_URL'=> $data_arr['store_url']
                );
                break;

            case '81':
                $this->toMailInfo('SET-INFO', array('to_id' => $data_arr['to_id'], 'to_mail' => $data_arr['to_mail'], 'to_name' => $data_arr['to_name']));
                //echo 'df gdfgdfgf';exit;
                $this->ph = array(
                    'STORE_NAME' => $data_arr['store_name'],
                    'CUSTOMER_NAME' => $data_arr['customer_name'],
                    'BILLING_ADDRESS' => $data_arr['billing_address'],
                    'PRODUCT_NAME' => $data_arr['order_item_detail'],
                    'GIFT_CERTIFICATE_APPLIED' => $data_arr['gift_certificate'],
                    'ORDER_DATE' => $data_arr['order_date'],
                    'DISCOUNT_COUPON_APPLIED' =>$data_arr['discount_coupon'],
                    'ORDER_ID' => $data_arr['order_id'],
                    'TOTAL_AMOUNT' => $data_arr['total_amount'],
                    'ORDER_DATE' => $data_arr['order_date'],
					'STORE_URL'=> $data_arr['store_url'],
					'STORE_CONTACT_NUMBER'=>$data_arr['store_phone'],
					'STORE_EMAIL_ID'=>$data_arr['store_emails']
                );
                break;
            case '63':
                $this->toMailInfo('SET-INFO', array('to_id' => $data_arr['to_id'], 'to_mail' => $data_arr['to_mail'], 'to_name' => $data_arr['to_name']));
                $this->ph = array(
                    'STORE_NAME' => $data_arr['store_name'],
                    'CUSTOMER_NAME' => $data_arr['customer_name'],
                    'TOTAL_AMOUNT' => $data_arr['total_amount'],
                    'ORDER_ID' => $data_arr['order_id'],
                    'TOTAL_AMOUNT' => $data_arr['total_amount'],
                    'ORDER_DATE' => $data_arr['order_date'],
                    'BILLING_ADDRESS' => $data_arr['billing_address'],
                    'CUSTOMER_EMAIL_ID' => $data_arr['to_mail'],
					 'PRODUCT_NAME' => $data_arr['order_item_detail'],
					  'DISCOUNT_COUPON_APPLIED' =>$data_arr['discount_coupon'],
					  'GIFT_CERTIFICATE_APPLIED' => $data_arr['gift_certificate'],
					   'TOTAL_SHIPPING_AMOUNT' => $data_arr['total_shipping']
					 
                );
                break; /**/

            case '33':      //confirm return shipment from goo2o to store owner
                $returnType = ($data_arr['orderData']['return_type'] == '1') ? 'Refund' : 'Replacement';
                $this->ph = array(
                    'ORDER_ITEM_ID' => $data_arr['orderData'][0]['orderItemId'],
                    'STORE_NAME' => $data_arr['orderData'][0]['title'],
                    'STORE_URL' => $data_arr['orderData'][0]['seller_mallurl'],
                    'SHIPPING_DATE' => date('d/m/y', $data_arr['orderData']['order_shipment_date_created']),
                    'EXPECTED_TRANSIT_TIME' => $data_arr['orderData']['order_ship_date'],
                    'COURIER_NAME' => $data_arr['orderData']['order_carrier_name'],
                    'DOCKET_NUMBER' => $data_arr['orderData']['tracking_id'],
                    'SHIPPING_ADDRESS' => $data_arr['orderData']['address_line_one'],
                    'CUSTOMER_EMAIL_ID' => $data_arr['orderData'][0]['buyer_email'],
                    'CUSTOMER_NAME' => $data_arr['orderData'][0]['buyer_name'],
                    'RETURN_ID' => $data_arr['orderData']['return_id'],
                    'SHIPMENT_ITEMS_LIST' => $data_arr['orderData']['shipment_list'],
                    'RETURN_TYPE' => $returnType,
                    'CUSTOMER_SHIPMENT_DETAIL_PAGE_LINK' => $data_arr['orderData']['shipment_detail_page_customer'],
                    'SHIPMENT_ID' => $data_arr['orderData']['shipment_id'],
                    'PURCHASE_DETAIL_PAGE_LINK' => $data_arr['orderData']['purchase_link'],
                    'STORE_EMAIL_ID' => 'harpreetankush@gmail.com',
                    'STORE_CONTACT_NUMBER' => $data_arr['orderData'][0]['seller_contact_number'],
                    'REASON_RETURN' => $data_arr['orderData']['return_reason']
                );
                break;

            case '72':      //Store location request sent for registration with FBG from goo2o to storeowner
                $this->ph = array(
                    'STORE_OWNER_NAME' => $data_arr['notiData']['store_owner_name'],
                    'NEW_STORE_LOCATION' => $data_arr['notiData']['address'],
                    //'NEW_STORE_LOCATION' => 'test address',   
                    'LOCATION_ID' => $data_arr['notiData']['location_label']
                );
                break;

            case '73':      //store location edited from goo2o to storeowner
                $this->ph = array(
                    'STORE_OWNER_NAME' => $data_arr['notiData']['store_owner_name'],
                    'NEW_STORE_LOCATION' => $data_arr['notiData']['address'],
                    'LOCATION_ID' => $data_arr['notiData']['location_label']
                );
                break;

            case '76':      //store location edited from goo2o to storeowner
                $this->ph = array(
                    'STORE_OWNER_NAME' => $data_arr['notiData']['store_owner_name'],
                    'NEW_STORE_LOCATION' => $data_arr['notiData']['address']
                );
                break;

            case '74':
                $this->ph = array('STORE_OWNER_NAME' => $data_arr['notiData']['store_owner_name'],
                    'NEW_STORE_LOCATION' => $data_arr['notiData']['address']
                        // 'BLUE_DART_CONTACT_NUMBER' => $data_arr['notiData']['bluedart_no']
                );

                break;

            case '75':
                $this->ph = array('STORE_OWNER_NAME' => $data_arr['notiData']['store_owner_name'],
                    'NEW_STORE_LOCATION' => $data_arr['notiData']['address'],
                    'BLUE_DART_CONTACT_NUMBER' => $data_arr['notiData']['bluedart_no']
                );
				break;
			case '76':
				$this->toMailInfo('SET-INFO', array('to_id' => $data_arr['to_id'], 'to_mail' => $data_arr['to_mail'], 'to_name' => $data_arr['store_owner_name']));
                $this->ph = array('STORE_OWNER_NAME' => $data_arr['store_owner_name'],'GC_SENDER_NAME' => $data_arr['gc_sender_name'],'ORDER_DATE' => $data_arr['order_date'],'ORDER_ID' => $data_arr['store_owner_name'],'GIFT_CERTIFICATE_NAME' => $data_arr['gc_name'],'GIFT_CERTIFICATE_AMOUNT' => $data_arr['gc_amount'],'RECIPIENT_EMAIL_ADDRESS' => $data_arr['recipient_email_address'],'GIFT_CERTIFICATE_SEND_DATE' => $data_arr['gc_send_date'],'GC_RECIPIENT_NAME' => $data_arr['recipient_name'],'GIFT_CERTIFICATE_QUANTITY' => $data_arr['gc_quantity']
                );
				break;
				 case '96':
                $this->toMailInfo('SET-INFO', array('to_id' => '','to_mail' => $data_arr['toemail'], 'to_name' =>''));
                $this->fromMailInfo('SET-INFO', array('from_id' => $data_arr['fromid'], 'from_mail' =>  $data_arr['fromemail'], 'from_name' =>  $data_arr['storename']));
                $this->ph = array(
                    'STORE_NAME' => $data_arr['storename'],
                    'ROLE_MODULE' => $data_arr['permissions'],
                    'STORE_EMAIL_ID' => $data_arr['storemail'],
                    'STORE_CONTACT_NUMBER' => $data_arr['storephone'],
                    'STORE_URL' =>  $data_arr['storeurl'],
                    'STORE_OWNER_NAME' => $data_arr['storeownername'],
					
                   
                );
                break;
				 case '95':
                $this->toMailInfo('SET-INFO', array('to_id' => '','to_mail' => $data_arr['toemail'], 'to_name' =>''));
                $this->fromMailInfo('SET-INFO', array('from_id' => $data_arr['fromid'], 'from_mail' =>  $data_arr['fromemail'], 'from_name' =>  $data_arr['storename']));
                $this->ph = array(
                    'STORE_NAME' => $data_arr['storename'],
                    'ROLE_MODULE' => $data_arr['permissions'],
                    'STORE_EMAIL_ID' => $data_arr['storemail'],
                    'STORE_CONTACT_NUMBER' => $data_arr['storephone'],
                    'STORE_URL' =>  $data_arr['storeurl'],
                    'STORE_OWNER_NAME' => $data_arr['storeownername'],
					
                   
                );
                break;
				 case '94':
                $this->toMailInfo('SET-INFO', array('to_id' => '','to_mail' => $data_arr['toemail'], 'to_name' =>''));
                $this->fromMailInfo('SET-INFO', array('from_id' => $data_arr['fromid'], 'from_mail' =>  $data_arr['fromemail'], 'from_name' =>  $data_arr['storename']));
                $this->ph = array(
                    'STORE_NAME' => $data_arr['storename'],
                    'ROLE_MODULE' => $data_arr['permissions'],
                    'STORE_EMAIL_ID' => $data_arr['storemail'],
                    'STORE_CONTACT_NUMBER' => $data_arr['storephone'],
                    'STORE_URL' =>  $data_arr['storeurl'],
                    'STORE_OWNER_NAME' => $data_arr['storeownername'],
					 'OLD_ROLE_STATUS' => $data_arr['oldstatus'],
					  'ROLE_STATUS' => $data_arr['newstatus'],
					
                   
                );
                break;
				 case '97':
                $this->toMailInfo('SET-INFO', array('to_id' => '','to_mail' => $data_arr['toemail'], 'to_name' =>''));
                $this->fromMailInfo('SET-INFO', array('from_id' => $data_arr['fromid'], 'from_mail' =>  $data_arr['fromemail'], 'from_name' =>  $data_arr['storename']));
                $this->ph = array(
                    'STORE_NAME' => $data_arr['storename'],
                    'ROLE_MODULE' => $data_arr['permissions'],
                    'STORE_EMAIL_ID' => $data_arr['storemail'],
                    'STORE_CONTACT_NUMBER' => $data_arr['storephone'],
                    'STORE_URL' =>  $data_arr['storeurl'],
                    'STORE_OWNER_NAME' => $data_arr['storeownername'],
					 'OLD_ROLE_STATUS' => $data_arr['oldstatus'],
					  'ROLE_STATUS' => $data_arr['newstatus'],
					
                   
                );
                break;
				 case '91':
                $this->toMailInfo('SET-INFO', array('to_id' => '','to_mail' => $data_arr['toemail'], 'to_name' =>''));
                $this->ph = array(
                    'STORE_NAME' => $data_arr['storename'],
                    'ROLE_MODULE' => $data_arr['permissions'],
                    'STORE_EMAIL_ID' => $data_arr['storemail'],
                    'STORE_CONTACT_NUMBER' => $data_arr['storephone'],
                    'STORE_URL' =>  $data_arr['storeurl'],
                    'STORE_OWNER_NAME' => $data_arr['storeownername'],
					 'OLD_ROLE_STATUS' => $data_arr['oldstatus'],
					  'ROLE_STATUS' => $data_arr['newstatus'],
					
                   
                );
                break;
            default:
                $this->result_arr['ph'] = 'ERROR';
                break;
        }
    }

    /**
     * Created By : Mrunal Kanti Roy
     * @var $data : required data_array 
     * Creation Date : 7-11-2011	* Modified Date :
     * Reason : get details of 
     * */
    public function demo($case, $data = '') {
        switch ($case) {
            case '':

                break;

            default:
                break;
        }
    }

}

?>
