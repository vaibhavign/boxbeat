<?php
switch($actionName){
case 'addtestimonial':
$displaytype = 'style="display:none;"';
break;
case 'managetestimonial':
$displaytype = 'style="display:block;"';
break; 
}
?>

<div class="clearBoth stepContainer">
	<div class="width688">
		<div class="floatLeft testimonials_heading">Testimonial</div>
		<div class="heightGap">&nbsp;</div>
		<div class="floatLeft orderDetails">Testimonials are the sayings of your customers about your products or services. 
		Publish these praises at your store to let your visitors know you better.</div>
	</div>
	<div class="floatRight paddTop30" <?php echo $displaytype;?>><a href="/admin/testimonial/#addtestimonial" title="Add Testimonial"><img src="/images/admin/addtestimonials.png" /></a>
	</div>    
</div>