<?php
switch($actionName){
case 'designpanel' :
$moduleName = "Design";
$text = "Our design has fully professional & completely managed environment for creating & maintaining a beautiful store.";
$displayrightblock = false;
break;

case 'pages-list' :
$moduleName = "Pages";
$text = "Pages are usually used to give users additional information other than the products you offer. For example an 'About Us' or a 'Contact Us' page.";
$displayrightblock = true;
$rightblocktext = '<div class="clearBoth"><a id="add_page_link" class="add_page cursor_pointer"><img title="Add a page" alt="Add a page" src="/images/admin/add_a_page.gif"></a></div>'; 
break;

case 'create-page' :
$moduleName = "Pages";
$text = "Pages are used to display content that doesn't change often. For example an 'About Us' or a 'Contact Us' page.";
$displayrightblock = false;
//$rightblocktext = '<div class="clearBoth"><a id="add_page_link" class="add_page cursor_pointer"><img title="Add a page" alt="Add a page" src="/images/admin/add_a_page.gif"></a></div>'; 
break;

case 'defaultappearance' :
$moduleName = "Pages";
$text = "Pages are usually used to give users additional information other than the products you offer. For example an 'About Us' or a 'Contact Us' page.";
$displayrightblock = true;
$rightblocktext = '<div class="clearBoth"><a id="add_page_link" class="add_page cursor_pointer"><img src="/images/admin/add_a_page.gif"  alt="Add a page" title="Add a page" /></a></div>'; 
break;

case 'banners-list' :
$moduleName = "Banner";
$text = "Banners allow you to add promotional links and images throughout your store to advertise special deals and discounts to your shoppers.";
$displayrightblock = true;
$rightblocktext = '<div class="clearBoth"><a href="/admin/design/#create-banner/"><img src="/images/admin/themecustomize/add_a_banner.gif"  alt="Add a banner" title="Add a banner" /></a></div>'; 
break;

case 'create-banner' :
$moduleName = "Banner";
$text = "Banners allow you to add promotional links and images throughout your store to advertise special deals and discounts to your shoppers.";
$displayrightblock = false;
//$rightblocktext = '<div class="clearBoth"><a href="/admin/design/#create-banner/"><img src="/images/admin/themecustomize/add_a_banner.gif"  alt="Add a banner" title="Add a banner" /></a></div>'; 
break;

case 'my-templates' :
$moduleName = "My Templates";
$text = "Templates are your store design preferences. Create, modify and save templates for flexible usage.";
$displayrightblock = false;
//$rightblocktext = '<div class="clearBoth"><a href="/admin/design/#create-banner/"><img src="/images/admin/themecustomize/add_a_banner.gif"  alt="Add a banner" title="Add a banner" /></a></div>'; 
break;

}
?>
<div class="subheaderContainer">
	<div class="floatLeft">
    	<div class="subheaderheadingGap">&nbsp;</div>
        <div class="subheaderheading"><?php echo $moduleName;  ?></div>
        <div class="subheadersubHeading"><?php echo $text; ?></div>
        <?php if($suggestiveText!=''){ echo $suggestiveText;}?>
    </div>
    <?php if($displayrightblock){  ?>	
        <div class="floatRight">
        	<div class="subheaderheadingGap">&nbsp;</div>
            <div class="clearBoth"><?php echo $rightblocktext;  ?></div>
        </div>
	<?php }  ?>
</div>