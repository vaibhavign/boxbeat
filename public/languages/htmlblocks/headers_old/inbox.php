

<?php

if($edata->filter=='')
{
$heading='All messages';
$total=(($edata->totalunread['total']>0)?"(".$edata->totalunread['total'].")":"");
}
if($edata->filter=='dispute')
{
$heading='Disputes';
$total=(($edata->totalunread['dispute']>0)?"(".$edata->totalunread['dispute'].")":"");
}
if($edata->filter=='request')
{
$heading='Requests';
$total=(($edata->totalunread['request']>0)?"(".$edata->totalunread['request'].")":"");
}
if($edata->filter=='alert')
{
$heading='Alerts';
$total=(($edata->totalunread['alert']>0)?"(".$edata->totalunread['alert'].")":"");
}
if($edata->filter=='conversation')
{
$heading='Conversations';
$total=(($edata->totalunread['chat']>0)?"(".$edata->totalunread['chat'].")":"");
}
if($edata->filter=='starred')
{
$heading='Starred';
//$total=(($edata->totalunread['star']>0)?"(".$edata->totalunread['star'].")":"");
$total='';
}


switch($actionName){
case 'list' :
?>

<div class="clearBoth" id="bodytopbar">
                            	<div class="topbarheading"><?=$heading.'<span  class="'.$heading.'_MSG"> '.$total.'</span>' ?></div>
                                <!--<div class="floatRight">
                                	<div class="search_bg">
                                    	<div class="floatLeft"><input type="text" class="searchbox" /></div>
                                        <div class="floatLeft"><input type="image" src="/images/admin/search_icon.png" class="searchbutton" title="Search" alt="Search" /></div>					</div>
                                </div>-->
                           </div>
						   
<?php break;
case 'detail':
 
?>
						<tr>
                    		<td valign="top" height="32">
                        	<div class="clearBoth" id="bodytopbar">
                            <!--if($messagedata[$i]['username']!=''--><? ?>
                            	<div class="topbarheading"><?=$edata->otheruserdetail['name']?></div><? /*}
								else{
								break;
								}
								$username=$name;
								}*/?>
								<div class="floatRight">
                                	<div class="floatLeft">
										<div class="floatLeft"><a href="#" class="actionbuttondefault" id="actionbutton"></a></div>
										  <!-- Action Drop Down  Start Form here-->
										 <div id="actiondropdown">
							  			 	<?php
											$class=new General();
											$image=$class->getuserimageSrc($edata->otheruserdetail[0]['id'],22,22,'small',0,1);
											?>
		
											<ul>
												<li class="first"><a href="/inbox/#ru/cid/<?=$edata->cid?>/a/0/re/1" id="unreadremoveclass_" title="Mark as unread" rel="<?=$edata->cid?>">Mark as unread</a></li>
												<li><a href="javascript:void(0);" id="ask-detail" title="New conversation" rel="<?=$edata->otheruserdetail[0]['user_full_name']?>^<?=$image?>^<?=$edata->otheruserdetail[0]['id']?>">New conversation</a></li>
												<li><a href="javascript:void(0);" rel="<?=$edata->cid?> " id="deleteconversation" title="Delete conversation" >Delete conversation</a></li>
											</ul>
									   </div>
									   	<!-- Action Drop Down Down here-->
									
									</div>
                                  <div class="floatLeft"><a href="/inbox/#list" class="back_link" title="Back to conversations"><span class="backtoarrow"></span>Back to conversations</a></div>				
                                </div>
                           </div>
                         
                        </td>
                    	</tr>
 						<tr>
                    	<td valign="top"><div class="bottomline">&nbsp;</div></td>
                    </tr>
<?php 
}
?>
