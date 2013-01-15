
            		<h3><div class="blocktitle">Tags</div></h3>
                	<div class="blockcontent">
                         <ul>
							{foreach from=$footer_tags_array item=tag}
                            <li><a href="{$tag.url}">{$tag.title|@stripslashes}</a></li>
							{/foreach}
                           
                        </ul>
                </div>         
            