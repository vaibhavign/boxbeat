<?php
switch($actionName){
case 'promotepanel' :
$moduleName = "Promote";
$text = "Use powerful ecommerce promotional tools to promote your store in a better way";
$displayrightblock = false;
break;
}
?>
<div id="headingSection"  class="stepContainer">
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

