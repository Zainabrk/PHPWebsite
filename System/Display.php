<?php
/**
 * A PHP file which magages the Display functionality of the system
 */
 
/**
 * A class which magages the Display functionality of the system
 *
 * @category   System_Display
 * @package    System_Display
 * @author     Tim Steinbrecher
 * @version    Release: 1.0
 */
class System_Display extends Main
{
	
	/**
     * Contains the Config array
     *
     * @access protected
     * @static
     * @var string
     */
	protected static $Template = '';
	
	/**
     * Contains the Config array
     *
     * @access protected
     * @static
     * @var string
     */
	protected static $Title = '';
	
	/**
     * Contains the Config array
     *
     * @access protected
     * @static
     * @var string
     */
	protected static $Headline = '';
	
	/**
     * Contains the Config array
     *
     * @access protected
     * @static
     * @var array
     */
	protected static $Js = array();
	
	/**
     * Contains the Config array
     *
     * @access protected
     * @static
     * @var array
     */
	protected static $Css = array();
	
	/**
     * Contains the Config array
     *
     * @access protected
     * @static
     * @var array
     */
	protected static $Out = array();
	
	 /**
     * Constructor
     *
     * @access public
     */
	public function __construct(){
	}
	
	/**
     * Checks if the Template file exist
	 * if exists: load the Template file
	 * not exists: load error Template
     *
     * @access public
     * @return boolean true on success
     */
	public function getContent( )
	{
		
		if( !empty( self::getTemplate() ) ){
			
			if( file_exists( self::getTemplate() ) ){
				require self::getTemplate();
				return true;
				
			}
		}
		
		$className = 'Design/' . Main::$Config->get('model') . '.phtml';
		
		if( file_exists( $className ) ){
			require $className;
			return true;
			
		}
		
		require 'Design/Error.phtml';;
		
	}
	
	/**
     * Set Template value to its key
     *
     * @access public
     * @param string $key key
     * @param string $val value
     */
    public static function set($key, $val)
    {
        self::$Out[$key] = $val;
    }

	/**
     * Get Template value to its key
     *
     * @access public
     * @param string $key key
     * @return mixed false on error
	 */
    public static function get($key)
    {
		if( isset( self::$Out[$key] ) )
			return self::$Out[$key];
		else
			return false;
    }
	
	/**
     * Set Template file
     *
     * @access public
     * @param string $setTemplate key
	 */
	public function setTemplate( $setTemplate )
	{
		
		self::$Template = $setTemplate;	
		
	}
	
	/**
     * Get Template file
     *
     * @access public
     * @return string Template file
	 */
	public function getTemplate( )
	{
		
		return self::$Template;	
		
	}
	
	/**
     * Set Title
     *
     * @access public
     * @param string $setTitle Title
	 */
	public function setTitle( $setTitle )
	{
		
		self::$Title = $setTitle;	
		
	}
	
	/**
     * Get Title
     *
     * @access public
     * @return string Title
	 */
	public function getTitle( )
	{
		
		return self::$Title;	
		
	}
	
	/**
     * Set Headline
     *
     * @access public
     * @param string $setHeadline Headline
	 */
	public function setHeadline( $setHeadline )
	{
		
		self::$Headline = $setHeadline;		
		
	}
	
	/**
     * Get Headline
     *
     * @access public
     * @return string Headline
	 */
	public function getHeadline( )
	{
		
		return self::$Headline;		
		
	}
	
	/**
     * Add Javascript file
     *
     * @access public
     * @param string $addJs Javascript file
	 */
	public function addJs( $addJs )
	{
		
		self::$Js[] = $addJs;
		
	}
	
	/**
     * Get Javascript file
     *
     * @access public
     * @return string Javascript file
	 */
	public function getJs( )
	{
		
		if( count( self::$Js ) == 0 ){
			return '';
		}
		
		$out = '';
		foreach( self::$Js as $link ){
			$out .= '<script src="'.$link.'"></script>';
		}
		return $out;
		
	}
	
	/**
     * Add CSS file
     *
     * @access public
     * @param string $addCss CSS file
	 */
	public function addCss( $addCss )
	{
		
		self::$Css[] = $addCss;
		
	}
	
	/**
     * Get CSS file
     *
     * @access public
     * @return string CSS file
	 */
	public function getCss( )
	{

		if( count( self::$Css ) == 0 ){
			return '';
		}
		
		$out = '';
		foreach( self::$Css as $link ){
			$out .= '<link rel="stylesheet" href="'.$link.'">';
		}
		return $out;
		
	}
}


?>