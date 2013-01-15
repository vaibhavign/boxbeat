<?php
switch($actionName){
    
case 'return-shipment-details':
$moduleName = "Return Shipment Details";
 $text = "View detailed information of the return shipment created from your store.";
$displayrightblock = false;
break;
case 'list-order' :
$moduleName = "Orders";
$text = "Orders help you to view and manage the products purchased from your store.";
$displayrightblock = false;
$rightblocktext = '<a href="" class="createNewOrderBtn" title="Create a new order">&nbsp;</a>'; 
break;

case 'orderdetail':
$moduleName = "Orders";
$text = "View detailed information of the items included in this particular order.";
$displayrightblock = false;
break;

case 'return-item-details':
$moduleName = "Return Item Details";
$text = "View details of the items to be returned.";
$displayrightblock = false;
break;

case 'list-shipments':
$moduleName = "Shipments";
$text = "Shipments created for the orders placed on your store are shown below.";
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
break;

case 'shipment-details':
    $moduleName = 'Shipment Details';
    $text = "View detailed information of the shipment created from your store.";
    
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
<div class="subheaderContainer">
	<div class="floatLeft">
    	<div class="subheaderheadingGap">&nbsp;</div>
        <div class="subheaderheading"><?php echo $moduleName;  ?></div>
        <div class="subheadersubHeading"><?php echo $text; ?></div>
    </div>
    <?php if($displayrightblock){  ?>	
        <div class="floatRight">
        	<div class="subheaderheadingGap">&nbsp;</div>
            <div class="clearBoth"><?php echo $rightblocktext;  ?></div>
        </div>
	<?php }  ?>
</div>