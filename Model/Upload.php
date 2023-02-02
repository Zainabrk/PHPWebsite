<?php
/**
 * A PHP file which magages the Upload and PDF file Data extraction
 */
 
/**
 * A class which magages the Upload and PDF file Data extraction
 *
 * @category   Model
 * @package    Model
 * @author     Tim Steinbrecher
 * @version    Release: 1.0
 */
class Model_Upload
{
	
	/**
     * Int with the PDF ID
	 *
     * @access private
     * @var int $pdfId
     */
	private $pdfId;
	
	/**
     * Array with the Meta Data
     *
     * @access private
     * @var array
     */
	private $pdfMeta = array();
	
	/**
     * String with PDF TEXT Data
     *
     * @access private
     * @var string
     */
	private $pdfRaw;
	
	/**
     * String with PDF Language
     *
     * @access private
     * @var string
     */
	private $pdfLang;
	
	/**
     * Array with PDF main Data
     *
     * @access private
     * @var array
     */
	private $pdfData = array();
	
	/**
     * String with upload message
     *
     * @access private
     * @var string
     */
	private $uploadMsg;
	
	 /**
     * Constructor
     *
     * @access public
     * @return true on Uploading file
     */
	public function __construct()
	{

		if( !empty( $_FILES ) ){
			
			$this->postUpload();
			
			return true;
			
		}
		
		Main::$Display->setTitle('Upload');
		Main::$Display->setHeadline('Upload');
		Main::$Display->setTemplate('Design/Upload.phtml');
		Main::$Display->addJs('data/js/dropzone.js');
		Main::$Display->addJs('data/js/dropzone.cfg.js');
		Main::$Display->addCss('data/css/dropzone.css');
		
	}
	
	 /**
     * Manage the upload process
     * Checks if the file exits.
     * Uploads the file.
     * Checks if the file exits in the DB.
     * Initializes the parser.
     *
     * @access private
     * @return boolean false on error
     */
	private function postUpload()
	{
		
		$uploaddir = getcwd().'/Documents/';
		$uploadfile = $uploaddir . $_FILES['file']['name'];
		
		$file = $this->checkFile( $uploadfile );
		
		if( $file === true ){
			
			echo json_encode(array('code' => 400, 'msg' => $this->uploadMsg ) );
			
			return false;
			
		}

		if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
			
			$hash = $this->checkHash( $uploadfile );
			
			if( $hash === true ){

				echo json_encode(array('code' => 400, 'msg' => $this->uploadMsg ) );
				
				return false;
				
			}
			
			$parse = $this->initParser( $uploadfile );
			
			if( $parse ){
				
				echo json_encode(array('code' => 200 ) );
				
			}
			else{
				
				echo json_encode(array('code' => 400, 'msg' => $parse ) );
				
			}			
		}
		
