<?php 
switch($actionName){
case 'requestforcancellation' :
$moduleName = "Requests";
$text = "Fill-in the details required to request a cancellation. This is only a request to cancel an item. An item will get cancelled only if a store owner approves the request.";
break;

case 'purchase-listing' :
$moduleName = "Purchases";
$text = "List of all items purchased by you from different stores.";
break;

case 'return-listing' :
$moduleName = "Returns";
$text = "List of all the item returned by you to different stores.";
break;

case 'shipment-details':
    $moduleName = 'Shipment Details';
    $text = "View complete shipment information.";
    
break;
case 'return-shipment-details':
    $moduleName = 'Return shipment details';
    $text = "View complete return shipment information.";
    
break;
case 'shipment-listing' :
$moduleName = "Shipments";
$text = "Shipments created for your purchases are shown below.";
break;

case 'confirmdelivery' :
$moduleName = "Shipment details";
$text = "View complete shipment information.";
break;

case 'return-item-details' :
$moduleName = "Return Item Details";
$text = "View your return order details";
break;

case 'purchase-details' :
$moduleName = "Purchase details";
$text = "View detail order information and item added in it. ";
break;

case 'create-shipment' :
$moduleName = "Create shipment";
$text = "Combine &amp; pack items to create shipment.";
break;

case 'edit-shipment' :
$moduleName = "Edit shipment";
$text = "Combine & pack items to create shipment.";
break;

case 'requestachangeinshippingaddress' :
$moduleName = "Requests";
$text = "This is only a request to change an existing shipping address. A shipping address can only be changed, if a store owner approves the request.";
break;

case 'return-request' :
$moduleName = "Requests";
$text = "Fill-in the details required to request a return. Please note that this is only a request to return a product. Please do not ship the item(s) till the seller approves the request.";
break;

case 'leave-a-feedback':
$moduleName = "Feedback";
$text = "Thank you for offering your feedback.";
break;

case 'edit-carrier-details':
$moduleName = "Edit Carrier Details";
$text = "Combine & pack items to create shipment.";
break;

case 'confirm-shipment':
$moduleName = "Confirm Shipment";
$text = "Confirm the shipment you have created";
break;

case 'feedback-listing':
$moduleName = "Feedbacks";
$text = "Confirm the shipment you have created";
break;

case 'buyer-purchase-list':
$moduleName = "Gift Certificates";
$text = "List of all gift certificates purchased by you are shown below.";
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
<!--
<div class="clearBoth stepContainer" id="headingSection">
	<div class="heightGap30"></div>
    <div class="clearBoth">
    	<div class="clearBoth ordersHeading"><?php echo $head;  ?></div>
    	<div class="heightGap">&nbsp;</div>
    	<div class="orderDetails"><?php echo $text; ?></div>
	</div>
</div>
-->