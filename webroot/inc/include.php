<?php
/* ========== Define System path Start ========== */
// HTTP Request
if(isset($_SERVER['DOCUMENT_ROOT']) === true && empty($_SERVER['DOCUMENT_ROOT']) === false)
{  
	$_SERVER['DOCUMENT_ROOT'] = str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']);
	// Define system path constant
	define("sSYSTEM_PATH", substr($_SERVER['DOCUMENT_ROOT'], 0, strrpos($_SERVER['DOCUMENT_ROOT'], "/") ) );
}
// Command line
else
{
	$aTemp = explode("/", str_replace("\\", "/", __FILE__) );
	$sPath = "";
	for($i=0; $i<count($aTemp)-3; $i++)
	{
		$sPath .= $aTemp[$i] ."/";
	}
	// Define system path constant
	define("sSYSTEM_PATH", substr($sPath, 0, strlen($sPath)-1) );
}
/* ========== Define System path End ========== */

// Define path to the General API classes
define("sAPI_CLASS_PATH", substr(sSYSTEM_PATH, 0, strrpos(sSYSTEM_PATH, "/") ) ."/php5api/classes/");
// Define path to the General API interfaces
define("sAPI_INTERFACE_PATH", substr(sSYSTEM_PATH, 0, strrpos(sSYSTEM_PATH, "/") ) ."/php5api/interfaces/");
// Define path to the General API functions
define("sAPI_FUNCTION_PATH", substr(sSYSTEM_PATH, 0, strrpos(sSYSTEM_PATH, "/") ) ."/php5api/functions/");

// Define path to the System classes
define("sCLASS_PATH", sSYSTEM_PATH ."/api/classes/");
// Define path to the System interfaces
define("sINTERFACE_PATH", sSYSTEM_PATH ."/api/interfaces/");
// Define path to the System functions
define("sFUNCTION_PATH", sSYSTEM_PATH ."/api/functions/");
// Define path to the System Configuration
define("sCONF_PATH", sSYSTEM_PATH ."/conf/");

// Require API for defining the Database interface
require_once(sAPI_INTERFACE_PATH ."database.php");

// Require API for handling and reporting errors
require_once(sAPI_CLASS_PATH ."report.php");
// Require API for parsing Templates with text tags: {TEXT_TAG}
require_once(sAPI_CLASS_PATH ."template.php");
// Require API for handling the connection to a remote webserver using HTTP
require_once(sAPI_CLASS_PATH ."http_client.php");
// Require API for handling and reporting errors to a remote host
require_once(sAPI_CLASS_PATH ."remote_report.php");
// Require Database Abstraction API
require_once(sAPI_CLASS_PATH ."database.php");
// Require API for Custom User Session handling
require_once(sAPI_CLASS_PATH ."session.php");
// Require API for Text Transalation
require_once(sAPI_CLASS_PATH ."text.php");
// Require API for controlling Output prior to sending it to the device
require_once(sAPI_CLASS_PATH ."output.php");
// Require API for handling resizing of images
require_once(sAPI_CLASS_PATH ."image.php");

// Require global API functions
require_once(sAPI_FUNCTION_PATH ."global.php");


// Require API for Web Session handling
require_once(sCLASS_PATH ."websession.php");
// Require API for general functionality
require_once(sCLASS_PATH ."general.php");

// Require global Application functions
require_once(sFUNCTION_PATH ."global.php");

// Require global settings file
require_once(sCONF_PATH ."global.php");

// Define Language Path Constant
define("sLANGUAGE_PATH", sSYSTEM_PATH ."/webroot/text/". sLANG ."/");

// Intialise Text Translation Object
$_OBJ_TXT = new TranslateText(array(sLANGUAGE_PATH ."global.txt", sLANGUAGE_PATH ."custom.txt"), sSYSTEM_PATH, 0);

// Set Custom Error & Exception handlers
new RemoteReport(HTTPConnInfo::produceHTTPConnInfo($aHTTP_CONN_INFO), iOUTPUT_METHOD, sERROR_LOG, iDEBUG_LEVEL);

// Web Request
if ( eregi("/api/", $_SERVER['PHP_SELF']) == false && eregi("/internal/", $_SERVER['PHP_SELF']) == false && empty($_SERVER['DOCUMENT_ROOT']) === false)
{
	// Start user session
	new Session($aDB_CONN_INFO["session"], iOUTPUT_METHOD, sERROR_LOG);
	
	// Session object not initialized
	if (isset($_SESSION['obj_Info']) === false)
	{
		$_SESSION['obj_Info'] = new WebSession();
	}
}

/*
 * Use Output buffering to "magically" transform the XML via XSL behind the scene
 * This means that all PHP scripts must output a wellformed XML document.
 * The XML in turn must refer to an XSL Stylesheet by using the xml-stylesheet tag
 */
ob_start(array(new Output(), "transform") );

// Ensure that output buffer is flushed and ended so transform method is called when a script terminates
register_shutdown_function("ob_end_flush");
?>