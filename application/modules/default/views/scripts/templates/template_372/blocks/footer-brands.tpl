
            		<h3><div class="blocktitle">Brands</div></h3>
                	<div class="blockcontent">
						  <ul>
							 {foreach from=$footer_brands_array item=brand}
								<li><a href="{$brand.url}" title="{$brand.title}">{$brand.title|@stripslashes}</a></li>
							   {/foreach}
						  </ul>
                </div>         
            