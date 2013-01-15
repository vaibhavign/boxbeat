<?php
//error_reporting(E_ALL);
class Secure_CheckoutSettingsController extends Zend_Controller_Action {

    private $mapp;
    private $sessionData;
    private $notification;
    private $storeInfo;

    public function init() {
        
		$this->view->headLink()->appendStylesheet('/css/secure/headersetting.css');
        $this->view->controller = $this->_request->getParam('controller');
        Zend_Layout::getMvcInstance()->setLayout('securesetting');
        Zend_Layout::getMvcInstance()->setLayoutPath(APPLICATION_PATH . '/modules/secure/layouts');
        $this->view->headLink()->appendStylesheet('/css/secure/mypapment_index.css');
        $this->mapp = new Secure_Model_CheckoutSettingsMapper();
        $this->orgLogin = new Zend_Session_Namespace('original_login');
        $user = new Zend_Session_Namespace('USER');
        $this->storeInfo = $user->stores[0];

       // echo_pre($_SESSION);
        if ($user->userId == '') {
            $_SESSION['mypage'] = CUR_URL;
           $this->_redirect(HTTP_SECURE . '/login');
        }
        $apikey = $user->userDetails[0]['title'];

        $this->view->businessName = $user->userDetails[0]['title'];
        
        $this->notification = new Notification();
    }

    public function indexAction() {

          $this->mapp->redirectToaddAccount($this->storeInfo['store_apikey']);
    }

    public function noAccountAction() {
        $this->view->headTitle('Checkout -' . $_SESSION['USER']['userDetails'][0]['title'] . PAGE_EXTENSION);
        $this->view->headMeta()->setName('keywords', 'Account setting , GoO2o Technologies');
        $this->view->headMeta()->setName('description', 'Account setting , GoO2o Technologies');
        $this->title = new Zend_Session_Namespace('USER');
        $userlocation = $_SESSION['USER']['userDetails'][0]['user_location'];
        $userstate_city = explode(',', $userlocation);
        $mapper = new Secure_Model_AccountsettingMapper();
        $userstate = $mapper->getuserstate($userstate_city[0]);
        $usercity = $mapper->getusercity($userstate_city[1]);
        $this->view->user_state = $userstate;
        $this->view->user_city = $usercity;
        $this->view->userimage = $this->title->userDetails[0]['user_image'];
       // echo_pre($_SESSION,0);
    }

