<?php
switch($actionName){
case 'list-order' :
$moduleName = "Orders";
$text = "List of all orders placed from your store.";
$displayrightblock = false;
$rightblocktext = '<a href="#" class="createNewOrderBtn" title="Create a new order">&nbsp;</a>'; 
break;

case 'orderdetail':
$moduleName = "Orders";
$text = "View detail order information and item added in it.";
$displayrightblock = false;
break;

case 'list-shipments':
$moduleName = "Shipments";
$text = "Shipments created from your orders are shown below.";
$displayrightblock = false;
break;

case 'confirmshipment':
$moduleName = "Shipment details";
$text = "View complete shipment information.";
$displayrightblock = false;
break;

case 'returns':
$moduleName = "Returns";
$text = "List of all the item returned by the buyers.";
$displayrightblock = false;
break;

case 'createshipment':
    case 'create-shipment':
$moduleName = "Create shipment";
$text = "Combine &amp; pack items to create shipment.";
$displayrightblock = false;
break;

case 'editshipment':
    case 'edit-shipment':
$moduleName = "Edit shipment";
$text = "Combine &amp; pack items to create shipment.";
$displayrightblock = false;
break;
case 'editcarrierdetails':
    
$moduleName = "Edit Carrier Details";
$text = "View complete shipment information.";
$displayrightblock = false;
case 'shipment-details':
    $moduleName = 'Shipment Details';
    $text = "View complete shipment information.";
    
break;

case 'confirmdelivery':
$moduleName = "Shipment details";
$text = "View complete shipment information.";
$displayrightblock = false;
break;

case 'feedback-listing':
$moduleName = "Feedbacks";
$text = "Feedbacks allows you to know the experiences of customers while shopping from your store.";
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

