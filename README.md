mugo_view_extras
==
An extension to manage page elements around the content area. It allows
administrators to configure those areas (view_extras) in the admin interface.

Installation
==
1) Enable the extension
2) Rebuild autoload map
3) Configure the view_extra fields in mugo_view_extra.ini

Code Example
==
```
{def $current_node = fetch( 'content', 'node', hash( 'node_id', $current_node_id ) )}
{def $view_extras = fetch( 'mugo_view_extras', 'get', hash( 'node_id', $current_node.node_id ) )}

{if $view_extras.top}
	{def $view_extra_top = fetch( 'content', 'node', hash( 'node_id', $view_extras.top ) )}
	{node_view_gui content_node=$view_extra_top view='extra'}
{/if}
```

Code Example with 'include' tags
==
```
{set-block variable=$output}
<div>
	<include ttl="3600" id="above_footer_1"/>
</div>
{/set-block}
{$output|parse_includes( $view_extras_lookup_node_id )}
```

Fetch method details
==

Usually you submit a node_id to the fetch method. It's possible to submit no node_id and the fetch method
returns the "default" view_extra array:

`{def $view_extras = fetch( 'mugo_view_extras', 'get' )`

But before you need to set the default view_extra array:

```
{def $view_extras = fetch( 'mugo_view_extras', 'get', hash(
	'node_id',     $view_extras_lookup_node_id,
	'set_default', true()
) )}
```
This is useful if you have template not in context of a node_id and you use another template in the same
request which sets the default view_extra array.
