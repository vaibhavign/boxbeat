<?php
$generalObj = new General();
$url_arr = explode('/', $_SERVER['REQUEST_URI']);
$id = is_numeric($url_arr[5]) ? $url_arr[5] : 0;
$displayrightblock = true;
switch ($actionName) {
	case 'managevariations':
		$moduleName = "Variations";
		$text = "Variations allow you to create multiple variants of a product with different sizes, color etc.";
		$rightblockGap = true;
		$rightblockGapCode = '<div class="subheaderheadingGap">&nbsp;</div>';
		if ($generalObj->checkUserModuleActionPermission($_SESSION['USER']['userDetails'][0]['apikey'], $_SESSION['USER']['userDetails'][0]['user_email_address'], 5, 1)) {
			$rightblocktext = '<a class="addVariationLinkImage" title="Add Variation" href="/admin/variation/#createvariation"></a>';
		}
		break;
	
	case 'createvariation':
		//echo $id ? "Edit variation" : "Create a new variation";
		$moduleName = $id ? "Edit variation" : "Create a new variation";
		$text = $id ? "Edit your variation and add it to groups" : "Fill information required to create a variation and add it to groups";
		$rightblockGap = false;
		$rightblocktext = '<div class="subheader_rightblock">
			<ul>
				<li class="bottomborder">
					<div class="subheader_container">
						<div class="subheader_activecontent">Fill variation information</div> 
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
		
	case 'addtogroup':
		$moduleName = "Create a new variation";
		$text = "Fill information required to create a variation and add it to groups";
		$rightblockGap = false;
		$rightblocktext = '<div class="subheader_rightblock">
			<ul>
				<li class="bottomborder">
					<div class="subheader_container">
						<div class="subheader_leftcontent">Fill variation information</div> 
						<div class="subheader_activerightcontent"><a href="/admin/variation/#createvariation/variationid/'.$id.'" class="modify_link" title="modify"><span class="edit_icon"></span>modify</a></div>
					</div>
				</li>
				<li>
					<div class="subheader_container">
						<div class="subheader_activecontent">Add to groups</div> 
						<div class="subheader_activerightcontent">next</div>
					</div>
				</li>
			</ul>
		</div>';
		break;
	
	case 'createvariationgroup':
		$moduleName = $id ? "Edit variation group" : "Create a new variation group";
		$text = "Search and select the variations you wish to group.";
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