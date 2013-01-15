<?php
$generalObj = new General();
//$url_arr = explode('/', $_SERVER['REQUEST_URI']);
//$id = is_numeric($url_arr[5]) ? $url_arr[5] : 0;
$displayrightblock = true;
switch ($actionName) {
	case 'managediscountcoupon':
		$moduleName = "Discount coupons";
		$text = "Discount coupons are used for promotional purposes. It allows you to provide discounts on products available for purchase at your store.";
		$rightblockGap = true;
		$rightblockGapCode = '<div class="subheaderheadingGap">&nbsp;</div>';
		if ($generalObj->checkUserModuleActionPermission($_SESSION['USER']['userDetails'][0]['apikey'], $_SESSION['USER']['userDetails'][0]['user_email_address'], 10, 24)) {
			$rightblocktext = '<a href="/admin/coupons/#creatediscountcoupons" class="add_coupon" title="Add a Coupon">&nbsp;</a>';
		}
		break;
	case 'creatediscountcoupons':
		$moduleName = "Discount coupons";
		$text = "Discount coupons are used for promotional purposes. It allows you to provide discounts on products available for purchase at your store.";
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
