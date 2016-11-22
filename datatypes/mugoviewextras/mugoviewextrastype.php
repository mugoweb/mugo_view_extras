<?php

/**
 * @author pkamps
 * TODO: consider to use the object ids instead of the node ids.
 * 
 */
class MugoViewExtrasType extends eZDataType
{

	const DATA_TYPE_STRING = 'mugoviewextras';

	/*!
	  Construtor
	*/
	function __construct()
	{
		parent::__construct( self::DATA_TYPE_STRING, 'Mugo View Extras', array( 'serialize_supported' => true ) );
	}
		
	function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
	{
		if( $originalContentObjectAttribute->attribute( 'data_text' ) != null )
		{
			$contentObjectAttribute->setAttribute( 'data_text', $originalContentObjectAttribute->attribute( 'data_text' ) );				
		}
	}

	/*!
	 Validates input on content object level
	 \return eZInputValidator::STATE_ACCEPTED or eZInputValidator::STATE_INVALID if
			 the values are accepted or not
	*/
	function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
	{
		return eZInputValidator::STATE_ACCEPTED;
	}

	/**
     * Set parameters from post data
     *
     * @param eZHTTPTool $http
     * @param string $base
     * @param eZContentObjectAttribute $contentObjectAttribute
     */
	function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
	{
		$data = $_REQUEST[ $base . '_' . $contentObjectAttribute->attribute( 'id' ) . '_view_extra' ];
		$contentObjectAttribute->setAttribute( 'data_text', serialize( $data ) );
		return true;
	}

	/*!
	 Returns the content.
	*/
	function objectAttributeContent( $contentObjectAttribute )
	{
		return unserialize( $contentObjectAttribute->attribute( 'data_text' ) );
	}

