<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation of catalog session pinned section for HTML clients.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Catalog_Session_Pinned_Default
	extends Client_Html_Abstract
{
	/** client/html/catalog/session/pinned/default/subparts
	 * List of HTML sub-clients rendered within the catalog session pinned section
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
	private $_subPartPath = 'client/html/catalog/session/pinned/default/subparts';
	private $_subPartNames = array();
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
		$context = $this->_getContext();
		$session = $context->getSession();

		$html = null;
		$config = $context->getConfig()->get( 'client/html/catalog/session/pinned', array() );
		$key = $this->_getParamHash( array(), $uid . ':catalog:session-pinned-body', $config );

		if( ( $html = $session->get( $key ) ) === null )
		{
			$view = $this->_setViewParams( $this->getView(), $tags, $expire );

			$output = '';
			foreach( $this->_getSubClients() as $subclient ) {
				$output .= $subclient->setView( $view )->getBody( $uid, $tags, $expire );
			}
			$view->pinnedBody = $output;

			/** client/html/catalog/session/pinned/default/template-body
			 * Relative path to the HTML body template of the catalog session pinned client.
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
			 * @see client/html/catalog/session/pinned/default/template-header
			 */
			$tplconf = 'client/html/catalog/session/pinned/default/template-body';
			$default = 'catalog/session/pinned-body-default.html';

			$html = $view->render( $this->_getTemplate( $tplconf, $default ) );

			$cached = $session->get( 'arcavias/catalog/session/pinned/cache', array() ) + array( $key => true );
			$session->set( 'arcavias/catalog/session/pinned/cache', $cached );
			$session->set( $key, $html );
		}

		return $html;
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
		$context = $this->_getContext();
		$session = $context->getSession();

		$config = $context->getConfig()->get( 'client/html/catalog/session/pinned', array() );
		$key = $this->_getParamHash( array(), $uid . ':catalog:session-pinned-header', $config );

		if( ( $html = $session->get( $key ) ) === null )
		{
			$view = $this->_setViewParams( $this->getView(), $tags, $expire );

			$output = '';
			foreach( $this->_getSubClients() as $subclient ) {
				$output .= $subclient->setView( $view )->getHeader( $uid, $tags, $expire );
			}
			$view->pinnedHeader = $output;

			/** client/html/catalog/session/pinned/default/template-header
			 * Relative path to the HTML header template of the catalog session pinned client.
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
			 * @see client/html/catalog/session/pinned/default/template-body
			 */
			$tplconf = 'client/html/catalog/session/pinned/default/template-header';
			$default = 'catalog/session/pinned-header-default.html';

			$html = $view->render( $this->_getTemplate( $tplconf, $default ) );

			$cached = $session->get( 'arcavias/catalog/session/pinned/cache', array() ) + array( $key => true );
			$session->set( 'arcavias/catalog/session/pinned/cache', $cached );
			$session->set( $key, $html );
		}

		return $html;
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
		return $this->_createSubClient( 'catalog/session/pinned/' . $type, $name );
	}


	/**
	 * Processes the input, e.g. store given values.
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 */
	public function process()
	{
		$refresh = false;
		$view = $this->getView();
		$context = $this->_getContext();
		$session = $context->getSession();
		$str = $session->get( 'arcavias/catalog/session/pinned/list' );

		if( ( $pinned = @unserialize( $str ) ) === false ) {
			$pinned = array();
		}

		switch( $view->param( 'pin-action' ) )
		{
			case 'add':

				foreach( (array) $view->param( 'pin-id', array() ) as $id ) {
					$pinned[$id] = $id;
				}

				/** client/html/catalog/session/pinned/default/maxitems
				 * Maximum number of products displayed in the "pinned" section
				 *
				 * This option limits the number of products that are shown in the
				 * "pinned" section after the users added the product to their list
				 * of pinned products. It must be a positive integer value greater
				 * than 0.
				 *
				 * Note: The higher the value is the more data has to be transfered
				 * to the client each time the user loads a page with the list of
				 * pinned products.
				 *
				 * @param integer Number of products
				 * @since 2014.09
				 * @category User
				 * @category Developer
				 */
				$max = $context->getConfig()->get( 'client/html/catalog/session/pinned/default/maxitems', 50 );

				$pinned = array_slice( $pinned, -$max, $max, true );
				$refresh = true;
				break;

			case 'delete':

				foreach( (array) $view->param( 'pin-id', array() ) as $id ) {
					unset( $pinned[$id] );
				}

				$refresh = true;
				break;
		}


		if( $refresh )
		{
			$session->set( 'arcavias/catalog/session/pinned/list', serialize( $pinned ) );

			foreach( $session->get( 'arcavias/catalog/session/pinned/cache', array() ) as $key => $value ) {
				$session->set( $key, null );
			}
		}

		parent::process();
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
			$expire = null;
			$tags = $items = array();
			$context = $this->_getContext();
			$config = $context->getConfig();
			$session = $context->getSession();

			$default = array( 'media', 'price', 'text' );
			$domains = $config->get( 'client/html/catalog/domains', $default );

			/** client/html/catalog/detail/pinned/domains
			 * A list of domain names whose items should be available in the pinned view template for the product
			 *
			 * The templates rendering product details usually add the images,
			 * prices and texts, etc. associated to the product
			 * item. If you want to display additional or less content, you can
			 * configure your own list of domains (attribute, media, price, product,
			 * text, etc. are domains) whose items are fetched from the storage.
			 * Please keep in mind that the more domains you add to the configuration,
			 * the more time is required for fetching the content!
			 *
			 * @param array List of domain names
			 * @since 2014.09
			 * @category Developer
			 * @see client/html/catalog/domains
			 * @see client/html/catalog/list/domains
			 * @see client/html/catalog/detail/domains
			 */
			$domains = $config->get( 'client/html/catalog/detail/pinned/domains', $default );

			$str = $session->get( 'arcavias/catalog/session/pinned/list', '' );

			if( ( $pinned = @unserialize( $str ) ) === false ) {
				$pinned = array();
			}

			$manager = MShop_Factory::createManager( $context, 'product' );
			$search = $manager->createSearch( true );
			$search->setConditions( $search->compare( '==', 'product.id', $pinned ) );
			$result = $manager->searchItems( $search, $domains );

			foreach( array_reverse( $pinned ) as $id )
			{
				if( isset( $result[$id] ) ) {
					$items[$id] = $result[$id];
				}
			}

			$view->pinnedProductItems = $items;
			$view->pinnedParams = $this->_getClientParams( $view->param() );

			$this->_cache = $view;
		}

		return $this->_cache;
	}
}