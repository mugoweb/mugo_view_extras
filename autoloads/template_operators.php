<?php

class MugoViewExtrasTemplateOperators
{
	function operatorList()
	{
		return array(
			'parse_includes'
		);
	}

	function namedParameterPerOperator()
	{
		return true;
	}

	function namedParameterList()
	{
		return array(
				'parse_includes' => array( 'context_node_id' => array( 'type' => 'int', 'required' => true ) )
		);
	}

	/**
	 * @param $tpl
	 * @param $operatorName
	 * @param $operatorParameters
	 * @param $rootNamespace
	 * @param $currentNamespace
	 * @param $operatorValue
	 * @param $namedParameters
	 */
	function modify( $tpl,
	                 $operatorName,
	                 $operatorParameters,
	                 $rootNamespace,
	                 $currentNamespace,
	                 &$operatorValue,
	                 $namedParameters )
	{		
		switch ( $operatorName )
		{
			case 'parse_includes':
			{
				eZDebug::addTimingPoint( 'Before parse_includes' );

				$view_extras_obj = new MugoViewExtras();
				$view_extras = $view_extras_obj->get_view_extras( $namedParameters[ 'context_node_id' ] );

				// search and replaces all includes
				preg_match_all( '#<include(?: ttl="(?P<ttl>.*?)"|) id="(?P<id>.*?)"\/>#', $operatorValue, $includes );
				
				if( !empty( $includes[0] ) )
				{
					foreach( $includes[0] as $index => $include )
					{
						$id  = $includes[ 'id' ][ $index ];
						// Consider to read ttl from view_extra template instead
						$ttl = $includes[ 'ttl' ][ $index ] !== '' ? $includes[ 'ttl' ][ $index ] : null;
						
						if( $id )
						{
							$operatorValue = str_replace(
								$include,
								$this->parseIncludeHtml( $id, $view_extras, $ttl ),
								$operatorValue
							);
						}
					}
				}
				eZDebug::addTimingPoint( 'After parse_includes' );
			}
			break;

			default:
		}
	}

	/**
	 * @param $id
	 * @param $view_extras
	 * @param $ttl
	 * @return array|string
	 */
	protected function parseIncludeHtml( $id, $view_extras, $ttl )
	{
		$return = '';

		if( isset( $view_extras[ $id ] ) && (int) $view_extras[ $id ] )
		{
			$tpl = eZTemplate::factory();
			$tpl->setVariable( 'node_id', $view_extras[ $id ] );
			$tpl->setVariable( 'view_extra_name', $id );
			if( $ttl !== null )
			{
				$tpl->setVariable( 'ttl', $ttl );
			}

			// Set additional DesignKeys
			$res = eZTemplateDesignResource::instance();
			$res->setKeys( array( array( 'view_extra_node_id', $view_extras[ $id ] ) ) );
			$res->setKeys( array( array( 'view_extra_node_name', $id ) ) );

			$return = $tpl->fetch( 'design:modules/mugo_view_extras/extra_cached.tpl' );
		}
		
		return $return;
	}
}
