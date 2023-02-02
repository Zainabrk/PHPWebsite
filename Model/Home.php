<?php
/**
 * A PHP file which magages the Home Site
 */
 
/**
 * A class which magages the Home Site
 *
 * @category   Model
 * @package    Model
 * @author     Tim Steinbrecher
 * @version    Release: 1.0
 */
class Model_Home{
	
	 /**
     * Constructor
     *
     * @access public
     */
	public function __construct(){
		
		Main::$Display->setTitle('Start');
		Main::$Display->setHeadline('Start');
		Main::$Display->setTemplate('Design/Home.phtml');
		
	}
}



?>