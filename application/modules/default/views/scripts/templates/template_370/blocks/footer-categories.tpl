
            			<h3><div class="blocktitle">Categories</div></h3>
						<div class="blockcontent">
						 <ul>
							 {foreach from=$footer_categories_array item=category}
								<li><a href="{$category.url}" title="{$category.title}">{$category.title|@stripslashes}</a></li>
							 {/foreach}
						  </ul>
						
					</div>         
            