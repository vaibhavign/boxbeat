<?php

/**
 * @author : Vaibhav Sharma
 * Used for Setting bank account and verification
 * Used DML library for the data manipulation 
 * Creation date : 11-11-2011
 * Confirmbank account is dependent on superadmin which is not yet decided
 * used setting_bank_transaction table for validating the entries
 * Values used for testing amount = 1.50 transaction id = abcd123456
 * modified data : 13-11-2011
 * reason : 
 */
class Secure_Model_MypaymentMapper extends DML {

    public $controller;
    public $action;
    private $userId;
    private $notification;

    public function __construct($str = '') {
        parent::__construct($str);
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $this->controller = $request->getControllerName();
        $this->action = $request->getActionName();
        $userName = new Zend_Session_Namespace('USER');
        $this->userId = $userName->userId;
        $this->notification = new Notification();
    }

    /*
      Author : Vaibhav Sharma
      Usage 	: Used for saving user bank account details
      args : $bdata => form post data
      : $userApiKey => user api key
      Create Date : 13-11-2011
      Modified Date :
     */

    public function saveBankAccount() {
        $args = func_get_args();
        $bdata = $args[0];
        $userApiKey = $args[1];
        $primaryBankAccout = $this->returnPrimaryBankAccount($userApiKey);
        if (empty($primaryBankAccout)) {
            $makeisprimary = 1;
        } else {
            $makeisprimary = 0;
        }
        if (!empty($bdata)) {
            $data = array(
                'user_api' => $userApiKey,
                'fullname' => $bdata['fullname'],
                'ifsc_code' => $bdata['ifsc_code'],
                'account_number' => $bdata['account_number'],
                'bankname' => $bdata['bankname'],
                'is_primary' => $makeisprimary,
                'status' => 0,
                'branch_name' => $bdata['branchname'],
                'pan_number' => $bdata['pan_card_number'],
                'added_on' => time());
            $lastInsertedIdMaster = $this->insertRecord('setting_bank', $data);
        } else {
            // redirect to listing page	
        }
    }

    /*
      Author : Vaibhav Sharma
      Usage 	: Return user bank account details
      args : $userApi => user api key
      Create Date : 13-11-2011
      Modified Date :
     */

    public function returnPrimaryBankAccount() {
        $args = func_get_args();
        $userApi = $args[0];
        $rs = $this->select('*')
                ->from('setting_bank')
                ->where(array('user_api' => $userApi))
                ->get()
                ->resultArray();
        return $rs;
    }

    /*
      Author : Vaibhav Sharma
      Usage 	: Return user bank account details
      args : $userApi => user api key
      Create Date : 13-11-2011
      Modified Date :
     */

    public function getBankAccountLisiting() {
        $args = func_get_args();
        $userApi = $args[0];
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select * from setting_bank where user_api='$userApi' and status > -1 and sb_type='1' order by modified_on desc";
        $rs = $db->fetchAll($query);
        return $rs;
    }

    /*
      Author : Vaibhav Sharma
      Usage 	: Return status and action dropdown
      args : $status => Status of the bank account
      $isprimary => 0 => no-primary 1 => primary
      $bankId => row id
      Create Date : 13-11-2011
      Modified Date :
     */

