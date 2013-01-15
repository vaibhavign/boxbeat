<?php
switch($actionName){
case 'page' :
$moduleName = "Getting Started";
$text = "Welcome to your store management panel.";
$displayrightblock = false;
$rightblocktext = '<a href="#" class="createNewOrderBtn" title="Create a new order">&nbsp;</a>'; 
break;

}
?>


	<div  class="landing_main_header">
	<div class="landingpage_heading"><?php echo $moduleName;  ?></div>
    <div class="lh5">&nbsp;</div>
    <div class="landingpage_desc"><?php echo $text; ?></div>
    <div class="lh10">&nbsp;</div>
   	<div class="borderSolid"></div>	
    <div class="lh30">&nbsp;</div>
 	</div>
    

