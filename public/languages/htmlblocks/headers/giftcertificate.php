<?php
switch($actionName){
case 'manage-purchased-gc' :
$moduleName = "Gift certificate";
$text = "It is an e-card which is presented as a gift to someone. It is purchased by a buyer's choice of amount which he can send to anybody. The recipient then can buy anything within that amount from your store";
$displayrightblock = true;
$rightblockGap = true;
$rightblockGapCode = '<div class="subheaderheadingGap">&nbsp;</div>';
$rightblocktext = '<a href="/admin/giftcertificate/#managegc"><img src="/images/admin/gift_certificate/manage_gc.png" alt="Manage Gift Certificates" title="Manage Gift Certificates" /></a>'; 
break;

case 'managegc' :
$moduleName = "Gift certificate";
$text = "It is an e-card which is presented as a gift to someone. It is purchased by a buyer's choice of amount which he can send to anybody. The recipient then can buy anything within that amount from your store";
$displayrightblock = false;
break;
}
?>
<div class="subheaderContainer">
	<div class="floatLeft">
    	<div class="subheaderheadingGap">&nbsp;</div>
        <div class="subheaderheading"><?php echo $moduleName;  ?></div>
        <div class="subheadersubHeading"><?php echo $text; ?></div>
    </div>
    <?php if($displayrightblock){  ?>	
		<div class="floatRight">
        	<?php if($rightblockGap) echo $rightblockGapCode; ?>
            <div class="clearBoth">
            	<?php echo $rightblocktext;?>
            </div>
        </div> 			
	<?php } ?>
</div>