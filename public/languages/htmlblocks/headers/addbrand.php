<?php
$this->view->generalObj = new General();
$request = Zend_Controller_Front::getInstance()->getRequest();
$brandid = $request->getParam('brandid');

$displayrightblock = true;
switch($actionName){
case 'managebrands':
	$moduleName = "Brands";
	$text = "Brands allow your customers to browse &amp; shop products of their favourite brands.";
	$rightblockGap = true;
	$rightblockGapCode = '<div class="subheaderheadingGap">&nbsp;</div>';
	if($this->view->generalObj->checkUserModuleActionPermission($_SESSION['USER']['userDetails'][0]['apikey'],$_SESSION['USER']['userDetails'][0]['user_email_address'],3,1)){
		$rightblocktext = '<a href="/admin/addbrand/#brandbasicinfo" title="Add Brand" class="addBrandLinkImage"></a>'; 
	}
break;

case 'brandbasicinfo':
case 'branduploadimage':
case 'brandoptimize':
	$moduleName = is_numeric($brandid) ? 'Edit brand' : 'Create a new brand';
	$text = "Fill up the details required to create a brand on your store.";
	if($this->view->generalObj->checkUserModuleActionPermission($_SESSION['USER']['userDetails'][0]['apikey'],$_SESSION['USER']['userDetails'][0]['user_email_address'],3,1)){
		$rightblocktext = '<div class="subheader_rightblock">
		<ul>
			<li class="bottomborder">
				<div class="subheader_container">
					<div class="subheader_activecontent">Fill brand information</div> 
					<div class="subheader_activerightcontent">now</div>
				</div>
			</li>
			<li>
				<div class="subheader_container">
					<div class="subheader_leftcontent">Review & publish</div> 
					<div class="subheader_rightcontent">next</div>
				</div>
			</li>
		</ul>
	</div>';
	}
break;

case 'brandpublish':
	$moduleName = "Create a new brand";
	$text = "Fill up the details required to create a brand on your store.";
	if($this->view->generalObj->checkUserModuleActionPermission($_SESSION['USER']['userDetails'][0]['apikey'],$_SESSION['USER']['userDetails'][0]['user_email_address'],3,1)){
		$rightblocktext = '<div class="subheader_rightblock">
		<ul>
			<li class="bottomborder">
				<div class="subheader_container">
					<div class="subheader_leftcontent">Fill brand information</div> 
					<div class="subheader_activerightcontent"><a href="/admin/addbrand/#brandbasicinfo" class="modify_link" title="modify"><span class="edit_icon"></span>modify</a></div>
				</div>
			</li>
			<div class="bottomline"></div>
			<li>
				<div class="subheader_container">
					<div class="subheader_activecontent">Review & publish</div> 
					<div class="subheader_activerightcontent">well done!</div>
				</div>
			</li>
		</ul>
	</div>';
	}
break;

case 'branddetails':
	$moduleName = "Brands";
	$text = "Brands allow you to group products by similar attributes.";	
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