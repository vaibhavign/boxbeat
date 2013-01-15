
                	<div class="blocktitle">Popular Tags</div>
                    <div class="blockcontent">
                    	<div class="list">
                        <ul>
							{foreach from=$popular_tags_array item=tag}
                            <li><a title="{$tag.title}" class="{$tag.font}" href="{$tag.url}">{$tag.title|@stripslashes}</a></li>
                            {/foreach}
                        </ul>
                        </div>
                    </div>
                