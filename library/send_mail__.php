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
    
    public function __construct(){
        require_once 'Zend/Mail.php';
        require_once 'Zend/Mail/Transport/Smtp.php';
        
        $this->db = Zend_Db_Table::getDefaultAdapter();
        $this->user = new Zend_Session_Namespace('SESSION');
        
    }
    
    public function test($data_arr = 'test'){
        return $data_arr;
    }
    /**
     * Created By : Mrunal Kanti Roy
     * @var $id : Trigger Id to be fire
     * @var $data_arr : input data in the trigger
     * Creation Date : 5-11-2011	* Modified Date :
     * Reason : 
     **/
    public function triggerFire($tId,$data_arr = array()){
        $this->tid = $tId;
        try {
            $sql = $this->db->select()->from(array(TABLE_NOTIFICATION_TRIGGERS),array('*'))
                               ->where('trigger_id = ?',$this->tid)
                               ->where('trash = ?','NO');
            $this->trigger_info = $this->db->fetchRow($sql);

            /*------ set placeholder if Trigger Active ----*/
            if($this->trigger_info['trigger_status'] == '1' && (($this->trigger_info['trigger_email_flag'] == 'ON') || ($this->trigger_info['trigger_update_flag'] == 'ON'))){
                if($data_arr['no_alert_flag'] == 'YES'){$this->trigger_info['trigger_update_flag'] = 'OFF';}
                /*-----Set from mail info--(SELLER , O2O)---*/
                if($this->trigger_info['trigger_ownership'] == 'SELLER'){
                    $this->fromMailInfo('USER-ID', $data_arr['from_id']);
                } else {
                    $this->fromMailInfo('DO-NOT-REPLY');
                }

                /*-----Set to mail info---------- (0 = Store Owner, 1 = Product owner, 2 = Customer) ------------- */
                $this->toMailInfo('USER-ID', $data_arr['to_id']);

                $this->setPlaceholder($this->trigger_info['trigger_id'], $data_arr);

                /*------ send Mail if Mail Active ----*/
                if($this->trigger_info['trigger_email_flag'] == 'ON'){
                        $this->manageMail();
                }

                /*------ alert fire if alert Active ----*/
                if($this->trigger_info['trigger_update_flag'] == 'ON'){
                    $this->manageAlert();
                }

            }else{
                return 'Invalid trigger (497.8)';
            }
        }
        catch (Exception $e)
        {
            echo 'Exception caught: ',  $e->getMessage(), "\n";
        }
    }
    
    /**
     * Created By : Mrunal Kanti Roy
     * Creation Date : 7-11-2011	* Modified Date :
     * Reason : set 'mail_body' and 'mail_subject' then send_mail
     **/
    public function manageMail(){
        
        $sql = $this->db->select()->from(array('nda'=>TABLE_NOTIFICATION_DETAILS_ADMIN),array('*'))
                                  ->joinLeft(array('nsf' => TABLE_NOTIFICATION_SUPPORT_FOOTER),'nsf.footer_id = nda.support_footer_id',array('sf_body'=>'support_footer_body'))
                                  ->joinLeft(array('ns' => TABLE_NOTIFICATION_SIGNATURE),'ns.signature_id = nda.signature_id',array('s_body'=>'signature_body'))
                                  ->joinLeft(array('ndf' => TABLE_NOTIFICATION_DEFAULT_FOOTER),'ndf.default_footer_id = nda.default_footer_id',array('df_body'=>'df_body'))
                                  ->where('trigger_id = ?',$this->tid);
        $admin_mail = $this->db->fetchRow($sql);
        
//        $supportFooter = $this->getSupportFooter($admin_mail['support_footer_id']);
//        $signature = $this->getSignature($admin_mail['signature_id']);
//        $default_footer = $this->getDefaultFooter($admin_mail['default_footer_id']);
        
        /*-------Assign placeholder in support footer-------*/
        $ph_sf = array();
        foreach($ph_sf as $key=>$val){
            $admin_mail['sf_body'] = str_replace('{'.$key.'}', $val, $admin_mail['sf_body']);
        }
        
        /*-------Assign placeholder in signature------------*/
        $ph_s = array();
        foreach($ph_s as $key=>$val){
            $admin_mail['s_body'] = str_replace('{'.$key.'}', $val, $admin_mail['s_body']);
        }
        
        /*-------Assign placeholder in default footer------------*/
        $ph_df = array('PH_NOT_MY_ACCOUNT'=> $this->ph_not_my_account , 'PH_ACCOUNT_SETTINGS'=>HTTP_SECURE."/accountsetting" );
        foreach($ph_df as $key=>$val){
            $admin_mail['df_body'] = str_replace('{'.$key.'}', $val, $admin_mail['df_body']);
        }
        
        /*-------Assign placeholder in mail body ------------*/
        foreach($this->ph as $key=>$val){
            $admin_mail['mail_body'] = str_replace('{'.$key.'}', $val, $admin_mail['mail_body']);
            $admin_mail['mail_body'] = str_replace('%7B'.$key.'%7D', $val, $admin_mail['mail_body']);
            $admin_mail['mail_subject'] = str_replace('{'.$key.'}', $val, $admin_mail['mail_subject']);
        }
        
        $this->mail['body'] = $admin_mail['mail_body'].'<br/><br/>'.$admin_mail['sf_body'].'<br/><br/>'.$admin_mail['s_body'].'<br/><br/>'.$admin_mail['df_body'];
        $this->mail['subject']= $admin_mail['mail_subject'];

        $this->mailFire();
        
    }
    
    /**
     * Created By : Mrunal Kanti Roy
     * Creation Date : 7-11-2011	* Modified Date :
     * Reason : 
     **/
    public function manageAlert(){
        $sql = $this->db->select()->from(array(TABLE_NOTIFICATION_UPDATE_DATA),array('update_body'))
                                  ->where('trigger_id = ?',$this->tid)
                                  ->where('api_key = ?',0);

        $msg_text = $this->db->fetchOne($sql);
        
        foreach($this->ph as $key=>$val){
            $msg_text = str_replace('{'.$key.'}', $val, $msg_text);
            $msg_text = str_replace('%7B'.$key.'%7D', $val, $msg_text);
        }
        
        $insert_data = array('user1'=>$this->mail['from_id'],
                             'user2'=>$this->mail['to_id'],
                             'created_date'=>  time(),
                             'modified_date'=>  time(),
                             'message_type'=>'4',
                             'request_text'=>$msg_text,
                             'ipaddress'=>$_SERVER['REMOTE_ADDR'],
                             'readby'=>$this->mail['from_id']);
        $this->db->insert(TABLE_CHAT, $insert_data);
        $this->result_arr['alert'] = is_numeric($this->db->lastInsertId()) ? 'INSERTED' : 'ERROR';
    }
    
    /**
     * Created By : Mrunal Kanti Roy
     * @var 
     * Creation Date : 7-11-2011	* Modified Date :
     * Reason : 
     **/
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
     **/
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
     **/
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
     **/
    public function mailFire($mail = array()){
        if(count($mail)>0){
            $this->mail = $mail;
        }
        if(count($this->mail)>0){
            $mail = new Zend_Mail();
            $mail->setFrom($this->mail['from_mail'], $this->mail['from_name']);
            $mail->setBodyHtml($this->mail['body']);
            $mail->addTo($this->mail['to_mail'], $this->mail['to_name']);
//          $mail->addTo('ashislubumohanty@gmail.com', 'Ashis');
            $mail->addBcc('mrunal.roy@live.com', 'Mrunal');
            $mail->setSubject($this->mail['subject']);
            $mail->send();
            
        }else{
            return 'ERROR';
        }
    }
    
    /**
     * Created By : Mrunal Kanti Roy
     * @var $id : update/alert fire
     * Creation Date : 7-11-2011	* Modified Date :
     * Reason : 
     **/
    public function fromMailInfo($case, $data = ''){
        switch ($case) {
            case 'DO-NOT-REPLY':
                $this->from_info = $this->db->fetchRow("SELECT *  FROM ".TABLE_USER." WHERE user_email_address = 'do-not-reply@goo2o.com'");
                $this->mail['from_id'] = $this->from_info['id'];
                $this->mail['from_mail'] = $this->from_info['user_email_address'];
                $this->mail['from_name'] = $this->from_info['user_full_name'];
                break;
            
            case 'USER-ID':
                $this->to_info = $this->db->fetchRow('select * from '.TABLE_USER.' where id = '.$data['to_id']);
                $this->mail['from_id'] = $this->from_info['id'];
                $this->mail['from_mail'] = $this->from_info['user_email_address'];
                $this->mail['from_name'] = $this->from_info['user_full_name'];
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
     **/
    public function toMailInfo($case, $data = ''){
        switch ($case) {
            
            case 'USER-ID':
                $this->to_info = $this->db->fetchRow('select * from '.TABLE_USER.' where id = '.$data);
                $this->mail['to_id'] = $this->to_info['id'];
                $this->mail['to_mail'] = $this->to_info['user_email_address'];
                $this->mail['to_name'] = $this->to_info['user_full_name'];
                $this->ph_not_my_account = HTTPS_SECURE.'/login/notyouraccount/passcode/'.$this->to_info['vcode'];
                break;
            
            case 'SET-INFO':
                $this->to_info = $data;
                $this->mail['to_id'] = $data['to_id'];
                $this->mail['to_mail'] = $data['to_mail'];
                $this->mail['to_name'] = $data['to_name'];
                break;
            
            case 'TEST':        //for test
                $this->to_info = array('id'=>0, 'email'=>'mrunal.roy@goo2o.com', 'name'=>'Mrunal');
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
     **/
    public function setPlaceholder($tId,$data_arr = array()){
        
        $id = $tId;
        switch ($id){
            
            case '6':       //for test 
//                $this->toMailInfo('SET-INFO', array('to_mail'=>'mrunal69@yahoo.com', 'to_name'=>'Mrunal Kanti Roy','to_id'=>0));
//                $this->fromMailInfo('DO-NOT-REPLY');
                $data_str = 'Data Array()<br/>&nbsp;&nbsp;';
                
                $ph = array('OOOOOOOOOOOOOOOOOOOOO'=>'OOOOOOOOOOOOOOOOOOOOO');
                $all = array_merge($ph, $data_arr,array('OOOOOO - To - OOOOOO'=>'OOOOOO - mail - OOOOOOO'),$this->to_info, array('OOOOOO - From - OOOOOO'=>'OOOOOO - mail - OOOOOOO'), $this->from_info);
                
                foreach($all as $key=>$val){
                    $data_str .= $key.'&nbsp;&nbsp;##&nbsp;&nbsp;'.$val.'<br/>&nbsp;&nbsp;';
                }
                $this->ph = array('DATA'=>$data_str);
                
                break;
            
            case '1':
//                $this->toMailInfo('SET-INFO', array('to_mail'=>$data_arr['to_mail'], 'to_name'=>$data_arr['to_name'],'to_id'=>$data_arr['to_id']));
//                $this->fromMailInfo('DO-NOT-REPLY');
                
                $this->ph = array('USER_FULL_NAME' => $data_arr['to_name'],
                                  'ACCOUNT_VERIFICATION_LINK'=>$data_arr['account_verification_link'],
                                  'EDIT_BASIC_INFO_LINK'=>HTTPS_SECURE.'/accountsetting/editbasicinfo',
                                  'PRIMARY_EMAIL_ID'=>$data_arr['to_mail'],
                                  'MY_PAYMENTS_LINK'=>HTTPS_SECURE.'/mypayment');
                
                break;
            
            case '3':
//                $this->toMailInfo('SET-INFO', array('to_mail'=>$data_arr['to_mail'], 'to_name'=>$data_arr['to_name'],'to_id'=>$data_arr['to_id']));
//                $this->fromMailInfo('DO-NOT-REPLY');
                
                $this->ph = array('USER_FULL_NAME' => $data_arr['to_name'], 'PASSWORD_RESET_LINK'=>$data_arr['password_reset_link']);
                
                break;
                
            case '4':
//                $this->toMailInfo('SET-INFO', array('to_mail'=>$data_arr['to_mail'], 'to_name'=>$data_arr['to_name'],'to_id'=>$data_arr['to_id']));
//                $this->fromMailInfo('DO-NOT-REPLY');
                
                $this->ph = array('USER_FULL_NAME' => $data_arr['to_name']);
                
                break;
            
            case '7':
//                $this->toMailInfo('SET-INFO', array('to_mail'=>$data_arr['to_mail'], 'to_name'=>$data_arr['to_name'],'to_id'=>$data_arr['to_id']));
//                $this->fromMailInfo('DO-NOT-REPLY');
                
                $this->ph = array('USER_FULL_NAME' => $data_arr['to_name']);
                
                break;
            
            case '8':
//                $this->toMailInfo('SET-INFO', array('to_mail'=>$data_arr['to_mail'], 'to_name'=>$data_arr['to_name'],'to_id'=>$data_arr['to_id']));
//                $this->fromMailInfo('DO-NOT-REPLY');
                
                $this->ph = array('STORE_OWNER_NAME' => $data_arr['to_name'],'STORE_SUBDOMAIN_LINK' => $data_arr['store_subdomain_link']);
                
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
     **/
    public function demo($case, $data = ''){
        switch ($case) {
            case '':
                
                break;

            default:
                break;
        }
    }
}
?>