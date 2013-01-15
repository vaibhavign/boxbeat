
            		<h3><div class="blocktitle">Brands</div></h3>
                	<div class="blockcontent">
						  <ul>
							 {foreach from=$footer_brands_array item=brand}
								<li><a href="{$brand.url}">{$brand.title|@stripslashes}</a></li>
							   {/foreach}
						  </ul>
                </div>         
            