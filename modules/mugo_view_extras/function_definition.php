<?php
$FunctionList = array();

$FunctionList[ 'get' ] = array(
                           'name' => 'get_view_extras',
                           'call_method' => array( 
                                                  'include_file' => 'extension/mugo_view_extras/classes/MugoViewExtrasFetchFunctions.php',
                                                  'class' => 'MugoViewExtrasFetchFunctions',
                                                  'method' => 'fetch_function_get_view_extras' ),
                           'parameter_type' => 'standard',
                           'parameters' => array(
                                                 array( 'name'     => 'node_id',
                                                        'type'     => 'integer',
                                                        'required' => false
                                                 ),
                                                 array( 'name'     => 'set_default',
                                                        'type'     => 'boolean',
                                                        'required' => false
                                                 ),
                                                 array( 'name'     => 'set',
                                                        'type'     => 'string',
                                                        'required' => false
                                                 )
                                                 )
                                     );

?>