<?php


	namespace Joomshoping_prod_sys\Helpers;


	use Joomla\CMS\Language\Text;

	class Helper
	{
		private $app;
		public static $instance;
		protected $params  ;

		/**
		 * helper constructor.
		 * @throws \Exception
		 * @since 3.9
		 */
		private function __construct( $options = array() )
		{
			$this->app = \JFactory::getApplication();
			$this->params = $options ;
			return $this;
		}#END FN

		/**
		 * @param array $options
		 *
		 * @return helper
		 * @throws \Exception
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

		public function addExcludeList(){
			$Exclude = trim( $this->app->input->get('Exclude' , null , 'RAW') );
			if( empty( $Exclude ) )
			{
				$this->app->enqueueMessage( Text::_('Empty Value'));
				return ['Empty'] ;
			}#END IF

		}

	}