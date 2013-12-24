mugo_view_extras
================
An extension to manage page elements around the content area. It allows
administrators to configure those areas (view_extras) in the admin interface.

Code Example
==============

{def $current_node = fetch( 'content', 'node', hash( 'node_id', $current_node_id ) )}
{def $view_extras = fetch( 'mugo_view_extras', 'get', hash( 'node_id', $current_node.node_id ) )}

{if $view_extras.top}
	{def $view_extra_top = fetch( 'content', 'node', hash( 'node_id', $view_extras.top ) )}
	{node_view_gui content_node=$view_extra_top view='extra'}
{/if}


Fetch method details
=======================

Usually you submit a node_id to the fetch method. It's possible to submit no node_id and the fetch method
returns the "default" view_extra array:

{def $view_extras = fetch( 'mugo_view_extras', 'get' )}

But before you need to set the default view_extra array:

{def $view_extras = fetch( 'mugo_view_extras', 'get', hash( 'node_id',     $view_extras_lookup_node_id,
                                                            'set_default', true()
                                                          ) )}

This is usefull if you have template not in context of a node_id and you use another template in the same
request which sets the default view_extra array.

