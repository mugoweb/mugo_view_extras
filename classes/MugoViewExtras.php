<?php

/**
 * @author pek
 *
 */
class MugoViewExtras
{
	/**
	 * @param int $node_id
	 * @param boolean $set_default
	 * @param string $set
	 * @return multitype:
	 */
	public function get_view_extras( $node_id = null, $set_default = false, $set = 'standard' )
	{
		if( $node_id )
		{
			// check for cached version
			if( !isset( $GLOBALS[ 'mugo' ][ 'extra_views' ][ 'cache' ][ $node_id ] ) )
			{
				$node = eZContentObjectTreeNode::fetch( $node_id );
			
				if( $node instanceof eZContentObjectTreeNode )
				{
					$GLOBALS[ 'mugo' ][ 'extra_views' ][ 'cache' ][ $node_id ] = $this->build_view_extras( $node, $set );
				}
				else
				{
					eZDebug::writeWarning( 'Mugo View Extra: No valid node_id provided' );
					return array();
				}
			}

			if( $set_default )
			{
				$GLOBALS[ 'mugo' ][ 'extra_views' ][ 'cache' ][ 'default' ] = $GLOBALS[ 'mugo' ][ 'extra_views' ][ 'cache' ][ $node_id ];
			}
			
			return $GLOBALS[ 'mugo' ][ 'extra_views' ][ 'cache' ][ $node_id ];
		}
		else
		{
			if( isset( $GLOBALS[ 'mugo' ][ 'extra_views' ][ 'cache' ][ 'default' ] ) )
			{
				return $GLOBALS[ 'mugo' ][ 'extra_views' ][ 'cache' ][ 'default' ];
			}
			else
			{
				eZDebug::writeWarning( 'Mugo View Extra: No default config submitted, yet.' );
				return array();
			}
		}
	}
	
	private function build_view_extras( $node, $set = 'standard' )
	{
		$return = array();
		
		// in case NULL or empty string was provided
		$set = $set ? $set : 'standard';

		$ini     = eZINI::instance( 'mugo_view_extras.ini' );
		$tags    = $ini->variable( 'Set-'. $set, 'Tags' );
		
		// build data struture
		$data = $this->build_data_structure( $node );
		
		//Reads all view_extra tags for the current path (data structure)
		$node_tags = $this->get_view_extra_node_tags( $data );
		
		if( !empty( $tags ) )
		{
			$tag_ids = array_keys( $tags );
			
			foreach( $tag_ids as $tag_id )
			{
				//Exclude 'self-instance' to allow single instance overrides
				if( $tag_id != 'self-instance' )
				{
					//Just do it for configured tags
					if( in_array( $tag_id, $node_tags ) )
					{
						foreach( $data as $step )
						{
							$data_array = isset( $step[ 'view_extras' ][ $tag_id ] ) ? $step[ 'view_extras' ][ $tag_id ] : null;
							$return = $this->array_overlay( $return, $data_array );
						}
					}
				}
			}
			
			
			if( in_array( 'self-instance', $tag_ids ) )
			{
				$self_step = array_pop( $data );
				
				if( !empty( $self_step[ 'view_extras' ][ 'self-instance' ] ) )
				{
					$data_array = $self_step[ 'view_extras' ][ 'self-instance' ];
					$return = $this->array_overlay( $return, $data_array );
				}
			}
		}
		else
		{
			eZDebug::writeError( 'Mugo View Extra: No tags configured for set "Set-'. $set . '".' );
		}

		return $return;
	}

	/**
	 * Walks down the path and checks for view_extra attributes and tags attributes
	 * 
	 * @param int $node
	 * @return Ambigous <string, multitype:multitype:multitype: NULL  >
	 */
	private function build_data_structure( $node )
	{
		$return = array();
		
		// Build full path from root node to submitted node
		$path   = $node->attribute( 'path' );
		$path[] = $node;
		
		foreach( $path as $step )
		{
			$step_data = array();
			
			$data_map = $step->attribute( 'data_map' );

			if( isset( $data_map[ 'tags' ] ) && $data_map[ 'tags' ]->attribute( 'has_content' ) )
			{
				$content = $data_map[ 'tags' ]->attribute( 'content' );
				$step_data[ 'tags' ] = $content->KeywordArray;
			}
			else
			{
				$step_data[ 'tags' ] = array();
			}
			
			if( isset( $data_map[ 'view_extras' ] ) && $data_map[ 'view_extras' ]->attribute( 'has_content' ) )
			{
				$step_data[ 'view_extras' ] = $data_map[ 'view_extras' ]->attribute( 'content' );
			}
			
			$return[] = $step_data;
		}

		// Add the tag "standard" to root element
		if( !in_array( 'standard', $return[ 0 ][ 'tags' ] ) )
		{
			$return[ 0 ][ 'tags' ][] = 'standard';
		}
		
		return $return;
	}
	
	
	public function get_view_extra_node_tags( $data )
	{
		$return = array();

		foreach( $data as $step )
		{
			$return = array_unique( array_merge( $return, $this->map_view_extra_node_tag( $step[ 'tags' ] ) ) );
		}

		return $return;
	}
	
	private function array_overlay( $skel, $arr )
	{
		foreach( $skel as $key => $val )
		{
			if( ! isset( $arr[ $key ] ) || $arr[ $key ] == null )
			{
				$arr[ $key ] = $val;
			}
		}

		return $arr;
	}
	
	// not every tag is a view_extra tag - so let's have some mapping
	private function map_view_extra_node_tag( $node_tags )
	{
		return $node_tags;
	}
}

?>