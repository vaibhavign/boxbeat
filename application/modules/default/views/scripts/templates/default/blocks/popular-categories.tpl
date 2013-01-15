
                            <h2><div class="blocktitle">Popular Categories</div></h2>
                            <div class="blockcontent">
                            <ul>
								{foreach from=$popular_categories_array item=category}
								  <h5><li><a title="{$category.title}" href="{$category.url}">{$category.title|@stripslashes}</a></li></h5>
								 
								{/foreach}
							</ul>
                            	</div>
                        	