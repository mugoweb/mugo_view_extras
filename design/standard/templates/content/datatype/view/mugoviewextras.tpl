{if is_unset( $attribute_base )}
  {def $attribute_base = 'ContentObjectAttribute'}
{/if}

{def $input_prefix = concat( $attribute_base, '_', $attribute.id, '_' )
     $content      = $attribute.content
     $tags         = ezini( 'Set-standard', 'Tags', 'mugo_view_extras.ini' )
     $fields       = ezini( 'Set-standard', 'Fields', 'mugo_view_extras.ini' )
     $fieldgroups  = ezini( 'Set-standard', 'FieldGroups', 'mugo_view_extras.ini' )
     $view_extras  = fetch( 'mugo_view_extras', 'get', hash( 'node_id', $attribute.object.main_node_id ) )
     $field_ids    = array()
}

{ezscript_require( 'ezjsc::jquery' )}
{ezscript_require( 'ezjsc::jqueryUI' )}
{ezcss_require( 'ui-lightness/jquery-ui-1.8.11.custom.css' )}
{ezcss_require( 'view_extras.css' )}

{if $tags|count()}
	<div class="ui-view-extras" id="tabs">
	
		<ul>
			{foreach $tags as $id => $name}
				<li {if array( 'self-instance', 'standard' )|contains( $id )|not()}class="ui-special-tag"{/if}>
					<a href="#tabs-{$id|wash()}">{$name|wash()}</a>
				</li>
			{/foreach}
			{if $view_extras}
				<li><a href="#tabs-current-config}">Node Values</a></li>
			{/if}
		</ul>

		{foreach $tags as $tag_id => $tag_name}
			<div id="tabs-{$tag_id|wash()}">

				{if $fieldgroups|count()}
					{foreach $fieldgroups as $name => $fieldgroup}
						<fieldset>
							<legend>{$name|wash()}:</legend>

							{set $field_ids = $fieldgroup|explode( ',' )}
							
							{if $field_ids|count()}
								<table>
									{foreach $field_ids as $field_id}
										<tr>
											<td>{$fields[ $field_id ]|wash()}:</td>
											<td>
												{if is_set( $content[ $tag_id ][ $field_id ] )}{$content[ $tag_id ][ $field_id ]|wash()}{/if}
											</td>
										</tr>
									{/foreach}
								</table>
							{/if}
						</fieldset>
					{/foreach}
				{/if}
			</div>
		{/foreach}
		
		{if $view_extras}
			<div id="tabs-current-config">
				{if $fieldgroups|count()}
					{foreach $fieldgroups as $name => $fieldgroup}
						<fieldset>
							<legend>{$name|wash()}:</legend>

							{set $field_ids = $fieldgroup|explode( ',' )}
							
							{if $field_ids|count()}
								<table>
									<thead>
										<td>Label</td>
										<td>Id</td>
										<td>Class</td>
										<td>Name</td>
									</thead>
									<tbody>
									{def $view_extra_loop = false()}
									{foreach $field_ids as $field_id}
										<tr>
											{if and( is_set( $view_extras[ $field_id ] ), $view_extras[ $field_id ] )}
												{set $view_extra_loop = fetch( 'content', 'node', hash( 'node_id', $view_extras[ $field_id ] ) )}
												
												<td>{$fields[ $field_id ]|wash()}</td>
												<td>{$view_extras[ $field_id ]}</td>
												<td>{$view_extra_loop.class_identifier}</td>
												<td>
													<a href={concat( 'content/view/full/', $view_extras[ $field_id ])|ezurl()}>{$view_extra_loop.name|wash()}</a>
												</td>
											{else}
												<td>{$view_extras[ $field_id ]|wash()}</td>
											{/if}
										</tr>
									{/foreach}
									</tbody>
								</table>
							{/if}
						</fieldset>
					{/foreach}
				{/if}
			</div>
		{/if}
	</div>
{/if}

<script>
{literal}

$(function()
{
	$( "#tabs" ).tabs();
	
	$( '.ui-button' ).button();
});

{/literal}
</script>
