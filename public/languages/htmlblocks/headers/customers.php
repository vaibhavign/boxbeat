<?php
switch($actionName){ 
case 'customerlist' :
    $this->generalObj = new General();
    $this->userName = new Zend_Session_Namespace('USER');
    $Sapikey=$this->userName->stores[0]['store_apikey'];
                        $user_email_id=$this->userName->userDetails[0]['user_email_address'];
                        $storepermission=$this->generalObj->checkUserPermissions($Sapikey,$user_email_id,7);
                        if(count($storepermission)==0){
                            $displayrightblock= 'false';
                        }
                       else if(count($storepermission)>0){ 
                            $storepermissionvalue=$this->generalObj->returnModuleAction($storepermission);
  if(in_array('inviteacustomer',$storepermissionvalue))                          

                            {
                               $displayrightblock= 'true';
                            }
	 else{
                                $displayrightblock= 'false';
                            }
                        
                       }
$moduleName = "Customers";
$text = "View all of your customers and their activities performed on your store.";
$rightblocktext = '<a title="Invite a Customer" class="addnewcustomer" style="cursor:pointer;"><img src="/images/admin/customers/invite_a_customer.png" /></a>'; 
break;

case 'customerdetail' :
$moduleName = "Customers";
$text = "View all of your customers and their activities performed on your store.";
$displayrightblock = false;
$rightblocktext = '<a href="" class="createNewOrderBtn" title="Create a new order">&nbsp;</a>'; 
break;

}
?>
<div class="subheaderContainer">
	<div class="floatLeft">
    	<div class="subheaderheadingGap">&nbsp;</div>
        <div class="subheaderheading"><?php echo $moduleName;  ?></div>
        <div class="subheadersubHeading"><?php echo $text; ?></div>
        <?php if($suggestiveText!=''){ echo $suggestiveText;}?>
    </div>
    <?php if($displayrightblock=='true'){  ?>	
        <div class="floatRight">
        	<div class="subheaderheadingGap">&nbsp;</div>
            <div class="clearBoth"><?php echo $rightblocktext;  ?></div>
        </div>
	<?php }  ?>
</div>
