               <h2><div class="blocktitle">{$custom_block_title}</div></h2>
                	<div class="blockcontent">
						        <ol class = "list">
						        	{foreach from=$custom_block_image_with_description_array item=custom_block_content}
                                      <li>
			                            <p class="image">
			                                <a href="{$custom_block_content.url}"><img src="{$custom_block_content.image}" alt="{$custom_block_content.image_title}" title="{$custom_block_content.image_title}"/></a>
			                            </p>
			                            <div class="container">
			                             	<div class="title"><a href="{$custom_block_content.url}">{$custom_block_content.title|@stripslashes}</a></div>
			                             	<div class="shortdescription">{$custom_block_content.desc|@stripslashes}</div>
			                             </div>
			                        </li>
                                   {/foreach}
                                </ol>
                        
                    	</div>
