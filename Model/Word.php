<?php
/**
 * A PHP file which magages the Wordlist visualization.
 */
 
/**
 * A class which magages the Wordlist visualization
 *
 * @category   Model
 * @package    Model
 * @author     Tim Steinbrecher
 * @version    Release: 1.0
 */
class Model_Word{
	
	 /**
     * Constructor
     *
     * @access public
     */
	public function __construct(){
		
		Main::$Display->setTitle('Wordlist');
		Main::$Display->setHeadline('Wordlist');
		Main::$Display->setTemplate('Design/Word.phtml');
		
		$this->getWord();
		
	}
	
	 /**
     * Get Wordlist
     *
     * @access private
     */
	private function getWord(){
		
		if( isset( $_GET['word'] ) ){
			/*
			$sql  = "SELECT word.pdf_id AS id, word.word AS word, word.count AS count, pdf.title AS title FROM pdf_words AS word ";
			$sql .= "LEFT JOIN pdf AS pdf ON pdf.id = word.pdf_id ";
			$sql .= "WHERE word.word LIKE :word ";
			$sql .= "ORDER BY word.count DESC;";
			*/
			
			$sql  = "SELECT word.count, word.pdf_id AS id, word.word AS word, pdf.title AS title, word.type ";
			$sql .= "FROM ( SELECT SUM(count) AS count, word, pdf_id, type FROM pdf_words WHERE word LIKE :word AND type = 'F' GROUP BY pdf_id ORDER BY count DESC ) AS word ";
			$sql .= "LEFT JOIN pdf AS pdf ON pdf.id = word.pdf_id ";
			$sql .= "WHERE word.type = 'F'; ";

			
			try{
				
				$word =  $_GET['word'].'%';
				
				MAIN::$DB->prepare( $sql );
				MAIN::$DB->bind_param( 'word', $word );
				MAIN::$DB->exe();
				
				if( MAIN::$DB->nr() > 0 ){
					
					Main::$Display->set('word', $_GET['word'] );
					Main::$Display->set('wordlist', MAIN::$DB->fa() );
					
				}
				else{
					
					Main::$Display->set('wordlist', 'Nicht vorhanden.');
					
				}
			}
			catch(PDOException $e){
				
				echo $e->getMessage();
				
			}
		}
		elseif( isset( $_GET['ids'] ) ){
									
			$sql  = "SELECT word.pdf_id AS id, word.word AS word, word.count AS count, pdf.title AS title FROM pdf_words AS word ";
			$sql .= "LEFT JOIN pdf AS pdf ON pdf.id = word.pdf_id ";
			$sql .= "WHERE pdf.id IN ( ". $_GET['ids']." ) AND word.type = 'F' ";
			$sql .= "ORDER BY word.count DESC LIMIT 50;";
			
			try{
				
				MAIN::$DB->prepare( $sql );
				MAIN::$DB->exe();
				
				if( MAIN::$DB->nr() > 0 ){
					
					Main::$Display->set('wordlist_ids', MAIN::$DB->fa() );
					
				}
				else{
					
					Main::$Display->set('wordlist', 'Nicht vorhanden.');
					
				}
			}
			catch(PDOException $e){
				
				echo $e->getMessage();
				
			}
		}
		elseif( isset( $_GET['id'] ) ){
									
			$sql  = "SELECT word.pdf_id AS id, word.word AS word, word.count AS count, pdf.title AS title FROM pdf_words AS word ";
			$sql .= "LEFT JOIN pdf AS pdf ON pdf.id = word.pdf_id ";
			$sql .= "WHERE pdf.id = :id AND word.type = 'F' ";
			$sql .= "ORDER BY word.count DESC LIMIT 50;";
			
			try{
				
				MAIN::$DB->prepare( $sql );
				MAIN::$DB->bind_param( 'id', $_GET['id'] );
				MAIN::$DB->exe();
				
				if( MAIN::$DB->nr() > 0 ){
					
					Main::$Display->set('wordlist_id', MAIN::$DB->fa() );
					
				}
				else{
					
					Main::$Display->set('wordlist', 'Nicht vorhanden.');
					
				}
			}
			catch(PDOException $e){
				
				echo $e->getMessage();
				
			}
		}
		else{
			
			#$sql  = "SELECT pdf.id AS id, word.word AS word, word.count AS countFROM pdf_words AS word ";
			#$sql .= "LEFT JOIN pdf AS pdf ON pdf.id = word.pdf_id ";
			#$sql .= "ORDER BY word.count DESC LIMIT 50;";
			
			$sql  = "SELECT pdf_id AS id, word, SUM( count ) AS count, pdf.title AS title ";
			$sql .= "FROM pdf_words AS word ";
			$sql .= "LEFT JOIN pdf AS pdf ON pdf.id = word.pdf_id ";
			$sql .= "WHERE word.type = 'F' ";
			$sql .= "GROUP BY word.word, word.pdf_id ORDER BY SUM( word.count ) DESC ";
			$sql .= "LIMIT 50;";
			
			try{
				
				MAIN::$DB->prepare( $sql );
				MAIN::$DB->exe();
				
				if( MAIN::$DB->nr() > 0 ){
					
					Main::$Display->set('wordlist_all', MAIN::$DB->fa() );
					
				}
				else{
					
					Main::$Display->set('wordlist', 'Nicht vorhanden.');
					
				}
			}
			catch(PDOException $e){
				
				echo $e->getMessage();
				
			}			
		}
	}
}
?>