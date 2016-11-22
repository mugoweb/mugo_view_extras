<?php 

class MugoViewExtrasFetchFunctions
{
	public static function fetch_function_get_view_extras( $node_id = null, $set_default = false, $set = 'standard' )
	{
		$return = array();
		$ini   = eZINI::instance( 'mugo_view_extras.ini' );
		$class = $ini->variable( 'MugoViewExtras', 'HandlerClass' );

		if( class_exists( $class ) )
		{
			$handler = new $class;
		
			if( $handler instanceof MugoViewExtras )
			{
				eZDebug::accumulatorStart( 'View Extra building' );
				$return = $handler->get_view_extras( $node_id, $set_default, $set );
				eZDebug::accumulatorStop( 'View Extra building' );
			}
		}
		
		return array( 'result' => $return );
	}
	public static function node_tags( $node_id )
	{
		$return = array();
		
		$ini   = eZINI::instance( 'mugo_view_extras.ini' );
		$class = $ini->variable( 'MugoViewExtras', 'HandlerClass' );

		if( class_exists( $class ) )
		{
			$handler = new $class;
		
			if( $handler instanceof MugoViewExtras )
			{
				$return = $handler->get_node_tags( $node_id );
			}
		}
		return array( 'result' => $return );
	}
	
	public static function resolve_tag( $tag )
	{
		// Assuming interger tags can get resolved
		$intTag = (int) $tag;
		if( $intTag && $intTag == $tag )
		{
			// eztags lookup
			if( class_exists( 'eZTagsObject' ) )
			{
				$result = eZTagsObject::fetch( $intTag );

				if( $result instanceof eZTagsObject )
				{
					$tag = $result->Keyword . ' (' . $intTag . ')';
				}
			}
		}
		
		return array( 'result' => $tag );
	}
}
