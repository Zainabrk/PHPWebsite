<?php
/**
 * A PHP file which magages the Configuration functionality of the system
 */
 
/**
 * A class which magages the Configuration functionality of the system
 *
 * @category   System_Config
 * @package    System_Config
 * @author     Tim Steinbrecher
 * @version    Release: 1.0
 */
class System_Config
{
	/**
     * Contains the Config array
     *
     * @access protected
     * @static
     * @var array
     */
	protected static $Config = array();

	 /**
     * Constructor
     *
     * @access public
     */
    public function __construct() {
		
		self::set('db_name', 'mysql');
		self::set('db_user', 'root');
		self::set('db_pass', '');
		self::set('db_host', '127.0.0.1');
		self::set('db_init_cmd', "SET NAMES UTF8;");
		
		self::setProtocol();
		self::setRequestUri();
		self::setDomain();
		self::setRootDir();
		self::setBasehref();
		self::setModel();
		self::setQuery();
		self::setPost();
	}

	 /**
     * Saves al value to its key in the config
     *
     * @access public
     * @param string $key key
     * @param string $val value
     */
    public static function set($key, $val)
    {
        self::$Config[$key] = $val;
    }

	 /**
     * Gets the value from its key
     *
     * @access public
     * @param string $key key
     * @return string value
     */
    public static function get($key)
    {
        return self::$Config[$key];
    }
	
	 /**
     * Set the Protocol  
     *
     * @access private
     */
	private function setProtocol()
	{
		if( isset( $_SERVER["HTTPS"] ) && $_SERVER["HTTPS"] == "on" )
		{
			$protocol = "https";
		}
		$protocol = "http";
		
		self::set('protocol', $protocol);
		
	}
	
	 /**
     * Set the Basehref  
     *
     * @access private
     */
	private function setBasehref()
	{
		
		self::set('basehref', self::get('protocol')."://".self::get('domain').self::get('root_dir')."/" );

	}
	
	 /**
     * Set the Domain  
     *
     * @access private
     */
	private function setDomain()
	{
		if( preg_match("/([0-9A-z\-]{2,100}\.[A-z]{1,8})$/u", $_SERVER["HTTP_HOST"], $temp ) )
		{
			$domain = $temp[1];}
		else
		{
			$domain = $_SERVER["HTTP_HOST"];
		}
		
		self::set('domain', $domain);
		
	}
	
	 /**
     * Set the RootDir  
     *
     * @access private
     */
	private function setRootDir()
	{
		$root_dir = "";
		$temp = strrpos( $_SERVER["SCRIPT_NAME"], "/" );
		if( $temp != false )
		{
			$root_dir = substr( $_SERVER["SCRIPT_NAME"], 0, $temp );
		}
		
		self::set('root_dir', $root_dir);
		
	}
	
	 /**
     * Set the RequestUri  
     *
     * @access private
     */
	private function setRequestUri()
	{
		
		self::set('request_uri', $_SERVER['REQUEST_URI']);
		
	}
	
	 /**
     * Set the Model  
     *
     * @access private
     */
	private function setModel()
	{
		if( stristr( self::get('request_uri'), self::get('root_dir') ) ){
			
			$url = ltrim( self::get('request_uri'), self::get('root_dir') );
			
			if( strstr($url, '?') )
				$url = strstr($url, '?', true);
			
			self::set('model', rtrim( $url ,'/' ) );
			
		}
	}
	
	 /**
     * Set the Query ( _GET Request )     
	 *
     * @access private
     */
	private function setQuery()
	{
		
		$query = parse_url( self::get('request_uri'), PHP_URL_QUERY );
		
		if( empty( $query ) ){

			self::set('query', false );
			
		}
		else{
			
			parse_str( $query, $array );
			self::set('query', $array );
			
		}
	}
	
	 /**
     * Set the _POST Request  
     *
     * @access private
     */
	private function setPost()
	{
		
		$post = array();
		$sess = array();
		$array = array();
		
		if( count( $_POST ) ){

			$post = $_POST;
			#unset( $_POST );
			
		}
		
		if( isset( $_SESSION['post'] ) ){

			$sess = $_SESSION['post'];
			unset( $_SESSION['post'] );
			
		}
		
		$array = array_replace($sess, $post);
		
		self::set('post', $array );
		$_SESSION['post'] = $array;

	}
}

?>