    public function getStatusAndDropdown() {
        $args = func_get_args();
        $status = $args[0];
        $isprimary = $args[1];
        $bankid = $args[2];
        if ($isprimary == 1) {
            $primaryText = ' (Primary)';
        } else {
            $primaryText = '';
        }
        $efg = "";
        switch ($status) {
            case 0:
                $Text = "Unconfirmed";
                $supportingText = "Deposits not sent";
                $dropDownArray = array('Remove');
                break;

            case 1:
                $Text = "Unconfirmed";
                $supportingText = "Deposits sent";
                $dropDownArray = array('Confirm', 'Remove');
                break;

            case 2:
                $Text = "Confirmed";
                $dropDownArray = array('Make it primary', 'Remove');
                break;

            default :
                $efg = "";
                $isprimary = 1;
                break;
        }

        if ($isprimary != 1) {
            $efg .= '<ul>';

            foreach ($dropDownArray as $key) {

                if ($key == "Confirm") {
                    $efg .='<li style="cursor:pointer" class="o2ojsactiondropdown" rel="' . $key . '_' . $bankid . '_' . $Text . '_' . $supportingText . '" ><a>' . $key . '</a></li>';
                } else {
                    $efg .='<li style="cursor:pointer" class="o2ojsactiondropdown" rel="' . $key . '_' . $bankid . '_' . $Text . '_' . $supportingText . '" ><a>' . $key . '</a></li>';
                }
            }
            $efg .= '</ul>';
        } else {
            if ($status == 1) {
                $efg .= '<ul>';

                foreach ($dropDownArray as $key) {

                    if ($key == "Confirm") {
                        $efg .='<li style="cursor:pointer" class="o2ojsactiondropdown" rel="' . $key . '_' . $bankid . '_' . $Text . '_' . $supportingText . '" ><a>' . $key . '</a></li>';
                    } else {
                        $efg .='<li style="cursor:pointer" class="o2ojsactiondropdown" rel="' . $key . '_' . $bankid . '_' . $Text . '_' . $supportingText . '" ><a>' . $key . '</a></li>';
                    }
                }
                $efg .= '</ul>';
            } else {
                $efg .= '<ul>';

                foreach ($dropDownArray as $key) {

                    if ($key == "Confirm") {
                        $efg .='<li style="cursor:pointer" class="o2ojsactiondropdown" rel="' . $key . '_' . $bankid . '_' . $Text . '_' . $supportingText . '" ><a>' . $key . '</a></li>';
                    } else {
                        $efg .='<li style="cursor:pointer" class="o2ojsactiondropdown" rel="' . $key . '_' . $bankid . '_' . $Text . '_' . $supportingText . '" ><a>' . $key . '</a></li>';
                    }
                }
                $efg .= '</ul>';
            }
        }
        $data = array();
        $data['dropdown'] = $efg;
        $data['text'] = $Text . $primaryText;
        $data['supporting'] = $supportingText;

//			$abc ='<div class="wid136">
//			<div class="lh5">&nbsp;</div>
//			<div class="content_text12Lh25">'.$Text.$primaryText.'</div>
//			<div class="clearBoth content_text11Lh12">'.$supportingText.'</div>
//			</div>
//			<div class="floatLeft">
//			<div class="lh5">&nbsp;</div>'.$efg
//			.'</div>';
        return $data;
    }

    /*
      Author : Vaibhav Sharma
      Usage 	: Increase tried counter
      args : $bankId => row id

      Create Date : 13-11-2011
      Modified Date :
     */

    public function increaseCounter() {
        $args = func_get_args();
        $bankId = $args[0];
        $rs = $this->select('*')
                ->from('setting_bank')
                ->where(array('id' => $bankId))
                ->get()
                ->resultArray();
        $tried = array('try' => ($rs[0]['try'] + 1));
        $nooftry = $rs[0]['try'];
        if ($nooftry > 1) {
            $this->updateRecord('setting_bank', $tried, array('id' => $bankId));
            $remove = array('status' => -2);
            $this->updateRecord('setting_bank', $remove, array('id' => $bankId));
            $info = $this->getTriggerInfo($bankId, 1);
            $this->notification->triggerFire(92, $info);

            header("Location: " . HTTPS_SECURE . "/mypayment/bankaccountrestricted/acid/" . $bankId);
        } else {
            $this->updateRecord('setting_bank', $tried, array('id' => $bankId));
        }
    }

    /*
      Author : Vaibhav Sharma
      Usage 	: Validate Transaction Amount
      args : $bankId => row id
      $transId => Transaction id

      Create Date : 13-11-2011
      Modified Date :
     */

//    public function validateTransactionandAmount() {
//        $args = func_get_args();
//        $transId = $args[0];
//        $amount = $args[1];
//        $bankId = $args[2];
//        $rs = $this->select('*')
//                ->from('setting_bank_transaction as sbt')
//                ->join('setting_bank as sb','sb.tid=sbt.tid','inner')
//                ->where(array('sbt.transaction_id' => $transId, 'sbt.amount' => $amount,'sb.id'=>$bankId))
//                ->get()
//                ->resultArray();
//        $abc = $this->countResult();
//
//        if ($abc == 1) {
//            $tried = array('status' => 2);
//            $this->updateRecord('setting_bank', $tried, array('id' => $bankId));
//            $return = "success";
//        } else {
//            $return = "fail";
//        }
//
//        return $return;
//    }
    
