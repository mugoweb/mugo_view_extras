<?php
/*
 * Indexing logic for the datatype mugoviewextra - ezfind uses it
 * 
 */

class ezfSolrDocumentFieldMugoViewExtras extends ezfSolrDocumentFieldBase
{
	/**
	 * (non-PHPdoc)
	 * @see ezfSolrDocumentFieldBase::getData()
	 */	
	public function getData()
	{
		$values = array();

		$content = $this->ContentObjectAttribute->attribute( 'content' );

		if( !empty( $content ) )
		{
			foreach( $content as $tag )
			{
				foreach( $tag as $id => $value )
				{
					if( trim( $value ) )
					{
						$values[] = trim( $value );
					}
				}
			}
		}
		
		return array( 'attr_view_extras____ms' => array_unique( $values ) );
	}
}

?>