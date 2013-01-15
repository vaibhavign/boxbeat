<?php

$request = Zend_Controller_Front::getInstance()->getRequest();
$brandid = $request->getParam('brandid');
if($brandid>0){
$mainHeadingText = 'Create a new brand';
$nextToHeadingText = "Fill up the details required to create a brand on your store.";
$displayButtonBlock = 'style="display:none;"';
$displayUlBlock = 'style="display:none;"';
$gapdivstyle = 'style="display:block;"';
}else{
$gapdivstyle = 'style="display:none;"';
$mainHeadingText = 'Create a new brand';
$nextToHeadingText = "Fill up the details required to create a brand on your store.";
$firstHeadingText = 'Fill brand information';
$secondHeadingText = 'Review &amp; publish';
$displayUlBlock = 'style="display:block;"';
$displayButtonBlock = 'style="display:none;"';

switch($actionName){

case 'brandbasicinfo':
case 'branduploadimage':
case 'brandoptimize':
$headingClassFirst = 'nowHeading';
$headingClassSecond = 'notnowHeading';
$nowClassFirst = 'now';
$nowClassSecond = 'notnow';
$nowtext = 'now';
$nextText = 'next';
break;

case 'brandpublish':
$headingClassFirst = 'notnowHeading';
$headingClassSecond = 'nowHeading';
$headingClassThird = 'nowHeading';
$nowClassFirst = '';
$nowClassSecond = 'now';
$nowClassThird = 'now';
$nowtext = '<div class="floatLeft"><a title="modify" class="modifyImg">&nbsp;</a></div><div class="floatLeft"><a href="/admin/addbrand/#brandbasicinfo" title="modify" class="modifyText">modify</a></div>';
$nextText = 'well done!';
$laterText = '&nbsp;well done!';
$style = 'style="padding-right:3px;"';
break;

case 'managebrands':
$mainHeadingText = 'Brands';
$nextToHeadingText = "Brands allow your customers to browse &amp; shop products of their favourite brands.";
$displayUlBlock = 'style="display:none;"';
$displayButtonBlock = 'style="display:block;"';
break;

case 'branddetails':
$mainHeadingText = 'Brands';
$nextToHeadingText = "Brands allow you to group products by similar attributes.";
$displayUlBlock = 'style="display:none;"';
$displayButtonBlock = 'style="display:none;"';
break;
}}
?>
<div class="main_header">
	<div class="floatLeft">
		<div class="floatLeft main_heading"><?php echo $mainHeadingText;?></div>
		<div class="lh5">&nbsp;</div>
		<div class="clearBoth main_heading_desc"><?php echo $nextToHeadingText;?></div>
	</div>
	<div class="floatRight" <?php echo $displayButtonBlock;?>>
		<div class="floatLeft"><a href="/admin/addbrand/#brandbasicinfo" title="Add Brand" class="addBrandLinkImage"></a></div>
    </div>
	<div class="floatRight" <?php echo $displayUlBlock;?>>
		<!--<div class="lh10" >&nbsp;</div>-->
		<div class="floatLeft new_step">
			<ul>
				<li>
					<div class="wid285">
						<div class="floatLeft <?php echo $headingClassFirst;?>"><?php echo $firstHeadingText;?></div>
						<div class="floatRight <?php echo $nowClassFirst;?>"><?php echo $nowtext;?></div>
						<div class="bottomLine"></div>
						<div class="floatLeft <?php echo $headingClassSecond;?>"><?php echo $secondHeadingText;?></div>
						<div class="floatRight <?php echo $nowClassSecond;?>"><?php echo $nextText;?></div>
						<!--<div class="bottomLine"></div>
						<div class="floatLeft <?php echo $headingClassThird?>"><?php echo $thirdHeadingText;?></div>
						<div class="floatRight <?php echo $nowClassThird.'"'.$style;?>"><?php echo $laterText;?></div>-->
					</div>
				</li>
			</ul>
		</div>
	</div>
</div>