    public function validateTransactionandAmount() {
        $args = func_get_args();
        $transId = $args[0];
        $amount = $args[1];
        $bankId = $args[2];


        $rs = $this->select('*')
                ->from('setting_bank_transaction as sbt')
                ->join('setting_bank as sb', 'sb.tid=sbt.tid', 'inner')
                ->where(array('sbt.transaction_id' => $transId, 'sbt.amount' => $amount, 'sb.id' => $bankId))
                ->get()
                ->resultArray();
			
		 $this->lastQuery();	
        $abc = $this->countResult();

        if ($abc == 1) {
            $status = array('status' => 2);
            $this->updateRecord('setting_bank', $status, array('id' => $bankId));
            $return = "success";
        } else {
            $rs = $this->select('try')
                    ->from('setting_bank')
                    ->where(array('id' => $bankId))
                    ->get()
                    ->rowArray();
            $try = $rs['try'];
            if ($try == 2) {
                $info = $this->getTriggerInfo($bankId, 1);
                $this->notification->triggerFire(92, $info);
                $this->updateRecord('setting_bank',array('status'=>PAYMENTS_BANK_ACCOUNT_RESTRICTED),array('id'=>$bankId));
                header("Location: " . HTTPS_SECURE . "/mypayment/bankaccountrestricted/acid/" . $bankId);
                exit;
            } else {
                $this->updateRecord('setting_bank', array('try' => $rs['try'] + 1), array('id' => $bankId));
				//echo $this->lastQuery(); exit;
                $return = "fail";
            }
        }

        return $return;
    }

    /*
      Author : Vaibhav Sharma
      Usage 	: Get specific bank entry detail
      args : $bankId => row id
      Create Date : 13-11-2011
      Modified Date :
     */

    public function getBankDetails() {
        $args = func_get_args();
        $bankId = $args[0];
        $rs = $this->select('*')
                ->from('setting_bank')
                ->where(array('id' => $bankId))
                ->get()
                ->resultArray();
        return $rs;
    }

    /*
      Author : Vaibhav Sharma
      Usage 	: Make a particular bank account as primary
      args : $acId => row id
      Create Date : 13-11-2011
      Modified Date :
     */

    public function makeprimary() {
        $args = func_get_args();
        $acId = $args[0];
        $primary = array('is_primary' => 0);
        $this->updateRecord('setting_bank', $primary, array('is_primary' => 1));
        $primary = array('is_primary' => 1);
        $this->updateRecord('setting_bank', $primary, array('id' => $acId));
    }

    /*
      Author : Vaibhav Sharma
      Usage 	: Remove a particular account
      args : $acId => row id
      Create Date : 13-11-2011
      Modified Date :
     */

    public function removeAccount() {
        $args = func_get_args();
        $acId = $args[0];
        $remove = array('status' => -1);
        $this->updateRecord('setting_bank', $remove, array('id' => $acId));
    }

//    public function redirectToaddAccount() {
//        $args = func_get_args();
//        $userApi = $args[0];
//        $db = Zend_Db_Table::getDefaultAdapter();
//        $query = "select * from setting_bank where user_api='$userApi' and status > -1";
//        $rs = $db->fetchAll($query);
//        if (sizeof($rs) == 0) {
//            header("Location: " . HTTPS_SECURE . "/mypayment/bankaccountrestricted/addabankaccount");
//        } else {
//            header("Location: " . HTTPS_SECURE . "/mypayment/bankaccountlisting");
//        }
//    }

