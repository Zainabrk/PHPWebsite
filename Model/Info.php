<?php
/**
 * A PHP file which magages the Information visualization.
 */
 
/**
 * A class which magages the Information visualization
 *
 * @category   Model
 * @package    Model
 * @author     Tim Steinbrecher
 * @version    Release: 1.0
 */
class Model_Info{
	
	 /**
     * Constructor
     *
     * @access public
     */
	public function __construct(){
		
		Main::$Display->setTitle('Info');
		Main::$Display->setHeadline('Info');
		Main::$Display->setTemplate('Design/Info.phtml');
		Main::$Display->addJs('https://maps.googleapis.com/maps/api/js?key=AIzaSyAQjuOKfwl6Ri7o8dpER7WPd1-5mBdYrjY&libraries=visualization,drawing');
		
		$this->getInfo();
		
	}
	
	 /**
     * Get Main Information
     *
     * @access private
     */
	private function getInfo(){
	
		if( isset( $_GET['id'] ) && is_numeric( $_GET['id'] ) ){
			
			$sql  = "SELECT info.id AS id, info.filename AS filename, info.title AS title, info.subject AS subject, info.abstract AS abstract, ";
			$sql .= "info.keywords AS keywords, info.date AS date, info.language AS language, info.raw AS raw FROM pdf AS info ";
			$sql .= "LEFT JOIN pdf_words AS words ";
			$sql .= "ON info.id = words.id ";
			$sql .= "WHERE info.id = :id LIMIT 1;";
			
			try{
			
				MAIN::$DB->prepare( $sql );
				MAIN::$DB->bind_param( 'id' , $_GET['id'] );
				MAIN::$DB->exe( );
				
				if( MAIN::$DB->nr() == 1 ){
					
					Main::$Display->set('result', MAIN::$DB->fas());
					
					$this->getAuthors( $_GET['id'] );
					$this->getWordlist( $_GET['id'] );
					$this->getWordCloud( $_GET['id'] );
					$this->getReferences( $_GET['id'] );
					$this->getBibData( $_GET['id'] );
					$this->getMap( $_GET['id'] );
					
				}
				else{
					
					Main::$Display->set('result', 'Nix gefunden.');
					
				}
			
			}
			catch(PDOException $e){
				
				echo $e->getMessage();
				
			}
		
		}
		else{
			
			$sql  = "SELECT id, filename, title FROM pdf ORDER BY id ASC;";
			
			try{
				MAIN::$DB->prepare( $sql );
				MAIN::$DB->exe();
				
				if( MAIN::$DB->nr() > 0 ){
					
					Main::$Display->set('info', MAIN::$DB->fa() );
					
				}
				else{
					
					Main::$Display->set('info', 'Nicht vorhanden.');
					
				}
			}
			catch(PDOException $e){
				
				echo $e->getMessage();
				
			}
			
		}	
	}
	
	 /**
     * Get Authors List
     *
     * @access private
	 * @param int $id PDF id
     */
	private function getAuthors( $id ){
		
		$sql  = "SELECT id, author, location FROM pdf_authors ";
		$sql .= "WHERE pdf_id = :id;";
		
		try{
			
			MAIN::$DB->prepare( $sql );
			MAIN::$DB->bind_param( 'id' , $id );
			MAIN::$DB->exe( );
		
			if( MAIN::$DB->nr() > 0 ){
				
				Main::$Display->set('authors', MAIN::$DB->fa());
			
			}
			else{
				
				Main::$Display->set('authors', false);
			
			}
		}
		catch(PDOException $e){
			
			echo $e->getMessage();
			
		}	
	}
	
	 /**
     * Get Wordlist
     *
     * @access private
	 * @param int $id PDF id
     */
	private function getWordlist( $id ){
		
		$sql  = "SELECT pdf_id as id, word, count FROM pdf_words ";
		$sql .= "WHERE pdf_id = :id AND type = 'F' ORDER BY count DESC LIMIT 20;";
		
		$sql2 = "SELECT pdf_id as id, word, count FROM pdf_words ";
		$sql2.= "WHERE pdf_id = :id AND type = 'A' ORDER BY count DESC LIMIT 20;";
		
		try{
			
			MAIN::$DB->prepare( $sql );
			MAIN::$DB->bind_param( 'id' , $id );
			MAIN::$DB->exe( );
		
			if( MAIN::$DB->nr() > 0 ){
				
				Main::$Display->set('words', MAIN::$DB->fa());
			
			}
			else{
				
				Main::$Display->set('words', false);
			
			}
			
			MAIN::$DB->prepare( $sql2 );
			MAIN::$DB->bind_param( 'id' , $id );
			MAIN::$DB->exe( );
		
			if( MAIN::$DB->nr() > 0 ){
				
				Main::$Display->set('words_A', MAIN::$DB->fa());
			
			}
			else{
				
				Main::$Display->set('words_A', false);
			
			}
		}
		catch(PDOException $e){
			
			echo $e->getMessage();
			
		}	
	}
	
