{*
	INPUT
		node_id         : id of context node
		view_extra_name : id of view_extra
*}

{if is_unset( $ttl )}
	{def $ttl = 7200}
{/if}

{* TODO: drop the wrapper - needs to go into the extra.tpl files *}
{* Avoid inserting block nodes into the document head *}
{def
	$show_wrapper = array(
		'head_first_1',
		'head_first_2',
		'head_first_3',
		'additional_related_object'
		 )|contains( $view_extra_name )|not()
	$view_extra_id = concat( "ezve-", $view_extra_name|explode('_')|implode('-') )
}

{if $show_wrapper}<div id="{$view_extra_id}" class="eznid-{$node_id}">{/if}
{* custom solution to allow no cache block *}
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
{if $show_wrapper}</div>{/if}