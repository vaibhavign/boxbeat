<?php
switch($actionName){
case 'page' :
$moduleName = "Getting Started";
$text = "Welcome to your store management panel.";
$displayrightblock = false;
$rightblocktext = '<a href="#" class="createNewOrderBtn" title="Create a new order">&nbsp;</a>'; 
break;

}
?>
<div class="subheaderContainer">
	<div class="clearBoth">
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
    <div class="lh10">&nbsp;</div>
	<div class="borderSolid"></div>
</div>