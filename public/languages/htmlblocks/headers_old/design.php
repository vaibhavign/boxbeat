<?php
switch($actionName){
case 'designpanel' :
$moduleName = "Design";
$text = "Our design has fully professional & completely managed environment for creating & maintaining a beautiful store.";
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

