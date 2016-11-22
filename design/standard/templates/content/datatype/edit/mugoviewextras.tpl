{ezscript_require( 'ezjsc::jquery' )} 
{ezscript_require( 'ezjsc::jqueryUI' )} 
{ezcss_require( 'ui-lightness/jquery-ui-1.8.11.custom.css' )} 
{ezcss_require( 'view_extras.css' )}

{if is_unset( $attribute_base )}
  {def $attribute_base = 'ContentObjectAttribute'}
{/if}

{def $input_prefix = concat( $attribute_base, '_', $attribute.id, '_' )
     $content      = $attribute.content
     $tags         = ezini( 'Set-standard', 'Tags', 'mugo_view_extras.ini' )
     $fields       = ezini( 'Set-standard', 'Fields', 'mugo_view_extras.ini' )
     $fieldgroups  = ezini( 'Set-standard', 'FieldGroups', 'mugo_view_extras.ini' )
     $selectedNode = false()
     $field_value  = ''
}

{ezcss_require( 'view_extras.css' )}

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
		         sheet=$content[ 'standard' ]
		         edit=true()
		         sheet_id='standard'}
	</div>
	<div id="tabs-tags">
		<select name="currenttag">
			<option>- Select a tag -</option>
			{foreach $content as $id => $sheet}
				{if array( 'standard', 'self-instance' )|contains( $id )|not()}
					<option value="{$id|wash()}">{fetch( 'mugo_view_extras', 'resolve_tag', hash( 'tag', $id ) )|wash()}</option>
				{/if}
			{/foreach}
		</select>
		<input class="button" type="submit" name="CustomActionButton[{$attribute.id}_remove_tag]" value="{'Remove a Tag'|i18n( 'design/standard/content/datatype' )}" />
		
		{foreach $content as $id => $sheet}
			{if array( 'standard', 'self-instance' )|contains( $id )|not()}
				<div id="user-tag-sheet-{$id}">
					{include uri='design:includes/view_extra_sheet.tpl'
							 fieldgroups=$fieldgroups
							 fields=$fields
							 sheet=$content[ $id ]
					         edit=true()
					         sheet_id=$id}
				</div>
			{/if}
		{/foreach}

		<input type="text" name="newtagname" value="" />
		<input class="button" type="submit" name="CustomActionButton[{$attribute.id}_add_tag]" value="{'Add a Tag'|i18n( 'design/standard/content/datatype' )}" />
	</div>
	<div id="tabs-self-instance">
		{include uri='design:includes/view_extra_sheet.tpl'
		         fieldgroups=$fieldgroups
		         fields=$fields
		         sheet=$content[ 'self-instance' ]
		         edit=true()
		         sheet_id='self-instance'}
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
	
	$( '#tabs .ui-button' ).button();
});

{/literal}
</script>

{* show unconfigured fields
 {if ezini( 'MugoViewExtras', 'ShowUnkownFields', 'mugo_view_extras.ini' )|eq( 'enabled' )} 
	{foreach $content[ $tag_id ] as $field_id => $value} 
			{if $configured_tag_fields|contains( $field_id )|not()} 			<tr class="unconfigured ui-state-error"> 				<td>{$field_id|wash()}:</td> 				<td> 					<input type="text" 					       value="{$value|wash()}" 					       name="{$input_prefix}view_extra[{$tag_id|wash()}][{$field_id|wash()}]" 					 /> 					<input class="button" type="submit" name="CustomActionButton[{$attribute.id}_browse_related_node][{$tag_id|wash()}][{$field_id|wash()}]" value="{'Browse for node'|i18n( 'design/standard/content/datatype' )}" /> 					{if ne( '', $value )} 						{set $selectedNode = fetch( 'content', 'node', hash( 'node_id', $field_value ) )} 						{if $selectedNode} 							Node name: {$selectedNode.name|wash()} 						{/if} 					{/if} 				</td> 			</tr> 		{/if} 	{/foreach} {/if} 
*}