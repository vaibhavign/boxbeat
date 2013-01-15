				   <h2><div class="blocktitle">{$custom_block_title}</div></h2>
                	<div class="blockcontent">
						        <ol class="grid">
						        	{foreach from=$custom_block_image_only_array item=custom_block_content}
                                    <li>
                                    <p class="image">
                                        <a href="{$custom_block_content.url}"><img src="{$custom_block_content.image}" alt="{$custom_block_content.image_title}" title="{$custom_block_content.image_title}" /></a>
                                    </p>
                                </li>
                                   {/foreach}
                                </ol>
                        
                    	</div>
                    	
                    