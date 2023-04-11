{ezscript_require( 'ezjsc::jquery' )}
{ezcss_require( 'view_extras.css' )}

{if is_unset( $attribute_base )}
  {def $attribute_base = 'ContentObjectAttribute'}
{/if}

{def $input_prefix = concat( $attribute_base, '_', $attribute.id, '_' )
     $content      = $attribute.content
     $fields       = ezini( 'Set-standard', 'Fields', 'mugo_view_extras.ini' )
     $fieldgroups  = ezini( 'Set-standard', 'FieldGroups', 'mugo_view_extras.ini' )
     $view_extras  = fetch( 'mugo_view_extras', 'get', hash( 'node_id', $attribute.object.main_node_id ) )
}

<div class="ui-view-extras" id="tabs">

	<ul>
		<li>
			<a href="#tabs-standard">Standard</a>
		</li>
		<li>
			<a href="#tabs-tags">Tags</a>
		</li>
		<li>
			<a href="#tabs-self-instance">Self</a>
		</li>
	</ul>

	<div id="tabs-standard">
		{include uri='design:includes/view_extra_sheet.tpl'
		         fieldgroups=$fieldgroups
		         fields=$fields
		         sheet=$content[ 'standard' ]}
	</div>
	
	<div id="tabs-tags">
		<select>
			<option>- Select a tag -</option>
			{foreach $content as $id => $sheet}
				{if array( 'standard', 'self-instance' )|contains( $id )|not()}
					<option value="{$id|wash()}">{fetch( 'mugo_view_extras', 'resolve_tag', hash( 'tag', $id ) )|wash()}</option>
				{/if}
			{/foreach}
		</select>
		
		{foreach $content as $id => $sheet}
			{if array( 'standard', 'self-instance' )|contains( $id )|not()}
				<div id="user-tag-sheet-{$id}">
					{include uri='design:includes/view_extra_sheet.tpl'
							 fieldgroups=$fieldgroups
							 fields=$fields
							 sheet=$content[ $id ]}
				</div>
			{/if}
		{/foreach}
	</div>
	
	<div id="tabs-self-instance">
		{include uri='design:includes/view_extra_sheet.tpl'
		         fieldgroups=$fieldgroups
		         fields=$fields
		         sheet=$content[ 'self-instance' ]}
	</div>
</div>

<script>
{literal}

$(function()
{
	//Dropdown reveals specific user tag sheet
	$( '#tabs-tags select' ).change( function()
	{
		var tag = $(this).val();
		
		$(this).parent().find( '> div' ).hide();
		$(this).parent().find( '#user-tag-sheet-' + tag ).show();
	});
	
	$( "#tabs" ).tabs();
	
	$( '.ui-button' ).button();
});

{/literal}
</script>

{* show unconfigured fields
 {if ezini( 'MugoViewExtras', 'ShowUnkownFields', 'mugo_view_extras.ini' )|eq( 'enabled' )} 
	{foreach $content[ $tag_id ] as $field_id => $value} 
		{if $configured_tag_fields|contains( $field_id )|not()} 
						<tr class="unconfigured ui-state-error"> 
										<td>{$field_id|wash()}:</td> 
														<td>
														 					{$value|wash()} 
																		</td>
 			</tr> 
		{/if}
 	{/foreach}
 {/if}
 *}