    function customObjectAttributeHTTPAction( $http, $action, $contentObjectAttribute, $parameters )
    {
        switch( $action )
        {
            case "browse_related_node":
            {
                $customActionButton = $http->postVariable( 'CustomActionButton' );
                $viewExtraCustomActionButton = $customActionButton[$contentObjectAttribute->attribute( 'id' ) . '_browse_related_node'];
                $tagID = key( $viewExtraCustomActionButton );
                $fieldID = key( $viewExtraCustomActionButton[$tagID] );

                $module = $parameters['module'];
                $redirectionURI = $parameters['current-redirection-uri'];

                $mugoViewExtrasINI = eZINI::instance( 'mugo_view_extras.ini' );

                $browseParameters = array( 'action_name' => 'AddRelatedNodeViewExtras',
                                           'browse_custom_action' => array( 'name' => 'CustomActionButton[' . $contentObjectAttribute->attribute( 'id' ) . '_set_related_node]',
                                                                            'value' => $tagID . '|' . $fieldID ),
                                           // If we don't set this, we will lose the attribute content when we return from browse mode
                                           'persistent_data' => array( 'HasObjectInput' => 0 ),
                                           'from_page' => $redirectionURI );

                // Add browse mode start node if configured in the INI file
                if( $mugoViewExtrasINI->hasVariable( $fieldID . '_' . $tagID . '_field_settings', 'BrowseModeStartNode' ) )
                {
                    $browseParameters['start_node'] = $mugoViewExtrasINI->variable( $fieldID . '_' . $tagID . '_field_settings', 'BrowseModeStartNode' );
                }
                elseif( $mugoViewExtrasINI->hasVariable( $fieldID . '_default_field_settings', 'BrowseModeStartNode' ) )
                {
                    $browseParameters['start_node'] = $mugoViewExtrasINI->variable( $fieldID . '_default_field_settings', 'BrowseModeStartNode' );
                }

                // Limit the content classes that can be selected if configured in the INI file
                if( $mugoViewExtrasINI->hasVariable( $fieldID . '_' . $tagID . '_field_settings', 'BrowseModeAllowedClasses' ) )
                {
                    $browseParameters['class_array'] = $mugoViewExtrasINI->variable( $fieldID . '_' . $tagID . '_field_settings', 'BrowseModeAllowedClasses' );
                }
                elseif( $mugoViewExtrasINI->hasVariable( $fieldID . '_default_field_settings', 'BrowseModeAllowedClasses' ) )
                {
                    $browseParameters['class_array'] = $mugoViewExtrasINI->variable( $fieldID . '_default_field_settings', 'BrowseModeAllowedClasses' );
                }

                eZContentBrowse::browse( $browseParameters,
                                         $module );
            } break;
            
            case "set_related_node":
            {
                if ( !$http->hasPostVariable( 'BrowseCancelButton' ) )
                {
                    // Find out which view extra field to update
                    $customActionButton = $http->postVariable( 'CustomActionButton' );
                    $values = $customActionButton[$contentObjectAttribute->attribute( 'id' ) . '_set_related_node'];
                    $valuesArray = explode( '|', $values );
                    list( $tagID, $fieldID ) = $valuesArray;
                    
                    // Add the node as the view extra value
                    $selectedNodeIDArray = $http->postVariable( "SelectedNodeIDArray" );
                    $content = $contentObjectAttribute->attribute( 'content' );
                    if( $selectedNodeIDArray !== null )
                    {
                        // Only one node is allowed to be selected
                        $nodeID = $selectedNodeIDArray[0];
                        if ( !is_numeric( $nodeID ) )
                        {
                            eZDebug::writeError( "Related node ID (nodeID): '$nodeID', is not a numeric value.", __METHOD__ );
                            return;
                        }
                        $content[$tagID][$fieldID] = $nodeID;
                        $contentObjectAttribute->setContent( $content );
                        $contentObjectAttribute->store();
                    }
                }
            }

			case 'add_tag':
			{
				if( $_REQUEST[ 'newtagname' ] )
				{
					$content = $contentObjectAttribute->attribute( 'content' );

					$content[ $_REQUEST[ 'newtagname' ] ] = array();

					$contentObjectAttribute->setContent( $content );
					$contentObjectAttribute->store();
				}
			}
			break;

			case 'remove_tag':
			{
				if( $_REQUEST[ 'currenttag' ] )
				{
					$content = $contentObjectAttribute->attribute( 'content' );

					unset( $content[ $_REQUEST[ 'currenttag' ] ] );

					$contentObjectAttribute->setContent( $content );
					$contentObjectAttribute->store();
				}
			}
			break;

            default:
            {
                eZDebug::writeError( "Unknown custom HTTP action: " . $action, "eZObjectRelationType" );
            } break;
        }
    }
	/*!
	 Returns the meta data used for storing search indeces.
	*/
	function metaData( $contentObjectAttribute )
	{
		return null;
	}
	
	function toString( $contentObjectAttribute )
	{
		return serialize( $contentObjectAttribute->attribute( 'data_text' ) );
	}

	function fromString( $contentObjectAttribute, $string )
	{
		// not yet implemented
		//return $contentObjectAttribute->attribute( 'data_text' );
	}

	/*!
	 Returns the value as it will be shown if this attribute is used in the object name pattern.
	*/
	function title( $contentObjectAttribute, $name = null )
	{
		return 'View Extras';
	}
		
	function hasObjectAttributeContent( $contentObjectAttribute )
	{
		return $contentObjectAttribute->attribute( 'data_text' ) !== null;
	}

	/**
	 * @param eZContentObjectAttribute $contentObjectAttribute
	 * @return bool
	 */
	function storeObjectAttribute( $contentObjectAttribute )
	{
		$contentObjectAttribute->setAttribute( 'data_text', serialize( $contentObjectAttribute->content() ) );
		return true;
	}

	/**
	 * @param $http
	 * @param $base
	 * @param $classAttribute
	 * @return int
	 */
	function validateClassAttributeHTTPInput( $http, $base, $classAttribute )
	{
		return eZInputValidator::STATE_ACCEPTED;
	}

	function isIndexable()
	{
		return true;
	}
}

eZDataType::register( MugoViewExtrasType::DATA_TYPE_STRING, 'MugoViewExtrasType' );
