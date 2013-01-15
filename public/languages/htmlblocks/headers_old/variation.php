<?php
if (($controllerName == 'variation') && ($actionName == 'createvariation')) {
    $url_arr = explode('/', $_SERVER['REQUEST_URI']);
    $id = is_numeric($url_arr[5]) ? $url_arr[5] : 0;
    ?>
    <!--<div class="main_header">
    <div class="clearBoth">
        <div class="floatLeft">
            <div class="mainHeadingGap">&nbsp;</div>
            <div class="floatLeft main_heading"><?php echo $id ? "Edit variation" : "Create a new variation"; ?></div>
            <div class="clearBoth main_heading_desc"><?php echo $id ? "Edit your variation" : "Fill information required to create a variation"; ?> and add it to groups</div>
        </div>
        <div class="floatRight">
            <div class="topRightmain">
                <div class="mainRightHeadingGap">&nbsp;</div>
                <div class="clearBoth">
                    <div class="floatLeft add_to_groupRightText RightTextAdjustment">Fill variation information</div>
                    <div class="floatRight RightTextAdjustment1 rightNowText">now</div>
                </div>
                <div class="lh8">&nbsp;</div>
                <div class="borderSolid">&nbsp;</div>
                <div class="lh8">&nbsp;</div>
                <div class="clearBoth">
                    <div class="floatLeft fillVariationInfoText RightTextAdjustment">Add to groups</div>
                    <div class="floatRight RightTextAdjustment1 nextText">next</div>
                </div>
            </div>
        </div>
    </div>
    </div>-->


    <div class="clearBoth">
        <div class="main_header">
            <div class="floatLeft">
                <div class="floatLeft main_heading"><?php echo $id ? "Edit variation" : "Create a new variation"; ?></div>
                <div class="lh5">&nbsp;</div>
                <div class="clearBoth main_heading_desc"><?php echo $id ? "Edit your variation" : "Fill information required to create a variation"; ?> and add it to groups</div>
            </div>
            <div class=""></div>
            <div style="display:none;" class="floatRight">
                <div class="lh17">&nbsp;</div>
                <div class="floatLeft"><a class="addProductLinkImage" title="Add Product" href="/admin/product/#add"></a></div>
            </div>
            <div class="floatRight">
                <div class="floatLeft new_step">
                    <ul>
                        <li>
                            <div class="wid285">
                                <div class="floatLeft nowHeading">Fill variation information</div>
                                <div class="floatRight now">now</div>
                            </div>
                        </li>
                        <div class="bottomLine"></div>
                        <li>
                            <div class="wid285">
                                <div class="floatLeft notnowHeading">Add to groups</div>
                                <div class="floatRight notnow">next</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>            </div>



<?php
} else if (($controllerName == 'variation') && ($actionName == 'addtogroup')) {
    $url_arr = explode('/', $_SERVER['REQUEST_URI']);
    $id = is_numeric($url_arr[5]) ? $url_arr[5] : 0;
    ?>



    <div class="clearBoth">
        <div class="main_header">
            <div class="floatLeft">
                <div class="floatLeft main_heading">Create a new variation</div>
                <div class="lh5">&nbsp;</div>
                <div class="clearBoth main_heading_desc">Fill information required to ceate a variation and add it to groups</div>
            </div>
            <div class=""></div>
            <div style="display:none;" class="floatRight">
                <div class="lh17">&nbsp;</div>
                <div class="floatLeft"><a class="addProductLinkImage" title="Add Product" href="/admin/product/#add"></a></div>
            </div>
            <div class="floatRight">
                <div class="floatLeft new_step">
                    <ul>
                        <li>
                            <div class="wid285">
                                <div class="floatLeft notnowHeading">Fill variation information</div>
                                <div class="floatRight">
                                    <div class="floatLeft modifyImg"></div>
                                    <div class="floatLeft RightTextAdjustment1"><a href="/admin/variation/#createvariation/variationid/<?php echo $id; ?>" title="modify">modify</a></div>
                                </div>
                            </div>
                        </li>
                        <div class="bottomLine"></div>
                        <li>
                            <div class="wid285">
                                <div class="floatLeft nowHeading">Add to groups</div>
                                <div class="floatRight now">next</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>            
    </div>



    <!--    <div class="main_header">
                <div class="clearBoth">
                    <div class="floatLeft">
                        <div class="floatLeft main_heading">Create a new variation</div>
                        <div class="lh5">&nbsp;</div>
                        <div class="clearBoth main_heading_desc">Fill information required to ceate a variation and add it to groups</div>
                    </div>
                    
                    <div class="floatRight">
                        <div class="topRightmain">
                            <div class="clearBoth">
                                <div class="floatLeft fillVariationInfoText RightTextAdjustment">Fill variation information</div>
                                <div class="floatRight">
                                    <div class="floatLeft modifyImg"></div>
                                    <div class="floatLeft RightTextAdjustment1"><a href="/admin/variation/#createvariation/variationid/<?php echo $id; ?>" title="modify">modify</a></div>
                                </div>
                            </div>
                            <div class="lh8">&nbsp;</div>
                            <div class="borderSolid">&nbsp;</div>
                            <div class="lh8">&nbsp;</div>
                            <div class="clearBoth">
                                <div class="floatLeft add_to_groupRightText RightTextAdjustment">Add to groups</div>
                                <div class="floatRight RightTextAdjustment1 rightNowText">now</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>-->
<?php } else if (($controllerName == 'variation') && ($actionName == 'createvariationgroup')) { ?>
    <div class="main_header">
        <div class="clearBoth">
            <div class="floatLeft">
                <div class="main_heading floatLeft">Craete a new variation group</div>
                <div class="lh5">&nbsp;</div>
                <div class="clearBoth main_heading_desc">Search and select the variations you wish to group.</div>
            </div>
        </div>
    </div>
<?php
        } else if (($controllerName == 'variation') && ($actionName == 'managevariations')) { 
            $generalObj = new General();
?>
    <div class="main_header">
        <div class="clearBoth">
            <div class="floatLeft">
                <div class="main_heading floatLeft">Variations</div>
                <div class="lh5">&nbsp;</div>
                <div class="clearBoth main_heading_desc">Variations allow you to create multiple variants of a product with different sizes, color etc.</div>
            </div>
            <div class="floatRight">
            <?php if ($generalObj->checkUserModuleActionPermission($_SESSION['USER']['userDetails'][0]['apikey'], $_SESSION['USER']['userDetails'][0]['user_email_address'], 5, 1)) { ?>
                <div class="floatLeft"><a href="/admin/variation/#createvariation" title="Add Variation" class="imgCursor addVariationLinkImage"></a></div>
            <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>