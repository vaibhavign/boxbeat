<?php

$request = Zend_Controller_Front::getInstance()->getRequest();
$catid = $request->getParam('catid');
if($catid>0){
$mainHeadingText = 'Create a new category';
$nextToHeadingText = "It will help you to group similar products and organize your store catalogue.";
$displayButtonBlock = 'style="display:none;"';
$displayUlBlock = 'style="display:none;"';
$gapdivstyle = 'style="display:block;"';
}else{
$gapdivstyle = 'style="display:none;"';
$mainHeadingText = 'Create a new category';
$nextToHeadingText = "It will help you to group similar products and organize your store catalogue.";
$firstHeadingText = 'Place your category';
$secondHeadingText = 'Fill category information';
$thirdHeadingText = 'Review &amp; publish';
$displayUlBlock = 'style="display:block;"';
$displayButtonBlock = 'style="display:none;"';

switch($actionName){
case 'placecategory':
$headingClassFirst = 'nowHeading';
$headingClassSecond = 'notnowHeading';
$headingClassThird = 'notnowHeading';
$nowClassFirst = 'now';
$nowClassSecond = 'notnow';
$nowClassThird = 'notnow';
$nowtext = 'now';
$nextText = 'next';
$laterText = 'later';
break;

case 'categorybasicinfo':
case 'categoryimagemanager':
case 'categoryoptimize':
$headingClassFirst = 'notnowHeading';
$headingClassSecond = 'nowHeading';
$headingClassThird = 'notnowHeading';
$nowClassFirst = '';
$nowClassSecond = 'now';
$nowClassThird = 'notnow';
$nowtext = '<div class="floatLeft"><a title="modify" class="modifyImg">&nbsp;</a></div><div class="floatLeft"><a href="/admin/addcategory/#placecategory" title="modify" class="modifyText">modify</a></div>';
$nextText = 'now';
$laterText = 'next';
break;

case 'categorypublish':
$headingClassFirst = 'notnowHeading';
$headingClassSecond = 'notnowHeading';
$headingClassThird = 'nowHeading';
$nowClassFirst = '';
$nowClassSecond = '';
$nowClassThird = 'now';
$nowtext = '<div class="floatLeft"><a title="modify" class="modifyImg">&nbsp;</a></div><div class="floatLeft"><a href="/admin/addcategory/#placecategory" title="modify" class="modifyText">modify</a></div>';
$nextText = '<div class="floatLeft"><a title="modify" class="modifyImg">&nbsp;</a></div><div class="floatLeft"><a href="/admin/addcategory/#categorybasicinfo" title="modify" class="modifyText">modify</a></div>';
$laterText = '&nbsp;well done!';
$style = 'style="padding-right:3px;"';
break;

case 'managecategory':
$mainHeadingText = 'Category';
$nextToHeadingText = "Categories allow you to group products by similar attributes.";
$displayUlBlock = 'style="display:none;"';
$displayButtonBlock = 'style="display:block;"';
break;

case 'categorydetails':
$mainHeadingText = 'Category';
$nextToHeadingText = "Categories allow you to group products by similar attributes.";
$displayUlBlock = 'style="display:none;"';
$displayButtonBlock = 'style="display:none;"';
break;
}}
?>
<div class="main_header" style="height:124px;">
	<div class="heading_left">
		<div class="" style="height:32px; line-height:32px; clear:both;">&nbsp;</div>
		<div class="floatLeft Cproduct_heading"><?php echo $mainHeadingText;?></div>
		<div class="lh5">&nbsp;</div>
		<div class="clearBoth Cproduct_desc"><?php echo $nextToHeadingText;?></div>
	</div>
	<div class="lh17" <?php echo $gapdivstyle;?>>&nbsp;</div>
	<div class="floatRight" <?php echo $displayButtonBlock;?>>
		<div class="" style="height:32px; line-height:32px; clear:both;">&nbsp;</div>
		<div class="floatLeft"><a href="/admin/addcategory/#placecategory" title="Add Category" class="addCateoryLinkImage"></a></div>
    </div>
	<div class="floatRight" <?php echo $displayUlBlock;?>>
		<div class="" style="height:10px; line-height:10px; clear:both; font-size:0px;">&nbsp;</div>
		<div class="floatLeft new_step">
			<ul>
				<li>
					<div class="wid285">
						<div class="floatLeft <?php echo $headingClassFirst;?>"><?php echo $firstHeadingText;?></div>
						<div class="floatRight <?php echo $nowClassFirst;?>"><?php echo $nowtext;?></div>
						<div class="bottomLine"></div>
						<div class="floatLeft <?php echo $headingClassSecond;?>"><?php echo $secondHeadingText;?></div>
						<div class="floatRight <?php echo $nowClassSecond;?>"><?php echo $nextText;?></div>
						<div class="bottomLine"></div>
						<div class="floatLeft <?php echo $headingClassThird?>"><?php echo $thirdHeadingText;?></div>
						<div class="floatRight <?php echo $nowClassThird.'"'.$style;?>"><?php echo $laterText;?></div>
					</div>
				</li>
			</ul>
		</div>
	</div>
</div>