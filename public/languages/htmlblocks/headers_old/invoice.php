<?php 
switch($actionName){
case 'view-invoice-details' :
$head = "Invoices";
$text = "It is a permanent record of the receipt of payment of an order.";
break;

case 'create-step-one' :
$head = "Invoices";
$text = "It is a permanent record of the receipt of payment of an order.";
break;

case 'my-invoice' :
$head = "Invoices";
$text = "It is a permanent record of the receipt of payment of an order.";
break;


}
				
?>
<div id="headingSection"  class="stepContainer">
	<div class="heightGap30"></div>
    <div class="clearBoth">
    	<div class="clearBoth ordersHeading"><?php echo $head;  ?></div>
    	<div class="heightGap">&nbsp;</div>
    	<div class="orderDetails"><?php echo $text; ?></div>
	</div>
</div>
