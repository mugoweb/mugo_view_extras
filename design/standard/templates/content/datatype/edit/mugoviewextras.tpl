{if is_unset( $attribute_base )}
  {def $attribute_base = 'ContentObjectAttribute'}
{/if}

{def $input_prefix = concat( $attribute_base, '_', $attribute.id, '_' )
     $content      = $attribute.content
     $tags         = ezini( 'Set-standard', 'Tags', 'mugo_view_extras.ini' )
     $fields       = ezini( 'Set-standard', 'Fields', 'mugo_view_extras.ini' )
     $fieldgroups  = ezini( 'Set-standard', 'FieldGroups', 'mugo_view_extras.ini' )
     $field_ids    = array()
     $selectedNode = false()
     $field_value  = ''
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
										{if is_set( $content[ $tag_id ][ $field_id ] )}
											{set $field_value = $content[ $tag_id ][ $field_id ]}
										{else}
											{set $field_value = ''}
										{/if}
										<tr>
											<td>{$fields[ $field_id ]|wash()}:</td>
											<td>
												<input type="text"
												       value="{$field_value|wash()}"
												       name="{$input_prefix}view_extra[{$tag_id|wash()}][{$field_id|wash()}]"
												 />
												<input class="button" type="submit" name="CustomActionButton[{$attribute.id}_browse_related_node][{$tag_id|wash()}][{$field_id|wash()}]" value="{'Browse for node'|i18n( 'design/standard/content/datatype' )}" />
												{if ne( '', $field_value )}
													{set $selectedNode = fetch( 'content', 'node', hash( 'node_id', $field_value ) )}
													{if $selectedNode}
														Node name: {$selectedNode.name|wash()}
													{/if}
												{/if}
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
