
                      		  <h2> <div class="blocktitle"><span>Compare Products</span></div></h2>
								<div class="blockcontent">
									<div class="list"> 
	
									{if $compare_products_array|@sizeof eq 0}
						
									  <div class="empty">You have no product to compare</div>
						
									  {else}	    
						
									  {foreach from=$compare_products_array item=product}
										  <div class="main_container">
												<div class="clearBoth">
													<div class="icon"><a href="javascript://"><span id="{$product.id}" class="delete_icon"></span></a></div>
													<p class="image">
													   <a href="{$product.url}"><img src="{$product.image}" alt="{$product.image_title}" title="{$product.image_title}"/></a>
													</p>
													<div class="container">
														<div class="title"><a href="{$product.url}" title="{$product.title}">{$product.title|@stripslashes}</a></div>
													</div>
													<div class="product">
														<div class="name">{$product.title|@stripslashes}</div>
														<div class="delete_icon"><a class="delete" id="{$product.id}" href="javascript://" title="Delete"></a></div>
													</div>
												</div>
											</div>
						
									  {/foreach}
						
									{/if}
						
									{if $compare_products_array|@sizeof neq 0}
									
										 <div class="buttons">
											<div class="compare_btn"><a href = "http://{$smarty.server.HTTP_HOST}/compareall"><input type="image" src="{$image_path}compare_btn.png"  alt="Compare all" title="Compare all" /></a></div>
											<div class="clearall_btn"><input type="image" src="{$image_path}clearall_btn.png" alt="Clear all" title="Clear all" /></div>
										 </div> 
						
									{/if}
						
								 </div>
                            </div>
                        
                        