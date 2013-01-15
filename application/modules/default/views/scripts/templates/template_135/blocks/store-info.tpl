
            		<h3><div class="blocktitle">Store information</div></h3>
                	<div class="blockcontent">
					 <ul>
						 {foreach from=$store_info_array item=link}
						<li><a href="{$link.url}">{$link.title|@stripslashes}</a></li>
						{/foreach}
					  </ul>
                	</div>         
            