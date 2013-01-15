<?php
$this->view->generalObj = new General();
$request = Zend_Controller_Front::getInstance()->getRequest();
$productid = $request->getParam('productid');
if($productid>0){
	$productid = 'productid/'.$productid;
	$mainHeadingText = 'Edit a product';
}else{
	$mainHeadingText = 'Create a new product';
	$productid = '';
}

$displayrightblock = true;
switch($actionName){
case 'manageproduct' :
$moduleName = "Products";
$text = "View all the products you are selling from your online store";
$rightblockGap = true;
$rightblockGapCode = '<div class="subheaderheadingGap">&nbsp;</div>';
if($this->view->generalObj->checkUserModuleActionPermission($_SESSION['USER']['userDetails'][0]['apikey'],$_SESSION['USER']['userDetails'][0]['user_email_address'],1,1)){ 
	$rightblocktext = '<div class="clearBoth"><a href="/admin/product/#add" title="Add Product" class="addProductLinkImage"></a></div>'; 
}
break;
case 'managedraft' :
$moduleName = "Products";
$text = "View all the products you are selling from your online store";
$rightblockGap = true;
$rightblockGapCode = '<div class="subheaderheadingGap">&nbsp;</div>';
break;

case 'add' :
$moduleName = "Create a new product";
$text = "List your stuff with in your store catalog to sell & earn! It' easy fast and safe!";
if($this->view->generalObj->checkUserModuleActionPermission($_SESSION['USER']['userDetails'][0]['apikey'],$_SESSION['USER']['userDetails'][0]['user_email_address'],1,1)){ 
	$rightblocktext = '<div class="subheader_rightblock">
		<ul>
			<li class="bottomborder">
				<div class="subheader_container">
					<div class="subheader_activecontent">Place your product</div> 
					<div class="subheader_activerightcontent">now</div>
				</div>
			</li>
			<li class="bottomborder">
				<div class="subheader_container">
					<div class="subheader_leftcontent">Fill product information</div> 
					<div class="subheader_rightcontent">next</div>
				</div>
			</li>
			<li>
				<div class="subheader_container">
					<div class="subheader_leftcontent">Review & publish</div> 
					<div class="subheader_rightcontent">later</div>
				</div>
			</li>
		</ul>
	</div>';
}
break;

case 'basicinfo':
case 'imagemanager':
case 'feature':
case 'optimize':
case 'variant':
case 'termsandpolicies':
	$moduleName = "Create a new product";
	$text = "List your stuff with in your store catalog to sell & earn! It' easy fast and safe!";
	if($this->view->generalObj->checkUserModuleActionPermission($_SESSION['USER']['userDetails'][0]['apikey'],$_SESSION['USER']['userDetails'][0]['user_email_address'],1,1)){ 
		$rightblocktext = '<div class="subheader_rightblock">
			<ul>
				<li class="bottomborder">
					<div class="subheader_container">
						<div class="subheader_leftcontent">Place your product</div> 
						<div class="subheader_rightcontent"><a href="/admin/product/#add/'.$productid.'" class="modify_link" title="modify"><span class="edit_icon"></span>modify</a></div>
					</div>
				</li>
				<li class="bottomborder">
					<div class="subheader_container">
						<div class="subheader_activecontent">Fill product information</div> 
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
case 'publish':
        $moduleName = "Create a new product";
		$text = "List your stuff with in your store catalog to sell & earn! It' easy fast and safe!";
		if($this->view->generalObj->checkUserModuleActionPermission($_SESSION['USER']['userDetails'][0]['apikey'],$_SESSION['USER']['userDetails'][0]['user_email_address'],1,1)){ 
			$rightblocktext = '<div class="subheader_rightblock">
				<ul>
					<li class="bottomborder">
						<div class="subheader_container">
							<div class="subheader_leftcontent">Place your product</div> 
							<div class="subheader_rightcontent"><a href="/admin/product/#add/'.$productid.'" class="modify_link" title="modify"><span class="edit_icon"></span>modify</a></div>
						</div>
					</li>
					<li class="bottomborder">
						<div class="subheader_container">
							<div class="subheader_leftcontent">Fill product information</div> 
							<div class="subheader_rightcontent"><a href="/admin/product/#basicinfo/'.$productid.'" class="modify_link" title="modify"><span class="edit_icon"></span>modify</a></div>
						</div>
					</li>
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
	case 'productdetail':
        $moduleName = "Product details";
		$text = "View detailed information about your product";
        break;
        case 'draftdetail':
        $moduleName = "Draft details";
		$text = "View detailed information about your draft";
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