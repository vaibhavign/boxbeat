<?php
switch($actionName){
case 'list' :
?>

<div class="clearBoth" id="bodytopbar">
                            	<div class="topbarheading">Conversations</div>
                                <div class="floatRight">
                                	<div class="search_bg">
                                    	<div class="floatLeft"><input type="text" class="searchbox" /></div>
                                        <div class="floatLeft"><input type="image" src="/images/admin/search_icon.png" class="searchbutton" title="Search" alt="Search" /></div>					</div>
                                </div>
                           </div>
						   
<?php break;
case 'detail':
?>
						<tr>
                    		<td valign="top" height="32">
                        	<div class="clearBoth" id="bodytopbar">
                            <!--if($messagedata[$i]['username']!=''--><? ?>
                            	<div class="topbarheading"><?=$edata->userfullname?></div><? /*}
								else{
								break;
								}
								$username=$name;
								}*/?>
								<div class="floatRight">
                                	<div class="floatLeft"><a href="#" class="actionbuttondefault" id="actionbutton"></a></div>
                                  <div class="floatLeft"><a href="/admin/conversation/#list" class="back_link" title="Back to messages"><span class="backtoarrow"></span>Back to conversations</a></div>				
                                </div>
                           </div>
                           <!-- Action Drop Down  Start Form here-->
                           <div id="actiondropdown">
                           		<ul>
                                	<li><a href="inbox_listing.php" title="Mark as unread">Mark as unread</a></li>
                                    <li><a href="popup.php" title="Delete conversation" >Delete conversation</a></li>
                                    <li><a href="popup.php" title="New conversation">New conversation</a></li>
                                    <li><a href="popup.php" title="Report/Block user">Report/Block user</a></li>
                                </ul>
                           
                           </div>
                           <!-- Action Drop Down Down here-->
                        </td>
                    	</tr>
 						<tr>
                    	<td valign="top"><div class="bottomline">&nbsp;</div></td>
                    </tr>
<?php 
}
?>