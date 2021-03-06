<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Attribute processor for CSV imports
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Product_Import_Csv_Processor_Attribute_Default
	extends Controller_Jobs_Product_Import_Csv_Processor_Abstract
	implements Controller_Jobs_Product_Import_Csv_Processor_Interface
{
	private $_cache;
	private $_listTypes;


	/**
	 * Initializes the object
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 * @param array $mapping Associative list of field position in CSV as key and domain item key as value
	 * @param Controller_Jobs_Product_Import_Csv_Processor_Interface $object Decorated processor
	 */
	public function __construct( MShop_Context_Item_Interface $context, array $mapping,
		Controller_Jobs_Product_Import_Csv_Processor_Interface $object = null )
	{
		parent::__construct( $context, $mapping, $object );

		/** controller/jobs/product/import/csv/processor/attribute/listtypes
		 * Names of the product list types for attributes that are updated or removed
		 *
		 * If you want to associate attribute items manually via the administration
		 * interface to products and don't want these to be touched during the
		 * import, you can specify the product list types for these attributes
		 * that shouldn't be updated or removed.
		 *
		 * @param array|null List of product list type names or null for all
		 * @since 2015.05
		 * @category Developer
		 * @category User
		 * @see controller/jobs/product/import/csv/domains
		 * @see controller/jobs/product/import/csv/processor/media/listtypes
		 * @see controller/jobs/product/import/csv/processor/product/listtypes
		 * @see controller/jobs/product/import/csv/processor/price/listtypes
		 * @see controller/jobs/product/import/csv/processor/text/listtypes
		 */
		$this->_listTypes = $context->getConfig()->get( 'controller/jobs/product/import/csv/processor/attribute/listtypes');

		$this->_cache = $this->_getCache( 'attribute' );
	}


	/**
	 * Saves the attribute related data to the storage
	 *
	 * @param MShop_Product_Item_Interface $product Product item with associated items
	 * @param array $data List of CSV fields with position as key and data as value
	 * @return array List of data which hasn't been imported
	 */
	public function process( MShop_Product_Item_Interface $product, array $data )
	{
		$context = $this->_getContext();
		$manager = MShop_Factory::createManager( $context, 'attribute' );
		$listManager = MShop_Factory::createManager( $context, 'product/list' );

		$manager->begin();

		try
		{
			$pos = 0;
			$delete = $attrcodes = array();
			$map = $this->_getMappedData( $data );
			$listItems = $product->getListItems( 'attribute', $this->_listTypes );

			foreach( $listItems as $listId => $listItem )
			{
				if( isset( $map[$pos] ) )
				{
					if( !isset( $map[$pos]['attribute.code'] ) || !isset( $map[$pos]['attribute.type'] ) )
					{
						unset( $map[$pos] );
						continue;
					}

					$refItem = $listItem->getRefItem();

					if( $refItem !== null && $map[$pos]['attribute.code'] === $refItem->getCode()
						&& $map[$pos]['attribute.type'] === $refItem->getType()
						&& ( !isset( $map[$pos]['product.list.type'] ) || isset( $map[$pos]['product.list.type'] )
						&& $map[$pos]['product.list.type'] === $listItem->getType() )
					) {
						$pos++;
						continue;
					}
				}

				$listItems[$listId] = null;
				$delete[] = $listId;
				$pos++;
			}

			$listManager->deleteItems( $delete );

			foreach( $map as $pos => $list )
			{
				if( $list['attribute.code'] === '' || $list['attribute.type'] === '' || isset( $list['product.list.type'] )
					&& $this->_listTypes !== null && !in_array( $list['product.list.type'], (array) $this->_listTypes )
				) {
					continue;
				}

				$attrItem = $this->_getAttributeItem( $list['attribute.code'], $list['attribute.type'] );
				$attrItem->fromArray( $list );
				$manager->saveItem( $attrItem );

				if( ( $listItem = array_shift( $listItems ) ) === null ) {
					$listItem = $listManager->createItem();
				}

				$typecode = ( isset( $list['product.list.type'] ) ? $list['product.list.type'] : 'default' );
				$list['product.list.typeid'] = $this->_getTypeId( 'product/list/type', 'attribute', $typecode );
				$list['product.list.refid'] = $attrItem->getId();
				$list['product.list.parentid'] = $product->getId();
				$list['product.list.domain'] = 'attribute';

				$listItem->fromArray( $this->_addListItemDefaults( $list, $pos ) );
				$listManager->saveItem( $listItem );
			}

			$remaining = $this->_getObject()->process( $product, $data );

			$manager->commit();
		}
		catch( Exception $e )
		{
			$manager->rollback();
			throw $e;
		}

		return $remaining;
	}


	/**
	 * Returns the attribute item for the given code and type
	 *
	 * @param string $code Attribute code
	 * @param string $type Attribute type
	 * @return MShop_Attribute_Item_Interface Attribute item object
	 */
	protected function _getAttributeItem( $code, $type )
	{
		if( ( $item = $this->_cache->get( $code, $type ) ) === null )
		{
			$manager = MShop_Factory::createManager( $this->_getContext(), 'attribute' );

			$item = $manager->createItem();
			$item->setTypeId( $this->_getTypeId( 'attribute/type', 'product', $type ) );
			$item->setCode( $code );
			$item->setLabel( $type . ' ' . $code );
			$item->setStatus( 1 );

			$manager->saveItem( $item );

			$this->_cache->set( $item );
		}

		return $item;
	}
}
