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
	 * onAfterCompileHead.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function onAfterCompileHead()
	{

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
	/**
	 * Точка входа Ajax
	 *
	 * @throws Exception
	 * @since 3.9
	 * @author Gartes
	 * @creationDate 2020-04-30, 16:59
	 * @see {url : https://docs.joomla.org/Using_Joomla_Ajax_Interface/ru }
	 */
	public function onAjaxCountry_filter()
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
