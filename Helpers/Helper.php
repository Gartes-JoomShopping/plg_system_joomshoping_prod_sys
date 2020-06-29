<?php


	namespace Joomshoping_prod_sys\Helpers;


	use Exception;
	use Joomla\CMS\Language\Text;
	use function GuzzleHttp\Psr7\str;

	class Helper
	{
		private $app;
		private $db ;
		public static $instance;
		protected $params  ;

		/**
		 * helper constructor.
		 * @throws Exception
		 * @since 3.9
		 */
		private function __construct( $options = array() )
		{
			$this->app = \Joomla\CMS\Factory::getApplication();
			$this->db = \Joomla\CMS\Factory::getDbo();
			$this->params = $options ;
			return $this;
		}#END FN

		/**
		 * @param array $options
		 *
		 * @return helper
		 * @throws Exception
		 * @since 3.9
		 */
		public static function instance( $options = array() )
		{
			if( self::$instance === null )
			{
				self::$instance = new self( $options );
			}
			return self::$instance;
		}#END FN

		/**
		 * Восстановить резервную копию
		 * @since 3.9
		 */
		public function restoreBackup(){

			$backupId = $this->app->input->get('backupId' , false , 'INT') ;

			$options = [
				'component' => 'jshopping',
				'backup_type'=>'sql',           // Def sql
				'backup_namespace' => 'mergeProduct'
			];
			$JoomlaBackup = \GNZ11\Core\Backup\Joomla::instance( $options );
			$JoomlaBackup->restoreById( $backupId );

			$MessageQueue = $this->app->getMessageQueue();

			echo'<pre>';print_r( $MessageQueue );echo'</pre>'.__FILE__.' '.__LINE__;
			die(__FILE__ .' '. __LINE__ );




		}

		/**
		 * Получить список резервных копий
		 * @throws Exception
		 * @since 3.9
		 */
		public function getListBackup(){
			$options = [
				'component' => 'jshopping',
				'backup_type'=>'sql',           // Def sql
				'backup_namespace' => 'mergeProduct'
			];
			$JoomlaBackup = \GNZ11\Core\Backup\Joomla::instance( $options );
			$listBackups = $JoomlaBackup->getBackups();


			echo new \JResponseJson($listBackups );
			die();

			 
		}
		protected $BackupOptions = [
			'component' => 'jshopping',
			'backup_type'=>'sql',           // Def sql
			'backup_namespace' => 'mergeProduct'
		] ;
		public function removeBackup(){
			$JoomlaBackup = \GNZ11\Core\Backup\Joomla::instance( $this->BackupOptions );
			$JoomlaBackup->removeBackup();
		}
		public function RunBackupComponent(){

			$JoomlaBackup = \GNZ11\Core\Backup\Joomla::instance( $this->BackupOptions );

			/* @var int Количество строк в одном запросе */
			$JoomlaBackup->set( 'limit' , $this->params->get('limit' , 100 )  );
			$JoomlaBackup->set('interval' , $this->params->get('backup_time' , 3 ) * 60  );
			$JoomlaBackup->backup();



			/*if( empty($listBackups['data']) )
			{

			}else{
				$jdata= new \Joomla\CMS\Date\Date();
				$now = $jdata->toSql();
				$nowTime =  strtotime($now) ;

				# 2020-06-22 02:39:36 => 1592782776
				$backupsTime = strtotime( $listBackups['data'][0]['date'] ) ;

				# Сколько прошло
				$time_has_passed = $nowTime - $backupsTime ;
				$backupTime = $this->params->get('backup_time' , 3 ) * 60;
				if( $time_has_passed>=$backupTime )
				{
					$JoomlaBackup->backup();
				}#END IF
			}#END IF*/
		}

		public function mergeProduct(){
			$str = $this->app->input->get('form' , false , 'RAW' ) ;
			$formData = array();
			parse_str($str, $formData);

			$Index= $this->app->input->get('Index' , false , 'RAW' ) ;

			$this->changeAttr( $formData[ 'ulLiNew' ][$Index][ 'product_id' ] , $formData[ 'product_id' ] , $formData );


		}
		private function SetRedirect($fromProduct , $toProduct , $formData){
			$Index = $this->app->input->get('Index' , false , 'RAW' ) ;
			$fromProductCategory = $formData['ulLiNew'][$Index]['Category'] ;
			$toProductCategory = $formData['category_id'][0]  ;

			$query = $this->db->getQuery(true) ;
			$table = $this->db->quoteName('#__gnz11_core_redirect') ;

			$columns = array('category_id','product_id','category_id_redirect','product_id_redirect' );
			$values =
				$this->db->quote( $fromProductCategory ).","
				.$this->db->quote($formData['ulLiNew'][$Index]['product_id']).","
				.$this->db->quote($toProductCategory).","
				.$this->db->quote( $toProduct ) ;

			$query->values(  $values );
			$query->insert( $table )->columns($this->db->quoteName( $columns ) );
			$this->db->setQuery($query);
//			echo $query->dump();
			$this->db->execute();

			try
			{
			    // Code that may throw an Exception or Error.
				# Удаление товара !
				$this->DelProduct( $fromProduct );
			    // throw new Exception('Code Exception '.__FILE__.':'.__LINE__) ;
			}
			catch (Exception $e)
			{
			    // Executed only in PHP 5, will not be reached in PHP 7
			    echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
			    echo'<pre>';print_r( $e );echo'</pre>'.__FILE__.' '.__LINE__;
			    die(__FILE__ .' '. __LINE__ );
			}



			echo'<pre>';print_r( $fromProduct );echo'</pre>'.__FILE__.' '.__LINE__;
			echo'<pre>';print_r( $toProduct );echo'</pre>'.__FILE__.' '.__LINE__;
			echo'<pre>';print_r( $formData );echo'</pre>'.__FILE__.' '.__LINE__;
			die(__FILE__ .' '. __LINE__ );


		}
		/**
		 * Добавить товару атрибуты другого товара
		 * @param $fromProduct int ID - из какого товара добавить : 7147 | Шервуд-2 ДГ 800*2000 Каштан
		 * @param $toProduct int ID товара к которому добавлять !
		 * @since 3.9
		 */
		private function changeAttr ( $fromProduct , $toProduct , $formData ){
			$Index= $this->app->input->get('Index' , false , 'RAW' ) ;
			$redirect = $this->app->input->get('redirect' , 0 ) ;

			if( $redirect )
			{
				$this->SetRedirect($fromProduct , $toProduct , $formData);
				return true ;
			}#END IF

			$query = $this->db->getQuery(true);
			$query->update($this->db->quoteName('#__jshopping_products_attr'));

			$fields = $this->db->quoteName('product_id') . ' = ' . $this->db->quote($toProduct) ;
			// Условия обновления
			$conditions =  $this->db->quoteName('product_id') . ' = '  . $this->db->quote( $fromProduct ) ;
			$query->set($fields);
			$query->where($conditions);
			$this->db->setQuery($query);
			$this->db->execute();


			$jdata= new \JDate();
			$now = $jdata->toSql();

			$query = $this->db->getQuery(true);
			$query->update($this->db->quoteName('#__jshopping_products'));
			$fields = [
				# Код товара
				$this->db->quoteName('product_ean') . ' = ' . $this->db->quote($formData['ulLiNew'][$Index]['product_ean']) ,
				$this->db->quoteName('image') . ' = ' . $this->db->quote($formData['ulLiNew'][$Index]['image']) ,
				# Цена*
				$this->db->quoteName('product_price') . ' = 0.000'  ,
				$this->db->quoteName('min_price') . ' = 0.000'    ,
				$this->db->quoteName('date_modify') . ' = ' . $this->db->quote($now) ,
				$this->db->quoteName('different_prices') . ' = ' . $this->db->quote(1) ,
			] ;
			$conditions =  $this->db->quoteName('product_id') . ' = '  . $this->db->quote( $toProduct ) ;
			$query->set($fields);
			$query->where($conditions);
//			echo $query->dump();
			$this->db->setQuery($query);
			$this->db->execute();

			$query = $this->db->getQuery(true);
			$query->update($this->db->quoteName('#__jshopping_products'));
			$fields = [
				$this->db->quoteName('parent_id') . ' = ' . $this->db->quote( $toProduct ) ,
			] ;
			$conditions =  $this->db->quoteName('parent_id') . ' = '  . $this->db->quote( $fromProduct ) ;
			$query->set($fields);
			$query->where($conditions);
//			echo $query->dump();
			$this->db->setQuery($query);
			$this->db->execute();

			# Удаление товара !
			$this->DelProduct( $fromProduct );










			echo'<pre>';print_r( $Index );echo'</pre>'.__FILE__.' '.__LINE__;
			echo'<pre>';print_r( $formData );echo'</pre>'.__FILE__.' '.__LINE__;

			die(__FILE__ .' '. __LINE__ );







		}
		private function DelProduct ( $fromProduct ){

			/*$query = $this->db->getQuery(true);
			$query->delete( $this->db->quoteName('#__jshopping_products_to_categories' , 'tc'));
			$query->delete( $this->db->quoteName('#__jshopping_products' , 'p'));
			$query->where( $this->db->quoteName('tc.product_id') . '=' . $this->db->quoteName('p.product_id') );
			$query->where( $this->db->quoteName('p.product_id') . '=' . $this->db->quote( $fromProduct ) );*/


			$query = '
				DELETE FROM `dveri_jshopping_products_to_categories` WHERE `product_id`= ' .$this->db->quote( $fromProduct ) .';' ;
			$query = ' 
 				DELETE FROM `dveri_jshopping_products` WHERE `product_id`= ' .$this->db->quote( $fromProduct ) .';' ;
			$this->db->setQuery($query);






			/*$query ='DELETE FROM `#__jshopping_products_to_categories`
						WHERE `product_id` = '.$this->db->quote( $fromProduct ).';' ;
			$query .='
						DELETE FROM `#__jshopping_products` 
						WHERE `product_id` = '.$this->db->quote($fromProduct).';' ;

			$query = $this->db->getQuery(false);*/
			$this->db->setQuery($query);
			/*echo'<pre>';print_r( $query );echo'</pre>'.__FILE__.' '.__LINE__;
			die(__FILE__ .' '. __LINE__ );*/



			$this->db->execute();
			return true ;
		}

		/**
		 * Загрузите файл макета плагина. Эти файлы могут быть переопределены с помощью стандартного Joomla! Шаблон
		 *
		 * Переопределение :
		 *                  JPATH_THEMES . /html/plg_{TYPE}_{NAME}/{$layout}.php
		 *                  JPATH_PLUGINS . /{TYPE}/{NAME}/tmpl/{$layout}.php
		 *                  or default : JPATH_PLUGINS . /{TYPE}/{NAME}/tmpl/default.php
		 *
		 *
		 * переопределяет. Load a plugin layout file. These files can be overridden with standard Joomla! template
		 * overrides.
		 *
		 * @param string $layout The layout file to load
		 * @param array  $params An array passed verbatim to the layout file as the `$params` variable
		 *
		 * @return  string  The rendered contents of the file
		 *
		 * @since   5.4.1
		 */
		private function loadTemplate ( $layout = 'default' )
		{

			$path = \Joomla\CMS\Plugin\PluginHelper::getLayoutPath( 'system', 'joomshoping_prod_sys', $layout );
			// Render the layout
			ob_start();
			include $path;
			return ob_get_clean();
		}



		/*public function addExcludeList(){
			$Exclude = trim( $this->app->input->get('Exclude' , null , 'RAW') );
			if( empty( $Exclude ) )
			{
				$this->app->enqueueMessage( Text::_('Empty Value'));
				return ['Empty'] ;
			}#END IF

		}*/









	}