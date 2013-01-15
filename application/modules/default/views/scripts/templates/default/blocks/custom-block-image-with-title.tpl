                <h2><div class="blocktitle">{$custom_block_title}</div></h2>
                	<div class="blockcontent">
						        <ol class = "grid">
						        	{foreach from=$custom_block_image_with_title_array item=custom_block_content}
                                      <li  align="center">
			                            <p class="image">
			                                <a href="{$custom_block_content.url}"><img src="{$custom_block_content.image}" alt="{$custom_block_content.image_title}" title="{$custom_block_content.image_title}" /></a>
			                            </p>
			                            <div class="container">
			                             	<h4><div class="title"><a href="{$custom_block_content.url}">{$custom_block_content.title|@stripslashes}</a></div></h4>
			                             </div>
			                        </li>
                                   {/foreach}
                                </ol>
                        
                    	</div>
