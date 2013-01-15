
                          <h2>
							  <div class="blocktitle"><span>Current top sellers</span></div>
							</h2>
							<div class="blockcontent">
							  <ol class="grid">
								{foreach from=$current_top_sellers_array item=product}
								<li>
								  <h4>
									<div class="gridtitle"><a href="{$product.url}">{$product.title|@stripslashes}</a></div>
								  </h4>
								  <p class="image"> <a href="{$product.url}"><img src="{$product.image}" alt="{$product.image_title}" title="{$product.image_title}"/></a> </p>
								  <div class="container">
									<div class="icon_container"> <a href="{$product.url}" class="cart_icon" title="Add to cart"></a> <a href="javascript://" rel="{$product.id}" class="compare_icon" title="Add to wishlist"></a> <a href="javascript://" rel="{$product.id}" class="wishlist_icon" title="Add to compare"></a> </div>
									<h4>
									  <div class="title"><a href="{$product.url}" title="{$product.title}">{$product.title|@stripslashes}</a></div>
									</h4>
									<div class="price_container"> <span class="label">Rs</span> <span class="mrpsmall"><del>{$product.mrp}</del></span> <span class="srpsmall">{$product.srp}</span> </div>
									<div class="rating_container">
										{section name = loop_rating  start = 1 loop = 6 step = 1}
											{if $smarty.section.loop_rating.index lt $product.rating}
												<div><a href="javascript://" class="ration_iconactive"></a></div>
											{else}
												<div><a href="javascript://" class="ration_iconinactive"></a></div>
											{/if}
										{/section}
									</div>
									<div class="shortdescription">{$product.desc}</div>
									<div class="cart_btn"><a href="{$product.url}">
									  <input type="image" src="{$image_path}addtocart_btn.png" alt="Add to cart" title="Add to cart"  />
									  </a></div>
									<div class="wishlistlink">
									  <div class="wishlist"><a href="javascript://" rel="{$product.id}" title="Add to wishlist">Add to wishlist</a></div>
									  <div class="compare"><a href="javascript://" rel="{$product.id}" title="Add to compare">Add to compare</a></div>
									</div>
								  </div>
								</li>
								{/foreach}
							  </ol>
							</div>

                         