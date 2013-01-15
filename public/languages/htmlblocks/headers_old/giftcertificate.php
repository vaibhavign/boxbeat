<?php
switch($actionName){
case 'manage-purchased-gc' :
$moduleName = "Gift certificate";
$text = "It is an e-card which is presented as a gift to someone. It is purchased by a buyer's choice of amount which he can send to anybody. The recipient then can buy anything within that amount from your store";
$displayrightblock = true;
$rightblocktext = '<a href="/admin/giftcertificate/#managegc"><img src="/images/admin/gift_certificate/manage_gc.png" alt="Manage Gift Certificates" title="Manage Gift Certificates" /></a>'; 
break;

case 'managegc' :
$moduleName = "Gift certificate";
$text = "It is an e-card which is presented as a gift to someone. It is purchased by a buyer's choice of amount which he can send to anybody. The recipient then can buy anything within that amount from your store";
$displayrightblock = false;
break;
}
?>
<div id="headingSection" class="stepContainer">
	<div class="heightGap30"></div>
    <div class="clearBoth">
        <div class="floatLeft">
            <div class="clearBoth ordersHeading"><?php echo $moduleName;  ?></div>
            <div class="heightGap">&nbsp;</div>
            <div class="floatLeft orderDetails <?php echo($displayrightblock)?'editSignatureLeftPanel':'';?>"><?php echo $text; ?></div>
        </div>
        <?php if($displayrightblock){  ?>	
        <div class="floatRight">
            <div class="clearBoth"><?php echo $rightblocktext;  ?></div>
        </div>
		<?php } ?>
    </div>
</div>