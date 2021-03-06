<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Attribute cache for CSV imports
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Product_Import_Csv_Cache_Attribute_Default
	extends Controller_Jobs_Product_Import_Csv_Cache_Abstract
	implements Controller_Jobs_Product_Import_Csv_Cache_Interface
{
	private $_attributes = array();


	/**
	 * Initializes the object
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 */
	public function __construct( MShop_Context_Item_Interface $context )
	{
		parent::__construct( $context );

		$manager = MShop_Factory::createManager( $context, 'attribute' );
		$result = $manager->searchItems( $manager->createSearch() );

		foreach( $result as $id => $item ) {
			$this->_attributes[ $item->getCode() ][ $item->getType() ] = $item;
		}
	}


	/**
	 * Returns the attribute item for the given code and type
	 *
	 * @param string $code Attribute code
	 * @param string|null $type Attribute type
	 * @return MShop_Attribute_Item_Interface|null Attribute object or null if not found
	 */
	public function get( $code, $type = null )
	{
		if( isset( $this->_attributes[$code] ) && isset( $this->_attributes[$code][$type] ) ) {
			return $this->_attributes[$code][$type];
		}

		$manager = MShop_Factory::createManager( $this->_getContext(), 'attribute' );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.code', $code ),
			$search->compare( '==', 'attribute.type.code', $type ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) !== false )
		{
			$this->_attributes[$code][$type] = $item;
			return $item;
		}
	}


	/**
	 * Adds the attribute item to the cache
	 *
	 * @param MShop_Common_Item_Interface $item Attribute object
	 */
	public function set( MShop_Common_Item_Interface $item )
	{
		$code = $item->getCode();

		if( !isset( $this->_attributes[$code] ) || !is_array( $this->_attributes[$code] ) ) {
			$this->_attributes[$code] = array();
		}

		$this->_attributes[$code][ $item->getType() ] = $item;
	}
}