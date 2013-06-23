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
}

?>