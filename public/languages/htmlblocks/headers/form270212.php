<?php
$displayrightblock = false;
switch ($actionName) {
	case 'assignlabel':
		$moduleName = "Create a new form";
		$text = "Forms enable you to obtain information from your customers,fast and easy.";
		$displayrightblock = true;
		$rightblocktext = '<div class="subheader_rightblock">
		<ul>
			<li class="bottomborder">
				<div class="subheader_container">
					<div class="subheader_leftcontent">fill form info</div> 
					<div class="subheader_rightcontent"><a href="/admin/form/#fillforminfo" class="modify_link" title="modify"><span class="edit_icon"></span>modify</a></div>
				</div>
			</li>
			<li class="bottomborder">
				<div class="subheader_container">
					<div class="subheader_activecontent">Assign labels</div> 
					<div class="subheader_rightcontent">now</div>
				</div>
			</li>
			<li>
				<div class="subheader_container">
					<div class="subheader_leftcontent">Apply form</div> 
					<div class="subheader_rightcontent">later</div>
				</div>
			</li>
		</ul>
	</div>';
		break;
		case 'fillforminfo':
		$moduleName = "Create a new form";
		$text = "Forms enable you to obtain information from your customers,fast and easy.";
		$displayrightblock = true;
		$rightblocktext = '<div class="subheader_rightblock">
		<ul>
			<li class="bottomborder">
				<div class="subheader_container">
					<div class="subheader_activecontent">Fill form Info</div> 
					<div class="subheader_activerightcontent">now</div>
				</div>
			</li>
			<li class="bottomborder">
				<div class="subheader_container">
					<div class="subheader_leftcontent">Assign labels</div> 
					<div class="subheader_rightcontent">next</div>
				</div>
			</li>
			<li>
				<div class="subheader_container">
					<div class="subheader_leftcontent">Apply form</div> 
					<div class="subheader_rightcontent">later</div>
				</div>
			</li>
		</ul>
	</div>';
		break;
            case 'applyform':
		$moduleName = "Create a new form";
		$text = "Forms enable you to obtain information from your customers,fast and easy.";
		$displayrightblock = true;		
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



<!--<div class="clearBoth">
        <div class="floatLeft">
                <div class="lh17">&nbsp;</div>
                <div class="floatLeft Cproduct_heading">Create a new form</div>
                <div class="lh5">&nbsp;</div>
                <div class="mainDescText">Forms enable you to obtain information from your customers,fast and easy.</div>
        </div>
        <div class="new_step floatRight">
                <ul>
                        <li>
                                <div class="wid285">
                                        <div class="floatLeft notnowHeading">Fill form Info</div>
                                        <div class="floatRight"><span class="modify_icon"></span><a href="#" class="modify_link">modify</a></div>
                                        <div class="bottomLine"></div>
                                        <div class="floatLeft nowHeading">Assign labels</div>
                                        <div class="floatRight notnow">now</div>
                                        <div class="bottomLine"></div>
                                        <div class="floatLeft notnowHeading">Apply form</div>
                                        <div class="floatRight notnow">later</div>
                                </div>
                        </li>
                </ul>
        </div>
</div>-->