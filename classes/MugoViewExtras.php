<?php
/*
 * make get_view_extras a static method and fix $this references
 * @author pek
 */
class MugoViewExtras
{
	/**
	 * Caching wrapper to the function 'build_view_extras'
	 *
	 * @param int $node_id
	 * @param boolean $set_default
	 * @param string $set
	 * @return multitype:
	 */
	public function get_view_extras( $node_id = null, $set_default = false )
	{
		if( $node_id )
		{
			// check for cached version
			if( !isset( $GLOBALS[ 'mugo' ][ 'extra_views' ][ 'cache' ][ $node_id ] ) )
			{
				$node = eZContentObjectTreeNode::fetch( $node_id );
			
				if( $node instanceof eZContentObjectTreeNode )
				{
					$GLOBALS[ 'mugo' ][ 'extra_views' ][ 'cache' ][ $node_id ] = $this->build_view_extras( $node );
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

	/**
	 *
	 * @param integer $node_id
	 * @return array
	 */
	public function get_node_tags( $node_id )
	{
		$return = array();

		$node = eZContentObjectTreeNode::fetch( $node_id );

		if( $node instanceof eZContentObjectTreeNode )
		{
			$return = $this->get_view_extra_node_tags( $this->build_data_structure( $node ) );
		}

		return $return;
	}

	/**
	 * Builds a data array containing all view_extras and attribute tags per element in the
	 * node path. Then it extracts all tags in the node path.
	 * Looping of the node path tags and the node path itself to calculate a single sheet
	 * of view_extra config values.
	 *
	 * @param type $node
	 * @return type
	 */
	private function build_view_extras( $node )
	{
		$return = array();

		// build data struture
		$data = $this->build_data_structure( $node );
		
		// Reads all view_extra tags for the current path (data structure)
		// So node tags are all tags on the node plus the tags configured
		// on the node path
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