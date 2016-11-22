{*
	INPUT
		node_id         : id of context node
		view_extra_name : id of view_extra
*}

{if is_unset( $ttl )}
	{def $ttl = 7200}
{/if}

{def $view_extra_id = concat( "ezve-", $view_extra_name|explode('_')|implode('-') )}

{* solution to avoid cache block *}
{if eq( $ttl, -1 )}
	{def $node = fetch( 'content', 'node', hash( 'node_id', $node_id ) )}
	{node_view_gui
		content_node=$node
		view='extra'
		view_extra_name=$view_extra_name
		id=concat( $view_extra_id, '-block' )
	}
{else}
	{def $layout = $DesignKeys:used.layout}
	{cache-block ignore_content_expiry keys=array( $node_id, $layout ) subtree_expiry=$node_id expiry=$ttl}
		{def $node = fetch( 'content', 'node', hash( 'node_id', $node_id ) )}
		{node_view_gui
			content_node=$node
			view='extra'
			view_extra_name=$view_extra_name
			id=concat( $view_extra_id, '-block' )
		}
	{/cache-block}
{/if}
