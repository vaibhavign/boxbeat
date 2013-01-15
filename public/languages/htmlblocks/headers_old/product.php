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
$nextToHeadingText = "List your stuff with in your store catalog to sell &amp; earn! It&rsquo; easy fast and safe!";
$firstHeadingText = 'Place your product';
$secondHeadingText = 'Fill product information';
$thirdHeadingText = 'Review &amp; publish';
switch ($actionName) {
    case 'add':
        $headingClassFirst = 'nowHeading';
        $headingClassSecond = 'notnowHeading';
        $headingClassThird = 'notnowHeading';
        $nowClassFirst = 'now';
        $nowClassSecond = 'notnow';
        $nowClassThird = 'notnow';
        $nowtext = 'now';
        $nextText = 'next';
        $laterText = 'later';
		$displayButtonBlock = 'style="display:none;"';
        break;

    case 'basicinfo':
    case 'imagemanager':
    case 'feature':
    case 'optimize':
    case 'variant':
    case 'termsandpolicies':
        $nowtext = '<div class="floatRight"><a href="/admin/product/#add/'.$productid.'" class="modify_link" title="modify"><span class="modify_icon"></span>modify</a></div>';
        $nextText = 'now';
        $laterText = 'next';
        $headingClassFirst = 'notnowHeading';
        $headingClassSecond = 'nowHeading';
        $headingClassThird = 'notnowHeading';
        $nowClassFirst = '';
        $nowClassSecond = 'now';
        $nowClassThird = 'notnow';
		$displayButtonBlock = 'style="display:none;"';
        break;

    case 'publish':
        $nextToHeadingText = "List your stuff in your store catalog to sell &amp; earn! It&rsquo;s easy, fast and safe! ";
        $headingClassFirst = 'notnowHeading';
        $headingClassSecond = 'notnowHeading';
        $headingClassThird = 'nowHeading';
        $nowClassFirst = '';
        $nowClassSecond = '';
        $nowClassThird = 'now';
        $nowtext = '<div class="floatRight"><a href="/admin/product/#add/'.$productid.'" class="modify_link" title="modify"><span class="modify_icon"></span>modify</a></div>';
        $nextText = '<div class="floatRight"><a href="/admin/product/#basicinfo/'.$productid.'" class="modify_link" title="modify"><span class="modify_icon"></span>modify</a></div>';
        $laterText = 'well done!';
		$displayButtonBlock = 'style="display:none;"';
        break;

    case 'productdetail':
        $mainHeadingText = 'Product details';
        $nextToHeadingText = 'View detailed information about your product';
        $displayRightBlock = 'style="display:none;"';
		$displayButtonBlock = 'style="display:none;"';
        $gapdiv = 'lh30';
        break;
		
	case 'manageproduct':
		$mainHeadingText = 'Products';
        $nextToHeadingText = 'View all the products you are selling from your online store';
		$displayRightBlock = 'style="display:none;"';
		$displayButtonBlock = 'style="display:block;"';
		break;
}
?>
<div class="main_header">
    <div class="floatLeft">
        <div class="floatLeft main_heading"><?php echo $mainHeadingText; ?></div>
        <div class="lh5">&nbsp;</div>
        <div class="clearBoth main_heading_desc"><?php echo $nextToHeadingText; ?></div>
    </div>
    <div class="<?php echo $gapdiv; ?>"></div>
	<div class="floatRight" <?php echo $displayButtonBlock;?>>
            <?php if($this->view->generalObj->checkUserModuleActionPermission($_SESSION['USER']['userDetails'][0]['apikey'],$_SESSION['USER']['userDetails'][0]['user_email_address'],1,1)){
		echo '<div class="lh17">&nbsp;</div><div class="floatLeft"><a href="/admin/product/#add" title="Add Product" class="addProductLinkImage"></a></div>';
            }?>
    </div>
    <div class="floatRight" <?php echo $displayRightBlock; ?>>
        <div class="floatLeft new_step">
            <ul>
                <li>
                    <div class="wid285">
                        <div class="floatLeft <?php echo $headingClassFirst; ?>"><?php echo $firstHeadingText; ?></div>
                        <div class="floatRight <?php echo $nowClassFirst; ?>"><?php echo $nowtext; ?></div>
                    </div>
                </li>
                <div class="bottomLine"></div>
                <li>
                    <div class="wid285">
                        <div class="floatLeft <?php echo $headingClassSecond; ?>"><?php echo $secondHeadingText; ?></div>
                        <div class="floatRight <?php echo $nowClassSecond; ?>"><?php echo $nextText; ?></div>
                    </div>
                </li>
                <div class="bottomLine"></div>
                <li>
                    <div class="wid285">
                        <div class="floatLeft <?php echo $headingClassThird ?>"><?php echo $thirdHeadingText; ?></div>
                        <div class="floatRight <?php echo $nowClassThird; ?>"><?php echo $laterText; ?></div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>