	 /**
     * Get Wordcloud
     *
     * @access private
	 * @param int $id PDF id
     */
	private function getWordCloud( $id ){
		
		$sql  = "SELECT word, count FROM ( SELECT * FROM pdf_words ";
		$sql .= "WHERE pdf_id = :id AND type = 'F' ORDER BY count DESC Limit 100 ) a ORDER BY RAND(); ";
		
		#$sql1 = "SELECT MIN(a.count) AS min, MAX(a.count) AS max FROM (SELECT * FROM pdf_words WHERE pdf_id = :id AND type = 'F' ORDER BY count DESC Limit 100) AS a;";
		$sql1 = "SELECT MAX(count) AS max, MIN(count) AS min FROM pdf_words WHERE pdf_id = :id AND type = 'F' ORDER BY count DESC Limit 100; ";
		
		try{
			
			MAIN::$DB->prepare( $sql );
			MAIN::$DB->bind_param( 'id' , $id );
			MAIN::$DB->exe( );
		
			if( MAIN::$DB->nr() > 0 ){
				
				Main::$Display->set('cloud', MAIN::$DB->fa());
				
				MAIN::$DB->prepare( $sql1 );
				MAIN::$DB->bind_param( 'id' , $id );
				MAIN::$DB->exe( );
				$max = MAIN::$DB->fas();
				Main::$Display->set('max', $max['max']);
				Main::$Display->set('min', $max['min']);
			
			}
			else{
				
				Main::$Display->set('cloud', false);
			
			}
		}
		catch(PDOException $e){
			
			echo $e->getMessage();
			
		}	
	}
	
	 /**
     * Get References
     *
     * @access private
	 * @param int $id PDF id
     */
	private function getReferences( $id ){
		
		$sql  = "SELECT `reference` FROM `pdf_ref` ";
		$sql .= "WHERE pdf_id = :id; ";
		
		try{
			
			MAIN::$DB->prepare( $sql );
			MAIN::$DB->bind_param( 'id' , $id );
			MAIN::$DB->exe( );
		
			if( MAIN::$DB->nr() > 0 ){
				
				Main::$Display->set('ref', MAIN::$DB->fa());
			
			}
			else{
				
				Main::$Display->set('ref', false);
			
			}
		}
		catch(PDOException $e){
			
			echo $e->getMessage();
			
		}	
	}
	
	 /**
     * Get Bib Tex Data
     *
     * @access private
	 * @param int $id PDF id
     */
	private function getBibData( $id ){
		
		$sql  = "SELECT id, pdf_id, type, doi, author, title, journal, publisher, year, pages, url, booktitle, abstract FROM pdf_bib ";
		$sql .= "WHERE pdf_id = :id; ";
			
		try{
			
			MAIN::$DB->prepare( $sql );
			MAIN::$DB->bind_param( 'id' , $id );
			MAIN::$DB->exe( );
		
			if( MAIN::$DB->nr() > 0 ){
				
				Main::$Display->set('bib', MAIN::$DB->fa());
			
			}
			else{
				
				Main::$Display->set('bib', false);
			
			}
		}
		catch(PDOException $e){
			
			echo $e->getMessage();
			
		}	
	}
	
	 /**
     * Get Map Lat Lng
     *
     * @access private
	 * @param int $id PDF id
     */
	private function getMap( $id ){
		
		$sql  = "SELECT lat, lng, id FROM pdf_location ";
		$sql .= "WHERE pdf_id = :id; ";
			
		try{
			
			MAIN::$DB->prepare( $sql );
			MAIN::$DB->bind_param( 'id' , $id );
			MAIN::$DB->exe( );
		
			if( MAIN::$DB->nr() > 0 ){
				
				Main::$Display->set('map', MAIN::$DB->fa());
			
			}
			else{
				
				Main::$Display->set('map', false);
			
			}
		}
		catch(PDOException $e){
			
			echo $e->getMessage();
			
		}	
	}
}
?>