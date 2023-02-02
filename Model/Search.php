<?php
/**
 * A PHP file which magages the Search function
 */
 
/**
 * A class which magages the Search function
 *
 * @category   Model
 * @package    Model
 * @author     Tim Steinbrecher
 * @version    Release: 1.0
 */
class Model_Search{
		
	 /**
     * Constructor
     *
     * @access public
     */
	public function __construct(){
		
		Main::$Display->setTitle('Suche');
		Main::$Display->setHeadline('Suche');
		Main::$Display->setTemplate('Design/Search.phtml');
		
		$this->search();
		
	}
	
	 /**
     * Mange Search Input
     *
     * @access private
     */	
	private function search(){
		
		$post = Main::$Config->get('post');
		
		if( isset( $post['search'] ) && trim( $post['search'] ) !== '' ){
			
			if( !isset( $post['options'] ) || $post['options'] == 'text' ){
			
				$sql  = "SELECT pdf.id, pdf.title, pdf.subject, pdf.abstract, pdf.keywords, pdf.filename, ";
				$sql .= "MATCH( pdf.raw ) AGAINST( :query IN NATURAL LANGUAGE MODE ) AS score, ";
				$sql .= "GROUP_CONCAT( IFNULL( author.author, 'NULL') SEPARATOR ', ' ) AS author ";
				$sql .= "FROM pdf AS pdf ";
				$sql .= "LEFT JOIN pdf_authors AS author ON pdf.id = author.pdf_id ";
				$sql .= "WHERE MATCH( pdf.raw ) AGAINST( :query IN NATURAL LANGUAGE MODE) > 0 ";
				$sql .= "GROUP BY author.pdf_id ORDER BY score DESC;";
				
			}
			elseif( $post['options'] == 'author' ){
							
				$sql  = "SELECT pdf.id as id, pdf.title, pdf.subject, pdf.abstract, pdf.keywords, pdf.filename, author.author ";
				$sql .= "FROM pdf_authors author LEFT JOIN pdf pdf ON author.pdf_id = pdf.id ";
				$sql .= "WHERE author.author LIKE :query; ";
				
				$post['search'] = '%'.$post['search'].'%';

			}
			elseif( $post['options'] == 'abstract' ){
			
				$sql  = "SELECT pdf.id, title, subject, abstract, keywords, filename, author.author, ";
                $sql .= "MATCH( abstract ) AGAINST( :query IN NATURAL LANGUAGE MODE) AS score ";
				$sql .= "FROM pdf LEFT JOIN pdf_authors author ON author.pdf_id = pdf.id ";
				$sql .= "WHERE MATCH(abstract) AGAINST( :query IN NATURAL LANGUAGE MODE) > 0 ";
				$sql .= "GROUP BY id ORDER BY score DESC;";
				
			}
			elseif( $post['options'] == 'keyword' ){
							
				$sql  = "SELECT pdf.id, title, subject, abstract, keywords, filename, author.author ";
				$sql .= "FROM pdf LEFT JOIN pdf_authors author ON author.pdf_id = pdf.id ";
				$sql .= "WHERE keywords LIKE :query GROUP BY id;";
				
				$post['search'] = '%'.$post['search'].'%';
				
			}

			MAIN::$DB->prepare( $sql );
			
			MAIN::$DB->bind_param( 'query' , $post['search'] );
			
			try{
			
				MAIN::$DB->exe( );
				
				if( MAIN::$DB->nr() > 0 ){
					
					Main::$Display->set('result', MAIN::$DB->fa());
				
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
			
			Main::$Display->set('result', 'Keine sucheingabe.');
			
		}
	}
}
?>