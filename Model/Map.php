<?php
/**
 * A PHP file which magages the Map visualization.
 */
 
/**
 * A class which magages the Map visualization
 *
 * @category   Model
 * @package    Model
 * @author     Tim Steinbrecher
 * @version    Release: 1.0
 */
class Model_Map{
	
	 /**
     * Constructor
     *
     * @access public
     */
	public function __construct(){
		
		Main::$Display->setTitle('Karte');
		Main::$Display->setHeadline('Karte');
		Main::$Display->setTemplate('Design/Map.phtml');
		
		#Main::$Display->addJs('https://maps.googleapis.com/maps/api/js?key=AIzaSyAQjuOKfwl6Ri7o8dpER7WPd1-5mBdYrjY&libraries=visualization,drawing&callback=initMap');
		Main::$Display->addJs('https://maps.googleapis.com/maps/api/js?key=AIzaSyAQjuOKfwl6Ri7o8dpER7WPd1-5mBdYrjY&libraries=visualization,drawing');
		Main::$Display->addJs('data/js/maps.google.polygon.containsLatLng.js');
		Main::$Display->addJs('data/js/markerclusterer.min.js');
		
		$this->getLocation();
		
	}
	
	 /**
     * Get Map Lat Lng
     *
     * @access private
     */
	private function getLocation(){
		
		if( isset( $_GET['id'] ) ){
			
			$sql  = "SELECT pdf.id, pdf.title, loc.lat, loc.lng FROM pdf_location loc ";
			$sql .= "LEFT JOIN pdf pdf ON pdf.id = loc.pdf_id ";
			$sql .= "WHERE  pdf.id = :id LIMIT 1;";
			
			try{
				
				MAIN::$DB->prepare( $sql );
				MAIN::$DB->bind_param( 'id', $_GET['id'] );
				MAIN::$DB->exe( );
			
				if( MAIN::$DB->nr() > 0 ){
					
					$res = MAIN::$DB->fas();
					
					Main::$Display->set('location', $res );
					Main::$Display->set('title', $res['title'] );
				
				}
				else{
					
					Main::$Display->set('locations', false);
				
				}
			}
			catch(PDOException $e){
				
				echo $e->getMessage();
				
			}	
		}
		elseif( isset( $_GET['ids'] ) ){
			
			$sql  = "SELECT pdf.id, pdf.title, loc.lat, loc.lng FROM pdf_location loc ";
			$sql .= "LEFT JOIN pdf pdf ON pdf.id = loc.pdf_id ";
			$sql .= "WHERE  pdf.id IN ( ".$_GET['ids']." );";
			
			try{
				
				MAIN::$DB->prepare( $sql );
				MAIN::$DB->exe( );
			
				if( MAIN::$DB->nr() > 0 ){
					
					Main::$Display->set('locations', MAIN::$DB->fa());
					
					if( isset( $_GET['src']) ){
						
						Main::$Display->set('title', 'Karte ausgewählt Dokumente von der Karte' );
						
					}
					else{
						
						Main::$Display->set('title', 'Karte ausgewählt Dokumente' );
						
					}
				
				}
				else{
					
					Main::$Display->set('locations', false);
				
				}
			}
			catch(PDOException $e){
				
				echo $e->getMessage();
				
			}	
		}
		else{
			
			$sql  = "SELECT pdf.id, pdf.title, loc.lat, loc.lng FROM pdf_location loc ";
			$sql .= "LEFT JOIN pdf pdf ON pdf.id = loc.pdf_id;";
			
			try{
				
				MAIN::$DB->prepare( $sql );
				MAIN::$DB->exe( );
			
				if( MAIN::$DB->nr() > 0 ){
					
					Main::$Display->set('locations', MAIN::$DB->fa());
					Main::$Display->set('title', 'Karte alle Dokumente' );
				
				}
				else{
					
					Main::$Display->set('locations', false);
				
				}
			}
			catch(PDOException $e){
				
				echo $e->getMessage();
				
			}	
		}		
	}	
}
?>