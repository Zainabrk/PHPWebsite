<?php
 /**
 * A PHP file which magages the Index functionality of the system.
 */
 
/**
 * Index
 *
 * The Index of the system
 *
 * @category   Index
 * @package    Index
 * @author     Tim Steinbrecher
 * @version    Release: 1.0
 */
 
	ini_set("display_errors","on");
	ini_set('error_reporting', E_ALL);
	error_reporting(E_ALL);
	session_start();
	#unset($_SESSION['post'] );
   /**
    * OPTIONAL ON SOME INSTALLATIONS
    *
    * Set include path to root of library, relative to Samples directory.
    * Only needed when running library from local directory.
    * If library is installed in PHP include path, this is not needed
    */
    set_include_path(get_include_path() . PATH_SEPARATOR . './');
    set_include_path(get_include_path() . PATH_SEPARATOR . './Lib/');

   /**
    * OPTIONAL ON SOME INSTALLATIONS
    *
    * Autoload function is reponsible for loading classes of the library on demand
    *
    * NOTE: Only one __autoload function is allowed by PHP per each PHP installation,
    * and this function may need to be replaced with individual require_once statements
    * in case where other framework that define an __autoload already loaded.
    *
    * However, since this library follow common naming convention for PHP classes it
    * may be possible to simply re-use an autoload mechanism defined by other frameworks
    * (provided library is installed in the PHP include path), and so classes may just
    * be loaded even when this function is removed
	*
	* @param string $className Class Name
    */
     function myAutoload($className){
        $filePath = str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
        $includePaths = explode(PATH_SEPARATOR, get_include_path());
        foreach($includePaths as $includePath){
            if(file_exists($includePath . DIRECTORY_SEPARATOR . $filePath)){
                require_once $filePath;
                return;
            }
        }
    }
	spl_autoload_register('myAutoload');
	Main::_init();

	
	if( !empty( $_FILES ) ){

		return true;
		
	}
	
?>
<!DOCTYPE html>
<html lang="de">
	<head>
		<meta charset="utf-8">
		<title><?php echo Main::$Display->getTitle(); ?></title>
		<base href="<?php echo Main::$Config->get('basehref'); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">
		<meta name="apple-mobile-web-app-title" content="<?php echo Main::$Display->getTitle(); ?>">
		<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
		<link rel="apple-touch-icon" href="img/touch-icon-iphone.png">
		<link rel="apple-touch-icon" sizes="76x76" href="img/touch-icon-ipad.png">
		<link rel="apple-touch-icon" sizes="120x120" href="img/touch-icon-iphone-retina.png">
		<link rel="apple-touch-icon" sizes="152x152" href="img/touch-icon-ipad-retina.png">
		<link rel="stylesheet" href="data/css/material.min.css">
		<link rel="stylesheet" href="data/css/style.css">
		<script src="data/js/material.min.js"></script>
		<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
		<?php echo Main::$Display->getJs(); ?>
		<?php echo Main::$Display->getCss(); ?>
	</head>
	<body>
		<div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
			<header class="mdl-layout__header">
				<div class="mdl-layout__header-row">
					<span class="mdl-layout-title"><?php echo Main::$Display->getHeadline(); ?></span>
					<div class="mdl-layout-spacer"></div>
					<nav class="mdl-navigation">
						<a class="mdl-navigation__link" href="Home" id="home-link"><i class="material-icons">home</i></a>
						<div class="mdl-tooltip" for="home-link">Home</div>
						<a class="mdl-navigation__link" href="Search" id="search-link"><i class="material-icons">search</i></a>
						<div class="mdl-tooltip" for="search-link">Suche</div>
						<a class="mdl-navigation__link" href="Info" id="info-link"><i class="material-icons">info</i></a>
						<div class="mdl-tooltip" for="info-link">Information</div>
						<a class="mdl-navigation__link" href="Author" id="author-link"><i class="material-icons">account_box</i></a>
						<div class="mdl-tooltip" for="author-link">Author</div>
						<a class="mdl-navigation__link" href="Word" id="word-link"><i class="material-icons">list</i></a>
						<div class="mdl-tooltip" for="word-link">Wordlist</div>
						<a class="mdl-navigation__link" href="Cloud" id="cloud-link"><i class="material-icons">cloud</i></a>
						<div class="mdl-tooltip" for="cloud-link">Wordcloud</div>
						<a class="mdl-navigation__link" href="Map" id="map-link"><i class="material-icons">map</i></a>
						<div class="mdl-tooltip" for="map-link">Landkarte</div>
						<a class="mdl-navigation__link" href="Download" id="download-link"><i class="material-icons">file_download</i></a>
						<div class="mdl-tooltip" for="download-link">Download</div>
						<a class="mdl-navigation__link" href="Upload" id="upload-link"><i class="material-icons">file_upload</i></a>
						<div class="mdl-tooltip" for="upload-link">Upload</div>
					</nav>
				</div>
			</header>
			<main class="mdl-layout__content">
				<div class="page-content">
					<div class="mdl-cell mdl-cell--12-col">
						<?php Main::$Display->getContent(); ?>
					</div>
				</div>
			</main>
		</div>
	</body>
</html>