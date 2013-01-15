<?php
switch($actionName){
case 'wishlist-detail' :
$moduleName = "Wishlist";
$text = "List of all items you wish to purchase from various Goo2o stores.";
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