<?php
$displayrightblock = true;
switch ($actionName) {
case 'listreviews':
case 'updatereview':
	$moduleName = "Reviews";
	$text = "Review defines the trustworthy information given by the users, which is helpful for other site users to make their buying decision.";
	$rightblockGap = false;
	$rightblockGapCode = '<div class="subheaderheadingGap">&nbsp;</div>';
	$rightblocktext = '<a href="/admin/features/#featurebasicinfo" title="Add Feature" class="addfeaturesLinkImage"></a>';
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