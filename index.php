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
		<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
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
                        <div id="myform">
            <h4 id="sub"></h4>
            <label id="uploadfile" widht="200px">
                <input id="Files" name="file" type="file" onchange="Submitpaper()" accept=".pdf, .docx"/>
                Chose Paper
            </label>
            <br>
            <br>
            <br>
            <input id="submitfile" type="submit" value="Submit" onclick="UploadFile()">
            <h4 id="paper">Choose paper for chatbot</h4>

        </div>
        <!-- Chatbot interface showing chatbot replies on the left and users message on the right while getting user response from the bottom of the div -->
        <div id="container">
            <div id="message">
                <table id="mymessage">
                    <tr>
                        <th colspan="2" width="100%"></th>
                        <th width="100%"></th>
                    </tr>
                    <tr>
                        <td id="h11">Hello! How can i help you with the paper?</td>
                        <td></td>
                    </tr>
                </table>
            </div>
            <hr style="height:1px;border:0;color:black;background-color:black;margin: 0;padding: 0;">
            <div id="InputDiv">
                <input id="input" type="text" name="message" placeholder="Enter Message!">
                <a id="btn" onclick="userMessage()">Send</a>

            </div>
        </div>
        <!-- Floating button that shows option of chosing paper when clicked and initiate chat -->
        <button id="chatbot" style="position:fixed;right:1%;bottom:1%;" onclick="Chatbot()">Chatbot</button>
        <!-- In Line Styles For Web Page -->
        
					</div>
				</div>
			</main>
		</div>

		<style>
            .spinner {
                animation: rotate 2s linear infinite;
                z-index: 2;
                position: absolute;
                top: 50%;
                left: 50%;
                margin: -25px 0 0 -25px;
                width: 50px;
                height: 50px;
            }
            .path {
                animation: rotate 2s linear infinite;
                z-index: 2;
                position: absolute;
                top: 50%;
                left: 50%;
                margin: -25px 0 0 -25px;
                width: 50px;
                height: 50px;
                stroke: hsl(210, 70, 75);
                stroke-linecap: round;
                animation: dash 1.5s ease-in-out infinite;
            }


            @keyframes rotate {
                100% {
                    transform: rotate(360deg);
                }
            }

            @keyframes dash {
                0% {
                    stroke-dasharray: 1, 150;
                    stroke-dashoffset: 0;
                }
                50% {
                    stroke-dasharray: 90, 150;
                    stroke-dashoffset: -35;
                }
                100% {
                    stroke-dasharray: 90, 150;
                    stroke-dashoffset: -124;
                }
            }

            #h12 {
                font-size: medium;
                padding: 10px;
                background-color: #B4DEC5;
                width: fit-content;
                border-radius: 10px;
                float: right;
            }
            #mymessage {
                padding: 10px;
                width: 100%;
            }

            #h11 {
                padding: 10px;
                font-size: medium;
                width: 60%;
                border-radius: 10px;
                background-color: #B4B9DE;
                text-align: left;
            }

            #input {
                width: 86%;
                border-radius: 0 0 0 10px;
                padding: 1%;
                color: black;
                height: 4vh;
                border-width: 0 1px 0 0;
                background-color: rgb(203, 203, 203);
            }

            #input:focus,
            input:focus {
                outline: none;
            }

            #btn {
                border-radius: 0 0 10px 0;
                height: 4vh;
                cursor: pointer;
                border: black;
                color: rgb(130, 7, 7);
            }


            #container {
                visibility: hidden;
                margin-top: -3%;
                background-color: rgb(203, 203, 203);
                border-radius: 10px;
                margin-left: 30%;
                margin-right: 30%;
            }

            #message {
                margin-top: 15%;
                height: 400px;
                overflow-y: scroll;

            }

            #message::-webkit-scrollbar {
                display: none;
            }


            #title {
                font-size: xx-large;
                font-weight: bold;
                color: white;
            }

            input[type="file"] {
                display: none;
            }
            #myform {
                text-align: center;
                visibility: hidden;
            }
            #uploadfile {
                margin-top: 2%;
                color: rgb(255, 255, 255);
                background-color: #727272;
                border-radius: 10px;
                padding: 1%;
                cursor: pointer;
                text-decoration: none;
                width: 100%;
            }
            #chatbot {
                color: rgb(255, 255, 255);
                background-color: #3d7bb0;
                border-color: transparent;
                border-radius: 10px;
                padding: 1%;
                cursor: pointer;
                text-decoration: none;
            }
            #submitfile {
                color: rgb(255, 255, 255);
                background-color: #3d4ddc;
                border-color: transparent;
                border-radius: 10px;
                padding: 1%;
                cursor: pointer;
                text-decoration: none;
            }
        </style>
        <script>
            function Submitpaper() {
                var paper = document.getElementById('paper');
                paper.innerHTML = "Submit the selected paper"

            }
            // Function caling flask api when click on the submit button and file is send in the API to flask
            $(function () {
                $('#submitfile').click(function () {
                    var sub = document.getElementById('sub');
                    sub.innerHTML = "&#x2705; Your paper is submitted wait for some seconds to chat..."
                    const formData = new FormData();
                    formData.append("file", document.getElementById('Files').files[0]);
                    $.ajax({
                        type: 'POST',
                        url: "http://127.0.0.1:3000/chosecsv",
                        data: formData,
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function (data) {
                            var ins = document.getElementById('container');
                            ins.style.visibility = "visible"
                            var sub = document.getElementById('sub');
                            sub.innerHTML = ""
                            var paper = document.getElementById('paper');
                            paper.innerHTML = ""
                        }
                    });
                });
            });
            // Fuction initiate chat by when click on the chatbot floating button
            function Chatbot() {
                var ins = document.getElementById('myform');
                ins.style.visibility = "visible"
            }
            // Fuction Checking if a file is uploaded before submitting if not then it wil show a alert
            function UploadFile() {
                var ins = document.getElementById('Files').files.length;
                if (ins === 0) {
                    alert("No File Selected")
                }

            }
            // Checking whether enter is pressed while getting response from the user and if yes then call user message function by activating
            // on click function on element with btn id
            input.addEventListener("keypress", function (event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    document.getElementById("btn").click();
                }
            });
            // Function that call flask API to send the user response to the chatbot and get the chatbot response in return
            function userMessage() {
                let myphase = document.querySelector("#convophase");
                let userInput = document.querySelector("#input");
                let message = document.querySelector("#message");
                let mymessage = document.querySelector("#mymessage");
                if (userInput.value != "") {
                    let table = document.getElementById("mymessage");
                    let row = table.insertRow(-1);
                    let c1 = row.insertCell(0);
                    let c2 = row.insertCell(1);
                    c1.innerText = ""
                    c2.innerText = userInput.value
                    c2.setAttribute("id", "h12")
                    $.ajax({
                        url: "http://127.0.0.1:3000/response",
                        method: "post",
                        data: {
                            usermessage: userInput.value
                        },
                        success: function (reply) {
                            let table = document.getElementById("mymessage");
                            let row = table.insertRow(-1);
                            let c1 = row.insertCell(0);
                            let c2 = row.insertCell(1);
                            c1.innerText = reply
                            c2.innerText = ""
                            c1.setAttribute("id", "h11")
                            userInput.value = ""
                            var objDiv = document.getElementById("message");
                            objDiv.scrollTop = objDiv.scrollHeight;
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown) {
                            alert("errorThrown");
                        }
                    })


                }

            }
        </script>
       
    
	</body>
</html>