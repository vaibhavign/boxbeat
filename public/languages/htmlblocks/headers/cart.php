<?php
switch($actionName){
case 'list' :
$moduleName = "Shopping Cart";
$text = "List of all items accumulated for purchase before proceeding for checkout.";
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
</div>
<!--<div id="headingSection"  class="stepContainer">
	<div class="heightGap30"></div>
    <div class="clearBoth">
        <div class="clearBoth ordersHeading "><?php echo $moduleName;  ?></div>
        <div class="heightGap">&nbsp;</div>
        <div class="orderDetails"><?php echo $text; ?></div>
    </div>
	<?php if($displayrightblock){  ?>	
        <div class="floatRight">
            <div class="clearBoth"><?php echo $rightblocktext;  ?></div>
        </div>
	<?php }  ?>
</div>
-->