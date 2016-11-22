{ezcss_require( 'view_extras.css' )}

{def $view_extras  = fetch( 'mugo_view_extras', 'get', hash( 'node_id', $node.node_id ) )
     $node_tags    = fetch( 'mugo_view_extras', 'node_tags', hash( 'node_id', $node.node_id ) )}

<div id="view-extras-current-config" class="ui-view-extras">
	
	<div class="tags">
		<h3>All tags for path</h3>
		<ol>
		{foreach $node_tags as $tag}
			<li>
				{fetch( 'mugo_view_extras', 'resolve_tag', hash( 'tag', $tag ) )|wash()},
			</li>
		{/foreach}
		</ol>
		
		<div style="clear: both"></div>
	</div>
	
	<h3>View Extra Values</h3>
	{include uri='design:includes/view_extra_sheet.tpl'
			 sheet=$view_extras}
</div>
