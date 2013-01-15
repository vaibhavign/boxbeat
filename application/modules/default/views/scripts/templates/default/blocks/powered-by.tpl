
            		<h3><div class="blocktitle">Powered by Goo2o</div></h3>
                	<div class="blockcontent">
					<ul>
					   {foreach from=$powered_by_array item=link}
						<li><a href="{$link.url}" target="_blank">{$link.title|@stripslashes}</a></li>
						{/foreach}
					  </ul>
                       
                </div>         
            