    public function addabankaccountAction() {
     
        $url = $this->_request->getParams();
        if ($url['edit'] == 'true') {
            $this->view->headTitle('Edit bank account - ' . $_SESSION['USER']['userDetails'][0]['title'] . PAGE_EXTENSION);
        } else {
            $this->view->headTitle('Add bank account - ' . $_SESSION['USER']['userDetails'][0]['title'] . PAGE_EXTENSION);
        }
        $this->view->headMeta()->setName('keywords', 'Account setting , GoO2o Technologies');
        $this->view->headMeta()->setName('description', 'Account setting , GoO2o Technologies');
        $this->title = new Zend_Session_Namespace('USER');
        $this->bank = new Zend_Session_Namespace('BANK');
        $url = $this->_request->getParams();

        if (isset($_POST['isposted'])) {
            unset($errorMsg);
            $error = false;
            $validateEmpty = new Zend_Validate_NotEmpty();
            $validatorOnlyAlphabets = new Zend_Validate_Alpha(array('allowWhiteSpace' => true));
            $validatorMinMax11 = new Zend_Validate_StringLength(array('min' => 11, 'max' => 11));
            $validatorMinMax1016 = new Zend_Validate_StringLength(array('min' => 10, 'max' => 16));
            $validatorMinMax911 = new Zend_Validate_StringLength(array('min' => 9, 'max' => 11));
            $validatorOnlyAlphaNumeric = new Zend_Validate_Alnum(array('allowWhiteSpace' => true));
            $validatorOnlyNumeric = new Zend_Validate_Digits();

            // End validation for fullname
            // Validation for bankname
            if (!$validateEmpty->isValid($_POST['bankname'])) {
                $error = true;
                $errorMsg['bankname'] = "It can not be left blank";
            } else {
                // only alphbets to be used space allowed no special character
                if ($validatorOnlyAlphaNumeric->isValid($_POST['bankname'])) {
                    // value contains only allowed chars
                } else {
                    $error = true;
                    $errorMsg['bankname'] = "Please enter a valid bank name";
                }
            }
            if (!$validateEmpty->isValid($_POST['branch_name'])) {
                $error = true;
                $errorMsg['branch_name'] = "It can not be left blank";
            } else {
                // only alphbets to be used space allowed no special character
                if (!$validatorOnlyNumeric->isValid($_POST['branch_name'])) {
                    // value contains only allowed chars
                } else {
                    $error = true;
                    $errorMsg['branch_name'] = "Please enter a valid branch name";
                }
            }
            // End Validation for bankname
            // Validation for IFSC Code

            if (!$validateEmpty->isValid($_POST['ifsc_code'])) {
                $error = true;
                $errorMsg['ifsccode'] = "It can not be left blank";
            } else {
                // only alphbets to be used space allowed no special character
                if ($validatorOnlyAlphaNumeric->isValid($_POST['ifsc_code'])) {
                    // value contains only allowed chars
                } else {
                    $error = true;
                    $errorMsg['ifsccode'] = "Please enter a valid IFSC code";
                }

                if ($validatorMinMax11->isValid($_POST['ifsc_code'])) {
                    // value contains only allowed chars
                } else {
                    $error = true;
                    $errorMsg['ifsccode'] = "Please enter a valid IFSC code";
                }
            }

            // End Validation for IFSC Code
            // Validation for account number

            if (!$validateEmpty->isValid($_POST['account_number'])) {
                $error = true;
                $errorMsg['accountnum1'] = "It can not be left blank";
            } else {
                // only alphbets to be used space allowed no special character
                if ($validatorOnlyNumeric->isValid($_POST['account_number'])) {
                    // value contains only allowed chars
                } else {
                    $error = true;
                    $errorMsg['accountnum1'] = "Please enter a valid account number";
                }
                if ($validatorMinMax1016->isValid($_POST['account_number'])) {
                    // value contains only allowed chars
                } else {
                    $error = true;
                    $errorMsg['accountnum1'] = "Please enter a valid account number";
                }
                $arr = $this->mapp->checkAccount(trim($_POST['account_number']));
                if ($arr['account_exists'] == 1) {
                    $error = true;
                    $errorMsg['accountnum1'] = $arr['msg'];
                }
            }

            // End Validation for account number
            // Validation for Re-enter Account Number

            if (!$validateEmpty->isValid($_POST['re_account_number'])) {
                $error = true;
                $errorMsg['accountnum2'] = "It can not be left blank";
            } else {
                // only alphbets to be used space allowed no special character
                if ($validatorOnlyNumeric->isValid($_POST['re_account_number'])) {
                    // value contains only allowed chars
                } else {
                    $error = true;
                    $errorMsg['accountnum2'] = "Please enter a valid account number";
                }
                if ($validatorMinMax1016->isValid($_POST['re_account_number'])) {
                    // value contains only allowed chars

                    if ($_POST['re_account_number'] != $_POST['account_number']) {
                        $error = true;
                        $errorMsg['accountnum2'] = "Account number didn't match";
                    }
                } else {
                    $error = true;
                    $errorMsg['accountnum2'] = "Please enter a valid account number";
                }
            }

            if (!$validateEmpty->isValid($_POST['pan_number'])) {
                $error = true;
                $errorMsg['pan_number'] = "It can not be left blank";
            } else {
                if (!$validatorMinMax911->isValid($_POST['pan_number'])) {
                    $error = true;
                    $errorMsg['pan_number'] = "Please enter valid PAN number";
                } else {
                  
                    if (!validatePanCardNumber(strtoupper($_POST['pan_number']))) {

                        $error = true;
                        $errorMsg['pan_number'] = "Please enter a valid PAN number.";
                    }
                }
            }

            // End Validation for Re-enter Account number


            $this->bank->bank2 = $_POST;
            //echo_pre($_POST);
            if (!$error) {
                $this->_redirect('checkout-settings/bankreview');
            }
            $postDataArray = $_POST;
        } else if ($url['edit'] == 'true') {

            $postDataArray = $this->bank->bank2;
        } else {
            $postDataArray = "";
        }
        $this->view->dispData = $postDataArray;
        $this->view->errorMessages = $errorMsg;
    }

    public function bankreviewAction() {
        $this->view->headTitle('Review bank details - ' . $_SESSION['USER']['userDetails'][0]['title'] . PAGE_EXTENSION);
        $this->view->headMeta()->setName('keywords', 'Account setting , GoO2o Technologies');
        $this->view->headMeta()->setName('description', 'Account setting , GoO2o Technologies');
        $this->bank = new Zend_Session_Namespace('BANK');
        $this->view->reviewbank = $_SESSION['BANK']['bank2'];
        $this->sessionData = $this->view->reviewbank;
    }

