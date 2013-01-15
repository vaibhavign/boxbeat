<?php
$this->view->generalObj = new General();
$request = Zend_Controller_Front::getInstance()->getRequest();
$catid = $request->getParam('catid');
$linkvalue='';
if($catid>0){
	$linkvalue="/catid/".$catid;
}
//echo $linkvalue;
$displayrightblock = true;
switch($actionName){

case 'managecategory':
	$moduleName = 'Category';
	$text = "Categories allow you to group products by similar attributes.";
	$rightblockGap = true;
	$rightblockGapCode = '<div class="subheaderheadingGap">&nbsp;</div>';
	if($this->view->generalObj->checkUserModuleActionPermission($_SESSION['USER']['userDetails'][0]['apikey'],$_SESSION['USER']['userDetails'][0]['user_email_address'],2,1)){
		$rightblocktext = '<a href="/admin/addcategory/#placecategory" title="Add Category" class="addCateoryLinkImage"></a>'; 
	}
break;

case 'placecategory':
	$moduleName = 'Create a new category';
	$text = "It will help you to group similar products and organize your store catalogue.";
	$rightblockGap = false;
	$rightblockGapCode = '<div class="subheaderheadingGap">&nbsp;</div>';
	if($this->view->generalObj->checkUserModuleActionPermission($_SESSION['USER']['userDetails'][0]['apikey'],$_SESSION['USER']['userDetails'][0]['user_email_address'],2,1)){
		$rightblocktext = '<div class="subheader_rightblock">
		<ul>
			<li class="bottomborder">
				<div class="subheader_container">
					<div class="subheader_activecontent">Place your category</div> 
					<div class="subheader_activerightcontent">now</div>
				</div>
			</li>
			<li class="bottomborder">
				<div class="subheader_container">
					<div class="subheader_leftcontent">Fill category information</div> 
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
case 'categorybasicinfo':
case 'categoryimagemanager':
case 'categoryoptimize':
	$moduleName = 'Create a new category';
	$text = "It will help you to group similar products and organize your store catalogue.";
	$rightblockGap = false;
	$rightblockGapCode = '<div class="subheaderheadingGap">&nbsp;</div>';
	if($this->view->generalObj->checkUserModuleActionPermission($_SESSION['USER']['userDetails'][0]['apikey'],$_SESSION['USER']['userDetails'][0]['user_email_address'],2,1)){
		$rightblocktext = '<div class="subheader_rightblock">
		<ul>
			<li>
				<div class="subheader_container">
					<div class="subheader_leftcontent">Place your category</div> 
					<div class="subheader_rightcontent"><a href="/admin/addcategory/#placecategory'.$linkvalue.'" class="modify_link" title="modify"><span class="edit_icon"></span>modify</a></div>
				</div>
			</li>
			<div class="bottomline"></div>
			<li>
				<div class="subheader_container">
					<div class="subheader_activecontent">Fill category information</div> 
					<div class="subheader_activerightcontent">now</div>
				</div>
			</li>
			<div class="bottomline"></div>
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
case 'categorybasicinfo':
case 'categoryimagemanager':
case 'categoryoptimize':
	$moduleName = "Create a new category";
	$text = "It will help you to group similar products and organize your store catalogue.";
	if($this->view->generalObj->checkUserModuleActionPermission($_SESSION['USER']['userDetails'][0]['apikey'],$_SESSION['USER']['userDetails'][0]['user_email_address'],2,1)){
		$rightblocktext = '<div class="subheader_rightblock">
			<ul>
				<!--<li>
					<div class="subheader_container">
						<div class="subheader_activecontent">Place your category</div> 
						<div class="subheader_activerightcontent">now</div>
					</div>
				</li>
				<div class="bottomline"></div>-->
				<li>
					<div class="subheader_container">
						<div class="subheader_leftcontent">Place your category</div> 
						<div class="subheader_rightcontent"><a href="/admin/addcategory/#placecategory'.$linkvalue.'" class="modify_link" title="modify"><span class="edit_icon"></span>modify</a></div>
					</div>
				</li>
				<div class="bottomline"></div>
				<li>
					<div class="subheader_container">
						<div class="subheader_activecontent">Fill category information</div> 
						<div class="subheader_activerightcontent">now</div>
					</div>
				</li>
				<div class="bottomline"></div>
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
case 'categorypublish':
	$moduleName = "Create a new category";
	$text = "It will help you to group similar products and organize your store catalogue.";
	if($this->view->generalObj->checkUserModuleActionPermission($_SESSION['USER']['userDetails'][0]['apikey'],$_SESSION['USER']['userDetails'][0]['user_email_address'],2,1)){
		$rightblocktext = '<div class="subheader_rightblock">
			<ul>
				<li>
					<div class="subheader_container">
						<div class="subheader_leftcontent">Place your category</div> 
						<div class="subheader_rightcontent"><a href="/admin/addcategory/#placecategory'.$linkvalue.'" class="modify_link" title="modify"><span class="edit_icon"></span>modify</a></div>
					</div>
				</li>
				<div class="bottomline"></div>
				<li>
					<div class="subheader_container">
						<div class="subheader_leftcontent">Fill category information</div> 
						<div class="subheader_rightcontent"><a href="/admin/addcategory/#categorybasicinfo'.$linkvalue.'" class="modify_link" title="modify"><span class="edit_icon"></span>modify</a></div>
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
case 'categorydetails':
	$moduleName = "Category";
	$text = "Categories allow you to group products by similar attributes.";
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