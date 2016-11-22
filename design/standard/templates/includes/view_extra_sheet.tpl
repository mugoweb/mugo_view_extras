{*
   INPUT
   
   $sheet
   $fieldgroups
   $fields
   $edit
*}

{if is_unset( $fieldgroups )}
	{def $fieldgroups  = ezini( 'Set-standard', 'FieldGroups', 'mugo_view_extras.ini' )}
{/if}
{if is_unset( $fields )}
	{def $fields = ezini( 'Set-standard', 'Fields', 'mugo_view_extras.ini' )}
{/if}
{if is_unset( $edit )}
	{def $edit = false()}
{/if}

{if $fieldgroups|count()}
	
	{def $field_ids = array()
	     $selectedNode = false()}
	
	{foreach $fieldgroups as $name => $fieldgroup}
		<fieldset>
			<legend>{$name|wash()}:</legend>

			{set $field_ids = $fieldgroup|explode( ',' )}

			{if $field_ids|count()}
				<table>
					<tr>
						<th>View Extra</th>
						<th>Id</th>
						<th>Class</th>
						<th>Name</th>
					</tr>
					{foreach $field_ids as $field_id}
						<tr>
							<td>{$fields[ $field_id ]|wash()}:</td>
							{if $edit}
								<td>
									<input type="text"
									       value="{if is_set( $sheet[ $field_id ] )}{$sheet[ $field_id ]|wash()}{/if}"
									       name="{$input_prefix}view_extra[{$sheet_id|wash()}][{$field_id|wash()}]"
									 />
									<input class="button" type="submit" name="CustomActionButton[{$attribute.id}_browse_related_node][{$sheet_id|wash()}][{$field_id|wash()}]" value="{'Browse for node'|i18n( 'design/standard/content/datatype' )}" />
								</td>
							{else}
								<td>
									{if is_set( $sheet[ $field_id ] )}{$sheet[ $field_id ]|wash()}{/if}
								</td>
							{/if}
							{* Node details *}
							{if and( is_set( $sheet[ $field_id ] ), $sheet[ $field_id ] )}
								{set $selectedNode = fetch( 'content', 'node', hash( 'node_id', $sheet[ $field_id ] ) )}
								{if $selectedNode}
									<td>
										{$selectedNode.class_name|wash()}
									</td>
									<td>
										<a href={$selectedNode.url_alias|ezurl()}>{$selectedNode.name|wash()}</a>
									</td>
								{/if}
							{/if}
						</tr>
					{/foreach}
				</table>
			{/if}
		</fieldset>
	{/foreach}
{/if}
