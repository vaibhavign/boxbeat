<?php
switch($actionName){
case 'wishlist-detail' :
$moduleName = "Wishlist";
$text = "List of all items you wish to purchase from various Goo2o stores.";
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
<!--<div id="headingSection" class="stepContainer">
	<div class="heightGap30"></div>
    <div class="clearBoth">
        <div class="floatLeft">
            <div class="clearBoth ordersHeading"><?php //echo $moduleName;  ?></div>
            <div class="heightGap">&nbsp;</div>
            <div class="floatLeft orderDetails <?php //echo($displayrightblock)?'editSignatureLeftPanel':'';?>"><?php //echo $text; ?></div>
        </div>
        <?php //if($displayrightblock){  ?>	
        <div class="floatRight">
            <div class="clearBoth"><?php //echo $rightblocktext;  ?></div>
        </div>
		<?php //} ?>
    </div>
</div>-->