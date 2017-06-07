<?php

define("sGOMOBILE_CONF_PATH", "conf");
define("sGOMOBILE_API_PATH", "conf/lib/gomobile");

// HTTP Request
if (isset ( $_SERVER ['DOCUMENT_ROOT'] ) === true && empty ( $_SERVER ['DOCUMENT_ROOT'] ) === false) {
	$_SERVER ['DOCUMENT_ROOT'] = str_replace ( "\\", "/", $_SERVER ['DOCUMENT_ROOT'] );
	// Define system path constant
	define ( "sSYSTEM_PATH", substr ( $_SERVER ['DOCUMENT_ROOT'], 0, strrpos ( $_SERVER ['DOCUMENT_ROOT'], "/" ) ) );
} // Command line
else {
	$aTemp = explode ( "/", str_replace ( "\\", "/", __FILE__ ) );
	$sPath = "";
	for($i = 0; $i < count ( $aTemp ) - 3; $i ++) {
		$sPath .= $aTemp [$i] . "/";
	}
	// Define system path constant
	define ( "sSYSTEM_PATH", substr ( $sPath, 0, strlen ( $sPath ) - 1 ) );
}
/* ========== Define System path End ========== */

// Define path to the General API classes
define ( "sAPI_CLASS_PATH", substr ( sSYSTEM_PATH, 0, strrpos ( sSYSTEM_PATH, "/" ) ) . "/../php5api/classes/" );
// Define path to the General API interfaces
define ( "sAPI_INTERFACE_PATH", substr ( sSYSTEM_PATH, 0, strrpos ( sSYSTEM_PATH, "/" ) ) . "/../php5api/interfaces/" );
// Define path to the System classes
define ( "sCLASS_PATH", sSYSTEM_PATH . "/api/classes/" );
// Define path to the System Configuration
define ( "sCONF_PATH", sSYSTEM_PATH . "/conf/" );

// Require API for handling and reporting errors
require_once (sAPI_CLASS_PATH . "report.php");
// Require API for defining the Database interface
require_once (sAPI_INTERFACE_PATH . "database.php");
// Require Database Abstraction API
require_once (sAPI_CLASS_PATH . "/database.php");
// Require API for parsing HTTP Header Template with text tags: {TEXT_TAG}
require_once(sAPI_CLASS_PATH ."/template.php");
// Require API for handling the connection to a remote webserver using HTTP
require_once(sAPI_CLASS_PATH ."/http_client.php");
// Require API for Simple DOM manipulation
require_once(sAPI_CLASS_PATH ."/simpledom.php");


// Require API for general functionality
require_once (sCLASS_PATH . "general.php");

// Require global settings file
require_once (sCONF_PATH . "global.php");

// Require the PHP API for handling the connection to GoMobile
require_once(sGOMOBILE_API_PATH ."/gomobile.php");

// Require the PHP API for handling the connection to SMTP server
require_once(sAPI_CLASS_PATH ."/smtp.php");

// Require global configuration file
require_once(sGOMOBILE_CONF_PATH ."/gomobile.php");

// Local mBE classes
require_once ("classes/enduseraccount.php");
require_once ("classes/chat.php");
require_once ("classes/transaction.php");

?>