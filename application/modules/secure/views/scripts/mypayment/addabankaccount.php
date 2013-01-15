<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Add a bank account</title>
<link href="css/add_a_bank_account.css" rel="stylesheet" type="text/css" />
<link type="text/css" href="css/setting_header.css" rel="stylesheet" />
</head>

<body>
<div class="clearBoth">
	<?php include("includes/setting_header.php");?>
</div>
<table cellpadding="0" cellspacing="0" align="center" width="1000">
	<tr>
    	<!--Right panel starts from here-->
    	<td width="260" valign="top">
        	<div class="add_a_bank_accountLeftPanel">
            	<div class="lh25">&nbsp;</div>
            	<ul>
                	<li><a href="#" title="Account settings">Account settings</a></li>
                    <li class="selected">My payment</li>
                    <li><a href="#" title="Alerts">Alerts</a></li>
                    <li><a href="#" title="Store">Store</a></li>
                </ul>
            	
            </div>
        </td>
        <!--Right panel ends from here-->
        <!--Left panel starts from here-->
        <td width="740" valign="top">
        	<div class="add_a_bank_accountRightPanel">
                <div class="lh25">&nbsp;</div>
                
                <div class="clearBoth">
                	<div class="floatLeft">
                        <div class="floatLeft blueMainHeading">My payment</div>
                        <div class="navArrow">&nbsp;</div>
                        <div class="floatLeft content_text15Lh25"><b>Add a bank account</b></div>
                    </div>
                    <div class="floatRight">
                    	<div class="backArrow"></div>
                        <div class="floatLeft"><a href="#" class="hyperLinkBlue13" title="Back to my payment">Back to my payment</a></div>
                    </div>
                </div>
                <div class="lh25">&nbsp;</div>
                <div class="borderDotted">&nbsp;</div>
                <div class="lh25">&nbsp;</div>
                <div class="clearBoth greyText12Lh20">
                	Once you add a bank account, goo2o will deposit an amount on your mentioned account. You have to check your account for the amount deposited by goo2o and its transaction id which is required for confirmation of your bank account. You can confirm your bank account by filling in the amount and its transaction id on the confirmation page. Transaction ids are available on your bank statements.
                </div>
                <div class="lh10">&nbsp;</div>
                <div class="clearBoth greyText12Lh20">You have to provide IFSC code of your bank-branch so that you can receive your payments and withdraw amounts online, for the transactions made from any of the goo2o stores. Without IFSC code, no online transfer is possible.</div>
                <div class="lh5">&nbsp;</div>
                <div class="clearBoth greyText12Lh20">Please refer to your chequebook of the bank account for IFSC code or contact your bank-branch. Please ensure that you enter only the IFSC code and not the other codes like MICR, SWIFT, etc else your transfer will not be completed.</div>
                <div class="lh5">&nbsp;</div>
                <div class="clearBoth greyText12Lh20">While filling up the form mentioned below please make sure that the information you provided is accurate and complete. Rs.250 will be charged if the information is found to be inaccurate.</div>
                <div class="lh20">&nbsp;</div>
                <div class="clearBoth">
                	<div class="floatLeft content_text14"><b>Enter your bank details:</b></div>
                	<div class="lh20">&nbsp;</div>
                    <div class="clearBoth">
                    	<div class="wid160 content_text12Lh20"><b>Name</b></div>
                        <div class="floatLeft">	
                        	<div class="clearBoth"><input type="text" class="inputWid204" /></div>
                            <div class="lh5">&nbsp;</div>
                            <div class="clearBoth greyText11">Enter your full name as it appears in your bank records</div>
                       	</div>
                    	
                    </div>
                    <div class="lh15">&nbsp;</div>
                    <div class="clearBoth">
                    	<div class="wid160 content_text12Lh20"><b>Bank Name</b></div>
                        <div class="floatLeft">	
                        	<div class="clearBoth"><input type="text" class="inputWid204" /></div>
                            <div class="lh3">&nbsp;</div>
                            <div class="clearBoth greyText11">Enter the name of the bank where you hold an account</div>
                            <div class="lh3">&nbsp;</div>
                            <div class="clearBoth greyText11"><b>e.g.</b> HDFCs</div>
                       	</div>
                    	
                    </div>
                    <div class="lh15">&nbsp;</div>
                    <div class="clearBoth">
                    	<div class="wid160 content_text12Lh20"><b>IFSC Code</b></div>
                        <div class="floatLeft">	
                        	<div class="clearBoth"><input type="text" class="inputWid117" /></div>
                            <div class="lh3">&nbsp;</div>
                            <div class="clearBoth greyText11"><b>e.g.</b> HDFC0011</div>
                       	</div>
                    	
                    </div>
                    <div class="lh15">&nbsp;</div>
                    <div class="clearBoth">
                    	<div class="wid160 content_text12Lh20"><b>Account Number</b></div>
                        <div class="floatLeft">	
                        	<div class="clearBoth"><input type="text" class="inputWid204" /></div>
                        </div>
                    	
                    </div>
                    <div class="lh10">&nbsp;</div>
                    <div class="clearBoth">
                    	<div class="wid160 content_text12Lh20"><b>Re-enter Account Number</b></div>
                        <div class="floatLeft">	
                        	<div class="clearBoth"><input type="text" class="inputWid204" /></div>
                        </div>
                    	
                    </div>
                    
                </div>
                <div class="lh40">&nbsp;</div>
              	<div class="wid138">
                        <div class="floatLeft"><input type="image" class="cursor" src="images/continue_btn.png" title="Continue" /></div>
                        <div class="floatRight"><a href="#" class="hyperLinkBlue12Lh25" title="Cancel">Cancel</a></div>
                    </div>
               <div class="lh40">&nbsp;</div>
                
           </div>
        
        </td>
        <!--Left panel ends from here-->
    </tr>
</table>
</body>
</html>