     public function redirectToaddAccount($userApi) {
        $rs = $this->getWhere('setting_bank', array('user_api' => $userApi, 'sb_type' => 1))->resultArray();
        if ($this->countResult() == 0) {
            header("Location: " . HTTPS_SECURE . "/mypayment/no-account");
        } else {
            $counter = 0;

            foreach ($rs as $items) {
                if ($items['status'] == '-1' OR $items['status'] == '-2') {
                    $counter++;
                }
            }

            if ($counter == $this->countResult()) {
                header("Location: " . HTTPS_SECURE . "/mypayment/no-account");
            } else {

                header("Location: " . HTTPS_SECURE . "/mypayment/bankaccountlisting");
            }
        }
    }
    public function checkwhetherBankidbelongstouser() {
        $args = func_get_args();
        $userApi = $args[0];
        $bankId = $args[1];
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select * from setting_bank where user_api='$userApi' and id=" . $bankId;
        $rs = $db->fetchAll($query);
        if (sizeof($rs) == 0) {
            header("Location: " . HTTPS_SECURE . "/mypayment/bankaccountrestricted/addabankaccount");
        }
    }

    public function returnBankAccountStatus() {
        $args = func_get_args();
        $bankAccountNumber = $args[0];
        $rs = $this->select('*')
                ->from('setting_bank')
                ->where(array('account_number' => $bankAccountNumber))
                ->where(array('status!' => '-1'))
                ->get()
                ->resultArray();
        //echo $this->lastQuery();exit;
        return $rs;
    }

    public function checking() {
        $args = func_get_args();
        $userApi = $args[0];
        $bankId = $args[1];
        $db = Zend_Db_Table::getDefaultAdapter();
        $query = "select * from setting_bank where user_api='$userApi' and status > -1 and sb_type=1"; //and id=".$bankId;
        $rs = $db->fetchAll($query);
        if (sizeof($rs) == 0) {
            return 'test';
            //header("Location: ".HTTPS_SECURE."/mypayment/bankaccountrestricted/addabankaccount");	
        }
    }

    public function getTriggerData($bankId) {
        return $this->select('*,u.id as userID')
                        ->from('setting_bank as sb')
                        ->join('username as un', 'sb.user_api=un.apikey', 'inner')
                        ->join('user as u', 'un.name=u.user_email_address', 'inner')
                        ->join('setting_bank_transaction as sbt', 'sb.tid=sbt.id', 'left')
                        ->where(array('sb.id' => $bankId))
                        ->get()
                        ->rowArray();
    }
public function getTriggerInfo($id, $sbtype=1) {

        switch ($sbtype) {
            case 1:
                $rs = $this->select('u.id as USER_ID, u.user_email_address as USER_EMAIL, u.user_full_name as USER_FULL_NAME,sb.account_number as ACCOUNT_NUMBER,sb.bankname as BANK_NAME , sbt.amount as AMOUNT_DEPOSITED,sbt.transaction_id as TRANSACTION_ID')
                        ->from('setting_bank as sb')
                        ->join('setting_bank_transaction as sbt', 'sbt.tid=sb.tid', 'left')
                        ->join('username as un', 'un.apikey=sb.user_api', 'left')
                        ->join('user as u', 'un.name=u.user_email_address', 'left')
                        ->where(array('sb.id' => $id, 'sb.sb_type' => 1))
                        ->get()
                        ->resultArray();
                break;
            case 2:
                $rs = $this->select('md.user_id as STORE_OWNER_ID, u.user_email_address as STORE_OWNER_EMAIL, u.user_full_name as STORE_OWNER_NAME,sb.account_number as ACCOUNT_NUMBER,sb.bankname as BANK_NAME , sbt.amount as AMOUNT_DEPOSITED,sbt.transaction_id as TRANSACTION_ID, md.business_name as BUSINESS_NAME , if(md.business_type=1,"Partnership",if(md.business_type=2,"Sole Proprietorship",if(md.business_type=3," Private limited","Public limited"))) as BUSINESS_STATUS')
                        ->from('setting_bank as sb')
                        ->join('setting_bank_transaction as sbt', 'sbt.tid=sb.tid', 'left')
                        ->join('username as un', 'un.apikey=sb.user_api', 'left')
                        ->join('user as u', 'un.name=u.user_email_address', 'left')
                        ->join('mall_detail as md', 'md.user_id=u.id', 'left')
                        ->where(array('sb.id' => $id, 'sb.sb_type' => 2))
                        ->get()
                        ->resultArray();
                break;
        }


        return $rs;
    }

}

// End class

