<?php 
switch($actionName){
case 'requestforcancellation' :
$head = "Requests";
$text = "Fill-in the details required to request a cancellation. This is only a request to cancel an item. An item will get cancelled only if a store owner approves the request.";
break;

case 'purchase-listing' :
$head = "Purchases";
$text = "List of all items purchased by you from different stores.";
break;

case 'return-listing' :
$head = "Returns";
$text = "List of all the item returned by you to different stores.";
break;

case 'shipment-listing' :
$head = "Shipments";
$text = "Shipments created for your purchases are shown below.";
break;

case 'confirmdelivery' :
$head = "Shipment details";
$text = "View complete shipment information.";
break;

case 'return-item-details' :
$head = "Return Item Details";
$text = "View your return order details";
break;

case 'purchase-details' :
$head = "Purchase details";
$text = "View detail order information and item added in it. ";
break;

case 'create-shipment' :
$head = "Create shipment";
$text = "Combine &amp; pack items to create shipment.";
break;

case 'edit-shipment' :
$head = "Edit shipment";
$text = "Combine & pack items to create shipment.";
break;

case 'requestachangeinshippingaddress' :
$head = "Requests";
$text = "Fill-in the details required to request a change in shipping address. This is only a request to change an existing shipping address. A shipping address can only be changed, if a store owner approves the request.";
break;

case 'return-request' :
$head = "Requests";
$text = "Fill-in the details required to request a return. Please note that this is only a request to return a product. Please do not ship the item(s) till the seller approves the request.";
break;

case 'leave-a-feedback':
$head = "Feedback";
$text = "Thank you for offering your feedback.";
break;

case 'edit-carrier-details':
$head = "Edit Carrier Details";
$text = "Combine & pack items to create shipment.";
break;

case 'confirm-shipment':
$head = "Confirm Shipment";
$text = "Confirm the shipment you have created";
break;

case 'feedback-listing':
$head = "Feedbacks";
$text = "List of all feedbacks given by you for different store owners.";
break;

case 'buyer-purchase-list':
$head = "Gift Certificates";
$text = "List of all gift certificates purchased by you are shown below.";
break;

}
				
?>
<div class="clearBoth stepContainer" id="headingSection">
	<div class="heightGap30"></div>
    <div class="clearBoth">
    	<div class="clearBoth ordersHeading"><?php echo $head;  ?></div>
    	<div class="heightGap">&nbsp;</div>
    	<div class="orderDetails"><?php echo $text; ?></div>
	</div>
</div>
