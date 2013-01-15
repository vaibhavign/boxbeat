<?php 
switch($actionName){
case 'view-invoice-details' :
$moduleName = "Invoices";
$text = "It is a permanent record of the receipt of payment of an order.";
break;

case 'create-step-one' :
$moduleName = "Invoices";
$text = "It is a permanent record of the receipt of payment of an order.";
break;

case 'create-step-two' :
$moduleName = "Invoices";
$text = "It is a permanent record of the receipt of payment of an order.";
break;

case 'my-invoice' :
$moduleName = "Invoices";
$text = "It is a permanent record of the receipt of payment of an order.";
break;

case 'manage-invoice' :
$moduleName = "Invoices";
$text = "It is a permanent record of the receipt of payment of an order.";
$displayrightblock = true;
$rightblockGap = true;
$rightblockGapCode = '<div class="subheaderheadingGap">&nbsp;</div>';
$rightblocktext = '<a href="#create-step-one/no-cache/'. rand(599, 2999) .'" title="Add Invoice"><img src="/images/admin/invoice/add_invoice_btn.png" /></a>';
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