<?php
$displayrightblock = false;
switch ($actionName) {
	case 'getfeedback':
		$moduleName = "Get Satisfaction feedback";
		$text = "Goo2o empowers your store with Get Satisfaction, a top-class customer service application to allow your visitors to offer feedback, make suggestions &amp; get their questions answered";
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