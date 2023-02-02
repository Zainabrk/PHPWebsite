<?php
/**
 * A PHP file which magages the Download
 */
 
/**
 * A class which magages the file Download
 *
 * @category   Model
 * @package    Model
 * @author     Tim Steinbrecher
 * @version    Release: 1.0
 */
class Model_Download{
	
	 /**
     * Constructor
     *
     * @access public
     */
	public function __construct(){
		
		Main::$Display->setTitle('Download');
		Main::$Display->setHeadline('Download');
		Main::$Display->setTemplate('Design/Download.phtml');
		
		$this->getDownload();
		
	}
	
	 /**
     * Manage the Download
     *
     * @access private
     */
	private function getDownload(){
		
		if( isset( $_GET['ids'] ) ){
					
			$sql  = "SELECT id, filename, title FROM pdf ";
			$sql .= "WHERE id IN (".$_GET['ids'].") ";
			$sql .= "ORDER BY id ASC;";
			
			try{
				
				MAIN::$DB->prepare( $sql );
				MAIN::$DB->exe();
				
				if( MAIN::$DB->nr() > 0 ){
					
					Main::$Display->set('download', MAIN::$DB->fa() );
					
				}
				else{
					
					Main::$Display->set('download', 'Nicht vorhanden.');
					
				}
			}
			catch(PDOException $e){
				
				echo $e->getMessage();
				
			}
		}
		elseif( isset( $_GET['zip'] ) ){

			$zip = new ZipArchive;
			
			$sql  = "SELECT id, filename, title FROM pdf ";
			$sql .= "WHERE id IN (".$_GET['zip'].") ";
			$sql .= "ORDER BY id ASC;";
			
			try{
				
				MAIN::$DB->prepare( $sql );
				MAIN::$DB->exe();
				
				if( MAIN::$DB->nr() > 0 ){
					
					$download = MAIN::$DB->fa();
					
					$zipname = 'Documents.zip';
					$zippath = getcwd().'/Documents/Zip/';
					$zipfile = $zippath.$zipname;
					
					$docpath = getcwd().'/Documents/';
					
					if( $zip->open( $zipfile,  ZipArchive::CREATE ) ) {
				
						foreach( $download as $file ){
							$zip->addFile( $docpath.$file['filename'], $file['filename'] );
						}
						$zip->close();
						header('Content-disposition: attachment; filename='.$zipname);
						header('Content-type: application/zip');
						@readfile( $zipfile );
						
					}
					else {
						
						header('Location: Download?ids='.$_GET['zip']);
						
					}
				}
				else{
					
					Main::$Display->set('download', 'Nicht vorhanden.');
					
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
					
					Main::$Display->set('download', MAIN::$DB->fa() );
					
				}
				else{
					
					Main::$Display->set('download', 'Nicht vorhanden.');
					
				}
			}
			catch(PDOException $e){
				
				echo $e->getMessage();
				
			}
		}
	}
}
?>