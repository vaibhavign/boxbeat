<?php
$generalObj = new General();
$request = Zend_Controller_Front::getInstance()->getRequest();
$featureId = $request->getParam('featureid');
$groupId = $request->getParam('groupid');

$displayrightblock = true;
switch ($actionName) {
case 'managefeatures':
	$moduleName = "Features";
	$text = "Features are properites of a product such as fabric, optical zoom, memory etc.";
	$rightblockGap = true;
	$rightblockGapCode = '<div class="subheaderheadingGap">&nbsp;</div>';
        if ($generalObj->checkUserModuleActionPermission($_SESSION['USER']['userDetails'][0]['apikey'], $_SESSION['USER']['userDetails'][0]['user_email_address'], 4, 1)) {
            $rightblocktext = '<a href="/admin/features/#featurebasicinfo" title="Add Feature" class="addfeaturesLinkImage"></a>';
        }
	break;

case 'featurebasicinfo':
	$moduleName = is_numeric($featureId) ? 'Edit feature' : 'Create a new feature';
	$text = "A feature represents a property of product such as its color,speed,model & so on.";
	$rightblockGap = false;
	$rightblocktext = '<div class="subheader_rightblock">
		<ul>
			<li class="bottomborder">
				<div class="subheader_container">
					<div class="subheader_activecontent">Fill feature information</div> 
					<div class="subheader_activerightcontent">now</div>
				</div>
			</li>
			<li>
				<div class="subheader_container">
					<div class="subheader_leftcontent">Add to groups</div> 
					<div class="subheader_rightcontent">next</div>
				</div>
			</li>
		</ul>
	</div>';
	break;

case 'addgroups':
	$moduleName = "Create a new feature";
	$text = "Fill information required to create a feature and add it to groups.";
	$rightblockGap = false;
	$rightblocktext = '<div class="subheader_rightblock">
		<ul>
			<li class="bottomborder">
				<div class="subheader_container">
					<div class="subheader_leftcontent">Fill feature information</div> 
					<div class="subheader_rightcontent"><a href="/admin/features/#featurebasicinfo/featureid/'.$featureId.'" class="modify_link" title="modify"><span class="edit_icon"></span>modify</a></div>
				</div>
			</li>
			<li>
				<div class="subheader_container">
					<div class="subheader_activecontent">Add to groups</div> 
					<div class="subheader_activerightcontent">now</div>
				</div>
			</li>
		</ul>
	</div>';
	break;
	
case 'setfeaturegroup':
	$moduleName = is_numeric($groupId) ? 'Edit feature group' : 'Create a new feature group';
	$text = "Organise the features by grouping them.";
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