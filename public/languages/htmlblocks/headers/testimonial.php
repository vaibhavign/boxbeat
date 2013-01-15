<?php
 $this->userName = new Zend_Session_Namespace('USER');
                        $this->generalObj= new General();
                        $Sapikey=$this->userName->stores[0]['store_apikey'];
                        $user_email_id=$this->userName->userDetails[0]['user_email_address'];
                        $storepermission=$this->generalObj->checkUserPermissions($Sapikey,$user_email_id,8);                     
                        if(count($storepermission)==0){
                                $displayaddtestimonial= 'no';
                         }
                        else if(count($storepermission)>0){ 
                            $storepermissionvalue=$this->generalObj->returnModuleAction($storepermission);
                            if(in_array('writeatestimonial',$storepermissionvalue))
                            {
                                $displayaddtestimonial= 'yes';
                            }
                            else{
                                $displayaddtestimonial= 'no';
                            }
                           
                       }         

$displayrightblock = true;
switch ($actionName) {
case 'managetestimonial':
	$moduleName = "Testimonial";
	$text = "Testimonials are the sayings of your customers about your products or services. Publish these praises at your store to let your visitors know you better.";
	$rightblockGap = true;
	$rightblockGapCode = '<div class="subheaderheadingGap">&nbsp;</div>';
	$rightblocktext = '<a href="/admin/testimonial/#addtestimonial" title="Add Testimonial"><img src="/images/admin/addtestimonials.png" /></a>';
	break;
	
case 'addtestimonial':
	$moduleName = "Testimonial";
	$text = "Testimonials are the sayings of your customers about your products or services. Publish these praises at your store to let your visitors know you better.";
	$displayrightblock = false;
	$rightblocktext = '<a href="/admin/testimonial/#addtestimonial" title="Add Testimonial"><img src="/images/admin/addtestimonials.png" /></a>';
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
       <?php if($displayaddtestimonial=='yes') {?>   <div class="clearBoth">
            	<?php echo $rightblocktext;?>
            </div> <?php } ?>
        </div> 			
	<?php } ?>
</div>
