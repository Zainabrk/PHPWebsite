<?php
/**
 * A PHP file which magages the Wordcloud visualization.
 */
 
/**
 * A class which magages the Wordcloud visualization
 *
 * @category   Model
 * @package    Model
 * @author     Tim Steinbrecher
 * @version    Release: 1.0
 */
class Model_Cloud{
	
	 /**
     * Constructor
     *
     * @access public
     * @return true on Uploading file
     */
	public function __construct(){
		
		Main::$Display->setTitle('Cloud');
		Main::$Display->setHeadline('Cloud');
		Main::$Display->setTemplate('Design/Cloud.phtml');
		
		$this->getCloud();
		
	}
	
	 /**
     * Get Words for Wordcloud
     *
     * @access private
     */
	private function getCloud(){
		
		if( isset( $_GET['id'] ) ){
			
			$sql1 = "SELECT word, count FROM ( SELECT * FROM pdf_words WHERE pdf_id = :id AND type = 'F' ORDER BY count DESC Limit 100 ) a ORDER BY RAND(); ";
			$sql2 = "SELECT MAX(count) AS max, MIN(count) AS min FROM pdf_words WHERE pdf_id = :id AND type = 'F' ORDER BY count DESC Limit 100; ";
			$sql3 = "SELECT id, title FROM pdf WHERE id = :id; ";
			
			try{
				
				MAIN::$DB->prepare( $sql1 );
				MAIN::$DB->bind_param( 'id', $_GET['id'] );
				MAIN::$DB->exe();
				
				if( MAIN::$DB->nr() > 0 ){
					
					Main::$Display->set('cloud', MAIN::$DB->fa() );
					
					MAIN::$DB->prepare( $sql2 );
					MAIN::$DB->bind_param( 'id', $_GET['id'] );
					MAIN::$DB->exe();
					$max = MAIN::$DB->fas();
					Main::$Display->set('max', $max['max']);
					Main::$Display->set('min', $max['min']);
					
					MAIN::$DB->prepare( $sql3 );
					MAIN::$DB->bind_param( 'id', $_GET['id'] );
					MAIN::$DB->exe();
					Main::$Display->set('info', MAIN::$DB->fas());
				}

			}
			catch(PDOException $e){
				
				echo $e->getMessage();
				
			}
			
		}
		elseif( isset( $_GET['ids'] ) ){
			
			$sql1  = "SELECT pdf.id as id, word.word, word.count FROM ";
			$sql1 .= "( SELECT * FROM pdf_words WHERE pdf_id IN ( ".$_GET['ids']." ) ORDER BY count DESC Limit 100 ) word ";
			$sql1 .= "LEFT JOIN pdf pdf ON pdf.id = word.pdf_id ORDER BY RAND(); ";
			$sql2 = "SELECT MAX(count) as max, MIN(count) as min FROM pdf_words WHERE pdf_id IN ( ".$_GET['ids']." ) ORDER BY count DESC Limit 100; ";
			
			try{
				
				MAIN::$DB->prepare( $sql1 );
				#MAIN::$DB->bind_param( 'id', $_GET['id'] );
				MAIN::$DB->exe();
				
				if( MAIN::$DB->nr() > 0 ){
					
					Main::$Display->set('cloud2', MAIN::$DB->fa() );
					
					MAIN::$DB->prepare( $sql2 );
					#MAIN::$DB->bind_param( 'id', $_GET['id'] );
					MAIN::$DB->exe();
					$max = MAIN::$DB->fas();
					Main::$Display->set('max', $max['max']);
					Main::$Display->set('min', $max['min']);
					
					#MAIN::$DB->prepare( $sql3 );
					#MAIN::$DB->bind_param( 'id', $_GET['id'] );
					#MAIN::$DB->exe();
					#Main::$Display->set('info', MAIN::$DB->fas());
					
					if( isset( $_GET['src']) ){
						
						Main::$Display->set('title', 'Wordcloud über ausgewählt Dokumente aus der Karte');
						
					}
					else{
						
						Main::$Display->set('title', 'Wordcloud über ausgewählt Dokumente');
						
					}
					
				}

			}
			catch(PDOException $e){
				
				echo $e->getMessage();
				
			}
			
		}
		else{
			
			#$sql1  = "SELECT pdf.id as id, word.word, word.count FROM ";
			#$sql1 .= "( SELECT * FROM pdf_words ORDER BY count DESC Limit 100 ) word ";
			#$sql1 .= "LEFT JOIN pdf pdf ON pdf.id = word.pdf_id ORDER BY RAND(); ";
			$sql1  = "SELECT word.ids AS ids, word.word, word.counts AS count ";
			$sql1 .= "FROM ( SELECT GROUP_CONCAT( DISTINCT pdf_id SEPARATOR ',' ) AS ids, word, SUM( count ) AS counts ";
			$sql1 .= "FROM pdf_words GROUP BY word ORDER BY count DESC LIMIT 100 ) AS word ";
			$sql1 .= "GROUP BY word.word ORDER BY RAND();";
			$sql2 = "SELECT MAX(count) as max, MIN(count) as min FROM pdf_words ORDER BY count DESC Limit 100;";
			
			try{
				
				MAIN::$DB->prepare( $sql1 );
				MAIN::$DB->exe();
				
				if( MAIN::$DB->nr() > 0 ){
					
					Main::$Display->set('cloud3', MAIN::$DB->fa() );
					
					MAIN::$DB->prepare( $sql2 );
					MAIN::$DB->exe();
					$max = MAIN::$DB->fas();
					Main::$Display->set('max', $max['max']);
					Main::$Display->set('min', $max['min']);
					
				}

			}
			catch(PDOException $e){
				
				echo $e->getMessage();
				
			}
		}
	}
}
?>