    public function bankaccountlistingAction() {
        // $this->mapp->redirectToaddAccount($this->orgLogin->apikey);
        // check if any data exists
        $resultSet = $this->mapp->getWhere('setting_bank', array('user_api' => $this->storeInfo['store_apikey']))->resultArray();
        $arrRemoved = array();
        $arrRestricted = array();
        $arrRest = array();

        if (count($resultSet) > 0) {
            foreach ($resultSet as $items) {
                switch ($items['status']) {
                    case PAYMENTS_BANK_ACCOUNT_REMOVED:
                        array_push($arrRemoved, $items);
                        break;
                    case PAYMENTS_BANK_ACCOUNT_RESTRICTED:
                        array_push($arrRestricted, $items);
                        break;
                    default:
                        array_push($arrRest, $items);
                        break;
                }
            }
        }
       $this->bank = new Zend_Session_Namespace('BANK');
        if(!isset($this->bank->bank2)  && count($arrRest)<=0 ){
             $this->_redirect('/checkout-settings/no-account');
        }

        $this->view->headTitle('Checkouts - ' . $_SESSION['USER']['userDetails'][0]['title'] . PAGE_EXTENSION);
        $this->view->headMeta()->setName('keywords', 'Account setting , GoO2o Technologies');
        $this->view->headMeta()->setName('description', 'Account setting , GoO2o Technologies');
        $url = $this->_request->getParams();


        $orgLogin = new Zend_Session_Namespace('original_login');
        if ($url['save'] == 'account') {
            $this->bank = new Zend_Session_Namespace('BANK');
            if (isset($this->bank->bank2['account_number'])) {
                //echo_pre($this->bank->bank2);

                $this->mapp->saveBankAccount($this->bank->bank2, $this->storeInfo['store_apikey']);
                //die;
                unset($this->bank->bank2);
                $this->_redirect('checkout-settings/bankaccountlisting');
            }
        }
       // $this->mapp->redirectToaddAccount($this->orgLogin->apikey);

		
        $this->view->accountListing = $this->mapp->getStoreAccountListing($this->storeInfo['store_apikey']);
    }

    public function confirmbankaccountAction() {
        $this->view->headTitle('Confirm bank account - ' . $_SESSION['USER']['userDetails'][0]['title'] . PAGE_EXTENSION);
        $this->view->headMeta()->setName('keywords', 'Account setting , GoO2o Technologies');
        $this->view->headMeta()->setName('description', 'Account setting , GoO2o Technologies');
        $validateEmpty = new Zend_Validate_NotEmpty();
        $validatorFloat = new Zend_Validate_Float();
        $url = $this->_request->getParams();
        $mapper = $this->mapp;
        $errorMsg = "";
        if (isset($_POST['posted'])) {
            // code for validation
            $error = false;
            if (!$validateEmpty->isValid($_POST['transid'])) {
                $error = true;
                $errorMsg['transid'] = "It can not be left blank";
            }

            if (!$validateEmpty->isValid($_POST['amount'])) {
                $error = true;
                $errorMsg['amount'] = "It can not be left blank";
            } else {
                if (!$validatorFloat->isValid($_POST['amount'])) {
                    $error = true;
                    $errorMsg['amount'] = "Please enter a valid amount";
                }
            }

            if (!$error) {
               // $mapper->increaseCounter($url['acid']);
                $returnStatus = $mapper->validateTransactionandAmount($_POST['transid'], $_POST['amount'], $url['acid']);
                if ($returnStatus == "success") {
                    $info = $this->mapp->getTriggerInfo($url['acid'], 2);
                   // echo_pre($info);
                    $this->notification->triggerFire(132, $info);
                    $this->_redirect('checkout-settings/bankaccountconfirmedsuccess/acid/' . $url['acid']);
                } else {
                    $error = true;
                    $errorMsg['err_deposit_details'] = "Please enter the correct deposit details";
                }
            }
        }
        $this->view->errorMessages = $errorMsg;
        $this->view->bankaccountDetails = $mapper->getBankDetails($url['acid']);
    }

    public function bankaccountconfirmedsuccessAction() {
        $this->view->headTitle('Bank account confirmed - ' . $_SESSION['USER']['userDetails'][0]['title'] . PAGE_EXTENSION);
        $this->view->headMeta()->setName('keywords', 'Account setting , GoO2o Technologies');
        $this->view->headMeta()->setName('description', 'Account setting , GoO2o Technologies');
        $url = $this->_request->getParams();
        $mapper = $this->mapp;
        $this->view->bankaccountDetails = $mapper->getBankDetails($url['acid']);
    }

    public function bankaccountrestrictedAction() {

        $this->view->headTitle('Bank Account Restricted - ' . $_SESSION['USER']['userDetails'][0]['title'] . PAGE_EXTENSION);
        $this->view->headMeta()->setName('keywords', 'Account setting , GoO2o Technologies');
        $this->view->headMeta()->setName('description', 'Account setting , GoO2o Technologies');
        $url = $this->_request->getParams();
        $this->view->bankaccountDetails = $this->mapp->getBankDetails($url['acid']);
    }

    public function makeprimaryAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $acidId = $_GET['acid'];
        $mapper = $this->mapp;
        $this->view->bankaccountDetails = $mapper->makeprimary($acidId);
    }

    public function removeaccountAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $acidId = $_GET['acid'];
        $mapper = $this->mapp;
        $this->view->bankaccountDetails = $mapper->removeAccount($acidId);
    }

}

// End Class
?>
