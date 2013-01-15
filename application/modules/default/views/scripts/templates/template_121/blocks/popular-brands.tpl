
                            <h2><div class="blocktitle"><span>Popular Brands</span></div></h2>
							<div class="blockcontent">
                            <ul>
								 {foreach from=$popular_brands_array item=brand}
									<h5><li><a title="{$brand.title}" href="{$brand.url}">{$brand.title|@stripslashes}</a></li></h5>
								{/foreach}
							</ul>
							</div>
                        	