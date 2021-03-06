<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of catalog detail basket section for HTML clients.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Catalog_Detail_Basket_Default
	extends Client_Html_Abstract
{
	/** client/html/catalog/detail/basket/default/subparts
	 * List of HTML sub-clients rendered within the catalog detail basket section
	 *
	 * The output of the frontend is composed of the code generated by the HTML
	 * clients. Each HTML client can consist of serveral (or none) sub-clients
	 * that are responsible for rendering certain sub-parts of the output. The
	 * sub-clients can contain HTML clients themselves and therefore a
	 * hierarchical tree of HTML clients is composed. Each HTML client creates
	 * the output that is placed inside the container of its parent.
	 *
	 * At first, always the HTML code generated by the parent is printed, then
	 * the HTML code of its sub-clients. The order of the HTML sub-clients
	 * determines the order of the output of these sub-clients inside the parent
	 * container. If the configured list of clients is
	 *
	 *  array( "subclient1", "subclient2" )
	 *
	 * you can easily change the order of the output by reordering the subparts:
	 *
	 *  client/html/<clients>/subparts = array( "subclient1", "subclient2" )
	 *
	 * You can also remove one or more parts if they shouldn't be rendered:
	 *
	 *  client/html/<clients>/subparts = array( "subclient1" )
	 *
	 * As the clients only generates structural HTML, the layout defined via CSS
	 * should support adding, removing or reordering content by a fluid like
	 * design.
	 *
	 * @param array List of sub-client names
	 * @since 2014.03
	 * @category Developer
	 */
	private $_subPartPath = 'client/html/catalog/detail/basket/default/subparts';

	/** client/html/catalog/detail/basket/selection/name
	 * Name of the selection part used by the catalog detail basket client implementation
	 *
	 * Use "Myname" if your class is named "Client_Html_Catalog_Detail_Basket_Selection_Myname".
	 * The name is case-sensitive and you should avoid camel case names like "MyName".
	 *
	 * @param string Last part of the client class name
	 * @since 2014.03
	 * @category Developer
	 */

	/** client/html/catalog/detail/basket/attribute/name
	 * Name of the attribute part used by the catalog detail basket client implementation
	 *
	 * Use "Myname" if your class is named "Client_Html_Catalog_Detail_Basket_Attribute_Myname".
	 * The name is case-sensitive and you should avoid camel case names like "MyName".
	 *
	 * @param string Last part of the client class name
	 * @since 2014.03
	 * @category Developer
	 */
	private $_subPartNames = array( 'selection', 'attribute' );
	private $_cache;


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return string HTML code
	 */
	public function getBody( $uid = '', array &$tags = array(), &$expire = null )
	{
		$view = $this->_setViewParams( $this->getView(), $tags, $expire );

		$html = '';
		foreach( $this->_getSubClients() as $subclient ) {
			$html .= $subclient->setView( $view )->getBody( $uid, $tags, $expire );
		}
		$view->basketBody = $html;

		/** client/html/catalog/detail/basket/default/template-body
		 * Relative path to the HTML body template of the catalog detail basket client.
		 *
		 * The template file contains the HTML code and processing instructions
		 * to generate the result shown in the body of the frontend. The
		 * configuration string is the path to the template file relative
		 * to the layouts directory (usually in client/html/layouts).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "default" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "default"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating code for the HTML page body
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/catalog/detail/basket/default/template-header
		 */
		$tplconf = 'client/html/catalog/detail/basket/default/template-body';
		$default = 'catalog/detail/basket-body-default.html';

		return $view->render( $this->_getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return string String including HTML tags for the header
	 */
	public function getHeader( $uid = '', array &$tags = array(), &$expire = null )
	{
		$view = $this->_setViewParams( $this->getView(), $tags, $expire );

		$html = '';
		foreach( $this->_getSubClients() as $subclient ) {
			$html .= $subclient->setView( $view )->getHeader( $uid, $tags, $expire );
		}
		$view->basketHeader = $html;

		/** client/html/catalog/detail/basket/default/template-header
		 * Relative path to the HTML header template of the catalog detail basket client.
		 *
		 * The template file contains the HTML code and processing instructions
		 * to generate the HTML code that is inserted into the HTML page header
		 * of the rendered page in the frontend. The configuration string is the
		 * path to the template file relative to the layouts directory (usually
		 * in client/html/layouts).
		 *
		 * You can overwrite the template file configuration in extensions and
		 * provide alternative templates. These alternative templates should be
		 * named like the default one but with the string "default" replaced by
		 * an unique name. You may use the name of your project for this. If
		 * you've implemented an alternative client class as well, "default"
		 * should be replaced by the name of the new class.
		 *
		 * @param string Relative path to the template creating code for the HTML page head
		 * @since 2014.03
		 * @category Developer
		 * @see client/html/catalog/detail/basket/default/template-body
		 */
		$tplconf = 'client/html/catalog/detail/basket/default/template-header';
		$default = 'catalog/detail/basket-header-default.html';

		return $view->render( $this->_getTemplate( $tplconf, $default ) );
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return Client_Html_Interface Sub-client object
	 */
	public function getSubClient( $type, $name = null )
	{
		return $this->_createSubClient( 'catalog/detail/basket/' . $type, $name );
	}


	/**
	 * Returns the list of sub-client names configured for the client.
	 *
	 * @return array List of HTML client names
	 */
	protected function _getSubClientNames()
	{
		return $this->_getContext()->getConfig()->get( $this->_subPartPath, $this->_subPartNames );
	}


	/**
	 * Sets the necessary parameter values in the view.
	 *
	 * @param MW_View_Interface $view The view object which generates the HTML output
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return MW_View_Interface Modified view object
	 */
	protected function _setViewParams( MW_View_Interface $view, array &$tags = array(), &$expire = null )
	{
		if( !isset( $this->_cache ) )
		{
			$config = $this->_getContext()->getConfig();

			/** client/html/catalog/detail/stock/enable
			 * Enables or disables displaying product stock levels in product detail view
			 *
			 * This configuration option allows shop owners to display product
			 * stock levels for each product in the detail views or to disable
			 * fetching product stock information.
			 *
			 * The stock information is fetched via AJAX and inserted via Javascript.
			 * This allows to cache product items by leaving out such highly
			 * dynamic content like stock levels which changes with each order.
			 *
			 * @param boolean Value of "1" to display stock levels, "0" to disable displaying them
			 * @since 2014.03
			 * @category User
			 * @category Developer
			 * @see client/html/catalog/list/stock/enable
			 * @see client/html/catalog/stock/url/target
			 * @see client/html/catalog/stock/url/controller
			 * @see client/html/catalog/stock/url/action
			 * @see client/html/catalog/stock/url/config
			 */
			if( isset( $view->detailProductItem ) && $config->get( 'client/html/catalog/detail/stock/enable', true ) === true )
			{
				$stockTarget = $config->get( 'client/html/catalog/stock/url/target' );
				$stockController = $config->get( 'client/html/catalog/stock/url/controller', 'catalog' );
				$stockAction = $config->get( 'client/html/catalog/stock/url/action', 'stock' );
				$stockConfig = $config->get( 'client/html/catalog/stock/url/config', array() );

				$prodIds = array_keys( $view->detailProductItem->getRefItems( 'product', null, 'default' ) );
				array_unshift( $prodIds, $view->detailProductItem->getId() );
				$params = array( 's_prodid' => $prodIds );

				$view->basketStockUrl = $view->url( $stockTarget, $stockController, $stockAction, $params, array(), $stockConfig );
				$view->basketProductIds = $prodIds;
			}

			$this->_cache = $view;
		}

		return $this->_cache;
	}
}