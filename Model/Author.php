<?php
/**
 * A PHP file which magages the Author visualization.
 */
 
 /**
 * A class which magages the Author visualization
 *
 * @category   Model
 * @package    Model
 * @author     Tim Steinbrecher
 * @version    Release: 1.0
 */
class Model_Author{
	
	 /**
     * Constructor
     *
     * @access public
     */
	public function __construct(){
		
		Main::$Display->setTitle('Author');
		Main::$Display->setHeadline('Author');
		Main::$Display->setTemplate('Design/Author.phtml');
		
		$this->getAuthorInfo();
		
	}
	
	 /**
     * Display Author Information
     *
     * @access private
     */
	private function getAuthorInfo(){
	
		if( isset( $_GET['id'] ) && is_numeric( $_GET['id'] ) ){
			
			$sql  = "SELECT author, institution, location FROM pdf_authors ";
			$sql .= "WHERE id = :id ;";
			
			MAIN::$DB->prepare( $sql );
			MAIN::$DB->bind_param( 'id' , $_GET['id'] );
			
			try{
			
				MAIN::$DB->exe( );
				
				if( MAIN::$DB->nr() == 1 ){
					
					$res = MAIN::$DB->fas();
					
					Main::$Display->set('result', $res);
					
					$this->getPublications( $res['author'] );
					$this->getReferences( $res['author'] );
					#$this->getWordlist( $_GET['id'] );
					##$this->getWordCloud( $_GET['id'] );					
					#$this->getBibData( $_GET['id'] );					
					
				}
				else{
					
					Main::$Display->set('error', 'Autor nicht gefunden.');
					
				}
			
			}
			catch(PDOException $e){
				
				echo $e->getMessage();
				
			}
		
		}
		elseif( isset( $_GET['ids'] ) ){
			
			$sql  = "SELECT author, institution, location FROM pdf_authors ;";
			
			MAIN::$DB->prepare( $sql );
			MAIN::$DB->bind_param( 'id' , $_GET['id'] );
			
			try{
			
				MAIN::$DB->exe( );
				
				if( MAIN::$DB->nr() > 0 ){
					
					Main::$Display->set('author_liste', MAIN::$DB->fa());
					
					#$this->getPublications( $res['author'] );
					#$this->getReferences( $res['author'] );
					#$this->getWordlist( $_GET['id'] );
					##$this->getWordCloud( $_GET['id'] );					
					#$this->getBibData( $_GET['id'] );					
					
				}
				else{
					
					Main::$Display->set('error', 'Keine Autor vorhanden.');
					
				}
			
			}
			catch(PDOException $e){
				
				echo $e->getMessage();
				
			}
			
		}	
		elseif( isset( $_GET['search'] ) ){
			
			$sql  = "SELECT id, author, institution, location FROM pdf_authors WHERE author LIKE :search ;";
			
			$search = '%' . $_GET['search'] . '%';
			
			MAIN::$DB->prepare( $sql );
			MAIN::$DB->bind_param( 'search' , $search );

			try{
			
				MAIN::$DB->exe( );
				
				if( MAIN::$DB->nr() > 0 ){
					
					Main::$Display->set('author_search', MAIN::$DB->fa());		
					
				}
				else{
					
					Main::$Display->set('author_search', 'Keine Autor vorhanden.');
					
				}
			
			}
			catch(PDOException $e){
				
				echo $e->getMessage();
				
			}
		}
		else{
			
			$sql  = "SELECT id, author, institution, location FROM pdf_authors ;";
			
			try{
				
				MAIN::$DB->prepare( $sql );
				MAIN::$DB->bind_param( 'id' , $_GET['id'] );
				MAIN::$DB->exe( );
				
				if( MAIN::$DB->nr() > 0 ){
					
					Main::$Display->set('author_liste', MAIN::$DB->fa());
					
					#$this->getPublications( $res['author'] );
					#$this->getReferences( $res['author'] );
					#$this->getWordlist( $_GET['id'] );
					##$this->getWordCloud( $_GET['id'] );					
					#$this->getBibData( $_GET['id'] );					
					
				}
				else{
					
					Main::$Display->set('error', 'Keine Autoren vorhanden.');
					
				}
			
			}
			catch(PDOException $e){
				
				echo $e->getMessage();
				
			}			
		}	
	}

	 /**
     * Manage the Download
     *
     * @access private
	 * @param string $author Author name
     */
	private function getPublications( $author ){
		
		$sql  = "SELECT pdf.id AS id, pdf.title AS title FROM pdf AS pdf ";
		$sql .= "LEFT JOIN pdf_authors AS authors ";
		$sql .= "ON authors.pdf_id = pdf.id ";
		$sql .= "WHERE authors.author LIKE :author ;";

		$author = "%".$author."%";
		
		MAIN::$DB->prepare( $sql );
		MAIN::$DB->bind_param( 'author' , $author, PDO::PARAM_STR );
		
		try{
			
			MAIN::$DB->exe( );
		
			if( MAIN::$DB->nr() > 0 ){
				
				Main::$Display->set('pdfs', MAIN::$DB->fa());
			
			}
			else{
				
				Main::$Display->set('pdfs', false);
			
			}
		}
		catch(PDOException $e){
			
			echo $e->getMessage();
			
		}	
	}
	
	 /**
     * Manage the Download
     *
     * @access private
	 * @param string $author Author name
     */
	private function getReferences( $author ){
		
		$sql  = "SELECT * FROM pdf_bib ";
		$sql .= "WHERE author REGEXP :author ;";
		#$sql .= "GROUP BY title;";

		$author = '('.str_replace( ' ', '|', $author).')+.*('.str_replace( ' ', '|', $author).')+';
		
		MAIN::$DB->prepare( $sql );
		MAIN::$DB->bind_param( 'author' , $author, PDO::PARAM_STR );
		
		try{
			
			MAIN::$DB->exe( );
		
			if( MAIN::$DB->nr() > 0 ){
				
				Main::$Display->set('bibs', MAIN::$DB->fa());
			
			}
			else{
				
				Main::$Display->set('bibs', false);
			
			}
		}
		catch(PDOException $e){
			
			echo $e->getMessage();
			
		}	
	}
}
?>