<?php
 /**
 * A PHP file which magages the Main functionality of the system.
 */
 
/**
 * A class which magages the Main functionality of the system
 *
 * @category   Main
 * @package    Main
 * @author     Tim Steinbrecher
 * @version    Release: 1.0
 */
class Main{
	
	/**
     * Contains the Config variables
     *
     * @access public
     * @static
     * @var System_Config
     */
	static public $Config;
	
	/**
     * DB connection 
     *
     * @access public
     * @static
     * @var System_Db
     */
	static public $DB;
	
	/**
     * Contains the Display variables
     *
     * @access public
     * @static
     * @var System_Display
     */
	static public $Display;
	
    /**
     * Initialization
	 *
     */
	public static function _init(){
		
		self::set_conf();
		self::set_db();
		self::set_display();
		
		self::loadModel();

		self::getSelectedtPdfs();
	}
		
    /**
     * Call of the System_Config
     *
     */
	private static function set_conf(){

		self::$Config = new System_Config();
		
	}
	
    /**
     * Initialization from the Database Connection
     *
     */
	private static function set_db()
    {	
		try
		{
			self::$DB = new System_Db(
				self::$Config->get('db_host'), 
				self::$Config->get('db_user'), 
				self::$Config->get('db_pass'), 
				self::$Config->get('db_name'), 
				true, 
				self::$Config->get('db_init_cmd')
			);
		}
		catch(Exception $e)
		{
			
			die('Verbindung konnte nicht hergestellt werden. Fehler: ' . $e->getMessage());
			
		}
	}
		
    /**
     * Call of the System_Display
     *
     */
	private static function set_display(){
		
		self::$Display = new System_Display();
		
	}
		
    /**
     * Load of the Model Class
     *
     */
	private static function loadModel(){

		if( empty( self::$Config->get('model') ) ){

			new Model_Home();
			
		}
		else{
			
			$model = 'Model_' . str_replace('/', '_', self::$Config->get('model') );

			if( class_exists( $model ) ){

				new $model();
				
			}
			else{
				
				Main::$Display->setTitle('Error');
				Main::$Display->setHeadline('Error');
				Main::$Display->setTemplate('Design/Error.phtml');
			
			}
		}
	}
	
    /**
     * Loading the SelectedPdfs Template
     *
     */
	public static function getSelectedPdfsContent(){
	
		require 'Design/SelectedPdfs.phtml';
		
	}
	
    /**
     * Mage the SelectedPdfs interactions
     *
     */
	private static function getSelectedtPdfs(){
		
		if( !isset( $_SESSION['selectetdPdfs'] ) ){
			
			$_SESSION['selectetdPdfs'] = array();
			
		}
		
		$post = Main::$Config->get('post');

		if( isset( $_POST['search-results-submit'] ) ){

			$selectetdPdfs = array();
		
			if( isset( $post['switch'] ) ){
		
				foreach( $post['switch'] as $id => $status ){
					
					if(  $status == 1 ){
						
						$selectetdPdfs[$id] = 1;
						
					}
					else{
						
						unset( $_SESSION['selectetdPdfs'][$id]);
						
					}
				}
			}
			
			$_SESSION['selectetdPdfs'] = $_SESSION['selectetdPdfs'] + $selectetdPdfs;
			unset($_SESSION['search-results-submit']);
			
		}

		if( isset( $_POST['map-results-submit'] ) && $post['map-results-submit'] !== '' ){

			$selectetdPdfs = array();

			$select = explode( ',' , $post['map-results-submit'] );
			
			foreach( $select as $id ){

				$selectetdPdfs[$id] = 1;
				
			}

			$_SESSION['selectetdPdfs'] =  $selectetdPdfs + $_SESSION['selectetdPdfs'];
			unset($_SESSION['map-results-submit']);

		}

		if( isset( $_POST['info-switch'] ) && $post['info-switch'] !== '' ){
			
			reset($post['info-switch']);
			$key =  key($post['info-switch']);
			
			$selectetdPdfs = array();
			
			if( $post['info-switch'][$key] == 1 ){
				$selectetdPdfs[$key] = 1;
			}
			else{
				unset( $_SESSION['selectetdPdfs'][$key]);
			}
				
			$_SESSION['selectetdPdfs'] = $selectetdPdfs + $_SESSION['selectetdPdfs'];
			unset($_SESSION['info-switch']);

		}
		
		if( isset( $_POST['selectetdPdfs-submit'] ) ){

			$selectetdPdfs = array();
		
			foreach( $post['selectetdPdfs'] as $id => $status ){
				if(  $status == 1 ){
					
					$selectetdPdfs[$id] = 1;
					
				}
			}
			
			$_SESSION['selectetdPdfs'] = $selectetdPdfs;
			unset($_SESSION['selectetdPdfs-submit']);

		}

		Main::$Display->set('selectetdPdfs', false);
		
		if( isset( $_SESSION['selectetdPdfs'] ) && count( $_SESSION['selectetdPdfs'] ) > 0 ){

			$sql  = "SELECT id, title, subject ";
			$sql .= "FROM pdf WHERE id IN (" . implode( ',' , array_keys($_SESSION['selectetdPdfs'] ) ) . ") ;";
			#$sql .= "LIMIT 10;";

			MAIN::$DB->prepare( $sql );
			
			try{
			
				MAIN::$DB->exe( );
				
				if( MAIN::$DB->nr() > 0 ){

					Main::$Display->set('selectetdPdfs', MAIN::$DB->fa());
				
				}
			}
			catch(PDOException $e){
				
				echo $e->getMessage();
				
			}
		}
	}
}

?>