		else {
			
			echo json_encode(array('code' => 400 ) );
			
		}
	}
	
    /**
     * Initializes the parser
     *
     * @access private
     * @param string $pdf PDF file name
     * @return boolean true on success
     */
	private function initParser( $pdf )
	{
				
		$this->pdf = $pdf;
		
		$this->collectData();
		
		return true;
		
	}
	 
	 /**
     * Collect all data from PDF
     *
     * @access private
     */
	private function collectData()
	{

		$this->getMeta();

		$this->getRawText();

		$this->getLanguage();
		
		$this->getTitle();
		
		$this->getSubject();
	
		$this->getKeywords();

		$this->getAbstract();

		$this->getYear();

		$this->insertPdfData();

		$this->getWordList( $this->pdfData['abstract'] , 'A' );
		
		$this->getAuthorMeta();

		$this->getReferences();
		
		$this->getBibTex();

		$this->getLocation();

		$this->getWordList( $this->pdfRaw, 'F' );

	}
	
	/**
     * Check if file exists
     *
     * @access private
     * @param string $pdf PDF file name
     * @return boolean true on success, false on error
     */
	private function checkFile( $pdf )
	{
	
		if( file_exists( $pdf ) ){
			
			$this->uploadMsg = "Dokument existiert bereits!";
			return true;
			
		}
		
		return false;
		
	}
		
	/**
     * Check if Hash from file exists in DB
     *
     * @access private
     * @param string $file PDF file
     * @return boolean true on success, false on error
     */
	private function checkHash( $file )
	{
		
		$hash = $this->getHash( $file );

		$sql = "SELECT * FROM `pdf` WHERE hash= '" . $hash . "' ;";

		MAIN::$DB->prepare( $sql );
		MAIN::$DB->exe( );
		
		if( MAIN::$DB->nr( ) > 0 ){
			
			$this->uploadMsg = "Dokument wurde bereits eingelesen!";
			
			return true;
			
		}
		
		$this->pdfData['hash'] = $hash;
		
		return false;
		
	}
			
	/**
     * Set the new PDF ID from DB 
     *
     * @access private
     * @param int $id PDF DB ID
     */
	private function setPdfId( $id )
	{
		
		$this->pdfId = $id;
		
	}
	
	/**
     * Hashes the PDF file
     *
     * @access private
     * @param string $file PDF file
     * @return string PDF hash
     */
	private function getHash( $file )
	{
		
		$hash = hash_file('md5', $file );

		return $hash;
		
	}
	
	/**
     * Deteckts the PDF language and stored in pdfData array
     *
     * @access private
     * @return boolean true on success
     */
	private function getLanguage()
	{
		
		$l = new TextLanguageDetect_TextLanguageDetect();
		
		try {

			$l->setNameMode(2);

			$result = $l->detect($this->pdfRaw, 4);

			if( !empty( $result ) ){
				
				$lang = max( $result );
				$this->pdfData['lang'] = array_search($lang, $result);
				
				return true;
				
			}
			
			return false;

		} catch (Exception $e) {
			
			echo $e->getMessage();
			
		}
	}
	
	/**
     * Inserts PDF Main Data into DB
     *
     * @access private
     * @return boolean false on error
     */
	private function insertPdfData()
	{
		
		$sql  = "INSERT INTO `pdf` ( filename, title, subject, keywords, date, year, language, raw, abstract, hash ) ";
		$sql .= "VALUES ( :filename ,  :title , :subject , :keywords , :date , :year , :lang , :raw , :abstract , :hash ) ;";

		MAIN::$DB->prepare( $sql );
		
		$filename = explode( '/', $this->pdf );
		$filename = end( $filename );
		
		MAIN::$DB->bind_param( 'filename' , $filename );
		MAIN::$DB->bind_param( 'title' , $this->pdfData['title'] );
		MAIN::$DB->bind_param( 'subject' , $this->pdfData['subject'] );
		MAIN::$DB->bind_param( 'date' , $this->pdfData['date'] );
		MAIN::$DB->bind_param( 'year' , $this->pdfData['year'] );
		MAIN::$DB->bind_param( 'keywords' , $this->pdfData['keywords'] );
		MAIN::$DB->bind_param( 'raw' , $this->pdfRaw );
		MAIN::$DB->bind_param( 'lang' , $this->pdfData['lang'] );
		MAIN::$DB->bind_param( 'abstract' , $this->pdfData['abstract'] );
		MAIN::$DB->bind_param( 'hash' , $this->pdfData['hash'] );
		
		try{
			
			MAIN::$DB->exe( );
			
			$this->setPdfId( MAIN::$DB->lastInsertId() );

		}
		catch(PDOException $e){

			return false;
			
		}
	}
	
	/**
     * Retrieves PDF title and stored in pdfData array
     *
     * @access private
     */
	private function getTitle()
	{
		
		$this->pdfData['title'] = $this->getTitleMeta();
		
		if( $this->pdfData['title'] === false ){ 
		
			$this->pdfData['title'] =  $this->getTitleExtract();
			
			if( $this->pdfData['title'] === false ){ 
			
				$this->pdfData['title'] = NULL;
				
			}
		}
	}
	
	/**
     * Retrieves PDF date and year and stored in pdfData array
     *
     * @access private
     * @return boolean true on success
     */
	private function getYear()
	{

		if( !isset( $this->pdfMeta['CreationDate'] ) || trim( $this->pdfMeta['CreationDate'] ) == "" ){

			$this->pdfData['date'] = NULL;
			$this->pdfData['year'] = NULL;
			
			return false;
			
		}
		
		$date =  $this->parse( "(" . $this->pdfMeta['CreationDate'] . ")" );

		if( $date !== false ){
		
			$this->pdfData['date'] = $date->format('Y-m-d');
			$this->pdfData['year'] = $date->format('Y');

			return true;
			
		}
	
		$d = new PHPFindDateInString_FindDate();
		$date = $d->finddate($this->pdfMeta['CreationDate']);
		
		$this->pdfData['date'] = $date['year'].'-'.$date['month'].'-'.$date['day'];
		$this->pdfData['year'] = $date['year'];
		
		return true;
		
	}
	
	/**
     * Pases the PDF creation Date from Meta Data 
     *
     * @access private
     * @param string $content PDF Meta Data
     * @return boolean false on error
     */	
	private function parse( $content )
	{
		
        if (preg_match('/^\s*\(D\:(?P<name>.*?)\)/s', $content, $match)) {
			
            $name = $match['name'];
            $name = str_replace("'", '', $name);
            $date = false;
			$formats = array(
				4  => 'Y',
				6  => 'Ym',
				8  => 'Ymd',
				10 => 'YmdH',
				12 => 'YmdHi',
				14 => 'YmdHis',
				15 => 'YmdHise',
				17 => 'YmdHisO',
				18 => 'YmdHisO',
				19 => 'YmdHisO',
			);
			
            if (preg_match('/^\d{4}(\d{2}(\d{2}(\d{2}(\d{2}(\d{2}(Z(\d{2,4})?|[\+-]?\d{2}(\d{2})?)?)?)?)?)?)?$/', $name)) {
				
                if ($pos = strpos($name, 'Z')) {
					
                    $name = substr($name, 0, $pos + 1);
					
                } elseif (strlen($name) == 18 && preg_match('/[^\+-]0000$/', $name)) {
					
                    $name = substr($name, 0, -4) . '+0000';
					
                }
				
                $format = $formats[strlen($name)];
                $date   = \DateTime::createFromFormat($format, $name);
				
            } else {
                
                if (preg_match('/^\d{1,2}-\d{1,2}-\d{4},?\s+\d{2}:\d{2}:\d{2}[\+-]\d{4}$/', $name)) {
					
                    $name   = str_replace(',', '', $name);
                    $format = 'n-j-Y H:i:sO';
                    $date   = \DateTime::createFromFormat($format, $name);
					
                }
            }
			
            if (!$date) {
				
                return false;
				
            }
			
			return $date;
			
        }
		
        return false;
		
    }
	
	/**
     * Sets the PDF Title from Meta Data 
     *
     * @access private
     * @return mixed false on error
     */	
	private function getTitleMeta()
	{
				
		if( !isset( $this->pdfMeta['Title'] ) || trim( $this->pdfMeta['Title'] ) == "" ){

			return false;
			
		}
		
		return trim( $this->pdfMeta['Title'] );

	}
	
	/**
     * Extracts the PDF Title from PDF file
     *
     * @access private
     * @return string title
     */	
	private function getTitleExtract()
	{

		$out = shell_exec( 'pdf-extract extract --titles ' . escapeshellarg( $this->pdf ) );

		$xml = simplexml_load_string( $out );

		return trim( $xml->title );
	
	}
		
	/**
     * Sets the PDF Subject from Meta Data and stored in pdfData array
     *
     * @access private
     * @return boolean true on success, false on error
     */	
	private function getSubject()
	{
				
		if( !isset( $this->pdfMeta['Subject'] ) || trim( $this->pdfMeta['Subject'] ) == "" ){

			$this->pdfData['subject'] = NULL;

			return false;
			
		}
		
		$this->pdfData['subject'] = trim( $this->pdfMeta['Subject'] );
		
		return true;;

	}
	
	/**
     * Sets the PDF keywords and stored in pdfData array
     *
     * @access private
     */	
	private function getKeywords()
	{
		
		$this->pdfData['keywords'] = $this->getKeywordsMeta();
		
		if( $this->pdfData['keywords'] === false ){ 
		
			$this->pdfData['keywords'] =  $this->getKeywordsRaw();
			
			if( $this->pdfData['keywords'] === false ){ 
			
				$this->pdfData['keywords'] = NULL;
				
			}
		}
	}
	
	/**
     * Check and gets the PDF Keywords from Meta Data 
     *
     * @access private
     * @return mixed false on error
     */	
	private function getKeywordsMeta()
	{
		
		if( !isset( $this->pdfMeta['Keywords'] ) || trim( $this->pdfMeta['Keywords'] ) == "" ){

			return false;
			
		}
		
		$keywords = preg_split("/\s?(,|\.|-|\/|·|•|;|:|_|\*)\s/", $this->pdfMeta['Keywords'] );
		$keywords = array_map( 'trim', $keywords );
		
		return json_encode( $keywords );
		
	}
	
	/**
     * Check and gets the PDF Keywords from Rawtext
     *
     * @access private
     * @return mixed false on error
     */	
	private function getKeywordsRaw()
	{
		
		if( preg_match("/keywords?[:|-]?\s?(.{10,}?)(\n|\r\n){2}/msi", $this->pdfRaw, $keywords ) ){

			$keywords = str_replace( array("\n", "\r"), '', $keywords[1] );
			$keywords = preg_split("/\s?(,|\.|-|\/|·|•|;|:|_|\*)\s/", $keywords );
			
			if( count( $keywords ) > 0 ){
				
				return json_encode( $keywords );
				
			}
		}
		
		return false;
		
	}
	
	/**
     * Check and gets the PDF Abstract from Rawtext and stored in pdfData array
     *
     * @access private
     * @return boolean true on success, false on error
     */	
	private function getAbstract()
	{
		
		if( preg_match("/abstra[c|k]t[:|-]?\s?(.{10,}?)(\n|\r\n){2}/msi", $this->pdfRaw, $raw ) ){
			
			$this->pdfData['abstract'] = $raw[1];
			
			return true;
			
		}
		
		$this->pdfData['abstract'] = NULL;
		
		return false;
		
	}
	
	/**
     * Check and gets the PDF Author from Meta Data and stored in pdfData array
     *
     * @access private
     * @return boolean false on error
     */	
	private function getAuthorMeta()
	{
		
		
		if( !isset( $this->pdfMeta['Author'] ) || trim( $this->pdfMeta['Author'] ) == "" ){

			return false;
			
		}
		
		$authors = preg_split("/\s?(,|;|&|\sand\s)\s?/", $this->pdfMeta['Author'] );
		$authors = array_map( 'trim', $authors );
		
		$this->insertAuthors( $authors );
		
		return $authors;
		
	}
	
	/**
     * Inserts the PDF Author into DB
     *
     * @access private
     * @param string $authors PDF Author
     * @return boolean false on error
     */	
	private function insertAuthors( $authors )
	{
		
		$sql = "INSERT INTO `pdf_authors` ( pdf_id, author, institution, location ) VALUES ";	
		
		foreach( $authors as $author ){
			
			$sql .= " ( " . $this->pdfId . ", '" . $author . "' , NULL, NULL ),";
		
		}

		$sql = rtrim($sql, ",") . " ; ";

		try{
			
			MAIN::$DB->query( $sql );

		}
		catch(PDOException $e){

			return false;
			
		}
	}
	
	/**
     * Checks the Locations in the PDF Raw Text
     * First checks if the domain part from E-Mail Address exists in the universities table.
     * Saves the latitude and longitude in Database.
     * If Not
     * Gets the IP from Host = Domain Part from E-Mail Address.
     * Saves the latitude and longitude in Database.
     *
     * @access private
     * @return boolean false on error
     */	
	private function getLocation(  )
	{
		
		$text = $this->getRawTextPages( 1, 2 );
		
		if( preg_match_all("/@((?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?)/mi", $text, $domains ) ){

			$domains = array_unique( $domains[1] );

			foreach( $domains as $domain ){

				$sql = "SELECT name FROM universities WHERE domain = :domain LIMIT 1;";
				
				try{
					
					MAIN::$DB->prepare( $sql );
					MAIN::$DB->bind_param( 'domain', $domain );
					MAIN::$DB->exe();
					
					if( MAIN::$DB->nr() > 0 ){
						
						$res = MAIN::$DB->fas();
						
						$geo = $this->getGeo( $res['name'] );
						
						if( $geo !== false ){
								
							$sql = "INSERT INTO pdf_location ( pdf_id, lat, lng, name ) VALUES ( :id, :lat, :lng, :name );";
							
							try{

								MAIN::$DB->prepare( $sql );
								MAIN::$DB->bind_param( 'id', $this->pdfId );
								MAIN::$DB->bind_param( 'lat', $geo['lat'] , PDO::PARAM_STR );
								MAIN::$DB->bind_param( 'lng', $geo['lng'] , PDO::PARAM_STR );
								MAIN::$DB->bind_param( 'name', $res['name'] );
								MAIN::$DB->exe();
								
							}
							catch(PDOException $e){
								
								echo $e->getMessage();
								
							}					
						}						
					}

					else{
						
						$ipinfo = new Ipinfo_Ipinfo;
						
						$ip = gethostbyname( $domain );
						
						if( $ip != $domain ){
						
							$host = $ipinfo->getFullIpDetails( $ip ) ;
							$loc = $host->getLoc();
							
							if( !empty( $loc ) ){
							
								list( $lat, $lng) = explode( ',' , $loc );
								$name = $host->getOrg();
								
								if( isset( $lat, $lng, $name ) ){
									
									$sql = "INSERT INTO pdf_location ( pdf_id, lat, lng, name ) VALUES ( :id, :lat, :lng, :name );";
									
									try{

										MAIN::$DB->prepare( $sql );
										MAIN::$DB->bind_param( 'id', $this->pdfId );
										MAIN::$DB->bind_param( 'lat', $lat , PDO::PARAM_STR );
										MAIN::$DB->bind_param( 'lng', $lng , PDO::PARAM_STR );
										MAIN::$DB->bind_param( 'name', $name );
										MAIN::$DB->exe();
										
									}
									catch(PDOException $e){
										
										echo $e->getMessage();
										
									}
								}
							}
						}
					}
				}
				catch(PDOException $e){
					
					echo $e->getMessage();
					
				}
			}
		}
		
		return false;
		
	}
	
	/**
     * Retrieves and parse the geolocation from Google Maps API
     *
     * @access private
     * @param string $address address
     * @param string $region region
     * @return mixed false on error
     */	
	private function getGeo( $address, $region = "" )
	{
		
		$address = str_replace(" ", "+", $address);

		$json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=" . $address . "&sensor=false&region=" . $region);
		$json = json_decode( $json );
		
		$loc = array();

		if( $json->{'status'} == "OK" ){

			$loc['lat'] = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
			$loc['lng'] = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
			
			return $loc;
		
		}
		
		return false;
		
	}
	
	/**
     * Extracts the Meta Data from PDF file and stored in pdfMeta array
     *
     * @access private
     */
	private function getMeta()
	{
		
		$meta = shell_exec('pdfinfo -rawdates ' . escapeshellarg( $this->pdf ) );
		
		$lines = explode("\n", $meta);
		
		array_pop( $lines );
		
		$metaArray = array();
		
		foreach( $lines as $data){

			list($key, $value) = explode(":", $data, 2 );
			
			$metaArray[ trim( $key ) ] = trim( $value );
			
		}
		
		$this->pdfMeta = $metaArray;
		
	}
	
	/**
     * Extracts the Raw Text from PDF file and stored in pdfRaw string
     *
     * @access private
     */
	private function getRawText()
	{

		$this->pdfRaw = shell_exec('pdftotext -eol dos -enc UTF-8 -nopgbrk ' . escapeshellarg( $this->pdf ) . ' -');
		
	}
	
	/**
     * Extracts the Raw Text from PDF file
     *
     * @access private
     * @param string $first first page
     * @param string $last last page
     * @return string text
     */
	private function getRawTextPages( $first, $last )
	{

		return shell_exec('pdftotext -eol dos -enc UTF-8 -nopgbrk -f ' . $first . ' -l ' . $last . ' ' . escapeshellarg( $this->pdf ) . ' -');
		
	}
	
	/**
     * Extracts the References from PDF file
     *
     * @access private
     * @return boolean true on success, false on error
     */
	private function getReferences()
	{
		
		$references = shell_exec('pdf-extract extract --references  ' . escapeshellarg( $this->pdf ) . ' -');

		try{
		
			$refxml = new SimpleXMLElement( $references );
			
			foreach( $refxml->reference as $reference ){
				$refs[] = $reference;
			}
			
			if( !empty( $refs ) ){
				
				$this->insertRefToDb( $refs );
				
				return true;
				
			}
		}
		catch(Exception $e){
								
			return false;
			
		}
		
		return false;
		
	}
	
	/**
     * Inserts the References into DB
     *
     * @access private
     * @param string $refs references
     * @return boolean false on error
     */
	private function insertRefToDb( $refs )
	{
		
		$sql  = "INSERT INTO pdf_ref ( pdf_id, reference ) VALUES ";

		foreach( $refs as $ref ){
			
			$sql .= "( " . $this->pdfId . ", ? ),";
		
		}

		$sql = rtrim($sql, ",") . " ; ";

		MAIN::$DB->prepare( $sql );

		foreach( $refs as $key => &$ref ){

			MAIN::$DB->bind_param( $key+1 , $ref, PDO::PARAM_STR );

		}
		
		try{
			
			MAIN::$DB->exe( $sql );

		}
		catch(PDOException $e){
			
			return false;
			
		}
	}
	
	/**
     * Extract the BibTex References from PDF file
     *
     * @access private
     */
	private function getBibTex()
	{
        
        $filename = explode( '/', $this->pdf );
		$filename = end( $filename );
        
		shell_exec("pdf-extract extract-bib --resolved_references " . escapeshellarg( $this->pdf ) . " --output '" . getcwd(). "/Documents/bib/" . $filename . ".bib' ");
		
		$bibtex = new Structures_BibTex();
		$bibtex->setOption('validate', true);
        $bibtex->setOption('unwrap', true);
        $bibtex->setOption('removeCurlyBraces', true);
		$bibtex->loadFile(getcwd() . '/Documents/bib/' . $filename . '.bib');

		$bibtex->parse();
        $bibtex->data;
		$this->parseBibTex( $bibtex->data );
		
	}
	
	/**
     * Parses the BibTex References
     *
     * @access private
     * @param string $parsed BibTex references
     */
	private function parseBibTex( $parsed )
	{
				
		foreach( $parsed as $data ) {
			
			$new_bib = array();
			
			if(isset($data['entryType']))
				$new_bib['type'] = $data['entryType'];
			
			if(isset($data['author']))
				$new_bib['author'] = json_encode( $data['author'] );
			
			if(isset($data['doi']))
				$new_bib['doi'] = $data['doi'];
			
			if(isset($data['title']))
				$new_bib['title'] = $data['title'];
			
			if(isset($data['booktitle']))
				$new_bib['booktitle'] = $data['booktitle'];
			
			if(isset($data['journal']))
				$new_bib['journal'] = $data['journal'];
			
			if(isset($data['publisher']))
				$new_bib['publisher'] = $data['publisher'];
			
			if(isset($data['year']))
				$new_bib['year'] = $data['year'];
			
			if(isset($data['url']))
				$new_bib['url'] = $data['url'];
			
			if(isset($data['pages']))
				$new_bib['pages'] = $data['pages'];
			
			if(isset($data['number']))
				$new_bib['number'] = $data['number'];
		
			$this->insertBibToDb( $new_bib );
			
		}
	}
	
	/**
     * Inserts the BibTex References into DB
     *
     * @access private
     * @param string $assoc BibTex references
     * @return boolean false on error
     */
	private function insertBibToDb( $assoc )
	{
	
		$cols[] = 'pdf_id';
		$vals[] = $this->pdfId;
	
		foreach ($assoc as $column => $value) {
			
			$cols[] = $column;
			$vals[] = $value;
			
		}
		
		$colnames = "`".implode("`, `", $cols)."`";
		$colvals = "'".implode("', '", $vals)."'";
		$sql = "INSERT INTO `pdf_bib` ( " . $colnames . " ) VALUES ( " . $colvals . " ) ;";

		try{
			
			MAIN::$DB->query( $sql );

		}
		catch(PDOException $e){
			
			return false;
			
		}
	}
	
	/**
     * Extracts the Wordlist from PDF Raw Text or Abstract
     *
     * @access private
	 * @param string $text The String to get the Wordlist from
	 * @param string $type Abstract or Raw Text
     */
	private function getWordList( $text, $type )
	{
		
		if( is_null( $text ) ){
			
			return false;
			
		}
		
		$rexpArray = array();
	
		$rexpArray[] = "/(\r\n|\n|\r)/m";
		$rexpArray[] = "/\.\s+/";
		$rexpArray[] = "/[,;\:\"\“\?\!\$%#_\{\}\&\=\+\(\)\^\[\]\<\>\*\|]/";
		$rexpArray[] = "/[\›\·\•\.\…\—\«\»\©\®\¿\±\¬\¢\£\¥\§\¯\°\-\–]/";
		$rexpArray[] = "/”/";
		$rexpArray[] = "/\s+/";
		$rexpArray[] = "/\.\./";
		$rexpArray[] = "/\.\.\./";
		$rexpArray[] = "/' /";
		$rexpArray[] = "/ '/";
		$rexpArray[] = "/'/";
		// filter Single Letters
		$rexpArray[] = "/(^|\s+)(\S(\s+|$))+/";
		// filter Roman numbers from 1 to 3999.
		$rexpArray[] = "/\s(?=[MDCLXVI])(M{0,3})(C[DM]|D?C{0,3})(X[LC]|L?X{0,3})(I[VX]|V?I{0,3})\s/i";
		
		$text = preg_replace( $rexpArray, " ", $text );
		
		$text = trim( $text );
		
		$wordlist = array_count_values( str_word_count( $text , 1, 'ÄäÖöÜüßñ' ) );
		
		$stopwordsEn = array("a", "able", "about", "above", "according", "accordingly", "across", "actually", "after", "afterwards", "again", "against", "ain't", "all", "allow", "allows", "almost", "alone", "along", "already", "also", "although", "always", "am", "among", "amongst", "an", "and", "another", "any", "anybody", "anyhow", "anyone", "anything", "anyway", "anyways", "anywhere", "apart", "appear", "appreciate", "appropriate", "are", "aren't", "around", "as", "aside", "ask", "asking", "associated", "at", "available", "away", "awfully", "b", "be", "became", "because", "become", "becomes", "becoming", "been", "before", "beforehand", "behind", "being", "believe", "below", "beside", "besides", "best", "better", "between", "beyond", "both", "brief", "but", "by", "c", "c'mon", "c's", "came", "can", "can't", "cannot", "cant", "cause", "causes", "certain", "certainly", "changes", "clearly", "co", "com", "come", "comes", "concerning", "consequently", "consider", "considering", "contain", "containing", "contains", "corresponding", "could", "couldn't", "course", "currently", "d", "definitely", "described", "despite", "did", "didn't", "different", "do", "does", "doesn't", "doing", "don't", "done", "down", "downwards", "during", "e", "each", "edu", "eg", "eight", "either", "else", "elsewhere", "enough", "entirely", "especially", "et", "etc", "even", "ever", "every", "everybody", "everyone", "everything", "everywhere", "ex", "exactly", "example", "except", "f", "far", "few", "fifth", "first", "five", "followed", "following", "follows", "for", "former", "formerly", "forth", "four", "from", "further", "furthermore", "g", "get", "gets", "getting", "given", "gives", "go", "goes", "going", "gone", "got", "gotten", "greetings", "h", "had", "hadn't", "happens", "hardly", "has", "hasn't", "have", "haven't", "having", "he", "he's", "hello", "help", "hence", "her", "here", "here's", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "hi", "him", "himself", "his", "hither", "hopefully", "how", "howbeit", "however", "i", "i'd", "i'll", "i'm", "i've", "ie", "if", "ignored", "immediate", "in", "inasmuch", "inc", "indeed", "indicate", "indicated", "indicates", "inner", "insofar", "instead", "into", "inward", "is", "isn't", "it", "it'd", "it'll", "it's", "its", "itself", "j", "just", "k", "keep", "keeps", "kept", "know", "knows", "known", "l", "last", "lately", "later", "latter", "latterly", "least", "less", "lest", "let", "let's", "like", "liked", "likely", "little", "look", "looking", "looks", "ltd", "m", "mainly", "many", "may", "maybe", "me", "mean", "meanwhile", "merely", "might", "more", "moreover", "most", "mostly", "much", "must", "my", "myself", "n", "name", "namely", "nd", "near", "nearly", "necessary", "need", "needs", "neither", "never", "nevertheless", "new", "next", "nine", "no", "nobody", "non", "none", "noone", "nor", "normally", "not", "nothing", "novel", "now", "nowhere", "o", "obviously", "of", "off", "often", "oh", "ok", "okay", "old", "on", "once", "one", "ones", "only", "onto", "or", "other", "others", "otherwise", "ought", "our", "ours", "ourselves", "out", "outside", "over", "overall", "own", "p", "particular", "particularly", "per", "perhaps", "placed", "please", "plus", "possible", "presumably", "probably", "provides", "q", "que", "quite", "qv", "r", "rather", "rd", "re", "really", "reasonably", "regarding", "regardless", "regards", "relatively", "respectively", "right", "s", "said", "same", "saw", "say", "saying", "says", "second", "secondly", "see", "seeing", "seem", "seemed", "seeming", "seems", "seen", "self", "selves", "sensible", "sent", "serious", "seriously", "seven", "several", "shall", "she", "should", "shouldn't", "since", "six", "so", "some", "somebody", "somehow", "someone", "something", "sometime", "sometimes", "somewhat", "somewhere", "soon", "sorry", "specified", "specify", "specifying", "still", "sub", "such", "sup", "sure", "t", "t's", "take", "taken", "tell", "tends", "th", "than", "thank", "thanks", "thanx", "that", "that's", "thats", "the", "their", "theirs", "them", "themselves", "then", "thence", "there", "there's", "thereafter", "thereby", "therefore", "therein", "theres", "thereupon", "these", "they", "they'd", "they'll", "they're", "they've", "think", "third", "this", "thorough", "thoroughly", "those", "though", "three", "through", "throughout", "thru", "thus", "to", "together", "too", "took", "toward", "towards", "tried", "tries", "truly", "try", "trying", "twice", "two", "u", "un", "under", "unfortunately", "unless", "unlikely", "until", "unto", "up", "upon", "us", "use", "used", "useful", "uses", "using", "usually", "uucp", "v", "value", "various", "very", "via", "viz", "vs", "w", "want", "wants", "was", "wasn't", "way", "we", "we'd", "we'll", "we're", "we've", "welcome", "well", "went", "were", "weren't", "what", "what's", "whatever", "when", "whence", "whenever", "where", "where's", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "who's", "whoever", "whole", "whom","whose", "why", "will", "willing", "wish", "with", "within", "without", "won't", "wonder", "would", "would", "wouldn't", "x", "y", "yes", "yet", "you", "you'd", "you'll", "you're", "you've", "your", "yours", "yourself", "yourselves", "z", "zero");
		$stopwordsEnU = array_map('ucfirst', $stopwordsEn);
		$stopwordsDe = array("ab", "bei", "da", "deshalb", "ein", "für", "haben", "hier", "ich", "ja", "kann", "machen", "muesste", "nach", "oder", "seid", "sonst", "und", "vom", "wann", "wenn", "wie", "zu", "bin", "eines", "hat", "manche", "solches", "an", "anderm", "bis", "das", "deinem", "demselben", "dir", "doch", "einig", "er", "eurer", "hatte", "ihnen", "ihre", "ins", "jenen", "keinen", "manchem", "meinen", "nichts", "seine", "soll", "unserm", "welche", "werden", "wollte", "während", "alle", "allem", "allen", "aller", "alles", "als", "also", "am", "ander", "andere", "anderem", "anderen", "anderer", "anderes", "andern", "anders", "auch", "auf", "aus", "bist", "bsp.", "daher", "damit", "dann", "dasselbe", "dazu", "daß", "dein", "deine", "deinen", "deiner", "deines", "dem", "den", "denn", "denselben", "der", "derer", "derselbe", "derselben", "des", "desselben", "dessen", "dich", "die", "dies", "diese", "dieselbe", "dieselben", "diesem", "diesen", "dieser", "dieses", "dort", "du", "durch", "eine", "einem", "einen", "einer", "einige", "einigem", "einigen", "einiger", "einiges", "einmal", "es", "etwas", "euch", "euer", "eure", "eurem", "euren", "eures", "ganz", "ganze", "ganzen", "ganzer", "ganzes", "gegen", "gemacht", "gesagt", "gesehen", "gewesen", "gewollt", "hab", "habe", "hatten", "hin", "hinter", "ihm", "ihn", "ihr", "ihrem", "ihren", "ihrer", "ihres", "im", "in", "indem", "ist", "jede", "jedem", "jeden", "jeder", "jedes", "jene", "jenem", "jener", "jenes", "jetzt", "kein", "keine", "keinem", "keiner", "keines", "konnte", "können", "könnte", "mache", "machst", "macht", "machte", "machten", "man", "manchen", "mancher", "manches", "mein", "meine", "meinem", "meiner", "meines", "mich", "mir", "mit", "muss", "musste", "müßt", "nicht", "noch", "nun", "nur", "ob", "ohne", "sage", "sagen", "sagt", "sagte", "sagten", "sagtest", "sehe", "sehen", "sehr", "seht", "sein", "seinem", "seinen", "seiner", "seines", "selbst", "sich", "sicher", "sie", "sind", "so", "solche", "solchem", "solchen", "solcher", "sollte", "sondern", "um", "uns", "unse", "unsen", "unser", "unses", "unter", "viel", "von", "vor", "war", "waren", "warst", "was", "weg", "weil", "weiter", "welchem", "welchen", "welcher", "welches", "werde", "wieder", "will", "wir", "wird", "wirst", "wo", "wolle", "wollen", "wollt", "wollten", "wolltest", "wolltet", "würde", "würden", "z.B.", "zum", "zur", "zwar", "zwischen", "über", "aber", "abgerufen", "abgerufene", "abgerufener", "abgerufenes", "acht", "allein", "allerdings", "allerlei", "allgemein", "allmählich", "allzu", "alsbald", "andererseits", "andernfalls", "anerkannt", "anerkannte", "anerkannter", "anerkanntes", "anfangen", "anfing", "angefangen", "angesetze", "angesetzt", "angesetzten", "angesetzter", "ansetzen", "anstatt", "arbeiten", "aufgehört", "aufgrund", "aufhören", "aufhörte", "aufzusuchen", "ausdrücken", "ausdrückt", "ausdrückte", "ausgenommen", "ausser", "ausserdem", "author", "autor", "außen", "außer", "außerdem", "außerhalb", "bald", "bearbeite", "bearbeiten", "bearbeitete", "bearbeiteten", "bedarf", "bedurfte", "bedürfen", "befragen", "befragte", "befragten", "befragter", "begann", "beginnen", "begonnen", "behalten", "behielt", "beide", "beiden", "beiderlei", "beides", "beim", "beinahe", "beitragen", "beitrugen", "bekannt", "bekannte", "bekannter", "bekennen", "benutzt", "bereits", "berichten", "berichtet", "berichtete", "berichteten", "besonders", "besser", "bestehen", "besteht", "beträchtlich", "bevor", "bezüglich", "bietet", "bisher", "bislang", "bis", "bleiben", "blieb", "bloss", "bloß", "brachte", "brachten", "brauchen", "braucht", "bringen", "bräuchte", "bzw", "böden", "ca.", "dabei", "dadurch", "dafür", "dagegen", "dahin", "damals", "danach", "daneben", "dank", "danke", "danken", "dannen", "daran", "darauf", "daraus", "darf", "darfst", "darin", "darum", "darunter", "darüber", "darüberhinaus", "dass", "davon", "davor", "demnach", "denen", "dennoch", "derart", "derartig", "derem", "deren", "derjenige", "derjenigen", "derzeit", "desto", "deswegen", "diejenige", "diesseits", "dinge", "direkt", "direkte", "direkten", "direkter", "doppelt", "dorther", "dorthin", "drauf", "drei", "dreißig", "drin", "dritte", "drunter", "drüber", "dunklen", "durchaus", "durfte", "durften", "dürfen", "dürfte", "eben", "ebenfalls", "ebenso", "ehe", "eher", "eigenen", "eigenes", "eigentlich", "einbaün", "einerseits", "einfach", "einführen", "einführte", "einführten", "eingesetzt", "einigermaßen", "eins", "einseitig", "einseitige", "einseitigen", "einseitiger", "einst", "einstmals", "einzig", "ende", "entsprechend", "entweder", "ergänze", "ergänzen", "ergänzte", "ergänzten", "erhalten", "erhielt", "erhielten", "erhält", "erneut", "erst", "erste", "ersten", "erster", "eröffne", "eröffnen", "eröffnet", "eröffnete", "eröffnetes", "etc", "etliche", "etwa", "fall", "falls", "fand", "fast", "ferner", "finden", "findest", "findet", "folgende", "folgenden", "folgender", "folgendes", "folglich", "fordern", "fordert", "forderte", "forderten", "fortsetzen", "fortsetzt", "fortsetzte", "fortsetzten", "fragte", "frau", "frei", "freie", "freier", "freies", "fuer", "fünf", "gab", "ganzem", "gar", "gbr", "geb", "geben", "geblieben", "gebracht", "gedurft", "geehrt", "geehrte", "geehrten", "geehrter", "gefallen", "gefiel", "gefälligst", "gefällt", "gegeben", "gehabt", "gehen", "geht", "gekommen", "gekonnt", "gemocht", "gemäss", "genommen", "genug", "gern", "gestern", "gestrige", "getan", "geteilt", "geteilte", "getragen", "gewissermaßen", "geworden", "ggf", "gib", "gibt", "gleich", "gleichwohl", "gleichzeitig", "glücklicherweise", "gmbh", "gratulieren", "gratuliert", "gratulierte", "gute", "guten", "gängig", "gängige", "gängigen", "gängiger", "gängiges", "gänzlich", "haette", "halb", "hallo", "hast", "hattest", "hattet", "heraus", "herein", "heute", "heutige", "hiermit", "hiesige", "hinein", "hinten", "hinterher", "hoch", "hundert", "hätt", "hätte", "hätten", "höchstens", "igitt", "immer", "immerhin", "important", "indessen", "info", "infolge", "innen", "innerhalb", "insofern", "inzwischen", "irgend", "irgendeine", "irgendwas", "irgendwen", "irgendwer", "irgendwie", "irgendwo", "je", "jedenfalls", "jederlei", "jedoch", "jemand", "jenseits", "jährig", "jährige", "jährigen", "jähriges", "kam", "kannst", "kaum", "keines", "keinerlei", "keineswegs", "klar", "klare", "klaren", "klares", "klein", "kleinen", "kleiner", "kleines", "koennen", "koennt", "koennte", "koennten", "komme", "kommen", "kommt", "konkret", "konkrete", "konkreten", "konkreter", "konkretes", "konnten", "könn", "könnt", "könnten", "künftig", "lag", "lagen", "langsam", "lassen", "laut", "lediglich", "leer", "legen", "legte", "legten", "leicht", "leider", "lesen", "letze", "letzten", "letztendlich", "letztens", "letztes", "letztlich", "lichten", "liegt", "liest", "links", "längst", "längstens", "mag", "magst", "mal", "mancherorts", "manchmal", "mann", "margin", "mehr", "mehrere", "meist", "meiste", "meisten", "meta", "mindestens", "mithin", "mochte", "morgen", "morgige", "muessen", "muesst", "musst", "mussten", "muß", "mußt", "möchte", "möchten", "möchtest", "mögen", "möglich", "mögliche", "möglichen", "möglicher", "möglicherweise", "müssen", "müsste", "müssten", "müßte", "nachdem", "nacher", "nachhinein", "nahm", "natürlich", "nacht", "neben", "nebenan", "nehmen", "nein", "neu", "neue", "neuem", "neuen", "neuer", "neues", "neun", "nie", "niemals", "niemand", "nimm", "nimmer", "nimmt", "nirgends", "nirgendwo", "nutzen", "nutzt", "nutzung", "nächste", "nämlich", "nötigenfalls", "nützt", "oben", "oberhalb", "obgleich", "obschon", "obwohl", "oft", "per", "pfui", "plötzlich", "pro", "reagiere", "reagieren", "reagiert", "reagierte", "rechts", "regelmäßig", "rief", "rund", "sang", "sangen", "schlechter", "schließlich", "schnell", "schon", "schreibe", "schreiben", "schreibens", "schreiber", "schwierig", "schätzen", "schätzt", "schätzte", "schätzten", "sechs", "sect", "sehrwohl", "sei", "seit", "seitdem", "seite", "seiten", "seither", "selber", "senke", "senken", "senkt", "senkte", "senkten", "setzen", "setzt", "setzte", "setzten", "sicherlich", "sieben", "siebte", "siehe", "sieht", "singen", "singt", "sobald", "sodaß", "soeben", "sofern", "sofort", "sog", "sogar", "solange", "solc hen", "solch", "sollen", "sollst", "sollt", "sollten", "solltest", "somit", "sonstwo", "sooft", "soviel", "soweit", "sowie", "sowohl", "spielen", "später", "startet", "startete", "starteten", "statt", "stattdessen", "steht", "steige", "steigen", "steigt", "stets", "stieg", "stiegen", "such", "suchen", "sämtliche", "tages", "tat", "tatsächlich", "tatsächlichen", "tatsächlicher", "tatsächliches", "tausend", "teile", "teilen", "teilte", "teilten", "titel", "total", "trage", "tragen", "trotzdem", "trug", "trägt", "tun", "tust", "tut", "txt", "tät", "ueber", "umso", "unbedingt", "ungefähr", "unmöglich", "unmögliche", "unmöglichen", "unmöglicher", "unnötig", "unsem", "unser", "unsere", "unserem", "unseren", "unserer", "unseres", "unten", "unterbrach", "unterbrechen", "unterhalb", "unwichtig", "usw", "vergangen", "vergangene", "vergangener", "vergangenes", "vermag", "vermutlich", "vermögen", "verrate", "verraten", "verriet", "verrieten", "version", "versorge", "versorgen", "versorgt", "versorgte", "versorgten", "versorgtes", "veröffentlichen", "veröffentlicher", "veröffentlicht", "veröffentlichte", "veröffentlichten", "veröffentlichtes", "viele", "vielen", "vieler", "vieles", "vielleicht", "vielmals", "vier", "vollständig", "voran", "vorbei", "vorgestern", "vorher", "vorne", "vorüber", "völlig", "während", "wachen", "waere", "warum", "weder", "wegen", "weitere", "weiterem", "weiteren", "weiterer", "weiteres", "weiterhin", "weiß", "wem", "wen", "wenig", "wenige", "weniger", "wenigstens", "wenngleich", "wer", "werdet", "weshalb", "wessen", "wichtig", "wieso", "wieviel", "wiewohl", "willst", "wirklich", "wodurch", "wogegen", "woher", "wohin", "wohingegen", "wohl", "wohlweislich", "womit", "woraufhin", "woraus", "worin", "wurde", "wurden", "währenddessen", "wär", "wäre", "wären", "zahlreich", "zehn", "zeitweise", "ziehen", "zieht", "zog", "zogen", "zudem", "zuerst", "zufolge", "zugleich", "zuletzt", "zumal", "zurück", "zusammen", "zuviel", "zwanzig", "zwei", "zwölf", "ähnlich", "übel", "überall", "überallhin", "überdies", "übermorgen", "übrig", "übrigens");
		$stopwordsDeU = array_map('ucfirst', $stopwordsDe);
		
		$wordlist = array_diff_key( $wordlist, array_flip( $stopwordsEn ) );
		$wordlist = array_diff_key( $wordlist, array_flip( $stopwordsEnU ) );
		$wordlist = array_diff_key( $wordlist, array_flip( $stopwordsDe ) );
		$wordlist = array_diff_key( $wordlist, array_flip( $stopwordsDeU ) );
				
		$this->insertWordList( $wordlist, $type );
		
	}	
	
	/**
     * Inserts the Wordlist into DB
     *
     * @access private
     * @param array $wordlist Wordlist array
	 * @param string $type Abstract or Raw Text
     * @return boolean false on error
     */
	private function insertWordList( $wordlist, $type )
	{
		
		if( !empty( $wordlist ) ){
		
			$sql  = "INSERT INTO `pdf_words` ( pdf_id, word, count, type ) VALUES ";
			
			foreach( $wordlist as $word => $count ){
				
				$sql .= "( " . $this->pdfId . ", '" . $word . "' , " . $count . ", '" . $type . "' ),";
			
			}

			$sql = rtrim($sql, ",") . " ; ";

			try{
				
				MAIN::$DB->query( $sql );

			}
			catch(PDOException $e){
				
				return false;
				
			}
		}		
	}	
}

?>