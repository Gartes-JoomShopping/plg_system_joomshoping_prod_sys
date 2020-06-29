<?php
/**
 * @package    plg_system_joomshoping_prod_sys
 *
 * @author     oleg <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
	use Joomla\CMS\Factory;
	use Joomla\CMS\Plugin\CMSPlugin;

/**
 * Plg_system_joomshoping_prod_sys plugin.
 *
 * @package   plg_system_joomshoping_prod_sys
 * @since     1.0.0
 */
class plgSystemJoomshoping_prod_sys extends CMSPlugin
{
	/**
	 * Application object
	 *
	 * @var    CMSApplication
	 * @since  1.0.0
	 */
	protected $app;

	/**
	 * Database object
	 *
	 * @var    JDatabaseDriver
	 * @since  1.0.0
	 */
	protected $db;

	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 * @since  1.0.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * onAfterInitialise.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function onAfterInitialise()
	{

	}

	/**
	 * onAfterRoute.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function onAfterRoute()
	{
		$ip = $this->app->input->server->get('REMOTE_ADDR', '', '');
		$uri = \Joomla\CMS\Uri\Uri::getInstance( $uri = 'SERVER' );
		if( $ip == '178.209.70.115' )
		{
			$DEV = true ;


			
			if( $this->app->input->get('controller' , false )== 'product'  && $this->app->input->get('task' , false )== 'view'  )
			{
				$category_id = $this->app->input->get('category_id' , false , 'INT');
				$product_id = $this->app->input->get('product_id' , false , 'INT');
				$query = $this->db->getQuery(true);
				$query->select('*')->from( $this->db->quoteName('#__gnz11_core_redirect'))
					->where( $this->db->quoteName('category_id') .'='.$this->db->quote($category_id) )
					->where( $this->db->quoteName('product_id') .'='.$this->db->quote($product_id) );
				$this->db->setQuery($query);
				$res = $this->db->loadAssoc();
				if( $res )
				{
					$product_link = \JURI::root(true).SEFLink('index.php?option=com_jshopping&controller=product&task=view&category_id='
							. $res['category_id_redirect']
							.'&product_id=' . $res['product_id_redirect']  ,2);
					$this->app->redirect( $product_link ,  301);
				}#END IF



/*

				if( $this->app->input->get('product_id' ,false , 'INT') != 669 )
				{
//
				}#END IF*/




			}#END IF

//			echo'<pre>';print_r( $this->app->input->get('product_id' ,false , 'INT') );echo'</pre>'.__FILE__.' '.__LINE__;
//			echo'<pre>';print_r( $this->app->input->get('controller' , false ) );echo'</pre>'.__FILE__.' '.__LINE__;
//			die(__FILE__ .' '. __LINE__ );
			
			
//
//			$domain = \JRoute::_($product_link, true, ($force_ssl > 0 ? 1:-1) ) ;

			//		$this->app->input->set( 'product_id' , 669 );
//			echo'<pre>';print_r( $domain );echo'</pre>'.__FILE__.' '.__LINE__;
//			echo'<pre>';print_r( $product_link );echo'</pre>'.__FILE__.' '.__LINE__;


		}#END IF



	}

	/**
	 * onAfterDispatch.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function onAfterDispatch()
	{

	}

	/**
	 * onAfterRender.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function onAfterRender()
	{



		// Access to plugin parameters
		$sample = $this->params->get('sample', '42');


	}



	/**
	 * OnAfterCompress.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function onAfterCompress()
	{

	}

	/**
	 * onAfterRespond.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function onAfterRespond()
	{

	}


	public function onAfterLoadShopParamsAdmin(){
		$__group = 	$this->app->input->get('group' , false );
		$__plugin = $this->app->input->get('plugin' , false );
		if(  $__group != 'system'  &&  $__plugin != 'joomshoping_prod_sys' ) return false; #END IF
		return $this->onAjaxJoomshoping_prod_sys();
	}

	public function onLoadEditProduct(){



		$doc = Factory::getDocument();
		$doc->addScript ( '/plugins/system/joomshoping_prod_sys/Assets/js/joomshoping_prod_sys.js' );
		$doc->addStyleSheet (  '/plugins/system/joomshoping_prod_sys/Assets/css/joomshoping_prod_sys.css' );


		\Joomla\CMS\Toolbar\ToolbarHelper::divider();
		$bar = \Joomla\CMS\Toolbar\Toolbar::getInstance('toolbar');
		$_title = "Резервные копии" ;
		$title = 'Backup' ;
		$dhtml = "<a id='btn-a-backup-list' href=\"index.php\" title=\"$_title\" class=\"btn btn-small \">
					<i class=\"icon-database\"></i>$title</a>"; //HTML кнопки
		$bar->appendButton('Custom', $dhtml, 'backup-list');//давляем ее на тулбар

	}



	/**
	 * Точка входа Ajax
	 *
	 * @throws Exception
	 * @since 3.9
	 * @author Gartes
	 * @creationDate 2020-04-30, 16:59
	 * @see {url : https://docs.joomla.org/Using_Joomla_Ajax_Interface/ru }
	 */
	public function onAjaxJoomshoping_prod_sys()
	{
		JLoader::registerNamespace( 'GNZ11', JPATH_LIBRARIES . '/GNZ11', $reset = false, $prepend = false, $type = 'psr4' );
		JLoader::registerNamespace( 'Joomshoping_prod_sys', JPATH_PLUGINS . '/system/joomshoping_prod_sys', $reset = false, $prepend = false, $type = 'psr4' );
		$helper = \Joomshoping_prod_sys\Helpers\Helper::instance( $this->params );
		$task = $this->app->input->get( 'task', null, 'STRING' );


		try
		{
			// Code that may throw an Exception or Error.
			$results = $helper->$task();
		} catch (Exception $e)
		{
			$results = $e;
		}
		return $results;
	}
}
