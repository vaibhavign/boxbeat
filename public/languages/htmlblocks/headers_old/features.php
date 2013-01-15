<?php
$generalObj = new General();
$request = Zend_Controller_Front::getInstance()->getRequest();
$featureId = $request->getParam('featureid');
$groupId = $request->getParam('groupid');

switch ($actionName) {
    case 'featurebasicinfo':
        $mainHeadingText = is_numeric($featureId) ? 'Edit feature' : 'Create a new feature';
        $nextToHeadingText = "A feature represents a property of product such as its color,speed,model & so on.";
        $displayUlBlock = 'style="display:block;"';
        $displayButtonBlock = 'style="display:none;"';


        $firstHeadingText = 'Fill feature information';
        $secondHeadingText = 'Add to qroups';
        $headingClassFirst = 'nowHeading';
        $headingClassSecond = 'notnowHeading';
        $nowClassFirst = 'now';
        $nowClassSecond = 'notnow';
        $nowtext = 'now';
        $nextText = 'next';
        break;

    case 'addgroups':
        $mainHeadingText = 'Create a new feature';
        $nextToHeadingText = "Fill information required to create a feature and add it to groups.";
        $displayUlBlock = 'style="display:block;"';
        $displayButtonBlock = 'style="display:none;"';
        $firstHeadingText = 'Fill feature information';
        $secondHeadingText = 'Add to qroups';
        $headingClassFirst = 'notnowHeading';
        $headingClassSecond = 'nowHeading';
        //$nowClassFirst = 'notnow';
        $nowClassSecond = 'now';
        $nowtext = '<div class="floatLeft"><span class="modify_icon"></span><a href="/admin/features/#featurebasicinfo/featureid/' . $request->getParam('featureid') . '" title="modify" class="modify_link">modify</a></div>';
        $nextText = 'now';
        break;

    case 'setfeaturegroup':
        $mainHeadingText = is_numeric($groupId) ? 'Edit feature group' : 'Create a new feature group';
        $nextToHeadingText = "Organise the features by grouping them.";
        $displayUlBlock = 'style="display:none;"';
        $displayButtonBlock = 'style="display:none;"';
        break;

    case 'managefeatures':
        $mainHeadingText = 'Features';
        $nextToHeadingText = "Features are properites of a product such as fabric, optical zoom, memory etc.";
        $displayUlBlock = 'style="display:none;"';
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
    <div class="floatRight" <?php echo $displayButtonBlock; ?>>
        <?php
        if ($generalObj->checkUserModuleActionPermission($_SESSION['USER']['userDetails'][0]['apikey'], $_SESSION['USER']['userDetails'][0]['user_email_address'], 4, 1)) {
            echo '<div class="floatLeft"><a href="/admin/features/#featurebasicinfo" title="Add Feature" class="addfeaturesLinkImage"></a></div>';
        }
        ?>
    </div>
    <div class="floatRight" <?php echo $displayUlBlock; ?>>
        <div class="floatLeft new_step">
            <ul>
                <li>
                    <div class="wid285">
                        <div class="floatLeft <?php echo $headingClassFirst; ?>"><?php echo $firstHeadingText; ?></div>
                        <div class="floatRight <?php echo $nowClassFirst; ?>"><?php echo $nowtext; ?></div>
                        <div class="bottomLine"></div>
                        <div class="floatLeft <?php echo $headingClassSecond; ?>"><?php echo $secondHeadingText; ?></div>
                        <div class="floatRight <?php echo $nowClassSecond; ?>"><?php echo $nextText; ?></div>
                        <?php /* ?>
                          <div class="bottomLine"></div>
                          <div class="floatLeft <?php echo $headingClassThird?>"><?php echo $thirdHeadingText;?></div>
                          <div class="floatRight <?php echo $nowClassThird.'"'.$style;?>"><?php echo $laterText;?></div>
                          <?php */ ?>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<!--------------------------------------------------->
